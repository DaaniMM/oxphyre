"""
Oxphyre MiDaS microservice.

Expone POST /process — recibe una imagen y devuelve su mapa de
profundidad generado con MiDaS Small como PNG en base64.

Corre en 127.0.0.1:5000 (no accesible desde el exterior).
El modelo se carga una sola vez al arrancar para minimizar latencia.
"""

import os
import io
import hmac
import base64
import logging
import numpy as np

import torch
from PIL import Image
from flask import Flask, request, jsonify
from transformers import DPTForDepthEstimation, DPTImageProcessor

# ── Configuración ──────────────────────────────────────────────────────────────

BASE_DIR      = os.path.dirname(os.path.abspath(__file__))
MODEL_ID      = "Intel/dpt-small-midas"
SERVICE_TOKEN = os.environ.get("PYTHON_SERVICE_TOKEN", "")
MAX_BYTES     = 20 * 1024 * 1024  # 20 MB

logging.basicConfig(level=logging.INFO,
                    format="%(asctime)s [%(levelname)s] %(message)s")
log = logging.getLogger(__name__)

app = Flask(__name__)
app.config["MAX_CONTENT_LENGTH"] = MAX_BYTES

# ── Carga del modelo al arrancar (una sola vez) ────────────────────────────────

log.info("Cargando DPTImageProcessor desde %s ...", MODEL_ID)
processor = DPTImageProcessor.from_pretrained(MODEL_ID)

log.info("Cargando MiDaS Small (DPTForDepthEstimation) desde %s ...", MODEL_ID)
model = DPTForDepthEstimation.from_pretrained(MODEL_ID)

model.eval()
log.info("Modelo listo.")


# ── Helpers de validación ──────────────────────────────────────────────────────

def _is_localhost(req) -> bool:
    return req.remote_addr in ("127.0.0.1", "::1")


def _token_valid(req) -> bool:
    if not SERVICE_TOKEN:
        # Si no hay token configurado se rechaza siempre para no operar sin auth
        return False
    received = req.headers.get("X-Service-Token", "")
    # hmac.compare_digest evita timing attacks
    return hmac.compare_digest(SERVICE_TOKEN, received)


# ── Endpoint principal ─────────────────────────────────────────────────────────

@app.route("/process", methods=["POST"])
def process():
    # Solo localhost
    if not _is_localhost(request):
        return jsonify({"success": False, "error": "Forbidden"}), 403

    # Token de autenticación
    if not _token_valid(request):
        return jsonify({"success": False, "error": "Unauthorized"}), 401

    # Archivo de imagen obligatorio
    if "image" not in request.files:
        return jsonify({"success": False, "error": "Campo 'image' no encontrado"}), 400

    file = request.files["image"]

    # Validar que es una imagen real (MIME real, no extensión)
    try:
        image = Image.open(file.stream).convert("RGB")
        image.verify()          # detecta archivos corruptos
        file.stream.seek(0)     # volver al inicio tras verify()
        image = Image.open(file.stream).convert("RGB")
    except Exception as exc:
        log.warning("Imagen inválida: %s", exc)
        return jsonify({"success": False, "error": "Archivo no es una imagen válida"}), 400

    original_size = image.size  # (width, height)

    try:
        # Preparar inputs con el procesador de HuggingFace
        inputs = processor(images=image, return_tensors="pt")

        # Inferencia sin gradientes para ahorrar memoria y acelerar
        with torch.no_grad():
            outputs = model(**inputs)
            predicted_depth = outputs.predicted_depth  # shape: (1, H', W')

        # Interpolar al tamaño original (height, width) con bicúbica
        prediction = torch.nn.functional.interpolate(
            predicted_depth.unsqueeze(1),
            size=(original_size[1], original_size[0]),  # (H, W)
            mode="bicubic",
            align_corners=False,
        )

        # Normalizar a [0, 255]
        depth_np = prediction.squeeze().cpu().numpy()
        max_val  = depth_np.max()
        if max_val > 0:
            depth_np = (depth_np * 255.0 / max_val).astype(np.uint8)
        else:
            depth_np = depth_np.astype(np.uint8)

        # Guardar como PNG en memoria
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
    if not _is_localhost(request):
        return jsonify({"status": "ok"}), 200  # público pero sin info
    return jsonify({"status": "ok", "model": MODEL_ID}), 200


if __name__ == "__main__":
    # Arranque directo solo para desarrollo; en producción usar gunicorn via start.sh
    app.run(host="127.0.0.1", port=5000, debug=False)
