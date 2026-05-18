<?php

class PhotoUrlResolver
{
    // Centraliza la decision entre R2 y almacenamiento local para que
    // controllers y vistas no dupliquen la misma regla en cada preview.
    public static function resolve(array $photo, int $positionId): string
    {
        $publicUrl = trim((string) ($photo['public_url'] ?? ''));
        $storageProvider = (string) ($photo['storage_provider'] ?? 'local');

        // En Fase 2B, R2 se usa solo cuando la BD ya tiene una URL publica
        // valida. No se consulta R2 ni se lee .env: la BD decide que foto sirve.
        if ($storageProvider === 'r2' && $publicUrl !== '') {
            return $publicUrl;
        }

        // Las fotos antiguas y cualquier subida sin metadata R2 siguen usando
        // el WebP local de EC2. Este fallback mantiene compatible todo el
        // historico mientras R2 se valida como fuente principal del visor.
        $resolvedPositionId = isset($photo['position_id']) ? (int) $photo['position_id'] : $positionId;
        $filename = (string) ($photo['filename'] ?? '');

        return '/uploads/' . $resolvedPositionId . '/' . $filename;
    }
}
