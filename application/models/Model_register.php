<?php

class Model_register extends CI_Model {

    var $tabel = 'user';

    function __construct() {
        parent::__construct();
    }

    // Fungsi untuk menyimpan data ke database
    public function save($data) {
        $this->db->insert($this->tabel, $data);

        return TRUE;
    }

}
