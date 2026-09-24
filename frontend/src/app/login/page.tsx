"use client";

import { Suspense, useEffect, useState, type FormEvent } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { BarChart3, Eye, EyeOff, Lock, Mail, ShieldCheck, Zap } from "lucide-react";
import { toast } from "sonner";
import { api, ApiError } from "@/lib/api";
import type { User } from "@/lib/types";
import { useAuth, useAuthHydrated } from "@/stores/auth";
import { Button } from "@/components/ui/button";
import { Field, Input } from "@/components/ui/input";
import { Logo, ThemeToggle } from "@/components/layout/app-shell";

function LoginForm() {
  const router = useRouter();
  const params = useSearchParams();
  const { token, setSession } = useAuth();
  const hydrated = useAuthHydrated();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [show, setShow] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<ApiError | null>(null);

  const next = params.get("next");
  const target = next && next.startsWith("/") && !next.startsWith("//") ? next : "/dashboard";

  useEffect(() => {
    if (hydrated && token) router.replace(target);
  }, [hydrated, token, router, target]);

  const submit = async (e: FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      const res = await api.post<{ token: string; user: User }>("/auth/login", { email, password, device: "balipos-web" });
      setSession(res.token, res.user);
      toast.success(`Selamat datang, ${res.user.name}!`);
      router.replace(target);
    } catch (err) {
      setError(err instanceof ApiError ? err : new ApiError(0, "Gagal masuk."));
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={submit} className="space-y-4">
      {error && !error.field("email") && !error.field("password") && (
        <div className="rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2.5 text-sm text-red-700 dark:text-red-400">{error.message}</div>
      )}
      <Field label="Email" error={error?.field("email")}>
        <div className="relative">
          <Mail className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted" />
          <Input type="email" autoComplete="email" required autoFocus value={email} onChange={(e) => setEmail(e.target.value)} placeholder="nama@toko.com" className="h-11 pl-9" invalid={!!error?.field("email")} />
        </div>
      </Field>
      <Field label="Password" error={error?.field("password")}>
        <div className="relative">
          <Lock className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted" />
          <Input type={show ? "text" : "password"} autoComplete="current-password" required value={password} onChange={(e) => setPassword(e.target.value)} placeholder="••••••••" className="h-11 pr-10 pl-9" />
          <button type="button" onClick={() => setShow((v) => !v)} className="absolute top-1/2 right-3 -translate-y-1/2 text-muted hover:text-fg" aria-label="Tampilkan password">
            {show ? <EyeOff className="size-4" /> : <Eye className="size-4" />}
          </button>
        </div>
      </Field>
      <Button type="submit" size="lg" className="w-full" loading={loading}>
        Masuk
      </Button>
    </form>
  );
}

const features = [
  { icon: Zap, title: "Kasir super cepat", text: "Scan barcode, shortcut keyboard, tahan pesanan & cetak struk." },
  { icon: BarChart3, title: "Analitik real-time", text: "Penjualan, profit, dan stok terpantau langsung dari dashboard." },
  { icon: ShieldCheck, title: "Aman & terkontrol", text: "Hak akses per peran, shift kasir, dan riwayat stok lengkap." },
];

export default function LoginPage() {
  return (
    <div className="grid min-h-dvh lg:grid-cols-[1.1fr_1fr]">
      <div className="relative hidden overflow-hidden bg-gradient-to-br from-brand-700 via-brand-800 to-slate-950 p-12 text-white lg:flex lg:flex-col">
        <div className="absolute -top-32 -right-32 size-[28rem] rounded-full bg-brand-400/20 blur-3xl" />
        <div className="absolute -bottom-40 -left-20 size-[32rem] rounded-full bg-emerald-300/10 blur-3xl" />
        <div className="relative [&_p]:text-white/70 [&_p:first-child]:text-white">
          <Logo />
        </div>
        <div className="relative my-auto max-w-lg">
          <h1 className="text-4xl leading-tight font-bold tracking-tight">Kelola toko lebih cerdas, dari kasir sampai laporan.</h1>
          <p className="mt-4 text-lg text-white/70">Satu sistem untuk penjualan, stok, pembelian, dan keuangan bisnis ritel Anda.</p>
          <div className="mt-10 space-y-5">
            {features.map((f) => (
              <div key={f.title} className="flex gap-4">
                <div className="grid size-10 shrink-0 place-items-center rounded-xl bg-white/10 ring-1 ring-white/15">
                  <f.icon className="size-5" />
                </div>
                <div>
                  <p className="font-semibold">{f.title}</p>
                  <p className="text-sm text-white/65">{f.text}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
        <p className="relative text-xs text-white/50">© {new Date().getFullYear()} Bali Tech Solution</p>
      </div>

      <div className="relative flex items-center justify-center p-6">
        <div className="absolute top-4 right-4">
          <ThemeToggle />
        </div>
        <div className="w-full max-w-sm">
          <Logo className="mb-10 lg:hidden" />
          <h2 className="text-2xl font-semibold tracking-tight">Masuk ke akun Anda</h2>
          <p className="mt-1.5 mb-8 text-sm text-muted">Gunakan email dan password yang diberikan admin toko.</p>
          <Suspense>
            <LoginForm />
          </Suspense>
        </div>
      </div>
    </div>
  );
}
