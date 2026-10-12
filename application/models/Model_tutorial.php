<?php

class Model_tutorial extends CI_Model {

    var $tabel = 'tutor';

    function __construct() {
        parent::__construct();
    }

    function viewalldata() {
        $sql = "SELECT * FROM tutor where id=1";
        $query = $this->db->query($sql);
        return $query->result();
    }
    function viewalldata2() {
        $sql = "SELECT * FROM tutor where id=2";
        $query = $this->db->query($sql);
        return $query->result();
    }
    function vieww() {
        $sql = "SELECT description FROM tutor where id=1";
        $query = $this->db->query($sql);
        return $query->fetch_assoc();
    }
    // Fungsi untuk menyimpan data ke database
    public function save($data) {
        $this->db->insert($this->tabel, $data);

        return TRUE;
    }

    public function update($id,$data) {
         $this->db->where('id', $id);
        $this->db->update($this->tabel, $data);
        return TRUE;
    }


}
