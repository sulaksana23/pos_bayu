"use client";

import { useMemo, useState } from "react";
import Link from "next/link";
import { useQuery } from "@tanstack/react-query";
import { Minus, Package, Plus, ShoppingBag, Sparkles, Trash2 } from "lucide-react";
import { api } from "@/lib/api";
import type { Category, PaymentMethod, Product, Transaction } from "@/lib/types";
import { cn, rupiah, toNumber } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Modal } from "@/components/ui/modal";
import { EmptyState, SearchInput, Skeleton } from "@/components/ui/misc";
import { Logo, ThemeToggle } from "@/components/layout/app-shell";
import { PaymentModal } from "@/components/pos/payment-modal";
import { ProductThumb } from "@/components/pos/product-thumb";
import { Receipt } from "@/components/pos/receipt";

type Line = { product: Product; qty: number };

/** Public cashier demo (embedded on the main website). Nothing is saved. */
export default function DemoPage() {
  const [q, setQ] = useState("");
  const [category, setCategory] = useState<number | null>(null);
  const [lines, setLines] = useState<Line[]>([]);
  const [paying, setPaying] = useState(false);
  const [receipt, setReceipt] = useState<Transaction | null>(null);

  const { data, isLoading } = useQuery({
    queryKey: ["demo-catalog"],
    queryFn: () => api.get<{ categories: Category[]; products: Product[] }>("/demo/catalog"),
    staleTime: Infinity,
  });

  const products = useMemo(
    () =>
      (data?.products ?? []).filter(
        (p) => (!category || p.category_id === category) && (!q || p.name.toLowerCase().includes(q.toLowerCase())),
      ),
    [data, category, q],
  );
  const total = lines.reduce((s, l) => s + toNumber(l.product.price) * l.qty, 0);
  const items = lines.reduce((s, l) => s + l.qty, 0);

  const add = (p: Product) =>
    setLines((ls) => (ls.some((l) => l.product.id === p.id) ? ls.map((l) => (l.product.id === p.id ? { ...l, qty: l.qty + 1 } : l)) : [...ls, { product: p, qty: 1 }]));
  const setQty = (id: number, qty: number) => setLines((ls) => (qty <= 0 ? ls.filter((l) => l.product.id !== id) : ls.map((l) => (l.product.id === id ? { ...l, qty } : l))));

  const pay = ({ method, paid }: { method: PaymentMethod; paid: number }) => {
    const now = new Date().toISOString();
    setReceipt({
      id: 0,
      invoice_no: "DEMO-" + now.slice(0, 10).replace(/-/g, ""),
      shift_id: 0,
      user_id: 0,
      customer_id: null,
      cashier: { id: 0, name: "Kasir Demo" },
      subtotal: String(total),
      discount: "0",
      tax: "0",
      total: String(total),
      paid: String(paid),
      change_amount: String(Math.max(0, paid - total)),
      payment_method: method,
      status: "completed",
      notes: null,
      void_reason: null,
      voided_at: null,
      created_at: now,
      items: lines.map((l, i) => ({
        id: i,
        product_id: l.product.id,
        product_name: l.product.name,
        product_sku: l.product.sku,
        price: l.product.price,
        cost: null,
        qty: l.qty,
        discount: "0",
        subtotal: String(toNumber(l.product.price) * l.qty),
      })),
    });
    setPaying(false);
    setLines([]);
  };

  return (
    <div className="flex h-dvh flex-col overflow-hidden">
      <header className="flex h-14 shrink-0 items-center gap-3 border-b border-border bg-surface px-4">
        <Logo />
        <span className="ml-2 hidden items-center gap-1.5 rounded-full bg-amber-500/10 px-3 py-1 text-xs font-medium text-amber-700 sm:inline-flex dark:text-amber-400">
          <Sparkles className="size-3.5" /> Mode demo — transaksi tidak disimpan
        </span>
        <div className="flex-1" />
        <ThemeToggle />
        <Link href="/login" target="_top">
          <Button size="sm">Masuk</Button>
        </Link>
      </header>

      <div className="flex min-h-0 flex-1 flex-col md:flex-row">
        <section className="flex min-h-0 min-w-0 flex-1 flex-col">
          <div className="space-y-3 border-b border-border p-3">
            <SearchInput value={q} onChange={setQ} placeholder="Cari produk…" delay={100} />
            <div className="flex gap-2 overflow-x-auto">
              {[{ id: null as number | null, name: "Semua", color: null as string | null }, ...(data?.categories ?? [])].map((c) => (
                <button
                  key={c.id ?? "all"}
                  onClick={() => setCategory(c.id)}
                  className={cn("flex shrink-0 items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium", category === c.id ? "border-fg bg-fg text-bg" : "border-border bg-surface hover:bg-surface-2")}
                >
                  {c.color && <span className="size-2 rounded-full" style={{ background: c.color }} />}
                  {c.name}
                </button>
              ))}
            </div>
          </div>
          <div className="flex-1 overflow-y-auto p-3">
            {isLoading ? (
              <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                {Array.from({ length: 8 }).map((_, i) => <Skeleton key={i} className="aspect-[4/5] rounded-xl" />)}
              </div>
            ) : !products.length ? (
              <EmptyState icon={Package} title="Produk tidak ditemukan" />
            ) : (
              <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                {products.map((p) => (
                  <button key={p.id} onClick={() => add(p)} className="group flex flex-col overflow-hidden rounded-xl border border-border bg-surface text-left transition-all hover:-translate-y-0.5 hover:border-brand-500/60 hover:shadow-lg active:scale-[0.98]">
                    <ProductThumb name={p.name} src={p.image_url} color={p.category?.color} className="aspect-[4/3] w-full" textClassName="text-2xl" />
                    <div className="p-2.5">
                      <p className="line-clamp-2 text-[13px] font-medium">{p.name}</p>
                      <p className="tabular mt-1 text-sm font-bold text-brand-700 dark:text-brand-400">{rupiah(p.price)}</p>
                    </div>
                  </button>
                ))}
              </div>
            )}
          </div>
        </section>

        <aside className="flex max-h-[45dvh] shrink-0 flex-col border-t border-border bg-surface md:max-h-none md:w-80 md:border-t-0 md:border-l">
          <p className="px-4 pt-3 pb-1 text-sm font-semibold">
            Keranjang <span className="font-normal text-muted">({items} item)</span>
          </p>
          <ul className="flex-1 divide-y divide-border overflow-y-auto">
            {lines.length === 0 ? (
              <EmptyState icon={ShoppingBag} title="Keranjang kosong" description="Klik produk untuk mencoba." />
            ) : (
              lines.map((l) => (
                <li key={l.product.id} className="flex items-center gap-2 px-4 py-2.5">
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-sm font-medium">{l.product.name}</p>
                    <p className="tabular text-xs text-muted">{rupiah(toNumber(l.product.price) * l.qty)}</p>
                  </div>
                  <button onClick={() => setQty(l.product.id, l.qty - 1)} className="rounded-md p-1 text-muted hover:bg-surface-2" aria-label="Kurangi">
                    {l.qty === 1 ? <Trash2 className="size-3.5" /> : <Minus className="size-3.5" />}
                  </button>
                  <span className="tabular w-6 text-center text-sm font-semibold">{l.qty}</span>
                  <button onClick={() => setQty(l.product.id, l.qty + 1)} className="rounded-md p-1 text-muted hover:bg-surface-2" aria-label="Tambah">
                    <Plus className="size-3.5" />
                  </button>
                </li>
              ))
            )}
          </ul>
          <div className="space-y-2 border-t border-border p-4">
            <div className="flex items-end justify-between">
              <span className="font-semibold">Total</span>
              <span className="tabular text-xl font-bold text-brand-700 dark:text-brand-400">{rupiah(total)}</span>
            </div>
            <Button size="lg" className="w-full" disabled={!lines.length} onClick={() => setPaying(true)}>
              Bayar
            </Button>
          </div>
        </aside>
      </div>

      {paying && <PaymentModal open onClose={() => setPaying(false)} total={total} loading={false} onPay={pay} />}
      <Modal
        open={!!receipt}
        onClose={() => setReceipt(null)}
        title="Transaksi demo berhasil 🎉"
        description="Di aplikasi asli, stok & laporan diperbarui otomatis."
        size="sm"
        footer={<Button onClick={() => setReceipt(null)}>Coba lagi</Button>}
      >
        {receipt && (
          <div className="rounded-lg border border-border">
            <Receipt trx={receipt} />
          </div>
        )}
      </Modal>
    </div>
  );
}
