export const API_BASE = "/laravel/api";
export const STORAGE_BASE = "/laravel";

export const APP_NAME = process.env.NEXT_PUBLIC_APP_NAME ?? "BaliPOS";
export const STORE = {
  name: process.env.NEXT_PUBLIC_STORE_NAME ?? "Toko BaliPOS",
  address: process.env.NEXT_PUBLIC_STORE_ADDRESS ?? "Bali, Indonesia",
  phone: process.env.NEXT_PUBLIC_STORE_PHONE ?? "",
};

/** Sewa bulanan / beli source code — tampilkan di modal harga (lihat PricingModal). */
export const PRICING = {
  monthly: process.env.NEXT_PUBLIC_PRICE_MONTHLY || "Hubungi kami",
  source: process.env.NEXT_PUBLIC_PRICE_SOURCE || "Hubungi kami",
};

/**
 * Kanal kontak untuk pembelian/lisensi. Kosongkan env var untuk
 * menyembunyikan tombolnya (mis. belum ada nomor WhatsApp resmi).
 */
export const CONTACT = {
  whatsapp: process.env.NEXT_PUBLIC_CONTACT_WHATSAPP || null,
  email: process.env.NEXT_PUBLIC_CONTACT_EMAIL || null,
  otherLabel: process.env.NEXT_PUBLIC_CONTACT_OTHER_LABEL || null,
  otherUrl: process.env.NEXT_PUBLIC_CONTACT_OTHER_URL || null,
};

/** wa.me link with a prefilled message; digits-only, country code included (mis. 62812xxxxxxx). */
export function waLink(number: string, message: string) {
  return `https://wa.me/${number.replace(/\D/g, "")}?text=${encodeURIComponent(message)}`;
}

/** Resolve an image path from Laravel (e.g. /storage/products/x.jpg). */
export function assetUrl(path: string | null | undefined) {
  if (!path) return null;
  if (/^https?:\/\//.test(path)) return path;
  return `${STORAGE_BASE}${path.startsWith("/") ? "" : "/"}${path}`;
}
