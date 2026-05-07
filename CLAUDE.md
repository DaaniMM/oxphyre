# CLAUDE.md - Oxphyre Project Context

> Lee también AGENTS.md para instrucciones de comportamiento.
> Lee DEVLOG.md para historial completo de decisiones y avances.

## Qué es Oxphyre
SaaS de tours virtuales inmersivos para pequeños negocios locales. El dueño sube fotos de su local (4 por posición: N,S,E,O) → Python + MiDaS genera mapas de profundidad reales (disponible en Pro y Business) → editor canvas drag&drop permite construir la estructura de navegación del local → Three.js renderiza el tour inmersivo con hotspots, minimapa (Pro/Business) y tour guiado (Pro/Business) → clientes visitan escaneando un QR o mediante embed en su web (Pro/Business). Plan Free incluye 1 posición con MiDaS de prueba y 4 posiciones con esfera Three.js navegable sin profundidad IA.

## Stack técnico
- **Frontend:** HTML5 + CSS custom con variables globales + JS vanilla + Three.js
- **Backend:** PHP 8.1 puro, patrón MVC, Front Controller (todo pasa por index.php)
- **BD:** MySQL 8.0 · BD: `oxphyre` · usuario: `oxphyre`@`localhost`
- **Python:** Flask + Pillow + MiDaS (Intel, open source, profundidad real con IA gratuita)
- **Emails:** PHPMailer + Gmail SMTP
- **Despliegue:** AWS EC2 · IP: 13.62.93.7 · Dominio: https://oxphyre.com
- **OS servidor:** Ubuntu 22.04 · Nginx · PHP-FPM · Let's Encrypt
- **Repo:** /var/www/oxphyre en servidor · github.com/DaaniMM/oxphyre

## Estructura del proyecto
oxphyre/
public/              → frontend servido por Nginx
assets/            → imágenes, iconos, fuentes
css/               → estilos compilados
js/                → scripts vanilla + Three.js
uploads/           → fotos procesadas de los negocios
backend/
controllers/       → lógica de negocio
models/            → acceso BD (prepared statements siempre)
views/             → templates PHP
routes/            → mini-router
middleware/        → auth, roles, rate limiting
config/            → BD, constantes, .env loader
python-service/      → Flask + Pillow + MiDaS
venv/              → en .gitignore, no se sube
docs/                → memoria TFG
DEVLOG.md            → diario de desarrollo
AGENTS.md            → instrucciones de comportamiento
CLAUDE.md            → este archivo
.env                 → credenciales (en .gitignore, nunca a GitHub)

## Base de datos - tablas principales
users, businesses, plans, tours, positions, photos, hotspots, qr_codes, qr_scans, contact_messages, cookies_consent

## Rutas importantes del servidor
- Proyecto: `/var/www/oxphyre`
- Nginx config: `/etc/nginx/sites-available/oxphyre`
- Logs Nginx: `/var/log/nginx/`
- PHP config: `/etc/php/8.1/fpm/`
- Certbot: `/etc/letsencrypt/live/oxphyre.com/`
- Python venv: `/var/www/oxphyre/python-service/venv`

## Planes SaaS — Definición técnica y comercial

### FREE (0€)
- 1 tour, 1 negocio (no se pueden crear más tours ni negocios adicionales)
- Hasta 5 posiciones por tour
- 1 posición con MiDaS real incluida como crédito de prueba permanente
- Las otras 4 posiciones: esfera Three.js navegable con efecto parallax/giroscopio, sin profundidad IA (foto plana dentro de la esfera)
- Todas las posiciones conectadas con hotspots navegables (misma estructura que Pro)
- Sin minimapa
- Sin embed/iframe — solo enlace público oxphyre.com/[slug-negocio]
- Marca de agua Oxphyre visible dentro del visor
- URL siempre bajo dominio oxphyre.com (nunca dominio propio)
- Objetivo estratégico: el contraste entre la posición MiDaS y las 4 planas genera disonancia que impulsa el upgrade a Pro

### PRO (19€/mes — 182€/año)
- MiDaS activado en todas las posiciones (profundidad 3D real)
- Hasta 5 negocios, 20 posiciones por tour, tours ilimitados
- Minimapa automático generado desde el canvas
- Sin marca de agua
- Embed/iframe para incrustar el tour en la web propia del negocio
- QR descargable
- Hotspots informativos: el dueño añade pines sobre el espacio con texto, descripción o precio
- Tour guiado automático: el dueño define el orden de posiciones y un mensaje por posición; la cámara va sola y muestra los mensajes al visitante
- Compartir en redes sociales: botón para compartir directamente en WhatsApp, Instagram y Google Maps
- Foto de portada personalizable: imagen Open Graph propia al compartir el enlace
- Idioma del tour elegido por el dueño (español o inglés), sin traducción automática
- Chatbot básico precargado: el dueño configura hasta 60 preguntas frecuentes y respuestas (horario, precios, ubicación, reservas...); basado en palabras clave, sin IA, se ejecuta en el navegador del visitante
- Analíticas básicas: visitas totales, escaneos QR, dispositivo (móvil/desktop/tablet), visitas por día
- Analíticas Business visibles pero bloqueadas con candado + CTA de upgrade
- URL bajo oxphyre.com/negocio (sin dominio propio)
- Soporte por email, respuesta en 48h, acceso a documentación y tutoriales

### BUSINESS (49€/mes — 470€/año)
- Todo lo incluido en Pro, más:
- Negocios ilimitados, posiciones ilimitadas por tour
- Dominio personalizado (tour.tunegocio.com) — marca blanca total, sin rastro de Oxphyre en URL ni visor
- Tours privados con contraseña — acceso restringido a compradores o clientes cualificados
- Historial de versiones del tour — posibilidad de restaurar versiones anteriores
- Integración con Google My Business — publicar el tour directamente en la ficha de Google del negocio
- Traducción automática IA de todos los textos del tour (hotspots, tour guiado, descripciones)
- Hotspots enriquecidos: además de texto, permiten vídeo embebido, botón de reserva directa y formulario de contacto
- Múltiples usuarios con acceso al dashboard (dueño + empleados con roles diferenciados)
- API access para integrar el tour en sistemas propios del negocio
- Agente IA completo (OpenClaw/Make/n8n): responde con lenguaje natural, recoge leads, detecta intención del visitante, crea perfil del visitante, notifica al dueño por WhatsApp/email/Telegram, conecta con calendario para reservas directas — IMPLEMENTACIÓN PREVISTA EN ROADMAP, marcado como "próximamente" en la UI hasta su despliegue
- Analíticas avanzadas completas: mapa de calor de posiciones más visitadas, tiempo medio por posición, país y ciudad del visitante, fuente de tráfico (QR/embed/enlace directo), tasa de rebote por posición, comparativa entre tours, exportación CSV, alertas de pico de visitas
- Soporte prioritario por email + chat, respuesta en 24h, onboarding personalizado (llamada de configuración inicial incluida)

## Contexto TFG
- Estudiante DAW (Desarrollo de Aplicaciones Web), 2º año
- Entrega: finales mayo 2026
- Objetivo: nota máxima + producto real comercializable
- El tribunal evaluará específicamente: SEO, PageSpeed, seguridad (intentarán inyecciones SQL y XSS), UX/UI, MVC correcto
- Exposición: profesores probarán la app en tiempo real desde sus portátiles escaneando un QR

### Estrategia de procesado MiDaS y demo para la exposición

#### Hardware del desarrollador (PC local)
- CPU: Intel Core i5-12400F 12th Gen
- RAM: 16GB DDR4 3200MHz (uso normal ~63% con Chrome abierto, ~50% sin Chrome)
- GPU: NVIDIA GeForce RTX 3060 12GB VRAM (CUDA 13.0)
- Disco C:: 948GB total, ~24.5GB libres actualmente (liberar ~90GB borrando Fortnite de Epic Games)
- Python: 3.12.6 instalado en Windows
- OS: Windows 11

#### Por qué procesamos en local y no en el servidor
El servidor EC2 t3.small tiene 2GB RAM. MiDaS DPT-Hybrid necesita ~1800MB para cargar. Con el stack completo (Nginx+PHP+MySQL) corriendo solo quedan ~1200MB libres — insuficiente. El servidor se colgó al intentarlo.

El PC local tiene RTX 3060 con CUDA — procesa cada foto en 2-3 segundos en lugar de 45 segundos en CPU. La calidad es máxima (DPT-Hybrid).

#### Plan de procesado en PC local

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

#### Tours de demo para la exposición (OBLIGATORIO)

Tener preparados 1-2 tours completos y visualmente impecables ANTES de la exposición:
- Fotos 360° equirectangulares de alta calidad (buscar en Flickr 360°, Poly Pizza, o generar con IA)
- Procesadas con MiDaS DPT-Hybrid en PC local con GPU
- Subidas al servidor y tours publicados y navegables
- El tribunal navega estos tours y ve el producto en su máximo esplendor

**Regla de oro:** nunca depender de que algo funcione en tiempo real delante del tribunal. Los tours pregenerados son el plan A siempre.

#### Subida en directo (si el tribunal quiere probar)

El servidor usa MiDaS Small (80MB, cabe en RAM) para procesado en tiempo real. Tiempo estimado: 30-60 segundos por foto en t3.small con swap de 2GB.

La UX debe camuflar el tiempo de espera:
- Barra de progreso animada durante el procesado
- Mensaje: "Analizando profundidad con IA..."
- Formulario con campos adicionales visibles mientras procesa (nombre de posición, descripción) para que el usuario esté ocupado
- El tiempo percibido es mucho menor cuando el usuario interactúa

**Si algo falla en directo:** los tours pregenerados demuestran que el producto funciona. El fallo puntual es atribuible a las limitaciones del servidor de desarrollo (t3.small), no al producto.

#### Estado actual del servidor (t3.small)

- RAM: 1910MB total, ~1142MB disponibles con stack completo corriendo
- Swap: pendiente de configurar (2GB en disco — 13GB libres disponibles)
- MiDaS en servidor: usar Small (80MB) para no saturar RAM
- MiDaS DPT-Hybrid: solo en PC local para los tours de demo
- Modelo descargado en servidor: /var/www/oxphyre/python-service/dpt_hybrid.pt (467MB) → Este modelo NO se usa en el servidor, solo el Small → Considerar borrarlo del servidor para liberar espacio si hace falta

#### Pendiente de implementar
- Script Python local para procesado con GPU en Windows
- Swap de 2GB en servidor t3.small
- Instalar MiDaS Small en servidor (en lugar del Hybrid)
- Microservicio Flask funcional con MiDaS Small
- Flujo completo PHP → Flask → mapa de profundidad → BD
- UX de progreso durante el procesado

---

## Diseño Visual y Storytelling

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

Soft delete activo en `businesses`, `tours`, `positions`, `photos`.
- **NUNCA usar `DELETE FROM`** en estos modelos — siempre `UPDATE ... SET deleted_at = NOW() WHERE id = ?`
- **Todos los `SELECT`** de estos modelos deben incluir `WHERE deleted_at IS NULL` (o `AND deleted_at IS NULL` si ya hay `WHERE`)
- Las tablas `users`, `plans`, `hotspots`, `qr_codes`, `qr_scans`, `contact_messages`, `cookies_consent` y `login_attempts` **no tienen soft delete** — en ellas sí se puede usar `DELETE FROM`

### Pendientes y deuda técnica
- Logo y favicon: diseñar cuando la página esté terminada
- Modo claro: implementar cuando modo oscuro esté completamente cerrado
- Video demo real: grabar y sustituir placeholder de S4
- Responsive: verificar todas las secciones en móvil y tablet tras implementar
- API externa obligatoria (requisito tribunal): integrar Google Maps o Mapbox para mostrar ubicación del negocio en el dashboard/tour. Sin esto el proyecto no cumple los requisitos mínimos.
- Roles documentados (requisito tribunal): documentar explícitamente en la memoria qué puede hacer cada rol (admin, business_owner, viewer) tanto en frontend como en backend. Los roles ya existen en BD pero no están documentados.
- Emails transaccionales: actualmente PHPMailer + Gmail SMTP con cuenta danimm3097@gmail.com (válido para TFG). La cuenta digitechfp.com se descartó porque el centro educativo tiene SMTP capado. En producción real migrar a Resend, SendGrid o Mailgun con dominio propio noreply@oxphyre.com — Gmail muestra la cuenta del remitente en lugar de una dirección de marca y tiene límite de ~500 emails/día.
- UserModel::create() tiene el rol "business_free" hardcodeado en SQL. Refactorizar cuando existan más roles: pasar $role como parámetro o definir constante ROLE_DEFAULT en config.php
- Gmail SMTP requiere App Password en .env, no la contraseña de cuenta. MAIL_USERNAME y MAIL_FROM deben ser el mismo email o Gmail rechazará la conexión
- Wizard paso 2 (Tu plan): mostrar los 3 planes en cards lado a lado (Free, Pro destacado, Business) en lugar del plan Free solo con link discreto. El momento del onboarding es el de mayor motivación del usuario — es el mejor punto para mostrar el valor de Pro y Business y conseguir upgrades. Mismo diseño de cards que la sección de precios de la landing.
- Dashboard y wizard: revisar visibilidad general — inputs, labels y texto secundario tienen contraste insuficiente (texto gris oscuro sobre fondo negro). Mejorar colores para que los campos que el usuario debe rellenar sean claramente visibles. Nunca texto gris oscuro sobre fondo negro en zonas interactivas.
- /precios: crear página independiente con las 3 cards de planes (Free, Pro, Business), mismo diseño que la sección de precios de la landing pero como página propia. Slug correcto para SEO es /precios (no /planes). Todos los CTAs de upgrade del dashboard apuntan aquí.
- El enlace "Ver planes Pro/Business →" del wizard paso 2 y los CTAs de upgrade del dashboard apuntan a /precios. Verificar que todos son consistentes cuando se cree esa página.
- Dashboard: añadir tooltips de ayuda contextual en las métricas para clarificar la jerarquía del producto al usuario no técnico. Ejemplo: icono ? en "Tours activos" con tooltip "Un tour es la experiencia 360° que verán tus clientes", y en "Negocios" con "Un negocio agrupa todos tus tours". Hacerlo en el pass de UX final junto con el tutorial del editor.
- Editor canvas: implementar tutorial/onboarding la primera vez que el usuario accede, con botón para volver a verlo. El editor de nodos y conexiones puede resultar confuso — el tutorial debe explicar la jerarquía negocio → tour → posiciones → fotos y cómo usar el canvas.
- BusinessController tiene go() y verifyCsrf() como métodos privados propios. AuthController tiene redirect() y validateCsrf() con la misma funcionalidad pero distintos nombres. Unificar en BaseController como métodos protegidos y eliminar duplicados en los controllers hijos. Hacer en un refactor pass cuando todos los controllers estén creados.