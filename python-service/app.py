"""
Oxphyre MiDaS microservice.

Expone POST /process — recibe una imagen y devuelve su mapa de
profundidad generado con MiDaS Small (torch.hub) como PNG en base64.

Corre en 127.0.0.1:5000 (no accesible desde el exterior).
El modelo se carga una sola vez al arrancar para minimizar latencia.
"""

import io
import os
import hmac
import base64
import logging
import numpy as np

import torch
from PIL import Image
from flask import Flask, request, jsonify

# ── Configuración ──────────────────────────────────────────────────────────────

SERVICE_TOKEN = os.environ.get("PYTHON_SERVICE_TOKEN", "")
MAX_BYTES     = 20 * 1024 * 1024  # 20 MB
DEVICE        = torch.device("cpu")  # el servidor no tiene GPU

logging.basicConfig(level=logging.INFO,
                    format="%(asctime)s [%(levelname)s] %(message)s")
log = logging.getLogger(__name__)

app = Flask(__name__)
app.config["MAX_CONTENT_LENGTH"] = MAX_BYTES

# ── Carga del modelo al arrancar (una sola vez) ────────────────────────────────
# torch.hub descarga y cachea el modelo en ~/.cache/torch/hub/ la primera vez.
# En llamadas posteriores lo carga desde disco sin conexión a internet.

log.info("Cargando MiDaS Small via torch.hub ...")
midas = torch.hub.load("intel-isl/MiDaS", "MiDaS_small")
midas.to(DEVICE)
midas.eval()

# Transformaciones oficiales de MiDaS Small (normalización + resize esperado por el modelo)
midas_transforms = torch.hub.load("intel-isl/MiDaS", "transforms")
transform = midas_transforms.small_transform

log.info("MiDaS Small listo en %s.", DEVICE)


# ── Helpers de validación ──────────────────────────────────────────────────────

def _is_localhost(req) -> bool:
    """Rechaza cualquier request que no venga de localhost."""
    return req.remote_addr in ("127.0.0.1", "::1")


def _token_valid(req) -> bool:
    """
    Compara el header X-Service-Token con PYTHON_SERVICE_TOKEN del entorno.
    hmac.compare_digest evita timing attacks.
    Si el token no está configurado en el entorno rechaza siempre.
    """
    if not SERVICE_TOKEN:
        return False
    received = req.headers.get("X-Service-Token", "")
    return hmac.compare_digest(SERVICE_TOKEN, received)


# ── Endpoint principal ─────────────────────────────────────────────────────────

@app.route("/process", methods=["POST"])
def process():
    # Solo se aceptan requests desde localhost
    if not _is_localhost(request):
        return jsonify({"success": False, "error": "Forbidden"}), 403

    # Token de autenticación obligatorio
    if not _token_valid(request):
        return jsonify({"success": False, "error": "Unauthorized"}), 401

    # El campo 'image' es obligatorio en el multipart form
    if "image" not in request.files:
        return jsonify({"success": False, "error": "Campo 'image' no encontrado"}), 400

    file = request.files["image"]

    # Validar que el archivo es una imagen real (no solo por extensión)
    try:
        pil_img = Image.open(file.stream)
        pil_img.verify()       # lanza excepción si el archivo está corrupto
        file.stream.seek(0)    # volver al inicio tras verify()
        pil_img = Image.open(file.stream).convert("RGB")
    except Exception as exc:
        log.warning("Imagen inválida recibida: %s", exc)
        return jsonify({"success": False, "error": "Archivo no es una imagen válida"}), 400

    # Tamaño original para la interpolación final
    orig_w, orig_h = pil_img.size

    try:
        # MiDaS espera un array NumPy en formato RGB (uint8, HxWx3)
        img_np = np.array(pil_img)

        # Aplicar las transformaciones oficiales de MiDaS Small:
        # convierte a tensor float32, normaliza y redimensiona al tamaño esperado
        input_batch = transform(img_np).to(DEVICE)

        # Inferencia sin gradientes — ahorra memoria y acelera en CPU
        with torch.no_grad():
            prediction = midas(input_batch)

        # Interpolar el mapa de profundidad al tamaño original de la imagen
        prediction = torch.nn.functional.interpolate(
            prediction.unsqueeze(1),
            size=(orig_h, orig_w),
            mode="bicubic",
            align_corners=False,
        ).squeeze()

        # Normalizar a [0, 255] para guardar como PNG en escala de grises
        depth_np = prediction.cpu().numpy()
        max_val  = depth_np.max()
        if max_val > 0:
            depth_np = (depth_np * 255.0 / max_val).astype(np.uint8)
        else:
            depth_np = depth_np.astype(np.uint8)

        # Codificar el resultado como PNG en memoria y convertir a base64
        depth_img = Image.fromarray(depth_np, mode="L")
        buf = io.BytesIO()
        depth_img.save(buf, format="PNG")
        depth_b64 = base64.b64encode(buf.getvalue()).decode("utf-8")

    except Exception as exc:
        log.error("Error en inferencia MiDaS: %s", exc, exc_info=True)
        return jsonify({"success": False, "error": "Error procesando la imagen"}), 500

    return jsonify({"success": True, "depth_map": depth_b64})


# ── Health check ───────────────────────────────────────────────────────────────

@app.route("/health", methods=["GET"])
def health():
    # Desde el exterior solo devuelve ok sin información adicional
    if not _is_localhost(request):
        return jsonify({"status": "ok"}), 200
    return jsonify({"status": "ok", "model": "MiDaS_small", "device": str(DEVICE)}), 200


if __name__ == "__main__":
    # Arranque directo solo para desarrollo; en producción usar gunicorn via start.sh
    app.run(host="127.0.0.1", port=5000, debug=False)
