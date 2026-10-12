<?php

class Model_type_user extends CI_Model {

    var $tabel = 'type_user';

    function __construct() {
        parent::__construct();
    }

    function viewalldata() {
        $sql = "SELECT * FROM type_user";
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    function getFieldDepag($id) {
        $sql = "SELECT type_user FROM type_user where id = '" . $id . "'";
        $query = $this->db->query($sql);
        $data = $query->result();
        return $data[0]->type_user;
    }

    public function getSingleTypeUser($id) {
        $filter = "";
        if (!empty($id)) {
            $filter .= " and id = '$id'";
        }

        $sql = "SELECT * FROM type_user WHERE true " . $filter . " ";
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
