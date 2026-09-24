"use client";

import { use, useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { ArrowLeft, Ban, Check, PackageCheck, Pencil, Printer, Send, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { api, errorMessage } from "@/lib/api";
import type { PurchaseOrder } from "@/lib/types";
import { cn, date, PO_STATUS, rupiah, toNumber } from "@/lib/utils";
import { Card, CardHeader } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { ConfirmDialog, EmptyState, PageHeader, PageLoader } from "@/components/ui/misc";
import { Table, Td, Th, Tr } from "@/components/ui/table";
import { RoleGate } from "@/components/layout/auth-guard";

const STEPS = [
  { key: "pending", label: "Draft" },
  { key: "ordered", label: "Dipesan" },
  { key: "received", label: "Diterima" },
] as const;

export default function PurchaseOrderDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);
  const router = useRouter();
  const qc = useQueryClient();
  const [confirm, setConfirm] = useState<null | "receive" | "cancel" | "delete">(null);

  const { data: po, isLoading } = useQuery({
    queryKey: ["purchase-order", id],
    queryFn: async () => (await api.get<{ data: PurchaseOrder }>(`/purchase-orders/${id}`)).data,
  });

  const action = useMutation({
    mutationFn: (a: "mark-ordered" | "mark-received" | "cancel" | "delete") =>
      a === "delete" ? api.delete<{ message: string }>(`/purchase-orders/${id}`) : api.post<{ message: string }>(`/purchase-orders/${id}/${a}`),
    onSuccess: (res, a) => {
      toast.success(res.message);
      setConfirm(null);
      qc.invalidateQueries({ queryKey: ["purchase-orders"] });
      qc.invalidateQueries({ queryKey: ["purchase-order", id] });
      if (a === "mark-received") qc.invalidateQueries({ queryKey: ["products"] });
      if (a === "delete") router.push("/purchase-orders");
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  if (isLoading) return <PageLoader />;
  if (!po)
    return (
      <RoleGate roles={["admin", "manager"]}>
        <EmptyState title="Purchase order tidak ditemukan" />
      </RoleGate>
    );
  const stepIdx = STEPS.findIndex((s) => s.key === po.status);

  return (
    <RoleGate roles={["admin", "manager"]}>
      <PageHeader
        title={po.po_no}
        description={`Dibuat oleh ${po.user?.name ?? "-"} · ${date(po.created_at)}`}
        actions={
          <>
            <Link href="/purchase-orders">
              <Button variant="outline">
                <ArrowLeft className="size-4" /> Kembali
              </Button>
            </Link>
            <Button variant="outline" onClick={() => window.print()}>
              <Printer className="size-4" /> Cetak
            </Button>
            {po.status === "pending" && (
              <>
                <Link href={`/purchase-orders/${po.id}/edit`}>
                  <Button variant="outline">
                    <Pencil className="size-4" /> Edit
                  </Button>
                </Link>
                <Button loading={action.isPending && action.variables === "mark-ordered"} onClick={() => action.mutate("mark-ordered")}>
                  <Send className="size-4" /> Tandai dipesan
                </Button>
              </>
            )}
            {po.status === "ordered" && (
              <Button onClick={() => setConfirm("receive")}>
                <PackageCheck className="size-4" /> Terima barang
              </Button>
            )}
          </>
        }
      />

      <div className="print-area grid gap-6 xl:grid-cols-[1fr_340px]">
        <div className="space-y-6">
          {po.status !== "cancelled" ? (
            <Card className="p-5">
              <ol className="flex items-center">
                {STEPS.map((s, i) => (
                  <li key={s.key} className={cn("flex items-center", i < STEPS.length - 1 && "flex-1")}>
                    <div className="flex items-center gap-2">
                      <span className={cn("grid size-8 place-items-center rounded-full text-xs font-bold", i <= stepIdx ? "bg-brand-600 text-white" : "bg-surface-2 text-muted")}>
                        {i < stepIdx || po.status === "received" ? <Check className="size-4" /> : i + 1}
                      </span>
                      <span className={cn("text-sm font-medium", i <= stepIdx ? "text-fg" : "text-muted")}>{s.label}</span>
                    </div>
                    {i < STEPS.length - 1 && <div className={cn("mx-3 h-0.5 flex-1 rounded", i < stepIdx ? "bg-brand-600" : "bg-border")} />}
                  </li>
                ))}
              </ol>
            </Card>
          ) : (
            <Card className="border-red-500/30 bg-red-500/5 p-4 text-sm text-red-700 dark:text-red-400">PO ini telah dibatalkan.</Card>
          )}

          <Card>
            <CardHeader title="Item" description={`${po.items?.length ?? 0} baris`} />
            <Table>
              <thead>
                <tr>
                  <Th>Produk</Th>
                  <Th className="text-right">Qty</Th>
                  <Th className="text-right">Harga</Th>
                  <Th className="text-right">Subtotal</Th>
                </tr>
              </thead>
              <tbody>
                {po.items?.map((it) => (
                  <Tr key={it.id}>
                    <Td>
                      <p className="font-medium">{it.product_name}</p>
                      <p className="font-mono text-xs text-muted">
                        {it.product_sku || "manual"}
                        {it.product && ` · stok sekarang ${it.product.stock} ${it.product.unit}`}
                      </p>
                    </Td>
                    <Td className="tabular text-right">{it.qty}</Td>
                    <Td className="tabular text-right">{rupiah(it.price)}</Td>
                    <Td className="tabular text-right font-medium">{rupiah(it.subtotal)}</Td>
                  </Tr>
                ))}
              </tbody>
            </Table>
          </Card>
        </div>

        <div className="space-y-6">
          <Card className="space-y-3 p-5 text-sm">
            <div className="flex items-center justify-between">
              <span className="text-muted">Status</span>
              <Badge tone={PO_STATUS[po.status].tone} dot>{PO_STATUS[po.status].label}</Badge>
            </div>
            <div className="flex justify-between"><span className="text-muted">Supplier</span><span className="text-right font-medium">{po.supplier?.name}</span></div>
            {po.supplier?.phone && <div className="flex justify-between"><span className="text-muted">Telepon</span><span>{po.supplier.phone}</span></div>}
            <div className="flex justify-between"><span className="text-muted">Tanggal order</span><span>{date(po.order_date)}</span></div>
            <div className="flex justify-between"><span className="text-muted">Estimasi tiba</span><span>{date(po.expected_date)}</span></div>
            {po.received_date && <div className="flex justify-between"><span className="text-muted">Diterima</span><span>{date(po.received_date)}</span></div>}
            {po.notes && <p className="rounded-lg bg-surface-2 px-3 py-2 text-muted">{po.notes}</p>}
          </Card>
          <Card className="space-y-2 p-5 text-sm">
            <div className="flex justify-between"><span className="text-muted">Subtotal</span><span className="tabular">{rupiah(po.subtotal)}</span></div>
            {toNumber(po.discount) > 0 && <div className="flex justify-between"><span className="text-muted">Diskon</span><span className="tabular">-{rupiah(po.discount)}</span></div>}
            {toNumber(po.tax) > 0 && <div className="flex justify-between"><span className="text-muted">Pajak</span><span className="tabular">{rupiah(po.tax)}</span></div>}
            <div className="flex items-end justify-between border-t border-dashed border-border pt-2">
              <span className="font-semibold">Total</span>
              <span className="tabular text-xl font-bold">{rupiah(po.total)}</span>
            </div>
          </Card>
          {(po.status === "pending" || po.status === "ordered") && (
            <div className="flex gap-2">
              <Button variant="outline" className="flex-1" onClick={() => setConfirm("cancel")}>
                <Ban className="size-4" /> Batalkan
              </Button>
              <Button variant="outline" className="flex-1 text-red-600" onClick={() => setConfirm("delete")}>
                <Trash2 className="size-4" /> Hapus
              </Button>
            </div>
          )}
        </div>
      </div>

      <ConfirmDialog
        open={confirm === "receive"}
        onClose={() => setConfirm(null)}
        onConfirm={() => action.mutate("mark-received")}
        loading={action.isPending}
        danger={false}
        title="Terima barang?"
        description="Stok setiap produk pada PO ini akan ditambahkan sesuai jumlah yang dipesan."
        confirmText="Ya, terima"
      />
      <ConfirmDialog open={confirm === "cancel"} onClose={() => setConfirm(null)} onConfirm={() => action.mutate("cancel")} loading={action.isPending} title="Batalkan PO?" description="PO akan ditandai dibatalkan dan tidak bisa diproses lagi." confirmText="Batalkan PO" />
      <ConfirmDialog open={confirm === "delete"} onClose={() => setConfirm(null)} onConfirm={() => action.mutate("delete")} loading={action.isPending} title="Hapus PO?" description="PO beserta itemnya akan dihapus permanen." confirmText="Hapus" />
    </RoleGate>
  );
}
