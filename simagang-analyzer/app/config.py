import os

from dotenv import load_dotenv

load_dotenv()  # baca .env di working directory kalau ada; env var asli tetap menang

API_KEY = os.environ.get("ANALISIS_API_KEY", "")
LLM_AKTIF = os.environ.get("ANALISIS_LLM_AKTIF", "false").lower() == "true"
UPLOAD_DIR = os.environ.get("ANALISIS_UPLOAD_DIR", "")  # kosong = tidak dibatasi (dev lokal)
VERSI = "1.0.0"

# ponytail: Groq = API eksternal (cloud), bukan "server sendiri" seperti diminta spek
# bagian 4. Dipakai sekarang hanya untuk dev/testing atas persetujuan — baca bagian 12.2
# #17 & #24 spesifikasi teknis sebelum dipakai dengan data peserta asli di produksi.
GROQ_API_KEY = os.environ.get("GROQ_API_KEY", "")
GROQ_MODEL = os.environ.get("GROQ_MODEL", "openai/gpt-oss-20b")
GROQ_TIMEOUT_DETIK = 20

LLM_SIAP = LLM_AKTIF or bool(GROQ_API_KEY)
