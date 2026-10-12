-- Seeder prk_sinyal_divisi, prk_pemetaan_jurusan, prk_mapel_relevan dari
-- kolom "Sinyal kecocokan di proposal" di dokumen
-- "Analisis & Desain Sistem - Aplikasi HR Manajemen Anak Magang" bagian
-- "Struktur departemen dan posisi magang" dan "Jurusan dengan peminatan".
-- Semua usulan awal di dokumen sumber (ditandai "untuk dikoreksi client"),
-- bisa diubah HR lewat halaman Pengaturan tanpa ubah kode. Jenis kata kunci:
-- 'alat' = metode/tools yang disebut proposal, 'topik' = bidang/subjek kerja.
-- Dicocokkan ke id_divisi lewat nama (bukan id, karena ada gap dari migrasi lama).

-- ============================================================
-- prk_sinyal_divisi (kata kunci alat & topik per divisi)
-- ============================================================

-- Riset & Pengembangan
INSERT INTO prk_sinyal_divisi (id_divisi, kata_kunci, jenis, bobot)
SELECT id, v.kata_kunci, v.jenis, v.bobot FROM prk_divisi d
JOIN (
    SELECT 'Development produk baru' AS divisi, 'formulasi' AS kata_kunci, 'topik' AS jenis, 3 AS bobot
    UNION ALL SELECT 'Development produk baru', 'riset laboratorium', 'topik', 2
    UNION ALL SELECT 'Development produk baru', 'agroteknologi', 'topik', 2
    UNION ALL SELECT 'Development produk baru', 'kimia', 'topik', 2

    UNION ALL SELECT 'Perizinan merk dan izin edar', 'dokumen teknis', 'topik', 2
    UNION ALL SELECT 'Perizinan merk dan izin edar', 'administrasi regulasi', 'topik', 3
    UNION ALL SELECT 'Perizinan merk dan izin edar', 'hukum', 'topik', 2

    UNION ALL SELECT 'Desain kemasan dan label', 'photoshop', 'alat', 3
    UNION ALL SELECT 'Desain kemasan dan label', 'illustrator', 'alat', 3
    UNION ALL SELECT 'Desain kemasan dan label', 'coreldraw', 'alat', 3
    UNION ALL SELECT 'Desain kemasan dan label', 'desain produk', 'topik', 2

    UNION ALL SELECT 'Analisa produk 1/2 jadi', 'hplc', 'alat', 3
    UNION ALL SELECT 'Analisa produk 1/2 jadi', 'gc', 'alat', 3
    UNION ALL SELECT 'Analisa produk 1/2 jadi', 'kimia analitik', 'topik', 3
    UNION ALL SELECT 'Analisa produk 1/2 jadi', 'laboratorium', 'topik', 2

    UNION ALL SELECT 'QC produk jadi', 'pengendalian mutu', 'topik', 3
    UNION ALL SELECT 'QC produk jadi', 'teknologi pangan', 'topik', 2
    UNION ALL SELECT 'QC produk jadi', 'teknologi industri', 'topik', 2
    UNION ALL SELECT 'QC produk jadi', 'titrasi', 'alat', 3
    UNION ALL SELECT 'QC produk jadi', 'kimia', 'topik', 2

    UNION ALL SELECT 'QC bahan material dan aksesoris masuk gudang', 'inspeksi material', 'topik', 3
    UNION ALL SELECT 'QC bahan material dan aksesoris masuk gudang', 'teknik industri', 'topik', 2
    UNION ALL SELECT 'QC bahan material dan aksesoris masuk gudang', 'qc', 'topik', 2

    UNION ALL SELECT 'Patrol produk jadi', 'pengawasan mutu', 'topik', 3
    UNION ALL SELECT 'Patrol produk jadi', 'teknik industri', 'topik', 2

    -- Produksi
    UNION ALL SELECT 'Rendal dan perencanaan', 'perencanaan produksi', 'topik', 3
    UNION ALL SELECT 'Rendal dan perencanaan', 'excel', 'alat', 2
    UNION ALL SELECT 'Rendal dan perencanaan', 'erp', 'alat', 2
    UNION ALL SELECT 'Rendal dan perencanaan', 'teknik industri', 'topik', 2

    UNION ALL SELECT 'K3LH', 'k3', 'topik', 3
    UNION ALL SELECT 'K3LH', 'kesehatan lingkungan', 'topik', 2
    UNION ALL SELECT 'K3LH', 'sertifikasi k3', 'topik', 2
    UNION ALL SELECT 'K3LH', 'teknik lingkungan', 'topik', 2

    UNION ALL SELECT 'Pemeliharaan', 'maintenance', 'topik', 3
    UNION ALL SELECT 'Pemeliharaan', 'perawatan mesin', 'topik', 3

    UNION ALL SELECT 'Produksi', 'operasional pabrik', 'topik', 3
    UNION ALL SELECT 'Produksi', 'enjiniring', 'topik', 2
    UNION ALL SELECT 'Produksi', 'proses produksi', 'topik', 3

    -- Komersil
    UNION ALL SELECT 'Penjualan produk', 'pemasaran', 'topik', 3
    UNION ALL SELECT 'Penjualan produk', 'agribisnis', 'topik', 2
    UNION ALL SELECT 'Penjualan produk', 'komunikasi', 'topik', 2

    UNION ALL SELECT 'Penjualan produk pupuk dan chemical', 'penjualan b2b', 'topik', 3
    UNION ALL SELECT 'Penjualan produk pupuk dan chemical', 'pertanian', 'topik', 2
    UNION ALL SELECT 'Penjualan produk pupuk dan chemical', 'kimia', 'topik', 2

    UNION ALL SELECT 'Digital marketing', 'media sosial', 'alat', 3
    UNION ALL SELECT 'Digital marketing', 'analitik web', 'alat', 2
    UNION ALL SELECT 'Digital marketing', 'pemasaran digital', 'topik', 3
    UNION ALL SELECT 'Digital marketing', 'konten', 'topik', 2

    UNION ALL SELECT 'Pengembangan dan promosi', 'riset pasar', 'topik', 3
    UNION ALL SELECT 'Pengembangan dan promosi', 'event', 'topik', 2
    UNION ALL SELECT 'Pengembangan dan promosi', 'relasi mitra', 'topik', 2

    UNION ALL SELECT 'Penagihan', 'akuntansi', 'topik', 3
    UNION ALL SELECT 'Penagihan', 'administrasi piutang', 'topik', 3
    UNION ALL SELECT 'Penagihan', 'keuangan', 'topik', 2

    -- Rantai Pasok
    UNION ALL SELECT 'Pengadaan barang dalam dan luar negeri', 'impor-ekspor', 'topik', 3
    UNION ALL SELECT 'Pengadaan barang dalam dan luar negeri', 'negosiasi', 'topik', 2
    UNION ALL SELECT 'Pengadaan barang dalam dan luar negeri', 'manajemen rantai pasok', 'topik', 3
    UNION ALL SELECT 'Pengadaan barang dalam dan luar negeri', 'logistik', 'topik', 2

    UNION ALL SELECT 'Pengadaan jasa', 'administrasi kontrak', 'topik', 3
    UNION ALL SELECT 'Pengadaan jasa', 'hukum bisnis', 'topik', 2
    UNION ALL SELECT 'Pengadaan jasa', 'negosiasi vendor', 'topik', 3

    UNION ALL SELECT 'Penyimpanan bahan baku', 'manajemen gudang', 'topik', 3
    UNION ALL SELECT 'Penyimpanan bahan baku', 'inventori', 'topik', 2
    UNION ALL SELECT 'Penyimpanan bahan baku', 'penanganan bahan kimia', 'topik', 3
    UNION ALL SELECT 'Penyimpanan bahan baku', 'logistik', 'topik', 2

    UNION ALL SELECT 'Penyimpanan produk jadi', 'manajemen gudang', 'topik', 3
    UNION ALL SELECT 'Penyimpanan produk jadi', 'inventori', 'topik', 2
    UNION ALL SELECT 'Penyimpanan produk jadi', 'teknik industri', 'topik', 2
    UNION ALL SELECT 'Penyimpanan produk jadi', 'logistik', 'topik', 2

    UNION ALL SELECT 'Distribusi produk ke customer', 'transportasi', 'topik', 3
    UNION ALL SELECT 'Distribusi produk ke customer', 'distribusi', 'topik', 3
    UNION ALL SELECT 'Distribusi produk ke customer', 'manajemen operasi', 'topik', 2
    UNION ALL SELECT 'Distribusi produk ke customer', 'logistik', 'topik', 2

    UNION ALL SELECT 'Perencanaan dan pengendalian rantai pasok', 'excel', 'alat', 2
    UNION ALL SELECT 'Perencanaan dan pengendalian rantai pasok', 'erp', 'alat', 2
    UNION ALL SELECT 'Perencanaan dan pengendalian rantai pasok', 'analisis data', 'topik', 3
    UNION ALL SELECT 'Perencanaan dan pengendalian rantai pasok', 'teknik industri', 'topik', 2
    UNION ALL SELECT 'Perencanaan dan pengendalian rantai pasok', 'perencanaan', 'topik', 2

    -- SDM & Tata Kelola
    UNION ALL SELECT 'SDM dan pengembangan kompetensi', 'psikologi', 'topik', 3
    UNION ALL SELECT 'SDM dan pengembangan kompetensi', 'manajemen sdm', 'topik', 3
    UNION ALL SELECT 'SDM dan pengembangan kompetensi', 'administrasi pegawai', 'topik', 2

    UNION ALL SELECT 'Hubungan masyarakat', 'humas', 'topik', 3
    UNION ALL SELECT 'Hubungan masyarakat', 'penulisan', 'topik', 2
    UNION ALL SELECT 'Hubungan masyarakat', 'media', 'topik', 2
    UNION ALL SELECT 'Hubungan masyarakat', 'ilmu komunikasi', 'topik', 3

    UNION ALL SELECT 'Tata kelola perusahaan dan kesekretariatan', 'kearsipan', 'topik', 3
    UNION ALL SELECT 'Tata kelola perusahaan dan kesekretariatan', 'surat-menyurat', 'topik', 2
    UNION ALL SELECT 'Tata kelola perusahaan dan kesekretariatan', 'administrasi', 'topik', 2
    UNION ALL SELECT 'Tata kelola perusahaan dan kesekretariatan', 'manajemen', 'topik', 2

    UNION ALL SELECT 'Hukum dan perizinan perusahaan', 'hukum', 'topik', 3
    UNION ALL SELECT 'Hukum dan perizinan perusahaan', 'administrasi perizinan', 'topik', 3

    UNION ALL SELECT 'Pelayanan umum', 'administrasi', 'topik', 2
    UNION ALL SELECT 'Pelayanan umum', 'layanan operasional kantor', 'topik', 3

    UNION ALL SELECT 'Pengamanan aset', 'manajemen aset', 'topik', 3
    UNION ALL SELECT 'Pengamanan aset', 'keamanan', 'topik', 2
    UNION ALL SELECT 'Pengamanan aset', 'administrasi inventaris', 'topik', 2

    -- Administrasi & Keuangan
    UNION ALL SELECT 'Pengelolaan akuntansi dan anggaran', 'akuntansi', 'topik', 3
    UNION ALL SELECT 'Pengelolaan akuntansi dan anggaran', 'perpajakan', 'topik', 2
    UNION ALL SELECT 'Pengelolaan akuntansi dan anggaran', 'penganggaran', 'topik', 2
    UNION ALL SELECT 'Pengelolaan akuntansi dan anggaran', 'excel', 'alat', 2

    UNION ALL SELECT 'Pengelolaan keuangan perusahaan', 'manajemen keuangan', 'topik', 3
    UNION ALL SELECT 'Pengelolaan keuangan perusahaan', 'kas dan treasuri', 'topik', 2
    UNION ALL SELECT 'Pengelolaan keuangan perusahaan', 'analisis keuangan', 'topik', 3
) v ON v.divisi = d.nama
WHERE NOT EXISTS (
    SELECT 1 FROM prk_sinyal_divisi s
    WHERE s.id_divisi = d.id AND s.kata_kunci = v.kata_kunci AND s.jenis = v.jenis
);

-- ============================================================
-- prk_pemetaan_jurusan (dari bagian "Jurusan dengan peminatan" +
-- kolom jurusan tersirat di sinyal kecocokan tiap divisi). skor 0-100,
-- dasar F1. Baris tanpa peminatan ('') berlaku untuk semua peminatan.
-- ============================================================
INSERT INTO prk_pemetaan_jurusan (jurusan_norm, peminatan, id_divisi, skor)
SELECT v.jurusan_norm, v.peminatan, d.id, v.skor FROM prk_divisi d
JOIN (
    -- Kimia murni: cenderung R&D (Development, Analisa 1/2 jadi); pergeseran via
    -- proposal ke QC produk jadi (dicatat di dokumen sebagai contoh pergeseran)
    SELECT 'kimia' AS jurusan_norm, 'kimia murni' AS peminatan, 'Development produk baru' AS divisi, 85 AS skor
    UNION ALL SELECT 'kimia', 'kimia murni', 'Analisa produk 1/2 jadi', 85
    UNION ALL SELECT 'kimia', 'kimia murni', 'QC produk jadi', 70
    UNION ALL SELECT 'kimia', 'kimia murni', 'Produksi', 50

    -- Kimia industri: cenderung Produksi, QC produk jadi, QC bahan masuk
    UNION ALL SELECT 'kimia', 'kimia industri', 'Produksi', 85
    UNION ALL SELECT 'kimia', 'kimia industri', 'QC produk jadi', 85
    UNION ALL SELECT 'kimia', 'kimia industri', 'QC bahan material dan aksesoris masuk gudang', 80
    UNION ALL SELECT 'kimia', 'kimia industri', 'Development produk baru', 60

    -- Kimia tanpa peminatan terdeteksi (seri/kosong): nilai netral tapi masih kimia
    UNION ALL SELECT 'kimia', '', 'QC produk jadi', 65
    UNION ALL SELECT 'kimia', '', 'Development produk baru', 60
    UNION ALL SELECT 'kimia', '', 'Analisa produk 1/2 jadi', 60
    UNION ALL SELECT 'kimia', '', 'Produksi', 55

    -- Jurusan lain dari kolom sinyal kecocokan tiap divisi (bagian "Struktur departemen")
    UNION ALL SELECT 'teknik kimia industri', '', 'Produksi', 90
    UNION ALL SELECT 'teknik kimia industri', '', 'QC produk jadi', 80
    UNION ALL SELECT 'teknik kimia industri', '', 'K3LH', 55

    UNION ALL SELECT 'teknik industri', '', 'Rendal dan perencanaan', 90
    UNION ALL SELECT 'teknik industri', '', 'QC bahan material dan aksesoris masuk gudang', 80
    UNION ALL SELECT 'teknik industri', '', 'Patrol produk jadi', 80
    UNION ALL SELECT 'teknik industri', '', 'Penyimpanan produk jadi', 70
    UNION ALL SELECT 'teknik industri', '', 'Perencanaan dan pengendalian rantai pasok', 85

    UNION ALL SELECT 'teknik mesin', '', 'Pemeliharaan', 90
    UNION ALL SELECT 'teknik elektro', '', 'Pemeliharaan', 90

    UNION ALL SELECT 'agroteknologi', '', 'Development produk baru', 80
    UNION ALL SELECT 'pertanian', '', 'Penjualan produk pupuk dan chemical', 85
    UNION ALL SELECT 'agribisnis', '', 'Penjualan produk', 85

    UNION ALL SELECT 'hukum', '', 'Perizinan merk dan izin edar', 80
    UNION ALL SELECT 'hukum', '', 'Hukum dan perizinan perusahaan', 90
    UNION ALL SELECT 'hukum', '', 'Pengadaan jasa', 65

    UNION ALL SELECT 'dkv', '', 'Desain kemasan dan label', 90
    UNION ALL SELECT 'desain produk', '', 'Desain kemasan dan label', 85

    UNION ALL SELECT 'ilmu komunikasi', '', 'Hubungan masyarakat', 90
    UNION ALL SELECT 'ilmu komunikasi', '', 'Penjualan produk', 55
    UNION ALL SELECT 'ilmu komunikasi', '', 'Digital marketing', 60

    UNION ALL SELECT 'manajemen', '', 'Penjualan produk', 70
    UNION ALL SELECT 'manajemen', '', 'Pengembangan dan promosi', 70
    UNION ALL SELECT 'manajemen', '', 'Tata kelola perusahaan dan kesekretariatan', 70
    UNION ALL SELECT 'manajemen', '', 'Pengadaan jasa', 70
    UNION ALL SELECT 'manajemen', '', 'Pengamanan aset', 60

    UNION ALL SELECT 'manajemen rantai pasok', '', 'Pengadaan barang dalam dan luar negeri', 90
    UNION ALL SELECT 'logistik', '', 'Penyimpanan bahan baku', 85
    UNION ALL SELECT 'logistik', '', 'Penyimpanan produk jadi', 85
    UNION ALL SELECT 'logistik', '', 'Distribusi produk ke customer', 90

    UNION ALL SELECT 'akuntansi', '', 'Pengelolaan akuntansi dan anggaran', 90
    UNION ALL SELECT 'akuntansi', '', 'Penagihan', 80
    UNION ALL SELECT 'keuangan', '', 'Pengelolaan keuangan perusahaan', 90
    UNION ALL SELECT 'keuangan', '', 'Penagihan', 70

    UNION ALL SELECT 'psikologi', '', 'SDM dan pengembangan kompetensi', 90

    UNION ALL SELECT 'pemasaran digital', '', 'Digital marketing', 90
    UNION ALL SELECT 'pemasaran', '', 'Penjualan produk', 80
    UNION ALL SELECT 'pemasaran', '', 'Pengembangan dan promosi', 80
    UNION ALL SELECT 'pemasaran', '', 'Digital marketing', 75

    UNION ALL SELECT 'teknologi pangan', '', 'QC produk jadi', 85
    UNION ALL SELECT 'teknologi industri', '', 'QC produk jadi', 80
) v ON v.divisi = d.nama
WHERE NOT EXISTS (
    SELECT 1 FROM prk_pemetaan_jurusan p
    WHERE p.jurusan_norm = v.jurusan_norm AND p.peminatan = v.peminatan AND p.id_divisi = d.id
);

-- ============================================================
-- prk_mapel_relevan (pola nama mata kuliah/mapel yang dianggap relevan
-- per divisi, dipakai di faktor F2/S_nilai). Usulan awal berdasarkan
-- jenis pekerjaan tiap divisi, belum ada daftar eksplisit di dokumen
-- sumber -- tandai untuk dikoreksi HR setelah ada data nyata.
-- ============================================================
INSERT INTO prk_mapel_relevan (pola_mapel, id_divisi, bobot)
SELECT v.pola_mapel, d.id, v.bobot FROM prk_divisi d
JOIN (
    SELECT 'kimia analisis' AS pola_mapel, 'Analisa produk 1/2 jadi' AS divisi, 3 AS bobot
    UNION ALL SELECT 'kimia analitik', 'Analisa produk 1/2 jadi', 3
    UNION ALL SELECT 'kimia analisis', 'QC produk jadi', 3
    UNION ALL SELECT 'kimia analitik', 'QC produk jadi', 3
    UNION ALL SELECT 'kimia dasar', 'Development produk baru', 2
    UNION ALL SELECT 'kimia organik', 'Development produk baru', 2
    UNION ALL SELECT 'proses produksi', 'Produksi', 3
    UNION ALL SELECT 'teknik kimia', 'Produksi', 2
    UNION ALL SELECT 'statistik', 'Rendal dan perencanaan', 2
    UNION ALL SELECT 'manajemen operasi', 'Rendal dan perencanaan', 3
    UNION ALL SELECT 'k3', 'K3LH', 3
    UNION ALL SELECT 'mesin', 'Pemeliharaan', 2
    UNION ALL SELECT 'elektro', 'Pemeliharaan', 2
    UNION ALL SELECT 'pemasaran', 'Penjualan produk', 3
    UNION ALL SELECT 'pemasaran', 'Digital marketing', 2
    UNION ALL SELECT 'komunikasi pemasaran', 'Digital marketing', 3
    UNION ALL SELECT 'akuntansi', 'Pengelolaan akuntansi dan anggaran', 3
    UNION ALL SELECT 'perpajakan', 'Pengelolaan akuntansi dan anggaran', 2
    UNION ALL SELECT 'manajemen keuangan', 'Pengelolaan keuangan perusahaan', 3
    UNION ALL SELECT 'manajemen rantai pasok', 'Pengadaan barang dalam dan luar negeri', 3
    UNION ALL SELECT 'manajemen logistik', 'Penyimpanan bahan baku', 3
    UNION ALL SELECT 'manajemen logistik', 'Distribusi produk ke customer', 3
    UNION ALL SELECT 'psikologi industri', 'SDM dan pengembangan kompetensi', 3
    UNION ALL SELECT 'hukum bisnis', 'Hukum dan perizinan perusahaan', 3
    UNION ALL SELECT 'hukum bisnis', 'Pengadaan jasa', 2
) v ON v.divisi = d.nama
WHERE NOT EXISTS (
    SELECT 1 FROM prk_mapel_relevan m
    WHERE m.pola_mapel = v.pola_mapel AND m.id_divisi = d.id
);
