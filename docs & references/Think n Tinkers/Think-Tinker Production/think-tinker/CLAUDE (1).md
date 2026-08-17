# CLAUDE.md

## Project

Think & Tinker Kids Club — a full digital ecosystem for a Lagos-based children's educational services business. Two connected platforms sharing one MySQL database:

- **Public Website + Portals** → `https://think-tinker.com`
- **Operations Hub (mini-ERP)** → `https://hub.think-tinker.com`

Read `docs/Think_Tinker_Product_Description_v2.md` for the FULL specification. It is the single source of truth — every page, every module, every database table, every data flow, every UI decision is defined there. Do not deviate from it. When in doubt, consult the product description.

## Tech Stack

- **Backend:** Vanilla PHP 8.x (no framework). OOP with class-based controllers.
- **Frontend JS:** jQuery 3.x via CDN + Vanilla JS. All data operations via `$.ajax` returning JSON.
- **CSS:** Custom mobile-first CSS. BEM-like naming. CSS variables for brand tokens defined in `assets/css/variables.css`.
- **Database:** MySQL 8.x via PDO (prepared statements only — never concatenate queries).
- **PDF Generation:** DomPDF (Composer: `dompdf/dompdf`). All branded document templates in `pdf-templates/`.
- **Email:** PHPMailer (Composer: `phpmailer/phpmailer`). SMTP config from `.env`.
- **Voice-to-Text:** Web Speech API (browser-native, client-side only, zero server dependency).
- **Icons:** Phosphor Icons via CDN (24px, regular weight).
- **Fonts:** Google Fonts CDN — Nunito Sans (400, 600, 700) + Fredoka One.
- **WYSIWYG:** TinyMCE via CDN (blog editor in Hub).
- **Hosting:** Shared hosting (TrueHost.com.ng / cPanel). No Node.js at runtime. No SSH. Composer dependencies committed in `vendor/`.

## Environment

`.env` holds ONLY server credentials (database, SMTP). Nothing else. Loaded by `includes/config.php` using a simple parser. Never hardcode credentials.

```bash
cp .env.example .env
# Fill in: DB_HOST, DB_NAME, DB_USER, DB_PASS, SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS
# That's it. Everything else is managed from Hub → Business Setup.
```

**All business settings live in the `settings` table in the database**, editable from the Hub's Business Setup module. This includes: business name, address, phone, email, social handle, WhatsApp number, RC number, bank details, pricing for every service, and any other config Janeth might change. She never touches a file — she opens the Hub and updates it herself.

## Key Directories

```
public_html/              ← Document root (think-tinker.com)
├── api/                  ← Backend controllers (one file per module, handles full CRUD)
├── includes/             ← Shared PHP: config, auth, rbac, db, helpers, mailer, pdf, upload
├── templates/            ← Shared HTML partials: headers, footers, og-meta, notification bell
├── pdf-templates/        ← DomPDF HTML templates for all 9 document types
├── parent/               ← Parent Portal pages (auth-gated)
├── teacher/              ← Teacher Portal pages
├── school/               ← School Portal pages (fully public)
├── services/             ← Individual service pages
├── assets/css/           ← Stylesheets (variables.css, main.css, parent.css, hub.css, shop.css)
├── assets/js/            ← Scripts (main.js, parent.js, voice-recorder.js, signature-pad.js, messenger.js)
├── assets/img/           ← Static images (logo/, og/, defaults/)
├── uploads/              ← User-uploaded files (products/, signatures/, invoices/, contracts/, etc.)
├── vendor/               ← Composer autoload (dompdf, phpmailer)
└── hub/                  ← Operations Hub (subdomain or subfolder, auth-enforced)
    ├── dashboard.php
    ├── clients.php
    ├── sessions.php
    ├── finance.php
    ├── staff.php
    ├── bookstore.php
    ├── club.php
    ├── blog.php
    ├── messages.php
    ├── documents.php
    └── settings.php
```

## Architecture Pattern — Single Controller Per Module

Every module has ONE controller in `api/` that handles its entire CRUD. The controller receives `action` via POST/GET and uses a switch statement. Example:

```php
// api/FinanceController.php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/rbac.php';

header('Content-Type: application/json');
$user = requireAuth();
requirePermission($user, 'finance');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_expenses':    getExpenses($user); break;
    case 'add_expense':     addExpense($user); break;
    case 'update_expense':  updateExpense($user); break;
    case 'delete_expense':  deleteExpense($user); break;
    // ... all other finance actions
    default: json_response(false, 'Invalid action');
}
```

Frontend calls:

```javascript
$.ajax({
    url: '/api/FinanceController.php',
    method: 'POST',
    data: { action: 'add_expense', amount: 5000, category: 'transportation' },
    dataType: 'json',
    success: function(res) { /* update UI */ }
});
```

**Controllers to build (13 total):** AuthController, ParentController, ChildController, SessionController, MessageController, ShopController, BlogController, ContactController, FinanceController, StaffController, ClubController, DocumentController, SettingsController, NotificationController, UploadController, DashboardController.

## Database

- MySQL 8.x via PDO. All queries use prepared statements.
- Connection via `includes/db.php` — singleton PDO wrapper.
- Schema defined in `docs/Think_Tinker_Product_Description_v2.md` Part 3 (30+ tables with every column specified).
- Generate a `database/schema.sql` migration file with all CREATE TABLE statements.
- Generate a `database/seed.sql` with default data: locations, roles, permissions, role_permissions, settings.
- Every table has: `id` (AUTO_INCREMENT PK), `created_at` (TIMESTAMP DEFAULT CURRENT_TIMESTAMP), `updated_at` (TIMESTAMP ON UPDATE CURRENT_TIMESTAMP). Soft deletes via nullable `deleted_at`.
- Foreign keys enforced. InnoDB engine. utf8mb4 charset.

## Authentication & Security

- PHP sessions with secure cookies: `HttpOnly`, `Secure`, `SameSite=Strict`.
- Passwords: `password_hash()` with `PASSWORD_BCRYPT`.
- CSRF token on every form and AJAX request. Validate server-side.
- All output escaped with `htmlspecialchars()`.
- File uploads: validate MIME server-side, rename with random hash, store in `uploads/`.
- Rate limit login: 5 failed attempts → 15-minute lockout per IP.
- Session kill: setting `users.is_active = 0` immediately blocks access.
- HTTPS enforced via `.htaccess`.

## RBAC — Role-Based Access Control

Implemented via `users` → `user_roles` → `roles` → `role_permissions` → `permissions` tables.

Roles: `super_admin`, `finance_admin`, `education_admin`, `tutor`, `parent`, `teacher`.

`includes/rbac.php` provides `requirePermission($user, $module, $action)`. Hub pages call this to gate access. Sidebar/bottom-nav rendered dynamically — modules a user cannot access are **completely hidden**, not greyed out.

Tutors see ONLY their assigned students. Finance admin sees ONLY finance. This is non-negotiable. See product description Part 4 for the full permissions matrix.

## UI / UX Conventions

### Mobile-First — Must Feel Like a Native App

- **No hamburger menus.** Bottom tab navigation everywhere on mobile.
- Public site mobile: 5 fixed bottom tabs → Home, Services, Shop, Blog, More.
- Parent Portal mobile: 5 fixed bottom tabs → Home, Calendar, Notes, Messages, Menu.
- Hub mobile: 4 fixed bottom tabs → Dashboard, Quick Actions, Notifications, Menu.
- PWA manifest (`manifest.json`) for "Add to Home Screen" capability.
- `<meta name="apple-mobile-web-app-capable" content="yes">` + `<meta name="theme-color" content="#1AAFA0">`.

### Brand Colours (CSS Variables in variables.css)

```css
:root {
  --tinker-teal: #1AAFA0;
  --spark-orange: #F47B20;
  --idea-gold: #FDB813;
  --deep-navy: #1B2A4A;
  --story-coral: #E8614D;
  --leaf-green: #27AE60;
  --sky-blue: #3498DB;
  --charcoal: #2C3E50;
  --warm-white: #FFF9F0;
  --cloud-gray: #F2F3F4;
  --soft-cream: #FEF5E7;
}
```

### Portal Colour Schemes

- Parent Portal: Tinker Teal primary, Spark Orange accent, Warm White bg.
- Teacher Portal: Deep Navy primary, Sky Blue accent, Cloud Gray bg.
- School Portal: Tinker Teal primary, Leaf Green accent, Warm White bg.
- Hub / Admin: Charcoal primary, Tinker Teal accent, Cloud Gray bg.
- Bookstore: Deep Navy primary, Tinker Teal accent, Warm White bg.

### Component Rules

- 8px base grid. All spacing multiples of 8.
- Buttons: 48px height, full-width on mobile, border-radius 8px.
- Inputs: 48px height, full-width on mobile, border-radius 8px.
- Cards: border-radius 8px, subtle shadow (Level 1: `0 2px 4px rgba(0,0,0,0.08)`).
- Modals: border-radius 16px.
- Avatars: border-radius 9999px (circle).
- Toast notifications: slide from top, auto-dismiss 4s.
- Skeleton loaders on data-fetch views before content loads.

## Critical Business Rules

1. **Parents NEVER message teachers directly.** All messages route through admin. Replies show as "Think & Tinker" — never a tutor's name.
2. **Consult booklets are NOT sold to teachers.** First 3 pages contain proprietary IP. Bookstore shows cover + generic description only.
3. **Payment is manual bank transfer only.** No Paystack. Orders/invoices created as "pending_payment." Finance admin confirms manually.
4. **Draw-to-sign contracts.** HTML5 canvas signature capture. Store: signature PNG, signer name, IP, timestamp, SHA-256 hash.
5. **Session notes submitted directly by tutor** (no admin approval). Tutor records via Web Speech API → reviews → submits → visible to parent immediately.
6. **Club registration is a separate lightweight flow** from tutorial onboarding.
7. **Admin can kill any user's session instantly** by setting `is_active = 0`.

## Business Setup Module (Hub → Settings → Business Setup)

All dynamic business configuration is stored in the database and managed from the Hub. No .env editing needed.

**`settings` table (key-value):** business name, address, phone, email, social handle, WhatsApp number, RC number, smtp_from_name, smtp_from_email, and all service pricing (consultation_fee, registration_fee, assessment_fee, session_rate_primary, session_rate_secondary, club plans).

**`bank_accounts` table (dedicated):** Bank accounts are NOT key-value pairs — they are a proper table because Janeth may have multiple accounts (FCMB today, GT Bank tomorrow). Columns: `id`, `bank_name`, `account_name`, `account_number`, `is_primary` (TINYINT, only one can be 1), `is_active` (TINYINT), `notes` (e.g. "For bookstore orders"), `created_at`, `updated_at`. The checkout page and invoice PDFs display ALL active bank accounts so parents can choose where to pay. The primary account is shown first.

The Hub's Business Setup page has three tabs:
1. **Business Info** — reads/writes `settings` table (business identity fields).
2. **Bank Accounts** — full CRUD on `bank_accounts` table. Add, edit, deactivate, set primary.
3. **Services & Pricing** — reads/writes `settings` table (all pricing fields).

When invoices, checkout pages, or PDF templates need business info or prices, they call `getSetting('session_rate_primary')` from `includes/helpers.php`. When they need bank accounts, they call `getActiveBankAccounts()` which returns all active rows ordered by `is_primary DESC`.

## PDF Generation

Use DomPDF for all 9 document types. Templates in `pdf-templates/` are full HTML with inline CSS. Brand fonts embedded. Wrapper in `includes/pdf.php`.

Document types: Invoice, Receipt, Contract, Admission Form, Progress Report, Monthly Calendar, Quote/Proposal, Club Certificate, Staff ID Badge.

## Email

PHPMailer via SMTP. Config from `.env`. Wrapper in `includes/mailer.php`. Every email uses a branded HTML template (header with logo, teal accent bar, footer with address + social links). See product description Section 2.7 for the full list of automated email triggers.

## Messaging System

WhatsApp-style chat UI. See `assets/js/messenger.js`. Chat bubbles: parent messages right (Teal), "Think & Tinker" replies left (Gray). Poll for new messages every 10 seconds via AJAX. No WebSockets.

## File Uploads

- Product images: max 800px width, JPEG, ≤200KB (auto-resize via GD).
- Profile photos: max 400px width, JPEG, ≤100KB.
- Signatures: PNG, canvas-captured.
- All files: validate MIME, rename with hash, organize by type and date (`uploads/invoices/2026/06/`).

## Conventions

- PHP: strict types, type hints on function parameters and returns where practical.
- Naming: `snake_case` for PHP functions/variables, `camelCase` for JS, `kebab-case` for CSS classes.
- Dates: store as `DATE` or `TIMESTAMP` in MySQL, display in `D, M jS Y` format (e.g., "Mon, Jun 1st 2026").
- Currency: always display as `₦XX,XXX` with Naira sign and comma formatting.
- Document numbers: auto-generated pattern `TTK-{TYPE}-{YYYYMMDD}-{XXXX}` (e.g., `TTK-INV-20260601-0001`).
- API responses: always `{ "success": true/false, "message": "...", "data": {...} }`.

## Do Not

- Do not use any PHP framework (Laravel, Symfony, CodeIgniter, etc.).
- Do not use npm, Webpack, Vite, or any build tools. All JS/CSS via CDN or handwritten.
- Do not use React, Vue, or any JS framework.
- Do not use localStorage or sessionStorage for auth (use PHP sessions only).
- Do not use WebSockets (shared hosting doesn't support them).
- Do not use Paystack or any payment gateway — manual bank transfer only.
- Do not create hamburger menus on mobile — bottom tab navigation only.
- Do not expose stock quantities on the public storefront (internal tracking only).
- Do not allow direct parent-teacher messaging — everything routes through admin.
- Do not skip the `.env` file — never hardcode credentials.
