        <!-- DataTales Example -->
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Type User</h6>
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
                                    <th>TYPE USER</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>NO</th>
                                    <th>TYPE USER</th>
                                    <th>ACTION</th>
                                </tr>
                            </tfoot>
                            <tbody>
                        <?php
                            $no = 1;
                            foreach ($type_user as $key => $rowdata) {
                                $id = $rowdata->id;
                            ?>
                                <tr>
                                    <td><?php echo $no?></td>
                                    <td><?php echo $rowdata->type_user?></td>
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
                            <h5 class="modal-title" id="exampleModalLabel">Register Type User</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama Type User</h6>
                                <input type="text" class="form-control form-control-user" id="type_user" name="type_user" placeholder="Nama type user" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-primary" onclick="insert();">Simpan</a>
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
                            <h5 class="modal-title" id="exampleModalLabel">Ubah Type User</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <input type="hidden" name="ub_id" id="ub_id" class="form-control">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama Type User</h6>
                                <input type="text" class="form-control form-control-user" id="ub_type_user" name="ub_type_user" placeholder="Nama Type User" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-primary" onclick="update();">Ubah</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--<div id="check"></div>-->
<script>
//    function newFieldPelanggaran() {
//        $("#pelanggaran_field").modal("show");
//    }
    function insert() {
        var type_user = $('#type_user').val();
        if (type_user != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>type_user/data/insert",
                data: {
                    type_user: type_user
                },
                type: "POST",
                success: function (data) {
                    alert('Data berhasil disimpan');
                    window.location.href = "<?php echo base_url(); ?>type_user/data/list";
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
            url: "<?php echo base_url(); ?>type_user/data/getFieldTypeUser",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var obj = JSON.parse(data);
                $('#UbahModal').modal('show');
//                $('#check').html(data);
                $('#ub_id').val(obj.id);
                $('#ub_type_user').val(obj.type_user);
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    function update() {
        var id = $('#ub_id').val();
        var type_user = $('#ub_type_user').val();
        if (type_user != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>type_user/data/update",
                data: {
                    id: id,
                    type_user: type_user
                },
                type: "POST",
                success: function (data) {
                    alert("Data berhasil diubah");
                    window.location.href = "<?php echo base_url(); ?>type_user/data/list";
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
                url: "<?php echo base_url(); ?>type_user/data/delete",
                data: {
                    id: id,
                },
                type: "POST",
                success: function (data) {
//                    alert("Data berhasil dihapus");
                    window.location.href = "<?php echo base_url(); ?>type_user/data/list";
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