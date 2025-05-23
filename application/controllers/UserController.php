<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('M_Model');
        if(!$this->session->userdata()){
          redirect('AuthController');
        }
    }

    public function index() {
        $data['title'] = 'home';
        $data['conten'] = 'users/home';

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
        // Load view
        $this->load->view('users/template', $data);
    }

    public function ambil_antrian(){
        // Ambil data layanan dari model
        $data['data_layanan'] = $this->M_Model->get_data('tbl_layanan');
        $data['title'] = 'Antrian';
        $data['conten'] = 'users/ambil_antrian';

        // Load view
        $this->load->view('users/template', $data);
    }

    // public function histori(){

    //     $data['title'] = 'Antrian';
    //     $data['conten'] = 'users/histori';

    //     // Load view
    //     $this->load->view('users/template', $data);
    // }


    public function get_waktu_booking() {
        $id_layanan = $this->input->post('id_layanan');
        $date       = date('Y-m-d'); // Atau bisa dari input $this->input->post('date')
     
        $slots = $this->M_Model->get_waktu_booking_slot($id_layanan, $date);
        
        header('Content-Type: application/json');
        echo json_encode($slots);
    }
    
    public function simpan_booking() {
        $id_layanan         = $this->input->post('id_layanan');
        $kode_layanan       = $this->input->post('kode_layanan');
        $id_user            = $this->session->userdata('user_id');
        $id_waktu_booking   = $this->input->post('id_waktu_booking');
        $date               = date('Y-m-d');
        
        // Validasi input
        if (empty($id_waktu_booking)) {
            echo json_encode(['status' => 'warning', 'message' => 'Harap lengkapi semua data']);
            return;
        }
        
        // Cek apakah user sudah memiliki antrian aktif di tanggal yang sama
        $existing_antrian = $this->M_Model->cek_antrian_user($id_user, $date);
        if ($existing_antrian) {
            echo json_encode([
                'status' => 'warning', 
                'message' => 'Anda sudah memiliki antrian aktif hari ini.'
            ]);
            return;
        }
        
        // Cek ketersediaan slot
        if (!$this->M_Model->cek_slot($id_layanan, $date, $id_waktu_booking)) {
            echo json_encode(['status' => 'warning', 'message' => 'Slot booking tidak tersedia. Silakan pilih waktu lain.']);
            return;
        }
        
        // Simpan booking
        $booking_data = [
            'no_antrian'            => $this->M_Model->generate_no_antrian_booking($id_layanan,$date),
            'id_layanan'            => $id_layanan,
            'status'                => 'buat',
            'waktu_buat'            => $date,
            'id_waktu_booking'      => $id_waktu_booking,
            'jenis_antrian'         => 'booking',
            'id_user'               => $id_user
        ];
        
        if ($this->M_Model->Insert_Data($booking_data,'tbl_antrian')) {
            $response = [
                'status'  => 'success',
                'message' => 'Berhasil menyimpan data antrian.'
            ];
        } else {
            $response = [
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            ];
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($response));
    }

    public function histori() {
        $id_user = $this->session->userdata('user_id');
    
        // Lakukan join antara tbl_antrian, tbl_layanan, dan tbl_waktu_booking
        $this->db->select('
            tbl_antrian.*, tbl_antrian.status as status_antrian,
            tbl_layanan.nama as nama_layanan,
            CONCAT(TIME_FORMAT(tbl_waktu_booking.waktu_awal, "%H:%i"), " - ", TIME_FORMAT(tbl_waktu_booking.waktu_akhir, "%H:%i")) as waktu_booking,
            tbl_waktu_booking.waktu_awal,
            tbl_waktu_booking.waktu_akhir
        ');
        
        $this->db->from('tbl_antrian');
        $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_antrian.id_layanan', 'left');
        $this->db->join('tbl_waktu_booking', 'tbl_waktu_booking.id_waktu_booking = tbl_antrian.id_waktu_booking', 'left');
        $this->db->where('tbl_antrian.id_user', $id_user);
        
        // Tambahkan pengurutan berdasarkan tanggal dan waktu
        $this->db->order_by('tbl_antrian.no_antrian', 'ASC');
        $this->db->order_by('tbl_antrian.id_layanan', 'ASC');
        $this->db->order_by('tbl_antrian.waktu_buat', 'DESC');
        $this->db->order_by('tbl_waktu_booking.waktu_awal', 'DESC');
        
        $query = $this->db->get();
        
        // Cek apakah query berhasil
        if ($query) {
            $data['data_histori'] = $query->result_array();
        } else {
            // Jika query gagal, set array kosong dan log error
            $data['data_histori'] = array();
            log_message('error', 'Database error: ' . $this->db->error()['message']);
        }
        
        $data['title'] = 'Histori Antrian';
        $data['conten'] = 'users/histori';
        $this->load->view('users/template', $data);
    }

    // Fungsi untuk halaman profile
        public function profile() {
        $user_id = $this->session->userdata('user_id');
        $user = $this->db->get_where('tbl_users', ['id_user' => $user_id])->row_array();
        $data['user'] = $user;
        $data['title'] = 'Profile';
        $data['conten'] = 'users/profile';
        $this->load->view('users/template', $data);
    }

}


