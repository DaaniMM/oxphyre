-- Mapa 1A: campos estructurados de ubicacion para negocios.
-- Ejecutar manualmente en servidor antes de guardar ciudad, codigo postal, pais o coordenadas.
-- La ubicacion pertenece al negocio; la geocodificacion externa queda para un microbloque posterior.

DELIMITER //

DROP PROCEDURE IF EXISTS oxphyre_add_business_location_column//

CREATE PROCEDURE oxphyre_add_business_location_column(IN p_column_name VARCHAR(64), IN p_column_definition TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'businesses'
      AND COLUMN_NAME = p_column_name
  ) THEN
    SET @sql = CONCAT('ALTER TABLE businesses ADD COLUMN ', p_column_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END//

DELIMITER ;

CALL oxphyre_add_business_location_column('city', 'city VARCHAR(100) NULL AFTER address');
CALL oxphyre_add_business_location_column('postal_code', 'postal_code VARCHAR(20) NULL AFTER city');
CALL oxphyre_add_business_location_column('country', 'country VARCHAR(100) NULL AFTER postal_code');
CALL oxphyre_add_business_location_column('latitude', 'latitude DECIMAL(10,7) NULL AFTER country');
CALL oxphyre_add_business_location_column('longitude', 'longitude DECIMAL(10,7) NULL AFTER latitude');
CALL oxphyre_add_business_location_column('geocoded_at', 'geocoded_at DATETIME NULL AFTER longitude');
CALL oxphyre_add_business_location_column('geocoding_provider', 'geocoding_provider VARCHAR(32) NULL AFTER geocoded_at');

DROP PROCEDURE IF EXISTS oxphyre_add_business_location_column;
