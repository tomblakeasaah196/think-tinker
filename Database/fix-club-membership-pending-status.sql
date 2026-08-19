-- fix-club-membership-pending-status.sql
--
-- Problem: club_memberships.status only ever allowed 'active','expired',
-- 'cancelled'. Both the self-service club registration flow
-- (AuthController::handleRegisterClub) and the hub's own membership
-- creation inserted new rows as 'active' with start_date = today, at the
-- exact same moment an UNPAID invoice ('sent') was created for that same
-- membership. Result: the countdown started immediately, so a parent could
-- register, never pay, and still see "Active Membership · N days remaining"
-- burning down on their dashboard.
--
-- Fix: add a 'pending_payment' status. The code now inserts new
-- self-service registrations as 'pending_payment' and only flips them to
-- 'active' (restamping start_date/end_date to the real payment date) inside
-- FinanceController::markInvoicePaid() once the linked invoice actually
-- clears. See includes/helpers.php (clubPlanEndDate), api/AuthController.php
-- (handleRegisterClub), api/FinanceController.php (markInvoicePaid).
--
-- This is purely additive — it only widens the allowed status values, so it
-- is safe to run against the live database with existing rows in place.
-- Run this once, before deploying the updated PHP files.

ALTER TABLE `club_memberships`
  MODIFY COLUMN `status` ENUM('pending_payment','active','expired','cancelled')
  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_payment';
