# AGENTS.md - Instrucciones de comportamiento para Claude Code

## Comportamiento general
- Respuestas cortas y directas. Sin introducciones, sin resúmenes al final.
- No expliques lo que ya está documentado en DEVLOG.md o CLAUDE.md.
- No repitas código que no ha cambiado. Archivos completos siempre, nunca fragmentos con "// resto igual".
- Si una tarea es ambigua, haz UNA sola pregunta antes de proceder.
- Al terminar cada tarea, actualiza DEVLOG.md con lo que se hizo y por qué.
- Antes de escribir código en una tarea compleja, escribe en una línea el enfoque que vas a seguir.

## Ahorro de tokens sin sacrificar calidad
- Lee DEVLOG.md y CLAUDE.md al inicio de cada sesión, no pidas que te expliquen el proyecto.
- No uses comentarios obvios en el código. Solo comenta lo que no es evidente.
- No generes código de ejemplo o placeholders. Solo código real y funcional.
- Si necesitas contexto de un archivo, léelo directamente en vez de preguntar.

## Reglas absolutas de código (nunca las rompas)
- NUNCA frameworks JS (React, Vue, Angular). Solo vanilla JS + Three.js.
- NUNCA frameworks PHP (Laravel, Symfony). PHP puro con MVC.
- NUNCA Bootstrap ni librerías CSS. CSS custom puro en la landing. Tailwind en páginas internas.
- NUNCA SQL sin prepared statements. Sin excepción, ni una sola query directa.
- NUNCA credenciales en código. Siempre en .env, nunca en GitHub.
- NUNCA subas .env, .pem, ni credenciales a GitHub.
- NUNCA localStorage para tokens de sesión o datos sensibles.
- NUNCA confíes en extensión de archivo para validar uploads. Valida MIME real.
- NUNCA dejes XSS posible. htmlspecialchars() en toda salida, strip_tags() en entrada.
- SIEMPRE usa Lucide Icons para iconos. Nunca Font Awesome, nunca emojis como iconos, nunca SVGs ad-hoc sin justificación.

## Seguridad (prioridad máxima)
- Prepared statements en el 100% de queries MySQL.
- CSRF tokens en todos los formularios POST, validados server-side.
- Rate limiting en login: máx 5 intentos, bloqueo temporal registrado en BD.
- Sesiones PHP: regenerar ID tras login, HttpOnly + Secure flags.
- Uploads: validar tipo MIME real, tamaño máximo, renombrar con hash aleatorio.
- Headers Nginx: X-Frame-Options DENY, Content-Security-Policy, HSTS, X-Content-Type-Options.
- Variables sensibles siempre en .env, cargadas con dotenv en PHP.

## Flujo de trabajo git
git add .
git commit -m "tipo: descripción corta"
git push

En servidor:
cd /var/www/oxphyre && git pull
Tipos de commit: feat, fix, docs, style, refactor, chore

## Estilo de código PHP
- Clases en PascalCase, métodos y variables en camelCase.
- Un archivo por clase.
- Todos los métodos de modelo usan prepared statements.
- Controllers delgados: solo coordinan, la lógica va en modelos o servicios.

## Estilo de código JS
- ES6+ siempre (const, let, arrow functions, async/await).
- Sin var.
- Comentarios solo cuando el código no es autoexplicativo.

## Estilo CSS
- Variables CSS globales en :root para tema día/noche, colores de marca, tipografía.
- Tailwind para layout, espaciados y utilidades. CSS custom para animaciones, efectos visuales complejos, glassmorphism, modo día/noche y todo lo que Tailwind no puede hacer.
- Animaciones con transform y opacity (GPU), nunca con propiedades que causan reflow.

## Prioridad de desarrollo actual
1. Landing desplegada en https://oxphyre.com — revisar visualmente y ajustar bugs visuales pendientes
2. Auth completa: registro, verificación email, login, recuperar contraseña
3. Dashboard base con navegación y layout
4. Onboarding wizard para nuevos negocios
5. Subida de fotos + procesado Python + MiDaS
6. Editor canvas drag & drop
7. Vista tour Three.js con hotspots y minimapa
8. QR descargable con analíticas
9. Ver DEVLOG.md para historial completo