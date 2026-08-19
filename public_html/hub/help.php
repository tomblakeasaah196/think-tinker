<?php
$pageTitle = 'Help Centre';
$currentModule = 'help';
require_once __DIR__ . '/../templates/header-hub.php';

// Get user's accessible modules
$userModules = getUserModules($user);
?>

<div class="page-header">
    <h1>Help Centre</h1>
    <p class="text-muted text-sm">Learn how to use every feature available to you.</p>
</div>

<div class="search-bar mb-3" style="max-width:100%;">
    <span class="search-icon"><?= ttIcon('search', 'tt-icon', 16) ?></span>
    <input type="text" placeholder="Search help topics..." id="helpSearch" oninput="filterHelp(this.value)">
</div>

<div id="helpModules">

<?php
// ============================================================
// HELP CONTENT — only shows modules the user can access
// ============================================================

$helpContent = [

'dashboard' => [
    'icon' => 'home', 'title' => 'Dashboard',
    'intro' => 'Your command centre. See everything at a glance — revenue, client stats, upcoming sessions, and recent activity.',
    'guides' => [
        ['title' => 'Understanding Your Stats', 'content' => 'The top row shows financial metrics: revenue this month, expenses, net margin, and pending invoices. Below that, client metrics show active clients, children, upcoming sessions, and club members. If you\'re a tutor, you\'ll see your personal stats instead — assigned students, pending notes, and upcoming sessions.'],
        ['title' => 'Activity Feed', 'content' => 'The activity feed shows recent events across the system: payments received, sessions completed, new inquiries, and orders placed. Each item is clickable and takes you to the relevant module.'],
        ['title' => 'Quick Actions', 'content' => 'Use the quick action buttons to jump straight to common tasks: add a client, schedule a session, create an invoice, add a product, take club attendance, or write a blog post.'],
    ]
],

'clients' => [
    'icon' => 'users', 'title' => 'Client Management',
    'intro' => 'Manage parents and their children. Add new clients, view their history, and track their engagement.',
    'guides' => [
        ['title' => 'Adding a New Client', 'content' => 'Click "+ Add Client" to open the form. Fill in the parent\'s details (name, email, phone). Optionally add a child\'s profile and assign a service right away. The system creates their account and sends a welcome email with a temporary password.'],
        ['title' => 'Viewing Client Details', 'content' => 'Click any client name to see their full profile: children, active services, assigned tutors, recent invoices, and contracts. You can deactivate a client here — this immediately kills their session and locks them out.'],
        ['title' => 'Searching & Filtering', 'content' => 'Use the search bar to find clients by name, email, or phone. Filter by active/inactive status. Results paginate — use the arrows at the bottom to navigate.'],
        ['title' => 'Children & Services', 'content' => 'Each child can have multiple services (tutorials, STEM club, or both). Services track the assigned tutor, frequency, and session rate. Use the Children page to see all students across all parents.'],
    ]
],

'sessions' => [
    'icon' => 'calendar', 'title' => 'Session Management',
    'intro' => 'Schedule, track, and manage tutorial sessions. View the calendar, add notes, and handle rescheduling.',
    'guides' => [
        ['title' => 'Scheduling a Session', 'content' => 'Click "+ Add Session" to schedule a single session. Select the student, tutor, date, and time. The session appears in both the admin calendar and the parent\'s portal immediately.'],
        ['title' => 'Bulk Scheduling', 'content' => 'Use "Bulk Schedule" to create recurring sessions. Select the student, tutor, date range, and which days of the week. The system creates all sessions at once — perfect for setting up a month of tutorials.'],
        ['title' => 'Completing & Cancelling', 'content' => 'Mark a session as "Completed" when it\'s done, or "Cancel" if it won\'t happen. Completed sessions prompt the tutor to submit session notes.'],
        ['title' => 'Session Notes', 'content' => 'Tutors submit notes after each session describing what was covered, topics taught, and any observations. Notes can be typed or dictated using the voice-to-text button (uses the device microphone). Once submitted, the parent receives a notification and can view the note in their portal.'],
    ]
],

'finance' => [
    'icon' => 'card', 'title' => 'Finance',
    'intro' => 'Create invoices, track expenses, confirm payments, and view profit & loss reports.',
    'guides' => [
        ['title' => 'Creating an Invoice', 'content' => 'Click "+ Create Invoice", select the client, add line items (description, quantity, price), and optionally add a discount. When you click "Create & Send", the system generates a PDF, emails it to the parent with bank transfer details, and creates an in-app notification.'],
        ['title' => 'Confirming Payment', 'content' => 'When a parent pays via bank transfer, click "Mark Paid" on their invoice. Enter the payment reference if available. The system automatically generates a receipt PDF, emails it to the parent, and creates a notification.'],
        ['title' => 'Recording Expenses', 'content' => 'Click "+ Add Expense" to log business expenses. Categorise them (transport, materials, salary, etc.) and optionally attach a receipt photo. Expenses appear in the P&L report.'],
        ['title' => 'Profit & Loss Report', 'content' => 'The P&L tab shows revenue vs expenses for any date range. Revenue comes from paid invoices, expenses from your logged costs. The margin percentage tells you how much of your revenue is actual profit.'],
    ]
],

'staff' => [
    'icon' => 'id-badge', 'title' => 'Staff Management',
    'intro' => 'Add staff members, track attendance, and generate ID badges.',
    'guides' => [
        ['title' => 'Adding Staff', 'content' => 'Click "+ Add Staff" to create a new staff account. Choose their role (tutor, staff, or admin), fill in their details, and the system creates their account and sends a welcome email with login credentials. They can then access the Hub.'],
        ['title' => 'Daily Attendance', 'content' => 'Switch to the Attendance tab, select a date, and mark each staff member as present, absent, late, or on leave. Optionally record clock-in and clock-out times. Click "Save Attendance" to store the records.'],
        ['title' => 'Generating ID Badges', 'content' => 'Click "Badge" next to any staff member to generate a printable PDF ID badge with their photo, name, title, staff ID number, and emergency contact info. Front and back are included for double-sided printing.'],
    ]
],

'bookstore' => [
    'icon' => 'book', 'title' => 'Bookstore',
    'intro' => 'Manage products, process orders, and track inventory.',
    'guides' => [
        ['title' => 'Adding Products', 'content' => 'Click "+ Add Product" to list a new item. Add a name, description, category, price, stock quantity, and cover image. Mark it as "Featured" to highlight it on the shop page. Products appear on the public bookstore immediately.'],
        ['title' => 'Processing Orders', 'content' => 'When a parent places an order, it appears in the Orders tab as "Pending Payment". After they transfer payment, update the status to "Processing", then "Shipped", and finally "Delivered". The parent gets a notification at each stage.'],
        ['title' => 'Inventory', 'content' => 'Stock decreases automatically when orders are placed. Keep an eye on the stock count — products with zero stock show as "Out of Stock" on the public shop.'],
    ]
],

'club' => [
    'icon' => 'tent', 'title' => 'STEM & Reading Club',
    'intro' => 'Manage Saturday club memberships, take attendance, and generate certificates.',
    'guides' => [
        ['title' => 'Managing Memberships', 'content' => 'View all active, expired, or upcoming memberships. Each membership has a plan (trial, monthly, quarterly, bi-annual), a start and end date, and a days-remaining countdown. You can add new memberships manually via "+ Add Membership".'],
        ['title' => 'Saturday Attendance', 'content' => 'On the Attendance tab, select the Saturday date and mark each child as present or absent. Record check-in and check-out times if desired. Click "Save Attendance" to store the records. The system shows a summary of total members vs present.'],
        ['title' => 'Certificates', 'content' => 'Click the graduation cap icon next to any membership to generate a participation certificate. It\'s a landscape PDF with the child\'s name, plan, dates, and the Think & Tinker seal. The parent can view and download it from their document vault.'],
        ['title' => 'Expiring Memberships', 'content' => 'The "Expiring Soon" tab shows memberships ending within 7 days. Each has a "Remind" button that opens WhatsApp with a pre-written renewal message to the parent.'],
    ]
],

'blog' => [
    'icon' => 'book', 'title' => 'Blog',
    'intro' => 'Write and publish blog posts that appear on the public website.',
    'guides' => [
        ['title' => 'Creating a Post', 'content' => 'Click "+ New Post" to open the editor. Add a title, select a category, write your content (HTML is supported — paste from any rich-text editor), add tags, and upload a featured image. Click "Publish" to make it live, or "Save as Draft" to finish later.'],
        ['title' => 'Editing & Deleting', 'content' => 'Click any post title to edit it. You can change the content, category, image, or status. Delete removes the post permanently.'],
    ]
],

'messages' => [
    'icon' => 'message', 'title' => 'Messages',
    'intro' => 'Chat with parents in a WhatsApp-style interface. All replies show as "Think & Tinker".',
    'guides' => [
        ['title' => 'Viewing Conversations', 'content' => 'The left panel shows all conversations, sorted by most recent. Unread messages show a red badge. Click any conversation to open it.'],
        ['title' => 'Replying', 'content' => 'Type your message and click Send. Your reply appears in the parent\'s portal as from "Think & Tinker" — they never see individual staff names. This keeps communication professional and consistent.'],
        ['title' => 'Assigning to Tutors', 'content' => 'If a conversation needs a tutor\'s input, you can assign it to them. The tutor then sees the conversation in their own Hub view and can respond.'],
    ]
],

'documents' => [
    'icon' => 'clipboard', 'title' => 'Document Generator',
    'intro' => 'Generate any branded PDF on demand — invoices, receipts, contracts, certificates, and more.',
    'guides' => [
        ['title' => 'Generating a Document', 'content' => 'Click any document card, fill in the required fields (usually just selecting a student or staff member), and click "Generate". The PDF opens in a new tab. It\'s automatically saved on the server and linked to the relevant record.'],
        ['title' => 'Available Documents', 'content' => 'Invoice (with line items + bank details), Receipt (payment confirmation), Service Contract (terms + signature area), Admission Form (pre-filled), Progress Report (topics + notes), Monthly Calendar (session grid), Club Certificate (landscape, ornamental), Staff ID Badge (front + back), and Quote/Proposal (for school partnerships).'],
        ['title' => 'Automatic Generation', 'content' => 'Some documents generate automatically: invoices are created when you create one in Finance, and receipts are generated when you mark an invoice as paid. You can regenerate any document from this page at any time.'],
    ]
],

'settings' => [
    'icon' => 'sliders', 'title' => 'Settings',
    'intro' => 'Configure your business details, bank accounts, and user permissions.',
    'guides' => [
        ['title' => 'Business Setup', 'content' => 'The first tab contains all your business information: company name, RC number, address, phone, email, WhatsApp number, club plan prices, and more. These values appear on PDFs, emails, and the public website. Edit any field and click Save.'],
        ['title' => 'Bank Accounts', 'content' => 'Add multiple bank accounts that appear on invoices, the checkout page, and payment emails. Set one as "Primary" (recommended to parents). You can activate, deactivate, or delete accounts. The system prevents deleting the last account.'],
        ['title' => 'Roles & Permissions', 'content' => 'Select a role (e.g. Tutor, Staff, Admin) and toggle which permissions they have. Permissions control which Hub modules each role can see and what actions they can perform. Changes take effect immediately — the sidebar updates the next time the user loads a page.'],
    ]
],

]; // end $helpContent

foreach ($helpContent as $slug => $module):
    if (!in_array($slug, $userModules) && $user['user_type'] !== 'super_admin') continue;
?>

<div class="help-module" data-module="<?= $slug ?>">
    <div class="help-module-header" onclick="toggleModule(this)">
        <div style="display:flex;align-items:center;gap:12px;">
            <span class="icon-badge" style="width:40px;height:40px;margin:0;"><?= ttIcon($module['icon'], 'tt-icon', 20) ?></span>
            <div>
                <h3 style="font-size:1rem;margin:0;"><?= $module['title'] ?></h3>
                <p style="font-size:0.75rem;color:#999;margin:0;"><?= htmlspecialchars($module['intro']) ?></p>
            </div>
        </div>
        <span class="help-chevron">▸</span>
    </div>
    <div class="help-module-body" style="display:none;">
        <?php foreach ($module['guides'] as $guide): ?>
        <div class="help-guide">
            <h4><?= htmlspecialchars($guide['title']) ?></h4>
            <p><?= $guide['content'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php endforeach; ?>

</div>

<style>
.help-module {
    background: #FFF;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.help-module:hover { box-shadow: var(--shadow-md); }
.help-module-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    cursor: pointer;
    transition: background 0.15s;
}
.help-module-header:hover { background: rgba(26,175,160,0.03); }
.help-chevron {
    font-size: 1.25rem;
    color: #999;
    transition: transform 0.3s;
}
.help-module.open .help-chevron { transform: rotate(90deg); color: var(--tinker-teal); }
.help-module-body {
    padding: 0 24px 20px;
    border-top: 1px solid var(--cloud-gray);
}
.help-guide {
    padding: 16px 0;
    border-bottom: 1px solid #F0F0F0;
}
.help-guide:last-child { border-bottom: none; }
.help-guide h4 {
    font-size: 0.9375rem;
    color: var(--deep-navy);
    margin-bottom: 6px;
}
.help-guide p {
    font-size: 0.8125rem;
    color: #555;
    line-height: 1.7;
}
</style>

<script>
function toggleModule(header) {
    const module = header.closest('.help-module');
    const body = module.querySelector('.help-module-body');
    const isOpen = module.classList.contains('open');

    if (isOpen) {
        $(body).slideUp(200);
        module.classList.remove('open');
    } else {
        $(body).slideDown(200);
        module.classList.add('open');
    }
}

function filterHelp(query) {
    query = query.toLowerCase();
    document.querySelectorAll('.help-module').forEach(function(mod) {
        const text = mod.textContent.toLowerCase();
        mod.style.display = text.includes(query) ? '' : 'none';
    });
}
</script>

<?php require_once __DIR__ . '/../templates/footer-hub.php'; ?>
