<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_prk_master extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function listDepartemen() {
        return $this->db->order_by('urutan', 'ASC')->get('prk_departemen')->result();
    }

    function listDivisi($id_departemen = NULL) {
        $this->db->order_by('urutan', 'ASC');
        if (!empty($id_departemen)) {
            $this->db->where('id_departemen', $id_departemen);
        }
        return $this->db->get('prk_divisi')->result();
    }

    function getDivisi($id_divisi) {
        return $this->db->where('id', $id_divisi)->get('prk_divisi')->row();
    }

    function listDivisiDenganDepartemen() {
        $this->db
            ->select('prk_divisi.*, prk_departemen.nama as nama_departemen, prk_departemen.kode as kode_departemen')
            ->from('prk_divisi')
            ->join('prk_departemen', 'prk_departemen.id = prk_divisi.id_departemen')
            ->order_by('prk_departemen.urutan', 'ASC')
            ->order_by('prk_divisi.urutan', 'ASC');
        return $this->db->get()->result();
    }

    function getKuota($id_divisi) {
        return $this->db->where('id_divisi', $id_divisi)->get('prk_kuota')->row();
    }

    function setKuota($id_divisi, $kuota_maks, $diubah_oleh) {
        $existing = $this->getKuota($id_divisi);
        $data = array(
            'kuota_maks' => $kuota_maks,
            'diubah_oleh' => $diubah_oleh,
            'diubah_pada' => date('Y-m-d H:i:s'),
        );
        if ($existing) {
            $this->db->where('id_divisi', $id_divisi)->update('prk_kuota', $data);
        } else {
            $data['id_divisi'] = $id_divisi;
            $this->db->insert('prk_kuota', $data);
        }
    }

    function getPengaturan($kunci, $default = NULL) {
        $row = $this->db->where('kunci', $kunci)->get('prk_pengaturan')->row();
        return $row ? $row->nilai : $default;
    }

    function setPengaturan($kunci, $nilai, $diubah_oleh) {
        $data = array(
            'nilai' => $nilai,
            'diubah_oleh' => $diubah_oleh,
            'diubah_pada' => date('Y-m-d H:i:s'),
        );
        $existing = $this->db->where('kunci', $kunci)->get('prk_pengaturan')->row();
        if ($existing) {
            $this->db->where('kunci', $kunci)->update('prk_pengaturan', $data);
        } else {
            $data['kunci'] = $kunci;
            $this->db->insert('prk_pengaturan', $data);
        }
    }

    function getIdDepartemenUntukDepag($id_depag) {
        $row = $this->db->where('id_depag', $id_depag)->get('prk_depag_map')->row();
        return $row ? $row->id_departemen : NULL;
    }
}
