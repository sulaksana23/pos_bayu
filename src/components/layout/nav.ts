import {
  BarChart3,
  Boxes,
  ClipboardList,
  Clock,
  LayoutDashboard,
  Receipt,
  ShoppingCart,
  Tags,
  Truck,
  UserCog,
  Users,
  Wallet,
  type LucideIcon,
} from "lucide-react";
import type { Role } from "@/lib/types";

export interface NavItem {
  href: string;
  label: string;
  icon: LucideIcon;
  roles?: Role[];
}

export const NAV: { title: string; items: NavItem[] }[] = [
  {
    title: "Utama",
    items: [
      { href: "/dashboard", label: "Dashboard", icon: LayoutDashboard },
      { href: "/cashier", label: "Kasir", icon: ShoppingCart },
      { href: "/transactions", label: "Transaksi", icon: Receipt },
      { href: "/shifts", label: "Shift", icon: Clock },
    ],
  },
  {
    title: "Master Data",
    items: [
      { href: "/products", label: "Produk & Stok", icon: Boxes },
      { href: "/categories", label: "Kategori", icon: Tags, roles: ["admin", "manager"] },
      { href: "/customers", label: "Pelanggan", icon: Users },
    ],
  },
  {
    title: "Pembelian & Biaya",
    items: [
      { href: "/suppliers", label: "Supplier", icon: Truck, roles: ["admin", "manager"] },
      { href: "/purchase-orders", label: "Purchase Order", icon: ClipboardList, roles: ["admin", "manager"] },
      { href: "/expenses", label: "Pengeluaran", icon: Wallet, roles: ["admin", "manager"] },
    ],
  },
  {
    title: "Analitik",
    items: [
      { href: "/reports", label: "Laporan", icon: BarChart3, roles: ["admin", "manager"] },
      { href: "/users", label: "Pengguna", icon: UserCog, roles: ["admin"] },
    ],
  },
];

export function canSee(item: NavItem, role?: Role) {
  if (!item.roles) return true;
  if (role === "superadministrator") return true;
  return !!role && item.roles.includes(role);
}
