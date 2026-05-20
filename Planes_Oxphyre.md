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
- Hotspots completos:
  - navegación;
  - texto;
  - descripción;
  - precios;
  - información de productos/servicios;
  - CTA básico como “Reservar”, “Llamar” o “Contactar”.
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
  - hotspots completos;
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
- Dominio personalizado.
- Marca blanca.
- Tours privados con contraseña.
- Historial de versiones.
- Múltiples usuarios/roles.
- API access.
- Analíticas avanzadas.
- Exportación CSV.
- Soporte prioritario.
- Onboarding personalizado.
- Integraciones futuras.

### Gaussian Splatting
Business será el plan natural para la evolución post-TFG:

- Oxphyre 3D Capture.
- Cliente graba vídeo del local.
- Oxphyre procesa el vídeo con Gaussian Splatting.
- Resultado: recorrido 3D fotorrealista navegable.
- Stack previsto:
  - OpenSplat como herramienta externa sin modificar.
  - SuperSplat Viewer para visualización.
- No forma parte del core obligatorio del TFG.
- Para TFG puede mostrarse como demo pregenerada si da tiempo.
- En producción comercial se procesaría con GPU bajo demanda.

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

Las siguientes decisiones quedan cerradas para implementar `/precios` sin contradicciones:

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