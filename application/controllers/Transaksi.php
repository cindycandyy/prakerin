<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transaksi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_all_function');
        $this->load->model("model_transaksi");
        $this->load->model("model_depag");
        $this->load->model("model_setting");
    }

    public function index() {
        redirect(base_url() . "transaksi/data/list");
    }

    public function data() {
        $auth    = $this->session->userdata("SES_PRIV");
        $ses_id  = $this->session->userdata("SES_ID");
        $ses_user_id  = $this->session->userdata("SES_LOGIN");
        // Sisi pelamar (type 3/4) sekarang punya jalur sendiri dengan cek kepemilikan
        // (Pelamar.php) -- controller ini (tanpa cek user_id/peran di banyak aksi tulisnya,
        // lihat SPESIFIKASI_PELAMAR.md C2) tidak lagi diizinkan untuk pelamar.
        if ($auth == 3 || $auth == 4) {
            redirect(base_url() . 'pelamar/beranda');
        }
        if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {
                if($auth == 3 || $auth == 4){
                    $data['data'] = $this->model_transaksi->getSingleData($ses_user_id, "user_id");
                }else if($auth == 1 || $auth == 2){
                    $data['data'] = $this->model_transaksi->viewalldata_st('');
                }
                $data['depag'] = $this->model_depag->viewalldata();
				$pen = $this->model_transaksi->getCountPending($ses_user_id);

                $this->load->view('header');
                $this->load->view('transaksi/v_transaksi', $data);
                $this->load->view('footer');
            } 
			else if ($path1 == 'list_pending') {
                if($auth == 3 || $auth == 4){
                    $data['data'] = $this->model_transaksi->getSingleDatax($ses_user_id, "user_id","pending");
                }else if($auth == 1 || $auth == 2){
						/**/
				   $data['data'] = $this->model_transaksi->viewalldata_st('pending');
                }
                $data['depag'] = $this->model_depag->viewalldata();

                $this->load->view('header');
                $this->load->view('transaksi/v_transaksi', $data);
                $this->load->view('footer');
            } 
			else if ($path1 == 'list_inactive') {
                if($auth == 3 || $auth == 4){
                    $data['data'] = $this->model_transaksi->getSingleDatax($ses_user_id, "user_id","inactive");
                }else if($auth == 1 || $auth == 2){
						/**/
				   $data['data'] = $this->model_transaksi->viewalldata_st('inactive');
                }
                $data['depag'] = $this->model_depag->viewalldata();

                $this->load->view('header');
                $this->load->view('transaksi/v_transaksi', $data);
                $this->load->view('footer');
            } 
			else if ($path1 == 'list_accept') {
                if($auth == 3 || $auth == 4){
                    $data['data'] = $this->model_transaksi->getSingleDatax($ses_user_id, "user_id","accept");
                }else if($auth == 1 || $auth == 2){
						/**/
				   $data['data'] = $this->model_transaksi->viewalldata_st('accept');
                }
                $data['depag'] = $this->model_depag->viewalldata();

                $this->load->view('header');
                $this->load->view('transaksi/v_transaksi', $data);
                $this->load->view('footer');
            } else if ($path1 == 'list_reject') {
                if($auth == 3 || $auth == 4){
                    $data['data'] = $this->model_transaksi->getSingleDatax($ses_user_id, "user_id","reject");
                }else if($auth == 1 || $auth == 2){
						/**/
				   $data['data'] = $this->model_transaksi->viewalldata_st('reject');
                }
                $data['depag'] = $this->model_depag->viewalldata();

                $this->load->view('header');
                $this->load->view('transaksi/v_transaksi', $data);
                $this->load->view('footer');
            } else if ($path1 == 'insert') {
                $user_id        = $this->input->post('user_id');
                $nama           = $this->input->post('nama');
                $kegiatan       = $this->input->post('kegiatan');
                $alamat         = $this->input->post('alamat');
                $tmpt_lahir     = $this->input->post('tmpt_lahir');
                $tgl_lahir      = $this->input->post('tgl_lahir');
                $jk             = $this->input->post('jk');
                $pembimbing     = $this->input->post('pembimbing');
                $hp_pembimbing  = $this->input->post('hp_pembimbing');
                $nm_ortu        = $this->input->post('nm_ortu');
                $hp_ortu        = $this->input->post('hp_ortu');
                $al_institusi   = $this->input->post('al_institusi');
                $email          = $this->input->post('email');
                $telp           = $this->input->post('telp');
                $pendidikan     = $this->input->post('pendidikan');
                $jurusan        = $this->input->post('jurusan');
                $institusi      = $this->input->post('institusi');
                $id_depag       = $this->input->post('id_depag');
                $tgl_mulai      = $this->input->post('tgl_mulai');//$this->model_all_function->date2sql($this->input->post('tgl_mulai'));
                $tgl_selesai    = $this->input->post('tgl_selesai');//$this->model_all_function->date2sql($this->input->post('tgl_selesai'));
                //file
                $file1 = date('dmYHis') . str_replace(" ", "", basename($_FILES["file1"]["name"]));$loc1 = 'gambar/' . $file1;
                $file2 = date('dmYHis') . str_replace(" ", "", basename($_FILES["file2"]["name"]));$loc2 = 'gambar/' . $file2;
                $file3 = date('dmYHis') . str_replace(" ", "", basename($_FILES["file3"]["name"]));$loc3 = 'gambar/' . $file3;
                $file7 = date('dmYHis') . str_replace(" ", "", basename($_FILES["file7"]["name"]));$loc7 = 'gambar/' . $file7;
                $file5 = date('dmYHis') . str_replace(" ", "", basename($_FILES["file5"]["name"]));$loc5 = 'gambar/' . $file5;
                $file6 = date('dmYHis') . str_replace(" ", "", basename($_FILES["file6"]["name"]));$loc6 = 'gambar/' . $file6;
                $foto = date('dmYHis') . str_replace(" ", "", basename($_FILES["foto"]["name"]));$locfoto = 'gambar/' . $foto;
                $idkey=$user_id.$nama.$telp;
                $data = array(
                    'user_id' => $user_id,
                    'kegiatan' => $kegiatan,
                    'nama' => $nama,
                    'alamat' => $alamat,
                    'jk' => $jk,
                    'tgl_lahir' => $tgl_lahir,
                    'tmpt_lahir' => $tmpt_lahir,
                    'nm_ortu' => $nm_ortu,
                    'hp_ortu' => $hp_ortu,
                    'telp' => $telp,
                    'email' => $email,
                    'pendidikan' => $pendidikan,
                    'jurusan' => $jurusan,
                    'institusi' => $institusi,
                    'almt_institusi' => $al_institusi,
                    'id_depag' => $id_depag,
                    'pembimbing' => $pembimbing,
                    'hp_pembimbing' => $hp_pembimbing,
                    'tgl_mulai' => $tgl_mulai,
                    'tgl_selesai' => $tgl_selesai,
                    'status' => 'Pending',
                    'doc_1' => $file1,
                    'doc_2' => $file2,
                    'doc_3' => $file3,
                    'doc_7' => $file7,
                    'doc_5' => $file5,
                    'doc_6' => $file6,
                    'foto' => $foto,
                    'idkey' => $idkey
                );

                $this->model_transaksi->save($data);
                
                //upload file 1
                $ex1 = pathinfo($loc1, PATHINFO_EXTENSION);
                $ex1 = strtolower($ex1);
                $img_ext1 = array("pdf");
                if (in_array($ex1, $img_ext1)) {
                    if (move_uploaded_file($_FILES['file1']['tmp_name'], $loc1)) {
                    }
                }
                //upload file 2
                $ex2 = pathinfo($loc2, PATHINFO_EXTENSION);
                $ex2 = strtolower($ex2);
                $img_ext2 = array("pdf");
                if (in_array($ex2, $img_ext2)) {
                    if (move_uploaded_file($_FILES['file2']['tmp_name'], $loc2)) {
                    }
                }
                //upload file 3
                $ex3 = pathinfo($loc3, PATHINFO_EXTENSION);
                $ex3 = strtolower($ex3);
                $img_ext3 = array("pdf");
                if (in_array($ex3, $img_ext3)) {
                    if (move_uploaded_file($_FILES['file3']['tmp_name'], $loc3)) {
                    }
                }
                //upload file 4
                $ex7 = pathinfo($loc7, PATHINFO_EXTENSION);
                $ex7 = strtolower($ex7);
                $img_ext7 = array("pdf");
                if (in_array($ex7, $img_ext7)) {
                    if (move_uploaded_file($_FILES['file7']['tmp_name'], $loc7)) {
                    }
                }
                //upload file 5
                $ex5 = pathinfo($loc5, PATHINFO_EXTENSION);
                $ex5 = strtolower($ex5);
                $img_ext5 = array("pdf");
                if (in_array($ex5, $img_ext5)) {
                    if (move_uploaded_file($_FILES['file5']['tmp_name'], $loc5)) {
                    }
                }
                //upload file 6
                $ex6 = pathinfo($loc6, PATHINFO_EXTENSION);
                $ex6 = strtolower($ex6);
                $img_ext6 = array("pdf");
                if (in_array($ex6, $img_ext6)) {
                    if (move_uploaded_file($_FILES['file6']['tmp_name'], $loc6)) {
                    }
                }
                //upload foto
                $exfoto = pathinfo($locfoto, PATHINFO_EXTENSION);
                $exfoto = strtolower($exfoto);
                $img_extfoto = array("jpg");
                $foto_terunggah = false;
                if (in_array($exfoto, $img_extfoto)) {
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $locfoto)) {
                        $foto_terunggah = true;
                    }
                }

                $id_baru = $this->db->insert_id();
                $this->antrekanAnalisis($id_baru);
                if ($foto_terunggah) {
                    $this->periksaFoto($id_baru, $foto);
                }
            }
             else if ($path1 == 'cekData') {
                $user_id        = $this->input->post('user_id');
                $nama           = $this->input->post('nama');
                $telp           = $this->input->post('telp');
                $idkey          =$user_id.$nama.$telp;
                $data   = $this->model_transaksi->getCountMagang($idkey);
                echo $x = json_encode($data);
             }
            else if ($path1 == 'getDataTransaksi') {
                $id     = $this->input->post("id");
                $data   = $this->model_transaksi->getSingleRowData($id);

//                $this->session->set_userdata('SES_ID', $data[0]->id_depag);
                echo $dataSingleUser = json_encode($data);
            } else if ($path1 == 'update') {
                $id             = $this->input->post('user_id');
                $nama           = $this->input->post('nama');
                $kegiatan       = $this->input->post('kegiatan');
                $alamat         = $this->input->post('alamat');
                $jk             = $this->input->post('jk');
                $tmpt_lahir     = $this->input->post('tmpt_lahir');
                $tgl_lahir      = $this->input->post('tgl_lahir');
                $nm_ortu        = $this->input->post('nm_ortu');
                $hp_ortu        = $this->input->post('hp_ortu');
                $pembimbing     = $this->input->post('pembimbing');
                $hp_pembimbing  = $this->input->post('hp_pembimbing');
                $email          = $this->input->post('email');
                $telp           = $this->input->post('telp');
                $pendidikan     = $this->input->post('pendidikan');
                $jurusan        = $this->input->post('jurusan');
                $institusi      = $this->input->post('institusi');
                $al_institusi   = $this->input->post('al_institusi');
                $id_depag       = $this->input->post('id_depag');
                $tgl_mulai      = $this->input->post('tgl_mulai');
                $tgl_selesai    = $this->input->post('tgl_selesai');

                $data = array(
                    'kegiatan' => $kegiatan,
                    'nama' => $nama,
                    'alamat' => $alamat,
                    'jk' => $jk,
                    'tmpt_lahir' => $tmpt_lahir,
                    'tgl_lahir' => $tgl_lahir,
                    'pembimbing' => $pembimbing,
                    'hp_pembimbing' => $hp_pembimbing,
                    'nm_ortu' => $nm_ortu,
                    'hp_ortu' => $hp_ortu,
                    'telp' => $telp,
                    'email' => $email,
                    'pendidikan' => $pendidikan,
                    'jurusan' => $jurusan,
                    'institusi' => $institusi,
                    'almt_institusi' => $al_institusi,
                    'id_depag' => $id_depag,
                    'tgl_mulai' => $tgl_mulai,
                    'tgl_selesai' => $tgl_selesai
                );

                    $this->model_transaksi->update($id, $data);
            } else if ($path1 == 'updateStatus') {
                $id     = $this->input->post('id');
                $status = $this->input->post('status');

                $data = array(
                    'status' => $status,
                );

                    $this->model_transaksi->update($id, $data);

            } else if ($path1 == 'updateBadge') {
                $id     = $this->input->post('id');
                $badge  = $this->model_all_function->customNoDoc("pkl_data", "no_pkl", "SDM");

                $data = array(
                    'no_pkl' => $badge,
                );

                    $this->model_transaksi->update($id, $data);

            } else if ($path1 == "delete") {
                $id = $this->input->post("id");
                $data = $this->model_transaksi->getSingleRowData($id);
                foreach (array('doc_1', 'doc_2', 'doc_3', 'doc_4', 'doc_5', 'doc_6', 'doc_7', 'foto') as $slot) {
                    if (!empty($data->{$slot}) && file_exists("gambar/" . $data->{$slot})) {
                        unlink("gambar/" . $data->{$slot});
                    }
                }

                $this->model_transaksi->delete($id);
            } else if ($path1 == "unlink") {
                $url = basename($this->input->post("url"));
                if ($url !== '' && file_exists("gambar/" . $url)) {
                    unlink("gambar/" . $url);
                }
            } else if ($path1 == 'uploadGambarEdit1') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["file1"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
                //$image_ext = array("jpg", "png", "jpeg", "gif", "pdf");
                $image_ext = array("pdf");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['file1']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            } else if ($path1 == 'uploadGambarEdit2') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["file2"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
                //$image_ext = array("jpg", "png", "jpeg", "gif", "pdf");
                $image_ext = array("pdf");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['file2']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            } else if ($path1 == 'uploadGambarEdit3') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["file3"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
               // $image_ext = array("jpg", "png", "jpeg", "gif", "pdf");
                 $image_ext = array("pdf");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['file3']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            } else if ($path1 == 'uploadGambarEdit4') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["file4"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
               // $image_ext = array("jpg", "png", "jpeg", "gif", "pdf");
			    $image_ext = array("pdf");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['file4']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            } else if ($path1 == 'uploadGambarEdit5') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["file5"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
                $image_ext = array("pdf");//$image_ext = array("jpg", "png", "jpeg", "gif", "pdf");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['file5']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            }else if ($path1 == 'uploadGambarFoto') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["foto"]["name"]));
                $ukuran	= $_FILES['foto']['size'];
                
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
                $image_ext = array("jpg");
                $response = 0;
                
                if($ukuran < 3044070){
                    if (in_array($file_extension, $image_ext)) {
                        // Upload file
                        if (move_uploaded_file($_FILES['foto']['tmp_name'], $location)) {
                            $response = $location;
                        }
                    }
                    echo $filename;
                }
            } else if ($path1 == 'uploadGambarEdit6') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["file6"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
                //$image_ext = array("jpg", "png", "jpeg", "gif", "pdf");
                $image_ext = array("pdf");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['file6']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            } else if ($path1 == 'uploadGambarEdit7') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["file7"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
               // $image_ext = array("jpg", "png", "jpeg", "gif", "pdf");
                $image_ext = array("pdf");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['file7']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            } else if ($path1 == 'updateDoc1') {
                $id = $this->input->post('id');
                $url = $this->input->post('url');

                if ($url != null) {
                    $data = $this->model_transaksi->getSingleData($id);
                    if (!empty($data->doc_1)) { @unlink("gambar/" . $data->doc_1); }

                    $data = array(
                        'doc_1' => $url
                    );

                    $this->model_transaksi->update($id, $data);
                }
            } else if ($path1 == 'updateDoc2') {
                $id = $this->input->post('id');
                $url = $this->input->post('url');

                if ($url != null) {
                    $data = $this->model_transaksi->getSingleData($id);
                    if (!empty($data->doc_2)) { @unlink("gambar/" . $data->doc_2); }

                    $data = array(
                        'doc_2' => $url
                    );

                    $this->model_transaksi->update($id, $data);
                    $this->antrekanAnalisis($id);
                }
            } else if ($path1 == 'updateDoc3') {
                $id = $this->input->post('id');
                $url = $this->input->post('url');

                if ($url != null) {
                    $data = $this->model_transaksi->getSingleData($id);
                    if (!empty($data->doc_3)) { @unlink("gambar/" . $data->doc_3); }

                    $data = array(
                        'doc_3' => $url
                    );

                    $this->model_transaksi->update($id, $data);
                    $this->antrekanAnalisis($id);
                }
            } else if ($path1 == 'updateDoc4') {
                $id = $this->input->post('id');
                $url = $this->input->post('url');

                if ($url != null) {
                    $data = $this->model_transaksi->getSingleData($id);
                    if (!empty($data->doc_4)) { @unlink("gambar/" . $data->doc_4); }

                    $data = array(
                        'doc_4' => $url
                    );

                    $this->model_transaksi->update($id, $data);
                }
            } else if ($path1 == 'updateDoc5') {
                $id = $this->input->post('id');
                $url = $this->input->post('url');

                if ($url != null) {
                    $data = $this->model_transaksi->getSingleData($id);
                    if (!empty($data->doc_5)) { @unlink("gambar/" . $data->doc_5); }

                    $data = array(
                        'doc_5' => $url
                    );

                    $this->model_transaksi->update($id, $data);
                }
            }
else if ($path1 == 'updateFoto') {
                $id = $this->input->post('id');
                $url = $this->input->post('url');

                if ($url != null) {
                    $data = $this->model_transaksi->getSingleData($id);
                    if (!empty($data->foto)) { @unlink("gambar/" . $data->foto); }

                    $data = array(
                        'foto' => $url
                    );

                    $this->model_transaksi->update($id, $data);
                    $this->periksaFoto($id, $url);
                }
            }			else if ($path1 == 'updateDoc6') {
                $id = $this->input->post('id');
                $url = $this->input->post('url');

                if ($url != null) {
                    $data = $this->model_transaksi->getSingleData($id);
                    if (!empty($data->doc_6)) { @unlink("gambar/" . $data->doc_6); }

                    $data = array(
                        'doc_6' => $url
                    );

                    $this->model_transaksi->update($id, $data);
                }
            } else if ($path1 == 'updateDoc7') {
                $id = $this->input->post('id');
                $url = $this->input->post('url');

                if ($url != null) {
                    $data = $this->model_transaksi->getSingleData($id);
                    if (!empty($data->doc_7)) { @unlink("gambar/" . $data->doc_7); }

                    $data = array(
                        'doc_7' => $url
                    );

                    $this->model_transaksi->update($id, $data);
                }
            } else if ($path1 == 'isi') {
                $id = $this->input->post('id');
                $data = $this->model_transaksi->getSingleRowData($id);
                
                $this->session->set_userdata('SES_01', $data->nama);
                $this->session->set_userdata('SES_02', $data->no_pkl);
                $this->session->set_userdata('SES_03', $data->institusi);
                $this->session->set_userdata('SES_04', $this->model_all_function->sql2date($data->tgl_mulai)." s/d ".$this->model_all_function->sql2date($data->tgl_selesai));
            } else if ($path1 == 'cetak') {
				// seting cetak ID CARD
                $nama       = $this->session->userdata("SES_01");
                $badge      = $this->session->userdata("SES_02");
                $institusi  = $this->session->userdata("SES_03");
                $waktu      = $this->session->userdata("SES_04");
//                $siswa = $this->$this->model_transaksi->viewalldata();
                
                include APPPATH.'third_party/Classes/PHPExcel.php';
//                include APPPATH.'third_party/excel_reader.php';
                $excel = new PHPExcel();
                // Settingan awal fil excel
                $excel->getProperties()->setCreator('Admin SDM')
                             ->setLastModifiedBy('Admin SDM')
                             ->setTitle("Data Siswa Magang")
                             ->setSubject("PKL")
                             ->setDescription("Laporan Semua Data Magang")
                             ->setKeywords("Data PKL");
                // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
                $style_col = array(
                  'font' => array('bold' => true), // Set font nya jadi bold
                  'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                  ),
                  'borders' => array(
                    'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                    'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                    'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                    'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
                  ),
			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'cccccc')
        	)
                );
                // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
                $style_row = array(
                  'alignment' => array(
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                  ),
                  'borders' => array(
                    'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                    'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                    'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                    'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
                  )
                );
                
                $default_border = array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('rgb'=>'000000')
                );
                $style_border = array(
                    'borders' => array(
                        'bottom' => $default_border,
                        'left' => $default_border,
                        'top' => $default_border,
                        'right' => $default_border,
                    )
                );
                // Memberi garis batas kartu
                    $excel->getSheet(0)->getStyle('A2:L19')->applyFromArray($style_border);
                    
                // Memberi garis batas foto
                    $excel->getSheet(0)->getStyle('B12:B18')->applyFromArray($style_border);
                    
                // Memberi garis batas tdd pkl
                    $excel->getSheet(0)->getStyle('D12:H15')->applyFromArray($style_border);
                
                // Memberi garis batas tdd manager
                    $excel->getSheet(0)->getStyle('J12:K15')->applyFromArray($style_border);
                    
                // Set width kolom
                    $excel->getActiveSheet()->getColumnDimension('A')->setWidth(1);
                    $excel->getActiveSheet()->getColumnDimension('B')->setWidth(11);
                    $excel->getActiveSheet()->getColumnDimension('C')->setWidth(1);
                    $excel->getActiveSheet()->getColumnDimension('D')->setWidth(2);
                    $excel->getActiveSheet()->getColumnDimension('E')->setWidth(2);
                    $excel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
                    $excel->getActiveSheet()->getColumnDimension('G')->setWidth(2);
                    $excel->getActiveSheet()->getColumnDimension('H')->setWidth(2);
                    $excel->getActiveSheet()->getColumnDimension('I')->setWidth(1);
                    $excel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
                    $excel->getActiveSheet()->getColumnDimension('K')->setWidth(4);
                    $excel->getActiveSheet()->getColumnDimension('L')->setWidth(1);
                    $excel->getActiveSheet()->getColumnDimension('M')->setWidth(1);
                  
                // Set width baris
                    $excel->getActiveSheet()->getRowDimension(1)->setRowHeight(5);
                    $excel->getActiveSheet()->getRowDimension(2)->setRowHeight(5);
                    $excel->getActiveSheet()->getRowDimension(3)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(4)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(5)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(6)->setRowHeight(5);
                    $excel->getActiveSheet()->getRowDimension(7)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(8)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(9)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(10)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(11)->setRowHeight(5);
                    $excel->getActiveSheet()->getRowDimension(12)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(13)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(14)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(15)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(16)->setRowHeight(5);
                    $excel->getActiveSheet()->getRowDimension(17)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(18)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(19)->setRowHeight(10);
                    $excel->getActiveSheet()->getRowDimension(20)->setRowHeight(10);
                    
                    //Isi gambar logo
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('Data SDM Dan Umum');
                    $objDrawing->setDescription('Image logo');
                    $objDrawing->setPath('gambar\Logo Petrosidax.png');
                    $objDrawing->setHeight(39);
                    $objDrawing->setCoordinates('B3'); 
                    $objDrawing->setWorksheet($excel->getActiveSheet());
                    
                    //Isi gambar footer
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('Data SDM Dan Umum');
                    $objDrawing->setDescription('Image footer');
                    $objDrawing->setPath('gambar\Footer.png');
                    $objDrawing->setHeight(31);
                    $objDrawing->setCoordinates('D17'); 
                    $objDrawing->setWorksheet($excel->getActiveSheet());
                    
                    $excel->getActiveSheet()->setCellValue('E4', 'KARTU TANDA PENGENAL');
                    $excel->getActiveSheet()->getStyle('E4')->getFont()->setSize(10);
                    $excel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
                    $excel->getSheet(0)->getStyle('E4')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
//                    $excel->getSheet(0)->getStyle('E4:H4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    
                    $excel->getActiveSheet()->setCellValue('B7', 'Nama');
                    $excel->getActiveSheet()->setCellValue('B8', 'No. Badge');
                    $excel->getActiveSheet()->setCellValue('B9', 'Sekolah/PT');
                    $excel->getActiveSheet()->setCellValue('B10', 'Masa Berlaku');
                    $excel->getActiveSheet()->setCellValue('D7', ':');
                    $excel->getActiveSheet()->setCellValue('D8', ':');
                    $excel->getActiveSheet()->setCellValue('D9', ':');
                    $excel->getActiveSheet()->setCellValue('D10', ':');
                    $excel->getActiveSheet()->setCellValue('E7', $nama);
                    $excel->getActiveSheet()->setCellValue('E8', $badge);
                    $excel->getActiveSheet()->setCellValue('E9', $institusi);
                    $excel->getActiveSheet()->setCellValue('E10', $waktu);
                    
                    $excel->getActiveSheet()->getStyle('B7')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('B8')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('B9')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('B10')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('D7')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('D8')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('D9')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('D10')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('E7')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('E8')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('E9')->getFont()->setSize(8);
                    $excel->getActiveSheet()->getStyle('E10')->getFont()->setSize(8);
                    
                    $excel->getActiveSheet()->mergeCells('B12:B18');
                    $excel->getActiveSheet()->getStyle('B12:B18')->getAlignment()->applyFromArray(
                        array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
                    $excel->getActiveSheet()->getStyle('B12:B18')->getAlignment()->applyFromArray(
                        array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));
                    $excel->getActiveSheet()->setCellValue('B12', 'Foto');

                    $excel->getActiveSheet()->mergeCells('D12:H12');
                    $excel->getActiveSheet()->getStyle('D12:H12')->getAlignment()->applyFromArray(
                        array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
                    $excel->getActiveSheet()->setCellValue('D12', 'Peserta Magang');
                    $excel->getActiveSheet()->getStyle('D12')->getFont()->setSize(9);

                    $excel->getActiveSheet()->mergeCells('J12:K12');
                    $excel->getActiveSheet()->getStyle('J12:K12')->getAlignment()->applyFromArray(
                        array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
                    $excel->getActiveSheet()->setCellValue('J12', 'Manager SDM & Umum');
                    $excel->getActiveSheet()->getStyle('J12')->getFont()->setSize(9);

                    $excel->getActiveSheet()->mergeCells('D15:H15');
                    $excel->getActiveSheet()->getStyle('D15:H15')->getAlignment()->applyFromArray(
                        array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
                    $excel->getActiveSheet()->getStyle('D15:H15')->getAlignment()->applyFromArray(
                        array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));
                    $excel->getActiveSheet()->setCellValue('D15', $nama);
                    $excel->getActiveSheet()->getStyle('D15')->getFont()->setSize(9);

                    $excel->getActiveSheet()->mergeCells('J15:K15');
                    $excel->getActiveSheet()->getStyle('J15:K15')->getAlignment()->applyFromArray(
                        array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
                    $excel->getActiveSheet()->getStyle('J15:K15')->getAlignment()->applyFromArray(
                        array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));
                    $excel->getActiveSheet()->setCellValue('J15', $this->model_setting->getSingleSetting()->manager_name);
                    $excel->getActiveSheet()->getStyle('J15')->getFont()->setSize(9);
                    
                // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
                $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
                // Set orientasi kertas jadi LANDSCAPE
//                $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                // Set judul file excel nya
                $excel->getActiveSheet(0)->setTitle("Kartu Siswa Magang");
                $excel->setActiveSheetIndex(0);
                // Proses file excel
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="Kartu Siswa Magang.xlsx"'); // Set nama file excel nya
                header('Cache-Control: max-age=0');
                $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
                $write->save('php://output');
            }else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
                redirect(base_url() . "login/sistem/login_sistem");
            }
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
            redirect(base_url() . "login/sistem/login_sistem");
        }
    }

    // Hook modul Analisis Penempatan (spesifikasi teknis bagian 1 & 9.4): dipanggil
    // setelah pendaftaran baru atau proposal/transkrip diunggah ulang. Dibungkus
    // try/catch dan dicek saklar config supaya kegagalan di sini TIDAK PERNAH membuat
    // alur pendaftaran/unggah lama (di atas) gagal -- modul analisis boleh mati total
    // tanpa memengaruhi E-Prakerin.
    private function antrekanAnalisis($id_transaksi) {
        if (empty($id_transaksi)) {
            return;
        }
        try {
            $this->config->load('analisis');
            if (!$this->config->item('analisis_aktif')) {
                return;
            }
            $this->load->library('analisis_queue');
            $this->analisis_queue->enqueue($id_transaksi);
        } catch (Exception $e) {
            log_message('error', 'antrekanAnalisis gagal untuk id=' . $id_transaksi . ': ' . $e->getMessage());
        }
    }

    // FR-29: pemeriksaan foto otomatis lokal, dipanggil tiap foto diunggah/diganti.
    // Sinkron (menunggu respons Python) tapi dibungkus try/catch seperti
    // antrekanAnalisis() -- kalau layanan Python mati, upload foto E-Prakerin lama
    // tetap sukses, cuma tidak ada hasil pemeriksaan untuk foto itu.
    private function periksaFoto($id_transaksi, $nama_file) {
        if (empty($id_transaksi) || empty($nama_file)) {
            return;
        }
        try {
            $this->config->load('analisis');
            if (!$this->config->item('analisis_aktif')) {
                return;
            }
            $this->load->library('analisis_foto');
            $this->analisis_foto->periksaDanSimpan($id_transaksi, $nama_file);
        } catch (Exception $e) {
            log_message('error', 'periksaFoto gagal untuk id=' . $id_transaksi . ': ' . $e->getMessage());
        }
    }

}
