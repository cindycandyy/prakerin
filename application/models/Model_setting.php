<?php

class Model_setting extends CI_Model {

    var $tabel = 'setting';

    function __construct() {
        parent::__construct();
    }

    function viewalldata() {
        $sql = "SELECT * FROM ".$this->tabel;
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    function getCountSetting() {
        $sql = "SELECT COUNT(*) as jml FROM ".$this->tabel;
        $query = $this->db->query($sql);
        $data = $query->result();
        return $data[0]->jml;
    }

    public function getSingleSetting() {
        $sql = "SELECT * FROM ".$this->tabel;
//        echo $sql;
        $query = $this->db->query($sql);

        $data = $query->result();

        return $data[0];
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
