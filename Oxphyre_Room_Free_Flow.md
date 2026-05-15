# Oxphyre Room — Especificación funcional del flujo Free/base

## Estado del documento

**Estado:** Sprint 1 implementado y validado en servidor para el flujo base de subida/visualización. Decisión UX posterior documentada: Oxphyre Room pasa a ser la experiencia completa de una posición, no un modo dependiente de 4 fotos.

Este documento define el flujo de creación y visualización de posiciones en Oxphyre para el modo Free/base.

El Sprint 1 ya está implementado en la app real:
- pantalla de subida con panorámica principal obligatoria;
- fotos detalle opcionales guardadas internamente como N/S/E/O;
- visor público entrando siempre por la panorámica;
- botón "Ver detalles" pendiente de adaptar para mostrar 1-4 detalles disponibles sin exigir las 4 fotos;
- retorno desde Oxphyre Room a vista principal;
- subida conjunta de N/S/E/O + `photo_360`;
- pipeline WebP/libvips en `ImageProcessingService`;
- HEIC/HEIF implementado en pipeline y soportado por servidor;
- flujo iPhone normal validado;
- pendiente prueba con archivo `.heic` puro sin conversión automática.

Quedan pendientes para fases posteriores:
- adaptar UX/dashboard para bloquear "Ver posición" si falta panorámica `360`;
- adaptar UI y visor para detalles parciales 1-4 sin exigir 4 fotos;
- editor real de hotspots;
- QR;
- R2/CDN;
- limpieza física de archivos asociados a soft delete;
- pulido opcional de ruido/granulado.

Documentos que deben permanecer sincronizados cuando cambie este flujo:

- `CLAUDE.md`
- `AI_SYNC.md`
- `DEVLOG.md`
- `Planes_Oxphyre.md` si afecta a los tiers
- landing
- `/precios`
- límites reales de la app si procede

---

## 0. Contexto

Oxphyre es un SaaS de tours virtuales inmersivos para pequeños negocios locales. El objetivo actual es cerrar un flujo Free/base que sea:

- viable técnicamente para el TFG;
- entendible para usuarios no técnicos;
- defendible ante tribunal;
- comercialmente vendible a una PYME real;
- coherente con el stack actual;
- fácil de continuar por ChatGPT, Claude Web, Codex o Claude Code.

El sistema actual ya permite subir dos tipos de imagen por posición:

```txt
fotos detalle opcionales
panorámica
```

Históricamente, la lógica documentada usaba `positions.active_mode` para decidir si una posición se visualizaba en modo `4photos` o `panoramic`.

Esta especificación redefine el flujo de producto:

Oxphyre Room = experiencia completa de posición
Panorámica principal = vista base obligatoria
Fotos detalle = opcionales, de 1 a 4
Hotspots = navegación entre posiciones sobre la panorámica

No se elimina todavía la estructura existente. Se reutiliza para avanzar sin romper la app.

## Decisión vigente — 2026-05-15

Oxphyre Room deja de entenderse como "modo 4 fotos" y pasa a ser la experiencia completa de una posición en el visor.

- Panorámica principal / 360 adaptativa: obligatoria para que una posición sea visitable.
- Fotos detalle: opcionales, de 1 a 4, para destacar zonas concretas que no se aprecian bien en la panorámica.
- Si hay 0 fotos detalle, la posición funciona solo con panorámica.
- Si hay 1-4 fotos detalle, el visor deberá poder mostrar las disponibles.
- El usuario no debe estar obligado a subir las 4 fotos detalle.

En UI, dejar de mostrar "Frente / Fondo / Izquierda / Derecha". Usar:
- Foto detalle 1
- Foto detalle 2
- Foto detalle 3
- Foto detalle 4

Decisión técnica temporal:
- No migrar todavía la BD ni el enum interno `direction`.
- `N = Foto detalle 1`
- `S = Foto detalle 2`
- `E = Foto detalle 3`
- `O = Foto detalle 4`
- Migrar a `detail_1/detail_2/detail_3/detail_4` queda como posible mejora futura.

Regla UX crítica:
- Si una posición no tiene panorámica `360`, no debe parecer visitable.
- El botón "Ver posición" debe aparecer desactivado/no clickable en la card/listado y dentro de la pantalla de gestión/subida.
- Tooltip sugerido: "Sube una panorámica principal para activar esta experiencia Oxphyre Room. Las fotos detalle son opcionales."

## 1. Nombre del sistema

### Decisión

El nombre comercial visible será:

Oxphyre Room

No se debe presentar al usuario como:

Modo 4 fotos
Direction Sphere
Modo 4photos
Esfera sectorizada

Esos nombres solo sirven internamente para Dani, ChatGPT, Claude, Codex o Claude Code.

### Subtítulo recomendado

Vista direccional inmersiva de tu negocio

### Descripción general recomendada

Oxphyre Room convierte fotos y panorámicas tomadas con tu móvil en una experiencia visual navegable para tu negocio.

### Enfoque correcto

Oxphyre Room no debe entenderse como el modo de 4 fotos. Debe funcionar como nombre de marca/paraguas para la experiencia visual completa de cada posición Free/base.

Estructura conceptual:

```txt
Oxphyre Room
├── Vista panorámica principal
├── Fotos detalle opcionales 1-4
└── Hotspots de navegación entre posiciones
```

## 2. Promesa de producto

### Qué promete Oxphyre Room

Promete crear una visita inmersiva básica de una zona del negocio usando fotos normales o panorámicas de móvil.

Texto recomendado:

Explora una zona de tu negocio desde varios ángulos, con una experiencia visual envolvente y fácil de crear.
Ideal para mostrar entradas, zonas principales, barras, salas pequeñas o rincones destacados sin necesitar cámaras especiales.
### Qué NO promete

No debe prometer:

- 360º real completo
- Tour Matterport
- Reconstrucción 3D libre
- Recorrido 3D fotorrealista
- Ver todo el negocio desde una sola posición

### Regla de comunicación

Free no vende:

- 360 real completo con cualquier móvil

Free vende:

- Visita inmersiva básica con fotos normales o panorámicas, sin cámaras especiales.

## 3. Guía de captura para el creador

La calidad final depende muchísimo de cómo el usuario haga las fotos. Oxphyre debe educar al creador sin abrumarlo.

### Recomendaciones principales

Para Oxphyre Room / fotos de detalle:

- Haz las fotos en horizontal.
- Usa lente 1x.
- No uses zoom digital.
- Busca buena luz.
- Limpia la lente antes.
- Sube las fotos originales directamente desde el móvil.
- Evita WhatsApp, Telegram u otras apps porque comprimen las imágenes y pierden calidad.
- Mantén el móvil a altura de pecho/ojos.
- Evita fotos borrosas, torcidas u oscuras.
- Evita personas moviéndose cerca.
### Desde dónde hacer las fotos

No se debe obligar al usuario a colocarse siempre en el centro.

Recomendación correcta:

Haz las fotos desde un punto estratégico de la zona.
Si el espacio es amplio, puedes colocarte en el centro.
Si el espacio es pequeño, colócate cerca de una pared o esquina para mostrar más.
### Si no cabe todo
Si no cabe todo desde una sola posición, crea varias posiciones.
Cada posición debe mostrar una zona concreta del negocio.

Ejemplo para una peluquería pequeña:

Entrada
Zona de peinado
Lavacabezas
Mostrador
Escaparate

Aunque todo sea una sola habitación, cada posición representa un punto de vista diferente.

### Modal inicial recomendado

La primera vez que el creador entra en la pantalla de gestión/subida de una posición, mostrar un modal:

Antes de subir tus fotos

Para que Oxphyre Room se vea bien:
- Haz las fotos en horizontal.
- Usa lente 1x, sin zoom.
- Busca buena luz.
- Sube las fotos originales desde el móvil.
- Evita WhatsApp porque comprime.
- Si el espacio es pequeño, colócate en una esquina o punto estratégico.
- Si no cabe todo, crea varias posiciones.

Botones:

Entendido, empezar
No volver a mostrar

La preferencia puede guardarse en localStorage, porque no es dato sensible.

## 4. Comportamiento visual de Oxphyre Room / modo detalle

### Base visual

La vista de detalles se basará en el prototipo tipo Direction Sphere, pero con nombre y textos de producto. Debe poder funcionar con las fotos detalle disponibles, de 1 a 4, sin exigir el set completo.

### Comportamiento
- 1 a 4 paneles/zonas de detalle disponibles.
- Fondo oscuro Oxphyre.
- Partículas y glow ámbar.
- Bordes tratados con oscuridad/glow para no fingir unión perfecta.
- Drag con mouse o dedo para mirar.
- Pitch vertical limitado.
- Navegación discreta entre detalles disponibles.
- Auto-rotación suave tras unos segundos de inactividad.
- Watermark visible en Free.
### Texto para visitante dentro de Oxphyre Room

Solo texto mínimo:

Oxphyre Room
Arrastra para mirar alrededor

Botón:

Volver a vista principal
### En móvil
- Auto-rotación suave por defecto si el usuario no toca.
- Drag táctil para mirar.
- Giroscopio desactivado por defecto.
- Icono pequeño para activar giroscopio.
- Si se activa, el usuario puede mirar moviendo físicamente el móvil.
- El botón de giroscopio debe ser discreto.
### Performance
- Limitar pixel ratio a Math.min(devicePixelRatio, 2).
- Partículas moderadas en móvil.
- Precargar las fotos detalle disponibles antes de mostrarlas.
- Error elegante si falta o falla una foto.
## 5. Nuevo flujo por posición

### Decisión central

Cada posición se entiende así:

Posición = Oxphyre Room
Oxphyre Room = panorámica principal obligatoria + fotos detalle opcionales 1-4
### Flujo final
1. El creador crea una posición.
2. Sube una panorámica principal.
3. Opcionalmente añade 1-4 fotos detalle para destacar zonas concretas.
4. El visitante entra siempre en la panorámica.
5. Sobre la panorámica aparecen hotspots.
6. Si hay fotos detalle disponibles, aparece el acceso a detalles.
7. Al pulsar “Ver detalles”, entra en la vista de detalles disponibles de esa experiencia Oxphyre Room.
8. Dentro de detalles puede ver las zonas destacadas.
9. Puede volver con “Volver a vista principal”.
### Reglas
- Panorámica obligatoria para que la posición sea válida.
- Fotos detalle 1-4 opcionales, pero recomendadas cuando ayudan a vender mejor una zona.
- Hotspots solo sobre la panorámica.
- El botón “Ver detalles” debe poder aparecer si existe al menos una foto detalle.
- Si no hay fotos detalle, no aparece ningún botón ni aviso al visitante.
- Si no hay panorámica, la posición está incompleta y no se muestra en el tour público.
### Cambio frente al flujo anterior

Antes:

Elige panorámica O 4 fotos

Ahora:

Panorámica para navegar
Fotos detalle para destacar partes clave
Hotspots para viajar entre posiciones
## 6. Hotspots de navegación

### Dónde aparecen

Solo sobre la panorámica principal.

No aparecen dentro de Oxphyre Room.

### Para qué sirven en Free

Solo navegación básica:

Ir a otra posición del tour

Ejemplo:

Ir a entrada
Ir a barra
Ir a lavacabezas
Ir a mostrador
### Qué NO hacen en Free

No incluyen:

- precios
- formularios
- reservas
- vídeos
- fichas de producto
- textos comerciales avanzados
- analíticas avanzadas por interacción

Eso queda para Pro.

### Visual
Punto ámbar discreto + etiqueta al hover/tap

En móvil:

Tap en punto → muestra etiqueta
Tap/confirmación → viaja
### Al clicar
Click hotspot
↓
fade suave
↓
carga panorámica de destino
↓
actualiza posición actual
↓
muestra hotspots de la nueva posición
↓
muestra “Ver detalles” si esa posición tiene fotos detalle disponibles
### Editor de hotspots para el creador

El creador debe poder colocar hotspots visualmente sobre la panorámica.

Flujo:

Editar hotspots
→ abrir panorámica
→ girar hasta la zona deseada
→ pulsar “Añadir hotspot”
→ tocar/clicar el punto exacto
→ elegir posición destino
→ guardar
### PC
Drag para mirar
Botón “Añadir hotspot”
Click sobre la panorámica
Modal para elegir destino
Guardar
### Móvil
Drag táctil para mirar
Botón flotante +
Tap sobre la panorámica
Bottom sheet para elegir destino
Guardar
### Restricción

No se pueden crear hotspots si el tour tiene menos de 2 posiciones completas con panorámica principal.

Mensaje:

Necesitas al menos 2 posiciones completas para crear hotspots de navegación.
Añade otra posición para conectar zonas de tu negocio.
### Datos a guardar

Ideal:

position_id
target_position_id
longitude
latitude
title
type = navigation

MVP aceptable si no se quiere migrar BD todavía:

position_x = longitude
position_y = latitude

La tabla actual de hotspots ya tiene position_x, position_y y target_position_id, por lo que se puede reutilizar en una primera versión.

## 7. Validación y publicación

### Posición válida

Una posición válida necesita:

Panorámica principal subida

Si no tiene panorámica:

Esta posición todavía no está lista.
Sube una panorámica principal para poder incluirla en el tour.

### Posición sin panorámica

Técnicamente sí se puede crear una posición vacía para completar después.

Pero:

Una posición sin panorámica queda incompleta y no se muestra en el tour público.

### Fotos detalle de Oxphyre Room

Las fotos detalle son opcionales dentro de la experiencia Oxphyre Room.

Estados:

0 fotos detalle subidas
1 foto detalle subida
2 fotos detalle subidas
3 fotos detalle subidas
4 fotos detalle subidas

Mensaje de ayuda:

La panorámica principal activa esta experiencia Oxphyre Room. Las fotos detalle son opcionales y sirven para destacar partes concretas: barra, mesa, escaparate, producto, decoración o un rincón especial.

### Hotspots

Si solo hay una posición completa:

Necesitas al menos 2 posiciones completas para crear hotspots de navegación.
Añade otra posición para conectar zonas de tu negocio.

Si un hotspot apunta a una posición incompleta o eliminada:

Este hotspot apunta a una posición no disponible.
Edita el destino o elimina el hotspot.

Regla pública:

Los hotspots rotos no se muestran en el tour público.

### Publicar tour

Mínimo para publicar:

1 posición completa con panorámica principal

Si no hay ninguna posición válida:

No puedes publicar este tour todavía.
Añade al menos una posición con panorámica principal.

Si solo hay una posición válida:

Puedes publicar el tour, pero te recomendamos añadir más posiciones para crear una experiencia navegable.

Si tiene 2 o más posiciones válidas:

Tu tour ya puede convertirse en una visita navegable. Añade hotspots para conectar las zonas entre sí.

### Checklist antes de publicar

Al pulsar “Publicar”, mostrar una revisión:

Revisión antes de publicar

✓ 3 posiciones completas
✓ 2 hotspots de navegación
✓ 2 posiciones con fotos detalle
⚠ 1 posición solo con panorámica principal

Botones:

Publicar tour
Seguir editando

Si falta lo mínimo:

Tu tour todavía no está listo

✕ No hay posiciones completas
Añade al menos una posición con panorámica principal.
## 8. BD y estructura técnica

### No hacer migración grande ahora

Mantener lo existente:

positions.active_mode
photos.direction = '360'
photos.direction = N/S/E/O

### Cambio conceptual

Antes:

active_mode decide si se usa panorámica o 4 fotos

Ahora:

photo_360 existe → panorámica principal
N/S/E/O → fotos detalle opcionales disponibles

positions.active_mode queda como campo heredado/compatibilidad. No debe ser la lógica principal del visor público nuevo.

### Mapeo interno temporal

No se migra todavía la BD ni el enum interno `direction`.

```txt
N = Foto detalle 1
S = Foto detalle 2
E = Foto detalle 3
O = Foto detalle 4
```

La UI no debe mostrar Frente/Fondo/Izquierda/Derecha al usuario. Migrar a `detail_1/detail_2/detail_3/detail_4` queda como posible mejora futura, no ahora.

### Crear posición sin panorámica

Sí se permite crear la posición sin panorámica.

Motivo: el flujo natural es:

Crear posición
↓
Subir panorámica
↓
Completar detalles

Pero:

Sin panorámica, la posición no aparece en el tour público.
Sin al menos 1 posición con panorámica, el tour no puede publicarse.

### Hotspots

Para MVP se puede reutilizar:

position_x = longitude
position_y = latitude

Más adelante, si se quiere limpiar BD:

```sql
ALTER TABLE hotspots
ADD COLUMN longitude DECIMAL(10,6) NULL,
ADD COLUMN latitude DECIMAL(10,6) NULL;
```

Pero no es imprescindible para el primer sprint.

## 9. Pantalla de subida / gestión de posición

La pantalla debe explicar el flujo nuevo:

Panorámica principal = obligatoria
Oxphyre Room = detalle opcional recomendado
Hotspots = navegación entre posiciones

### Header

Configurar posición
[Nombre de la posición]

Subtexto:

Sube una panorámica principal para activar esta experiencia Oxphyre Room y, si quieres destacar zonas concretas, añade hasta 4 fotos detalle.

### Bloque 1 — Panorámica principal

Panorámica principal
Obligatoria

Texto:

Será la vista principal que verán tus clientes al entrar en esta posición.

Ayuda:

Usa una panorámica hecha con tu móvil. No pasa nada si no cubre los 360º completos: Oxphyre limitará la vista para evitar zonas vacías.

Estado:

Pendiente
Completada

CTA:

Subir panorámica
Cambiar panorámica

### Bloque 2 — Oxphyre Room

Fotos detalle
Opcional recomendado

Texto:

Añade de 1 a 4 fotos detalle para destacar partes clave de esta zona: barra, mesa, escaparate, producto, decoración o un rincón especial.

Ayuda:

Recomendamos fotos horizontales, con buena luz y subidas directamente desde el móvil.

Estado:

0 fotos detalle
1 foto detalle
2 fotos detalle
3 fotos detalle
4 fotos detalle

CTA:

Subir fotos
Actualizar fotos

### Bloque 3 — Hotspots de navegación

Hotspots de navegación
Conecta esta posición con otras zonas del tour.

Ayuda:

Los hotspots aparecen sobre la panorámica principal y permiten que tus clientes viajen entre posiciones.

Si hay menos de 2 posiciones completas:

Necesitas al menos 2 posiciones completas para crear hotspots.
Añade otra posición para conectar zonas de tu negocio.

Si puede crear hotspots:

Añadir hotspot

Si ya existen:

2 hotspots creados
Editar hotspots

### Modal inicial de ayuda

Cómo completar una posición

1. Sube una panorámica principal. Es obligatoria para activar la posición.
2. Añade fotos detalle si quieres mostrar mejor zonas concretas.
3. Crea hotspots para conectar esta zona con otras posiciones.

Consejo: sube las fotos originales desde el móvil y evita WhatsApp para conservar calidad.

Botones:

Entendido
No volver a mostrar

### Eliminar de la UI

Eliminar o esconder:

Usar estas fotos en el visor
Usar panorámica en el visor
Toggle 4 Fotos / Panorámica como decisión final

Ya no hay un “modo activo” visible para el usuario.

## 10. Visor público final

### Entrada al tour

El visitante entra siempre en la panorámica principal de la primera posición válida.

Ve:

Nombre del negocio
Nombre del tour / posición actual
Panorámica a pantalla completa

No ve textos técnicos.

### Desktop
- Drag con mouse para mirar.
- Hotspots sobre la panorámica.
- Botón “Ver detalles” si esa posición tiene al menos una foto detalle disponible.
- Watermark Oxphyre en Free.
- Sin toggle técnico “panorámica / 4 fotos”.

### Móvil
- Auto-rotación suave si no toca.
- Drag táctil para mirar.
- Botón pequeño de giroscopio desactivado por defecto.
- Hotspots tocables.
- Botón “Ver detalles” si existen fotos detalle.
- Watermark Free.

### Al clicar hotspot

Click/tap hotspot
↓
fade suave
↓
carga panorámica de destino
↓
actualiza posición actual
↓
muestra hotspots de esa posición
↓
muestra “Ver detalles” si hay fotos detalle disponibles

### Al pulsar “Ver detalles”

Entra en la vista de fotos detalle de esa experiencia Oxphyre Room.

HUD:

Oxphyre Room
Arrastra para mirar alrededor

Botón:

Volver a vista principal

No hay hotspots dentro de Oxphyre Room.

### Si no hay fotos detalle

No aparece botón.

No mostrar al visitante mensajes como:

Modo no disponible
Faltan fotos
Sube fotos detalle

Eso es información del creador, no del cliente final.

### Si el tour está incompleto

Si no hay posiciones válidas:

Tour no disponible
Este tour todavía no está listo.

## 11. Alcance de implementación para Codex / Claude Code

La implementación debe hacerse por bloques verticales funcionales, no por piezas sueltas.

### Sprint 1 — Nuevo flujo base completo

#### Objetivo

Convertir la posición en:

Panorámica principal obligatoria
+
Fotos detalle opcionales 1-4 dentro de Oxphyre Room

**Estado:** Sprint 1 base implementado y validado en servidor. La decisión UX de detalles parciales queda documentada, pero no implementada todavía.
#### Incluye

Dashboard / pantalla de subida:

1. Panorámica principal — Obligatoria
2. Fotos detalle — Opcional recomendado
3. Hotspots — Bloque informativo / próximamente si todavía no se implementan

#### Eliminar

Usar estas fotos en el visor
Usar panorámica en el visor
Toggle 4 Fotos / Panorámica como modo activo

#### Estados

Panorámica: Pendiente / Completada
Fotos detalle: 0, 1, 2, 3 o 4 disponibles

#### Backend

No migración grande.
Mantener active_mode por compatibilidad.
Detectar panorámica por photos.direction='360'.
Detectar fotos detalle por existencia parcial de N/S/E/O.

#### Visor público

Entra siempre en panorámica principal.
Muestra botón “Ver detalles” si hay al menos una foto detalle disponible.
“Ver detalles” abre los detalles disponibles de Oxphyre Room.
Oxphyre Room tiene “Volver a vista principal”.
No mostrar posiciones sin panorámica.

#### No incluye todavía

Editor real de hotspots.
Guardar coordenadas de hotspots.
Checklist avanzado.
QR.
Minimapa.
Analíticas.
### Sprint 2 — Validaciones y estados

#### Objetivo

Evitar tours rotos.

#### Incluye

No publicar si no hay mínimo 1 posición con panorámica.
No mostrar posiciones sin panorámica.
Estados claros en dashboard.
Aviso si solo hay 1 posición.
### Sprint 3 — Hotspots de navegación

#### Objetivo

Convertir el tour en visita navegable real.

#### Incluye

Editor visual de hotspots sobre panorámica.
PC y móvil.
Guardar longitude/latitude o reutilizar position_x/position_y.
Mostrar hotspots públicos.
Click/tap → cargar posición destino.
Bloquear si hay menos de 2 posiciones completas.
### Sprint 4 — Pulido UX premium

#### Incluye

Modal inicial de ayuda.
Tooltips.
Avisos de baja resolución. Implementado en subida.
Aviso evitar WhatsApp/Instagram como recomendación secundaria. Implementado en subida.
Auto-rotación suave.
Giroscopio móvil discreto.
Responsive fino.
Microanimaciones.
### Orden definitivo
1. Sprint 1 — Nuevo flujo base completo: subida + visor público. Implementado.
2. Sprint 2 — Validaciones y estados.
3. Sprint 3 — Hotspots de navegación.
4. Sprint 4 — Pulido UX premium.
## 12. Sincronización entre IAs y documentación

Todo prompt/resumen debe ser entendible por:

- Dani
- ChatGPT
- Claude Web
- Codex
- Claude Code

Cada prompt debe dejar claro:

- contexto
- decisión de producto
- qué está cerrado
- qué sigue en debate
- archivos relevantes
- restricciones
- qué NO tocar
- resultado esperado
- cómo comprobarlo
### Plantilla obligatoria para prompts
Contexto:
Estamos trabajando en Oxphyre, TFG DAW. Respeta AGENTS.md, CLAUDE.md, DEVLOG.md, AI_SYNC.md y Planes_Oxphyre.md si aplica.

Objetivo:
[qué queremos conseguir]

Decisión de producto:
[qué flujo hemos cerrado]

Archivos relevantes:
[lista]

Restricciones:
[qué no tocar]

Tarea:
[pasos concretos]

Resultado esperado:
[qué debe funcionar]

Comprobación:
[cómo probarlo]

Al finalizar:
- resume archivos tocados
- indica si actualizaste DEVLOG.md
- propone actualización de AI_SYNC.md si cambió estado vivo
### Regla

Nada debe quedar ambiguo. Clasificar cada cosa como:

Decisión tomada
Idea en debate
Pendiente de validar
Descartado
## 13. Qué queda fuera del primer sprint

Para evitar que Codex o Claude Code toquen media app, el primer sprint NO incluye:

- Editor real de hotspots.
- QR.
- Minimap.
- Analíticas.
- Página /precios.
- Cambios definitivos de planes.
- Gaussian.
- Sweep Capture.
- Refactor grande de BD.
- Rediseño completo del dashboard.
- Migración grande de tablas.
- Implementar Pro/Business.

### Primer sprint es solo

Nuevo flujo base de posición + visor público coherente.
## 14. Criterios de aceptación

Para aceptar el Sprint 1, debe cumplirse:

- Puedo crear una posición.
- Puedo subir panorámica principal.
- Puedo subir de 1 a 4 fotos detalle opcionales.
- La UI ya no muestra “usar este modo”.
- La UI diferencia claramente:
  Panorámica principal obligatoria
  Fotos detalle opcionales
- El tour público entra en panorámica.
- Si hay al menos 1 foto detalle, aparece “Ver detalles”.
- “Ver detalles” abre los detalles disponibles de Oxphyre Room.
- “Volver a vista principal” funciona.
- Si no hay fotos detalle, no aparece “Ver detalles”.
- Si una posición no tiene panorámica, no aparece en el tour público.
- Si una posición no tiene panorámica, “Ver posición” aparece desactivado/no clickable en dashboard y gestión/subida.
- Si el tour no tiene posiciones válidas, muestra “Tour no disponible”.
- No se rompe desktop.
- No se rompe móvil.
- No se introducen frameworks nuevos.
- No se rompe seguridad de uploads.
- No se rompe soft delete.

### Criterios técnicos

- PHP puro MVC.
- JS vanilla.
- Three.js / PSV según stack actual.
- Prepared statements.
- CSRF en POST.
- Validación MIME real en uploads.
- htmlspecialchars en salida.
- Sin localStorage para datos sensibles.

## 15. Documentación después de implementar

Después de implementar y probar, si funciona:

### DEVLOG.md

Registrar:

2026-05-12/13 — Nuevo flujo Oxphyre Room / posición Free.

Debe incluir:

- qué se cambió
- por qué
- archivos tocados
- cómo se prueba
- qué queda pendiente

### AI_SYNC.md

Actualizar estado vivo:

Nuevo flujo validado:
Oxphyre Room como experiencia completa de posición: panorámica principal obligatoria + fotos detalle opcionales 1-4 + hotspots sobre panorámica.

También indicar si:

- active_mode queda heredado/compatibilidad
- hotspots quedan pendientes
- validaciones quedan pendientes

### CLAUDE.md

Solo actualizar cuando esté validado o implementado.

Cambiar la sección antigua donde dice que active_mode decide el modo visual público.

Nueva idea:

La panorámica es la vista principal obligatoria de cada posición. Las fotos detalle 1-4 son opcionales dentro de Oxphyre Room. active_mode queda como campo heredado hasta refactor posterior.

### Planes_Oxphyre.md

Actualizar si el flujo afecta a Free/Pro/Business.

Especialmente:

Free = panorámica principal + fotos detalle opcionales dentro de Oxphyre Room + hotspots básicos cuando estén implementados.

## Decisión final global

### Producto

Oxphyre Room deja de ser “4 fotos contra panorámica”.
Pasa a ser una experiencia por posición:
panorámica obligatoria para navegar,
fotos detalle opcionales 1-4 para destacar partes clave,
hotspots para viajar entre zonas.

### Técnica

No se rompe la BD actual.
No se hace migración grande.
Se reutiliza photos.direction='360' para panorámica.
Se reutiliza N/S/E/O como mapeo interno temporal de Foto detalle 1-4.
active_mode se mantiene por compatibilidad, pero deja de ser la lógica principal del nuevo visor.

### UX

El creador entiende qué debe hacer:
1. Subir panorámica.
2. Añadir fotos detalle si quiere destacar zonas concretas.
3. Conectar posiciones con hotspots.

El visitante entiende qué hacer:
1. Mirar la panorámica.
2. Usar hotspots para moverse.
3. Pulsar “Ver detalles” si quiere ver las fotos detalle disponibles.

### Comercial

Free no promete 360 real.
Free ofrece una visita inmersiva básica, honesta y útil.
Pro/Business podrán añadir más capacidad, publicación profesional, QR, embed, analíticas, minimapa, hotspots avanzados y Gaussian futuro.

### Prioridad inmediata

El Sprint 1 base ya está implementado. Siguientes pasos posibles:
- HEIC/HEIF si se prioriza captación móvil real de iPhone.
- R2/CDN si se prioriza hosting/rendimiento/producción.
- QR descargable si se prioriza demo comercial y tribunal.
- Hotspots si se prioriza navegación entre posiciones.
