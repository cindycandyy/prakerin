<?php
    $auth    = $this->session->userdata("SES_PRIV");
        $ses_id  = $this->session->userdata("SES_ID");
    ?>
        <!-- DataTales Example -->
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data User <?php echo $ses_id?></h6>
                </div>
                <div class="card-body">
                    <?php
                        if($auth == 1 || $auth == 2){
                    ?>
                    <a href="#" data-toggle="modal" data-target="#InputModal">
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-primary">Register Baru</button>
                    </a>
                    <?php
                        }
                    ?>
                    <?php echo $this->session->flashdata('pesan');?>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>USER ID</th>
                                    <th>NAMA</th>
                                    <th>PASSWORD</th>
                                    <th>ALAMAT EMAIL</th>
                                    <th>TYPE USER</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <?php
                            if($auth == 1 || $auth == 2){
                            ?>
                            <tfoot>
                                <tr>
                                    <th>NO</th>
                                    <th>USER ID</th>
                                    <th>NAMA</th>
                                    <th>PASSWORD</th>
                                    <th>ALAMAT EMAIL</th>
                                    <th>TYPE USER</th>
                                    <th>ACTION</th>
                                </tr>
                            </tfoot>
                            <?php
                            }
                            ?>
                            <tbody>
                        <?php
                            $no = 1;
                            foreach ($data as $key => $rowdata) {
                            ?>
                                <tr>
                                    <td><?php echo $no?></td>
                                    <td><?php echo $rowdata->user_id?></td>
                                    <td><?php echo $rowdata->name?></td>
                                    <td><?php echo $rowdata->password?></td>
                                    <td><?php echo $rowdata->email?></td>
                                    <td><?php echo $this->model_all_function->getTypeUser($rowdata->type)?></td>
                                    <td class='text-center'>
                                        <a href="javascript:void(0)" class="btn btn-info" onclick="editPass(<?php echo $rowdata->id?>)"><i class="fa fa-edit"></i> Ubah Password</a>
                                        <a href="javascript:void(0)" class="btn btn-warning" onclick="edit(<?php echo $rowdata->id?>)"><i class="fa fa-edit"></i> Ubah Data</a>
                                        <a href="javascript:void(0)" class="btn btn-secondary" onclick="editProfil(<?php echo $rowdata->id?>)"><i class="fa fa-image"></i> Ubah Profil</a>
                                        <a href="javascript:void(0)" class="btn btn-danger" onclick="hapus(<?php echo $rowdata->id?>)"><i class="fa fa-trash"></i> Hapus</a>
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

            <!--Ubah Modal-->
            <div class="modal fade" id="PassModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ubah Password User</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <input type="hidden" name="p_id" id="p_id" class="form-control form-control-user">
                            <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Password</h6>
                                        <input type="password" class="form-control form-control-user" id="p_password" name="p_password" placeholder="Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">Ketik Ulang Password</h6>
                                        <input type="password" class="form-control form-control-user" onchange="cekPass()" id="p_re_password" name="p_re_password" placeholder="Password">
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-primary" onclick="updatePassword();">Simpan</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            
            <!--Ubah Modal-->
            <div class="modal fade" id="UbahProfilModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ubah Profil User</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <input type="hidden" name="fo_id" id="fo_id" class="form-control form-control-user">
                            <div class="form-group" id="perluHilangSaja">
                                <h6 class="modal-title" id="exampleModalLabel">Preview</h6>
                                <div style="align-content: center;">
                                    <div style="width:100%; height:100%" id='vimage'></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Preview</h6>
                                        <div style="align-content: center;">
                                            <div style="width:100%; height:100%" id='preview'></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">URL</h6>
                                        <div style="align-content: center;">
                                            <div id='url' name="url"></div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group row" id="perluHilangSekalian">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Select file</h6>
                                        <form method='post' action='' enctype="multipart/form-data">
                                            <input type='file' name='file' id='file' class='form-control' >
                                            
                                            <a href="javascript:void(0)" class='btn btn-info' onclick="upload()" id='upload'>&nbsp;Upload</a>
                                        </form>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-default hvr-bounce-in" onclick="unlinkEdit();" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-success hvr-bounce-in" onclick="updateProfil();"><i class="fa fa-save"></i>&nbsp;Simpan&nbsp;&nbsp;</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>

            <!--Insert Modal-->
            <div class="modal fade" id="InputModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Register User</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama User</h6>
                                <input type="text" class="form-control form-control-user" id="i_name" name="i_name" placeholder="Nama User" required>
                            </div>
                            <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Password</h6>
                                        <input type="password" class="form-control form-control-user" id="i_password" name="i_password" placeholder="Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">Ketik Ulang Password</h6>
                                        <input type="password" class="form-control form-control-user" onchange="cekPass()" id="i_re_password" name="i_re_password" placeholder="Password">
                                    </div>
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Email</h6>
                                <input type="text" class="form-control form-control-user" onchange="validateEmail_I()" id="i_email" name="i_email" placeholder="Alamat Email">
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Type User</h6>
                                <select class="list-group-item" name="i_type" id="i_type">
                                    <?php
                                    foreach ($type as $key => $rowdata) {
                                        echo "<option value='" . $rowdata->id . "'>" . $rowdata->type_user . "</option>";
                                    }
                                    ?>

                                </select>
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

            <!-- Ubah Modal-->
            <div class="modal fade" id="UbahModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ubah Data User</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <input type="hidden" name="ub_id" id="ub_id" class="form-control">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">User ID</h6>
                                <input type="text" class="form-control form-control-user" id="ub_user_id" name="ub_user_id" placeholder="ID User" readonly="readonly">
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama User</h6>
                                <input type="text" class="form-control form-control-user" id="ub_name" name="ub_name" placeholder="Nama User" required>
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Email</h6>
                                <input type="text" class="form-control form-control-user" onchange="validateEmail_U()" id="ub_email" name="ub_email" placeholder="Alamat Email">
                            </div>
                            <?php
                                if($auth == 1 || $auth == 2){
                                ?>
                                <div class="form-group">
                                    <h6 class="modal-title" id="exampleModalLabel">Type User</h6>
                                    <select class="list-group-item" name="ub_type" id="ub_type">
                                        <?php
                                        foreach ($type as $key => $rowdata) {
                                            echo "<option value='" . $rowdata->id . "'>" . $rowdata->type_user . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php
				}else{
				?>
				<input type="hidden" name="ub_type" id="ub_type" class="form-control">
                            <?php
				}
                                ?>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-primary" onclick="update();">Ubah</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <div id="check"></div>
<script>
    function insert() {
        var name = $('#i_name').val();
        var password = $('#i_password').val();
        var email = $('#i_email').val();
        var type_user = $('#i_type').val();
        if (name != "" && password != "" && email != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>user/data/insert",
                data: {
                    name: name,
                    password: password,
                    email: email,
                    type_user: type_user
                },
                type: "POST",
                success: function (data) {
                    alert('Data berhasil disimpan.');
                    window.location.href = "<?php echo base_url(); ?>user/data/list";
                },
                error: function (data) {
                    alert('Salah');
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function validateEmail_U() {
        var u_x       = $('#u_email').val();
        var u_atpos   = u_x.indexOf("@");
        var u_dotpos  = u_x.lastIndexOf(".");
        
        if (u_atpos<1 || u_dotpos<u_atpos+2 || u_dotpos+2>=u_x.length) {
            alert("Mohon isikan email dengan benar !");
        }
        
    }
    function validateEmail_I() {
        var i_x       = $('#i_email').val();
        var i_atpos   = i_x.indexOf("@");
        var i_dotpos  = i_x.lastIndexOf(".");
        
        if (i_atpos<1 || i_dotpos<i_atpos+2 || i_dotpos+2>=i_x.length) {
            alert("Mohon isikan email dengan benar !");
        }
        
    }
    function cekPass() {
        var password1 = $('#i_password').val();
        var password2 = $('#i_re_password').val();
        var ppassword1 = $('#p_password').val();
        var ppassword2 = $('#p_re_password').val();
        if (password1 != password2) {
            alert("Password tidak sama, silahkan ketik ulang password anda.");
        }else if (ppassword1 != ppassword2) {
            alert("Password tidak sama, silahkan ketik ulang password anda.");
        }
    }
    function edit(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>user/data/getDataUser",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahModal').modal('show');
//                $('#check').html(data);
                $('#ub_id').val(isi.id);
                $('#ub_user_id').val(isi.user_id);
                $('#ub_name').val(isi.name);
                $('#ub_email').val(isi.email);
                $('#ub_type').val(isi.type);
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    function editProfil(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>user/data/getDataUser",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahProfilModal').modal('show');
                var urlfoto = "<?php echo base_url(); ?>" + 'gambar/';
                $('#check').html(data);
                $('#fo_id').val(isi.id);
                $('#vimage').html("<img src='" + urlfoto + isi.foto + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    function editPass(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>user/data/getDataUser",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var obj = JSON.parse(data);
                $('#PassModal').modal('show');
                $('#p_id').val(obj.id);
                $('#p_password').val(obj.password);
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    function update() {
        var id      = $('#ub_id').val();
        var name    = $('#ub_name').val();
        var email   = $('#ub_email').val();
        var type    = $('#ub_type').val();
        if (id != "" && name != "" && email != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>user/data/update",
                data: {
                    id: id,
                    name: name,
                    type: type,
                    email: email
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>user/data/list";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function updatePassword() {
        var id          = $('#p_id').val();
        var password    = $('#p_password').val();
        if (id != "" && password != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>user/data/updatePass",
                data: {
                    id: id,
                    password: password,
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>user/data/list";
                },
                error: function () {
                    alert("GAGAL !!!");
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
                url: "<?php echo base_url(); ?>user/data/delete",
                data: {
                    id: id,
                },
                type: "POST",
                success: function (data) {
//                    alert("Data berhasil dihapus !");
                    window.location.href = "<?php echo base_url(); ?>user/data/list";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            return false;
        }
    }
    function updateProfil() {
        var id      = $('#fo_id').val();
        var url    = document.getElementById("url").textContent;
        if (id != "" && url != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>user/data/updateProfil",
                data: {
                    id: id,
                    url: url
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>user/data/list";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function unlinkEdit() {
        var url = document.getElementById("url").textContent;
        $.ajax({
            url: "<?php echo base_url(); ?>user/data/unlink",
            data: {
                url: url,
            },
            type: "POST",
            success: function (data) {
                window.location.href = "<?php echo base_url(); ?>user/data/list";
            },
            error: function () {
                alert("GAGAL !!!");
            }
        });
    }
    function upload() {
            var fd = new FormData();
            var files = $('#file')[0].files[0];
            fd.append('file', files);
            var url = "<?php echo base_url(); ?>" + 'gambar/';
            if (files) {
                $('#check').html(files);
                $.ajax({
                    url: "<?php echo base_url(); ?>user/data/uploadGambarEdit",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response != 0) {
                            // Show image preview
                            $('#preview').append("<img src='" + url + response + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
                            $('#url').append(response);
                            document.getElementById("perluHilangSaja").style.display = 'none';
                            document.getElementById("perluHilangSekalian").style.display = 'none';
                        } else {
                            alert('file not uploaded');
                        }
                    }
                });
            } else {
                alert("Pilih Gambar !!!");
            }
    }
</script>