<?php
class Model_transaksi extends CI_Model{

    var $tabel = 'pkl_data';
    
    function __construct() {
        parent::__construct();
    }
    
    function viewalldata() {
        $sql = "SELECT * FROM pkl_data where tgl_selesai != date(now()) order by tgl_mulai desc";
        $query = $this->db->query($sql);
        return $query->result();
    }
	
	/*data pkl pengajuan*/
    function viewalldata_st($st) {
        $sql = "SELECT * FROM pkl_data where status like '%$st%' order by tgl_mulai desc";//tgl_selesai >= date(now()) and 
        $query = $this->db->query($sql);
        return $query->result();
    }
	//menghitung jumlah pending
	function getCountPending($ses_user_id) {
        $sql = "SELECT count(status) as jumlah FROM pkl_data where status='pending' and user_id='$ses_user_id' order by tgl_mulai desc";
        $query = $this->db->query($sql);
        return $query->result();
    }
	//menghitung jumlah data magang
	function getCountMagang($ses_user_id) {
        $sql = "SELECT COUNT(*) as jumlah FROM pkl_data where idkey='$ses_user_id'";
        $query = $this->db->query($sql);
        $temp = $query->row();
        if ($temp) {
            return (INT) $temp->jumlah;
        } else {
            return 0;
        }
    }
    function getCountfield($field, $where = NULL, $key = NULL) {
        $filter = "";
        if (!empty($where)) {
            $filter .= " where ".$where." = '$key'";
        }
        
        $sql = "SELECT COUNT(".$field.") as jumlah FROM pkl_data".$filter;
        $query = $this->db->query($sql);
        $data = $query->result();

        return $data[0]->jumlah;
    }
    
    function getSubJmlfield($where = NULL, $key = NULL, $year = NULL, $month = NULL) {
        $filter = "";
        if (!empty($where)) {
            $filter .= " where ".$where." = '$key'";
        }
        if (empty($where) && !empty($year)) {
            $filter .= " where tgl_mulai like '".($year-1)."%'";
        }
        if (!empty($where) && !empty($year)) {
            $filter .= " and tgl_mulai like '".($year-1)."%'";
        }
        
        $sql = "SELECT tgl_mulai, tgl_selesai FROM pkl_data".$filter;
        $query = $this->db->query($sql);
        $data = $query->result();
        
        $jml = 0;
        $array = array();
        foreach ($data as $key => $rowdata){
            $pisah_start = explode("-",$rowdata->tgl_mulai);
            $pisah_end   = explode("-",$rowdata->tgl_selesai);
            
            $year_start = $pisah_start[0];
            $year_end   = $pisah_end[0];
            
            $start = $pisah_start[1];
            $end   = $pisah_end[1];
            
            if($year_end == ($year)){
                $start = "01";
                $a = 0;
                while ($start <= $end) {
                    $array[$a] = array($start);
                    $start = "0".($start+1);
                    $a++;
                } 
                $ODArray = array_map('current', $array);
                if(in_array($month, $ODArray)){
                    $jml = $jml+1;
                }
                unset($array);
            }
        }
            return $jml;
    }
    
    function getJmlfield($where = NULL, $key = NULL, $year = NULL, $month = NULL) {
        
        $Subjml = $this->getSubJmlfield($where, $key, $year, $month);
        
        $filter = "";
        if (!empty($where)) {
            $filter .= " where ".$where." = '$key'";
        }
        if (empty($where) && !empty($year)) {
            $filter .= " where tgl_mulai >= '".$year."-01-01'";
        }
        if (!empty($where) && !empty($year)) {
            $filter .= " and tgl_mulai >= '".$year."-01-01'";
        }
        
        $sql = "SELECT tgl_mulai, tgl_selesai FROM pkl_data".$filter;
        $query = $this->db->query($sql);
        $data = $query->result();
        
        $jml = 0;
        foreach ($data as $key => $rowdata){
            $pisah_start = explode("-",$rowdata->tgl_mulai);
            $pisah_end   = explode("-",$rowdata->tgl_selesai);
            
            $year_start = $pisah_start[0];
            $year_end   = $pisah_end[0];
            
            $start = $pisah_start[1];
            if($year_end>$year_start){
                $end   = 12;
            }else{
                $end   = $pisah_end[1];
            }
            
            $a = 0;
            while ($start <= $end) {
                $array[$a] = array($start);
                $start = "0".($start+1);
                $a++;
            } 
            @$ODArray = array_map('current', $array);
            if(@in_array($month, $ODArray)){
                $jml = $jml+1;
            }
            unset($array);
        }
        return ($Subjml+$jml);
    }
    
    public function getSingleRowData($id, $field_where = NULL) {
        $filter = "";
        if (!empty($id) && empty($field_where)) {
            $filter .= " and id = '" . $id . "'";
        }else if (!empty($id) && !empty($field_where)) {
            $filter .= " and ".$field_where." = '" . $id . "'";
        }

        $sql = "SELECT * FROM pkl_data WHERE true " . $filter;

        $query = $this->db->query($sql);

        $data = $query->result();
//        $data = array($sql);

        return $data[0];
    }
    
    public function getSingleData($id, $field_where = NULL) {
        $filter = "";
        if (!empty($id) && empty($field_where)) {
            $filter .= " and id = '" . $id . "'";
        }else if (!empty($id) && !empty($field_where)) {
            $filter .= " and ".$field_where." = '" . $id . "'";
        }

        $sql = "SELECT * FROM pkl_data WHERE true " . $filter;

        $query = $this->db->query($sql);

        $data = $query->result();
//        $data = array($sql);

        return $data;
    }
    public function getSingleDatax($id, $field_where = NULL,$status = NULL) {
        $filter = "";
        if (!empty($id) && empty($field_where)) {
            $filter .= " and id = '" . $id . "'";
        }else if (!empty($id) && !empty($field_where)) {
            $filter .= " and ".$field_where." = '" . $id . "'";
        }

        $sql = "SELECT * FROM pkl_data WHERE status='$status' " . $filter;

        $query = $this->db->query($sql);

        $data = $query->result();
//        $data = array($sql);

        return $data;
    }
    public function getCountDepag($id) {
        $filter = "";
        if (!empty($id)) {
            $filter .= " and id_depag= '$id'";
        }
        $sql = "Select COUNT(*) AS total from pkl_data where true " . $filter . " ";
        $query = $this->db->query($sql);
        $temp = $query->row();
        if ($temp) {
            return (INT) $temp->total;
        } else {
            return 0;
        }
    }
    
    public function getCountUserId($id) {
        $filter = "";
        if (!empty($id)) {
            $filter .= " and user_id= '$id'";
        }
        $sql = "Select COUNT(*) AS total from pkl_data where true " . $filter . " ";
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
        $config['allowed_types'] = 'jpg|png|jpeg|pdf';
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

