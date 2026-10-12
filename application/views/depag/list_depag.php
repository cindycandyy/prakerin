        <!-- DataTales Example -->
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Departemen / Bagian</h6>
                </div>
                <div class="card-body">
                    <a href="#" data-toggle="modal" data-target="#InputModal">
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-primary">Register Baru</button>
                    </a>
                    <?php echo $this->session->flashdata('pesan');?>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA BAGIAN/DEPARTEMEN</th>
                                <th>KUOTA</th>
                                <th>DESKRIPSI</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA BAGIAN/DEPARTEMEN</th>
                                    <th>KUOTA</th>
                                <th>DESKRIPSI</th>
                                    <th>ACTION</th>
                                </tr>
                            </tfoot>
                            <tbody>
                        <?php
                            $no = 1;
                            foreach ($depag as $key => $rowdata) {
                                $id = $rowdata->id;
                            ?><tr>
                                    <td><?php echo $no?></td>
                                    <td><?php echo $rowdata->nama_depag?></td>
                                    <td><?php echo  $rowdata->kuota?></td>
                                    <td><?php echo  $rowdata->deskripsi?></td>
                                    <td class='text-center'>
                                        <a href="javascript:void(0)" class="btn btn-info" onclick="edit(<?php echo $id?>)"><i class="fa fa-edit"></i>Ubah</a>
                                        <a href="javascript:void(0)" class="btn btn-info" onclick="hapus(<?php echo $id?>)"><i class="fa fa-trash"></i>Hapus</a>
                                    </td>
                        <?php
                            $no++;
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

             <!--Insert Modal-->
            <div class="modal fade" id="InputModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Register Departemen / Bagian</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama Departemen</h6>
                                <input type="text" class="form-control form-control-user" id="nama_depag" name="nama_depag" placeholder="Nama Departemen / Bagian" required>
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Jumlah Kuota</h6>
                                <input type="text" class="form-control form-control-user" id="kuota" name="kuota" placeholder="Kuota Departemen / Bagian">
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Deskripsi</h6>
                                <textarea class="form-control form-control-user" id="deskripsi" name="deskripsi"></textarea>
                            </div>
                               </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-secondary btn-block" data-dismiss="modal">Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-block" onclick="insert();">Simpan</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>

            <!-- Insert Modal-->
            <div class="modal fade" id="UbahModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ubah Data Departemen / Bagian</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <input type="hidden" name="ub_id" id="ub_id" class="form-control">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama Departemen</h6>
                                <input type="text" class="form-control form-control-user" id="ub_nama_depag" name="ub_nama_depag" placeholder="Nama Departemen / Bagian" required>
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Jumlah Kuota</h6>
                                <input type="text" class="form-control form-control-user" id="ub_kuota" name="ub_kuota" placeholder="Kuota Departemen / Bagian">
                            </div><div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Deskripsi</h6>
                                <textarea class="form-control form-control-user" id="ub_deskripsi" name="ub_deskripsi" ></textarea>
                            </div>
                              </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-secondary btn-block" data-dismiss="modal">Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-block" onclick="update();">Ubah</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--<div id="check"></div>-->
<script>
    function insert() {
        var nama_depag = $('#nama_depag').val();
        var kuota = $('#kuota').val();
        var deskripsi = $('#deskripsi').val();
        if (nama_depag != "" && kuota != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>depag/data/insert",
                data: {
                    nama_depag: nama_depag,
                    kuota: kuota,
                    deskripsi: deskripsi
                },
                type: "POST",
                success: function (data) {
                    alert('Data berhasil disimpan');
                    window.location.href = "<?php echo base_url(); ?>depag/data/list";
                },
                error: function (data) {
                    alert('Data gagal disimpan');
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function edit(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>depag/data/getFieldDepag",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var obj = JSON.parse(data);
                $('#UbahModal').modal('show');
               $('#ub_id').val(obj.id);
                $('#ub_nama_depag').val(obj.nama_depag);
                $('#ub_kuota').val(obj.kuota);
                $('#ub_deskripsi').val(obj.deskripsi);
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    function update() {
        var id = $('#ub_id').val();
        var nama_depag = $('#ub_nama_depag').val();
        var kuota = $('#ub_kuota').val();
        var deskripsi = $('#ub_deskripsi').val();
        if (nama_depag != "" && kuota != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>depag/data/update",
                data: {
                    id: id,
                    nama_depag: nama_depag,
                    kuota: kuota,
                    deskripsi: deskripsi
                },
                type: "POST",
                success: function (data) {
                    alert("Data berhasil diubah");
                    window.location.href = "<?php echo base_url(); ?>depag/data/list";
                },
                error: function () {
                    alert("Data gagal disimpan !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function hapus(value) {
        var conf = confirm('Apakah Anda Yakin Ingin Menghapus Data Ini?');
        if (conf) {
            var id = value;
            $.ajax({
                url: "<?php echo base_url(); ?>depag/data/delete",
                data: {
                    id: id,
                },
                type: "POST",
                success: function (data) {
                    window.location.href = "<?php echo base_url(); ?>depag/data/list";
                },
                error: function () {
                    alert("Data gagal dihapus !!!");
                }
            });
        } else {
            return false;
        }
    }
</script>