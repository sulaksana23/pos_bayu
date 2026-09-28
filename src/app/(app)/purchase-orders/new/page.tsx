"use client";

import Link from "next/link";
import { ArrowLeft } from "lucide-react";
import { PageHeader } from "@/components/ui/misc";
import { Button } from "@/components/ui/button";
import { RoleGate } from "@/components/layout/auth-guard";
import { PurchaseOrderForm } from "@/components/pos/po-form";

export default function NewPurchaseOrderPage() {
  return (
    <RoleGate roles={["admin", "manager"]}>
      <PageHeader
        title="Purchase order baru"
        actions={
          <Link href="/purchase-orders">
            <Button variant="outline">
              <ArrowLeft className="size-4" /> Kembali
            </Button>
          </Link>
        }
      />
      <PurchaseOrderForm />
    </RoleGate>
  );
}
