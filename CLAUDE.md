# CLAUDE.md - Oxphyre Project Context

> Lee también AGENTS.md para instrucciones de comportamiento.
> Lee DEVLOG.md para historial completo de decisiones y avances.

## Fuentes de verdad del proyecto

**Estado actual:** `CLAUDE.md` es el documento amplio de contexto, visión de producto, decisiones técnicas/comerciales y razonamiento. `AI_SYNC.md` es la fuente rápida del estado vivo actual y del próximo paso recomendado. `DEVLOG.md` es el historial completo de lo que se hizo, cuándo, con qué archivos y por qué. `AGENTS.md` contiene las normas de comportamiento, seguridad, estilo y coordinación entre IAs.

**Nota histórica:** Antes, parte del estado vivo y parte del historial quedaban mezclados dentro de `CLAUDE.md`. Eso era útil para no perder contexto, pero podía confundir a una IA futura si una decisión antigua ya había sido sustituida por otra más reciente.

**Decisión vigente:** Si hay contradicción, usar este orden: 1) `DEVLOG.md` para confirmar historial real, 2) `AI_SYNC.md` para estado vivo actual, 3) `CLAUDE.md` como contexto general que debe mantenerse sincronizado, 4) `AGENTS.md` para normas de trabajo.

## Qué es Oxphyre

**Estado actual:** Oxphyre es un SaaS de tours virtuales inmersivos para pequeños negocios locales y, a la vez, el proyecto TFG de 2º DAW. El dueño crea un negocio, crea tours, añade posiciones, sube fotos por posición y publica una experiencia visitable mediante URL pública, QR o embed según el plan. El producto mantiene la visión completa: posiciones conectadas, mapas de profundidad MiDaS, editor canvas drag & drop, QR descargable, embed, hotspots, minimapa, analíticas, planes Free/Pro/Business y evolución comercial real.

El visor público vigente usa **Three.js vanilla**: panorámica principal cilíndrica/adaptativa y Oxphyre Room como experiencia completa de posición. Three.js también sigue formando parte de la landing y efectos visuales. Photo Sphere Viewer v4 quedó retirado del visor público Sprint 1 porque deformaba panorámicas parciales de móvil al tratarlas como esferas completas.

**Nota histórica:** El planteamiento inicial era un visor Three.js propio: esfera navegable, shader de profundidad, giroscopio, hotspots, minimapa y cambio de texturas implementados a mano. Ese enfoque permitió validar el concepto y construir una primera versión inmersiva, pero al probar fotos reales de smartphone aparecieron problemas críticos: FOV/zoom incorrecto, distorsión en panorámicas, depth map visible como textura, touch y giroscopio frágiles.

**Decisión vigente:** Mantener el visor público actual en Three.js vanilla sin reintroducir Photo Sphere Viewer salvo problema claro, justificado y aprobado. El producto sigue orientado a PYMES con smartphone normal: no exigir cámara 360° profesional ni hardware especial.

#### -- 08/05/2026 -- Decisión crítica sobre el visor y upload de fotos
### Sistema de subida de fotos por posición

**Estado actual:** Cada posición se entiende como una experiencia Oxphyre Room completa: panorámica principal obligatoria + fotos detalle opcionales.

**Panorámica principal**:
- Se guarda con `photos.direction='360'`.
- Es la vista de entrada de cada posición pública.
- Puede ser panorámica parcial de smartphone, no necesariamente equirectangular 360°x180° real.
- El visor la renderiza como cilindro parcial/adaptativo y la UI no promete cobertura total cuando la imagen no la tenga.

**Oxphyre Room**:
- Deja de entenderse como "modo 4 fotos"; es la experiencia completa de una posición.
- La panorámica principal `photos.direction='360'` es obligatoria para que la posición sea visitable.
- Las fotos detalle son opcionales, de 1 a 4, y sirven para destacar zonas concretas que no se aprecian bien en la panorámica: barra, mesa, escaparate, producto, decoración o rincón especial.
- UI visible: "Foto detalle 1", "Foto detalle 2", "Foto detalle 3", "Foto detalle 4". No mostrar "Frente/Fondo/Izquierda/Derecha" al usuario.
- Mapeo técnico temporal sin migrar BD ni enum: `N = Foto detalle 1`, `S = Foto detalle 2`, `E = Foto detalle 3`, `O = Foto detalle 4`.
- Si hay 0 fotos detalle, la posición funciona solo con panorámica. Si hay 1-4, el visor deberá poder mostrar las disponibles sin exigir 4.
- Si una posición no tiene panorámica `360`, no debe parecer visitable. El botón "Ver posición" debe aparecer desactivado/no clickable en listado/card y en la pantalla de gestión/subida.
- Tooltip sugerido: "Sube una panorámica principal para activar esta experiencia Oxphyre Room. Las fotos detalle son opcionales."

**BD:** `positions.active_mode ENUM('4photos','panoramic') DEFAULT '4photos'` se mantiene como campo heredado/compatibilidad.
**Visor:** ya no debe usar `active_mode` como decisión principal del flujo público nuevo; usa `photos.direction='360'` para la panorámica obligatoria y debe poder mostrar las fotos detalle disponibles. `TourController::showPublic()` ya descarta posiciones sin `360`.

**Nota histórica:** El 08/05/2026 se decidió permitir una panorámica 360° equirectangular como alternativa ideal al modo de 4 fotos. El 09/05/2026, tras probar fotos reales de iPhone/smartphone, se ajustó la decisión: muchos móviles generan panorámicas cilíndricas o parciales (~270°), no equirectangulares completas. Photo Sphere Viewer v4 se eligió porque gestiona mejor panorámicas, touch, giroscopio y FOV que el visor manual.

**Decisión vigente:** Mantener Oxphyre Room como experiencia completa de posición: panorámica principal obligatoria + detalles opcionales 1-4. No exigir cámaras 360°. No recomendar gran angular como solución principal porque sacrifica calidad. No prometer 360° completo si el usuario sube una panorámica parcial. Si una parte antigua habla de “panorámica 360° equirectangular”, debe leerse como el ideal histórico, no como requisito actual. No migrar ahora la BD ni el enum `direction`; `N/S/E/O` quedan como mapeo interno temporal.

## Stack técnico
- **Frontend:** HTML5 + CSS custom con variables globales + JS vanilla + Three.js
- **Visor público:** Three.js vanilla, panorámica principal cilíndrica/adaptativa + Oxphyre Room
- **Uso directo de Three.js:** landing, hero, efectos visuales y visor público
- **Backend:** PHP 8.1 puro, patrón MVC, Front Controller (todo pasa por index.php)
- **BD:** MySQL 8.0 · BD: `oxphyre` · usuario: `oxphyre`@`localhost`
- **Python servidor:** Flask + Pillow + OpenCV/CLAHE + MiDaS Small (Intel, open source, profundidad real con IA gratuita)
- **Python local/demo:** MiDaS DPT-Hybrid + CUDA en PC local con RTX 3060 para generar tours demo de máxima calidad
- **Emails:** PHPMailer + Gmail SMTP
- **Cache-busting de assets:** helper `asset()` con `filemtime()` en `backend/config/config.php`, disponible en todas las vistas sin `require_once` adicional. Evita caché vieja de CSS/JS tras despliegues sin builds ni versiones manuales.
- **Despliegue:** AWS EC2 t3.small · IP: 13.62.93.7 · Dominio: https://oxphyre.com
- **OS servidor:** Ubuntu 22.04 · Nginx · PHP-FPM · Let's Encrypt
- **Repo:** /var/www/oxphyre en servidor · github.com/DaaniMM/oxphyre

**Nota histórica:** El stack empezó con Three.js como visor público propio, migró temporalmente a PSV v4 y volvió a Three.js vanilla para el Sprint 1 al comprobar que PSV deformaba panorámicas parciales de móvil.

**Decisión vigente:** Mantener PHP puro MVC, MySQL, JS vanilla, Three.js y Python Flask. No introducir React, Vue, Angular, Laravel, Symfony, Bootstrap ni frameworks no autorizados.

## Estructura del proyecto
oxphyre/
public/              → frontend servido por Nginx
assets/            → imágenes, iconos, fuentes
css/               → estilos compilados
js/                → scripts vanilla + Three.js + visor público
uploads/           → fotos procesadas de los negocios
backend/
controllers/       → lógica de negocio
models/            → acceso BD (prepared statements siempre)
views/             → templates PHP
routes/            → mini-router
middleware/        → auth, roles, rate limiting
config/            → BD, constantes, .env loader
services/          → servicios PHP como MiDaSService e ImageProcessingService
python-service/      → Flask + Pillow + OpenCV/CLAHE + MiDaS Small
venv/              → en .gitignore, no se sube
docs/                → memoria TFG
DEVLOG.md            → diario de desarrollo
AGENTS.md            → instrucciones de comportamiento
CLAUDE.md            → este archivo
.env                 → credenciales (en .gitignore, nunca a GitHub)

## Base de datos - tablas principales

**Estado actual:** Tablas principales: `users`, `businesses`, `plans`, `tours`, `positions`, `photos`, `hotspots`, `qr_codes`, `qr_scans`, `contact_messages`, `cookies_consent`, `login_attempts`.

**Campos y reglas importantes:**
- `positions.active_mode ENUM('4photos','panoramic') DEFAULT '4photos'` queda como compatibilidad heredada.
- `photos.direction='360'` identifica la panorámica de una posición.
- `hotspots` conserva columnas legacy como `photo_id`, `position_x`, `position_y`, `yaw_rad` y `pitch_rad`, pero el nuevo flujo de navegacion usa `position_id` como origen logico, `target_position_id` como destino, `panorama_photo_id` para detectar sustituciones de panoramica y `texture_x`/`texture_y` como coordenadas principales sobre la textura. `photo_id` queda nullable legacy.
- `qr_codes.token` es un token base62 de 12 caracteres para URL permanente `/qr/{token}`. `token` es UNIQUE; `tour_id` NO es UNIQUE para permitir multiples tokens/campanas futuras.
- `qr_scans` es la fuente de verdad de analitica QR 2A: guarda `qr_code_id`, `ip_hash`, `device_type` y `scanned_at`; `ip_address`, `user_agent` y `country` quedan en `NULL` por privacidad.
- `businesses` contiene la ubicacion estructurada del negocio: `address` como direccion principal visible, `city`, `postal_code`, `country` y campos reservados para geocodificacion `latitude`, `longitude`, `geocoded_at`, `geocoding_provider`.
- El contador QR se calcula con `COUNT(*)` sobre `qr_scans`; `qr_codes.total_scans` queda como columna legacy/cache futura y QR 2A no la usa ni la actualiza.
- `login_attempts` existe para rate limiting de login.
- `businesses`, `tours`, `positions`, `photos` y `hotspots` tienen soft delete con `deleted_at`.

**Nota histórica:** La tabla `photos` nació para N/S/E/O. Después se añadió `direction='360'` para permitir panorámica sin cambiar la estructura general. `positions.active_mode` se añadió para permitir que 4 fotos y panorámica coexistan. La decisión vigente mantiene `360` como panorámica obligatoria y reutiliza N/S/E/O como mapeo interno temporal de Foto detalle 1-4.

**Decisión vigente:** Todos los modelos deben usar prepared statements. En tablas con soft delete, no usar `DELETE FROM`; usar `UPDATE ... SET deleted_at = NOW()` y filtrar `deleted_at IS NULL` en todos los SELECT.

### Ubicacion de negocios y mapa publico

**Estado actual:** Mapa 1A/1B/1C implementados y validados en servidor real.

- Mapa 1A: migracion `docs/sql/2026-05-20_business_location_fields.sql` ejecutada. `businesses` tiene `address`, `city`, `postal_code`, `country`, `latitude`, `longitude`, `geocoded_at` y `geocoding_provider`. Crear/editar negocio guarda ubicacion estructurada completa.
- Mapa 1B: endpoint `POST /dashboard/negocios/{slug}/geocode`. Backend llama server-side a Nominatim/OpenStreetMap. Guarda lat/lng + direccion coherente en BD. No acepta lat/lng desde cliente. CSRF validado sin consumir. Boton "Buscar en el mapa" en formulario de edicion del negocio (`business-location.js`).
- Mapa 1C: tour publico muestra boton "Donde estamos" solo si `latitude`/`longitude` no son NULL. Bottom sheet responsive con backdrop blur, nombre del negocio, direccion textual, mapa Leaflet/OSM con pin y enlace "Como llegar" a OpenStreetMap. Schema.org `LocalBusiness` JSON-LD en `<head>` del tour publico. Leaflet desde CDN jsdelivr. CSP actualizada con `*.tile.openstreetmap.org` y `cdn.jsdelivr.net` en `img-src`.

**Decision vigente:** La ubicacion pertenece al negocio, no al tour. `address` sigue siendo el campo principal visible. No hay todavia card publica de ubicacion fuera del tour (Mapa 1D queda pendiente si se decide). No usar Google Maps ni Mapbox para esta funcionalidad: Nominatim/OSM/Leaflet cubre el requisito sin API key ni cuotas. Leaflet se carga desde CDN jsdelivr (ya en allowlist CSP); no se descarga localmente. El token CSRF del geocoding se valida sin consumir porque el formulario de edicion normal tambien lo necesita en la misma sesion.

## Rutas importantes del servidor
- Proyecto: `/var/www/oxphyre`
- Nginx config: `/etc/nginx/sites-available/oxphyre`
- Logs Nginx: `/var/log/nginx/`
- PHP config: `/etc/php/8.1/fpm/`
- Certbot: `/etc/letsencrypt/live/oxphyre.com/`
- Python venv: `/var/www/oxphyre/python-service/venv`

### QR y Cloudflare/Nginx

**Estado actual:** QR 2A esta validado en servidor real. Solo `GET /qr/{token}` valido y no bot registra escaneo; `HEAD /qr/{token}` queda para debug y no cuenta. La deduplicacion se hace por `qr_code_id + ip_hash` durante 30 minutos.

**Decision vigente:** En produccion, Nginx debe pasar `HTTP_CF_CONNECTING_IP` a PHP. Si se vacia esa cabecera, `QrController::getClientIp()` cae a `REMOTE_ADDR`; detras de Cloudflare, `REMOTE_ADDR` puede ser el edge de Cloudflare y variar entre peticiones, generando hashes distintos para el mismo visitante. Configuracion validada:

```nginx
fastcgi_param HTTP_X_FORWARDED_FOR $http_x_forwarded_for;
fastcgi_param HTTP_CF_CONNECTING_IP $http_cf_connecting_ip;
```

## Estado implementado resumido

**Estado actual:** Según `DEVLOG.md` y `AI_SYNC.md`, están implementados: landing completa y desplegada, pagina publica `/precios` validada en produccion (cards Free/Pro/Business, Pro destacado, toggle mensual/anual, tabla comparativa, FAQ de planes y CTA final), SEO tecnico inicial validado (sitemap minimo en `public/sitemap.xml`, `https://oxphyre.com/sitemap.xml` con HTTP/2 200 y `content-type: text/xml`, home indexada en Search Console, FAQ valida, sitemap enviado y pendiente de procesamiento/reintento por Google), auth completo, dashboard base, wizard de negocio, gestión de negocios/tours/posiciones, subida de fotos por posición, pipeline WebP/libvips en `ImageProcessingService`, procesado MiDaS mediante Flask, visor público Three.js con panorámica principal + Oxphyre Room, QR 1 descargable y QR 2A con tracking pseudonimizado validados en servidor real, Hotspots 1A BD/modelo validado en servidor real, Hotspots 1B render público validado visualmente en servidor real, Hotspots 1C editor dashboard validado en servidor real (listado de zonas con estados "Sin flecha"/"Enlazada", modal para colocar/editar/eliminar flechas, visor público muestra flecha en el punto colocado con hover "Ir a {posición}"), Hotspots 1D automatización `needs_review` y avisos de revisión en dashboard validados en servidor real, helper `asset()` con `filemtime()` en `backend/config/config.php` para cache-busting automático de CSS/JS sin versiones manuales, mensajes friendly de calidad y soft delete en `businesses`, `tours`, `positions`, `photos` y `hotspots`, **Mapa 1A/1B/1C validados en servidor real**: geocodificación server-side Nominatim, botón "Dónde estamos" en tour público con mapa Leaflet/OpenStreetMap, Schema.org LocalBusiness JSON-LD — cubre el requisito de API externa del tribunal TFG.

**Nota histórica:** Estas piezas se construyeron incrementalmente entre abril y mayo de 2026. El detalle de fechas, archivos tocados, bugs y motivos está en `DEVLOG.md`; `CLAUDE.md` no debe duplicar todo el historial, pero sí conservar el contexto suficiente para que una IA no actúe como si el proyecto empezara de cero.

**Decisión vigente:** Antes de implementar algo, comprobar si ya existe. Si se necesita el estado vivo y la prioridad inmediata, consultar `AI_SYNC.md`.

### Pipeline de imágenes actual

**Estado actual:** `backend/services/ImageProcessingService.php` concentra el pipeline local de imágenes. Valida errores de upload, tamaño por dirección, MIME real, dimensiones, protección de memoria, conversión, warnings de calidad, metadata y temporales.

- JPG/PNG/WebP y HEIC/HEIF se convierten a WebP visible.
- N/S/E/O se guardan como WebP quality 92.
- Panorámica `360` se guarda como WebP quality 96.
- Panorámicas grandes usan libvips CLI y se limitan a 8192px de ancho manteniendo proporción.
- HEIC/HEIF se procesa siempre con libvips; si `getimagesize()` no lee dimensiones, se usa `vipsheader`.
- MiDaS procesa un JPG temporal separado quality 92.
- El WebP visible no se sobrescribe con MiDaS ni CLAHE.
- El flujo TFG actual genera depth maps cuando MiDaS responde, pero la imagen pública visible sigue siendo el WebP optimizado.
- La definición comercial de planes podrá limitar créditos MiDaS en el futuro; esa política de producto no cambia el pipeline actual.
- HEIC/HEIF implementado en pipeline y soportado por servidor vía libvips/libheif.
- Flujo iPhone normal validado: la subida funcionó, generó WebP/depth y el visor móvil cargó correctamente. En esa prueba iOS/Safari entregó el archivo como JPEG, no como `.heic` puro.
- Queda pendiente probar un archivo `.heic` puro sin conversión automática.
- Cloudflare R2/CDN Fase 2B queda implementada y validada en servidor real. Las nuevas subidas mantienen WebP local en EC2 y, si `R2_ENABLED=true`, duplican el WebP visible final en R2 con metadata en BD. Visor publico y dashboard de subida usan `public_url` si existe mediante `PhotoUrlResolver`, con fallback local si no.

**Decisión vigente:** No volver a meter lógica pesada de imagen en `PositionController`. El controlador coordina CSRF, ownership, llamada al servicio, MiDaS, `PhotoModel` y flashes; el servicio procesa imágenes y no escribe en BD.

### Arquitectura de almacenamiento — Cloudflare R2 Fase 0 validada

**Estado (2026-05-14):** Fase 0 R2 validada. Sin código de aplicación escrito todavía.

**DNS Cloudflare:**
- oxphyre.com gestionado por Cloudflare en plan Free. IONOS sigue siendo el registrador; solo los nameservers apuntan a Cloudflare (`elliot.ns.cloudflare.com`, `julissa.ns.cloudflare.com`).
- Records de correo (MX, SPF, DKIM, DMARC) en DNS only para no romper el mail de IONOS.

**Roles:**
- **EC2** = procesamiento temporal. Recibe el upload, valida, convierte a WebP y genera depth map. Sube el WebP final a R2 y guarda la URL en BD.
- **Cloudflare R2** = almacenamiento final y CDN para WebP visibles de posiciones/tours de usuarios. Bandwidth gratuito (sin coste de egress).

**Buckets:**
- `oxphyre-assets` — ya existe; solo para assets de landing, demo e imágenes estáticas. **No se usa para fotos reales de tours de usuarios.**
- `oxphyre-tour-media` — **creado**; para WebP finales de posiciones de usuarios. Custom domain `media.oxphyre.com` configurado con TLS 1.2; estado al 2026-05-14: **Active**. Prueba WebP pública validada: objeto subido y servido correctamente desde `https://media.oxphyre.com/`; objeto de prueba eliminado tras verificación.

**Restricción crítica:** mantener coste 0€ mientras no haya ingresos. Free tier R2: 10 GB almacenamiento, 1M escrituras/mes, 10M lecturas/mes, egress gratuito. No activar Workers, Streams ni otros servicios de pago de Cloudflare hasta tener ingresos reales.

**Scope inicial:**
- Solo WebP visibles de posiciones. Depth maps quedan en EC2 por ahora.
- Fallback local obligatorio: si R2 falla, el WebP queda en EC2 y el visor lo sirve desde `/uploads/` como ahora.
- Migración de fotos antiguas: postergada hasta validar R2 en producción.
- Limpieza física en EC2: solo después de confirmar que R2 tiene y sirve el archivo correctamente.

**BD:** migración SQL de metadata R2 ejecutada en servidor. La tabla `photos` ya tiene `storage_provider` ('local'|'r2', default 'local'), `storage_key` y `public_url`.
`storage_key` es la referencia principal dentro del bucket R2; `public_url` es una comodidad regenerable desde `R2_PUBLIC_BASE_URL + storage_key` si cambia el dominio CDN. Las fotos antiguas siguen compatibles como `local` con `storage_key` y `public_url` en `NULL`.

**Fase 1 validada de forma aislada:**
- Los controllers no deben contener lógica R2. `R2StorageService.php` centraliza upload/getUrl/delete.
- Decisión arquitectónica: no introducir Composer ni AWS SDK para R2. El proyecto no tiene `composer.json`, `composer.lock` ni `vendor/`, `public/index.php` no carga autoloader de Composer y añadir un SDK pesado no compensa para tres operaciones.
- `R2StorageService.php` usa cURL puro: `upload()` hace PUT firmado, `delete()` hace DELETE firmado y `getPublicUrl()` concatena `R2_PUBLIC_BASE_URL` + `storage_key`.
- `R2StorageService.php` no decide si R2 está habilitado. `R2_ENABLED` queda para el caller en Fase 2; si el servicio se instancia, asume que se quiere usar R2.
- El constructor debe lanzar `RuntimeException` si faltan credenciales críticas o configuración necesaria.
- Endpoint firmado: usar virtual-host style `https://{bucket}.{accountId}.r2.cloudflarestorage.com/{key}`. No usar path-style. La firma debe coincidir exactamente con el host usado por cURL.
- Upload con streaming: usar `CURLOPT_UPLOAD`, `CURLOPT_INFILE` y `CURLOPT_INFILESIZE`; no usar `CURLOPT_POSTFIELDS` para archivos, para evitar cargar panorámicas grandes en memoria en EC2 t3.small.
- Encoding de keys: aplicar `rawurlencode()` por segmento (`implode('/', array_map('rawurlencode', explode('/', $key)))`), nunca `urlencode($key)` completo porque rompe los `/`.
- La firma AWS Signature Version 4 quedará encapsulada en métodos privados del servicio. PUT firma como mínimo `content-type`, `host`, `x-amz-content-sha256`, `x-amz-date`; DELETE firma `host`, `x-amz-content-sha256`, `x-amz-date`. PUT usa `hash_file('sha256', $localPath)`, DELETE usa SHA256 de string vacío y fechas UTC con `gmdate()`.
- Las keys serán seguras y controladas: `tours/{tourId}/positions/{positionId}/{direction}/{filename}.webp`, sin espacios, sin `..`, sin barra inicial, solo caracteres seguros y `direction` limitada a `360`, `N`, `S`, `E`, `O`. `validateKey()` debe llamarse al inicio de `upload()`, `getPublicUrl()` y `delete()`.
- El servicio lee credenciales desde `$_ENV`; fallo silencioso (devuelve `false`) para que el caller pueda aplicar fallback local sin romper el flujo.
- Validación real en servidor: `php -l scripts/test_r2_service.php` sin errores; `php scripts/test_r2_service.php` subió WebP temporal a `https://media.oxphyre.com/tests/r2-probe/360/r2-test-probe.webp`, obtuvo HTTP 200, ejecutó `delete()` y confirmó limpieza.
- `PhotoModel` persistirá `storage_provider`, `storage_key` y `public_url` solo cuando se integre en Fase 2. El servicio ya está probado, pero no integrado en pipeline real.
- Criterio de coste: mantener 0€; Composer/AWS SDK queda descartado por ahora; no subir originales ni depth maps a R2; no dejar objetos de prueba en el bucket; vigilar consumo del free tier.
- No incluir en Fase 1: presigned URLs, reintentos automáticos, integración con upload ni cambios en visor/dashboard.

**Política de caché Cloudflare/R2:**
- Cloudflare puede servir objetos cacheados durante unas horas aunque ya se hayan borrado del bucket R2. Un HTTP 200 post-delete con `cf-cache-status=HIT`, `cache-control=max-age=14400` y `age` no es fallo del servicio si el objeto ya no existe en el bucket.
- No se implementará purga activa de caché en el TFG/MVP inicial.
- Regla permanente para Fase 2: nunca reutilizar `storage_key`. Cada upload genera una key única e irrepetible; si una foto se sustituye, se sube como objeto nuevo con nueva key.
- La BD decide qué foto está activa y el visor solo debe consumir fotos activas desde BD. Los objetos huérfanos/antiguos se limpiarán en una fase posterior.

**Fase 2A implementada y validada en servidor real:**
- Local significa archivo físico en EC2: `/public/uploads/{positionId}/...`.
- BD significa metadata/referencias; la BD no almacena imágenes.
- R2 será el almacenamiento final futuro de WebP visibles.
- En Fase 2A, las nuevas subidas guardan el WebP local como hasta ahora y, si `R2_ENABLED=true`, tambien intentan subir el WebP final visible a R2.
- Si R2 funciona, la BD guarda metadata R2 (`storage_provider='r2'`, `storage_key`, `public_url`). Si R2 falla, la subida sigue funcionando en local.
- El visor sigue usando local durante Fase 2A. Este almacenamiento doble es temporal y deliberado para validar R2 en flujo real sin perder imagenes ni romper el visor actual.
- Esto no contradice la arquitectura final: EC2 será procesador/temporal y R2 almacenamiento final, pero la limpieza local queda para Fase 3.

**Validacion real Fase 2A:**
- `R2_ENABLED=false`: subida N validada como flujo legacy/local. BD id 56, `direction=N`, `storage_provider='local'`, `storage_key=NULL`, `public_url=NULL`.
- `R2_ENABLED=true`: subida S validada con R2. BD id 57, `storage_provider='r2'`, `storage_key=tours/1/positions/2/S/S_961208678db1224b.webp`, `public_url=https://media.oxphyre.com/tours/1/positions/2/S/S_961208678db1224b.webp`. `curl -I` devolvio HTTP/2 200, `content-type: image/webp`, `cf-cache-status: MISS`.
- Panoramica `360` con `R2_ENABLED=true`: BD id 58, `storage_provider='r2'`, `storage_key=tours/1/positions/2/360/360_cfd6bad8b5a15a40.webp`, `public_url=https://media.oxphyre.com/tours/1/positions/2/360/360_cfd6bad8b5a15a40.webp`. `curl -I` devolvio HTTP/2 200, `content-type: image/webp`, `cf-cache-status: MISS`. Posicion con panoramica R2 validada y visitable.
- Fallback controlado: con `R2_ENABLED=true` y `R2_SECRET_ACCESS_KEY=INVALIDA_TEST_FALLO`, subida E guardada como local. BD id 59, `direction=E`, `storage_provider='local'`, `storage_key=NULL`, `public_url=NULL`. La subida no se rompio y `.env` fue restaurado.
- No se suben depth maps ni originales a R2, no se borra el WebP local y no se reutiliza `storage_key`.

**Secuencia futura R2:**
- **2A:** implementada y validada. Nuevas subidas = WebP local + intento R2 + metadata R2 si funciona.
- **2B:** implementada y validada. Visor/dashboard usan `public_url` si existe y fallback local si no.
- **3:** pendiente. Limpieza de WebP locales y objetos R2 huerfanos/antiguos. R2 ya es fuente validada del visor, pero no se borra local hasta definir esta fase.

**Fase 2B validada en servidor real:**
- `backend/services/PhotoUrlResolver.php` es el unico punto autorizado para resolver la URL visible final de una foto.
- `PhotoModel` no debe contener logica de resolucion de URLs publicas; devuelve datos de BD.
- Controllers preparan datos con URLs resueltas: `TourController::showPublic()` construye `TOUR_DATA` con `PhotoUrlResolver::resolve()` y `PositionController::showUpload()` anade `resolved_url`.
- Vistas y JS consumen URL ya resuelta; solo se permite fallback defensivo local en vista si `resolved_url` no existe.
- `R2StorageService.php` no debe tocarse salvo bug real de firma/upload/delete.
- No borrar WebP local hasta Fase 3. La copia local sigue siendo fallback temporal y compatibilidad para fotos legacy.
- `public/index.php` debe permitir `https://media.oxphyre.com` en `img-src`.
- El bucket R2 `oxphyre-tour-media` necesita CORS para `https://oxphyre.com` y `https://www.oxphyre.com` con `GET`/`HEAD`; WebGL/Three.js requiere CORS aunque la imagen responda HTTP 200.
- Si una imagen R2 devuelve 200 pero Three.js muestra negro o el visor cae a "Tour no disponible", comprobar primero CORS y cache Cloudflare. Una respuesta antigua puede venir sin CORS desde cache (`cf-cache-status=HIT`); las keys unicas por upload evitan reutilizar objetos cacheados.

**Alcance cerrado Fase 2A:**
- Archivos modificados: `backend/models/PhotoModel.php`, `backend/controllers/PositionController.php`.
- `R2StorageService.php` ya estaba implementado y validado; no se modifico en Fase 2A.
- No se tocaron `ImageProcessingService.php`, visor, dashboard ni `TourController.php`.
- No migrar fotos antiguas, no subir depth maps/originales, no purgar cache Cloudflare y no reutilizar keys.
- Siguiente bloque recomendado: bloquear/desactivar "Ver posición" si falta panorámica `360`.

## Definicion vigente de tiers

**Estado actual:** `Planes_Oxphyre.md` ya no es una propuesta pendiente. Tras validar `/precios` y la seccion `#precios` de la landing en produccion, Free, Pro y Business quedan como definicion comercial vigente.

**Decision vigente:** La pagina publica `/precios`, la landing y la documentacion deben mantener la misma segmentacion: Free como prueba real limitada, Pro como plan comercial principal y Business como plan premium con varias capacidades marcadas como proximamente/roadmap.

## Planes SaaS — Definición técnica y comercial

**Estado actual:** Los planes Free, Pro y Business siguen siendo la definición técnica y comercial del producto. Esta sección conserva precios, límites, features y posicionamiento de cada plan porque forma parte de la visión comercial y del TFG. Algunas capacidades están implementadas, otras están en desarrollo TFG y otras pertenecen al roadmap.

**Nota histórica:** Parte de esta definición se escribió cuando el visor principal era Three.js manual y después PSV. En el estado actual, las referencias antiguas a PSV deben interpretarse como visor público navegable Three.js vigente, no como obligación de reintroducir Photo Sphere Viewer.

**Decisión vigente:** No simplificar ni borrar la definición comercial de planes. Al implementar una feature concreta, verificar en `AI_SYNC.md` y `DEVLOG.md` si está lista, pendiente o en roadmap.

### FREE (0€)
- 1 negocio, 1 tour, hasta 3 posiciones por tour
- Enlace público bajo oxphyre.com incluido
- QR básico descargable con branding Oxphyre incluido (no QR profesional)
- Flechas de navegación básicas entre posiciones incluidas (no hotspots informativos con texto/precio/CTA)
- Mapa/ubicación del negocio (Leaflet/OSM) visible en el visor público
- Sin minimapa
- Sin embed/iframe — solo enlace público, no se puede incrustar en web propia
- Marca de agua Oxphyre visible dentro del visor: overlay semitransparente sobre el visor (no solo etiqueta discreta en esquina) + badge opcional "Creado con Oxphyre" clicable hacia /precios. Diseñada para incentivar upgrade a Pro sin destruir la experiencia del visitante.
- URL siempre bajo dominio oxphyre.com (nunca dominio propio)
- Sin analíticas
- **Nota histórica:** La estrategia anterior contemplaba 5 posiciones y "1 posición con MiDaS como crédito de prueba". Decisión vigente (2026-05-20): 3 posiciones, sin crédito MiDaS diferenciado. La watermark más visible es el principal mecanismo de incentivo a upgrade.

### PRO (19 EUR/mes - 182 EUR/ano)
- Hasta 5 negocios, tours ilimitados, hasta 20 posiciones por tour
- Sin marca de agua
- QR profesional
- Embed/iframe para incrustar el tour en la web propia del negocio
- Analiticas basicas
- Soporte por email
- Hotspots comerciales/informativos quedan como roadmap/proximamente, no como disponible inmediato
- MiDaS queda como tecnologia interna/futura; no se vende como promesa comercial principal del plan

### BUSINESS (49 EUR/mes - 470 EUR/ano)
- Negocios ilimitados, posiciones ilimitadas por tour
- Soporte prioritario + onboarding
- Dominio personalizado, marca blanca, API access, multiples usuarios y analiticas avanzadas quedan marcadas como proximamente/roadmap hasta implementacion real
- Hotspots enriquecidos, formularios, reservas, agente IA y automatizaciones quedan como roadmap/proximamente
- Business no debe presentarse como si todas sus features avanzadas estuvieran disponibles hoy; la UI debe marcarlas como "Proximamente" o equivalente.

## Contexto TFG
- Estudiante DAW (Desarrollo de Aplicaciones Web), 2º año
- Entrega: finales mayo 2026
- Objetivo: nota máxima + producto real comercializable
- El tribunal evaluará específicamente: SEO, PageSpeed, seguridad (intentarán inyecciones SQL y XSS), UX/UI, MVC correcto
- Exposición: profesores probarán la app en tiempo real desde sus portátiles escaneando un QR

**Decisión vigente:** Para el TFG prima estabilidad, seguridad, SEO, PageSpeed, UX y demo fiable sobre ampliar alcance. No sacrificar calidad ni robustez por añadir features grandes de roadmap.

### Estrategia de procesado MiDaS y demo para la exposición

**Estado actual:** El servidor t3.small usa MiDaS Small mediante un microservicio Flask en localhost, gestionado con systemd. El flujo de subida y procesado está implementado: PHP valida y guarda la imagen, llama a `MiDaSService`, Flask procesa con MiDaS Small y devuelve el depth map para guardarlo y asociarlo en BD. CLAHE se aplica para mejorar contraste/iluminación antes del procesado cuando el servicio responde correctamente.

**Nota histórica:** Se intentó usar MiDaS DPT-Hybrid en el servidor porque da más calidad, pero el t3.small no tiene RAM suficiente para cargarlo junto con Nginx, PHP-FPM y MySQL. El servidor se colgó por OOM al intentar cargar Hybrid. Por eso se cambió a MiDaS Small en servidor y se reservó DPT-Hybrid para PC local con GPU.

**Decisión vigente:** Servidor = MiDaS Small para demo/subidas puntuales. PC local con RTX 3060 = DPT-Hybrid para tours demo pregenerados de alta calidad. Nunca depender del procesado en directo como plan principal de la exposición.

#### Hardware del desarrollador (PC local)

**Estado actual:** Este hardware sigue siendo la referencia para generar tours demo con MiDaS DPT-Hybrid + CUDA antes de la exposición.

- CPU: Intel Core i5-12400F 12th Gen
- RAM: 16GB DDR4 3200MHz (uso normal ~63% con Chrome abierto, ~50% sin Chrome)
- GPU: NVIDIA GeForce RTX 3060 12GB VRAM (CUDA 13.0)
- Disco C:: 948GB total, ~24.5GB libres actualmente (liberar ~90GB borrando Fortnite de Epic Games)
- Python: 3.12.6 instalado en Windows
- OS: Windows 11

**Decisión vigente:** No depender del servidor para procesado pesado de máxima calidad. Usar el PC local para pregenerar material demo cuando se necesite máxima calidad visual.

#### Por qué procesamos en local y no en el servidor

**Estado actual:** El servidor procesa con MiDaS Small. El PC local procesa con DPT-Hybrid cuando se necesita calidad máxima.

El servidor EC2 t3.small tiene 2GB RAM. MiDaS DPT-Hybrid necesita ~1800MB para cargar. Con el stack completo (Nginx+PHP+MySQL) corriendo solo quedan ~1200MB libres — insuficiente. El servidor se colgó al intentarlo.

El PC local tiene RTX 3060 con CUDA — procesa cada foto en 2-3 segundos en lugar de 45 segundos en CPU. La calidad es máxima (DPT-Hybrid).

**Nota histórica:** La decisión no fue por preferencia estética, sino por límite físico de RAM y estabilidad. DPT-Hybrid en servidor se descartó tras comprobar que el t3.small no puede sostenerlo junto al stack web.

**Decisión vigente:** No reinstalar ni activar DPT-Hybrid como modelo de servidor en t3.small salvo cambio real de infraestructura.

#### Plan de procesado en PC local

**Estado actual:** El script local Windows para procesar con DPT-Hybrid + CUDA sigue pendiente. Este plan conserva los pasos previstos porque son útiles para preparar los tours demo.

**Requisitos previos (hacer una sola vez):**
1. Desinstalar Fortnite desde Epic Games Launcher (~92GB) → disco C: pasa a ~115GB libres
2. Instalar PyTorch con CUDA en Windows:
   - Abrir PowerShell como administrador
   - Ejecutar: pip install torch torchvision --index-url https://download.pytorch.org/whl/cu121
3. Instalar dependencias: pip install transformers timm opencv-python Pillow
4. Descargar modelo DPT-Hybrid (~467MB) — instrucciones pendientes de implementar en script local

**Cada vez que vayas a procesar fotos:**
1. Cerrar Chrome completamente (libera ~1GB RAM extra)
2. Cerrar League of Legends si está abierto (~480MB)
3. RAM disponible resultante: ~6-7GB libres (40-45% de uso) — sobrado para MiDaS
4. VRAM disponible: ~10GB libres — sobrado para DPT-Hybrid
5. Ejecutar script local de procesado (pendiente de crear)
6. Tiempo por foto: 2-3 segundos con GPU
7. Subir fotos originales + mapas de profundidad al servidor

**RAM mínima recomendada para procesar:** 35-40% de uso (6GB+ libres)
**RAM máxima aceptable para procesar:** 65% de uso (5GB+ libres)
**No procesar nunca con RAM >70%** — riesgo de lentitud o cuelgue

**Nota histórica:** Este plan nació para poder enseñar calidad máxima en la exposición sin depender del rendimiento limitado del servidor t3.small.

**Decisión vigente:** El script local no debe convertirse en requisito para clientes; es una herramienta interna para demo/TFG.

#### Tours de demo para la exposición (OBLIGATORIO)

**Estado actual:** Tener preparados 1-2 tours completos y visualmente impecables antes de la exposición sigue siendo obligatorio. Pueden usar material de alta calidad, panorámicas controladas o imágenes pregeneradas, pero no se debe presentar la captura 360° perfecta como requisito para clientes con smartphone normal.

Tener preparados 1-2 tours completos y visualmente impecables ANTES de la exposición:
- Fotos 360° equirectangulares de alta calidad (buscar en Flickr 360°, Poly Pizza, o generar con IA)
- Procesadas con MiDaS DPT-Hybrid en PC local con GPU
- Subidas al servidor y tours publicados y navegables
- El tribunal navega estos tours y ve el producto en su máximo esplendor

**Regla de oro:** nunca depender de que algo funcione en tiempo real delante del tribunal. Los tours pregenerados son el plan A siempre.

**Nota histórica:** La idea de usar fotos equirectangulares 360° de alta calidad se planteó como estrategia de demo para maximizar impacto visual. Después se detectó que los smartphones comunes no garantizan equirectangulares reales, así que esa opción debe entenderse como material demo controlado, no como requisito comercial para el cliente final.

**Decisión vigente:** Los tours pregenerados son el plan A de la exposición. La subida en directo es demostración secundaria y debe tener fallback.

#### Subida en directo (si el tribunal quiere probar)

**Estado actual:** El servidor usa MiDaS Small (80MB, cabe en RAM) para procesado en tiempo real/puntual. Tiempo estimado: 30-60 segundos por foto en t3.small con swap de 2GB. La UX debe camuflar la espera y dejar claro que está analizando profundidad.

La UX debe camuflar el tiempo de espera:
- Barra de progreso animada durante el procesado
- Mensaje: "Analizando profundidad con IA..."
- Formulario con campos adicionales visibles mientras procesa (nombre de posición, descripción) para que el usuario esté ocupado
- El tiempo percibido es mucho menor cuando el usuario interactúa

**Si algo falla en directo:** los tours pregenerados demuestran que el producto funciona. El fallo puntual es atribuible a las limitaciones del servidor de desarrollo (t3.small), no al producto.

**Nota histórica:** Esta sección nació para evitar que la latencia de CPU en servidor se perciba como fallo de producto durante la exposición.

**Decisión vigente:** Si el tribunal prueba subida en directo, usar progreso/feedback y mantener tours pregenerados listos como respaldo.

#### Estado actual del servidor (t3.small)

**Estado actual:**
- Servidor AWS EC2 t3.small con Ubuntu 22.04.
- RAM total aproximada: 1910MB.
- Swap 2GB configurado como colchón de seguridad.
- Microservicio Flask MiDaS activo en `127.0.0.1:5000` mediante systemd (`oxphyre-midas`).
- Modelo en servidor: MiDaS Small, cargado desde caché torch/hub.
- Flujo funcionando en producción: upload PHP → `MiDaSService` → Flask → depth map base64 → guardado de archivo → asociación en BD.
- CLAHE integrado mediante endpoint `/enhance` para mejorar imagen antes del depth map cuando el servicio responde correctamente.
- MiDaS DPT-Hybrid queda reservado para PC local y tours demo.

**Nota histórica:** En un momento anterior el swap figuraba como pendiente y el modelo `dpt_hybrid.pt` estaba descargado en servidor. Después se configuró swap 2GB y se migró el servicio a MiDaS Small porque Hybrid no era viable en t3.small. Si el archivo Hybrid pesado sigue existiendo en servidor, considerarlo residuo histórico y valorar borrarlo solo si hace falta espacio.

**Decisión vigente:** No usar DPT-Hybrid en servidor t3.small. Mantener MiDaS Small para producción/demo puntual y reservar Hybrid para PC local.

#### Pendiente de implementar

**Estado actual:** MiDaS Small, Flask, systemd, swap y flujo PHP → Flask → mapa de profundidad → BD ya están implementados según `DEVLOG.md` y `AI_SYNC.md`.

**Pendiente real relacionado con MiDaS/demo:**
- Script Python local para procesado con GPU en Windows usando DPT-Hybrid + CUDA.
- Preparar 1-2 tours demo pregenerados visualmente impecables.
- Revisar/afinar UX de progreso durante procesado si el tribunal prueba subida en directo.
- Reimplementar o decidir si se descarta el shader/parallax MiDaS sobre el visor Three.js actual.

**Nota histórica:** Antes estaban pendientes: swap de 2GB, instalar MiDaS Small, levantar microservicio Flask funcional y cerrar el flujo PHP → Flask → mapa de profundidad → BD. Esos puntos ya se completaron durante la integración del microservicio y la subida de fotos.

**Decisión vigente:** No tratar Flask/MiDaS Small/swap/flujo PHP-Flask como tareas pendientes. La prioridad pendiente real es demo local de alta calidad y decisiones sobre parallax MiDaS en el visor actual.

---

## Roadmap post-TFG: 3D Gaussian Splatting

**Estado actual:** Roadmap post-TFG documentado y validado conceptualmente. No forma parte obligatoria del núcleo TFG salvo decisión posterior. Para la exposición puede presentarse como evolución potente del producto o tecnología futura, no como requisito para terminar la versión actual.

**Nota histórica:** Se evaluaron alternativas como Luma AI, Polycam, Google Street View app, gran angular de smartphone, OpenSplat y SuperSplat Viewer. Se decidió OpenSplat + SuperSplat por ser open source, viable comercialmente y compatible con procesado propio.

**Decisión vigente:** No borrar este roadmap. No implementarlo como prioridad TFG salvo aprobación explícita. Mantenerlo como visión post-TFG y diferenciación futura.

### Decisión comercial post-TFG

**Estado actual:** 3D Gaussian Splatting queda como la dirección comercial definitiva post-TFG de Oxphyre. No forma parte del core obligatorio del TFG; para la entrega solo se contempla una demo pregenerada si da tiempo y no compromete estabilidad.

**Nota histórica:** OpenSplat se confirmó como herramienta externa AGPLv3 sin modificar, igual que MiDaS. SuperSplat Viewer se mantiene como visor MIT para servir el resultado en navegador. Esta decisión conserva el razonamiento legal ya documentado: la obligación AGPLv3 afecta a modificaciones de OpenSplat, no al código PHP, backend, dashboard ni lógica de negocio de Oxphyre cuando se usa como herramienta externa.

**Decisión vigente:** Oxphyre mantiene privado su código PHP/backend/dashboard. Los vídeos de clientes se procesan en infraestructura controlada por Oxphyre o GPU bajo demanda. El cliente no interactúa con OpenSplat: ve una experiencia de marca tipo **"Oxphyre 3D Capture"**. El valor comercial no es solo usar una herramienta open source, sino ofrecer el pack completo: captura guiada, procesado automático, hosting, visor, QR, embed, analíticas, soporte y UX pensada para PYMES.

### Qué es y por qué es relevante para Oxphyre
3D Gaussian Splatting (3DGS) es una tecnología de reconstrucción 3D que permite
al visitante MOVERSE LIBREMENTE por el espacio — no solo girar desde un punto fijo
como en el visor actual. La IA reconstruye el espacio completo a partir de un vídeo
grabado con el smartphone del cliente. El resultado se renderiza en tiempo real en
el navegador sin plugins ni descargas.

Es la evolución natural del producto: mientras el visor actual muestra fotos dentro
de una esfera, 3DGS crea un modelo 3D fotorrealista navegable. Es lo que hace que
Matterport cueste $45k-$85k MXN de equipo — nosotros lo replicamos con un móvil
y software open source.

El resultado se sirve desde una URL, funciona en cualquier navegador (móvil y desktop)
y soporta WebXR (AR/VR). No requiere app nativa.

### Stack técnico decidido (open source, sin coste de licencias)

**Procesado (generación del modelo 3D):**
- OpenSplat (AGPLv3): convierte vídeo → archivo .splat/.ply
  GitHub: github.com/pierotofy/OpenSplat
  Requiere GPU NVIDIA para procesado viable (CPU es 100x más lento)
  Uso comercial permitido bajo AGPLv3

**Visor (renderizado en navegador):**
- SuperSplat Viewer (MIT license): renderiza archivos .splat en el navegador
  GitHub: github.com/playcanvas/supersplat
  Self-hosteable sin restricciones, MIT = 100% libre para uso comercial
  Soporta hotspots, anotaciones, animaciones de cámara, WebXR

**Pipeline completo:**
1. Cliente graba vídeo lento de su local con el smartphone (2-3 minutos)
2. Sube el vídeo a Oxphyre (igual que sube fotos ahora)
3. Oxphyre procesa con OpenSplat en GPU → genera archivo .splat
4. Oxphyre sirve el .splat con SuperSplat Viewer embebido
5. Visitante navega libremente por el local en el navegador

### Legalidad y privacidad — confirmado y cerrado

**Código de Oxphyre:** 100% privado siempre.
La obligación AGPLv3 de OpenSplat solo afecta a modificaciones del código
de OpenSplat en sí — no al código de Oxphyre. Si se usa OpenSplat como
herramienta externa sin modificarlo (igual que usamos MiDaS), todo el código
PHP, lógica de negocio, dashboard y sistema de Oxphyre permanece privado.
Ningún competidor puede reclamarlo.

**Vídeos y datos de clientes:** 100% privados.
El vídeo se procesa en los servidores de Oxphyre y nunca sale de ellos.
OpenSplat procesa localmente — no hay ningún servicio externo que reciba
los datos del cliente. Los archivos .splat resultantes son propiedad del
cliente según los términos y condiciones de Oxphyre.

**SuperSplat Viewer:** MIT license — sin ninguna restricción legal.
Se puede integrar, modificar y comercializar sin obligaciones de publicar código.

### Hardware requerido para producción

Para el TFG (tours de demo pregenerados):
- PC local del desarrollador con RTX 3060 (CUDA) — procesa en minutos
- El servidor t3.small no tiene GPU — no puede procesar en tiempo real

Para producción real con clientes:
- Instancia GPU en AWS (G4dn.xlarge ~0.50$/hora) bajo demanda
- Se enciende al recibir un vídeo, procesa, se apaga — coste por uso
- No es un coste fijo — solo se paga cuando un cliente sube un vídeo

### Diferenciación por tiers (propuesta, no definitiva)

**FREE (visor actual, sin cambios):**
- 4 fotos estáticas desde puntos fijos
- El visitante gira la cámara pero no se mueve por el espacio
- "Mira tu negocio desde dentro"

**PRO (Gaussian Splatting básico):**
- Cliente graba 1 vídeo con el móvil → Oxphyre genera el tour 3D navegable
- El visitante se mueve libremente por el local
- 1 escena por negocio, resolución estándar, procesado en cola compartida
- Hotspots básicos en el espacio 3D
- "Pasea por tu negocio como si estuvieras ahí"

**BUSINESS (Gaussian Splatting avanzado):**
- Escenas ilimitadas, resolución máxima, procesado prioritario
- Hotspots enriquecidos: vídeo, reservas, formularios dentro del espacio 3D
- Dominio personalizado, marca blanca total
- Exportación del modelo 3D para uso en webs propias
- Ideal para hoteles, gimnasios, inmobiliarias, espacios grandes
- "Tu negocio en 3D fotorrealista, integrado en tu web"

### Requisitos para la captura (instrucciones al cliente)
- Grabar vídeo lento y suave con el smartphone (sin movimientos bruscos)
- Iluminación homogénea — evitar ventanas muy brillantes con resto oscuro
- Objetos estáticos durante la grabación
- Recorrer todo el espacio en 2-3 minutos
- Lente normal (1x) — nunca gran angular
- El resultado mejora significativamente con buena iluminación del local

### Estado actual
- Tecnología evaluada y validada: ✓
- Stack técnico definido: ✓
- Legalidad confirmada: ✓
- Implementación en Oxphyre: pendiente post-TFG
- Para la exposición del TFG: generar 1-2 tours de demo con RTX 3060 local
  mostrándolo como "la tecnología que potencia los planes Pro/Business"

### Herramientas descartadas
- Luma AI: servicio de pago ($30-300/mes), sin API pública gratuita
- Polycam: ídem, servicio de pago
- Google Street View app: eliminada de las stores en 2023
- Gran angular del smartphone: sacrifica calidad inaceptablemente

---

## Decisiones descartadas o no reabrir sin motivo

**Estado actual:** Estas opciones están descartadas o no son el camino principal del proyecto.

- React, Vue, Angular.
- Laravel, Symfony.
- Bootstrap.
- SQL directo sin prepared statements.
- Guardar tokens o datos sensibles en localStorage.
- Validar uploads solo por extensión.
- Visor público Three.js manual anterior como solución principal.
- DPT-Hybrid en servidor t3.small.
- Depender del procesado MiDaS en directo durante la exposición.
- Cámaras 360° profesionales como requisito para clientes.
- Gran angular del smartphone como recomendación principal.
- OpenCV stitching automático como núcleo del TFG.
- Luma AI y Polycam como núcleo del producto.
- Google Street View app como solución de captura.

**Nota histórica:** Varias de estas opciones se descartaron tras pruebas reales con fotos de smartphone, límites de RAM del servidor, coste de servicios externos, desaparición de apps o mala compatibilidad con el público objetivo de PYMES con smartphone normal.

**Decisión vigente:** No reabrir estas decisiones salvo problema claro, explicado antes y aprobado. Si una IA cree que debe reabrir una decisión, primero debe justificar qué cambió desde el análisis anterior.

## Diseño Visual y Storytelling

**Estado actual:** Esta sección funciona como referencia visual y narrativa de la landing. La landing está implementada y desplegada, pero cualquier ajuste visual debe verificarse contra los archivos reales y contra `DEVLOG.md`.

**Nota histórica:** Esta especificación recoge el diseño objetivo definido durante el rediseño completo de la landing: estética negra cinematográfica, acento ámbar, storytelling por secciones, hero con Three.js y experiencia visual de producto premium.

**Decisión vigente:** Mantener el estilo oscuro/cinematográfico y la identidad Oxphyre. No usar esta sección para reabrir decisiones ya cerradas sin revisar implementación real. Three.js en landing sigue permitido y forma parte de la identidad visual.

### Identidad Visual
- Fondo: #000000 puro en toda la página
- Acento: #FEB354 (sandy brown)
- Texto primario: #FFFFFF blanco puro
- Texto secundario: rgba(255,255,255,0.65) MÍNIMO, nunca gris puro sobre negro
- Grain cinematográfico en toda la página (CSS, SVG data URI, mix-blend-mode overlay, opacity 0.04)
- Todas las secciones fondo #000000, sin bordes ni hr entre ellas
- La luz separa visualmente las secciones, no los bordes
- Three.js SOLO en el hero y CTA final (esfera pequeña decorativa)
- Todo efecto interactivo es un plus, nunca requisito para leer el contenido
- Sin eyebrows en ninguna sección
- Scroll behavior: smooth en toda la página
- Animaciones entrada: fade in + translateY con IntersectionObserver en todas las secciones

### Tipografías
- H1/H2/H3: **Wix Madefor Display** (Google Fonts, sin serifa, moderna)
- Body/UI/botones: **Inter** (Google Fonts)
- Números/métricas/código: **JetBrains Mono** (Google Fonts)

### Iconos
- Librería: **Lucide Icons** (open source, SVG limpio, moderno)

### Logo y Favicon
- PENDIENTE: diseñar logo real cuando la página esté terminada
- Actualmente: texto "Oxphyre" en color #FEB354 como logo temporal

### Cursor personalizado
- Reemplaza el cursor nativo en toda la página
- Círculo de ~20px, borde ámbar #FEB354 fino (1px), sin punto central
- Se agranda (~32px) al hacer hover sobre elementos interactivos
- Transición suave 0.2s ease
- En móvil/tablet: desactivado completamente

### Nav Desktop
- Logo Oxphyre (#FEB354) a la izquierda
- Links centrados en orden: Cómo funciona · Demo · Características · Precios · FAQ
- Derecha: toggle oscuro/claro (sol=oscuro, luna=claro) + ES/EN + "Iniciar sesión" (ghost) + "Empezar gratis" (primario ámbar)
- Transparent al inicio, glassmorphism al hacer scroll

### Nav Mobile
- Izquierda: logo Oxphyre
- Derecha: solo icono hamburguesa
- Menú abierto: overlay negro completo, links centrados grandes, toggle oscuro/claro y ES/EN al final del overlay

### Loader
- Pantalla negra total, cursor personalizado activo desde el primer segundo
- Foco de luz ámbar barre de izquierda a derecha revelando letras OXPHYRE una a una
- Timing: 0.0s inicio · 0.5s empieza foco · 1.5s empiezan letras · 3.0s OXPHYRE completo · 4.0s explosión
- Las letras explotan en partículas que forman el espacio interior de la esfera
- Duración total: ~4 segundos

### S1 — Hero (100vh)

**Fase 1 - Dentro de la esfera (Three.js):**
- Cámara dentro de una esfera invertida, negro con partículas ámbar flotando
- Partículas: puntos pequeños #FEB354, movimiento lento y orgánico, dan profundidad 3D
- En 180° las partículas brillan con más intensidad
- Nav oculto excepto logo pequeño esquina superior izquierda
- Frases por zonas de rotación con profundidad Z y easing:
  - 0°: "Bienvenido a la profundidad."
  - 90°: "Aquí, tu espacio cobra vida."
  - 180°: "Cada rincón, capturado en su mejor momento."
  - 270°: "No es una foto. Es tu negocio vivo."
  - 360°: "↓ Explora la dimensión Oxphyre" (pulsa como latido)
- Scroll = cámara sale de la esfera hacia atrás (eje Z), máximo 0.8s

**Fase 2 - Fuera de la esfera:**
- Nav completo: opacity 0→1 + blur 4px→0, transición 1s ease-in
- H1: "Tours virtuales 3D para negocios que quieren brillar."
- Subtítulo: "Convierte tu local en una experiencia 360° que tus clientes pueden visitar desde cualquier lugar. Sin cámaras especiales, sin técnicos, sin complicaciones."
- Botón primario: "Crear mi tour gratis →"
- Botón secundario: "Ver un tour en vivo"
- 3 pills: "✓ Sin hardware especial" · "✓ Listo en menos de 1 hora" · "✓ Funciona en cualquier móvil"
- Scroll hint: línea vertical ~40px ámbar pulsante, desaparece al primer scroll
- Esfera visible desde fuera, posición derecha-abajo (ajustar por ensayo-error)
- Esfera fuente de luz: glow ámbar desde su parte inferior

### S2 — Carrusel negocios (100vh)
- Carrusel horizontal, avance automático + drag
- Cards con perspectiva 3D CSS (rotateY en laterales ~25°)
- Card central: frontal, iluminada por foco desde arriba
- Cards laterales: rotadas, perdiéndose en oscuridad
- Foto Unsplash gratuita por card (oscura, dramática, interior atmosférico)
- 8 negocios:
  - Restaurante: "Que reserven antes de probar tu cocina"
  - Gimnasio: "Que vean las instalaciones antes de apuntarse"
  - Peluquería: "Que conozcan tu espacio antes de su cita"
  - Hotel: "Que elijan su habitación antes de reservar"
  - Tienda: "Que exploren tu tienda desde el sofá"
  - Inmobiliaria: "Que visiten la propiedad sin salir de casa"
  - Clínica: "Que conozcan tu consulta antes de su primera cita"
  - Coworking: "Que sientan el espacio antes de reservar su mesa"

### S3 — Cómo funciona (100vh)
- H2: "Cómo funciona"
- Subtítulo: "Tu tour virtual en tres pasos. Sin curva de aprendizaje."
- Grid 3 cards simultáneas
- Luz: aro/círculo gigante CSS con dos anillos (exterior tenue, interior brillante)
- El aro proyecta luz sobre las cards y sobra hacia S4
- Hover cards: elevación 4px + borde ámbar más brillante
- 01: "Fotografías tu local" · 02: "Construyes el tour" · 03: "Lo compartes (QR + embed)"

### S4 — Demo video (100vh)
- H2: "Mira cómo funciona"
- Subtítulo: "Descubre cómo un negocio real se convierte en un tour virtual 3D navegable. Sin registro."
- Video centrado, grande (placeholder hasta tener demo real grabado)
- El aro de S3 llega hasta aquí y el video lo tapa
- Video emite glow propio (box-shadow ámbar sutil)
- Destino del botón "Ver un tour en vivo" del hero (anchor #demo)

### S5 — Características (altura natural)
- H2: "Todo lo que necesitas"
- Subtítulo: "Herramientas pensadas para negocios reales."
- Bento grid asimétrico (cards de distintos tamaños)
- 6 características con iconos Lucide: Tour 3D navegable · Hotspots interactivos · QR + embed · Analíticas · Modo día/noche · Compatible móvil
- Cursor de luz ilumina cards cercanas al mouse (~150px radio)
- Estado base: cards legibles sin interacción (fondo #0A0A0A, borde rgba(254,179,84,0.15))
- En móvil: efecto cursor desactivado

### S6 — Precios (altura natural)
- H2: "Precios transparentes"
- Subtítulo: "Sin comisiones ocultas. Cancela cuando quieras."
- Toggle mensual/anual con badge "Ahorra 20%"
- Free · Pro (destacada, más alta) · Business
- Luz desde abajo: Pro más intensa, Free y Business tenue
- Hover: glow intensifica + card sube 4px

### S7 — FAQ (altura natural)
- H2: "Preguntas frecuentes"
- Schema.org FAQPage en JSON-LD
- Glow muy difuso y tenue detrás de la lista
- Pregunta abierta: texto blanco + línea izquierda ámbar 2px
- Solo una pregunta abierta a la vez

### S8 — CTA Final (100vh)
- Esfera Three.js pequeña decorativa (gira sola, sin interacción, sin partículas)
- Luz ámbar intensa desde abajo
- H2: "Tu negocio merece ser descubierto."
- Subtítulo: "Empieza gratis hoy. Sin tarjeta de crédito."
- UN solo botón: "Crear mi tour gratis →" con glow ámbar intenso

### S9 — Footer (altura natural)
- Negro absoluto sin efectos de luz
- Logo + tagline: "Tours virtuales 3D para negocios locales."
- 4 columnas: Producto · Legal · Contacto · Redes sociales
- Selector ES/EN + copyright dinámico PHP date('Y')
- Links RGPD obligatorios: Privacidad · Términos · Cookies

### Transiciones entre secciones
- Fade out/in simultáneo: luz de sección saliente se apaga mientras luz de entrante se enciende
- Nunca oscuridad total, siempre algo de luz durante la transición
- Implementado con IntersectionObserver + transition: opacity 1.2s ease en glows CSS

### Regla global: Soft delete

**Estado actual:** Soft delete activo en `businesses`, `tours`, `positions`, `photos` y `hotspots`.

Soft delete activo en `businesses`, `tours`, `positions`, `photos`, `hotspots`.
- **NUNCA usar `DELETE FROM`** en estos modelos — siempre `UPDATE ... SET deleted_at = NOW() WHERE id = ?`
- **Todos los `SELECT`** de estos modelos deben incluir `WHERE deleted_at IS NULL` (o `AND deleted_at IS NULL` si ya hay `WHERE`)
- Las tablas `users`, `plans`, `qr_codes`, `qr_scans`, `contact_messages`, `cookies_consent` y `login_attempts` **no tienen soft delete** — en ellas sí se puede usar `DELETE FROM`

**Nota histórica:** Esta regla se añadió tras implementar borrado lógico en negocios, tours, posiciones y fotos para evitar pérdida definitiva de datos y mantener consistencia en el dashboard.

**Decisión vigente:** Nunca usar borrado físico en esos modelos. Cualquier query nueva debe respetar `deleted_at IS NULL`.

### Pendientes y deuda técnica

**Estado actual:** Esta lista se organiza por prioridad para el TFG, prioridad media, deuda técnica y roadmap/futuro. No se borran pendientes antiguos útiles; se reclasifican para que una IA no confunda tareas críticas de entrega con mejoras post-TFG.

#### Prioridad alta para TFG
- Roles documentados (requisito tribunal): documentar explícitamente en la memoria qué puede hacer cada rol (`admin`, `business_owner`, `viewer`) tanto en frontend como en backend. Los roles ya existen en BD pero no están documentados.
- `/precios`: implementada y validada en produccion. Mantener coherencia con landing `#precios` y planes vigentes; no volver a marcarla como pendiente salvo regresion real.
- API externa obligatoria (requisito tribunal): cubierta por Nominatim/OpenStreetMap server-side (Mapa 1B) + Leaflet/OSM publico (Mapa 1C), validado en servidor real.
- SEO tecnico inicial: sitemap minimo creado y validado, robots.txt gestionado por Cloudflare sin tocar, Search Console con home indexada/FAQ valida y sitemap enviado. Queda pendiente que Google procese/reintente el sitemap.
- Preparar 1-2 tours demo visualmente impecables antes de la exposición.
- Video demo real: grabar y sustituir placeholder de S4.
- Responsive: verificar todas las secciones en móvil y tablet.
- Revisar SEO técnico final: schema, metas, Open Graph y seguimiento en Search Console.
- Revisar PageSpeed final.
- Dashboard y wizard: revisar visibilidad general — inputs, labels y texto secundario tienen contraste insuficiente (texto gris oscuro sobre fondo negro). Mejorar colores para que los campos que el usuario debe rellenar sean claramente visibles. Nunca texto gris oscuro sobre fondo negro en zonas interactivas.

#### Prioridad media
- QR 2A cerrado: tracking basico con privacidad validado en servidor real. Futuro QR 2B queda para analiticas avanzadas, graficas, campanas o QR por posicion si se decide.
- Editor canvas drag & drop.
- Hotspots 1A, 1B, 1C y 1D implementados. 1D validado parcialmente en servidor real: `needs_review` automático al subir/borrar panorámica, ciclo de revisión/recolocación cerrado, avisos en dashboard de posición y de tour. La deuda P1 de estilos inline de esos avisos ya está cerrada en `dashboard.css`. Pendiente confirmar ciclo completo con borrado de panorámica. Hotspots 1E: pulido UX mobile/labels/límites.
- Minimap real.
- Tutorial/onboarding del editor: implementar tutorial la primera vez que el usuario accede, con botón para volver a verlo. Debe explicar la jerarquía negocio → tour → posiciones → fotos y cómo usar el canvas.
- Dashboard: añadir tooltips de ayuda contextual en las métricas para clarificar la jerarquía del producto al usuario no técnico. Ejemplo: icono ? en "Tours activos" con tooltip "Un tour es la experiencia 360° que verán tus clientes", y en "Negocios" con "Un negocio agrupa todos tus tours".
- Wizard paso 2 (Tu plan): mostrar los 3 planes en cards lado a lado (Free, Pro destacado, Business) en lugar del plan Free solo con link discreto. El momento del onboarding es el de mayor motivación del usuario — es el mejor punto para mostrar el valor de Pro y Business y conseguir upgrades. Mismo diseño de cards que la sección de precios de la landing.
- Logo y favicon: diseñar cuando la página esté terminada.
- Modo claro: implementar cuando modo oscuro esté completamente cerrado.
- 404/500 personalizadas.
- Legal/RGPD: privacidad, términos, cookies.
- PWA: manifest y service worker.

#### Deuda técnica
- BusinessController tiene `go()` y `verifyCsrf()` como métodos privados propios. AuthController tiene `redirect()` y `validateCsrf()` con la misma funcionalidad pero distintos nombres. Unificar en BaseController como métodos protegidos y eliminar duplicados en los controllers hijos. Hacer en un refactor pass cuando todos los controllers estén creados.
- `UserModel::create()` tiene el rol "business_free" hardcodeado en SQL. Refactorizar cuando existan más roles: pasar `$role` como parámetro o definir constante `ROLE_DEFAULT` en config.php.
- Emails transaccionales: actualmente PHPMailer + Gmail SMTP con cuenta `danimm3097@gmail.com` (válido para TFG). La cuenta `digitechfp.com` se descartó porque el centro educativo tiene SMTP capado. En producción real migrar a Resend, SendGrid o Mailgun con dominio propio `noreply@oxphyre.com` — Gmail muestra la cuenta del remitente en lugar de una dirección de marca y tiene límite de ~500 emails/día.
- Gmail SMTP requiere App Password en `.env`, no la contraseña de cuenta. `MAIL_USERNAME` y `MAIL_FROM` deben ser el mismo email o Gmail rechazará la conexión.
- Shader MiDaS/parallax: el efecto de profundidad con depth map está pendiente de reimplementar sobre el visor Three.js actual o descartarse. En esta versión la imagen visible no se sobrescribe ni se altera con CLAHE/MiDaS.
- Script Python local (Windows) para procesar tours de demo con MiDaS DPT-Hybrid + CUDA (RTX 3060) antes de la exposición del TFG. Genera calidad máxima en 2-3 segundos por foto. Pendiente de crear el script.

#### Roadmap/futuro
- OpenCV Stitching automático de fotos: mejora futura post-TFG. Requiere que el usuario haga fotos cada 45° (8 fotos) con lente 1x y solapamiento mínimo 30%. Genera equirectangular de calidad. Librería: `cv2.Stitcher_create(cv2.Stitcher_PANORAMA)`. Falla en paredes lisas (Error 2 — sin puntos clave).
- 3D Gaussian Splatting con OpenSplat + SuperSplat Viewer como evolución post-TFG.
- Agente IA completo (OpenClaw/Make/n8n) según definición Business, marcado como roadmap/próximamente hasta implementación real.
- n8n solo si hay tiempo y RAM suficiente; si no, documentarlo como integración futura.

**Nota histórica:** La lista original mezclaba tareas críticas, deuda técnica y mejoras futuras. Se conserva todo el contenido útil, pero queda separado para priorizar mejor la entrega del TFG.

**Decision vigente:** Para el TFG, priorizar requisitos visibles del tribunal y estabilidad: roles, demo, responsive, SEO/PageSpeed y seguridad. `/precios`, API externa y SEO tecnico inicial ya estan cerrados y validados tecnicamente; no ampliar alcance grande si pone en riesgo la entrega.
