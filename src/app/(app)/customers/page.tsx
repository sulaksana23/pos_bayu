"use client";

import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Mail, MapPin, Pencil, Phone, Plus, Trash2, Users } from "lucide-react";
import { api } from "@/lib/api";
import type { Customer } from "@/lib/types";
import { date, dateTime, initials, isBackOffice, number, rupiah } from "@/lib/utils";
import { useAuth } from "@/stores/auth";
import { useCrud } from "@/hooks/use-crud";
import { useList } from "@/hooks/use-list";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Field, Input, Textarea } from "@/components/ui/input";
import { Drawer, Modal } from "@/components/ui/modal";
import { ConfirmDialog, EmptyState, PageHeader, PageLoader, Pagination, SearchInput } from "@/components/ui/misc";
import { Table, Td, Th, Tr } from "@/components/ui/table";
import { TrxStatusBadge } from "@/components/pos/status";

type Form = { name: string; phone: string; email: string; address: string; notes: string };
const empty: Form = { name: "", phone: "", email: "", address: "", notes: "" };

export default function CustomersPage() {
  const backOffice = isBackOffice(useAuth((s) => s.user?.role));
  const [q, setQ] = useState("");
  const [page, setPage] = useState(1);
  const [editing, setEditing] = useState<Customer | null | undefined>(undefined);
  const [deleting, setDeleting] = useState<Customer | null>(null);
  const [detail, setDetail] = useState<number | null>(null);
  const [f, setF] = useState<Form>(empty);
  const { save, remove, errors } = useCrud<Form>("customers", ["customers", "customer"], () => setEditing(undefined));

  const { data, isLoading } = useList<Customer>("customers", "/customers", { q, page, per_page: 20 });
  const customer = useQuery({
    queryKey: ["customer", detail],
    enabled: detail !== null,
    queryFn: async () => (await api.get<{ data: Customer }>(`/customers/${detail}`)).data,
  });

  const open = (c: Customer | null) => {
    setF(c ? { name: c.name, phone: c.phone ?? "", email: c.email ?? "", address: c.address ?? "", notes: c.notes ?? "" } : empty);
    setEditing(c);
  };
  const c = customer.data;

  return (
    <>
      <PageHeader
        title="Pelanggan"
        description="Data pelanggan, riwayat belanja, dan loyalitas."
        actions={
          <Button onClick={() => open(null)}>
            <Plus className="size-4" /> Tambah pelanggan
          </Button>
        }
      />
      <Card>
        <div className="border-b border-border p-4">
          <SearchInput value={q} onChange={(v) => { setQ(v); setPage(1); }} placeholder="Cari nama, telepon, email…" className="w-full sm:w-80" />
        </div>
        {isLoading ? (
          <PageLoader />
        ) : !data?.data.length ? (
          <EmptyState icon={Users} title="Belum ada pelanggan" />
        ) : (
          <>
            <Table>
              <thead>
                <tr>
                  <Th>Pelanggan</Th>
                  <Th className="hidden md:table-cell">Kontak</Th>
                  <Th className="text-right">Kunjungan</Th>
                  <Th className="text-right">Total belanja</Th>
                  <Th className="w-24" />
                </tr>
              </thead>
              <tbody>
                {data.data.map((cu) => (
                  <Tr key={cu.id} onClick={() => setDetail(cu.id)}>
                    <Td>
                      <div className="flex items-center gap-3">
                        <span className="grid size-9 shrink-0 place-items-center rounded-full bg-sky-500/10 text-xs font-semibold text-sky-700 dark:text-sky-400">{initials(cu.name)}</span>
                        <div className="min-w-0">
                          <p className="truncate font-medium">{cu.name}</p>
                          <p className="text-xs text-muted">Sejak {date(cu.created_at)}</p>
                        </div>
                      </div>
                    </Td>
                    <Td className="hidden text-sm text-muted md:table-cell">
                      {cu.phone && <p>{cu.phone}</p>}
                      {cu.email && <p className="text-xs">{cu.email}</p>}
                    </Td>
                    <Td className="tabular text-right">{number(cu.visit_count)}</Td>
                    <Td className="tabular text-right font-medium">{rupiah(cu.total_spent)}</Td>
                    <Td onClick={(e) => e.stopPropagation()}>
                      <div className="flex justify-end gap-1">
                        <Button variant="ghost" size="icon" className="size-8" onClick={() => open(cu)} aria-label="Edit">
                          <Pencil className="size-4" />
                        </Button>
                        {backOffice && (
                          <Button variant="ghost" size="icon" className="size-8 hover:text-red-600" onClick={() => setDeleting(cu)} aria-label="Hapus">
                            <Trash2 className="size-4" />
                          </Button>
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
        title={editing ? "Edit pelanggan" : "Pelanggan baru"}
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
          <Field label="Nama" required error={errors?.field("name")} className="sm:col-span-2">
            <Input autoFocus value={f.name} onChange={(e) => setF({ ...f, name: e.target.value })} />
          </Field>
          <Field label="Telepon" error={errors?.field("phone")}>
            <Input inputMode="tel" value={f.phone} onChange={(e) => setF({ ...f, phone: e.target.value })} />
          </Field>
          <Field label="Email" error={errors?.field("email")}>
            <Input type="email" value={f.email} onChange={(e) => setF({ ...f, email: e.target.value })} />
          </Field>
          <Field label="Alamat" className="sm:col-span-2" error={errors?.field("address")}>
            <Textarea rows={2} value={f.address} onChange={(e) => setF({ ...f, address: e.target.value })} />
          </Field>
          <Field label="Catatan" className="sm:col-span-2">
            <Textarea rows={2} value={f.notes} onChange={(e) => setF({ ...f, notes: e.target.value })} />
          </Field>
        </div>
      </Modal>

      <Drawer open={detail !== null} onClose={() => setDetail(null)} title={c?.name ?? "Pelanggan"} description={c ? `Pelanggan sejak ${date(c.created_at)}` : undefined}>
        {!c ? (
          <PageLoader />
        ) : (
          <div className="space-y-5">
            <div className="grid grid-cols-3 gap-3 text-sm">
              {[
                ["Transaksi", number(c.transactions_count)],
                ["Kunjungan", number(c.visit_count)],
                ["Total belanja", rupiah(c.total_spent)],
              ].map(([l, v]) => (
                <div key={l} className="rounded-lg bg-surface-2 px-3 py-2">
                  <p className="text-xs text-muted">{l}</p>
                  <p className="tabular font-semibold">{v}</p>
                </div>
              ))}
            </div>
            <div className="space-y-2 text-sm">
              {c.phone && <p className="flex items-center gap-2"><Phone className="size-4 text-muted" /> {c.phone}</p>}
              {c.email && <p className="flex items-center gap-2"><Mail className="size-4 text-muted" /> {c.email}</p>}
              {c.address && <p className="flex items-center gap-2"><MapPin className="size-4 text-muted" /> {c.address}</p>}
              {c.notes && <p className="rounded-lg bg-surface-2 px-3 py-2 text-muted">{c.notes}</p>}
            </div>
            <div>
              <p className="mb-2 text-sm font-semibold">Transaksi terakhir</p>
              <ul className="divide-y divide-border rounded-lg border border-border">
                {c.transactions?.map((t) => (
                  <li key={t.id} className="flex items-center gap-3 px-3 py-2 text-sm">
                    <div className="min-w-0 flex-1">
                      <p className="font-mono text-xs">{t.invoice_no}</p>
                      <p className="text-xs text-muted">{dateTime(t.created_at)}</p>
                    </div>
                    <TrxStatusBadge status={t.status} />
                    <span className="tabular font-medium">{rupiah(t.total)}</span>
                  </li>
                ))}
                {!c.transactions?.length && <li className="px-3 py-6 text-center text-sm text-muted">Belum ada transaksi</li>}
              </ul>
            </div>
          </div>
        )}
      </Drawer>

      <ConfirmDialog
        open={!!deleting}
        onClose={() => setDeleting(null)}
        onConfirm={() => deleting && remove.mutate(deleting.id, { onSuccess: () => setDeleting(null) })}
        loading={remove.isPending}
        title="Hapus pelanggan?"
        description={<>Pelanggan <b>{deleting?.name}</b> akan dihapus permanen. Pelanggan dengan riwayat transaksi tidak dapat dihapus.</>}
        confirmText="Hapus"
      />
    </>
  );
}
