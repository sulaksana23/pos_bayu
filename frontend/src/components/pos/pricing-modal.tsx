"use client";

import { Check, Code2, Mail, MessageCircle, Rocket, Server } from "lucide-react";
import { CONTACT, PRICING, waLink } from "@/lib/config";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Modal } from "@/components/ui/modal";

const RENT_PERKS = ["Hosting, database & backup dikelola", "Update fitur otomatis", "Support & konsultasi setup"];
const BUY_PERKS = ["Source code lengkap (frontend + backend)", "Install & jalankan di server sendiri", "Tanpa biaya langganan bulanan"];

function Plan({
  icon: Icon,
  title,
  price,
  period,
  perks,
  whatsappMessage,
  highlight,
}: {
  icon: typeof Rocket;
  title: string;
  price: string;
  period?: string;
  perks: string[];
  whatsappMessage: string;
  highlight?: boolean;
}) {
  return (
    <div className={cn("flex flex-col rounded-2xl border p-5", highlight ? "border-brand-500 bg-brand-500/5 ring-1 ring-brand-500/20" : "border-border bg-surface")}>
      <div className="flex items-center gap-2.5">
        <span className={cn("grid size-9 place-items-center rounded-xl", highlight ? "bg-brand-600 text-white" : "bg-surface-2 text-muted")}>
          <Icon className="size-4.5" />
        </span>
        <p className="font-semibold">{title}</p>
      </div>
      <p className="mt-4">
        <span className="text-2xl font-bold tracking-tight">{price}</span>
        {period && <span className="text-sm text-muted"> {period}</span>}
      </p>
      <ul className="mt-4 flex-1 space-y-2">
        {perks.map((p) => (
          <li key={p} className="flex items-start gap-2 text-sm text-muted">
            <Check className="mt-0.5 size-4 shrink-0 text-brand-600 dark:text-brand-400" />
            {p}
          </li>
        ))}
      </ul>
      {CONTACT.whatsapp && (
        <a href={waLink(CONTACT.whatsapp, whatsappMessage)} target="_blank" rel="noreferrer" className="mt-5">
          <Button className="w-full gap-1.5" variant={highlight ? "primary" : "outline"}>
            <MessageCircle className="size-4" /> Tanya via WhatsApp
          </Button>
        </a>
      )}
    </div>
  );
}

export function PricingModal({ open, onClose }: { open: boolean; onClose: () => void }) {
  const noContact = !CONTACT.whatsapp && !CONTACT.email && !CONTACT.otherUrl;

  return (
    <Modal
      open={open}
      onClose={onClose}
      title="Pakai data toko Anda sendiri"
      description="Halaman ini hanya demo dengan data contoh. Untuk produk, transaksi, dan laporan milik toko Anda sendiri, pilih salah satu paket berikut."
      size="lg"
    >
      <div className="grid gap-4 sm:grid-cols-2">
        <Plan icon={Server} title="Sewa Bulanan" price={PRICING.monthly} period="/ bulan" perks={RENT_PERKS} whatsappMessage="Halo, saya mau tanya paket sewa bulanan BaliPOS." highlight />
        <Plan icon={Code2} title="Beli Source Code" price={PRICING.source} perks={BUY_PERKS} whatsappMessage="Halo, saya mau tanya harga beli source code BaliPOS." />
      </div>

      {(CONTACT.email || CONTACT.otherUrl || noContact) && (
        <div className="mt-4 flex flex-col items-center gap-2 border-t border-border pt-4 sm:flex-row sm:justify-center sm:gap-3">
          {noContact ? (
            <p className="text-sm text-muted">Kontak pembelian belum diatur — hubungi pemilik aplikasi ini secara langsung.</p>
          ) : (
            <>
              <p className="text-sm text-muted">Atau hubungi lewat:</p>
              {CONTACT.email && (
                <a href={`mailto:${CONTACT.email}`} className="inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">
                  <Mail className="size-4" /> {CONTACT.email}
                </a>
              )}
              {CONTACT.otherUrl && (
                <a href={CONTACT.otherUrl} target="_blank" rel="noreferrer" className="inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 hover:underline dark:text-brand-400">
                  {CONTACT.otherLabel || CONTACT.otherUrl}
                </a>
              )}
            </>
          )}
        </div>
      )}
    </Modal>
  );
}
