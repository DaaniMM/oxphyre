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
- Roadmap post-TFG de 3D Gaussian Splatting documentado.

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

### Visor público
- El visor público Sprint 1 usa Three.js vanilla para la panorámica principal adaptativa y Oxphyre Room.
- La panorámica principal no debe tratarse como esfera/equirectangular 360 completa: se renderiza como vista cilíndrica parcial, con arrastre horizontal y pitch muy limitado.
- Photo Sphere Viewer v4 queda retirado del visor público Sprint 1 porque deformaba panorámicas parciales de móvil al forzarlas como esfera completa.
- La imagen visible siempre debe ser el WebP final optimizado y fiel a la imagen subida; MiDaS/CLAHE quedan como procesado interno o futuro, no como textura pública en Sprint 1.

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
- Cloudflare R2/CDN Fase 2A implementada y validada en servidor real: nuevas subidas mantienen WebP local en EC2 y, si `R2_ENABLED=true`, duplican el WebP visible final en R2 con metadata en BD. Visor/dashboard/TourController aun no usan `public_url`.
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

**Fase 2A implementada y validada:** upload real integrado en `PositionController::upload()` con `resolveStorage()` y `buildR2Key()`. Las nuevas subidas pueden guardar metadata R2 si la copia a R2 funciona, manteniendo siempre WebP local como fallback. Visor/dashboard/TourController aun no usan `public_url`.

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
- **Fase 2A:** implementada y validada. Nuevas subidas guardan WebP local como hasta ahora y, si `R2_ENABLED=true`, tambien intentan subir el WebP final visible a R2. Si R2 funciona, la BD guarda `storage_provider='r2'`, `storage_key` y `public_url`. El visor sigue usando local.
- **Fase 2B:** pendiente. Visor/dashboard usaran `public_url` si existe y fallback local si no.
- **Fase 3:** pendiente. Limpieza local/R2 de objetos huerfanos, cuando R2 sea fuente validada del visor.

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

Archivos tocados en Fase 2A:
- `backend/models/PhotoModel.php`
- `backend/controllers/PositionController.php`
- `backend/services/R2StorageService.php` ya estaba implementado y validado; no se modifico en 2A.

Archivos que no deberían tocarse en Fase 2A salvo necesidad justificada:
- `backend/services/ImageProcessingService.php`
- Visor público
- Dashboard
- `backend/controllers/TourController.php`

Siguiente microbloque real: **Fase 2B** para que visor/dashboard usen `public_url` si existe y fallback local si no, despues de cerrar el debate UX de Oxphyre Room.

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
- Free, Pro y Business definidos en CLAUDE.md.
- Free: 1 negocio, 1 tour, hasta 5 posiciones.
- Pro: MiDaS en todas las posiciones, más negocios, más posiciones, QR, embed, minimapa, hotspots, analíticas básicas.
- Business: funciones avanzadas, dominio personalizado, usuarios, API, analíticas avanzadas y features futuras.
- Agente IA completo queda como roadmap/post-TFG salvo decisión contraria.

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
- Integrar una API externa obligatoria para el tribunal: Google Maps o Mapbox.
- Documentar roles en la memoria: admin, business_owner, viewer.
- Revisar contraste en dashboard y wizard: inputs, labels y textos secundarios.
- Preparar 1-2 tours demo visualmente impecables antes de la exposición.
- Grabar o sustituir el placeholder del video demo en la landing.
- Revisar responsive en móvil/tablet.
- Revisar SEO técnico final: sitemap, robots, schema, metas, Open Graph.
- Revisar PageSpeed final.
- Pipeline de imágenes: JPG/PNG/WebP + HEIC/HEIF implementados en el pipeline WebP/libvips; flujo iPhone normal validado en servidor; queda pendiente prueba con archivo `.heic` puro sin conversión automática.

### Prioridad media
- QR descargable con analíticas.
- Editor canvas drag & drop.
- Hotspots.
- Minimap real.
- Tutorial/onboarding del editor.
- Tooltips de ayuda en métricas del dashboard.
- Página 404/500 personalizada si no está completa.
- Legal/RGPD: privacidad, términos, cookies.
- PWA: manifest y service worker.
- R2/CDN Fase 2B: visor/dashboard deben usar `public_url` si existe y fallback local si no.
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

Última sesión de implementación:
- Pipeline de imágenes Fase 1.2 cerrado para JPG/PNG/WebP y ampliado a HEIC/HEIF:
  - `ImageProcessingService.php` centraliza validación, conversión y temporales.
  - N/S/E/O se guardan como WebP quality 92.
  - Panorámica `360` se guarda como WebP quality 96.
  - Panorámicas grandes y HEIC/HEIF se procesan con libvips CLI; 360 usa máximo 8192px de ancho.
  - MiDaS procesa JPG temporal separado quality 92.
  - Subida conjunta de 5 imágenes y delete de fotos funcionan.
- Sprint 1 Oxphyre Room Free/base implementado en pantalla de subida y visor público.
- La pantalla de subida muestra panorámica principal obligatoria. Decisión UX posterior: fotos detalle 1-4 opcionales dentro de Oxphyre Room.
- El visor público filtra posiciones sin panorámica y entra siempre en `direction='360'`. Queda pendiente adaptar "Ver detalles" para detalles parciales 1-4.
- Oxphyre Room MVP histórico carga 4 fotos en una escena Three.js tipo Direction Sphere; decisión vigente: permitir detalles disponibles sin exigir 4 y ocultar direcciones N/S/E/O al usuario.
- Corrección visual posterior: CLAHE ya no sobrescribe la imagen visible, `depthUrl` no se expone en el JSON público y la panorámica principal se renderiza como cilindro parcial Three.js con pitch limitado.
- Corrección operativa posterior: `tour-viewer.js` carga con cache-busting para evitar copias antiguas con PSV, y la pantalla de posición permite borrar fotos/panorámica con soft delete y previsualizar el tour público.
- Estado: flujo base y pipeline WebP/libvips validados en servidor; HEIC/HEIF implementado pendiente de prueba real tras deploy; quedan pendientes R2/CDN, QR, limpieza física de soft delete y posibles mejoras de ruido/granulado.

Sesión anterior importante:
- Migración del visor público a Photo Sphere Viewer v4.
- CLAHE integrado en el microservicio Python.
- Correcciones de API PSV v4.
- Visor Three.js manual descartado como solución principal.

---

## Próximo paso recomendado

Siguiente orden recomendado para cerrar antes del TFG:

1. **UX dashboard — bloquear "Ver posición" si falta panorámica `360`**:
   - En la card/listado de posiciones y dentro de la pantalla de gestión/subida, el botón debe aparecer desactivado/no clickable si la posición no tiene panorámica principal.
   - Tooltip/mensaje sugerido: "Sube una panorámica principal para activar esta experiencia Oxphyre Room. Las fotos detalle son opcionales."
2. **Detalles parciales de Oxphyre Room**:
   - Adaptar UI/visor cuando proceda para usar "Foto detalle 1-4" y permitir mostrar 1-4 detalles disponibles sin exigir las 4 fotos.
3. **R2/CDN — Fase 2B**:
   - Fase 0 **validada**: bucket `oxphyre-tour-media` creado, DNS Cloudflare activo, `media.oxphyre.com` Active, WebP público servido correctamente.
   - Fase 1 **validada de forma aislada**: variables R2 en `.env.example`, migración SQL metadata `photos`, `R2StorageService.php` y test CLI real contra R2 completados.
   - Fase 2A **implementada y validada**: nuevas subidas mantienen WebP local y, si `R2_ENABLED=true`, duplican WebP final en R2 con fallback local obligatorio.
   - Fase 2B pendiente: visor/dashboard usan `public_url` si existe y fallback local si no. No marcar como implementada hasta probarlo.
4. Limpieza física de soft delete: borrar WebP/depth asociados cuando proceda. No implementado todavía. Esperar a validar R2 como fuente del visor antes de borrar físico.
5. QR descargable con analíticas. No implementado todavía.
6. Hotspots de navegación entre posiciones. No implementado todavía.
7. Pulido opcional de ruido/granulado si sobra tiempo. No bloqueante.

Micro-pendiente (no bloqueante): probar archivo `.heic` puro de iPhone sin conversión automática de iOS/Safari para confirmar el path HEIC del pipeline. HEIC/HEIF está implementado en código y el servidor soporta libheif/libvips; es verificación, no implementación.

Mantener `positions.active_mode` como campo heredado/compatibilidad; el flujo público actual depende de `photos.direction='360'` para la panorámica principal. `N/S/E/O` quedan como mapeo interno temporal de Foto detalle 1-4.

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
