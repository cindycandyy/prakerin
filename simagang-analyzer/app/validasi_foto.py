"""Pemeriksaan foto otomatis lokal (spesifikasi teknis FR-29, bagian 4).

Foto wajib setengah badan dengan wajah jelas, bukan foto KTM/kartu pelajar (dipakai
untuk kartu masuk pabrik). Ini "pemeriksaan otomatis lokal sebagai peringatan" --
bukan penolakan keras: hasilnya flag + catatan yang dibaca HR, keputusan terima atau
minta foto ulang tetap di tangan HR. Deteksi wajah pakai YuNet (OpenCV DNN, model
ONNX resmi dari OpenCV Zoo), bukan Haar Cascade -- OpenCV 5.x menghapus
cv2.CascadeClassifier dari API publik.
"""
import os

import cv2

_PATH_MODEL = os.path.join(os.path.dirname(__file__), "models_cv", "face_detection_yunet.onnx")
_LEBAR_DETEKSI = 640  # YuNet dilatih & paling akurat pada resolusi kecil, resize dulu
_SKOR_AMBANG = 0.6

_detector = None


def _ambil_detector():
    global _detector
    if _detector is None:
        _detector = cv2.FaceDetectorYN_create(_PATH_MODEL, "", (_LEBAR_DETEKSI, _LEBAR_DETEKSI), score_threshold=_SKOR_AMBANG)
    return _detector


def validasi_foto(path: str) -> dict:
    """Return {"ok": bool, "catatan": [str, ...]}. "ok" True hanya kalau tidak ada
    catatan sama sekali -- tiap catatan adalah satu alasan kenapa foto perlu ditinjau
    manual oleh HR, bukan penolakan otomatis."""
    catatan = []

    img = cv2.imread(path)
    if img is None:
        return {"ok": False, "catatan": ["Berkas tidak bisa dibaca sebagai gambar."]}

    tinggi, lebar = img.shape[:2]
    if lebar < 200 or tinggi < 200:
        catatan.append("Resolusi foto terlalu kecil untuk diperiksa.")
        return {"ok": False, "catatan": catatan}

    if lebar > tinggi:
        # KTM/kartu pelajar hasil scan/foto biasanya landscape; pas foto setengah
        # badan untuk kartu masuk pabrik biasanya portrait atau persegi.
        catatan.append("Foto berorientasi landscape -- pas foto biasanya portrait, periksa apakah ini scan KTM/kartu pelajar.")

    skala = _LEBAR_DETEKSI / lebar
    img_kecil = cv2.resize(img, (_LEBAR_DETEKSI, max(1, int(tinggi * skala))))

    detector = _ambil_detector()
    detector.setInputSize((img_kecil.shape[1], img_kecil.shape[0]))
    _, faces = detector.detect(img_kecil)

    jumlah_wajah = 0 if faces is None else len(faces)
    if jumlah_wajah == 0:
        catatan.append("Tidak ada wajah yang terdeteksi pada foto.")
    elif jumlah_wajah > 1:
        catatan.append(f"Terdeteksi {jumlah_wajah} wajah -- pastikan foto hanya berisi satu orang.")
    else:
        lebar_wajah = float(faces[0][2])
        rasio = lebar_wajah / img_kecil.shape[1]
        if rasio < 0.12:
            catatan.append("Wajah terlalu kecil dalam foto -- pastikan setengah badan, bukan foto jarak jauh.")
        elif rasio > 0.65:
            catatan.append("Wajah terlalu memenuhi foto -- pastikan setengah badan, bukan close-up wajah saja.")

    return {"ok": len(catatan) == 0, "catatan": catatan}
