<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role_id') != "1") {
            redirect("/administrator/login");
        }
    }
}

/* End of file Admin_Controller.php */
/* Location: ./application/core/Admin_Controller.php */