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
- Photo Sphere Viewer v4 para el visor público actual, basado en Three.js.
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
- CLAHE para mejora automática de imagen.
- Visor público con Photo Sphere Viewer v4.
- Sistema dual de fotos por posición: 4 fotos normales o panorámica.
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
- El visor público actual usa Photo Sphere Viewer v4.
- No volver al visor Three.js manual salvo problema claro y justificado.
- PSV se usa porque resuelve FOV, touch, giroscopio y panorámicas mejor que el visor propio.
- Three.js sigue formando parte del proyecto: landing, efectos visuales y base interna de PSV.

### Sistema de fotos por posición
Cada posición puede tener dos modos:
- `4photos`: Frente/Fondo/Izquierda/Derecha, guardadas internamente como N/S/E/O.
- `panoramic`: panorámica guardada como direction='360'.

`positions.active_mode` decide qué modo usa el visor.

Las panorámicas de smartphone pueden ser parciales, no necesariamente 360° equirectangulares reales. La UI debe explicarlo sin prometer cobertura total cuando no exista.

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

### Deuda técnica
- Unificar métodos duplicados de controllers en BaseController.
- `UserModel::create()` tiene rol `business_free` hardcodeado; refactorizar cuando existan más roles reales.
- Gmail SMTP sirve para TFG, pero en producción migrar a Resend, SendGrid o Mailgun.
- Reimplementar o decidir si se descarta el shader MiDaS/parallax sobre PSV.
- Script local Windows para procesado DPT-Hybrid + CUDA con RTX 3060.
- Revisar si queda documentación antigua diciendo que MiDaS Small/swap/microservicio están pendientes, porque ya se implementaron.

---

## Última sesión de trabajo

Última decisión documentada:
- 3D Gaussian Splatting queda como dirección comercial definitiva post-TFG de Oxphyre.
- Stack decidido: OpenSplat como herramienta externa sin modificar y SuperSplat Viewer como visor MIT.
- Legalidad revisada: Oxphyre puede mantener privado su código PHP/backend/dashboard si usa OpenSplat como herramienta externa.
- Producción futura: procesado en infraestructura controlada por Oxphyre o GPU bajo demanda.
- Para TFG: no es core obligatorio; solo demo pregenerada si da tiempo.

Sesión anterior importante:
- Migración del visor público a Photo Sphere Viewer v4.
- CLAHE integrado en el microservicio Python.
- Correcciones de API PSV v4.
- Visor Three.js manual descartado como solución principal.

---

## Próximo paso recomendado

Antes de escribir más código, hacer una pasada de sincronización documental:

1. Actualizar CLAUDE.md para reflejar:
   - PSV v4 como visor público actual.
   - MiDaS Small + Flask + systemd + subida de fotos ya funcionando.
   - Swap configurado.
   - `positions.active_mode`, `photos.direction='360'` y soft delete.
   - Panorámicas parciales de smartphone como caso real.

2. Actualizar AGENTS.md:
   - Quitar o suavizar la “Prioridad de desarrollo actual” antigua.
   - Indicar que la prioridad viva está en AI_SYNC.md.

3. Después, elegir una prioridad funcional:
   - Opción recomendada para TFG: `/precios` + API externa Google Maps/Mapbox + documentación de roles.
   - Motivo: son requisitos visibles para tribunal y reducen riesgo académico.

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
- Mantener PHP puro MVC, JS vanilla, Three.js/PSV, MySQL y seguridad fuerte.
