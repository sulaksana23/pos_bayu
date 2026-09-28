"use client";

import { useQuery } from "@tanstack/react-query";
import { api } from "@/lib/api";
import type { Shift, User } from "@/lib/types";
import { useAuth } from "@/stores/auth";

/** Keeps the cached user fresh and exposes the current open shift. */
export function useSession() {
  const token = useAuth((s) => s.token);
  const user = useAuth((s) => s.user);
  const setUser = useAuth((s) => s.setUser);

  useQuery({
    queryKey: ["me"],
    enabled: !!token,
    queryFn: async () => {
      const res = await api.get<{ data: User }>("/auth/user");
      setUser(res.data);
      return res.data;
    },
  });

  return { user, token };
}

export function useCurrentShift() {
  const token = useAuth((s) => s.token);
  return useQuery({
    queryKey: ["shift", "current"],
    enabled: !!token,
    queryFn: async () => (await api.get<{ data: Shift | null }>("/shifts/current")).data,
  });
}
