-- QR 1: token permanente para que los QR impresos no dependan de slugs.
-- Ejecutar manualmente antes de usar la descarga/redireccion /qr/{token}.

ALTER TABLE qr_codes
  ADD COLUMN token VARCHAR(12) NULL AFTER tour_id,
  ADD UNIQUE KEY uq_qr_codes_token (token),
  ADD KEY idx_qr_codes_tour_id (tour_id);
