<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Persyaratan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_persyaratan');
        $this->load->model('model_depag');
    }

    public function index() {
        redirect(base_url() . "persyaratan/data/list");
    }

    public function data() {
        $auth    = $this->session->userdata("SES_PRIV");
        $ses_id  = $this->session->userdata("SES_ID");
        $ses_user_id  = $this->session->userdata("SES_LOGIN");
        if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {
                $data['data'] = $this->model_persyaratan->viewalldata();
                $data['depag'] = $this->model_depag->viewalldata();

                $this->load->view('header');
                $this->load->view('persyaratan/v_persyaratan', $data);
                $this->load->view('footer');
            } else if ($path1 == 'insert') {
                $id_depag  = $this->input->post('id_depag');
                $syarat    = $this->input->post('syarat');

                $data = array(
                    'id_depag' => $id_depag,
                    'syarat' => $syarat
                );

                $this->model_persyaratan->save($data);
            } else if ($path1 == 'getDataPersyaratan') {
                $id     = $this->input->post("id");
                $data   = $this->model_persyaratan->getSingleRowData($id);

//                $this->session->set_userdata('SES_ID', $data[0]->id_depag);
                echo $dataSingleUser = json_encode($data);
            } else if ($path1 == 'update') {
                $id        = $this->input->post('id');
                $id_depag  = $this->input->post('id_depag');
                $syarat    = $this->input->post('syarat');

                $data = array(
                    'id_depag' => $id_depag,
                    'syarat' => $syarat
                );

                    $this->model_persyaratan->update($id, $data);
            } else if ($path1 == "delete") {
                $id = $this->input->post("id");
                $this->model_persyaratan->delete($id);
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
