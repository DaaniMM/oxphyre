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
- Utiliza comentarios para explicar de forma intuitiva lo qué se pretende/se hace con cada bloque de código
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
> Roadmap base del proyecto desde cero. Para saber el estado real actual y el próximo paso vivo, consultar `AI_SYNC.md`.
1. Landing desplegada en https://oxphyre.com — revisar visualmente y ajustar bugs visuales pendientes
2. Auth completa: registro, verificación email, login, recuperar contraseña
3. Dashboard base con navegación y layout
4. Onboarding wizard para nuevos negocios
5. Subida de fotos + procesado Python + MiDaS
6. Editor canvas drag & drop
7. Vista tour Three.js con hotspots y minimapa
8. QR descargable con analíticas
9. Ver DEVLOG.md para historial completo

## Coordinación entre IAs

- AI_SYNC.md es la fuente rápida de verdad del estado actual.
- DEVLOG.md es el historial completo.
- CLAUDE.md es el contexto general del proyecto.
- Antes de cualquier tarea importante, leer AGENTS.md, CLAUDE.md, DEVLOG.md y AI_SYNC.md.
- No contradecir AI_SYNC.md salvo que haya un problema claro y se explique antes.

### Cómo actualizar DEVLOG.md y AI_SYNC.md

- DEVLOG.md debe registrar el historial: qué se hizo, qué archivos se tocaron, qué bugs se corrigieron, qué decisiones se tomaron y por qué.
- AI_SYNC.md debe reflejar el estado actual: decisiones activas, problemas pendientes, ideas en debate, opciones descartadas y próximo paso recomendado.
- No copiar la misma información en ambos archivos.
- Si algo ya está cerrado y forma parte del historial, va en DEVLOG.md.
- Si algo afecta a cómo debe continuar la siguiente IA, va en AI_SYNC.md.
- Al terminar una tarea importante, actualizar DEVLOG.md y actualizar AI_SYNC.md solo si cambió el estado actual del proyecto.


## Regla crítica sobre GitHub remoto

Aunque GitHub pueda estar conectado para lectura de contexto, ningún agente tiene permiso operativo para modificar el repositorio remoto.

Reglas obligatorias:
- No crear commits.
- No hacer push.
- No abrir, cerrar ni modificar pull requests.
- No modificar issues.
- No modificar workflows/actions.
- No escribir, actualizar, borrar ni renombrar archivos directamente en GitHub.
- No ejecutar acciones destructivas sobre el repositorio remoto.

Uso permitido:
- Leer archivos del repositorio.
- Analizar código, arquitectura, bugs y diffs.
- Proponer cambios.
- Generar prompts o instrucciones.
- Indicar comandos para que el usuario los ejecute manualmente.

Flujo obligatorio:
1. El agente analiza.
2. El agente propone cambios.
3. Los cambios se hacen localmente en VS Code mediante Codex, Claude Code o manualmente.
4. El usuario revisa el diff.
5. Solo el usuario decide commit/push, salvo autorización explícita en ese mismo momento.
