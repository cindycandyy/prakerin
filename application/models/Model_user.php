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
        $this->db->from('user')->where('true', null, false);
        if (!empty($id)) {
            $this->db->where('id', $id);
        }
        $data = $this->db->get()->result();
        return isset($data[0]) ? $data[0] : null;
    }

    public function getSingleUser($id) {
        $this->db->from('user')->where('true', null, false);
        if (!empty($id)) {
            $this->db->where('id', $id);
        }
        return $this->db->get()->result();
    }

    public function getCountTypeUSer($id) {
        $this->db->select('COUNT(*) AS total', false)->from('user')->where('true', null, false);
        if (!empty($id)) {
            $this->db->where('type', $id);
        }
        $temp = $this->db->get()->row();
        return $temp ? (INT) $temp->total : 0;
    }

    public function getCountEmailUSer($email) {
        $this->db->select('COUNT(*) AS total', false)->from('user')->where('true', null, false);
        if (!empty($email)) {
            $this->db->where('email', $email);
        }
        $temp = $this->db->get()->row();
        return $temp ? (INT) $temp->total : 0;
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

