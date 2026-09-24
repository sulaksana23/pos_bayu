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
  p(1, 1, "Indomie Goreng", "IDM-GRG", 3500, 120),
  p(2, 1, "Roti Tawar Sari Roti", "RT-SR", 13000, 40),
  p(3, 1, "Telur Ayam 1kg", "TLR-1KG", 28000, 60, "kg"),
  p(4, 1, "Nugget Ayam 500g", "NGT-500", 32000, 25),
  p(5, 2, "Aqua 600ml", "AQ-600", 4000, 200, "botol"),
  p(6, 2, "Teh Botol Sosro", "TB-SOSRO", 5000, 150, "botol"),
  p(7, 2, "Kopi Kapal Api Sachet", "KPL-API", 1500, 300, "sachet"),
  p(8, 2, "Susu Ultra Coklat 250ml", "ULT-CKL", 6000, 4, "kotak"),
  p(9, 3, "Chitato Sapi Panggang", "CHT-SP", 12000, 45),
  p(10, 3, "Oreo Original", "ORO-ORI", 8500, 70),
  p(11, 3, "Kacang Garuda 200g", "GRD-200", 18000, 36),
  p(12, 3, "Beng Beng", "BNG-BNG", 2500, 120),
  p(13, 4, "Beras 5kg", "BRS-5KG", 70000, 30, "karung"),
  p(14, 4, "Minyak Goreng 2L", "MYK-2L", 35000, 40, "botol"),
  p(15, 4, "Gula Pasir 1kg", "GLA-1KG", 15000, 50, "kg"),
  p(16, 4, "Tepung Terigu 1kg", "TPG-1KG", 12000, 45, "kg"),
  p(17, 5, "Sabun Cuci Piring 800ml", "SBN-CP", 14000, 30, "botol"),
  p(18, 5, "Deterjen Bubuk 800g", "DTR-800", 18000, 28),
  p(19, 5, "Tisu Gulung 4 Roll", "TSU-4R", 16000, 55, "pack"),
  p(20, 5, "Baterai AA 4pcs", "BTR-AA4", 12000, 3, "pack"),
];
