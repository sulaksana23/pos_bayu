"use client";

import { useMemo, useState } from "react";
import { useRouter } from "next/navigation";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Plus, Search, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api, ApiError, errorMessage } from "@/lib/api";
import type { Paginated, Product, PurchaseOrder, Supplier } from "@/lib/types";
import { isoDate, rupiah, toNumber } from "@/lib/utils";
import { Card, CardHeader } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Field, Input, MoneyInput, Select, Textarea } from "@/components/ui/input";
import { EmptyState } from "@/components/ui/misc";

interface Line {
  key: string;
  product_id: number | null;
  product_name: string;
  product_sku: string;
  qty: number;
  price: number;
}

export function PurchaseOrderForm({ order }: { order?: PurchaseOrder }) {
  const router = useRouter();
  const qc = useQueryClient();
  const [err, setErr] = useState<ApiError | null>(null);
  const [supplierId, setSupplierId] = useState(order ? String(order.supplier_id) : "");
  const [orderDate, setOrderDate] = useState(order?.order_date?.slice(0, 10) ?? isoDate());
  const [expected, setExpected] = useState(order?.expected_date?.slice(0, 10) ?? "");
  const [discount, setDiscount] = useState(toNumber(order?.discount));
  const [tax, setTax] = useState(toNumber(order?.tax));
  const [notes, setNotes] = useState(order?.notes ?? "");
  const [lines, setLines] = useState<Line[]>(
    order?.items?.map((i) => ({ key: crypto.randomUUID(), product_id: i.product_id, product_name: i.product_name, product_sku: i.product_sku ?? "", qty: i.qty, price: toNumber(i.price) })) ?? [],
  );
  const [search, setSearch] = useState("");

  const suppliers = useQuery({
    queryKey: ["suppliers", "active"],
    queryFn: () => api.get<Paginated<Supplier>>("/suppliers", { active_only: true, per_page: 200 }),
  });
  const products = useQuery({
    queryKey: ["products", "po-search", search],
    enabled: search.length > 0,
    queryFn: () => api.get<Paginated<Product>>("/products", { q: search, per_page: 8 }),
  });

  const subtotal = useMemo(() => lines.reduce((s, l) => s + l.qty * l.price, 0), [lines]);
  const total = Math.max(0, subtotal - discount + tax);

  const addProduct = (p: Product) => {
    setLines((ls) => {
      const ex = ls.find((l) => l.product_id === p.id);
      if (ex) return ls.map((l) => (l.product_id === p.id ? { ...l, qty: l.qty + 1 } : l));
      return [...ls, { key: crypto.randomUUID(), product_id: p.id, product_name: p.name, product_sku: p.sku, qty: 1, price: toNumber(p.cost) || toNumber(p.price) }];
    });
    setSearch("");
  };
  const update = (key: string, patch: Partial<Line>) => setLines((ls) => ls.map((l) => (l.key === key ? { ...l, ...patch } : l)));

  const save = useMutation({
    mutationFn: () => {
      const body = {
        supplier_id: supplierId,
        order_date: orderDate || null,
        expected_date: expected || null,
        discount,
        tax,
        notes: notes || null,
        items: lines.map(({ product_id, product_name, product_sku, qty, price }) => ({ product_id, product_name, product_sku, qty, price })),
      };
      return order ? api.put<{ data: PurchaseOrder; message: string }>(`/purchase-orders/${order.id}`, body) : api.post<{ data: PurchaseOrder; message: string }>("/purchase-orders", body);
    },
    onSuccess: (res) => {
      toast.success(res.message);
      qc.invalidateQueries({ queryKey: ["purchase-orders"] });
      qc.invalidateQueries({ queryKey: ["purchase-order"] });
      router.push(`/purchase-orders/${res.data.id}`);
    },
    onError: (e) => {
      if (e instanceof ApiError) setErr(e);
      toast.error(errorMessage(e));
    },
  });

  return (
    <div className="grid gap-6 xl:grid-cols-[1fr_360px]">
      <Card>
        <CardHeader title="Item pembelian" description="Cari produk untuk ditambahkan, atur jumlah dan harga beli." />
        <div className="border-b border-border p-4">
          <div className="relative">
            <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted" />
            <Input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari produk berdasarkan nama atau SKU…" className="pl-9" />
            {search && products.data && (
              <div className="absolute inset-x-0 top-full z-10 mt-1 max-h-72 overflow-y-auto rounded-xl border border-border bg-surface p-1 shadow-xl">
                {products.data.data.length === 0 ? (
                  <p className="px-3 py-4 text-center text-sm text-muted">Tidak ditemukan</p>
                ) : (
                  products.data.data.map((p) => (
                    <button key={p.id} onClick={() => addProduct(p)} className="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm hover:bg-surface-2">
                      <div className="min-w-0 flex-1">
                        <p className="truncate font-medium">{p.name}</p>
                        <p className="font-mono text-xs text-muted">{p.sku} · stok {p.stock}</p>
                      </div>
                      <span className="tabular text-xs text-muted">{rupiah(p.cost)}</span>
                    </button>
                  ))
                )}
              </div>
            )}
          </div>
          {err?.field("items") && <p className="mt-1 text-xs text-red-500">{err.field("items")}</p>}
        </div>
        {lines.length === 0 ? (
          <EmptyState title="Belum ada item" description="Cari produk di atas atau tambahkan item manual." />
        ) : (
          <ul className="divide-y divide-border">
            {lines.map((l) => (
              <li key={l.key} className="grid grid-cols-[1fr_auto] items-center gap-3 px-4 py-3 sm:grid-cols-[1fr_90px_150px_130px_auto]">
                <div className="min-w-0">
                  {l.product_id ? (
                    <>
                      <p className="truncate text-sm font-medium">{l.product_name}</p>
                      <p className="font-mono text-xs text-muted">{l.product_sku}</p>
                    </>
                  ) : (
                    <Input value={l.product_name} onChange={(e) => update(l.key, { product_name: e.target.value })} placeholder="Nama item" className="h-9" />
                  )}
                </div>
                <Button variant="ghost" size="icon" className="size-8 hover:text-red-600 sm:order-last" onClick={() => setLines((ls) => ls.filter((x) => x.key !== l.key))} aria-label="Hapus">
                  <Trash2 className="size-4" />
                </Button>
                <Input type="number" min={1} value={l.qty} onChange={(e) => update(l.key, { qty: Math.max(1, Number(e.target.value)) })} className="h-9 text-right" />
                <MoneyInput value={l.price} onChange={(v) => update(l.key, { price: v })} className="h-9" />
                <p className="tabular text-right text-sm font-semibold">{rupiah(l.qty * l.price)}</p>
              </li>
            ))}
          </ul>
        )}
        <div className="border-t border-border p-3">
          <Button variant="ghost" size="sm" onClick={() => setLines((ls) => [...ls, { key: crypto.randomUUID(), product_id: null, product_name: "", product_sku: "", qty: 1, price: 0 }])}>
            <Plus className="size-4" /> Item manual (tanpa produk)
          </Button>
        </div>
      </Card>

      <div className="space-y-6">
        <Card className="space-y-4 p-5">
          <Field label="Supplier" required error={err?.field("supplier_id")}>
            <Select value={supplierId} onChange={(e) => setSupplierId(e.target.value)}>
              <option value="">Pilih supplier…</option>
              {suppliers.data?.data.map((s) => (
                <option key={s.id} value={s.id}>{s.name}{s.company ? ` — ${s.company}` : ""}</option>
              ))}
            </Select>
          </Field>
          <div className="grid grid-cols-2 gap-3">
            <Field label="Tanggal order" error={err?.field("order_date")}>
              <Input type="date" value={orderDate} onChange={(e) => setOrderDate(e.target.value)} />
            </Field>
            <Field label="Estimasi tiba" error={err?.field("expected_date")}>
              <Input type="date" value={expected} onChange={(e) => setExpected(e.target.value)} />
            </Field>
          </div>
          <Field label="Catatan">
            <Textarea rows={2} value={notes} onChange={(e) => setNotes(e.target.value)} />
          </Field>
        </Card>
        <Card className="space-y-3 p-5 text-sm">
          <div className="flex justify-between">
            <span className="text-muted">Subtotal ({lines.length} item)</span>
            <span className="tabular">{rupiah(subtotal)}</span>
          </div>
          <Field label="Diskon">
            <MoneyInput value={discount} onChange={setDiscount} />
          </Field>
          <Field label="Pajak">
            <MoneyInput value={tax} onChange={setTax} />
          </Field>
          <div className="flex items-end justify-between border-t border-dashed border-border pt-3">
            <span className="font-semibold">Total</span>
            <span className="tabular text-xl font-bold">{rupiah(total)}</span>
          </div>
          <Button size="lg" className="w-full" loading={save.isPending} disabled={!lines.length || !supplierId} onClick={() => save.mutate()}>
            {order ? "Simpan perubahan" : "Buat purchase order"}
          </Button>
        </Card>
      </div>
    </div>
  );
}
