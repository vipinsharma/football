<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Public_page extends Public_Controller {

    public function __construct()
    {
        parent::__construct();
        // pre-load
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    function index() {

        $data['title']='Public page';
        $this->load->view('public/header',$data);
        $this->load->view('public/footer');
    }

}

/* End of file public_page.php */
/* Location: ./application/controllers/public_page.php */