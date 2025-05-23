<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model'); // Load model jika diperlukan
        check_login();
    }


    /**
     * Menampilkan halaman home.
     */
    public function index() {
        $data['title']  = 'Home';
        $data['conten'] = 'admin/page/home';
        $this->load->view('admin/template/template',$data);
    }
}