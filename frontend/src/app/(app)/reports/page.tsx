"use client";

import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Bar, BarChart, CartesianGrid, Cell, ComposedChart, Line, Pie, PieChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts";
import { Ban, Boxes, DollarSign, Download, Percent, Receipt, TrendingUp, Wallet } from "lucide-react";
import { api } from "@/lib/api";
import type { PaymentMethod } from "@/lib/types";
import { daysAgo, downloadCsv, EXPENSE_LABELS, isoDate, number, PAYMENT_LABELS, rupiah, rupiahShort, startOfMonth, toNumber } from "@/lib/utils";
import { Card, CardHeader } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { EmptyState, PageHeader, PageLoader, StatCard, Tabs } from "@/components/ui/misc";
import { Table, Td, Th, Tr } from "@/components/ui/table";
import { ChartTooltip, CHART_COLORS } from "@/components/pos/chart";
import { RoleGate } from "@/components/layout/auth-guard";

interface SalesReport {
  summary: { total_transactions: number; total_revenue: number; avg_transaction: number; total_discount: number; gross_profit: number; void_count: number };
  chart: { period: string; transaction_count: number; revenue: string | number }[];
  payments: { payment_method: PaymentMethod; count: number; total: string | number }[];
  cashiers: { name: string; count: number; total: string | number }[];
}
interface ProductRow { product_name: string; product_sku: string; qty: number; revenue: number; cost: number; profit: number; margin: number }
interface ExpenseReport { summary: { total_expenses: number; total_count: number; avg_expense: number }; breakdown: { category: string; total: string | number; count: number }[] }
interface InventoryReport {
  summary: { total_products: number; total_units: number; stock_value_cost: number; stock_value_price: number; low: number; out: number };
  by_category: { category: string; products: number; units: number | string; value: number | string }[];
}

type Tab = "sales" | "products" | "expenses" | "inventory";

const PRESETS = [
  { label: "Hari ini", from: () => isoDate(), to: () => isoDate() },
  { label: "7 hari", from: () => daysAgo(6), to: () => isoDate() },
  { label: "30 hari", from: () => daysAgo(29), to: () => isoDate() },
  { label: "Bulan ini", from: () => startOfMonth(), to: () => isoDate() },
];

export default function ReportsPage() {
  const [tab, setTab] = useState<Tab>("sales");
  const [from, setFrom] = useState(startOfMonth());
  const [to, setTo] = useState(isoDate());
  const range = { start_date: from, end_date: to };

  const sales = useQuery({ queryKey: ["reports", "sales", range], queryFn: () => api.get<SalesReport>("/reports/sales", range), enabled: tab === "sales" });
  const products = useQuery({ queryKey: ["reports", "products", range], queryFn: () => api.get<{ data: ProductRow[] }>("/reports/products", { ...range, limit: 100 }), enabled: tab === "products" });
  const expenses = useQuery({ queryKey: ["reports", "expenses", range], queryFn: () => api.get<ExpenseReport>("/reports/expenses", range), enabled: tab === "expenses" || tab === "sales" });
  const inventory = useQuery({ queryKey: ["reports", "inventory"], queryFn: () => api.get<InventoryReport>("/reports/inventory"), enabled: tab === "inventory" });

  const exportCsv = () => {
    if (tab === "sales" && sales.data) downloadCsv(`penjualan_${from}_${to}.csv`, ["Tanggal", "Transaksi", "Pendapatan"], sales.data.chart.map((r) => [r.period, r.transaction_count, toNumber(r.revenue)]));
    if (tab === "products" && products.data) downloadCsv(`produk_${from}_${to}.csv`, ["Produk", "SKU", "Qty", "Pendapatan", "Modal", "Laba", "Margin %"], products.data.data.map((r) => [r.product_name, r.product_sku, r.qty, r.revenue, r.cost, r.profit, r.margin]));
    if (tab === "expenses" && expenses.data) downloadCsv(`pengeluaran_${from}_${to}.csv`, ["Kategori", "Jumlah transaksi", "Total"], expenses.data.breakdown.map((r) => [EXPENSE_LABELS[r.category as keyof typeof EXPENSE_LABELS] ?? r.category, r.count, toNumber(r.total)]));
    if (tab === "inventory" && inventory.data) downloadCsv("inventaris.csv", ["Kategori", "Produk", "Unit", "Nilai"], inventory.data.by_category.map((r) => [r.category, r.products, toNumber(r.units), toNumber(r.value)]));
  };

  const net = sales.data && expenses.data ? sales.data.summary.gross_profit - expenses.data.summary.total_expenses : null;

  return (
    <RoleGate roles={["admin", "manager"]}>
      <PageHeader
        title="Laporan"
        description="Analisis penjualan, produk, pengeluaran, dan persediaan."
        actions={
          <Button variant="outline" onClick={exportCsv}>
            <Download className="size-4" /> Ekspor CSV
          </Button>
        }
      />

      <div className="mb-6 flex flex-wrap items-center gap-3">
        <Tabs
          value={tab}
          onChange={setTab}
          items={[
            { value: "sales", label: "Penjualan" },
            { value: "products", label: "Produk" },
            { value: "expenses", label: "Pengeluaran" },
            { value: "inventory", label: "Inventaris" },
          ]}
        />
        {tab !== "inventory" && (
          <div className="flex flex-wrap items-center gap-2 lg:ml-auto">
            {PRESETS.map((p) => (
              <Button key={p.label} variant={from === p.from() && to === p.to() ? "secondary" : "ghost"} size="sm" onClick={() => { setFrom(p.from()); setTo(p.to()); }}>
                {p.label}
              </Button>
            ))}
            <Input type="date" value={from} max={to} onChange={(e) => setFrom(e.target.value)} className="h-8 w-auto text-xs" />
            <span className="text-muted">–</span>
            <Input type="date" value={to} min={from} onChange={(e) => setTo(e.target.value)} className="h-8 w-auto text-xs" />
          </div>
        )}
      </div>

      {tab === "sales" &&
        (sales.isLoading || !sales.data ? (
          <PageLoader />
        ) : (
          <div className="space-y-6">
            <div className="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
              <StatCard label="Pendapatan" value={rupiah(sales.data.summary.total_revenue)} icon={DollarSign} hint={`Diskon ${rupiah(sales.data.summary.total_discount)}`} />
              <StatCard label="Transaksi" value={number(sales.data.summary.total_transactions)} icon={Receipt} tone="blue" hint={`Rata-rata ${rupiah(sales.data.summary.avg_transaction)}`} />
              <StatCard label="Laba kotor" value={rupiah(sales.data.summary.gross_profit)} icon={TrendingUp} tone="violet" hint={sales.data.summary.total_revenue > 0 ? `Margin ${((sales.data.summary.gross_profit / sales.data.summary.total_revenue) * 100).toFixed(1)}%` : undefined} />
              <StatCard label="Laba bersih" value={net === null ? "…" : rupiah(net)} icon={Wallet} tone={net !== null && net < 0 ? "red" : "amber"} hint={`${sales.data.summary.void_count} transaksi void`} />
            </div>
            <Card>
              <CardHeader title="Pendapatan harian" description={`${from} s/d ${to}`} />
              {sales.data.chart.length === 0 ? (
                <EmptyState title="Tidak ada penjualan pada periode ini" />
              ) : (
                <div className="h-80 p-4 pl-0">
                  <ResponsiveContainer width="100%" height="100%">
                    <ComposedChart data={sales.data.chart.map((r) => ({ ...r, revenue: toNumber(r.revenue), label: new Date(r.period).toLocaleDateString("id-ID", { day: "2-digit", month: "short" }) }))} margin={{ left: 8, right: 8 }}>
                      <CartesianGrid strokeDasharray="3 3" stroke="var(--border)" vertical={false} />
                      <XAxis dataKey="label" tick={{ fontSize: 12, fill: "var(--muted)" }} axisLine={false} tickLine={false} />
                      <YAxis yAxisId="l" tickFormatter={rupiahShort} tick={{ fontSize: 12, fill: "var(--muted)" }} axisLine={false} tickLine={false} width={56} />
                      <YAxis yAxisId="r" orientation="right" tick={{ fontSize: 12, fill: "var(--muted)" }} axisLine={false} tickLine={false} width={32} />
                      <Tooltip content={({ active, payload, label }) => (
                        <ChartTooltip active={active} label={label} payload={payload?.filter((p) => p.dataKey === "revenue") as never} />
                      )} cursor={{ fill: "var(--surface-2)" }} />
                      <Bar yAxisId="l" dataKey="revenue" name="Pendapatan" fill={CHART_COLORS[0]} radius={[4, 4, 0, 0]} maxBarSize={32} />
                      <Line yAxisId="r" dataKey="transaction_count" name="Transaksi" stroke={CHART_COLORS[1]} strokeWidth={2} dot={false} />
                    </ComposedChart>
                  </ResponsiveContainer>
                </div>
              )}
            </Card>
            <div className="grid gap-6 lg:grid-cols-2">
              <Card>
                <CardHeader title="Metode pembayaran" />
                {sales.data.payments.length === 0 ? (
                  <EmptyState title="Tidak ada data" />
                ) : (
                  <div className="flex flex-col items-center gap-4 p-4 sm:flex-row">
                    <div className="h-44 w-44 shrink-0">
                      <ResponsiveContainer width="100%" height="100%">
                        <PieChart>
                          <Pie data={sales.data.payments.map((p) => ({ name: PAYMENT_LABELS[p.payment_method], value: toNumber(p.total) }))} dataKey="value" innerRadius={48} outerRadius={75} paddingAngle={3} stroke="none">
                            {sales.data.payments.map((_, i) => <Cell key={i} fill={CHART_COLORS[i % CHART_COLORS.length]} />)}
                          </Pie>
                          <Tooltip content={<ChartTooltip />} />
                        </PieChart>
                      </ResponsiveContainer>
                    </div>
                    <ul className="w-full space-y-2.5">
                      {sales.data.payments.map((p, i) => {
                        const pct = sales.data.summary.total_revenue > 0 ? (toNumber(p.total) / sales.data.summary.total_revenue) * 100 : 0;
                        return (
                          <li key={p.payment_method} className="text-sm">
                            <div className="flex items-center gap-2">
                              <span className="size-2.5 rounded-sm" style={{ background: CHART_COLORS[i % CHART_COLORS.length] }} />
                              {PAYMENT_LABELS[p.payment_method]}
                              <span className="text-xs text-muted">{p.count}x · {pct.toFixed(0)}%</span>
                              <span className="tabular ml-auto font-medium">{rupiah(p.total)}</span>
                            </div>
                          </li>
                        );
                      })}
                    </ul>
                  </div>
                )}
              </Card>
              <Card>
                <CardHeader title="Performa kasir" />
                {sales.data.cashiers.length === 0 ? (
                  <EmptyState title="Tidak ada data" />
                ) : (
                  <Table>
                    <thead>
                      <tr><Th>Kasir</Th><Th className="text-right">Transaksi</Th><Th className="text-right">Penjualan</Th></tr>
                    </thead>
                    <tbody>
                      {sales.data.cashiers.map((c) => (
                        <Tr key={c.name}><Td className="font-medium">{c.name}</Td><Td className="tabular text-right">{c.count}</Td><Td className="tabular text-right font-medium">{rupiah(c.total)}</Td></Tr>
                      ))}
                    </tbody>
                  </Table>
                )}
              </Card>
            </div>
          </div>
        ))}

      {tab === "products" &&
        (products.isLoading || !products.data ? (
          <PageLoader />
        ) : products.data.data.length === 0 ? (
          <Card><EmptyState icon={Boxes} title="Belum ada produk terjual pada periode ini" /></Card>
        ) : (
          <div className="space-y-6">
            <Card>
              <CardHeader title="10 produk teratas" description="Berdasarkan pendapatan" />
              <div className="h-80 p-4 pl-0">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={products.data.data.slice(0, 10)} layout="vertical" margin={{ left: 8, right: 16 }}>
                    <CartesianGrid strokeDasharray="3 3" stroke="var(--border)" horizontal={false} />
                    <XAxis type="number" tickFormatter={rupiahShort} tick={{ fontSize: 12, fill: "var(--muted)" }} axisLine={false} tickLine={false} />
                    <YAxis type="category" dataKey="product_name" tick={{ fontSize: 12, fill: "var(--muted)" }} axisLine={false} tickLine={false} width={140} />
                    <Tooltip content={<ChartTooltip />} cursor={{ fill: "var(--surface-2)" }} />
                    <Bar dataKey="revenue" name="Pendapatan" fill={CHART_COLORS[0]} radius={[0, 4, 4, 0]} maxBarSize={22} />
                    <Bar dataKey="profit" name="Laba" fill={CHART_COLORS[2]} radius={[0, 4, 4, 0]} maxBarSize={22} />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </Card>
            <Card>
              <Table>
                <thead>
                  <tr>
                    <Th>#</Th><Th>Produk</Th><Th className="text-right">Qty</Th><Th className="text-right">Pendapatan</Th>
                    <Th className="hidden text-right md:table-cell">Modal</Th><Th className="text-right">Laba</Th><Th className="text-right">Margin</Th>
                  </tr>
                </thead>
                <tbody>
                  {products.data.data.map((r, i) => (
                    <Tr key={r.product_name + r.product_sku}>
                      <Td className="text-muted">{i + 1}</Td>
                      <Td><p className="font-medium">{r.product_name}</p><p className="font-mono text-xs text-muted">{r.product_sku}</p></Td>
                      <Td className="tabular text-right">{number(r.qty)}</Td>
                      <Td className="tabular text-right font-medium">{rupiah(r.revenue)}</Td>
                      <Td className="tabular hidden text-right text-muted md:table-cell">{rupiah(r.cost)}</Td>
                      <Td className="tabular text-right">{rupiah(r.profit)}</Td>
                      <Td className="text-right"><span className={`inline-flex items-center gap-0.5 text-xs font-medium ${r.margin >= 20 ? "text-emerald-600" : r.margin >= 10 ? "text-amber-600" : "text-red-600"}`}>{r.margin}<Percent className="size-3" /></span></Td>
                    </Tr>
                  ))}
                </tbody>
              </Table>
            </Card>
          </div>
        ))}

      {tab === "expenses" &&
        (expenses.isLoading || !expenses.data ? (
          <PageLoader />
        ) : (
          <div className="space-y-6">
            <div className="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-3">
              <StatCard label="Total pengeluaran" value={rupiah(expenses.data.summary.total_expenses)} icon={Wallet} tone="red" />
              <StatCard label="Jumlah catatan" value={expenses.data.summary.total_count} icon={Receipt} tone="blue" />
              <StatCard label="Rata-rata" value={rupiah(expenses.data.summary.avg_expense)} icon={Ban} tone="amber" />
            </div>
            <Card>
              <CardHeader title="Per kategori" />
              {expenses.data.breakdown.length === 0 ? (
                <EmptyState title="Tidak ada pengeluaran pada periode ini" />
              ) : (
                <ul className="divide-y divide-border">
                  {expenses.data.breakdown.map((b, i) => {
                    const pct = expenses.data.summary.total_expenses > 0 ? (toNumber(b.total) / expenses.data.summary.total_expenses) * 100 : 0;
                    return (
                      <li key={b.category} className="px-5 py-3.5">
                        <div className="flex items-center justify-between text-sm">
                          <span className="font-medium">{EXPENSE_LABELS[b.category as keyof typeof EXPENSE_LABELS] ?? b.category} <span className="text-xs font-normal text-muted">({b.count}x)</span></span>
                          <span className="tabular font-semibold">{rupiah(b.total)}</span>
                        </div>
                        <div className="mt-2 flex items-center gap-3">
                          <div className="h-2 flex-1 overflow-hidden rounded-full bg-surface-2">
                            <div className="h-full rounded-full" style={{ width: `${pct}%`, background: CHART_COLORS[i % CHART_COLORS.length] }} />
                          </div>
                          <span className="w-10 text-right text-xs text-muted">{pct.toFixed(0)}%</span>
                        </div>
                      </li>
                    );
                  })}
                </ul>
              )}
            </Card>
          </div>
        ))}

      {tab === "inventory" &&
        (inventory.isLoading || !inventory.data ? (
          <PageLoader />
        ) : (
          <div className="space-y-6">
            <div className="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
              <StatCard label="Produk aktif" value={number(inventory.data.summary.total_products)} icon={Boxes} hint={`${number(inventory.data.summary.total_units)} unit`} />
              <StatCard label="Nilai (harga modal)" value={rupiah(inventory.data.summary.stock_value_cost)} icon={Wallet} tone="violet" />
              <StatCard label="Nilai (harga jual)" value={rupiah(inventory.data.summary.stock_value_price)} icon={DollarSign} tone="blue" hint={`Potensi laba ${rupiah(inventory.data.summary.stock_value_price - inventory.data.summary.stock_value_cost)}`} />
              <StatCard label="Perlu restock" value={inventory.data.summary.low + inventory.data.summary.out} icon={TrendingUp} tone="amber" hint={`${inventory.data.summary.out} habis`} />
            </div>
            <Card>
              <CardHeader title="Nilai persediaan per kategori" />
              <Table>
                <thead>
                  <tr><Th>Kategori</Th><Th className="text-right">Produk</Th><Th className="text-right">Unit</Th><Th className="text-right">Nilai (modal)</Th></tr>
                </thead>
                <tbody>
                  {inventory.data.by_category.map((r) => (
                    <Tr key={r.category}><Td className="font-medium">{r.category}</Td><Td className="tabular text-right">{r.products}</Td><Td className="tabular text-right">{number(r.units)}</Td><Td className="tabular text-right font-medium">{rupiah(r.value)}</Td></Tr>
                  ))}
                </tbody>
              </Table>
            </Card>
          </div>
        ))}
    </RoleGate>
  );
}
