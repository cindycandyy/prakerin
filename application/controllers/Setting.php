<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("model_setting");
    }

    public function index() {
        redirect(base_url() . "setting/data/list");
    }

    public function data() {
        $auth = $this->session->userdata("SES_PRIV");
        if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {
                $data['setting'] = $this->model_setting->viewalldata();

                $this->load->view('header');
                $this->load->view('setting/v_setting', $data);
                $this->load->view('footer');
            } else if ($path1 == 'insert') {
                $email_send   = $this->input->post('email_send');
                $email_pass   = $this->input->post('email_pass');
                $manager_name = $this->input->post('manager_name');
                $maintenance  = $this->input->post('maintenance');

                $data = array(
                    'email_send' => $email_send,
                    'email_pass' => $email_pass,
                    'manager_name' => $manager_name,
                    'maintenance' => $maintenance
                );

                $this->model_setting->save($data);
                
                $data['setting'] = $this->model_setting->viewalldata();

                $this->load->view('header');
                $this->load->view('setting/v_setting', $data);
                $this->load->view('footer');
            } else if ($path1 == 'getFieldSetting') {
                $id = $this->input->post('id');
                $data = $this->model_setting->getSingleSetting($id);
                echo $dataSingleSetting = json_encode($data);
            } else if ($path1 == 'update') {
                $id = $this->input->post('id');
                $email_send   = $this->input->post('email_send');
                $email_pass   = $this->input->post('email_pass');
                $manager_name = $this->input->post('manager_name');
                $maintenance  = $this->input->post('maintenance');

                $data = array(
                    'email_send' => $email_send,
                    'email_pass' => $email_pass,
                    'manager_name' => $manager_name,
                    'maintenance' => $maintenance
                );

                $this->model_setting->update($id, $data);
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
