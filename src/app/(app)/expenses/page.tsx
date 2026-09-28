"use client";

import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { Download, Paperclip, Pencil, Plus, Trash2, Wallet } from "lucide-react";
import { toast } from "sonner";
import { api, ApiError, errorMessage } from "@/lib/api";
import type { Expense, ExpenseCategory } from "@/lib/types";
import { assetUrl } from "@/lib/config";
import { date, downloadCsv, EXPENSE_LABELS, isoDate, rupiah, startOfMonth, toNumber } from "@/lib/utils";
import { useList } from "@/hooks/use-list";
import { Card } from "@/components/ui/card";
import { Badge, type Tone } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Field, Input, MoneyInput, Select, Textarea } from "@/components/ui/input";
import { Modal } from "@/components/ui/modal";
import { ConfirmDialog, EmptyState, PageHeader, PageLoader, Pagination, SearchInput } from "@/components/ui/misc";
import { Table, Td, Th, Tr } from "@/components/ui/table";
import { RoleGate } from "@/components/layout/auth-guard";

const TONES: Record<ExpenseCategory, Tone> = { operational: "blue", utilities: "amber", rent: "violet", salary: "green", maintenance: "gray", marketing: "red", other: "gray" };

function ExpenseForm({ expense, onClose }: { expense: Expense | null; onClose: () => void }) {
  const qc = useQueryClient();
  const [err, setErr] = useState<ApiError | null>(null);
  const [file, setFile] = useState<File | null>(null);
  const [removeReceipt, setRemoveReceipt] = useState(false);
  const [f, setF] = useState({
    category: expense?.category ?? ("operational" as ExpenseCategory),
    amount: toNumber(expense?.amount),
    description: expense?.description ?? "",
    expense_date: expense?.expense_date?.slice(0, 10) ?? isoDate(),
    payment_method: expense?.payment_method ?? "cash",
    notes: expense?.notes ?? "",
  });

  const save = useMutation({
    mutationFn: () => {
      const fd = new FormData();
      Object.entries(f).forEach(([k, v]) => fd.append(k, String(v)));
      if (file) fd.append("receipt_image", file);
      if (removeReceipt) fd.append("delete_receipt", "1");
      return expense ? api.put<{ message: string }>(`/expenses/${expense.id}`, fd) : api.post<{ message: string }>("/expenses", fd);
    },
    onSuccess: (res) => {
      toast.success(res.message);
      qc.invalidateQueries({ queryKey: ["expenses"] });
      qc.invalidateQueries({ queryKey: ["dashboard"] });
      onClose();
    },
    onError: (e) => {
      if (e instanceof ApiError) setErr(e);
      toast.error(errorMessage(e));
    },
  });

  return (
    <Modal
      open
      onClose={onClose}
      title={expense ? `Edit ${expense.expense_no}` : "Catat pengeluaran"}
      footer={
        <>
          <Button variant="outline" onClick={onClose}>Batal</Button>
          <Button loading={save.isPending} onClick={() => save.mutate()}>Simpan</Button>
        </>
      }
    >
      <div className="grid gap-4 sm:grid-cols-2">
        <Field label="Kategori" required error={err?.field("category")}>
          <Select value={f.category} onChange={(e) => setF({ ...f, category: e.target.value as ExpenseCategory })}>
            {Object.entries(EXPENSE_LABELS).map(([k, v]) => (
              <option key={k} value={k}>{v}</option>
            ))}
          </Select>
        </Field>
        <Field label="Tanggal" required error={err?.field("expense_date")}>
          <Input type="date" value={f.expense_date} onChange={(e) => setF({ ...f, expense_date: e.target.value })} />
        </Field>
        <Field label="Jumlah" required error={err?.field("amount")}>
          <MoneyInput value={f.amount} onChange={(v) => setF({ ...f, amount: v })} autoFocus />
        </Field>
        <Field label="Metode bayar">
          <Select value={f.payment_method} onChange={(e) => setF({ ...f, payment_method: e.target.value })}>
            <option value="cash">Tunai</option>
            <option value="transfer">Transfer</option>
            <option value="qris">QRIS</option>
            <option value="wallet">E-Wallet</option>
          </Select>
        </Field>
        <Field label="Keterangan" required error={err?.field("description")} className="sm:col-span-2">
          <Input value={f.description} onChange={(e) => setF({ ...f, description: e.target.value })} placeholder="Contoh: Bayar listrik bulan Juli" />
        </Field>
        <Field label="Bukti pembayaran" error={err?.field("receipt_image")} className="sm:col-span-2" hint="JPG/PNG/WEBP, maks. 2 MB">
          <Input type="file" accept="image/*" onChange={(e) => setFile(e.target.files?.[0] ?? null)} className="py-2 file:mr-3 file:rounded-md file:border-0 file:bg-surface-2 file:px-2 file:py-1 file:text-xs" />
        </Field>
        {expense?.receipt_url && !file && (
          <label className="flex items-center gap-2 text-sm sm:col-span-2">
            <input type="checkbox" checked={removeReceipt} onChange={(e) => setRemoveReceipt(e.target.checked)} /> Hapus bukti yang ada
          </label>
        )}
        <Field label="Catatan" className="sm:col-span-2">
          <Textarea rows={2} value={f.notes} onChange={(e) => setF({ ...f, notes: e.target.value })} />
        </Field>
      </div>
    </Modal>
  );
}

export default function ExpensesPage() {
  const qc = useQueryClient();
  const [q, setQ] = useState("");
  const [category, setCategory] = useState("");
  const [from, setFrom] = useState(startOfMonth());
  const [to, setTo] = useState(isoDate());
  const [page, setPage] = useState(1);
  const [editing, setEditing] = useState<Expense | null | undefined>(undefined);
  const [deleting, setDeleting] = useState<Expense | null>(null);

  const params = { q, category, date_from: from, date_to: to, page, per_page: 20 };
  const { data, isLoading } = useList<Expense, { total_amount: number }>("expenses", "/expenses", params);

  const del = useMutation({
    mutationFn: (id: number) => api.delete<{ message: string }>(`/expenses/${id}`),
    onSuccess: (res) => {
      toast.success(res.message);
      setDeleting(null);
      qc.invalidateQueries({ queryKey: ["expenses"] });
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  const exportCsv = async () => {
    const all = await api.get<{ data: Expense[] }>("/expenses", { ...params, page: 1, per_page: 200 });
    downloadCsv(`pengeluaran_${from}_${to}.csv`, ["No", "Tanggal", "Kategori", "Keterangan", "Metode", "Jumlah", "Dicatat oleh"], all.data.map((e) => [e.expense_no, e.expense_date?.slice(0, 10), EXPENSE_LABELS[e.category] ?? e.category, e.description, e.payment_method, toNumber(e.amount), e.user?.name]));
  };

  return (
    <RoleGate roles={["admin", "manager"]}>
      <PageHeader
        title="Pengeluaran"
        description="Catat biaya operasional agar laba bersih akurat."
        actions={
          <>
            <Button variant="outline" onClick={exportCsv}>
              <Download className="size-4" /> Ekspor
            </Button>
            <Button onClick={() => setEditing(null)}>
              <Plus className="size-4" /> Catat pengeluaran
            </Button>
          </>
        }
      />
      <Card>
        <div className="flex flex-wrap items-center gap-2 border-b border-border p-4">
          <SearchInput value={q} onChange={(v) => { setQ(v); setPage(1); }} placeholder="Cari keterangan…" className="w-full sm:w-64" />
          <Select value={category} onChange={(e) => { setCategory(e.target.value); setPage(1); }} className="w-auto">
            <option value="">Semua kategori</option>
            {Object.entries(EXPENSE_LABELS).map(([k, v]) => (
              <option key={k} value={k}>{v}</option>
            ))}
          </Select>
          <Input type="date" value={from} onChange={(e) => { setFrom(e.target.value); setPage(1); }} className="w-auto" />
          <span className="text-muted">–</span>
          <Input type="date" value={to} onChange={(e) => { setTo(e.target.value); setPage(1); }} className="w-auto" />
          {data && (
            <div className="rounded-lg bg-red-500/10 px-3 py-2 text-sm sm:ml-auto">
              <span className="text-muted">Total: </span>
              <span className="tabular font-semibold text-red-700 dark:text-red-400">{rupiah(data.total_amount)}</span>
            </div>
          )}
        </div>
        {isLoading ? (
          <PageLoader />
        ) : !data?.data.length ? (
          <EmptyState icon={Wallet} title="Tidak ada pengeluaran" description="Belum ada pengeluaran pada periode ini." />
        ) : (
          <>
            <Table>
              <thead>
                <tr>
                  <Th>Tanggal</Th>
                  <Th>Keterangan</Th>
                  <Th className="hidden sm:table-cell">Kategori</Th>
                  <Th className="hidden md:table-cell">Dicatat</Th>
                  <Th className="text-right">Jumlah</Th>
                  <Th className="w-28" />
                </tr>
              </thead>
              <tbody>
                {data.data.map((e) => {
                  const receipt = assetUrl(e.receipt_image ? `/storage/${e.receipt_image}` : null);
                  return (
                    <Tr key={e.id}>
                      <Td className="text-sm whitespace-nowrap text-muted">{date(e.expense_date)}</Td>
                      <Td>
                        <p className="font-medium">{e.description}</p>
                        <p className="font-mono text-xs text-muted">{e.expense_no}</p>
                      </Td>
                      <Td className="hidden sm:table-cell"><Badge tone={TONES[e.category] ?? "gray"}>{EXPENSE_LABELS[e.category] ?? e.category}</Badge></Td>
                      <Td className="hidden text-sm text-muted md:table-cell">{e.user?.name}</Td>
                      <Td className="tabular text-right font-semibold whitespace-nowrap">{rupiah(e.amount)}</Td>
                      <Td>
                        <div className="flex justify-end gap-1">
                          {receipt && (
                            <a href={receipt} target="_blank" rel="noreferrer" title="Lihat bukti">
                              <Button variant="ghost" size="icon" className="size-8"><Paperclip className="size-4" /></Button>
                            </a>
                          )}
                          <Button variant="ghost" size="icon" className="size-8" onClick={() => setEditing(e)} aria-label="Edit"><Pencil className="size-4" /></Button>
                          <Button variant="ghost" size="icon" className="size-8 hover:text-red-600" onClick={() => setDeleting(e)} aria-label="Hapus"><Trash2 className="size-4" /></Button>
                        </div>
                      </Td>
                    </Tr>
                  );
                })}
              </tbody>
            </Table>
            <Pagination page={data.current_page} lastPage={data.last_page} total={data.total} onPage={setPage} />
          </>
        )}
      </Card>

      {editing !== undefined && <ExpenseForm expense={editing} onClose={() => setEditing(undefined)} />}
      <ConfirmDialog
        open={!!deleting}
        onClose={() => setDeleting(null)}
        onConfirm={() => deleting && del.mutate(deleting.id)}
        loading={del.isPending}
        title="Hapus pengeluaran?"
        description={<>Pengeluaran <b>{deleting?.description}</b> ({rupiah(deleting?.amount)}) akan dihapus permanen.</>}
        confirmText="Hapus"
      />
    </RoleGate>
  );
}
