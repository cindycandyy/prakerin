<?php 
    date_default_timezone_set('Asia/Jakarta');
//    $tahun = $this->session->userdata("SES_TH");
?>
<!-- DataTales Example -->
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cetak Laporan</h6>
                </div>
                <div class="card-body">
                    
                            <div class="form-group">
                                <select class="list-group-item" name="tahun" id="tahun">
                                    <option>Pilih periode tahun</option>
                                    <option value="<?php echo date('Y')?>">Cetak data magang periode : <?php echo date('Y')-1?></option>
                                    <option value="<?php echo date('Y')?>">Cetak data magang periode : <?php echo date('Y')?></option>
                                    <option value="<?php echo (date('Y')+1)?>">Cetak data magang periode : <?php echo (date('Y')+1)?></option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <a href="javascript:void(0)" class="btn btn-primary" onclick="report();">Unduh Laporan</a>
                            </div>
                </div>
            </div>
            <div id="check"></div>
<script>
    
    function report(){
        var tahun = $('#tahun').val();
        if (tahun != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>laporan/data/isi",
                data: {
                    tahun: tahun
                },
                type: "POST",
                success: function (data) {
//                    alert('Data berhasil proses.');
//                    $('#check').html(data); 
                    window.location.href = "<?php echo base_url(); ?>laporan/data/cetak";
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