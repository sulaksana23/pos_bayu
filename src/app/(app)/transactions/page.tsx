"use client";

import { useState } from "react";
import { Download, Receipt as ReceiptIcon } from "lucide-react";
import { api } from "@/lib/api";
import type { Paginated, Transaction } from "@/lib/types";
import { dateTime, downloadCsv, isoDate, PAYMENT_LABELS, rupiah, startOfMonth, toNumber } from "@/lib/utils";
import { useList } from "@/hooks/use-list";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input, Select } from "@/components/ui/input";
import { EmptyState, PageHeader, PageLoader, Pagination, SearchInput } from "@/components/ui/misc";
import { Table, Td, Th, Tr } from "@/components/ui/table";
import { PaymentBadge, TrxStatusBadge } from "@/components/pos/status";
import { TransactionDrawer } from "@/components/pos/transaction-drawer";

export default function TransactionsPage() {
  const [q, setQ] = useState("");
  const [status, setStatus] = useState("all");
  const [method, setMethod] = useState("");
  const [from, setFrom] = useState(startOfMonth());
  const [to, setTo] = useState(isoDate());
  const [page, setPage] = useState(1);
  const [selected, setSelected] = useState<number | null>(null);
  const [exporting, setExporting] = useState(false);

  const params = { q, status, payment_method: method, date_from: from, date_to: to, page, per_page: 25 };
  const { data, isLoading } = useList<Transaction>("transactions", "/transactions", params);

  const exportCsv = async () => {
    setExporting(true);
    try {
      const all = await api.get<Paginated<Transaction>>("/transactions", { ...params, page: 1, per_page: 200 });
      downloadCsv(
        `transaksi_${from}_${to}.csv`,
        ["Invoice", "Tanggal", "Kasir", "Pelanggan", "Metode", "Status", "Subtotal", "Diskon", "Pajak", "Total"],
        all.data.map((t) => [t.invoice_no, dateTime(t.created_at), t.cashier?.name, t.customer?.name ?? "Umum", PAYMENT_LABELS[t.payment_method], t.status, toNumber(t.subtotal), toNumber(t.discount), toNumber(t.tax), toNumber(t.total)]),
      );
    } finally {
      setExporting(false);
    }
  };

  return (
    <>
      <PageHeader
        title="Transaksi"
        description="Riwayat penjualan, detail struk, dan void transaksi."
        actions={
          <Button variant="outline" onClick={exportCsv} loading={exporting}>
            <Download className="size-4" /> Ekspor CSV
          </Button>
        }
      />
      <Card>
        <div className="flex flex-wrap gap-2 border-b border-border p-4">
          <SearchInput value={q} onChange={(v) => { setQ(v); setPage(1); }} placeholder="Cari nomor invoice…" className="w-full sm:w-64" />
          <Select value={status} onChange={(e) => { setStatus(e.target.value); setPage(1); }} className="w-auto">
            <option value="all">Semua status</option>
            <option value="completed">Selesai</option>
            <option value="void">Void</option>
          </Select>
          <Select value={method} onChange={(e) => { setMethod(e.target.value); setPage(1); }} className="w-auto">
            <option value="">Semua metode</option>
            {Object.entries(PAYMENT_LABELS).map(([k, v]) => (
              <option key={k} value={k}>{v}</option>
            ))}
          </Select>
          <div className="flex items-center gap-2">
            <Input type="date" value={from} onChange={(e) => { setFrom(e.target.value); setPage(1); }} className="w-auto" />
            <span className="text-muted">–</span>
            <Input type="date" value={to} onChange={(e) => { setTo(e.target.value); setPage(1); }} className="w-auto" />
          </div>
        </div>
        {isLoading ? (
          <PageLoader />
        ) : !data?.data.length ? (
          <EmptyState icon={ReceiptIcon} title="Tidak ada transaksi" description="Coba ubah filter tanggal atau status." />
        ) : (
          <>
            <Table>
              <thead>
                <tr>
                  <Th>Invoice</Th>
                  <Th>Waktu</Th>
                  <Th className="hidden md:table-cell">Kasir</Th>
                  <Th className="hidden md:table-cell">Pelanggan</Th>
                  <Th className="hidden sm:table-cell">Metode</Th>
                  <Th>Status</Th>
                  <Th className="text-right">Total</Th>
                </tr>
              </thead>
              <tbody>
                {data.data.map((t) => (
                  <Tr key={t.id} onClick={() => setSelected(t.id)}>
                    <Td className="font-mono text-xs font-medium">{t.invoice_no}</Td>
                    <Td className="text-xs whitespace-nowrap text-muted">{dateTime(t.created_at)}</Td>
                    <Td className="hidden md:table-cell">{t.cashier?.name}</Td>
                    <Td className="hidden md:table-cell">{t.customer?.name ?? <span className="text-muted">Umum</span>}</Td>
                    <Td className="hidden sm:table-cell"><PaymentBadge method={t.payment_method} /></Td>
                    <Td><TrxStatusBadge status={t.status} /></Td>
                    <Td className={`tabular text-right font-semibold whitespace-nowrap ${t.status === "void" ? "text-muted line-through" : ""}`}>{rupiah(t.total)}</Td>
                  </Tr>
                ))}
              </tbody>
            </Table>
            <Pagination page={data.current_page} lastPage={data.last_page} total={data.total} onPage={setPage} />
          </>
        )}
      </Card>
      <TransactionDrawer id={selected} onClose={() => setSelected(null)} />
    </>
  );
}
