"use client";

import { useEffect, type ReactNode } from "react";
import { usePathname, useRouter } from "next/navigation";
import { ShieldAlert } from "lucide-react";
import { useAuth, useAuthHydrated } from "@/stores/auth";
import { useSession } from "@/hooks/use-session";
import type { Role } from "@/lib/types";
import { EmptyState, Spinner } from "@/components/ui/misc";

export function AuthGuard({ children }: { children: ReactNode }) {
  const hydrated = useAuthHydrated();
  const token = useAuth((s) => s.token);
  const router = useRouter();
  const pathname = usePathname();
  useSession();

  useEffect(() => {
    if (hydrated && !token) router.replace(`/login?next=${encodeURIComponent(pathname)}`);
  }, [hydrated, token, router, pathname]);

  if (!hydrated || !token) {
    return (
      <div className="flex min-h-dvh items-center justify-center">
        <Spinner className="size-8" />
      </div>
    );
  }
  return <>{children}</>;
}

/** Renders children only for the given roles (superadministrator always passes). */
export function RoleGate({ roles, children }: { roles: Role[]; children: ReactNode }) {
  const role = useAuth((s) => s.user?.role);
  if (role !== "superadministrator" && (!role || !roles.includes(role))) {
    return <EmptyState icon={ShieldAlert} title="Akses ditolak" description="Halaman ini hanya untuk peran tertentu. Hubungi admin jika Anda membutuhkan akses." />;
  }
  return <>{children}</>;
}
