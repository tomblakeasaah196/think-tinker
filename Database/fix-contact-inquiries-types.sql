-- fix-contact-inquiries-types.sql
--
-- Problem: contact_inquiries.inquiry_type is a strict ENUM allowing only
-- 'general','tutorial','club','consulting','school_proposal'. Two live forms
-- submit values that AREN'T in that list:
--   - teacher/apply.php (tutor applications) submits 'tutor_application'
--   - school/inquiry.php (school partnership requests) submitted
--     'school_partnership' (now fixed in code to use the existing
--     'school_proposal' value instead — no migration needed for that one)
--
-- An INSERT with an invalid ENUM value either fails outright (strict SQL
-- mode, the modern MySQL/MariaDB default) or silently stores '' (non-strict
-- mode) — either way, every tutor application submitted through the public
-- "Apply as Tutor" page has been failing or getting miscategorised, with no
-- visible error to the applicant.
--
-- Fix: add 'tutor_application' as its own value, distinct from 'tutorial'
-- (a parent asking about getting tutoring for their child) — these are two
-- different audiences (prospective staff vs. prospective customers) and
-- staff will want to filter/triage them separately once a dedicated
-- inquiries view exists in the Hub.
--
-- This is purely additive — it only widens the allowed values, so it is
-- safe to run against the live database with existing rows in place.
-- Run this once, before deploying the updated PHP files.

ALTER TABLE `contact_inquiries`
  MODIFY COLUMN `inquiry_type` ENUM('general','tutorial','club','consulting','school_proposal','tutor_application')
  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general';
