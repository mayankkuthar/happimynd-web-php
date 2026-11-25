-- =====================================================================
--  Website Enquiries — Schema & Stored Procedures
--  Target : AWS RDS for MySQL 8.0+ / Aurora MySQL 3.x
--  Purpose: Persist and manage enquiries submitted from the website's
--           three forms: General, Pathway (coaching), and Speaking.
--
--  How to run (MySQL CLI / Workbench / DBeaver):
--     mysql -h <rds-endpoint> -u <user> -p <database> < enquiry_db_procedures.sql
--
--  Note: On RDS, creating procedures requires the CREATE ROUTINE privilege.
--        If binary logging is on, set log_bin_trust_function_creators = 1
--        in the DB parameter group (only needed for functions/triggers).
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. Table
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS enquiries (
    enquiry_id          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    enquiry_type        ENUM('GENERAL','PATHWAY','SPEAKING') NOT NULL,

    -- Contact details (common to all forms)
    full_name           VARCHAR(150)     NOT NULL,
    email               VARCHAR(255)     NOT NULL,
    phone               VARCHAR(30)      NULL,
    organization        VARCHAR(200)     NULL,

    -- General enquiry
    interest            VARCHAR(150)     NULL,
    message             TEXT             NULL,

    -- Pathway enquiry
    pathway_id          VARCHAR(100)     NULL,
    current_role_title  VARCHAR(200)     NULL,
    primary_goal        TEXT             NULL,
    preferred_timeline  VARCHAR(50)      NULL,

    -- Speaking enquiry
    engagement_type     VARCHAR(100)     NULL,
    audience            VARCHAR(150)     NULL,
    proposed_date       DATE             NULL,
    event_format        VARCHAR(50)      NULL,
    notes               TEXT             NULL,

    -- Workflow
    status              ENUM('NEW','CONTACTED','IN_PROGRESS','CLOSED','SPAM')
                                         NOT NULL DEFAULT 'NEW',
    admin_remarks       TEXT             NULL,
    created_at          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (enquiry_id),
    KEY idx_enquiries_type_status (enquiry_type, status),
    KEY idx_enquiries_email (email),
    KEY idx_enquiries_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DELIMITER $$

-- ---------------------------------------------------------------------
-- 2. Internal helper: validate common contact fields
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_validate_contact $$
CREATE PROCEDURE sp_validate_contact(
    IN p_full_name VARCHAR(150) CHARACTER SET utf8mb4,
    IN p_email     VARCHAR(255) CHARACTER SET utf8mb4
)
BEGIN
    IF p_full_name IS NULL OR TRIM(p_full_name) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Full name is required';
    END IF;

    IF p_email IS NULL
       OR TRIM(p_email) NOT REGEXP '^[^@[:space:]]+@[^@[:space:]]+\\.[^@[:space:]]+$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'A valid email address is required';
    END IF;
END $$


-- ---------------------------------------------------------------------
-- 3. Create a GENERAL enquiry
--    CALL sp_create_general_enquiry('Individual Coaching & Growth',
--         'Riya Shah','riya@example.com','+91 98765 43210','Acme Ltd',
--         'Looking for leadership coaching', @new_id);
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_create_general_enquiry $$
CREATE PROCEDURE sp_create_general_enquiry(
    IN  p_interest      VARCHAR(150) CHARACTER SET utf8mb4,
    IN  p_full_name     VARCHAR(150) CHARACTER SET utf8mb4,
    IN  p_email         VARCHAR(255) CHARACTER SET utf8mb4,
    IN  p_phone         VARCHAR(30) CHARACTER SET utf8mb4,
    IN  p_organization  VARCHAR(200) CHARACTER SET utf8mb4,
    IN  p_message       TEXT CHARACTER SET utf8mb4,
    OUT p_enquiry_id    BIGINT UNSIGNED
)
BEGIN
    CALL sp_validate_contact(p_full_name, p_email);

    IF p_message IS NULL OR TRIM(p_message) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Message is required';
    END IF;

    INSERT INTO enquiries
        (enquiry_type, full_name, email, phone, organization, interest, message)
    VALUES
        ('GENERAL', TRIM(p_full_name), LOWER(TRIM(p_email)),
         NULLIF(TRIM(p_phone), ''), NULLIF(TRIM(p_organization), ''),
         NULLIF(TRIM(p_interest), ''), TRIM(p_message));

    SET p_enquiry_id = LAST_INSERT_ID();
END $$


-- ---------------------------------------------------------------------
-- 4. Create a PATHWAY enquiry
--    CALL sp_create_pathway_enquiry('working-professionals','Riya Shah',
--         'riya@example.com','+91 98765 43210','Senior Product Lead',
--         'Acme Ltd','Move into a director role','Within 2 weeks', @new_id);
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_create_pathway_enquiry $$
CREATE PROCEDURE sp_create_pathway_enquiry(
    IN  p_pathway_id          VARCHAR(100) CHARACTER SET utf8mb4,
    IN  p_full_name           VARCHAR(150) CHARACTER SET utf8mb4,
    IN  p_email               VARCHAR(255) CHARACTER SET utf8mb4,
    IN  p_phone               VARCHAR(30) CHARACTER SET utf8mb4,
    IN  p_current_role        VARCHAR(200) CHARACTER SET utf8mb4,
    IN  p_organization        VARCHAR(200) CHARACTER SET utf8mb4,
    IN  p_primary_goal        TEXT CHARACTER SET utf8mb4,
    IN  p_preferred_timeline  VARCHAR(50) CHARACTER SET utf8mb4,
    OUT p_enquiry_id          BIGINT UNSIGNED
)
BEGIN
    CALL sp_validate_contact(p_full_name, p_email);

    IF p_pathway_id IS NULL OR TRIM(p_pathway_id) = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Pathway is required';
    END IF;

    INSERT INTO enquiries
        (enquiry_type, full_name, email, phone, organization,
         pathway_id, current_role_title, primary_goal, preferred_timeline)
    VALUES
        ('PATHWAY', TRIM(p_full_name), LOWER(TRIM(p_email)),
         NULLIF(TRIM(p_phone), ''), NULLIF(TRIM(p_organization), ''),
         TRIM(p_pathway_id), NULLIF(TRIM(p_current_role), ''),
         NULLIF(TRIM(p_primary_goal), ''), NULLIF(TRIM(p_preferred_timeline), ''));

    SET p_enquiry_id = LAST_INSERT_ID();
END $$


-- ---------------------------------------------------------------------
-- 5. Create a SPEAKING enquiry
--    CALL sp_create_speaking_enquiry('Leadership talk',
--         'Corporate leaders and teams','2026-12-15','In person',
--         'Annual offsite, ~200 attendees','Riya Shah','riya@example.com',
--         '+91 98765 43210','Acme Ltd', @new_id);
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_create_speaking_enquiry $$
CREATE PROCEDURE sp_create_speaking_enquiry(
    IN  p_engagement_type  VARCHAR(100) CHARACTER SET utf8mb4,
    IN  p_audience         VARCHAR(150) CHARACTER SET utf8mb4,
    IN  p_proposed_date    DATE,
    IN  p_event_format     VARCHAR(50) CHARACTER SET utf8mb4,
    IN  p_notes            TEXT CHARACTER SET utf8mb4,
    IN  p_full_name        VARCHAR(150) CHARACTER SET utf8mb4,
    IN  p_email            VARCHAR(255) CHARACTER SET utf8mb4,
    IN  p_phone            VARCHAR(30) CHARACTER SET utf8mb4,
    IN  p_organization     VARCHAR(200) CHARACTER SET utf8mb4,
    OUT p_enquiry_id       BIGINT UNSIGNED
)
BEGIN
    CALL sp_validate_contact(p_full_name, p_email);

    IF p_proposed_date IS NOT NULL AND p_proposed_date < CURRENT_DATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Proposed date cannot be in the past';
    END IF;

    INSERT INTO enquiries
        (enquiry_type, full_name, email, phone, organization,
         engagement_type, audience, proposed_date, event_format, notes)
    VALUES
        ('SPEAKING', TRIM(p_full_name), LOWER(TRIM(p_email)),
         NULLIF(TRIM(p_phone), ''), NULLIF(TRIM(p_organization), ''),
         NULLIF(TRIM(p_engagement_type), ''), NULLIF(TRIM(p_audience), ''),
         p_proposed_date, NULLIF(TRIM(p_event_format), ''),
         NULLIF(TRIM(p_notes), ''));

    SET p_enquiry_id = LAST_INSERT_ID();
END $$


-- ---------------------------------------------------------------------
-- 6. List enquiries with optional filters + pagination
--    Pass NULL for any filter to ignore it.
--    CALL sp_get_enquiries('SPEAKING', 'NEW', '2026-11-01', '2026-11-30', 1, 20);
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_get_enquiries $$
CREATE PROCEDURE sp_get_enquiries(
    IN p_enquiry_type  VARCHAR(20) CHARACTER SET utf8mb4,
    IN p_status        VARCHAR(20) CHARACTER SET utf8mb4,
    IN p_from_date     DATE,
    IN p_to_date       DATE,
    IN p_page          INT,
    IN p_page_size     INT
)
BEGIN
    DECLARE v_page      INT DEFAULT GREATEST(IFNULL(p_page, 1), 1);
    DECLARE v_page_size INT DEFAULT LEAST(GREATEST(IFNULL(p_page_size, 20), 1), 200);
    DECLARE v_offset    INT;
    SET v_offset = (v_page - 1) * v_page_size;

    -- Result set 1: rows for the requested page
    SELECT *
      FROM enquiries
     WHERE (p_enquiry_type IS NULL OR enquiry_type = p_enquiry_type)
       AND (p_status       IS NULL OR status       = p_status)
       AND (p_from_date    IS NULL OR created_at  >= p_from_date)
       AND (p_to_date      IS NULL OR created_at  <  p_to_date + INTERVAL 1 DAY)
     ORDER BY created_at DESC, enquiry_id DESC
     LIMIT v_page_size OFFSET v_offset;

    -- Result set 2: total count for pagination
    SELECT COUNT(*) AS total_records
      FROM enquiries
     WHERE (p_enquiry_type IS NULL OR enquiry_type = p_enquiry_type)
       AND (p_status       IS NULL OR status       = p_status)
       AND (p_from_date    IS NULL OR created_at  >= p_from_date)
       AND (p_to_date      IS NULL OR created_at  <  p_to_date + INTERVAL 1 DAY);
END $$


-- ---------------------------------------------------------------------
-- 7. Get a single enquiry by ID
--    CALL sp_get_enquiry_by_id(1);
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_get_enquiry_by_id $$
CREATE PROCEDURE sp_get_enquiry_by_id(IN p_enquiry_id BIGINT UNSIGNED)
BEGIN
    SELECT * FROM enquiries WHERE enquiry_id = p_enquiry_id;
END $$


-- ---------------------------------------------------------------------
-- 8. Update enquiry status / remarks
--    CALL sp_update_enquiry_status(1, 'CONTACTED', 'Called on 4 Nov');
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_update_enquiry_status $$
CREATE PROCEDURE sp_update_enquiry_status(
    IN p_enquiry_id     BIGINT UNSIGNED,
    IN p_status         VARCHAR(20) CHARACTER SET utf8mb4,
    IN p_admin_remarks  TEXT CHARACTER SET utf8mb4
)
BEGIN
    IF p_status IS NULL OR p_status NOT IN ('NEW','CONTACTED','IN_PROGRESS','CLOSED','SPAM') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid status value';
    END IF;

    UPDATE enquiries
       SET status        = p_status,
           admin_remarks = COALESCE(p_admin_remarks, admin_remarks)
     WHERE enquiry_id = p_enquiry_id;

    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Enquiry not found';
    END IF;
END $$


-- ---------------------------------------------------------------------
-- 9. Dashboard summary: counts by type and status
--    CALL sp_enquiry_summary('2026-11-01', '2026-11-30');
-- ---------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_enquiry_summary $$
CREATE PROCEDURE sp_enquiry_summary(
    IN p_from_date DATE,
    IN p_to_date   DATE
)
BEGIN
    SELECT enquiry_type,
           COUNT(*)                         AS total,
           SUM(status = 'NEW')              AS new_count,
           SUM(status = 'CONTACTED')        AS contacted_count,
           SUM(status = 'IN_PROGRESS')      AS in_progress_count,
           SUM(status = 'CLOSED')           AS closed_count,
           SUM(status = 'SPAM')             AS spam_count
      FROM enquiries
     WHERE (p_from_date IS NULL OR created_at >= p_from_date)
       AND (p_to_date   IS NULL OR created_at <  p_to_date + INTERVAL 1 DAY)
     GROUP BY enquiry_type
     ORDER BY enquiry_type;
END $$

DELIMITER ;

-- =====================================================================
-- Quick smoke test (optional — uncomment to run)
-- =====================================================================
-- CALL sp_create_general_enquiry('Individual Coaching & Growth','Test User',
--      'test@example.com','+91 90000 00000','Test Org','Hello', @id);
-- SELECT @id AS new_enquiry_id;
-- CALL sp_get_enquiries(NULL, NULL, NULL, NULL, 1, 10);
-- CALL sp_update_enquiry_status(@id, 'CONTACTED', 'Smoke test');
-- CALL sp_enquiry_summary(NULL, NULL);
