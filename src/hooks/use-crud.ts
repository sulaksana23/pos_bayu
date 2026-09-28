"use client";

import { useState } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";
import { api, ApiError, errorMessage } from "@/lib/api";

/**
 * Save (create/update) + delete mutations for a REST resource, with
 * toast feedback, 422 error capture and cache invalidation.
 */
export function useCrud<TForm>(resource: string, invalidate: string[], onSaved?: () => void) {
  const qc = useQueryClient();
  const [errors, setErrors] = useState<ApiError | null>(null);
  const refresh = () => invalidate.forEach((k) => qc.invalidateQueries({ queryKey: [k] }));

  const save = useMutation({
    mutationFn: ({ id, data }: { id?: number; data: TForm }) =>
      id ? api.put<{ message: string }>(`/${resource}/${id}`, data) : api.post<{ message: string }>(`/${resource}`, data),
    onMutate: () => setErrors(null),
    onSuccess: (res) => {
      toast.success(res.message);
      refresh();
      onSaved?.();
    },
    onError: (e) => {
      if (e instanceof ApiError && e.status === 422) setErrors(e);
      toast.error(errorMessage(e));
    },
  });

  const remove = useMutation({
    mutationFn: (id: number) => api.delete<{ message: string }>(`/${resource}/${id}`),
    onSuccess: (res) => {
      toast.success(res.message);
      refresh();
    },
    onError: (e) => toast.error(errorMessage(e)),
  });

  return { save, remove, errors, setErrors, refresh };
}
