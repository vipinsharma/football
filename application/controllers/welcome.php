<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends Membership_Controller {

    public function __construct()
    {
        parent::__construct();
        // pre-load
        $this->load->helper(array('form','send_email'));
        $this->load->library('form_validation');
        $this->load->model('membership/login_model');
        $this->load->helper();

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
            redirect('/welcome/login');
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
            redirect('/welcome/login');
            exit();
        }

        // database validation
        $data = $this->login_model->validate_login($this->input->post('username'), $this->input->post('password'));
        if($data == "banned") {
            $this->session->set_flashdata('message', $this->lang->line('access_denied'));
            redirect('/welcome/login');
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

        $data = $this->login_model->validate_login($username, $password);
        if($data == "banned") {
            echo $this->lang->line('access_denied');
        }elseif (is_array($data)) {
            if($data['active'] == 0) {
                echo $this->lang->line('activate_account');
            }else{
                $this->load->helper('cookie');
                if (isset($_POST['remember']) && !get_cookie('unique_token')) {
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

    /**
      * forget_password : Load the view for retrive password
      *
      */

    public function forget_password(){
        $content_data['title'] = "Forget Password";
        $this->load->view('forgetpassword',$content_data);
    }

    /**
     *
     * ajax_send_password: send the reset member password link
     *
     */

    public function ajax_send_password() {
        // form input validation
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('email', 'e-mail', 'trim|required|is_valid_email');

        if (!$this->form_validation->run()) {
            if (form_error('email')) {
                echo form_error('email');
            }
        }

        $this->load->model('database_tools_model');
        $data = $this->database_tools_model->get_data_by_email($this->input->post('email'));
        if (isset($data['active']) && $data['active'] != 1) {
            echo $this->lang->line('is_account_active');
        }elseif (!empty($data['nonce'])) {

            $token = hash_hmac('ripemd160', md5($data['nonce'] . uniqid(mt_rand(), true)), SITE_KEY);
            $this->load->model('membership/forgot_password_model');

            $this->forgot_password_model->delete_tokens_by_email($this->input->post('email'));

            if ($this->forgot_password_model->insert_recover_password_data($data['id'], $token, $this->input->post('email'))) {

            $to = $this->input->post('email');
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
            $headers = 'From: mgftest@mygridironface.com' . "\r\n" .
                        'Reply-To: mgftest@mygridironface.com' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
            $subject = $this->lang->line('forgot_password_subject');
            $message = $this->lang->line('email_greeting') ." ". $data['username'] . $this->lang->line('forgot_password_message') ."\r\n\r\n". base_url() ."reset_password/reset/". urlencode($this->input->post('email')) ."/". $token;
            mail($to,$subject,$message,$headers);
                /*$this->load->library('email', load_email_config('3'));
                $this->email->from(Settings_model::$db_config['admin_email_address'], $_SERVER['HTTP_HOST']);
                $this->email->to($this->input->post('email'));
                $this->email->subject($this->lang->line('forgot_password_subject'));
                $this->email->message($this->lang->line('email_greeting') ." ". $data['username'] . $this->lang->line('forgot_password_message') ."\r\n\r\n". base_url() ."reset_password/reset/". urlencode($this->input->post('email')) ."/". $token);
                $this->email->send();*/
                $this->session->set_flashdata('message', $this->lang->line('forgot_password_success'));
                echo 1;
            }else{
                echo $this->lang->line('forgot_password_failed_db');
            }
        }else{
            echo $this->lang->line('email_not_found');
        }
    }

    /**
     *
     * ajax_add_member: insert a new member into the database via ajax after all input fields have met the requirements
     *
     *
     */
    public function ajax_add_member() {
        // check whether creating member is allowed
        if(Settings_model::$db_config['registration_enabled'] == 0) {
            echo $this->lang->line('registration_disabled');
        }
        $this->load->model('membership/register_model');
        // form input validation
        $this->form_validation->set_error_delimiters('', '');
        
        $this->form_validation->set_rules('email', 'e-mail', 'trim|required|max_length[255]|is_existing_unique_field[user.email]');
        $this->form_validation->set_rules('password', 'password', 'trim|required|max_length[64]|min_length[6]|matches[password1]');
        $this->form_validation->set_rules('password1', 'repeat password', 'trim|required|max_length[64]|min_length[6]');

        if (!$this->form_validation->run()) {
            if (form_error('email')) {
                echo form_error('email'); exit;
            }elseif (form_error('password')) {
                echo form_error('password'); exit;
            }elseif (form_error('password1')) {
                echo form_error('password1'); exit;
            }
        }

        if($nonce = $this->register_model->create_member($this->input->post('email'), $this->input->post('password'), $this->input->post('email'), '', '')) {
            /*echo "<pre>"; print_r(load_email_config('3')); 
            echo "<br/>1".Settings_model::$db_config['admin_email_address'];
            echo "<br/>2".$this->input->post('email');
            echo "<br/>3".$this->lang->line('membership_subject');
            echo "<br/>4".$this->lang->line('email_greeting');
            echo "<br/>5".$this->lang->line('membership_message'); */

            /*$this->load->library('email', load_email_config('3'));
            $this->email->from(Settings_model::$db_config['admin_email_address'], $_SERVER['HTTP_HOST']);
            $this->email->to($this->input->post('email'));
            $this->email->subject($this->lang->line('membership_subject'));
            $this->email->message($this->lang->line('email_greeting') . " ". $this->input->post('email') . $this->lang->line('membership_message'). base_url() ."welcome/activate_membership/". urlencode($this->input->post('email')) ."/". $nonce);
            $this->email->send();*/
            $to = $this->input->post('email');
            $from = Settings_model::$db_config['admin_email_address'];
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
            $headers = 'From: mgftest@mygridironface.com' . "\r\n" .
                        'Reply-To: mgftest@mygridironface.com' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
            $subject = $this->lang->line('membership_subject');
            $message = $this->lang->line('email_greeting') . " ". $this->input->post('email') . $this->lang->line('membership_message'). base_url() ."welcome/activate_membership/". urlencode($this->input->post('email')) ."/". $nonce;
            mail($to,$subject,$message,$headers);
            //echo Settings_model::$db_config['admin_email_address'].$this->input->post('email').$this->lang->line('email_greeting') . " ". $this->input->post('email') . $this->lang->line('membership_message'). base_url() ."welcome/activate_membership/check/". urlencode($this->input->post('email')) ."/". $nonce; die;

            echo 1;
        }else{
            echo $this->lang->line('membership_failed_db');
        }
    }

    /**
     *
     * add_member: insert a new member into the database after all input fields have met the requirements
     *
     *
     */
    public function add_member() {
        // check whether creating member is allowed
        if(Settings_model::$db_config['registration_enabled'] == 0) {
            $this->session->flashdata('message', $this->lang->line('registration_disabled'));
            redirect('/membership/register');
        }
        // form input validation
        $this->form_validation->set_error_delimiters('', '');
        
        $this->form_validation->set_rules('first_name', 'first name', 'trim|required|max_length[40]|min_length[2]');
        $this->form_validation->set_rules('last_name', 'last name', 'trim|required|max_length[60]|min_length[2]');
        $this->form_validation->set_rules('email', 'e-mail', 'trim|required|max_length[255]|is_valid_email|is_existing_unique_field[user.email]');
        $this->form_validation->set_rules('username', 'username', 'trim|required|max_length[16]|min_length[6]|is_valid_username|is_existing_unique_field[user.username]');
        $this->form_validation->set_rules('password', 'password', 'trim|required|max_length[64]|min_length[9]|matches[password_confirm]|is_valid_password');
        $this->form_validation->set_rules('password_confirm', 'repeat password', 'trim|required|max_length[64]|min_length[9]');
        $this->form_validation->set_rules('recaptcha_response_field', 'captcha response field', 'required|check_captcha');

        if (!$this->form_validation->run()) {
            if (form_error('first_name')) {
                $this->session->set_flashdata('message', form_error('first_name'));
            }elseif (form_error('last_name')) {
                $this->session->set_flashdata('message', form_error('last_name'));
            }elseif (form_error('email')) {
                $this->session->set_flashdata('message', form_error('email'));
            }elseif (form_error('username')) {
                $this->session->set_flashdata('message', form_error('username'));
            }elseif (form_error('password')) {
                $this->session->set_flashdata('message', form_error('password'));
            }elseif (form_error('password_confirm')) {
                $this->session->set_flashdata('message', form_error('password_confirm'));
            }elseif(form_error('recaptcha_response_field')) {
                $this->session->set_flashdata('message', form_error('recaptcha_response_field'));
            }

            $data['post'] = $_POST;
            $this->session->set_flashdata($data['post']);
            redirect('/membership/register');
            exit();
        }

        if($nonce = $this->register_model->create_member($this->input->post('username'), $this->input->post('password'), $this->input->post('email'), $this->input->post('first_name'), $this->input->post('last_name'))) {
            $this->load->helper('send_email');
            $this->load->library('email', load_email_config(Settings_model::$db_config['email_protocol']));
            $this->email->from(Settings_model::$db_config['admin_email_address'], $_SERVER['HTTP_HOST']);
            $this->email->to($this->input->post('email'));
            $this->email->subject($this->lang->line('membership_subject'));
            $this->email->message($this->lang->line('email_greeting') . " ". $this->input->post('username') . $this->lang->line('membership_message'). base_url() ."membership/activate_membership/". urlencode($this->input->post('email')) ."/". $nonce);
            $this->email->send();
            $this->session->set_flashdata('message', $this->lang->line('membership_success'));
            redirect('/membership/register');
        }else{
            $this->session->set_flashdata('message', $this->lang->line('membership_failed_db'));
            redirect('/membership/register');
        }
    }

    /**/
    public function check_mail(){
        $this->load->model('membership/register_model');
        $email=$_POST['email'];
        echo $this->register_model->check_mail($email);
    }

    /**
     *
     * activate_membership: verify and activate account
     *
     * @param int $email the e-mail address that received the activation link
     * @param string $nonce the member nonce associated with the e-mail address
     *
     */

    public function activate_membership($email = NULL, $nonce = NULL) {
       $content_data['page_title'] = "Activate Membership";

        $content_data['message'] = 'The activation link is invalid or expired, or your account is already active.';

        $this->load->model('membership/register_model');
        if($this->register_model->activate_member(urldecode($email), $nonce)) {
            $content_data['message'] = 'Account has been activated.';
        }

        $this->load->view('private/header');
        $this->load->view('private/activate',$content_data);
        $this->load->view('private/header');
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */