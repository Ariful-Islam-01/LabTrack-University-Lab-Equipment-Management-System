# University Lab Equipment Management System

> A database-driven web application for managing university laboratory equipment, bookings, borrowing, returns, fines, reports, and user management.

---

## 📋 Project Overview

The **University Lab Equipment Management System (LabTrack)** is a comprehensive database-focused web application developed for managing laboratory resources in an academic institution. 

Traditionally, lab equipment tracking, student bookings, equipment issuance, return logs, and fine calculations are handled manually via paper logs or informal spreadsheets. This manual approach often leads to data redundancy, unrecorded equipment damage, untracked overdue returns, and inventory mismatch.

**LabTrack** solves these challenges by providing a centralized digital platform that seamlessly integrates a modern PHP framework (**Laravel 12**) with a enterprise relational database management system (**Oracle Database 11g XE**). The system automates inventory availability updates, booking approval workflows, overdue fine calculations, and comprehensive audit reporting using native database triggers, stored procedures, user-defined functions, and views.

### Target User Groups
* **Students**: Browse equipment catalog, submit booking requests, track borrow status, and view fine history.
* **Teachers**: Review, approve, or reject student equipment booking requests with remarks.
* **Lab Assistants**: Manage equipment inventory, issue approved equipment, process returns, manage overdue fines, administer student/teacher user accounts, and view global reporting metrics.

---

## ✨ Key Features

### 🛠️ Equipment Management
* **Equipment Inventory**: Add, update, and delete lab equipment records.
* **Category & Lab Tracking**: Organize equipment by category (e.g., Microcontrollers, Sensors, Oscilloscopes) and physical lab room locations.
* **Stock Tracking**: Dynamic tracking of total quantity and real-time available quantity.
* **Status Control**: Track operational status (`AVAILABLE`, `OUT_OF_STOCK`, `UNDER_MAINTENANCE`).
* **Search & Filter**: Search equipment by ID or name; filter by category and status.

### 📅 Booking Management
* **Request Submission**: Students can submit equipment booking requests with specified quantities.
* **Duplicate Prevention**: Automated checks to prevent duplicate pending booking requests for the same equipment.
* **Teacher Approval Workflow**: Teachers review pending requests and mark them as `APPROVED` or `REJECTED` with custom remarks.
* **Status History**: Comprehensive tracking of booking request lifecycle (`PENDING` → `APPROVED` / `REJECTED`).

### 📦 Borrow & Return Management
* **Equipment Issuance**: Lab Assistants issue approved equipment, creating formal borrow records with automated expected return dates.
* **Return Processing**: Lab Assistants log equipment returns with actual return timestamps.
* **Automated Stock Restoration**: Database triggers automatically restore available inventory when items are returned.
* **Borrow History**: Track active, returned, and overdue borrow records across users.

### 💰 Fine Management
* **Automated Fine Calculation**: Computes fine amounts automatically based on overdue days using PL/SQL function rules (50 BDT / overdue day).
* **Fine Issuance**: Lab Assistants generate fines for late returns via stored procedure logic.
* **Payment Status Tracking**: Track unpaid and paid fines (`UNPAID` vs `PAID`) with payment marking capability.
* **Student Fine Ledger**: Students can view their personal fine records and outstanding balances.

### 📊 Reports & Analytics Dashboard
* **Dashboard Overview**: Summary statistics tailored to user roles (Lab Assistant, Teacher, Student).
* **Equipment Report**: Full inventory audit with current available stock and purchase dates.
* **Booking Report**: Global request analytics filtered by status and category.
* **Borrow Report**: Comprehensive borrowing history with borrower information.
* **Fine Report**: Financial breakdown of total, paid, and unpaid fines.
* **Most Borrowed Equipment**: Analytics query aggregating total borrow frequency and quantities using `GROUP BY`.
* **Top Borrowers Report**: Analytical report identifying top student borrowers using subqueries and explicit cursors.
* **Category Borrow Metrics**: Aggregated equipment borrow metrics generated directly from an Oracle SQL View (`vw_borrow_details`).
* **Recent Activities Log**: Unified activity stream combining approved bookings and borrow actions via `UNION ALL` set operation.

### 👥 User Management
* **Student Administration**: Add, edit, update, and delete student accounts.
* **Teacher Administration**: Add, edit, update, and delete teacher accounts.
* **Referential Safety**: Deletion prevention safety checks to prevent deleting users with active bookings, borrows, or log entries.
* **Role-Based Access Control (RBAC)**: Strict permission middleware enforcing role constraints across all routes.

---

## 👥 User Roles & Permissions

| Feature / Action                  | Student | Teacher | Lab Assistant |
| --------------------------------- | :-----: | :-----: | :-----------: |
| **View Equipment Catalog**        |   ✓     |    ✓    |       ✓       |
| **Submit Booking Request**        |   ✓     |    —    |       —       |
| **View Personal Bookings**        |   ✓     |    —    |       —       |
| **Approve / Reject Booking**      |   —     |    ✓    |       —       |
| **Equipment Inventory Management**|   —     |    —    |       ✓       |
| **Issue Equipment (Create Borrow)**|  —     |    —    |       ✓       |
| **Process Return**                |   —     |    —    |       ✓       |
| **Generate Fine**                 |   —     |    —    |       ✓       |
| **Mark Fine Paid**                |   —     |    —    |       ✓       |
| **View Personal Fines**           |   ✓     |    —    |       ✓       |
| **Student Management**            |   —     |    —    |       ✓       |
| **Teacher Management**            |   —     |    —    |       ✓       |
| **View Global Reports & Analytics**| Limited| Limited |       ✓       |

---

## 🔄 System Workflow

```text
  ┌──────────┐
  │ Student  │
  └────┬─────┘
       │ Login
       ▼
  Browse Equipment Catalog
       │
       ▼
  Submit Booking Request (Quantity, Date)
       │
       ▼
 ┌───────────┐
 │  Teacher  │ ──► Reviews Pending Request ──► Approves / Rejects (with remarks)
 └───────────┘
       │ (If Approved)
       ▼
 ┌───────────┐
 │    Lab    │ ──► Issues Equipment (Creates Borrow Record)
 │ Assistant │      └─► Trigger: Decreases available stock & logs borrow
 └───────────┘
       │
       ▼
  Student uses Equipment & Returns it
       │
       ▼
  Lab Assistant processes Return
       └─► Trigger: Increases available stock & logs return
       │
       ▼
  Is Return Overdue?
     ├── ❌ No  ──► Borrow Cycle Complete
     └── ⚠️ Yes ──► Lab Assistant generates Fine
                      └─► PL/SQL Function computes rate (50 BDT / overdue day)
                      └─► Fine Record created (UNPAID)
                      │
                      ▼
                 Student pays Fine
                      │
                      ▼
                 Lab Assistant marks Fine as PAID
```

---

## 💻 Technology Stack

| Technology | Role / Purpose |
| ---------- | -------------- |
| **Laravel 12.0** | Web Application Backend Framework |
| **PHP 8.2+** | Server-side Scripting & Logic Execution |
| **Oracle Database 11g XE** | Primary Enterprise Relational Database Management System |
| **Yajra Laravel OCI8 (v12.11)** | Laravel–Oracle OCI8 Database Bridge & Driver |
| **Bootstrap 5** | Responsive UI Layout & Styling Framework |
| **JavaScript (ES6)** | Client-side Interactivity & Form Manipulations |
| **HTML5 & CSS3** | Semantic Templating & Custom Styling |
| **Vite & Composer** | Asset Bundling & PHP Dependency Management |

---

## 🗄️ Database Design

The system relies on an **Oracle Database** backend composed of 9 relational tables linked through primary key and foreign key constraints:

| Table | Purpose / Description |
| ----- | --------------------- |
| **`users`** | Stores system users, credentials, roles (`STUDENT`, `TEACHER`, `LAB_ASSISTANT`), and department. |
| **`labs`** | Stores laboratory room information and physical locations. |
| **`categories`** | Stores equipment category classifications (e.g., Microcontrollers, Sensors). |
| **`equipment`** | Stores equipment inventory, total/available stock, status, and purchase dates. |
| **`booking_requests`** | Stores student booking requests, quantities, status (`PENDING`, `APPROVED`, `REJECTED`), and teacher approval timestamps. |
| **`borrow_records`** | Stores issued equipment borrow logs, expected return dates, actual return dates, and status (`BORROWED`, `RETURNED`, `OVERDUE`). |
| **`damage_reports`** | Stores equipment damage assessments and associated monetary fine amounts. |
| **`fines`** | Stores fine records, overdue penalty amounts, reasons, and payment statuses (`PAID`, `UNPAID`). |
| **`equipment_logs`** | Stores audit trail entries for inventory actions (`BORROW`, `RETURN`, `ADD_STOCK`, `REMOVE_STOCK`, `DAMAGE_REPORTED`). |

### Database Schemas & Diagrams
The project includes complete database diagrams located in the `documentation/` directory:
* **ER Diagram**: `documentation/LabTrack_ER_Diagram.png`
* **Schema Diagram**: `documentation/LabTrack_Schema_Diagram.png`

---

## 🎓 Database Concepts Implemented

As a **database-focused CSE university project**, this system incorporates advanced relational database principles in both SQL scripts and runtime Laravel execution.

### 🔷 SQL Concepts
* **Data Definition Language (DDL)**: `CREATE TABLE` scripts with inline/attribute-level `PRIMARY KEY`, `FOREIGN KEY`, `UNIQUE`, `CHECK`, `NOT NULL`, and `DEFAULT` constraints.
* **Data Manipulation Language (DML)**: `INSERT`, `UPDATE`, and `DELETE` operations executed directly and via PL/SQL routines.
* **Data Retrieval & Filtering**: `SELECT` statements utilizing string manipulation (`UPPER`, `LIKE`), date comparisons, sorting (`ORDER BY`), and row filtering (`ROWNUM`).
* **Multi-Table JOINs**: Extensive usage of `INNER JOIN` and `LEFT JOIN` operations combining up to 4 tables for comprehensive report views.
* **Aggregate Functions**: `COUNT()`, `SUM()`, `AVG()`, `MAX()`, and `NVL()` for data summaries and handling nulls.
* **Grouping & Aggregation**: `GROUP BY` and `HAVING` clauses for category-wise equipment metrics and user borrow counts.
* **Subqueries**: Scalar subqueries and set membership subqueries (`WHERE user_id IN (SELECT ...)`).
* **Set Operations**: `UNION ALL` set operations joining approved booking streams with borrow history into a unified activity feed.
* **Transactions & Control**: Explicit `COMMIT` and `ROLLBACK` management to maintain database ACID properties.
* **Database Views**: Created `vw_borrow_details` view joining borrow records, user metadata, equipment names, and categories.
* **Date Arithmetic**: Dynamic date math using `SYSDATE`, `SYSDATE + 7` expected return calculations, and difference computation (`actual_return_date - expected_return_date`).

### 🔶 PL/SQL Concepts
* **Stored Procedures (10 Implemented)**:
  1. `approve_request`: Approves a student booking request.
  2. `borrow_equipment`: Issues equipment after validating stock and approval status.
  3. `return_equipment`: Marks borrow records as returned.
  4. `add_equipment`: Inserts new equipment into inventory.
  5. `generate_fine`: Computes overdue days and creates fine records.
  6. `update_equipment`: Updates equipment metadata and checks stock validity.
  7. `delete_equipment`: Safely deletes equipment after foreign key dependency validation.
  8. `add_booking_request`: Submits a booking request after validating stock and pending limits.
  9. `update_booking_status`: Updates request status to `APPROVED` or `REJECTED`.
  10. `mark_fine_paid`: Updates fine status from `UNPAID` to `PAID`.
* **User-Defined Functions (3 Implemented)**:
  1. `get_available_stock(p_equipment_id)`: Returns current available stock for a given equipment item.
  2. `calculate_fine(p_expected_date, p_actual_date)`: Calculates fine amount at 50 BDT per overdue day.
  3. `get_borrow_count(p_user_id)`: Returns total cumulative borrow count for a given user.
* **Automated Triggers (4 Implemented)**:
  1. `trg_decrease_stock`: `AFTER INSERT ON borrow_records` — automatically decrements available equipment stock.
  2. `trg_increase_stock`: `AFTER UPDATE OF borrow_status ON borrow_records WHEN (NEW.borrow_status = 'RETURNED')` — automatically increments available stock upon return.
  3. `trg_log_borrow`: `AFTER INSERT ON borrow_records` — automatically writes an audit record into `equipment_logs`.
  4. `trg_log_return`: `AFTER UPDATE OF borrow_status ON borrow_records WHEN (NEW.borrow_status = 'RETURNED')` — automatically writes a return audit entry into `equipment_logs`.
* **Control Structures & Cursors**:
  * **Anonymous Blocks**: PL/SQL blocks (`DECLARE ... BEGIN ... END;`) used for standalone execution and testing.
  * **Conditional Statements**: `IF / ELSIF / ELSE` and `CASE` structures evaluating business logic states.
  * **Iterative Loops**: `FOR` loops, `WHILE` loops, and `SIMPLE` loops with `EXIT WHEN` termination.
  * **Explicit Cursors**: `CURSOR c_top_borrowers IS ...` demonstrating `OPEN`, `FETCH`, `%NOTFOUND`, and `CLOSE` lifecycle for top borrower ranking.
  * **Custom Exception Handling**: Custom application error codes using `raise_application_error(-20001 to -20061, 'Error message')` for fine-grained validation feedback.

> *Note: All database scripts are preserved in the `database_scripts/` directory for direct execution in Oracle SQL Developer / SQL\*Plus.*

---

## 📂 Project Structure

```text
LabTrack/
├── database_scripts/             # Oracle SQL & PL/SQL Database Scripts
│   ├── tables.sql                # DDL Table schemas & integrity constraints
│   ├── sample_data.sql           # Initial dataset for users, labs, equipment
│   ├── procedures.sql            # 10 PL/SQL Stored Procedures
│   ├── functions.sql             # 3 PL/SQL User-Defined Functions
│   ├── triggers.sql              # 4 Automated Database Triggers
│   ├── cursor_top_borrowers.sql  # SQL View (vw_borrow_details) & PL/SQL Explicit Cursor
│   └── queries.sql               # Complex SQL queries, JOINs, subqueries & transaction tests
├── documentation/                # Project Documentation & Diagrams
│   ├── LabTrack_ER_Diagram.png   # Entity-Relationship Diagram
│   ├── LabTrack_Schema_Diagram.png # Relational Database Schema Diagram
│   ├── Labtrack_Project_Proposal.pdf
│   └── Labtrack_Project_Report.pdf
└── laravel_project_labtrack/     # Laravel 12 Application Source
    ├── app/
    │   ├── Http/
    │   │   ├── Controllers/      # App Controllers (Auth, Equipment, Booking, Borrow, Fine, etc.)
    │   │   └── Middleware/       # Custom RoleMiddleware for RBAC
    │   └── Models/               # Eloquent Models (User, Equipment, BookingRequest, etc.)
    ├── config/                   # Config files including database.php (Oracle OCI8 config)
    ├── database/                 # Seeders & Migrations
    ├── resources/
    │   └── views/                # Blade View Templates (Auth, Equipment, Booking, Reports, etc.)
    ├── routes/
    │   └── web.php               # Application Route Definitions with role middleware
    ├── .env.example              # Environment Configuration Template
    ├── composer.json             # PHP Dependencies & Package Definitions
    └── package.json              # Assets & Frontend Tooling
```

---

## ⚙️ Installation & Setup

Follow these steps to set up and run the application on your local machine:

### System Requirements
* **PHP**: `>= 8.2` (with `pdo` and `oci8` extensions enabled)
* **Composer**: `>= 2.0`
* **Node.js & NPM**: `>= 18.0`
* **Database**: Oracle Database 11g Express Edition (XE) or higher
* **Tools**: Oracle SQL Developer / SQL\*Plus

---

### Step 1: Clone the Repository
```bash
git clone https://github.com/your-username/LabTrack-University-Lab-Equipment-Management-System.git
cd LabTrack-University-Lab-Equipment-Management-System/laravel_project_labtrack
```

### Step 2: Install Dependencies
```bash
composer install
npm install
```

### Step 3: Configure Environment File
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

Generate the Laravel application encryption key:
```bash
php artisan key:generate
```

---

### Step 4: Configure Oracle Database

1. Open your Oracle SQL Developer / SQL\*Plus tool and connect to your Oracle Database instance (e.g., `xe`).
2. Run the database scripts located in `database_scripts/` in the following order:

```sql
@../database_scripts/tables.sql
@../database_scripts/functions.sql
@../database_scripts/procedures.sql
@../database_scripts/triggers.sql
@../database_scripts/cursor_top_borrowers.sql
@../database_scripts/sample_data.sql
```

---

### Step 5: Configure `.env` Credentials

Update your `.env` file to point to your local Oracle Database instance:

```env
APP_NAME=LabTrack
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=oracle
DB_HOST=127.0.0.1
DB_PORT=1521
DB_DATABASE=xe
DB_USERNAME=your_oracle_username
DB_PASSWORD=your_oracle_password
```

> **Note**: Ensure the Oracle OCI8 extension (`php_oci8_11g.dll` or equivalent for your version) is enabled in your `php.ini` configuration file.

---

## 🚀 Running the Application

Start the Laravel development server:

```bash
php artisan serve
```

Access the application in your web browser at:
```text
http://127.0.0.1:8000
```

---

## 🖼️ Diagrams

### Database Diagrams
* **Entity-Relationship Diagram**: [LabTrack_ER_Diagram.png](../documentation/LabTrack_ER_Diagram.png)
* **Relational Schema Diagram**: [LabTrack_Schema_Diagram.png](../documentation/LabTrack_Schema_Diagram.png)

---

## 🔒 Security & Access Control

* **Session Authentication**: Custom session management preserving authenticated `user_id`, `full_name`, and `role` credentials.
* **Role-Based Access Middleware**: Route guard middleware (`RoleMiddleware`) validating user roles against permitted endpoint scopes (`STUDENT`, `TEACHER`, `LAB_ASSISTANT`).
* **Password Hashing**: Secure password hashing via `Hash::make()` for user profiles.
* **CSRF Protection**: Native Laravel CSRF token verification across all HTML form submissions.
* **Defensive Deletion Validation**: Foreign key dependency checks before record deletion to prevent orphan data or database constraint violations.

---

## 🎓 Academic Project Information

* **University**: Khulna University of Engineering & Technology (KUET)
* **Department**: Computer Science and Engineering (CSE)
* **Course**: Database Systems Laboratory
* **Project Title**: University Lab Equipment Management System (LabTrack)

---

## 🔮 Future Improvements

* **QR / Barcode Equipment Scanning**: Fast check-in and check-out via QR/Barcode scanner.
* **Automated Email / SMS Notifications**: Instant reminders for upcoming return deadlines and generated fines.
* **Maintenance Scheduling Module**: Track scheduled calibration and routine maintenance for lab instruments.
* **Online Fine Payment Gateway**: Integration with digital payment gateways (bKash, Nagad) for fine settlement.
* **Mobile Application**: Native mobile app interface for students and lab assistants.

---

## 📄 License

This project was developed for academic purposes as part of the CSE curriculum at Khulna University of Engineering & Technology (KUET).
