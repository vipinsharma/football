<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_cookies_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     *
     * load_session_vars: load the session variables from the DB
     *
     * @param string $nonce use the member nonce to load session vars
     * @return mixed
     *
     */

    public function load_session_vars($nonce) {
        $this->db->select('username, role_id');
        $this->db->from('user');
        $this->db->where('nonce', $nonce);
        $this->db->limit(1);

        $query = $this->db->get();

        if($query->num_rows() == 1) {
            $row = $query->row();
            $data['username'] = $row->username;
            $data['role_id'] = $row->role_id;
            return $data;
        }
        return false;
    }
}

/* End of file set_cookies_model.php */
///* Location: ./application/models/system/set_cookies_model.php */