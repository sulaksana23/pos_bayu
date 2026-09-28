"use client";

import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";
import { api, errorMessage } from "@/lib/api";
import type { Shift } from "@/lib/types";
import { cn, dateTime, rupiah, toNumber } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Field, MoneyInput, Textarea } from "@/components/ui/input";
import { Modal } from "@/components/ui/modal";

export function OpenShiftForm({ onDone }: { onDone?: (s: Shift) => void }) {
  const [cash, setCash] = useState(0);
  const [notes, setNotes] = useState("");
  const qc = useQueryClient();
  const open = useMutation({
    mutationFn: () => api.post<{ data: Shift; message: string }>("/shifts/open", { opening_cash: cash, opening_notes: notes || null }),
    onSuccess: (res) => {
      toast.success(res.message);
      qc.invalidateQueries({ queryKey: ["shift"] });
      qc.invalidateQueries({ queryKey: ["shifts"] });
      qc.invalidateQueries({ queryKey: ["me"] });
      onDone?.(res.data);
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  return (
    <form
      className="space-y-4"
      onSubmit={(e) => {
        e.preventDefault();
        open.mutate();
      }}
    >
      <Field label="Modal awal di laci kas" hint="Uang tunai yang ada di laci saat shift dimulai.">
        <MoneyInput value={cash} onChange={setCash} autoFocus />
      </Field>
      <div className="flex flex-wrap gap-2">
        {[0, 100000, 200000, 500000].map((v) => (
          <button key={v} type="button" onClick={() => setCash(v)} className={cn("rounded-lg border px-3 py-1.5 text-xs font-medium", cash === v ? "border-brand-500 bg-brand-500/10 text-brand-700 dark:text-brand-400" : "border-border hover:bg-surface-2")}>
            {rupiah(v)}
          </button>
        ))}
      </div>
      <Field label="Catatan (opsional)">
        <Textarea value={notes} onChange={(e) => setNotes(e.target.value)} rows={2} />
      </Field>
      <Button type="submit" size="lg" className="w-full" loading={open.isPending}>
        Buka shift
      </Button>
    </form>
  );
}

export function CloseShiftModal({ shift, open, onClose }: { shift: Shift; open: boolean; onClose: () => void }) {
  const [cash, setCash] = useState<number>(toNumber(shift.expected_cash));
  const [notes, setNotes] = useState("");
  const qc = useQueryClient();
  const diff = cash - toNumber(shift.expected_cash);

  const close = useMutation({
    mutationFn: () => api.post<{ message: string }>(`/shifts/${shift.id}/close`, { closing_cash: cash, closing_notes: notes || null }),
    onSuccess: (res) => {
      toast.success(res.message);
      qc.invalidateQueries({ queryKey: ["shift"] });
      qc.invalidateQueries({ queryKey: ["shifts"] });
      qc.invalidateQueries({ queryKey: ["dashboard"] });
      onClose();
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  return (
    <Modal
      open={open}
      onClose={onClose}
      title="Tutup shift"
      description={`Dibuka ${dateTime(shift.opened_at)}`}
      footer={
        <>
          <Button variant="outline" onClick={onClose}>
            Batal
          </Button>
          <Button variant="danger" loading={close.isPending} onClick={() => close.mutate()}>
            Tutup shift
          </Button>
        </>
      }
    >
      <div className="space-y-4">
        <div className="grid grid-cols-2 gap-3 text-sm">
          {[
            ["Transaksi", String(shift.transaction_count)],
            ["Total penjualan", rupiah(shift.total_sales)],
            ["Penjualan tunai", rupiah(shift.total_cash)],
            ["Non-tunai", rupiah(shift.total_non_cash)],
            ["Modal awal", rupiah(shift.opening_cash)],
            ["Kas seharusnya", rupiah(shift.expected_cash)],
          ].map(([l, v]) => (
            <div key={l} className="rounded-lg bg-surface-2 px-3 py-2">
              <p className="text-xs text-muted">{l}</p>
              <p className="tabular font-semibold">{v}</p>
            </div>
          ))}
        </div>
        <Field label="Uang tunai aktual di laci">
          <MoneyInput value={cash} onChange={setCash} autoFocus />
        </Field>
        <div className={cn("rounded-lg px-3 py-2.5 text-sm font-medium", diff === 0 ? "bg-emerald-500/10 text-emerald-700 dark:text-emerald-400" : diff > 0 ? "bg-sky-500/10 text-sky-700 dark:text-sky-400" : "bg-red-500/10 text-red-700 dark:text-red-400")}>
          {diff === 0 ? "Kas sesuai ✓" : diff > 0 ? `Lebih ${rupiah(diff)}` : `Kurang ${rupiah(Math.abs(diff))}`}
        </div>
        <Field label="Catatan penutupan (opsional)">
          <Textarea value={notes} onChange={(e) => setNotes(e.target.value)} rows={2} />
        </Field>
      </div>
    </Modal>
  );
}
