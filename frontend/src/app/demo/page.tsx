"use client";

import { useMemo, useState } from "react";
import Link from "next/link";
import {
  ArrowRight,
  Cookie,
  CupSoda,
  LayoutGrid,
  Minus,
  Package,
  Plus,
  Receipt as ReceiptIcon,
  ShieldCheck,
  ShoppingBasket,
  ShoppingCart,
  Sparkles,
  SprayCan,
  Trash2,
  UtensilsCrossed,
  type LucideIcon,
} from "lucide-react";
import type { PaymentMethod, Product, Transaction } from "@/lib/types";
import { MOCK_CATEGORIES, MOCK_PRODUCTS } from "@/lib/mock-catalog";
import { cn, PAYMENT_LABELS, rupiah, toNumber } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Modal } from "@/components/ui/modal";
import { SearchInput } from "@/components/ui/misc";
import { Logo, ThemeToggle } from "@/components/layout/app-shell";
import { PaymentModal } from "@/components/pos/payment-modal";
import { Receipt } from "@/components/pos/receipt";

type Line = { product: Product; qty: number };

const CATEGORY_ICONS: Record<string, LucideIcon> = {
  UtensilsCrossed,
  CupSoda,
  Cookie,
  ShoppingBasket,
  SprayCan,
};

function CategoryIcon({ icon, className, style }: { icon: string | null | undefined; className?: string; style?: React.CSSProperties }) {
  const Icon = (icon && CATEGORY_ICONS[icon]) || Package;
  return <Icon className={className} style={style} />;
}

function ProductTile({ product, qtyInCart, popular, onAdd }: { product: Product; qtyInCart: number; popular: boolean; onAdd: () => void }) {
  const color = product.category?.color || "#64748b";
  const low = product.stock > 0 && product.stock <= product.min_stock;
  const out = product.stock <= 0;

  return (
    <button
      onClick={onAdd}
      disabled={out}
      className={cn(
        "group relative flex flex-col overflow-hidden rounded-2xl border bg-surface text-left shadow-sm transition-all",
        "hover:-translate-y-1 hover:shadow-xl active:scale-[0.97]",
        qtyInCart ? "border-brand-500 ring-2 ring-brand-500/25" : "border-border hover:border-brand-500/50",
        out && "cursor-not-allowed opacity-50 grayscale hover:translate-y-0 hover:shadow-sm",
      )}
    >
      <div className="relative flex aspect-[4/3] w-full items-center justify-center overflow-hidden" style={{ background: `linear-gradient(140deg, ${color}26, ${color}0d)` }}>
        <CategoryIcon icon={product.category?.icon} className="size-9 opacity-90 transition-transform duration-300 group-hover:scale-110" />
        <span className="pointer-events-none absolute inset-0" style={{ background: `radial-gradient(circle at 30% 20%, ${color}22, transparent 60%)` }} />

        {popular && !out && (
          <span className="absolute top-2 left-2 inline-flex items-center gap-1 rounded-full bg-white/90 px-2 py-0.5 text-[10px] font-semibold text-amber-600 shadow-sm backdrop-blur dark:bg-black/60">
            <Sparkles className="size-3" /> Populer
          </span>
        )}
        {qtyInCart > 0 && (
          <span className="absolute top-2 right-2 grid size-6 place-items-center rounded-full bg-brand-600 text-xs font-bold text-white shadow ring-2 ring-white dark:ring-surface">{qtyInCart}</span>
        )}
        {(out || low) && (
          <span className={cn("absolute bottom-2 left-2 rounded-md px-1.5 py-0.5 text-[10px] font-semibold text-white shadow-sm", out ? "bg-red-600" : "bg-amber-500")}>
            {out ? "Stok habis" : `Sisa ${product.stock}`}
          </span>
        )}

        <span className="absolute right-2 bottom-2 grid size-7 place-items-center rounded-full bg-brand-600 text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
          <Plus className="size-4" />
        </span>
      </div>
      <div className="flex flex-1 flex-col p-3">
        <p className="line-clamp-2 text-[13px] leading-snug font-medium">{product.name}</p>
        <div className="mt-auto flex items-end justify-between gap-1 pt-1.5">
          <p className="tabular text-sm font-bold text-brand-700 dark:text-brand-400">{rupiah(product.price)}</p>
          {!out && <p className="text-[10px] text-muted">/{product.unit}</p>}
        </div>
      </div>
    </button>
  );
}

/** Public cashier demo (embedded on the main website / shown before login). Nothing is saved. */
export default function DemoPage() {
  const [q, setQ] = useState("");
  const [category, setCategory] = useState<number | null>(null);
  const [lines, setLines] = useState<Line[]>([]);
  const [paying, setPaying] = useState(false);
  const [receipt, setReceipt] = useState<Transaction | null>(null);

  const products = useMemo(
    () =>
      MOCK_PRODUCTS.filter(
        (p) => (!category || p.category_id === category) && (!q || p.name.toLowerCase().includes(q.toLowerCase())),
      ),
    [category, q],
  );
  const qtyById = useMemo(() => Object.fromEntries(lines.map((l) => [l.product.id, l.qty])), [lines]);
  const popularIds = useMemo(() => new Set(MOCK_PRODUCTS.slice(0, 3).map((p) => p.id)), []);

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

  const cartPanel = (
    <>
      <div className="flex items-center gap-2 border-b border-border px-4 py-3.5">
        <span className="grid size-8 place-items-center rounded-lg bg-brand-500/10 text-brand-600 dark:text-brand-400">
          <ShoppingCart className="size-4" />
        </span>
        <p className="text-sm font-semibold">
          Keranjang <span className="font-normal text-muted">({items} item)</span>
        </p>
        {lines.length > 0 && (
          <button onClick={() => setLines([])} className="ml-auto flex items-center gap-1 text-xs font-medium text-muted hover:text-red-600">
            <Trash2 className="size-3.5" /> Kosongkan
          </button>
        )}
      </div>

      <ul className="flex-1 divide-y divide-border overflow-y-auto">
        {lines.length === 0 ? (
          <div className="flex flex-1 flex-col items-center justify-center gap-3 px-6 py-10 text-center">
            <div className="grid size-14 place-items-center rounded-2xl bg-surface-2 text-muted">
              <ShoppingCart className="size-6" />
            </div>
            <div>
              <p className="text-sm font-medium">Keranjang masih kosong</p>
              <p className="mt-1 text-xs text-muted">Klik salah satu produk di sebelah kiri untuk mencoba alur kasir.</p>
            </div>
          </div>
        ) : (
          lines.map((l) => {
            const color = l.product.category?.color || "#64748b";
            return (
              <li key={l.product.id} className="flex items-center gap-3 px-4 py-2.5">
                <span className="grid size-10 shrink-0 place-items-center rounded-xl" style={{ background: `${color}1f`, color }}>
                  <CategoryIcon icon={l.product.category?.icon} className="size-4.5" />
                </span>
                <div className="min-w-0 flex-1">
                  <p className="truncate text-sm font-medium">{l.product.name}</p>
                  <p className="tabular text-xs text-muted">{rupiah(toNumber(l.product.price) * l.qty)}</p>
                </div>
                <div className="flex shrink-0 items-center rounded-lg border border-border">
                  <button onClick={() => setQty(l.product.id, l.qty - 1)} className="grid size-7 place-items-center text-muted hover:text-fg" aria-label="Kurangi">
                    {l.qty === 1 ? <Trash2 className="size-3.5" /> : <Minus className="size-3.5" />}
                  </button>
                  <span className="tabular w-6 text-center text-sm font-semibold">{l.qty}</span>
                  <button onClick={() => setQty(l.product.id, l.qty + 1)} className="grid size-7 place-items-center text-muted hover:text-fg" aria-label="Tambah">
                    <Plus className="size-3.5" />
                  </button>
                </div>
              </li>
            );
          })
        )}
      </ul>

      <div className="space-y-3 border-t border-border bg-surface-2/40 p-4">
        <div className="flex justify-between text-sm text-muted">
          <span>Subtotal</span>
          <span className="tabular">{rupiah(total)}</span>
        </div>
        <div className="flex items-end justify-between">
          <span className="font-semibold">Total</span>
          <span className="tabular text-2xl font-bold tracking-tight text-brand-700 dark:text-brand-400">{rupiah(total)}</span>
        </div>
        <Button size="lg" className="w-full text-base" disabled={!lines.length} onClick={() => setPaying(true)}>
          Bayar Sekarang
        </Button>
      </div>
    </>
  );

  return (
    <div className="flex h-dvh flex-col overflow-hidden bg-bg">
      <header className="flex h-16 shrink-0 items-center gap-3 border-b border-border bg-surface/90 px-4 backdrop-blur-md sm:px-5">
        <Logo />
        <span className="ml-1 hidden items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-1 text-[11px] font-medium text-amber-700 sm:inline-flex dark:text-amber-400">
          <Sparkles className="size-3.5" /> Demo interaktif — data tidak disimpan
        </span>
        <div className="flex-1" />
        <ThemeToggle />
        <Link href="/login" target="_top">
          <Button size="sm" className="gap-1.5">
            Masuk <ArrowRight className="size-3.5" />
          </Button>
        </Link>
      </header>

      <div className="flex min-h-0 flex-1 flex-col md:flex-row">
        <section className="flex min-h-0 min-w-0 flex-1 flex-col">
          <div className="space-y-3 border-b border-border bg-surface px-3 py-3 sm:px-4">
            <SearchInput value={q} onChange={setQ} placeholder="Cari produk (mis. “indomie”, “aqua”)…" delay={100} className="sm:max-w-sm" />
            <div className="-mx-1 flex gap-2 overflow-x-auto px-1 pb-0.5">
              <button
                onClick={() => setCategory(null)}
                className={cn(
                  "flex shrink-0 items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-xs font-medium transition-colors",
                  category === null ? "border-fg bg-fg text-bg" : "border-border bg-surface hover:bg-surface-2",
                )}
              >
                <LayoutGrid className="size-3.5" /> Semua
              </button>
              {MOCK_CATEGORIES.map((c) => {
                const active = category === c.id;
                return (
                  <button
                    key={c.id}
                    onClick={() => setCategory(active ? null : c.id)}
                    className="flex shrink-0 items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-xs font-medium transition-colors"
                    style={active ? { background: c.color ?? undefined, borderColor: c.color ?? undefined, color: "#fff" } : undefined}
                  >
                    <CategoryIcon icon={c.icon} className={cn("size-3.5", !active && "opacity-70")} style={!active ? { color: c.color ?? undefined } : undefined} />
                    <span className={active ? "" : "text-fg"}>{c.name}</span>
                  </button>
                );
              })}
            </div>
          </div>

          <div className="flex-1 overflow-y-auto p-3 pb-28 sm:p-4 md:pb-4">
            {!products.length ? (
              <div className="flex flex-col items-center justify-center gap-3 py-16 text-center">
                <div className="grid size-14 place-items-center rounded-2xl bg-surface-2 text-muted">
                  <Package className="size-6" />
                </div>
                <div>
                  <p className="text-sm font-medium">Produk tidak ditemukan</p>
                  <p className="mt-1 text-xs text-muted">Coba kata kunci atau kategori lain.</p>
                </div>
              </div>
            ) : (
              <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                {products.map((p) => (
                  <ProductTile key={p.id} product={p} qtyInCart={qtyById[p.id] ?? 0} popular={popularIds.has(p.id)} onAdd={() => add(p)} />
                ))}
              </div>
            )}
          </div>
        </section>

        {/* Cart — desktop */}
        <aside className="hidden shrink-0 flex-col border-l border-border bg-surface md:flex md:w-[340px] lg:w-[380px]">{cartPanel}</aside>
      </div>

      {/* Cart — mobile bottom bar */}
      <div className="fixed inset-x-0 bottom-0 z-30 border-t border-border bg-surface p-3 md:hidden">
        <Button size="lg" className="w-full justify-between" disabled={!lines.length} onClick={() => setPaying(true)}>
          <span className="flex items-center gap-2">
            <ShoppingCart className="size-5" /> {items} item
          </span>
          <span className="tabular">{rupiah(total)}</span>
        </Button>
      </div>

      {paying && <PaymentModal open onClose={() => setPaying(false)} total={total} loading={false} onPay={pay} />}

      <Modal
        open={!!receipt}
        onClose={() => setReceipt(null)}
        title="Transaksi demo berhasil 🎉"
        description="Ini simulasi — di aplikasi asli, stok & laporan diperbarui otomatis setiap transaksi."
        size="sm"
        footer={
          <div className="flex w-full flex-col gap-2 sm:flex-row sm:justify-end">
            <Button variant="outline" onClick={() => setReceipt(null)}>
              Coba lagi
            </Button>
            <Link href="/login" target="_top" className="sm:contents">
              <Button className="w-full gap-1.5 sm:w-auto">
                Coba fitur lengkap <ArrowRight className="size-3.5" />
              </Button>
            </Link>
          </div>
        }
      >
        {receipt && (
          <div className="space-y-4">
            <div className="grid grid-cols-3 gap-2 text-center">
              {[
                { icon: ReceiptIcon, label: "Item", value: String(receipt.items?.reduce((s, i) => s + i.qty, 0) ?? 0) },
                { icon: ShoppingCart, label: "Metode", value: PAYMENT_LABELS[receipt.payment_method] },
                { icon: ShieldCheck, label: "Status", value: "Selesai" },
              ].map((s) => (
                <div key={s.label} className="rounded-xl bg-surface-2 px-2 py-3">
                  <s.icon className="mx-auto mb-1 size-4 text-brand-600 dark:text-brand-400" />
                  <p className="text-[11px] text-muted">{s.label}</p>
                  <p className="truncate text-xs font-semibold">{s.value}</p>
                </div>
              ))}
            </div>
            <div className="overflow-hidden rounded-lg border border-border">
              <Receipt trx={receipt} />
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
