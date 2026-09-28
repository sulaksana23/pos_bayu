"use client";

import { rupiah } from "@/lib/utils";

export const CHART_COLORS = ["#059669", "#0ea5e9", "#8b5cf6", "#f59e0b", "#ef4444", "#64748b"];

interface TooltipProps {
  active?: boolean;
  label?: string | number;
  payload?: { name?: string; value?: number | string; color?: string; dataKey?: string | number }[];
  money?: boolean;
}

export function ChartTooltip({ active, payload, label, money = true }: TooltipProps) {
  if (!active || !payload?.length) return null;
  return (
    <div className="rounded-lg border border-border bg-surface px-3 py-2 text-xs shadow-xl">
      {label !== undefined && <p className="mb-1 font-medium">{label}</p>}
      {payload.map((p) => (
        <div key={String(p.dataKey)} className="flex items-center gap-2">
          <span className="size-2 rounded-full" style={{ background: p.color }} />
          <span className="text-muted">{p.name}</span>
          <span className="tabular ml-auto pl-3 font-medium">{money ? rupiah(p.value as number) : p.value}</span>
        </div>
      ))}
    </div>
  );
}
