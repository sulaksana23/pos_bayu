"use client";

import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { Pencil, Plus, Power, Trash2, UserCog } from "lucide-react";
import { toast } from "sonner";
import { api, errorMessage } from "@/lib/api";
import type { Role, User } from "@/lib/types";
import { date, initials, ROLE_LABELS } from "@/lib/utils";
import { useAuth } from "@/stores/auth";
import { useCrud } from "@/hooks/use-crud";
import { useList } from "@/hooks/use-list";
import { Card } from "@/components/ui/card";
import { Badge, type Tone } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Field, Input, Select, Switch } from "@/components/ui/input";
import { Modal } from "@/components/ui/modal";
import { ConfirmDialog, EmptyState, PageHeader, PageLoader, Pagination, SearchInput, Tabs } from "@/components/ui/misc";
import { Table, Td, Th, Tr } from "@/components/ui/table";
import { RoleGate } from "@/components/layout/auth-guard";

const ROLE_TONE: Record<Role, Tone> = { superadministrator: "violet", admin: "red", manager: "blue", cashier: "green" };

type Form = { name: string; email: string; phone: string; role: Role; pin: string; password: string; password_confirmation: string; is_active: boolean };
const empty: Form = { name: "", email: "", phone: "", role: "cashier", pin: "", password: "", password_confirmation: "", is_active: true };

export default function UsersPage() {
  const qc = useQueryClient();
  const me = useAuth((s) => s.user);
  const [q, setQ] = useState("");
  const [role, setRole] = useState<Role | "">("");
  const [page, setPage] = useState(1);
  const [editing, setEditing] = useState<User | null | undefined>(undefined);
  const [deleting, setDeleting] = useState<User | null>(null);
  const [f, setF] = useState<Form>(empty);
  const { save, remove, errors } = useCrud<Form>("users", ["users"], () => setEditing(undefined));
  const { data, isLoading } = useList<User>("users", "/users", { q, role, page, per_page: 20 });

  const toggle = useMutation({
    mutationFn: (id: number) => api.patch<{ message: string }>(`/users/${id}/toggle`),
    onSuccess: (res) => {
      toast.success(res.message);
      qc.invalidateQueries({ queryKey: ["users"] });
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  const open = (u: User | null) => {
    setF(u ? { ...empty, name: u.name, email: u.email, phone: u.phone ?? "", role: u.role, is_active: u.is_active ?? true } : empty);
    setEditing(u);
  };
  const roles: Role[] = me?.role === "superadministrator" ? ["superadministrator", "admin", "manager", "cashier"] : ["admin", "manager", "cashier"];
  const self = editing?.id === me?.id;

  return (
    <RoleGate roles={["admin"]}>
      <PageHeader
        title="Pengguna"
        description="Kelola akun staf dan hak akses."
        actions={
          <Button onClick={() => open(null)}>
            <Plus className="size-4" /> Tambah pengguna
          </Button>
        }
      />
      <Card>
        <div className="flex flex-wrap items-center gap-2 border-b border-border p-4">
          <SearchInput value={q} onChange={(v) => { setQ(v); setPage(1); }} placeholder="Cari nama, email, telepon…" className="w-full sm:w-72" />
          <div className="sm:ml-auto">
            <Tabs
              value={role}
              onChange={(v) => { setRole(v); setPage(1); }}
              items={[{ value: "", label: "Semua" }, { value: "admin", label: "Admin" }, { value: "manager", label: "Manajer" }, { value: "cashier", label: "Kasir" }]}
            />
          </div>
        </div>
        {isLoading ? (
          <PageLoader />
        ) : !data?.data.length ? (
          <EmptyState icon={UserCog} title="Tidak ada pengguna" />
        ) : (
          <>
            <Table>
              <thead>
                <tr><Th>Pengguna</Th><Th>Peran</Th><Th className="hidden md:table-cell">Telepon</Th><Th className="hidden md:table-cell">Terdaftar</Th><Th>Status</Th><Th className="w-28" /></tr>
              </thead>
              <tbody>
                {data.data.map((u) => (
                  <Tr key={u.id}>
                    <Td>
                      <div className="flex items-center gap-3">
                        <span className="grid size-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-slate-600 to-slate-800 text-xs font-semibold text-white">{initials(u.name)}</span>
                        <div className="min-w-0">
                          <p className="truncate font-medium">{u.name} {u.id === me?.id && <span className="text-xs text-muted">(Anda)</span>}</p>
                          <p className="truncate text-xs text-muted">{u.email}</p>
                        </div>
                      </div>
                    </Td>
                    <Td><Badge tone={ROLE_TONE[u.role]}>{ROLE_LABELS[u.role]}</Badge></Td>
                    <Td className="hidden text-muted md:table-cell">{u.phone || "-"}</Td>
                    <Td className="hidden text-muted md:table-cell">{date(u.created_at)}</Td>
                    <Td><Badge tone={u.is_active ? "green" : "gray"} dot>{u.is_active ? "Aktif" : "Nonaktif"}</Badge></Td>
                    <Td>
                      <div className="flex justify-end gap-1">
                        <Button variant="ghost" size="icon" className="size-8" onClick={() => open(u)} aria-label="Edit"><Pencil className="size-4" /></Button>
                        {u.id !== me?.id && (
                          <>
                            <Button variant="ghost" size="icon" className="size-8" title={u.is_active ? "Nonaktifkan" : "Aktifkan"} onClick={() => toggle.mutate(u.id)}><Power className="size-4" /></Button>
                            <Button variant="ghost" size="icon" className="size-8 hover:text-red-600" onClick={() => setDeleting(u)} aria-label="Hapus"><Trash2 className="size-4" /></Button>
                          </>
                        )}
                      </div>
                    </Td>
                  </Tr>
                ))}
              </tbody>
            </Table>
            <Pagination page={data.current_page} lastPage={data.last_page} total={data.total} onPage={setPage} />
          </>
        )}
      </Card>

      <Modal
        open={editing !== undefined}
        onClose={() => setEditing(undefined)}
        title={editing ? "Edit pengguna" : "Pengguna baru"}
        footer={
          <>
            <Button variant="outline" onClick={() => setEditing(undefined)}>Batal</Button>
            <Button loading={save.isPending} onClick={() => save.mutate({ id: editing?.id, data: f })}>Simpan</Button>
          </>
        }
      >
        <div className="grid gap-4 sm:grid-cols-2">
          <Field label="Nama" required error={errors?.field("name")} className="sm:col-span-2">
            <Input autoFocus value={f.name} onChange={(e) => setF({ ...f, name: e.target.value })} />
          </Field>
          <Field label="Email" required error={errors?.field("email")}>
            <Input type="email" value={f.email} onChange={(e) => setF({ ...f, email: e.target.value })} />
          </Field>
          <Field label="Telepon" error={errors?.field("phone")}>
            <Input value={f.phone} onChange={(e) => setF({ ...f, phone: e.target.value })} />
          </Field>
          <Field label="Peran" required error={errors?.field("role")} hint={self ? "Anda tidak dapat mengubah peran sendiri" : undefined}>
            <Select value={f.role} disabled={self} onChange={(e) => setF({ ...f, role: e.target.value as Role })}>
              {roles.map((r) => <option key={r} value={r}>{ROLE_LABELS[r]}</option>)}
            </Select>
          </Field>
          <Field label="PIN (4–6 digit)" error={errors?.field("pin")} hint={editing ? "Kosongkan jika tidak diubah" : undefined}>
            <Input inputMode="numeric" maxLength={6} value={f.pin} onChange={(e) => setF({ ...f, pin: e.target.value.replace(/\D/g, "") })} />
          </Field>
          <Field label={editing ? "Password baru" : "Password"} required={!editing} error={errors?.field("password")} hint={editing ? "Kosongkan jika tidak diubah" : "Minimal 8 karakter"}>
            <Input type="password" autoComplete="new-password" value={f.password} onChange={(e) => setF({ ...f, password: e.target.value })} />
          </Field>
          <Field label="Konfirmasi password">
            <Input type="password" autoComplete="new-password" value={f.password_confirmation} onChange={(e) => setF({ ...f, password_confirmation: e.target.value })} />
          </Field>
          {!self && <Switch checked={f.is_active} onChange={(v) => setF({ ...f, is_active: v })} label="Akun aktif" />}
        </div>
      </Modal>

      <ConfirmDialog
        open={!!deleting}
        onClose={() => setDeleting(null)}
        onConfirm={() => deleting && remove.mutate(deleting.id, { onSuccess: () => setDeleting(null) })}
        loading={remove.isPending}
        title="Hapus pengguna?"
        description={<>Akun <b>{deleting?.name}</b> akan dihapus. Pengguna yang memiliki riwayat transaksi/shift tidak dapat dihapus — nonaktifkan saja.</>}
        confirmText="Hapus"
      />
    </RoleGate>
  );
}
