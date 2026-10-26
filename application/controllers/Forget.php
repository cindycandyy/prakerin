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

        // Token acak asli (bukan cipher reversibel) -- ini yang dikirim lewat email ke
        // pengguna; yang disimpan di DB cuma hash bcrypt-nya, jadi kalaupun DB bocor
        // password baru ini tidak bisa dibalik.
        $new_password = bin2hex(random_bytes(4));
        $data = array(
                'password' => password_hash($new_password, PASSWORD_DEFAULT)
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
