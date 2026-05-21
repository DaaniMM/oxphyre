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


## 2026-05-05 — Auth probado end-to-end en producción

Flujo completo verificado en https://oxphyre.com:
- Registro → email de verificación recibido en bandeja (diseño HTML de marca correcto)
- Clic en enlace → email verificado → redirect a /login
- Login → session_regenerate_id → /dashboard
- Dashboard muestra nombre, email, rol (business_free) y métricas placeholder
- Logout → sesión destruida → redirect a /

PHPMailer funcionando con Gmail SMTP (danimm3097@gmail.com + App Password).
La cuenta digitechfp.com se descartó — SMTP capado por el centro educativo.

→ Deuda técnica consolidada en sección 'Pendientes y deuda técnica' de CLAUDE.md


## 2026-05-05 — Dashboard base con navegación y layout

### Archivos creados/modificados
- **`DashboardModel.php`** (nuevo): 3 métodos con prepared statements — `countTours(userId)`, `countBusinesses(userId)`, `countQrScansLast30Days(userId)`. Queries con JOINs correctos a través de businesses → user_id
- **`DashboardController.php`**: añadido DashboardModel, extrae y pasa a la vista: `$stats` (array con 3 métricas reales), `$userName`, `$userEmail`, `$planLabel` (mapeado desde rol a Free/Pro/Business/Admin), `$userInitial` (primera letra para avatar), `$csrfToken`
- **`dashboard.css`** (nuevo): variables OKLCH idénticas a auth.css, layout grid `240px 1fr` en desktop con sidebar sticky, topbar sticky, main area. Sidebar colapsable en móvil con `transform: translateX(-100%)` + clase `.is-open`
- **`dashboard/index.php`**: reescritura completa — sidebar con nav (Inicio/Mis tours/Negocios/Analíticas/Configuración con Lucide Icons), badge del plan con link "Mejorar →" si no es Business, topbar con hamburguesa + título + avatar con inicial, métricas reales desde BD con notas según plan, CTA "Crea tu primer tour" condicional si tours === 0; JS vanilla para abrir/cerrar sidebar en móvil con overlay y Escape

### Decisiones
- `<style>` inline del placeholder eliminado — externalizado a `dashboard.css`
- Métricas con notas contextuales según plan (Free/Pro/Business) sin hardcodear strings
- Avatar muestra la inicial del nombre desde sesión — sin imagen necesaria
- Sidebar: `position:sticky; height:100vh` en desktop (sin JS), `position:fixed` en móvil (con JS para overlay)

### Deuda técnica resuelta en este paso
- **`<style>` inline en dashboard**: externalizado a `public/css/dashboard.css` con variables OKLCH y diseño completo del layout
- **Métricas hardcodeadas a 0**: conectadas a BD mediante `DashboardModel` con 3 prepared statements reales (tours, negocios, escaneos QR últimos 30 días vía JOINs businesses→user_id)

→ Deuda técnica consolidada en sección 'Pendientes y deuda técnica' de CLAUDE.md


## 2026-05-05 — Onboarding wizard para nuevos negocios

### Archivos creados/modificados
- **`BusinessModel.php`** (nuevo): 3 métodos — `slugExists(slug)`, `countByUser(userId)`, `create(userId, name, slug, description, phone, address)`. Inserta con `PLAN_FREE` (constante de `config.php`), `is_active=1`, timestamps `NOW()`. 100% prepared statements.
- **`BusinessController.php`** (nuevo): extiende `BaseController`. Métodos: `showCreate()` (guard plan Free ≥1 negocio → redirect), `store()` (POST: CSRF, validación, slug único, guard plan, insert → redirect), `showSuccess()` (lee `$_SESSION['created_business']`, elimina tras leer). `go()` con return type `never` (PHP 8.1) para que el análisis estático reconozca el `exit()` y no emita falso positivo sobre `$userId` declarado pero "no usado".
- **`dashboard.css`**: añadidos estilos del wizard — `.wizard-header`, `.wizard-title`, `.wizard-steps`, `.wizard-step`, `.step-bubble`, `.step-label`, `.wizard-connector` (+ variante `.is-done` para la vista de éxito), `.wizard-panel`, `.wizard-card`, `.db-form-group/label/input/textarea/error`, `.slug-row`, `.slug-prefix`, `.char-counter`, `.plan-features-list`, `.plan-feature-item`, `.wizard-nav`, `.wizard-btn-back/next/submit`, `.wizard-success` y sus hijos.
- **`dashboard/business/create.php`** (nuevo): layout completo con sidebar+topbar idéntico al dashboard. Indicador de 3 pasos con burbujas. Panel 1: formulario con nombre (char counter + autogeneración de slug), slug (prefijo `oxphyre.com/`), descripción, teléfono, dirección. Panel 2: lista de features del plan Free con íconos check/x. Un único `<form>` con POST a `/dashboard/business/store` — el cambio paso 1→2 es JS puro sin recarga. Validación client-side en `validateStep1()` antes de avanzar.
- **`dashboard/business/success.php`** (nuevo): paso 3 de éxito. Indicador con pasos 1 y 2 marcados como `is-done` (burbuja verde con check), paso 3 activo. Card centrada con ícono check, nombre del negocio en itálica ámbar, URL pública en `JetBrains Mono`, dos CTA: "Crear mi primer tour" y "Volver al dashboard".
- **`web.php`**: añadidas 3 rutas con guard `auth` — `GET /dashboard/tours/nuevo → BusinessController::showCreate`, `POST /dashboard/business/store → BusinessController::store`, `GET /dashboard/business/created → BusinessController::showSuccess`.

### Flujo completo
1. Dashboard → botón "Crea tu primer tour" → `GET /dashboard/tours/nuevo`
2. Paso 1: rellena nombre + slug (autocompletado) + datos opcionales → JS valida → avanza a paso 2
3. Paso 2: confirma plan Free → `POST /dashboard/business/store`
4. Controller valida CSRF + datos + unicidad del slug + límite plan → inserta en BD → guarda `$_SESSION['created_business']` → redirect a `/dashboard/business/created`
5. `showSuccess()` lee y elimina `$_SESSION['created_business']` → muestra paso 3 con nombre y URL del negocio

### Seguridad
- CSRF validado en POST con `hash_equals()`; token consumido tras uso (`unset $_SESSION['csrf_token']`)
- Guard plan Free en `showCreate()` y `store()`: si ya tiene ≥1 negocio → redirect con flash de error
- `strip_tags()` en todos los campos de texto, `mb_strlen()` para límites, slug con regex `[^a-z0-9-]+`
- Variables extraídas directamente en cada método público (no con `extract()`) — compatibilidad con análisis estático


## 2026-05-05 — Fix: modal límite de negocios en dashboard + pendientes documentados

### Bug corregido
El botón "Empezar ahora" del dashboard llevaba siempre al wizard aunque el usuario ya hubiera alcanzado el límite de negocios de su plan (plan Free = 1 negocio). Flujo incorrecto: el wizard sí lo bloqueaba con flash, pero la UX era mala — el usuario entraba en el wizard, rellenaba datos y solo entonces recibía el error.

### Corrección implementada
- **`DashboardController.php`**: añadida propiedad estática `$businessLimits` (Free=1, Pro=5, Business/Admin=-1). En `index()`, se calculan `$businessLimit` y `$atBusinessLimit` (bool) usando `$stats['businesses']` ya disponible — sin query extra.
- **`dashboard/index.php`**: el botón "Empezar ahora" es ahora condicional — `<a href="/dashboard/tours/nuevo">` si no está al límite, `<button id="btn-limit-trigger">` si está al límite. El modal `#limit-modal` se renderiza solo cuando `$atBusinessLimit` es true (sin nodo DOM innecesario). JS vanilla gestiona apertura/cierre (click trigger, botón X, botón Cerrar, click en overlay, Escape). El modal muestra el plan actual y el límite exacto con enlace a `/precios`.
- **`dashboard.css`**: añadidos `.db-modal-overlay`, `.db-modal`, `.db-modal-close`, `.db-modal-icon`, `.db-modal-title`, `.db-modal-body`, `.db-modal-actions`, `.db-btn-ghost`. Animación de entrada con `scale(0.94) → scale(1)` + `cubic-bezier` spring. Overlay con `backdrop-filter:blur(4px)`.

### Pendientes añadidos a CLAUDE.md
- `/precios`: página propia con las 3 cards de planes para SEO y CTAs de upgrade del dashboard
- Wizard paso 2: 3 planes en cards en lugar del plan Free solo con link discreto
- Dashboard y wizard: contraste insuficiente en inputs/labels/texto secundario — mejorar visibilidad
- CTAs de upgrade: verificar consistencia cuando se cree `/precios`


## 2026-05-05 — Fix modal límite negocios + pendientes UX

### Bug corregido
El modal de límite de negocios tenía dos problemas de implementación:
1. El botón "Empezar ahora" cambiaba de `<a>` a `<button>` visualmente según `$atBusinessLimit` — la card no se veía igual en ambos casos.
2. El modal solo se renderizaba en el DOM cuando `$atBusinessLimit` era true, lo que hacía que `btnClose` y `btnCancel` fueran null si el modal no estaba presente, con riesgo de error JS.

### Corrección
- **`dashboard/index.php`**: botón unificado como `<button type="button" id="btn-start-tour" data-at-limit="0|1">` siempre con el mismo HTML y clase `db-btn-primary`. El modal `#limit-modal` siempre en el DOM (sin `<?php if ($atBusinessLimit): ?>`). JS lee `btnStart.dataset.atLimit`: si `'1'` → abre modal, si `'0'` → `window.location.href = '/dashboard/tours/nuevo'`. Los listeners de cierre (btnClose, btnCancel, overlay, Escape) ya no dependen de que el modal sea condicional.

### Pendientes añadidos a CLAUDE.md
- Dashboard: tooltips de ayuda contextual en métricas (jerarquía del producto para usuario no técnico)
- Editor canvas: tutorial/onboarding en el primer acceso, con botón para volver a verlo


## 2026-05-05 — Soft delete implementado

### Contexto
Borrado lógico para que los datos nunca se eliminen físicamente de la BD. Permite recuperar contenido borrado por error, mantener integridad referencial y cumplir RGPD (derecho al olvido se gestiona por separado con anonimización, no con DELETE).

### BD — columnas añadidas (ejecutar manualmente en servidor via SSH)
```sql
ALTER TABLE businesses ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL;
ALTER TABLE tours      ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL;
ALTER TABLE positions  ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL;
ALTER TABLE photos     ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL;
```
Las tablas `users`, `plans`, `hotspots`, `qr_codes`, `qr_scans`, `contact_messages`, `cookies_consent` y `login_attempts` no tienen soft delete.

### Modelos actualizados

**`BusinessModel.php`**
- `slugExists()`: añadido `AND deleted_at IS NULL` — los slugs de negocios borrados quedan liberados para reutilización
- `countByUser()`: añadido `AND deleted_at IS NULL` — los negocios borrados no cuentan contra el límite del plan
- `softDelete(int $id): void` (nuevo): `UPDATE businesses SET deleted_at = NOW() WHERE id = ?`

**`DashboardModel.php`**
- `countTours()`: añadido `AND t.deleted_at IS NULL AND b.deleted_at IS NULL`
- `countBusinesses()`: añadido `AND deleted_at IS NULL`
- `countQrScansLast30Days()`: añadido `AND t.deleted_at IS NULL AND b.deleted_at IS NULL`

### Regla global documentada en CLAUDE.md
Nueva sección "Regla global: Soft delete" con la norma completa: NUNCA DELETE FROM en businesses/tours/positions/photos, siempre UPDATE SET deleted_at = NOW(), todos los SELECT con deleted_at IS NULL.


## 2026-05-05 — Listado de negocios y tours

### Archivos creados/modificados

**`BusinessModel.php`** — nuevo método `getByUser(int $userId): array`: SELECT id, name, slug, description, phone, address, plan_id, created_at WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC.

**`TourModel.php`** (nuevo) — `getByBusiness(int $businessId): array`: SELECT id, title, description, slug, is_published, created_at WHERE business_id = ? AND deleted_at IS NULL ORDER BY created_at DESC. 100% prepared statements.

**`BusinessController.php`** — añadida propiedad estática `$businessLimits` (mismo que DashboardController, necesaria para el modal de límite en la vista de negocios). Nuevo método `showList()`: llama a `getByUser()`, calcula `$atBusinessLimit` y `$businessLimit`, pasa todo a `dashboard/negocios/index.php`.

**`TourController.php`** (nuevo) — extiende BaseController. `showList()`: carga todos los negocios del usuario con `getByUser()`, añade los tours de cada negocio con `getByBusiness()` (bucle foreach + unset de referencia), y reutiliza `DashboardModel` para las 3 métricas. Pasa `$businesses` (array con clave `tours` añadida) y `$stats` a la vista.

**`web.php`** — 2 nuevas rutas con guard auth: `GET /dashboard/negocios → BusinessController::showList`, `GET /dashboard/tours → TourController::showList`.

**`dashboard.css`** — añadidos bloques: `.db-list-header/title`, `.db-biz-grid/card` (con top, name, url, desc, meta, meta-row, actions), `.db-badge` (variantes plan/published/draft), `.db-btn-secondary`, `.db-stat-bar` (con nums y sep), `.db-tour-section` (con header, title, hr), `.db-tour-grid/card` (con title, desc, footer, date), `.db-empty` (con icon, title, sub).

**`dashboard/negocios/index.php`** (nuevo) — sidebar con "Negocios" activo. Si sin negocios: empty state. Si tiene negocios: header con título + botón "Nuevo negocio →" (data-at-limit para modal/nav). Grid de cards con nombre, URL monospace, descripción opcional, teléfono/dirección con iconos Lucide solo si están rellenos, badge de plan, botones "Gestionar →" y "Ver tours →". Modal de límite siempre en DOM.

**`dashboard/tours/index.php`** (nuevo) — sidebar con "Mis tours" activo. Mini-navbar con 3 estadísticas + botón "Nuevo tour →" (apunta a # pendiente de implementar). Si sin negocios: empty state con link a negocios. Si hay negocios pero 0 tours: empty state. Si hay tours: secciones por negocio (header con nombre + hr) con grid de cards (título, descripción, fecha, badge publicado/borrador). Negocios sin tours muestran "Sin tours aún. Crear tour →".



## 2026-05-07 — Gestión de negocio individual /dashboard/negocios/{slug}

### Routing dinámico
El router tabla-fija no soporta segmentos variables. Se añaden dos bloques `elseif` con `preg_match` en `web.php` antes del 404:
- `GET /dashboard/negocios/([a-z0-9-]+)` → `BusinessController::showManage()`
- `POST /dashboard/negocios/([a-z0-9-]+)/edit` → `BusinessController::update()`

El slug capturado se almacena en `$routeSlug` (global scope de web.php), los métodos del controller lo leen con `global $routeSlug` y sanitizan con `preg_replace('/[^a-z0-9-]/', '', ...)`.

### Archivos creados/modificados

**`BusinessModel.php`**
- `getBySlug(string $slug, int $userId): ?array` — SELECT * WHERE slug = ? AND user_id = ? AND deleted_at IS NULL. Devuelve null si no existe o no pertenece al usuario.
- `update(int $id, string $name, ?string $description, ?string $phone, ?string $address): void` — UPDATE SET name, description, phone, address, updated_at=NOW() WHERE id = ? AND deleted_at IS NULL.

**`BusinessController.php`**
- `showManage()` — extrae slug global, llama getBySlug() (redirect a /dashboard/negocios si no existe), carga tours con TourModel::getByBusiness(), pasa flash de sesión a la vista.
- `update()` — extrae slug global, verifyCsrf con fallback a /dashboard/negocios/{slug}, valida campos, getBySlug() para verificar propiedad, update(), flash success, redirect a /dashboard/negocios/{slug}.

**`dashboard.css`** — nuevos bloques `.db-manage-layout` (grid 1fr 2fr → 1fr en <900px), `.db-manage-card`, `.db-manage-name`, `.db-manage-url-row`, `.db-manage-url`, `.db-manage-copy-btn` (con variante `.copied` verde), `.db-manage-desc`, `.db-manage-meta/meta-row`, `.db-manage-divider`, `.db-manage-actions`, `.db-manage-tours-header/title`.

**`dashboard/negocios/manage.php`** (nuevo) — breadcrumb en topbar (Negocios / nombre). Layout 2 columnas. Columna izquierda: card con nombre, URL monospace + botón copiar (Clipboard API, icono toggle check/copy), descripción, teléfono/dirección con iconos Lucide, badge plan + fecha creación, botón "Editar negocio". Formulario inline oculto con `hidden` attribute — JS toggle con btn-edit/btn-cancel sin recarga de página; inputs pre-rellenos con `htmlspecialchars`. Columna derecha: header "Tours" + botón "Nuevo tour". Si vacío: empty state. Si tours: grid con título, descripción, fecha, badge publicado/borrador, botón "Gestionar" (apunta a /dashboard/negocios/{biz-slug}/tours/{tour-slug}, pendiente de implementar).

### Rediseño layout manage.php (mismo día)
Layout 1fr/2fr reemplazado por patrón header-arriba + contenido-abajo (estándar Vercel/Linear/Stripe). Panel superior full-width con `.db-manage-header` (flex row: info izquierda + botón derecha). Formulario de edición inline `.db-manage-card` full-width con grid 2 columnas (nombre y descripción span-full, teléfono y dirección en paralelo; colapsa a 1 col en <600px). Sección tours `.db-manage-tours-section` full-width debajo. `.db-manage-meta` cambia de flex-column a flex-row para mostrar teléfono y dirección en horizontal.

### Seguridad
- `getBySlug` incluye `user_id = ?` — un usuario no puede ver ni editar negocios de otro aunque conozca el slug
- CSRF validado en update() con fallback correcto al slug dinámico
- `strip_tags()` en todos los campos de entrada


## — Creación de tours

### Migración de rutas
`GET /dashboard/tours/nuevo` apuntaba al wizard de negocio (BusinessController::showCreate). Se separan en dos rutas distintas:
- `GET /dashboard/negocios/nuevo` → `BusinessController::showCreate` (wizard creación de negocio)
- `GET /dashboard/tours/nuevo?negocio={slug}` → `TourController::showCreate` (formulario creación de tour)
- `POST /dashboard/tours/store` → `TourController::store`

Todos los enlaces que apuntaban a `/dashboard/tours/nuevo` como wizard de negocio se actualizaron a `/dashboard/negocios/nuevo`: BusinessController::store() (verifyCsrf + redirects de error × 3), dashboard/index.php (JS), negocios/index.php (empty state + JS), business/success.php (CTA "Crear mi primer tour" — también corregido de `?business={id}` a `?negocio={slug}`).

Los dos enlaces en manage.php que ya apuntaban a `/dashboard/tours/nuevo?negocio={slug}` se mantienen igual (ahora correctos).

### TourModel.php — métodos añadidos
- `countByBusiness(int $businessId): int` — count WHERE business_id = ? AND deleted_at IS NULL
- `slugExistsInBusiness(int $businessId, string $slug): bool` — unicidad de slug dentro del negocio
- `create(int $businessId, string $title, ?string $description, string $slug): int` — INSERT con is_published=0, views_count=0, devuelve lastInsertId()

### TourController.php — métodos añadidos
- `showCreate()`: lee `?negocio` de $_GET, sanitiza, verifica business pertenece al usuario, aplica límites de plan (Free: máx 1 tour total via DashboardModel::countTours; Pro: máx 20 por negocio via TourModel::countByBusiness; Business/Admin: ilimitado), ensureCsrfToken, carga vista.
- `store()`: verifyCsrf inline (fallback /dashboard/negocios), verifica propiedad del negocio, valida title+description, genera slug desde título con `slugify()` PHP (soporte diacríticos), resuelve colisiones añadiendo `-2`/`-3`, inserta tour, flash success, redirect a /dashboard/negocios/{slug}.
- `slugify(string $str): string` — private, normaliza UTF-8, elimina diacríticos ES, convierte a kebab-case.
- `go(string $url): never` — private, igual que BusinessController (pendiente unificar en BaseController).

### tours/create.php (nuevo)
Breadcrumb en topbar: Negocios / {nombre} / Nuevo tour. Formulario con título (char counter, slug autogenerado via JS), slug editable con prefijo `oxphyre.com/{biz-slug}/`, descripción opcional (max 500). Validación client-side en submit. Mismos estilos wizard de dashboard.css.


## — Eliminación de tours y negocios + texto informativo en create

### 1. Texto informativo en tours/create.php
Párrafo con icono `info` de Lucide debajo del campo descripción: "Una vez creado el tour podrás añadir posiciones, subir fotos 360°, configurar hotspots y mucho más." Estilo `var(--ox-text-muted)`.

### 2. Eliminar tour (soft delete)
**`TourModel.php`** — nuevos métodos: `getBySlugAndBusiness(string $slug, int $businessId): ?array` (SELECT * WHERE slug + business_id + deleted_at IS NULL), `softDelete(int $id): void` (UPDATE SET deleted_at=NOW()), `softDeleteByBusiness(int $businessId): void` (UPDATE WHERE business_id + deleted_at IS NULL — para cascade).

**`TourController::delete()`** — extrae tourSlug de `$routeSlug` global, valida CSRF inline, lee `biz_slug` de POST, verifica ownership business→user con `getBySlug`, verifica ownership tour→business con `getBySlugAndBusiness`, soft delete, flash success, redirect a /dashboard/negocios/{bizSlug}.

**`web.php`** — `POST /dashboard/tours/([a-z0-9-]+)/delete` → `TourController::delete` con guard auth.

**`negocios/manage.php`** — botón "Eliminar" con clase `btn-delete-tour` + `data-tour-slug` + `data-tour-title` en cada card de tour. Modal compartido `#modal-delete-tour` con form action y body text poblados dinámicamente por JS al hacer click. CSRF y `biz_slug` en inputs hidden.

### 3. Eliminar negocio (soft delete en cascada)
**`BusinessController::delete()`** — valida CSRF, verifica ownership, cascade: `TourModel::softDeleteByBusiness()` primero, luego `BusinessModel::softDelete()`, flash success, redirect a /dashboard/negocios.

**`web.php`** — `POST /dashboard/negocios/([a-z0-9-]+)/delete` → `BusinessController::delete` con guard auth.

**`negocios/manage.php`** — botón "Eliminar" junto a "Editar" en el header del negocio. Modal `#modal-delete-biz` con form action fija, CSRF en input hidden.

### 4. CSS — dashboard.css
`.db-btn-danger` — botón rojo semi-transparente para acciones destructivas. `.db-modal-icon--danger` — variante del icono modal en rojo. `.db-tour-card-actions` — flex row con gap para los botones de cada card de tour.

### Seguridad
- Ownership verificado en dos niveles: user→business, business→tour — ningún usuario puede borrar recursos ajenos aunque conozca el slug
- CSRF en ambas rutas de borrado
- Soft delete conforme a la regla global de CLAUDE.md (nunca DELETE FROM)
- Slug sanitizado antes de usarse en cualquier query o redirect


## — Gestión individual de tour + posiciones

### Routing con dos parámetros dinámicos
Las rutas `GET /dashboard/negocios/{biz}/tours/{tour}` y `POST .../edit` usan `$routeParams = ['biz' => $m[1], 'tour' => $m[2]]` en lugar de `$routeSlug`. Los métodos del controller los leen con `global $routeParams`.

### Archivos creados/modificados

**`PositionModel.php`** (nuevo) — `getByTour(int $tourId): array`: SELECT * WHERE tour_id = ? AND deleted_at IS NULL ORDER BY order_index ASC.

**`TourModel.php`** — `update(int $id, string $title, ?string $description, bool $isPublished): void`: UPDATE SET title, description, is_published, updated_at=NOW() WHERE id = ? AND deleted_at IS NULL.

**`TourController.php`**
- `showManage()`: extrae `$routeParams` global, verifica ownership user→business→tour, carga posiciones con PositionModel, pasa flash, ensureCsrfToken.
- `update()`: extrae `$routeParams` global, CSRF inline, verifica ownership, valida title (max 100) + description (max 500), is_published desde checkbox POST, llama TourModel::update(), redirect con flash.

**`web.php`** — 2 nuevas rutas con guard auth:
- `GET /dashboard/negocios/([a-z0-9-]+)/tours/([a-z0-9-]+)$` → `TourController::showManage`
- `POST /dashboard/negocios/([a-z0-9-]+)/tours/([a-z0-9-]+)/edit$` → `TourController::update`

**`dashboard.css`** — `.db-pos-grid` (auto-fill 240px), `.db-pos-card` (mismo estilo que tour cards), `.db-pos-card-title/order/actions`.

**`tours/manage.php`** (nuevo) — Breadcrumb 3 niveles: Negocios / {nombre} / {título tour}. Bloque 1: header con título + badge publicado/borrador, URL con copy button, descripción, fecha; botones "Editar" + toggle publicar/despublicar (mini-form con hidden inputs para title/description, is_published invertido) + "Eliminar". Bloque 2: formulario de edición inline con checkbox `is_published`. Bloque 3: grid de posiciones o empty state. Modal de eliminación con form action fija + biz_slug hidden. Botones "Eliminar" en position cards marcados `disabled` + `title="Próximamente"` hasta implementar PositionController.

### Lógica de publicación
El toggle "Publicar/Despublicar" en el header es una mini-form independiente que reutiliza el endpoint `/edit`. Envía title + description actuales como hidden inputs y el valor `is_published` invertido. No requiere un endpoint separado ni JS — funciona como un POST estándar.

## — Upgrade instancia EC2 t3.micro → t3.small

### Motivo
MiDaS (modelo de IA para mapas de profundidad) requiere ~500MB de RAM para cargar el modelo. La instancia t3.micro tenía 914MB totales y solo 148MB disponibles con el stack completo corriendo (Nginx + PHP-FPM + MySQL). Insuficiente para ejecutar MiDaS sin riesgo de OOM (out of memory).

### Cambio realizado
- Instancia parada desde consola AWS
- Tipo cambiado de t3.micro a t3.small (misma zona eu-north-1b, mismo disco EBS de 20GB, misma IP elástica 13.62.93.7)
- Instancia arrancada
- Verificado con free -m: 1910MB totales, 1187MB disponibles

### Comparativa
| | t3.micro | t3.small |
|---|---|---|
| RAM | 1024MB | 2048MB |
| vCPU | 2 | 2 |
| Precio | 0.0108$/hora | 0.0216$/hora |
| Nivel gratuito | ✓ | ✓ |

### Impacto
- Sin cambios en código, configuración Nginx, PHP ni MySQL
- IP elástica mantenida — oxphyre.com sin interrupción prolongada
- Créditos AWS restantes: ~113$ (102 días) — suficiente para ~5000 horas de t3.small
- MiDaS ahora viable con ~1187MB disponibles

## 2026-05-07 — Instalación MiDaS + dependencias Python

### Dependencias instaladas en venv
- torch 2.11.0+cpu — motor de deep learning (Meta/PyTorch)
- torchvision 0.26.0+cpu — procesado de imágenes para PyTorch
- timm 1.0.26 — arquitecturas de redes neuronales preentrenadas
- opencv-python-headless 4.13.0 — visión por computador sin interfaz gráfica

### Modelo descargado
- DPT-Hybrid (Intel MiDaS) — 400MB
- Ruta: /var/www/oxphyre/python-service/dpt_hybrid.pt
- Fuente: huggingface.co/Intel/dpt-hybrid-midas
- Elección: equilibrio óptimo calidad/velocidad en CPU. 
  En producción con GPU se migrará a Depth Anything V2.
  El código soporta el cambio con una sola línea.

### Flujo de procesado previsto
Foto JPG/PNG → OpenCV prepara imagen → PyTorch + timm 
ejecutan MiDaS → mapa de profundidad en escala de grises → 
OpenCV guarda PNG → Three.js usa el resultado para efecto 3D

### Verificación
- torch.load() confirma que el modelo carga correctamente en CPU
- Claves iniciales: dpt.embeddings.cls_token, 
  dpt.embeddings.position_embeddings,
  dpt.embeddings.backbone.bit.embedder.convolution.weight
- Arquitectura DPT confirmada — listo para escribir el microservicio Flask


## — Microservicio Flask MiDaS implementado

### Archivos creados
- `python-service/app.py` — microservicio completo
- `python-service/start.sh` — script de arranque vía gunicorn

### Instalación de gunicorn (ejecutar en servidor)
```bash
cd /var/www/oxphyre/python-service
source venv/bin/activate
pip install gunicorn
chmod +x start.sh
```

### Descripción del microservicio
Flask app con un worker gunicorn en 127.0.0.1:5000. El modelo DPT-Hybrid-MiDaS se carga una sola vez al arrancar (no en cada request). Si existe `python-service/dpt_hybrid.pt` se carga desde ahí; si no, desde la caché de Hugging Face. El servicio no es accesible desde el exterior — solo desde localhost.

### Endpoint POST /process
**Request:** `multipart/form-data` con campo `image` (imagen JPG/PNG, máx 20MB)
**Headers requeridos:** `X-Service-Token: <PYTHON_SERVICE_TOKEN del .env>`

**Response éxito:**
```json
{ "success": true, "depth_map": "<base64 PNG>" }
```
**Response error:**
```json
{ "success": false, "error": "<mensaje>" }
```

**Códigos HTTP:** 200 OK · 400 Bad Request · 401 Unauthorized · 403 Forbidden · 500 Internal Server Error

### Seguridad
- `_is_localhost()`: rechaza 403 cualquier request que no venga de 127.0.0.1 o ::1
- `_token_valid()`: compara X-Service-Token con `PYTHON_SERVICE_TOKEN` env var usando `hmac.compare_digest` (timing-safe). Si el token no está configurado, rechaza siempre.
- `MAX_CONTENT_LENGTH = 20MB`: Flask rechaza automáticamente uploads mayores con 413
- `Image.verify()` + `convert("RGB")`: valida que el archivo es una imagen real, no solo por extensión

### Flujo de inferencia
1. `DPTImageProcessor` prepara la imagen (normalización, resize según modelo)
2. `torch.no_grad()` evita acumulación de gradientes — ahorra memoria en CPU
3. `predicted_depth` interpolado a tamaño original con bicúbica
4. Normalizado a [0, 255] como PNG escala de grises (modo "L")
5. Guardado en `BytesIO` → base64 → JSON

### GET /health
Devuelve `{"status": "ok"}` — accesible públicamente para checks básicos. Desde localhost incluye además el `model` ID.

### Configurar servicio systemd (ejecutar en servidor)
```bash
# 1. Crear el archivo de unidad
sudo nano /etc/systemd/system/oxphyre-midas.service
```
Contenido del archivo:
```ini
[Unit]
Description=Oxphyre MiDaS Python Service
After=network.target

[Service]
Type=simple
User=ubuntu
WorkingDirectory=/var/www/oxphyre/python-service
EnvironmentFile=/var/www/oxphyre/.env
ExecStart=/var/www/oxphyre/python-service/start.sh
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```
```bash
# 2. Activar y arrancar
sudo systemctl daemon-reload
sudo systemctl enable oxphyre-midas
sudo systemctl start oxphyre-midas

# 3. Verificar estado
sudo systemctl status oxphyre-midas
journalctl -u oxphyre-midas -f
```

### Añadir token al .env (servidor)
```bash
# Generar token seguro
python3 -c "import secrets; print(secrets.token_hex(32))"
# Añadir al .env:
echo "PYTHON_SERVICE_TOKEN=<token-generado>" >> /var/www/oxphyre/.env
```
El mismo token debe configurarse en el `.env` para que PHP lo use al llamar al microservicio.


## — Cambio de modelo MiDaS: DPT-Hybrid → Small

### Motivo
DPT-Hybrid necesita ~1800MB de RAM para cargar. El t3.small tiene 1910MB totales; con Nginx + PHP-FPM + MySQL corriendo solo quedan ~1142MB libres — insuficiente. El servidor se cuelga por OOM al intentar cargar Hybrid.

MiDaS Small carga en ~80MB de RAM — perfectamente viable en el servidor.

### Cambios en `python-service/app.py`
- `MODEL_ID` cambiado de `"Intel/dpt-hybrid-midas"` a `"Intel/dpt-small-midas"`
- Eliminadas las 3 líneas del bloque que cargaba pesos locales desde `dpt_hybrid.pt` (`LOCAL_PT`, `os.path.exists`, `torch.load`, `model.load_state_dict`) — ese archivo no existe ni debe existir en el servidor
- Eliminada la constante `LOCAL_PT`
- Docstring y log de carga actualizados para reflejar Small
- La inferencia (interpolación, normalización, base64) no cambia

### Estrategia actualizada
| Entorno | Modelo | RAM uso | Tiempo/foto |
|---|---|---|---|
| Servidor t3.small | MiDaS Small | ~80MB | ~30-60s CPU |
| PC local (demo) | DPT-Hybrid | ~1800MB | ~2-3s GPU |

DPT-Hybrid solo se usa en PC local con GPU para pre-generar los tours de demo. El servidor usa Small para las subidas en directo.



## 2026-05-08 — Reescritura app.py: transformers → torch.hub

### Motivo
La API de Hugging Face `transformers` (DPTForDepthEstimation + DPTImageProcessor) requiere `transformers` instalado y usaba un flujo de inferencia que no coincide con la documentación oficial de MiDaS. La API canónica de MiDaS Small es `torch.hub.load("intel-isl/MiDaS", ...)`, que descarga y cachea el modelo en `~/.cache/torch/hub/` y expone las transformaciones correctas para cada variante del modelo.

### Cambios en `python-service/app.py`
- Eliminados imports `transformers`, `DPTForDepthEstimation`, `DPTImageProcessor`, `cv2` (cv2 se importó por error — nunca se usó)
- Carga del modelo con `torch.hub.load("intel-isl/MiDaS", "MiDaS_small")`
- Transformaciones con `torch.hub.load("intel-isl/MiDaS", "transforms").small_transform`
- Flujo de inferencia: PIL → NumPy RGB → `transform(img_np)` → `midas(input_batch)` → interpolar → normalizar → PNG base64
- `DEVICE = torch.device("cpu")` explícito — el servidor no tiene GPU
- Toda la seguridad se mantiene intacta: localhost check, hmac token, MAX_CONTENT_LENGTH, PIL verify
- `/health` devuelve `"model": "MiDaS_small"` en lugar del MODEL_ID anterior


## — Flujo completo de subida de fotos y procesado MiDaS

### Archivos creados/modificados

**`PositionModel.php`** — añadidos: `getByIdAndTour(int $id, int $tourId): ?array` (ownership check), `countByTour(int $tourId): int` (límite plan), `create(int $tourId, string $name, int $orderIndex): int`, `softDelete(int $id): void`.

**`PhotoModel.php`** (nuevo) — `getByPosition(int $positionId): array` y `create(...)` con 6 campos. `processed=true` solo cuando MiDaS generó el depth map. `depth_map_filename` vacío si falló el procesado.

**`MiDaSService.php`** (nuevo, `backend/services/`) — `process(string $imagePath): ?string`. Usa cURL multipart con `CURLFile` para enviar la imagen al microservicio Flask en `127.0.0.1:5000`. Header `X-Service-Token` desde `$_ENV['PYTHON_SERVICE_TOKEN']`. Timeout 120s. Fallo silencioso con `error_log` — devuelve null si cURL falla, HTTP ≠ 200, o `success !== true`. SSL verify desactivado (conexión localhost).

**`PositionController.php`** (nuevo) — 4 métodos:
- `showCreate()`: verifica user→business→tour, carga vista
- `store()`: CSRF, valida nombre, verifica ownership, aplica límite de plan (Free 5, Pro 20, Business ilimitado), inserta con `order_index = count + 1`, redirect al tour
- `showUpload()`: verifica user→business→tour→position, carga fotos existentes por dirección (`$photosByDir`), carga vista
- `upload()`: CSRF, verifica ownership completa, crea directorio `uploads/{position_id}/`, para cada dirección válida: valida MIME real con `finfo`, valida tamaño, rename con `uniqid()`, mueve archivo, llama `MiDaSService::process()`, guarda PNG del depth map si hay base64, inserta en `photos`

**`web.php`** — 4 nuevas rutas auth: `GET /dashboard/posicion/nueva`, `POST /dashboard/posicion/store`, `GET /dashboard/posicion/upload`, `POST /dashboard/posicion/upload`.

**`dashboard.css`** — `.db-upload-grid` (2 columnas → 1 en <600px), `.db-upload-zone` (dashed border, `.has-file` variante verde sólido), `.db-upload-preview` (aspect-ratio 2:1), `.db-upload-preview-placeholder`, `.db-upload-input` (oculto), `.db-upload-btn`.

**`position/create.php`** (nueva) — breadcrumb 4 niveles, formulario con nombre de posición y texto informativo.

**`position/upload.php`** (nueva) — breadcrumb 4 niveles, grid 2x2 con zonas de upload (N/S/E/O). Cada zona muestra foto existente si la hay (con badge "IA ✓" o "Sin IA"). Preview client-side con FileReader API. Botón de submit se deshabilita durante el procesado con texto "Procesando con IA...". Hidden inputs: `position_id`, `biz_slug`, `tour_slug`, `csrf_token`.

**`tours/manage.php`** — 3 links de posiciones actualizados de `#` a rutas reales: "Añadir posición" → `/dashboard/posicion/nueva?negocio=&tour=`, "Añadir primera posición" → misma ruta, "Gestionar" en position cards → `/dashboard/posicion/upload?position=&negocio=&tour=`.

### Seguridad
- Ownership verificado en cadena completa: user→business→tour→position en cada operación
- MIME real validado con `finfo(FILEINFO_MIME_TYPE)` — nunca la extensión
- `uniqid()` para nombres de archivo — oculta nombres originales y evita colisiones
- `MAX_UPLOAD_SIZE` de config.php (10MB) aplicado en el controller
- `ALLOWED_MIME_TYPES` de config.php (`image/jpeg`, `image/png`, `image/webp`)
- Token MiDaS desde `$_ENV` nunca hardcodeado
- Fallo silencioso en MiDaS: si falla, foto se guarda sin depth map (`processed=false`) — el tour sigue funcionando

### Verificado en producción
- `curl http://127.0.0.1:5000/health` devuelve `{"device":"cpu","model":"MiDaS_small","status":"ok"}`
- RAM con servicio activo: 534MB usados, 1200MB disponibles
- Swap: 426MB usados de 2047MB — estable
- Solución `trust_repo`: modelo pre-cargado interactivamente desde terminal para poblar caché antes de arrancar como servicio systemd


## — Mejoras UX en vistas de posición y tours

- **`position/upload.php`**: mensaje del header cambiado a "Sube las fotos de cada orientación de tu local (imagen normal o 360°)" — más accesible para usuarios sin conocimiento técnico. Etiquetas de las 4 zonas cambiadas de N/S/E/O a "Frente/Fondo/Izquierda/Derecha"; las claves en BD siguen siendo N/S/E/O sin cambio.
- **`position/create.php`**: añadido texto informativo con icono `info` bajo el subtítulo del wizard explicando qué es una posición con ejemplos concretos (entrada, barra, terraza).
- **`tours/index.php`**: añadido botón "Gestionar →" en cada card de tour de las secciones agrupadas por negocio, enlazando a `/dashboard/negocios/{biz-slug}/tours/{tour-slug}`.
- **`PositionController::upload()`**: directorio de destino construido con `$positionId = (int) $position['id']` (del registro verificado, no del input GET) con trailing slash — `UPLOADS_PATH . '/' . $positionId . '/'`. La `$destPath` se forma sin doble barra: `$uploadDir . $filename`. Garantiza que el directorio se crea antes del primer `move_uploaded_file()` usando el ID real de la posición, no el parámetro sin sanitizar.

## — Subida de fotos + procesado MiDaS funcionando en producción

### Flujo verificado end-to-end
1. Usuario sube hasta 4 fotos por posición (Frente/Fondo/Izquierda/Derecha)
2. PHP valida MIME real con finfo (nunca la extensión) — acepta jpeg, png, webp
3. Crea subdirectorio public/uploads/{position_id}/ si no existe
4. Guarda foto con nombre aleatorio uniqid()
5. Llama a MiDaSService que hace cURL al microservicio Flask en 127.0.0.1:5000
6. Flask procesa con MiDaS Small y devuelve mapa de profundidad en base64
7. PHP decodifica el base64 y guarda depth_{filename}.png en el mismo directorio
8. Inserta registro en tabla photos con processed=1

### Verificado en BD
4 fotos con processed=1 y depth_map_filename relleno en position_id=1.

### Bugs corregidos durante la implementación
- Directorio uploads/{position_id}/ no se creaba → añadido mkdir() antes de move_uploaded_file()
- PHP-FPM (www-data) sin permisos en uploads/ → sudo chown -R www-data:www-data public/uploads/
- Log temporal de debug eliminado del controller tras verificación

### Estado del microservicio MiDaS
- Corriendo en 127.0.0.1:5000 con systemd (arranque automático)
- Modelo: MiDaS Small (~80MB en caché ~/.cache/torch/hub/)
- RAM con servicio activo: ~534MB usados, ~1200MB disponibles
- Swap 2GB configurado como colchón de seguridad

→ Siguiente paso: visor Three.js del tour

## — Visor público Three.js del tour

### URL pública
`GET /tour/{biz-slug}/{tour-slug}` → sin guard auth, acceso libre. Responde 404 si el tour no está publicado (`is_published=0`) o no existe.

### Archivos creados/modificados

**`BusinessModel.php`** — añadido `getBySlugPublic(string $slug): ?array` — igual que `getBySlug` pero sin filtro `user_id`, necesario para acceso público al visor.

**`TourModel.php`** — añadido `getBySlugAndBusinessPublic(int $bizId, string $slug): ?array` — filtra `is_published=1` además del `deleted_at IS NULL` estándar.

**`TourController.php`** — añadidos `showPublic()` y `serve404()`:
- `showPublic()`: extrae slugs de `$routeParams`, busca negocio + tour (métodos Public), determina features por `plan_id` (>= PLAN_PRO → MiDaS + minimapa, <= PLAN_FREE → watermark), carga posiciones con `PositionModel::getByTour()` y fotos con `PhotoModel::getByPosition()`, construye `$tourData` con URLs y depth URLs, pasa a vista.
- `serve404()`: responde 404 con vista `/errors/404.php` si existe, fallback inline. Tipo de retorno `never`.

**`web.php`** — ruta pública añadida antes del bloque 404: `elseif preg_match #^/tour/([a-z0-9-]+)/([a-z0-9-]+)$# → TourController::showPublic`. Sin `AuthMiddleware::check()`.

**`backend/views/tour.php`** (nuevo) — vista pública full screen sin sidebar: canvas, loading overlay, fade overlay, header (negocio + título), barra de posiciones en el fondo, botón giroscopio, marca de agua (solo Free), minimapa placeholder (solo Pro/Business). `TOUR_DATA` inyectado como JSON con `JSON_HEX_TAG | JSON_HEX_AMP`.

**`public/css/tour.css`** (nuevo) — `body overflow:hidden`, canvas `position:fixed inset:0`, barra de posiciones con glassmorphism + backdrop-filter, punto ámbar activo con glow, botón giroscopio oculto en `pointer:fine` (desktop), watermark semitransparente esquina inferior izquierda.

**`public/js/tour-viewer.js`** (nuevo):
- Renderer con `pixelRatio min(dpr, 2)` para no saturar GPU móvil
- `SphereGeometry(500, 60, 40)` con `side: BackSide` — cámara dentro mirando hacia afuera
- `standardMat` (MeshBasicMaterial) para plan Free / fotos sin depth map
- `midasMat` (ShaderMaterial) para Pro/Business con depth map disponible: desplaza UV por `u_shift * depth * 0.035` creando parallax 3D; shift calculado con EMA (factor 0.85) sobre el delta de lon/lat frame a frame
- Carga de posición: fade negro → `loadTexture()` async → elige material según features + `photo.processed` → fade out
- Drag mouse + drag touch con `passive:false` para bloquear scroll nativo
- Giroscopio: botón togglable, pide permiso en iOS 13+ con `DeviceOrientationEvent.requestPermission()`; beta−90 → lat, −alpha → lon
- Auto-rotación `lon += 0.03` por frame cuando no hay drag ni giroscopio
- `camera.target` recalculado cada frame desde lon/lat con coordenadas esféricas estándar

### Seguridad
- Tours no publicados (is_published=0): 404 — nunca se puede forzar la URL para ver borradores
- `json_encode` con `JSON_HEX_TAG | JSON_HEX_AMP` — previene XSS en la inyección de TOUR_DATA
- URLs de archivos construidas en el controller (sin user input), solo expone `/uploads/{id}/{filename}` que ya valida ownership en la subida

### Features por plan en el visor
| Feature | Free | Pro | Business |
|---|---|---|---|
| Esfera 360° navegable | ✓ | ✓ | ✓ |
| Profundidad MiDaS (parallax) | — | ✓ | ✓ |
| Minimapa | — | ✓ (placeholder) | ✓ (placeholder) |
| Marca de agua Oxphyre | ✓ | — | — |

→ Siguiente paso: editor canvas drag&drop o QR descargable

## 2026-05-08 — Decisión: sistema de subida de fotos dual por posición

Tras debate exhaustivo se establece definitivamente cómo funciona la subida
de fotos por posición y el visor:

### Decisión
El usuario puede subir dos tipos de foto por posición:
- **4 fotos normales** (Frente/Fondo/Izquierda/Derecha): más accesible, 
  cualquier smartphone. Maricarmen puede hacerlo sin instrucciones técnicas.
- **1 foto panorámica 360° equirectangular**: mejor resultado visual si se 
  hace correctamente. Requiere modo panorama del móvil o cámara 360°.

Puede tener ambas subidas simultáneamente. Un toggle "Activo" determina 
cuál usa el visor. Se guarda en BD como positions.active_mode.

### Comportamiento del visor
- Modo 4 fotos: el visor cambia entre foto N/S/E/O según la dirección 
  que mira el usuario, con transición suave entre ellas.
- Modo panorámica: la foto equirectangular se mapea completa en la esfera, 
  cobertura 360° continua sin saltos.
- Pro/Business: MiDaS aplica depth map en ambos modos.
- Free: fotos planas sin depth map, con marca de agua.

### Cambios en BD necesarios
ALTER TABLE positions ADD COLUMN active_mode 
ENUM('4photos','panoramic') NOT NULL DEFAULT '4photos';
La tabla photos usa direction='360' para la panorámica — sin cambio de estructura.

### UX de la pantalla de subida
Toggle arriba: "4 Fotos" | "Panorámica 360°"
Cada sección tiene su grid de upload y su botón "Usar en el visor" 
que marca active_mode en BD.
Tooltip informativo visible al entrar explicando ambas opciones con 
instrucciones claras y sencillas sobre cómo hacer cada tipo de foto.

## — Sistema de fotos dual implementado

### Migración BD (ejecutar manualmente vía SSH)
```sql
ALTER TABLE positions ADD COLUMN active_mode ENUM('4photos','panoramic') NOT NULL DEFAULT '4photos';
```

### Archivos modificados/creados

**`CLAUDE.md`** — añadida sección "Sistema de subida de fotos por posición" con decisión sobre modo dual.

**`PositionModel.php`** — `updateActiveMode(int $id, string $mode): void` — valida que `$mode` sea '4photos' o 'panoramic' antes de ejecutar el UPDATE, previene inyección de valores arbitrarios al ENUM.

**`web.php`** — nueva ruta `POST /dashboard/posicion/set-mode → PositionController::setActiveMode` con guard `auth`.

**`PositionController.php`**:
- `setActiveMode()`: endpoint AJAX que responde JSON. Valida CSRF (sin consumirlo — el usuario puede llamarlo varias veces sin recargar). Verifica ownership completa user→business→tour→position. Llama `PositionModel::updateActiveMode()`.
- `showUpload()`: extrae `$photo360 = $photosByDir['360'] ?? null` y `$activeMode = $position['active_mode'] ?? '4photos'` para pasar a la vista.
- `upload()`: añadido bloque para `photo_360` — misma validación MIME + tamaño que N/S/E/O. Se guarda con `direction='360'`. Nombre único con prefijo `360_` para distinguirlo. `$depthPath` sin doble barra.

**`TourController::showPublic()`** — añadido `'activeMode' => $pos['active_mode'] ?? '4photos'` en el array de cada posición dentro de `$tourPositions`, así el JS lo recibe en `TOUR_DATA`.

**`upload.php`** (rediseño completo):
- Modal de ayuda con instrucciones (4 fotos paso a paso + panorámica). Controlado por `localStorage key='oxphyre_upload_tip_seen'`. Reabierto con botón ?.
- Toggle "4 Fotos" / "Panorámica 360°" (JS puro, sin recarga).
- Sección 4 fotos: grid 2×2 existente + botón AJAX "Usar estas fotos en el visor".
- Sección panorámica: zona única grande (`db-upload-zone-360`) + preview + botón AJAX "Usar panorámica en el visor".
- Un solo `<form>` con todos los campos (photo_N/S/E/O + photo_360); el controller procesa solo los que vienen con UPLOAD_ERR_OK.
- AJAX `setActiveMode()` con `fetch()` + `URLSearchParams`; actualiza el estado visual sin recargar.

**`tour-viewer.js`** (actualización):
- `loadPosition()` bifurca según `pos.activeMode`: panoramic → foto '360', 4photos → foto 'N'.
- Extraído `applyPhoto(photo)` como función reutilizable (evita duplicar lógica MiDaS/standard).
- `getLonDirection(lon)`: mapea lon normalizado (0–360) a N/E/S/O con cuadrantes de 90°.
- `switchDirection(newDir, pos)`: fade 200ms → `applyPhoto()` → fade out. Protegido con `isSwitchingDir` flag + `DIR_COOLDOWN_MS = 800` para evitar cambios rápidos al borde de umbral.
- En `animate()`: solo en modo '4photos', si han pasado 800ms desde el último cambio y la dirección nueva difiere de la actual → llama `switchDirection()`.

**`dashboard.css`** — añadidas al final: `.upload-mode-toggle`, `.upload-mode-btn`, `.upload-section`, `.btn-set-active` (con estado `.is-active`), `.upload-tip-overlay`, `.upload-tip-modal`, `.upload-tip-col`, `.db-upload-zone-360`, `.db-upload-zone-360-preview`.

### Seguridad
- `setActiveMode()`: ownership verificada, CSRF validado (no consumido para AJAX multi-llamada)
- `photo_360`: misma pipeline de validación que N/S/E/O (MIME real con `finfo`, tamaño MAX_UPLOAD_SIZE)
- `active_mode` validado en modelo antes de INSERT (whitelist explícita)

## 2026-05-09 — Decisión arquitectural: migración a Photo Sphere Viewer (PSV) + CLAHE

### Contexto y problema
Tras probar el visor Three.js actual con fotos reales de smartphone (iPhone 12)
se detectaron bugs críticos inaceptables para un producto comercial:
- Imagen gigante/zoom excesivo — FOV mal configurado
- Depth map visible como textura en lugar de la foto original
- Distorsión grave en panorámicas (efecto "pinwheel" en techo y suelo)
- Giroscopio, touch y hotspots implementados a mano con comportamiento incorrecto

Se realizó una sesión completa de análisis y debate el 09/05/2026 evaluando
todas las alternativas posibles.

### Alternativas evaluadas y descartadas

**Panorámica equirectangular completa con smartphone:**
Descartada definitivamente. El iPhone genera imágenes cilíndricas (~270° horizontales),
no equirectangulares reales (360°x180°). El modo gran angular sacrifica calidad de imagen
inaceptablemente. Google Street View app (única solución gratuita) fue eliminada en 2023.
No existe forma de conseguir 360° completo con smartphone sin hardware adicional.

**Cubemap (6 fotos — frente/fondo/izquierda/derecha/techo/suelo):**
Evaluado. Técnicamente completo pero el stitching automático requiere solapamiento del
30% entre fotos que el usuario no hace naturalmente a 90° exactos. Descartado como
opción principal para el TFG. Apuntado como mejora futura en roadmap.

**OpenCV Stitching automático (propuesta Gemini):**
Evaluado. cv2.Stitcher_create(cv2.Stitcher_PANORAMA) requiere fotos con solapamiento
que el flujo actual no garantiza. Error código 2 (paredes lisas sin puntos clave) es
frecuente en locales pequeños. Descartado para TFG. Roadmap post-TFG.

**Visor cilíndrico con truco CSS (propuesta Gemini):**
Evaluado y probado en HTML de prueba. Oculta bordes negros con gradientes CSS y
bloquea rotación vertical a ±10°. No mejora la calidad real de la foto — es un
truco cosmético. Válido como fallback visual para panorámicas parciales pero no
como solución principal.

**Regla permanente establecida en esta sesión:**
NUNCA sugerir cámaras 360° profesionales como solución. El cliente objetivo son
dueños de PYMES con smartphone normal. Sin inversión en hardware adicional.
Esta regla está guardada en memoria permanente de Claude.

### Decisión final: Photo Sphere Viewer (PSV) + CLAHE

**¿Por qué PSV?**
- Librería estándar de la industria para visores 360° web, usada en productos
  comerciales reales (no experimental)
- Basada en Three.js — mismo stack, migración sin cambio radical de arquitectura
- Resuelve de golpe todos los bugs críticos del visor actual
- Soporte nativo de panorámicas incompletas (panoData) — clave para iPhone ~270°
- Plugins nativos ya probados: virtual tour, markers, compass, minimap
- MIT license, activamente mantenido (última versión 2025-2026)

**¿Por qué CLAHE?**
CLAHE (Contrast Limited Adaptive Histogram Equalization) mejora automáticamente
la iluminación y contraste de cada foto que sube el cliente. Especialmente útil
para locales con iluminación desigual (ventanas brillantes + rincones oscuros).
Se aplica en el servidor al subir la foto, antes del procesado MiDaS.
OpenCV ya está instalado en el servidor — sin dependencia adicional.

### Sistema de fotos definitivo

**Opción A — 4 fotos normales (opción principal):**
Frente/Fondo/Izquierda/Derecha con lente 1x (nunca gran angular).
El visor PSV muestra la foto correcta según la dirección que mira el usuario.
Funciona con cualquier smartphone de cualquier gama.

**Opción B — 6 fotos (mejora opcional):**
Igual que A + foto de techo y foto de suelo opcionales.
Si el cliente las sube, el visor las muestra al mirar arriba/abajo.

**Opción C — Panorámica parcial (~270°):**
PSV la muestra con panoData indicando cobertura real — sin distorsión.
El usuario no ve negro ni zonas vacías dentro de la zona cubierta.
Limitación documentada en UI: no cubre los 360° completos.

Las tres opciones coexisten. positions.active_mode determina cuál usa el visor.

### Qué se implementa en esta sesión
- Migración completa del visor a PSV (tour-viewer.js + tour.php + tour.css)
- CLAHE automático en servidor (nuevo endpoint /enhance en app.py +
  método enhance() en MiDaSService.php + integración en PositionController.php)

### Bugs del visor Three.js que PSV resuelve
- Imagen gigante: PSV gestiona FOV correctamente por defecto
- Depth map visible: PSV carga texturas correctamente
- Distorsión en panorámicas: soporte nativo de cropped panoramas con panoData
- Giroscopio/touch: nativos en PSV, sin implementación manual con bugs

→ Deuda técnica actualizada en sección 'Pendientes y deuda técnica' de CLAUDE.md
## — Migración a PSV + CLAHE implementada

### Archivos modificados

**`python-service/app.py`** — añadido endpoint `POST /enhance`:
- Importa `cv2` con try/except (fallo silencioso si OpenCV no disponible)
- Proceso CLAHE: PIL→BGR→LAB→CLAHE canal L→LAB→BGR→RGB→JPEG base64
- Parámetros: `clipLimit=3.0`, `tileGridSize=(8,8)` — mejora perceptible sin artefactos
- Misma seguridad que `/process`: localhost + X-Service-Token

**`MiDaSService.php`** — refactorizado y ampliado:
- Constantes renombradas: `ENDPOINT_PROCESS`, `ENDPOINT_ENHANCE`, `TIMEOUT_PROCESS`, `TIMEOUT_ENHANCE`
- Método privado `callService()` extrae la lógica cURL compartida (DRY)
- `enhance(string $imagePath): ?string` — llama `/enhance`, devuelve base64 JPEG o null
- `curl_close()` eliminado: deprecated en PHP 8.4, GC libera el recurso automáticamente

**`PositionController.php`** — CLAHE integrado en `upload()`:
- Añadido en bucle N/S/E/O y en bloque photo_360: `$miDaS->enhance($destPath)` → si retorna base64, sobreescribe el archivo con la versión mejorada → continúa con `process()` sobre la foto ya mejorada
- Fallo silencioso: si `enhance()` devuelve null, el flujo continúa normalmente con la foto original

**`public/index.php`** — CSP actualizado:
- `cdn.jsdelivr.net` añadido a `script-src` y `style-src` para cargar PSV desde CDN

**`backend/views/tour.php`** — reescrita completamente:
- Eliminado todo el HTML de Three.js manual (canvas, overlays complejos)
- Estructura mínima: `#psv-viewer` + watermark condicional + barra de puntos + botón giroscopio
- PSV cargado desde CDN (Three.js + PSV core standalone)
- TOUR_DATA inyectado con `JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE`

**`public/js/tour-viewer.js`** — reescrito completamente con PSV:
- `PhotoSphereViewer.Viewer` con `navbar:false`, `mousewheel:false`, `zoomSpeed:0`
- `getPhotoUrl(pos, dir)`: bifurca según `activeMode` — panoramic→'360', 4photos→dirección
- `getPanoData(pos)`: retorna `panoData` 4096×2048 para panorámicas, null para 4 fotos
- `getDirectionFromYaw(deg)`: cuadrantes N/E/S/O cada 90° con normalización 0–360
- `position-updated`: detecta cruce de umbral de dirección (solo modo 4 fotos) + flag `isSwitchingPhoto` para evitar llamadas simultáneas a `setPanorama`
- `loadPosition(idx)`: navega entre posiciones con `transition:'fade'`
- Giroscopio: `DeviceOrientationEvent` + `requestPermission` iOS 13+, `viewer.rotate()` con yaw = -alpha

**`public/css/tour.css`** — reescrito:
- Eliminados todos los estilos del visor Three.js anterior
- `#psv-viewer` 100vw×100vh, `.tour-watermark`, `.tour-positions-bar`, `.tour-pos-btn`, `#tour-gyro-btn`

### Pendiente post-migración
- Reimplementar shader MiDaS sobre PSV (efecto parallax con depth map) — ver CLAUDE.md pendientes
- Recomendar al servidor: `sudo systemctl restart oxphyre-midas` tras desplegar app.py

## — Correcciones iterativas de la integración PSV v4

Tras el deploy inicial se detectaron y corrigieron varios errores en el visor público.

### Limpieza de scripts en tour.php
- Eliminada la carga separada de `three.min.js` desde cdn.jsdelivr.net — PSV standalone ya incluye Three.js internamente; cargarlos dos veces generaba conflicto de namespaces.
- Eliminada la carga de Lucide — no se usa en el visor público (solo en el dashboard).
- Eliminado el bloque `lucide.createIcons()` que dependía de Lucide.
- Todos los `<script>` del visor sin atributo `defer` para garantizar el orden de ejecución.

### Migración de PSV v5 a PSV v4
La versión 5 de PSV exige un bundle standalone diferente y su API (`PhotoSphereViewer.Viewer`) no estaba disponible tal como se esperaba en el CDN. Se bajó a v4 que tiene soporte standalone estable en jsDelivr.

URLs finales en `tour.php`:
- CSS: `https://cdn.jsdelivr.net/npm/photo-sphere-viewer@4/dist/photo-sphere-viewer.min.css`
- JS (orden obligatorio): `three@0.147/build/three.min.js` → `uevent@2/browser.min.js` → `photo-sphere-viewer@4/dist/photo-sphere-viewer.min.js`
- `cdn.jsdelivr.net` añadido a `connect-src` en la CSP de `index.php`

### Correcciones de API PSV v4 en tour-viewer.js

**Análisis realizado:** revisión de código + consulta a la documentación oficial en `photo-sphere-viewer-4.netlify.app` + inspección del bundle CDN minificado para confirmar qué expone realmente.

**Bugs corregidos:**

| Línea | Error | Corrección |
|---|---|---|
| Constructor | `new PhotoSphereViewer({})` | `new PhotoSphereViewer.Viewer({})` — el CDN expone un namespace `{}`, la clase está en `.Viewer` |
| Opciones constructor | `pano_data`, `default_long`, `default_lat`, `loading_img` en snake_case | `panoData`, `defaultLong`, `defaultLat`, `loadingImg` en camelCase (API v4) |
| Evento de posición | `'position-changed'` | `'position-updated'` (nombre correcto en v4) |
| Conversión ángulo | `THREE.Math.radToDeg()` | `THREE.MathUtils.radToDeg()` — `THREE.Math` deprecado en Three.js ≥ r130 |
| Giroscopio | `viewer.rotate({ yaw, pitch })` | `viewer.rotate({ longitude, latitude })` — API v4 usa coordenadas esféricas |

**Confirmado por inspección del bundle:** `setPanorama()` devuelve `this.prop.loadingPromise` (Promise válida), por lo que el uso de `.then()` es correcto y no necesitó cambio.

**Error en diagnóstico del agente:** el agente infirió incorrectamente que el constructor era `new PhotoSphereViewer({})` a partir del UMD wrapper. El error en runtime `PhotoSphereViewer is not a constructor` confirmó que `PhotoSphereViewer` es el namespace y `.Viewer` es la clase.

## 2026-05-11 — Roadmap 3D Gaussian Splatting documentado en CLAUDE.md

Sesión de análisis y evaluación de tecnologías para la evolución post-TFG del visor.
Decisión documentada en la nueva sección "## Roadmap post-TFG: 3D Gaussian Splatting" de CLAUDE.md.

**Decisión:** OpenSplat (AGPLv3) para procesado de vídeo → modelo 3D + SuperSplat Viewer (MIT) para renderizado en navegador. Stack 100% open source, sin costes de licencia, uso comercial permitido.

**Conclusión legal:** la obligación AGPLv3 de OpenSplat solo afecta a modificaciones del propio código de OpenSplat. El código de Oxphyre permanece 100% privado al usar OpenSplat como herramienta externa, igual que con MiDaS.

**Hardware:** PC local del desarrollador (RTX 3060) para los tours de demo del TFG. Producción real: instancia GPU AWS G4dn.xlarge bajo demanda (~0.50$/hora), solo se paga al procesar.

**Herramientas descartadas:** Luma AI, Polycam (de pago sin API gratuita), Google Street View (eliminada de stores en 2023), gran angular de smartphone (calidad inaceptable).

Ver CLAUDE.md para el detalle completo: stack técnico, pipeline, tiers de producto, instrucciones de captura y estado actual.

## 2026-05-12 — Coordinación entre IAs añadida a AGENTS.md

Activado Codex para mejorar efectividad y velocidad en producción.
Creado archivo AI_SYNC.md para garantizar contexto entre ClaudeCode y Codex.
Se añadió al final de `AGENTS.md` una sección de coordinación entre IAs.

**Motivo:** dejar claro qué función cumple cada archivo de contexto: `AI_SYNC.md` como fuente rápida del estado actual, `DEVLOG.md` como historial completo y `CLAUDE.md` como contexto general. También se documentó cuándo actualizar `DEVLOG.md` y `AI_SYNC.md` para evitar duplicar información.

##  — Decisión comercial post-TFG sobre 3D Gaussian Splatting

Se refinó la decisión sobre 3D Gaussian Splatting como dirección comercial definitiva post-TFG de Oxphyre.

**Decisión:** OpenSplat se mantiene como herramienta externa AGPLv3 sin modificar, igual que MiDaS. SuperSplat Viewer se confirma como visor MIT para renderizar el resultado en navegador.

**Motivo legal y técnico:** usar OpenSplat como herramienta externa sin modificarlo permite que el código PHP, backend, dashboard y lógica de negocio de Oxphyre permanezcan privados. El valor comercial de Oxphyre no es solo la herramienta open source, sino el producto completo: captura guiada, procesado automático, hosting, visor, QR, embed, analíticas, soporte y UX para PYMES.

**Alcance TFG:** 3D Gaussian Splatting no se implementa como núcleo obligatorio del TFG por tiempo, coste GPU y riesgo de desestabilizar la entrega. Para el TFG solo se contempla una demo pregenerada en el PC local con RTX 3060 si da tiempo.

**Visión comercial:** tras la entrega, Gaussian Splatting queda como la evolución principal para convertir Oxphyre en producto comercial real. El cliente no interactúa con OpenSplat; ve una experiencia de marca tipo "Oxphyre 3D Capture". Los vídeos de clientes se procesan en infraestructura controlada por Oxphyre o GPU bajo demanda.

## — Propuesta candidata de nuevos tiers en Planes_Oxphyre.md

Se creó/guardó `Planes_Oxphyre.md` como documento de propuesta consolidada para redefinir los tiers Free, Pro y Business.

**Estado:** la propuesta queda en evaluación, no cerrada. No sustituye todavía la definición vigente de planes en la documentación principal ni debe aplicarse a código.

**Motivo:** evitar actualizar límites y estrategia comercial principal antes de validar visual y comercialmente el plan Free y confirmar si la segmentación propuesta encaja con el producto.

## — Especificación Oxphyre Room Free/base creada

Se creó `Oxphyre_Room_Free_Flow.md` como especificación funcional propuesta del nuevo flujo Free/base del visor.

**Contenido:** el documento define un flujo con panorámica principal obligatoria por posición, Oxphyre Room opcional con 4 fotos, hotspots sobre la panorámica y botón "Ver detalles" si hay 4 fotos completas.

**Motivo:** sincronizar a Dani, ChatGPT, Claude Web, Codex y Claude Code antes de implementar, evitando que cada IA interprete el flujo Free/base de forma distinta.

**Alcance:** no cambia código y no sustituye todavía `CLAUDE.md` hasta validar Sprint 1 funcionando.

**Próximo paso técnico:** implementar Sprint 1: adaptar pantalla de subida + adaptar visor público al nuevo flujo panorámica principal / Oxphyre Room opcional.

## — Sprint 1 Oxphyre Room Free/base implementado

Se implementó el Sprint 1 definido en `Oxphyre_Room_Free_Flow.md`, sin tocar `CLAUDE.md` ni `Planes_Oxphyre.md`.

**Qué se cambió:**
- La pantalla de subida/gestión de posición deja de presentar la decisión antigua "4 fotos o panorámica".
- La panorámica principal aparece como bloque obligatorio y explica que será la vista base del visitante.
- Oxphyre Room aparece como bloque opcional recomendado con contador 0/4, 1/4, 2/4, 3/4 o "4/4 · Disponible".
- Hotspots de navegación quedan como bloque informativo bloqueado para el siguiente sprint.
- El visor público filtra posiciones sin panorámica y entra siempre en `photos.direction = '360'`.
- El botón "Ver detalles" solo aparece si existen las 4 fotos N/S/E/O completas.
- "Ver detalles" abre una vista MVP de Oxphyre Room con las 4 fotos, arrastre mouse/touch y botón "Volver a vista principal".
- `positions.active_mode` se mantiene en BD y endpoint como compatibilidad, pero ya no controla la UI nueva ni el visor público Sprint 1.

**Archivos tocados:**
- `backend/controllers/PositionController.php`
- `backend/controllers/TourController.php`
- `backend/views/dashboard/position/upload.php`
- `backend/views/tour.php`
- `public/js/tour-viewer.js`
- `public/css/dashboard.css`
- `public/css/tour.css`
- `AI_SYNC.md`
- `DEVLOG.md`

**Pendiente:**
- Validación manual visual y funcional.
- Validaciones avanzadas/checklist de publicación.
- Editor real de hotspots.
- Pulido UX de Oxphyre Room.
- Confirmar responsive móvil/tablet.
- Revisar consola JS en navegador real.

**Cómo probarlo:**
- Crear o usar un tour publicado con varias posiciones.
- Crear una posición sin panorámica y confirmar que no aparece en el visor público.
- Subir panorámica y confirmar que el visor entra en esa vista principal.
- Subir 1, 2 o 3 fotos N/S/E/O y confirmar que no aparece "Ver detalles".
- Subir las 4 fotos N/S/E/O y confirmar que aparece "Ver detalles".
- Abrir Oxphyre Room, arrastrar para mirar alrededor y volver a la vista principal.

**Verificación técnica local:**
- `node --check public/js/tour-viewer.js` correcto.
- No se pudo ejecutar `php -l` porque PHP no está disponible en el PATH local de Windows.

## 2026-05-13 — Corrección visual Sprint 1: imagen original + panorámica adaptativa

Se corrigió la validación visual de Sprint 1 para que Free/base no prometa ni fuerce un 360 real cuando el usuario sube panorámicas parciales de móvil.

**Qué se cambió:**
- `PositionController.php`: se dejó de aplicar CLAHE sobre el archivo subido. La foto visible en dashboard/visor queda como el original del móvil. MiDaS puede seguir generando `depth_map_filename`, pero no altera la imagen pública.
- `TourController.php`: el JSON público del tour ya no expone `depthUrl`; `processed` sigue como dato interno de estado.
- `tour-viewer.js`: la panorámica principal deja de renderizarse como esfera/equirectangular completa. Ahora se muestra en una superficie cilíndrica parcial con drag horizontal, yaw limitado según cobertura estimada por aspecto y pitch bloqueado a ±6°.
- `tour.php`: se retiraron las dependencias de Photo Sphere Viewer para la vista principal; queda Three.js como dependencia del visor y de Oxphyre Room.
- `tour.css`: se añadieron estados de drag/canvas para el contenedor de la panorámica principal.

**Motivo:** evitar contraste artificial, sombras exageradas, efecto túnel/pinchazo al mirar arriba y deformación por tratar una panorámica móvil parcial como 360 equirectangular completo.

**Se mantiene:**
- Oxphyre Room con 4 paneles curvos N/E/S/O.
- Botón "Ver detalles" si hay 4/4 fotos.
- Botón "Volver a vista principal".
- MiDaS en el proyecto como procesado interno/futuro, sin uso visual en Sprint 1.

**Verificación técnica local:**
- `node --check public/js/tour-viewer.js` correcto.
- `rg` confirma que no quedan usos de `PhotoSphereViewer`, `panoData`, `getPanoData` ni `depthUrl` en `backend`/`public`.
- No se pudo ejecutar `php -l` porque PHP no está disponible en el PATH local de Windows.

## 2026-05-13 — Sprint 1: bug PSV residual, borrado de fotos y preview público

Se corrigió el fallo detectado en producción `PhotoSphereViewer is not defined` reforzando el visor Sprint 1 para que no dependa de PSV y forzando cache-busting del JS público con `tour-viewer.js?v=20260513-2`.

**Qué se cambió:**
- `tour-viewer.js`: se protegió `getPositions()` si `TOUR_DATA` no existe y se mantuvo la panorámica principal en Three.js cilíndrico/adaptativo, sin referencias a `PhotoSphereViewer`.
- `tour.php`: el script del visor carga con versión para evitar que producción siga usando una copia cacheada antigua.
- `PhotoModel.php`: `getByPosition()` filtra `deleted_at IS NULL`; añadidos `getByPositionAndDirection()` y `softDeleteByPositionAndDirection()`.
- `PositionController.php`: añadido `deletePhoto()` con CSRF, whitelist de dirección `N/S/E/O/360` y ownership completo usuario → negocio → tour → posición antes de borrar.
- `web.php`: añadido endpoint POST `/dashboard/posicion/photo/delete`.
- `upload.php`: añadidos botones de eliminar para panorámica y fotos N/S/E/O, confirmación JS y botón "Ver tour público" en nueva pestaña.
- `dashboard.css`: estilos para preview y botones discretos de eliminación.

**Decisiones:**
- El borrado de foto es soft delete en BD; no se elimina físicamente el archivo en esta tarea.
- No se reintroduce PSV, depth map/parallax visual ni CLAHE sobre la imagen visible.

**Verificación técnica local:**
- `node --check public/js/tour-viewer.js` correcto.
- `git diff --check` correcto.
- `rg` confirma que no quedan usos de `PhotoSphereViewer`, `photo-sphere-viewer`, `panoData`, `getPanoData` ni `depthUrl` en `backend`/`public`.
- No se pudo ejecutar `php -l` porque PHP no está disponible en el PATH local de Windows.



## 2026-05-14 — UX de previsualización Sprint 1

Se ajustaron accesos de previsualización y textos visibles sin tocar subida, BD, MiDaS ni CLAHE.

**Qué se cambió:**
- `upload.php`: el acceso público de la posición apunta a `?position={id}` y se muestra como "Ver esta posición" cuando el tour está publicado.
- `upload.php`: la confirmación de borrado usa nombres de usuario: panorámica principal, Frente, Fondo, Derecha e Izquierda.
- `tours/index.php`: cada card de tour publicado añade "Ver tour" sin quitar "Gestionar" como acción de edición.
- `tours/manage.php`: el header del tour publicado añade "Ver tour público" y cada posición añade "Ver posición".
- `tour-viewer.js`: lee `?position=`, busca la posición en `TOUR_DATA.positions` y arranca ahí si tiene panorámica; si no, cae a la primera posición válida.
- `tour.php`: cache-busting actualizado a `tour-viewer.js?v=20260514-1`.
- `dashboard.css`: los botones de preview usan borde/acento ámbar como acción secundaria destacada.

**Motivo:** facilitar la validación manual de Sprint 1 desde el dashboard y evitar que el creador vea direcciones internas `N/S/E/O/360`.

## — Ajuste UX de cards de posiciones

Se rediseñaron las cards de posiciones en la gestión de tour sin tocar backend, rutas, visor ni BD.

**Qué se cambió:**
- `tours/manage.php`: la card separa fila superior, título y acciones. El orden queda arriba izquierda y la papelera arriba derecha.
- `dashboard.css`: el grid de posiciones usa cards de 340px cuando hay espacio, la fila superior fuerza `space-between`, la papelera queda visible con borde rojo sutil y las acciones principales se adaptan sin overflow.
- "Ver posición" no parte línea y, en móvil o cards estrechas, los botones pasan a columna y ocupan el ancho completo.

**Motivo:** evitar overflow tras añadir "Ver posición" y mantener una UI limpia para Gestionar, previsualizar y futura eliminación.

## 2026-05-14 — Ajuste neutral de color en panorámica principal

Se corrigió el pipeline de color/render del visor público para que la panorámica principal respete mejor el archivo original subido.

**Qué se cambió:**
- `tour-viewer.js`: añadido `configureNeutralRenderer()` para fijar salida sRGB (`outputColorSpace` si existe, `outputEncoding` en Three r147), `NoToneMapping` y exposición `1`.
- `tour-viewer.js`: añadido `configureNeutralTexture()` para marcar las texturas fotográficas como sRGB sin tocar filtros, material ni geometría.
- `tour.php`: actualizado cache-busting del visor a `tour-viewer.js?v=20260514-3`.

**Motivo:** la textura se estaba declarando como sRGB, pero el renderer no tenía salida sRGB explícita. En Three r147 eso puede producir conversión incompleta de color y hacer que la imagen se vea más oscura o con dominante no deseada frente al archivo original.

**Se mantiene:**
- Sin cambios en geometría, FOV, cobertura angular, yaw/pitch ni navegación.
- Sin Photo Sphere Viewer.
- Sin CLAHE ni mejoras visuales sobre la imagen visible.

**Verificación técnica local:**
- `node --check public/js/tour-viewer.js` correcto.

## 2026-05-14 — Ajuste controlado de drag y cobertura panorámica

Se ajustó solo la interacción horizontal y la estimación de cobertura de la panorámica principal.

**Qué se cambió:**
- `tour-viewer.js`: el drag horizontal principal invierte el signo de `dx`, pasando de `targetYaw - dx * 0.0032` a `targetYaw + dx * 0.0032`.
- `tour-viewer.js`: la cobertura estimada pasa de `clamp(aspect * 68, 130, 285)` a `clamp(aspect * 55, 110, 240)` para evitar sobreestirar panorámicas parciales de móvil.
- `tour.php`: cache-busting actualizado a `tour-viewer.js?v=20260514-4`.

**Motivo:** hacer que el arrastre se sienta natural y reducir la sensación de imagen blanda/estirada sin tocar color, geometría vertical, FOV, pitch ni Oxphyre Room.

**Análisis pendiente separado:**
- Geometría principal actual: `radius = 5.2`, `height = 6.1`, `MAIN_DEFAULT_FOV = 62`, `MAIN_PITCH_LIMIT_DEG = 6`.
- La altura fija podría contribuir a una percepción de imagen grande en panorámicas estrechas; conviene probarlo aparte, por ejemplo con `height` entre `5.0` y `5.5` manteniendo radio/FOV constantes.

**Verificación técnica local:**
- `node --check public/js/tour-viewer.js` correcto.
- `git diff --check` correcto.

## 2026-05-14 — Fase 1 pipeline WebP seguro

Se implementó la primera fase del pipeline de imágenes sin HEIC/HEIF ni R2.

**Qué se cambió:**
- `PositionController.php`: las subidas JPG/PNG/WebP se validan por MIME real y se convierten a WebP visible con GD (`imagewebp`) a calidad 92.
- `PositionController.php`: corregido el bug de WebP permitido pero guardado con extensión `.jpg`; la imagen visible final se guarda como `.webp` en `photos.filename`.
- `PositionController.php`: MiDaS procesa una copia temporal JPG generada durante la conversión, sin tocar ni sobrescribir el WebP visible.
- `PositionController.php`: los temporales internos para MiDaS se eliminan tras el procesado.
- `PositionController.php`: añadidos mensajes friendly para formato no soportado, imagen demasiado grande, error de conversión y baja resolución.
- `PositionController.php`: añadida detección no bloqueante de baja calidad: panorámicas con alto ratio y altura < 700px, y fotos normales con width < 1000 o height < 700.
- `PositionController.php`: añadida comprobación preventiva de memoria antes de decodificar con GD para evitar OOM en EC2.

**Decisiones:**
- No se modifica BD en esta fase; las dimensiones se usan solo para avisos durante el upload.
- No se conserva el original de usuario; solo queda el WebP final visible y el depth map si MiDaS responde.
- CLAHE sigue sin aplicarse a la imagen visible.
- HEIC/HEIF y Cloudflare R2 quedan para fases posteriores.

**Verificación técnica local:**
- `php -l` no disponible en el PATH local de Windows.
- `git diff --check` correcto.

## 2026-05-14 — Recomendación secundaria en avisos de calidad

Se ajustó la UX de los mensajes de subida cuando una imagen se detecta como comprimida o de baja resolución.

**Qué se cambió:**
- `BaseController.php`: `flash()` acepta una línea secundaria opcional sin romper llamadas existentes.
- `PositionController.php`: cuando hay warnings de calidad, añade la recomendación secundaria de Oxphyre sobre evitar WhatsApp, Instagram u otras apps antes de subir fotos.
- `upload.php`: renderiza `flash['secondary']` debajo del mensaje principal con tamaño menor y color suave.

**Motivo:** mantener el éxito/aviso principal claro y añadir una recomendación educativa sin presentarla como error grave.

**Verificación técnica local:**
- `php -l` no disponible en el PATH local de Windows.
- `git diff --check` correcto.

## 2026-05-14 — Límite específico para panorámica principal

Se ajustó el límite interno de subida para permitir panorámicas originales algo más pesadas sin abrir el límite global de fotos.

**Qué se cambió:**
- `PositionController.php`: las fotos normales N/S/E/O mantienen `MAX_UPLOAD_SIZE` de 10MB.
- `PositionController.php`: la panorámica principal `direction='360'` usa un límite específico de 15MB (`PANORAMA_MAX_UPLOAD_SIZE`), coherente con `upload_max_filesize=15M`.
- `PositionController.php`: el log interno de tamaño excedido ahora incluye dirección, tamaño recibido y límite aplicado en bytes.

**Motivo:** una panorámica original de móvil pesaba ~10.48MB y era rechazada por el límite interno de 10MB, aunque PHP/Nginx ya permiten hasta 15-20MB.

**Verificación técnica local:**
- `php -l` no disponible en el PATH local de Windows.
- `git diff --check` correcto.

## 2026-05-14 — Experimento de altura en cilindro panorámico

Se redujo solo la altura vertical del cilindro de la panorámica principal.

**Qué se cambió:**
- `tour-viewer.js`: `createMainPanoramaGeometry()` pasa de `height = 6.1` a `height = 5.3`, manteniendo `radius = 5.2`.
- `tour.php`: cache-busting actualizado a `tour-viewer.js?v=20260514-5`.

**Motivo:** comprobar si una menor escala vertical reduce la percepción de imagen ampliada/pixelada sin tocar color, drag, cobertura horizontal, FOV, pitch ni Oxphyre Room.

**Verificación técnica local:**
- `node --check public/js/tour-viewer.js` correcto.
- `git diff --check` correcto.

## 2026-05-14 — Segundo ajuste de altura en panorámica principal

Se redujo de nuevo solo la altura vertical del cilindro principal para comparar nitidez percibida.

**Qué se cambió:**
- `tour-viewer.js`: `createMainPanoramaGeometry()` pasa de `height = 5.3` a `height = 4.8`, manteniendo `radius = 5.2`.
- `tour.php`: cache-busting actualizado a `tour-viewer.js?v=20260514-6`.

**Motivo:** probar si una escala vertical aún menor reduce la sensación de ampliación/pixelado sin tocar color, drag, cobertura horizontal, FOV, pitch ni Oxphyre Room.

**Verificación técnica local:**
- `node --check public/js/tour-viewer.js` correcto.
- `git diff --check` correcto.

## 2026-05-14 — Fase 1.1 ImageProcessingService

Se extrajo el pipeline local de imágenes desde `PositionController.php` a un servicio dedicado sin cambiar el comportamiento funcional.

**Qué se cambió:**
- `backend/services/ImageProcessingService.php`: nuevo servicio para validar errores de upload, aplicar límites por dirección, detectar MIME real, leer dimensiones, proteger GD frente a imágenes demasiado grandes, convertir JPG/PNG/WebP a WebP visible, generar JPG temporal para MiDaS, detectar baja calidad y devolver metadata estructurada.
- `PositionController.php`: mantiene CSRF, ownership, creación de directorio, bucles de subida, llamada al servicio, llamada a MiDaS, guardado de depth map, `PhotoModel` y construcción del flash final.
- El servicio no escribe en BD ni llama a MiDaS.
- MiDaS sigue usando el JPG temporal y el WebP visible no se sobrescribe.

**Motivo:** mantener controller delgado y preparar el pipeline para la Fase 1.2 sin seguir acumulando lógica central de imagen dentro del controlador.

**Verificación técnica local:**
- `php -l` no disponible en el PATH local de Windows.
- `git diff --check` correcto.

## 2026-05-14 — Prioridad de subida para panorámica principal

Se corrigió el caso en el que al seleccionar N/S/E/O + panorámica en el mismo envío la panorámica podía quedarse sin procesar.

**Qué se cambió:**
- `PositionController.php`: la panorámica `photo_360` se procesa antes que las 4 fotos de Oxphyre Room.
- `upload.php`: revisado; `photo_360` ya estaba dentro del mismo formulario `multipart/form-data` y no requería cambios.

**Motivo:** la panorámica es la vista obligatoria y antes quedaba al final del procesado, después de 4 llamadas potencialmente lentas a MiDaS.

**Verificación técnica local:**
- `php -l` no disponible en el PATH local de Windows.
- `git diff --check` correcto.

## 2026-05-14 — Fase 1.2 libvips para panorámicas grandes

Se añadió soporte de procesamiento con libvips CLI para panorámicas principales grandes sin cargar toda la imagen en GD.

**Qué se cambió:**
- `ImageProcessingService.php`: decide herramienta de procesado según dirección, tamaño y capacidad de GD.
- Fotos N/S/E/O siguen usando GD como antes.
- Panorámicas `360` usan libvips si superan el ancho final recomendado o si GD no puede procesarlas con seguridad.
- Panorámicas grandes se redimensionan a un máximo de 8192px de ancho manteniendo proporción.
- La salida visible sigue siendo WebP; en la prueba posterior quedó fijada en quality 96 para panorámica `360`.
- N/S/E/O siguen en WebP quality 92.
- MiDaS sigue recibiendo un JPG temporal quality 92 separado del WebP visible.
- Los comandos CLI se construyen con `escapeshellarg()` y se comprueba código de salida, existencia y tamaño de archivos generados.

**Motivo:** permitir panorámicas reales de móvil, como 16248x3832, sin tumbar EC2 ni ampliar lógica en `PositionController`.

**Verificación técnica local:**
- `php -l` no disponible en el PATH local de Windows.
- `git diff --check` correcto.

## 2026-05-14 — Prueba de calidad WebP en panorámica principal

Se ajustó solo la calidad del WebP visible de la panorámica principal para comprobar si reduce artefactos/granulado.

**Qué se cambió:**
- `ImageProcessingService.php`: separadas constantes de calidad WebP por tipo de imagen.
- N/S/E/O mantienen WebP calidad 92.
- Panorámica `360` pasa a WebP calidad 96, tanto si la convierte GD como si la convierte libvips.
- JPG temporal para MiDaS se mantiene en calidad 92.

**Motivo:** probar más calidad visual en la panorámica final sin tocar ancho máximo, visor, geometría, color, MiDaS ni BD.

**Verificación técnica local:**
- `php -l` no disponible en el PATH local de Windows.
- `git diff --check` correcto.

## 2026-05-14 — Sincronización de documentación del pipeline de imágenes

Se sincronizó la documentación de estado tras cerrar el bloque WebP/libvips.

**Qué se cambió:**
- `AI_SYNC.md`: actualizado el estado vivo con `ImageProcessingService`, WebP quality 92/96, libvips para panorámicas grandes, temporales MiDaS, subida de 5 imágenes y pendientes reales.
- `CLAUDE.md`: actualizado el contexto general para reflejar visor Three.js vigente, `active_mode` como compatibilidad e `ImageProcessingService` como servicio responsable de imágenes.
- `Oxphyre_Room_Free_Flow.md`: marcado Sprint 1 como implementado y listados los pendientes posteriores.

**Motivo:** dejar claro para la siguiente sesión qué está implementado, qué queda pendiente y cuál es el siguiente paso recomendado.

**Verificación técnica local:**
- `git diff --check` correcto.

**Validación real en servidor:**
- `php -l backend/controllers/PositionController.php` correcto.
- `php -l backend/services/ImageProcessingService.php` correcto.
- `php8.1-gd` validado con soporte JPEG/PNG/WebP.
- `libvips-tools` validado: vips 8.12.1, ruta `/usr/bin/vips`, WebP load/save confirmado.
- Panorámica iPhone 16248x3832 procesada con libvips a WebP aprox. 8192x1932, ~2.9MB, `processed=1`.
- Subida conjunta `photo_360` + N/S/E/O validada.
- Delete de fotos validado.

**Pendiente opcional:**
- La panorámica original de iPhone ya se ve mucho mejor que la versión comprimida por WhatsApp, pero queda ruido/granulado residual en zonas oscuras de interiores.
- Causa probable: captura en interior/poca luz, ruido real de cámara y visualización fullscreen.
- No aplicar denoise por defecto todavía: podría suavizar demasiado o generar efecto acuarela.

## 2026-05-14 — Soporte HEIC/HEIF con libvips

Se añadió soporte de entrada para fotos HEIC/HEIF de iPhone sin depender de Imagick.

**Qué se cambió:**
- `config.php`: añadidos MIME reales `image/heic`, `image/heif`, `image/heic-sequence` e `image/heif-sequence`.
- `ImageProcessingService.php`: HEIC/HEIF se procesan siempre con libvips, nunca con GD.
- `ImageProcessingService.php`: si `getimagesize()` no puede leer dimensiones HEIC/HEIF, usa `vipsheader`.
- `ImageProcessingService.php`: convierte HEIC/HEIF a WebP final y genera JPG temporal para MiDaS quality 92.
- `ImageProcessingService.php`: panorámicas `360` HEIC/HEIF mantienen WebP quality 96 y máximo 8192px de ancho si hace falta redimensionar.
- `upload.php`: el selector de archivos acepta también `image/heic` e `image/heif`.

**Motivo:** permitir subidas reales desde iPhone sin pedir al usuario cambiar ajustes del móvil ni convertir manualmente.

**Verificación técnica local:**
- `php -l` no disponible en el PATH local de Windows.
- `git diff --check` correcto.

**Pendiente:**
- Prueba manual en servidor con archivo HEIC/HEIF real de iPhone.

## 2026-05-14 — Validación parcial iPhone tras soporte HEIC/HEIF

### Verificación de sintaxis en servidor
- `php -l backend/services/ImageProcessingService.php` → OK
- `php -l backend/config/config.php` → OK
- `php -l backend/views/dashboard/position/upload.php` → OK
- `git diff --check` → correcto en local antes del commit

### Subida real desde iPhone validada
- Subida desde móvil iPhone completada correctamente.
- El archivo llegó al servidor como `IMG_8024.jpeg` (iOS/Safari convirtió automáticamente a JPEG antes de enviarlo; no llegó como `.heic` puro).
- Pipeline ejecutó correctamente el path JPG/JPEG del servicio.

### Datos reales del archivo procesado
- `filename` en BD: `360_6a060457b60db7.84481882.webp`
- `original_filename`: `IMG_8024.jpeg`
- WebP final: `8192 × 1932 px`, peso `4.4 MB`
- Depth map: `443 KB`
- `processed = 1` en BD
- Visor móvil probado y carga correctamente.

### Estado de HEIC/HEIF
- Código HEIC/HEIF: implementado en `ImageProcessingService.php`.
- Servidor: `libvips 8.12.1`, `libheif1` instalados y validados (`vips list classes` muestra HEIF/HEIC load).
- config.php: permite `image/heic`, `image/heif`, `image/heic-sequence`, `image/heif-sequence`.
- Flujo iPhone normal: validado (aunque el archivo llegó como JPEG por conversión automática de iOS/Safari).
- Pendiente: prueba con archivo `.heic` puro sin conversión automática para confirmar el path HEIC del pipeline.

### Próximo bloque recomendado
R2/CDN — el pipeline de imágenes queda cerrado para uso normal de JPG/PNG/WebP y flujo iPhone habitual; el siguiente bloque es subir los WebP finales a Cloudflare R2.

## 2026-05-14 — Decisión arquitectónica R2/CDN documentada

Tipo: documentación/decisión. Sin cambios de código ni de base de datos.

### Contexto
El pipeline de imágenes WebP/libvips está cerrado y validado en servidor.
El siguiente bloque principal es integrar Cloudflare R2 como almacenamiento y CDN para los WebP finales de posiciones de usuarios.
Antes de escribir código se documenta la arquitectura decidida para que quede retomable desde cualquier IA o sesión.

### Decisión
- **EC2** procesa temporalmente: valida, convierte a WebP, genera depth map, sube a R2 y guarda la URL en BD.
- **Cloudflare R2** almacena y sirve los WebP finales. Bandwidth gratuito (sin coste de egress).
- **Bucket `oxphyre-assets`** — ya existe; se reserva para assets de landing/demo. No se usa para fotos reales de tours.
- **Bucket `oxphyre-tour-media`** — a crear; para WebP de posiciones de usuarios.
- **Custom domain:** `media.oxphyre.com` (CNAME a R2 en la zone de Cloudflare).
- **Restricción crítica:** coste 0€. Free tier R2: 10 GB, 1M escrituras/mes, 10M lecturas/mes. No activar servicios de pago hasta tener ingresos.

### Scope definido para la implementación
- Solo WebP visibles de nuevas subidas. Depth maps quedan en EC2.
- Migración de fotos antiguas: postergada hasta validar R2.
- Limpieza física en EC2: solo después de confirmar que R2 sirve el archivo.
- Fallback local obligatorio si R2 falla.
- BD: añadir `storage_provider`, `storage_key`, `public_url` a `photos`. SQL pendiente.

### Plan de implementación previsto (no ejecutado todavía)
- Fase 0: crear bucket `oxphyre-tour-media`, configurar CNAME `media.oxphyre.com`, añadir credenciales a `.env`/`.env.example`. Sin tocar código de aplicación.
- Fase 1: implementar `R2StorageService.php` (upload, url, delete). Sin tocar `PositionController`, `PhotoModel`, upload.php ni visor.
- Fases posteriores: integrar en el pipeline de subida, migrar fotos antiguas, limpieza física.

### Archivos de documentación actualizados en esta sesión
- `AI_SYNC.md`: nueva sección "Almacenamiento en Cloudflare R2" en Decisiones activas; Próximo paso actualizado con Fase 0+1.
- `CLAUDE.md`: nueva sección "Arquitectura de almacenamiento prevista" dentro del pipeline de imágenes.
- `DEVLOG.md`: esta entrada.

### Código modificado
Ninguno. Solo documentación.

## 2026-05-14 — Cloudflare DNS + R2 Fase 0

Tipo: infraestructura/configuración. Sin cambios de código, BD, upload, visor ni dashboard.

### Cloudflare DNS
- Dominio oxphyre.com conectado a Cloudflare en plan Free mediante "Connect a domain" (no transfer). IONOS sigue siendo el registrador del dominio.
- Nameservers actualizados en IONOS a:
  - `elliot.ns.cloudflare.com`
  - `julissa.ns.cloudflare.com`
- Cloudflare marca oxphyre.com como protegido/activo. Web https://oxphyre.com carga correctamente.
- DNS importados y revisados:
  - `A oxphyre.com → 13.62.93.7` (Proxied)
  - `A www → 13.62.93.7` (Proxied)
  - `MX IONOS` — DNS only (para no romper correo)
  - `TXT SPF` — DNS only
  - `CNAME autodiscover` — DNS only
  - `CNAME _dmarc` — DNS only
  - `CNAME _domainconnect` — DNS only
  - `CNAME s1-ionos._domainkey` — añadido en DNS only
  - `CNAME s2-ionos._domainkey` — añadido en DNS only

### R2
- Bucket `oxphyre-assets` — ya existía; se mantiene solo para assets/demo/landing. No se usa para fotos reales de usuarios.
- Bucket `oxphyre-tour-media` — **creado**; será el bucket de WebP finales de posiciones/tours de usuarios.
- Custom domain `media.oxphyre.com` configurado en R2 con TLS mínimo 1.2 y Access enabled.
- Estado al cerrar sesión: **Initializing** — puede tardar minutos/horas en pasar a Active.

### Qué NO se hizo
- No se tocó ningún archivo PHP, JS, CSS, SQL ni vista.
- No se creó R2StorageService.php.
- No se modificaron .env, .env.example ni config.php.
- No se tocó BD.
- No se subió ninguna imagen real de usuario a R2.
- No se hizo commit ni push.

### Pendientes para la próxima sesión
1. Verificar que `media.oxphyre.com` pase a estado **Active** en el dashboard de Cloudflare.
2. Confirmar que `https://media.oxphyre.com` resuelve (test con un objeto de prueba en el bucket).
3. Añadir credenciales R2 (Account ID, Access Key ID, Secret Access Key) a `.env` y documentar en `.env.example`.
4. Diseñar migración SQL: columnas `storage_provider`, `storage_key`, `public_url` en tabla `photos`.
5. Implementar `R2StorageService.php` (upload, getUrl, delete) sin tocar upload/visor todavía.

## 2026-05-14 — Cloudflare R2 Fase 0 validada

Tipo: validación de infraestructura. Sin cambios de código, BD, upload, visor ni dashboard.

### Estado al inicio
`media.oxphyre.com` había quedado en estado Initializing al finalizar la sesión anterior.

### Validación completada
- `media.oxphyre.com` pasó a estado **Active** en Cloudflare R2.
- Se subió un WebP de prueba llamado `Xiaomi 15 Ultra.webp` al bucket `oxphyre-tour-media`.
- La URL pública `https://media.oxphyre.com/Xiaomi%2015%20Ultra.webp` cargó correctamente en el navegador.
- El objeto de prueba fue **eliminado** del bucket tras verificar que servía correctamente.

### Métricas R2 tras la prueba (aproximadas)
- Class A Operations: ~20
- Class B Operations: ~330
- Estas cifras quedan muy por debajo del free tier (1M escrituras y 10M lecturas/mes).
- Nota: vigilar el uso de operaciones y storage para mantener coste 0€; no realizar migraciones masivas de fotos existentes ni subir depth maps u originales a R2.

### Resumen Fase 0
- DNS Cloudflare: ✓ Active
- Bucket `oxphyre-tour-media`: ✓ creado
- Custom domain `media.oxphyre.com`: ✓ Active
- URLs públicas WebP: ✓ verificadas
- Coste: 0€ (dentro del free tier)

### Qué NO se hizo
- No se tocó ningún archivo PHP, JS, CSS, SQL ni vista.
- No se creó R2StorageService.php.
- No se modificaron .env, .env.example ni config.php.
- No se tocó BD.
- No se integró R2 en el pipeline de subida.

### Pendientes para Fase 1
1. Añadir credenciales R2 (Account ID, Access Key ID, Secret Access Key, bucket name, public URL base) a `.env` y documentar en `.env.example`.
2. Diseñar y ejecutar migración SQL: columnas `storage_provider` (enum 'local'|'r2'), `storage_key` y `public_url` en tabla `photos`.
3. Implementar `R2StorageService.php`: métodos upload(), getUrl(), delete(). Sin tocar PositionController, PhotoModel, upload.php, visor ni dashboard todavía.

## 2026-05-14 — Plan técnico R2/CDN Fase 1 documentado

Tipo: planificación/documentación. Sin cambios de código, BD, .env.example ni servicios.

### Contexto
Fase 0 R2 validada: bucket `oxphyre-tour-media` creado, `media.oxphyre.com` Active, WebP público servido correctamente.
Antes de escribir código de Fase 1 se documentó el plan técnico detallado en AI_SYNC.md y CLAUDE.md.

### Alcance definido para Fase 1 (en orden)

1. **Revisar `composer.json`**: verificar si existe en el proyecto. Decidir SDK AWS S3 compatible con R2 (peso ~20 MB) vs cURL puro. cURL puro es suficiente para los tres métodos (upload/getUrl/delete) y más ligero para EC2 t3.small. Elegir antes de instalar.

2. **`.env.example`**: documentar las variables R2 definitivas:
   - `R2_ENABLED=false`
   - `R2_ACCOUNT_ID=`
   - `R2_ACCESS_KEY_ID=`
   - `R2_SECRET_ACCESS_KEY=`
   - `R2_BUCKET=oxphyre-tour-media`
   - `R2_ENDPOINT=https://<ACCOUNT_ID>.r2.cloudflarestorage.com`
   - `R2_PUBLIC_BASE_URL=https://media.oxphyre.com`
   - `R2_REGION=auto`

3. **Migración SQL**: `ALTER TABLE photos ADD COLUMN storage_provider ENUM('local','r2') NOT NULL DEFAULT 'local', ADD COLUMN storage_key VARCHAR(512) NULL, ADD COLUMN public_url VARCHAR(1024) NULL;` (query a ejecutar manualmente vía SSH en el servidor).

4. **`backend/services/R2StorageService.php`**: métodos `upload(string $localPath, string $key): bool`, `getPublicUrl(string $key): string`, `delete(string $key): bool`. Credenciales desde `$_ENV`. Sin escritura en BD. Fallo silencioso.

5. **Test aislado**: subir WebP real al bucket con el servicio, verificar URL pública, eliminar el objeto. Sin integrar en pipeline todavía.

### Restricciones de coste documentadas
- Solo WebP visibles a R2; no originales, no depth maps.
- No migrar fotos antiguas en Fase 1.
- No dejar objetos de prueba en el bucket.
- Controlar tamaño de dependencias nuevas.
- Vigilar free tier R2 (10 GB, 1M escrituras, 10M lecturas).

### Qué NO hace Fase 1
- No toca `PositionController`, `TourController`, `PhotoModel`, upload.php, visor ni dashboard.
- No integra R2 en el flujo de subida real.
- No modifica URLs servidas al visor.

### Archivos de documentación actualizados en esta sesión
- `AI_SYNC.md`: nueva subsección "R2/CDN Fase 1 planificada" con los 5 pasos, restricciones y límites.
- `CLAUDE.md`: bloque "Fase 1 prevista" dentro de la sección R2 con arquitectura del servicio y criterio de coste.
- `DEVLOG.md`: esta entrada.

### Código modificado
Ninguno.

## 2026-05-15 — Decisión técnica R2 Fase 1: cURL puro + AWS Signature V4

Tipo: decisión técnica/documentación. Sin cambios de código, BD, `.env.example` ni servicios.

### Contexto
Fase 0 R2 está validada y Fase 1 sigue pendiente de implementación.
Se revisó el proyecto y no existe `composer.json`, `composer.lock` ni `vendor/`; `public/index.php` tampoco carga autoloader de Composer.

### Decisión
- Usar cURL puro con firma AWS Signature Version 4 manual para Cloudflare R2.
- Descartar Composer/AWS SDK por ahora para no añadir dependencias solo por R2.
- Evitar un `vendor/` pesado y el coste de memoria/espacio asociado al AWS SDK en EC2 t3.small.
- Mantener `R2StorageService.php` aislado, sin escritura en BD.

### Alcance previsto del servicio
- `upload()` = PUT firmado.
- `delete()` = DELETE firmado.
- `getPublicUrl()` = concatenar `R2_PUBLIC_BASE_URL` + `storage_key`.
- La firma AWS V4 se encapsulará en métodos privados.
- Keys seguras previstas: `tours/{tourId}/positions/{positionId}/{direction}/{filename}.webp`.

### Riesgos y mitigación
- Riesgo: errores en canonical headers, hash del body, fechas UTC o URL encoding pueden romper la firma AWS V4.
- Mitigación: métodos privados aislados, `hash_file('sha256', $localPath)` para uploads, keys controladas, test real aislado antes de tocar upload y fallback local obligatorio en Fase 2 si R2 falla.

### Archivos actualizados
- `AI_SYNC.md`: decisión viva de Fase 1 actualizada.
- `CLAUDE.md`: decisión arquitectónica R2 documentada.
- `DEVLOG.md`: esta entrada.

### Qué NO se hizo
- No se tocó código PHP, JS, CSS, SQL ejecutable ni vistas.
- No se creó `composer.json`.
- No se instalaron dependencias.
- No se creó `R2StorageService.php`.
- No se modificó `.env.example`.
- No se tocó upload, visor ni dashboard.
- No se hizo commit ni push.

## 2026-05-15 — Migración SQL metadata R2 en `photos`

Tipo: documentación/BD ya ejecutada en servidor. Sin cambios de código de aplicación.

### Query ejecutada en MySQL
```sql
ALTER TABLE photos
  ADD COLUMN storage_provider ENUM('local','r2') NOT NULL DEFAULT 'local' AFTER processed,
  ADD COLUMN storage_key VARCHAR(512) NULL AFTER storage_provider,
  ADD COLUMN public_url VARCHAR(1024) NULL AFTER storage_key;
```

### Resultado verificado con `DESCRIBE photos`
- `storage_provider enum('local','r2') NOT NULL DEFAULT 'local'`
- `storage_key varchar(512) NULL`
- `public_url varchar(1024) NULL`

### Significado de los campos
- `storage_provider`: indica dónde está la imagen visible final. `local` = EC2 `/uploads/`; `r2` = Cloudflare R2.
- `storage_key`: ruta interna dentro del bucket R2 y referencia principal. Ejemplo: `tours/3/positions/12/360/360_xxxxx.webp`.
- `public_url`: URL pública construida con `R2_PUBLIC_BASE_URL + storage_key`. Se guarda por comodidad y lectura rápida, pero puede regenerarse desde `storage_key` si cambia el dominio CDN.

### Aclaraciones
- La URL pública del tour/visor sigue siendo `oxphyre.com/...`.
- `media.oxphyre.com/...webp` solo sirve imágenes internas del visor; el visitante normalmente no ve esas URLs salvo inspeccionando red/devtools.
- No usar nombres originales de usuario en R2. Usar nombres técnicos y seguros generados por Oxphyre.
- Fotos antiguas siguen compatibles: `storage_provider='local'`, `storage_key=NULL`, `public_url=NULL`.

### Qué NO se hizo
- No se tocó código PHP, JS, CSS, vistas ni servicios.
- No se creó `R2StorageService.php`.
- No se tocó `.env.example` en esta entrada; ya había sido actualizado previamente con variables R2.
- No se integró R2 en upload, visor ni dashboard.
- No se hizo commit ni push.

## 2026-05-15 — Ajuste de diseño técnico para `R2StorageService.php`

Tipo: ajuste de diseño/documentación. Sin cambios de código.

### Motivo
Antes de implementar `R2StorageService.php`, se refinó el diseño técnico de cURL + AWS Signature V4 para mejorar seguridad, consumo de memoria y robustez en EC2 t3.small.

### Criterios actualizados
- El servicio no decide si R2 está habilitado. `R2_ENABLED` lo leerá el caller en Fase 2; si `R2StorageService` se instancia, asume que se quiere usar R2.
- El constructor debe lanzar `RuntimeException` si faltan credenciales críticas o configuración necesaria.
- El endpoint firmado será virtual-host style: `https://{bucket}.{accountId}.r2.cloudflarestorage.com/{key}`. No usar path-style porque la firma debe coincidir con el host real usado en cURL.
- Upload por streaming con `CURLOPT_UPLOAD`, `CURLOPT_INFILE` y `CURLOPT_INFILESIZE`; no usar `CURLOPT_POSTFIELDS` para archivos.
- Keys codificadas por segmento con `implode('/', array_map('rawurlencode', explode('/', $key)))`; no usar `urlencode($key)` completo porque rompe los `/`.
- PUT debe firmar como mínimo `content-type`, `host`, `x-amz-content-sha256`, `x-amz-date`.
- DELETE debe firmar `host`, `x-amz-content-sha256`, `x-amz-date`.
- PUT usará `hash_file('sha256', $localPath)`, DELETE usará SHA256 de string vacío y fechas UTC con `gmdate()`.
- `validateKey()` debe ejecutarse al inicio de `upload()`, `getPublicUrl()` y `delete()`.

### Fuera de alcance ahora
- Presigned URLs.
- Reintentos automáticos.
- Integración con upload.
- Cambios en visor/dashboard.

### Qué NO se hizo
- No se tocó código PHP, JS, CSS, SQL ejecutable ni vistas.
- No se creó `R2StorageService.php`.
- No se tocó `.env.example`.
- No se tocó BD.
- No se integró R2 en upload, visor ni dashboard.
- No se hizo commit ni push.

## 2026-05-15 — Creación de `R2StorageService.php`

Tipo: implementación aislada de servicio. Sin integración en upload, visor, dashboard ni modelos.

### Qué se hizo
- Creado `backend/services/R2StorageService.php`.
- Implementados `upload(string $localPath, string $key): bool`, `getPublicUrl(string $key): string` y `delete(string $key): bool`.
- Constructor lee configuración desde `$_ENV`, normaliza `R2_PUBLIC_BASE_URL` y lanza `RuntimeException` si falta configuración crítica.
- Firma AWS Signature Version 4 manual con cURL puro, sin Composer ni AWS SDK.
- Endpoint virtual-host style para firma: `https://{bucket}.{accountId}.r2.cloudflarestorage.com/{encodedKey}`.
- Upload por streaming con `CURLOPT_UPLOAD`, `CURLOPT_INFILE` y `CURLOPT_INFILESIZE`.
- Validación de keys al inicio de los tres métodos públicos.

### Qué NO se hizo
- No se tocó `PositionController`, `TourController`, `PhotoModel`, vistas, JS ni CSS.
- No se escribió en BD.
- No se leyó ni decidió `R2_ENABLED`.
- No se implementaron presigned URLs ni reintentos automáticos.
- No se integró R2 en el pipeline real.
- No se hizo commit ni push.

### Verificación local
- `git diff --check` correcto.
- `php -l backend/services/R2StorageService.php` no se pudo ejecutar en Windows local porque `php` no está disponible en el PATH.

## 2026-05-15 — Script temporal de test aislado R2

Tipo: herramienta temporal de verificación. Sin integración en pipeline real.

### Qué se hizo
- Creado `scripts/test_r2_service.php`.
- Script CLI fuera de `public/`, sin credenciales hardcodeadas y sin acceso a BD.
- Carga `.env` con una función local mínima, sin imprimir secretos.
- Genera un WebP temporal 10x10 en `/tmp` usando GD.
- Usa la key de prueba `tours/0/positions/0/360/r2-test-probe.webp`.
- Prueba `getPublicUrl()`, `upload()`, comprobación HTTP HEAD, `delete()` y verificación posterior de que la URL ya no devuelve 200.
- Usa `try/finally` para borrar siempre el archivo temporal local y confirmar limpieza del objeto R2 de prueba.

### Qué NO se hizo
- No se tocó upload, visor, dashboard, modelos ni vistas.
- No se modificó `.env`, `.env.example` ni BD.
- No se ejecutó el test real desde local.
- No se hizo commit ni push.

## 2026-05-15 — Validación real de R2StorageService y política de caché

Tipo: validación real en servidor y decisión técnica. Sin integración en pipeline real.

### Comandos ejecutados en servidor
```bash
php -l scripts/test_r2_service.php
php scripts/test_r2_service.php
```

### Resultado
- `php -l` no mostró errores de sintaxis.
- `.env` cargado correctamente.
- `R2StorageService` instanciado con variables R2 reales del servidor.
- WebP temporal creado en `/tmp`.
- `getPublicUrl()` generó `https://media.oxphyre.com/tests/r2-probe/360/r2-test-probe.webp`.
- `upload()` subió correctamente el WebP a Cloudflare R2.
- La URL pública respondió HTTP 200.
- `delete()` eliminó el objeto de R2 correctamente.
- La limpieza final confirmó borrado.
- El archivo temporal local se eliminó.

### Warning de caché Cloudflare
Después del `delete()`, la comprobación HEAD seguía devolviendo HTTP 200 porque Cloudflare servía caché:
- `cf-cache-status=HIT`
- `cache-control=max-age=14400`
- `age=701`

Esto no se considera fallo de `R2StorageService.php`: el objeto ya no aparece en el bucket R2 y el 200 venía de caché CDN.

### Decisión
- No implementar purga activa de caché en TFG/MVP inicial.
- Nunca reutilizar `storage_key`.
- Cada upload debe generar una key única e irrepetible.
- Si una foto se sustituye, se sube como objeto nuevo con nueva key.
- La BD decide qué foto está activa.
- El visor solo debe usar fotos activas desde BD.
- Los objetos huérfanos/antiguos se limpiarán en una fase posterior.

### Qué NO se hizo
- No se tocó código PHP, JS, CSS, SQL ejecutable ni vistas en esta documentación.
- No se modificó `R2StorageService.php` ni `scripts/test_r2_service.php`.
- No se tocó BD.
- No se integró R2 en upload, visor ni dashboard.
- No se hizo commit ni push.

## 2026-05-15 — Plan R2/CDN Fase 2A documentado

Tipo: planificación/documentación. Sin cambios de código ni integración.

### Contexto
Fase 0 R2 validada, variables R2 documentadas, `.env` real del servidor configurado, metadata R2 en `photos` ejecutada, `R2StorageService.php` implementado y validado con test aislado real. R2 todavía no está integrado en el pipeline real y visor/dashboard aún no usan `public_url`.

### Decisión Fase 2A
Integrar R2 solo para nuevas subidas, manteniendo copia local en EC2 como fallback temporal.

Definiciones:
- **Local** = archivo físico en EC2: `/public/uploads/{positionId}/...`.
- **BD** = metadata/referencias; no almacena imágenes.
- **R2** = almacenamiento final futuro de WebP visibles.

### Plan por fases
- **Fase 2A:** nuevas subidas guardan WebP local como hasta ahora y, si `R2_ENABLED=true`, también intentan subir el WebP final a R2. La BD guarda metadata R2 si funciona. El visor sigue usando local.
- **Fase 2B:** visor/dashboard usarán `public_url` si existe y fallback local si no.
- **Fase 3:** limpieza local/R2 de objetos huérfanos cuando R2 esté validado en flujo real.

El doble almacenamiento local + R2 en Fase 2A es temporal y deliberado: permite validar R2 sin riesgo de perder imágenes ni romper el visor actual. No contradice la arquitectura final; EC2 será procesador/temporal y R2 almacenamiento final, pero la limpieza local queda para Fase 3.

### Reglas Fase 2A
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

### Archivos previstos
- `backend/models/PhotoModel.php`
- `backend/controllers/PositionController.php`
- `backend/services/R2StorageService.php` solo si aparece bug.

### Fuera de alcance Fase 2A
- `ImageProcessingService.php` salvo necesidad justificada.
- Visor público.
- Dashboard.
- `TourController.php`.
- Migración de fotos antiguas.
- Limpieza local/R2 de objetos huérfanos.

### Siguiente microbloque
Fase 2A.1: ampliar `PhotoModel::create()` con campos R2 opcionales (`storage_provider`, `storage_key`, `public_url`).

### Qué NO se hizo
- No se editó PHP, JS, CSS, SQL ejecutable ni vistas.
- No se modificó `R2StorageService.php`.
- No se tocó `.env`, `.env.example` ni BD.
- No se integró R2 en upload, visor ni dashboard.
- No se hizo commit ni push.

## 2026-05-15 — Fase 2A.1 PhotoModel preparado para metadata R2

Tipo: implementación compatible hacia atrás. Sin integración en upload/visor/dashboard.

### Qué se hizo
- `PhotoModel::create()` ahora acepta tres parámetros opcionales al final:
  - `storageProvider = 'local'`
  - `storageKey = null`
  - `publicUrl = null`
- El `INSERT` de `photos` guarda también `storage_provider`, `storage_key` y `public_url`.
- Las llamadas actuales con 6 parámetros siguen funcionando y guardan fotos locales con `storage_provider='local'`, `storage_key=NULL`, `public_url=NULL`.

### Motivo
Preparar Fase 2A para que `PositionController` pueda guardar metadata R2 cuando una subida nueva se duplique correctamente en Cloudflare R2, sin obligar todavía al pipeline real a usar R2.

### Qué NO se hizo
- No se tocó `PositionController`, `TourController`, vistas, JS, CSS ni visor.
- No se tocó `R2StorageService.php`.
- No se modificó `.env`, `.env.example` ni BD.
- No se integró R2 en upload real todavía.
- No se hizo commit ni push.

### Siguiente microbloque
Fase 2A.2: integrar `resolveStorage()` y `buildR2Key()` en `PositionController` manteniendo fallback local.

## 2026-05-15 — Fase 2A.2 R2 integrado en nuevas subidas

Tipo: implementacion de integracion controlada. Sin cambios en visor, dashboard ni `TourController`.

### Que se hizo
- `PositionController::upload()` carga `R2StorageService.php` junto al resto de servicios del flujo de subida.
- Se anadio `buildR2Key()` para generar keys unicas con `bin2hex(random_bytes(8))` y formato `tours/{tourId}/positions/{positionId}/{direction}/{direction}_{random}.webp`.
- Se anadio `resolveStorage()` para intentar subir a R2 solo cuando `R2_ENABLED=true`.
- La panoramica `360` y las fotos `N/S/E/O` guardan ahora metadata R2 en `PhotoModel::create()` si el upload a R2 funciona.
- Si R2 esta desactivado, falla, no hay WebP local o aparece una excepcion, la foto se guarda como local sin interrumpir la subida.

### Motivo
Validar Cloudflare R2 en el flujo real de nuevas subidas sin romper el visor actual ni perder el fallback local. Fase 2A mantiene doble almacenamiento temporal: WebP local en EC2 y copia R2 si esta disponible.

### Que NO se hizo
- No se toco `ImageProcessingService.php`.
- No se toco `R2StorageService.php`.
- No se toco `PhotoModel.php`.
- No se toco `TourController`, visor, dashboard, vistas, JS ni CSS.
- No se modifico `.env`, `.env.example` ni BD.
- No se suben depth maps ni originales a R2.
- No se borra el WebP local.
- No se hizo commit ni push.

### Siguientes pruebas recomendadas
1. `R2_ENABLED=false` -> subida local legacy.
2. `R2_ENABLED=true` -> subida local + metadata R2.
3. Comprobar `public_url` con `curl -I`.
4. Comprobar que el visor sigue funcionando por local.

## 2026-05-15 — Prefijo `[R2]` en logs de almacenamiento

Tipo: ajuste de depuracion. Sin cambios de logica.

### Que se hizo
- Los `error_log()` de R2 en `PositionController.php` empiezan ahora por `[R2]`.
- El objetivo es poder filtrar rapidamente logs de Nginx/PHP-FPM con `grep "\[R2\]"`.

### Que NO se hizo
- No se cambio flujo, validacion, firmas, metodos ni comportamiento.
- No se tocaron visor, dashboard, servicios, modelos, vistas, JS, CSS, BD ni `.env`.
- No se hizo commit ni push.

## 2026-05-15 — R2/CDN Fase 2A validada en servidor real

Tipo: documentacion de validacion. Sin cambios de codigo en esta entrada.

### Contexto
Fase 2A ya tenia implementado:
- `R2StorageService.php` probado de forma aislada.
- `PhotoModel::create()` con metadata R2 opcional.
- `PositionController::upload()` con `resolveStorage()` y `buildR2Key()`.

El objetivo de la prueba era confirmar el comportamiento real en servidor: WebP local siempre disponible, copia R2 solo si esta habilitada y fallback local obligatorio si R2 falla.

### Pruebas realizadas

1. `R2_ENABLED=false`
- Se subio una foto N en la posicion de prueba.
- BD: id 56, `direction=N`, `storage_provider=local`, `storage_key=NULL`, `public_url=NULL`.
- Resultado: flujo legacy/local validado.

2. `R2_ENABLED=true`
- Se subio una foto S en la posicion de prueba.
- BD: id 57, `direction=S`, `storage_provider=r2`.
- `storage_key=tours/1/positions/2/S/S_961208678db1224b.webp`.
- `public_url=https://media.oxphyre.com/tours/1/positions/2/S/S_961208678db1224b.webp`.
- `curl -I public_url` devolvio HTTP/2 200, `content-type: image/webp`, `cf-cache-status: MISS`.
- Resultado: upload R2 real validado.

3. Panoramica 360 con `R2_ENABLED=true`
- Se subio una foto 360 en la posicion de prueba.
- BD: id 58, `direction=360`, `storage_provider=r2`.
- `storage_key=tours/1/positions/2/360/360_cfd6bad8b5a15a40.webp`.
- `public_url=https://media.oxphyre.com/tours/1/positions/2/360/360_cfd6bad8b5a15a40.webp`.
- `curl -I public_url` devolvio HTTP/2 200, `content-type: image/webp`, `cf-cache-status: MISS`.
- Resultado: posicion con panoramica R2 validada y visitable.

4. Fallback R2 con fallo controlado
- Se hizo backup de `.env`.
- Se dejo `R2_ENABLED=true`.
- Se cambio temporalmente `R2_SECRET_ACCESS_KEY=INVALIDA_TEST_FALLO`.
- Se subio foto E.
- BD: id 59, `direction=E`, `storage_provider=local`, `storage_key=NULL`, `public_url=NULL`.
- Resultado: R2 fallo correctamente, pero la subida no se rompio y cayo a local.
- `.env` fue restaurado correctamente.

### Conclusiones
- Fase 2A queda validada.
- Nuevas subidas pueden guardarse local + R2.
- Fallback local obligatorio funciona.
- Keys unicas funcionan y no se reutiliza `storage_key`.
- No se suben depth maps ni originales a R2.
- No se borra el WebP local.
- Visor sigue funcionando por local.
- R2 todavia no es fuente principal del visor; eso queda para Fase 2B.
- Objetos huerfanos y limpieza local quedan para Fase 3.

### Que NO se hizo
- No se toco codigo PHP, JS, CSS, SQL ejecutable, vistas ni servicios en esta entrada.
- No se modifico `.env`, `.env.example` ni BD durante la documentacion.
- No se tocaron `PositionController`, `PhotoModel`, `R2StorageService`, visor ni dashboard.
- No se marco Fase 2B como implementada.
- No se hizo commit ni push.

### Siguiente bloque recomendado
Debate/plan UX de Oxphyre Room y detalles opcionales antes de seguir con R2 Fase 2B si procede.

## 2026-05-15 — Redefinicion conceptual de Oxphyre Room

Tipo: decision UX/conceptual. Solo documentacion.

### Decision
Oxphyre Room deja de entenderse como "modo 4 fotos" y pasa a ser la experiencia completa de una posicion en el visor.

Nueva definicion:
- Panoramica principal / 360 adaptativa: obligatoria para que una posicion sea visitable.
- Fotos detalle: opcionales, de 1 a 4, para destacar zonas concretas que no se aprecian bien en la panoramica.
- Si hay 0 fotos detalle, la posicion funciona solo con panoramica.
- Si hay 1-4 fotos detalle, el visor debera poder mostrar las disponibles.
- El usuario no debe estar obligado a subir las 4 fotos detalle.

### Motivo
La redefinicion mejora la claridad comercial y la flexibilidad para PYMES: las fotos detalle se venden como forma de destacar partes clave de la panoramica, como barra, mesa, escaparate, producto, decoracion o un rincon especial.

### Naming UI decidido
- No mostrar "Frente / Fondo / Izquierda / Derecha" al usuario.
- Mostrar "Foto detalle 1", "Foto detalle 2", "Foto detalle 3" y "Foto detalle 4".

### Decision tecnica temporal
- No migrar todavia la BD ni el enum interno `direction`.
- Backend mantiene `N = Foto detalle 1`, `S = Foto detalle 2`, `E = Foto detalle 3`, `O = Foto detalle 4`.
- Migrar a `detail_1/detail_2/detail_3/detail_4` queda como posible mejora futura.

### Regla UX critica
Si una posicion no tiene panoramica `360`, no debe parecer visitable. El boton "Ver posicion" debe aparecer desactivado/no clickable tanto en la card/listado de posiciones como dentro de la pantalla de gestion/subida.

Tooltip sugerido:
"Sube una panoramica principal para activar esta experiencia Oxphyre Room. Las fotos detalle son opcionales."

### Regla de visor
- El visor publico solo debe incluir posiciones con panoramica `360`; esto ya ocurre en `TourController::showPublic()` al descartar posiciones sin `360`.
- Cuando haya fotos detalle parciales, el visor debera permitir mostrar las disponibles sin exigir las 4.

### Que NO se hizo
- No se toco codigo PHP, JS, CSS, SQL ejecutable, vistas ni servicios.
- No se modifico BD.
- No se migro el enum `direction`.
- No se toco R2.
- No se marcaron cambios UX como implementados.
- No se hizo commit ni push.

### Siguiente paso exacto
Bloquear/desactivar "Ver posicion" si falta panoramica `360`.

## 2026-05-15 — Bloqueo UX de "Ver posicion" sin panoramica 360

Tipo: UX/dashboard.

### Que se hizo
- `PhotoModel.php`: añadido `getPanoramaPositionIdsByTour()` con prepared statement y JOIN con `positions` para obtener en una sola consulta las posiciones visitables de un tour.
- `TourController.php`: `showManage()` enriquece cada posicion con `has_panorama` para evitar consultas N+1 desde la vista.
- `dashboard/tours/manage.php`: el boton "Ver posicion" solo es enlace si la posicion tiene panoramica `360`; si no, se muestra desactivado/no clickable con `aria-disabled="true"`.
- `dashboard/position/upload.php`: el enlace "Ver esta posicion" usa `$hasPanorama`; si falta la panoramica, se muestra desactivado/no clickable.

### Motivo
Una posicion solo debe parecer visitable cuando tiene panoramica principal `360`. Las fotos detalle 1-4 son opcionales y no desbloquean por si solas la experiencia publica de esa posicion.

### Tooltip aplicado
"Sube una panoramica principal para activar esta experiencia Oxphyre Room. Las fotos detalle son opcionales."

### Que NO se hizo
- No se toco `TourController::showPublic()`, porque ya filtra posiciones sin `360`.
- No se bloqueo "Ver tour" general en `tours/index.php`.
- No se toco visor JS.
- No se toco R2.
- No se modifico BD ni rutas.
- No se cambio N/S/E/O ni el enum `direction`.
- No se hizo commit ni push.

## 2026-05-15 — Correccion query de posiciones con panoramica

Tipo: fix dashboard.

### Que se hizo
- `PhotoModel::getPanoramaPositionIdsByTour()` deja de usar `DISTINCT` con `ORDER BY positions.order_index`.
- La query usa ahora `EXISTS` para detectar posiciones con panoramica `360` y mantener el orden sin romper MySQL.

### Motivo
MySQL devolvia `ERROR 3065` porque `ORDER BY positions.order_index` no estaba en el `SELECT DISTINCT`, provocando error 500 en la gestion del tour.

### Que NO se hizo
- No se tocaron `TourController`, vistas, R2, BD, rutas ni visor.
- No se hizo commit ni push.

## 2026-05-15 — UX de fotos detalle parciales en Oxphyre Room

Tipo: UX/visor.

### Que se hizo
- `dashboard/position/upload.php`: el naming visible deja de usar Frente/Fondo/Derecha/Izquierda y pasa a mostrar Foto detalle 1-4.
- Se mantiene el mapeo interno `N/S/E/O` por compatibilidad con la BD y el enum actual.
- `TourController::showPublic()` sigue exigiendo panoramica `360` para que una posicion entre en el visor, pero ahora marca detalles disponibles si existe al menos una foto N/S/E/O.
- `tour-viewer.js`: el boton "Ver detalles" aparece con 1-4 fotos detalle disponibles y el visor renderiza solo esas fotos, sin exigir las cuatro.

### Motivo
Oxphyre Room queda alineado con la nueva decision UX: la panoramica principal activa la posicion y las fotos detalle son opcionales para destacar partes concretas de la experiencia.

### Que NO se hizo
- No se migro la BD ni el enum `direction`.
- No se cambio el contrato interno N/S/E/O.
- No se toco R2, rutas, `.env`, subida R2 ni limpieza fisica.
- No se hizo commit ni push.

## 2026-05-15 — Ajuste visual de detalles parciales en subida

Tipo: fix UX/dashboard.

### Que se hizo
- `dashboard/position/upload.php`: el estado visual del bloque de fotos detalle depende directamente de si existe al menos una foto detalle.
- Se evita que una variable legacy con semantica antigua 4/4 deje el badge como borrador cuando ya hay 1-3 detalles subidos.

### Que NO se hizo
- No se cambio logica de BD, N/S/E/O, TourController, visor JS, R2 ni rutas.
- No se hizo commit ni push.

## 2026-05-15 — Texto de estado de imagen procesada

Tipo: copy UX/dashboard.

### Que se hizo
- `dashboard/position/upload.php`: el badge visible `IA OK` pasa a mostrar `Procesada`.

### Que NO se hizo
- No se cambio logica de procesado, estados, CSS, BD, R2, visor JS ni rutas.
- No se hizo commit ni push.

## 2026-05-18 - R2/CDN Fase 2B inicial

Tipo: integracion de URLs publicas R2 con fallback local.

### Que se hizo
- Creado `backend/services/PhotoUrlResolver.php` para centralizar la resolucion de URL visible final entre R2 y almacenamiento local.
- `TourController::showPublic()` usa `public_url` cuando la foto esta en R2 y conserva fallback local `/uploads/{positionId}/{filename}` cuando no existe.
- `PositionController::showUpload()` anade `resolved_url` a las fotos antes de pasarlas a la vista de subida.
- `dashboard/position/upload.php` usa `resolved_url` para previews de panoramica y fotos detalle, con fallback local defensivo si no llega ese campo.
- `public/index.php` permite `https://media.oxphyre.com` en `img-src` de la Content-Security-Policy.

### Motivo
Activar R2 como fuente visible para visor publico y previews del dashboard sin romper fotos antiguas ni perder el fallback local validado en Fase 2A.

### Que NO se hizo
- No se borro ningun archivo local.
- No se migraron fotos antiguas.
- No se toco `PhotoModel.php`.
- No se toco la subida R2 ni `resolveStorage()`/`buildR2Key()`.
- No se toco `R2StorageService.php`.
- No se modifico BD, rutas, `.env`, `.env.example`, Composer/vendor, `tour-viewer.js` ni N/S/E/O.
- No se hizo commit ni push.

## 2026-05-18 - URL directa a posicion sin panoramica

Tipo: fix UX/visor.

### Que se hizo
- `public/js/tour-viewer.js` deja de hacer fallback silencioso a la primera posicion disponible cuando la URL trae `?position={id}` y esa posicion no esta en `TOUR_DATA`.
- Si la posicion pedida no tiene panoramica `360` activa, el visor muestra el estado no disponible.
- El fallback a la primera posicion disponible se mantiene solo cuando el tour se abre sin parametro `position`.
- El visor ahora distingue entre tour completo no disponible y zona solicitada no disponible.
- `backend/views/tour.php` anade el estado "Esta zona no esta disponible en el tour" con boton para ver el tour desde el principio sin recargar la pagina.
- `public/css/tour.css` anade el estilo minimo del boton de vuelta al inicio.
- El boton de vuelta al inicio limpia el parametro `position` con History API sin recargar y usa estilo visual coherente con Oxphyre.

### Que NO se hizo
- No se toco R2, logica backend de controllers/models, BD, rutas, dashboard, CSP ni N/S/E/O.
- No se hizo commit ni push.

## 2026-05-18 - R2/CDN Fase 2B validada en servidor real

Tipo: validacion real de visor/dashboard con R2 como fuente visible y fallback local.

### Que se valido
- `R2_ENABLED=true` en servidor.
- Nueva panoramica subida en posicion 2 tras configurar CORS en el bucket R2.
- BD correcta para la nueva foto: `direction='360'`, `storage_provider='r2'`, `public_url=https://media.oxphyre.com/...` y `deleted_at=NULL`.
- `TourController::showPublic()` construye `TOUR_DATA` con `PhotoUrlResolver::resolve()`.
- `PositionController::showUpload()` anade `resolved_url` y `upload.php` lo usa en previews.
- `public/index.php` permite `https://media.oxphyre.com` en `img-src`.
- Network confirmo carga mixta con HTTP 200:
  - panoramica `360` desde `https://media.oxphyre.com/...`;
  - detalle `S` desde `https://media.oxphyre.com/...`;
  - detalles `N`/`E` desde `/uploads/2/...`.
- Three.js renderiza correctamente texturas R2 y detalles mixtos R2/local.

### Problema detectado y diagnostico
- Al principio, el visor mostraba "Tour no disponible" al cargar una panoramica desde R2.
- La imagen R2 respondia HTTP 200, la BD era correcta, `TOUR_DATA` era correcto y CSP ya permitia `media.oxphyre.com`.
- El fallo real era que la respuesta R2 no incluia `access-control-allow-origin`, necesario para que WebGL/Three.js pueda usar la textura.
- Una URL antigua siguio fallando porque Cloudflare tenia cacheada una respuesta sin CORS (`cf-cache-status=HIT`).
- Al probar la misma URL con `?cors-test=1`, Cloudflare devolvio `MISS` y ya aparecio `access-control-allow-origin: https://oxphyre.com`.

### Solucion aplicada
- Configurado CORS en el bucket `oxphyre-tour-media` para `https://oxphyre.com` y `https://www.oxphyre.com`, metodos `GET`/`HEAD`, headers `*`, expose `ETag`, `Content-Length`, `Content-Type`, `MaxAgeSeconds=3600`.
- No se purgo cache Cloudflare.
- Se subio una nueva panoramica, generando una `storage_key` unica. La nueva URL no estaba cacheada y salio con CORS correcto.

### Estado final
- R2/CDN Fase 2B queda validada en servidor real.
- El visor publico carga `public_url` R2 cuando existe y fallback local cuando no.
- El dashboard de subida muestra previews R2/local mediante `resolved_url`.
- La mezcla R2 + local funciona.
- Las fotos legacy sin `public_url` siguen funcionando desde `/uploads/`.
- Los detalles parciales 1-4 siguen funcionando.
- El bug UX de `?position={id}` sin panoramica tambien queda corregido: no carga otra posicion silenciosamente, muestra "Esta zona no esta disponible en el tour".

### Pendiente
- Fase 3: limpieza local/R2 de objetos huerfanos y archivos fisicos cuando proceda.
- Migracion futura de fotos antiguas a R2 solo si se decide; no es necesaria para el estado actual.

### Que NO se hizo
- No se borro local.
- No se migraron fotos antiguas.
- No se toco BD, codigo, rutas, `.env`, subida R2 ni `R2StorageService.php` en esta documentacion.
- No se hizo commit ni push.

## 2026-05-18 - DEP 1 Composer normalizado para PHPMailer

Tipo: deuda tecnica de dependencias. Sin cambios funcionales en QR, BD, R2 ni rutas.

### Que se hizo
- Se creo `composer.json` minimo del proyecto con PHP `>=8.1 <8.5` y la dependencia real actual `phpmailer/phpmailer`.
- Se fijo `config.platform.php` en `8.1.0` para que Composer resuelva dependencias compatibles con el PHP objetivo del servidor.
- Se ejecuto Composer con PHP 8.1 de WAMP y se genero `composer.lock`.
- Composer instalo `phpmailer/phpmailer` en version `v6.12.0`.
- Se anadio `vendor/` a `.gitignore` para que las dependencias instaladas no se versionen y puedan regenerarse con `composer install`.

### Motivo
`EmailService.php` ya dependia de `vendor/autoload.php`, pero el repo no tenia `composer.json` ni `composer.lock`. DEP 1 deja PHPMailer reproducible entre local y servidor antes de anadir QR como nueva dependencia Composer.

### Que NO se hizo
- No se implemento QR.
- No se anadio ninguna libreria QR.
- No se modifico `EmailService.php`.
- No se toco BD, R2, rutas, `.env`, uploads, Python ni codigo no relacionado.
- No se versiono `vendor/`.
- No se hizo commit ni push.

## 2026-05-18 - QR 1 con URL permanente por token

Tipo: implementacion QR base. Sin analiticas, sin PDF y sin guardar PNG en disco.

### Que se hizo
- Se anadio `bacon/bacon-qr-code` via Composer como dependencia QR compatible con PHP 8.1 y licencia BSD-2-Clause.
- Se creo `QrCodeService.php` para generar PNG en memoria con `GDLibRenderer`, validando `vendor/autoload.php`, `iconv` y `gd`.
- Se creo `QrCodeModel.php` para obtener o crear un token permanente base62 de 12 caracteres por tour usando prepared statements.
- Se creo `QrController.php` con dos flujos:
  - descarga protegida del QR para tours publicados pertenecientes al usuario autenticado;
  - redireccion publica `/qr/{token}` hacia `/tour/{businessSlug}/{tourSlug}?src=qr`.
- Se anadio la ruta protegida `/dashboard/negocios/{biz}/tours/{tour}/qr/download`.
- Se anadio la ruta publica `/qr/{token}`.
- Se anadio el boton "Descargar QR" en la gestion del tour, visible solo cuando el tour esta publicado.
- Se creo `docs/sql/2026-05-18_qr_codes_token.sql` con la migracion minima para anadir `token`, hacerlo unico y dejar un indice normal en `tour_id`.

### Decision
El QR apunta a `/qr/{token}` en lugar de apuntar directamente a `/tour/{businessSlug}/{tourSlug}`. Asi, si cambian los slugs del negocio o del tour en el futuro, el QR impreso podra seguir funcionando porque el token redirige al slug actual.
QR 1 reutiliza un token por tour mediante logica find-or-create; `tour_id` no es UNIQUE para permitir multiples tokens/campanas futuras.

### Pendiente antes de validar en servidor
- Ejecutar manualmente la migracion `docs/sql/2026-05-18_qr_codes_token.sql` en la BD antes de usar la descarga QR.
- Ejecutar `composer install --no-dev --optimize-autoloader` en servidor para instalar `bacon/bacon-qr-code` desde `composer.lock`.
- Probar descarga real y redireccion `/qr/{token}` en servidor.

### Que NO se hizo
- No se implementaron analiticas ni escritura en `qr_scans`.
- No se implemento PDF.
- No se implementaron QR por posicion ni campanas.
- No se guardaron PNG en disco.
- No se toco R2, `.env`, uploads ni logica de fotos.
- No se versiono `vendor/`.
- No se hizo commit ni push.

## 2026-05-18 - QR 1 validado en servidor real

Tipo: validacion real en produccion. Sin cambios de codigo en esta entrada.

### Que se valido
- Pull realizado en EC2.
- `composer install --no-dev --optimize-autoloader` ejecutado correctamente en servidor.
- `bacon/bacon-qr-code v3.1.1` instalado desde `composer.lock`.
- `php -l` correcto en:
  - `backend/services/QrCodeService.php`
  - `backend/models/QrCodeModel.php`
  - `backend/controllers/QrController.php`
  - `backend/routes/web.php`
  - `backend/views/dashboard/tours/manage.php`
- Migracion `docs/sql/2026-05-18_qr_codes_token.sql` ejecutada.
- `qr_codes` tiene `token VARCHAR(12)`, `UNIQUE KEY uq_qr_codes_token (token)` e indice normal `idx_qr_codes_tour_id (tour_id)`.
- `tour_id` NO es UNIQUE, para permitir multiples tokens/campanas futuras.
- El boton "Descargar QR" aparece en la gestion de un tour publicado.
- La descarga genera PNG correctamente.
- Escaneo real con movil redirige correctamente al tour publico.

### Resultado real de BD y HTTP
- BD confirmo fila QR:
  - `tour_id = 1`
  - `token = LAeYLVmf5QUb`
  - `filename = qr_LAeYLVmf5QUb.png`
  - `total_scans = 0`
- `curl -s -o /dev/null -D - https://oxphyre.com/qr/LAeYLVmf5QUb` devuelve `HTTP/2 302`.
- Header `location`: `/tour/primer-negocio-de-prueba/primer-tour-de-prueba?src=qr`.
- `curl -L` termina en `https://oxphyre.com/tour/primer-negocio-de-prueba/primer-tour-de-prueba?src=qr`.

### Decision confirmada
- QR 1 usa URL permanente `/qr/{token}`, no URL directa con slugs.
- `/qr/{token}` redirige con 302 a `/tour/{businessSlug}/{tourSlug}?src=qr`.
- El token es base62 de 12 caracteres generado desde PHP con `random_bytes()`.
- `token` es UNIQUE.
- `tour_id` NO es UNIQUE.
- El PNG se genera al vuelo, no se guarda en disco ni en R2.
- QR 1 reutiliza un token por tour mediante logica find-or-create, pero el esquema permite multiples tokens por tour para Pro/Business futuro.

### Pendientes
- QR 2: registrar escaneos en `qr_scans` y mostrar contador.
- QR 1.1 opcional: soportar HEAD en `/qr/{token}` para que `curl -I` ayude a debug sin cambiar el comportamiento GET. Actualmente `curl -I` devuelve 404 porque la ruta solo acepta GET; no bloquea QR 1 porque moviles/navegadores usan GET.

### Que NO se hizo
- No se implementaron analiticas: `total_scans` sigue en 0.
- No se implemento PDF.
- No se implementaron QR por posicion, campanas, logos ni gating Free/Pro/Business.
- No se guardo el PNG en disco ni R2.
- No se toco BD adicional, R2, `.env`, rutas ni Composer durante esta documentacion.
- No se hizo commit ni push.

## 2026-05-18 - QR 1.1 HEAD para debug

Tipo: mejora menor de mantenimiento. Sin cambios en GET ni analiticas.

### Que se hizo
- La ruta publica `/qr/{token}` acepta ahora `GET` y `HEAD` con la misma regex exacta `[A-Za-z0-9]{12}`.
- `QrController::redirectToTour()` mantiene la misma validacion de token, existencia y publicacion.
- `GET` conserva el comportamiento validado: devuelve 302 hacia `/tour/{businessSlug}/{tourSlug}?src=qr`.
- `HEAD` devuelve el mismo 302 y el mismo `Location`, pero sin body, para facilitar debug con `curl -I`.
- Token invalido o no existente devuelve 404 tambien en `HEAD`, sin body.

### Decision
`HEAD` queda solo como ayuda de mantenimiento/debug. No implementa analiticas y no debe contarse como escaneo cuando llegue QR 2.

### Que NO se hizo
- No se toco BD, Composer, R2, dashboard, `QrCodeService.php` ni `QrCodeModel.php`.
- No se implementaron analiticas ni escritura en `qr_scans`.
- No se hizo commit ni push.

## 2026-05-18 - QR 2A tracking basico local

Tipo: implementacion QR minima con privacidad. Pendiente de migracion y validacion real en servidor.

### Que se hizo
- Se creo `backend/models/QrScanModel.php` para registrar escaneos QR con prepared statements.
- `QrScanModel::recordScan()` inserta solo `qr_code_id`, `ip_hash`, `device_type` y `scanned_at`; deja `ip_address`, `user_agent` y `country` en `NULL`.
- `QrScanModel::isDuplicate()` deduplica por `qr_code_id + ip_hash` durante 30 minutos.
- `QrScanModel::countByQrCode()` y `countByTour()` calculan contadores desde `qr_scans` con `COUNT(*)`.
- `QrController::redirectToTour()` registra el escaneo solo en `GET /qr/{token}` valido, despues de validar token/tour publicado y antes del 302.
- `HEAD /qr/{token}` sigue siendo debug y no cuenta.
- Se filtro User-Agent vacio y bots basicos como curl, wget, bots, crawlers, headless, monitores y clientes automatizados.
- Se deriva `device_type` simple: mobile/tablet/desktop/unknown, sin guardar el User-Agent completo.
- El hash de IP usa `QR_HASH_SALT`; si falta, cae temporalmente a `APP_KEY` y despues a `APP_URL` para no romper local.
- `QrCodeModel::findByTourId()` pasa a ser publico para poder leer el QR existente sin crear uno nuevo.
- `TourController::showManage()` obtiene el contador total del QR existente sin crear token si no existe.
- `dashboard/tours/manage.php` muestra "X escaneos desde el QR" o "QR listo para compartir" junto al boton de descarga.
- Se creo `docs/sql/2026-05-18_qr_scans_2a_privacy_dedupe.sql` para anadir `ip_hash` e indices de deduplicacion/contador.
- `.env.example` documenta `QR_HASH_SALT` sin valor real.

### Motivo
QR 2A activa una metrica basica util para el dashboard sin ampliar alcance a graficas, stats, PDF, campanas, QR por posicion ni analitica avanzada. La implementacion reduce datos personales: no guarda IP clara, User-Agent completo ni geolocalizacion.

### Pendiente
- Ejecutar manualmente la migracion SQL en servidor antes de validar el tracking real.
- Definir `QR_HASH_SALT` real en `.env` de produccion.
- Validar en servidor que GET de navegador registra una vez, que HEAD no registra y que curl/wget no registran.

### Que NO se hizo
- No se toco BD real.
- No se toco `.env` real.
- No se uso ni actualizo `qr_codes.total_scans`.
- No se implementaron graficas, pagina stats, PDF, campanas, QR por posicion ni gating por plan.
- No se toco Composer, R2, uploads ni `QrCodeService.php`.
- No se hizo commit ni push.

## 2026-05-19 - QR 2A validado en servidor real

Tipo: cierre de analitica QR basica con privacidad e incidencia Nginx/Cloudflare resuelta.

### Que se valido
- La migracion `docs/sql/2026-05-18_qr_scans_2a_privacy_dedupe.sql` se ejecuto correctamente en servidor.
- `qr_scans` tiene `ip_hash VARCHAR(64)`, indice `idx_qr_scans_dedupe (qr_code_id, ip_hash, scanned_at)` e indice `idx_qr_scans_qr_code_scanned_at (qr_code_id, scanned_at)`.
- `GET /qr/{token}` valido registra escaneo en `qr_scans`.
- `HEAD /qr/{token}` no registra escaneo.
- curl, wget y bots filtrados por User-Agent no registran escaneo.
- No se guarda IP real: `ip_address = NULL`.
- No se guarda User-Agent completo: `user_agent = NULL`.
- No se guarda pais/geolocalizacion: `country = NULL`.
- Cada escaneo contado guarda solo `qr_code_id`, `ip_hash`, `device_type` y `scanned_at`.
- El contador simple se calcula desde `qr_scans` con `COUNT(*)` y aparece junto al boton "Descargar QR" en `manage.php`.
- `qr_codes.total_scans` queda como columna legacy/cache futura; QR 2A no la usa ni la actualiza para evitar inconsistencias.

### Incidencia encontrada
Durante la validacion inicial, dos GET con User-Agent de navegador en menos de 30 minutos crearon dos filas distintas:
- id 1, hash8 `99e2c11f`
- id 2, hash8 `ca3a6e14`

La deduplicacion no fallaba por SQL. El problema era que PHP recibia una IP distinta entre peticiones. En Nginx se encontro que el bloque PHP-FPM vaciaba las cabeceras:

```nginx
fastcgi_param HTTP_X_FORWARDED_FOR "";
fastcgi_param HTTP_CF_CONNECTING_IP "";
```

Al no llegar `HTTP_CF_CONNECTING_IP`, `QrController::getClientIp()` caia a `REMOTE_ADDR`. Detras de Cloudflare, `REMOTE_ADDR` puede ser el edge de Cloudflare y variar entre requests; al cambiar la IP usada para el hash, cambiaba `ip_hash` y la deduplicacion no podia encontrar duplicado.

### Solucion aplicada en servidor
Se cambio el bloque PHP-FPM de Nginx para pasar las cabeceras reales:

```nginx
fastcgi_param HTTP_X_FORWARDED_FOR $http_x_forwarded_for;
fastcgi_param HTTP_CF_CONNECTING_IP $http_cf_connecting_ip;
```

Despues se valido Nginx con:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

### Validacion final
Se ejecutaron dos GET con User-Agent de navegador separados por 10 segundos:

```bash
TOKEN="LAeYLVmf5QUb"

curl -A "Mozilla/5.0 QRTest" -s -o /dev/null -D - "https://oxphyre.com/qr/$TOKEN"
sleep 10
curl -A "Mozilla/5.0 QRTest" -s -o /dev/null -D - "https://oxphyre.com/qr/$TOKEN"
```

Resultado: solo se creo una fila nueva:
- id 3
- hash8 `319ee091`
- `device_type = desktop`
- `scanned_at = 2026-05-19 08:42:11`

No aparecio id 4. La deduplicacion de 30 minutos quedo validada.

### Decision
No anadir columna tipo `veces_escaneado`. La fuente de verdad es `qr_scans`: cada fila representa un escaneo contado. `COUNT(*) FROM qr_scans WHERE qr_code_id = ?` es el contador real y permite futuras analiticas por dia, dispositivo y evolucion temporal.

### Que NO se hizo
- No se cambio codigo PHP durante este cierre documental.
- No se modifico BD adicional.
- No se toco Composer, R2, `.env`, uploads ni codigo de fotos.
- No se hizo commit ni push.

## 2026-05-19 - Hotspots 1A contrato BD/modelo

Tipo: base tecnica para hotspots de navegacion. Sin editor visual ni render publico.

### Que se hizo
- Se creo `docs/sql/2026-05-19_hotspots_navigation_coordinates.sql` como migracion defensiva para `hotspots`.
- La migracion crea la tabla si no existe y, si ya existe una tabla legacy, anade columnas nuevas sin borrar columnas antiguas como `photo_id`.
- `photo_id` queda como columna legacy nullable para compatibilidad con produccion; el nuevo flujo no la usa como origen principal y guarda el origen logico en `position_id`.
- Se definio que los hotspots de navegacion se guardan como `yaw_rad` y `pitch_rad` en radianes relativos al cilindro de la panoramica principal, no como pixeles de pantalla ni porcentajes 2D.
- Se anadio `position_id` como origen logico del hotspot, `target_position_id` como destino y `panorama_photo_id` para saber sobre que panoramica se coloco.
- Se anadieron `needs_review`, `is_active`, `updated_at` y `deleted_at` para permitir ocultar hotspots desactualizados, desactivar sin borrar y aplicar soft delete.
- Se creo `backend/models/HotspotModel.php` con prepared statements para listar, listar publicos, crear hotspots de navegacion, soft delete, activar/desactivar y marcar una posicion como pendiente de revision.
- El modelo valida IDs positivos, `yaw_rad` entre `-M_PI` y `M_PI`, `pitch_rad` entre `-M_PI/2` y `M_PI/2`, y limpia `label` con `strip_tags()` limitandola a 80 caracteres.

### Decision
El contrato de coordenadas queda preparado para el visor Three.js actual: panoramica cilindrica parcial/adaptativa, FOV responsive y proyeccion futura a pantalla desde coordenadas angulares.

### Pendiente
- Ejecutar la migracion manualmente en servidor antes de usar hotspots.
- Hotspots 1B: inyectar hotspots validos en `TOUR_DATA` y render publico minimo con overlay HTML proyectado desde yaw/pitch usando datos manuales/de prueba. No editor todavia.
- Hotspots 1C: editor dashboard para crear/recolocar hotspots con click sobre la panoramica.
- Hotspots 1D: conectar `markNeedsReviewByPosition()` al flujo de sustitucion/borrado de panoramica y mostrar aviso/confirmacion en dashboard.
- Hotspots 1E: pulido UX/mobile/labels/limites si procede.

### Que NO se hizo
- No se ejecuto la migracion en BD real.
- No se implemento editor visual.
- No se implemento render publico.
- No se toco `TourController::showPublic()`, `backend/views/tour.php`, `public/js/tour-viewer.js`, `public/css/tour.css`, dashboard, fotos, R2, QR, Composer ni `.env`.
- No se hizo commit ni push.

## 2026-05-19 - Hotspots 1A validado en servidor real

Tipo: validacion real de migracion BD/modelo base de hotspots.

### Que se valido
- Se ejecuto en EC2: `mysql -u oxphyre -p oxphyre < docs/sql/2026-05-19_hotspots_navigation_coordinates.sql`.
- La tabla legacy `hotspots` ya existia en produccion con `photo_id INT UNSIGNED NOT NULL`, `type enum('navigation','info','link')`, `title`, `description`, `target_position_id`, `position_x`, `position_y` y `created_at`.
- La migracion se aplico correctamente sin romper columnas legacy.
- `photo_id` quedo nullable para compatibilidad con el nuevo flujo, pero no pasa a ser el origen principal.
- La tabla ahora incluye `position_id`, `panorama_photo_id`, `label`, `yaw_rad`, `pitch_rad`, `needs_review`, `is_active`, `updated_at` y `deleted_at`.
- Indices confirmados:
  - `idx_hotspots_position (position_id, deleted_at)`
  - `idx_hotspots_target_position (target_position_id)`
  - `idx_hotspots_public (position_id, type, is_active, needs_review, deleted_at)`

### Decision confirmada
- El nuevo origen logico de hotspots de navegacion es `position_id`, no `photo_id`.
- `photo_id`, `position_x` y `position_y` quedan como columnas legacy; el nuevo sistema no las usa como coordenadas principales.
- Las coordenadas principales son `yaw_rad` y `pitch_rad` en radianes relativos al cilindro de la panoramica principal.
- `panorama_photo_id` permitira detectar sustituciones de panoramica.
- `needs_review` permitira ocultar hotspots en publico hasta que se revisen.

### Siguiente bloque
- Hotspots 1B: inyectar hotspots validos en `TOUR_DATA` y render publico minimo con overlay HTML proyectado desde yaw/pitch usando datos manuales/de prueba. No editor todavia.

### Que NO se hizo
- No se implemento editor visual.
- No se implemento render publico.
- No se cambio codigo durante esta validacion documental.
- No se toco visor, dashboard, fotos, R2, QR, Composer ni `.env`.
- No se hizo commit ni push desde esta sesion.

## 2026-05-19 - Hotspots 1B render publico validado visualmente

Tipo: validacion visual en servidor real del render publico minimo de hotspots.

### Que se valido
- Se pivoto de `yaw_rad`/`pitch_rad` como fuente principal a `texture_x`/`texture_y`.
- Se creo y ejecuto la migracion `docs/sql/2026-05-19_hotspots_texture_coordinates.sql`.
- La tabla `hotspots` tiene `texture_x` y `texture_y`.
- Hotspot de prueba validado:
  - `position_id = 1`
  - `target_position_id = 2`
  - `texture_x = 0.5`
  - `texture_y = 0.5`
  - `label = 'Ir a probando R2'`
- `TOUR_DATA` trae el hotspot en la posicion 1 y `[]` en la posicion 2.
- El overlay aparece en el visor publico.
- El hotspot se mantiene anclado sobre la cortina al mover el visor horizontalmente.
- El hotspot se mantiene correctamente al modificar el tamano de ventana y con F12 responsive.

### Decision
- `texture_x` y `texture_y` pasan a ser la fuente principal para render publico y futuro editor visual.
- `yaw_rad` y `pitch_rad` quedan como legacy/derivadas futuras.
- La razon del cambio es UX/implementacion: el usuario crea hotspots haciendo click sobre la imagen, no introduciendo coordenadas angulares.
- `texture_x` y `texture_y` representan un punto relativo de la panoramica/textura. No son pixeles de pantalla ni posicion del visor.

### Pendiente
- Confirmar o documentar aparte si el click del hotspot ya navega a la posicion destino.
- Hotspots 1C: editor dashboard para crear/recolocar hotspots con click sobre la panoramica usando `texture_x`/`texture_y`.
- Hotspots 1D: `needs_review` automatico al sustituir/borrar panoramica + aviso/confirmacion en dashboard.
- Hotspots 1E: pulido UX/mobile/labels/limites si procede.

### Que NO se hizo en esta actualizacion documental
- No se toco codigo PHP, JS, CSS ni SQL.
- No se toco BD, visor, dashboard, fotos, R2, QR, Composer ni `.env`.
- No se hizo commit ni push desde esta sesion.

## 2026-05-19 - Hotspots 1C backend JSON seguro preparado

Tipo: preparacion backend/dashboard minimo para editor de flechas de navegacion. Sin editor visual completo.

### Que se hizo
- Se amplio `backend/models/HotspotModel.php` con metodos scoped para dashboard:
  - listar flechas de una posicion dentro de un tour;
  - crear flechas usando `texture_x`/`texture_y` como fuente principal;
  - mover/recolocar actualizando `texture_x`/`texture_y`;
  - activar/desactivar;
  - soft delete;
  - obtener una flecha por ID dentro de posicion/tour antes de modificar.
- Se creo `backend/controllers/HotspotController.php` para endpoints JSON protegidos.
- El controller valida ownership completo: usuario -> negocio -> tour -> posicion.
- Tambien valida que la posicion origen tenga panoramica `360`, que el destino pertenezca al mismo tour, que el destino tenga panoramica `360`, que no sea la misma posicion y que `texture_x`/`texture_y` esten entre 0 y 1.
- Los POST AJAX validan CSRF sin consumir el token, siguiendo el patron de `setActiveMode()`.
- Se registraron rutas JSON protegidas:
  - `GET /dashboard/hotspots/list`
  - `POST /dashboard/hotspots/create`
  - `POST /dashboard/hotspots/move`
  - `POST /dashboard/hotspots/toggle`
  - `POST /dashboard/hotspots/delete`
- En `backend/views/dashboard/position/upload.php` se sustituyo el bloque de "proximamente" por un bloque minimo visible como "Flechas de navegacion".
- Se creo `public/js/hotspot-editor.js` como esqueleto minimo: solo carga/lista flechas al pulsar "Editar flechas de navegacion".
- Se anadieron estilos minimos en `public/css/dashboard.css`.

### Decision tecnica
- `texture_x`/`texture_y` siguen siendo la fuente principal.
- Al crear o mover una flecha se rellenan `yaw_rad`/`pitch_rad` como valores legacy derivados para compatibilidad, pero no controlan el render ni el editor futuro.
- La palabra visible para el usuario es "flechas de navegacion"; no se usa "hotspot" en la UI.

### Pendiente
- Hotspots 1C visual: editor real sobre la panoramica con click/tap, preview de punto, selector de destino, mover arrastrando y guardado desde UI.
- Pulir textos/estados si aparecen flechas que apuntan a posiciones desactualizadas.
- Hotspots 1D: marcar `needs_review` automatico al sustituir o borrar panoramica.

### Que NO se hizo
- No se implemento el editor visual completo.
- No se cambio la BD ni se crearon migraciones.
- No se toco `public/js/tour-viewer.js` ni el visor publico.
- No se tocaron `TourController.php`, pipeline de imagenes, R2, QR, landing, planes/precios, `CLAUDE.md` ni `AI_SYNC.md`.
- No se hizo commit ni push.

## 2026-05-19 - Hotspots 1C-P0 editor visual minimo

Tipo: implementacion minima del editor visual de flechas de navegacion en dashboard.

### Que se hizo
- `PositionController::showUpload()` expone al JS la URL resuelta de la panoramica actual mediante `window.OXPHYRE_HOTSPOT_EDITOR.panoramaUrl`.
- `backend/views/dashboard/position/upload.php` muestra un panel de edicion al pulsar "Editar flechas de navegacion".
- El panel incluye:
  - panoramica actual;
  - instruccion clara para colocar una flecha;
  - marcador provisional;
  - selector de zona destino;
  - botones "Guardar flecha" y "Cancelar";
  - lista de flechas existentes.
- `public/js/hotspot-editor.js` carga flechas y destinos desde `/dashboard/hotspots/list`.
- Al hacer click sobre la panoramica calcula el punto relativo de la imagen y muestra el marcador provisional.
- Al guardar llama a `/dashboard/hotspots/create`, refresca la lista y muestra "Flecha guardada correctamente.".
- Si falla el guardado, muestra "No hemos podido guardar la flecha. Intentalo de nuevo.".
- `public/css/dashboard.css` anade estilos minimos para el panel visual, marcador y formulario.

### Decision
- El usuario no escribe coordenadas: el punto se obtiene del click sobre la panoramica.
- El editor guarda coordenadas relativas a la imagen completa, coherentes con `texture_x`/`texture_y` como fuente principal.
- No se implementa todavia recolocar flechas existentes ni flujo movil avanzado.

### Pendiente
- Probar en servidor real con sesion autenticada:
  - abrir posicion con panoramica y otra posicion destino;
  - crear flecha desde el dashboard;
  - confirmar que aparece en la lista;
  - abrir visor publico y confirmar que aparece y navega.
- Pulir editor visual despues de validar P0: mover flechas existentes, estados de error por flecha rota, mobile/crosshair si procede.

### Que NO se hizo
- No se toco `public/js/tour-viewer.js` ni el visor publico.
- No se cambio BD ni migraciones.
- No se tocaron R2, QR, MiDaS, pipeline de imagenes, landing ni planes.
- No se implemento recolocar flechas existentes, mobile con crosshair ni `needs_review`.
- No se hizo commit ni push.

## 2026-05-19 - Hotspots 1C-C apertura visual del editor

Tipo: correccion puntual de UI del editor de flechas de navegacion.

### Que se hizo
- Se corrigio `public/js/hotspot-editor.js` para retirar el atributo `hidden` de `#navigation-arrows-editor` cuando `/dashboard/hotspots/list` carga correctamente.
- La apertura del panel ya no depende de que existan flechas previas: si la lista viene vacia, el editor se muestra igualmente con el estado "Aun no hay flechas en esta zona.".

### Que NO se hizo
- No se tocaron backend, BD, visor publico, R2, QR, MiDaS, pipeline de imagenes, landing ni planes.
- No se implementaron nuevas funciones del editor.
- No se hizo commit ni push.

## 2026-05-19 - Hotspots 1B.1 y 1C listado visual + modal

Tipo: pivote de coordenadas a texture_x/texture_y, listado dashboard con estados y modal de colocacion.

### Que se hizo

#### Hotspots 1B.1 — coordenadas de textura

- `docs/sql/2026-05-19_hotspots_texture_coordinates.sql`: migracion idempotente que anade `texture_x FLOAT NULL` y `texture_y FLOAT NULL` a `hotspots` despues de `pitch_rad`. No modifica `yaw_rad` ni `pitch_rad`.
- `backend/models/HotspotModel.php`: `getValidForPublic()` ampliado para incluir `h.texture_x` y `h.texture_y` en SELECT y filtrar con `IS NOT NULL` y `BETWEEN 0 AND 1`. El array de retorno incluye `textureX` y `textureY`; `yawRad` y `pitchRad` quedan como legacy.
- `public/js/tour-viewer.js`: `updateHotspotOverlay()` reescrito para usar `textureX`/`textureY` como fuente principal de proyeccion. Formula: `theta = (u - 0.5) * widthAngle`, punto 3D con `(sin(theta)*r, (0.5-v)*h, -cos(theta)*r)`, identica a la que usa `createMainPanoramaGeometry()`. Eliminados `HOTSPOT_YAW_SIGN`, `normalizeHotspotAngle` y prefiltro de yaw que ya no son necesarios. `DEBUG_HOTSPOTS=false` conservado.

Razon del pivote: el usuario coloca flechas haciendo clic sobre la imagen plana en el dashboard, no introduciendo angulos. La coordenada UV directa elimina la fuente principal de desincronizacion entre el click del editor y la posicion en el visor.

#### Hotspots 1C — listado visual con estados

- `public/js/hotspot-editor.js`: `renderArrows()` sustituido por `renderTargetList()` que cruza `data.targets` con `data.arrows`:
  - Si no hay flecha hacia un destino: badge "Sin flecha" + boton "Anadir flecha".
  - Si ya existe flecha: badge "Enlazada" + botones "Editar flecha" y "Eliminar flecha".
  - Si `targets` viene vacio: mensaje "Aun no hay mas zonas..." en lugar del error anterior.
- `backend/views/dashboard/position/upload.php`:
  - Eliminado parrafo duplicado `upload-flow-copy` ("Conecta esta posicion con otras zonas del tour") que repetia el h3.
  - Texto de instrucciones cambiado a "Listado de zonas disponibles. Pulsa una zona para anadir o editar su flecha de navegacion."
  - `navigation-arrows-stage` marcado con `hidden` para que no aparezca al abrir el editor.
- `public/css/dashboard.css`: nuevas clases `.navigation-arrow-item-info`, `.navigation-arrow-item-actions`, `.navigation-arrow-state.is-linked` (badge ambar) y `.navigation-arrow-action-btn`/`--danger`.

#### Hotspots 1C — modal de colocacion

- `backend/views/dashboard/position/upload.php`: stage y form movidos fuera del editor inline y dentro de un modal `#navigation-arrows-modal` con overlay oscuro. El modal se renderiza condicionalmente solo cuando `$canEditNavigationArrows` es verdadero y siempre fuera del `<form id="upload-form">`. El `<select>` de destino fue sustituido por `<input type="hidden" id="navigation-arrows-target">` ya que el destino es siempre conocido al abrir el modal desde el listado.
- `public/js/hotspot-editor.js`: logica modal completa:
  - `openModal(targetName)` abre el modal con titulo "Colocar flecha hacia {nombre}", bloquea scroll de `body`.
  - `closeModal()` cierra el modal, restaura scroll.
  - `openStageForTarget(targetId, existingArrow)` abre el modal con modo add/edit, pre-llena `targetInput.value` y, si era edicion, muestra el marcador existente en la posicion guardada.
  - Clic en overlay y tecla Escape cierran el modal.
  - `saveDraft()` usa endpoint `create` en modo add y endpoint `move` en modo edit.
  - `deleteArrow()` usa endpoint `delete` con `confirm()` nativo.
  - Guard actualizado para referenciar nuevos elementos `modalEl`, `modalTitleEl`, `modalOverlayEl`, `targetInput` en lugar de `formEl` y `targetSelect`.
- `public/css/dashboard.css`: estilos del modal: `.navigation-arrows-modal` (fixed overlay), `.navigation-arrows-modal-overlay`, `.navigation-arrows-modal-box`, `.navigation-arrows-modal-title`, `.navigation-arrows-modal-hint`, `.navigation-arrows-modal-actions`. Override de imagen dentro del modal con `max-height: 60vh`.

#### Ajustes de espaciado CSS

- `.navigation-arrows-instructions`: `margin-top: 10px`.
- `.navigation-arrow-state.is-linked`: `margin-left: 10px`.
- `.navigation-arrow-item-actions`: `gap: 10px`.
- `.navigation-arrow-item-info`: `flex-wrap: wrap`, `gap: 0.6rem`.

### Problemas encontrados y soluciones

1. **Cache JS en produccion**: `hotspot-editor.js` cargaba version antigua porque el `<script src>` no tenia versioning. Se anadio `?v=20260519-4` al `<script src>` en `upload.php`. Diagnostico: consola mostraba `loadArrows @ hotspot-editor.js:55` (funcion antigua) mientras que `fetch('/js/hotspot-editor.js?v=' + Date.now())` confirmaba que el archivo en servidor ya tenia el codigo nuevo.

2. **Cache CSS en produccion**: `getComputedStyle(.navigation-arrows-instructions).marginTop` devolvia `0px` aunque el CSS nuevo en servidor contenia `margin-top: 10px`. Se anadio `?v=20260519-2` al `<link rel="stylesheet" href="/css/dashboard.css">` en `upload.php`.

3. **Listado oculto**: al pulsar "Editar flechas de navegacion", el editor no mostraba nada cuando `data.arrows` venia vacio porque `renderArrows()` solo iteraba flechas existentes. Se reemplazo por `renderTargetList()` que itera siempre `data.targets` y cruza con `data.arrows`.

4. **Panoramica gigante en pagina**: al abrir el editor, `navigation-arrows-stage` aparecia a pantalla completa sin restriccion porque no tenia `hidden` y su imagen no tenia `max-height` efectiva en ese contexto. Solucion: `hidden` en el elemento PHP y mover la panoramica a un modal con `max-height: 60vh`.

### Decisiones UX

- Texto publico: "flechas de navegacion", nunca "hotspots", "texture_x/y", "yaw/pitch" ni coordenadas.
- Estados del listado: "Sin flecha" (gris) y "Enlazada" (ambar).
- Botones por zona: "Anadir flecha", "Editar flecha", "Eliminar flecha".
- Destino siempre conocido antes de abrir el modal: el usuario elige la zona en el listado, no en un select dentro del editor.
- El marcador de edicion ya existente se muestra en el modal al abrir en modo edit.
- Eliminar flecha desde el listado (no desde el modal): es la accion destructiva, mejor separada del flujo de colocacion.

### Pendiente

- Validar en servidor real con sesion autenticada:
  1. Pulsar "Editar flechas de navegacion" y confirmar que el listado muestra zonas con estado correcto.
  2. Pulsar "Anadir flecha" y confirmar que el modal abre con panoramica contenida.
  3. Hacer clic sobre la imagen, guardar y confirmar flecha en listado.
  4. Pulsar "Editar flecha" y confirmar que el marcador existente se muestra en la posicion guardada.
  5. Abrir visor publico y confirmar que la flecha aparece anclada a la textura al arrastrar.
- Si el marcador en modo edit no aparece en la posicion correcta, revisar normalizacion de `textureX`/`textureY` desde `formatDashboardRow`.
- Hotspots 1D: `needs_review` automatico al sustituir o borrar panoramica.
- Hotspots 1E: pulido mobile/labels/limites y estado de flecha rota.

### Que NO se hizo
- No se toco `public/js/tour-viewer.js` salvo cambio ya documentado en Hotspots 1B.1.
- No se cambio BD ni se crearon migraciones salvo la de texture coordinates ya documentada.
- No se tocaron `TourController.php`, `PositionController.php`, pipeline de imagenes, R2, QR, MiDaS, landing ni planes.
- No se hizo commit ni push.

## 2026-05-19 - Hotspots 1C validado en servidor real + helper asset()

Tipo: validacion final de Hotspots 1C en produccion y correccion de cache de assets.

### Que se hizo

#### Validacion de Hotspots 1C en servidor real

Flujo completo validado con sesion autenticada en https://oxphyre.com:

1. Dashboard → posicion con panoramica y al menos una zona destino con panoramica.
2. Pulsar "Editar flechas de navegacion": el listado aparece con estados correctos.
3. Zona sin flecha muestra badge "Sin flecha" y boton "Anadir flecha".
4. Zona con flecha muestra badge "Enlazada" y botones "Editar flecha" / "Eliminar flecha".
5. Pulsar "Anadir flecha": modal se abre con titulo "Colocar flecha hacia {nombre}", panoramica contenida, sin desbordamiento visual.
6. Click sobre la panoramica coloca marcador provisional en la posicion pulsada.
7. Pulsar "Cancelar": modal se cierra, flecha no se crea, estado sigue "Sin flecha".
8. Pulsar "Guardar flecha": flecha creada, modal se cierra, listado se refresca, estado pasa a "Enlazada".
9. Pulsar "Editar flecha": modal se abre con el marcador existente en la posicion guardada previamente; se puede recolocar y guardar con el endpoint `move`.
10. Pulsar "Eliminar flecha": confirmacion nativa, soft delete, listado se refresca, estado vuelve a "Sin flecha".
11. Visor publico: la flecha aparece sobre la panoramica en el punto donde se coloco.
12. Hover sobre la flecha: muestra "Ir a" en linea superior y nombre de la posicion destino en linea inferior.
13. Click sobre la flecha: navegacion correcta a la posicion destino.

#### Helper asset() para cache-busting automatico

- `backend/config/config.php`: anadida funcion `asset(string $path): string` que devuelve la URL con `?v={filemtime}` usando el timestamp real del archivo en disco. Si el archivo no existe devuelve la ruta original. Valida el path con regex para evitar rutas raras. Disponible en todas las vistas sin ningun require_once adicional porque config.php se carga en el Front Controller antes de cualquier otro archivo.
- Vistas actualizadas para usar `asset()`:
  - `backend/views/tour.php`: `tour.css` y `tour-viewer.js`.
  - `backend/views/dashboard/position/upload.php`: `dashboard.css` y `hotspot-editor.js`.
  - `backend/views/dashboard/index.php`, `business/create.php`, `business/success.php`, `negocios/index.php`, `negocios/manage.php`, `tours/index.php`, `tours/create.php`, `tours/manage.php`, `position/create.php`: `dashboard.css`.
- El helper sustituye todos los `?v=20260519-X` manuales que habia que actualizar a mano en cada deploy. Ahora basta subir el archivo para que el version hash cambie automaticamente.

#### Mejora visual en flechas del visor publico

- `public/js/tour-viewer.js` + `public/css/tour.css`: el label de las flechas muestra dos lineas: "Ir a" (pequeño, 80% opacidad) arriba y el nombre de la posicion destino abajo. `aria-hidden` en el prefijo porque el `aria-label` del boton ya incluye "Ir a {nombre}".

### Archivos tocados

- `backend/config/config.php`
- `backend/views/tour.php`
- `backend/views/dashboard/position/upload.php`
- `backend/views/dashboard/index.php`
- `backend/views/dashboard/business/create.php`
- `backend/views/dashboard/business/success.php`
- `backend/views/dashboard/negocios/index.php`
- `backend/views/dashboard/negocios/manage.php`
- `backend/views/dashboard/tours/index.php`
- `backend/views/dashboard/tours/create.php`
- `backend/views/dashboard/tours/manage.php`
- `backend/views/dashboard/position/create.php`
- `public/js/tour-viewer.js`
- `public/css/tour.css`

### Pendiente

- Hotspots 1D: marcar `needs_review=1` automaticamente cuando se sustituye o borra la panoramica de una posicion que tiene flechas activas. Mostrar aviso/confirmacion en dashboard y ocultar hotspot en visor publico hasta que el propietario lo revise.
- Hotspots 1E: pulido UX mobile, limites de zoom/crosshair en movil, estado visual de flecha con `needs_review`.

### Que NO se hizo
- No se cambio BD ni migraciones.
- No se tocaron R2, QR, MiDaS, pipeline de imagenes, landing ni planes.

## 2026-05-19 - Hotspots 1D-B/C/D implementado y validado en servidor real

Tipo: automatizacion de `needs_review` al cambiar/borrar panoramica + avisos en dashboard.

### Que se hizo

#### 1D-B — Trigger automatico de needs_review

- `PositionController::deletePhoto()`: si `direction === '360'`, despues de `softDeleteByPositionAndDirection` se llama a `HotspotModel::markNeedsReviewByPosition((int) $position['id'])`. Las flechas de esa posicion pasan a `needs_review=1` y quedan ocultas en el visor publico.
- `PositionController::upload()`: dentro del bloque que procesa `photo_360` con exito, despues de `$photoModel->create()`, se llama a `markNeedsReviewByPosition($positionId)`. Una panoramica nueva puede descolocar flechas ya colocadas sobre la anterior; si no hay flechas, la llamada es no-op seguro.
- `HotspotModel::updateTextureScoped()`: anadido `h.needs_review = 0` al SET. Cuando el usuario recoloca y guarda una flecha, `needs_review` vuelve a 0 y la flecha vuelve a aparecer en el visor publico. Cierra el ciclo.
- `hotspot-editor.js`: `renderTargetList()` detecta `arrow.needsReview === true` y muestra badge "Revisar" en lugar de "Enlazada", y boton "Recolocar flecha" en lugar de "Editar flecha".
- `dashboard.css`: nueva clase `.navigation-arrow-state.is-review` con tono naranja/ambar.

#### 1D-C — Aviso en pantalla de gestion de posicion

- `HotspotModel::countNeedsReviewByPosition(int $positionId): int` anadido. Query COUNT(*) con filtros type/needs_review/deleted_at.
- `PositionController::showUpload()`: calcula `$navigationArrowsNeedReviewCount` y lo pasa a la vista.
- `upload.php`: aviso ambar visible entre flash y header cuando contador > 0. Titulo, texto sin jerga tecnica y boton/enlace "Revisar flechas" con anchor `#navigation-arrows-panel`.

#### 1D-D — Aviso en pantalla de gestion del tour

- `HotspotModel::getPositionsWithNeedsReviewByTour(int $tourId): array` anadido. JOIN con `positions`, GROUP BY posicion, devuelve `positionId`, `positionName`, `pendingCount` ordenado por `order_index`.
- `TourController::showManage()`: calcula `$arrowsNeedReviewByPosition` y mapa `$positionsWithArrowsNeedReview` indexado por positionId.
- `tours/manage.php`:
  - Aviso global ambar entre flash y header. Rama de 1 posicion (nombre + "Revisar flechas") y varias (lista con "Revisar" individual).
  - Badge pequeño "Flechas por revisar" en la card de cada posicion afectada.
  - Todos los enlaces apuntan al anchor `#navigation-arrows-panel` de la posicion correcta.

### Flujo validado en servidor real

1. Posicion con flecha activa, flecha aparece en visor publico.
2. Subir nueva panoramica 360, flecha pasa a badge "Revisar", desaparece del visor.
3. Aviso ambar visible en pantalla de posicion y en gestion del tour.
4. Badge "Flechas por revisar" en card de la posicion afectada.
5. Pulsar "Revisar flechas" / "Recolocar flecha", modal con nueva panoramica.
6. Guardar flecha, `needs_review=0`, vuelve a "Enlazada", vuelve a aparecer en visor.
7. Recargar cualquier pagina, avisos desaparecen.

### Deuda tecnica pendiente P1

- Los avisos en `upload.php` y `tours/manage.php` usan estilos inline. Funciona correctamente. Mover a clases reutilizables en `dashboard.css` en un paso de modularizacion posterior.

### Pendiente de validacion

- Probar borrar panoramica (no solo sustituir) para confirmar el mismo ciclo.

### Archivos tocados

- `backend/controllers/PositionController.php`
- `backend/controllers/TourController.php`
- `backend/models/HotspotModel.php`
- `backend/views/dashboard/position/upload.php`
- `backend/views/dashboard/tours/manage.php`
- `public/js/hotspot-editor.js`
- `public/css/dashboard.css`

### Que NO se hizo
- No se cambio BD ni migraciones.
- No se tocaron R2, QR, MiDaS, pipeline de imagenes, landing ni planes.

## 2026-05-20 - UX upload vuelve a la misma posicion

Tipo: ajuste puntual de flujo tras guardar/procesar fotos.

### Que se hizo

- `PositionController::upload()`: la redireccion final tras procesar fotos ahora vuelve a `/dashboard/posicion/upload?position={positionId}&negocio={bizSlug}&tour={tourSlug}` en lugar de volver al listado de posiciones del tour.
- Se mantienen los flash success/error/warnings existentes y no se cambia la logica de procesado de imagenes.

### Motivo

- Al subir o actualizar una panoramica, el propietario necesita revisar la misma posicion y recolocar flechas marcadas como "Revisar" sin perder el contexto de trabajo.

### Que NO se hizo

- No se tocaron HotspotModel, HotspotController, visor publico, BD, R2, QR, MiDaS, pipeline pesado ni planes.
- No se hizo commit ni push.

## 2026-05-20 - Limpieza CSS avisos de flechas por revisar

Tipo: cierre de deuda tecnica P1 de Hotspots 1D.

### Que se hizo

- `public/css/dashboard.css`: creadas clases reutilizables `navigation-review-alert`, `navigation-review-alert__icon`, `navigation-review-alert__content`, `navigation-review-alert__title`, `navigation-review-alert__text`, `navigation-review-alert__list`, `navigation-review-alert__item`, `navigation-review-alert__item-icon`, `navigation-review-alert__action`, `navigation-review-alert__action--compact` y `navigation-review-badge`.
- `backend/views/dashboard/position/upload.php`: el aviso de flechas pendientes deja de usar CSS inline propio y usa las clases reutilizables.
- `backend/views/dashboard/tours/manage.php`: el aviso global y el badge "Flechas por revisar" dejan de usar CSS inline propio y usan las clases reutilizables.
- `AI_SYNC.md` y `CLAUDE.md`: actualizados para reflejar que la deuda P1 de estilos inline quedo cerrada.

### Que NO se hizo

- No se cambio logica PHP, textos, enlaces, JS ni carga de CSS.
- No se tocaron BD, R2, QR, MiDaS, pipeline, landing, planes ni visor publico.
- No se hizo commit ni push.

## 2026-05-20 - Microfix UX en flechas pendientes de revisar

Tipo: mejora puntual de claridad en Hotspots 1D.

### Que se hizo

- `public/js/hotspot-editor.js`: al abrir el modal de una flecha con `needsReview`, se muestra el aviso "Esta flecha no se ve en el tour porque cambiaste la panorámica. Colócala de nuevo para que vuelva a aparecer.".
- `public/js/hotspot-editor.js`: al guardar una flecha que estaba en revision, el estado muestra "¡Listo! La flecha ya vuelve a verse en el tour."; las flechas normales mantienen "Flecha guardada correctamente.".
- `public/css/dashboard.css`: añadida la clase `navigation-arrows-review-notice` para el aviso dentro del modal.

### Que NO se hizo

- No se toco backend, BD, visor publico, endpoints, logica de guardado ni JS ajeno al editor.
- No se hizo commit ni push.

## 2026-05-20 - Mapa 1A base de ubicacion del negocio

Tipo: preparacion de base de datos, modelo, controller y formularios para SEO local/API externa posterior.

### Que se hizo

- `docs/sql/2026-05-20_business_location_fields.sql`: migracion idempotente para anadir en `businesses` los campos `city`, `postal_code`, `country`, `latitude`, `longitude`, `geocoded_at` y `geocoding_provider`.
- `backend/models/BusinessModel.php`: `create()` y `update()` aceptan y guardan `city`, `postal_code` y `country` con prepared statements. `getByUser()` tambien devuelve los campos visibles de ubicacion.
- `backend/controllers/BusinessController.php`: `store()` y `update()` leen/sanean `city`, `postal_code` y `country`; validan maximos simples sin hacerlos obligatorios.
- `backend/views/dashboard/business/create.php`: anadida seccion "Ubicacion de tu negocio" con direccion, ciudad, codigo postal y pais.
- `backend/views/dashboard/negocios/manage.php`: anadida la misma seccion en edicion y se muestra la ubicacion compuesta en la ficha del negocio si existe.
- `public/css/dashboard.css`: estilos reutilizables para la seccion de ubicacion.
- `AI_SYNC.md` y `CLAUDE.md`: actualizado el estado vivo y la decision de que la ubicacion pertenece al negocio.

### Que NO se hizo

- No se implemento Nominatim, Leaflet, boton "Buscar en el mapa", card publica "Donde estamos" ni cambios CSP.
- No se tocaron tour publico, Hotspots, QR, R2, MiDaS, pipeline, landing ni planes.

## 2026-05-20 - Cierre de decisiones comerciales de planes pre-/precios

Tipo: decision comercial/documentacion. Sin cambios de codigo.

### Decision

Antes de implementar `/precios` se cerraron las contradicciones entre `Planes_Oxphyre.md`, `CLAUDE.md`, `AI_SYNC.md` y la definicion viva del producto:

- **Free**: 3 posiciones (no 5 como decian CLAUDE.md y AI_SYNC.md). QR basico con branding Oxphyre incluido. Flechas de navegacion basicas incluidas. Mapa/ubicacion del negocio incluido. Sin embed. Watermark mas visible: overlay semitransparente + badge "Creado con Oxphyre" clicable hacia /precios.
- **Pro**: QR profesional (distinto del QR basico de Free). Embed/iframe solo desde Pro. Sin marca de agua.
- **Business**: negocios y posiciones ilimitadas, marca blanca, dominio personalizado, API, features avanzadas como roadmap.
- **QR en todos los planes**: Free = basico con branding; Pro/Business = profesional con analiticas.
- **Embed solo Pro+**: Free solo enlace publico, no incrustable en web propia.
- Se elimina de la definicion vigente la estrategia historica de "1 posicion con MiDaS como credito de prueba" — era una propuesta anterior, no el estado actual.

### Archivos actualizados

- `Planes_Oxphyre.md`: contradicciones resueltas en seccion FREE, PRO actualizado con QR profesional/embed, tabla de decisiones cerradas añadida al final.
- `CLAUDE.md`: seccion `### FREE (0€)` actualizada con 3 posiciones, QR basico, mapa, watermark agresiva, nota historica de la estrategia anterior.
- `AI_SYNC.md`: seccion `### Planes SaaS` reescrita con decision vigente completa.
- `DEVLOG.md`: esta entrada.

### Que NO se hizo

- No se toco codigo funcional.
- No se implemento `/precios`.
- No se modifico ningun controller, modelo, vista ni JS.
- No se hizo commit ni push.

## 2026-05-20 - Mapa 1B/1C — Geocodificacion Nominatim y mapa publico Leaflet

Tipo: implementacion de API externa (requisito tribunal TFG) + SEO local. Validado en servidor real.

### Que se hizo

#### Mapa 1B — Geocodificacion server-side con Nominatim/OpenStreetMap

- `backend/controllers/BusinessController.php`: metodo `geocode()` nuevo. Endpoint `POST /dashboard/negocios/{slug}/geocode`. Valida sesion, ownership y CSRF (sin consumir — el formulario de edicion tambien lo necesita). Lee valores del formulario desde el body JSON, no desde BD ni desde el cliente. Llama server-side a Nominatim con `curl`, timeout 8s, User-Agent `Oxphyre/1.0 (TFG tour virtual; https://oxphyre.com)`. Valida lat/lng en rango valido y no 0.0/0.0. Guarda `address`, `city`, `postal_code`, `country`, `latitude`, `longitude`, `geocoded_at` y `geocoding_provider='nominatim'` en BD para mantener coherencia entre coordenadas y direccion mostrada al usuario. No acepta lat/lng desde cliente.
- `backend/models/BusinessModel.php`: `saveGeocoding()` guarda tambien los campos de direccion ademas de las coordenadas.
- `backend/routes/web.php`: ruta `POST /dashboard/negocios/([a-z0-9-]+)/geocode` con guard auth.
- `backend/views/dashboard/negocios/manage.php`: boton "Buscar en el mapa" y parrafo de estado `#business-geocode-status` debajo de los campos de ubicacion.
- `public/js/business-location.js` (nuevo): JS vanilla con `fetch` POST JSON al endpoint geocode. Lee campos actuales del formulario (no la version guardada en BD). Muestra estados de carga/exito/error sin recargar pagina.
- `public/css/dashboard.css`: clases `.business-geocode-row`, `.business-geocode-status`, `.business-geocode-status--success` y `.business-geocode-status--error`.

Validado en servidor real con "Calle Preciados 7, Madrid, Espana". BD confirmo `latitude`, `longitude`, `geocoded_at` y `geocoding_provider='nominatim'` guardados correctamente.

#### Mapa 1C — Mapa publico en tour con Leaflet/OpenStreetMap

- `backend/controllers/TourController.php`: `showPublic()` extrae `$businessLocation` con lat, lng, address, city, postalCode y country. Anade `location.hasCoords/lat/lng` a `$tourData`. Solo lat/lng viajan al cliente en el JSON; datos textuales se usan en PHP.
- `backend/views/tour.php`:
  - Schema.org `LocalBusiness` JSON-LD en `<head>` si `hasCoords`: `name`, `PostalAddress` y `GeoCoordinates`.
  - Leaflet CSS desde CDN jsdelivr (`leaflet@1.9.4`), solo si `hasCoords`.
  - Boton `#tour-location-btn` (top-center, pill glassmorphism, icono pin de mapa + "Donde estamos"). Solo se renderiza si `hasCoords`.
  - Bottom sheet: `#tour-location-backdrop` (overlay oscuro/blur) + `#tour-location-sheet` con header (titulo "Donde estamos" + boton X), cuerpo con nombre del negocio, `<address>` con datos textuales, contenedor del mapa Leaflet y enlace "Como llegar" a `openstreetmap.org/directions?to={lat},{lng}` en nueva pestana.
  - Leaflet JS y `tour-location.js` al final del body, condicionalmente.
- `public/js/tour-location.js` (nuevo): inicializacion lazy de Leaflet (primera apertura: init a los 50ms + `invalidateSize` a los 350ms para esperar la animacion CSS de subida del sheet; aperturas siguientes: solo `invalidateSize`). Apertura/cierre con clase `.is-open`, bloqueo del visor via `body.location-sheet-open`, cierre con X/backdrop/Escape.
- `public/js/tour-viewer.js`: `handleGyro()` agrega comprobacion `body.location-sheet-open` al mismo patron que ya usaba para `body.room-is-open`.
- `public/css/tour.css`: boton top-center con `left: 50%; transform: translateX(-50%)`, backdrop (`z-index: 200`, blur + oscuro), bottom sheet (slide-up `cubic-bezier(0.34,1.08,0.64,1)`, ancho `min(860px, calc(100vw - 32px))`, `max-height: 78vh` desktop, 100% movil con border-radius), mapa `320px` desktop / `240px` movil, enlace "Como llegar" en acento ambar, `.tour-location-biz-name` para el nombre.
- `public/index.php`: CSP actualizada con `https://*.tile.openstreetmap.org` y `https://cdn.jsdelivr.net` en `img-src` para tiles OSM y markers de Leaflet.

Validado visualmente en produccion: boton visible en visor, sheet sube suavemente, backdrop oscurece el visor, mapa Leaflet carga con pin en la ubicacion del negocio, drag/zoom funcionan, "Como llegar" abre OSM en nueva pestana, visor no gira mientras el sheet esta abierto.

#### Aportacion al TFG

Nominatim/OpenStreetMap cubre el **requisito de API externa del tribunal**: llamada HTTP server-side real a un servicio externo, con gestion de errores, validacion de respuesta y persistencia en BD. Leaflet + OSM en el visor publico es la segunda capa visible para el tribunal. Se eligio OpenStreetMap/Nominatim/Leaflet sobre Google Maps/Mapbox: gratuito, sin API key, sin cuotas en uso razonable con User-Agent correcto, codigo abierto, y sin dependencia de servicios de pago.

### Archivos tocados

- `backend/controllers/TourController.php`
- `backend/controllers/BusinessController.php`
- `backend/models/BusinessModel.php`
- `backend/views/tour.php`
- `backend/views/dashboard/negocios/manage.php`
- `backend/routes/web.php`
- `public/js/tour-location.js` (nuevo)
- `public/js/tour-viewer.js`
- `public/js/business-location.js` (nuevo)
- `public/css/tour.css`
- `public/css/dashboard.css`
- `public/index.php`

### Que NO se hizo

- No se implemento Mapa 1D ni card publica "Donde estamos" en paginas de negocio fuera del tour.
- No se uso Google Maps ni Mapbox.
- No se tocaron Hotspots, QR, R2, MiDaS, pipeline, landing ni planes.
- No se hizo commit ni push.

## 2026-05-20 - Coherencia visual entre landing precios y /precios

Tipo: ajuste visual acotado.

### Que se hizo

- `backend/views/home.php`: se anadio un CTA debajo de las cards de la seccion `#precios` hacia `/precios`, sin cambiar cards ni layout.
- `public/css/main.css`: se anadieron solo clases nuevas para el CTA de la landing.
- `public/js/i18n.js`: se anadieron claves ES/EN para el nuevo CTA.
- `backend/views/precios.php`: se aumento el aire del bloque superior, se anadio un halo/esfera CSS estatico local y se corrigio la card Business para que los textos `Próximamente` queden en linea.

### Que NO se hizo

- No se cargo `main.js` ni Three.js en `/precios`.
- No se tocaron rutas, dashboard, BD, Hotspots, QR, R2, mapa publico ni pipeline.
- No se hizo commit ni push.

## 2026-05-21 - Reubicacion CTA comparativa en landing

Tipo: ajuste visual acotado en seccion de precios.
Mover cta precios de section 3 a section 6.

### Que se hizo

- `backend/views/home.php`: se movio el bloque `.pricing-details-cta` desde el cierre de `#como-funciona` hasta el cierre natural de `#precios`, justo debajo de las cards de planes.
- `backend/views/home.php` y `public/js/i18n.js`: se actualizo el texto ES/EN del CTA para orientar a comparar limites, funciones y diferencias plan por plan.
- `public/css/main.css`: se ajustaron solo clases `.pricing-details-cta*` para dar al CTA aire, contenedor propio y coherencia visual bajo las cards.

### Que NO se hizo

- No se tocaron `.pricing-card`, `.pricing-grid` ni `.plan-*`.
- No se toco `/precios`, `main.js`, rutas, dashboard, BD, Hotspots, QR, R2, mapa publico ni pipeline.


