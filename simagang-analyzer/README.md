# simagang-analyzer

Layanan baca PDF + skor rekomendasi untuk modul Analisis Penempatan. FastAPI, tanpa
database — PHP yang memanggil dan PHP yang menulis hasil ke MySQL (lihat spesifikasi
teknis bagian 3). Jalan di `127.0.0.1:8001` saja.

## Jalankan lokal

```
pip install -r requirements.txt
cp .env.example .env   # isi nilai yang perlu, lihat di bawah
uvicorn app.main:app --host 127.0.0.1 --port 8001
```

Konfigurasi dibaca dari `.env` (tidak masuk git) kalau ada, atau environment variable
biasa. `.env.example` daftar semua kunci yang dikenal.

Env var opsional: `ANALISIS_API_KEY` (cocokkan dengan `analisis_layanan_api_key` di
`application/config/analisis.php`), `ANALISIS_UPLOAD_DIR` (batasi path berkas yang
boleh dibaca).

### Model bahasa (opsional, hanya dipanggil saat aturan lokal kurang cukup)

Set `GROQ_API_KEY` untuk mengaktifkan tahap LLM lewat Groq. **Peringatan:** Groq adalah
API eksternal (cloud), bukan "server sendiri" seperti disyaratkan spesifikasi teknis
bagian 4 — hanya untuk dev/testing. Jangan pakai dengan data peserta asli di produksi
tanpa persetujuan tertulis Petrosida (bagian 12.2 #17 & #24). Teks tetap diredaksi
(bagian 4, langkah 1-5) sebelum dikirim, dan diperiksa ulang — bila masih ada digit
panjang/email, teks tidak dikirim sama sekali.

Isi `GROQ_API_KEY` di `.env` (lihat `.env.example`), atau `export GROQ_API_KEY=...`
langsung di shell.

## Tes

```
python tests/test_ekstraksi.py
```

PDF uji dibuat on-the-fly dengan PyMuPDF (tanpa dependency tambahan), bukan dari berkas
fixture asli — belum ada contoh proposal/transkrip nyata di repo ini.

## Belum dikerjakan di sini

- Model bahasa di server sendiri (`app/llm.py:panggil_model_lokal`) — placeholder,
  kembalikan `None`. Groq dipakai sementara sebagai pengganti untuk dev/testing (lihat
  di atas); pilihan model lokal/hosting tetap belum diputuskan (bagian 12.2 #17).
- Redaksi langkah 6 (pengenal nama lokal/NER) — pola regex (langkah 1-5) + pemeriksaan
  ulang jadi pengaman utama untuk saat ini.

Pemeriksaan foto otomatis (bagian 7.7, FR-29) **sudah dikerjakan** sebagai
`POST /v1/validasi_foto` (bukan `/v1/foto-cek` seperti rencana awal) — lihat
`app/main.py` dan `app/validasi_foto.py`. Dipanggil otomatis dari PHP lewat
`application/libraries/Analisis_foto.php` setiap kali foto diunggah (baik lewat
`Transaksi.php` lama maupun wizard `Pelamar.php` baru), hasilnya disimpan dan
ditampilkan ke HR lewat `Model_prk_pelamar::getFotoCek()` di halaman detail pelamar.
