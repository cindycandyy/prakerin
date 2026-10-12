<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tutorial extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("model_persyaratan");
        $this->load->model("model_depag");
        $this->load->model("model_tutorial");
    }

    
    public function index() {
        parent::__construct();
    }

    public function data() {
        $auth    = $this->session->userdata("SES_PRIV");
        if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {
                $data['data']  = $this->model_persyaratan->viewalldata();
                $data['depag'] = $this->model_depag->viewalldata();
                 
                $this->load->view('header');
                $this->load->view('tutorial/v_tutorial', $data);
                $this->load->view('footer');
            }else if ($path1 == 'edit') {
                $id        = $this->input->post('id');
                $desc       = $this->input->post('desk');
             
                $data = array(
                    'description' => $desc
                );

                $this->model_tutorial->update($id,$data);
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
?>