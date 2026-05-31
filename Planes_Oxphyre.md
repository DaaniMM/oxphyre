# Definición final de planes Oxphyre

## Decisión general

Oxphyre se organiza en tres niveles:

- Free: prueba real limitada.
- Pro: herramienta comercial profesional.
- Business: experiencia premium avanzada y evolución futura con Gaussian Splatting.

La idea clave es que Free permita entender el valor del producto, pero no sea suficiente para explotar Oxphyre profesionalmente. Pro debe ser el plan principal para negocios reales. Business queda como el plan avanzado/premium, incluyendo la evolución post-TFG con Gaussian Splatting.

---

## FREE — Prueba real limitada

### Objetivo
Permitir que un pequeño negocio pruebe Oxphyre y cree una visita básica real, pero con límites claros que incentiven el paso a Pro.

### Límites y funciones
- 1 negocio, 1 tour, hasta 3 posiciones por tour.
- Enlace público bajo dominio oxphyre.com incluido.
- QR básico descargable con branding Oxphyre incluido (no QR profesional ni analíticas QR avanzadas).
- Flechas de navegación básicas entre posiciones incluidas (no hotspots informativos con texto/precio/CTA).
- Mapa/ubicación del negocio (Leaflet/OpenStreetMap) visible en el visor público.
- Marca de agua Oxphyre visible en el visor: overlay semitransparente (no solo etiqueta discreta en esquina) + badge "Creado con Oxphyre" clicable hacia /precios. Diseñada para incentivar upgrade a Pro sin destruir la experiencia del visitante.
- Sin embed/iframe — solo enlace público; el tour no se puede incrustar en web propia.
- Sin analíticas.
- Sin minimapa.
- Sin dominio personalizado.
- Sin Gaussian.
- Sin Sweep Capture como feature estable.

### Captura y visor
Cada posición puede usar uno de estos modos:

#### Modo 4 fotos
- 4 fotos horizontales: frente, derecha, fondo, izquierda.
- Vista direccional inmersiva tipo Direction Sphere.
- Las fotos se muestran como paneles curvados dentro de una experiencia visual Oxphyre.
- No se promete 360º real.
- Los bordes entre fotos pueden usar zonas oscuras, glow y partículas para evitar sensación de corte brusco.
- Recomendación: fotos horizontales, lente 1x, buena luz, sin WhatsApp, subir originales directamente desde el móvil.

#### Modo panorámica
- El usuario puede subir una panorámica móvil.
- Se trata como panorámica adaptativa, no como 360º completo garantizado.
- El visor debe limitar giro horizontal/vertical cuando la panorámica sea parcial.
- No debe mostrar huecos negros ni zonas no capturadas.
- Recomendación: capturar desde un punto amplio o central, girando lentamente.

### Hotspots Free
- Hotspots básicos de navegación entre posiciones.
- Sirven para viajar entre puntos del tour.
- Ejemplo: desde “Entrada” ir a “Zona principal” o “Mostrador”.
- No incluyen textos comerciales avanzados, botones, precios, formularios ni reservas.

### Calidad de imagen
Oxphyre debe recomendar:
- subir fotos directamente desde el móvil;
- evitar WhatsApp porque comprime;
- usar fotos horizontales;
- usar lente 1x;
- evitar zoom digital;
- limpiar la lente;
- buena iluminación;
- evitar fotos borrosas.

Si una imagen tiene baja resolución, la app debería avisar:
“Esta imagen puede verse borrosa en el visor. Recomendamos subir la foto original desde el móvil.”

### Posicionamiento comercial
Free no vende “360 real”.
Free vende:
“Crea una visita inmersiva básica de tu negocio con fotos normales o panorámicas, sin cámaras especiales.”

---

## PRO — Plan profesional comercial

### Objetivo
Ser el plan principal para negocios que quieren usar Oxphyre de verdad para captar clientes, compartir el tour y medir resultados.

### Límites
- Hasta 5 negocios.
- Tours ilimitados.
- Hasta 20 posiciones por tour.
- Sin marca de agua.
- URL pública bajo oxphyre.com.
- Más capacidad de personalización.

### Funciones principales
- Todo lo incluido en Free, más:
- QR profesional (no básico Free, sino QR para uso profesional con analíticas asociadas).
- Embed/iframe para insertar el tour en la web del negocio (solo disponible desde Pro).
- Hotspots comerciales completos quedan como roadmap/proximamente:
  - texto;
  - descripcion;
  - precios;
  - informacion de productos/servicios;
  - CTA basico como "Reservar", "Llamar" o "Contactar".
- Analíticas básicas:
  - visitas;
  - escaneos QR;
  - dispositivo;
  - visitas por día.
- Minimap o mapa visual del tour.
- Foto de portada personalizable para compartir en redes/Open Graph.
- Mejor presentación comercial del tour.
- Soporte por email.
- Posible Sweep Capture como beta o feature futura si se valida.

### Captura y visor
- Panorámica adaptativa mejor presentada.
- 4 fotos direccionales como fallback.
- Más posiciones permiten cubrir negocios grandes o con varias zonas.
- La experiencia final debe sentirse más profesional porque combina:
  - más posiciones;
  - hotspots comerciales completos cuando salgan del roadmap;
  - QR;
  - embed;
  - analíticas;
  - sin marca de agua.

### Posicionamiento comercial
Pro no es solo “Free con más cantidad”.
Pro vende:
“Publica, comparte y mide una visita virtual profesional de tu negocio.”

---

## BUSINESS — Premium / futuro avanzado

### Objetivo
Plan premium para negocios con necesidades avanzadas, marca blanca, mayor personalización, soporte prioritario y tecnologías futuras de reconstrucción 3D.

### Funciones principales
- Todo lo incluido en Pro, más:
- Negocios ilimitados.
- Posiciones ilimitadas.
- Dominio personalizado (proximamente/roadmap).
- Marca blanca (proximamente/roadmap).
- Tours privados con contrasena (roadmap).
- Historial de versiones (roadmap).
- Multiples usuarios/roles (roadmap).
- API access (proximamente/roadmap).
- Analiticas avanzadas (proximamente/roadmap).
- Exportacion CSV (roadmap).
- Soporte prioritario.
- Onboarding personalizado.
- Integraciones futuras.

### Gaussian Splatting

Business será el plan natural para la evolución avanzada de Oxphyre hacia reconstrucciones 3D fotorrealistas.

La idea comercial es que un negocio pueda pasar de un tour panorámico tradicional a una experiencia más inmersiva, navegable y premium, especialmente útil para alojamientos, restaurantes, espacios turísticos, showrooms, clínicas, gimnasios o locales con alto valor visual.

#### Demo experimental para TFG

Para la exposición del TFG, Gaussian Splatting no forma parte del core obligatorio ni del flujo productivo principal.

Se tratará únicamente como una demo experimental Business si se cumplen estas condiciones:

- El resultado debe ser visualmente espectacular y estable.
- No debe afectar al dashboard, base de datos, rutas críticas ni visor Free/Pro.
- Debe mostrarse como escena pregenerada, página aislada o iframe externo.
- No debe venderse como funcionalidad completamente productiva.
- Si el resultado no mejora claramente la percepción del plan Business, no se enseña.

Posible flujo de demo:

1. Grabar un vídeo corto del espacio.
2. Procesarlo con una herramienta externa gratuita si permite uso manual.
3. Generar una escena Gaussian Splatting.
4. Mostrarla como demo Business mediante enlace, iframe o página aislada.
5. Explicar que representa una línea de evolución premium, no el MVP principal.

#### Posible pipeline real de producción

En una versión comercial futura, Oxphyre podría integrar Gaussian Splatting de forma más completa mediante dos caminos:

**Opción A — Proveedor externo / API Enterprise**

- El cliente Business graba o envía un vídeo siguiendo una guía de captura.
- Oxphyre sube el vídeo a un proveedor especializado mediante API.
- El sistema consulta el estado del procesado.
- Al finalizar, descarga o enlaza el resultado generado.
- La escena se almacena o sirve desde infraestructura controlada por Oxphyre.
- El visor Business carga la escena final desde la web pública del tour.

Ventaja: menor complejidad técnica inicial y salida al mercado más rápida.
Riesgo: dependencia del proveedor, coste por uso, privacidad y límites de API.

**Opción B — Pipeline propio con GPU**

- El cliente Business sube el vídeo desde el dashboard.
- Oxphyre crea un trabajo de procesado en cola.
- Un worker con GPU procesa el vídeo mediante herramientas de Gaussian Splatting.
- El resultado se exporta como `.ply`, `.spz`, `.splat`, `.ksplat` o formato compatible.
- El archivo optimizado se guarda en Cloudflare R2/CDN.
- El visor Business lo carga desde Oxphyre como experiencia premium.
- El dashboard muestra estados como pendiente, procesando, listo o error de captura.

Ventaja: mayor control técnico, privacidad y diferenciación de producto.
Riesgo: más coste, más mantenimiento, necesidad de GPU, colas de trabajo y control de calidad.

#### Criterio de producto

Oxphyre no necesita inventar desde cero el algoritmo de Gaussian Splatting para aportar valor comercial.

El valor de Oxphyre Business estaría en:

- guía de captura para el cliente;
- procesado controlado;
- alojamiento del resultado;
- visor web integrado;
- QR, enlace público y embed;
- analíticas;
- soporte;
- marca blanca;
- experiencia final lista para negocio local.

Por tanto, Gaussian queda como evolución premium post-TFG, no como sustituto del MVP Free/Pro ya funcional.

### Posicionamiento comercial
Business vende:
“Tu negocio en 3D fotorrealista, con marca blanca, analíticas avanzadas y experiencia premium.”

---

## Decisión final de producto

### Free
Tour funcional limitado para probar Oxphyre.

### Pro
Tour profesional para captar clientes.

### Business
Experiencia premium/fotorrealista y evolución comercial avanzada.

---

## Regla de comunicación

Nunca prometer en Free:
“360º real completo con cualquier móvil.”

Comunicar:
“Visita inmersiva básica con fotos normales o panorámicas, sin cámaras especiales.”

Comunicar Pro:
“Tour profesional para compartir, incrustar, medir y convertir visitantes en clientes.”

Comunicar Business:
“Recorrido 3D fotorrealista y solución premium para negocios avanzados.”

---

## Decisiones cerradas (2026-05-20) — pre /precios

Las siguientes decisiones guiaron la implementacion de `/precios` y quedan como definicion vigente:

| Decisión | Valor |
|---|---|
| Posiciones Free | **3** (no 5 como en definiciones antiguas) |
| QR | Todos los planes: básico (Free, con branding Oxphyre) y profesional (Pro/Business) |
| Embed/iframe | Solo Pro y Business. Free solo tiene enlace público |
| Watermark Free | Overlay semitransparente en el visor + badge clicable “Creado con Oxphyre” hacia /precios |
| Mapa/ubicación | Incluido en Free (Leaflet/OSM, ya implementado en Mapa 1C) |
| Flechas navegación | Incluidas en Free (básicas, sin pines de texto/precio/CTA) |
| MiDaS crédito Free | Eliminado de la definición vigente; era estrategia anterior |

**Contradicciones resueltas:**
- `CLAUDE.md` y `AI_SYNC.md` decían 5 posiciones Free → actualizado a 3.
- `CLAUDE.md` ponía QR solo en Pro → actualizado: QR básico en Free, profesional en Pro.
- Watermark Free era solo “visible” → actualizada a overlay semitransparente más agresivo.
- Mapa/ubicación no aparecía en ningún plan → añadido a Free (ya implementado).
- “1 posición con MiDaS como crédito” en `CLAUDE.md` → estrategia histórica eliminada.

## Cierre validado en produccion (2026-05-21)

`/precios` y la seccion `#precios` de la landing ya estan implementadas y validadas visualmente en produccion.

- Free, Pro y Business se muestran como definicion comercial vigente.
- Pro queda destacado como plan recomendado.
- Business contiene varias capacidades avanzadas marcadas como proximamente/roadmap, no como disponibles de forma inmediata.
- Hotspots comerciales Pro/Business quedan como roadmap/proximamente; las flechas basicas de navegacion son la capacidad disponible actual.
- MiDaS no se vende como promesa comercial principal; queda como tecnologia interna/futura del producto.
