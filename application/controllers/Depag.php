<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Depag extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("model_depag");
        $this->load->model("model_transaksi");
        $this->load->model("model_persyaratan");
    }

    public function index() {
        redirect(base_url() . "depag/data/list");
    }

    public function data() {
        $auth = $this->session->userdata("SES_PRIV");
        if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {
                $data['depag'] = $this->model_depag->viewalldata();
                $this->load->view('header');
                $this->load->view('depag/list_depag', $data);
                $this->load->view('footer');
                
            } else if ($path1 == 'insert') {
                $nama = $this->input->post('nama_depag');
                $kuota = $this->input->post('kuota');
                $deskripsi = $this->input->post('deskripsi');
                $data = array(
                    'nama_depag' => $nama,
                    'kuota' => $kuota,
                    'deskripsi' => $deskripsi
                );

                $this->model_depag->save($data);
                $data['depag'] = $this->model_depag->viewalldata();

                $this->load->view('header');
                $this->load->view('depag/list_depag', $data);
                $this->load->view('footer');
                
            } else if ($path1 == 'getFieldDepag') {
                $id = $this->input->post('id');
                $data = $this->model_depag->getSingleDepag($id);
                echo $dataSingleDepag = json_encode($data);
                
            } else if ($path1 == 'update') {
                $id = $this->input->post('id');
                $nama = $this->input->post('nama_depag');
                $kuota = $this->input->post('kuota');
                $deskripsi = $this->input->post('deskripsi');

                $data = array(
                    'nama_depag' => $nama,
                    'kuota' => $kuota,
                    'deskripsi' => $deskripsi
                );

                $this->model_depag->update($id, $data);
            } else if ($path1 == "delete") {
                $id = $this->input->post("id");
                $cekReady1 = $this->model_persyaratan->getCountDepag($id);
                $cekReady2 = $this->model_transaksi->getCountPersyaratan($id);
                if ($cekReady1 == 0 && $cekReady2 == 0) {
                    $this->model_depag->delete($id);
                    $this->session->set_flashdata('pesan', '<div class="alert alert-success">&nbsp;&nbsp;Item Berhasil Di Hapus !!!</div>');
                } else {
                    $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Terdapat Transaksi Pada Item yang Akan Di Hapus !!!</div>');
                }
            } else if ($path1 == "unlink") {
                $url = $this->input->post("url");
                unlink("gambar/" . $url);
            } else if ($path1 == 'uploadGambar') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["file"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
                $image_ext = array("jpg", "png", "jpeg", "gif");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            } else if ($path1 == 'uploadGambarEdit') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["efile"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
                $image_ext = array("jpg", "png", "jpeg", "gif");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['efile']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
                redirect(base_url() . "login/sistem/login_sistem");
            }
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
            redirect(base_url() . "login/sistem/login_sistem");
        }
    }

}
