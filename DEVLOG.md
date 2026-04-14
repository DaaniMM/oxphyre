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