"use client";

import { useEffect, useState, type ReactNode } from "react";
import { ChevronLeft, ChevronRight, Inbox, Loader2, Search, TrendingDown, TrendingUp, type LucideIcon } from "lucide-react";
import { cn } from "@/lib/utils";
import { Button } from "./button";
import { Modal } from "./modal";

export function Spinner({ className }: { className?: string }) {
  return <Loader2 className={cn("size-5 animate-spin text-brand-600", className)} />;
}

export function PageLoader() {
  return (
    <div className="flex min-h-60 items-center justify-center">
      <Spinner className="size-7" />
    </div>
  );
}

export function Skeleton({ className }: { className?: string }) {
  return <div className={cn("animate-pulse rounded-md bg-surface-2", className)} />;
}

export function EmptyState({ icon: Icon = Inbox, title, description, action }: { icon?: LucideIcon; title: string; description?: string; action?: ReactNode }) {
  return (
    <div className="flex flex-col items-center justify-center px-6 py-14 text-center">
      <div className="mb-3 rounded-full bg-surface-2 p-3 text-muted">
        <Icon className="size-6" />
      </div>
      <p className="text-sm font-medium">{title}</p>
      {description && <p className="mt-1 max-w-sm text-sm text-muted">{description}</p>}
      {action && <div className="mt-4">{action}</div>}
    </div>
  );
}

export function PageHeader({ title, description, actions }: { title: string; description?: string; actions?: ReactNode }) {
  return (
    <div className="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 className="text-xl font-semibold tracking-tight sm:text-2xl">{title}</h1>
        {description && <p className="mt-1 text-sm text-muted">{description}</p>}
      </div>
      {actions && <div className="flex flex-wrap items-center gap-2">{actions}</div>}
    </div>
  );
}

export function SearchInput({
  value,
  onChange,
  placeholder = "Cari…",
  className,
  delay = 300,
  autoFocus,
}: {
  value: string;
  onChange: (v: string) => void;
  placeholder?: string;
  className?: string;
  delay?: number;
  autoFocus?: boolean;
}) {
  const [local, setLocal] = useState(value);
  // Sync when the parent resets the value (adjusting state during render, not in an effect).
  const [prev, setPrev] = useState(value);
  if (prev !== value) {
    setPrev(value);
    setLocal(value);
  }
  useEffect(() => {
    if (local === value) return;
    const t = setTimeout(() => onChange(local), delay);
    return () => clearTimeout(t);
  }, [local, value, onChange, delay]);

  return (
    <div className={cn("relative", className)}>
      <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted" />
      <input
        autoFocus={autoFocus}
        value={local}
        onChange={(e) => setLocal(e.target.value)}
        placeholder={placeholder}
        className="h-10 w-full rounded-lg border border-border bg-surface pr-3 pl-9 text-sm placeholder:text-muted/70 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none"
      />
    </div>
  );
}

export function Pagination({ page, lastPage, total, onPage }: { page: number; lastPage: number; total?: number; onPage: (p: number) => void }) {
  if (lastPage <= 1) return total !== undefined ? <p className="px-4 py-3 text-xs text-muted">{total} data</p> : null;
  return (
    <div className="flex items-center justify-between gap-3 border-t border-border px-4 py-3">
      <p className="text-xs text-muted">
        Halaman {page} dari {lastPage}
        {total !== undefined && ` · ${total} data`}
      </p>
      <div className="flex gap-1">
        <Button variant="outline" size="icon" className="size-8" disabled={page <= 1} onClick={() => onPage(page - 1)} aria-label="Sebelumnya">
          <ChevronLeft className="size-4" />
        </Button>
        <Button variant="outline" size="icon" className="size-8" disabled={page >= lastPage} onClick={() => onPage(page + 1)} aria-label="Berikutnya">
          <ChevronRight className="size-4" />
        </Button>
      </div>
    </div>
  );
}

export function StatCard({
  label,
  value,
  icon: Icon,
  growth,
  hint,
  tone = "brand",
}: {
  label: string;
  value: ReactNode;
  icon: LucideIcon;
  growth?: number;
  hint?: ReactNode;
  tone?: "brand" | "blue" | "violet" | "amber" | "red";
}) {
  const tones = {
    brand: "bg-brand-500/10 text-brand-600 dark:text-brand-400",
    blue: "bg-sky-500/10 text-sky-600 dark:text-sky-400",
    violet: "bg-violet-500/10 text-violet-600 dark:text-violet-400",
    amber: "bg-amber-500/10 text-amber-600 dark:text-amber-400",
    red: "bg-red-500/10 text-red-600 dark:text-red-400",
  };
  return (
    <div className="rounded-xl border border-border bg-surface p-4 shadow-[0_1px_2px_rgba(16,24,40,0.04)] sm:p-5">
      <div className="flex items-start justify-between gap-3">
        <p className="text-xs font-medium text-muted sm:text-sm">{label}</p>
        <span className={cn("rounded-lg p-2", tones[tone])}>
          <Icon className="size-4" />
        </span>
      </div>
      <p className="tabular mt-2 truncate text-lg font-semibold tracking-tight sm:text-2xl">{value}</p>
      <div className="mt-1 flex items-center gap-2 text-xs">
        {growth !== undefined && (
          <span className={cn("inline-flex items-center gap-0.5 font-medium", growth >= 0 ? "text-emerald-600 dark:text-emerald-400" : "text-red-600 dark:text-red-400")}>
            {growth >= 0 ? <TrendingUp className="size-3.5" /> : <TrendingDown className="size-3.5" />}
            {Math.abs(growth)}%
          </span>
        )}
        {hint && <span className="truncate text-muted">{hint}</span>}
      </div>
    </div>
  );
}

export function ConfirmDialog({
  open,
  onClose,
  onConfirm,
  title,
  description,
  confirmText = "Ya, lanjutkan",
  danger = true,
  loading,
}: {
  open: boolean;
  onClose: () => void;
  onConfirm: () => void;
  title: string;
  description?: ReactNode;
  confirmText?: string;
  danger?: boolean;
  loading?: boolean;
}) {
  return (
    <Modal
      open={open}
      onClose={onClose}
      title={title}
      size="sm"
      footer={
        <>
          <Button variant="outline" onClick={onClose}>
            Batal
          </Button>
          <Button variant={danger ? "danger" : "primary"} onClick={onConfirm} loading={loading}>
            {confirmText}
          </Button>
        </>
      }
    >
      <div className="text-sm text-muted">{description}</div>
    </Modal>
  );
}

export function Tabs<T extends string>({ value, onChange, items }: { value: T; onChange: (v: T) => void; items: { value: T; label: ReactNode }[] }) {
  return (
    <div className="inline-flex rounded-lg border border-border bg-surface-2 p-0.5">
      {items.map((it) => (
        <button
          key={it.value}
          onClick={() => onChange(it.value)}
          className={cn(
            "rounded-md px-3 py-1.5 text-xs font-medium whitespace-nowrap transition-colors",
            value === it.value ? "bg-surface text-fg shadow-sm" : "text-muted hover:text-fg",
          )}
        >
          {it.label}
        </button>
      ))}
    </div>
  );
}
