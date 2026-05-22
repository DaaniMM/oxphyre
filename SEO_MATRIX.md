# SEO_MATRIX.md — Matriz viva SEO de Oxphyre

## Propósito

Este documento es el equivalente interno al Excel SEO usado en clase, adaptado a Oxphyre y guardado en el repositorio como markdown versionado. Sirve para revisar de forma rápida qué intención trabaja cada URL pública, qué keyword principal se está atacando, cómo está orientado el H1/meta, qué enlaces internos sostienen cada página y qué acción conviene tomar cuando haya datos reales.

No sustituye a Google Search Console ni a herramientas SEO profesionales. Es una matriz táctica para coordinar trabajo entre ChatGPT, Claude Web, Codex, Claude Code y el alumno, y también puede enseñarse en el TFG como metodología SEO real aplicada al proyecto.

Las métricas reales de impresiones, clics, CTR y posición media se rellenarán cuando Search Console tenga datos suficientes. Hasta entonces, cualquier dato de dificultad, competencia o SERP queda marcado como estimado / por validar.

## Arquitectura SEO actual

```text
/
├── /tour-virtual-para-negocios
│   └── /tour-virtual-para-restaurantes
│
├── /blog
│   ├── /blog/como-hacer-fotos-para-tour-virtual
│   ├── /blog/tour-virtual-con-movil-sin-camara-360
│   └── /blog/como-usar-qr-para-ensenar-tu-local
│
├── /precios
├── /sobre-nosotros
├── /soporte
├── /privacidad
├── /terminos
└── /cookies
```

## Decisiones SEO

- `/tour-virtual-para-negocios` es la página pilar core del silo de tours virtuales para negocios.
- `/tour-virtual-para-restaurantes` es la primera página sectorial hija/comercial del silo principal.
- `/blog` es el hub de recursos.
- Los 3 posts actuales son apoyo informativo para reforzar la pilar y la sectorial, no páginas comerciales independientes.
- No crear más posts ni sectoriales sin estrategia, validación posterior o una razón clara basada en datos.
- No prometer Matterport, digital twin, escaneo 3D, Gaussian, tour 360 profesional completo ni integraciones automáticas no existentes.
- El foco diferencial de Oxphyre es self-service/autogestión con móvil, fotos/panorámicas, zonas, flechas, QR, mapa y enlace público.
- Estado general: MVP SEO implementado y pendiente de datos reales.

## Matriz SEO por URL

| URL | Tipo de página | Rol SEO | Keyword principal | Keywords secundarias | Intención de búsqueda | H1 actual | Meta title actual | Meta description actual | Densidad objetivo | Densidad real | Competencia/dificultad estimada | Competidores SERP | Enlaces internos entrantes | Enlaces internos salientes | CTA principal | Riesgo de canibalización | Estado indexación | Impresiones | Clics | CTR | Posición media | Próxima acción |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `/tour-virtual-para-negocios` | Página pilar | Pilar core del silo de tours virtuales para negocios | tour virtual para negocios | crear tour virtual para mi negocio<br>visita virtual para negocios<br>tour virtual con móvil<br>tour virtual sin cámara 360<br>visita virtual negocio local | Comercial/informativa | Crea la visita virtual de tu negocio con tu móvil — tú mismo, sin agencias ni cámaras 360 | Crea un Tour Virtual para tu Negocio con el Móvil \| Oxphyre | Sube fotos de tu local y crea una visita inmersiva navegable. Tus clientes exploran zona a zona antes de llegar. Sin cámara 360 ni fotógrafo. | 0,8% - 1,3% en keyword principal y variaciones naturales | Pendiente de auditoría / por validar | Media-alta (estimada / por validar) | Estimado / por validar: agencias de tours virtuales, soluciones 360, directorios y herramientas SaaS | Home, `/precios`, `/blog`, 3 posts, `/tour-virtual-para-restaurantes`, `/soporte` | `/registro?plan=free`, `/precios`, `/tour-virtual-para-restaurantes`, 3 posts relacionados | Crear mi tour gratis | Medio: puede mezclarse con posts de móvil si los anchors no separan intención | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Revisar Search Console en 24-72h y reforzar autogestión si aparece tráfico de agencias |
| `/tour-virtual-para-restaurantes` | Página sectorial | Primera hija comercial del silo principal | tour virtual para restaurantes | visita virtual restaurante<br>tour virtual para bares<br>enseñar restaurante online<br>visita virtual hostelería<br>tour virtual para cafeterías<br>QR para restaurante | Comercial sectorial | Tour virtual para restaurantes: enseña tu ambiente antes de que reserven | Tour virtual para restaurantes \| Oxphyre | Muestra comedor, barra y terraza con una visita virtual sencilla. Crea zonas, comparte enlace o QR y ayuda al cliente antes de reservar. | 0,8% - 1,3% en keyword principal y variaciones naturales | Pendiente de auditoría / por validar | Media (estimada / por validar) | Estimado / por validar: agencias locales, visitas 360 para hostelería, herramientas de fotografía y plataformas de reservas | `/tour-virtual-para-negocios`, `/blog`, posts de móvil y QR | `/registro?plan=free`, `/precios`, `/tour-virtual-para-negocios`, posts de fotos y QR | Crear mi tour gratis | Medio: evitar competir con pilar general; mantener foco restaurante/hostelería | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Validar impresiones para hostelería y revisar que no prometa reservas, mesas ni integraciones automáticas |
| `/blog` | Hub de recursos | Hub navegacional/informativo | No aplica / marca + recursos | guías tours virtuales<br>recursos para negocios locales<br>consejos para visitas virtuales | Navegacional/informativa | Blog de Oxphyre: guías para crear mejores tours virtuales | Blog de Oxphyre: guías para crear mejores tours virtuales | Guías prácticas de Oxphyre para negocios locales que quieren crear, mejorar y compartir tours virtuales con fotos hechas desde el móvil. | No aplica; priorizar claridad y navegación | Pendiente de auditoría / por validar | Baja-media (estimada / por validar) | Estimado / por validar: blogs de marketing local, guías de fotografía y recursos SaaS | Footer público, home, posts, pilar, sectorial | `/tour-virtual-para-negocios`, `/tour-virtual-para-restaurantes`, 3 posts, `/registro?plan=free`, `/precios` | Crear mi tour gratis | Bajo: riesgo principal de thin content si no se actualiza | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Medir si recibe impresiones de marca/recursos y mantenerlo como hub, no como página comercial |
| `/blog/como-hacer-fotos-para-tour-virtual` | Blog informativo | Apoyo práctico a la pilar | fotos para tour virtual | cómo hacer fotos para tour virtual con móvil<br>fotos para visita virtual<br>fotos panorámicas para negocio<br>hacer fotos de negocio con móvil<br>cómo fotografiar mi local<br>subir fotos sin perder calidad | Informativa práctica | Cómo hacer fotos para un tour virtual con el móvil y que tu negocio se vea mejor | Fotos para Tour Virtual con Móvil \| Guía para Negocios | Aprende a hacer fotos y panorámicas de tu local con el móvil: luz, encuadre, orden, errores comunes y checklist para crear un tour virtual claro. | 0,8% - 1,3% en keyword principal y variaciones naturales | Pendiente de auditoría / por validar | Media (estimada / por validar) | Estimado / por validar: blogs de fotografía, guías de Google/360, agencias y tutoriales móviles | `/blog`, `/tour-virtual-para-negocios`, `/tour-virtual-para-restaurantes`, post QR | `/tour-virtual-para-negocios`, `/soporte`, post móvil, `/registro?plan=free` | Crear mi tour gratis | Bajo: vigilar que no compita con pilar; mantener enfoque fotográfico | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Revisar consultas de fotos/móvil y reforzar calidad de imagen si hay impresiones sin clic |
| `/blog/tour-virtual-con-movil-sin-camara-360` | Blog informativo/comparativo | Diferenciación frente a cámaras 360/agencias/apps | tour virtual con móvil sin cámara 360 | visita virtual con fotos del móvil<br>crear visita virtual con móvil<br>tour virtual sin cámara 360<br>visita inmersiva sin cámara especial<br>recorrido virtual con smartphone | Informativa/comercial | Tour virtual con móvil: cómo enseñar tu negocio sin cámara 360 | Tour virtual con móvil sin cámara 360 \| Oxphyre | Crea una visita virtual con fotos y panorámicas del móvil. Organiza tu local por zonas, conecta con flechas y comparte el tour sin cámara 360. | 0,8% - 1,3% en keyword principal y variaciones naturales | Pendiente de auditoría / por validar | Media (estimada / por validar) | Estimado / por validar: apps 360, tutoriales de cámara móvil, agencias y herramientas de visitas virtuales | `/blog`, `/tour-virtual-para-negocios`, post fotos | `/tour-virtual-para-negocios`, `/precios`, post fotos, `/tour-virtual-para-restaurantes` | Ver guía principal | Medio: puede solaparse con pilar; mantener enfoque comparativo y limitaciones | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Si atrae tráfico de apps 360, reforzar que Oxphyre crea recorrido por zonas, no tour 360 profesional completo |
| `/blog/como-usar-qr-para-ensenar-tu-local` | Blog informativo contextual | Apoyo a distribución/QR del tour | QR para tour virtual | código QR para visita virtual<br>QR para enseñar local<br>QR en escaparate<br>QR en carta<br>QR para restaurante<br>QR descargable tour | Informativa contextual | Cómo usar el QR de tu tour virtual para enseñar tu local antes de que el cliente entre | QR para Tour Virtual \| Escaparate, Carta y Tarjetas | Usa el QR de tu tour virtual en escaparate, carta, mesa o tarjeta. Un escaneo permite ver tu local antes de entrar o reservar. | 0,8% - 1,3% en keyword principal y variaciones naturales | Pendiente de auditoría / por validar | Media-baja (estimada / por validar) | Estimado / por validar: generadores QR, blogs de marketing local, herramientas de carta QR y SaaS de QR | `/blog`, `/tour-virtual-para-negocios`, `/tour-virtual-para-restaurantes`, `/soporte` | `/tour-virtual-para-negocios`, `/tour-virtual-para-restaurantes`, `/precios`, `/soporte`, `/registro?plan=free` | Crear mi tour gratis | Medio: puede atraer tráfico genérico de QR; mantener foco en QR del tour virtual | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Revisar consultas; si entra tráfico de generadores QR, ajustar title/H1/primer párrafo hacia tour virtual |

## Seguimiento Search Console

Fecha de solicitud de indexación manual: 2026-05-22.

| URL | Fecha solicitud indexación | Estado indexación | Impresiones 24-72h | Impresiones 7-14 días | Clics | CTR | Posición media | Consultas detectadas | Acción siguiente |
| --- | --- | --- | --- | --- | --- | --- | --- | --- | --- |
| `https://oxphyre.com/blog` | 2026-05-22 | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Revisar si Google la toma como hub y si detecta enlaces a los 3 posts |
| `https://oxphyre.com/blog/como-hacer-fotos-para-tour-virtual` | 2026-05-22 | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Revisar consultas de fotos, panorámicas y móvil |
| `https://oxphyre.com/blog/tour-virtual-con-movil-sin-camara-360` | 2026-05-22 | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Revisar si capta búsquedas sobre móvil/cámara 360 sin canibalizar la pilar |
| `https://oxphyre.com/blog/como-usar-qr-para-ensenar-tu-local` | 2026-05-22 | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Revisar si las consultas son sobre QR del tour o generadores QR genéricos |
| `https://oxphyre.com/tour-virtual-para-restaurantes` | 2026-05-22 | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Revisar impresiones de restaurantes, bares, cafeterías y hostelería |
| `https://oxphyre.com/tour-virtual-para-negocios` | 2026-05-22 | Indexación solicitada manualmente | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Pendiente de Search Console | Revisar queries generales del silo y posible competencia con posts |

## Baremos de revisión

- Si una URL no recibe impresiones: revisar indexación, sitemap, enlazado interno, intención y dificultad de keyword.
- Si recibe impresiones pero pocos clics: revisar meta title, meta description y propuesta de valor en SERP.
- Si recibe clics pero no convierte: revisar CTA, primer bloque, claridad del producto y fricción de registro.
- Si dos URLs aparecen para consultas parecidas: revisar canibalización, anchors internos, H1 y foco semántico.
- Si una URL atrae tráfico no cualificado: ajustar keyword, title, H1 y primer párrafo.
- Si Search Console muestra consultas inesperadas interesantes: valorar crear nueva página o ajustar contenido, pero no crear contenido sin estrategia.

## Próximas acciones

- Revisar Search Console en 24-72h.
- Revisar Search Console en 7-14 días.
- Rellenar métricas reales cuando existan.
- Preparar una versión tipo Excel/tabla para memoria o defensa si el profesor pide análisis SEO.
- No ampliar el silo hasta validar datos iniciales o tener una razón clara.
