<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class type_user extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("model_type_user");
        $this->load->model("model_user");
    }

    public function index() {
        redirect(base_url() . "type_user/data/list");
    }

    public function data() {
        $auth = $this->session->userdata("SES_PRIV");
        if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {
                $data['type_user'] = $this->model_type_user->viewalldata();

                $this->load->view('header');
                $this->load->view('type_user/v_type_user', $data);
                $this->load->view('footer');
            } else if ($path1 == 'insert') {
                $type_user = $this->input->post('type_user');

                $data = array(
                    'type_user' => $type_user
                );

                $this->model_type_user->save($data);
                
                $data['type_user'] = $this->model_type_user->viewalldata();

                $this->load->view('header');
                $this->load->view('type_user/v_type_user', $data);
                $this->load->view('footer');
            } else if ($path1 == 'getFieldTypeUser') {
                $id = $this->input->post('id');
                $data = $this->model_type_user->getSingleTypeUser($id);
                echo $dataSingleDepag = json_encode($data);
            } else if ($path1 == 'update') {
                $id = $this->input->post('id');
                $type_user = $this->input->post('type_user');

                $data = array(
                    'type_user' => $type_user
                );

                $this->model_type_user->update($id, $data);
            } else if ($path1 == "delete") {
                $id = $this->input->post("id");
                $cekReady = $this->model_user->getCountTypeUSer($id);
                if ($cekReady == 0) {
                    $this->model_type_user->delete($id);
                    $this->session->set_flashdata('pesan', '<div class="alert alert-success">&nbsp;&nbsp;Item Berhasil Di Hapus !!!</div>');
                } else {
                    $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Terdapat Transaksi Pada Item yang Akan Di Hapus !!!</div>');
                }
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
