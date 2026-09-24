"use client";

import { useState } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { ImagePlus, Wand2 } from "lucide-react";
import { toast } from "sonner";
import { api, ApiError, errorMessage } from "@/lib/api";
import type { Category, Product } from "@/lib/types";
import { assetUrl } from "@/lib/config";
import { rupiah, toNumber } from "@/lib/utils";
import { Modal } from "@/components/ui/modal";
import { Button } from "@/components/ui/button";
import { Field, Input, MoneyInput, Select, Switch, Textarea } from "@/components/ui/input";

export function ProductFormModal({ product, onClose }: { product: Product | null; onClose: () => void }) {
  const editing = !!product;
  const qc = useQueryClient();
  const [err, setErr] = useState<ApiError | null>(null);
  const [image, setImage] = useState<File | null>(null);
  const [preview, setPreview] = useState<string | null>(assetUrl(product?.image_url));
  const [f, setF] = useState({
    name: product?.name ?? "",
    sku: product?.sku ?? "",
    barcode: product?.barcode ?? "",
    category_id: product?.category_id ? String(product.category_id) : "",
    price: toNumber(product?.price),
    cost: toNumber(product?.cost),
    stock: product?.stock ?? 0,
    min_stock: product?.min_stock ?? 5,
    unit: product?.unit ?? "pcs",
    description: product?.description ?? "",
    is_active: product?.is_active ?? true,
  });
  const set = <K extends keyof typeof f>(k: K, v: (typeof f)[K]) => setF((s) => ({ ...s, [k]: v }));

  const categories = useQuery({
    queryKey: ["categories"],
    queryFn: async () => (await api.get<{ data: Category[] }>("/categories")).data,
  });

  const save = useMutation({
    mutationFn: () => {
      const fd = new FormData();
      Object.entries(f).forEach(([k, v]) => {
        if (editing && k === "stock") return;
        fd.append(k, typeof v === "boolean" ? (v ? "1" : "0") : String(v));
      });
      if (image) fd.append("image", image);
      return editing ? api.put<{ message: string }>(`/products/${product.id}`, fd) : api.post<{ message: string }>("/products", fd);
    },
    onSuccess: (res) => {
      toast.success(res.message);
      qc.invalidateQueries({ queryKey: ["products"] });
      qc.invalidateQueries({ queryKey: ["product"] });
      qc.invalidateQueries({ queryKey: ["categories"] });
      qc.invalidateQueries({ queryKey: ["products-stats"] });
      onClose();
    },
    onError: (e) => {
      if (e instanceof ApiError && e.status === 422) setErr(e);
      toast.error(errorMessage(e));
    },
  });

  const margin = f.price > 0 && f.cost > 0 ? ((f.price - f.cost) / f.price) * 100 : null;

  return (
    <Modal
      open
      onClose={onClose}
      size="lg"
      title={editing ? "Edit produk" : "Tambah produk"}
      footer={
        <>
          <Button variant="outline" onClick={onClose}>
            Batal
          </Button>
          <Button loading={save.isPending} onClick={() => { setErr(null); save.mutate(); }}>
            Simpan
          </Button>
        </>
      }
    >
      <form
        className="grid gap-4 sm:grid-cols-[160px_1fr]"
        onSubmit={(e) => {
          e.preventDefault();
          save.mutate();
        }}
      >
        <div>
          <label className="group relative flex aspect-square cursor-pointer flex-col items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-border bg-surface-2 text-muted hover:border-brand-500">
            {preview ? (
              // eslint-disable-next-line @next/next/no-img-element
              <img src={preview} alt="" className="size-full object-cover" />
            ) : (
              <>
                <ImagePlus className="size-7" />
                <span className="mt-1 text-xs">Unggah foto</span>
              </>
            )}
            <input
              type="file"
              accept="image/png,image/jpeg,image/webp"
              className="hidden"
              onChange={(e) => {
                const file = e.target.files?.[0];
                if (!file) return;
                if (file.size > 2 * 1024 * 1024) return toast.error("Ukuran gambar maksimal 2 MB");
                setImage(file);
                setPreview(URL.createObjectURL(file));
              }}
            />
          </label>
          {err?.field("image") && <p className="mt-1 text-xs text-red-500">{err.field("image")}</p>}
          <div className="mt-3">
            <Switch checked={f.is_active} onChange={(v) => set("is_active", v)} label="Produk aktif" />
          </div>
        </div>

        <div className="grid gap-4 sm:grid-cols-2">
          <Field label="Nama produk" required error={err?.field("name")} className="sm:col-span-2">
            <Input value={f.name} onChange={(e) => set("name", e.target.value)} autoFocus />
          </Field>
          <Field label="SKU" required error={err?.field("sku")}>
            <div className="flex gap-1.5">
              <Input value={f.sku} onChange={(e) => set("sku", e.target.value.toUpperCase())} className="font-mono" />
              <Button
                variant="outline"
                size="icon"
                className="size-10"
                title="Buat SKU otomatis"
                onClick={() => set("sku", `${(f.name.replace(/[^a-z]/gi, "").slice(0, 3) || "PRD").toUpperCase()}-${Date.now().toString().slice(-5)}`)}
              >
                <Wand2 className="size-4" />
              </Button>
            </div>
          </Field>
          <Field label="Barcode" hint="Kosongkan untuk dibuat otomatis" error={err?.field("barcode")}>
            <Input value={f.barcode} onChange={(e) => set("barcode", e.target.value)} className="font-mono" />
          </Field>
          <Field label="Kategori" error={err?.field("category_id")}>
            <Select value={f.category_id} onChange={(e) => set("category_id", e.target.value)}>
              <option value="">Tanpa kategori</option>
              {categories.data?.map((c) => (
                <option key={c.id} value={c.id}>{c.name}</option>
              ))}
            </Select>
          </Field>
          <Field label="Satuan" error={err?.field("unit")}>
            <Input value={f.unit} onChange={(e) => set("unit", e.target.value)} list="units" />
            <datalist id="units">
              {["pcs", "botol", "bungkus", "kg", "gram", "liter", "box", "pack", "lusin", "sachet"].map((u) => (
                <option key={u} value={u} />
              ))}
            </datalist>
          </Field>
          <Field label="Harga jual" required error={err?.field("price")}>
            <MoneyInput value={f.price} onChange={(v) => set("price", v)} />
          </Field>
          <Field label="Harga modal" error={err?.field("cost")} hint={margin !== null ? `Margin ${margin.toFixed(1)}% · untung ${rupiah(f.price - f.cost)}` : undefined}>
            <MoneyInput value={f.cost} onChange={(v) => set("cost", v)} />
          </Field>
          {!editing && (
            <Field label="Stok awal" required error={err?.field("stock")}>
              <Input type="number" min={0} value={f.stock} onChange={(e) => set("stock", Number(e.target.value))} />
            </Field>
          )}
          <Field label="Stok minimum" hint="Peringatan stok menipis" error={err?.field("min_stock")}>
            <Input type="number" min={0} value={f.min_stock} onChange={(e) => set("min_stock", Number(e.target.value))} />
          </Field>
          <Field label="Deskripsi" className="sm:col-span-2">
            <Textarea rows={2} value={f.description} onChange={(e) => set("description", e.target.value)} />
          </Field>
          {editing && <p className="text-xs text-muted sm:col-span-2">Stok diubah melalui tombol &quot;Sesuaikan stok&quot; agar setiap perubahan tercatat.</p>}
        </div>
        <button type="submit" className="hidden" />
      </form>
    </Modal>
  );
}

export function AdjustStockModal({ product, onClose }: { product: Product; onClose: () => void }) {
  const qc = useQueryClient();
  const [type, setType] = useState<"in" | "out" | "adjust">("in");
  const [qty, setQty] = useState(1);
  const [cost, setCost] = useState(toNumber(product.cost));
  const [notes, setNotes] = useState("");

  const result = type === "in" ? product.stock + qty : type === "out" ? product.stock - qty : qty;

  const save = useMutation({
    mutationFn: () => api.post<{ message: string }>(`/products/${product.id}/adjust`, { type, qty, unit_cost: type === "in" ? cost : null, notes: notes || null }),
    onSuccess: (res) => {
      toast.success(res.message);
      qc.invalidateQueries({ queryKey: ["products"] });
      qc.invalidateQueries({ queryKey: ["product", product.id] });
      qc.invalidateQueries({ queryKey: ["products-stats"] });
      onClose();
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  const types = [
    { v: "in", l: "Stok masuk", d: "Barang datang" },
    { v: "out", l: "Stok keluar", d: "Rusak / hilang" },
    { v: "adjust", l: "Stok opname", d: "Set jumlah aktual" },
  ] as const;

  return (
    <Modal
      open
      onClose={onClose}
      title="Sesuaikan stok"
      description={product.name}
      footer={
        <>
          <Button variant="outline" onClick={onClose}>
            Batal
          </Button>
          <Button loading={save.isPending} disabled={result < 0} onClick={() => save.mutate()}>
            Simpan
          </Button>
        </>
      }
    >
      <div className="space-y-4">
        <div className="grid grid-cols-3 gap-2">
          {types.map((t) => (
            <button
              key={t.v}
              onClick={() => setType(t.v)}
              className={`rounded-xl border px-3 py-2.5 text-left transition-colors ${type === t.v ? "border-brand-500 bg-brand-500/10 ring-2 ring-brand-500/20" : "border-border hover:bg-surface-2"}`}
            >
              <p className="text-sm font-medium">{t.l}</p>
              <p className="text-xs text-muted">{t.d}</p>
            </button>
          ))}
        </div>
        <div className="grid grid-cols-2 gap-4">
          <Field label={type === "adjust" ? "Jumlah stok aktual" : "Jumlah"}>
            <Input type="number" min={0} value={qty} onChange={(e) => setQty(Math.max(0, Number(e.target.value)))} autoFocus />
          </Field>
          {type === "in" && (
            <Field label="Harga modal / unit">
              <MoneyInput value={cost} onChange={setCost} />
            </Field>
          )}
        </div>
        <div className="flex items-center justify-between rounded-lg bg-surface-2 px-4 py-3 text-sm">
          <span className="text-muted">Stok saat ini → setelah</span>
          <span className="tabular font-semibold">
            {product.stock} → <span className={result < 0 ? "text-red-600" : "text-brand-600"}>{result}</span> {product.unit}
          </span>
        </div>
        <Field label="Catatan">
          <Textarea rows={2} value={notes} onChange={(e) => setNotes(e.target.value)} placeholder="Opsional" />
        </Field>
      </div>
    </Modal>
  );
}
