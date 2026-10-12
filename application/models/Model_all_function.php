<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_all_function extends CI_Model {
    function getTypeUser($id_type) {
        $sql = "SELECT type_user FROM type_user where id = '" . $id_type . "'";
        $query = $this->db->query($sql);
        $data = $query->result();
        return $data[0]->type_user;
    }
    //membuat id user
    function customidCreator($table, $field, $text) {
        $sql = "SELECT ".$field." FROM ".$table." ORDER BY ". $field ." DESC";
        $query = $this->db->query($sql);
        $arr = $query->row();
        
        $pisah = explode($text,$arr->$field);
            $num   = $pisah[1]+1;
            if ($num < 10) {
                $user_id = $text.'00000' . $num;
            } else if ($num >= 10 && $num < 100) {
                $user_id = $text.'000' . $num;
            } else if ($num >= 100 && $num < 1000) {
                $user_id = $text.'00' . $num;
            } else if ($num >= 1000 && $num < 10000) {
                $user_id = $text.'0' . $num;
            }
        return $user_id;
    }
    
    function customNoDoc($table, $field, $text, $jml_nol) {
        $sql = "SELECT ".$field." FROM ".$table." ORDER BY ". $field ." DESC";
        $query = $this->db->query($sql);
        $arr = $query->row();
        
        $tahun = date("Y");
        $pisah = explode("/",$arr->$field);
        
            $th    = $pisah[3];
            if($tahun>$th){
                $num   = 1;
            }else{
                $num   = $pisah[0]+1;
            }
            
            if ($num < 10) {
                $user_id = '00' . $num . '/' . $this->num2RomBulan(date("m")) .'/' . $text .  '/' . date("Y");
            } else if ($num > 10 && $num < 100) {
                $user_id = '0' . $num . '/' . $this->num2RomBulan(date("m")) .'/' . $text .  '/' . date("Y");
            }else {
                $user_id = $num . '/' . $this->num2RomBulan(date("m")) .'/' . $text .  '/' . date("Y");
            }
        return $user_id;
    }

    function sql2date($date){
        $data = explode('-',$date);
        $new_date = $data[2].'-'.$data[1].'-'.$data[0];
        return $new_date;
    }

    function date2sql($date){
        $data = explode('-',$date);
        $new_date = $data[2].'-'.$data[1].'-'.$data[0];
        return $new_date;
    }

    function num2RomBulan($angka){
               if($angka==1){
            $bulan = "I";
        } else if($angka==2){
            $bulan = "II";
        } else if($angka==3){
            $bulan = "III";
        } else if($angka==4){
            $bulan = "IV";
        } else if($angka==5){
            $bulan = "V";
        } else if($angka==6){
            $bulan = "VI";
        } else if($angka==7){
            $bulan = "VII";
        } else if($angka==8){
            $bulan = "VIII";
        } else if($angka==9){
            $bulan = "IX";
        } else if($angka==10){
            $bulan = "X";
        } else if($angka==11){
            $bulan = "XI";
        } else if($angka==12){
            $bulan = "XII";
        }
        return $bulan;
    }
    
    function num2Bulan($angka){
               if($angka==1){
            $bulan = "Januari";
        } else if($angka==2){
            $bulan = "Februari";
        } else if($angka==3){
            $bulan = "Maret";
        } else if($angka==4){
            $bulan = "April";
        } else if($angka==5){
            $bulan = "Mei";
        } else if($angka==6){
            $bulan = "Juni";
        } else if($angka==7){
            $bulan = "Juli";
        } else if($angka==8){
            $bulan = "Agustus";
        } else if($angka==9){
            $bulan = "September";
        } else if($angka==10){
            $bulan = "Oktober";
        } else if($angka==11){
            $bulan = "November";
        } else if($angka==12){
            $bulan = "Desember";
        }
        return $bulan;
    }

}

?>