<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Register extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_all_function');
        $this->load->model('model_register');
        $this->load->model('model_login');
        $this->load->model('model_user');
        $this->load->library('session');
    }

    function sistem() {
        $path = $this->uri->segment(3);
        if ($path == "register_sistem") {
            $this->load->view("register/v_register");
        } else if ($path == "daftar") {
            $username = $this->input->post('username');
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $cekReady = $this->model_user->getCountEmailUSer($email);
            if ($cekReady > 0) {
                $this->session->set_flashdata('pesan', '<div class="alert alert-success">&nbsp;&nbsp;Email sudah terdaftar !!!. Silahkan gunakan email lain atau login dengan akun yang sudah ada.</div>');
                redirect(base_url() . "register/sistem/register_sistem");
            } else {
                $user_id = $this->model_all_function->customidCreator("user", "user_id", "USR");
                $data = array(
                    'user_id' => $user_id,
                    'name' => $username,
                    'email' => $email,
                    'type' => 3,
                    'password' => $password
                );

                $this->model_register->save($data);
                 $this->session->set_flashdata('pesan', '<div class="alert alert-success">&nbsp;&nbsp;Data berhasil didaftarkan, Silahkan login</div>');
                 redirect(base_url() . "login/sistem/login_sistem");
                //koreksi dari variabel username ke email
        /*        $qlogin = $this->model_login->getlogin($email, $password);
                if ($qlogin) {
                    $S_id = $qlogin->id;
                    $S_login = $qlogin->user_id;
                    $S_username = $qlogin->name;
                    $S_priv = $qlogin->type;
                    $S_pass = $qlogin->password;
                    $S_Pict = $qlogin->foto;
                    $this->session->set_userdata('SES_ID', $S_id);
                    $this->session->set_userdata('SES_LOGIN', $S_login);
                    $this->session->set_userdata('SES_USERNAME', $S_username);
                    $this->session->set_userdata('SES_PRIV', $S_priv);
                    $this->session->set_userdata('SES_PASS', $S_pass);
                    $this->session->set_userdata('SES_PICT', $S_Pict);
                    $this->session->set_userdata('SES_TAHUN', date('Y'));
                    $this->session->set_userdata('SES_BULAN', date('m'));

                    $date = date('Y-m-d H:i:s');
                    $data = array(
                        'last_login' => $date
                    );
                    $this->model_login->updatelogin($data, $S_id);
                    redirect(base_url() . "home/index_login");
                } else {
                    $this->session->set_flashdata('pesan', '<div class="alert alert-danger" style="width:350px;margin: 10px auto;"><i class="fa fa-times"></i>&nbsp;USERNAME atau PASSWORD anda SALAH.</div>');
                    $this->session->set_flashdata('pesanscript', 'formlogin()');
                    redirect(base_url() . "login/sistem/login_sistem");
                }*/
            }
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;USERNAME atau PASSWORD anda SALAH.</div>');
        }
    }

}
