"use client";

import { useState } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Phone, UserPlus, Users } from "lucide-react";
import { toast } from "sonner";
import { api, ApiError, errorMessage } from "@/lib/api";
import type { Customer, Paginated } from "@/lib/types";
import { initials, rupiah } from "@/lib/utils";
import { Modal } from "@/components/ui/modal";
import { EmptyState, SearchInput, Spinner } from "@/components/ui/misc";
import { Button } from "@/components/ui/button";
import { Field, Input } from "@/components/ui/input";

export function CustomerPicker({ open, onClose, onSelect }: { open: boolean; onClose: () => void; onSelect: (c: Customer | null) => void }) {
  const [q, setQ] = useState("");
  const [creating, setCreating] = useState(false);
  const [form, setForm] = useState({ name: "", phone: "" });
  const [err, setErr] = useState<ApiError | null>(null);
  const qc = useQueryClient();

  const { data, isFetching } = useQuery({
    queryKey: ["customers", "picker", q],
    enabled: open,
    queryFn: () => api.get<Paginated<Customer>>("/customers", { q, per_page: 20 }),
  });

  const create = useMutation({
    mutationFn: () => api.post<{ data: Customer }>("/customers", form),
    onSuccess: (res) => {
      toast.success("Pelanggan ditambahkan");
      qc.invalidateQueries({ queryKey: ["customers"] });
      onSelect(res.data);
      setCreating(false);
      setForm({ name: "", phone: "" });
      onClose();
    },
    onError: (e) => (e instanceof ApiError ? setErr(e) : toast.error(errorMessage(e))),
  });

  return (
    <Modal open={open} onClose={onClose} title={creating ? "Pelanggan baru" : "Pilih pelanggan"} size="md">
      {creating ? (
        <form
          className="space-y-4"
          onSubmit={(e) => {
            e.preventDefault();
            setErr(null);
            create.mutate();
          }}
        >
          <Field label="Nama" required error={err?.field("name")}>
            <Input autoFocus value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
          </Field>
          <Field label="No. telepon" error={err?.field("phone")}>
            <Input inputMode="tel" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} placeholder="08xxxxxxxxxx" />
          </Field>
          <div className="flex justify-end gap-2">
            <Button variant="outline" onClick={() => setCreating(false)}>
              Kembali
            </Button>
            <Button type="submit" loading={create.isPending}>
              Simpan & pilih
            </Button>
          </div>
        </form>
      ) : (
        <div className="space-y-3">
          <div className="flex gap-2">
            <SearchInput value={q} onChange={setQ} placeholder="Cari nama atau nomor telepon…" className="flex-1" autoFocus />
            <Button variant="outline" onClick={() => setCreating(true)}>
              <UserPlus className="size-4" /> Baru
            </Button>
          </div>
          <button
            onClick={() => {
              onSelect(null);
              onClose();
            }}
            className="flex w-full items-center gap-3 rounded-lg border border-dashed border-border px-3 py-2.5 text-left text-sm text-muted hover:bg-surface-2"
          >
            <Users className="size-4" /> Pelanggan umum (tanpa data)
          </button>
          <div className="max-h-80 space-y-1 overflow-y-auto">
            {isFetching && !data ? (
              <div className="flex justify-center py-8">
                <Spinner />
              </div>
            ) : data?.data.length === 0 ? (
              <EmptyState title="Pelanggan tidak ditemukan" action={<Button size="sm" onClick={() => { setForm({ name: q, phone: "" }); setCreating(true); }}>Tambah &quot;{q}&quot;</Button>} />
            ) : (
              data?.data.map((c) => (
                <button
                  key={c.id}
                  onClick={() => {
                    onSelect(c);
                    onClose();
                  }}
                  className="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-surface-2"
                >
                  <span className="grid size-9 place-items-center rounded-full bg-sky-500/10 text-xs font-semibold text-sky-700 dark:text-sky-400">{initials(c.name)}</span>
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-sm font-medium">{c.name}</p>
                    <p className="flex items-center gap-1 text-xs text-muted">
                      {c.phone && (
                        <>
                          <Phone className="size-3" /> {c.phone} ·{" "}
                        </>
                      )}
                      {c.visit_count}x kunjungan
                    </p>
                  </div>
                  <span className="tabular text-xs text-muted">{rupiah(c.total_spent)}</span>
                </button>
              ))
            )}
          </div>
        </div>
      )}
    </Modal>
  );
}
