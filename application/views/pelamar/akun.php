<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun saya - E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-form.css" rel="stylesheet">
    <?php include APPPATH . 'views/pelamar/_style.php'; ?>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'akun'; include APPPATH . 'views/pelamar/_sidebar.php'; ?>
    <div class="main">
        <?php $breadcrumb_akhir = 'Akun saya'; include APPPATH . 'views/pelamar/_topbar.php'; ?>
        <main class="content">
            <div class="page-head"><div><h1>Akun Saya</h1><p>Profil dan keamanan akun.</p></div></div>

            <?php
            $CI = &get_instance();
            $pesan_sukses = $CI->session->flashdata('sukses');
            $pesan_error = $CI->session->flashdata('error');
            $CI->session->unset_userdata('sukses');
            $CI->session->unset_userdata('error');
            if ($pesan_sukses) { ?>
            <div style="background:var(--hijau-100); color:var(--hijau-800); border-radius:10px; padding:12px 14px; font-size:12px;"><?php echo htmlspecialchars($pesan_sukses); ?></div>
            <?php } if ($pesan_error) { ?>
            <div style="background:var(--merah-100); color:var(--merah-700); border-radius:10px; padding:12px 14px; font-size:12px;"><?php echo htmlspecialchars($pesan_error); ?></div>
            <?php } ?>

            <div class="two-col">
                <div class="panel">
                    <div class="panel-head"><h2>Profil</h2></div>
                    <div class="form-grid">
                        <div><span class="field-note">Nama</span><div><?php echo htmlspecialchars($user->name); ?></div></div>
                        <div><span class="field-note">Email</span><div><?php echo htmlspecialchars($user->email); ?></div></div>
                        <div><span class="field-note">ID pengguna</span><div><?php echo htmlspecialchars($user->user_id); ?></div></div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-head"><h2>Ubah Kata Sandi</h2></div>
                    <form method="post" action="<?php echo base_url(); ?>pelamar/akun/ganti_password">
                        <label class="field-label">Kata Sandi Lama</label>
                        <input class="field-input" type="password" name="password_lama" required style="margin-bottom:12px;">
                        <label class="field-label">Kata Sandi Baru (minimal 8 karakter)</label>
                        <input class="field-input" type="password" name="password_baru" minlength="8" required style="margin-bottom:12px;">
                        <button type="submit" class="btn-primary">Simpan Kata Sandi Baru</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/analisis-form.js"></script>
</body>
</html>
