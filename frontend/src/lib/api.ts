"use client";

import { API_BASE } from "./config";
import { useAuth } from "@/stores/auth";

export class ApiError extends Error {
  status: number;
  errors: Record<string, string[]>;
  code?: string;

  constructor(status: number, message: string, errors: Record<string, string[]> = {}, code?: string) {
    super(message);
    this.status = status;
    this.errors = errors;
    this.code = code;
  }

  /** First validation message for a field, if any. */
  field(name: string) {
    return this.errors[name]?.[0];
  }
}

type Query = Record<string, string | number | boolean | null | undefined>;

export function qs(params?: Query) {
  if (!params) return "";
  const s = new URLSearchParams();
  for (const [k, v] of Object.entries(params)) {
    if (v === undefined || v === null || v === "") continue;
    s.set(k, typeof v === "boolean" ? (v ? "1" : "0") : String(v));
  }
  const str = s.toString();
  return str ? `?${str}` : "";
}

async function request<T>(method: string, path: string, body?: unknown, query?: Query): Promise<T> {
  const token = useAuth.getState().token;
  const headers: Record<string, string> = { Accept: "application/json" };
  if (token) headers.Authorization = `Bearer ${token}`;

  let payload: BodyInit | undefined;
  let httpMethod = method;
  if (body instanceof FormData) {
    // PHP only parses multipart bodies on POST — spoof other verbs.
    if (method !== "POST") {
      body.append("_method", method);
      httpMethod = "POST";
    }
    payload = body;
  } else if (body !== undefined) {
    headers["Content-Type"] = "application/json";
    payload = JSON.stringify(body);
  }

  let res: Response;
  try {
    res = await fetch(`${API_BASE}${path}${qs(query)}`, { method: httpMethod, headers, body: payload });
  } catch {
    throw new ApiError(0, "Tidak dapat terhubung ke server. Periksa koneksi Anda.");
  }

  const text = await res.text();
  let json: Record<string, unknown> = {};
  try {
    json = text ? JSON.parse(text) : {};
  } catch {
    /* non-JSON response */
  }

  if (!res.ok) {
    if (res.status === 401 && token) {
      useAuth.getState().clear();
    }
    const fallback =
      res.status === 403
        ? "Anda tidak memiliki akses."
        : res.status === 404
          ? "Data tidak ditemukan."
          : res.status >= 500
            ? "Terjadi kesalahan pada server."
            : "Permintaan gagal.";
    throw new ApiError(
      res.status,
      (json.message as string) || fallback,
      (json.errors as Record<string, string[]>) ?? {},
      json.code as string | undefined,
    );
  }

  return json as T;
}

export const api = {
  get: <T>(path: string, query?: Query) => request<T>("GET", path, undefined, query),
  post: <T>(path: string, body?: unknown) => request<T>("POST", path, body ?? {}),
  put: <T>(path: string, body?: unknown) => request<T>("PUT", path, body ?? {}),
  patch: <T>(path: string, body?: unknown) => request<T>("PATCH", path, body ?? {}),
  delete: <T>(path: string) => request<T>("DELETE", path),
};

export function errorMessage(e: unknown) {
  if (e instanceof ApiError) {
    const first = Object.values(e.errors)[0]?.[0];
    return first ?? e.message;
  }
  return e instanceof Error ? e.message : "Terjadi kesalahan.";
}
