<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_offline extends External_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    function index() {

        $data['title']='Site Is Offline';
        $this->load->view('membership/header',$data);
        $this->load->view('membership/footer');
    }

}

/* End of file site_offline.php */
/* Location: ./application/controllers/site_offline.php */