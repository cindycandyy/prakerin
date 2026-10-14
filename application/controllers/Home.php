<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_all_function');
        $this->load->model('model_depag');
        $this->load->model('model_transaksi');
        $this->load->model('model_setting');
    }

    public function index() {
        $auth    = $this->session->userdata("SES_PRIV");

        if (!$auth) {
            redirect(base_url() . "login/sistem/login_sistem");
        }
        redirect(base_url() . ($auth == 1 || $auth == 2 ? "analisis/dashboard" : "pelamar/beranda"));

        if($this->model_setting->getSingleSetting()->maintenance == "True" && $auth > 2){
             $this->load->view('maintenance');
        }else{
            
            //$data['depag'] = $this->model_depag->viewalldata();
			$data['depag'] = $this->model_depag->getJmlfield(1,'nama_depag');
            $tahun = $this->input->post('tahun');
            $bulan = $this->input->post('bulan');
            if (!empty($tahun)) {
                $this->session->set_userdata('SES_TAHUN', $tahun);
            }
            if (!empty($bulan)) {
                $this->session->set_userdata('SES_BULAN', $bulan);
            }

           // $this->load->view('header');
           // $this->load->view('home/home', $data);
            $this->load->view('home/home2', $data);
           // $this->load->view('footer');
        }
    }

    public function index_login() {
        $auth    = $this->session->userdata("SES_PRIV");
        
        if($this->model_setting->getSingleSetting()->maintenance == "True" && $auth > 2){
             $this->load->view('maintenance');
        }else{
            $data['depag'] = $this->model_depag->viewalldata();

            $tahun = $this->input->post('tahun');
            if (!empty($tahun)) {
                $this->session->set_userdata('SES_TAHUN', $tahun);
            }

            $this->load->view('header');
            $this->load->view('tutorial/data/list', $data);
            $this->load->view('footer');
        }
    }public function index_login2() {
        $auth    = $this->session->userdata("SES_PRIV");
        
        if($this->model_setting->getSingleSetting()->maintenance == "True" && $auth > 2){
             $this->load->view('maintenance');
        }else{
            $data['depag'] = $this->model_depag->viewalldata();

            $tahun = $this->input->post('tahun');
            if (!empty($tahun)) {
                $this->session->set_userdata('SES_TAHUN', $tahun);
            }

            $this->load->view('header');
            $this->load->view('home/home', $data);
            $this->load->view('footer');
        }
    }

}
