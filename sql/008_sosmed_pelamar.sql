-- Link media sosial pelamar (LinkedIn, Instagram, TikTok, Twitter) -- sebelumnya
-- panel "Media sosial" di Halaman B HR (detail_pelamar.php) hanya data contoh
-- hardcode, belum ada kolom DB sama sekali. Semua opsional, simpan link lengkap
-- (bukan username) supaya tidak perlu menyusun URL dari handle.
ALTER TABLE pkl_data
    ADD COLUMN sosmed_linkedin VARCHAR(255) NULL AFTER hp_pembimbing,
    ADD COLUMN sosmed_instagram VARCHAR(255) NULL AFTER sosmed_linkedin,
    ADD COLUMN sosmed_tiktok VARCHAR(255) NULL AFTER sosmed_instagram,
    ADD COLUMN sosmed_twitter VARCHAR(255) NULL AFTER sosmed_tiktok;
