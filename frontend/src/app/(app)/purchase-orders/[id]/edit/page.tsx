"use client";

import { use } from "react";
import Link from "next/link";
import { useQuery } from "@tanstack/react-query";
import { ArrowLeft } from "lucide-react";
import { api } from "@/lib/api";
import type { PurchaseOrder } from "@/lib/types";
import { EmptyState, PageHeader, PageLoader } from "@/components/ui/misc";
import { Button } from "@/components/ui/button";
import { RoleGate } from "@/components/layout/auth-guard";
import { PurchaseOrderForm } from "@/components/pos/po-form";

export default function EditPurchaseOrderPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);
  const { data, isLoading } = useQuery({
    queryKey: ["purchase-order", id],
    queryFn: async () => (await api.get<{ data: PurchaseOrder }>(`/purchase-orders/${id}`)).data,
  });

  return (
    <RoleGate roles={["admin", "manager"]}>
      <PageHeader
        title={data ? `Edit ${data.po_no}` : "Edit purchase order"}
        actions={
          <Link href={`/purchase-orders/${id}`}>
            <Button variant="outline">
              <ArrowLeft className="size-4" /> Kembali
            </Button>
          </Link>
        }
      />
      {isLoading || !data ? (
        <PageLoader />
      ) : data.status !== "pending" ? (
        <EmptyState title="PO tidak bisa diedit" description="Hanya PO berstatus draft yang dapat diubah." />
      ) : (
        <PurchaseOrderForm order={data} />
      )}
    </RoleGate>
  );
}
