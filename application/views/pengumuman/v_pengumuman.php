        <!-- DataTales Example -->
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Persyaratan Khusus</h6>
                </div>
                <div class="card-body">
                    <a href="#" data-toggle="modal" data-target="#InputModal">
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-primary">Register Persyaratan</button>
                    </a>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA BAGIAN/DEPARTEMEN</th>
                                <th>SYARAT</th>
                                <th>ACTION</th>
                            </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>NO</th>
                                    <th>NAMA BAGIAN/DEPARTEMEN</th>
                                    <th>SYARAT</th>
                                    <th>ACTION</th>
                                </tr>
                            </tfoot>
                            <tbody>
                        <?php
                            $no = 1;
                            foreach ($data as $key => $rowdata) {
                                $id = $rowdata->id;
                            ?>
                                <tr>
                                    <td><?php echo $no?></td>
                                    <td><?php echo $this->model_depag->getFieldDepag($rowdata->id_depag)?></td>
                                    <td><?php echo $rowdata->syarat?></td>
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
                            <h5 class="modal-title" id="exampleModalLabel">Register Persyaratan</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Departemen / Bagian yang dipilih</h6>
                                <select class="list-group-item" name="id_depag" id="id_depag">
                                    <?php
                                    foreach ($depag as $key => $rowdata) {
                                        echo "<option value='" . $rowdata->id . "'>" . $rowdata->nama_depag . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Isi Persyaratan</h6>
                                <textarea type="text" class="form-control form-control-user" id="syarat" name="syarat" placeholder="Isi Persyaratan"></textarea>
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
                            <h5 class="modal-title" id="exampleModalLabel">Ubah Data Persyaratan</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <input type="hidden" name="ub_id" id="ub_id" class="form-control">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Departemen / Bagian yang dipilih</h6>
                                <select class="list-group-item" name="ub_id_depag" id="ub_id_depag">
                                    <?php
                                    foreach ($depag as $key => $rowdata) {
                                        echo "<option value='" . $rowdata->id . "'>" . $rowdata->nama_depag . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Isi Persyaratan</h6>
                                <textarea type="text" class="form-control form-control-user" id="ub_syarat" name="ub_syarat" placeholder="Isi Persyaratan"></textarea>
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
        var id_depag = $('#id_depag').val();
        var syarat = $('#syarat').val();
        if (id_depag != "" && syarat != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>persyaratan/data/insert",
                data: {
                    id_depag: id_depag,
                    syarat: syarat
                },
                type: "POST",
                success: function (data) {
                    alert('Data berhasil disimpan');
                    window.location.href = "<?php echo base_url(); ?>persyaratan/data/list";
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
            url: "<?php echo base_url(); ?>persyaratan/data/getDataPersyaratan",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var obj = JSON.parse(data);
                $('#UbahModal').modal('show');
//                $('#check').html(data);
                $('#ub_id').val(obj.id);
                $('#ub_id_depag').val(obj.id_depag);
                $('#ub_syarat').val(obj.syarat);
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    function update() {
        var id = $('#ub_id').val();
        var id_depag = $('#ub_id_depag').val();
        var syarat = $('#ub_syarat').val();
        if (id_depag != "" && syarat != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>persyaratan/data/update",
                data: {
                    id: id,
                    id_depag: id_depag,
                    syarat: syarat
                },
                type: "POST",
                success: function (data) {
                    alert("Data berhasil diubah");
                    window.location.href = "<?php echo base_url(); ?>persyaratan/data/list";
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
                url: "<?php echo base_url(); ?>persyaratan/data/delete",
                data: {
                    id: id,
                },
                type: "POST",
                success: function (data) {
                    alert("Data berhasil dihapus");
                    window.location.href = "<?php echo base_url(); ?>persyaratan/data/list";
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