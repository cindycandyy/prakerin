        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Kuota Divisi</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Departemen</th>
                                <th>Divisi</th>
                                <th>Kuota</th>
                                <th>Simpan</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($divisi as $d) { ?>
                            <tr>
                                <td><?php echo $d->nama_departemen; ?></td>
                                <td><?php echo $d->nama; ?></td>
                                <td>
                                    <input type="number" min="0" max="99" class="form-control kuota-input"
                                           data-id="<?php echo $d->id; ?>" value="<?php echo (int) $d->kuota_maks; ?>">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary btn-simpan-kuota"
                                            data-id="<?php echo $d->id; ?>">Simpan</button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('click', function (e) {
            if (!e.target.classList.contains('btn-simpan-kuota')) return;
            var id = e.target.getAttribute('data-id');
            var input = document.querySelector('.kuota-input[data-id="' + id + '"]');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo base_url(); ?>analisis/kuota_simpan', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert('Kuota tersimpan.');
                } else {
                    alert('Gagal menyimpan kuota.');
                }
            };
            xhr.send('id_divisi=' + encodeURIComponent(id) + '&kuota_maks=' + encodeURIComponent(input.value));
        });
        </script>
