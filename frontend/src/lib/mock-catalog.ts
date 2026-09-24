import type { Category, Product } from "./types";

/**
 * Static sample catalog for the public demo cashier (`/demo`) — used while
 * no backend is connected. Swap `/demo` to fetch `/api/demo/catalog` once
 * a real API is available (see components/pos frontend/README.md).
 */
export const MOCK_CATEGORIES: Category[] = [
  { id: 1, name: "Makanan", slug: "makanan", description: null, color: "#f97316", icon: "UtensilsCrossed", sort_order: 1, is_active: true },
  { id: 2, name: "Minuman", slug: "minuman", description: null, color: "#3b82f6", icon: "CupSoda", sort_order: 2, is_active: true },
  { id: 3, name: "Snack", slug: "snack", description: null, color: "#a855f7", icon: "Cookie", sort_order: 3, is_active: true },
  { id: 4, name: "Sembako", slug: "sembako", description: null, color: "#10b981", icon: "ShoppingBasket", sort_order: 4, is_active: true },
  { id: 5, name: "Rumah Tangga", slug: "rumah-tangga", description: null, color: "#ef4444", icon: "SprayCan", sort_order: 5, is_active: true },
  { id: 6, name: "Kesehatan", slug: "kesehatan", description: null, color: "#ec4899", icon: "Pill", sort_order: 6, is_active: true },
  { id: 7, name: "Alat Tulis", slug: "alat-tulis", description: null, color: "#14b8a6", icon: "PenTool", sort_order: 7, is_active: true },
];

const p = (id: number, category_id: number, name: string, sku: string, price: number, stock: number, unit = "pcs"): Product => ({
  id,
  category_id,
  category: MOCK_CATEGORIES.find((c) => c.id === category_id) ?? null,
  name,
  sku,
  barcode: null,
  price: String(price),
  cost: null,
  stock,
  min_stock: 5,
  unit,
  image_url: null,
  description: null,
  is_active: true,
});

export const MOCK_PRODUCTS: Product[] = [
  // Makanan
  p(1, 1, "Indomie Goreng", "IDM-GRG", 3500, 120),
  p(2, 1, "Roti Tawar Sari Roti", "RT-SR", 13000, 40),
  p(3, 1, "Telur Ayam 1kg", "TLR-1KG", 28000, 60, "kg"),
  p(4, 1, "Nugget Ayam 500g", "NGT-500", 32000, 25),
  p(5, 1, "Sosis Sapi Isi 10", "SSS-10", 24000, 30),
  p(6, 1, "Mie Sedaap Goreng", "MSD-GRG", 3000, 100),
  // Minuman
  p(7, 2, "Aqua 600ml", "AQ-600", 4000, 200, "botol"),
  p(8, 2, "Teh Botol Sosro", "TB-SOSRO", 5000, 150, "botol"),
  p(9, 2, "Kopi Kapal Api Sachet", "KPL-API", 1500, 300, "sachet"),
  p(10, 2, "Susu Ultra Coklat 250ml", "ULT-CKL", 6000, 4, "kotak"),
  p(11, 2, "Pocari Sweat 500ml", "PCR-500", 9000, 45, "botol"),
  p(12, 2, "Es Teh Kotak", "EST-KTK", 3500, 80, "kotak"),
  // Snack
  p(13, 3, "Chitato Sapi Panggang", "CHT-SP", 12000, 45),
  p(14, 3, "Oreo Original", "ORO-ORI", 8500, 70),
  p(15, 3, "Kacang Garuda 200g", "GRD-200", 18000, 36),
  p(16, 3, "Beng Beng", "BNG-BNG", 2500, 120),
  p(17, 3, "Silverqueen Coklat 65g", "SQ-65", 15000, 30),
  // Sembako
  p(18, 4, "Beras 5kg", "BRS-5KG", 70000, 30, "karung"),
  p(19, 4, "Minyak Goreng 2L", "MYK-2L", 35000, 40, "botol"),
  p(20, 4, "Gula Pasir 1kg", "GLA-1KG", 15000, 50, "kg"),
  p(21, 4, "Tepung Terigu 1kg", "TPG-1KG", 12000, 45, "kg"),
  p(22, 4, "Garam Dapur 500g", "GRM-500", 4000, 60),
  // Rumah Tangga
  p(23, 5, "Sabun Cuci Piring 800ml", "SBN-CP", 14000, 30, "botol"),
  p(24, 5, "Deterjen Bubuk 800g", "DTR-800", 18000, 28),
  p(25, 5, "Tisu Gulung 4 Roll", "TSU-4R", 16000, 55, "pack"),
  p(26, 5, "Baterai AA 4pcs", "BTR-AA4", 12000, 3, "pack"),
  p(27, 5, "Kantong Plastik Besar", "KTG-BSR", 8000, 70, "pack"),
  // Kesehatan
  p(28, 6, "Paracetamol Tablet", "PCT-TAB", 6000, 80, "strip"),
  p(29, 6, "Betadine 15ml", "BTD-15", 9000, 40, "botol"),
  p(30, 6, "Masker Medis 50pcs", "MSK-50", 25000, 20, "box"),
  p(31, 6, "Vitamin C 1000mg", "VTC-1000", 22000, 35, "strip"),
  // Alat Tulis
  p(32, 7, "Pulpen Standard AE7", "PLP-AE7", 3000, 90),
  p(33, 7, "Buku Tulis 38 Lembar", "BKU-38", 4000, 100),
  p(34, 7, "Penghapus Karet", "PGH-KRT", 2000, 60),
  p(35, 7, "Spidol Whiteboard", "SPD-WB", 7500, 25),
];
