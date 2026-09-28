"use client";

import { useState } from "react";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Ban, Printer } from "lucide-react";
import { toast } from "sonner";
import { api, errorMessage } from "@/lib/api";
import type { Transaction } from "@/lib/types";
import { dateTime, isBackOffice, rupiah } from "@/lib/utils";
import { useAuth } from "@/stores/auth";
import { Drawer, Modal } from "@/components/ui/modal";
import { Button } from "@/components/ui/button";
import { Field, Textarea } from "@/components/ui/input";
import { PageLoader } from "@/components/ui/misc";
import { Receipt } from "./receipt";
import { TrxStatusBadge } from "./status";

export function TransactionDrawer({ id, onClose }: { id: number | null; onClose: () => void }) {
  const qc = useQueryClient();
  const role = useAuth((s) => s.user?.role);
  const [voidOpen, setVoidOpen] = useState(false);
  const [reason, setReason] = useState("");

  const { data: trx, isLoading } = useQuery({
    queryKey: ["transaction", id],
    enabled: id !== null,
    queryFn: async () => (await api.get<{ data: Transaction }>(`/transactions/${id}`)).data,
  });

  const voidMut = useMutation({
    mutationFn: () => api.post<{ message: string }>(`/transactions/${id}/void`, { reason }),
    onSuccess: (res) => {
      toast.success(res.message);
      setVoidOpen(false);
      setReason("");
      qc.invalidateQueries({ queryKey: ["transaction", id] });
      qc.invalidateQueries({ queryKey: ["transactions"] });
      qc.invalidateQueries({ queryKey: ["products"] });
      qc.invalidateQueries({ queryKey: ["dashboard"] });
      qc.invalidateQueries({ queryKey: ["shift"] });
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  return (
    <>
      <Drawer
        open={id !== null}
        onClose={onClose}
        width="max-w-md"
        title={trx?.invoice_no ?? "Detail transaksi"}
        description={trx ? dateTime(trx.created_at) : undefined}
        footer={
          trx && (
            <>
              {trx.status === "completed" && isBackOffice(role) && (
                <Button variant="outline" className="text-red-600" onClick={() => setVoidOpen(true)}>
                  <Ban className="size-4" /> Void
                </Button>
              )}
              <Button onClick={() => window.print()}>
                <Printer className="size-4" /> Cetak ulang
              </Button>
            </>
          )
        }
      >
        {isLoading || !trx ? (
          <PageLoader />
        ) : (
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <TrxStatusBadge status={trx.status} />
              <span className="tabular text-lg font-bold">{rupiah(trx.total)}</span>
            </div>
            {trx.status === "void" && (
              <div className="rounded-lg bg-red-500/10 px-3 py-2 text-sm text-red-700 dark:text-red-400">
                Di-void {dateTime(trx.voided_at)} — {trx.void_reason}
              </div>
            )}
            <div className="print-area overflow-hidden rounded-lg border border-border">
              <Receipt trx={trx} />
            </div>
          </div>
        )}
      </Drawer>
      <Modal
        open={voidOpen}
        onClose={() => setVoidOpen(false)}
        title="Void transaksi"
        description="Stok produk akan dikembalikan. Tindakan ini tidak bisa dibatalkan."
        size="sm"
        footer={
          <>
            <Button variant="outline" onClick={() => setVoidOpen(false)}>
              Batal
            </Button>
            <Button variant="danger" disabled={reason.trim().length < 3} loading={voidMut.isPending} onClick={() => voidMut.mutate()}>
              Void transaksi
            </Button>
          </>
        }
      >
        <Field label="Alasan void" required>
          <Textarea autoFocus value={reason} onChange={(e) => setReason(e.target.value)} placeholder="Contoh: salah input item" />
        </Field>
      </Modal>
    </>
  );
}
