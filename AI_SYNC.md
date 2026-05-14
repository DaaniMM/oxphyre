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
- Procesado MiDaS en servidor mediante microservicio Flask.
- CLAHE disponible en el microservicio, pero no aplicado a la imagen visible en Sprint 1.
- Visor público Sprint 1 sin Photo Sphere Viewer: panorámica parcial horizontal con pitch limitado.
- Sprint 1 Oxphyre Room Free/base implementado, pendiente de validación manual: panorámica principal obligatoria por posición + Oxphyre Room opcional con 4 fotos.
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
- La imagen visible siempre debe ser la foto original subida por el usuario; MiDaS/CLAHE quedan como procesado interno o futuro, no como textura pública en Sprint 1.

### Sistema de fotos por posición
Sprint 1 implementado, pendiente de validación manual:
- `photos.direction = '360'` define la panorámica principal obligatoria de una posición.
- `photos.direction = N/S/E/O` define las 4 fotos que activan Oxphyre Room como vista opcional de detalle.
- El visor público entra siempre en la panorámica principal.
- Las posiciones sin panorámica no se muestran en el tour público.
- El botón público "Ver detalles" solo aparece si existen las 4 fotos N/S/E/O completas.
- `positions.active_mode` se mantiene como campo heredado/compatibilidad durante la transición, pero ya no debe controlar el nuevo flujo público.

Las panorámicas de smartphone pueden ser parciales, no necesariamente 360° equirectangulares reales. La UI debe explicarlo sin prometer cobertura total cuando no exista.

### Pipeline de imágenes y almacenamiento

Decisión técnica para próximas iteraciones:
- El usuario podrá subir imágenes en formatos habituales de móvil, incluyendo HEIC/HEIF, JPG, PNG y WebP si el servidor lo soporta.
- Oxphyre no debe depender de que el usuario cambie ajustes del móvil ni convierta manualmente archivos.
- EC2 usará las imágenes originales como temporales de procesamiento, no como almacenamiento permanente.
- El formato visible final del visor será WebP optimizado.
- Cloudflare R2/CDN será el destino recomendado para servir imágenes finales del visor y reducir carga, tráfico y almacenamiento persistente en EC2.
- La BD debe guardar la referencia al archivo final WebP y metadatos útiles: formato original, dimensiones originales, dimensiones finales, tamaño final, storage provider/key y estado de procesamiento.
- En la versión TFG/MVP no se conservarán originales de usuario indefinidamente. La conservación de originales queda como posible feature Pro/Business o política temporal futura.
- Si una imagen llega con baja resolución o parece comprimida, la UI debe avisar al usuario de forma clara y no técnica.

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
- Existe `Oxphyre_Room_Free_Flow.md` como especificación funcional propuesta del nuevo flujo Free/base: panorámica principal obligatoria por posición, Oxphyre Room opcional con 4 fotos, hotspots sobre panorámica y botón "Ver detalles" si hay 4 fotos completas. Sprint 1 está implementado y queda pendiente de validación manual antes de convertirlo en decisión oficial o sincronizarlo en `CLAUDE.md`.

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
- Revisar pipeline de imágenes: aceptar HEIC/HEIF de iPhone, convertir a WebP optimizado, detectar imágenes comprimidas y mostrar mensajes de subida más claros.

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
- Evaluar integración de Cloudflare R2/CDN para servir imágenes finales del visor y reducir carga persistente en EC2.

### Deuda técnica
- Unificar métodos duplicados de controllers en BaseController.
- `UserModel::create()` tiene rol `business_free` hardcodeado; refactorizar cuando existan más roles reales.
- Gmail SMTP sirve para TFG, pero en producción migrar a Resend, SendGrid o Mailgun.
- Reimplementar o decidir si se descarta el shader MiDaS/parallax sobre PSV.
- Script local Windows para procesado DPT-Hybrid + CUDA con RTX 3060.
- Revisar si queda documentación antigua diciendo que MiDaS Small/swap/microservicio están pendientes, porque ya se implementaron.

---

## Última sesión de trabajo

Última sesión de implementación:
- Sprint 1 Oxphyre Room Free/base implementado en pantalla de subida y visor público.
- La pantalla de subida muestra panorámica principal obligatoria, Oxphyre Room opcional 4/4 y hotspots como próximo sprint.
- El visor público filtra posiciones sin panorámica, entra siempre en `direction='360'` y muestra "Ver detalles" solo si hay N/S/E/O completas.
- Oxphyre Room MVP carga las 4 fotos en una escena Three.js tipo Direction Sphere, con paneles curvos, arrastre, brújula N/E/S/O y botón "Volver a vista principal".
- Corrección visual posterior: CLAHE ya no sobrescribe la imagen visible, `depthUrl` no se expone en el JSON público y la panorámica principal se renderiza como cilindro parcial Three.js con pitch limitado.
- Corrección operativa posterior: `tour-viewer.js` carga con cache-busting para evitar copias antiguas con PSV, y la pantalla de posición permite borrar fotos/panorámica con soft delete y previsualizar el tour público.
- Estado: pendiente de validación manual visual/funcional antes de actualizar `CLAUDE.md` como decisión oficial.

Sesión anterior importante:
- Migración del visor público a Photo Sphere Viewer v4.
- CLAHE integrado en el microservicio Python.
- Correcciones de API PSV v4.
- Visor Three.js manual descartado como solución principal.

---

## Próximo paso recomendado

Antes de cerrar la validación visual de panorámica, analizar viabilidad del pipeline HEIC/HEIF → WebP optimizado. La prueba real con iPhone confirmó que WhatsApp comprime la panorámica de 16248x3832 a 1600x377, provocando pixelación. El flujo objetivo debe permitir subir desde móvil sin barreras y evitar mensajes técnicos como “MIME inválido”.
Validar manualmente Sprint 1 de `Oxphyre_Room_Free_Flow.md` antes de seguir con otras features:

**Nota operativa:** aunque `CLAUDE.md` y parte de la documentación histórica describen el sistema vigente basado en `positions.active_mode` como selector entre `4photos` y `panoramic`, el flujo que se va a probar ahora es el definido en `Oxphyre_Room_Free_Flow.md`. Sigue siendo propuesta hasta validar Sprint 1, pero es la referencia operativa actual para la siguiente implementación.

- Probar posición sin panorámica: debe aparecer incompleta en dashboard y no mostrarse en visor público.
- Probar panorámica subida: el visor debe entrar en la panorámica principal.
- Probar 1, 2 o 3 fotos N/S/E/O: contador parcial en dashboard y sin botón "Ver detalles" público.
- Probar 4/4 fotos: estado "4/4 · Disponible" y botón "Ver detalles" público.
- Probar abrir Oxphyre Room, arrastrar para mirar y volver a vista principal.
- Comprobar en Oxphyre Room que N = Frente, E = Derecha, S = Fondo y O = Izquierda.
- Revisar responsive básico y consola JS.
- Mantener `positions.active_mode` como lógica actual/compatibilidad durante la transición; el documento propone dejarlo como campo heredado cuando el nuevo flujo esté implementado y validado.
- No actualizar `CLAUDE.md` como decisión oficial hasta validar Sprint 1 funcionando.

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
