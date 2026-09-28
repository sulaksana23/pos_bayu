"use client";

import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Banknote, Clock, CreditCard, Lock, Receipt, Wallet } from "lucide-react";
import { api } from "@/lib/api";
import type { Shift } from "@/lib/types";
import { cn, dateTime, rupiah, time, toNumber } from "@/lib/utils";
import { useCurrentShift } from "@/hooks/use-session";
import { useList } from "@/hooks/use-list";
import { Card, CardHeader } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Drawer } from "@/components/ui/modal";
import { EmptyState, PageHeader, PageLoader, Pagination, StatCard } from "@/components/ui/misc";
import { Table, Td, Th, Tr } from "@/components/ui/table";
import { CloseShiftModal, OpenShiftForm } from "@/components/pos/shift-forms";
import { PaymentBadge, TrxStatusBadge } from "@/components/pos/status";

function DiffBadge({ v }: { v: string | null }) {
  if (v === null) return <span className="text-muted">-</span>;
  const n = toNumber(v);
  return <Badge tone={n === 0 ? "green" : n > 0 ? "blue" : "red"}>{n === 0 ? "Sesuai" : `${n > 0 ? "+" : ""}${rupiah(n)}`}</Badge>;
}

function ShiftDetail({ id, onClose }: { id: number | null; onClose: () => void }) {
  const { data, isLoading } = useQuery({
    queryKey: ["shifts", "detail", id],
    enabled: id !== null,
    queryFn: async () => (await api.get<{ data: Shift }>(`/shifts/${id}`)).data,
  });
  return (
    <Drawer open={id !== null} onClose={onClose} title={data ? `Shift ${data.user?.name}` : "Detail shift"} description={data ? `${dateTime(data.opened_at)} – ${data.closed_at ? dateTime(data.closed_at) : "sekarang"}` : undefined}>
      {isLoading || !data ? (
        <PageLoader />
      ) : (
        <div className="space-y-5">
          <div className="grid grid-cols-2 gap-3 text-sm">
            {[
              ["Modal awal", rupiah(data.opening_cash)],
              ["Total penjualan", rupiah(data.total_sales)],
              ["Tunai", rupiah(data.total_cash)],
              ["Non-tunai", rupiah(data.total_non_cash)],
              ["Kas seharusnya", rupiah(data.expected_cash)],
              ["Kas aktual", data.closing_cash ? rupiah(data.closing_cash) : "-"],
            ].map(([l, v]) => (
              <div key={l} className="rounded-lg bg-surface-2 px-3 py-2">
                <p className="text-xs text-muted">{l}</p>
                <p className="tabular font-semibold">{v}</p>
              </div>
            ))}
          </div>
          {data.status === "closed" && (
            <div className="flex items-center justify-between rounded-lg border border-border px-3 py-2 text-sm">
              Selisih kas <DiffBadge v={data.cash_difference} />
            </div>
          )}
          {(data.opening_notes || data.closing_notes) && (
            <div className="space-y-1 text-sm">
              {data.opening_notes && <p><span className="text-muted">Catatan buka:</span> {data.opening_notes}</p>}
              {data.closing_notes && <p><span className="text-muted">Catatan tutup:</span> {data.closing_notes}</p>}
            </div>
          )}
          <div>
            <p className="mb-2 text-sm font-semibold">Transaksi ({data.transactions?.length ?? 0})</p>
            <ul className="divide-y divide-border rounded-lg border border-border">
              {data.transactions?.map((t) => (
                <li key={t.id} className="flex items-center gap-3 px-3 py-2 text-sm">
                  <div className="min-w-0 flex-1">
                    <p className="font-mono text-xs">{t.invoice_no}</p>
                    <p className="text-xs text-muted">{time(t.created_at)} · {t.items_count} item</p>
                  </div>
                  <PaymentBadge method={t.payment_method} />
                  <TrxStatusBadge status={t.status} />
                  <span className="tabular w-24 text-right font-medium">{rupiah(t.total)}</span>
                </li>
              ))}
              {!data.transactions?.length && <li className="px-3 py-6 text-center text-sm text-muted">Belum ada transaksi</li>}
            </ul>
          </div>
        </div>
      )}
    </Drawer>
  );
}

export default function ShiftsPage() {
  const current = useCurrentShift();
  const [page, setPage] = useState(1);
  const [closing, setClosing] = useState(false);
  const [selected, setSelected] = useState<number | null>(null);
  const history = useList<Shift>("shifts", "/shifts", { page });
  const s = current.data;

  return (
    <>
      <PageHeader title="Shift kasir" description="Buka dan tutup shift, serta rekap kas per shift." />

      {current.isLoading ? (
        <PageLoader />
      ) : s ? (
        <Card className="mb-6 overflow-hidden">
          <div className="flex flex-col gap-4 bg-gradient-to-r from-brand-600 to-brand-800 p-5 text-white sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p className="flex items-center gap-2 text-sm text-white/75">
                <span className="size-2 animate-pulse rounded-full bg-white" /> Shift sedang berjalan
              </p>
              <p className="mt-1 text-2xl font-bold">Sejak {dateTime(s.opened_at)}</p>
            </div>
            <Button variant="secondary" className="bg-white text-brand-800 hover:bg-white/90" onClick={() => setClosing(true)}>
              <Lock className="size-4" /> Tutup shift
            </Button>
          </div>
          <div className="grid grid-cols-2 gap-3 p-4 lg:grid-cols-4">
            <StatCard label="Transaksi" value={s.transaction_count} icon={Receipt} tone="blue" />
            <StatCard label="Total penjualan" value={rupiah(s.total_sales)} icon={Wallet} />
            <StatCard label="Tunai diterima" value={rupiah(s.total_cash)} icon={Banknote} tone="amber" />
            <StatCard label="Kas seharusnya" value={rupiah(s.expected_cash)} icon={CreditCard} tone="violet" hint={`Modal ${rupiah(s.opening_cash)}`} />
          </div>
        </Card>
      ) : (
        <Card className="mb-6 grid gap-6 p-6 md:grid-cols-2">
          <div>
            <div className="mb-3 grid size-11 place-items-center rounded-xl bg-amber-500/10 text-amber-600">
              <Clock className="size-5" />
            </div>
            <h2 className="text-lg font-semibold">Anda belum membuka shift</h2>
            <p className="mt-1 text-sm text-muted">Buka shift dengan mencatat modal awal di laci kas. Semua transaksi akan tercatat pada shift ini sampai Anda menutupnya.</p>
          </div>
          <OpenShiftForm />
        </Card>
      )}

      <Card>
        <CardHeader title="Riwayat shift" />
        {history.isLoading ? (
          <PageLoader />
        ) : !history.data?.data.length ? (
          <EmptyState icon={Clock} title="Belum ada riwayat shift" />
        ) : (
          <>
            <Table>
              <thead>
                <tr>
                  <Th>Kasir</Th>
                  <Th>Dibuka</Th>
                  <Th className="hidden md:table-cell">Ditutup</Th>
                  <Th className="text-right">Trx</Th>
                  <Th className="text-right">Penjualan</Th>
                  <Th className="hidden sm:table-cell">Selisih</Th>
                  <Th>Status</Th>
                </tr>
              </thead>
              <tbody>
                {history.data.data.map((sh) => (
                  <Tr key={sh.id} onClick={() => setSelected(sh.id)}>
                    <Td className="font-medium">{sh.user?.name}</Td>
                    <Td className="text-xs whitespace-nowrap text-muted">{dateTime(sh.opened_at)}</Td>
                    <Td className="hidden text-xs whitespace-nowrap text-muted md:table-cell">{sh.closed_at ? dateTime(sh.closed_at) : "-"}</Td>
                    <Td className="tabular text-right">{sh.transaction_count}</Td>
                    <Td className="tabular text-right font-medium">{rupiah(sh.total_sales)}</Td>
                    <Td className="hidden sm:table-cell"><DiffBadge v={sh.cash_difference} /></Td>
                    <Td>
                      <Badge tone={sh.status === "open" ? "green" : "gray"} dot className={cn(sh.status === "open" && "animate-pulse")}>
                        {sh.status === "open" ? "Aktif" : "Selesai"}
                      </Badge>
                    </Td>
                  </Tr>
                ))}
              </tbody>
            </Table>
            <Pagination page={history.data.current_page} lastPage={history.data.last_page} total={history.data.total} onPage={setPage} />
          </>
        )}
      </Card>

      {s && closing && <CloseShiftModal shift={s} open onClose={() => setClosing(false)} />}
      <ShiftDetail id={selected} onClose={() => setSelected(null)} />
    </>
  );
}
