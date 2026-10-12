<?php
class Model_persyaratan extends CI_Model{

    var $tabel = 'pengumuman';
    
    function __construct() {
        parent::__construct();
    }
    
    function viewalldata() {
        $sql = "SELECT * FROM pengumuman";
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    public function getSingleRowData($id, $field_where = NULL) {
        $filter = "";
        if (!empty($id) && empty($field_where)) {
            $filter .= " and id = '" . $id . "'";
        }else if (!empty($id) && !empty($field_where)) {
            $filter .= " and ".$field_where." = '" . $id . "'";
        }

        $sql = "SELECT * FROM pengumuman WHERE true " . $filter;

        $query = $this->db->query($sql);

        $data = $query->result();

        return $data[0];
    }
    
    public function getSingleData($id, $field_where = NULL) {
        $filter = "";
        if (!empty($id) && empty($field_where)) {
            $filter .= " and id = '" . $id . "'";
        }else if (!empty($id) && !empty($field_where)) {
            $filter .= " and ".$field_where." = '" . $id . "'";
        }

        $sql = "SELECT * FROM pengumuman WHERE true " . $filter;

        $query = $this->db->query($sql);

        $data = $query->result();
//        $data = array($sql);

        return $data;
    }
    
    public function getCountPengumuman($id) {
        $filter = "";
        if (!empty($id)) {
         //   $filter .= " and id_depag= '$id'";
        }
        $sql = "Select COUNT(*) AS total from pengumuman where true " . $filter . " ";
        $query = $this->db->query($sql);
        $temp = $query->row();
        if ($temp) {
            return (INT) $temp->total;
        } else {
            return 0;
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

