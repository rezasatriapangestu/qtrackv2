<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model');
    }

    public function index() {
        $data['title']  = 'Dashboard';
        // Lakukan join antara tbl_loket, tbl_layanan, dan tbl_users
        $this->db->select('tbl_loket.*, tbl_layanan.nama as nama_layanan, tbl_users.nama_lengkap'); // Pilih kolom yang ingin diambil
        $this->db->from('tbl_loket'); // Tabel utama
        $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_loket.id_layanan', 'left'); // Join dengan tbl_layanan
        $this->db->join('tbl_users', 'tbl_users.id_loket = tbl_loket.id_loket', 'left'); // Join dengan tbl_users
    
        // Tambahkan kondisi WHERE berdasarkan status loket
        $this->db->where('tbl_loket.status', '1'); // Hanya ambil data dengan status '1' (aktif)
    
        // Tambahkan kondisi WHERE khusus untuk operator
        if ($this->session->userdata('roles') == "operator") {
            $this->db->where('tbl_users.id_user', $this->session->userdata('user_id')); // Hanya ambil data loket yang terkait dengan operator
        }
    
        // Eksekusi query
        $query = $this->db->get();
    
        // Ambil hasil query
        $data['data_loket'] = $query->result_array();
  
        $this->load->view('dashboard.php', $data);
    }

    
}
