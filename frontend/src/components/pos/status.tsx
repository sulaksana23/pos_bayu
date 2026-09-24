import { Badge } from "@/components/ui/badge";
import type { PaymentMethod, Transaction } from "@/lib/types";
import { PAYMENT_LABELS } from "@/lib/utils";
import { Banknote, CreditCard, Layers, QrCode, Smartphone, type LucideIcon } from "lucide-react";

export const PAYMENT_ICONS: Record<PaymentMethod, LucideIcon> = {
  cash: Banknote,
  qris: QrCode,
  transfer: CreditCard,
  wallet: Smartphone,
  mixed: Layers,
};

export function PaymentBadge({ method }: { method: PaymentMethod }) {
  const Icon = PAYMENT_ICONS[method];
  return (
    <span className="inline-flex items-center gap-1.5 text-xs text-muted">
      <Icon className="size-3.5" />
      {PAYMENT_LABELS[method]}
    </span>
  );
}

export function TrxStatusBadge({ status }: { status: Transaction["status"] }) {
  if (status === "completed") return <Badge tone="green" dot>Selesai</Badge>;
  if (status === "void") return <Badge tone="red" dot>Void</Badge>;
  return <Badge tone="amber" dot>{status}</Badge>;
}

export function StockBadge({ stock, min, unit }: { stock: number; min: number; unit?: string }) {
  if (stock <= 0) return <Badge tone="red">Habis</Badge>;
  if (min > 0 && stock <= min) return <Badge tone="amber">{stock} {unit} · Menipis</Badge>;
  return <Badge tone="green">{stock} {unit}</Badge>;
}
