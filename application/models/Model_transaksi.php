<?php
class Model_transaksi extends CI_Model{

    var $tabel = 'pkl_data';

    function __construct() {
        parent::__construct();
    }

    function viewalldata() {
        return $this->db->where('tgl_selesai !=', date('Y-m-d'))
            ->order_by('tgl_mulai', 'desc')
            ->get('pkl_data')->result();
    }

	/*data pkl pengajuan*/
    function viewalldata_st($st) {
        return $this->db->like('status', $st)
            ->order_by('tgl_mulai', 'desc')
            ->get('pkl_data')->result();
    }
	//menghitung jumlah pending
	function getCountPending($ses_user_id) {
        return $this->db->select('COUNT(status) as jumlah', false)
            ->where('status', 'pending')
            ->where('user_id', $ses_user_id)
            ->order_by('tgl_mulai', 'desc')
            ->get('pkl_data')->result();
    }
	//menghitung jumlah data magang
	function getCountMagang($ses_user_id) {
        $temp = $this->db->select('COUNT(*) as jumlah', false)
            ->where('idkey', $ses_user_id)
            ->get('pkl_data')->row();
        if ($temp) {
            return (INT) $temp->jumlah;
        } else {
            return 0;
        }
    }
    function getCountfield($field, $where = NULL, $key = NULL) {
        $this->db->select('COUNT(' . $field . ') as jumlah', false)->from('pkl_data');
        if (!empty($where)) {
            $this->db->where($where, $key);
        }
        $data = $this->db->get()->result();
        return $data[0]->jumlah;
    }

    function getSubJmlfield($where = NULL, $key = NULL, $year = NULL, $month = NULL) {
        $this->db->select('tgl_mulai, tgl_selesai')->from('pkl_data');
        if (!empty($where)) {
            $this->db->where($where, $key);
        }
        if (empty($where) && !empty($year)) {
            $this->db->like('tgl_mulai', ($year - 1), 'after');
        }
        if (!empty($where) && !empty($year)) {
            $this->db->like('tgl_mulai', ($year - 1), 'after');
        }
        $data = $this->db->get()->result();

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

        $this->db->select('tgl_mulai, tgl_selesai')->from('pkl_data');
        if (!empty($where)) {
            $this->db->where($where, $key);
        }
        if (empty($where) && !empty($year)) {
            $this->db->where('tgl_mulai >=', $year . '-01-01');
        }
        if (!empty($where) && !empty($year)) {
            $this->db->where('tgl_mulai >=', $year . '-01-01');
        }
        $data = $this->db->get()->result();

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
        $this->db->from('pkl_data')->where('true', null, false);
        if (!empty($id)) {
            $this->db->where($field_where ?: 'id', $id);
        }
        $data = $this->db->get()->result();
        return isset($data[0]) ? $data[0] : null;
    }

    public function getSingleData($id, $field_where = NULL) {
        $this->db->from('pkl_data')->where('true', null, false);
        if (!empty($id)) {
            $this->db->where($field_where ?: 'id', $id);
        }
        return $this->db->get()->result();
    }

    public function getSingleDatax($id, $field_where = NULL, $status = NULL) {
        $this->db->from('pkl_data')->where('status', $status);
        if (!empty($id)) {
            $this->db->where($field_where ?: 'id', $id);
        }
        return $this->db->get()->result();
    }

    public function getCountDepag($id) {
        $this->db->select('COUNT(*) AS total', false)->from('pkl_data')->where('true', null, false);
        if (!empty($id)) {
            $this->db->where('id_depag', $id);
        }
        $temp = $this->db->get()->row();
        return $temp ? (INT) $temp->total : 0;
    }

    public function getCountUserId($id) {
        $this->db->select('COUNT(*) AS total', false)->from('pkl_data')->where('true', null, false);
        if (!empty($id)) {
            $this->db->where('user_id', $id);
        }
        $temp = $this->db->get()->row();
        return $temp ? (INT) $temp->total : 0;
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
