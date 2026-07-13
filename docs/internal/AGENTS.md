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

## Plantilla recomendada para prompts de implementación

Cuando se pida trabajo a Codex, Claude Code, otra IA o cualquier editor asistido, usar esta estructura siempre que la tarea implique código, infraestructura, base de datos, servicios, vistas o cambios relevantes del proyecto.

La plantilla puede acortarse para tareas pequeñas, pero no debe eliminar restricciones críticas de seguridad, alcance o verificación.

Contexto:
Estamos trabajando en Oxphyre, TFG DAW y producto real para PYMES. Respeta AGENTS.md, AI_SYNC.md, CLAUDE.md y DEVLOG.md. Mantén PHP puro MVC, JS vanilla, Three.js, MySQL y Python Flask/MiDaS. No introduzcas frameworks nuevos ni dependencias innecesarias.

Objetivo:
Explica en 1-3 frases qué se quiere conseguir en esta tarea concreta.

Estado actual:
Resume solo lo necesario para esta tarea:
- qué está ya implementado;
- qué está pendiente;
- qué decisiones previas hay que respetar;
- qué NO debe darse por validado si aún no se ha probado.

Archivos a tocar:
- Lista exacta de archivos permitidos.
- Si no se sabe el archivo exacto, indicar primero “revisar y proponer antes de modificar”.

Archivos que NO se deben tocar:
- Lista de archivos fuera de alcance.
- Incluir controllers/models/views/JS/CSS/BD si no forman parte de la tarea.

Tarea:
1. Paso concreto 1.
2. Paso concreto 2.
3. Paso concreto 3.
Divide la implementación en pasos pequeños y seguros. No hagas refactors grandes salvo que se pidan explícitamente.

Restricciones:
- No hacer commit ni push.
- No tocar GitHub remoto.
- No introducir credenciales en código.
- No modificar `.env` salvo instrucción explícita; usar `.env.example` para documentar variables.
- Mantener prepared statements en SQL.
- Mantener CSRF en POST.
- Mantener validación MIME real en uploads.
- Mantener fallback seguro si una integración externa falla.
- No romper compatibilidad con datos/fotos antiguas.
- No marcar como validado algo que no se haya probado realmente.

Comentarios de código:
Si modificas código con lógica relevante, añade comentarios por bloques en español, profesionales pero fáciles de entender.
Los comentarios deben explicar qué pretende cada bloque y por qué existe, sin comentar línea por línea ni repetir lo obvio.
El objetivo es que el desarrollador, otra IA o el tribunal puedan leer el archivo y entender rápidamente la intención técnica.
No hace falta añadir comentarios extensos en cambios triviales como textos, clases CSS simples, documentación o ajustes de una línea.

Verificación:
Ejecuta las comprobaciones necesarias según el tipo de cambio:
- `php -l archivo.php` para PHP.
- `git diff --check`.
- SQL de verificación si hay migración.
- Prueba manual indicada si aplica.
- Confirmar que no se tocaron archivos fuera de alcance.

Documentación:
Si la tarea cambia estado real del proyecto, actualizar DEVLOG.md.
Si cambia una decisión activa, próximo paso o advertencia para futuras IAs, actualizar AI_SYNC.md.
Si cambia contexto amplio o arquitectura estable, actualizar CLAUDE.md.
No duplicar lo mismo en los tres: cada archivo tiene su función.

Resultado esperado:
Al finalizar, responde con:
1. Resumen breve.
2. Archivos modificados.
3. Qué cambió.
4. Comprobaciones realizadas.
5. Pendientes reales.
6. Confirmación de que no hiciste commit ni push.

## Uso de la plantilla
Para tareas de documentación pura, se puede omitir “Comentarios de código”.
Para tareas pequeñas, la plantilla puede ser más corta, pero debe mantener: contexto, objetivo, alcance, restricciones, verificación y resultado esperado.
Para tareas complejas, dividir en fases: analizar → decidir arquitectura → implementar mínimo seguro → probar → documentar → seguir.
Antes de tocar código en una tarea compleja, escribir una línea con el enfoque previsto.
