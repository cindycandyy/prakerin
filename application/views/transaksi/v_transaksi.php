<?php 
    date_default_timezone_set('Asia/Jakarta');
    $auth    = $this->session->userdata("SES_PRIV");
    $ses_user_id  = $this->session->userdata("SES_LOGIN");
    ?>
        <!-- DataTales Example -->
            <div class="card shadow mb-4">
                
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Registrasi</h6>
                </div>
                <div class="card-body">
                    <a href="#" data-toggle="modal" data-target="#InputModal">
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-primary btn-sm"><em class='fa fa-plus'></em> Daftar</button>
                    </a>
					<?php
					if($auth==1 || $auth==2 ){
					?>
					<a href="<?php echo base_url()?>transaksi/data/list_pending" >
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-warning btn-sm btn-sm">Pending <span class='badge'></span></button>
						</a>
					<a href="<?php echo base_url()?>transaksi/data/list_accept" >
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-success btn-sm">Disetujui</button>
                    </a>
					<!--<a href="<?php echo base_url()?>transaksi/data/list" >
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-primary btn-sm">All Status</button>
                    </a>-->
					<a href="<?php echo base_url()?>transaksi/data/list_reject" >
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-danger btn-sm">Ditolak</button>
                    </a>
					<a href="<?php echo base_url()?>transaksi/data/list_inactive" >
                        <button style="margin-bottom: 15px" type="button"  class="btn btn-info btn-sm">Tidak Aktif</button>
                    </a>
                    <?php }?><br/>
					<em style='color:red;'>Ket : Cek kembali upload dokumen persyaratan</em>
                    <div class="table-responsive">
                            <!--<php echo print_r($data);?>-->
                        <table class="table table-bordered table-responsive" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>STATUS</th>
                                    <th>KEGIATAN</th>
                                    <th>NAMA PENDAFTAR</th>
                                    <th>PENDIDIKAN / JURUSAN</th>
                                    <th>INSTANSI</th>
                                    <th>DEPT. / BAGIAN</th>
                                    <th>TGL MULAI</th>
                                    <th>TGL SELESAI</th>
                                    <th>SURAT PENGANTAR</th>
                                    <th>PROPOSAL</th>
                                    <th>TRANSKRIP NILAI</th>
									 <th>FOTO</th>
                                    <th>KARTU ASURANSI KESEHATAN</th>
                                    <th>KARTU PELAJAR / MAHASISWA</th>
                                    <th>SURAT PERNYATAAN</th>
                                    <th>SURAT BALASAN</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <?php
                            if($auth == 1 || $auth == 2){
                            }
                            ?>
                            <tbody>
                        <?php
                            $no = 1;
                            foreach ($data as $key => $rowdata) {
                            ?>
                                <tr>
                                    <td><?php echo $no?></td><td><?php echo $rowdata->status;
                                        if($auth == 1 || $auth == 2){    
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-sm" title="Edit Status" onclick="editStatus('<?php echo $rowdata->id?>');"><i class="fa fa-edit"></i></a></td>
                                        <?php
                                        }
                                        ?>
                                    <td><?php echo $rowdata->kegiatan?></td>
                                   
                                    </td>
                                    <td><?php echo $rowdata->nama?></td>
                                   <!-- <td><?php echo $rowdata->alamat." RT : ".$rowdata->rt." RW : ".$rowdata->rw." Kelurahan : ".$rowdata->kelurahan." Kec. : ".$rowdata->kecamatan." Kab. : ".$rowdata->kabupaten?></td>
                                    <td><?php echo $rowdata->telp." / ".$rowdata->email?></td>--> <td align="center"><?php echo $rowdata->pendidikan?> / <?php echo $rowdata->jurusan?></td>
                                            <td><?php echo $rowdata->institusi?></td>
                                    <td><?php echo $this->model_depag->getFieldDepag($rowdata->id_depag)?></td>
                                    <td><?php echo $this->model_all_function->sql2date($rowdata->tgl_mulai)?></td>
                                    <td><?php echo $this->model_all_function->sql2date($rowdata->tgl_selesai)?></td>
                                    
                                    <td class='text-center'>
                                            <?php 
                                                if(empty($rowdata->doc_1)){
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Upload Dokumen Pengantar" onclick="editDoc1(<?php echo $rowdata->id?>)">
                                             Belum</br>Upload
                                        </a>
                                            <?php
                                                } else if(!empty($rowdata->doc_1)){
													if($rowdata->status !='pending'){
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm" title="Edit Dokumen" onclick="editDoc1(<?php echo $rowdata->id?>)">
                                            <i class="fa fa-edit"></i>
                                        </a>
										<?php
												}
										?>
                                        <a href="<?php echo base_url();?>gambar/<?php echo $rowdata->doc_1?>" title="Download Dokumen" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fa fa-download"></i>
                                        </a>
                                            <?php
                                                }
                                                ?>
                                    </td>
                                    <td class='text-center'>
                                            <?php 
                                                if(empty($rowdata->doc_2)){
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Upload Dokumen Proposal" onclick="editDoc2(<?php echo $rowdata->id?>)">
                                           Belum</br>Upload
                                        </a>
                                            <?php
                                                } else if(!empty($rowdata->doc_2)){
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm" title="Edit Dokumen" onclick="editDoc2(<?php echo $rowdata->id?>)">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="<?php echo base_url();?>gambar/<?php echo $rowdata->doc_2?>" title="Download Dokumen" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fa fa-download"></i>
                                        </a>
                                            <?php
                                                }
                                                ?>
                                    </td>
                                    <td class='text-center'>
                                            <?php 
                                                if(empty($rowdata->doc_3)){
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Upload Transkrip Nilai" onclick="editDoc3(<?php echo $rowdata->id?>)">
                                          Belum</br>Upload
                                        </a>
                                            <?php
                                                } else if(!empty($rowdata->doc_3)){
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm" title="Edit Dokumen" onclick="editDoc3(<?php echo $rowdata->id?>)">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="<?php echo base_url();?>gambar/<?php echo $rowdata->doc_3?>" title="Download Dokumen" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fa fa-download"></i>
                                        </a>
                                            <?php
                                                }
                                                ?>
                                    </td>
									<!--foto-->
									 <td class='text-center'>
                                            <?php 
                                                if(empty($rowdata->foto)){
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Upload Foto" onclick="editFoto(<?php echo $rowdata->id?>)">
                                             Belum</br>Upload
                                        </a>
                                            <?php
                                                } else if(!empty($rowdata->foto)){
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm" title="Edit Dokumen" onclick="editFoto(<?php echo $rowdata->id?>)">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="<?php echo base_url();?>gambar/<?php echo $rowdata->foto?>" title="Download Dokumen" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fa fa-download"></i>
                                        </a>
                                            <?php
                                                }
                                                ?>
                                    </td>
									 
                                    <td class='text-center'>
                                        <?php
                                           
                                                    if(empty($rowdata->doc_5)){
                                            ?>
                                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Upload Kartu Asuransi" onclick="editDoc5(<?php echo $rowdata->id?>)">
                                                         Belum</br>Upload
                                                    </a>
                                                        <?php
                                                    } else if(!empty($rowdata->doc_5)){
                                                        ?>
                                                    <a href="javascript:void(0)" class="btn btn-secondary btn-sm" title="Edit Dokumen" onclick="editDoc5(<?php echo $rowdata->id?>)">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo base_url();?>gambar/<?php echo $rowdata->doc_5?>" title="Download Dokumen" class="btn btn-info btn-sm" target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                            <?php
                                                    }
                                            ?>
                                    </td>
                                    <td class='text-center'>
                                        <?php
                                             if(empty($rowdata->doc_6)){
                                            ?>
                                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Upload Kartu Pelajar" onclick="editDoc6(<?php echo $rowdata->id?>)">
                                                          Belum</br>Upload
                                                    </a>
                                                        <?php
                                                    } else if(!empty($rowdata->doc_6)){
                                                        ?>
                                                    <a href="javascript:void(0)" class="btn btn-secondary btn-sm" title="Edit Dokumen" onclick="editDoc6(<?php echo $rowdata->id?>)">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo base_url();?>gambar/<?php echo $rowdata->doc_6?>" title="Download Dokumen" class="btn btn-info btn-sm" target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                            <?php
                                                    }
                                            ?>
                                    </td>
                                  <!--   <td><?php 
                                    if(empty($rowdata->no_pkl)){
                                    ?>
                                        Waiting..
                                        <?php
                                        if($auth == 1 || $auth == 2){    
                                            ?>
                                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" title="Generate No Badge" onclick="GenerateStatus(<?php echo $rowdata->id?>)"><i class="fa fa-edit"></i></a></td>
                                        <?php
                                        }    
                                    } else {
                                        echo $rowdata->no_pkl;
                                    }
                                        ?>
                                    <td class='text-center'>
                                        <?php
                                            if($rowdata->status == "Pending"){
                                        ?>
                                        Menunggu Persetujuan
                                        <?php
                                            } else {
                                                if(empty($rowdata->no_pkl)){
                                            ?>   
                                                   Waiting..
                                        <?php
                                                }else{
                                            ?>   
                                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" title="Download Kartu Magang" onclick="cetak(<?php echo $rowdata->id?>)"><i class="fa fa-print"></i></a>
                                        <?php
                                                }
                                            }
                                            ?>
                                    </td>-->
                                    <td class='text-center'>
                                      <?php
                                                    if(empty($rowdata->doc_7)){
                                            ?>
                                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Upload Surat Pernyataan" onclick="editDoc7(<?php echo $rowdata->id?>)">
                                                       Belum</br>Upload
                                                    </a>
                                                        <?php
                                                    } else if(!empty($rowdata->doc_7)){
                                                        ?>
                                                    <a href="javascript:void(0)" class="btn btn-secondary btn-sm" title="Edit Dokumen" onclick="editDoc7(<?php echo $rowdata->id?>)">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo base_url();?>gambar/<?php echo $rowdata->doc_7?>" title="Download Dokumen" class="btn btn-info btn-sm" target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                            <?php
                                                    }
                                            
                                            ?>
                                    </td>
                                   <td class='text-center'>
                                        <?php
                                            if($rowdata->status == "Pending"){
                                        ?>
                                        Menunggu Persetujuan
                                        <?php
                                            } else {
                                                if(empty($rowdata->doc_4)){
                                                    if($auth == 1 || $auth == 2){
                                            ?>
                                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Upload Surat Balasan" onclick="editDoc4(<?php echo $rowdata->id?>)">
                                                        Belum<br>Upload
                                                    </a>
                                                <?php
                                                    }
                                                }else {
                                                    if($auth == 1 || $auth == 2){
                                                ?>
                                                    <a href="javascript:void(0)" class="btn btn-secondary btn-sm" title="Edit Dokumen" onclick="editDoc4(<?php echo $rowdata->id?>)">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php
                                                    }
                                                    ?>
                                                    <a href="<?php echo base_url();?>gambar/<?php echo $rowdata->doc_4?>" title="Download Dokumen" class="btn btn-info btn-sm" target="_blank">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                            <?php
                                                }
                                            }
                                            ?>
                                    </td>
                                    <td class='text-center'>
									<?php
									
                                    if($rowdata->status =='Pending' || $this->session->userdata("SES_PRIV")==2 || $this->session->userdata("SES_PRIV")==1){
									?>
									 <a href="javascript:void(0)" class="btn btn-primary btn-sm" title="Edit Data" onclick="edit(<?php echo $rowdata->id?>)"><i class="fa fa-edit"></i></a>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-sm" title="Hapus Data" onclick="hapus(<?php echo $rowdata->id?>)"><i class="fa fa-trash"></i></a>
                                    <?php
									}else if($rowdata->status =='Accept'){
											echo '<i class="fa fa-check btn btn-success">Diterima</i>';										
									}
									?>
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
            <!--end table data -->
            
            <!--Ubah Doc1 Modal-->
            <div class="modal fade" id="UbahDoc1Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Unggah Dockumen</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="loading-bar"></div>
                            <input type="hidden" name="fo_id1" id="fo_id1" class="form-control form-control-user">
                            <div class="form-group row">
                                    <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">URL</h6>
                                        <div style="align-content: center;">
                                            <div id='url1' name="url1"></div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group row" id="perluHilangSekalian1">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Select file</h6>
                                        <form method='post' action='' enctype="multipart/form-data">
                                            <input type='file' name='file1_2' id='file1_2' accept='application/pdf' class='form-control' >
                                            Note : PDF file only.
                                            <br/>
                                            <a href="javascript:void(0)" class='btn btn-info btn-sm' onclick="upload1()" id='upload'>&nbsp;Upload</a>
                                        </form>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-default hvr-bounce-in" onclick="unlinkEdit1();" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm hvr-bounce-in" onclick="updateDoc1();"><i class="fa fa-save"></i>&nbsp;Simpan&nbsp;&nbsp;</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--Ubah Doc2 Modal-->
            <div class="modal fade" id="UbahDoc2Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Unggah Dockumen</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                       <div class="modal-body">
                            <div class="loading-bar"></div>
                            <input type="hidden" name="fo_id2" id="fo_id2" class="form-control form-control-user">
                              <div class="form-group row">
                                         <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">URL</h6>
                                        <div style="align-content: center;">
                                            <div id='url2' name="url2"></div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group row" id="perluHilangSekalian2">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Select file</h6>
                                        <form method='post' action='' enctype="multipart/form-data">
                                            <input type='file' name='file2_2' id='file2_2' accept='application/pdf' class='form-control' >
                                            Note : PDF file only.
                                            <br/>
                                            <a href="javascript:void(0)" class='btn btn-info btn-sm' onclick="upload2()" id='upload'>&nbsp;Upload</a>
                                        </form>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-default hvr-bounce-in" onclick="unlinkEdit2();" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm hvr-bounce-in" onclick="updateDoc2();"><i class="fa fa-save"></i>&nbsp;Simpan&nbsp;&nbsp;</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--Ubah Doc3 Modal-->
            <div class="modal fade" id="UbahDoc3Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Unggah Dockumen</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="loading-bar"></div>
                            <input type="hidden" name="fo_id3" id="fo_id3" class="form-control form-control-user">
                               <div class="form-group row">
                                <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">URL</h6>
                                        <div style="align-content: center;">
                                            <div id='url3' name="url3"></div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group row" id="perluHilangSekalian3">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Select file</h6>
                                        <form method='post' action='' enctype="multipart/form-data">
                                            <input type='file' name='file3_2' id='file3_2' accept='application/pdf'  class='form-control' >
                                            Note : PDF file only.
                                            <br/>   
                                            <a href="javascript:void(0)" class='btn btn-info btn-sm' onclick="upload3()" id='upload'>&nbsp;Upload</a>
                                        </form>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-default hvr-bounce-in" onclick="unlinkEdit3();" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm hvr-bounce-in" onclick="updateDoc3();"><i class="fa fa-save"></i>&nbsp;Simpan&nbsp;&nbsp;</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--Ubah Doc4 Modal-->
            <div class="modal fade" id="UbahDoc4Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Unggah Dockumen</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="loading-bar"></div>
                            <input type="hidden" name="fo_id4" id="fo_id4" class="form-control form-control-user">
                               <div class="form-group row">
                                     <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">URL</h6>
                                        <div style="align-content: center;">
                                            <div id='url4' name="url4"></div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group row" id="perluHilangSekalian4">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Select file</h6>
                                        <form method='post' action='' enctype="multipart/form-data">
                                            <input type='file' name='file4_2' id='file4_2' accept='application/pdf'  class='form-control' >
                                            Note : PDF file only.
                                            <br/>
                                            <a href="javascript:void(0)" class='btn btn-info btn-sm' onclick="upload4()" id='upload'>&nbsp;Upload</a>
                                        </form>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-default hvr-bounce-in" onclick="unlinkEdit4();" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm hvr-bounce-in" onclick="updateDoc4();"><i class="fa fa-save"></i>&nbsp;Simpan&nbsp;&nbsp;</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--Ubah Doc5 Modal-->
            <div class="modal fade" id="UbahDoc5Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Unggah Dockumen</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="loading-bar"></div>
                            <input type="hidden" name="fo_id5" id="fo_id5" class="form-control form-control-user">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">URL</h6>
                                        <div style="align-content: center;">
                                            <div id='url5' name="url5"></div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group row" id="perluHilangSekalian5">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Select file</h6>
                                        <form method='post' action='' enctype="multipart/form-data">
                                            <input type='file' name='file5_2' id='file5_2' accept='application/pdf'  class='form-control' >
                                            Note : PDF file only.
                                            <br/>
                                            <a href="javascript:void(0)" class='btn btn-info btn-sm' onclick="upload5()" id='upload'>&nbsp;Upload</a>
                                        </form>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-default hvr-bounce-in" onclick="unlinkEdit5();" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm hvr-bounce-in" onclick="updateDoc5();"><i class="fa fa-save"></i>&nbsp;Simpan&nbsp;&nbsp;</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--Ubah Doc6 Modal-->
            <div class="modal fade" id="UbahDoc6Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Unggah Dockumen</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="loading-bar"></div>
                            <input type="hidden" name="fo_id6" id="fo_id6" class="form-control form-control-user">
                         <div class="form-group row">
                           <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">URL</h6>
                                        <div style="align-content: center;">
                                            <div id='url6' name="url6"></div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group row" id="perluHilangSekalian6">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Select file</h6>
                                        <form method='post' action='' enctype="multipart/form-data">
                                            <input type='file' name='file6_2' id='file6_2' accept='application/pdf' class='form-control' >
                                            Note : PDF file only.
                                            <br/>
                                            <a href="javascript:void(0)" class='btn btn-info btn-sm' onclick="upload6()" id='upload'>&nbsp;Upload</a>
                                        </form>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-default hvr-bounce-in" onclick="unlinkEdit6();" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm hvr-bounce-in" onclick="updateDoc6();"><i class="fa fa-save"></i>&nbsp;Simpan&nbsp;&nbsp;</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--Ubah Doc7 Modal-->
            <div class="modal fade" id="UbahDoc7Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Unggah Dockumen</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="loading-bar"></div>
                            <input type="hidden" name="fo_id7" id="fo_id7" class="form-control form-control-user">
                         
                            <div class="form-group row">
                                
                                    <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">URL</h6>
                                        <div style="align-content: center;">
                                            <div id='url7' name="url7"></div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group row" id="perluHilangSekalian7">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Select file</h6>
                                        <form method='post' action='' enctype="multipart/form-data">
                                            <input type='file' name='file7_2' id='file7_2' accept='application/pdf' class='form-control' >
                                            Note : PDF file only.
                                            <br/>
                                            <a href="javascript:void(0)" class='btn btn-info btn-sm' onclick="upload7()" id='upload'>&nbsp;Upload</a>
                                        </form>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-default hvr-bounce-in" onclick="unlinkEdit7();" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm hvr-bounce-in" onclick="updateDoc7();"><i class="fa fa-save"></i>&nbsp;Simpan&nbsp;&nbsp;</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
<!--Ubah foto Modal-->
            <div class="modal fade" id="UbahFotoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Unggah Foto</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="loading-bar"></div>
                            <input type="hidden" name="fo_idfoto" id="fo_idfoto" class="form-control form-control-user">
                            
                            <div class="form-group" id="perluHilangSajaFoto">
                                <h6 class="modal-title" id="exampleModalLabel">Preview</h6>
                                <div style="align-content: center;">
                                    <div style="width:100%; height:100%" id='previewfoto'></div>
                                </div>
                            </div>
                            <div class="form-group row">
                               <div class="col-sm-6">
                                        <h6 class="modal-title" id="exampleModalLabel">URL</h6>
                                        <div style="align-content: center;">
                                            <div id='urlFoto' name="urlFoto"></div>
                                        </div>
                                    </div>
                            </div>
                            <div class="form-group row" id="perluHilangSekalianFoto">
                                    <div class="col-sm-12 mb-3 mb-sm-0">
                                        <h6 class="modal-title" id="exampleModalLabel">Select file</h6>
                                        <form method='post' action='' enctype="multipart/form-data">
                                            <input type='file' name='foto_2' id='foto_2' accept='.jpg, .jpeg'  class='form-control' >
                                            Note : JPG.
                                            <br/>
                                            <a href="javascript:void(0)" class='btn btn-info btn-sm' onclick="uploadFoto();" id='upload'>&nbsp;Upload</a>
                                        </form>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-default hvr-bounce-in" onclick="unlinkEditFoto();" data-dismiss="modal"><i class="fa fa-times"></i>&nbsp;Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm hvr-bounce-in" onclick="updateFoto();"><i class="fa fa-save"></i>&nbsp;Simpan&nbsp;&nbsp;</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
            <!--Insert Modal-->
            <div class="modal fade" id="InputModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Form Pendaftaran</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class='row'>
                                <div class='col-lg-6'>
                                       <form id="myForm" class="user" enctype="multipart/form-data" method="POST" action="<php echo base_url(); ?>transaksi/data/insert">
                                            <input type="hidden" id="user_id" name="user_id" value="<?php echo $ses_user_id?>">
                                            <span class="badge badge-info">Pengajuan</span>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Jenis Kegiatan</h6>
                                                <select class="form-control" name="kegiatan" id="kegiatan">
                                                    <option value="Penelitian">Penelitian</option>
                                                    <option value="Magang">Magang</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                    <h6 class="modal-title" id="exampleModalLabel">Tanggal Mulai</h6>
                                                    <input type="date"  class="form-control form-control-user" id="tgl_mulai" name="tgl_mulai">
                                                </div>
                                                <div class="col-sm-6">
                                                    <h6 class="modal-title" id="exampleModalLabel">Tanggal Selesai</h6>
                                                    <input type="date" class="form-control form-control-user" id="tgl_selesai" name="tgl_selesai">
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Penempatan</h6>
                                                <select class="form-control" name="id_depag" id="id_depag">
                                                    <?php
                                                    foreach ($depag as $key => $rowdata) {
                                                        echo "<option value='" . $rowdata->id . "'>" . $rowdata->nama_depag . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <span class="badge badge-danger">Identitas Pribadi</span>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Nama</h6>
                                                <input type="text" class="form-control form-control-user" id="nama" name="nama" placeholder="Nama pendaftar" required>
                                            </div>
                                                    <div class="form-group">
                                                        <h6 class="modal-title" id="exampleModalLabel">Jenis Kelamin</h6>
                                                            <select id="jk" name="jk" class="form-control">
                                                                <option value="L">Laki-laki</option>
                                                                <option value="P">Perempuan</option>
                                                            </select>
                                                        
                                                    </div>
                                                    
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Tempat lahir</h6>
                                                <input type="text" class="form-control form-control-user" id="tmpt_lahir" name="tmpt_lahir" placeholder="Tempat lahir" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Tanggal Lahir</h6>
                                                <input type="date" class="form-control form-control-user" id="tgl_lahir" name="tgl_lahir" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Alamat</h6>
                                                <input type="text" class="form-control form-control-user" id="alamat" name="alamat" required>
                                            </div>
                                            
                							<div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <h6 class="modal-title" id="exampleModalLabel">Nomor Handphone</h6>
                                                        <div style="align-content: center;">
                                                            <input type="number" class="form-control form-control-user" id="telp" name="telp" placeholder="Nomor Handphone" required>
                                                        </div>
                                                    </div>
                                            </div><div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <h6 class="modal-title" id="exampleModalLabel">Alamat Email</h6>
                                                        <div style="align-content: center;">
                                                            <input type="text" class="form-control form-control-user" id="email" name="email" required>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="form-group row">
                                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                                        <h6 class="modal-title" id="exampleModalLabel">Nama Orang Tua</h6>
                                                        <div style="align-content: center;">
                                                            <input type="text" class="form-control form-control-user" id="nm_ortu" name="nm_ortu" placeholder="Nama Orang Tua" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <h6 class="modal-title" id="exampleModalLabel">Nomor HP Orang Tua</h6>
                                                        <div style="align-content: center;">
                                                            <input type="number" class="form-control form-control-user" id="hp_ortu" name="hp_ortu" placeholder="Nomor HP Orang Tua" required>
                                                        </div>
                                                    </div>
                                            </div>
                                            
                                             <span class="badge badge-success">Data Institusi</span>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Jenjang Pendidikan</h6>
                                                <select class="form-control" name="pendidikan" id="pendidikan">
                                                    <option value="SMK">SMK</option>
                                                    <option value="D3">D3</option>
                                                    <option value="D4">D4</option>
                                                    <option value="S1">S1</option>
                                                    <option value="S2">S2</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Nama Institusi</h6>
                                                <input type="text" class="form-control form-control-user" id="institusi" name="institusi">
                                            </div>
                                            
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Nama Jurusan</h6>
                                                <input type="text" class="form-control form-control-user" id="jurusan" name="jurusan">
                                            </div>
                                            
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Alamat Institusi</h6>
                                                <input class="form-control form-control-user" id="al_institusi" name="al_institusi" required />
                                            </div>
                                            
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Nama Pembimbing</h6>
                                                <input type="text" class="form-control form-control-user" id="pembimbing" name="pembimbing">
                                            </div>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">No. Hp Pembimbing</h6></h6>
                                                <input type="number" class="form-control form-control-user" id="hp_pembimbing" name="hp_pembimbing">
                                            </div>
                                    </div>
                                    <div class='col-lg-6'>    
                                     <span class="badge badge-warning">Upload Kelengkapan Data</span>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel" style="color:red;">Upload Pas Photo (format JPG) (Bukan Foto Selfie)</h6>
                                                <input type='file' name='foto' id='foto' accept='.jpg, .jpeg' class='form-control' required >
                                            </div>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Upload Transkrip Nilai *)</h6>
                                                <input type='file' name='file3' id='file3' accept='application/pdf' class='form-control' required >
                                            </div>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Upload Kartu Pelajar *)</h6>
                                                <input type='file' name='file6' id='file6' accept='application/pdf' class='form-control' required >
                                            </div>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Upload Kartu Asuransi *)</h6>
                                                <input type='file' name='file5' id='file5' accept='application/pdf' class='form-control' required >
                                            </div>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Upload Surat Pengantar *)</h6>
                                                <input type='file' name='file1' id='file1' accept='application/pdf' class='form-control' required >
                                            </div>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Upload Proposal *)</h6>
                                                <input type='file' name='file2' id='file2' accept='application/pdf' class='form-control' required >
                                            </div>
                                            <div class="form-group">
                                                <h6 class="modal-title" id="exampleModalLabel">Upload Surat Pernyataan Kesanggupan *)</h6>
                                                <input type='file' name='file7' id='file7' accept='application/pdf' class='form-control' required >
                                            </div>
                                            </br>
                                            <span style="color:red;">Keterangan :</br>*) Format File PDF</span>
                                            <div class="modal-footer">
                                                <a href="javascript:void(0)" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</a>
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="insert();">Simpan</a>
                                            </div>
                                        </form>
                                        <div class="loading-bar"></div>
                                </div>
                                
                                        </div><!--row-->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ubah Data Modal-->
            <div class="modal fade" id="UbahModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ubah Data</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class='row'>
                                <div class='col-lg-6'>
                            <input type="hidden" name="ub_id" id="ub_id" class="form-control">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Jenis Kegiatan</h6>
                                <select class="form-control" name="ub_kegiatan" id="ub_kegiatan">
                                    <option value="Penelitian">Penelitian</option>
                                    <option value="Magang">Magang</option>
                                </select>
                            </div>
                             <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Departemen / Bagian yang dipilih</h6>
                                <select class="form-control" name="ub_id_depag" id="ub_id_depag">
                                    <?php
                                    foreach ($depag as $key => $rowdata) {
                                        echo "<option value='" . $rowdata->id . "'>" . $rowdata->nama_depag . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <h6 class="modal-title" id="exampleModalLabel">Tanggal Mulai</h6>
                                    <input type="date" class="form-control form-control-user" id="ub_tgl_mulai" name="ub_tgl_mulai">
                                </div>
                                <div class="col-sm-6">
                                    <h6 class="modal-title" id="exampleModalLabel">Tanggal Selesai</h6>
                                    <input type="date" class="form-control form-control-user" id="ub_tgl_selesai" name="ub_tgl_selesai">
                                </div>
                            </div>
                            
                          <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama</h6>
                                <input type="text" class="form-control form-control-user" id="ub_nama" name="ub_nama" placeholder="Nama pendaftar" required>
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Jenis Kelamin</h6>
                                <select class="form-control" name="ub_jk" id="ub_jk">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            
                          <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Tempat Lahir</h6>
                                <input type="text" class="form-control form-control-user" id="ub_tmpt_lahir" name="ub_tmpt_lahir" placeholder="Tempat Lahir" required>
                            </div>
                            
                          <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Tanggal Lahir</h6>
                                <input type="text" class="form-control form-control-user" id="ub_tgl_lahir" name="ub_tgl_lahir" placeholder="Tanggal Lahir" required>
                            </div>
                            
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Alamat</h6>
                                <input type="text" class="form-control form-control-user" id="ub_alamat" name="ub_alamat" required>
                            </div>
                            
							<div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nomor Handphone</h6>
                                <div style="align-content: center;">
                                    <input type="number" class="form-control form-control-user" id="ub_telp" name="ub_telp" placeholder="Nomor Handphone" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Alamat Email</h6>
                                <div style="align-content: center;">
                                    <input type="text" class="form-control form-control-user" id="ub_email" name="ub_email" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <h6 class="modal-title" id="exampleModalLabel">Nama Orang Tua</h6>
                                    <div style="align-content: center;">
                                        <input type="text" class="form-control form-control-user" id="ub_nm_ortu" name="ub_nm_ortu" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h6 class="modal-title" id="exampleModalLabel">Nomor HP Orang tua</h6>
                                    <div style="align-content: center;">
                                        <input type="number" class="form-control form-control-user" id="ub_hp_ortu" name="ub_hp_ortu" placeholder="Nomor HP Orang tua" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama Institusi</h6>
                                <input type="text" class="form-control form-control-user" id="ub_institusi" name="ub_institusi">
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Jenjang Pendidikan</h6>
                                <select class="form-control" name="ub_pendidikan" id="ub_pendidikan">
                                    <option value="SMK">SMK</option>
                                    <option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Nama Jurusan</h6>
                                <input type="text" class="form-control form-control-user" id="ub_jurusan" name="ub_jurusan" placeholder="jurusan pendaftar" required>
                            </div>
                            <div class="form-group">
                                    <h6 class="modal-title" id="exampleModalLabel">Alamat Institusi</h6>
                                    <div style="align-content: center;">
                                        <input type="text" class="form-control form-control-user" id="ub_al_institusi" name="ub_al_institusi" required>
                                    </div>
                            </div>
                            
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Pembimbing</h6>
                                <input type="text" class="form-control form-control-user" id="ub_pembimbing" name="ub_pembimbing" required>
                            </div>
                            
							<div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">HP Pembimbing</h6>
                                <div style="align-content: center;">
                                    <input type="number" class="form-control form-control-user" id="ub_hp_pembimbing" name="ub_hp_pembimbing" placeholder="HP Pembimbing" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="javascript:void(0)" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</a>
                                <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="update();">Ubah</a>
                            </div>
                            <div class="loading-bar"></div>
                            </div><!--end row-->
                        </div>
                    </div>
                </div>
            </div>
            </div>
    <?php
     if($auth == 1 || $auth == 2){
    ?>
            <!-- Ubah Status Modal-->
            <div class="modal fade" id="UbahStatusModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ubah Status Pengajuan</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                       <div class="modal-body">
                            <input type="hidden" name="st_id" id="st_id" class="form-control">
                            <div class="form-group">
                                <h6 class="modal-title" id="exampleModalLabel">Status Pengajuan</h6>
                                <select class="form-control" name="st_status" id="st_status">
                                    <option value="Pending">Pending</option>
                                    <option value="Accept">Accept</option>
                                    <option value="Rejected">Rejected</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="updStatus();">Ubah</a>
                        </div>
                        <!--</form>-->
                    </div>
                </div>
            </div>
        <?php
    }
        ?>
            <!--<div id="check"></div>-->
  <script src="<?php echo base_url(); ?>assets/vendor/jquery/jquery.min.js"></script>
<script>
    function insert() {
        
        var user_id     = $('#user_id').val();
        var nama        = $('#nama').val();
        var kegiatan    = $('#kegiatan').val();
        var alamat      = $('#alamat').val();
        var jk          = $('#jk').val();
        var tmpt_lahir  = $('#tmpt_lahir').val();
        var tgl_lahir  = $('#tgl_lahir').val();
        var nm_ortu     = $('#nm_ortu').val();
        var hp_ortu   = $('#hp_ortu').val();
        var hp_pembimbing   = $('#hp_pembimbing').val();
        var pembimbing   = $('#pembimbing').val();
		var email       = $('#email').val();
		var telp        = $('#telp').val();
        var pendidikan  = $('#pendidikan').val();
        var jurusan     = $('#jurusan').val();
        var institusi   = $('#institusi').val();
        var al_institusi   = $('#al_institusi').val();
        var id_depag    = $('#id_depag').val();
        var tgl_mulai   = $('#tgl_mulai').val();
        var tgl_selesai = $('#tgl_selesai').val();
        
        var fdx = new FormData();
        var f1 = $('#file1')[0].files[0];
        var f2 = $('#file2')[0].files[0];
        var f3 = $('#file3')[0].files[0];
        var f7 = $('#file7')[0].files[0];
        var f5 = $('#file5')[0].files[0];
        var f6 = $('#file6')[0].files[0];
        var foto = $('#foto')[0].files[0];
        
        fdx.append('user_id',user_id);
        fdx.append('nama',nama);
        fdx.append('kegiatan',kegiatan);
        fdx.append('alamat',alamat);
        fdx.append('tmpt_lahir',tmpt_lahir);
        fdx.append('tgl_lahir',tgl_lahir);
        fdx.append('jk',jk);
        fdx.append('pembimbing',pembimbing);
        fdx.append('hp_pembimbing',hp_pembimbing);
        fdx.append('hp_ortu',hp_ortu);
        fdx.append('nm_ortu',nm_ortu);
        fdx.append('email',email);
        fdx.append('telp',telp);
        fdx.append('pendidikan',pendidikan);
        fdx.append('jurusan',jurusan);
        fdx.append('institusi',institusi);
        fdx.append('al_institusi',al_institusi);
        fdx.append('id_depag',id_depag);
        fdx.append('tgl_mulai',tgl_mulai);
        fdx.append('tgl_selesai',tgl_selesai);
        fdx.append('file1', f1);
        fdx.append('file2', f2);
        fdx.append('file3', f3);
        fdx.append('file7', f7);
        fdx.append('file5', f5);
        fdx.append('file6', f6);
        fdx.append('foto', foto);
        
        if (kegiatan != "" && nama != "" && alamat != "" && jk != "" && tmpt_lahir != "" && pembimbing != "" && nm_ortu != "" && al_institusi != "" && pendidikan != "" && jurusan != "" && institusi != "" && tgl_mulai != "" && tgl_selesai != "") {
        $.ajax({
             url: "<?php echo base_url(); ?>transaksi/data/cekData",
                data: fdx,
                enctype:'multipart/form-data',
                type: "POST",
                contentType:false,
                processData: false,
                cache:false,
                beforeSend:function(){
                    $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                },
                success: function (datax) {
                   //var isi = JSON.parse(data);
                   console.log(datax);
                   if(datax==0){
                        $.ajax({
                            url: "<?php echo base_url(); ?>transaksi/data/insert",
                            data: fdx,
                            enctype:'multipart/form-data',
                            type: "POST",
                            contentType:false,
                            processData: false,
                            cache:false,
                            beforeSend:function(){
                                $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                            },
                            success: function (datax) {
                                alert('Data berhasil disimpan.');
                                setTimeout(function(){location.reload();},1000);
                            },
                            error: function (datax) {
                                alert('Salah');
                            }
                        });
                   }//end cek
                   else{
                       alert('Sudah terinput');
                   }
                }//end
        });//end cek dobel data
        } 
        else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
 
 //dua
 <?php
    if($auth==1 || $auth==2){
    ?>
  function editStatus(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var obj = JSON.parse(data);
                $('#UbahStatusModal').modal('show');
                $('#st_id').val(obj.id);
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    <?php
    }
    ?>
    function edit(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahModal').modal('show');
                $('#ub_id').val(isi.id);
                $('#ub_no_pkl').val(isi.no_pkl);
                $('#ub_nama').val(isi.nama);
                $('#ub_kegiatan').val(isi.kegiatan);
                $('#ub_alamat').val(isi.alamat);
                $('#ub_tmpt_lahir').val(isi.tmpt_lahir);
                $('#ub_tgl_lahir').val(isi.tgl_lahir);
                $('#ub_nm_ortu').val(isi.nm_ortu);
                $('#ub_hp_ortu').val(isi.hp_ortu);
                $('#ub_pembimbing').val(isi.pembimbing);
                $('#ub_hp_pembimbing').val(isi.hp_pembimbing);
                $('#ub_jk').val(isi.jk);
				$('#ub_email').val(isi.email);
				$('#ub_telp').val(isi.telp);
                $('#ub_pendidikan').val(isi.pendidikan);
                $('#ub_jurusan').val(isi.jurusan);
                $('#ub_institusi').val(isi.institusi);
                $('#ub_al_institusi').val(isi.al_institusi);
                $('#ub_id_depag').val(isi.id_depag);
                $('#ub_tgl_mulai').val(isi.tgl_mulai);
                $('#ub_tgl_selesai').val(isi.tgl_selesai);
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    
    function editDoc1(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahDoc1Modal').modal('show');
                var urlfoto = "<?php echo base_url(); ?>" + 'gambar/';
                $('#fo_id1').val(isi.id);
                $('#vimage1').html("<img src='" + urlfoto + isi.doc_1 + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    
    function editDoc2(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahDoc2Modal').modal('show');
                var urlfoto = "<?php echo base_url(); ?>" + 'gambar/';
                $('#fo_id2').val(isi.id);
                $('#vimage2').html("<img src='" + urlfoto + isi.doc_2 + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    
    function editDoc3(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahDoc3Modal').modal('show');
                var urlfoto = "<?php echo base_url(); ?>" + 'gambar/';
                $('#fo_id3').val(isi.id);
                $('#vimage3').html("<img src='" + urlfoto + isi.doc_3 + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    
    function editDoc4(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahDoc4Modal').modal('show');
                var urlfoto = "<?php echo base_url(); ?>" + 'gambar/';
                $('#fo_id4').val(isi.id);
                $('#vimage4').html("<img src='" + urlfoto + isi.doc_4 + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    
    function editDoc5(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahDoc5Modal').modal('show');
                var urlfoto = "<?php echo base_url(); ?>" + 'gambar/';
                $('#fo_id5').val(isi.id);
                $('#vimage5').html("<img src='" + urlfoto + isi.doc_5 + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
   function editFoto(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahFotoModal').modal('show');
                var urlfoto = "<?php echo base_url(); ?>" + 'gambar/';
                $('#fo_idfoto').val(isi.id);
                $('#vimagefoto').html("<img src='" + urlfoto + isi.doc_5 + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }  
    function editDoc6(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahDoc6Modal').modal('show');
                var urlfoto = "<?php echo base_url(); ?>" + 'gambar/';
                $('#fo_id6').val(isi.id);
                $('#vimage6').html("<img src='" + urlfoto + isi.doc_6 + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    function editDoc7(value) {
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/getDataTransaksi",
            data: {
                id: value
            },
            type: "POST",
            success: function (data) {
                var isi = JSON.parse(data);
                $('#UbahDoc7Modal').modal('show');
                var urlfoto = "<?php echo base_url(); ?>" + 'gambar/';
                $('#fo_id7').val(isi.id);
                $('#vimage7').html("<img src='" + urlfoto + isi.doc_7 + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
            },
            error: function (data) {
                alert("GAGAL");
            }
        });
    }
    
    function update() {
        
        var id          = $('#ub_id').val();
        var nama        = $('#ub_nama').val();
        var kegiatan    = $('#ub_kegiatan').val();
        var alamat      = $('#ub_alamat').val();
        var jk          = $('#ub_jk').val();
        var tmpt_lahir  = $('#ub_tmpt_lahir').val();
        var tgl_lahir   = $('#ub_tgl_lahir').val();
        var nm_ortu         = $('#ub_nm_ortu').val();
        var hp_ortu         = $('#ub_hp_ortu').val();
        var hp_pembimbing   = $('#ub_hp_pembimbing').val();
        var pembimbing   = $('#ub_pembimbing').val();
		var email       = $('#ub_email').val();
		var telp        = $('#ub_telp').val();
        var pendidikan  = $('#ub_pendidikan').val();
        var jurusan     = $('#ub_jurusan').val();
        var institusi   = $('#ub_institusi').val();
        var al_institusi   = $('#ub_al_institusi').val();
        var id_depag    = $('#ub_id_depag').val();
        var tgl_mulai   = $('#ub_tgl_mulai').val();
        var tgl_selesai = $('#ub_tgl_selesai').val();
        
        var fdxf= new FormData();
        
        fdxf.append('user_id',id);
        fdxf.append('nama',nama);
        fdxf.append('kegiatan',kegiatan);
        fdxf.append('alamat',alamat);
        fdxf.append('tmpt_lahir',tmpt_lahir);
        fdxf.append('tgl_lahir',tgl_lahir);
        fdxf.append('jk',jk);
        fdxf.append('pembimbing',pembimbing);
        fdxf.append('hp_pembimbing',hp_pembimbing);
        fdxf.append('hp_ortu',hp_ortu);
        fdxf.append('nm_ortu',nm_ortu);
        fdxf.append('email',email);
        fdxf.append('telp',telp);
        fdxf.append('pendidikan',pendidikan);
        fdxf.append('jurusan',jurusan);
        fdxf.append('institusi',institusi);
        fdxf.append('al_institusi',al_institusi);
        fdxf.append('id_depag',id_depag);
        fdxf.append('tgl_mulai',tgl_mulai);
        fdxf.append('tgl_selesai',tgl_selesai);
        
        if (id != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/update",
                data: fdxf,
                enctype:'multipart/form-data',
                type: "POST",
                contentType:false,
                processData: false,
                cache:false,
                beforeSend:function(){
                    $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                },
                success: function (data) {
                    
                    $(".loading-bar").html("");
                    alert("Data Berhasil Diubah !!!");
               		   window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    <?php
    if($auth==1 || $auth==2){
    ?>
    function updStatus() {
        var id      = $('#st_id').val();
        var status  = $('#st_status').val();
        if (id != "" && status != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateStatus",
                data: {
                    id: id,
                    status: status
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    <?php
    }
    ?>
    function hapus(value) {
        var conf = confirm('Apakah Anda Yakin Ingin Menghapus Data Ini?');
        if (conf) {
            var id = value;
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/delete",
                data: {
                    id: id,
                },
                type: "POST",
                success: function (data) {
                    alert("Data berhasil dihapus !");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/list";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            return false;
        }
    }
    
    function GenerateStatus(value){
        var id = value;
        if (id != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateBadge",
                data: {
                    id: id
                },
                type: "POST",
                success: function (data) {
                    alert("Nomor Badge Berhasil Dibuat !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            return false;
        }
    }
    
    function updateDoc1() {
        var id      = $('#fo_id1').val();
        var url    = document.getElementById("url1").textContent;
        if (id != "" && url != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateDoc1",
                data: {
                    id: id,
                    url: url
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    
    function updateDoc2() {
        var id      = $('#fo_id2').val();
        var url    = document.getElementById("url2").textContent;
        if (id != "" && url != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateDoc2",
                data: {
                    id: id,
                    url: url
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    
    function updateDoc3() {
        var id      = $('#fo_id3').val();
        var url    = document.getElementById("url3").textContent;
        if (id != "" && url != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateDoc3",
                data: {
                    id: id,
                    url: url
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    
    function updateDoc4() {
        var id      = $('#fo_id4').val();
        var url    = document.getElementById("url4").textContent;
        if (id != "" && url != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateDoc4",
                data: {
                    id: id,
                    url: url
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    
    function updateDoc5() {
        var id      = $('#fo_id5').val();
        var url    = document.getElementById("url5").textContent;
        if (id != "" && url != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateDoc5",
                data: {
                    id: id,
                    url: url
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    
    function updateDoc6() {
        var id      = $('#fo_id6').val();
        var url    = document.getElementById("url6").textContent;
        if (id != "" && url != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateDoc6",
                data: {
                    id: id,
                    url: url
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function updateFoto() {
        var id      = $('#fo_idfoto').val();
        var url    = document.getElementById("urlFoto").textContent;
        if (id != "" && url != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateFoto",
                data: {
                    id: id,
                    url: url
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function updateDoc7() {
        var id      = $('#fo_id7').val();
        var url    = document.getElementById("url7").textContent;
        if (id != "" && url != "") {
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/updateDoc7",
                data: {
                    id: id,
                    url: url
                },
                type: "POST",
                success: function (data) {
                    alert("Data Berhasil Diubah !!!");
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
                },
                error: function () {
                    alert("GAGAL !!!");
                }
            });
        } else {
            alert("Data Harap Dilengkapi !!!");
        }
    }
    function unlinkEditFoto() {
        var url = document.getElementById("urlFoto").textContent;
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/unlink",
            data: {
                url: url,
            },
            type: "POST",
            success: function (data) {
                window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
            },
            error: function () {
                alert("GAGAL !!!");
            }
        });
    }
    function unlinkEdit1() {
        var url = document.getElementById("url1").textContent;
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/unlink",
            data: {
                url: url,
            },
            type: "POST",
            success: function (data) {
                window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
            },
            error: function () {
                alert("GAGAL !!!");
            }
        });
    }
    
    function unlinkEdit2() {
        var url = document.getElementById("url2").textContent;
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/unlink",
            data: {
                url: url,
            },
            type: "POST",
            success: function (data) {
                window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
            },
            error: function () {
                alert("GAGAL !!!");
            }
        });
    }
    
    function unlinkEdit3() {
        var url = document.getElementById("url3").textContent;
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/unlink",
            data: {
                url: url,
            },
            type: "POST",
            success: function (data) {
                window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
            },
            error: function () {
                alert("GAGAL !!!");
            }
        });
    }
    
    function unlinkEdit4() {
        var url = document.getElementById("url4").textContent;
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/unlink",
            data: {
                url: url,
            },
            type: "POST",
            success: function (data) {
                window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
            },
            error: function () {
                alert("GAGAL !!!");
            }
        });
    }
    
    function unlinkEdit5() {
        var url = document.getElementById("url5").textContent;
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/unlink",
            data: {
                url: url,
            },
            type: "POST",
            success: function (data) {
                window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
            },
            error: function () {
                alert("GAGAL !!!");
            }
        });
    }
    
    function unlinkEdit6() {
        var url = document.getElementById("url6").textContent;
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/unlink",
            data: {
                url: url,
            },
            type: "POST",
            success: function (data) {
                window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
            },
            error: function () {
                alert("GAGAL !!!");
            }
        });
    }
    
    function unlinkEdit7() {
        var url = document.getElementById("url7").textContent;
        $.ajax({
            url: "<?php echo base_url(); ?>transaksi/data/unlink",
            data: {
                url: url,
            },
            type: "POST",
            success: function (data) {
                window.location.href = "<?php echo base_url(); ?>transaksi/data/<?php echo $this->uri->segment(3);?>";
            },
            error: function () {
                alert("GAGAL !!!");
            }
        });
    }
    
    function upload1() {
            var fd = new FormData();
            var files = $('#file1_2')[0].files[0];
            fd.append('file1', files);
            var url = "<?php echo base_url(); ?>" + 'gambar/';
            if (files) {
                $.ajax({
                    url: "<?php echo base_url(); ?>transaksi/data/uploadGambarEdit1",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                    },
                    success: function (response) {
                        $(".loading-bar").html("");
                        if (response != 0) {
                            // Show image preview
                           // $('#preview1').append("<img src='" + url + response + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
                            $('#url1').append(response);
                            document.getElementById("perluHilangSaja1").style.display = 'none';
                            document.getElementById("perluHilangSekalian1").style.display = 'none';
                        } else {
                            alert('file not uploaded');
                        }
                    }
                });
            } else {
                alert("Pilih file PDF !!!");
            }
    }
    
    function upload2() {
            var fd = new FormData();
            var files = $('#file2_2')[0].files[0];
            fd.append('file2', files);
            var url = "<?php echo base_url(); ?>" + 'gambar/';
            if (files) {
                $.ajax({
                    url: "<?php echo base_url(); ?>transaksi/data/uploadGambarEdit2",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                    },
                    success: function (response) {
                        $(".loading-bar").html("");
                        if (response != 0) {
                            // Show image preview
                          //  $('#preview2').append("<img src='" + url + response + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
                            $('#url2').append(response);
                            document.getElementById("perluHilangSaja2").style.display = 'none';
                            document.getElementById("perluHilangSekalian2").style.display = 'none';
                        } else {
                            alert('file not uploaded');
                        }
                    }
                });
            } else {
                alert("Pilih File PDF !!!");
            }
    }
    
    function upload3() {
            var fd = new FormData();
            var files = $('#file3_2')[0].files[0];
            fd.append('file3', files);
            var url = "<?php echo base_url(); ?>" + 'gambar/';
            if (files) {
                $.ajax({
                    url: "<?php echo base_url(); ?>transaksi/data/uploadGambarEdit3",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                    },
                    success: function (response) {
                        $(".loading-bar").html("");
                        if (response != 0) {
                            // Show image preview
                           // $('#preview3').append("<img src='" + url + response + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
                            $('#url3').append(response);
                            document.getElementById("perluHilangSaja3").style.display = 'none';
                            document.getElementById("perluHilangSekalian3").style.display = 'none';
                        } else {
                            alert('file not uploaded');
                        }
                    }
                });
            } else {
                alert("Pilih file PDF !!!");
            }
    }
    
    function upload4() {
            var fd = new FormData();
            var files = $('#file4_2')[0].files[0];
            fd.append('file4', files);
            var url = "<?php echo base_url(); ?>" + 'gambar/';
            if (files) {
                $.ajax({
                    url: "<?php echo base_url(); ?>transaksi/data/uploadGambarEdit4",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                    },
                    success: function (response) {
                        $(".loading-bar").html("");
                        if (response != 0) {
                            // Show image preview
                          //  $('#preview4').append("<img src='" + url + response + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
                            $('#url4').append(response);
                            document.getElementById("perluHilangSaja4").style.display = 'none';
                            document.getElementById("perluHilangSekalian4").style.display = 'none';
                        } else {
                            alert('file not uploaded');
                        }
                    }
                });
            } else {
                alert("Pilih file PDF !!!");
            }
    }
    
    function upload5() {
            var fd = new FormData();
            var files = $('#file5_2')[0].files[0];
            fd.append('file5', files);
            var url = "<?php echo base_url(); ?>" + 'gambar/';
            if (files) {
                $.ajax({
                    url: "<?php echo base_url(); ?>transaksi/data/uploadGambarEdit5",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                    },
                    success: function (response) {
                        $(".loading-bar").html("");
                        if (response != 0) {
                            // Show image preview
                          //  $('#preview5').append("<img src='" + url + response + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
                            $('#url5').append(response);
                            document.getElementById("perluHilangSaja5").style.display = 'none';
                            document.getElementById("perluHilangSekalian5").style.display = 'none';
                        } else {
                            alert('file not uploaded');
                        }
                    }
                });
            } else {
                alert("Pilih file PDF !!!");
            }
    }
    function uploadFoto() {
            var fd = new FormData();
            var files = $('#foto_2')[0].files[0];
            fd.append('foto', files);
            var url = "<?php echo base_url(); ?>" + 'gambar/';
            if (files) {
                $.ajax({
                    url: "<?php echo base_url(); ?>transaksi/data/uploadGambarFoto",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        document.getElementById("perluHilangSajaFoto").style.display = 'none';
                        $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                    },
                    success: function (response) {
                        $(".loading-bar").html("");
                        console.log(response);
                        if (response != 0) {
                            // Show image preview
                            $('#previewfoto').append("<img src='" + url + response + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
                            $('#urlFoto').append(response);
                            document.getElementById("perluHilangSajaFoto").style.display = 'block';
                            document.getElementById("perluHilangSekalianFoto").style.display = 'none';
                        } else {
                            alert('file not uploaded');
                        }
                    }
                });
            } else {
                alert("Cek Ukuran dan Format Gambar !!!");
            }
    }
    function upload6() {
            var fd = new FormData();
            var files = $('#file6_2')[0].files[0];
            fd.append('file6', files);
            var url = "<?php echo base_url(); ?>" + 'gambar/';
            if (files) {
                $.ajax({
                    url: "<?php echo base_url(); ?>transaksi/data/uploadGambarEdit6",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                    },
                    success: function (response) {
                        $(".loading-bar").html("");
                        if (response != 0) {
                            // Show image preview
                          //  $('#preview6').append("<img src='" + url + response + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
                            $('#url6').append(response);
                            document.getElementById("perluHilangSaja6").style.display = 'none';
                            document.getElementById("perluHilangSekalian6").style.display = 'none';
                        } else {
                            alert('file not uploaded');
                        }
                    }
                });
            } else {
                alert("Pilih file PDF !!!");
            }
    }
    
    function upload7() {
            var fd = new FormData();
            var files = $('#file7_2')[0].files[0];
            fd.append('file7', files);
            var url = "<?php echo base_url(); ?>" + 'gambar/';
            if (files) {
                $.ajax({
                    url: "<?php echo base_url(); ?>transaksi/data/uploadGambarEdit7",
                    type: 'POST',
                    data: fd,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        $(".loading-bar").html("<img width='100%' src='<?php echo base_url(); ?>gambar/tenor.gif'/>");
                    },
                    success: function (response) {
                        $(".loading-bar").html("");
                        if (response != 0) {
                            // Show image preview
                            //$('#preview7').append("<img src='" + url + response + "' width='30%' height='30%' align='center' style='display: inline-block;'>");
                            $('#url7').append(response);
                            document.getElementById("perluHilangSaja7").style.display = 'none';
                            document.getElementById("perluHilangSekalian7").style.display = 'none';
                        } else {
                            alert('file not uploaded');
                        }
                    }
                });
            } else {
                alert("Pilih file PDF !!!");
            }
    }
    
    function cetak(value){
            $.ajax({
                url: "<?php echo base_url(); ?>transaksi/data/isi",
                data: {
                    id: value
                },
                type: "POST",
                success: function (data) {
                    window.location.href = "<?php echo base_url(); ?>transaksi/data/cetak";
                },
                error: function (data) {
                    alert('Data gagal diproses !!');
                }
            });
    }
</script>