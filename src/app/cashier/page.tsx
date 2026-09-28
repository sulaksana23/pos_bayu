"use client";

import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import Link from "next/link";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import {
  ArrowLeft,
  Barcode,
  Clock,
  Minus,
  Package,
  PauseCircle,
  Percent,
  Plus,
  Printer,
  RotateCcw,
  ScanLine,
  ShoppingBag,
  Tag,
  Trash2,
  User as UserIcon,
  X,
} from "lucide-react";
import { toast } from "sonner";
import { api, ApiError, errorMessage } from "@/lib/api";
import type { Category, Paginated, Product, Transaction } from "@/lib/types";
import { cn, rupiah, time, toNumber } from "@/lib/utils";
import { cartTotals, useCart, type CartLine } from "@/stores/cart";
import { useCurrentShift } from "@/hooks/use-session";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Modal } from "@/components/ui/modal";
import { EmptyState, Skeleton, Spinner } from "@/components/ui/misc";
import { Logo, ThemeToggle, UserMenu } from "@/components/layout/app-shell";
import { CustomerPicker } from "@/components/pos/customer-picker";
import { PaymentModal } from "@/components/pos/payment-modal";
import { Receipt } from "@/components/pos/receipt";
import { OpenShiftForm } from "@/components/pos/shift-forms";
import { ProductThumb } from "@/components/pos/product-thumb";

function useClock() {
  const [now, setNow] = useState<Date | null>(null);
  useEffect(() => {
    const tick = () => setNow(new Date());
    tick();
    const t = setInterval(tick, 30_000);
    return () => clearInterval(t);
  }, []);
  return now;
}

function ProductCard({ p, inCart, onAdd }: { p: Product; inCart: number; onAdd: () => void }) {
  const out = p.stock <= 0;
  const low = !out && p.min_stock > 0 && p.stock <= p.min_stock;

  return (
    <button
      onClick={onAdd}
      disabled={out}
      className={cn(
        "group relative flex flex-col overflow-hidden rounded-xl border bg-surface text-left transition-all",
        "hover:-translate-y-0.5 hover:border-brand-500/60 hover:shadow-lg hover:shadow-brand-600/5 active:scale-[0.98]",
        inCart ? "border-brand-500 ring-2 ring-brand-500/20" : "border-border",
        out && "cursor-not-allowed opacity-50 grayscale hover:translate-y-0 hover:shadow-none",
      )}
    >
      <div className="relative aspect-[4/3] w-full overflow-hidden">
        <ProductThumb name={p.name} src={p.image_url} color={p.category?.color} className="size-full" textClassName="text-2xl" />
        {inCart > 0 && (
          <span className="absolute top-2 right-2 grid size-6 place-items-center rounded-full bg-brand-600 text-xs font-bold text-white shadow">{inCart}</span>
        )}
        {(out || low) && (
          <span className={cn("absolute bottom-2 left-2 rounded-md px-1.5 py-0.5 text-[10px] font-semibold text-white", out ? "bg-red-600" : "bg-amber-500")}>
            {out ? "HABIS" : `Sisa ${p.stock}`}
          </span>
        )}
      </div>
      <div className="flex flex-1 flex-col p-2.5">
        <p className="line-clamp-2 text-[13px] leading-snug font-medium">{p.name}</p>
        <div className="mt-auto flex items-end justify-between gap-1 pt-1.5">
          <p className="tabular text-sm font-bold text-brand-700 dark:text-brand-400">{rupiah(p.price)}</p>
          {!out && !low && <p className="text-[10px] text-muted">{p.stock} {p.unit}</p>}
        </div>
      </div>
    </button>
  );
}

function CartRow({ line }: { line: CartLine }) {
  const { setQty, remove, setLineDiscount } = useCart();
  const [discOpen, setDiscOpen] = useState(line.discount > 0);
  const price = toNumber(line.product.price);

  return (
    <li className="group px-4 py-3">
      <div className="flex items-start gap-3">
        <div className="min-w-0 flex-1">
          <p className="truncate text-sm font-medium">{line.product.name}</p>
          <p className="tabular text-xs text-muted">
            {rupiah(price)} / {line.product.unit}
          </p>
        </div>
        <p className="tabular text-sm font-semibold">{rupiah(Math.max(0, price * line.qty - line.discount))}</p>
      </div>
      <div className="mt-2 flex items-center gap-2">
        <div className="flex items-center rounded-lg border border-border">
          <button onClick={() => setQty(line.product.id, line.qty - 1)} className="grid size-8 place-items-center text-muted hover:text-fg" aria-label="Kurangi">
            <Minus className="size-3.5" />
          </button>
          <input
            value={line.qty}
            inputMode="numeric"
            onChange={(e) => setQty(line.product.id, Number(e.target.value.replace(/\D/g, "")) || 0)}
            className="tabular h-8 w-10 border-x border-border bg-transparent text-center text-sm font-semibold focus:outline-none"
          />
          <button
            onClick={() => {
              if (line.qty >= line.product.stock) toast.warning(`Stok ${line.product.name} tersisa ${line.product.stock}.`);
              else setQty(line.product.id, line.qty + 1);
            }}
            className="grid size-8 place-items-center text-muted hover:text-fg"
            aria-label="Tambah"
          >
            <Plus className="size-3.5" />
          </button>
        </div>
        <button onClick={() => setDiscOpen((v) => !v)} className={cn("rounded-lg p-1.5 text-muted hover:bg-surface-2 hover:text-fg", line.discount > 0 && "text-brand-600")} title="Diskon item">
          <Tag className="size-4" />
        </button>
        <button onClick={() => remove(line.product.id)} className="ml-auto rounded-lg p-1.5 text-muted hover:bg-red-500/10 hover:text-red-600" title="Hapus">
          <Trash2 className="size-4" />
        </button>
      </div>
      {discOpen && (
        <div className="mt-2 flex items-center gap-2">
          <span className="text-xs text-muted">Diskon item</span>
          <Input
            inputMode="numeric"
            className="tabular h-8 flex-1 text-right"
            value={line.discount ? new Intl.NumberFormat("id-ID").format(line.discount) : ""}
            placeholder="0"
            onChange={(e) => setLineDiscount(line.product.id, Math.min(price * line.qty, Number(e.target.value.replace(/\D/g, "")) || 0))}
          />
        </div>
      )}
    </li>
  );
}

export default function CashierPage() {
  const qc = useQueryClient();
  const now = useClock();
  const searchRef = useRef<HTMLInputElement>(null);
  const [search, setSearch] = useState("");
  const [debounced, setDebounced] = useState("");
  const [category, setCategory] = useState<number | null>(null);
  const [customerOpen, setCustomerOpen] = useState(false);
  const [payOpen, setPayOpen] = useState(false);
  const [heldOpen, setHeldOpen] = useState(false);
  const [cartOpen, setCartOpen] = useState(false);
  const [receipt, setReceipt] = useState<Transaction | null>(null);

  const cart = useCart();
  const totals = useMemo(() => cartTotals(cart.lines, cart.discount, cart.taxRate), [cart.lines, cart.discount, cart.taxRate]);
  const qtyById = useMemo(() => Object.fromEntries(cart.lines.map((l) => [l.product.id, l.qty])), [cart.lines]);

  const shift = useCurrentShift();

  useEffect(() => {
    const t = setTimeout(() => setDebounced(search.trim()), 200);
    return () => clearTimeout(t);
  }, [search]);

  const categories = useQuery({
    queryKey: ["categories"],
    queryFn: async () => (await api.get<{ data: Category[] }>("/categories")).data,
    staleTime: 5 * 60_000,
  });

  const products = useQuery({
    queryKey: ["products", "cashier", debounced, category],
    queryFn: () => api.get<Paginated<Product>>("/products", { q: debounced, category_id: category, per_page: 200 }),
    placeholderData: (prev) => prev,
  });

  const addProduct = useCallback(
    (p: Product) => {
      const res = cart.add(p);
      if (!res.ok) toast.warning(res.message);
    },
    [cart],
  );

  /** Enter in the search box: treat as barcode/SKU scan, else add the single match. */
  const onScan = async () => {
    const code = search.trim();
    if (!code) return;
    try {
      const res = await api.get<{ data: Product }>("/products/lookup", { code });
      addProduct(res.data);
      setSearch("");
    } catch (e) {
      const list = products.data?.data ?? [];
      if (e instanceof ApiError && e.status === 404 && list.length === 1 && debounced === code) {
        addProduct(list[0]);
        setSearch("");
      } else if (e instanceof ApiError && e.status === 404) {
        toast.error(`Produk "${code}" tidak ditemukan`);
      } else {
        toast.error(errorMessage(e));
      }
    }
  };

  const checkout = useMutation({
    mutationFn: (p: { method: string; paid: number; notes: string }) =>
      api.post<{ data: Transaction; message: string }>("/transactions", {
        items: cart.lines.map((l) => ({ product_id: l.product.id, qty: l.qty, discount: l.discount })),
        customer_id: cart.customer?.id ?? null,
        discount: cart.discount,
        tax: totals.tax,
        paid: p.paid,
        payment_method: p.method,
        notes: p.notes || null,
      }),
    onSuccess: (res) => {
      toast.success(res.message);
      setPayOpen(false);
      setCartOpen(false);
      setReceipt(res.data);
      cart.clear();
      qc.invalidateQueries({ queryKey: ["products"] });
      qc.invalidateQueries({ queryKey: ["shift"] });
      qc.invalidateQueries({ queryKey: ["dashboard"] });
      qc.invalidateQueries({ queryKey: ["transactions"] });
    },
    onError: (e) => {
      if (e instanceof ApiError && e.code === "shift_closed") qc.invalidateQueries({ queryKey: ["shift"] });
      // Stock may have changed at another register — refresh so the grid is accurate.
      qc.invalidateQueries({ queryKey: ["products"] });
      toast.error(errorMessage(e));
    },
  });

  const canPay = cart.lines.length > 0 && !!shift.data;

  // Keyboard shortcuts
  useEffect(() => {
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "F2") {
        e.preventDefault();
        searchRef.current?.focus();
        searchRef.current?.select();
      } else if (e.key === "F4") {
        e.preventDefault();
        setCustomerOpen(true);
      } else if ((e.key === "F9" || e.key === "F12") && canPay && !receipt) {
        e.preventDefault();
        setPayOpen(true);
      }
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [canPay, receipt]);

  const cartPanel = (
    <div className="flex h-full flex-col bg-surface">
      <div className="flex items-center gap-2 border-b border-border px-4 py-3">
        <button onClick={() => setCustomerOpen(true)} className="flex min-w-0 flex-1 items-center gap-3 rounded-lg border border-border px-3 py-2 text-left hover:bg-surface-2">
          <span className="grid size-8 shrink-0 place-items-center rounded-full bg-sky-500/10 text-sky-600">
            <UserIcon className="size-4" />
          </span>
          <span className="min-w-0 flex-1">
            <span className="block truncate text-sm font-medium">{cart.customer?.name ?? "Pelanggan umum"}</span>
            <span className="block text-xs text-muted">{cart.customer ? cart.customer.phone ?? "Ganti pelanggan" : "Pilih pelanggan (F4)"}</span>
          </span>
        </button>
        {cart.customer && (
          <Button variant="ghost" size="icon" onClick={() => cart.setCustomer(null)} aria-label="Hapus pelanggan">
            <X className="size-4" />
          </Button>
        )}
        <Button variant="ghost" size="icon" className="lg:hidden" onClick={() => setCartOpen(false)} aria-label="Tutup keranjang">
          <X className="size-5" />
        </Button>
      </div>

      <div className="flex items-center justify-between px-4 pt-3 pb-1">
        <p className="text-sm font-semibold">
          Keranjang <span className="font-normal text-muted">({totals.items} item)</span>
        </p>
        <div className="flex gap-1">
          <Button variant="ghost" size="sm" onClick={() => setHeldOpen(true)} className="relative">
            <Clock className="size-3.5" /> Ditahan
            {cart.held.length > 0 && <span className="grid size-4 place-items-center rounded-full bg-amber-500 text-[10px] font-bold text-white">{cart.held.length}</span>}
          </Button>
          {cart.lines.length > 0 && (
            <Button variant="ghost" size="sm" onClick={() => cart.clear()} className="text-red-600 hover:bg-red-500/10 hover:text-red-600">
              <RotateCcw className="size-3.5" /> Reset
            </Button>
          )}
        </div>
      </div>

      <ul className="flex-1 divide-y divide-border overflow-y-auto">
        {cart.lines.length === 0 ? (
          <EmptyState icon={ShoppingBag} title="Keranjang kosong" description="Klik produk atau scan barcode untuk menambahkan item." />
        ) : (
          cart.lines.map((l) => <CartRow key={l.product.id} line={l} />)
        )}
      </ul>

      <div className="space-y-2 border-t border-border bg-surface-2/40 px-4 py-3 text-sm">
        <div className="flex justify-between">
          <span className="text-muted">Subtotal</span>
          <span className="tabular">{rupiah(totals.subtotal)}</span>
        </div>
        <div className="flex items-center justify-between gap-3">
          <span className="text-muted">Diskon</span>
          <input
            inputMode="numeric"
            placeholder="0"
            value={cart.discount ? new Intl.NumberFormat("id-ID").format(cart.discount) : ""}
            onChange={(e) => cart.setDiscount(Math.min(totals.subtotal, Number(e.target.value.replace(/\D/g, "")) || 0))}
            className="tabular h-7 w-32 rounded-md border border-border bg-surface px-2 text-right focus:border-brand-500 focus:outline-none"
          />
        </div>
        <div className="flex items-center justify-between gap-3">
          <span className="flex items-center gap-1 text-muted">
            Pajak
            <span className="relative">
              <input
                inputMode="decimal"
                value={cart.taxRate || ""}
                placeholder="0"
                onChange={(e) => cart.setTaxRate(Math.min(100, Number(e.target.value.replace(/[^\d.]/g, "")) || 0))}
                className="tabular h-6 w-12 rounded-md border border-border bg-surface pr-5 pl-1.5 text-right text-xs focus:border-brand-500 focus:outline-none"
              />
              <Percent className="absolute top-1/2 right-1 size-3 -translate-y-1/2 text-muted" />
            </span>
          </span>
          <span className="tabular">{rupiah(totals.tax)}</span>
        </div>
        <div className="flex items-end justify-between border-t border-dashed border-border pt-2">
          <span className="font-semibold">Total</span>
          <span className="tabular text-2xl font-bold tracking-tight text-brand-700 dark:text-brand-400">{rupiah(totals.total)}</span>
        </div>
        <div className="grid grid-cols-[auto_1fr] gap-2 pt-1">
          <Button variant="outline" size="lg" disabled={!cart.lines.length} onClick={() => { cart.hold(); toast.success("Pesanan ditahan"); }} title="Tahan pesanan">
            <PauseCircle className="size-5" />
          </Button>
          <Button size="lg" disabled={!canPay} onClick={() => setPayOpen(true)} className="text-base">
            Bayar <kbd className="rounded bg-white/20 px-1.5 text-[10px]">F9</kbd>
          </Button>
        </div>
      </div>
    </div>
  );

  return (
    <div className="flex h-dvh flex-col overflow-hidden">
      {/* Top bar */}
      <header className="flex h-14 shrink-0 items-center gap-3 border-b border-border bg-surface px-3 sm:px-4">
        <Link href="/dashboard">
          <Button variant="ghost" size="icon" aria-label="Kembali">
            <ArrowLeft className="size-5" />
          </Button>
        </Link>
        <Logo className="hidden sm:flex" />
        <div className="flex-1" />
        {shift.data && (
          <div className="hidden items-center gap-4 rounded-lg bg-surface-2 px-3 py-1.5 text-xs md:flex">
            <span className="flex items-center gap-1.5 font-medium text-emerald-600">
              <span className="size-1.5 rounded-full bg-emerald-500" /> Shift aktif
            </span>
            <span className="text-muted">{shift.data.transaction_count} trx</span>
            <span className="tabular font-semibold">{rupiah(shift.data.total_sales)}</span>
          </div>
        )}
        <span className="tabular hidden text-sm font-medium text-muted sm:block">{now ? time(now.toISOString()) : ""}</span>
        <ThemeToggle />
        <UserMenu />
      </header>

      <div className="flex min-h-0 flex-1">
        {/* Products */}
        <section className="flex min-w-0 flex-1 flex-col">
          <div className="space-y-3 border-b border-border bg-surface/60 p-3 sm:p-4">
            <div className="relative">
              <ScanLine className="pointer-events-none absolute top-1/2 left-3.5 size-5 -translate-y-1/2 text-muted" />
              <input
                ref={searchRef}
                autoFocus
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                onKeyDown={(e) => {
                  if (e.key === "Enter") {
                    e.preventDefault();
                    onScan();
                  } else if (e.key === "Escape") setSearch("");
                }}
                placeholder="Cari produk atau scan barcode…"
                className="h-12 w-full rounded-xl border border-border bg-surface pr-20 pl-11 text-[15px] shadow-sm placeholder:text-muted/70 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/15 focus:outline-none"
              />
              <span className="absolute top-1/2 right-3 flex -translate-y-1/2 items-center gap-1 text-xs text-muted">
                <Barcode className="size-4" /> <kbd className="rounded border border-border px-1.5 py-0.5 text-[10px]">F2</kbd>
              </span>
            </div>
            <div className="-mx-1 flex gap-2 overflow-x-auto px-1 pb-0.5">
              <button
                onClick={() => setCategory(null)}
                className={cn("shrink-0 rounded-full border px-3.5 py-1.5 text-xs font-medium transition-colors", category === null ? "border-fg bg-fg text-bg" : "border-border bg-surface hover:bg-surface-2")}
              >
                Semua
              </button>
              {categories.data?.map((c) => (
                <button
                  key={c.id}
                  onClick={() => setCategory(c.id === category ? null : c.id)}
                  className={cn("flex shrink-0 items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-xs font-medium transition-colors", category === c.id ? "border-fg bg-fg text-bg" : "border-border bg-surface hover:bg-surface-2")}
                >
                  <span className="size-2 rounded-full" style={{ background: c.color || "#94a3b8" }} />
                  {c.name}
                  <span className="opacity-60">{c.products_count}</span>
                </button>
              ))}
            </div>
          </div>

          <div className="flex-1 overflow-y-auto p-3 pb-24 sm:p-4 lg:pb-4">
            {products.isLoading ? (
              <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                {Array.from({ length: 15 }).map((_, i) => (
                  <Skeleton key={i} className="aspect-[4/5] rounded-xl" />
                ))}
              </div>
            ) : !products.data?.data.length ? (
              <EmptyState icon={Package} title="Produk tidak ditemukan" description={debounced ? `Tidak ada hasil untuk "${debounced}".` : "Belum ada produk aktif."} />
            ) : (
              <>
                <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                  {products.data.data.map((p) => (
                    <ProductCard key={p.id} p={p} inCart={qtyById[p.id] ?? 0} onAdd={() => addProduct(p)} />
                  ))}
                </div>
                {products.isFetching && (
                  <div className="flex justify-center pt-4">
                    <Spinner />
                  </div>
                )}
              </>
            )}
          </div>
        </section>

        {/* Cart — desktop */}
        <aside className="hidden w-[400px] shrink-0 border-l border-border lg:block xl:w-[420px]">{cartPanel}</aside>
      </div>

      {/* Cart — mobile bottom bar & sheet */}
      <div className="fixed inset-x-0 bottom-0 z-30 border-t border-border bg-surface p-3 lg:hidden">
        <Button size="lg" className="w-full justify-between" onClick={() => setCartOpen(true)}>
          <span className="flex items-center gap-2">
            <ShoppingBag className="size-5" /> {totals.items} item
          </span>
          <span className="tabular">{rupiah(totals.total)}</span>
        </Button>
      </div>
      {cartOpen && (
        <div className="fixed inset-0 z-40 lg:hidden">
          <div className="absolute inset-0 animate-fade-in bg-black/50" onClick={() => setCartOpen(false)} />
          <div className="absolute inset-x-0 bottom-0 h-[88dvh] animate-slide-up overflow-hidden rounded-t-2xl">{cartPanel}</div>
        </div>
      )}

      {/* Shift gate */}
      {shift.isSuccess && !shift.data && (
        <div className="fixed inset-0 z-40 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
          <div className="w-full max-w-md animate-slide-up rounded-2xl border border-border bg-surface p-6 shadow-2xl">
            <div className="mb-5 flex items-center gap-3">
              <div className="grid size-11 place-items-center rounded-xl bg-amber-500/10 text-amber-600">
                <Clock className="size-5" />
              </div>
              <div>
                <h2 className="font-semibold">Buka shift terlebih dahulu</h2>
                <p className="text-sm text-muted">Transaksi hanya bisa dilakukan saat shift aktif.</p>
              </div>
            </div>
            <OpenShiftForm />
            <Link href="/dashboard" className="mt-3 block text-center text-sm text-muted hover:text-fg">
              Kembali ke dashboard
            </Link>
          </div>
        </div>
      )}

      <CustomerPicker open={customerOpen} onClose={() => setCustomerOpen(false)} onSelect={cart.setCustomer} />

      {payOpen && <PaymentModal open onClose={() => setPayOpen(false)} total={totals.total} loading={checkout.isPending} onPay={(p) => checkout.mutate(p)} />}

      <Modal open={heldOpen} onClose={() => setHeldOpen(false)} title="Pesanan ditahan" description="Lanjutkan pesanan pelanggan yang sebelumnya ditahan.">
        {cart.held.length === 0 ? (
          <EmptyState icon={PauseCircle} title="Tidak ada pesanan ditahan" />
        ) : (
          <ul className="space-y-2">
            {cart.held.map((h) => {
              const t = cartTotals(h.lines, 0, 0);
              return (
                <li key={h.id} className="flex items-center gap-3 rounded-lg border border-border p-3">
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-sm font-medium">{h.label}</p>
                    <p className="text-xs text-muted">
                      {time(h.heldAt)} · {t.items} item · {rupiah(t.total)}
                    </p>
                  </div>
                  <Button variant="ghost" size="icon" onClick={() => cart.dropHeld(h.id)} aria-label="Hapus">
                    <Trash2 className="size-4" />
                  </Button>
                  <Button
                    size="sm"
                    onClick={() => {
                      cart.resume(h.id);
                      setHeldOpen(false);
                    }}
                  >
                    Lanjutkan
                  </Button>
                </li>
              );
            })}
          </ul>
        )}
      </Modal>

      <Modal
        open={!!receipt}
        onClose={() => setReceipt(null)}
        title="Transaksi berhasil 🎉"
        description={receipt ? `Kembalian ${rupiah(receipt.change_amount)}` : undefined}
        size="sm"
        footer={
          <>
            <Button variant="outline" onClick={() => window.print()}>
              <Printer className="size-4" /> Cetak struk
            </Button>
            <Button
              onClick={() => {
                setReceipt(null);
                setTimeout(() => searchRef.current?.focus(), 50);
              }}
            >
              Transaksi baru
            </Button>
          </>
        }
      >
        {receipt && (
          <div className="print-area rounded-lg border border-border">
            <Receipt trx={receipt} />
          </div>
        )}
      </Modal>
    </div>
  );
}
