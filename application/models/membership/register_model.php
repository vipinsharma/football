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

    public function check_mail($email){
	$this->db->select('id');
        $this->db->from('user');
        $this->db->where('email', strtolower($email));
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1) {
            return  1;
        }
        return 0;
    }

    /**
     *
     * activate_member
     *
     * @param string $email e-mail addres of member
     * @param string $nonce the member nonce
     * @return boolean
     *
     */

    public function activate_member($email, $nonce) {
        $data = array('active' => 1);
        $where = array('email' => $email, 'nonce' => $nonce, 'unix_timestamp(NOW()) - unix_timestamp(last_login) <' => Settings_model::$db_config['activation_link_expires']);
        $this->db->where($where);
        $this->db->update('user', $data);
        if($this->db->affected_rows() == 1) {
            return true;
        }
        return false;
    }

}

/* End of file register_model.php */
/* Location: ./application/models/membership/register_model.php */
