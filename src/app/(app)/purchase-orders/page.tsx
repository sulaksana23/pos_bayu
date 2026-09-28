"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { ClipboardList, Plus } from "lucide-react";
import type { PoStatus, PurchaseOrder } from "@/lib/types";
import { date, PO_STATUS, rupiah } from "@/lib/utils";
import { useList } from "@/hooks/use-list";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { EmptyState, PageHeader, PageLoader, Pagination, SearchInput, Tabs } from "@/components/ui/misc";
import { Table, Td, Th, Tr } from "@/components/ui/table";
import { RoleGate } from "@/components/layout/auth-guard";

export default function PurchaseOrdersPage() {
  const router = useRouter();
  const [q, setQ] = useState("");
  const [status, setStatus] = useState<PoStatus | "">("");
  const [page, setPage] = useState(1);
  const { data, isLoading } = useList<PurchaseOrder>("purchase-orders", "/purchase-orders", { q, status, page, per_page: 20 });

  return (
    <RoleGate roles={["admin", "manager"]}>
      <PageHeader
        title="Purchase order"
        description="Pembelian barang ke supplier: draft → dipesan → diterima (stok otomatis bertambah)."
        actions={
          <Link href="/purchase-orders/new">
            <Button>
              <Plus className="size-4" /> Buat PO
            </Button>
          </Link>
        }
      />
      <Card>
        <div className="flex flex-wrap items-center gap-2 border-b border-border p-4">
          <SearchInput value={q} onChange={(v) => { setQ(v); setPage(1); }} placeholder="Cari nomor PO…" className="w-full sm:w-64" />
          <div className="sm:ml-auto">
            <Tabs
              value={status}
              onChange={(v) => { setStatus(v); setPage(1); }}
              items={[{ value: "", label: "Semua" }, ...(Object.keys(PO_STATUS) as PoStatus[]).map((s) => ({ value: s, label: PO_STATUS[s].label }))]}
            />
          </div>
        </div>
        {isLoading ? (
          <PageLoader />
        ) : !data?.data.length ? (
          <EmptyState icon={ClipboardList} title="Belum ada purchase order" action={<Link href="/purchase-orders/new"><Button>Buat PO pertama</Button></Link>} />
        ) : (
          <>
            <Table>
              <thead>
                <tr>
                  <Th>No. PO</Th>
                  <Th>Supplier</Th>
                  <Th className="hidden md:table-cell">Tanggal</Th>
                  <Th className="hidden md:table-cell">Estimasi tiba</Th>
                  <Th className="text-right">Item</Th>
                  <Th className="text-right">Total</Th>
                  <Th>Status</Th>
                </tr>
              </thead>
              <tbody>
                {data.data.map((po) => (
                  <Tr key={po.id} onClick={() => router.push(`/purchase-orders/${po.id}`)}>
                    <Td className="font-mono text-xs font-medium">{po.po_no}</Td>
                    <Td>
                      <p className="font-medium">{po.supplier?.name}</p>
                      {po.supplier?.company && <p className="text-xs text-muted">{po.supplier.company}</p>}
                    </Td>
                    <Td className="hidden text-muted md:table-cell">{date(po.order_date ?? po.created_at)}</Td>
                    <Td className="hidden text-muted md:table-cell">{date(po.expected_date)}</Td>
                    <Td className="tabular text-right">{po.items_count}</Td>
                    <Td className="tabular text-right font-semibold whitespace-nowrap">{rupiah(po.total)}</Td>
                    <Td><Badge tone={PO_STATUS[po.status].tone} dot>{PO_STATUS[po.status].label}</Badge></Td>
                  </Tr>
                ))}
              </tbody>
            </Table>
            <Pagination page={data.current_page} lastPage={data.last_page} total={data.total} onPage={setPage} />
          </>
        )}
      </Card>
    </RoleGate>
  );
}
