# THINK & TINKER — FULL PRODUCT DESCRIPTION

**Document Type:** Technical Product Description for Full-Stack Development Execution
**Version:** 2.0
**Date:** June 1, 2026
**Prepared by:** JBS Praxis — Lead Consultant & Systems Architect: Tom-Blake Forghab
**Client:** Think & Tinker Kids Club (Think & Tinker Kids LTD)
**Founder / Academic Lead:** Janeth Joseph
**RC Number:** RC9030989
**Business Address:** Atunrase Estate, 7 Alh. Owolegbon Street, Gbagada, Lagos
**Phone:** +2349042636948
**Email:** info.thinkntinkerkidsclub@gmail.com
**Social:** @thinkntinkerkids

---

## TABLE OF CONTENTS

- [PART 1: PROJECT OVERVIEW](#part-1-project-overview)
- [PART 2: TECHNOLOGY STACK & ARCHITECTURE](#part-2-technology-stack--architecture)
- [PART 3: DATABASE SCHEMA](#part-3-database-schema)
- [PART 4: RBAC — ROLE-BASED ACCESS CONTROL](#part-4-rbac--role-based-access-control)
- [PART 5: PUBLIC WEBSITE](#part-5-public-website-think-tinkercom)
- [PART 6: PARENT PORTAL](#part-6-parent-portal)
- [PART 7: TEACHER PORTAL](#part-7-teacher-portal)
- [PART 8: SCHOOL PORTAL](#part-8-school-portal)
- [PART 9: ONLINE BOOKSTORE](#part-9-online-bookstore)
- [PART 10: OPERATIONS HUB](#part-10-operations-hub-hubthink-tinkercom)
- [PART 11: AUTO-GENERATED DOCUMENTS (DomPDF)](#part-11-auto-generated-documents-dompdf)
- [PART 12: CONNECTIONS & DATA FLOW](#part-12-connections--data-flow)
- [PART 13: DEPLOYMENT & INFRASTRUCTURE](#part-13-deployment--infrastructure)
- [PART 14: PHASED ROLLOUT](#part-14-phased-rollout)

---

## PART 1: PROJECT OVERVIEW

### 1.1 Business Context

Think & Tinker Kids Club is a Lagos-based educational services business founded by Janeth Joseph. The business operates across five interconnected pillars: Consult Booklets, Tutorials (home lessons), STEM & Reading Club, Educational Consulting, and an Online Bookstore. The target age range is children aged 1–14.

The business currently operates with paper-based calendars, WhatsApp communication, and no separation between personal and business finances. This project builds the complete digital infrastructure to professionalize operations.

### 1.2 Project Scope

Two connected platforms will be built:

1. **Public Website** — `https://think-tinker.com` — The front-facing website including all public pages, the Parent Portal, Teacher Portal, School Portal, and Online Bookstore.
2. **Operations Hub** — `https://hub.think-tinker.com` — The internal admin panel (mini-ERP) where Janeth and her team manage every operational aspect of the business.

Both platforms share a single MySQL database and a unified PHP codebase deployed on shared hosting.

### 1.3 Key Stakeholders

| Stakeholder | Role | System Access |
|---|---|---|
| Janeth Joseph | Founder / Academic Lead / Super Admin | Full access to everything |
| Finance Admin (Janeth's sister) | Accountant — manages payments and expenses | Finance module only in Hub |
| Education Admin | Manages student records, scheduling | Client & Session modules in Hub |
| Tutors / Teachers | Deliver sessions, write notes | Assigned students only, session notes, personal dashboard |
| Parents / Guardians | Primary paying clients | Parent Portal only |
| Teachers (external) | Professional development seekers | Teacher Portal only (public) |
| Schools | Institutional clients | School Portal only (public) |
| Children (5–14) | End beneficiaries | No direct system access (everything via parent) |

### 1.4 Domain Architecture

```
think-tinker.com                    → Public website + Portals
think-tinker.com/about              → About Us
think-tinker.com/services           → Services overview
think-tinker.com/services/tutorials → Individual service pages
think-tinker.com/shop               → Online Bookstore
think-tinker.com/blog               → Blog / Resources
think-tinker.com/contact            → Contact page
think-tinker.com/parent             → Parent Portal (auth required)
think-tinker.com/teacher            → Teacher Portal (public + auth)
think-tinker.com/school             → School Portal (public)

hub.think-tinker.com                → Operations Hub (auth required)
hub.think-tinker.com/dashboard      → Admin Dashboard
hub.think-tinker.com/clients        → Client Management
hub.think-tinker.com/sessions       → Session Management
hub.think-tinker.com/finance        → Financial Management
hub.think-tinker.com/staff          → Staff & Tutor Management
hub.think-tinker.com/bookstore      → Bookstore Management
hub.think-tinker.com/club           → Club Operations
hub.think-tinker.com/blog           → Blog/Content Management
hub.think-tinker.com/messages       → Messaging Module
hub.think-tinker.com/settings       → Settings & RBAC
hub.think-tinker.com/documents      → Document Generation
```

### 1.5 Brand Identity Reference

All design decisions must follow the Think & Tinker Brand Architecture document. Key references:

**Primary Colour Palette:**
- Tinker Teal: `#1AAFA0` — Primary brand colour, headers, buttons, primary CTAs
- Spark Orange: `#F47B20` — Secondary brand colour, accents, highlights
- Idea Gold: `#FDB813` — Lightbulb icon, star ratings, badges, warning states

**Secondary Colour Palette:**
- Deep Navy: `#1B2A4A` — Formal text, headings, corporate documents
- Story Coral: `#E8614D` — Error states, urgent notices
- Leaf Green: `#27AE60` — Success states
- Sky Blue: `#3498DB` — Informational elements, links

**Neutral Palette:**
- Charcoal: `#2C3E50` — Primary body text
- Warm White: `#FFF9F0` — Page backgrounds
- Cloud Gray: `#F2F3F4` — Dividers, secondary backgrounds
- Soft Cream: `#FEF5E7` — Alternate section backgrounds

**Typography:**
- Primary (Workhorse): Nunito Sans — Regular 400 (body), SemiBold 600 (emphasis), Bold 700 (headings)
- Secondary (Display): Fredoka One — Hero headlines, children-facing materials, certificates
- Accent (Serif): Georgia Italic — Pull quotes, testimonials, formal documents

**Colour Application by Portal:**

| Portal | Primary | Accent | Background |
|---|---|---|---|
| Parent Portal | Tinker Teal | Spark Orange | Warm White |
| Teacher Portal | Deep Navy | Sky Blue | Cloud Gray |
| School Portal | Tinker Teal | Leaf Green | Warm White |
| Kids Club Materials | Spark Orange | Idea Gold | Soft Cream |
| Bookstore | Deep Navy | Tinker Teal | Warm White |
| Admin/ERP (Hub) | Charcoal | Tinker Teal | Cloud Gray |

**UI Grid:**
- 8px base grid. All spacing multiples of 8.
- Mobile (320–767px): 4-column grid, 16px gutters, 16px side margins, full-width cards.
- Tablet (768–1279px): 8-column grid, 24px gutters, 32px side margins.
- Desktop (1280px+): 12-column grid, 32px gutters, 64px side margins, max-width 1200px.

**Component Sizing:**
- Top Navigation Bar: 64px desktop / 56px mobile
- Primary Button: 48px height, full-width on mobile
- Input Fields: 48px height, full-width on mobile
- Card Component: min 280px desktop / full-width mobile
- Border Radius: 4px (badges), 8px (inputs, cards), 16px (modals), 9999px (avatars, pills)

**Icon System:** Phosphor Icons library, 24px grid, 2px stroke weight, rounded line caps.

**Tagline:** "Think. Tinker. Shine."

---

## PART 2: TECHNOLOGY STACK & ARCHITECTURE

### 2.1 Tech Stack

| Layer | Technology | Notes |
|---|---|---|
| Language | PHP 8.x | Vanilla PHP, no framework. OOP with class-based controllers. |
| Frontend JS | jQuery 3.x + Vanilla JS | jQuery for AJAX calls, DOM manipulation, animations. Loaded via CDN. |
| AJAX | jQuery $.ajax | All form submissions and data fetches via AJAX. JSON responses. |
| CSS | Custom CSS | Mobile-first, BEM-like naming convention. CSS variables for brand colours. |
| Fonts | Google Fonts CDN | Nunito Sans (400, 600, 700) + Fredoka One |
| Icons | Phosphor Icons CDN | 24px grid, regular weight |
| WYSIWYG Editor | TinyMCE (CDN) | Blog post creation in Hub |
| Database | MySQL 8.x | Managed via phpMyAdmin on shared hosting |
| PDF Generation | DomPDF (Composer) | All branded document generation |
| Email | PHP Mailer (Composer) | SMTP connection for all automated emails |
| Voice-to-Text | Web Speech API | Browser-native, runs on client device, zero server cost |
| Image Processing | GD Library (PHP built-in) | Image compression on upload |
| Hosting | TrueHost.com.ng | Shared hosting environment |
| SSL | Let's Encrypt | Free SSL via hosting panel |
| Version Control | Git | Private repository |

### 2.2 Hosting Environment

**Provider:** TrueHost Nigeria (truehost.com.ng)
**Type:** Shared hosting (cPanel)
**Domains:**
- `think-tinker.com` — Main website + portals
- `hub.think-tinker.com` — Operations Hub (subdomain pointing to `/hub` directory or separate subdomain folder)

**Constraints of shared hosting:**
- No SSH access (use cPanel file manager or FTP for deployment)
- No Node.js / npm packages at runtime (all JS via CDN)
- No Composer at runtime (run `composer install` locally, upload `vendor/` folder)
- Limited cron job granularity (minimum 5-minute intervals)
- PHP memory limit typically 256MB–512MB
- File upload limit typically 50MB–100MB (configurable in `.htaccess`)

### 2.3 File & Folder Structure

```
think-tinker.com/                       ← Document root (public_html)
│
├── index.php                           ← Homepage
├── about.php                           ← About Us
├── services.php                        ← Services Overview
├── services/
│   ├── tutorials.php
│   ├── stem-club.php
│   ├── consulting.php
│   └── consult-booklets.php            ← Redirects to /shop with filter
├── shop.php                            ← Bookstore listing
├── shop-product.php                    ← Single product view
├── cart.php                            ← Shopping cart
├── checkout.php                        ← Checkout + payment
├── blog.php                            ← Blog listing
├── blog-post.php                       ← Single blog post
├── contact.php                         ← Contact page
│
├── parent/                             ← Parent Portal (auth-gated)
│   ├── index.php                       ← Parent Dashboard
│   ├── login.php                       ← Parent Login
│   ├── register.php                    ← Parent Registration
│   ├── register-club.php              ← Express Club-Only Registration
│   ├── onboard.php                     ← Full Onboarding (admission + contract)
│   ├── child.php                       ← Child Profile View
│   ├── calendar.php                    ← Session Calendar
│   ├── notes.php                       ← Session Notes & Progress
│   ├── messages.php                    ← WhatsApp-style Messaging
│   ├── documents.php                   ← Document Vault
│   ├── payments.php                    ← Payment History
│   └── settings.php                    ← Account Settings
│
├── teacher/                            ← Teacher Portal
│   ├── index.php                       ← Teacher Hub Landing
│   ├── login.php                       ← Teacher Login
│   ├── register.php                    ← Teacher Registration
│   ├── dashboard.php                   ← Teacher Dashboard (auth-gated)
│   ├── resources.php                   ← Downloadable Resources
│   ├── workshops.php                   ← Workshop Registration
│   └── apply.php                       ← Apply as Tutor
│
├── school/                             ← School Portal (fully public)
│   ├── index.php                       ← School Services Overview
│   ├── case-studies.php                ← Case Studies
│   └── inquiry.php                     ← Proposal Request Form
│
├── api/                                ← Backend API Controllers
│   ├── AuthController.php              ← Login, register, password reset, session kill
│   ├── ParentController.php            ← Parent CRUD, onboarding, dashboard data
│   ├── ChildController.php             ← Child profile CRUD, progress data
│   ├── SessionController.php           ← Tutorial session CRUD, calendar data, notes
│   ├── MessageController.php           ← Messaging CRUD (send, receive, assign)
│   ├── ShopController.php              ← Product CRUD, cart, checkout, orders
│   ├── BlogController.php              ← Blog post CRUD
│   ├── ContactController.php           ← Contact form, inquiry form submissions
│   ├── FinanceController.php           ← Income, expenses, invoices, receipts, P&L
│   ├── StaffController.php             ← Staff CRUD, attendance, payroll
│   ├── ClubController.php              ← Club membership, attendance, certificates
│   ├── DocumentController.php          ← PDF generation (all document types)
│   ├── SettingsController.php          ← RBAC, system settings, locations
│   ├── NotificationController.php      ← Email dispatch, in-app notification bell
│   ├── UploadController.php            ← File upload, image compression
│   └── DashboardController.php         ← Hub dashboard metrics and activity feed
│
├── includes/                           ← Shared PHP includes
│   ├── config.php                      ← DB connection, constants, SMTP config
│   ├── auth.php                        ← Session management, auth middleware
│   ├── rbac.php                        ← Role/permission checking functions
│   ├── helpers.php                     ← Utility functions (slug, date format, etc.)
│   ├── mailer.php                      ← PHPMailer wrapper
│   ├── pdf.php                         ← DomPDF wrapper + template loader
│   ├── upload.php                      ← File upload + GD image compression
│   └── db.php                          ← PDO database wrapper class
│
├── templates/                          ← Shared HTML templates
│   ├── header.php                      ← Public site header
│   ├── footer.php                      ← Public site footer
│   ├── header-parent.php               ← Parent Portal header/nav
│   ├── header-teacher.php              ← Teacher Portal header/nav
│   ├── header-hub.php                  ← Operations Hub sidebar + topbar
│   ├── og-meta.php                     ← OG meta tag generator
│   └── notification-bell.php           ← In-app notification dropdown
│
├── pdf-templates/                      ← DomPDF HTML templates
│   ├── invoice.php
│   ├── receipt.php
│   ├── contract.php
│   ├── admission-form.php
│   ├── progress-report.php
│   ├── monthly-calendar.php
│   ├── quote-proposal.php
│   ├── club-certificate.php
│   └── staff-id-badge.php
│
├── assets/
│   ├── css/
│   │   ├── main.css                    ← Public site styles
│   │   ├── parent.css                  ← Parent Portal styles
│   │   ├── teacher.css                 ← Teacher Portal styles
│   │   ├── shop.css                    ← Bookstore styles
│   │   └── variables.css               ← CSS custom properties (brand colours)
│   ├── js/
│   │   ├── main.js                     ← Public site scripts
│   │   ├── parent.js                   ← Parent Portal scripts
│   │   ├── teacher.js                  ← Teacher Portal scripts
│   │   ├── shop.js                     ← Cart & checkout scripts
│   │   ├── voice-recorder.js           ← Web Speech API wrapper
│   │   ├── signature-pad.js            ← Draw-to-sign canvas
│   │   ├── messenger.js                ← WhatsApp-style chat UI scripts
│   │   └── notification.js             ← Notification bell polling
│   ├── img/
│   │   ├── logo/                       ← All logo variants
│   │   ├── og/                         ← OG banner images
│   │   └── defaults/                   ← Placeholder images
│   └── fonts/                          ← Fallback local font files if CDN fails
│
├── uploads/                            ← User-uploaded files
│   ├── products/                       ← Bookstore product images
│   ├── blog/                           ← Blog post images
│   ├── profiles/                       ← Staff and parent profile photos
│   ├── children/                       ← Child profile photos
│   ├── contracts/                      ← Signed contract PDFs (YYYY/MM/)
│   ├── invoices/                       ← Generated invoice PDFs (YYYY/MM/)
│   ├── receipts/                       ← Generated receipt PDFs (YYYY/MM/)
│   ├── progress-reports/               ← Session progress report PDFs
│   ├── certificates/                   ← Club membership certificates
│   ├── session-images/                 ← Photos from tutorial sessions
│   ├── signatures/                     ← Captured signature images (PNG)
│   ├── expenses/                       ← Expense receipt uploads
│   └── resources/                      ← Downloadable teacher resources
│
├── vendor/                             ← Composer dependencies (DomPDF, PHPMailer)
├── .htaccess                           ← URL rewriting, security headers, caching
└── composer.json

hub.think-tinker.com/                   ← Subdomain document root
│
├── index.php                           ← Hub login page
├── dashboard.php                       ← Admin Dashboard
├── clients.php                         ← Client Management
├── children.php                        ← Child Profiles
├── sessions.php                        ← Session Management
├── finance.php                         ← Financial Management
│   (sub-views: income, expenses, invoices, receipts, pl-report)
├── staff.php                           ← Staff Management
├── bookstore.php                       ← Bookstore Management
├── club.php                            ← Club Operations
├── blog.php                            ← Blog Management
├── messages.php                        ← Message Queue
├── documents.php                       ← Document Generation
├── settings.php                        ← Settings & RBAC
│
├── assets/                             ← Hub-specific assets
│   ├── css/hub.css                     ← Hub styles (Cloud Gray bg, Charcoal + Teal)
│   └── js/hub.js                       ← Hub scripts
│
└── .htaccess                           ← Auth enforcement, security
```

**Note:** The Hub can alternatively live under `think-tinker.com/hub/` as a directory if the hosting plan doesn't support subdomain routing easily. The `.htaccess` in the subdomain folder should enforce authentication on every page.

### 2.4 Backend Architecture Pattern — Single Controller per Module

Every frontend page has a corresponding API controller that handles its entire CRUD. The controller receives a JSON AJAX request, processes it, and returns a JSON response. Pattern:

```php
// api/FinanceController.php
<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/rbac.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

// Enforce authentication
$user = requireAuth();

// Check RBAC permission for this module
requirePermission($user, 'finance');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    // === INCOME / REVENUE ===
    case 'get_income':         getIncome($user); break;
    case 'add_income':         addIncome($user); break;
    case 'update_income':      updateIncome($user); break;
    case 'delete_income':      deleteIncome($user); break;

    // === EXPENSES ===
    case 'get_expenses':       getExpenses($user); break;
    case 'add_expense':        addExpense($user); break;
    case 'update_expense':     updateExpense($user); break;
    case 'delete_expense':     deleteExpense($user); break;

    // === INVOICES ===
    case 'get_invoices':       getInvoices($user); break;
    case 'create_invoice':     createInvoice($user); break;
    case 'mark_invoice_paid':  markInvoicePaid($user); break;

    // === REPORTS ===
    case 'get_pl_report':      getPLReport($user); break;
    case 'get_cashflow':       getCashflow($user); break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
```

**Frontend AJAX call pattern:**

```javascript
$.ajax({
    url: '/api/FinanceController.php',
    method: 'POST',
    data: { action: 'add_expense', amount: 5000, category: 'transportation', description: 'Tutor taxi to Lekki', child_id: 12 },
    dataType: 'json',
    success: function(res) {
        if (res.success) { /* update UI */ }
        else { /* show error */ }
    }
});
```

### 2.5 Frontend Architecture — Mobile-First Native App Feel

The entire system must feel like navigating a native mobile app. Key principles:

**Navigation (Public Website — Mobile):**
- Fixed bottom navigation bar with 5 tabs: Home, Services, Shop, Blog, More
- "More" opens a slide-up drawer with: About, Contact, Parent Login, Teacher Hub, School Portal
- No hamburger menu. Bottom tabs only.
- Smooth page transitions using CSS transitions or minimal jQuery animation.

**Navigation (Public Website — Desktop):**
- Horizontal top navigation bar with all links visible.
- Sticky header on scroll.
- Same page structure, just wider layout.

**Navigation (Parent Portal — Mobile):**
- Fixed bottom navigation bar with 5 tabs: Home, Calendar, Notes, Messages, Menu
- "Menu" opens a full-screen overlay with: Child Profile, Documents, Payments, Shop, Settings, Logout
- Swipe gestures for moving between tabs (CSS scroll-snap or lightweight jQuery).

**Navigation (Operations Hub — Mobile):**
- Fixed bottom navigation with 4 tabs: Dashboard, Quick Actions (floating + icon), Notifications, Menu
- "Menu" slides open a full-screen module list: Clients, Sessions, Finance, Staff, Bookstore, Club, Blog, Messages, Documents, Settings
- Each module opens as a full-screen view.

**Navigation (Operations Hub — Desktop):**
- Left sidebar (collapsible) with all module links + icons.
- Top bar with: search, notification bell, user avatar + dropdown.
- Main content area takes remaining width.

**PWA-Like Behaviour:**
- Add `manifest.json` with app name, theme colour (#1AAFA0), icons.
- Add `<meta name="apple-mobile-web-app-capable" content="yes">` for iOS.
- Add `<meta name="theme-color" content="#1AAFA0">`.
- This allows parents to "Add to Home Screen" on their phones — it opens full-screen like an app, no browser chrome.
- Cache static assets via `.htaccess` for fast repeat loads.

**Transitions & Micro-interactions:**
- Page loads: subtle fade-in (200ms).
- Button taps: 100ms scale-down (0.97) + colour shift.
- Card taps: gentle lift (translateY -2px) with shadow increase.
- Form submissions: button text changes to a spinner, re-enables on response.
- Toast notifications: slide in from top, auto-dismiss after 4 seconds.
- Skeleton loaders on data-fetching views (gray pulsing placeholders before content loads).

### 2.6 Security Architecture

- **Authentication:** PHP sessions with secure cookies (`HttpOnly`, `Secure`, `SameSite=Strict`). Session timeout after 30 minutes of inactivity.
- **Password Hashing:** `password_hash()` with `PASSWORD_BCRYPT`.
- **Password Reset:** Email a time-limited token (valid 1 hour). Token stored hashed in DB.
- **CSRF Protection:** Every form includes a CSRF token. Every AJAX request sends it in headers. Validated server-side.
- **SQL Injection:** All queries use PDO prepared statements. No string concatenation.
- **XSS Prevention:** All output escaped with `htmlspecialchars()`. TinyMCE output sanitized with HTMLPurifier.
- **File Upload Security:** Validate MIME type server-side (not just extension). Rename uploaded files with random hash. Store outside web root where possible, serve via PHP.
- **Rate Limiting:** Track login attempts per IP. Lock account after 5 failed attempts for 15 minutes.
- **Session Kill:** Admin can instantly invalidate any user's session from the Hub (by deleting their session record from the `sessions` table or setting an `is_active` flag to 0 on the user record).
- **HTTPS Enforcement:** `.htaccess` redirect all HTTP to HTTPS.
- **NDPR Compliance:** All personal data encrypted at rest where feasible. Explicit consent captured during registration. Data deletion capability for NDPR requests.

### 2.7 SMTP Email Configuration

```php
// includes/config.php — loads .env and sets up DB connection
// Only server credentials live in .env. All business settings are in the database.
$env = parse_ini_file(__DIR__ . '/../.env');

// Database
define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']);

// SMTP
define('SMTP_HOST', $env['SMTP_HOST']);
define('SMTP_PORT', $env['SMTP_PORT']);
define('SMTP_USER', $env['SMTP_USER']);
define('SMTP_PASS', $env['SMTP_PASS']);

// Business settings (prices, bank details, address, etc.) are fetched
// from the `settings` table via getSetting() in helpers.php.
// They are editable from Hub → Business Setup. Never hardcoded.
```

PHPMailer handles all outgoing email. Every email uses a branded HTML template with the Think & Tinker header, teal accent bar, and footer with address + social links.

**Automated emails triggered by the system:**
- Welcome email (on parent registration)
- Account activation link (on staff/tutor account creation)
- Password reset link
- Invoice email (with PDF attachment)
- Receipt email (after payment confirmation)
- Session note published (notify parent that a new session note is available)
- Contract ready for signing (link to digital contract page)
- Order confirmation (bookstore purchase)
- Order status update (shipped, delivered)
- Club membership welcome
- Monthly calendar PDF (attached, sent on the 1st of each month)
- Low stock alert (internal, to admin)
- New inquiry notification (internal, to admin)
- New message notification (parent or admin receives a new message)

### 2.8 PDF Generation — DomPDF

**Composer dependency:** `dompdf/dompdf`

All PDFs are generated from PHP/HTML templates located in `/pdf-templates/`. Each template is a full HTML page with inline CSS (DomPDF does not support external stylesheets reliably). The brand fonts are loaded as base64 or from local font files registered with DomPDF.

```php
// includes/pdf.php
use Dompdf\Dompdf;
use Dompdf\Options;

function generatePDF($template, $data, $filename, $orientation = 'portrait') {
    $options = new Options();
    $options->set('isRemoteEnabled', false);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'Nunito Sans');

    $dompdf = new Dompdf($options);

    ob_start();
    extract($data);
    include "../pdf-templates/{$template}.php";
    $html = ob_get_clean();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', $orientation);
    $dompdf->render();

    // Save to disk
    $path = "../uploads/{$template}s/" . date('Y/m/') . $filename . '.pdf';
    @mkdir(dirname($path), 0755, true);
    file_put_contents($path, $dompdf->output());

    return $path;
}
```

### 2.9 OG Meta Tags & WhatsApp Sharing

Every public page includes OG meta tags for rich WhatsApp/social media previews.

```html
<!-- templates/og-meta.php -->
<meta property="og:title" content="<?= $og_title ?? 'Think & Tinker Kids Club' ?>">
<meta property="og:description" content="<?= $og_description ?? 'Building curious minds through play, creativity, and exploration.' ?>">
<meta property="og:image" content="<?= $og_image ?? 'https://think-tinker.com/assets/img/og/default-banner.jpg' ?>">
<meta property="og:url" content="<?= $og_url ?? 'https://think-tinker.com' ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Think & Tinker Kids Club">
<meta name="twitter:card" content="summary_large_image">
```

**OG Banner Specifications:**
- Dimensions: 1200×630px
- Format: JPEG, optimized to under 300KB
- Default banner: Think & Tinker logo centered on Warm White background with "Think. Tinker. Shine." tagline and the teal/orange brand elements.
- Per-page banners: Each major page (Services, STEM Club, Bookstore) gets a unique OG image.
- Blog posts: Auto-generate an OG image or use the post's featured image.

**Share from Hub:**
The Admin Hub will have a "Share" button on Teacher Portal and School Portal pages that constructs a `https://wa.me/?text=` URL with pre-filled message + link, opening WhatsApp directly for sharing.

---

## PART 3: DATABASE SCHEMA

All tables use `InnoDB` engine, `utf8mb4` charset. Every table has `id` (auto-increment primary key), `created_at`, and `updated_at` timestamps. Soft deletes via `deleted_at` nullable timestamp.

### 3.1 Entity Relationship Overview

```
locations (1) ──────< all major tables (via location_id FK, default 1)
users (1) ──────< roles (many-to-many via user_roles)
users (1) ──────< children (via parent_id)
children (1) ──────< sessions
children (1) ──────< child_services
children (1) ──────< progress_records
users [tutor] (1) ──────< sessions (via tutor_id)
sessions (1) ──────< session_notes
users [parent] (1) ──────< invoices
users [parent] (1) ──────< contracts
users [parent] (1) ──────< orders (bookstore)
orders (1) ──────< order_items
products (1) ──────< order_items
messages ──────< message_replies
users (1) ──────< notifications
users (1) ──────< expenses (created_by)
club_memberships ──────< club_attendance
```

### 3.2 Table Definitions

**`locations`** — Multi-location readiness (single location for now)

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| name | VARCHAR(100) | "Gbagada HQ" |
| address | TEXT | Full address |
| phone | VARCHAR(20) | |
| email | VARCHAR(100) | |
| is_active | TINYINT(1) | Default 1 |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`users`** — All system users (parents, staff, tutors, admins, teachers)

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| first_name | VARCHAR(50) | |
| last_name | VARCHAR(50) | |
| email | VARCHAR(100) | UNIQUE |
| phone | VARCHAR(20) | |
| password_hash | VARCHAR(255) | bcrypt |
| user_type | ENUM('parent','tutor','staff','admin','super_admin','teacher') | |
| profile_photo | VARCHAR(255) | Path to uploaded image |
| is_active | TINYINT(1) | Default 1. Set to 0 to "kill session" |
| email_verified_at | TIMESTAMP NULL | |
| activation_token | VARCHAR(255) NULL | For email activation link |
| reset_token | VARCHAR(255) NULL | For password reset |
| reset_token_expires | TIMESTAMP NULL | |
| last_login_at | TIMESTAMP NULL | |
| location_id | INT | FK → locations.id, default 1 |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP NULL | Soft delete |

---

**`roles`** — Available roles in the system

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| name | VARCHAR(50) | e.g. "super_admin", "finance_admin", "education_admin", "tutor", "parent" |
| description | VARCHAR(255) | |
| created_at | TIMESTAMP | |

---

**`permissions`** — Granular permissions

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| name | VARCHAR(100) | e.g. "finance.view", "finance.edit", "clients.view", "sessions.edit" |
| module | VARCHAR(50) | e.g. "finance", "clients", "sessions", "staff", "bookstore", "club", "blog", "messages", "settings" |
| description | VARCHAR(255) | |

---

**`role_permissions`** — Many-to-many: which permissions each role has

| Column | Type | Notes |
|---|---|---|
| role_id | INT | FK → roles.id |
| permission_id | INT | FK → permissions.id |
| PRIMARY KEY | (role_id, permission_id) | |

---

**`user_roles`** — Many-to-many: which roles each user has

| Column | Type | Notes |
|---|---|---|
| user_id | INT | FK → users.id |
| role_id | INT | FK → roles.id |
| PRIMARY KEY | (user_id, role_id) | |

---

**`children`** — Child profiles

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| parent_id | INT | FK → users.id (parent) |
| first_name | VARCHAR(50) | |
| last_name | VARCHAR(50) | |
| date_of_birth | DATE | |
| gender | ENUM('male','female') | |
| current_school | VARCHAR(150) | |
| grade_level | VARCHAR(20) | e.g. "Primary 3", "JSS 1" |
| medical_notes | TEXT NULL | Allergies, conditions, medications |
| photo | VARCHAR(255) NULL | |
| status | ENUM('active','inactive','graduated') | Default 'active' |
| location_id | INT | FK → locations.id |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP NULL | |

---

**`child_services`** — Which services each child is enrolled in

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| child_id | INT | FK → children.id |
| service_type | ENUM('tutorials','stem_club','both') | |
| frequency | VARCHAR(50) NULL | e.g. "2x per week", "3x per week", "Saturdays only" |
| assigned_tutor_id | INT NULL | FK → users.id (tutor). NULL for club-only. |
| session_rate | DECIMAL(10,2) NULL | Per-session fee for this child (₦20,000 or ₦22,000) |
| start_date | DATE | |
| end_date | DATE NULL | |
| status | ENUM('active','paused','completed') | Default 'active' |
| notes | TEXT NULL | Special instructions |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`sessions`** — Individual tutorial sessions

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| child_id | INT | FK → children.id |
| tutor_id | INT | FK → users.id (tutor) |
| session_date | DATE | |
| start_time | TIME NULL | |
| end_time | TIME NULL | |
| status | ENUM('scheduled','completed','cancelled','rescheduled') | |
| rescheduled_from | DATE NULL | Original date if rescheduled |
| rescheduled_reason | VARCHAR(255) NULL | |
| location_id | INT | FK → locations.id |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`session_notes`** — Tutor notes per session (voice-to-text or typed)

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| session_id | INT | FK → sessions.id |
| tutor_id | INT | FK → users.id |
| note_text | TEXT | The transcribed/typed note |
| topics_covered | TEXT NULL | Comma-separated or JSON list of topics |
| input_method | ENUM('voice','typed') | How the note was created |
| images | JSON NULL | Array of image paths from the session |
| status | ENUM('draft','submitted') | Tutor saves as draft or submits directly |
| submitted_at | TIMESTAMP NULL | When tutor clicked submit |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`progress_records`** — Topic-level progress tracking per child

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| child_id | INT | FK → children.id |
| subject | VARCHAR(100) | e.g. "Mathematics", "English", "Science" |
| topic | VARCHAR(200) | e.g. "Fractions", "Comprehension Passages" |
| status | ENUM('not_started','in_progress','mastered') | Visual progress indicator |
| notes | TEXT NULL | |
| updated_by | INT | FK → users.id (tutor who updated) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`contracts`** — Digital service contracts

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| parent_id | INT | FK → users.id |
| child_id | INT | FK → children.id |
| contract_number | VARCHAR(20) | Auto-generated: TTK-CON-YYYYMMDD-XXXX |
| service_type | VARCHAR(50) | |
| terms_text | TEXT | Full contract terms (HTML allowed) |
| signature_image | VARCHAR(255) | Path to captured signature PNG |
| signer_name | VARCHAR(100) | Full name as typed by signer |
| signer_ip | VARCHAR(45) | IPv4 or IPv6 |
| signature_hash | VARCHAR(64) | SHA-256 hash of (signer_name + timestamp + IP + contract_number) |
| signed_at | TIMESTAMP NULL | |
| pdf_path | VARCHAR(255) | Path to generated PDF |
| status | ENUM('draft','sent','signed','expired','cancelled') | |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`invoices`** — Financial invoices

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| invoice_number | VARCHAR(20) | Auto-generated: TTK-INV-YYYYMMDD-XXXX |
| parent_id | INT | FK → users.id |
| child_id | INT NULL | FK → children.id (NULL for general invoices) |
| invoice_type | ENUM('registration','assessment','tutorial','club_membership','consultation','bookstore','custom') | |
| subtotal | DECIMAL(10,2) | |
| discount | DECIMAL(10,2) | Default 0 |
| total | DECIMAL(10,2) | |
| status | ENUM('draft','sent','paid','overdue','cancelled') | |
| due_date | DATE | |
| paid_at | TIMESTAMP NULL | |
| payment_method | ENUM('bank_transfer','cash','pos') NULL | |
| payment_reference | VARCHAR(100) NULL | Bank transfer reference / receipt number |
| confirmed_by | INT NULL | FK → users.id (admin who confirmed payment) |
| pdf_path | VARCHAR(255) | |
| notes | TEXT NULL | |
| location_id | INT | FK → locations.id |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`invoice_items`** — Line items on an invoice

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| invoice_id | INT | FK → invoices.id |
| description | VARCHAR(255) | e.g. "Tutorial Session - Primary Level (June 2026)" |
| quantity | INT | |
| unit_price | DECIMAL(10,2) | |
| total | DECIMAL(10,2) | quantity × unit_price |

---

**`receipts`** — Payment receipts (generated after invoice marked as paid)

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| receipt_number | VARCHAR(20) | Auto-generated: TTK-RCT-YYYYMMDD-XXXX |
| invoice_id | INT | FK → invoices.id |
| amount | DECIMAL(10,2) | |
| payment_method | ENUM('bank_transfer','cash','pos') | |
| payment_reference | VARCHAR(100) NULL | |
| pdf_path | VARCHAR(255) | |
| issued_at | TIMESTAMP | |
| created_at | TIMESTAMP | |

---

**`expenses`** — Business expense tracking

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| category | ENUM('transportation','salary','materials','printing','utilities','rent','equipment','marketing','miscellaneous') | |
| description | TEXT | |
| amount | DECIMAL(10,2) | |
| expense_date | DATE | |
| child_id | INT NULL | FK → children.id. NULL if general/operational expense. |
| receipt_image | VARCHAR(255) NULL | Uploaded receipt photo |
| created_by | INT | FK → users.id |
| approved_by | INT NULL | FK → users.id (admin approval) |
| location_id | INT | FK → locations.id |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`products`** — Bookstore products

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| name | VARCHAR(200) | |
| slug | VARCHAR(200) | URL-friendly name |
| description | TEXT | |
| category | ENUM('consult_booklets','workbooks','exercise_books','textbooks','stationery','educational_toys','recommended_reading') | |
| grade_level | VARCHAR(50) NULL | e.g. "Primary 1-3", "All Grades" |
| subject | VARCHAR(100) NULL | e.g. "Mathematics", "English" |
| series | VARCHAR(100) NULL | e.g. "Pink Series", "Schofield & Sims" |
| price | DECIMAL(10,2) | |
| compare_price | DECIMAL(10,2) NULL | Original price if on sale |
| stock_quantity | INT | Internal tracking. NOT shown on website. |
| low_stock_threshold | INT | Default 5. Trigger low-stock alert. |
| cover_image | VARCHAR(255) | Main product image |
| gallery_images | JSON NULL | Array of additional image paths |
| is_featured | TINYINT(1) | Default 0. "Staff Picks" |
| is_active | TINYINT(1) | Default 1. Show/hide on storefront. |
| location_id | INT | FK → locations.id |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP NULL | |

---

**`orders`** — Bookstore orders

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| order_number | VARCHAR(20) | Auto-generated: TTK-ORD-YYYYMMDD-XXXX |
| parent_id | INT | FK → users.id (buyer) |
| subtotal | DECIMAL(10,2) | |
| delivery_fee | DECIMAL(10,2) | Default 0 |
| total | DECIMAL(10,2) | |
| status | ENUM('pending_payment','awaiting_confirmation','processing','shipped','delivered','cancelled') | |
| payment_method | ENUM('bank_transfer','cash') | |
| payment_reference | VARCHAR(100) NULL | |
| delivery_address | TEXT | |
| delivery_phone | VARCHAR(20) | |
| delivery_notes | TEXT NULL | |
| confirmed_by | INT NULL | FK → users.id (admin who confirmed payment) |
| shipped_at | TIMESTAMP NULL | |
| delivered_at | TIMESTAMP NULL | |
| location_id | INT | FK → locations.id |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`order_items`** — Individual items in an order

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| order_id | INT | FK → orders.id |
| product_id | INT | FK → products.id |
| product_name | VARCHAR(200) | Snapshot at time of order |
| quantity | INT | |
| unit_price | DECIMAL(10,2) | Snapshot at time of order |
| total | DECIMAL(10,2) | |

---

**`club_memberships`** — STEM & Reading Club memberships

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| child_id | INT | FK → children.id |
| parent_id | INT | FK → users.id |
| plan | ENUM('trial','monthly','quarterly','biannual') | |
| plan_price | DECIMAL(10,2) | ₦8K / ₦30K / ₦85K / ₦165K |
| start_date | DATE | |
| end_date | DATE | Calculated based on plan |
| status | ENUM('active','expired','cancelled') | |
| invoice_id | INT NULL | FK → invoices.id |
| certificate_pdf | VARCHAR(255) NULL | Path to generated membership certificate |
| location_id | INT | FK → locations.id |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`club_attendance`** — Saturday club attendance records

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| membership_id | INT | FK → club_memberships.id |
| child_id | INT | FK → children.id |
| attendance_date | DATE | Saturday date |
| status | ENUM('present','absent','excused') | |
| checked_in_at | TIME NULL | |
| checked_out_at | TIME NULL | |
| notes | TEXT NULL | |
| marked_by | INT | FK → users.id |
| created_at | TIMESTAMP | |

---

**`messages`** — WhatsApp-style messaging (parent ↔ admin, with tutor loop-in)

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| conversation_id | VARCHAR(36) | UUID grouping a conversation thread |
| sender_id | INT | FK → users.id |
| sender_type | ENUM('parent','admin','tutor') | |
| display_as | VARCHAR(100) | What the parent sees. Always "Think & Tinker" for admin/tutor replies. Parent sees their own name. |
| message_text | TEXT | |
| is_read | TINYINT(1) | Default 0 |
| assigned_to | INT NULL | FK → users.id. If admin assigns to a tutor for response. |
| child_id | INT NULL | FK → children.id. Context: which child is this about. |
| created_at | TIMESTAMP | |

---

**`notifications`** — In-app notification bell

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| user_id | INT | FK → users.id |
| title | VARCHAR(200) | e.g. "New session note for Mila" |
| body | TEXT NULL | |
| link | VARCHAR(255) NULL | URL to navigate to on click |
| type | ENUM('info','success','warning','alert') | |
| is_read | TINYINT(1) | Default 0 |
| created_at | TIMESTAMP | |

---

**`blog_posts`** — Blog / Resources content

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| title | VARCHAR(255) | |
| slug | VARCHAR(255) | UNIQUE |
| excerpt | TEXT | Short preview text |
| body | LONGTEXT | Full HTML content from WYSIWYG |
| featured_image | VARCHAR(255) NULL | |
| category | ENUM('parenting_tips','stem_activities','reading_guides','educational_insights','announcements') | |
| tags | JSON NULL | Array of tag strings |
| author_id | INT | FK → users.id |
| status | ENUM('draft','published','archived') | |
| published_at | TIMESTAMP NULL | |
| og_image | VARCHAR(255) NULL | Custom OG image for this post |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`contact_inquiries`** — Contact form and school inquiry submissions

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| inquiry_type | ENUM('general','tutorial','club','consulting','school_proposal') | |
| name | VARCHAR(100) | |
| email | VARCHAR(100) | |
| phone | VARCHAR(20) NULL | |
| school_name | VARCHAR(200) NULL | For school inquiries |
| message | TEXT | |
| status | ENUM('new','responded','closed') | Default 'new' |
| responded_by | INT NULL | FK → users.id |
| responded_at | TIMESTAMP NULL | |
| created_at | TIMESTAMP | |

---

**`staff_records`** — Extended staff information (beyond users table)

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| user_id | INT | FK → users.id |
| staff_id_number | VARCHAR(20) | Auto-generated: YYYYMMXXX (e.g. 202601102) |
| job_title | VARCHAR(100) | e.g. "Tutor", "Admin Assistant", "Finance Admin" |
| employment_type | ENUM('full_time','part_time','contract') | |
| salary | DECIMAL(10,2) NULL | Monthly salary |
| bank_name | VARCHAR(100) NULL | |
| bank_account | VARCHAR(20) NULL | |
| guarantor_name | VARCHAR(100) NULL | |
| guarantor_phone | VARCHAR(20) NULL | |
| guarantor_address | TEXT NULL | |
| hire_date | DATE | |
| end_date | DATE NULL | |
| id_badge_pdf | VARCHAR(255) NULL | Generated staff ID badge PDF |
| location_id | INT | FK → locations.id |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`staff_attendance`** — Staff/tutor attendance tracking

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| user_id | INT | FK → users.id |
| attendance_date | DATE | |
| status | ENUM('present','absent','late','excused') | |
| clock_in | TIME NULL | |
| clock_out | TIME NULL | |
| notes | TEXT NULL | |
| created_at | TIMESTAMP | |

---

**`teacher_profiles`** — External teacher portal profiles (separate from staff)

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| user_id | INT | FK → users.id (user_type = 'teacher') |
| bio | TEXT NULL | |
| qualifications | TEXT NULL | |
| subjects | JSON NULL | Array of subjects they teach |
| experience_years | INT NULL | |
| school_name | VARCHAR(200) NULL | Where they currently teach |
| wants_to_tutor | TINYINT(1) | Default 0. Set to 1 if they applied to be a TTK tutor. |
| application_status | ENUM('none','applied','under_review','accepted','rejected') | Default 'none' |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`workshop_registrations`** — Teacher workshop sign-ups

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| teacher_id | INT | FK → users.id |
| workshop_title | VARCHAR(200) | |
| workshop_date | DATE | |
| status | ENUM('registered','attended','cancelled') | |
| created_at | TIMESTAMP | |

---

**`resources`** — Downloadable resources for teachers

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| title | VARCHAR(200) | |
| description | TEXT | |
| category | ENUM('lesson_plans','stem_guides','reading_lists','worksheets','training_materials') | |
| file_path | VARCHAR(255) | |
| file_size | INT | In bytes |
| download_count | INT | Default 0 |
| is_active | TINYINT(1) | Default 1 |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`case_studies`** — School consulting case studies

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| title | VARCHAR(200) | e.g. "Curriculum Restructuring for ABC School" |
| school_name | VARCHAR(200) | |
| challenge | TEXT | What was the problem |
| solution | TEXT | What Think & Tinker did |
| results | TEXT | Outcomes achieved |
| testimonial | TEXT NULL | Quote from school admin |
| image | VARCHAR(255) NULL | |
| is_published | TINYINT(1) | Default 0 |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

**`settings`** — Central business configuration (ALL business settings live here, NOT in .env)

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| setting_group | VARCHAR(50) | Groups for UI tabs: "business_info", "bank_details", "pricing", "email_settings" |
| setting_key | VARCHAR(100) | UNIQUE. e.g. "business_name", "bank_account_number", "session_rate_primary" |
| setting_value | TEXT | |
| setting_label | VARCHAR(100) | Human-readable label for the Business Setup form, e.g. "Session Rate (Primary)" |
| setting_type | ENUM('text','number','textarea','email','phone') | Determines the input type in the Business Setup form |
| updated_at | TIMESTAMP | |

This table is the single source of truth for all business configuration. The Hub's "Business Setup" module renders a tabbed form from this table. Janeth edits prices, bank details, and address directly from the Hub without touching any file. Every part of the system (invoices, checkout, PDFs, email templates, public website footer) reads from this table via `getSetting($key)` in `includes/helpers.php`, cached in the PHP session per request to avoid repeated queries.

**Default settings to seed:**

| Group | Key | Default Value | Label |
|---|---|---|---|
| business_info | business_name | Think & Tinker Kids Club | Business Name |
| business_info | business_address | Atunrase Estate, 7 Alh. Owolegbon Street, Gbagada, Lagos | Business Address |
| business_info | business_phone | +2349042636948 | Phone Number |
| business_info | business_email | info.thinkntinkerkidsclub@gmail.com | Email Address |
| business_info | business_social | @thinkntinkerkids | Social Media Handle |
| business_info | business_whatsapp | 2349042636948 | WhatsApp Number |
| business_info | business_rc | RC9030989 | RC Number |
| business_info | smtp_from_name | Think & Tinker Kids Club | Email Sender Name |
| business_info | smtp_from_email | no-reply@think-tinker.com | Email Sender Address |
| pricing | consultation_fee | 10000 | Consultation Fee (₦) |
| pricing | registration_fee | 8500 | Registration Fee (₦) |
| pricing | assessment_fee | 15000 | Assessment Fee (₦) |
| pricing | session_rate_primary | 20000 | Tutorial Rate — Primary (₦/session) |
| pricing | session_rate_secondary | 22000 | Tutorial Rate — Secondary (₦/session) |
| pricing | club_plan_trial | 8000 | Club — Trial (₦) |
| pricing | club_plan_monthly | 30000 | Club — Monthly (₦) |
| pricing | club_plan_quarterly | 85000 | Club — Quarterly (₦) |
| pricing | club_plan_biannual | 165000 | Club — Bi-Annual (₦) |

---

**`bank_accounts`** — Business bank accounts (supports multiple accounts)

Bank accounts are a dedicated table, not key-value settings, because the business may operate multiple bank accounts simultaneously. Parents see all active accounts on checkout pages and invoices so they can choose where to pay.

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PK |
| bank_name | VARCHAR(100) | e.g. "FCMB", "GT Bank", "Access Bank" |
| account_name | VARCHAR(200) | e.g. "Think & Tinker Kids LTD" |
| account_number | VARCHAR(20) | |
| account_type | ENUM('savings','current') | Default 'current' |
| is_primary | TINYINT(1) | Default 0. Only ONE account can be primary (shown first). |
| is_active | TINYINT(1) | Default 1. Inactive accounts are hidden from public views. |
| notes | VARCHAR(255) NULL | Internal note, e.g. "Main business account" or "Bookstore orders only" |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

**Default seed data:**

| bank_name | account_name | account_number | is_primary |
|---|---|---|---|
| FCMB | Think & Tinker Kids LTD | (to be provided) | 1 |

**How bank accounts are displayed:**
- **Checkout page:** All active accounts listed. Primary account shown first with a "Recommended" badge. Each account displays: Bank Name, Account Name, Account Number, with a "Copy" button next to the account number for easy mobile copying.
- **Invoice PDFs:** All active bank accounts listed in the payment section.
- **Hub → Business Setup → Bank Accounts tab:** Full CRUD — add new account, edit existing, toggle active/inactive, set primary. When setting a new account as primary, the old primary is automatically unset.

---

## PART 4: RBAC — ROLE-BASED ACCESS CONTROL

### 4.1 Roles

| Role | Description |
|---|---|
| super_admin | Janeth Joseph. Full unrestricted access to everything. |
| finance_admin | Janeth's sister. See and manage Finance module only. |
| education_admin | Education operations. Client, Session, Club modules. |
| tutor | See ONLY assigned students, write session notes, view personal schedule. |
| parent | Parent Portal only. See only their own children. |
| teacher | Teacher Portal only. Public profile, resources, workshops. |

### 4.2 Permissions Matrix

| Module | super_admin | finance_admin | education_admin | tutor |
|---|---|---|---|---|
| Dashboard | Full metrics | Finance metrics only | Client/session metrics only | Personal schedule only |
| Clients | Full CRUD | View only (for invoice context) | Full CRUD | View assigned only |
| Sessions | Full CRUD | No access | Full CRUD | View + write notes (assigned only) |
| Finance | Full CRUD | Full CRUD | No access | No access |
| Staff | Full CRUD | No access | View only | No access |
| Bookstore | Full CRUD | View orders + revenue | No access | No access |
| Club | Full CRUD | View membership revenue | Full CRUD | No access |
| Blog | Full CRUD | No access | No access | No access |
| Messages | Full access + assign | No access | View + respond | Respond to assigned only |
| Documents | Generate all | Generate invoices/receipts | Generate progress reports | No access |
| Settings / RBAC | Full access | No access | No access | No access |

### 4.3 How RBAC Works in Code

```php
// includes/rbac.php
function requirePermission($user, $module, $action = 'view') {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM user_roles ur
        JOIN role_permissions rp ON ur.role_id = rp.role_id
        JOIN permissions p ON rp.permission_id = p.id
        WHERE ur.user_id = ? AND p.module = ? AND p.name LIKE ?
    ");
    $stmt->execute([$user['id'], $module, "$module.$action%"]);

    if ($stmt->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
}
```

**Frontend Module Visibility:**
When the Hub loads, an AJAX call to `SettingsController.php?action=get_user_permissions` returns the list of modules the logged-in user can see. The sidebar/bottom nav is rendered dynamically — modules the user cannot access are not shown at all (not greyed out, completely hidden).

**RBAC is like a WhatsApp group** (as explained to the client): you add someone and choose which "groups" (modules) they can see. If a module doesn't concern them, it does not exist in their interface.

---

## PART 5: PUBLIC WEBSITE (think-tinker.com)

### 5.1 Homepage (`index.php`)

**Hero Section:**
- Full-width background: high-quality image of children engaged in STEM/reading activities.
- Headline (Fredoka One): "Where Curious Minds Come Alive"
- Subheadline (Nunito Sans): "Personalised tutoring, hands-on STEM & reading experiences, and expert educational support for children aged 1–14 in Lagos."
- Two CTA buttons: "Explore Our Services" (Tinker Teal, → /services) and "Join the Club" (Spark Orange, → /services/stem-club).
- On mobile: stacked buttons, full-width.

**Trust Bar:**
- Horizontal strip below hero.
- Items: RC9030989 (registered business icon), "5+ Years Experience", "Safe. Fun. Engaging."
- Background: Deep Navy. Text: White. Accent: Idea Gold icons.

**Five Pillars Section:**
- Section title: "Everything Your Child Needs Under One Roof"
- 5-card grid (2 columns on mobile, 5 across on desktop).
- Each card: icon + pillar name + one-line description + arrow link.
- Pillars: Consult Booklets, Tutorials, STEM & Reading Club, Educational Consulting, Bookstore.

**Social Proof Section:**
- Carousel of parent testimonials (auto-scroll, swipeable on mobile).
- Each testimonial: quote text (Georgia Italic), parent name, child's grade.
- Below: partner school logos in a horizontal scrolling strip (if available; placeholder "Trusted by schools across Lagos" otherwise).

**Club Spotlight Section:**
- Visually rich block with gallery thumbnails (activity photos).
- "STEM & Reading Club — Every Saturday, 9:30 AM – 2:00 PM"
- Quick pricing summary: "From ₦8,000 for a trial session"
- CTA: "Register for a Trial" → /parent/register-club

**Blog Preview:**
- "Tips from Ms. Janeth" — Latest 3 blog posts.
- Card format: featured image, title, excerpt, "Read More" link.

**Floating WhatsApp Button:**
- Fixed bottom-right (above bottom nav on mobile).
- Green WhatsApp icon.
- On click: opens `https://wa.me/2349042636948?text=Hi%20Think%20%26%20Tinker%2C%20I%27d%20like%20to%20enquire%20about%20your%20services.`

### 5.2 About Us Page (`about.php`)

- Hero: Janeth Joseph's professional photo + title "Meet the Founder"
- Origin Story: How Think & Tinker started (from the brand manifesto).
- Professional Bio: Janeth's qualifications — international degrees from US, Canada, UK (as mentioned in the meeting transcript). Years of frontline teaching experience in Lagos.
- Mission & Vision statements.
- Core Values (6 values from brand document).
- Team section: Admin staff, key tutors (pulled from `staff_records` if marked as "show on website").
- Company timeline (optional — milestones from founding to present).

### 5.3 Services Overview Page (`services.php`)

- Page title: "Our Services"
- 5 expandable cards, one per pillar.
- Each card shows: pillar name, description, key benefits, pricing summary, CTA button.
- On mobile: vertically stacked. On desktop: 2-column grid with the 5th card spanning full width.

### 5.4 Tutorials Page (`services/tutorials.php`)

- Explains the tutorial (home lesson) service.
- The 5-Step Process (visual step indicator):
  1. Initial Inquiry → Parent submits inquiry form.
  2. Consultation → Scheduled consultation (₦10,000) to assess child's needs.
  3. Assessment → Formal assessment (₦15,000) to build learning plan.
  4. Registration → Parent pays registration fee (₦8,500), completes admission form, signs digital contract.
  5. Sessions Begin → Scheduled sessions with post-session notes uploaded to Parent Portal.
- Pricing table: ₦20,000/session (Primary), ₦22,000/session (Secondary).
- FAQ section (collapsible).
- CTA: "Book a Consultation" → opens the contact inquiry form with "tutorial" pre-selected.

### 5.5 STEM & Reading Club Page (`services/stem-club.php`)

- Immersive page with photo gallery (activity shots from past Saturdays).
- Schedule: Every Saturday, 9:30 AM – 2:00 PM, Atunrase Estate, Gbagada.
- Age range: 1–14.
- Activities list: STEM experiments, creative art & craft (sculpture painting, tie-and-dye, canvas work), group reading with recount crafts, Mystery Basket, Show & Tell, etiquette training, author spotlight, book reviews, Young Authors Program, Literary Character Parade, Mental Maths & Quizzes, gardening, cooking basics, movement & wellness, culture & values.
- Membership Plans table:

| Plan | Sessions | Price |
|---|---|---|
| Trial | 1 Saturday | ₦8,000 |
| Monthly | 4 Saturdays | ₦30,000 |
| Quarterly | 12 Saturdays | ₦85,000 |
| Bi-Annual | 24 Saturdays | ₦165,000 |

- CTA: "Register Your Child" → /parent/register-club (express club-only registration).
- Gallery lightbox (tap to expand photos).

### 5.6 Educational Consulting Page (`services/consulting.php`)

- Three audience tabs: For Parents, For Teachers, For Schools.
- **For Parents:** One-on-one guidance on learning strategies, school selection, home support. Example from transcript: infant nurturing consultation, child development follow-up. Fee: ₦10,000.
- **For Teachers:** Practical workshops on modern teaching methodologies, classroom management, curriculum delivery.
- **For Schools:** End-to-end curriculum design, teacher onboarding, alignment with national standards. CTA → /school/inquiry
- Testimonials or case study previews.

### 5.7 Bookstore / Shop (`shop.php` + `shop-product.php`)

The "Consult Booklets" menu item on the website redirects to the shop filtered by consult booklets. The full shop is a standalone e-commerce experience.

**Shop Listing Page (`shop.php`):**
- Filters sidebar (slide-out drawer on mobile):
  - Category: Consult Booklets, Workbooks, Exercise Books, Textbooks, Stationery, Educational Toys, Recommended Reading
  - Grade Level: All Grades, Primary 1-3, Primary 4-6, JSS 1-3
  - Subject: Mathematics, English, Science, etc.
  - Sort by: Price (low-high, high-low), Newest, "Staff Picks" first
- Product grid: 2 columns mobile, 3-4 columns desktop.
- Each product card: cover image, name, price (with compare price strikethrough if on sale), "Add to Cart" button.
- "Staff Picks" badge on featured products (is_featured = 1).

**Single Product Page (`shop-product.php`):**
- Large product image (zoomable on tap).
- Product name, price, description.
- Grade level, subject, series (if applicable).
- "Add to Cart" button + quantity selector.
- "Related Products" section below.

**Cart (`cart.php`):**
- Line items with image thumbnail, name, quantity adjuster, unit price, line total.
- Cart summary: subtotal, delivery fee (configurable in settings), total.
- "Proceed to Checkout" button.

**Checkout (`checkout.php`):**
- If not logged in: prompt to login or create account (quick registration).
- Delivery address form: full name, phone, address, city, delivery notes.
- Payment method: Bank Transfer only.
- On submission:
  1. Order created with status `pending_payment`.
  2. Page shows all active bank accounts from the `bank_accounts` table. Primary account listed first with a "Recommended" badge. Each account shows: Bank Name, Account Name, Account Number with a "Copy" button for easy mobile copying.
  3. Text: "Please transfer ₦XX,XXX to any of the accounts above. Include your order number [TTK-ORD-XXXX] as payment reference. Once we confirm your payment, your order will be processed."
  4. Confirmation email sent with order summary and all bank account details.
  5. Admin receives "New Order" notification in Hub.
  6. Finance admin manually marks order as `awaiting_confirmation` → `processing` → `shipped` → `delivered`.

**IMPORTANT — Consult Booklets IP Protection:**
Consult booklets displayed on the shop show only the cover image and a generic description. The first 3 pages (which contain proprietary methodology — purpose page, scheme of work, author info) are NEVER shown as previews. The product description mentions "Includes full scheme of work and structured question papers" but does not reveal the content. Consult booklets are sold to parents/guardians only — this is enforced socially (no technical block needed), but the product page can include a note: "Designed for parents and students. For institutional licensing, contact us."

### 5.8 Blog / Resources Page (`blog.php` + `blog-post.php`)

**Blog Listing:**
- Grid of blog posts: featured image, title, category tag, excerpt, date, "Read More".
- Category filter tabs: All, Parenting Tips, STEM Activities, Reading Guides, Educational Insights, Announcements.
- Pagination (10 posts per page).

**Single Blog Post:**
- Full article with formatted HTML content.
- Author attribution: "By Janeth Joseph" with photo.
- Social share buttons: WhatsApp (wa.me with pre-filled text), copy link.
- Related posts section.
- Each blog post has its own OG meta tags for rich WhatsApp previews.

### 5.9 Contact Page (`contact.php`)

- Structured inquiry form:
  - Full Name, Email, Phone
  - Inquiry Type dropdown: General, Tutorials, STEM Club, Consulting, Other
  - Message textarea
  - Submit button
- On submission: saves to `contact_inquiries` table, sends email to admin, shows "Thank you" toast.
- Interactive map: Google Maps embed showing Atunrase Estate, 7 Alh. Owolegbon Street, Gbagada, Lagos.
- Direct contact info: phone, email, social handles.
- Business hours (if applicable).

### 5.10 Global Components

**Header (Desktop):**
- Left: Think & Tinker logo (compact wordmark variant).
- Center: Navigation links — Home, About, Services (dropdown), Shop, Blog, Contact.
- Right: "Parent Login" button (Tinker Teal outline), "Teacher Hub" link.
- Sticky on scroll with subtle shadow.

**Header (Mobile):**
- Top bar: logo centred, notification bell (if logged in) on right.
- Bottom navigation: 5 fixed tabs as described in section 2.5.

**Footer:**
- 4-column layout (stacked on mobile):
  - Column 1: Logo + tagline + social links.
  - Column 2: Quick Links — Home, About, Services, Shop, Blog, Contact.
  - Column 3: Portals — Parent Portal, Teacher Hub, School Portal.
  - Column 4: Contact — Address, Phone, Email, Map link.
- Bottom bar: "© 2026 Think & Tinker Kids Club. RC9030989. All Rights Reserved."
- NDPR compliance link.

---

## PART 6: PARENT PORTAL

### 6.1 Registration & Onboarding

**Two entry points for parent registration:**

**A. Full Tutorial Onboarding (`/parent/register` → `/parent/onboard`):**
1. **Registration Form:** Parent's full name, email, phone, password. On submit: account created, welcome email sent, auto-login.
2. **Admission Form (`/parent/onboard` step 1):** Child's first name, last name, date of birth, gender, current school, grade level, medical notes (allergies, conditions), photo (optional). Multiple children can be added.
3. **Service Selection (step 2):** Which service(s) — Tutorials, STEM Club, or Both. For tutorials: preferred frequency (2x/week, 3x/week), preferred days/times.
4. **Digital Contract (step 3):** Full service provision contract displayed. Draw-to-sign canvas at the bottom. Parent draws signature with finger (mobile) or mouse (desktop). On sign:
   - Signature image saved as PNG to `/uploads/signatures/`.
   - `contracts` record created with: signer_name (captured), signer_ip (`$_SERVER['REMOTE_ADDR']`), signed_at (current timestamp), signature_hash (SHA-256 of `signer_name|signed_at|signer_ip|contract_number`).
   - Contract PDF generated via DomPDF with embedded signature image.
   - PDF emailed to parent + saved to Document Vault.
5. **Invoice & Payment (step 4):** Invoice auto-generated (registration fee + assessment fee or club membership). Page shows bank account details for transfer. Parent copies order number as reference. Status: `pending_payment`.
6. **Account Active:** Parent dashboard becomes accessible immediately. Invoice status updated by admin when payment is confirmed.

**B. Express Club-Only Registration (`/parent/register-club`):**
1. Lightweight form: Parent name, email, phone, password, child name, date of birth, gender, medical notes, membership plan selection.
2. No contract signing (club terms agreed via checkbox).
3. Invoice generated for selected club plan.
4. Bank details shown for payment.
5. Dashboard accessible immediately. Club-specific views.

### 6.2 Parent Dashboard (`/parent/index.php`)

The first thing a parent sees when they log in. Must answer: "What's happening with my child right now?"

**Layout (Mobile):**
- Greeting: "Good morning, [Parent Name]" with current date.
- Child selector: If parent has multiple children, horizontal scrollable pills to switch between them.
- **Upcoming Sessions card:** Next 3 scheduled sessions with date, time, tutor name.
- **Latest Session Note card:** Most recent session note preview (2-3 lines) with "Read More" link.
- **Progress Snapshot:** Visual progress bar or radial ring showing topics mastered vs in-progress vs not started.
- **Quick Links grid:** Calendar, Messages, Documents, Payments, Shop.
- **Club Status card** (if enrolled): Next Saturday's club session, attendance streak.
- **Notification bell** in top nav: unread count badge.

### 6.3 Child Profile (`/parent/child.php`)

- Child's photo, name, age, grade, school.
- Active services: Tutorials (frequency, assigned tutor), Club membership (plan, expiry).
- Medical notes (editable by parent).
- All-time progress summary.

### 6.4 Session Calendar (`/parent/calendar.php`)

- Monthly calendar view (default: current month).
- Colour-coded dots on session dates: Green = completed, Blue = upcoming, Orange = rescheduled, Gray = cancelled.
- Tap a date → shows session details: time, tutor, status, session note (if completed).
- Navigation: left/right arrows to move months.
- "Download Calendar" button → generates DomPDF monthly calendar per child and downloads as PDF.
- If rescheduled: shows original date with strikethrough and new date highlighted.

This replaces the paper calendar that Janeth currently photographs and sends via WhatsApp.

### 6.5 Session Notes & Progress (`/parent/notes.php`)

- Chronological list of all session notes for the selected child.
- Each note shows: date, tutor name, topics covered (as tags), full note text, images (if any).
- Progress tracker: table or visual grid of subjects → topics → status (Not Started / In Progress / Mastered) with colour indicators (Gray / Idea Gold / Leaf Green).
- Filter by subject or date range.

### 6.6 Messaging — WhatsApp-Style Chat UI (`/parent/messages.php`)

**Design:** The messaging interface must look and feel like WhatsApp. This is critical for maximum friendliness and familiarity.

**Layout:**
- Chat bubble UI. Parent's messages on the right (Tinker Teal bubbles, white text). "Think & Tinker" replies on the left (Cloud Gray bubbles, Charcoal text).
- Timestamp below each bubble.
- Text input at the bottom with send button.
- "Think & Tinker" avatar: TTK logo icon.

**How it works under the hood:**
1. Parent types a message → saved to `messages` table with `sender_type = 'parent'`, `display_as = [parent's name]`.
2. Admin sees the message in the Hub's message queue (`hub.think-tinker.com/messages`).
3. Admin can:
   - Reply directly → saved with `sender_type = 'admin'`, `display_as = 'Think & Tinker'`.
   - Assign to a tutor → the tutor sees it on their dashboard. Tutor drafts a reply → saved with `sender_type = 'tutor'`, `display_as = 'Think & Tinker'`.
4. Parent always sees replies from "Think & Tinker" — never an individual tutor's name.
5. If the message is about a specific child, the `child_id` field links it for context.

**Notifications:**
- When parent sends a message: admin gets in-app notification + email.
- When admin/tutor replies: parent gets in-app notification + email ("You have a new message from Think & Tinker. Log in to view.").

**Polling:** The chat UI polls `MessageController.php?action=get_new_messages` every 10 seconds via AJAX to check for new messages. No WebSockets needed on shared hosting.

### 6.7 Document Vault (`/parent/documents.php`)

- List of all documents associated with this parent/child:
  - Signed contracts (PDF)
  - Invoices (PDF)
  - Receipts (PDF)
  - Progress Reports (PDF)
  - Monthly Calendars (PDF)
  - Club Membership Certificates (PDF)
- Each document: name, date, type icon, "Download" button, "View" button (opens in-browser PDF viewer).
- Sorted by date (newest first).

### 6.8 Payment History (`/parent/payments.php`)

- List of all invoices: invoice number, date, type (registration/tutorial/club/bookstore), amount, status (paid/pending/overdue).
- Status badges: Paid (Leaf Green), Pending (Idea Gold), Overdue (Story Coral).
- Tap an invoice → view full details + download PDF.
- If pending: shows bank account details for payment.
- "Download Receipt" button appears on paid invoices.

### 6.9 Bookstore Quick Link

- Shortcut to `/shop` with context: "Recommended materials for [child's grade level]" → pre-filtered.

---

## PART 7: TEACHER PORTAL

### 7.1 Teacher Portal Landing (`/teacher/index.php`)

Public page (no login required to view). Positions Think & Tinker as a professional development hub for educators.

- Hero: "Expert Resources for Educators Who Shape Tomorrow"
- Value proposition: Free resources, workshop access, community.
- CTA: "Create Your Free Profile" → /teacher/register
- CTA: "Browse Resources" → /teacher/resources

**Share from Hub:** Admin can share this page's URL directly via WhatsApp from the Operations Hub using a "Share" button that constructs a `wa.me` link.

### 7.2 Teacher Registration (`/teacher/register.php`)

- Form: Full name, email, phone, password, school name, subjects taught, years of experience, short bio.
- On submit: account created (user_type = 'teacher'), `teacher_profiles` record created, welcome email sent.
- Teachers can also apply to become official Think & Tinker tutors from within their dashboard.

### 7.3 Teacher Dashboard (`/teacher/dashboard.php`) — Auth required

- Welcome message with profile completeness indicator.
- Recent resources added.
- Upcoming workshops.
- "Apply as Tutor" call-out card (if `wants_to_tutor = 0`).
- If application submitted: status indicator (Under Review / Accepted / Rejected).

### 7.4 Resources (`/teacher/resources.php`) — Public

- Downloadable resources organized by category: Lesson Plans, STEM Guides, Reading Lists, Worksheets, Training Materials.
- Each resource: title, description, file type icon, download button.
- Downloads tracked (`download_count` incremented).
- Some resources may require login to download (configurable per resource).

### 7.5 Apply as Tutor (`/teacher/apply.php`) — Auth required

- Form: motivation letter, availability, preferred age groups, references.
- On submit: `teacher_profiles.wants_to_tutor = 1`, `application_status = 'applied'`.
- Admin reviews in Hub → can accept (convert to tutor role) or reject.

---

## PART 8: SCHOOL PORTAL

### 8.1 School Services Overview (`/school/index.php`) — Fully public

- Professional page targeting school administrators and principals.
- Services showcased:
  - Curriculum Audit & Restructuring
  - Teacher Training Workshops
  - Student Assessment Programs
  - Custom Resource Development
- Tone: Strategic, results-oriented, collaborative (per brand voice guidelines).

### 8.2 Case Studies (`/school/case-studies.php`)

- Grid of past consulting engagements (from `case_studies` table).
- Each case study: school name (anonymized if needed), challenge, solution, results.
- CTA after each: "Want similar results? Request a proposal."
- Initially seeded with any past work Janeth can provide (curriculum audits, teacher training, even pro-bono work — rebranded professionally).

### 8.3 Inquiry Form (`/school/inquiry.php`)

- Structured form: school name, contact person name, title/role, email, phone, service interest (checkboxes), additional details textarea.
- On submit: saved to `contact_inquiries` (type = 'school_proposal'), email notification to admin.
- Confirmation page: "Thank you. We'll prepare a tailored proposal within 48 hours."
- Admin can generate a Quote/Proposal PDF from the Hub and email it.

---

## PART 9: ONLINE BOOKSTORE

(Covered in detail in Section 5.7. This section adds the Hub-side management.)

### 9.1 Bookstore Management in Hub (`hub.think-tinker.com/bookstore`)

**Product Management:**
- List of all products with: image thumbnail, name, category, price, stock level, status (active/inactive).
- "Add Product" button → form: name, description (WYSIWYG), category, grade, subject, series, price, compare price, stock quantity, cover image upload, gallery images upload, is_featured toggle.
- Edit existing products inline.
- Image upload: max 800px width, auto-compressed via GD library.
- Adding a product is as simple as WhatsApp — the interface guides step by step (per the meeting discussion about ease of use).

**Order Management:**
- List of all orders with: order number, customer name, date, total, status.
- Status pipeline: Pending Payment → Awaiting Confirmation → Processing → Shipped → Delivered.
- Admin clicks to advance status. Each status change triggers a notification email to the parent.
- Order detail view: line items, delivery address, payment reference, status history.

**Inventory Dashboard:**
- Stock level overview for all products.
- Low stock alerts: products below their `low_stock_threshold` highlighted in Story Coral.
- Quick restock: update quantity inline.

---

## PART 10: OPERATIONS HUB (hub.think-tinker.com)

### 10.1 Hub Login & Authentication

- Clean login page: email + password + "Remember Me" checkbox + "Forgot Password" link.
- Branded with TTK logo and Cloud Gray background.
- After login: redirect to Dashboard.
- Session check on every Hub page. If `users.is_active = 0`, session is immediately destroyed ("Your account has been deactivated. Contact administration.").

### 10.2 Admin Dashboard (`hub/dashboard.php`)

Designed for Janeth to understand the health of her business in under 60 seconds.

**Key Metrics Row (4 cards):**
1. Total Revenue (this month) — Leaf Green
2. Total Expenses (this month) — Story Coral
3. Net Margin (Revenue - Expenses) — Tinker Teal if positive, Story Coral if negative
4. Active Clients — Deep Navy

**Second Metrics Row (4 cards):**
5. Pending Invoices (count + total value)
6. Upcoming Sessions (next 7 days)
7. New Inquiries (unread)
8. Pending Orders (bookstore)

**Activity Feed:**
- Live timeline (most recent first): new inquiry, signed contract, completed session, bookstore order, payment received, new club registration.
- Each item: icon + description + timestamp + link to relevant module.

**Quick Actions (floating action button on mobile, button row on desktop):**
- Create Invoice
- Record Expense
- Schedule Session
- Add Client
- New Blog Post

**Financial Mini-Chart:**
- Simple bar chart (using Chart.js via CDN or pure CSS bars): Revenue vs Expenses for the last 6 months.
- Below: Profit margin percentage trend.

### 10.3 Client Management Module (`hub/clients.php`)

**Client List View:**
- Searchable, filterable table of all parent/guardian records.
- Columns: Name, Phone, Email, Children (count), Active Services, Status, Last Activity.
- Search by name, phone, or email.
- Filter by: status (active/inactive), service type (tutorials/club/both).

**Client Detail View:**
- Parent info: name, phone, email, registration date.
- Children cards: each child with photo, name, age, grade, services, assigned tutor.
- Service history: list of all sessions, invoices, contracts.
- Communication log: all messages exchanged.
- "Add Child" button → add another child to this parent.
- "Generate Invoice" button → opens invoice creation pre-filled with parent info.
- "Generate Contract" button → opens contract generation.

**Add New Client Form:**
- Parent details + at least one child's details.
- Service selection + tutor assignment.
- Auto-generates account activation email to parent.
- As discussed in the meeting: admin creates accounts, the system sends an activation link to the parent's email, parent clicks the link to set their password. The parent never needs to contact admin for password issues.

### 10.4 Session Management Module (`hub/sessions.php`)

**Calendar View:**
- Full calendar UI (FullCalendar.js via CDN, or custom-built monthly grid).
- All students' sessions colour-coded by child or tutor.
- Click on a date → see all sessions that day.
- Click on a session → session detail (child, tutor, time, status, notes).
- "Add Session" → pick child, tutor, date, time.
- Bulk scheduling: "Schedule recurring" → select child, tutor, days of week, start/end date → auto-creates all sessions.
- Rescheduling: drag-and-drop a session to a new date (records `rescheduled_from`).

**Session Notes:**
- When a tutor submits a session note (via their limited dashboard), it appears here immediately for admin visibility.
- Admin can view all notes across all tutors.
- Notes status: Draft (tutor saved but hasn't submitted) / Submitted (visible to parent).

**Voice-to-Text Note Taking (Tutor View):**
- Tutor opens their dashboard → selects a completed session → "Add Note" button.
- Interface: large "Record" button (microphone icon, Spark Orange).
- On tap: browser requests microphone access. Web Speech API starts real-time transcription.
- Transcribed text appears in a text area in real-time as the tutor speaks.
- Tutor can edit the text, add topic tags, attach photos.
- "Submit" button → note saved with `status = 'submitted'`, instantly visible on parent dashboard.
- Fallback: if browser doesn't support Web Speech API, text area is shown for manual typing.

**Progress Tracking:**
- Table view per child: Subject → Topic → Status (Not Started / In Progress / Mastered).
- Tutor or admin can update status.
- Visual: colour-coded cells (Gray / Idea Gold / Leaf Green).

### 10.5 Financial Management Module (`hub/finance.php`)

This is where the business's financial health is managed. Addresses the transcript discussion about separating personal and business finances, tracking expenses, and calculating profit margins.

**Sub-views (tabs within the Finance module):**

**A. Income / Revenue:**
- Auto-populated from paid invoices.
- Each entry: date, source (tutorial payment, club membership, bookstore sale, consulting fee, registration fee), amount, client name, invoice reference.
- Filter by: date range, income type.
- Total revenue displayed prominently.

**B. Expenses:**
- Full expense log: date, category, description, amount, child (if applicable), receipt image.
- "Add Expense" button → form: category dropdown (Transportation, Salary, Materials, Printing, Utilities, Rent, Equipment, Marketing, Miscellaneous), description, amount, date, child (optional dropdown), receipt photo upload.
- Each expense can be linked to a specific child for per-child profitability analysis.
- Janeth's personal salary should be entered as a recurring monthly expense under "Salary" category.
- Filter by: date range, category, child.

**C. Invoices:**
- List of all invoices: number, client, date, amount, status, actions.
- "Create Invoice" button → select parent, add line items (description, quantity, unit price), set due date, notes.
- On create: PDF auto-generated via DomPDF, email sent to parent with PDF attachment.
- "Mark as Paid" button → captures: payment method, payment reference, confirmed by. Auto-generates receipt PDF. Sends receipt email to parent.
- Overdue invoices highlighted in Story Coral.

**D. Receipts:**
- List of all receipts generated.
- Each linked to its invoice.
- Download/resend options.

**E. Profit & Loss Report:**
- Auto-calculated: Total Revenue - Total Expenses = Net Profit.
- Period selector: This Month, Last Month, This Quarter, This Year, Custom Range.
- Breakdown by category (both income and expenses).
- Profit margin percentage.
- Trend line: monthly P&L over the last 12 months.
- "Download as PDF" button → generates branded P&L report via DomPDF.

**F. Cash Flow Projection (Phase 4):**
- Forward-looking: expected income (pending invoices + active subscriptions) vs expected expenses (recurring salaries, rent).

### 10.6 Staff & Tutor Management Module (`hub/staff.php`)

**Staff List:**
- All employees and tutors: photo, name, role, job title, status, hire date.
- Click → staff detail view.

**Staff Detail:**
- Personal info, employment details, salary, bank info, guarantor details.
- Assigned students (for tutors): list of children they tutor.
- Attendance history: calendar grid with colour-coded attendance (present/absent/late).
- Session history (for tutors): all sessions delivered.
- "Deactivate" button → sets `is_active = 0`, kills all sessions.
- "Generate ID Badge" button → generates staff ID badge PDF via DomPDF (using the existing badge design from context documents).

**Add Employee:**
- Form: personal details, job title, employment type, salary, bank details, guarantor details.
- On save: user account created, activation email sent. RBAC role assigned (tutor/finance_admin/education_admin).

**Attendance Tracking:**
- Daily attendance form: list of active staff → mark present/absent/late/excused.
- Monthly attendance summary per staff member.

### 10.7 Bookstore Management Module

(Covered in Section 9.1)

### 10.8 Club Operations Module (`hub/club.php`)

**Membership Registry:**
- All active club members: child name, parent name, plan, start date, end date, status.
- "Add Member" button → select existing child (or create new via express registration), select plan, generate invoice.
- Expiry alerts: memberships expiring within 7 days highlighted.

**Saturday Attendance:**
- Date picker (defaults to current/upcoming Saturday).
- Checklist of all active members → mark present/absent/excused.
- Check-in and check-out times (optional).
- Attendance rate percentage displayed.

**Certificate Generation:**
- "Generate Certificate" button per member.
- Uses the existing certificate template (from context documents): "Think 'n' Tinker Certificate of Membership" with child's name, date, signed by Janeth Joseph (Principal) and Committee Lead.
- Generated via DomPDF, saved to `club_memberships.certificate_pdf`.
- Downloadable from parent portal's Document Vault.

### 10.9 Blog / Content Management Module (`hub/blog.php`)

**Post List:**
- All blog posts: title, category, status (Draft/Published), date, author.
- "New Post" button.

**Post Editor:**
- Title field.
- Category dropdown.
- TinyMCE WYSIWYG editor (loaded via CDN) for full HTML editing.
- Featured image upload.
- Tags input (comma-separated or tag picker).
- Excerpt field (auto-generated from first 160 characters if empty).
- OG image upload (optional, uses featured image if not set).
- Status toggle: Draft / Published.
- "Publish" button → sets `published_at` timestamp, post appears on public blog.

### 10.10 Messaging Module (`hub/messages.php`)

**Message Queue:**
- List of all conversations, sorted by most recent activity.
- Each row: parent name, child name (if specified), last message preview, timestamp, unread badge.
- Click → opens the conversation thread.

**Conversation View:**
- WhatsApp-style chat UI (same as parent's view, but with admin controls).
- All messages in the thread displayed as bubbles.
- Admin can type and reply directly.
- "Assign to Tutor" button → select a tutor from dropdown. The tutor sees this conversation on their dashboard and can draft a reply. The reply is saved with `display_as = 'Think & Tinker'`.
- Admin can see who sent each message (internal label: "Replied by: [tutor name]") even though the parent only sees "Think & Tinker."

### 10.11 Settings & RBAC Module (`hub/settings.php`)

**Business Setup (Tab 1 — most important):**
This is where Janeth manages everything about her business without touching any code or config file.

- **Business Info tab:** Business name, address, phone, email, social handle, WhatsApp number, RC number, email sender name/address. Reads from and writes to the `settings` table. These values appear on the public website footer, contact page, invoices, contracts, and all PDFs.
- **Bank Accounts tab:** Full CRUD on the `bank_accounts` table. Janeth can add multiple bank accounts (FCMB, GT Bank, Access, etc.), edit details, toggle active/inactive, and set which one is primary. All active accounts appear on checkout pages and invoice PDFs so parents can choose where to pay. Primary account is shown first with a "Recommended" badge.
- **Services & Pricing tab:** Consultation fee, registration fee, assessment fee, session rate (primary), session rate (secondary), club plan prices (trial, monthly, quarterly, bi-annual). Reads from and writes to the `settings` table. These values are used when generating invoices, on the services pages, and on the club registration page.

Business Info and Pricing fields read from and write to the `settings` table. Bank Accounts are a dedicated `bank_accounts` table supporting multiple rows. When Janeth updates a price or adds a bank account, it takes effect immediately across the entire system — no developer intervention, no file editing, no redeployment.

**RBAC Management (Tab 2):**
- List of all roles with their permissions.
- "Edit Role" → checkboxes for each permission (grouped by module).
- "Add Role" → create a new custom role.
- User role assignment: on each user's profile, admin selects which role(s) to assign.

**Location Management (Tab 3 — future-ready):**
- Single location shown for now.
- "Add Location" form hidden but ready (name, address, phone, email).
- When activated, all data-fetching queries will filter by the user's assigned `location_id`.

### 10.12 Document Generation Module (`hub/documents.php`)

Central place to generate any branded document on demand.

**Document Types:**
- Invoice → select parent, add line items → generate PDF.
- Receipt → select paid invoice → generate PDF.
- Service Contract → select parent + child → generate PDF with signature capture.
- Admission Form → select child → generate pre-filled admission form PDF.
- Session Progress Report → select child + date range → generate comprehensive progress report PDF.
- Monthly Calendar → select child + month → generate branded monthly calendar with all session dates marked.
- Quote / Proposal → free-form: title, description, line items → generate professional proposal PDF for school consulting.
- Club Membership Certificate → select child → generate certificate PDF.
- Staff ID Badge → select staff member → generate badge PDF (front + back, as per existing design).

All documents are saved to the `/uploads/` directory structure and linked in the database.

---

## PART 11: AUTO-GENERATED DOCUMENTS (DomPDF)

### 11.1 Invoice PDF

- A4 portrait.
- Header: TTK logo (top-left), "INVOICE" title (top-right), invoice number, date, due date.
- From: Think & Tinker Kids Club, address, RC number.
- Bill To: Parent name, address, phone.
- Line items table: description, quantity, unit price, total.
- Subtotal, discount (if any), total.
- Bank details for payment.
- Footer: teal bar with contact info.
- TTKC watermark (faded, bottom-right of body area).

### 11.2 Receipt PDF

- Same layout as invoice but with "RECEIPT" title and "PAID" watermark in Leaf Green.
- Payment details: method, reference, date of payment.
- "Thank you for your payment" message.

### 11.3 Service Contract PDF

- Header with TTK branding.
- Full terms and conditions text.
- Service details: child name, service type, frequency, session rate.
- Parent obligations (from Parent Handbook).
- Think & Tinker obligations.
- Cancellation policy.
- Embedded signature image.
- Signer details: name, date/time, IP address, verification hash.
- Director signature line: Janeth Joseph, Academic Lead.

### 11.4 Admission Form PDF

- Pre-filled from the child's database record.
- Parent information section.
- Child information: name, DOB, gender, school, grade, medical notes.
- Emergency contact.
- Consent checkboxes (photography, medical emergency response).
- Signature area.

### 11.5 Session Progress Report PDF

- Child's name, grade, reporting period.
- Subject-by-subject breakdown with topic list and mastery status.
- Tutor's qualitative notes summary.
- Visual progress indicators (colour-coded bars).
- Recommendations for continued improvement.

### 11.6 Monthly Calendar PDF

- Branded monthly calendar grid.
- Child's name and month/year at top.
- Session dates marked with tutor name and time.
- Rescheduled sessions noted.
- Legend: colour codes for completed/upcoming/rescheduled.
- "Total Sessions This Month: X" summary.
- This replaces the paper calendar Janeth currently photographs.

### 11.7 Quote / Proposal PDF

- Professional consulting proposal for schools.
- Cover page with TTK branding.
- Problem statement / needs analysis.
- Proposed solution with scope of work.
- Timeline and deliverables.
- Pricing table.
- Terms and conditions.
- About Think & Tinker section.

### 11.8 Club Membership Certificate PDF

- Landscape A4.
- Design matches existing certificate template: rainbow, sun, cartoon children.
- "Think 'n' Tinker — Certificate of Membership"
- "This Certifies that [Child Name] is an official member of the Think & Tinker Club."
- "A community of curious minds, young creators, and problem solvers who explore ideas through reading, science, technology, art, and hands-on discovery."
- Date of membership.
- Signed by: Janeth Joseph (Principal) and Committee Lead.
- TTK Kids Club logo centred at bottom.

### 11.9 Staff ID Badge PDF

- Card-sized (85.6mm × 54mm or custom).
- Front: TTK logo at top, staff photo (circular), name, title ("STAFF" or "TUTOR"), ID number, email, phone, barcode.
- Back: TTK logo, Terms & Conditions (identification, authorized use), join date, expiry ("Upon conclusion of membership").
- Design matches existing badge templates from context documents.

---

## PART 12: CONNECTIONS & DATA FLOW

### 12.1 How the Website Connects to the Hub

Both the public website (`think-tinker.com`) and the Operations Hub (`hub.think-tinker.com`) share the same MySQL database and the same `/api/` controllers. The Hub is simply a different frontend that calls the same API endpoints with elevated permissions.

```
[Public Website]  ──AJAX──→  [api/ShopController.php]  ──PDO──→  [MySQL Database]
[Parent Portal]   ──AJAX──→  [api/ParentController.php] ──PDO──→  [MySQL Database]
[Operations Hub]  ──AJAX──→  [api/FinanceController.php] ──PDO──→ [MySQL Database]
```

The `/api/` directory contains ALL business logic. Frontend pages are just HTML/CSS/JS shells that call these endpoints. This means:
- The public website and the Hub always show the same data.
- When admin updates a product price in the Hub, it instantly reflects on the shop.
- When a parent sends a message from the portal, it instantly appears in the Hub's message queue.

### 12.2 Parent Portal ↔ Hub Data Flow

```
Parent registers → [users] + [children] created → Admin sees new client in Hub
Admin creates invoice → [invoices] created → Parent sees invoice in Payment History
Parent sends message → [messages] created → Admin sees in Message Queue
Admin replies → [messages] created → Parent sees in chat
Tutor writes note → [session_notes] created → Parent sees on Notes page
Admin generates calendar PDF → [file saved] → Parent downloads from Document Vault
```

### 12.3 Notification Flow

```
Event occurs (e.g., new session note submitted)
    ↓
NotificationController::create()
    ├──→ Insert into [notifications] table (in-app bell)
    └──→ Call mailer.php → PHPMailer sends email via SMTP
```

All notifications follow this dual-channel approach: in-app bell + email.

### 12.4 Payment & Order Flow (Bank Transfer)

```
1. Invoice/Order created → status: "pending_payment"
2. Parent sees bank account details on their portal/checkout page
3. Parent transfers money to FCMB account (offline)
4. Finance Admin logs into Hub → finds the invoice/order
5. Finance Admin clicks "Confirm Payment" → enters: payment reference, method
6. System: updates status to "paid" / "processing"
7. System: auto-generates Receipt PDF
8. System: sends receipt email to parent
9. System: creates in-app notification for parent
```

### 12.5 Session Reporting Flow

```
1. Admin schedules session → [sessions] record created (status: scheduled)
2. Parent sees session on their calendar
3. Tutor conducts session
4. Tutor opens their dashboard → selects the session → "Add Note"
5. Tutor taps "Record" → speaks → Web Speech API transcribes in real-time
6. Tutor reviews, edits, adds topics and photos → clicks "Submit"
7. [session_notes] record created (status: submitted)
8. [sessions] status updated to "completed"
9. Parent receives notification: "New session note available for [child]"
10. Parent opens Notes page → sees the note with topics and progress
```

---

## PART 13: DEPLOYMENT & INFRASTRUCTURE

### 13.1 Shared Hosting Setup

1. Purchase hosting plan on TrueHost.com.ng with: PHP 8.x, MySQL 8.x, minimum 10GB storage, SSL support.
2. Point `think-tinker.com` DNS to TrueHost nameservers.
3. Create subdomain `hub.think-tinker.com` in cPanel → points to a separate directory (e.g., `/home/username/hub.think-tinker.com/`).
4. Install SSL certificates via cPanel's "Let's Encrypt" or "AutoSSL" feature for both domains.
5. Create MySQL database + user via cPanel's "MySQL Databases."
6. Import schema SQL file via phpMyAdmin.
7. Upload codebase via cPanel File Manager or FTP.
8. Upload `vendor/` folder (Composer dependencies — DomPDF + PHPMailer — installed locally, uploaded to server).
9. Copy `.env.example` to `.env` and fill in database credentials and SMTP password. That's all — business settings (prices, address, bank details) are managed from the Hub's Business Setup module after first login.
10. Set up cron job (if needed) for: monthly calendar PDF generation, overdue invoice status updates, club membership expiry checks.

### 13.2 .htaccess Configuration

```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"

# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "\.(env|json|lock|md|gitignore)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Protect uploads directory from PHP execution
<Directory "uploads">
    php_flag engine off
</Directory>

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
</IfModule>

# GZIP compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

### 13.3 Backup Strategy

- **Database:** Set up a cPanel cron job to run `mysqldump` daily, saving to a `/backups/` directory. Keep last 7 days.
- **Files:** Use cPanel's built-in backup feature for weekly full backups.
- **Uploads:** The `/uploads/` directory is the most critical — contains all generated PDFs, signatures, and product images.
- **Off-site:** Periodically download backups to Google Drive or another cloud storage (manual process on shared hosting).

### 13.4 File Storage Policy

- **Product images:** Max 800px width, JPEG, compressed to ≤200KB. GD library auto-resizes on upload.
- **OG banners:** 1200×630px, JPEG, ≤300KB.
- **Profile photos:** Max 400px width, JPEG, ≤100KB.
- **Signature images:** PNG, canvas-captured, typically ≤50KB.
- **PDF documents:** DomPDF output, typically 100–500KB each.
- **Blog images:** Max 1200px width, JPEG, ≤300KB.
- **Expense receipts:** Max 1200px width, JPEG, ≤300KB.
- **Session photos:** Max 1200px width, JPEG, ≤300KB.
- **File naming convention:** `{type}_{ID}_{timestamp}.{ext}` — e.g., `invoice_TTK-INV-20260601-0001_1717200000.pdf`
- **Directory structure:** Year/month subdirectories for date-based documents: `/uploads/invoices/2026/06/`

---

## PART 14: PHASED ROLLOUT

### Phase 1 — Foundation (Week 1–2)

- Public website: Homepage, About, Services pages, Contact page.
- Hub: Login, Dashboard (basic), Settings (business info, bank details, pricing).
- Hub: Client Management (add parent + child, view list).
- Hub: RBAC (super_admin role, module visibility).
- Database schema deployed with seed data.
- SMTP email configured and tested.
- OG meta tags + WhatsApp sharing on all public pages.
- SSL + hosting setup.

### Phase 2 — Portals & Commerce (Week 3–4)

- Parent Portal: registration, onboarding, contract signing (draw-to-sign), dashboard, calendar, payment history, document vault.
- Hub: Session Management (calendar, scheduling, session notes with voice-to-text).
- Hub: Financial Management (income, expenses, invoices, receipts, P&L report).
- Online Bookstore: product catalogue, cart, checkout (bank transfer), order management.
- DomPDF: invoices, receipts, contracts, admission forms, monthly calendars.
- Parent-Admin messaging (WhatsApp-style chat).
- In-app notification bell.

### Phase 3 — Ecosystem Expansion (Week 5–6)

- Teacher Portal: registration, dashboard, resources, workshop registration, tutor application.
- School Portal: services showcase, case studies, inquiry form.
- Hub: Staff Management (profiles, attendance, ID badges, payroll tracking).
- Hub: Club Operations (membership registry, Saturday attendance, certificate generation).
- Hub: Blog Management (TinyMCE editor, publish to website).
- DomPDF: progress reports, club certificates, staff ID badges, quotes/proposals.
- Blog / Resources public pages.
- Express club-only registration flow.

### Phase 4 — Scale & Optimise (Week 7–8)

- Advanced reporting: cash flow projections, per-child profitability, monthly trend analysis.
- Tutor application review workflow.
- Bookstore: "Staff Picks" curation, related products, enhanced search.
- Performance optimization: query optimization, lazy loading images, CDN consideration.
- User training sessions (physical, with Janeth and team).
- Bug fixes and refinements based on real usage.
- Multi-location schema activation (add location switcher to Hub when ready).
- PWA manifest for "Add to Home Screen" native app feel.

---

## APPENDIX A: PRICING STRUCTURE (From Meeting)

| Service | Fee |
|---|---|
| Parent Consultation (1–2 hours, child development guidance) | ₦10,000 |
| Registration Fee (onboarding, pass cards, portal creation) | ₦8,500 |
| Assessment Fee (child assessment, learning plan creation) | ₦15,000 |
| Tutorial Session — Primary Level | ₦20,000 per session |
| Tutorial Session — Secondary Level | ₦22,000 per session |
| STEM & Reading Club — Trial (1 Saturday) | ₦8,000 |
| STEM & Reading Club — Monthly (4 Saturdays) | ₦30,000 |
| STEM & Reading Club — Quarterly (12 Saturdays) | ₦85,000 |
| STEM & Reading Club — Bi-Annual (24 Saturdays) | ₦165,000 |

## APPENDIX B: CURRENT STUDENT ROSTER (From Meeting)

| Student | Frequency | Schedule Pattern |
|---|---|---|
| Ayo Damola | 2x per week | Mon/Wed pattern (specific dates: 1st, 3rd, 8th, 10th, etc.) |
| Mila | 3x per week | Mon/Wed/Fri pattern |
| Rina | 2x per week | Flexible (mother travels for legal work — Nigeria, Cameroon, SA, Ghana). Days vary weekly. |
| Twins (names TBC) | 3x per week | Tue/Thu/Sat fixed. 1 rescheduled class carried over from May. |

## APPENDIX C: BOOKSTORE INITIAL PRODUCT CATEGORIES

| Category | Examples |
|---|---|
| Consult Booklets | Janeth's proprietary subject-specific workbooks (IP-protected, first 3 pages contain methodology) |
| Workbook Starters | Multiple varieties, series-based |
| Pink Series | Specific workbook series |
| Schofield & Sims | UK educational workbook publisher |
| Branded Exercise Books | Think & Tinker branded, subject-assigned (to be provided) |
| Textbooks | (Future — sourced from Yaba bookstore) |
| Stationery | (Future) |
| Educational Toys | (Future) |
| Recommended Reading | (Future) |

Initial launch: 6–10 products seeded by developer from client-provided images and descriptions. Client adds remaining products independently via the Hub's product management interface.

## APPENDIX D: KEY MEETING DECISIONS

1. **No direct parent-teacher communication.** All messages route through admin. Tutor replies display as "Think & Tinker."
2. **Consult booklets are NOT sold to teachers.** Only to parents for their children. The first 3 pages contain proprietary methodology.
3. **Payment is manual bank transfer only.** No Paystack integration for now. Finance admin (Janeth's sister) verifies payments manually.
4. **Janeth must pay herself a fixed salary** to separate personal and business finances. All expenses tracked in the system for accurate profit margin calculation.
5. **Tutor session notes are submitted directly** after the tutor records and reviews them. No admin approval step before publishing to parent dashboard.
6. **Club registration is separate** from tutorial onboarding — a lightweight express flow for club-only parents.
7. **The system must feel like a native app** on mobile. No hamburger menus. Bottom tab navigation. PWA-capable.
8. **Training will be done physically** once the system is built, not virtually.
9. **RBAC is non-negotiable.** Tutors see ONLY their assigned students. Finance admin sees ONLY finance. This was emphasized repeatedly throughout the meeting.
10. **Admin can "kill" any user's session** instantly by deactivating their account.

---

*End of Product Description — Version 2.0*
*This document should be treated as the single source of truth for development. Every page, every module, every table, every data flow is defined here. The full-stack team should execute against this specification.*
