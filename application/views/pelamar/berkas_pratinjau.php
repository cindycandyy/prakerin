<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($nama_label); ?> - E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <style>
        body { margin: 0; background: var(--abu-bg); height: 100vh; display: flex; flex-direction: column; }
        .pratinjau-bar {
            background: #fff; border-bottom: 1px solid var(--abu-200); padding: 14px 20px;
            display: flex; align-items: center; gap: 14px; flex-shrink: 0;
        }
        .pratinjau-kembali {
            display: flex; align-items: center; gap: 6px; color: var(--hijau-700); font-size: 13px;
            font-weight: 600; text-decoration: none; padding: 8px 12px; border-radius: 8px;
        }
        .pratinjau-kembali:hover { background: var(--hijau-100); }
        .pratinjau-judul { font-size: 14px; font-weight: 600; color: var(--abu-900); }
        .pratinjau-isi { flex: 1; min-height: 0; }
        .pratinjau-isi iframe { width: 100%; height: 100%; border: none; }
        .pratinjau-isi img { max-width: 100%; max-height: 100%; display: block; margin: 20px auto; object-fit: contain; }
        .pratinjau-isi.gambar { display: flex; align-items: center; justify-content: center; overflow: auto; }
    </style>
</head>
<body>
    <div class="pratinjau-bar">
        <a class="pratinjau-kembali" href="<?php echo base_url(); ?>pelamar/pengajuan/<?php echo $id_pengajuan; ?>">
            <?php icon_analisis('arrow-left', 16); ?> Kembali ke E-Prakerin
        </a>
        <span class="pratinjau-judul"><?php echo htmlspecialchars($nama_label); ?></span>
    </div>
    <div class="pratinjau-isi<?php echo $is_gambar ? ' gambar' : ''; ?>">
        <?php if ($is_gambar) { ?>
        <img src="<?php echo htmlspecialchars($url_file); ?>" alt="<?php echo htmlspecialchars($nama_label); ?>">
        <?php } else { ?>
        <iframe src="<?php echo htmlspecialchars($url_file); ?>" title="<?php echo htmlspecialchars($nama_label); ?>"></iframe>
        <?php } ?>
    </div>
</body>
</html>
