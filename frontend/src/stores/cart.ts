"use client";

import { create } from "zustand";
import { persist } from "zustand/middleware";
import type { Customer, Product } from "@/lib/types";
import { toNumber } from "@/lib/utils";

export interface CartLine {
  product: Product;
  qty: number;
  discount: number;
}

export interface HeldCart {
  id: string;
  label: string;
  lines: CartLine[];
  customer: Customer | null;
  heldAt: string;
}

interface CartState {
  lines: CartLine[];
  customer: Customer | null;
  discount: number;
  taxRate: number;
  notes: string;
  held: HeldCart[];
  add: (p: Product, qty?: number) => { ok: boolean; message?: string };
  setQty: (id: number, qty: number) => void;
  setLineDiscount: (id: number, discount: number) => void;
  remove: (id: number) => void;
  setCustomer: (c: Customer | null) => void;
  setDiscount: (v: number) => void;
  setTaxRate: (v: number) => void;
  setNotes: (v: string) => void;
  clear: () => void;
  hold: (label?: string) => void;
  resume: (id: string) => void;
  dropHeld: (id: string) => void;
}

export const useCart = create<CartState>()(
  persist(
    (set, get) => ({
      lines: [],
      customer: null,
      discount: 0,
      taxRate: 0,
      notes: "",
      held: [],
      add: (product, qty = 1) => {
        const existing = get().lines.find((l) => l.product.id === product.id);
        const nextQty = (existing?.qty ?? 0) + qty;
        if (product.stock <= 0) return { ok: false, message: `${product.name} sedang habis.` };
        if (nextQty > product.stock) return { ok: false, message: `Stok ${product.name} tersisa ${product.stock}.` };
        set((s) => ({
          lines: existing
            ? s.lines.map((l) => (l.product.id === product.id ? { ...l, qty: nextQty, product } : l))
            : [...s.lines, { product, qty, discount: 0 }],
        }));
        return { ok: true };
      },
      setQty: (id, qty) =>
        set((s) => ({
          lines:
            qty <= 0
              ? s.lines.filter((l) => l.product.id !== id)
              : s.lines.map((l) => (l.product.id === id ? { ...l, qty: Math.min(qty, l.product.stock) } : l)),
        })),
      setLineDiscount: (id, discount) =>
        set((s) => ({ lines: s.lines.map((l) => (l.product.id === id ? { ...l, discount: Math.max(0, discount) } : l)) })),
      remove: (id) => set((s) => ({ lines: s.lines.filter((l) => l.product.id !== id) })),
      setCustomer: (customer) => set({ customer }),
      setDiscount: (discount) => set({ discount: Math.max(0, discount) }),
      setTaxRate: (taxRate) => set({ taxRate: Math.max(0, taxRate) }),
      setNotes: (notes) => set({ notes }),
      clear: () => set({ lines: [], customer: null, discount: 0, notes: "" }),
      hold: (label) => {
        const { lines, customer, held } = get();
        if (!lines.length) return;
        const entry: HeldCart = {
          id: crypto.randomUUID(),
          label: label || customer?.name || `Pesanan #${held.length + 1}`,
          lines,
          customer,
          heldAt: new Date().toISOString(),
        };
        set({ held: [entry, ...held], lines: [], customer: null, discount: 0, notes: "" });
      },
      resume: (id) => {
        const entry = get().held.find((h) => h.id === id);
        if (!entry) return;
        const { lines, customer } = get();
        const rest = get().held.filter((h) => h.id !== id);
        // Park whatever is currently in the cart instead of losing it.
        const parked: HeldCart[] = lines.length
          ? [{ id: crypto.randomUUID(), label: customer?.name || "Pesanan ditahan", lines, customer, heldAt: new Date().toISOString() }]
          : [];
        set({ lines: entry.lines, customer: entry.customer, held: [...parked, ...rest] });
      },
      dropHeld: (id) => set((s) => ({ held: s.held.filter((h) => h.id !== id) })),
    }),
    { name: "balipos-cart" },
  ),
);

export function cartTotals(lines: CartLine[], discount: number, taxRate: number) {
  const gross = lines.reduce((sum, l) => sum + toNumber(l.product.price) * l.qty, 0);
  const lineDiscounts = lines.reduce((sum, l) => sum + l.discount, 0);
  const subtotal = Math.max(0, gross - lineDiscounts);
  const afterDiscount = Math.max(0, subtotal - discount);
  const tax = Math.round((afterDiscount * taxRate) / 100);
  const total = afterDiscount + tax;
  const items = lines.reduce((sum, l) => sum + l.qty, 0);
  return { gross, lineDiscounts, subtotal, tax, total, items };
}
