<?php 
    date_default_timezone_set('Asia/Jakarta');
?>
<!-- DataTales Example -->
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Regristrasi PKL</h6>
                </div>
                <div class="card-body">
                    
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama</h6>
                                <input type="text" class="form-control form-control-user" id="nama" name="nama" placeholder="Nama pendaftar" required>
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Alamat</h6>
                                <input type="text" class="form-control form-control-user" id="alamat" name="alamat" placeholder="Alamat tempat tinggal" required>
                            </div>
                            <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">RT</h6>
                                        <div style="align-content: center;">
                                            <input type="text" class="form-control form-control-user" id="rt" name="rt" placeholder="Nomor RT" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">RW</h6>
                                        <div style="align-content: center;">
                                            <input type="text" class="form-control form-control-user" id="rw" name="rw" placeholder="Nomor RW" required>
                                        </div>
                                    </div>
                            </div>
                            </div>
                            <div class="modal-footer">
                                <a href="javascript:void(0)" class="btn btn-primary" onclick="sendEmail();">Unduh Laporan</a>
                            </div>
                </div>
            </div>
            <div id="check"></div>
<script>
    
    function sendEmail(){
        var tahun = $('#tahun').val();
        if (tahun != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>email/data/isi",
                data: {
                    tahun: tahun
                },
                type: "POST",
                success: function (data) {
//                    alert('Data berhasil proses.');
//                    $('#check').html(data); 
                    window.location.href = "<?php echo base_url(); ?>email/data/cetak";
                },
                error: function (data) {
//                    $('#check').html(tahun);
                    alert('Data gagal diproses !!');
                }
            });
        } else {
            alert("Tahun belum dipilih !!!");
        }
    }
    
</script>