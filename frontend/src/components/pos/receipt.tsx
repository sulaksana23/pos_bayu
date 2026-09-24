import type { Transaction } from "@/lib/types";
import { STORE } from "@/lib/config";
import { dateTime, number, PAYMENT_LABELS, rupiah, toNumber } from "@/lib/utils";

/** 80mm thermal-style receipt. Wrap in `.print-area` to print only this. */
export function Receipt({ trx }: { trx: Transaction }) {
  const line = "border-t border-dashed border-slate-400 my-2";
  return (
    <div className="mx-auto w-full max-w-[320px] bg-white p-4 font-mono text-[12px] leading-relaxed text-slate-900">
      <div className="text-center">
        <p className="text-sm font-bold">{STORE.name}</p>
        <p>{STORE.address}</p>
        {STORE.phone && <p>Telp. {STORE.phone}</p>}
      </div>
      <div className={line} />
      <div className="space-y-0.5">
        <Row l="No" r={trx.invoice_no} />
        <Row l="Tanggal" r={dateTime(trx.created_at)} />
        <Row l="Kasir" r={trx.cashier?.name ?? "-"} />
        {trx.customer && <Row l="Pelanggan" r={trx.customer.name} />}
      </div>
      <div className={line} />
      {trx.items?.map((it) => (
        <div key={it.id} className="mb-1">
          <p>{it.product_name}</p>
          <Row l={`  ${number(it.qty)} x ${number(it.price)}`} r={number(toNumber(it.price) * it.qty)} />
          {toNumber(it.discount) > 0 && <Row l="  Diskon" r={`-${number(it.discount)}`} />}
        </div>
      ))}
      <div className={line} />
      <Row l="Subtotal" r={number(trx.subtotal)} />
      {toNumber(trx.discount) > 0 && <Row l="Diskon" r={`-${number(trx.discount)}`} />}
      {toNumber(trx.tax) > 0 && <Row l="Pajak" r={number(trx.tax)} />}
      <div className="flex justify-between text-sm font-bold">
        <span>TOTAL</span>
        <span>{rupiah(trx.total)}</span>
      </div>
      <Row l={`Bayar (${PAYMENT_LABELS[trx.payment_method]})`} r={number(trx.paid)} />
      <Row l="Kembali" r={number(trx.change_amount)} />
      {trx.status === "void" && <p className="mt-2 text-center font-bold">*** VOID ***</p>}
      {trx.notes && <p className="mt-2">Catatan: {trx.notes}</p>}
      <div className={line} />
      <p className="text-center">Terima kasih atas kunjungan Anda!</p>
      <p className="text-center text-[10px] text-slate-500">Barang yang sudah dibeli tidak dapat ditukar</p>
    </div>
  );
}

function Row({ l, r }: { l: string; r: string }) {
  return (
    <div className="flex justify-between gap-2">
      <span className="whitespace-pre">{l}</span>
      <span className="text-right">{r}</span>
    </div>
  );
}
