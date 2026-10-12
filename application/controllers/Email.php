<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email extends CI_Controller {

    public function __construct() {
         parent::__construct(); 
        $this->load->library('session');
        $this->load->helper('form');
      } 
      
    public function index() {
        $this->load->helper('form');
        $this->load->view('email/email_form');
    }

    public function data() {
        $auth    = $this->session->userdata("SES_PRIV");
       // if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {

                $this->load->view('header');
                $this->load->view('email/v_email');
                $this->load->view('footer');
            }else if ($path1 == 'send') {
                $this->config->load('email_smtp');
                $from_email = $this->config->item('smtp_from_email');
                $to_email = $this->input->post('email');
                //Load email library
                $this->load->library('email');

                $config = array( );
                $config ['protokol']    = 'smtp';
                $config ['smtp_host']   = $this->config->item('smtp_host');
                $config ['smtp_user']   = $this->config->item('smtp_user');
                $config ['smtp_pass']   = $this->config->item('smtp_pass');
                $config ['smtp_port']   = $this->config->item('smtp_port');
                $config ['mailtype']    = "html";
                $config ['smtp_port']   = 587;
				$config['smtp_timeout']   = 20;
                $config['smtp_crypto'] = 'tls';
               $config['_smtp_auth'] = TRUE;
                $this-> email-> initialize ($config);
                $this-> email-> set_newline ("\r\n");
                
                $this-> email-> from($from_email, 'Identification');
                $this-> email-> to($to_email);
                $this-> email-> subject('Send Email Codeigniter');
                $this-> email-> message('The email send using codeigniter library');
                //Send mail
                if($this->email->send())
                    $this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
                else{
                    $this->session->set_flashdata("email_sent","You have encountered an error");
                    $this->load->view('email/email_form');
                }
//                $ci = get_instance();
//                $config['protocol'] = "smtp";
//                $config['smtp_host'] = "smtp.gmail.com";
//                $config['smtp_port'] = "465";
////                $config['smtp_crypto'] = 'ssl';
////                $config['_smtp_auth'] = TRUE;
//                $config['smtp_user'] = "muchsin.acer@gmail.com";
//                $config['smtp_pass'] = "mu130108";
//                $config['charset'] = "utf-8";
//                $config['mailtype'] = "html";
//                $config['newline'] = "\r\n";
//                $ci->email->initialize($config);
//                $ci->email->from('muchsin.acer@gmail.com', 'Rumahweb');
//                $list = array('payroll.petrosida@gmail.com');
//                $ci->email->to($list);
//                $ci->email->subject('judul email');
//                $ci->email->message('isi email');
//                if ($this->email->send()) {
//                echo 'Email sent.';
//                } else {
//                show_error($this->email->print_debugger());
//                }
            }else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
                redirect(base_url() . "login/sistem/login_sistem");
            }
        //} else {
        //    $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
        //    redirect(base_url() . "login/sistem/login_sistem");
       // }
    }

}
?>