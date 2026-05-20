# AI_SYNC.md - Estado actual de Oxphyre

## Objetivo del archivo
Este archivo sincroniza el estado actual del proyecto entre ChatGPT, Claude Web, Claude Code y Codex.

AI_SYNC.md es la fuente rápida de verdad para:
- decisiones activas,
- ideas en debate,
- opciones descartadas,
- problemas pendientes,
- siguiente tarea recomendada.
- Para prompts de implementación dirigidos a Codex, Claude Code u otra IA, seguir la plantilla recomendada definida en `AGENTS.md`, incluyendo alcance exacto, restricciones, verificación y comentarios por bloques cuando se modifique código con lógica relevante.

Si hay contradicción entre una conversación antigua y este archivo, tiene prioridad este archivo.

---

## Estado actual resumido

Oxphyre es un TFG de 2º DAW: SaaS de tours virtuales inmersivos para pequeños negocios locales.

Stack activo:
- PHP 8.1 puro con patrón MVC y Front Controller.
- MySQL 8.0.
- JS vanilla.
- Three.js en landing y efectos visuales.
- Visor público actual en Three.js vanilla: panorámica principal cilíndrica/adaptativa + Oxphyre Room entendido como experiencia completa de posición.
- Python Flask + MiDaS Small en servidor para mapas de profundidad.
- PHPMailer + Gmail SMTP para emails transaccionales.
- AWS EC2 t3.small, Ubuntu 22.04, Nginx, PHP-FPM, Let's Encrypt.
- Dominio principal: https://oxphyre.com.

Estado implementado:
- Landing completa y desplegada.
- Auth completo: registro, verificación email, login, logout y recuperación de contraseña.
- Dashboard base con navegación, métricas y layout.
- Wizard de creación de negocio.
- Listado y gestión de negocios.
- Creación, edición, publicación y soft delete de tours.
- Creación de posiciones.
- Subida de fotos por posición.
- Pipeline de imágenes Fase 1.2 implementado:
  - `backend/services/ImageProcessingService.php` concentra validación, conversión, warnings, metadata y temporales.
  - JPG/PNG/WebP se convierten a WebP visible.
  - Fotos N/S/E/O se guardan como WebP quality 92.
  - Panorámica `360` se guarda como WebP quality 96.
  - Panorámicas grandes usan libvips CLI y se redimensionan a un máximo de 8192px de ancho manteniendo proporción.
  - Panorámica iPhone 16248x3832 validada en servidor: WebP final aprox. 8192x1932.
  - MiDaS procesa un JPG temporal separado quality 92; el WebP visible no se sobrescribe.
  - Temporales internos se limpian tras procesado.
  - Subida conjunta de 5 imágenes por posición funciona: N/S/E/O + `photo_360`.
  - Imágenes de baja resolución/compresión tipo WhatsApp se detectan y muestran aviso friendly con recomendación secundaria.
- Procesado MiDaS en servidor mediante microservicio Flask.
- CLAHE disponible en el microservicio, pero no aplicado a la imagen visible en Sprint 1.
- Visor público Sprint 1 sin Photo Sphere Viewer: panorámica parcial horizontal con pitch limitado.
- Sprint 1 Oxphyre Room Free/base implementado, con decisión UX posterior: Oxphyre Room pasa a ser la experiencia completa de posición. Panorámica `360` obligatoria para que la posición sea visitable; fotos detalle 1-4 opcionales.
- Soft delete en businesses, tours, positions y photos.
- QR 1 descargable y QR 2A validados en servidor real: `/qr/{token}` redirige a tour publico, `GET` valido registra escaneo pseudonimizado en `qr_scans`, `HEAD` y bots no cuentan, y el contador simple se calcula con `COUNT(*)`.
- Roadmap post-TFG de 3D Gaussian Splatting documentado.
- Mapa 1A validado en servidor: migracion `docs/sql/2026-05-20_business_location_fields.sql` ejecutada. `businesses` tiene `address`, `city`, `postal_code`, `country`, `latitude`, `longitude`, `geocoded_at` y `geocoding_provider`. Crear/editar negocio guarda ubicacion estructurada.
- Mapa 1B validado en servidor: boton "Buscar en el mapa" en edicion de negocio llama server-side a Nominatim/OpenStreetMap con los valores actuales del formulario. Guarda lat/lng + direccion coherente + `geocoding_provider='nominatim'` en BD. No acepta lat/lng desde cliente. CSRF validado sin consumir.
- Mapa 1C validado en servidor: tour publico muestra boton "Donde estamos" solo si el negocio tiene coordenadas. Bottom sheet responsive con backdrop blur, mapa Leaflet/OSM con pin, nombre del negocio, direccion textual y boton "Como llegar" a OSM. Schema.org LocalBusiness JSON-LD en pagina publica del tour. CSP actualizada para Leaflet CDN y tiles OSM. Cubre el requisito de API externa del tribunal TFG.

---

## Decisiones activas

### Stack y arquitectura
- Mantener PHP puro MVC, Front Controller, MySQL y JS vanilla.
- No usar Laravel, Symfony, React, Vue, Angular, Bootstrap ni frameworks no autorizados.
- Mantener controllers delgados: coordinan; la lógica va en modelos o servicios.
- Todos los modelos deben usar prepared statements.

### Seguridad
- Prepared statements en el 100% de queries.
- CSRF en todos los POST.
- Sesiones PHP seguras.
- No guardar tokens ni datos sensibles en localStorage.
- Validar uploads por MIME real, no solo extensión.
- Escapar salida con htmlspecialchars().
- Sanitizar entrada con strip_tags() cuando corresponda.
- Credenciales siempre en .env, nunca en código ni GitHub.

### QR y analitica basica
- QR 2A esta cerrado y validado en servidor real.
- `qr_scans` es la fuente de verdad de analitica QR. Cada fila representa un escaneo contado.
- El contador se calcula con `COUNT(*)` sobre `qr_scans`; no se usa ni actualiza `qr_codes.total_scans`.
- Solo `GET /qr/{token}` valido y no bot registra escaneo. `HEAD /qr/{token}` queda para debug y no cuenta.
- No se guarda IP real, User-Agent completo ni pais: `ip_address`, `user_agent` y `country` quedan en `NULL`.
- Se guarda solo `qr_code_id`, `ip_hash`, `device_type` y `scanned_at`.
- La deduplicacion usa `qr_code_id + ip_hash` durante 30 minutos.
- En produccion, Nginx debe pasar `HTTP_CF_CONNECTING_IP` a PHP. Si se vacia esa cabecera, PHP cae a `REMOTE_ADDR`; detras de Cloudflare puede variar el edge entre requests y romper la deduplicacion por cambio de `ip_hash`.
- Configuracion Nginx validada para QR 2A: `fastcgi_param HTTP_CF_CONNECTING_IP $http_cf_connecting_ip;` y `fastcgi_param HTTP_X_FORWARDED_FOR $http_x_forwarded_for;`.

### Visor público
- El visor público Sprint 1 usa Three.js vanilla para la panorámica principal adaptativa y Oxphyre Room.
- La panorámica principal no debe tratarse como esfera/equirectangular 360 completa: se renderiza como vista cilíndrica parcial, con arrastre horizontal y pitch muy limitado.
- Photo Sphere Viewer v4 queda retirado del visor público Sprint 1 porque deformaba panorámicas parciales de móvil al forzarlas como esfera completa.
- La imagen visible siempre debe ser el WebP final optimizado y fiel a la imagen subida; MiDaS/CLAHE quedan como procesado interno o futuro, no como textura pública en Sprint 1.

### Hotspots de navegacion
- Hotspots 1A esta implementado, pusheado y validado en servidor real. La migracion `docs/sql/2026-05-19_hotspots_navigation_coordinates.sql` se ejecuto correctamente en EC2.
- Hotspots 1B esta validado visualmente en servidor real: `TOUR_DATA` incluye hotspots, el overlay aparece y queda anclado a la panoramica al mover el visor horizontalmente y al cambiar tamano/responsive.
- Hotspots 1B.1 implementado: `texture_x`/`texture_y` como coordenadas principales en render publico. `public/js/tour-viewer.js` usa formula UV directa identica a la geometria del cilindro; `yaw_rad`/`pitch_rad` quedan como legacy.
- Hotspots 1C cerrado y validado en servidor real:
  - Dashboard muestra listado de zonas destino cruzando `data.targets` con `data.arrows`.
  - Cada zona muestra badge "Sin flecha" (gris) o "Enlazada" (ambar) y botones "Anadir/Editar/Eliminar flecha".
  - "Anadir/Editar flecha" abre modal con overlay oscuro, titulo dinamico y panoramica contenida (max-height 60vh).
  - Guardar usa endpoint `create` (add) o `move` (edit). Eliminar usa endpoint `delete` (soft delete).
  - Visor publico muestra la flecha en el punto colocado; hover muestra "Ir a" + nombre; click navega correctamente.
- Hotspots 1D implementado (1D-B/C/D validados en servidor real, pendiente confirmar ciclo con borrado de panoramica):
  - `PositionController::deletePhoto()` y `upload()` llaman a `markNeedsReviewByPosition` cuando `direction='360'`.
  - `updateTextureScoped` resetea `needs_review=0` al recolocar una flecha.
  - Editor muestra badge "Revisar" y boton "Recolocar flecha" cuando `needsReview=true`.
  - Aviso ambar en `upload.php` y badge en cards de `tours/manage.php` para posiciones afectadas.
  - Deuda tecnica P1 de estilos inline cerrada: los avisos y el badge "Flechas por revisar" usan clases reutilizables en `public/css/dashboard.css`.
- Los hotspots de navegacion van sobre la panoramica principal `photos.direction='360'`, nunca sobre fotos detalle.
- El hotspot pertenece logicamente a `position_id` y navega hacia `target_position_id`.
- Las coordenadas principales son `texture_x` y `texture_y`: punto relativo de la panoramica/textura.
- `yaw_rad` y `pitch_rad` quedan como legacy; no son la fuente principal del render publico ni del editor.
- `panorama_photo_id` guarda con que panoramica se coloco el hotspot; si cambia, se debe marcar `needs_review=1`.
- `is_active` permite desactivar sin borrar; `deleted_at` aplica soft delete.
- `backend/models/HotspotModel.php` tiene prepared statements para listar dashboard, listar publicos, crear con texture coords, move, toggle, soft delete y marcar `needs_review`.
- La tabla legacy conserva columnas antiguas (`photo_id`, `position_x`, `position_y`) que quedan como legacy.
- Migraciones ejecutadas en servidor: `hotspots_navigation_coordinates.sql` y `hotspots_texture_coordinates.sql`.
- Indices validados: `idx_hotspots_position`, `idx_hotspots_target_position` e `idx_hotspots_public`.
- Orden vigente: Hotspots 1D implementado y validado parcialmente (pendiente confirmar con borrado de panoramica); Hotspots 1E = pulido UX/mobile/labels/limites.

### Sistema de fotos por posición
Decisión viva:
- Oxphyre Room deja de entenderse como "modo 4 fotos" y pasa a ser la experiencia completa de una posición.
- `photos.direction = '360'` define la panorámica principal obligatoria. Sin panorámica, la posición no debe parecer visitable.
- Las fotos detalle son opcionales, de 1 a 4, para destacar zonas concretas de la panorámica: barra, mesa, escaparate, producto, decoración o rincón especial.
- El usuario no debe estar obligado a subir las 4 fotos detalle. Si hay 0, la posición funciona solo con panorámica. Si hay 1-4, el visor deberá poder mostrar las disponibles.
- UI visible: usar "Foto detalle 1", "Foto detalle 2", "Foto detalle 3", "Foto detalle 4"; no mostrar "Frente/Fondo/Izquierda/Derecha" al usuario.
- Mapeo interno temporal sin migrar BD ni enum: `N = Foto detalle 1`, `S = Foto detalle 2`, `E = Foto detalle 3`, `O = Foto detalle 4`.
- Migrar `N/S/E/O` a `detail_1/detail_2/detail_3/detail_4` queda como posible mejora futura, no ahora.
- El visor público entra siempre en la panorámica principal y solo incluye posiciones con `360`; esto ya ocurre en `TourController::showPublic()` al descartar posiciones sin panorámica.
- El botón "Ver posición" debe aparecer desactivado/no clickable si falta `360`, tanto en la card/listado de posiciones como dentro de la pantalla de gestión/subida.
- Tooltip sugerido: "Sube una panorámica principal para activar esta experiencia Oxphyre Room. Las fotos detalle son opcionales."
- `positions.active_mode` se mantiene como campo heredado/compatibilidad durante la transición, pero ya no debe controlar el nuevo flujo público.

Las panorámicas de smartphone pueden ser parciales, no necesariamente 360° equirectangulares reales. La UI debe explicarlo sin prometer cobertura total cuando no exista.

### Pipeline de imágenes y almacenamiento

Estado implementado:
- `ImageProcessingService.php` es el servicio responsable del pipeline local de imágenes.
- El usuario puede subir JPG, PNG, WebP y HEIC/HEIF; se valida MIME real con `finfo`.
- El formato visible final del visor es WebP optimizado.
- `php8.1-gd` está instalado y validado con soporte JPEG/PNG/WebP.
- `libvips-tools` está instalado y validado: vips 8.12.1, ruta `/usr/bin/vips`, WebP load/save confirmado.
- Límites reales actuales del servidor: `upload_max_filesize=15M`, `post_max_size=20M`, `nginx client_max_body_size=20M`.
- N/S/E/O usan WebP quality 92.
- `direction='360'` usa WebP quality 96.
- HEIC/HEIF se procesan siempre con libvips, no GD.
- Si `getimagesize()` no puede leer dimensiones HEIC/HEIF, se usa `vipsheader`.
- Panorámicas grandes usan libvips CLI si GD no puede procesarlas con seguridad o si superan 8192px de ancho.
- El ancho final máximo de panorámica es 8192px, manteniendo proporción.
- MiDaS recibe un JPG temporal quality 92 separado; CLAHE/MiDaS no sobrescriben la imagen visible.
- Prueba real validada en servidor: panorámica iPhone original 16248x3832 procesada con libvips a WebP final aprox. 8192x1932, ~2.9MB, `processed=1` en BD.
- Subida conjunta de 5 imágenes validada: `photo_360` + N/S/E/O en un solo envío.
- Delete de fotos validado.
- Panorámica WhatsApp 1600x377 detectada como baja calidad/compresión con mensaje friendly.
- Los originales nuevos de usuario se usan solo como temporales de procesamiento en EC2; no quedan como imagen visible final ni se conservan indefinidamente en TFG/MVP.
- Matiz importante: WebP/depth antiguos asociados a fotos con soft delete siguen ocupando almacenamiento hasta implementar limpieza física.
- La UI muestra mensajes friendly para formato no soportado, exceso de tamaño, baja resolución/compresión y error interno.
- Si una imagen parece comprimida, aparece recomendación secundaria: evitar WhatsApp, Instagram u otras apps antes de subir.

Pendiente:
- HEIC/HEIF implementado en código y soportado por servidor vía libvips/libheif. Prueba real desde iPhone validada: la subida funcionó, generó WebP/depth y el visor móvil cargó correctamente, aunque iOS/Safari entregó el archivo como `IMG_8024.jpeg` y no como `.heic` puro. Queda pendiente probar un archivo `.heic` real sin conversión automática.
- Cloudflare R2/CDN Fase 2B implementada y validada en servidor real: nuevas subidas mantienen WebP local en EC2 y, si `R2_ENABLED=true`, duplican el WebP visible final en R2 con metadata en BD. Visor publico y dashboard de subida usan `public_url` cuando existe y fallback local cuando no.
- BD de metadata avanzada pendiente: original_mime, original_width, original_height, final_width, final_height, final_size, processing_status/error_code.
- BD metadata R2 en `photos` ejecutada en servidor: `storage_provider`, `storage_key`, `public_url`.
- Política de limpieza de archivos físicos asociados a fotos con soft delete pendiente.
- Ruido/granulado residual en panorámicas interiores: mejora opcional/no bloqueante. La panorámica original de iPhone ya se ve mucho mejor que la versión comprimida por WhatsApp; el ruido restante probablemente viene de captura en interior/poca luz + ruido real de cámara + visualización fullscreen. No aplicar denoise por defecto todavía porque puede suavizar demasiado o generar efecto acuarela.

### Almacenamiento en Cloudflare R2 — Fase 0 validada

**Estado (2026-05-14):** Fase 0 R2 validada. Sin código de aplicación escrito todavía.

**Cloudflare DNS:**
- oxphyre.com conectado a Cloudflare en plan Free mediante "Connect a domain" (NO transfer). IONOS sigue siendo el registrador del dominio.
- Nameservers en IONOS apuntando a `elliot.ns.cloudflare.com` y `julissa.ns.cloudflare.com`.
- Dominio activo/protegido en Cloudflare. Web https://oxphyre.com carga correctamente.
- DNS importados y revisados: A records hacia EC2 (13.62.93.7), MX/TXT/CNAME de correo en DNS only para no romper IONOS mail.

**R2 buckets:**
- `oxphyre-assets` — ya existía; se mantiene exclusivamente para assets de landing, demo e imágenes estáticas. **No se usa para fotos de tours de usuarios.**
- `oxphyre-tour-media` — **creado**; será el bucket para WebP finales reales de posiciones/tours de usuarios.

**Custom domain:**
- `media.oxphyre.com` configurado en R2 con TLS mínimo 1.2. **Estado: Active.**
- Validado: WebP de prueba subido al bucket y servido correctamente desde `https://media.oxphyre.com/`. Objeto de prueba eliminado tras verificación.
- Métricas tras la prueba: Class A Operations ~20, Class B Operations ~330 — muy por debajo del free tier. Hay que vigilar el usage para mantener coste 0€; no hacer migraciones masivas ni subir depth maps u originales a R2.

**Estrategia de almacenamiento:**
- **EC2** = procesamiento temporal: valida, convierte a WebP, genera depth map, sube a R2 y guarda URL en BD.
- **Cloudflare R2** = almacenamiento final y CDN de WebP visibles. Bandwidth gratuito (sin coste de egress).
- **Depth maps:** quedan en EC2 fuera del scope R2 por ahora.
- **Migración de fotos antiguas:** postergada hasta validar R2 en producción.
- **Limpieza física en EC2:** solo después de confirmar que R2 sirve el archivo correctamente.
- **Fallback local obligatorio:** si R2 falla, el WebP queda en EC2 y el visor lo sirve desde `/uploads/` como ahora.
- **Restricción crítica — coste 0€:** free tier R2: 10 GB almacenamiento, 1M escrituras/mes, 10M lecturas/mes, egress gratuito. No activar Workers, Streams ni servicios de pago mientras no haya ingresos.
- **BD:** migración SQL de metadata R2 ejecutada en servidor. `photos` ya tiene `storage_provider` (enum: 'local'|'r2', default 'local'), `storage_key` y `public_url`.
- `storage_key` es la referencia principal dentro del bucket R2, por ejemplo `tours/3/positions/12/360/360_xxxxx.webp`.
- `public_url` se guarda por comodidad y lectura rápida, pero es regenerable con `R2_PUBLIC_BASE_URL + storage_key` si cambia el dominio CDN.
- La URL pública del tour/visor sigue siendo `oxphyre.com/...`; `media.oxphyre.com/...webp` solo sirve imágenes internas del visor y normalmente no es visible para el visitante salvo en red/devtools.
- Fotos antiguas siguen compatibles: `storage_provider='local'`, `storage_key=NULL`, `public_url=NULL`.

**Servicio R2:** `backend/services/R2StorageService.php` implementado y validado en test aislado real contra Cloudflare R2. Upload, URL pública y delete funcionan.

**Fase 2A implementada y validada:** upload real integrado en `PositionController::upload()` con `resolveStorage()` y `buildR2Key()`. Las nuevas subidas pueden guardar metadata R2 si la copia a R2 funciona, manteniendo siempre WebP local como fallback.

**Fase 2B implementada y validada:** `PhotoUrlResolver::resolve()` es el punto autorizado para resolver la URL visible final de una foto. `TourController::showPublic()` lo usa para construir `TOUR_DATA`; `PositionController::showUpload()` anade `resolved_url`; `upload.php` consume `resolved_url` en previews. Fotos nuevas R2 sirven desde `https://media.oxphyre.com/...`; fotos legacy sin `public_url` siguen por `/uploads/...`.

### R2/CDN Fase 1 validada de forma aislada

Decisión definitiva para Fase 1:
- Usar cURL puro con firma AWS Signature Version 4 manual para Cloudflare R2.
- AWS SDK/Composer queda descartado por ahora: no existe `composer.json`, `composer.lock` ni `vendor/`; `public/index.php` no carga autoloader de Composer; no compensa añadir Composer y un SDK pesado solo para R2.
- Motivo principal: mantener coste 0€, evitar dependencias innecesarias en EC2 t3.small y cubrir solo tres operaciones: `upload()` = PUT firmado, `delete()` = DELETE firmado y `getPublicUrl()` = concatenar `R2_PUBLIC_BASE_URL` + `storage_key`.
- Riesgo: la firma AWS V4 manual puede fallar por canonical headers, body hash, fechas UTC o URL encoding. Mitigación: encapsular la firma en métodos privados, usar `hash_file('sha256', $localPath)` en uploads, limitar keys a formato seguro, hacer test aislado real antes de tocar upload y mantener fallback local obligatorio en Fase 2.
- `R2StorageService` no decide si R2 está habilitado: `R2_ENABLED` lo leerá el caller cuando se integre en Fase 2. Si el servicio se instancia, asume que se quiere usar R2.
- El constructor debe fallar con `RuntimeException` si faltan credenciales críticas: account id, access key id, secret access key, bucket, endpoint/base datos necesarios y public base URL.
- Endpoint firmado: usar virtual-host style `https://{bucket}.{accountId}.r2.cloudflarestorage.com/{key}`. No usar path-style `https://{accountId}.r2.cloudflarestorage.com/{bucket}/{key}`. La firma debe coincidir exactamente con el host real usado en cURL.
- Upload con streaming: usar `CURLOPT_UPLOAD`, `CURLOPT_INFILE` y `CURLOPT_INFILESIZE`; no usar `CURLOPT_POSTFIELDS` para archivos, para no cargar panorámicas grandes en memoria en EC2 t3.small.
- Encoding de keys: codificar por segmento con `implode('/', array_map('rawurlencode', explode('/', $key)))`; no usar `urlencode($key)` porque rompe los `/`.
- Headers mínimos firmados: PUT firma `content-type`, `host`, `x-amz-content-sha256`, `x-amz-date`; DELETE firma `host`, `x-amz-content-sha256`, `x-amz-date`. PUT usa `hash_file('sha256', $localPath)`, DELETE usa SHA256 de string vacío y las fechas van siempre en UTC con `gmdate()`.

Formato previsto de `storage_key`:
`tours/{tourId}/positions/{positionId}/{direction}/{filename}.webp`

Reglas para keys:
- Sin espacios, sin `..` y sin barra inicial `/`.
- Solo letras, números, guion, guion bajo, punto y `/`.
- `direction` limitada a `360`, `N`, `S`, `E`, `O`.
- `validateKey()` debe llamarse al inicio de `upload()`, `getPublicUrl()` y `delete()`.

Estado Fase 1:

1. **No crear Composer ni instalar AWS SDK**: la revisión del proyecto confirmó que no existe `composer.json`, `composer.lock` ni `vendor/`, y `public/index.php` no carga autoloader de Composer. Implementar R2 con cURL puro + AWS Signature V4 manual.

2. **`.env.example`**: documentar las variables R2 definitivas. No tocar `.env` real en el repositorio.
   ```
   R2_ENABLED=false
   R2_ACCOUNT_ID=
   R2_ACCESS_KEY_ID=
   R2_SECRET_ACCESS_KEY=
   R2_BUCKET=oxphyre-tour-media
   R2_ENDPOINT=https://<ACCOUNT_ID>.r2.cloudflarestorage.com
   R2_PUBLIC_BASE_URL=https://media.oxphyre.com
   R2_REGION=auto
   ```
   `R2_ENABLED=false` permite preparar el código sin activar R2 en producción hasta validar. `R2_ENABLED` no lo decide `R2StorageService`; queda para el caller en Fase 2. `R2_PUBLIC_BASE_URL` es la URL base sobre la que se concatena `storage_key` para construir la URL pública.

3. **Migración SQL de metadata en `photos` — ejecutada**: `photos` ya tiene `storage_provider ENUM('local','r2') NOT NULL DEFAULT 'local'`, `storage_key VARCHAR(512) NULL` y `public_url VARCHAR(1024) NULL`. No implica integración R2 en upload/visor/dashboard.

4. **`backend/services/R2StorageService.php` — implementado y validado**: métodos `upload(string $localPath, string $key): bool`, `getPublicUrl(string $key): string` y `delete(string $key): bool`. Lee credenciales desde `$_ENV`, pero no lee ni decide `R2_ENABLED`. Usa cURL puro, endpoint virtual-host style, upload por streaming y firma AWS Signature V4 en métodos privados. El constructor falla con `RuntimeException` si faltan credenciales críticas. Fallo operativo silencioso: si upload/delete falla, devuelve `false` y el caller decide si usar fallback local. No escribe en BD.

5. **Test aislado del servicio — validado en servidor**: `php -l scripts/test_r2_service.php` correcto. `php scripts/test_r2_service.php` cargó `.env`, instanció el servicio, creó WebP temporal en `/tmp`, generó `https://media.oxphyre.com/tests/r2-probe/360/r2-test-probe.webp`, subió a R2, obtuvo HTTP 200, ejecutó `delete()` y confirmó limpieza final. Sin integrar aún en el pipeline.

Política de caché Cloudflare/R2 para Fase 2:
- Cloudflare puede seguir sirviendo un objeto cacheado durante horas aunque ya se haya borrado del bucket R2. En el test real, tras `delete()` la URL siguió devolviendo HTTP 200 con `cf-cache-status=HIT`, `cache-control=max-age=14400`, `age=701`.
- Ese 200 post-delete por caché CDN no es fallo de `R2StorageService.php` si el objeto ya no aparece en el bucket.
- No implementar purga activa de caché en TFG/MVP inicial.
- Regla absoluta: nunca reutilizar `storage_key`.
- Cada upload debe generar una key única e irrepetible. Si una foto se sustituye, se sube como objeto nuevo con nueva key.
- La BD decide qué foto está activa; el visor solo debe usar fotos activas desde BD.
- Objetos huérfanos/antiguos se limpiarán en una fase posterior.

### R2/CDN Fase 2A implementada y validada

Objetivo cumplido: integrar R2 solo para nuevas subidas, manteniendo copia local en EC2 como fallback temporal.

Aclaración de almacenamiento:
- **Local** = archivo físico en EC2: `/public/uploads/{positionId}/...`.
- **BD** = metadata/referencias; no almacena imágenes.
- **R2** = almacenamiento final futuro de WebP visibles.

Estado por fases:
- **Fase 2A:** implementada y validada. Nuevas subidas guardan WebP local como hasta ahora y, si `R2_ENABLED=true`, tambien intentan subir el WebP final visible a R2. Si R2 funciona, la BD guarda `storage_provider='r2'`, `storage_key` y `public_url`.
- **Fase 2B:** implementada y validada. Visor/dashboard usan `public_url` si existe y fallback local si no.
- **Fase 3:** pendiente. Limpieza local/R2 de objetos huerfanos. R2 ya es fuente validada del visor, pero no se borra local hasta definir esta fase.

La copia local + R2 en Fase 2A ya esta validada en flujo real. Es temporal y deliberada: valida R2 sin riesgo de perder imagenes ni romper el visor actual. No contradice la arquitectura final; EC2 seguira siendo procesador/temporal y R2 almacenamiento final, pero la limpieza local queda para Fase 3.

Validacion real de Fase 2A:
- `R2_ENABLED=false`: subida N guardada como local (`storage_provider='local'`, `storage_key=NULL`, `public_url=NULL`).
- `R2_ENABLED=true`: subida S guardada como R2 con `public_url` publica en `https://media.oxphyre.com/...`; `curl -I` devolvio HTTP/2 200, `content-type: image/webp`, `cf-cache-status: MISS`.
- `R2_ENABLED=true` con panoramica `360`: subida guardada como R2 y visitable; `curl -I` devolvio HTTP/2 200, `content-type: image/webp`, `cf-cache-status: MISS`.
- Fallback probado con `R2_SECRET_ACCESS_KEY=INVALIDA_TEST_FALLO`: R2 fallo, la subida E no se rompio y la BD guardo local. `.env` fue restaurado.

Reglas Fase 2A:
- No borrar WebP local todavía.
- No tocar visor/dashboard/TourController todavía.
- No migrar fotos antiguas.
- No subir depth maps ni originales a R2.
- No purgar caché Cloudflare.
- Cada upload debe generar `storage_key` única e irrepetible.
- Nunca reutilizar keys al sustituir fotos.
- Si R2 falla, la subida debe seguir funcionando en local.
- `R2_ENABLED` lo decide el caller, no `R2StorageService`.
- No meter lógica pesada R2 en `PositionController`; usar métodos privados pequeños tipo `resolveStorage()` y `buildR2Key()`.

Reglas Fase 2B:
- Toda URL visible de foto debe resolverse con `PhotoUrlResolver::resolve()`.
- No construir `/uploads/...` inline salvo fallback defensivo en una vista si `resolved_url` no existe.
- `PhotoModel` devuelve datos; no debe contener logica de resolucion de URLs publicas.
- Controllers preparan datos con URLs resueltas; vistas y JS consumen esas URLs.
- CSP debe permitir `https://media.oxphyre.com` en `img-src`.
- R2 necesita CORS configurado para `https://oxphyre.com` y `https://www.oxphyre.com` con `GET`/`HEAD`, porque WebGL/Three.js no puede usar texturas cross-origin sin CORS aunque la imagen responda HTTP 200.
- Si una imagen R2 devuelve 200 pero Three.js muestra negro o el visor cae a estado no disponible, revisar primero CORS y cache Cloudflare.
- No purgar cache Cloudflare en MVP/TFG; mantener keys unicas por upload.
- No borrar local todavia: la copia local sigue siendo fallback temporal hasta Fase 3.

Archivos tocados en Fase 2A:
- `backend/models/PhotoModel.php`
- `backend/controllers/PositionController.php`
- `backend/services/R2StorageService.php` ya estaba implementado y validado; no se modifico en 2A.

Archivos que no deberían tocarse en Fase 2A salvo necesidad justificada:
- `backend/services/ImageProcessingService.php`
- Visor público
- Dashboard
- `backend/controllers/TourController.php`

Siguiente microbloque real de almacenamiento: **Fase 3**, limpieza local/R2 de huerfanos cuando se decida. No borrar local todavia.

No pedir en Fase 1:
- Presigned URLs.
- Reintentos automáticos.
- Integración con upload.
- Cambios en visor/dashboard.

Restricciones de coste para Fase 1:
- No subir originales ni depth maps a R2, solo WebP visibles.
- No migrar fotos antiguas. Solo nuevas subidas cuando se integre en Fase 2.
- No dejar objetos de prueba en el bucket tras los tests.
- No instalar Composer ni AWS SDK en Fase 1.
- No consumir espacio EC2 o BD innecesariamente.
- Vigilar que el free tier de R2 no se supere en las pruebas.

Lo que **no** hace Fase 1:
- No toca `PositionController`, `TourController`, `PhotoModel`, upload.php, visor ni dashboard.
- No integra R2 en el flujo de subida real todavía.
- No modifica las rutas de imágenes que devuelve la BD ni el visor.

### MiDaS
- Servidor t3.small: MiDaS Small con CPU, viable para demo/subida puntual.
- PC local del desarrollador: DPT-Hybrid con RTX 3060 para generar tours demo de alta calidad.
- Nunca depender de procesado en directo en la exposición; los tours pregenerados son el plan A.

### Planes SaaS
- Free, Pro y Business definidos en CLAUDE.md y Planes_Oxphyre.md.
- **FREE (decisión vigente 2026-05-20):** 1 negocio, 1 tour, hasta 3 posiciones por tour. QR básico con branding Oxphyre incluido. Flechas de navegación básicas incluidas. Mapa/ubicación del negocio incluido. Sin embed/iframe. Sin analíticas. Marca de agua visible y agresiva: overlay semitransparente en el visor + badge "Creado con Oxphyre" clicable hacia /precios.
- **PRO:** hasta 5 negocios, tours ilimitados, hasta 20 posiciones por tour. Sin marca de agua. QR profesional. Embed/iframe. Analíticas básicas. Hotspots informativos (pines con texto/precio/CTA). Más personalización. Soporte email.
- **BUSINESS:** negocios y posiciones ilimitados, marca blanca, dominio personalizado, múltiples usuarios, API, analíticas avanzadas, soporte prioritario, features avanzadas como roadmap.
- Agente IA completo queda como roadmap/post-TFG salvo decisión contraria.
- QR disponible en todos los planes: básico (Free, con branding) y profesional (Pro/Business).
- Embed/iframe: solo Pro y Business. Free solo tiene enlace público.
- La diferencia Free→Pro es cantidad (3 vs 20 posiciones) + distribución (QR básico vs profesional, sin embed, watermark visible) + features (analíticas, hotspots informativos, personalización).

### Ubicacion de negocios y mapa publico
- La ubicacion pertenece al negocio, no al tour.
- `businesses.address` sigue siendo el campo principal visible.
- Mapa 1A implementado: migracion `docs/sql/2026-05-20_business_location_fields.sql` ejecutada. `businesses` tiene `address`, `city`, `postal_code`, `country`, `latitude`, `longitude`, `geocoded_at` y `geocoding_provider`. Crear/editar negocio guarda ubicacion estructurada.
- Mapa 1B implementado y validado: endpoint `POST /dashboard/negocios/{slug}/geocode`. Backend llama server-side a Nominatim. No acepta lat/lng desde cliente. Guarda coordenadas + campos de direccion coherente en BD. Boton "Buscar en el mapa" en el formulario de edicion del negocio. JS en `business-location.js`.
- Mapa 1C implementado y validado: `TourController::showPublic()` extrae `$businessLocation` y lo anade a `$tourData.location`. Tour publico muestra boton "Donde estamos" solo si hay coordenadas. Bottom sheet responsive con backdrop blur, mapa Leaflet/OSM con pin, nombre del negocio, direccion textual y enlace "Como llegar". Schema.org `LocalBusiness` JSON-LD en tour publico. CSP actualizada para Leaflet CDN y tiles OSM. Gyroscopio bloqueado mientras sheet esta abierto via `body.location-sheet-open`.
- Leaflet se carga desde CDN jsdelivr (ya en allowlist CSP). No hay copia local de Leaflet.
- La ubicacion no tiene todavia card publica fuera del tour (Mapa 1D queda pendiente si se decide).

### 3D Gaussian Splatting post-TFG
- 3D Gaussian Splatting queda como dirección comercial definitiva post-TFG de Oxphyre.
- No forma parte del core obligatorio del TFG.
- Para el TFG solo se contempla una demo pregenerada si da tiempo.
- OpenSplat se usará como herramienta externa sin modificar, igual que MiDaS.
- Oxphyre mantiene privado su código PHP, backend y dashboard.
- Los vídeos de clientes se procesan en infraestructura controlada por Oxphyre o en GPU bajo demanda.
- El cliente no interactúa con OpenSplat; ve una experiencia de marca tipo "Oxphyre 3D Capture".
- El valor comercial no es solo la herramienta open source, sino el pack completo: captura guiada, procesado automático, hosting, visor, QR, embed, analíticas, soporte y UX para PYMES.

### Soft delete
Soft delete activo en:
- businesses
- tours
- positions
- photos

Nunca usar `DELETE FROM` en esos modelos. Usar:
`UPDATE ... SET deleted_at = NOW() WHERE id = ?`

Todos los SELECT de esos modelos deben filtrar `deleted_at IS NULL`.

---

## Ideas en debate

- Cuánto alcance real incluir antes de la entrega del TFG sin arriesgar estabilidad.
- Si priorizar editor canvas drag & drop o QR descargable como siguiente bloque.
- Cómo representar el minimapa en la versión TFG si no hay tiempo para hacerlo completo.
- Cómo enseñar las limitaciones de panorámicas parciales de móvil sin empeorar la percepción del producto.
- Cuándo implementar modo claro: está pendiente hasta cerrar bien modo oscuro y funcionalidad principal.
- Si n8n entra en el TFG o queda documentado como integración futura.
- Cómo presentar 3D Gaussian Splatting en la memoria/exposición sin confundirlo con el core obligatorio del TFG.
- Existe una propuesta consolidada en `Planes_Oxphyre.md` para redefinir Free/Pro/Business: Free como prueba limitada con 3 posiciones, Pro como plan comercial profesional y Business como premium/Gaussian. Todavía no es decisión definitiva; no aplicar a código ni documentación principal hasta validar el visor Free y confirmar la estrategia comercial.
- `Oxphyre_Room_Free_Flow.md` debe leerse con la decisión UX vigente: Oxphyre Room = experiencia completa de posición, panorámica obligatoria y fotos detalle 1-4 opcionales. Hotspots sobre panorámica siguen pendientes.

---

## Opciones descartadas

- React, Vue, Angular.
- Laravel, Symfony.
- Bootstrap.
- SQL directo sin prepared statements.
- Guardar tokens o datos sensibles en localStorage.
- Validar uploads solo por extensión.
- Visor público Three.js manual anterior como solución principal.
- DPT-Hybrid en servidor t3.small: consume demasiada RAM.
- Depender del procesado MiDaS en directo durante la exposición.
- Luma AI y Polycam para el núcleo del producto: servicios de pago/sin API gratuita adecuada.
- Google Street View app: eliminada de stores.
- Gran angular del smartphone como recomendación principal: sacrifica calidad.
- Cámaras 360° profesionales como requisito para clientes: no encaja con el público objetivo de PYMES con smartphone normal.
- OpenCV stitching como parte central del TFG: requiere solapamiento y falla en paredes lisas.

---

## Problemas pendientes

### Prioridad alta para TFG
- Crear o terminar `/precios` con Free, Pro y Business.
- API externa para tribunal: **implementada y validada**. Nominatim/OpenStreetMap (geocodificacion server-side, Mapa 1B) + Leaflet/OSM (mapa publico en visor, Mapa 1C). Cubre el requisito sin Google Maps ni Mapbox (sin API key, sin cuotas, open source).
- Documentar roles en la memoria: admin, business_owner, viewer.
- Revisar contraste en dashboard y wizard: inputs, labels y textos secundarios.
- Preparar 1-2 tours demo visualmente impecables antes de la exposición.
- Grabar o sustituir el placeholder del video demo en la landing.
- Revisar responsive en móvil/tablet.
- Revisar SEO técnico final: sitemap, robots, schema, metas, Open Graph.
- Revisar PageSpeed final.
- Pipeline de imágenes: JPG/PNG/WebP + HEIC/HEIF implementados en el pipeline WebP/libvips; flujo iPhone normal validado en servidor; queda pendiente prueba con archivo `.heic` puro sin conversión automática.

### Prioridad media
- QR 1 descargable y QR 2A estan validados en servidor real. `/qr/{token}` redirige con 302 a `/tour/{businessSlug}/{tourSlug}?src=qr` por GET y soporta HEAD para debug sin contar escaneo. QR 2A registra solo GET validos no bot en `qr_scans`, guarda `ip_hash` y `device_type`, deja IP/User-Agent/pais en NULL, deduplica 30 minutos y muestra contador simple en `manage.php`. La incidencia de deduplicacion por `REMOTE_ADDR` variable detras de Cloudflare quedo resuelta pasando `HTTP_CF_CONNECTING_IP` desde Nginx a PHP.
- Editor canvas drag & drop.
- Hotspots 1B, 1C y 1D implementados. 1D pendiente de confirmar ciclo completo con borrado de panoramica. Deuda P1 de estilos inline de avisos cerrada en `dashboard.css`.
- Minimap real.
- Tutorial/onboarding del editor.
- Tooltips de ayuda en métricas del dashboard.
- Página 404/500 personalizada si no está completa.
- Legal/RGPD: privacidad, términos, cookies.
- PWA: manifest y service worker.
- UX dashboard: bloquear/desactivar "Ver posición" si falta panorámica `360` en listado/card y pantalla de gestión/subida.
- Limpieza física de archivos asociados a fotos con soft delete.
- Reducir ruido/granulado residual en panorámica si sobra tiempo tras tareas críticas.

### Deuda técnica
- Unificar métodos duplicados de controllers en BaseController.
- `UserModel::create()` tiene rol `business_free` hardcodeado; refactorizar cuando existan más roles reales.
- Gmail SMTP sirve para TFG, pero en producción migrar a Resend, SendGrid o Mailgun.
- Reimplementar o decidir si se descarta el shader MiDaS/parallax sobre el visor Three.js actual.
- Script local Windows para procesado DPT-Hybrid + CUDA con RTX 3060.
- Revisar si queda documentación antigua diciendo que MiDaS Small/swap/microservicio están pendientes, porque ya se implementaron.

---

## Última sesión de trabajo

Ultima sesion de implementacion (2026-05-20):
- Mapa 1A validado en servidor: campos de ubicacion en `businesses`, formularios de crear/editar negocio.
- Mapa 1B validado en servidor: geocodificacion Nominatim server-side, boton "Buscar en el mapa" en edicion de negocio, `business-location.js`, endpoint privado con CSRF/ownership.
- Mapa 1C validado en servidor: mapa Leaflet en tour publico, bottom sheet responsive, Schema.org LocalBusiness JSON-LD, CSP actualizada. Cubre requisito API externa del tribunal.
- Ajustes visuales Mapa 1C: boton centrado, sheet de 860px / 78vh, mapa 320px desktop, nombre del negocio en el sheet.

Sesiones anteriores clave:
- Hotspots 1A, 1B, 1B.1, 1C y 1D validados en servidor real.
- Helper `asset()` con `filemtime()` en `config.php`.
- Pipeline de imagenes Fase 1.2, R2/CDN Fase 2B, QR 1 y QR 2A validados en servidor real.

---

## Próximo paso recomendado

Siguiente orden recomendado para cerrar antes del TFG:

**Requisitos tribunal ya cubiertos:**
- API externa: Nominatim/OpenStreetMap (Mapa 1B) + Leaflet/OSM (Mapa 1C). **Validado.**
- Roles documentados: pendiente documentar en la memoria (admin, business_owner, viewer).

1. **`/precios`**: crear pagina independiente con las 3 cards de planes (Free, Pro, Business). Todos los CTAs de upgrade del dashboard apuntan aqui. Slug correcto para SEO es `/precios` (no `/planes`).
2. **Hotspots 1D**: confirmar ciclo completo con borrado de panoramica (no solo sustitucion). La deuda P1 de estilos inline de avisos esta cerrada en `dashboard.css`.
   **Hotspots 1E**: pulido UX mobile/labels/limites.
3. Preparar 1-2 tours demo visualmente impecables antes de la exposicion.
4. Responsive: verificar todas las secciones en movil y tablet.
5. SEO tecnico final: sitemap, robots, schema, metas, Open Graph.
6. PageSpeed final.
7. Limpieza fisica de soft delete: borrar WebP/depth asociados cuando proceda. Esperar a tener R2 como fuente validada (ya lo es en Fase 2B). No es bloqueante para el TFG.
8. Pulido opcional de ruido/granulado. No bloqueante.

Micro-pendiente (no bloqueante): probar archivo `.heic` puro de iPhone sin conversion automatica de iOS/Safari para confirmar el path HEIC del pipeline.

Mantener `positions.active_mode` como campo heredado/compatibilidad; el flujo publico actual depende de `photos.direction='360'` para la panoramica principal. `N/S/E/O` quedan como mapeo interno temporal de Foto detalle 1-4.

---

## Advertencias para la IA

- Respetar AGENTS.md, CLAUDE.md, DEVLOG.md y este AI_SYNC.md.
- No actuar como si el proyecto empezara de cero.
- No reabrir decisiones cerradas salvo problema claro.
- No cambiar stack ni arquitectura sin justificarlo.
- No hacer refactors grandes sin confirmación.
- No proponer frameworks nuevos.
- No proponer cámaras 360° profesionales como requisito para clientes.
- No implementar ideas en debate como si fueran decisiones tomadas.
- Priorizar terminar el TFG con calidad antes que ampliar demasiado el alcance.
- Mantener PHP puro MVC, JS vanilla, Three.js, MySQL y seguridad fuerte.
