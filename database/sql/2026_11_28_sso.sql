-- HappiMynd SSO for Quizzard + Games Hub (2026-11-28)
-- Run on prod with ~/run_sql.sh database/sql/2026_11_28_sso.sql
-- Matches database/migrations/2026_11_28_000001_create_sso_tables.php

-- ── Main app (happimynd) ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS happimynd.sso_codes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  code_hash CHAR(64) NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  app VARCHAR(20) NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  used_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY sso_codes_code_hash_unique (code_hash),
  KEY sso_codes_expires_at_index (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS happimynd.sso_admin_numbers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  mobile VARCHAR(15) NOT NULL,
  quizzard_role ENUM('none','admin','super_admin') NOT NULL DEFAULT 'none',
  quizzard_org_id VARCHAR(255) NULL DEFAULT NULL,
  hub_admin TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY sso_admin_numbers_mobile_unique (mobile)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO happimynd.sso_admin_numbers (name, mobile, quizzard_role, hub_admin, is_active, created_at, updated_at) VALUES
  ('Mayank Kuthar', '9318458907', 'super_admin', 1, 1, NOW(), NOW()),
  ('Ashish',        '8104247997', 'super_admin', 1, 1, NOW(), NOW());

INSERT INTO happimynd.migrations (migration, batch)
SELECT '2026_11_28_000001_create_sso_tables', MAX(batch) + 1 FROM happimynd.migrations
WHERE NOT EXISTS (SELECT 1 FROM happimynd.migrations WHERE migration = '2026_11_28_000001_create_sso_tables');

-- ── Games Hub (happimynd_hub) ─────────────────────────────────────────
-- Players now come from HappiMynd; email is optional (mobile-only signups).
ALTER TABLE happimynd_hub.users
  ADD COLUMN happimynd_user_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER id,
  ADD COLUMN mobile VARCHAR(20) NULL DEFAULT NULL AFTER happimynd_user_id,
  MODIFY email VARCHAR(255) NULL DEFAULT NULL,
  ADD UNIQUE KEY uq_users_happimynd_user (happimynd_user_id);

-- ── Quizzard (assessment_api) ─────────────────────────────────────────
ALTER TABLE assessment_api.users
  ADD COLUMN happimynd_user_id VARCHAR(32) NULL DEFAULT NULL,
  ADD COLUMN mobile VARCHAR(20) NULL DEFAULT NULL,
  ADD UNIQUE KEY uq_users_happimynd_user (happimynd_user_id);
