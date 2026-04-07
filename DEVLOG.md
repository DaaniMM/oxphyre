# DEVLOG - Oxphyre

## ¿Qué es Oxphyre?
Plataforma SaaS de tours virtuales 360° para pequeños negocios locales
(gimnasios, restaurantes, tiendas, peluquerías...).

El dueño del negocio sube fotos 360° → Python las optimiza → Three.js
genera un tour navegable → los clientes lo visitan escaneando un QR.

---

## Stack técnico
- **Frontend:** HTML5 + CSS3 + JavaScript vanilla + Three.js
- **Backend:** PHP 8 puro (sin frameworks)
- **Base de datos:** MySQL
- **Microservicio IA:** Python + Flask + Pillow (optimización imágenes)
- **Automatización:** n8n
- **Despliegue:** AWS EC2 (Ubuntu + Nginx)
- **Control de versiones:** Git + GitHub

---

## Decisiones importantes
- Sin frameworks JS (React, Vue...) porque el TFG debe demostrar dominio puro
- Three.js es la única librería externa permitida, es el core del proyecto
- AWS en vez de Webempresa porque soporta Python nativamente y ya hay créditos disponibles (126$)
- Repo público para que el tribunal pueda verlo
- La app estará desplegada online → los profesores pueden probarla desde
  sus portátiles durante la exposición escaneando un QR

---

## Registro de pasos

### [07/04/2025] Día 1 - Setup inicial

**Paso 1 - Crear repositorio GitHub**
- Nombre: `oxphyre`
- Descripción: `3D virtual tour platform for local businesses`
- Visibilidad: Público
- README: Sí
- Licencia: MIT
- .gitignore: Node (base, se ampliará)
- Motivo: Control de versiones desde el primer día, visible para el tribunal

**Paso 2 - Clonar en local**
- Ruta: `C:\Users\12dan\OneDrive\Escritorio\Desarrollo_Web\DAW\oxphyre`
- Comando: `git clone ... .`
- Motivo: Trabajar en local y sincronizar con GitHub

**Paso 3 - Crear estructura de carpetas**
- `src/` → código fuente del frontend y Three.js
- `src/Experience/` → clases principales de Three.js (patrón Experience)
- `src/Experience/Utils/` → utilidades (Sizes, Time, EventEmitter, Resources)
- `src/Experience/World/` → elementos de la escena 3D
- `public/` → archivos estáticos servidos directamente
- `public/360/` → fotos 360° de los negocios
- `public/models/` → modelos 3D (.glb) para hotspots
- `public/assets/` → imágenes, iconos, fuentes
- `backend/` → API REST en PHP
- `backend/api/` → endpoints de la API
- `backend/config/` → configuración BD y constantes
- `backend/models/` → clases PHP que interactúan con MySQL
- `docs/` → documentación y memoria del TFG
- `DEVLOG.md` → este archivo, diario de desarrollo

**Paso 4 - Configurar servidor AWS EC2**
- Instancia: t3.micro, Ubuntu 22.04 LTS, 20GB
- IP elástica asignada: 13.62.93.7 (fija, no cambia aunque se reinicie)
- Stack instalado: Nginx, PHP 8.1 + PHP-FPM, MySQL 8.0, Python 3 + pip + venv
- Motivo IP elástica: garantiza que el QR y los enlaces no se rompan si la instancia se reinicia
- Nginx configurado en /etc/nginx/sites-available/oxphyre
  - Puerto 80, root en /var/www/oxphyre/public
  - Rutas / → archivos estáticos (Three.js, HTML, CSS)
  - Rutas /api → PHP
- Repo clonado en /var/www/oxphyre
- Verificado: http://13.62.93.7 sirve correctamente

**Paso 5 - Flujo de trabajo establecido**
- Desarrollo en local (VSCode)
- git push desde local a GitHub
- git pull en el servidor para desplegar
- El servidor siempre tiene la versión actualizada
