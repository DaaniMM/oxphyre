---

### Seguridad (nivel producción)
- Passwords con password_hash() bcrypt
- Sesiones con regeneración de ID tras login
- CSRF tokens en todos los formularios
- Prepared statements en todas las queries (sin SQL injection)
- Rate limiting en login (máx 5 intentos, bloqueo temporal)
- Verificación de email al registrarse
- Recuperación de contraseña con token de un solo uso
- Headers de seguridad en Nginx (X-Frame-Options, CSP, HSTS)
- Sanitización de inputs en cliente y servidor

---

### UX/UI
- CSS custom con variables, sin frameworks pesados (demuestra dominio real)
- Animaciones con CSS + JavaScript Intersection Observer
- Fuente: Inter o Plus Jakarta Sans (Google Fonts)
- Paleta: oscura y futurista con acento naranja/ámbar (acorde con Oxphyre)
- Three.js integrado directamente en la hero de la landing
- Micro-interacciones en botones, inputs y transiciones
- Loading states y skeleton loaders
- 100% responsive para todos los dispositivos

---

### Performance y SEO (puntuado por el tribunal)
- Imágenes en WebP con lazy loading
- CSS y JS minificados
- Gzip activado en Nginx
- Cache headers configurados
- Sin librerías innecesarias
- Meta tags completos
- Open Graph para redes sociales
- sitemap.xml y robots.txt optimizados
- Objetivo: PageSpeed 100 en mobile y desktop

---

### Multiidioma
- Español e inglés como idiomas base
- Arquitectura preparada para añadir más idiomas en el futuro
- Selector de idioma visible en header y footer

---

### Legal y RGPD
- Banner de cookies obligatorio (RGPD = Reglamento General de Protección de Datos europeo)
- Política de privacidad real
- Términos y condiciones
- Todo visible desde el footer

---

### PWA (Progressive Web App)
Orientada principalmente a los visitantes que escanean el QR desde móvil.
- manifest.json → nombre, icono, colores de la app
- service-worker.js → cachea recursos para carga rápida con mala conexión
- Si en el futuro hay demanda real, se desarrolla app nativa

---

### Gestión de imágenes 360°
**Flujo:** Usuario sube foto → Python la procesa y optimiza (Pillow) → se guarda en servidor → Three.js la carga en el tour.
- Para el TFG: almacenamiento directo en el servidor EC2
- Para producción futura: migrar a AWS S3 (más escalable)
- Pendiente definir: formatos aceptados, tamaño máximo, resolución mínima recomendada

---

### Sistema de emails transaccionales
- Librería: PHPMailer + Gmail SMTP
- Gratuito, profesional, sin dependencias externas complejas
- Casos de uso: verificación de email, bienvenida, recuperar contraseña, notificación de contacto
- Instalación: Composer en el backend

---

### n8n - Automatización
- Herramienta de automatización visual self-hosted (gratuita)
- Casos de uso previstos: email bienvenida automático, alerta nuevos registros, notificación escaneos QR, recordatorio usuarios inactivos
- ⚠️ IMPORTANTE: verificar que la instancia EC2 t3.micro aguanta n8n junto al resto del stack antes de implementar. Si no hay RAM suficiente, dejar como integración futura documentada.
- Decisión: implementar al final si hay tiempo y recursos

---

### Esquema de base de datos

**users** → id, name, email, password, role, email_verified, verification_token, reset_token, reset_token_expires, created_at, updated_at

**businesses** → id, user_id, name, slug, logo, description, phone, address, plan_id, plan_expires_at, is_active, created_at, updated_at

**plans** → id, name, max_tours, max_photos_per_tour, watermark, analytics, price_monthly, created_at

**tours** → id, business_id, title, description, slug, is_published, views_count, created_at, updated_at

**photos** → id, tour_id, filename, original_filename, order_index, is_360, processed, created_at

**hotspots** → id, photo_id, type, title, description, target_photo_id, position_x, position_y, position_z, created_at

**qr_codes** → id, tour_id, filename, total_scans, created_at

**qr_scans** → id, qr_code_id, ip_address, user_agent, device_type, country, scanned_at

**contact_messages** → id, name, email, subject, message, is_read, created_at

**cookies_consent** → id, session_id, accepted, created_at

---

### Prioridad de desarrollo
1. Arquitectura MVC + router + estructura de carpetas backend
2. Esquema BD → crear todas las tablas en MySQL
3. Landing page impactante con Three.js en hero
4. Auth completa y segura (registro, login, verificación email, recuperar password)
5. Dashboard con creación y gestión de tours
6. Vista del tour 360° con hotspots navegables
7. QR descargable con analíticas básicas de escaneos
8. Onboarding wizard para nuevos negocios
9. Página de precios con los tres planes
10. Formulario de contacto con PHPMailer
11. Panel de administración (admin)
12. 404/500 personalizadas
13. Legal: cookies, términos, privacidad, RGPD
14. Multiidioma (español/inglés)
15. PWA (manifest.json + service-worker.js)
16. Optimización PageSpeed (minificación, WebP, gzip, cache)
17. SEO técnico (sitemap.xml, robots.txt, meta tags, Open Graph)
18. n8n (solo si hay tiempo y RAM suficiente)

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
- `public/360/` → fotos 360° de los negocios
- `public/models/` → modelos 3D (.glb) para hotspots
- `public/assets/` → imágenes, iconos, fuentes
- `backend/` → API REST en PHP
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
  - Puerto 80, root en /var/www/oxphyre/public
  - Rutas / → archivos estáticos (Three.js, HTML, CSS)
  - Rutas /api → PHP
- Repo clonado en /var/www/oxphyre
- Verificado: http://13.62.93.7 sirve correctamente

**Paso 5 - Flujo de trabajo establecido**
- Desarrollo en local (VSCode)
- git push desde local a GitHub
- git pull en el servidor para desplegar
- El servidor siempre tiene la versión actualizada

**Paso 6 - Base de datos MySQL**
- Creada base de datos: `oxphyre` (utf8mb4)
- Creado usuario: `oxphyre`@`localhost` con permisos completos sobre la BD
- Seguridad aplicada: sin usuarios anónimos, sin acceso root remoto, BD test eliminada

**Paso 7 - Microservicio Python**
- Entorno virtual creado en `/var/www/oxphyre/python-service/venv`
- Librerías instaladas: Flask 3.1.3, Pillow 12.2.0
- Flask: framework para crear la API REST del microservicio
- Pillow: librería para procesar y optimizar imágenes 360°
- El venv está en .gitignore (no se sube a GitHub, se crea en cada servidor)

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
- Renovación automática configurada (expira 08/07/2026)
- La app ya es accesible en https://oxphyre.com