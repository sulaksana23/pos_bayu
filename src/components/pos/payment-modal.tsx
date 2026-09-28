"use client";

import { useMemo, useState } from "react";
import { Check } from "lucide-react";
import type { PaymentMethod } from "@/lib/types";
import { cn, PAYMENT_LABELS, rupiah } from "@/lib/utils";
import { Modal } from "@/components/ui/modal";
import { Button } from "@/components/ui/button";
import { Field, MoneyInput, Textarea } from "@/components/ui/input";
import { PAYMENT_ICONS } from "./status";

const METHODS: PaymentMethod[] = ["cash", "qris", "transfer", "wallet"];

/** Mount this only while open so its state starts fresh for each payment. */

/** Suggest sensible cash notes above the total (exact, next 5k/10k/50k/100k). */
function quickCash(total: number) {
  const steps = [1000, 5000, 10000, 20000, 50000, 100000];
  const set = new Set<number>([total]);
  for (const s of steps) {
    const v = Math.ceil(total / s) * s;
    if (v >= total) set.add(v);
  }
  return [...set].sort((a, b) => a - b).slice(0, 6);
}

export function PaymentModal({
  open,
  onClose,
  total,
  loading,
  onPay,
}: {
  open: boolean;
  onClose: () => void;
  total: number;
  loading: boolean;
  onPay: (p: { method: PaymentMethod; paid: number; notes: string }) => void;
}) {
  const [method, setMethod] = useState<PaymentMethod>("cash");
  const [paid, setPaid] = useState(total);
  const [notes, setNotes] = useState("");

  const change = Math.max(0, paid - total);
  const short = paid < total;
  const suggestions = useMemo(() => quickCash(total), [total]);
  const submit = () => !short && !loading && onPay({ method, paid: method === "cash" ? paid : total, notes });

  return (
    <Modal
      open={open}
      onClose={onClose}
      title="Pembayaran"
      size="md"
      footer={
        <>
          <Button variant="outline" onClick={onClose}>
            Batal <kbd className="ml-1 text-[10px] text-muted">Esc</kbd>
          </Button>
          <Button size="lg" onClick={submit} loading={loading} disabled={method === "cash" && short}>
            <Check className="size-4" /> Bayar {rupiah(total)}
          </Button>
        </>
      }
    >
      <form
        onSubmit={(e) => {
          e.preventDefault();
          submit();
        }}
        className="space-y-5"
      >
        <div className="rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 p-5 text-white">
          <p className="text-sm text-white/70">Total tagihan</p>
          <p className="tabular text-3xl font-bold tracking-tight">{rupiah(total)}</p>
        </div>

        <div className="grid grid-cols-4 gap-2">
          {METHODS.map((m) => {
            const Icon = PAYMENT_ICONS[m];
            return (
              <button
                type="button"
                key={m}
                onClick={() => setMethod(m)}
                className={cn(
                  "flex flex-col items-center gap-1.5 rounded-xl border px-2 py-3 text-xs font-medium transition-all",
                  method === m ? "border-brand-500 bg-brand-500/10 text-brand-700 ring-2 ring-brand-500/20 dark:text-brand-400" : "border-border hover:bg-surface-2",
                )}
              >
                <Icon className="size-5" />
                {PAYMENT_LABELS[m]}
              </button>
            );
          })}
        </div>

        {method === "cash" ? (
          <>
            <Field label="Uang diterima">
              <MoneyInput value={paid} onChange={setPaid} autoFocus className="h-12 text-lg font-semibold" onFocus={(e) => e.target.select()} />
            </Field>
            <div className="grid grid-cols-3 gap-2">
              {suggestions.map((v) => (
                <button
                  type="button"
                  key={v}
                  onClick={() => setPaid(v)}
                  className={cn("tabular rounded-lg border px-2 py-2 text-sm font-medium", paid === v ? "border-brand-500 bg-brand-500/10" : "border-border hover:bg-surface-2")}
                >
                  {v === total ? "Uang pas" : rupiah(v)}
                </button>
              ))}
            </div>
            <div className={cn("flex items-center justify-between rounded-xl px-4 py-3", short ? "bg-red-500/10 text-red-700 dark:text-red-400" : "bg-emerald-500/10 text-emerald-700 dark:text-emerald-400")}>
              <span className="text-sm font-medium">{short ? "Kurang" : "Kembalian"}</span>
              <span className="tabular text-2xl font-bold">{rupiah(short ? total - paid : change)}</span>
            </div>
          </>
        ) : (
          <p className="rounded-lg bg-surface-2 px-4 py-3 text-sm text-muted">
            Pastikan pembayaran {PAYMENT_LABELS[method]} sebesar <b className="text-fg">{rupiah(total)}</b> sudah diterima sebelum menyelesaikan transaksi.
          </p>
        )}

        <Field label="Catatan (opsional)">
          <Textarea rows={2} value={notes} onChange={(e) => setNotes(e.target.value)} />
        </Field>
        <button type="submit" className="hidden" />
      </form>
    </Modal>
  );
}
