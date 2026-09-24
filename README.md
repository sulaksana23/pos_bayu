# BaliPOS — Frontend

Frontend Point of Sale (POS) BaliPOS, dibangun dengan **Next.js 16 (App Router)**, React 19, TypeScript, dan Tailwind CSS v4.

Repo ini hanya berisi aplikasi frontend. Backend/API dikelola terpisah — atur alamatnya lewat `BACKEND_URL`.

Dokumentasi lengkap (fitur, struktur folder, cara menjalankan, build produksi) ada di [`frontend/README.md`](frontend/README.md).

## Menjalankan

```bash
cd frontend
cp .env.example .env.local   # atur BACKEND_URL ke alamat API Anda
npm install
npm run dev                  # http://localhost:3000
```
