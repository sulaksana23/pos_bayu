import Link from "next/link";

export default function NotFound() {
  return (
    <div className="flex min-h-dvh flex-col items-center justify-center gap-3 p-6 text-center">
      <p className="text-6xl font-bold text-brand-600">404</p>
      <p className="text-lg font-semibold">Halaman tidak ditemukan</p>
      <Link href="/dashboard" className="text-sm text-brand-600 hover:underline">
        Kembali ke dashboard
      </Link>
    </div>
  );
}
