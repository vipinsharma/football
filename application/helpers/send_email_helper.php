<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('load_email_config')) {
    /**
     *
     * load_email_config: obscure password with specially designed salt - site_key combo in sha512
     *
     * @param int $i the config type: 1 = PHP mail(); 2 = sendmail; 3 = SMTP
     * @return array
     *
     */
    function load_email_config($i) {
        $config = array();
        switch ($i) {
            case 2:
                $config = array(
                    'protocol' => 'sendmail',
                    'mailpath' => Settings_model::$db_config['sendmail_path'],
                    'charset' => "utf-8",
                    'wordwrap' => TRUE,
                    'newline' => "\r\n"
                );
                break;
            case 3:
                $config = array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'mgftest@mygridironface.com',
                    'smtp_port' => 26,
                    'smtp_user' => $this->encrypt->encode('mgftest@mygridironface.com'),
					'smtp_pass' => $this->encrypt->encode('mgf7nfl7'),
                    'smtp_timeout' => 30,
                    'charset' => "utf-8",
                    'newline' => "\r\n"
                );
        }

        return $config;
    }
}


/*
Smtp: mail.mygridironface.com

Port: 26

Username: mgftest@mygridironface.com

Password: mgf7nfl7

 End of file send_email_helper.php */
/* Location: ./application/helpers/send_email_helper.php */