-- Hotspots 1B.1: coordenadas de textura para render público.
-- Añade texture_x y texture_y a hotspots si no existen.
-- Defensiva/idempotente: no modifica yaw_rad ni pitch_rad.
-- Ejecutar manualmente en el servidor antes de validar el visor público.

DELIMITER //

DROP PROCEDURE IF EXISTS oxphyre_add_hotspot_texture_col//

CREATE PROCEDURE oxphyre_add_hotspot_texture_col(
  IN p_column_name VARCHAR(64),
  IN p_after_col   VARCHAR(64)
)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'hotspots'
      AND COLUMN_NAME  = p_column_name
  ) THEN
    SET @sql = CONCAT(
      'ALTER TABLE hotspots ADD COLUMN ',
      p_column_name,
      ' FLOAT NULL AFTER ',
      p_after_col
    );
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END//

DELIMITER ;

CALL oxphyre_add_hotspot_texture_col('texture_x', 'pitch_rad');
CALL oxphyre_add_hotspot_texture_col('texture_y', 'texture_x');

DROP PROCEDURE IF EXISTS oxphyre_add_hotspot_texture_col;
