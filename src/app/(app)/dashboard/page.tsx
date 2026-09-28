"use client";

import Link from "next/link";
import { useQuery } from "@tanstack/react-query";
import { Area, AreaChart, Bar, BarChart, CartesianGrid, Cell, Pie, PieChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from "recharts";
import { AlertTriangle, ArrowRight, ClipboardList, DollarSign, Package, Receipt, ShoppingCart, TrendingUp, Users, Wallet } from "lucide-react";
import { api } from "@/lib/api";
import type { DashboardData } from "@/lib/types";
import { useAuth } from "@/stores/auth";
import { cn, date, isBackOffice, number, PAYMENT_LABELS, PO_STATUS, rupiah, rupiahShort, time, toNumber } from "@/lib/utils";
import { Card, CardHeader } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { EmptyState, PageHeader, Skeleton, StatCard } from "@/components/ui/misc";
import { ChartTooltip, CHART_COLORS } from "@/components/pos/chart";
import { PaymentBadge, TrxStatusBadge } from "@/components/pos/status";
import { Button } from "@/components/ui/button";

function greeting() {
  const h = new Date().getHours();
  if (h < 11) return "Selamat pagi";
  if (h < 15) return "Selamat siang";
  if (h < 19) return "Selamat sore";
  return "Selamat malam";
}

export default function DashboardPage() {
  const user = useAuth((s) => s.user);
  const { data, isLoading } = useQuery({
    queryKey: ["dashboard"],
    queryFn: async () => (await api.get<{ data: DashboardData }>("/dashboard")).data,
    refetchInterval: 60_000,
  });
  const backOffice = isBackOffice(user?.role);

  return (
    <>
      <PageHeader
        title={`${greeting()}, ${user?.name?.split(" ")[0] ?? ""} 👋`}
        description={`Ringkasan bisnis Anda hari ini, ${date(new Date().toISOString())}.`}
        actions={
          <Link href="/cashier">
            <Button>
              <ShoppingCart className="size-4" /> Mulai Transaksi
            </Button>
          </Link>
        }
      />

      {isLoading || !data ? (
        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
          {Array.from({ length: 8 }).map((_, i) => (
            <Skeleton key={i} className={cn("h-32", i >= 4 && "h-72 sm:col-span-2")} />
          ))}
        </div>
      ) : (
        <div className="space-y-6">
          <div className="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            <StatCard label="Penjualan hari ini" value={rupiah(data.kpi.today_sales)} icon={DollarSign} growth={data.kpi.sales_growth} hint="vs kemarin" />
            <StatCard label="Transaksi" value={number(data.kpi.today_count)} icon={Receipt} growth={data.kpi.count_growth} hint={`Rata-rata ${rupiah(data.kpi.today_avg)}`} tone="blue" />
            <StatCard label="Laba kotor" value={rupiah(data.kpi.today_profit)} icon={TrendingUp} growth={data.kpi.profit_growth} hint={`Margin ${data.kpi.today_margin}%`} tone="violet" />
            <StatCard
              label="Penjualan bulan ini"
              value={rupiah(data.kpi.month_sales)}
              icon={Wallet}
              growth={data.kpi.month_growth}
              hint={backOffice ? `Laba bersih ${rupiah(data.kpi.month_net_profit)}` : "vs bulan lalu"}
              tone="amber"
            />
          </div>

          <div className="grid gap-6 xl:grid-cols-3">
            <Card className="xl:col-span-2">
              <CardHeader title="Tren 7 hari" description="Penjualan dan laba kotor harian" />
              <div className="h-72 p-4 pl-0">
                <ResponsiveContainer width="100%" height="100%">
                  <AreaChart data={data.weekly} margin={{ left: 8, right: 8 }}>
                    <defs>
                      <linearGradient id="gSales" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stopColor={CHART_COLORS[0]} stopOpacity={0.3} />
                        <stop offset="100%" stopColor={CHART_COLORS[0]} stopOpacity={0} />
                      </linearGradient>
                      <linearGradient id="gProfit" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stopColor={CHART_COLORS[2]} stopOpacity={0.25} />
                        <stop offset="100%" stopColor={CHART_COLORS[2]} stopOpacity={0} />
                      </linearGradient>
                    </defs>
                    <CartesianGrid strokeDasharray="3 3" stroke="var(--border)" vertical={false} />
                    <XAxis dataKey="label" tick={{ fontSize: 12, fill: "var(--muted)" }} axisLine={false} tickLine={false} />
                    <YAxis tickFormatter={rupiahShort} tick={{ fontSize: 12, fill: "var(--muted)" }} axisLine={false} tickLine={false} width={56} />
                    <Tooltip content={<ChartTooltip />} />
                    <Area type="monotone" dataKey="sales" name="Penjualan" stroke={CHART_COLORS[0]} strokeWidth={2.5} fill="url(#gSales)" />
                    <Area type="monotone" dataKey="profit" name="Laba" stroke={CHART_COLORS[2]} strokeWidth={2} fill="url(#gProfit)" />
                  </AreaChart>
                </ResponsiveContainer>
              </div>
            </Card>

            <Card>
              <CardHeader title="Metode pembayaran" description="Hari ini" />
              {data.payments.length === 0 ? (
                <EmptyState title="Belum ada transaksi hari ini" />
              ) : (
                <div className="p-4">
                  <div className="h-44">
                    <ResponsiveContainer width="100%" height="100%">
                      <PieChart>
                        <Pie
                          data={data.payments.map((p) => ({ name: PAYMENT_LABELS[p.payment_method], value: toNumber(p.total) }))}
                          dataKey="value"
                          innerRadius={52}
                          outerRadius={78}
                          paddingAngle={3}
                          stroke="none"
                        >
                          {data.payments.map((_, i) => (
                            <Cell key={i} fill={CHART_COLORS[i % CHART_COLORS.length]} />
                          ))}
                        </Pie>
                        <Tooltip content={<ChartTooltip />} />
                      </PieChart>
                    </ResponsiveContainer>
                  </div>
                  <ul className="mt-3 space-y-2">
                    {data.payments.map((p, i) => (
                      <li key={p.payment_method} className="flex items-center gap-2 text-sm">
                        <span className="size-2.5 rounded-sm" style={{ background: CHART_COLORS[i % CHART_COLORS.length] }} />
                        {PAYMENT_LABELS[p.payment_method]}
                        <span className="text-xs text-muted">({p.count}x)</span>
                        <span className="tabular ml-auto font-medium">{rupiah(p.total)}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              )}
            </Card>
          </div>

          <div className="grid gap-6 xl:grid-cols-3">
            <Card className="xl:col-span-2">
              <CardHeader title="Penjualan per jam" description="Hari ini, 06:00 – 23:00" />
              <div className="h-60 p-4 pl-0">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={data.hourly} margin={{ left: 8, right: 8 }}>
                    <CartesianGrid strokeDasharray="3 3" stroke="var(--border)" vertical={false} />
                    <XAxis dataKey="hour" tick={{ fontSize: 11, fill: "var(--muted)" }} axisLine={false} tickLine={false} interval={1} />
                    <YAxis tickFormatter={rupiahShort} tick={{ fontSize: 12, fill: "var(--muted)" }} axisLine={false} tickLine={false} width={56} />
                    <Tooltip content={<ChartTooltip />} cursor={{ fill: "var(--surface-2)" }} />
                    <Bar dataKey="sales" name="Penjualan" fill={CHART_COLORS[1]} radius={[4, 4, 0, 0]} maxBarSize={28} />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </Card>

            <Card>
              <CardHeader title="Produk terlaris" description="7 hari terakhir" />
              {data.top_products.length === 0 ? (
                <EmptyState icon={Package} title="Belum ada penjualan" />
              ) : (
                <ul className="divide-y divide-border">
                  {data.top_products.map((p, i) => {
                    const max = toNumber(data.top_products[0].revenue) || 1;
                    return (
                      <li key={p.product_name} className="px-5 py-3">
                        <div className="flex items-center gap-3 text-sm">
                          <span className="grid size-6 place-items-center rounded-md bg-surface-2 text-xs font-semibold text-muted">{i + 1}</span>
                          <span className="min-w-0 flex-1 truncate font-medium">{p.product_name}</span>
                          <span className="tabular font-medium">{rupiah(p.revenue)}</span>
                        </div>
                        <div className="mt-2 ml-9 flex items-center gap-2">
                          <div className="h-1.5 flex-1 overflow-hidden rounded-full bg-surface-2">
                            <div className="h-full rounded-full bg-brand-500" style={{ width: `${(toNumber(p.revenue) / max) * 100}%` }} />
                          </div>
                          <span className="text-xs text-muted">{number(p.qty)} terjual</span>
                        </div>
                      </li>
                    );
                  })}
                </ul>
              )}
            </Card>
          </div>

          <div className="grid gap-6 xl:grid-cols-3">
            <Card className="xl:col-span-2">
              <CardHeader
                title="Transaksi terbaru"
                action={
                  <Link href="/transactions" className="inline-flex items-center gap-1 text-xs font-medium text-brand-600 hover:underline">
                    Lihat semua <ArrowRight className="size-3.5" />
                  </Link>
                }
              />
              {data.recent.length === 0 ? (
                <EmptyState icon={Receipt} title="Belum ada transaksi" />
              ) : (
                <ul className="divide-y divide-border">
                  {data.recent.map((t) => (
                    <li key={t.id} className="flex items-center gap-3 px-5 py-3 text-sm">
                      <div className="min-w-0 flex-1">
                        <p className="font-mono text-xs font-medium">{t.invoice_no}</p>
                        <p className="truncate text-xs text-muted">
                          {time(t.created_at)} · {t.cashier?.name} · {t.customer?.name ?? "Umum"} · {t.items_count} item
                        </p>
                      </div>
                      <div className="hidden sm:block">
                        <PaymentBadge method={t.payment_method} />
                      </div>
                      <TrxStatusBadge status={t.status} />
                      <span className={cn("tabular w-28 text-right font-semibold", t.status === "void" && "text-muted line-through")}>{rupiah(t.total)}</span>
                    </li>
                  ))}
                </ul>
              )}
            </Card>

            <div className="space-y-6">
              <Card>
                <CardHeader
                  title="Stok perlu perhatian"
                  description={`${data.stock.low} menipis · ${data.stock.out} habis dari ${data.stock.total} produk`}
                  action={<AlertTriangle className={cn("size-4", data.stock.low + data.stock.out > 0 ? "text-amber-500" : "text-muted")} />}
                />
                {data.stock.low_items.length === 0 ? (
                  <EmptyState icon={Package} title="Semua stok aman" />
                ) : (
                  <ul className="divide-y divide-border">
                    {data.stock.low_items.map((p) => (
                      <li key={p.id} className="flex items-center justify-between gap-3 px-5 py-2.5 text-sm">
                        <span className="truncate">{p.name}</span>
                        <Badge tone="amber">
                          {p.stock}/{p.min_stock} {p.unit}
                        </Badge>
                      </li>
                    ))}
                  </ul>
                )}
              </Card>

              <Card className="p-5">
                <div className="flex items-center gap-4">
                  <div className="grid size-11 place-items-center rounded-xl bg-sky-500/10 text-sky-600">
                    <Users className="size-5" />
                  </div>
                  <div>
                    <p className="text-sm text-muted">Pelanggan terdaftar</p>
                    <p className="text-xl font-semibold">{number(data.customers.total)}</p>
                  </div>
                  <Badge tone="blue" className="ml-auto">
                    +{data.customers.new_month} bulan ini
                  </Badge>
                </div>
              </Card>

              {backOffice && data.pending_pos.length > 0 && (
                <Card>
                  <CardHeader title="Purchase order aktif" action={<ClipboardList className="size-4 text-muted" />} />
                  <ul className="divide-y divide-border">
                    {data.pending_pos.map((po) => (
                      <li key={po.id}>
                        <Link href={`/purchase-orders/${po.id}`} className="flex items-center gap-3 px-5 py-2.5 text-sm hover:bg-surface-2/50">
                          <div className="min-w-0 flex-1">
                            <p className="font-mono text-xs">{po.po_no}</p>
                            <p className="truncate text-xs text-muted">{po.supplier?.name}</p>
                          </div>
                          <Badge tone={PO_STATUS[po.status].tone}>{PO_STATUS[po.status].label}</Badge>
                        </Link>
                      </li>
                    ))}
                  </ul>
                </Card>
              )}

              {data.active_shifts.length > 0 && (
                <Card>
                  <CardHeader title="Kasir aktif" description={`${data.active_shifts.length} shift terbuka`} />
                  <ul className="divide-y divide-border">
                    {data.active_shifts.map((s) => (
                      <li key={s.id} className="flex items-center justify-between px-5 py-2.5 text-sm">
                        <span className="flex items-center gap-2">
                          <span className="size-2 rounded-full bg-emerald-500" />
                          {s.user?.name}
                        </span>
                        <span className="text-xs text-muted">sejak {time(s.opened_at)}</span>
                      </li>
                    ))}
                  </ul>
                </Card>
              )}
            </div>
          </div>
        </div>
      )}
    </>
  );
}
