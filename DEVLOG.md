---

### Seguridad (nivel producción - un profesor intentará inyecciones explícitamente)
- **Passwords:** `password_hash()` bcrypt, nunca MD5 ni SHA1
- **SQL Injection:** prepared statements en el 100% de las queries, sin excepción
- **XSS:** sanitización de todos los inputs con `htmlspecialchars()` en salida, `strip_tags()` en entrada
- **CSRF:** tokens en todos los formularios, validados en cada POST
- **Sesiones:** regeneración de ID tras login, expiración automática, HttpOnly y Secure flags
- **Rate limiting:** máximo 5 intentos de login, bloqueo temporal con cuenta en BD
- **Verificación email:** token único de un solo uso al registrarse
- **Recuperación password:** token con expiración de 1 hora, invalidado tras uso
- **Headers Nginx:** X-Frame-Options, Content-Security-Policy, HSTS, X-Content-Type-Options
- **Uploads:** validación de tipo MIME real (no solo extensión), tamaño máximo, renombrado aleatorio
- **Variables de entorno:** credenciales en `.env` nunca en el código ni en GitHub
- **localStorage:** solo datos no sensibles (preferencia de idioma, tema día/noche). Nunca tokens de sesión.
- **Sesiones PHP:** datos de autenticación siempre en sesión de servidor, nunca en cliente

---

### UX/UI y estilos
- **Tailwind CSS** para componentes, utilidades y layout
- **CSS custom con variables globales** para: tema día/noche, colores de marca, tipografía, espaciados. Permite cambiar el tema completo modificando unas pocas variables.
- **Modo día/noche:** toggle visible en header, preferencia guardada en localStorage, respeta `prefers-color-scheme` del sistema
- **Animaciones 2026:** scroll-triggered con Intersection Observer, micro-interacciones en botones e inputs, transiciones entre páginas suaves, loading states y skeleton loaders. Moderno pero sin sacrificar PageSpeed.
- **Fuente:** Inter o Plus Jakarta Sans (Google Fonts, subset optimizado)
- **Paleta:** oscura y futurista con acento naranja/ámbar (acorde con Oxphyre)
- **Three.js** integrado directamente en la hero de la landing (no en página aparte)
- **CTAs estratégicos:** posicionados con sentido en cada sección de la landing, con copy orientado a conversión
- **100% responsive** para todos los dispositivos

---

### SEO (puntuado específicamente por el tribunal)
- **Keywords principales:** tours virtuales, tour virtual negocio, visita virtual tienda, tour 360 restaurante, tour virtual gimnasio
- **H1 único por página** con keyword principal
- **H2 y H3** con keywords secundarias y long-tail
- **Keyword density** natural, sin keyword stuffing
- **Sección FAQ** en la landing con preguntas reales que la gente busca en Google, marcadas con schema.org FAQPage
- **Meta tags** completos en todas las páginas: title (max 60 chars), description (max 160 chars), canonical
- **Open Graph** para compartir en redes sociales con imagen y descripción
- **Schema.org** marcado estructurado: SoftwareApplication, FAQPage, Organization
- **sitemap.xml** generado automáticamente con todas las URLs públicas
- **robots.txt** optimizado, bloqueando rutas de dashboard y admin
- **URLs amigables** y descriptivas (sin IDs numéricos en URLs públicas)
- **Core Web Vitals** optimizados: LCP, FID, CLS
- **Imágenes** con atributo alt descriptivo siempre
- **Links internos** estratégicos entre páginas
- **Objetivo:** PageSpeed 100 en mobile y desktop

---

### Performance y PageSpeed 100
- Imágenes en WebP con lazy loading nativo (`loading="lazy"`)
- CSS y JS minificados antes de desplegar
- Gzip activado en Nginx
- Cache headers configurados (assets estáticos con cache largo)
- Google Fonts cargadas con `display=swap` y preconnect
- Three.js cargado de forma diferida, no bloquea el render
- Sin librerías innecesarias, cada KB cuenta
- Critical CSS inline en el head para above-the-fold
- Animaciones con CSS transforms (GPU) nunca con propiedades que causan reflow

---

### Multiidioma
- Español e inglés como idiomas base
- Sistema de traducciones con archivos JSON por idioma (`/lang/es.json`, `/lang/en.json`)
- Selector de idioma visible en header y footer
- Preferencia guardada en localStorage
- URLs con prefijo de idioma: `/es/precios`, `/en/pricing`
- Hreflang tags correctos en el head para SEO internacional
- Arquitectura preparada para añadir más idiomas sin tocar código

---

### Legal y RGPD
- Banner de cookies obligatorio al primer acceso (RGPD = Reglamento General de Protección de Datos europeo)
- Política de privacidad real y completa
- Términos y condiciones
- Todo accesible desde el footer en todas las páginas
- Consentimiento de cookies guardado en BD (tabla cookies_consent)
- Solo se activan cookies de analíticas si el usuario las acepta

---

### PWA (Progressive Web App)
Orientada principalmente a los visitantes que escanean el QR desde móvil.
- `manifest.json` → nombre, icono, colores de la app, modo standalone
- `service-worker.js` → cachea recursos estáticos para carga rápida con mala conexión
- Instalable en móvil como app nativa desde el navegador
- Si en el futuro hay demanda real, se desarrolla app nativa iOS/Android

---

### Sistema de emails transaccionales
- Librería: PHPMailer + Gmail SMTP
- Gratuito, profesional, sin dependencias externas complejas
- Casos de uso: verificación de email, bienvenida, recuperar contraseña, notificación de nuevo contacto
- Instalación: Composer en el backend
- Templates HTML de email con diseño de marca Oxphyre

---

### n8n - Automatización
- Herramienta de automatización visual self-hosted (gratuita)
- Casos de uso previstos: notificación al admin de nuevos registros, alerta de escaneos QR, recordatorio a usuarios inactivos
- ⚠️ IMPORTANTE: verificar que la instancia EC2 t3.micro aguanta n8n junto al resto del stack antes de implementar. Si no hay RAM suficiente, dejar como integración futura documentada.
- Decisión: implementar al final si hay tiempo y recursos

---

### Esquema de base de datos

**users** → id, name, email, password, role, email_verified, verification_token, reset_token, reset_token_expires, created_at, updated_at

**businesses** → id, user_id, name, slug, logo, description, phone, address, plan_id, plan_expires_at, is_active, created_at, updated_at

**plans** → id, name, max_tours, max_positions_per_tour, max_photos_per_position, midas_enabled, minimap_enabled, watermark, analytics_level, price_monthly, created_at

**tours** → id, business_id, title, description, slug, is_published, views_count, created_at, updated_at

**positions** → id, tour_id, name, canvas_x, canvas_y, order_index, created_at

**photos** → id, position_id, direction, filename, original_filename, depth_map_filename, processed, created_at

**hotspots** → id, photo_id, type, title, description, target_position_id, position_x, position_y, created_at

**qr_codes** → id, tour_id, filename, total_scans, created_at

**qr_scans** → id, qr_code_id, ip_address, user_agent, device_type, country, scanned_at

**contact_messages** → id, name, email, subject, message, is_read, created_at

**cookies_consent** → id, session_id, analytics_accepted, created_at

---

### Prioridad de desarrollo
1. Reorganizar estructura de carpetas del proyecto acorde a MVC
2. Arquitectura MVC + router + Front Controller en PHP
3. Esquema BD completo → crear todas las tablas en MySQL
4. Variables de entorno (.env) y configuración base
5. Landing page impactante con Three.js en hero + SEO + FAQ + CTAs
6. Auth completa y segura (registro, verificación email, login, recuperar password)
7. Dashboard base con navegación y layout
8. Onboarding wizard para nuevos negocios
9. Subida de fotos + procesado con Python + MiDaS
10. Editor canvas drag & drop con nodos y conexiones
11. Vista del tour en Three.js con hotspots y minimapa
12. QR descargable con analíticas de escaneos
13. Página de precios con los tres planes
14. Formulario de contacto con PHPMailer
15. Panel de administración (admin)
16. Modo día/noche con CSS variables
17. Multiidioma español/inglés
18. 404/500 personalizadas
19. Legal: cookies, términos, privacidad, RGPD
20. PWA (manifest.json + service-worker.js)
21. Optimización PageSpeed (minificación, WebP, gzip, cache, critical CSS)
22. SEO técnico completo (sitemap.xml, robots.txt, schema.org, hreflang)
23. n8n (solo si hay tiempo y RAM suficiente)

---

## Registro de pasos

### [07/04/2026] Día 1 - Setup inicial

**Paso 1 - Crear repositorio GitHub**
- Nombre: `oxphyre`
- Descripción: `3D virtual tour platform for local businesses`
- Visibilidad: Público
- README: Sí
- Licencia: MIT
- .gitignore: Node (base, se ampliará)
- Motivo: Control de versiones desde el primer día, visible para el tribunal

**Paso 2 - Clonar en local**
- Ruta: `C:\Users\12dan\OneDrive\Escritorio\Desarrollo_Web\DAW\oxphyre`
- Comando: `git clone ... .`
- Motivo: Trabajar en local y sincronizar con GitHub

**Paso 3 - Crear estructura de carpetas**
- `src/` → código fuente del frontend y Three.js
- `src/Experience/` → clases principales de Three.js (patrón Experience)
- `src/Experience/Utils/` → utilidades (Sizes, Time, EventEmitter, Resources)
- `src/Experience/World/` → elementos de la escena 3D
- `public/` → archivos estáticos servidos directamente
- `public/360/` → fotos de los negocios procesadas
- `public/models/` → modelos 3D (.glb) para hotspots
- `public/assets/` → imágenes, iconos, fuentes
- `backend/` → API REST en PHP con patrón MVC
- `backend/api/` → endpoints de la API
- `backend/config/` → configuración BD y constantes
- `backend/models/` → clases PHP que interactúan con MySQL
- `docs/` → documentación y memoria del TFG
- `DEVLOG.md` → este archivo, diario de desarrollo

**Paso 4 - Configurar servidor AWS EC2**
- Instancia: t3.micro, Ubuntu 22.04 LTS, 20GB
- IP elástica asignada: 13.62.93.7 (fija, no cambia aunque se reinicie)
- Stack instalado: Nginx, PHP 8.1 + PHP-FPM, MySQL 8.0, Python 3 + pip + venv
- Motivo IP elástica: garantiza que el QR y los enlaces no se rompan si la instancia se reinicia
- Nginx configurado en /etc/nginx/sites-available/oxphyre
  - Puerto 80 y 443, root en /var/www/oxphyre/public
  - Rutas / → archivos estáticos (Three.js, HTML, CSS)
  - Rutas /api → PHP-FPM
- Repo clonado en /var/www/oxphyre
- Verificado: https://oxphyre.com sirve correctamente

**Paso 5 - Flujo de trabajo establecido**
- Desarrollo en local (VSCode)
- git push desde local a GitHub
- git pull en el servidor (/var/www/oxphyre) para desplegar
- El servidor siempre tiene la versión actualizada de main

**Paso 6 - Base de datos MySQL**
- Creada base de datos: `oxphyre` (utf8mb4)
- Creado usuario: `oxphyre`@`localhost` con permisos completos sobre la BD
- Seguridad aplicada: sin usuarios anónimos, sin acceso root remoto, BD test eliminada

**Paso 7 - Microservicio Python**
- Entorno virtual creado en `/var/www/oxphyre/python-service/venv`
- Librerías instaladas: Flask 3.1.3, Pillow 12.2.0
- Flask: framework para la API REST del microservicio
- Pillow: procesado y optimización de imágenes
- MiDaS (Intel): pendiente de instalar, generará mapas de profundidad reales
- El venv está en .gitignore (no se sube a GitHub, se recrea en cada servidor)

### [09/04/2026] Día 2 - Dominio y HTTPS

**Paso 8 - Dominio oxphyre.com**
- Comprado en IONOS: oxphyre.com + oxphyre.es + oxphyre.org + oxphyre.store por 1€/año
- Renovación automática desactivada en todos (expiran 07/04/2027)
- Dominio principal: oxphyre.com

**Paso 9 - Configuración DNS**
- Registro A @ → 13.62.93.7 (servidor AWS)
- Registro A www → 13.62.93.7
- Los cambios propagaron en minutos

**Paso 10 - HTTPS con Let's Encrypt**
- Certbot instalado en el servidor
- Certificado SSL gratuito para oxphyre.com y www.oxphyre.com
- Renovación automática configurada (expira 08/07/2026, se renueva solo)
- La app es accesible en https://oxphyre.com y https://www.oxphyre.com

### [14/04/2026] Día 3 - Definición completa del producto

**Paso 11 - Definición del sistema de tours y editor visual**
- Decidido el sistema de posiciones múltiples con 4 fotos por posición (N,S,E,O)
- Decidido el uso de MiDaS para profundidad real con IA gratuita
- Definido el editor canvas drag & drop con nodos y conexiones
- Definido el minimapa automático generado desde el canvas
- Actualizado esquema de BD con tablas positions y photos rediseñadas
- Definidos los 3 planes SaaS con sus funcionalidades específicas
- Definida la prioridad de desarrollo completa

**Paso 12 - Claude Code configurado**
- Instalado Claude Code globalmente: `npm install -g @anthropic-ai/claude-code`
- Autenticado con cuenta Claude Pro
- Creado CLAUDE.md → contexto del proyecto para Claude Code (stack, estructura, rutas, planes SaaS)
- Creado AGENTS.md → instrucciones de comportamiento (reglas absolutas, ahorro de tokens, estilo de código, seguridad)
- Motivo: Claude Code leerá ambos archivos al inicio de cada sesión y trabajará autónomamente sin necesidad de explicar el proyecto cada vez

**Paso 13 - Estructura de carpetas MVC definitiva**
- Reorganizada la estructura completa del proyecto para reflejar el patrón MVC
- Eliminado: src/, public/360/, public/models/, backend/api/
- Añadido: backend/controllers/, backend/views/, backend/routes/, backend/middleware/
- Añadido: public/css/, public/js/, public/uploads/, python-service/
- La estructura es idéntica en local y en el servidor AWS

### [16/04/2026] Día 4 - Base de datos completa

**Paso 14 - Creación de todas las tablas MySQL**
- Creadas 12 tablas: plans, users, businesses, tours, positions, photos, hotspots, qr_codes, qr_scans, contact_messages, cookies_consent, login_attempts
- Insertados los 3 planes iniciales: Free (0€), Pro (19€/mes, 182€/año), Business (49€/mes, 470€/año)
- login_attempts con índices en email e ip_address para rate limiting eficiente
- price_yearly añadido a plans para el toggle mensual/anual en la página de precios
- Foreign keys con ON DELETE CASCADE para evitar datos huérfanos
- Precios anuales con ~20% de descuento sobre el mensual (ajustar cuando se definan los planes al 100%)

**Paso 16 - Landing page completa**
- Creados 4 archivos: `backend/views/home.php`, `public/css/main.css`, `public/js/main.js`, `public/js/i18n.js`
- `home.php`: landing completa con 11 secciones (nav, hero, logos, cómo funciona, características, demo, precios, testimonios, FAQ, CTA final, footer). SEO completo: title + meta description + canonical + OG + Twitter Card + Schema.org SoftwareApplication + FAQPage en JSON-LD. H1 único con keyword "tours virtuales 3D". aria-labels en todas las secciones. Sin inline event handlers.
- `main.css`: variables CSS para tema oscuro/claro, glassmorphism con backdrop-filter, animaciones solo con transform+opacity (GPU, sin reflow), responsive hasta 480px, noise texture como SVG data URI
- `main.js`: 8 módulos — tema día/noche (localStorage + prefers-color-scheme), idioma (delega en i18n.js), nav glassmorphism con IntersectionObserver (no scroll listener), menú móvil, animaciones scroll con IntersectionObserver, acordeón FAQ con max-height animado, toggle precios mensual/anual desde data attributes, Three.js (esfera + wireframe dorado + anillo + luces)
- `i18n.js`: traducciones completas ES/EN con ~100 keys, applyLang() recorre data-i18n, initLang() detecta localStorage → prefers-language → fallback ES
- CSP actualizada en index.php: añadido `https://unpkg.com` a script-src para Three.js CDN
- Three.js cargado con defer desde unpkg.com (no bloquea render)

**Paso 15 - Arquitectura base del backend MVC**
- Creados 6 archivos que forman el núcleo del sistema MVC:
  - `public/index.php` → Front Controller: carga .env, configura sesión segura (HttpOnly, Secure, SameSite=Strict, strict_mode), emite headers de seguridad (X-Frame-Options, X-Content-Type-Options, CSP, Referrer-Policy, HSTS en producción) e incluye los archivos base en el orden correcto
  - `backend/config/database.php` → Clase Database con patrón Singleton, PDO con utf8mb4, ERRMODE_EXCEPTION, FETCH_ASSOC y EMULATE_PREPARES=false (prepared statements reales). Credenciales solo desde $_ENV
  - `backend/config/config.php` → Constantes globales: APP_NAME, APP_VERSION, APP_URL, APP_ENV, rutas de sistema (BASE_PATH, BACKEND_PATH, VIEWS_PATH, UPLOADS_PATH), MAX_UPLOAD_SIZE (10MB), ALLOWED_MIME_TYPES, SESSION_LIFETIME, IDs de planes SaaS (PLAN_FREE/PRO/BUSINESS)
  - `backend/routes/web.php` → Mini-router que mapea [método HTTP][URI] → [Controller, método, guard]. Soporta guards 'auth' y 'guest'. Parsea URI con parse_url(), normaliza slashes, carga controllers dinámicamente, responde 404 limpio para rutas no encontradas
  - `.env.example` → Plantilla completa con secciones: BD, aplicación (APP_KEY con instrucción de generación), correo (PHPMailer + Gmail SMTP), Python service. Sin valores reales
  - `backend/middleware/AuthMiddleware.php` → Métodos estáticos check() (bloquea no autenticados → /login, guarda redirect_after_login) y guest() (bloquea autenticados → /dashboard)
- Todos los archivos con comentarios en español explicando QUÉ hace cada sección y POR QUÉ (requisito para TFG)
- Seguridad: sin credenciales hardcodeadas, headers HTTP en cada respuesta, sesión con todos los flags de seguridad, validated session_id type (int > 0)

**Paso 16 - Nginx configurado para MVC + prueba end-to-end**
- Actualizada configuración Nginx: try_files ahora redirige a index.php (Front Controller)
- Eliminado index.html estático que sobreescribía el router
- Creado .env en el servidor con credenciales reales (no en GitHub)
- APP_KEY generada con bin2hex(random_bytes(32))
- Creado HomeController.php → método index() carga la vista home.php
- Creada backend/views/home.php → vista placeholder
- Verificado flujo completo: Nginx → index.php → Router → HomeController → Vista
- https://oxphyre.com responde correctamente con el MVC funcionando

**Decisión - Compartir tours: QR + Embed**
- Los tours no se comparten únicamente por QR
- También mediante código iframe embebible en la web propia del negocio
- El dueño copia un snippet de código y lo pega en su web → el tour aparece directamente
- Elimina la barrera del QR para clientes que ya están visitando la web del negocio
- Pendiente añadir al dashboard: sección "Compartir tour" con QR descargable + código embed copiable


## 2026-04-22 — Rediseño completo landing page

### Lo que se hizo
Reescritura completa de los 4 archivos de la landing:
- `i18n.js` — traducciones ES/EN completas sin testimonios
- `main.css` — sistema de diseño #000000 + acento #FEB354, cursor personalizado, loader, hero two-phase, carrusel 3D, aro de luz S3, bento grid spotlight, precios, FAQ, CTA final
- `main.js` — loader animado (beam + letras + explosión), hero Two-Phase Three.js (esfera BackSide + 300 partículas + drag orbital + frases por ángulo + scroll lerp cameraZ), carrusel autoavance + drag, spotlight características, FAQ acordeón, toggle precios, esfera CTA decorativa
- `home.php` — HTML completo 9 secciones, SEO completo, Schema.org SoftwareApplication + FAQPage

### Estado actual
Landing desplegada en https://oxphyre.com. Pendiente revisar visualmente y ajustar lo que no quede bien.

### Pendientes inmediatos
- Ver resultado en navegador y detectar bugs/ajustes visuales
- Ajustar posición/tamaño esfera en Phase 2 del hero (ensayo-error)
- Actualizar DEVLOG con resultado visual


## 2026-04-22 al 2026-04-29 — Pulido completo de la landing page

### Reescritura arquitectural (fixes.md)
- Canvas Three.js movido a `position:fixed` a nivel de body (`#three-canvas-container`), permitiendo que la esfera persista durante todo el scroll sin recrearse
- Scroll state machine con `lerp()`: la esfera transiciona suavemente entre Phase 1 (dentro, escala 1.4), secciones intermedias (escala 0.3, opacidad 0.2) y CTA final (escala 0→8, explosión de luz)
- Phase 1 bloqueada con `overflow:hidden` en `<html>` durante la experiencia dentro de la esfera; el primer wheel event dispara la transición a Phase 2
- Nav: transparent en Phase 1, glassmorphism (`backdrop-filter: blur(12px)`) al salir
- Carrusel: 8 cards con perspectiva 3D, card central iluminada, autoavance + drag + touch
- Bento grid características: cursor spotlight por proximidad con `--mx`/`--my` CSS vars
- Cards glassmorphism en S3 (Cómo funciona) y S5 (Características)
- Precios: `align-items: end` para que Pro sobresalga; `min-height: 480px` en Free y Business; `visibility:hidden` para totales anuales cuando está en modo mensual
- Esfera CTA decorativa: escena Three.js separada, sin interacción, giro automático

### 10 bugs corregidos (bugs.md — BUG 1-10)
- BUG 1: F5 en cualquier sección mostraba scroll visual al hero → `window.scrollTo(0,0)` + `overflow:hidden` al inicio de `startThreeJS()`
- BUG 2: Loader beam recorría toda la pantalla → travel calculado desde `firstRect.left` hasta `lastRect.right` con fade out al terminar
- BUG 3: Frase CTA "↓ Explora" no era visible → `position:absolute; bottom:48px` fija al fondo de la esfera, con animación `pulse-cta`
- BUG 4: Auto-rotación de la esfera demasiado rápida → reducida de 0.005 a 0.002 rad/frame
- BUG 5: Partículas sin textura, se veían como cuadrados → `createParticleTexture()` con gradiente radial ámbar en canvas 32×32 + `AdditiveBlending`
- BUG 6: Esfera Phase 2 desaparecía al hacer scroll → scroll state machine basada en `scrollY` ranges con lerp, sin destruir la escena
- BUG 7: Preview carrusel con efecto parallax roto → eliminado y sustituido por placeholder hasta BUG 17
- BUG 8: Cards Free y Business más pequeñas que Pro → `min-height: 480px` + `flex:1` en lista de features empuja CTA al fondo
- BUG 9: Elementos FAQ entraban con delay acumulado → `transitionDelay: 0s` para elementos dentro de `#faq`; `rootMargin` reducido a `-20px`
- BUG 10: Partículas Phase 1 visibles en Phase 2 → `innerSphere.visible = false` desde el primer frame de Phase 2

### 9 mejoras adicionales (nuevos_bugs.md — BUG 11-19)
- BUG 11: Antialias activado en Chrome causaba stuttering → `antialias: !isChrome` detectando Chrome con userAgent (excluye Edge y Brave)
- BUG 12: Beam del loader mal posicionado → `getBoundingClientRect()` sobre primer y último span para calcular travel exacto
- BUG 13: Chevron de scroll visible dentro de la esfera → eliminado `#phase1-scroll-hint` del HTML y sus estilos
- BUG 14: Drag Phase 1 giraba la vista → eliminados todos los listeners mousedown/mousemove/mouseup/touch del canvas; solo auto-rotación
- BUG 15: Frases Phase 1 dependían del ángulo de drag → sustituido por `setInterval(3600ms)` secuencial: 0.8s fade in + 2s hold + 0.8s fade out
- BUG 16: F5 mostraba scroll visual antes del loader → script síncrono en `<head>` pone `scrollBehavior:auto` + `overflow:hidden` antes del primer render; se restaura al terminar el loader
- BUG 17: Preview carrusel reemplazado por modal 360° → `#carousel-modal` con overlay + animación scale `cubic-bezier(0.34,1.56,0.64,1)`; abre solo en card activa
- BUG 18: Precios Free y Business de distinto tamaño → `align-items: end` + `min-height: 480px` en no-featured + `flex:1` en lista de features
- BUG 19: Grid características 2-3-2 con 7ª card "Profundidad real con IA" → `nth-child` spans 3/3/2/2/2/3/3; nueva card con icono `cpu`; claves `f7_title`/`f7_desc` en i18n.js

### Visor Three.js 360° inmersivo en modal del carrusel
- `createModalViewer(src)` crea escena Three.js aislada sobre `#carousel-modal-canvas`
- `SphereGeometry(500, 60, 40)` con `MeshBasicMaterial({ side: THREE.BackSide })`
- Textura cargada con `THREE.TextureLoader` + `tex.colorSpace = THREE.SRGBColorSpace` + `LinearFilter` sin mipmaps
- Cámara en `(0,0,0)`, FOV 75; drag mouse y touch modifican `lon`/`lat`; auto-rotación `lon += 0.03` cuando no hay drag
- `renderer.setPixelRatio(window.devicePixelRatio)` sin límite; dimensiones leídas con `getBoundingClientRect()` sobre el contenedor
- `dispose()` completo al cerrar: `cancelAnimationFrame` + `renderer.dispose()` + limpieza de listeners; ningún loop queda activo
- Scroll bloqueado (`document.body.style.overflow = 'hidden'`) mientras el modal está abierto
- Click en card activa → abre modal; click en card lateral → `setCarousel(clickIdx)` directo sin prev/next
- Pill informativa "Click para ver el tour 360°" encima del carrusel con estilo de feature-pill

### Imágenes 360° y CDN
- 8 imágenes panorámicas equirectangulares generadas con Gemini AI (una por sector: restaurante, gimnasio, peluquería, hotel, tienda, inmobiliaria, clínica, coworking)
- Almacenadas en Cloudflare R2 (`pub-b9106d772d3349409c0b98f07f931aa0.r2.dev`) como CDN de assets estáticos
- CSP `img-src` actualizada en `index.php` para permitir el dominio R2
- 8 imágenes card del carrusel convertidas a WebP con Pillow (calidad 85) y servidas localmente desde el servidor EC2
- `data-modal-src` en cada `<article>` del carrusel apunta a R2; `src` de las cards apunta a WebP local

### Estado final de la landing
- Completa visualmente. Todas las secciones implementadas: loader, hero two-phase, carrusel 360°, cómo funciona, demo, características, precios, FAQ, CTA final, footer
- SEO: Schema.org SoftwareApplication + FAQPage, 7 preguntas, canonical, OG, Twitter Card
- i18n: ES/EN completo con ~120 claves
- Enlace "Negocios" añadido al nav desktop y móvil con `scroll-margin-top` en `#carousel-section`
- Ocultado scrollbar durante el loader


## 2026-04-30 — Sistema auth completo + rediseño visual auth pages

### Auth backend (29/04)
- `AuthController.php`: CSRF con `hash_equals()` + regeneración tras cada POST, rate limiting (5/15min login, 3/IP/hora registro), bcrypt `password_hash(cost:12)`, anti-timing attack (dummy hash siempre ejecuta `password_verify`), `session_regenerate_id(true)` tras login, destrucción completa de sesión en logout
- `UserModel.php`: `findByEmail`, `emailExists`, `create` — 100% prepared statements
- `LoginAttemptModel.php`: `record`, `countRecent` (email+IP), `countRecentByIp` (solo IP para registro), `clearOld`
- `web.php` actualizado: 5 rutas nuevas, métodos renombrados a `showLogin`/`showRegister`, POST `/logout` con guard auth, `/register` como alias de `/registro`, guards guest en POST login/registro
- `public/index.php`: loader `.env` sustituido por parser manual con `file()` + manejo de comentarios inline y valores entre comillas, `INI_SCANNER_RAW` eliminado, `putenv()` mantenido

### Auth frontend — rediseño visual (30/04)
- **Esfera Three.js** (`auth-sphere.js`): 4 meshes apilados (glow BackSide size×1.4 respira con `sin()`, wireframe 64 segmentos, core oscuro, núcleo central sólido); `size=2.2`, `fov=50`, cámara en `z=5`; rotación con `THREE.Clock.getDelta()` → `delta×0.08` (Y) y `delta×0.02` (X); parallax vía CSS custom properties `--ox-sx`/`--ox-sy` en el canvas; `ResizeObserver` en lugar de `window.resize`; ningún listener de drag/touch en el canvas
- **Canvas cuadrado** (`100vh × 100vh`, `position:absolute`): garantiza esfera siempre circular; `translate3d(calc(-18vh + var(--ox-sx)), var(--ox-sy), 0)` centra visualmente la esfera; `transition 400ms cubic-bezier(0.22,1,0.36,1)` en el parallax
- **Glow + fade de fusión**: dos divs `pointer-events:none` — glow radial `oklch(0.78 0.16 65/0.18)` centrado en 32vh; fade lineal 20vw en el borde hacia el panel del formulario
- **Layout**: `display:flex` (no grid), `height:100vh`, `overflow:hidden`; login → esfera izquierda; register → `auth-layout--mirror` (row-reverse) esfera derecha, fade invertido
- **Checkbox personalizado**: `appearance:none`, cuadrado 16px, `border 1.5px solid rgba(254,179,84,0.4)`, `:checked` → fondo ámbar + checkmark SVG inline blanco
- **Tooltip botones sociales**: `::before` absoluto centrado arriba, `opacity:0→1` en hover, `cursor:not-allowed`


**Pendiente:** modo claro (implementar cuando modo oscuro esté totalmente cerrado), revisión final responsiva en móvil y tablet, video demo real


## 2026-05-01 — Rediseño definitivo vistas auth (login + register)

Motivo: las iteraciones anteriores no alcanzaban la referencia visual ni la legibilidad requerida para la defensa del TFG. Se rehace desde cero el diseño de las páginas de auth.

### Cambios implementados
- **Variables CSS OKLCH** (`--ox-bg`, `--ox-bg-elevated`, `--ox-border`, `--ox-text`, `--ox-amber`, etc.) — paleta de color perceptualmente uniforme sustituyendo los RGBA anteriores
- **Tipografías**: Instrument Serif (titular serif itálico del panel izquierdo), Inter (UI/form), JetBrains Mono (logo, eyebrow, dominio)
- **Panel izquierdo brand**: layout flex `justify-content:space-between`, tres bloques (logo, central con eyebrow+H2+subtítulo, dominio inferior); Three.js canvas `position:absolute; inset:0; width:100%; height:100%`; tres overlays independientes (glow radial, fade inferior, stage oscuro detrás del H2 para legibilidad)
- **H2 serif + italic**: Instrument Serif 5rem, parte em en `var(--ox-amber-bright)` itálica; separado en `<span>` + `<em>` para compatibilidad con `applyLang()` de i18n
- **Panel derecho formulario**: fondo `--ox-bg-elevated`, bleed ámbar 8rem en borde izquierdo, animación `ox-float-up` en el card interno
- **Botones sociales**: tooltip `::after` CSS puro con `opacity:0→1`, `cursor:not-allowed`, sin JS
- **Checkbox**: `appearance:none`, borde ámbar, `:checked::after` con checkmark via `border-right + border-bottom + rotate(45deg)` (sin SVG externo)
- **Esfera Three.js** (`auth-sphere.js`): id del panel cambiado a `#auth-brand-panel`, tamaño `size=2.0`, `clock.elapsedTime` para la respiración del glow, canvas llena el panel con `camera.aspect = panel.clientWidth / panel.clientHeight`
- **i18n**: namespace `auth` añadido a ES y EN con todas las claves del panel y formulario; vistas cargan `i18n.js` y llaman `initLang()` en `DOMContentLoaded`
- **Móvil**: panel brand oculto (`display:none`), fondo CSS estático con tres capas (radial gradients + SVG grid data URI sin JS ni canvas), formulario como card con `backdrop-filter:blur(12px)`, logo solo visible en móvil dentro del card


## 2026-05-01 — Definición completa de planes SaaS y preparación entrega TFG

### Planes SaaS cerrados definitivamente

Tras análisis exhaustivo se han definido los tres planes con sus funcionalidades exactas. Decisiones clave:

**FREE:** 1 tour, 1 negocio, 5 posiciones. Incluye 1 posición con MiDaS real como crédito de prueba permanente. Las otras 4 usan esfera Three.js con parallax/giroscopio sin profundidad IA. Sin embed, sin minimapa, con marca de agua, URL solo bajo oxphyre.com. Estrategia freemium basada en "efecto disonancia": el contraste entre la posición MiDaS y las planas genera la necesidad de upgrade por sí solo.

**PRO:** MiDaS en todas las posiciones, hasta 5 negocios, 20 posiciones por tour, tours ilimitados. Incluye minimapa, embed/iframe, QR descargable, hotspots informativos, tour guiado automático, compartir en redes, foto de portada Open Graph personalizable, chatbot básico precargado (hasta 60 preguntas/respuestas por palabras clave, sin IA), analíticas básicas con candado visual en features Business, soporte email 48h. Es el plan estrella — aparece remarcado en la landing.

**BUSINESS:** Todo lo de Pro más negocios y posiciones ilimitadas, dominio personalizado con marca blanca total, tours privados con contraseña, historial de versiones, integración Google My Business, traducción automática IA, hotspots enriquecidos (vídeo/reserva/formulario), múltiples usuarios con roles, API access, analíticas avanzadas completas, soporte prioritario 24h con onboarding personalizado. Agente IA completo (OpenClaw/Make/n8n) previsto en roadmap — marcado como "próximamente" en UI hasta su implementación.

### Preparación entrega TFG para el lunes 04/05/2026

Generados dos documentos para la entrega académica:
- Word: Fase 1 (Identificación de necesidades) + Fase 2 (Diseño del proyecto) con datos de mercado reales referenciados (Grand View Research, Allied Market Research, Visiting Media, Google), forma jurídica SL documentada, análisis DAFO implícito en la contextualización, viabilidad económica completa.
- Excel: 5 tablas financieras encadenadas con fórmulas (Plan de Inversiones, Plan de Financiación, Plan de Ingresos y Gastos, Plan de Tesorería, Plan Financiero) con desglose trimestral T1-T4 + Año 1/2/3. Todas las tablas coherentes entre sí mediante referencias directas — ningún valor duplicado a mano entre tablas.


## 2026-05-04 — Sistema de autenticación completo end-to-end

### Archivos creados/modificados
- **`BaseController.php`** (nuevo): clase base con `ensureCsrfToken()` y `flash()` compartidos. `AuthController` y `DashboardController` extienden esta clase eliminando duplicación
- **`UserModel.php`**: añadidos `verifyEmail(token)`, `findByResetToken(token)`, `updatePassword(userId, hash)`, `saveResetToken(email, token, expires)`. `findByEmail` incluye ahora `email_verified`. `create()` acepta `verification_token` e inserta `email_verified=0`
- **`AuthController.php`**: añadidos `showRecover()`, `showReset()`, `verifyEmail()` (GET), `recover()` (POST), `reset()` (POST). `login()` bloquea usuarios con email no verificado. `register()` genera token con `bin2hex(random_bytes(32))` y llama EmailService. `logout()` redirige a `/` en fallo CSRF (antes `/dashboard` — podía causar redirect loop)
- **`EmailService.php`** (nuevo, `backend/services/`): PHPMailer + Gmail SMTP desde `$_ENV`. `sendVerification()` y `sendPasswordReset()` con templates HTML tabla-based (fondo `#0a0800`, acento `#FEB354`). Fallo silencioso con `error_log`
- **`web.php`**: añadidas rutas `GET/POST /recover`, `GET/POST /reset`, `GET /verify`
- **`recover.php`**: formulario email, mismo diseño que login/register
- **`reset.php`**: formulario nueva contraseña con indicador de fuerza, token en hidden input
- **`verify.php`**: página de confirmación éxito/error. `$verified = $verified ?? false` al inicio para compatibilidad con linters estáticos
- **`DashboardController.php`** (nuevo): placeholder con guard auth
- **`dashboard/index.php`** (nuevo): muestra nombre, email, rol, 3 métricas placeholder, form logout con CSRF
- **`auth.css`**: `text-align:center` añadido a `.auth-form-inner`; `.auth-form-inner .form-sub` con márgenes; `.btn-submit` con `display:block; text-decoration:none; text-align:center`; clases `.verify-icon`, `.verify-icon--success`, `.verify-icon--error`

### Flujo completo
1. `/registro` → crea cuenta + envía email verificación → `/login` con flash
2. `/verify?token=xxx` → `verifyEmail()` → `verify.php` éxito/error
3. `/login` → comprueba `email_verified` → `session_regenerate_id(true)` → `/dashboard`
4. `/dashboard` → guard auth → datos de sesión + logout
5. POST `/logout` → CSRF validado → sesión destruida completamente → `/`
6. `/recover` → genera reset_token 1h → email → mismo mensaje siempre (anti-enumeración)
7. `/reset?token=xxx` → token validado en GET antes de mostrar formulario → POST → contraseña actualizada, token invalidado

### Seguridad
- CSRF en todos los POST, `hash_equals()`, token consumido tras cada uso
- Anti timing attack: `password_verify` siempre ejecuta aunque el email no exista
- Rate limiting: 5 intentos login/15min, 3 registros/IP/hora
- Email verificado obligatorio antes de login
- `logout()` fallback CSRF a `/` — evita redirect loop en sesión inconsistente
- Nginx en producción: `fastcgi_param HTTP_X_FORWARDED_FOR ""` y `HTTP_CF_CONNECTING_IP ""` fuerzan `getClientIp()` a usar `REMOTE_ADDR` (no falsificable)

→ Deuda técnica consolidada en sección 'Pendientes y deuda técnica' de CLAUDE.md