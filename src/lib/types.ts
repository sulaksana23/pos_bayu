export type Role = "superadministrator" | "admin" | "manager" | "cashier";

export interface Paginated<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
}

export interface Shift {
  id: number;
  user_id: number;
  user?: { id: number; name: string };
  opening_cash: string;
  closing_cash: string | null;
  expected_cash: string | null;
  cash_difference: string | null;
  transaction_count: number;
  total_sales: string;
  total_cash: string | null;
  total_non_cash: string | null;
  opened_at: string;
  closed_at: string | null;
  status: "open" | "closed";
  opening_notes: string | null;
  closing_notes: string | null;
  transactions?: Transaction[];
}

export interface User {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  role: Role;
  avatar_url?: string | null;
  is_active?: boolean;
  created_at?: string;
  current_shift?: Shift | null;
}

export interface Category {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  color: string | null;
  icon: string | null;
  sort_order: number;
  is_active: boolean;
  products_count?: number;
}

export interface Product {
  id: number;
  category_id: number | null;
  category?: Category | null;
  name: string;
  sku: string;
  barcode: string | null;
  price: string;
  cost: string | null;
  stock: number;
  min_stock: number;
  unit: string;
  image_url: string | null;
  description: string | null;
  is_active: boolean;
  created_at?: string;
}

export interface StockMovement {
  id: number;
  product_id: number;
  user?: { id: number; name: string } | null;
  type: "in" | "out" | "sale" | "return" | "adjust";
  qty: number;
  unit_cost: string | null;
  reference_type: string | null;
  reference_id: number | null;
  notes: string | null;
  created_at: string;
}

export interface Customer {
  id: number;
  name: string;
  phone: string | null;
  email: string | null;
  address: string | null;
  points: number;
  total_spent: string;
  visit_count: number;
  notes: string | null;
  is_active: boolean;
  transactions_count?: number;
  transactions?: Transaction[];
  created_at?: string;
}

export type PaymentMethod = "cash" | "qris" | "transfer" | "wallet" | "mixed";

export interface TransactionItem {
  id: number;
  product_id: number | null;
  product_name: string;
  product_sku: string | null;
  price: string;
  cost: string | null;
  qty: number;
  discount: string;
  subtotal: string;
}

export interface Transaction {
  id: number;
  invoice_no: string;
  shift_id: number;
  user_id: number;
  customer_id: number | null;
  customer?: Pick<Customer, "id" | "name" | "phone"> | null;
  cashier?: { id: number; name: string } | null;
  subtotal: string;
  discount: string;
  tax: string;
  total: string;
  paid: string;
  change_amount: string;
  payment_method: PaymentMethod;
  status: "completed" | "void" | "pending" | "held";
  notes: string | null;
  void_reason: string | null;
  voided_at: string | null;
  items?: TransactionItem[];
  items_count?: number;
  created_at: string;
}

export interface Supplier {
  id: number;
  name: string;
  company: string | null;
  phone: string | null;
  email: string | null;
  address: string | null;
  pic_name: string | null;
  pic_phone: string | null;
  tax_id: string | null;
  notes: string | null;
  is_active: boolean;
  purchase_orders_count?: number;
  purchase_orders?: PurchaseOrder[];
}

export type PoStatus = "pending" | "ordered" | "received" | "cancelled";

export interface PurchaseOrderItem {
  id?: number;
  product_id: number | null;
  product_name: string;
  product_sku: string | null;
  qty: number;
  price: string | number;
  subtotal?: string;
  product?: Pick<Product, "id" | "name" | "stock" | "unit"> | null;
}

export interface PurchaseOrder {
  id: number;
  po_no: string;
  supplier_id: number;
  supplier?: Pick<Supplier, "id" | "name" | "company"> & Partial<Supplier>;
  user?: { id: number; name: string } | null;
  status: PoStatus;
  order_date: string | null;
  expected_date: string | null;
  received_date: string | null;
  subtotal: string;
  discount: string;
  tax: string;
  total: string;
  notes: string | null;
  items?: PurchaseOrderItem[];
  items_count?: number;
  created_at: string;
}

export type ExpenseCategory = "operational" | "utilities" | "rent" | "salary" | "maintenance" | "marketing" | "other";

export interface Expense {
  id: number;
  expense_no: string;
  user?: { id: number; name: string } | null;
  category: ExpenseCategory;
  amount: string;
  description: string;
  expense_date: string;
  payment_method: string | null;
  receipt_image: string | null;
  receipt_url?: string | null;
  notes: string | null;
}

export interface DashboardData {
  kpi: {
    today_sales: number;
    today_count: number;
    today_avg: number;
    today_profit: number;
    today_margin: number;
    sales_growth: number;
    count_growth: number;
    profit_growth: number;
    month_sales: number;
    month_growth: number;
    month_profit: number;
    month_expenses: number;
    month_net_profit: number;
  };
  weekly: { date: string; label: string; sales: number; profit: number; count: number }[];
  hourly: { hour: string; sales: number; count: number }[];
  top_products: { product_name: string; qty: number | string; revenue: number | string }[];
  payments: { payment_method: PaymentMethod; count: number; total: number | string }[];
  stock: {
    total: number;
    low: number;
    out: number;
    low_items: Pick<Product, "id" | "name" | "stock" | "min_stock" | "unit">[];
  };
  customers: { total: number; new_month: number };
  recent: Transaction[];
  pending_pos: PurchaseOrder[];
  active_shifts: Shift[];
  current_shift: Shift | null;
}
