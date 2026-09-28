"use client";

import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { Building2, Mail, Pencil, Phone, Plus, Power, Trash2, Truck, User } from "lucide-react";
import { toast } from "sonner";
import { api, errorMessage } from "@/lib/api";
import type { Supplier } from "@/lib/types";
import { useCrud } from "@/hooks/use-crud";
import { useList } from "@/hooks/use-list";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Field, Input, Textarea } from "@/components/ui/input";
import { Modal } from "@/components/ui/modal";
import { ConfirmDialog, EmptyState, PageHeader, PageLoader, Pagination, SearchInput } from "@/components/ui/misc";
import { RoleGate } from "@/components/layout/auth-guard";

type Form = { name: string; company: string; phone: string; email: string; address: string; pic_name: string; pic_phone: string; tax_id: string; notes: string };
const empty: Form = { name: "", company: "", phone: "", email: "", address: "", pic_name: "", pic_phone: "", tax_id: "", notes: "" };

export default function SuppliersPage() {
  const qc = useQueryClient();
  const [q, setQ] = useState("");
  const [page, setPage] = useState(1);
  const [editing, setEditing] = useState<Supplier | null | undefined>(undefined);
  const [deleting, setDeleting] = useState<Supplier | null>(null);
  const [f, setF] = useState<Form>(empty);
  const { save, remove, errors } = useCrud<Form>("suppliers", ["suppliers"], () => setEditing(undefined));
  const { data, isLoading } = useList<Supplier>("suppliers", "/suppliers", { q, page, per_page: 18 });

  const toggle = useMutation({
    mutationFn: (id: number) => api.patch<{ message: string }>(`/suppliers/${id}/toggle`),
    onSuccess: (res) => {
      toast.success(res.message);
      qc.invalidateQueries({ queryKey: ["suppliers"] });
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  const open = (s: Supplier | null) => {
    setF(s ? (Object.fromEntries(Object.keys(empty).map((k) => [k, (s[k as keyof Supplier] as string) ?? ""])) as Form) : empty);
    setEditing(s);
  };
  const input = (k: keyof Form, props: React.InputHTMLAttributes<HTMLInputElement> = {}) => (
    <Input value={f[k]} onChange={(e) => setF({ ...f, [k]: e.target.value })} {...props} />
  );

  return (
    <RoleGate roles={["admin", "manager"]}>
      <PageHeader
        title="Supplier"
        description="Daftar pemasok barang untuk purchase order."
        actions={
          <Button onClick={() => open(null)}>
            <Plus className="size-4" /> Tambah supplier
          </Button>
        }
      />
      <div className="mb-4">
        <SearchInput value={q} onChange={(v) => { setQ(v); setPage(1); }} placeholder="Cari supplier…" className="w-full sm:w-80" />
      </div>
      {isLoading ? (
        <PageLoader />
      ) : !data?.data.length ? (
        <Card>
          <EmptyState icon={Truck} title="Belum ada supplier" action={<Button onClick={() => open(null)}>Tambah supplier</Button>} />
        </Card>
      ) : (
        <>
          <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            {data.data.map((s) => (
              <Card key={s.id} className={`flex flex-col p-5 ${s.is_active ? "" : "opacity-60"}`}>
                <div className="flex items-start gap-3">
                  <div className="grid size-11 shrink-0 place-items-center rounded-xl bg-violet-500/10 text-violet-600">
                    <Building2 className="size-5" />
                  </div>
                  <div className="min-w-0 flex-1">
                    <p className="truncate font-semibold">{s.name}</p>
                    <p className="truncate text-sm text-muted">{s.company || "—"}</p>
                  </div>
                  <Badge tone={s.is_active ? "green" : "gray"}>{s.is_active ? "Aktif" : "Nonaktif"}</Badge>
                </div>
                <div className="mt-4 flex-1 space-y-1.5 text-sm text-muted">
                  {s.phone && <p className="flex items-center gap-2"><Phone className="size-3.5" /> {s.phone}</p>}
                  {s.email && <p className="flex items-center gap-2 truncate"><Mail className="size-3.5" /> {s.email}</p>}
                  {s.pic_name && <p className="flex items-center gap-2"><User className="size-3.5" /> {s.pic_name} {s.pic_phone && `· ${s.pic_phone}`}</p>}
                </div>
                <div className="mt-4 flex items-center justify-between border-t border-border pt-3">
                  <span className="text-xs text-muted">{s.purchase_orders_count ?? 0} purchase order</span>
                  <div className="flex gap-1">
                    <Button variant="ghost" size="icon" className="size-8" title={s.is_active ? "Nonaktifkan" : "Aktifkan"} onClick={() => toggle.mutate(s.id)}>
                      <Power className="size-4" />
                    </Button>
                    <Button variant="ghost" size="icon" className="size-8" onClick={() => open(s)} aria-label="Edit">
                      <Pencil className="size-4" />
                    </Button>
                    <Button variant="ghost" size="icon" className="size-8 hover:text-red-600" onClick={() => setDeleting(s)} aria-label="Hapus">
                      <Trash2 className="size-4" />
                    </Button>
                  </div>
                </div>
              </Card>
            ))}
          </div>
          <Card className="mt-4">
            <Pagination page={data.current_page} lastPage={data.last_page} total={data.total} onPage={setPage} />
          </Card>
        </>
      )}

      <Modal
        open={editing !== undefined}
        onClose={() => setEditing(undefined)}
        size="lg"
        title={editing ? "Edit supplier" : "Supplier baru"}
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
        <div className="grid gap-4 sm:grid-cols-2">
          <Field label="Nama supplier" required error={errors?.field("name")}>{input("name", { autoFocus: true })}</Field>
          <Field label="Perusahaan" error={errors?.field("company")}>{input("company")}</Field>
          <Field label="Telepon" error={errors?.field("phone")}>{input("phone", { inputMode: "tel" })}</Field>
          <Field label="Email" error={errors?.field("email")}>{input("email", { type: "email" })}</Field>
          <Field label="Nama PIC">{input("pic_name")}</Field>
          <Field label="Telepon PIC">{input("pic_phone", { inputMode: "tel" })}</Field>
          <Field label="NPWP" error={errors?.field("tax_id")}>{input("tax_id")}</Field>
          <Field label="Alamat" className="sm:col-span-2">
            <Textarea rows={2} value={f.address} onChange={(e) => setF({ ...f, address: e.target.value })} />
          </Field>
          <Field label="Catatan" className="sm:col-span-2">
            <Textarea rows={2} value={f.notes} onChange={(e) => setF({ ...f, notes: e.target.value })} />
          </Field>
        </div>
      </Modal>

      <ConfirmDialog
        open={!!deleting}
        onClose={() => setDeleting(null)}
        onConfirm={() => deleting && remove.mutate(deleting.id, { onSuccess: () => setDeleting(null) })}
        loading={remove.isPending}
        title="Hapus supplier?"
        description={<>Supplier <b>{deleting?.name}</b> akan dihapus. Supplier yang memiliki purchase order tidak dapat dihapus — nonaktifkan saja.</>}
        confirmText="Hapus"
      />
    </RoleGate>
  );
}
