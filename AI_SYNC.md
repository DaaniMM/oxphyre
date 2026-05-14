# AI_SYNC.md - Estado actual de Oxphyre

## Objetivo del archivo
Este archivo sincroniza el estado actual del proyecto entre ChatGPT, Claude Web, Claude Code y Codex.

AI_SYNC.md es la fuente rápida de verdad para:
- decisiones activas,
- ideas en debate,
- opciones descartadas,
- problemas pendientes,
- siguiente tarea recomendada.

Si hay contradicción entre una conversación antigua y este archivo, tiene prioridad este archivo.

---

## Estado actual resumido

Oxphyre es un TFG de 2º DAW: SaaS de tours virtuales inmersivos para pequeños negocios locales.

Stack activo:
- PHP 8.1 puro con patrón MVC y Front Controller.
- MySQL 8.0.
- JS vanilla.
- Three.js en landing y efectos visuales.
- Visor público actual en Three.js vanilla: panorámica principal cilíndrica/adaptativa + Oxphyre Room.
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
- Sprint 1 Oxphyre Room Free/base implementado: panorámica principal obligatoria por posición + Oxphyre Room opcional con 4 fotos.
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
Sprint 1 implementado:
- `photos.direction = '360'` define la panorámica principal obligatoria de una posición.
- `photos.direction = N/S/E/O` define las 4 fotos que activan Oxphyre Room como vista opcional de detalle.
- El visor público entra siempre en la panorámica principal.
- Las posiciones sin panorámica no se muestran en el tour público.
- El botón público "Ver detalles" solo aparece si existen las 4 fotos N/S/E/O completas.
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
- Cloudflare R2/CDN sigue pendiente para servir imágenes finales y reducir carga persistente en EC2.
- BD de metadata avanzada pendiente: original_mime, original_width, original_height, final_width, final_height, final_size, storage_provider, storage_key, public_url, processing_status/error_code.
- Política de limpieza de archivos físicos asociados a fotos con soft delete pendiente.
- Ruido/granulado residual en panorámicas interiores: mejora opcional/no bloqueante. La panorámica original de iPhone ya se ve mucho mejor que la versión comprimida por WhatsApp; el ruido restante probablemente viene de captura en interior/poca luz + ruido real de cámara + visualización fullscreen. No aplicar denoise por defecto todavía porque puede suavizar demasiado o generar efecto acuarela.

### Almacenamiento en Cloudflare R2 — Fase 0 en progreso

**Estado (2026-05-14):** Infraestructura Cloudflare configurada. Sin código de aplicación escrito todavía.

**Cloudflare DNS:**
- oxphyre.com conectado a Cloudflare en plan Free mediante "Connect a domain" (NO transfer). IONOS sigue siendo el registrador del dominio.
- Nameservers en IONOS apuntando a `elliot.ns.cloudflare.com` y `julissa.ns.cloudflare.com`.
- Dominio activo/protegido en Cloudflare. Web https://oxphyre.com carga correctamente.
- DNS importados y revisados: A records hacia EC2 (13.62.93.7), MX/TXT/CNAME de correo en DNS only para no romper IONOS mail.

**R2 buckets:**
- `oxphyre-assets` — ya existía; se mantiene exclusivamente para assets de landing, demo e imágenes estáticas. **No se usa para fotos de tours de usuarios.**
- `oxphyre-tour-media` — **creado**; será el bucket para WebP finales reales de posiciones/tours de usuarios.

**Custom domain:**
- `media.oxphyre.com` configurado en R2 con TLS mínimo 1.2. **Estado al cerrar: Initializing** (puede tardar minutos/horas en activarse).
- Hasta que no esté Active, no se puede verificar que las URLs `https://media.oxphyre.com/...` resuelven correctamente.

**Estrategia de almacenamiento:**
- **EC2** = procesamiento temporal: valida, convierte a WebP, genera depth map, sube a R2 y guarda URL en BD.
- **Cloudflare R2** = almacenamiento final y CDN de WebP visibles. Bandwidth gratuito (sin coste de egress).
- **Depth maps:** quedan en EC2 fuera del scope R2 por ahora.
- **Migración de fotos antiguas:** postergada hasta validar R2 en producción.
- **Limpieza física en EC2:** solo después de confirmar que R2 sirve el archivo correctamente.
- **Fallback local obligatorio:** si R2 falla, el WebP queda en EC2 y el visor lo sirve desde `/uploads/` como ahora.
- **Restricción crítica — coste 0€:** free tier R2: 10 GB almacenamiento, 1M escrituras/mes, 10M lecturas/mes, egress gratuito. No activar Workers, Streams ni servicios de pago mientras no haya ingresos.
- **BD:** añadir `storage_provider` (enum: 'local'|'r2'), `storage_key` y `public_url` a `photos`. Migración SQL pendiente.

**No implementado todavía:** código de aplicación, R2StorageService.php, integración en upload/visor, cambios en BD.

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
- `Oxphyre_Room_Free_Flow.md` describe el flujo Free/base ya implementado para Sprint 1: panorámica principal obligatoria por posición, Oxphyre Room opcional con 4 fotos y botón "Ver detalles" si hay 4 fotos completas. Hotspots sobre panorámica siguen pendientes.

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
- Cloudflare R2/CDN para servir imágenes finales del visor y reducir carga persistente en EC2.
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
- La pantalla de subida muestra panorámica principal obligatoria, Oxphyre Room opcional 4/4 y hotspots como próximo sprint.
- El visor público filtra posiciones sin panorámica, entra siempre en `direction='360'` y muestra "Ver detalles" solo si hay N/S/E/O completas.
- Oxphyre Room MVP carga las 4 fotos en una escena Three.js tipo Direction Sphere, con paneles curvos, arrastre, brújula N/E/S/O y botón "Volver a vista principal".
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

1. **R2/CDN — completar Fase 0 y comenzar Fase 1** (siguiente bloque principal):
   - Fase 0 completada: bucket `oxphyre-tour-media` creado, DNS Cloudflare activo, `media.oxphyre.com` configurado.
   - Fase 0 pendiente: verificar que `media.oxphyre.com` pase de Initializing a **Active** antes de continuar.
   - Fase 1 (cuando custom domain esté Active): añadir credenciales R2 a `.env` y documentar en `.env.example`; diseñar migración SQL (`storage_provider`, `storage_key`, `public_url` en `photos`); implementar `R2StorageService.php` (upload, getUrl, delete). Sin tocar `PositionController`, `PhotoModel`, upload.php ni visor todavía.
2. Limpieza física de soft delete: borrar WebP/depth asociados cuando proceda. No implementado todavía. Esperar a validar R2 antes de borrar físico.
3. QR descargable con analíticas. No implementado todavía.
4. Hotspots de navegación entre posiciones. No implementado todavía.
5. Pulido opcional de ruido/granulado si sobra tiempo. No bloqueante.

Micro-pendiente (no bloqueante): probar archivo `.heic` puro de iPhone sin conversión automática de iOS/Safari para confirmar el path HEIC del pipeline. HEIC/HEIF está implementado en código y el servidor soporta libheif/libvips; es verificación, no implementación.

Mantener `positions.active_mode` como campo heredado/compatibilidad; el flujo público actual depende de `photos.direction='360'` para la panorámica principal y N/S/E/O para Oxphyre Room.

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
