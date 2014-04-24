<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->helper('password');
        }

    /**
     *
     * create_member
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @return mixed
     *
     */

    public function create_member($username, $password, $email, $first_name, $last_name, $role_id = 2, $active = 0) {

        $nonce = md5(uniqid(mt_rand(), true));

        $data = array(
            'username' => $username,
            'password' => hash_password($password, $nonce),
            'email' => $email,
            'nonce' => $nonce,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'role_id' => $role_id,
            'active' => $active
        );

        $this->db->set('date_registered', 'NOW()', FALSE);
        $this->db->set('last_login', 'NOW()', FALSE);
        $this->db->insert('user', $data);
        
        if ($this->db->affected_rows() == 1) {
            return $nonce;
        }
        return false;
    }


}

/* End of file register_model.php */
/* Location: ./application/models/membership/register_model.php */