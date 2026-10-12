<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pengumuman extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("model_pengumuman");
    }

    public function index() {
        redirect(base_url() . "pengumuman/data/list");
    }

    public function data() {
        $auth    = $this->session->userdata("SES_PRIV");
        $ses_id  = $this->session->userdata("SES_ID");
        $ses_user_id  = $this->session->userdata("SES_LOGIN");
        if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {
                $data['data'] = $this->model_pengumuman->viewalldata();
                $this->load->view('header');
                $this->load->view('pengumuman/list_pengumuman', $data);
                $this->load->view('footer');
            } else if ($path1 == 'insert') {
                $judul  = $this->input->post('judul');
                $umum 	= $this->input->post('pengumuman');

                $data = array(
                    'judul' => $judul,
                    'umum' => $umum
                );

                $this->model_pengumuman->save($data);
            } else if ($path1 == 'getDataPengumuman') {
                $id     = $this->input->post("id");
                $data   = $this->model_pengumuman->getSingleRowData($id);

//                $this->session->set_userdata('SES_ID', $data[0]->id_depag);
                echo $dataSingleUser = json_encode($data);
            } else if ($path1 == 'update') {
                $id        = $this->input->post('id');
                $judul  = $this->input->post('judul');
                $umum    = $this->input->post('pengumuman');

                $data = array(
                    'judul' => $judul,
                    'umum' => $umum
                );

                    $this->model_umum->update($id, $data);
            } else if ($path1 == "delete") {
                $id = $this->input->post("id");
                $this->model_pengumuman->delete($id);
            }else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
                redirect(base_url() . "login/sistem/login_sistem");
            }
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
            redirect(base_url() . "login/sistem/login_sistem");
        }
    }

}
