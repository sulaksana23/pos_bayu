"use client";

import { useState } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { AlertTriangle, Boxes, Download, MoreHorizontal, Package, PackageX, Pencil, Plus, SlidersHorizontal, Trash2, Wallet } from "lucide-react";
import { toast } from "sonner";
import { api, errorMessage } from "@/lib/api";
import type { Category, Paginated, Product, StockMovement } from "@/lib/types";
import { cn, dateTime, downloadCsv, isBackOffice, rupiah, toNumber } from "@/lib/utils";
import { useAuth } from "@/stores/auth";
import { useList } from "@/hooks/use-list";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Select } from "@/components/ui/input";
import { Drawer } from "@/components/ui/modal";
import { ConfirmDialog, EmptyState, PageHeader, PageLoader, Pagination, SearchInput, StatCard, Tabs } from "@/components/ui/misc";
import { Table, Td, Th, Tr } from "@/components/ui/table";
import { StockBadge } from "@/components/pos/status";
import { ProductThumb } from "@/components/pos/product-thumb";
import { AdjustStockModal, ProductFormModal } from "@/components/pos/product-form";

const MOVEMENT: Record<StockMovement["type"], { label: string; tone: "green" | "red" | "blue" | "amber" | "violet" }> = {
  in: { label: "Masuk", tone: "green" },
  out: { label: "Keluar", tone: "red" },
  sale: { label: "Penjualan", tone: "blue" },
  return: { label: "Retur", tone: "violet" },
  adjust: { label: "Opname", tone: "amber" },
};

function ProductDrawer({ id, onClose, onEdit, onAdjust }: { id: number | null; onClose: () => void; onEdit: (p: Product) => void; onAdjust: (p: Product) => void }) {
  const backOffice = isBackOffice(useAuth((s) => s.user?.role));
  const { data, isLoading } = useQuery({
    queryKey: ["product", id],
    enabled: id !== null,
    queryFn: () => api.get<{ data: Product; movements: StockMovement[] }>(`/products/${id}`),
  });
  const p = data?.data;

  return (
    <Drawer
      open={id !== null}
      onClose={onClose}
      title={p?.name ?? "Detail produk"}
      description={p ? `${p.sku}${p.barcode ? ` · ${p.barcode}` : ""}` : undefined}
      footer={
        p &&
        backOffice && (
          <>
            <Button variant="outline" onClick={() => onEdit(p)}>
              <Pencil className="size-4" /> Edit
            </Button>
            <Button onClick={() => onAdjust(p)}>
              <SlidersHorizontal className="size-4" /> Sesuaikan stok
            </Button>
          </>
        )
      }
    >
      {isLoading || !p ? (
        <PageLoader />
      ) : (
        <div className="space-y-5">
          <div className="flex gap-4">
            <ProductThumb name={p.name} src={p.image_url} color={p.category?.color} className="size-24 shrink-0 rounded-xl" textClassName="text-2xl" />
            <div className="space-y-1.5 text-sm">
              <div className="flex flex-wrap gap-1.5">
                {p.category && <Badge tone="blue">{p.category.name}</Badge>}
                <Badge tone={p.is_active ? "green" : "gray"}>{p.is_active ? "Aktif" : "Nonaktif"}</Badge>
              </div>
              <p className="text-xl font-bold">{rupiah(p.price)}</p>
              {backOffice && p.cost && (
                <p className="text-xs text-muted">
                  Modal {rupiah(p.cost)} · margin {toNumber(p.price) > 0 ? (((toNumber(p.price) - toNumber(p.cost)) / toNumber(p.price)) * 100).toFixed(1) : 0}%
                </p>
              )}
            </div>
          </div>
          <div className="grid grid-cols-3 gap-3 text-sm">
            {[
              ["Stok", `${p.stock} ${p.unit}`],
              ["Minimum", `${p.min_stock} ${p.unit}`],
              ["Nilai stok", rupiah(p.stock * toNumber(p.cost))],
            ].map(([l, v]) => (
              <div key={l} className="rounded-lg bg-surface-2 px-3 py-2">
                <p className="text-xs text-muted">{l}</p>
                <p className="tabular font-semibold">{v}</p>
              </div>
            ))}
          </div>
          {p.description && <p className="text-sm text-muted">{p.description}</p>}
          <div>
            <p className="mb-2 text-sm font-semibold">Riwayat stok</p>
            {data.movements.length === 0 ? (
              <p className="rounded-lg border border-dashed border-border py-6 text-center text-sm text-muted">Belum ada pergerakan stok</p>
            ) : (
              <ul className="divide-y divide-border rounded-lg border border-border">
                {data.movements.map((m) => (
                  <li key={m.id} className="flex items-center gap-3 px-3 py-2 text-sm">
                    <Badge tone={MOVEMENT[m.type].tone}>{MOVEMENT[m.type].label}</Badge>
                    <div className="min-w-0 flex-1">
                      <p className="truncate text-xs text-muted">{m.notes || m.user?.name || "-"}</p>
                      <p className="text-[11px] text-muted/80">{dateTime(m.created_at)}</p>
                    </div>
                    <span className={cn("tabular font-semibold", m.qty >= 0 ? "text-emerald-600" : "text-red-600")}>
                      {m.qty > 0 ? "+" : ""}
                      {m.qty}
                    </span>
                  </li>
                ))}
              </ul>
            )}
          </div>
        </div>
      )}
    </Drawer>
  );
}

export default function ProductsPage() {
  const qc = useQueryClient();
  const backOffice = isBackOffice(useAuth((s) => s.user?.role));
  const [q, setQ] = useState("");
  const [category, setCategory] = useState("");
  const [stock, setStock] = useState<"" | "low" | "out" | "available">("");
  const [active, setActive] = useState<"1" | "0" | "">("1");
  const [page, setPage] = useState(1);
  const [detail, setDetail] = useState<number | null>(null);
  const [editing, setEditing] = useState<Product | null | undefined>(undefined);
  const [adjusting, setAdjusting] = useState<Product | null>(null);
  const [deleting, setDeleting] = useState<Product | null>(null);
  const [menu, setMenu] = useState<number | null>(null);

  const params = { q, category_id: category, stock_status: stock, include_inactive: true, is_active: active, page, per_page: 20 };
  const { data, isLoading } = useList<Product>("products", "/products", params);
  const categories = useQuery({ queryKey: ["categories"], queryFn: async () => (await api.get<{ data: Category[] }>("/categories")).data });
  const stats = useQuery({
    queryKey: ["products-stats"],
    enabled: backOffice,
    queryFn: async () => (await api.get<{ data: { total: number; active: number; low: number; out: number; value: number } }>("/products-stats")).data,
  });

  const del = useMutation({
    mutationFn: (id: number) => api.delete<{ message: string }>(`/products/${id}`),
    onSuccess: (res) => {
      toast.success(res.message);
      setDeleting(null);
      qc.invalidateQueries({ queryKey: ["products"] });
      qc.invalidateQueries({ queryKey: ["products-stats"] });
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  const exportCsv = async () => {
    const all = await api.get<Paginated<Product>>("/products", { ...params, page: 1, per_page: 500 });
    downloadCsv(
      "produk.csv",
      ["SKU", "Barcode", "Nama", "Kategori", "Harga", "Modal", "Stok", "Min", "Satuan", "Aktif"],
      all.data.map((p) => [p.sku, p.barcode, p.name, p.category?.name, toNumber(p.price), toNumber(p.cost), p.stock, p.min_stock, p.unit, p.is_active ? "Ya" : "Tidak"]),
    );
  };

  const reset = () => setPage(1);

  return (
    <>
      <PageHeader
        title="Produk & stok"
        description="Kelola katalog, harga, dan persediaan barang."
        actions={
          <>
            <Button variant="outline" onClick={exportCsv}>
              <Download className="size-4" /> Ekspor
            </Button>
            {backOffice && (
              <Button onClick={() => setEditing(null)}>
                <Plus className="size-4" /> Tambah produk
              </Button>
            )}
          </>
        }
      />

      {backOffice && stats.data && (
        <div className="mb-6 grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
          <StatCard label="Total produk" value={stats.data.total} icon={Boxes} hint={`${stats.data.active} aktif`} />
          <StatCard label="Stok menipis" value={stats.data.low} icon={AlertTriangle} tone="amber" />
          <StatCard label="Stok habis" value={stats.data.out} icon={PackageX} tone="red" />
          <StatCard label="Nilai persediaan" value={rupiah(stats.data.value)} icon={Wallet} tone="violet" hint="berdasarkan harga modal" />
        </div>
      )}

      <Card>
        <div className="flex flex-wrap items-center gap-2 border-b border-border p-4">
          <SearchInput value={q} onChange={(v) => { setQ(v); reset(); }} placeholder="Cari nama, SKU, barcode…" className="w-full sm:w-72" />
          <Select value={category} onChange={(e) => { setCategory(e.target.value); reset(); }} className="w-auto">
            <option value="">Semua kategori</option>
            {categories.data?.map((c) => (
              <option key={c.id} value={c.id}>{c.name}</option>
            ))}
          </Select>
          <Select value={active} onChange={(e) => { setActive(e.target.value as typeof active); reset(); }} className="w-auto">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
            <option value="">Semua</option>
          </Select>
          <div className="sm:ml-auto">
            <Tabs
              value={stock}
              onChange={(v) => { setStock(v); reset(); }}
              items={[
                { value: "", label: "Semua" },
                { value: "available", label: "Tersedia" },
                { value: "low", label: "Menipis" },
                { value: "out", label: "Habis" },
              ]}
            />
          </div>
        </div>

        {isLoading ? (
          <PageLoader />
        ) : !data?.data.length ? (
          <EmptyState icon={Package} title="Tidak ada produk" description="Ubah filter atau tambahkan produk baru." action={backOffice && <Button onClick={() => setEditing(null)}><Plus className="size-4" /> Tambah produk</Button>} />
        ) : (
          <>
            <Table>
              <thead>
                <tr>
                  <Th>Produk</Th>
                  <Th className="hidden md:table-cell">Kategori</Th>
                  <Th className="text-right">Harga</Th>
                  {backOffice && <Th className="hidden text-right lg:table-cell">Modal</Th>}
                  <Th>Stok</Th>
                  {backOffice && <Th className="w-10" />}
                </tr>
              </thead>
              <tbody>
                {data.data.map((p) => {
                  return (
                    <Tr key={p.id} onClick={() => setDetail(p.id)} className={cn(!p.is_active && "opacity-60")}>
                      <Td>
                        <div className="flex items-center gap-3">
                          <ProductThumb name={p.name} src={p.image_url} color={p.category?.color} className="size-10 shrink-0 rounded-lg" textClassName="text-xs" />
                          <div className="min-w-0">
                            <p className="truncate font-medium">{p.name}</p>
                            <p className="font-mono text-xs text-muted">{p.sku}</p>
                          </div>
                        </div>
                      </Td>
                      <Td className="hidden md:table-cell">
                        {p.category ? (
                          <span className="inline-flex items-center gap-1.5 text-sm">
                            <span className="size-2 rounded-full" style={{ background: p.category.color || "#94a3b8" }} />
                            {p.category.name}
                          </span>
                        ) : (
                          <span className="text-muted">-</span>
                        )}
                      </Td>
                      <Td className="tabular text-right font-medium whitespace-nowrap">{rupiah(p.price)}</Td>
                      {backOffice && <Td className="tabular hidden text-right whitespace-nowrap text-muted lg:table-cell">{rupiah(p.cost)}</Td>}
                      <Td><StockBadge stock={p.stock} min={p.min_stock} unit={p.unit} /></Td>
                      {backOffice && (
                        <Td onClick={(e) => e.stopPropagation()} className="relative">
                          <Button variant="ghost" size="icon" className="size-8" onClick={() => setMenu(menu === p.id ? null : p.id)} aria-label="Aksi">
                            <MoreHorizontal className="size-4" />
                          </Button>
                          {menu === p.id && (
                            <>
                              <div className="fixed inset-0 z-10" onClick={() => setMenu(null)} />
                              <div className="absolute right-4 z-20 mt-1 w-48 animate-slide-up rounded-xl border border-border bg-surface p-1 shadow-xl">
                                {[
                                  { icon: Pencil, label: "Edit", fn: () => setEditing(p) },
                                  { icon: SlidersHorizontal, label: "Sesuaikan stok", fn: () => setAdjusting(p) },
                                  { icon: Trash2, label: "Hapus", fn: () => setDeleting(p), danger: true },
                                ].map((a) => (
                                  <button
                                    key={a.label}
                                    onClick={() => { setMenu(null); a.fn(); }}
                                    className={cn("flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm hover:bg-surface-2", a.danger && "text-red-600 hover:bg-red-500/10")}
                                  >
                                    <a.icon className="size-4" /> {a.label}
                                  </button>
                                ))}
                              </div>
                            </>
                          )}
                        </Td>
                      )}
                    </Tr>
                  );
                })}
              </tbody>
            </Table>
            <Pagination page={data.current_page} lastPage={data.last_page} total={data.total} onPage={setPage} />
          </>
        )}
      </Card>

      <ProductDrawer id={detail} onClose={() => setDetail(null)} onEdit={(p) => setEditing(p)} onAdjust={(p) => setAdjusting(p)} />
      {editing !== undefined && <ProductFormModal product={editing} onClose={() => setEditing(undefined)} />}
      {adjusting && <AdjustStockModal product={adjusting} onClose={() => setAdjusting(null)} />}
      <ConfirmDialog
        open={!!deleting}
        onClose={() => setDeleting(null)}
        onConfirm={() => deleting && del.mutate(deleting.id)}
        loading={del.isPending}
        title="Hapus produk?"
        description={<>Produk <b>{deleting?.name}</b> akan dihapus. Jika sudah pernah terjual, produk hanya dinonaktifkan agar riwayat tetap utuh.</>}
        confirmText="Hapus"
      />
    </>
  );
}
