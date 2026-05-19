-- Hotspots 1A: contrato de navegacion sobre panoramica principal.
-- Ejecutar manualmente en servidor antes de conectar editor o visor publico.
-- La migracion conserva columnas legacy existentes, como photo_id, si la tabla ya existe.

CREATE TABLE IF NOT EXISTS hotspots (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  position_id INT UNSIGNED NULL,
  target_position_id INT UNSIGNED NULL,
  panorama_photo_id INT UNSIGNED NULL,
  type VARCHAR(30) NOT NULL DEFAULT 'navigation',
  label VARCHAR(80) NULL,
  yaw_rad DECIMAL(10,7) NULL,
  pitch_rad DECIMAL(10,7) NULL,
  needs_review TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY idx_hotspots_position (position_id, deleted_at),
  KEY idx_hotspots_target_position (target_position_id),
  KEY idx_hotspots_public (position_id, type, is_active, needs_review, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER //

DROP PROCEDURE IF EXISTS oxphyre_add_hotspots_column//
DROP PROCEDURE IF EXISTS oxphyre_add_hotspots_index//

CREATE PROCEDURE oxphyre_add_hotspots_column(IN p_column_name VARCHAR(64), IN p_column_definition TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hotspots'
      AND COLUMN_NAME = p_column_name
  ) THEN
    SET @sql = CONCAT('ALTER TABLE hotspots ADD COLUMN ', p_column_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END//

CREATE PROCEDURE oxphyre_add_hotspots_index(IN p_index_name VARCHAR(64), IN p_index_definition TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'hotspots'
      AND INDEX_NAME = p_index_name
  ) THEN
    SET @sql = CONCAT('ALTER TABLE hotspots ADD INDEX ', p_index_name, ' ', p_index_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END//

DELIMITER ;

CALL oxphyre_add_hotspots_column('position_id', 'position_id INT UNSIGNED NULL');
CALL oxphyre_add_hotspots_column('target_position_id', 'target_position_id INT UNSIGNED NULL');
CALL oxphyre_add_hotspots_column('panorama_photo_id', 'panorama_photo_id INT UNSIGNED NULL');
CALL oxphyre_add_hotspots_column('type', 'type VARCHAR(30) NOT NULL DEFAULT ''navigation''');
CALL oxphyre_add_hotspots_column('label', 'label VARCHAR(80) NULL');
CALL oxphyre_add_hotspots_column('yaw_rad', 'yaw_rad DECIMAL(10,7) NULL');
CALL oxphyre_add_hotspots_column('pitch_rad', 'pitch_rad DECIMAL(10,7) NULL');
CALL oxphyre_add_hotspots_column('needs_review', 'needs_review TINYINT(1) NOT NULL DEFAULT 0');
CALL oxphyre_add_hotspots_column('is_active', 'is_active TINYINT(1) NOT NULL DEFAULT 1');
CALL oxphyre_add_hotspots_column('created_at', 'created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP');
CALL oxphyre_add_hotspots_column('updated_at', 'updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
CALL oxphyre_add_hotspots_column('deleted_at', 'deleted_at TIMESTAMP NULL');

CALL oxphyre_add_hotspots_index('idx_hotspots_position', '(position_id, deleted_at)');
CALL oxphyre_add_hotspots_index('idx_hotspots_target_position', '(target_position_id)');
CALL oxphyre_add_hotspots_index('idx_hotspots_public', '(position_id, type, is_active, needs_review, deleted_at)');

DROP PROCEDURE IF EXISTS oxphyre_add_hotspots_column;
DROP PROCEDURE IF EXISTS oxphyre_add_hotspots_index;
