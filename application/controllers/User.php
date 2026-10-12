<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("model_user");
        $this->load->model("model_transaksi");
        $this->load->model("model_all_function");
    }

    public function index() {
        redirect(base_url() . "user/data/list");
    }

    public function data() {
        $auth    = $this->session->userdata("SES_PRIV");
        $ses_id  = $this->session->userdata("SES_ID");
        if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {
                if($auth == 3 || $auth == 4){
                    $data['data'] = $this->model_user->getSingleUser($ses_id);
                }else if($auth == 1 || $auth == 2){
                    $data['data'] = $this->model_user->viewalldata();
                }
                $data['type'] = $this->model_user->viewalltype();

                $this->load->view('header');
                $this->load->view('user/list_user', $data);
                $this->load->view('footer');
            } else if ($path1 == 'insert') {
                $user_id    = $this->model_all_function->customidCreator("user", "user_id", "USR");
                $name       = $this->input->post('name');
                $password   = $this->input->post('password');
                $type_user  = $this->input->post('type_user');
                $email      = $this->input->post('email');

                $data = array(
                    'user_id' => $user_id,
                    'name' => $name,
                    'password' => $password,
                    'type' => $type_user,
                    'email' => $email,
                    'last_login' => ''
                );

                $this->model_user->save($data);
            } else if ($path1 == 'getDataUser') {
                $id = $this->input->post("id");
                $data = $this->model_user->getSingleRowUser($id);
                echo $dataSingleUser = json_encode($data);
            } else if ($path1 == 'update') {
                $id = $this->input->post('id');
                $name = $this->input->post('name');
                $type = $this->input->post('type');
                $email = $this->input->post('email');

                $data = array(
                        'name' => $name,
                        'type' => $type,
                        'email' => $email
                    );

                    $this->model_user->update($id, $data);
            } else if ($path1 == 'updatePass') {
                $id       = $this->input->post('id');
                $password = $this->input->post('password');

                $data = array(
                    'password' => $password,
                );

                    $this->model_user->update($id, $data);

            } else if ($path1 == "delete") {
                $id = $this->input->post("id");
                $data = $this->model_user->getSingleRowUser($id);
                $cekReady = $this->model_transaksi->getCountUserId($data->user_id);
                if ($cekReady == 0) {
                    $this->model_user->delete($id);
                    $this->session->set_flashdata('pesan', '<div class="alert alert-success">&nbsp;&nbsp;Item Berhasil Di Hapus !!!</div>');
                } else {
                    $this->session->set_flashdata("pesan", "<div class='alert alert-danger'><i class='fa fa-remove'></i>&nbsp;&nbsp;Terdapat Transaksi Pada Item yang Akan Di Hapus !!!</div>");
                }
            } else if ($path1 == "unlink") {
                $url = $this->input->post("url");
                unlink("gambar/" . $url);
            } else if ($path1 == 'uploadGambarEdit') {
                // file name
                $filename = date('dmYHis') . str_replace(" ", "", basename($_FILES["file"]["name"]));
                // Location
                $location = 'gambar/' . $filename;
                // file extension
                $file_extension = pathinfo($location, PATHINFO_EXTENSION);
                $file_extension = strtolower($file_extension);
                // Valid image extensions
                $image_ext = array("jpg", "png", "jpeg", "gif");
                $response = 0;
                if (in_array($file_extension, $image_ext)) {
                    // Upload file
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
                        $response = $location;
                    }
                }
                echo $filename;
            } else if ($path1 == 'updateProfil') {
                $id = $this->input->post('id');
                $url = $this->input->post('url');

                if ($url != null) {
                    $data = $this->model_user->getSingleUser($id);
                    unlink("gambar/" . $data->gambar);
                    
                    $this->session->set_userdata('SES_PICT', $url);

                    $data = array(
                        'foto' => $url
                    );

                    $this->model_user->update($id, $data);
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
