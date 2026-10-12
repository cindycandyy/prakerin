        <!-- DataTales Example -->
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">General Setting</h6>
                </div>
                <div class="card-body">
                    <?php
                    if($this->model_setting->getCountSetting()==0){
                        ?>
                    <a href="#" data-toggle="modal" data-target="#InputModal">
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-primary">Register Setting</button>
                    </a>
                    <?php
                    }
                    echo $this->session->flashdata('pesan');
                        ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>EMAIL SENDER</th>
                                    <th>EMAIL PASSWORD</th>
                                    <th>MANAGER NAME</th>
                                    <th>MAINTENANCE</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>NO</th>
                                    <th>EMAIL SENDER</th>
                                    <th>EMAIL PASSWORD</th>
                                    <th>MANAGER NAME</th>
                                    <th>MAINTENANCE</th>
                                    <th>ACTION</th>
                                </tr>
                            </tfoot>
                            <tbody>
                        <?php
                            $no = 1;
                            foreach ($setting as $key => $rowdata) {
                                $id = $rowdata->id;
                            ?>
                                <tr>
                                    <td><?php echo $no?></td>
                                    <td><?php echo $rowdata->email_send?></td>
                                    <td><?php echo $rowdata->email_pass?></td>
                                    <td><?php echo $rowdata->manager_name?></td>
                                    <td><?php echo $rowdata->maintenance?></td>
                                    <td class='text-center'>
                                        <a href="javascript:void(0)" class="btn btn-info" onclick="edit(<?php echo $id?>)"><i class="fa fa-edit"></i>Ubah</a>
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
                            <h5 class="modal-title" id="exampleModalLabel">Register General Setting</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Email Sender</h6>
                                <input type="text" class="form-control form-control-user" onchange="validateEmail_I()" id="email_send" name="email_send" placeholder="Nama email pengirim" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Email Password</h6>
                                <input type="text" class="form-control form-control-user" id="email_pass" name="email_pass" placeholder="Nama password email" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Manager Name</h6>
                                <input type="text" class="form-control form-control-user" id="manager_name" name="manager_name" placeholder="Nama manager untuk cetak kartu magang" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Maintenance</h6>
                                <select class="list-group-item" name="maintenance" id="maintenance">
                                    <option value="True">True</option>
                                    <option value="False">False</option>
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

            <!-- Insert Modal-->
            <div class="modal fade" id="UbahModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ubah General Setting</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!--<form class="user" method="POST" action="<php echo base_url(); ?>depag/data/insert">-->
                        <div class="modal-body">
                            <input type="hidden" name="ub_id" id="ub_id" class="form-control">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Email Sender</h6>
                                <input type="text" class="form-control form-control-user" onchange="validateEmail_U()" id="ub_email_send" name="ub_email_send" placeholder="Nama email pengirim" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Email Password</h6>
                                <input type="text" class="form-control form-control-user" id="ub_email_pass" name="ub_email_pass" placeholder="Nama password email" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Manager Name</h6>
                                <input type="text" class="form-control form-control-user" id="ub_manager_name" name="ub_manager_name" placeholder="Nama manager untuk cetak kartu magang" required>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Maintenance</h6>
                                <select class="list-group-item" name="ub_maintenance" id="ub_maintenance">
                                    <option value="True">True</option>
                                    <option value="False">False</option>
                                </select>
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
    function insert() {
        var email_send   = $('#email_send').val();
        var email_pass   = $('#email_pass').val();
        var manager_name = $('#manager_name').val();
        var maintenance = $('#maintenance').val();
        if (email_send != "" && email_pass != "" && manager_name != "" && maintenance != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>setting/data/insert",
                data: {
                    email_send: email_send,
                    email_pass: email_pass,
                    manager_name: manager_name,
                    maintenance: maintenance
                },
                type: "POST",
                success: function (data) {
                    alert('Data berhasil disimpan');
                    window.location.href = "<?php echo base_url(); ?>setting/data/list";
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
            url: "<?php echo base_url(); ?>setting/data/getFieldSetting",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var obj = JSON.parse(data);
                $('#UbahModal').modal('show');
//                $('#check').html(data);
                $('#ub_id').val(obj.id);
                $('#ub_email_send').val(obj.email_send);
                $('#ub_email_pass').val(obj.email_pass);
                $('#ub_manager_name').val(obj.manager_name);
                $('#ub_maintenance').val(obj.maintenance);
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    function update() {
        var id = $('#ub_id').val();
        var email_send   = $('#ub_email_send').val();
        var email_pass   = $('#ub_email_pass').val();
        var manager_name = $('#ub_manager_name').val();
        var maintenance = $('#ub_maintenance').val();
        if (email_send != "" && email_pass != "" && manager_name != "" && maintenance != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>setting/data/update",
                data: {
                    id: id,
                    email_send: email_send,
                    email_pass: email_pass,
                    manager_name: manager_name,
                    maintenance: maintenance
                },
                type: "POST",
                success: function (data) {
                    alert("Data berhasil diubah");
                    window.location.href = "<?php echo base_url(); ?>setting/data/list";
                },
                error: function () {
                    alert("Data gagal disimpan !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function validateEmail_I() {
        var i_x       = $('#email_send').val();
        var i_atpos   = i_x.indexOf("@");
        var i_dotpos  = i_x.lastIndexOf(".");
        
        if (i_atpos<1 || i_dotpos<i_atpos+2 || i_dotpos+2>=i_x.length) {
            alert("Mohon isikan email dengan benar !");
        }
        
    }
    function validateEmail_U() {
        var i_x       = $('#ub_email_send').val();
        var i_atpos   = i_x.indexOf("@");
        var i_dotpos  = i_x.lastIndexOf(".");
        
        if (i_atpos<1 || i_dotpos<i_atpos+2 || i_dotpos+2>=i_x.length) {
            alert("Mohon isikan email dengan benar !");
        }
        
    }
</script>