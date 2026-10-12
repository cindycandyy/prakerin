<?php 
    date_default_timezone_set('Asia/Jakarta');
    $tahun = $this->session->userdata("SES_TAHUN");
    $bulan = $this->session->userdata("SES_BULAN");
    $ses_user_id  = $this->session->userdata("SES_LOGIN");
    if(empty($tahun)){
        $tahun = date('Y');
        $this->session->set_userdata('SES_TAHUN', $tahun);
    }
    if(empty($bulan)){
        $bulan = date('m');
        $this->session->set_userdata('SES_BULAN', $bulan);
    }
?>
        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
              <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                            <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <select class="list-group-item" name="tahun" id="tahun" onchange="refresh()">
                                            <option>Pilih periode tahun (data tertampil periode <?php echo $tahun?>)</option>
                                            <option value="<?php echo date('Y')?>">Data yang ditampilkan periode : <?php echo date('Y')?></option>
                                            <option value="<?php echo (date('Y')+1)?>">Data yang ditampilkan periode : <?php echo (date('Y')+1)?></option>
                                            <option value="<?php echo (date('Y')+2)?>">Data yang ditampilkan periode : <?php echo (date('Y')+2)?></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="list-group-item" name="bulan" id="bulan" onchange="refreshBln()">
                                            <option>Pilih periode bulan (data tertampil periode <?php echo $this->model_all_function->num2Bulan($bulan)?>)</option>
                                            <option value="01">Data yang ditampilkan bulan : Januari</option>
                                            <option value="02">Data yang ditampilkan bulan : Februari</option>
                                            <option value="03">Data yang ditampilkan bulan : Maret</option>
                                            <option value="04">Data yang ditampilkan bulan : April</option>
                                            <option value="05">Data yang ditampilkan bulan : Mei</option>
                                            <option value="06">Data yang ditampilkan bulan : Juni</option>
                                            <option value="07">Data yang ditampilkan bulan : Juli</option>
                                            <option value="08">Data yang ditampilkan bulan : Agustus</option>
                                            <option value="09">Data yang ditampilkan bulan : September</option>
                                            <option value="10">Data yang ditampilkan bulan : Oktober</option>
                                            <option value="11">Data yang ditampilkan bulan : November</option>
                                            <option value="12">Data yang ditampilkan bulan : Desember</option>
                                        </select>
                                    </div>
                                </div>
          </div>
          <!-- Content Row -->
          <div class="row">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">JUMLAH KUOTA TERSEDIA</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo ($this->model_depag->getJmlfield(2, "kuota")-$this->model_transaksi->getJmlfield("status", "Accept", $tahun, $bulan))?> Kuota</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">JUMLAH KUOTA TERPAKAI</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $this->model_transaksi->getJmlfield("status", "Accept", $tahun, $bulan)?> Kuota</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">TOTAL KUOTA TEMPAT KERJA PRAKTIK</div>
                      <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                          <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $this->model_depag->getJmlfield(2, "kuota")?> Kuota</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Request</div>
                      <div class="row no-gutters align-items-center">
                        <div class="col-auto">
                          <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $this->model_transaksi->getCountfield("status", "status", "Pending")?> Pendaftar</div>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
              <!-- Project Card Example -->
                <div class="card shadow mb-8">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">DIAGRAM KUOTA KERJA PRAKTIK</h6>
                    </div>
                    <div class="card-body">
                        <?php
                            $no = 1;
                            foreach ($depag as $key => $rowdata) {
                                if($this->model_depag->getKuotaDepag($rowdata->id)!=0){
                                    $prosentase = 0;
                                    //($this->model_depag->getJmlDepag($rowdata->id, $tahun, $bulan)/$this->model_depag->getKuotaDepag($rowdata->id))*100;
                                }else{
                                    $prosentase = 0;
                                }
                                $sisa = $this->model_depag->getKuotaDepag($rowdata->id)-$this->model_depag->getJmlDepag($rowdata->id, $tahun, $bulan);
                                if ($prosentase <50) {
                                    $warna = "success";
                                } else if ($prosentase >50 && $prosentase <80 ) {
                                    $warna = "warning";
                                } else if ($prosentase >80 ) {
                                    $warna = "danger";
                                }
                                if($no<>1){
                        ?>
                        <hr class="sidebar-divider d-none d-md-block">
                        <?php
                            }
                        ?>
                        <h4 class="small font-weight-bold"><?php echo $rowdata->nama_depag?>  <span class="float-right"><?php echo @$this->model_depag->getKuotaDepag($rowdata->id)?> Kuota -> Sisa : <?php echo $sisa?></span></h4>
                        <div class="progress mb-8">
                            <div class="progress-bar bg-<?php echo $warna?>" role="progressbar" style="width: <?php echo $prosentase?>%" aria-valuenow="<?php echo $this->model_depag->getJmlDepag($rowdata->id, $tahun, $bulan)?>" aria-valuemin="0" aria-valuemax="<?php echo $this->model_depag->getKuotaDepag($rowdata->id)?>"></div>
                        </div>
                        <?php
                            $no++;
                            }
                        ?>
                    </div>
                </div>
              <?php
              //if(empty($ses_user_id)){
                  ?>
                <!-- Divider -->
<!--                <hr class="sidebar-divider d-none d-md-block">
              <div class="modal" id="myModal">
				  <div class="modal-dialog">
					<div class="modal-content">

					   Modal Header 
					  <div class="modal-header">
						<h4 class="modal-title">INFORMASI</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					  </div>

					   Modal body 
					  <div class="modal-body">
					<span id='infor'><img width="100%" src="<= base_url();?>gambar/informasi.jpeg"/></span>
					  </div>

					</div>
				  </div>
				</div>-->
                <?php //$this->load->view('tutorial/v_tutorial');?>
            <?php
              //}
              ?>

<script>
  // $(function(){
  // $("#myModal").modal();
		setTimeout(function(){$("#myModal").modal(); }, 500);
		//setTimeout(function(){$("#myModal").modal(); }, 6000);
  // });
    function refresh(){
        var tahun = $('#tahun').val();
        if (tahun != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>home/index",
                data: {
                    tahun: tahun
                },
                type: "POST",
                success: function (data) {
                    window.location.href = "<?php echo base_url(); ?>home/index";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Gagal !!!");
        }
    }
    function refreshBln(){
        var bulan = $('#bulan').val();
        if (tahun != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>home/index",
                data: {
                    bulan: bulan
                },
                type: "POST",
                success: function (data) {
                    window.location.href = "<?php echo base_url(); ?>home/index";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Gagal !!!");
        }
    }
    
</script>