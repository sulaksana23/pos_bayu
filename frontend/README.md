# BaliPOS — Frontend (Next.js)

Antarmuka BaliPOS yang sepenuhnya dibangun ulang dengan **Next.js 16 (App Router) + React 19 + TypeScript**.
Laravel di root repo berperan sebagai **backend API** (Sanctum token).

## Fitur

| Halaman | Isi |
|---|---|
| **Login** | Login email + password, redirect ke halaman tujuan |
| **Dashboard** | KPI hari ini vs kemarin (penjualan, transaksi, laba, bulan ini), tren 7 hari, penjualan per jam, metode bayar, produk terlaris, stok menipis, transaksi terbaru, PO aktif, kasir aktif |
| **Kasir** (`/cashier`) | Layar penuh, grid produk + filter kategori, pencarian & **scan barcode/SKU (Enter)**, keranjang dengan qty & diskon per item, diskon & pajak %, pilih/tambah pelanggan, **tahan & lanjutkan pesanan**, pembayaran Tunai/QRIS/Transfer/E-Wallet dengan tombol uang cepat & kembalian, struk 80 mm siap cetak, gerbang buka shift, tampilan mobile (bottom sheet) |
| **Transaksi** | Filter tanggal/status/metode, detail struk, cetak ulang, void (manager/admin), ekspor CSV |
| **Shift** | Buka shift (modal awal), ringkasan shift berjalan, tutup shift dengan hitung selisih kas, riwayat & detail |
| **Produk & stok** | Statistik stok, filter, CRUD produk + upload gambar, penyesuaian stok (masuk/keluar/opname), riwayat pergerakan stok, ekspor CSV |
| **Kategori** | CRUD dengan warna & urutan |
| **Pelanggan** | CRUD, riwayat transaksi, total belanja |
| **Supplier** | CRUD, aktif/nonaktif |
| **Purchase order** | Buat/edit PO, alur Draft → Dipesan → Diterima (stok otomatis bertambah), batal, cetak |
| **Pengeluaran** | CRUD + upload bukti, filter periode, total |
| **Laporan** | Penjualan (grafik harian, metode bayar, performa kasir, laba kotor & bersih), produk (margin), pengeluaran per kategori, nilai inventaris; preset periode & ekspor CSV |
| **Pengguna** | CRUD staf, peran, PIN, aktif/nonaktif (admin) |

Lainnya: dark mode, responsif (desktop/tablet/HP), menu & halaman sesuai peran, toast notifikasi, shortcut keyboard kasir.

### Shortcut kasir

| Tombol | Aksi |
|---|---|
| `F2` | Fokus ke pencarian / scan barcode |
| `Enter` (di pencarian) | Tambah produk berdasarkan barcode/SKU |
| `F4` | Pilih pelanggan |
| `F9` / `F12` | Bayar |
| `Esc` | Tutup dialog / kosongkan pencarian |

## Arsitektur

```
Browser ──► Next.js (:3000) ──/laravel/*──► Laravel (:8000) /api/*
```

- Semua request API lewat **rewrite** `/laravel/*` → `BACKEND_URL`, jadi tidak perlu konfigurasi CORS dan gambar `/storage/*` ikut terlayani.
- Token Sanctum disimpan di `localStorage` (Zustand persist). 401 → otomatis logout.
- Data server dikelola TanStack Query; keranjang & pesanan ditahan disimpan lokal (tidak hilang saat refresh).

```
src/
├── app/
│   ├── login/            # halaman login
│   ├── cashier/          # layar kasir penuh
│   └── (app)/            # halaman dengan sidebar (dashboard, produk, laporan, …)
├── components/
│   ├── ui/               # button, input, modal/drawer, table, badge, dll
│   ├── layout/           # app shell, sidebar, auth & role guard
│   └── pos/              # struk, pembayaran, form produk/PO, shift, dll
├── hooks/                # useList, useCrud, useSession, useTheme
├── lib/                  # api client, tipe, format rupiah/tanggal
└── stores/               # auth & keranjang (Zustand)
```

## Menjalankan

```bash
# 1. Backend (dari root repo)
php artisan serve            # http://127.0.0.1:8000

# 2. Frontend
cd frontend
cp .env.example .env.local   # atur BACKEND_URL & info toko untuk struk
npm install
npm run dev                  # http://localhost:3000
```

Akun demo dari seeder: `admin@balitechsolution.com`, `manager@balitechsolution.com`, `kasir1@balitechsolution.com` — password `password`.

### Produksi

```bash
npm run build
BACKEND_URL=https://api-pos.example.com npm start
# atau pakai output standalone: node .next/standalone/server.js
```

## Skrip

| Perintah | Fungsi |
|---|---|
| `npm run dev` | Server pengembangan |
| `npm run build` | Build produksi |
| `npm run lint` | ESLint |
| `npm run typecheck` | Pemeriksaan TypeScript |
