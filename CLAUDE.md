# CLAUDE.md - Oxphyre Project Context

> Lee también AGENTS.md para instrucciones de comportamiento.
> Lee DEVLOG.md para historial completo de decisiones y avances.

## Qué es Oxphyre
SaaS de tours virtuales inmersivos para pequeños negocios locales. El dueño sube fotos de su local (4 por posición: N,S,E,O) → Python + MiDaS genera mapas de profundidad reales → editor canvas drag&drop permite construir la estructura de navegación del local → Three.js renderiza el tour inmersivo con hotspots y minimapa → clientes visitan escaneando un QR o mediante embed en su web.

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

## Planes SaaS
- **Free:** 1 tour, 5 posiciones, sin MiDaS, sin minimapa, con marca de agua
- **Pro (~19€/mes):** tours ilimitados, 20 posiciones, MiDaS activado, minimapa, sin marca de agua, analíticas básicas
- **Business (~49€/mes):** todo ilimitado, MiDaS máxima calidad, analíticas avanzadas, dominio personalizado, API access

## Contexto TFG
- Estudiante DAW (Desarrollo de Aplicaciones Web), 2º año
- Entrega: finales mayo 2026
- Objetivo: nota máxima + producto real comercializable
- El tribunal evaluará específicamente: SEO, PageSpeed, seguridad (intentarán inyecciones SQL y XSS), UX/UI, MVC correcto
- Exposición: profesores probarán la app en tiempo real desde sus portátiles escaneando un QR

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

### Pendientes
- Logo y favicon: diseñar cuando la página esté terminada
- Modo claro: implementar cuando modo oscuro esté completamente cerrado
- i18n ES/EN: actualizar con todos los textos nuevos
- Video demo real: grabar y sustituir placeholder de S4
- Responsive: verificar todas las secciones en móvil y tablet tras implementar