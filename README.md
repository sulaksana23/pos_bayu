# BaliPOS — Point of Sale System

**v0.0.23** — Laravel 13 · PHP 8.4 · PostgreSQL · Alpine.js · Tailwind CSS v4

BaliPOS adalah sistem Point of Sale (POS) modern yang dibangun khusus untuk bisnis ritel Indonesia — mulai dari warung, toko kelontong, hingga retail skala menengah. Dikembangkan oleh **Bali Tech Solution** di bawah naungan ekosistem Balitech.

> **Produksi:** [pos.balitechsolution.com](https://pos.balitechsolution.com)

---

## Fitur

### Kasir (Core POS)
- Antarmuka kasir full-screen dengan grid produk dan pencarian real-time
- Dukungan scan barcode (termasuk shortcut F2)
- Kategori produk sebagai filter cepat
- Manajemen keranjang belanja dengan kontrol qty
- Multi payment method: **Cash**, **QRIS**, **Transfer**, **Wallet**
- Hitung kembalian otomatis + tombol cepat nominal uang
- Cetak struk setelah transaksi
- Void transaksi dengan pengembalian stok otomatis

### Manajemen Produk & Kategori
- CRUD Produk: SKU, barcode, harga jual, harga modal, stok, unit, gambar
- Kategori: warna, icon, urutan sortir
- Generate barcode otomatis
- Alert stok minimum
- Non-aktifkan produk (soft deactivation) untuk preservasi histori

### Manajemen Pelanggan
- Data pelanggan: nama, telepon, email, alamat
- Riwayat transaksi per pelanggan
- Poin loyalitas, total kunjungan, total belanja

### Shift Kasir
- Buka/tutup shift dengan setoran awal
- Hitung otomatis selisih kas (expected vs actual)
- Rekapitulasi transaksi per shift

### Dashboard & Analitik
- KPI real-time: penjualan hari ini, profit, breakdown tunai/non-tunai
- Grafik tren 7 hari
- Produk terlaris
- Metode pembayaran terpakai
- Status stok (stok aman / menipis / habis)
- Pengeluaran terkini
- PO pending

### Pembelian & Supplier
- Manajemen Supplier: PIC, telepon, NPWP
- Purchase Order: lifecycle dari draft → dipesan → diterima
- Update stok otomatis saat PO diterima
- Penomoran PO otomatis: `PO-{YYYYMMDD}-{XXXX}`

### Pengeluaran (Expenses)
- Kategori: operational, utilities, rent, salary, maintenance, marketing
- Upload gambar bukti pembayaran
- Filter rentang tanggal

### Pembukuan (Accounting)
- Ringkasan penjualan month-to-date
- Grafik tren harian
- Breakdown metode pembayaran
- Riwayat shift yang sudah ditutup

### Laporan & Ekspor
- Laporan penjualan (harian, periode, metode bayar)
- Laporan produk (terlaris, margin)
- Laporan pelanggan
- Laporan inventaris
- Laporan pengeluaran
- Ekspor CSV

### Manajemen User & RBAC
- Role: **superadministrator**, **admin**, **manager**, **cashier**
- Login dengan email + password + PIN
- Aktif/non-aktifkan user
- Permission policy: manajer bisa atur stok, kasir hanya transaksi

### API (Sanctum)
- REST API untuk integrasi eksternal
- Autentikasi token (30 hari masa berlaku)
- Endpoint: produk, pelanggan, kategori, transaksi, laporan, supplier, PO, pengeluaran

### SSO
- Single Sign-On terintegrasi dengan dashboard utama Balitech
- Shared session Redis (DB 2) untuk login lintas subdomain
- Verifikasi token via internal network (server-to-server)

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.4, Laravel 13, Laravel Sanctum, Spatie Permission |
| **Database** | PostgreSQL (produksi), SQLite (dev) |
| **Frontend** | Alpine.js 3, Tailwind CSS v4, Blade |
| **Build** | Vite 8, Laravel Vite Plugin |
| **Cache** | File-based (`pos_` prefix) |
| **Session** | Redis (produksi), DB 2 untuk shared SSO |
| **Queue** | Database-driven |
| **Icons** | Font Awesome 6.4 |
| **Fonts** | Inter, JetBrains Mono |

---

## Persyaratan Sistem

- PHP ^8.3 | ^8.4
- Composer 2
- PostgreSQL 15+ (atau SQLite untuk lokal)
- Redis (untuk session)
- Node.js 20+

---

## Instalasi

```bash
# Clone repositori
git clone <repo-url> balipos
cd balipos

# Setup lingkungan
cp .env.example .env
# Edit .env sesuai environment Anda

# Setup aplikasi (composer + key + migrate + npm build)
composer run setup

# Atau manual step-by-step:
composer install
php artisan key:generate
php artisan migrate --force
npm install --ignore-scripts
npm run build
```

## Development

```bash
composer run dev
```

Perintah di atas menjalankan secara concurrent:
- `php artisan serve`
- `php artisan queue:listen --tries=1 --timeout=0`
- `php artisan pail --timeout=0`
- `npm run dev`

## Testing

```bash
composer run test
```

## Seeder

```bash
php artisan db:seed
# atau hanya seeder tertentu:
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=DemoDataSeeder
```

---

## Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/          # REST API controllers (9)
│   │   ├── Auth/         # Login controller
│   │   ├── Pos/          # POS web controllers (15)
│   │   ├── Controller.php
│   │   └── SsoCallbackController.php
│   └── Middleware/        # 6 custom middleware
├── Models/                # 13 Eloquent models
└── Providers/
database/
├── migrations/            # 20 migration files
├── seeders/               # 6 seeders
└── factories/
resources/
├── css/app.css            # Tailwind CSS v4 entry
├── js/
│   ├── app.js             # Alpine.js bootstrap
│   └── alpine-stores.js   # Global store (sidebar state)
└── views/
    ├── layouts/           # app, auth, cashier layout
    ├── auth/
    ├── components/ui/     # 6 reusable Blade components
    ├── partials/          # sidebar, navbar, alert
    └── pos/               # 15 feature view directories
routes/
├── web.php                # Semua web routes
├── api.php                # Sanctum API routes
└── console.php
```

---

## Model & Database

### Models (13)

| Model | Table | Deskripsi |
|-------|-------|-----------|
| `User` | `users` | User dengan role, PIN, status aktif |
| `Category` | `pos_categories` | Kategori produk (warna, icon, urutan) |
| `Product` | `pos_products` | Produk dengan SKU, barcode, stok, harga |
| `Customer` | `pos_customers` | Pelanggan dengan poin & histori |
| `Supplier` | `pos_suppliers` | Pemasok dengan detail PIC & NPWP |
| `PosShift` | `pos_shifts` | Shift kasir (buka/tutup, setoran) |
| `PosTransaction` | `pos_transactions` | Transaksi penjualan |
| `PosTransactionItem` | `pos_transaction_items` | Item dalam transaksi |
| `PosStockMovement` | `pos_stock_movements` | Riwayat pergerakan stok |
| `PosInvoiceCounter` | `pos_invoice_counters` | Counter nomor invoice harian |
| `PurchaseOrder` | `pos_purchase_orders` | Purchase Order |
| `PurchaseOrderItem` | `pos_purchase_order_items` | Item dalam PO |
| `Expense` | `pos_expenses` | Pengeluaran bisnis |

### Penomoran Otomatis
- **Invoice:** `TRX{YYYYMMDD}-{XXXX}` (atomic via PostgreSQL upsert)
- **PO:** `PO-{YYYYMMDD}-{XXXX}`
- **Expense:** `EXP-{YYYYMMDD}-{XXXX}`

---

## API

### Autentikasi
```http
POST /api/auth/login
Content-Type: application/json

{ "email": "...", "password": "..." }
# Response: { "token": "..." }
```

### Endpoints
Semua endpoint (kecuali login) memerlukan header `Authorization: Bearer {token}`.

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/auth/login` | Login dapat token |
| POST | `/api/auth/logout` | Revoke token |
| GET | `/api/auth/user` | Info user saat ini |
| GET | `/api/products` | Daftar produk |
| GET | `/api/products/{id}` | Detail produk |
| GET | `/api/categories` | Daftar kategori |
| GET | `/api/customers` | Daftar pelanggan |
| GET | `/api/customers/{id}` | Detail pelanggan |
| GET | `/api/transactions` | Daftar transaksi |
| GET | `/api/transactions/{id}` | Detail transaksi |
| GET | `/api/reports/sales` | Laporan penjualan |
| GET | `/api/reports/expenses` | Laporan pengeluaran |
| GET | `/api/suppliers` | Daftar supplier |
| GET | `/api/purchase-orders` | Daftar PO |
| GET | `/api/expenses` | Daftar pengeluaran |

---

## SSO Integration

BaliPOS terintegrasi dengan dashboard utama Balitech via shared Redis session:

1. User login di `balitechsolution.com`
2. Main site generate token → redirect ke `pos.balitechsolution.com/sso/callback?token=...`
3. POS verifikasi token ke `DASHBOARD_INTERNAL_URL` (internal network)
4. User auto login via shared session Redis DB 2

Konfigurasi SSO:
```
SESSION_DRIVER=redis
SESSION_CONNECTION=session
SESSION_DOMAIN=.balitechsolution.com
DASHBOARD_URL=https://balitechsolution.com
DASHBOARD_INTERNAL_URL=http://devstack-app
SSO_SHARED_SECRET=...
```

---

## Konfigurasi Lingkungan

### Produksi (PostgreSQL + Redis)

```
APP_NAME=BaliPOS
APP_ENV=production
APP_URL=https://pos.balitechsolution.com

DB_CONNECTION=pgsql
DB_HOST=pos-db
DB_DATABASE=pos

SESSION_DRIVER=redis
SESSION_CONNECTION=session
SESSION_COOKIE=pos_session

CACHE_STORE=file
CACHE_PREFIX=pos_

QUEUE_CONNECTION=database
```

### Development Lokal (SQLite)

Copy `.env.example` dan sesuaikan:
```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
```

---

## Panduan Kontribusi

Silakan baca [CODING_GUIDELINES.md](CODING_GUIDELINES.md) untuk standar penulisan kode yang digunakan di proyek ini, mencakup:

- Struktur dan konvensi Blade components
- Best practices Alpine.js (component pattern, stores)
- Best practices Tailwind CSS (utility-first, `@apply`)
- Aturan formatting (Prettier, Laravel Pint, ESLint)
- Aksesibilitas dan performa

---

## Lisensi

MIT License — dikembangkan oleh [Bali Tech Solution](https://balitechsolution.com).
