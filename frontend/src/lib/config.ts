export const API_BASE = "/laravel/api";
export const STORAGE_BASE = "/laravel";

export const APP_NAME = process.env.NEXT_PUBLIC_APP_NAME ?? "BaliPOS";
export const STORE = {
  name: process.env.NEXT_PUBLIC_STORE_NAME ?? "Toko BaliPOS",
  address: process.env.NEXT_PUBLIC_STORE_ADDRESS ?? "Bali, Indonesia",
  phone: process.env.NEXT_PUBLIC_STORE_PHONE ?? "",
};

/** Resolve an image path from Laravel (e.g. /storage/products/x.jpg). */
export function assetUrl(path: string | null | undefined) {
  if (!path) return null;
  if (/^https?:\/\//.test(path)) return path;
  return `${STORAGE_BASE}${path.startsWith("/") ? "" : "/"}${path}`;
}
