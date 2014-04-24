<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends Membership_Controller {

    public function __construct()
    {
        parent::__construct();
        // pre-load
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->library('recaptcha');
        $this->lang->load('recaptcha');
    }

    /*
     * Index : default methode 
     *
    */
    function index() {
        $data['title']='Welcome';
        $this->load->view('welcome',$data);
    }

    /**
     *
     * validate: validate login after input fields have met requirements
     *
     *
     */
    public function validate() {

        if (Settings_model::$db_config['disable_all'] == 1 && $this->input->post('username') != ADMINISTRATOR) {
            $this->session->set_flashdata('message', $this->lang->line('site_disabled'));
            redirect('/membership/login');
            exit();
        }
        // form input validation
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('username', 'username', 'trim|required|max_length[16]');
        $this->form_validation->set_rules('password', 'password', 'trim|required|max_length[64]');
        if ($this->session->userdata('login_attempts') > 5) {
            $this->form_validation->set_rules('recaptcha_response_field', 'captcha response field', 'required|check_captcha');
        }

        if (!$this->form_validation->run())
        {
            if (form_error('username')) {
                $this->session->set_flashdata('message', form_error('username'));
            }elseif (form_error('password')) {
                $this->session->set_flashdata('message', form_error('password'));
            }elseif (form_error('recaptcha_response_field')) {
                $this->session->set_flashdata('message', $this->lang->line('check_captcha'));
            }
            redirect('/membership/login');
            exit();
        }

        // database validation
        $this->load->model('membership/login_model');
        $data = $this->login_model->validate_login($this->input->post('username'), $this->input->post('password'));
        if($data == "banned") {
            $this->session->set_flashdata('message', $this->lang->line('access_denied'));
            redirect('/membership/login');
        }elseif (is_array($data)) {
            if($data['active'] == 0) {
                $this->session->set_flashdata('message', $this->lang->line('activate_account'));
                redirect('/membership/login');
            }else{
                $this->load->helper('cookie');
                if ($this->input->post('remember_me') && !get_cookie('unique_token')) {
                    setcookie("unique_token", $data['nonce'], time() + Settings_model::$db_config['cookie_expires'], '/', $_SERVER['SERVER_NAME'], false, false);
                }

                // set session data
                $this->session->set_userdata('logged_in', true);
                $this->session->set_userdata('username', $data['username']);
                $this->session->set_userdata('role_id', $data['role_id']);
                // reset login attempts to 0
                $this->login_model->reset_login_attempts($data['username']);
                $this->session->unset_userdata('login_attempts');
                redirect('private/'. Settings_model::$db_config['home_page']);
            }
        }else{
            $this->session->set_flashdata('message', $this->lang->line('login_incorrect'));
            $this->session->set_userdata('login_attempts', $data);
            redirect('/membership/login');
        }
    }

     /**
     *
     * ajax_validate: validate login after input fields have met requirements
     *
     *
     */
    public function ajax_validate() {
        
        if (Settings_model::$db_config['disable_all'] == 1 && $this->input->post('username') != ADMINISTRATOR) {
            echo $this->lang->line('site_disabled');
        }
        // form input validation
        $username= $_POST['email'];
        $password= $_POST['password'];
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('email', 'username', 'trim|required|max_length[16]|min_length[3]');
        $this->form_validation->set_rules('password', 'password', 'trim|required|max_length[64]|min_length[6]');

        /*if ($this->session->userdata('login_attempts') > 5) {
            $this->form_validation->set_rules('recaptcha_response_field', 'captcha response field', 'required|check_captcha');
        }*/

        if (!$this->form_validation->run())
        {
            if (form_error('username')) {
                echo form_error('username');
            }elseif (form_error('password')) {
                echo form_error('password');
            }
        }

        // database validation
        $this->load->model('membership/login_model');
        $data = $this->login_model->validate_login($username, $password);
        if($data == "banned") {
            echo $this->lang->line('access_denied');
        }elseif (is_array($data)) {
            if($data['active'] == 0) {
                echo $this->lang->line('activate_account');
            }else{
                $this->load->helper('cookie');
                if ($_POST['remember'] && !get_cookie('unique_token')) {
                    setcookie("unique_token", $data['nonce'], time() + Settings_model::$db_config['cookie_expires'], '/', $_SERVER['SERVER_NAME'], false, false);
                }

                // set session data
                $this->session->set_userdata('logged_in', true);
                $this->session->set_userdata('username', $data['username']);
                $this->session->set_userdata('role_id', $data['role_id']);
                // reset login attempts to 0
                $this->login_model->reset_login_attempts($data['username']);
                $this->session->unset_userdata('login_attempts');
                echo 1;
            }
        }else{
            $this->session->set_userdata('login_attempts', $data);
            echo $this->lang->line('login_incorrect');
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */