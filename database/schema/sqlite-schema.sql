CREATE TABLE "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "role" varchar check("role" in('admin', 'cashier', 'manager')) not null default 'cashier',
  "pin" varchar,
  "phone" varchar,
  "avatar_url" varchar,
  "is_active" tinyint(1) not null default '1',
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE INDEX "users_role_index" on "users"("role");
CREATE INDEX "users_is_active_index" on "users"("is_active");
CREATE TABLE "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_expiration_index" on "cache"("expiration");
CREATE TABLE "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_locks_expiration_index" on "cache_locks"("expiration");
CREATE TABLE "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" varchar not null,
  "queue" varchar not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE INDEX "failed_jobs_connection_queue_failed_at_index" on "failed_jobs"(
  "connection",
  "queue",
  "failed_at"
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE "pos_categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "color" varchar not null default '#f97316',
  "icon" varchar not null default 'fa-tag',
  "sort_order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "pos_categories_slug_unique" on "pos_categories"("slug");
CREATE INDEX "pos_categories_sort_order_index" on "pos_categories"(
  "sort_order"
);
CREATE INDEX "pos_categories_is_active_index" on "pos_categories"("is_active");
CREATE TABLE "pos_products"(
  "id" integer primary key autoincrement not null,
  "category_id" integer,
  "name" varchar not null,
  "sku" varchar not null,
  "barcode" varchar,
  "price" numeric not null default '0',
  "cost" numeric,
  "stock" integer not null default '0',
  "min_stock" integer not null default '0',
  "unit" varchar not null default 'pcs',
  "image_url" varchar,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("category_id") references "pos_categories"("id") on delete set null
);
CREATE INDEX "pos_products_is_active_name_index" on "pos_products"(
  "is_active",
  "name"
);
CREATE UNIQUE INDEX "pos_products_sku_unique" on "pos_products"("sku");
CREATE UNIQUE INDEX "pos_products_barcode_unique" on "pos_products"("barcode");
CREATE INDEX "pos_products_is_active_index" on "pos_products"("is_active");
CREATE TABLE "pos_customers"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "phone" varchar,
  "email" varchar,
  "address" text,
  "points" integer not null default '0',
  "total_spent" numeric not null default '0',
  "visit_count" integer not null default '0',
  "notes" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "pos_customers_phone_index" on "pos_customers"("phone");
CREATE INDEX "pos_customers_is_active_index" on "pos_customers"("is_active");
CREATE TABLE "pos_shifts"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "opening_cash" numeric not null default '0',
  "closing_cash" numeric,
  "expected_cash" numeric,
  "cash_difference" numeric,
  "transaction_count" integer not null default '0',
  "total_sales" numeric not null default '0',
  "total_cash" numeric not null default '0',
  "total_non_cash" numeric not null default '0',
  "opened_at" datetime not null default CURRENT_TIMESTAMP,
  "closed_at" datetime,
  "status" varchar check("status" in('open', 'closed')) not null default 'open',
  "opening_notes" text,
  "closing_notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "pos_shifts_user_id_status_index" on "pos_shifts"(
  "user_id",
  "status"
);
CREATE INDEX "pos_shifts_status_index" on "pos_shifts"("status");
CREATE TABLE "pos_transactions"(
  "id" integer primary key autoincrement not null,
  "invoice_no" varchar not null,
  "shift_id" integer not null,
  "user_id" integer not null,
  "customer_id" integer,
  "subtotal" numeric not null default '0',
  "discount" numeric not null default '0',
  "tax" numeric not null default '0',
  "total" numeric not null default '0',
  "paid" numeric not null default '0',
  "change_amount" numeric not null default '0',
  "payment_method" varchar check("payment_method" in('cash', 'qris', 'transfer', 'wallet', 'mixed')) not null default 'cash',
  "payment_details" text,
  "status" varchar check("status" in('completed', 'void', 'pending', 'held')) not null default 'completed',
  "notes" text,
  "void_reason" varchar,
  "voided_at" datetime,
  "voided_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("shift_id") references "pos_shifts"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete restrict,
  foreign key("customer_id") references "pos_customers"("id") on delete set null,
  foreign key("voided_by") references "users"("id") on delete set null
);
CREATE INDEX "pos_transactions_created_at_status_index" on "pos_transactions"(
  "created_at",
  "status"
);
CREATE INDEX "pos_transactions_shift_id_status_index" on "pos_transactions"(
  "shift_id",
  "status"
);
CREATE UNIQUE INDEX "pos_transactions_invoice_no_unique" on "pos_transactions"(
  "invoice_no"
);
CREATE INDEX "pos_transactions_payment_method_index" on "pos_transactions"(
  "payment_method"
);
CREATE INDEX "pos_transactions_status_index" on "pos_transactions"("status");
CREATE TABLE "pos_transaction_items"(
  "id" integer primary key autoincrement not null,
  "transaction_id" integer not null,
  "product_id" integer,
  "product_name" varchar not null,
  "product_sku" varchar,
  "price" numeric not null default '0',
  "cost" numeric,
  "qty" integer not null default '1',
  "discount" numeric not null default '0',
  "subtotal" numeric not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("transaction_id") references "pos_transactions"("id") on delete cascade,
  foreign key("product_id") references "pos_products"("id") on delete set null
);
CREATE INDEX "pos_transaction_items_transaction_id_index" on "pos_transaction_items"(
  "transaction_id"
);
CREATE TABLE "pos_stock_movements"(
  "id" integer primary key autoincrement not null,
  "product_id" integer not null,
  "user_id" integer not null,
  "type" varchar check("type" in('in', 'out', 'sale', 'return', 'adjust')) not null default 'in',
  "qty" integer not null,
  "unit_cost" numeric,
  "reference_type" varchar,
  "reference_id" integer,
  "notes" text,
  "created_at" datetime not null default CURRENT_TIMESTAMP,
  "updated_at" datetime not null default CURRENT_TIMESTAMP,
  foreign key("product_id") references "pos_products"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete restrict
);
CREATE INDEX "pos_stock_movements_product_id_type_created_at_index" on "pos_stock_movements"(
  "product_id",
  "type",
  "created_at"
);
CREATE INDEX "pos_stock_movements_type_index" on "pos_stock_movements"("type");
CREATE TABLE "pos_invoice_counters"(
  "id" integer primary key autoincrement not null,
  "date" date not null,
  "last_seq" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "pos_invoice_counters_date_unique" on "pos_invoice_counters"(
  "date"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2026_06_27_000001_create_pos_categories_table',1);
INSERT INTO migrations VALUES(5,'2026_06_27_000002_create_pos_products_table',1);
INSERT INTO migrations VALUES(6,'2026_06_27_000003_create_pos_customers_table',1);
INSERT INTO migrations VALUES(7,'2026_06_27_000004_create_pos_shifts_table',1);
INSERT INTO migrations VALUES(8,'2026_06_27_000005_create_pos_transactions_table',1);
INSERT INTO migrations VALUES(9,'2026_06_27_000006_create_pos_transaction_items_table',1);
INSERT INTO migrations VALUES(10,'2026_06_27_000007_create_pos_stock_movements_table',1);
INSERT INTO migrations VALUES(11,'2026_06_27_000008_create_pos_invoice_counters_table',1);
