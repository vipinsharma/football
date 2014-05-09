<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Private_Controller extends MY_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->load->helper('cookie');
        if(Settings_model::$db_config['login_enabled'] == 0 && $this->session->userdata('username') != ADMINISTRATOR) {
            $this->session->sess_destroy();
            redirect("/membership/login");
        }elseif(!$this->session->userdata('logged_in') && get_cookie('unique_token') != "") {
            $this->load->model('system/set_cookies_model');
            $data = $this->set_cookies_model->load_session_vars(get_cookie('unique_token'));
            if (!empty($data)) {
                setcookie("unique_token", get_cookie('unique_token'), time() + Settings_model::$db_config['cookie_expires'], '/', $_SERVER['SERVER_NAME'], false, false);
                $this->session->set_userdata('logged_in', true);
                $this->session->set_userdata('username', $data['username']);
                $this->session->set_userdata('role_id', $data['role_id']);
                redirect("/welcome/login");
            }
        }elseif(!$this->session->userdata('logged_in') && !get_cookie('unique_token')) {
            redirect("/welcome/login");
        }
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
        $this->output->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }

}

/* End of file Private_Controller.php */
/* Location: ./application/core/Private_Controller.php */
