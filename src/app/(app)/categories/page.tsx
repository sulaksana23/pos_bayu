"use client";

import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Pencil, Plus, Tags, Trash2 } from "lucide-react";
import { api } from "@/lib/api";
import type { Category } from "@/lib/types";
import { useCrud } from "@/hooks/use-crud";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Field, Input, Switch, Textarea } from "@/components/ui/input";
import { Modal } from "@/components/ui/modal";
import { ConfirmDialog, EmptyState, PageHeader, PageLoader } from "@/components/ui/misc";
import { RoleGate } from "@/components/layout/auth-guard";

const COLORS = ["#059669", "#0ea5e9", "#6366f1", "#8b5cf6", "#ec4899", "#ef4444", "#f97316", "#f59e0b", "#84cc16", "#14b8a6", "#64748b"];

type Form = { name: string; description: string; color: string; sort_order: number; is_active: boolean };

export default function CategoriesPage() {
  const [editing, setEditing] = useState<Category | null | undefined>(undefined);
  const [deleting, setDeleting] = useState<Category | null>(null);
  const [f, setF] = useState<Form>({ name: "", description: "", color: COLORS[0], sort_order: 0, is_active: true });
  const { save, remove, errors } = useCrud<Form>("categories", ["categories", "products"], () => setEditing(undefined));

  const { data, isLoading } = useQuery({
    queryKey: ["categories", "all"],
    queryFn: async () => (await api.get<{ data: Category[] }>("/categories", { include_inactive: true })).data,
  });

  const open = (c: Category | null) => {
    setF(c ? { name: c.name, description: c.description ?? "", color: c.color ?? COLORS[0], sort_order: c.sort_order, is_active: c.is_active } : { name: "", description: "", color: COLORS[(data?.length ?? 0) % COLORS.length], sort_order: data?.length ?? 0, is_active: true });
    setEditing(c);
  };

  return (
    <RoleGate roles={["admin", "manager"]}>
      <PageHeader
        title="Kategori"
        description="Kelompokkan produk agar mudah dicari di kasir."
        actions={
          <Button onClick={() => open(null)}>
            <Plus className="size-4" /> Tambah kategori
          </Button>
        }
      />
      {isLoading ? (
        <PageLoader />
      ) : !data?.length ? (
        <Card>
          <EmptyState icon={Tags} title="Belum ada kategori" action={<Button onClick={() => open(null)}>Tambah kategori</Button>} />
        </Card>
      ) : (
        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
          {data.map((c) => (
            <Card key={c.id} className="group relative overflow-hidden p-5">
              <div className="absolute inset-y-0 left-0 w-1" style={{ background: c.color ?? "#94a3b8" }} />
              <div className="flex items-start gap-3">
                <div className="grid size-11 shrink-0 place-items-center rounded-xl text-sm font-bold" style={{ background: `${c.color ?? "#94a3b8"}1f`, color: c.color ?? "#64748b" }}>
                  {c.name.slice(0, 2).toUpperCase()}
                </div>
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2">
                    <p className="truncate font-semibold">{c.name}</p>
                    {!c.is_active && <Badge>Nonaktif</Badge>}
                  </div>
                  <p className="mt-0.5 line-clamp-2 text-sm text-muted">{c.description || "Tanpa deskripsi"}</p>
                  <p className="mt-2 text-xs text-muted">{c.products_count ?? 0} produk · urutan {c.sort_order}</p>
                </div>
                <div className="flex gap-1 opacity-100 transition-opacity sm:opacity-0 sm:group-hover:opacity-100">
                  <Button variant="ghost" size="icon" className="size-8" onClick={() => open(c)} aria-label="Edit">
                    <Pencil className="size-4" />
                  </Button>
                  <Button variant="ghost" size="icon" className="size-8 hover:text-red-600" onClick={() => setDeleting(c)} aria-label="Hapus">
                    <Trash2 className="size-4" />
                  </Button>
                </div>
              </div>
            </Card>
          ))}
        </div>
      )}

      <Modal
        open={editing !== undefined}
        onClose={() => setEditing(undefined)}
        title={editing ? "Edit kategori" : "Kategori baru"}
        footer={
          <>
            <Button variant="outline" onClick={() => setEditing(undefined)}>
              Batal
            </Button>
            <Button loading={save.isPending} onClick={() => save.mutate({ id: editing?.id, data: f })}>
              Simpan
            </Button>
          </>
        }
      >
        <div className="space-y-4">
          <Field label="Nama" required error={errors?.field("name")}>
            <Input autoFocus value={f.name} onChange={(e) => setF({ ...f, name: e.target.value })} />
          </Field>
          <Field label="Deskripsi" error={errors?.field("description")}>
            <Textarea rows={2} value={f.description} onChange={(e) => setF({ ...f, description: e.target.value })} />
          </Field>
          <Field label="Warna">
            <div className="flex flex-wrap gap-2">
              {COLORS.map((c) => (
                <button key={c} type="button" onClick={() => setF({ ...f, color: c })} className={`size-8 rounded-full ring-offset-2 ring-offset-surface transition ${f.color === c ? "ring-2 ring-fg" : ""}`} style={{ background: c }} aria-label={c} />
              ))}
            </div>
          </Field>
          <div className="grid grid-cols-2 items-end gap-4">
            <Field label="Urutan tampil">
              <Input type="number" min={0} value={f.sort_order} onChange={(e) => setF({ ...f, sort_order: Number(e.target.value) })} />
            </Field>
            <div className="pb-2">
              <Switch checked={f.is_active} onChange={(v) => setF({ ...f, is_active: v })} label="Aktif" />
            </div>
          </div>
        </div>
      </Modal>

      <ConfirmDialog
        open={!!deleting}
        onClose={() => setDeleting(null)}
        onConfirm={() => deleting && remove.mutate(deleting.id, { onSuccess: () => setDeleting(null) })}
        loading={remove.isPending}
        title="Hapus kategori?"
        description={<>Kategori <b>{deleting?.name}</b> akan dihapus. Kategori yang masih memiliki produk tidak dapat dihapus.</>}
        confirmText="Hapus"
      />
    </RoleGate>
  );
}
