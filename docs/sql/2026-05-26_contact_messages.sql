-- Formulario publico de contacto Oxphyre.
-- Migracion defensiva: crea la tabla si no existe y anade columnas nuevas si la tabla existia con esquema antiguo.

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  business_or_lastname VARCHAR(120) NULL,
  email VARCHAR(160) NOT NULL,
  phone VARCHAR(40) NULL,
  inquiry_type VARCHAR(40) NOT NULL,
  plan_interest VARCHAR(40) NOT NULL,
  message TEXT NOT NULL,
  privacy_accepted TINYINT(1) NOT NULL DEFAULT 0,
  commercial_contact TINYINT(1) NOT NULL DEFAULT 0,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_contact_messages_created_at (created_at),
  KEY idx_contact_messages_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER //

DROP PROCEDURE IF EXISTS oxphyre_add_contact_messages_column//

CREATE PROCEDURE oxphyre_add_contact_messages_column(IN p_column_name VARCHAR(64), IN p_column_definition TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'contact_messages'
      AND COLUMN_NAME = p_column_name
  ) THEN
    SET @sql = CONCAT('ALTER TABLE contact_messages ADD COLUMN ', p_column_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END//

DROP PROCEDURE IF EXISTS oxphyre_add_contact_messages_index//

CREATE PROCEDURE oxphyre_add_contact_messages_index(IN p_index_name VARCHAR(64), IN p_index_definition TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'contact_messages'
      AND INDEX_NAME = p_index_name
  ) THEN
    SET @sql = CONCAT('ALTER TABLE contact_messages ADD INDEX ', p_index_name, ' ', p_index_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END//

DELIMITER ;

CALL oxphyre_add_contact_messages_column('business_or_lastname', 'business_or_lastname VARCHAR(120) NULL AFTER name');
CALL oxphyre_add_contact_messages_column('phone', 'phone VARCHAR(40) NULL AFTER email');
CALL oxphyre_add_contact_messages_column('inquiry_type', 'inquiry_type VARCHAR(40) NOT NULL DEFAULT ''other'' AFTER phone');
CALL oxphyre_add_contact_messages_column('plan_interest', 'plan_interest VARCHAR(40) NOT NULL DEFAULT ''unknown'' AFTER inquiry_type');
CALL oxphyre_add_contact_messages_column('privacy_accepted', 'privacy_accepted TINYINT(1) NOT NULL DEFAULT 0 AFTER message');
CALL oxphyre_add_contact_messages_column('commercial_contact', 'commercial_contact TINYINT(1) NOT NULL DEFAULT 0 AFTER privacy_accepted');
CALL oxphyre_add_contact_messages_column('is_read', 'is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER commercial_contact');
CALL oxphyre_add_contact_messages_column('created_at', 'created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');

CALL oxphyre_add_contact_messages_index('idx_contact_messages_created_at', '(created_at)');
CALL oxphyre_add_contact_messages_index('idx_contact_messages_is_read', '(is_read)');

DROP PROCEDURE IF EXISTS oxphyre_add_contact_messages_column;
DROP PROCEDURE IF EXISTS oxphyre_add_contact_messages_index;
