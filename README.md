# Oxphyre

Oxphyre es una plataforma SaaS de tours virtuales inmersivos para pequeños negocios locales.

El objetivo del proyecto es permitir que restaurantes, peluquerías, gimnasios, alojamientos, clínicas u otros espacios físicos puedan mostrar su local de forma digital mediante un tour web accesible desde navegador, enlace público o código QR.

## Estado del proyecto

Proyecto desarrollado como TFG de 2º de Desarrollo de Aplicaciones Web.

Actualmente incluye:

- Landing pública y páginas informativas.
- Registro, login y recuperación de contraseña.
- Dashboard privado para usuarios.
- Gestión de negocios, tours, posiciones e imágenes.
- Visor público inmersivo basado en JavaScript vanilla y Three.js.
- Códigos QR descargables para compartir tours.
- Analíticas Free/Pro.
- Almacenamiento de imágenes preparado para Cloudflare R2.
- Diseño responsive para desktop, tablet y móvil.
- Despliegue en producción con dominio propio y HTTPS.

## Stack técnico

- PHP 8.1
- MySQL 8
- Arquitectura MVC propia
- Front Controller
- JavaScript vanilla
- Three.js
- HTML5 / CSS3
- Composer
- PHPMailer
- Cloudflare R2
- Nginx + PHP-FPM
- AWS EC2
- Git / GitHub

## Funcionalidades principales

- Creación de negocios.
- Creación de tours virtuales.
- Gestión de posiciones del recorrido.
- Subida de imágenes.
- Visor público Oxphyre Room.
- Compartición mediante URL pública y QR.
- Analíticas por plan.
- Páginas públicas de soporte, precios, privacidad, términos y cookies.

## Planes del producto

Oxphyre está planteado con un modelo SaaS por niveles:

- **Free:** entrada al producto con límites básicos.
- **Pro:** mayor capacidad, visor más profesional y analíticas.
- **Business:** evolución premium con marca blanca, analíticas avanzadas y posibles experiencias 3D avanzadas como Gaussian Splatting.

## Seguridad

El proyecto aplica buenas prácticas como:

- Sesiones PHP.
- Contraseñas hasheadas.
- Consultas preparadas con PDO.
- Protección CSRF en formularios sensibles.
- Validación de permisos y ownership.
- Variables sensibles fuera del repositorio mediante `.env`.
- HTTPS en producción.
- No inclusión de credenciales reales en el repositorio.

## Autor

Daniel Martínez Martos  
Proyecto Final de Grado — 2º Desarrollo de Aplicaciones Web (DAW)
Curso 2024-2026
