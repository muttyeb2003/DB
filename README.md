# MUC Web Application — PHP + MySQL

A small CRUD-style web app built with **PHP (PDO)** and **MySQL** to manage parts, suppliers, orders, and simple budget analytics.

This README explains the **files**, **setup**, **data loading** (from JSON to MySQL), and **how to run** the app locally (no Docker required).

---

## 1) Repository structure (what each file does)

**Web app (PHP + CSS)**

* `index.php` — Main router/page with buttons to open each feature page.
* `db.php` — Database connection (PDO). **Update this** to your local DB host/user/pass/db.
* `style.css` — Styling for the whole app.
* `show_table.php` — Form to type a table name (`parts`, `suppliers`, `orders`, `supplier_phones`, `order_parts`) and display its rows.
* `add_supplier.php` — Form to insert a new supplier into `suppliers` (with duplicate checks and basic validation).
* `annual_expenses.php` — Given a **year range**, computes total supplier expenses per year from `orders`/`order_parts`.
* `budget_projection.php` — Uses a simple growth-rate projection based on recent expenses.

**Database DDL + data pipeline**

* `parts_table.sql` — Creates table `parts(_id, price, description)`.
* `make_tables.sql` — Creates relational tables: `suppliers`, `supplier_phones`, `orders`, `order_parts` (with FKs to `parts`). Drops/recreates on reruns.
* `parts_100.json` — Sample parts data (JSON lines or array style, used to seed `parts`).
* `suppliers_100.json` — Sample suppliers with `tel` arrays.
* `orders_4000.json` — Sample orders ({ when, supp\_id, items:\[{part\_id, qty}] }).
* `generate_inserts.php` — Reads the supplier and order JSON files and **generates** `suppliers_orders_inserts.sql` with `INSERT` statements for `suppliers`, `supplier_phones`, `orders`, `order_parts` (deduplicates and aggregates order items per part).
* `j2sql_parts2.sh` — Helper script: imports `parts_100.json` into Mongo (optional), exports CSV, converts to TSV, and `LOAD DATA` into MySQL `parts`. (You can skip the Mongo step and load directly if you want.)
* `j2sql_supp+order.sh` — Helper script to: run `make_tables.sql`, call `generate_inserts.php`, then load `suppliers_orders_inserts.sql` into MySQL.
* `scripts_08.zip` — Archive of helper scripts (not required if you run steps manually).

---

## 2) Quick start (local, **no Docker**, using XAMPP/MAMP/WAMP)

### Prereqs

* **PHP** and **MySQL**. On Windows, install **XAMPP** (includes Apache + PHP + MySQL). On macOS, **MAMP** or XAMPP.

### Steps

1. **Create a database**

   * Start MySQL (via XAMPP/MAMP).
   * Go to `http://localhost/phpmyadmin` → create a DB, e.g., `mucdb`.

2. **Create tables**

   * In phpMyAdmin → select `mucdb` → **Import** → upload `parts_table.sql` and run it.
   * Import `make_tables.sql` and run it (creates `suppliers`, `supplier_phones`, `orders`, `order_parts`).

3. **Load data**
   **Option A — Run the helper scripts (CLI):**

   * Edit `j2sql_parts2.sh` and `j2sql_supp+order.sh` with your MySQL credentials.
   * Run `bash j2sql_parts2.sh` to fill `parts`.
   * Run `bash j2sql_supp+order.sh` to create other tables and load suppliers + orders.

   **Option B — Manual (no shell scripts):**

   * **Parts**: Convert `parts_100.json` to a tab-separated file `parts.tsv` with columns `_id	price	description` and run:

     ```sql
     LOAD DATA LOCAL INFILE 'parts.tsv' INTO TABLE parts;
     ```

     (Or just write INSERTs—`parts_table.sql` defines the schema.)
   * **Suppliers & Orders**: Run in terminal:

     ```bash
     php generate_inserts.php
     ```

     This creates `suppliers_orders_inserts.sql`. Then import that file into `mucdb` via phpMyAdmin **Import**.

4. **Configure the app**

   * Update `db.php` to point at your local DB:

     ```php
     $host = 'localhost';
     $user = 'root';               // XAMPP default
     $pass = '';                   // XAMPP default (empty)
     $db   = 'mucdb';
     ```

5. **Run the site**

   * Put the repo folder into your web root, e.g., `C:/xampp/htdocs/muc` on Windows.
   * Start Apache + MySQL from XAMPP.
   * Open `http://localhost/muc/index.php`.

---

## 3) Using the app

* **Show Table** → Type one of: `parts`, `suppliers`, `orders`, `supplier_phones`, `order_parts`.
* **Add Supplier** → Insert a new supplier (id, name, email). Duplicate IDs/emails are handled.
* **Annual Expenses** → Enter a 2-year range (e.g., 2019–2022). It will compute totals per year.
* **Budget Projection** → Enter growth rate and years to project; it uses recent totals as a base.

---

## 4) Typical execution order (fresh DB)

1. `parts_table.sql` → creates `parts`.
2. Load `parts`: either `j2sql_parts2.sh` or manual TSV/INSERTs.
3. `make_tables.sql` → creates `suppliers`, `supplier_phones`, `orders`, `order_parts` with FKs to `parts`.
4. `php generate_inserts.php` → produces `suppliers_orders_inserts.sql`.
5. Import `suppliers_orders_inserts.sql` → populates suppliers, phones, orders, order\_parts.
6. Use the website via `index.php`.

---

## 5) Troubleshooting

* **`LOAD DATA LOCAL INFILE is disabled`** → Add `--local-infile=1` to your MySQL client command or enable it in server config.
* **Foreign key errors** → Ensure `parts` is loaded **before** `order_parts`.
* **Duplicate key errors** → Rerun `make_tables.sql` to drop/recreate tables, then reload.
* **PDO connection failure** → Check `db.php` host/user/password/db; for XAMPP, user is `root` and password is often empty.

---

## 6) Security notes (for GitHub)

* Do **not** commit real passwords or production hosts in `db.php`.
* Add a `.env` (if you later use a library) or keep a `db.local.php` ignored by Git.

---

## 7) Optional next steps

* **Deployment**: Upload to shared PHP hosting (cPanel) or run on Render/Railway with a managed MySQL.
* **Docker (later)**: Add a `docker-compose.yml` with `php:apache` and `mysql:8` services so anyone can `docker compose up` to run it.
* **Migrations**: If you expand the project, consider a migration tool or versioned SQL.

---

## 8) Credits

Course project files: JSON generators, PHP pages, and helper scripts authored for a uni database assignment.
