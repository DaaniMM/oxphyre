#!/bin/bash
# Arranque del microservicio MiDaS en producción via gunicorn.
# Un solo worker: MiDaS ocupa ~500MB de RAM; más workers provocarían OOM en t3.small.
# Timeout 120s: la primera inferencia tras un reinicio puede tardar ~30s en CPU.
cd /var/www/oxphyre/python-service
source venv/bin/activate
exec gunicorn --bind 127.0.0.1:5000 --workers 1 --timeout 120 app:app
