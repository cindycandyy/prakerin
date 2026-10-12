<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
error_reporting(0);

class Forget extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model("model_forget");
        $this->load->model("model_setting");
    }

    public function index() {
        $this->load->view('forget/v_forget');
    }
    
    function send_email() {
        
        $from_email = $this->model_setting->getSingleSetting()->email_send;//"pkl.petrosida@gmail.com";
        $to_email = $this->input->post('email');
        $str = rand();
        
        $kunci = 'c89b29cb-493d-4ade-9cd7-0e147f8a7830';
        for ($i = 0; $i < strlen($str); $i++) {
            $karakter = substr($str, $i, 1);
            $kuncikarakter = substr($kunci, ($i % strlen($kunci))-1, 1);
            $karakter = chr(ord($karakter)+ord($kuncikarakter));
            $hasil .= $karakter;
        }
        
        $new_password = urlencode(base64_encode($hasil));
            $data = array(
                    'password' => $new_password
                    );
        
        $this->model_forget->update($to_email, $data);
        
        //Load email library
        $this->load->library('email');
                
        //Configurasi email
        $config = array( );
        $config ['protokol']    = 'ssmtp';
        $config ['smtp_host']   = 'smtp.gmail.com';
        $config ['smtp_user']   = $this->model_setting->getSingleSetting()->email_send;//'pkl.petrosida@gmail.com';
        $config ['smtp_pass']   = $this->model_setting->getSingleSetting()->email_pass;//'petrosida-gresik';
        $config ['mailtype']    = "html";
        $config ['smtp_port']   = 465;
        $this-> email-> initialize ($config);
        $this-> email-> set_newline ("\r\n");
                
        $this-> email-> from($from_email, 'Admin PKL PT Petrosida Gresik');
        $this-> email-> to($to_email);
        $this-> email-> subject('Reseter Password');
        $this-> email-> message('Password berhasil direset, Password anda yang baru adalah : '.$new_password.'. Silahkan login kembali dan anda bisa mengubahnya kembali ketika sudah login ke dalam sistem.');
            //Send mail
            if($this->email->send()){
                $this->session->set_flashdata("pesan",'<div class="alert alert-danger">&nbsp;&nbsp;Password sudah dikirim ke email anda.</div>');
                $this->load->view('forget/v_forget');
            }else{
                $this->session->set_flashdata("pesan",'<div class="alert alert-danger">&nbsp;&nbsp;You have encountered an error</div>');
                $this->load->view('forget/v_forget');
            }
    }

}
