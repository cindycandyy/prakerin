<?php
class Model_laporan extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
    public function reportRekapPkl($tahun = NULL) {
        $filter = " ";
        if (!empty($tahun)) {
            //$filter .= " WHERE a.tgl_mulai >= '".$tahun."-01-01' AND a.tgl_selesai <= '".$tahun."-12-31'";
            $filter .= " WHERE a.tgl_mulai >= '".$tahun."-01-01'";
        }
        
        $sql = "SELECT a.*, b.`nama_depag`, c.`name` FROM pkl_data a INNER JOIN depag b ON(a.`id_depag`=b.`id`) INNER JOIN `user` c ON (a.`user_id`=c.`user_id`)".$filter;

        $query = $this->db->query($sql);

        $data = $query->result();

        return $data;
    }
}