-- ============================================================
--  Think & Tinker — settings corrections
--
--  1. `business_name` and `smtp_from_name` are stored as
--     "Think &amp; Tinker Kids Club". The front end escapes on
--     output, so the stored "&amp;" gets re-escaped to
--     "&amp;amp;" and the visitor literally reads
--     "Think &amp; Tinker Kids Club". Store the raw "&".
--
--  2. `business_email` points at think-tinkerS.com (extra "s").
--     The real domain — and every value in .env — is
--     think-tinker.com.
--
--  Scoped to the `settings` table on purpose. Do NOT run a
--  blanket entity replace across blog_posts or products:
--  entities such as &mdash; and &rsquo; are intentional there
--  and a global replace would corrupt them.
-- ============================================================

SET NAMES utf8mb4;

-- 1. Un-escape HTML entities in settings values
UPDATE `settings`
   SET `setting_value` = REPLACE(`setting_value`, '&amp;', '&')
 WHERE `setting_value` LIKE '%&amp;%';

-- 2. Correct the mistyped domain
UPDATE `settings`
   SET `setting_value` = REPLACE(`setting_value`, 'think-tinkers.com', 'think-tinker.com')
 WHERE `setting_value` LIKE '%think-tinkers.com%';

-- Show the result
SELECT `setting_key`, `setting_value`
  FROM `settings`
 WHERE `setting_group` = 'business_info'
 ORDER BY `sort_order`;
