<?php

class Model_depag extends CI_Model {

    var $tabel = 'depag';

    function __construct() {
        parent::__construct();
    }

    function viewalldata() {
        $sql = "SELECT * FROM depag where kuota > 0";
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    function getFieldDepag($id) {
        $sql = "SELECT nama_depag FROM depag where id = '" . $id . "'";
        $query = $this->db->query($sql);
        $data = $query->result();
        return $data[0]->nama_depag;
    }
    function getFieldDepag2($id) {
        $sql = "SELECT deskripsi FROM depag where id = '" . $id . "'";
        $query = $this->db->query($sql);
        $data = $query->result();
        return $data[0]->deskripsi;
    }
    function getKuotaDepag($id) {
        $sql = "SELECT kuota FROM depag where id = '" . $id . "'";
        $query = $this->db->query($sql);
        $data = $query->result();
        return $data[0]->kuota;
    }
    function getJmlfield($type, $field, $where = NULL, $key = NULL) {
        $filter = " ";
        if (!empty($where)) {
            $filter .= " where ".$where." = '$key'";
        }
        
        if ($type==1){
            $p = "COUNT";
        } else if ($type==2){
            $p = "SUM";
        }
        $sql = "SELECT ".$p."(".$field.") as jumlah FROM depag".$filter;
        
        $query = $this->db->query($sql);
        $data = $query->result();

        return $data[0]->jumlah;
    }
    
    function getSubJmlDepag($id, $tahun, $bulan) {
        
        $sql = "SELECT tgl_mulai, tgl_selesai FROM pkl_data WHERE `status` = 'Accept' AND `id_depag` = '" . $id . "' and tgl_mulai like '".($tahun-1)."%'";

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
            
            if($year_end == ($tahun)){
                $start = "01";
                $a = 0;
                while ($start <= $end) {
                    $array[$a] = array($start);
                    $start = "0".($start+1);
                    $a++;
                } 
                $ODArray = array_map('current', $array);
                if(in_array($bulan, $ODArray)){
                    $jml = $jml+1;
                }
                unset($array);
            }
        }
            return $jml;
    }
    
    function getJmlDepag($id, $tahun, $bulan) {
        
        $Subjml = $this->getSubJmlDepag($id, $tahun, $bulan);
        $sql = "SELECT tgl_mulai, tgl_selesai FROM pkl_data WHERE `status` = 'Accept' AND `id_depag` = '" . $id . "' and tgl_mulai like '".$tahun."%'";

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
            if(@in_array($bulan, $ODArray)){
                $jml = $jml+1;
            }
            unset($array);
        }
        return ($Subjml+$jml);
    }

    public function getSingleDepag($id) {
        $filter = "";
        if (!empty($id)) {
            $filter .= " and id = '$id'";
        }

        $sql = "SELECT * FROM depag WHERE true " . $filter . " ";
//        echo $sql;
        $query = $this->db->query($sql);

        $data = $query->result();

        return $data[0];
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
