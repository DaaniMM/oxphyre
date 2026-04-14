# CLAUDE.md - Oxphyre Project Context

> Lee también AGENTS.md para instrucciones de comportamiento.
> Lee DEVLOG.md para historial completo de decisiones y avances.

## Qué es Oxphyre
SaaS de tours virtuales inmersivos para pequeños negocios locales. El dueño sube fotos de su local (4 por posición: N,S,E,O) → Python + MiDaS genera mapas de profundidad reales → editor canvas drag&drop permite construir la estructura de navegación del local → Three.js renderiza el tour inmersivo con hotspots y minimapa → clientes visitan escaneando un QR.

## Stack técnico
- **Frontend:** HTML5 + Tailwind CSS + CSS custom con variables globales + JS vanilla + Three.js
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