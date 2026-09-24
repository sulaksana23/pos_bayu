"use client";

import { useQuery } from "@tanstack/react-query";
import { api } from "@/lib/api";
import type { Paginated } from "@/lib/types";

type Params = Record<string, string | number | boolean | null | undefined>;

/** Paginated list query; `key` is also used as the invalidation prefix. */
export function useList<T, Extra = object>(key: string, path: string, params: Params, enabled = true) {
  return useQuery({
    queryKey: [key, params],
    queryFn: () => api.get<Paginated<T> & Extra>(path, params),
    placeholderData: (prev) => prev,
    enabled,
  });
}
