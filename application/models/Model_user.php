<?php
class Model_user extends CI_Model{

    var $tabel = 'user';
    
    function __construct() {
        parent::__construct();
    }
    
    function viewalldata() {
        $sql = "SELECT * FROM user";
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    function viewalltype() {
        $sql = "SELECT * FROM type_user";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getSingleRowUser($id) {
        $filter = "";
        if (!empty($id)) {
            $filter .= " and id = '$id'";
        }

        $sql = "SELECT * FROM user WHERE true " . $filter . " ";

        $query = $this->db->query($sql);

        $data = $query->result();

        return $data[0];
    }
    
    public function getSingleUser($id) {
        $filter = "";
        if (!empty($id)) {
            $filter .= " and id = '$id'";
        }

        $sql = "SELECT * FROM user WHERE true " . $filter . " ";

        $query = $this->db->query($sql);

        $data = $query->result();

        return $data;
    }
    
    public function getCountTypeUSer($id) {
        $filter = "";
        if (!empty($id)) {
            $filter .= " and type= '$id'";
        }
        $sql = "Select COUNT(*) AS total from user where true " . $filter . " ";
        $query = $this->db->query($sql);
        $temp = $query->row();
        if ($temp) {
            return (INT) $temp->total;
        } else {
            return 0;
        }
    }
    
    public function getCountEmailUSer($email) {
        $filter = "";
        if (!empty($email)) {
            $filter .= " and email= '$email'";
        }
        $sql = "Select COUNT(*) AS total from user where true " . $filter . " ";
        $query = $this->db->query($sql);
        $temp = $query->row();
        if ($temp) {
            return (INT) $temp->total;
        } else {
            return 0;
        }
    }

    // Fungsi untuk melakukan proses upload file
    function upload() {
        $config['upload_path'] = 'gambar/';
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['max_size'] = '2048';
        $config['remove_space'] = TRUE;

        $this->load->library('upload', $config); // Load konfigurasi uploadnya
        if ($this->upload->do_upload('gambar')) { // Lakukan upload dan Cek jika proses upload berhasil
            // Jika berhasil :
            $return = array('result' => 'success', 'file' => $this->upload->data(), 'error' => '');
            return $return;
        } else {
            // Jika gagal :
            $return = array('result' => 'failed', 'file' => '', 'error' => $this->upload->display_errors());
            return $return;
        }
    }

    // Fungsi untuk menyimpan data ke database
    public function save($data) {
        $this->db->insert($this->tabel, $data);

        return TRUE;
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update($this->tabel, $data);

        return TRUE;
    }

    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->tabel);
        if ($this->db->affected_rows() == 1) {

            return TRUE;
        }
        return FALSE;
    }
}

