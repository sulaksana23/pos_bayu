import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";
import type { ExpenseCategory, PaymentMethod, PoStatus, Role } from "./types";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

const idr = new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 });
const num = new Intl.NumberFormat("id-ID", { maximumFractionDigits: 2 });

export const toNumber = (v: string | number | null | undefined) => {
  const n = typeof v === "number" ? v : parseFloat(v ?? "0");
  return Number.isFinite(n) ? n : 0;
};

export const rupiah = (v: string | number | null | undefined) => idr.format(toNumber(v)).replace(/ /g, " ");
export const number = (v: string | number | null | undefined) => num.format(toNumber(v));

/** Compact rupiah for chart axes: 1.2 jt, 350 rb */
export function rupiahShort(v: number) {
  const abs = Math.abs(v);
  if (abs >= 1e9) return `${(v / 1e9).toFixed(1).replace(".0", "")} M`;
  if (abs >= 1e6) return `${(v / 1e6).toFixed(1).replace(".0", "")} jt`;
  if (abs >= 1e3) return `${Math.round(v / 1e3)} rb`;
  return String(v);
}

export function dateTime(v: string | null | undefined) {
  if (!v) return "-";
  return new Date(v).toLocaleString("id-ID", { day: "2-digit", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" });
}

export function date(v: string | null | undefined) {
  if (!v) return "-";
  return new Date(v).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" });
}

export function time(v: string | null | undefined) {
  if (!v) return "-";
  return new Date(v).toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit" });
}

/** yyyy-mm-dd in local time */
export function isoDate(d = new Date()) {
  const off = d.getTimezoneOffset();
  return new Date(d.getTime() - off * 60000).toISOString().slice(0, 10);
}

export function startOfMonth() {
  const d = new Date();
  return isoDate(new Date(d.getFullYear(), d.getMonth(), 1));
}

export function daysAgo(n: number) {
  const d = new Date();
  d.setDate(d.getDate() - n);
  return isoDate(d);
}

export const PAYMENT_LABELS: Record<PaymentMethod, string> = {
  cash: "Tunai",
  qris: "QRIS",
  transfer: "Transfer",
  wallet: "E-Wallet",
  mixed: "Campuran",
};

export const ROLE_LABELS: Record<Role, string> = {
  superadministrator: "Super Admin",
  admin: "Admin",
  manager: "Manajer",
  cashier: "Kasir",
};

export const EXPENSE_LABELS: Record<ExpenseCategory, string> = {
  operational: "Operasional",
  utilities: "Utilitas",
  rent: "Sewa",
  salary: "Gaji",
  maintenance: "Perawatan",
  marketing: "Marketing",
  other: "Lainnya",
};

export const PO_STATUS: Record<PoStatus, { label: string; tone: "amber" | "blue" | "green" | "gray" }> = {
  pending: { label: "Draft", tone: "amber" },
  ordered: { label: "Dipesan", tone: "blue" },
  received: { label: "Diterima", tone: "green" },
  cancelled: { label: "Dibatalkan", tone: "gray" },
};

export const isBackOffice = (role?: Role) => role === "admin" || role === "manager" || role === "superadministrator";
export const isAdmin = (role?: Role) => role === "admin" || role === "superadministrator";

export function initials(name?: string) {
  if (!name) return "?";
  return name
    .split(/\s+/)
    .slice(0, 2)
    .map((p) => p[0]?.toUpperCase())
    .join("");
}

/** Build a CSV file from rows and trigger a browser download. */
export function downloadCsv(filename: string, header: string[], rows: (string | number | null | undefined)[][]) {
  const esc = (v: string | number | null | undefined) => {
    const s = v == null ? "" : String(v);
    return /[",\n;]/.test(s) ? `"${s.replace(/"/g, '""')}"` : s;
  };
  const csv = [header, ...rows].map((r) => r.map(esc).join(",")).join("\n");
  const blob = new Blob(["﻿" + csv], { type: "text/csv;charset=utf-8" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = filename;
  a.click();
  URL.revokeObjectURL(url);
}
