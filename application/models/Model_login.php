<?php

class Model_login extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // Login lewat email saja (Query Builder, aman dari SQL injection). Password dicocokkan
    // di cekPassword() karena bisa plaintext lama (kolom belum pernah di-hash) atau hash
    // bcrypt baru -- password lama otomatis di-upgrade ke hash begitu login berhasil.
    function getlogin($username, $password) {
        $tgl_ini = date("Y-m-d");
        $this->db->set('status', 'Inactive')
            ->where('status', 'Accept')
            ->where('tgl_selesai <', $tgl_ini)
            ->update('pkl_data');

        $user = $this->db->where('email', $username)->get('user')->row();
        if (!$user) {
            return NULL;
        }

        if ($this->cekPassword($password, $user->password)) {
            if (strpos($user->password, '$2y$') !== 0) {
                $this->updatelogin(array('password' => password_hash($password, PASSWORD_DEFAULT)), $user->id);
            }
            return $user;
        }
        return NULL;
    }

    // Hash bcrypt ($2y$...) dicek via password_verify(); kalau bukan hash, berarti akun
    // lama yang passwordnya masih plaintext -- cocokkan langsung sekali, lalu di-upgrade.
    private function cekPassword($input, $stored) {
        if (strpos($stored, '$2y$') === 0) {
            return password_verify($input, $stored);
        }
        return hash_equals($stored, $input);
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
