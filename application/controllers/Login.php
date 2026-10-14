<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('model_login');
        $this->load->library('session');
    }

    function sistem() {
        $path = $this->uri->segment(3);
        if ($path == "login_sistem") {
            $this->load->view("login/login");
        } else if ($path == "login") {
            $username = $this->input->post('userName');
            $password = $this->input->post('password');

            $qlogin = $this->model_login->getlogin($username, $password);

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

                redirect(base_url() . ($S_priv == 1 || $S_priv == 2 ? "analisis/dashboard" : "pelamar/beranda"));
            } else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;USERNAME atau PASSWORD anda SALAH.</div>');
                $this->session->set_flashdata('pesanscript', 'formlogin()');
                redirect(base_url() . "login/sistem/login_sistem");
            }
        } else if ($path == "logout") {
            $this->session->unset_userdata('SES_LOGIN');
            $this->session->unset_userdata('SES_USERNAME');
            $this->session->unset_userdata('SES_PRIV');
            $this->session->unset_userdata('SES_ID', $S_id);
            $this->session->unset_userdata('SES_LOGIN', $S_login);
            $this->session->unset_userdata('SES_PASS', $S_pass);
            $this->session->unset_userdata('SES_PICT', $S_Pict);
            $this->session->unset_userdata('SES_TAHUN', date('Y'));
            $this->session->set_flashdata('pesanscript', 'formlogin()');
            redirect(base_url());
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;USERNAME atau PASSWORD anda SALAH.</div>');
        }
    }

}
