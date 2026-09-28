"use client";

import { useState, type ReactNode } from "react";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useQueryClient } from "@tanstack/react-query";
import { LogOut, Menu, Moon, ShoppingCart, Store, Sun, X } from "lucide-react";
import { useAuth } from "@/stores/auth";
import { useCurrentShift } from "@/hooks/use-session";
import { useTheme } from "@/hooks/use-theme";
import { api } from "@/lib/api";
import { APP_NAME } from "@/lib/config";
import { cn, initials, ROLE_LABELS, rupiah, time } from "@/lib/utils";
import { NAV, canSee } from "./nav";
import { Button } from "@/components/ui/button";

export function Logo({ className }: { className?: string }) {
  return (
    <div className={cn("flex items-center gap-2.5", className)}>
      <div className="grid size-9 place-items-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-lg shadow-brand-600/30">
        <Store className="size-5" />
      </div>
      <div className="leading-tight">
        <p className="text-sm font-bold tracking-tight">{APP_NAME}</p>
        <p className="text-[11px] text-muted">Point of Sale</p>
      </div>
    </div>
  );
}

function Sidebar({ onNavigate }: { onNavigate?: () => void }) {
  const pathname = usePathname();
  const role = useAuth((s) => s.user?.role);

  return (
    <nav className="flex-1 space-y-6 overflow-y-auto px-3 py-4">
      {NAV.map((group) => {
        const items = group.items.filter((i) => canSee(i, role));
        if (!items.length) return null;
        return (
          <div key={group.title}>
            <p className="mb-1.5 px-3 text-[11px] font-semibold tracking-wider text-muted/80 uppercase">{group.title}</p>
            <ul className="space-y-0.5">
              {items.map((item) => {
                const active = pathname === item.href || pathname.startsWith(item.href + "/");
                return (
                  <li key={item.href}>
                    <Link
                      href={item.href}
                      onClick={onNavigate}
                      className={cn(
                        "group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors",
                        active ? "bg-brand-500/10 text-brand-700 dark:text-brand-400" : "text-muted hover:bg-surface-2 hover:text-fg",
                      )}
                    >
                      <item.icon className={cn("size-[18px]", active ? "text-brand-600 dark:text-brand-400" : "text-muted group-hover:text-fg")} />
                      {item.label}
                    </Link>
                  </li>
                );
              })}
            </ul>
          </div>
        );
      })}
    </nav>
  );
}

function ShiftPill() {
  const { data: shift } = useCurrentShift();
  if (!shift) {
    return (
      <Link href="/shifts" className="hidden items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-medium text-amber-700 sm:inline-flex dark:text-amber-400">
        <span className="size-1.5 rounded-full bg-amber-500" />
        Shift belum dibuka
      </Link>
    );
  }
  return (
    <Link href="/shifts" className="hidden items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-700 sm:inline-flex dark:text-emerald-400">
      <span className="relative flex size-1.5">
        <span className="absolute inline-flex size-full animate-ping rounded-full bg-emerald-500 opacity-75" />
        <span className="relative inline-flex size-1.5 rounded-full bg-emerald-500" />
      </span>
      Shift sejak {time(shift.opened_at)} · {rupiah(shift.total_sales)}
    </Link>
  );
}

export function UserMenu() {
  const user = useAuth((s) => s.user);
  const clear = useAuth((s) => s.clear);
  const router = useRouter();
  const qc = useQueryClient();
  const [open, setOpen] = useState(false);

  const logout = async () => {
    try {
      await api.post("/auth/logout");
    } catch {
      /* token may already be invalid */
    }
    clear();
    qc.clear();
    router.replace("/login");
  };

  return (
    <div className="relative">
      <button onClick={() => setOpen((v) => !v)} className="flex items-center gap-2.5 rounded-lg p-1 pr-2 hover:bg-surface-2">
        <span className="grid size-8 place-items-center rounded-full bg-gradient-to-br from-slate-700 to-slate-900 text-xs font-semibold text-white dark:from-slate-500 dark:to-slate-700">
          {initials(user?.name)}
        </span>
        <span className="hidden text-left leading-tight md:block">
          <span className="block max-w-32 truncate text-sm font-medium">{user?.name}</span>
          <span className="block text-[11px] text-muted">{user ? ROLE_LABELS[user.role] : ""}</span>
        </span>
      </button>
      {open && (
        <>
          <div className="fixed inset-0 z-30" onClick={() => setOpen(false)} />
          <div className="absolute right-0 z-40 mt-2 w-56 animate-slide-up rounded-xl border border-border bg-surface p-1.5 shadow-xl">
            <div className="border-b border-border px-3 pt-1.5 pb-2.5">
              <p className="truncate text-sm font-medium">{user?.name}</p>
              <p className="truncate text-xs text-muted">{user?.email}</p>
            </div>
            <button onClick={logout} className="mt-1 flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-500/10">
              <LogOut className="size-4" /> Keluar
            </button>
          </div>
        </>
      )}
    </div>
  );
}

export function ThemeToggle() {
  const { dark, toggle } = useTheme();
  return (
    <Button variant="ghost" size="icon" onClick={toggle} aria-label="Ganti tema">
      {dark ? <Sun className="size-[18px]" /> : <Moon className="size-[18px]" />}
    </Button>
  );
}

export function AppShell({ children }: { children: ReactNode }) {
  const [mobileOpen, setMobileOpen] = useState(false);

  return (
    <div className="flex min-h-dvh">
      {/* Desktop sidebar */}
      <aside className="sticky top-0 hidden h-dvh w-64 shrink-0 flex-col border-r border-border bg-surface lg:flex">
        <div className="flex h-16 items-center border-b border-border px-5">
          <Logo />
        </div>
        <Sidebar />
        <div className="border-t border-border p-3">
          <Link href="/cashier" className="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-3 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 hover:bg-brand-700">
            <ShoppingCart className="size-4" /> Buka Kasir
          </Link>
        </div>
      </aside>

      {/* Mobile sidebar */}
      {mobileOpen && (
        <div className="fixed inset-0 z-50 lg:hidden">
          <div className="absolute inset-0 animate-fade-in bg-black/50" onClick={() => setMobileOpen(false)} />
          <aside className="relative flex h-full w-72 flex-col bg-surface shadow-2xl">
            <div className="flex h-16 items-center justify-between border-b border-border px-5">
              <Logo />
              <button onClick={() => setMobileOpen(false)} className="rounded-lg p-1 text-muted hover:bg-surface-2">
                <X className="size-5" />
              </button>
            </div>
            <Sidebar onNavigate={() => setMobileOpen(false)} />
          </aside>
        </div>
      )}

      <div className="flex min-w-0 flex-1 flex-col">
        <header className="sticky top-0 z-20 flex h-16 items-center gap-3 border-b border-border bg-surface/80 px-4 backdrop-blur-md sm:px-6">
          <Button variant="ghost" size="icon" className="lg:hidden" onClick={() => setMobileOpen(true)} aria-label="Menu">
            <Menu className="size-5" />
          </Button>
          <div className="lg:hidden">
            <Logo />
          </div>
          <div className="flex-1" />
          <ShiftPill />
          <ThemeToggle />
          <UserMenu />
        </header>
        <main className="mx-auto w-full max-w-[1400px] flex-1 px-4 py-6 sm:px-6 lg:py-8">{children}</main>
      </div>
    </div>
  );
}
