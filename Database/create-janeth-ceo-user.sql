-- create-janeth-ceo-user.sql
--
-- Creates (or updates) Janeth Joseph's Hub account: CEO, full permissions
-- (user_type = 'super_admin', which bypasses every permission check in
-- includes/rbac.php — see hasPermission()/getUserModules()).
--
-- IMPORTANT — read before running: this database's own schema/seed export
-- (Database/vmtqyldz_thinktinker_db.sql) already contains a row at
-- users.id = 1 with this exact name and email (first_name='Janeth',
-- last_name='Joseph', email='janet.joseph@think-tinker.com',
-- user_type='super_admin'), dated 2026-06-01. If that dump reflects what's
-- still live today, this script is really an UPDATE of an existing
-- placeholder account rather than a brand-new INSERT.
--
-- Because I can't query the live database from here to know for certain
-- which case applies, this script is written to be safe either way: it's
-- an idempotent upsert keyed on the unique `email` column. Run it as many
-- times as you like —
--   - if no row with this email exists yet, it INSERTS a new one.
--   - if a row with this email already exists (e.g. that id=1 placeholder),
--     it UPDATES that row in place (name, phone, password, role) instead
--     of erroring out on the UNIQUE KEY `uk_users_email` constraint or
--     creating a duplicate.
--
-- Password: the plain-text password is "Jesus@123" (as given). The hash
-- below is bcrypt (PASSWORD_BCRYPT, matching includes/auth.php's
-- createUser()/resetPassword() — password_hash($pw, PASSWORD_BCRYPT)),
-- generated locally and round-trip verified with password_verify() before
-- being included here. Treat "Jesus@123" as a bootstrap password only —
-- the new Hub > My Profile page (hub/profile.php, shipped alongside this
-- migration) lets Janeth change it herself after her first login, the
-- same way the existing "temporary password" emails already ask new staff
-- to do.
--
-- Safe to run against the live database at any time; touches only the one
-- row matching this email (plus its staff_records/user_roles companions).

-- 1. users — create or update the account itself.
INSERT INTO `users`
  (`first_name`, `last_name`, `email`, `phone`, `password_hash`, `user_type`,
   `is_active`, `email_verified_at`, `location_id`)
VALUES
  ('Janeth', 'Joseph', 'janet.joseph@think-tinker.com', '+2348149806770',
   '$2y$12$KYahVoGifyAuhVseKEYWouy80GBbrm0EK7wGqvUTvDmTxtzQ/2VPW', 'super_admin',
   1, NOW(), 1)
ON DUPLICATE KEY UPDATE
  `first_name`         = VALUES(`first_name`),
  `last_name`          = VALUES(`last_name`),
  `phone`              = VALUES(`phone`),
  `password_hash`      = VALUES(`password_hash`),
  `user_type`          = VALUES(`user_type`),
  `is_active`           = 1,
  `email_verified_at`  = COALESCE(`email_verified_at`, NOW()),
  `reset_token`        = NULL,
  `reset_token_expires`= NULL;

-- Capture her user id (works whether the row above was just inserted or
-- already existed) for the two companion inserts below.
SET @janeth_id = (SELECT `id` FROM `users` WHERE `email` = 'janet.joseph@think-tinker.com');

-- 2. staff_records — she's an employee (CEO), so she should show up in
-- Hub > Staff like any other team member. staff_records has no unique
-- constraint on user_id alone (only on id and staff_id_number), so a
-- plain ON DUPLICATE KEY UPDATE can't key off user_id — this INSERT...
-- WHERE NOT EXISTS guards the same idempotency instead: only creates a
-- record if she doesn't already have one, never duplicates it on rerun.
-- Job title/employment type/hire date are reasonable CEO defaults — all
-- editable afterward from Hub > Staff (admin) or Hub > My Profile (self).
INSERT INTO `staff_records`
  (`user_id`, `staff_id_number`, `job_title`, `employment_type`, `hire_date`, `location_id`)
SELECT
  @janeth_id,
  CONCAT(DATE_FORMAT(CURDATE(), '%Y%m'), LPAD(@janeth_id, 3, '0')),
  'Chief Executive Officer',
  'full_time',
  CURDATE(),
  1
WHERE NOT EXISTS (SELECT 1 FROM `staff_records` WHERE `user_id` = @janeth_id);

-- 3. user_roles — mirrors what createUser() does for every new super_admin
-- (role_id 1 = 'super_admin' in the roles table). Not required for her to
-- have full access (user_type='super_admin' alone bypasses every check in
-- includes/rbac.php), but kept for consistency with how every other
-- account in this system is set up. INSERT IGNORE is safe here because
-- user_roles' primary key is the (user_id, role_id) pair itself.
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`) VALUES (@janeth_id, 1);
