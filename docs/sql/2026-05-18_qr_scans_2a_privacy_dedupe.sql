-- QR 2A: analitica basica con privacidad y deduplicacion temporal.
-- Ejecutar manualmente antes de activar el registro de escaneos QR.

ALTER TABLE qr_scans
  ADD COLUMN ip_hash VARCHAR(64) NULL AFTER ip_address,
  ADD KEY idx_qr_scans_dedupe (qr_code_id, ip_hash, scanned_at),
  ADD KEY idx_qr_scans_qr_code_scanned_at (qr_code_id, scanned_at);
