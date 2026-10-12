        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pengaturan Analisis Penempatan</h6>
            </div>
            <div class="card-body">
                <?php echo $this->session->flashdata('sukses'); ?>
                <?php echo $this->session->flashdata('error'); ?>
                <form method="post" action="<?php echo base_url(); ?>analisis/pengaturan_simpan">
                    <div class="form-group">
                        <label>Bobot Jurusan (F1)</label>
                        <input type="number" name="bobot_f1" class="form-control" value="<?php echo $pengaturan['bobot_f1']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label>Bobot Skill (F2)</label>
                        <input type="number" name="bobot_f2" class="form-control" value="<?php echo $pengaturan['bobot_f2']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label>Bobot Proposal (F3)</label>
                        <input type="number" name="bobot_f3" class="form-control" value="<?php echo $pengaturan['bobot_f3']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                    </div>
                    <div class="form-group">
                        <label>Bobot Kebutuhan Divisi (F4)</label>
                        <input type="number" name="bobot_f4" class="form-control" value="<?php echo $pengaturan['bobot_f4']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                    </div>
                    <p class="text-muted">Jumlah keempat bobot harus 100.</p>
                    <?php if (!$hanya_lihat) { ?>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <?php } ?>
                </form>
            </div>
        </div>
