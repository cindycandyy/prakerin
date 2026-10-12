<?php

class Model_login extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getlogin($username, $password) {
              $tgl_ini = date("Y-m-d");
  $sq = "UPDATE pkl_data set status='Inactive' WHERE '$tgl_ini' > tgl_selesai and status='Accept'";
        $query_2 = $this->db->query($sq);
        
        $sql = "select * from user where email = '" . $username . "' and password = '" . $password . "'";
        $query = $this->db->query($sql);
        return $query->row();
  
      
    }

    function updatelogin($data, $id) {
        $this->db->where('id', $id)->update("user", $data);
        if ($this->db->affected_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
