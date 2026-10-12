<?php
class Model_forget extends CI_Model{

    var $tabel = 'user';
    
    function __construct() {
        parent::__construct();
    }
    
    public function update($email, $data) {
        
        $this->db->where('email', $email);
        $this->db->update($this->tabel, $data);

        return TRUE;
    }

}

