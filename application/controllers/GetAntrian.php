<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GetAntrian extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('M_Model'); // Load model
    }

    public function index() {
        // Periksa status lock - gunakan !== TRUE untuk kejelasan
        if($this->session->userdata('lock') !== TRUE) {
            redirect('GetAntrian/lock'); // Gunakan redirect ke method lock dalam controller ini
        }
        
        // Ambil data layanan dari model
        $data['data_layanan'] = $this->M_Model->get_data('tbl_layanan');
        $data['title'] = 'Antrian';
    
        // Load view (hilangkan .php karena sudah dihandle oleh CI)
        $this->load->view('get_antrian', $data);
    }
    
    public function lock() {
        // Jika sudah unlock, redirect ke index
        if($this->session->userdata('lock') === TRUE) {
            redirect('GetAntrian'); // Redirect ke method index
        }
        
        $data['page'] = 'lock';
        $data['title'] = 'Lock Screen';
        $this->load->view('auth/template', $data); // Hilangkan .php
    }
    
    public function proses_unlock() {
        // Validasi request harus POST
        
        $pin = $this->input->post('pin');
        $kode_akses = get_web_info('kode_akses');
        
        // Validasi input
        if (empty($pin)) {
            $response = [
                'status' => 'error',
                'message' => 'PIN harus diisi'
            ];
        } elseif ($pin == $kode_akses) {
            // Set session lock
            $this->session->set_userdata('lock', TRUE);
            $this->session->set_userdata('last_activity', time()); // Tambahkan timestamp aktivitas
            
            $response = [
                'status' => 'success',
                'message' => 'Berhasil membuka kunci!'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'PIN yang Anda masukkan salah'
            ];
        }
    
        // Keluarkan response JSON dengan header yang tepat
        echo json_encode($response);
    }
    
    public function keluar() {
        // Hapus data session terkait lock
        $this->session->unset_userdata(['lock', 'last_activity']);
        
        // Hancurkan session
        $this->session->sess_destroy();
    
        // Redirect ke halaman lock
        redirect('GetAntrian/lock');
    }

    public function simpan_antrian() {
        // Pastikan request adalah POST
        if ($this->input->post()) {
            // Ambil data dari POST
            $id_layanan     = $this->input->post('id_layanan');
            $kode_antrian   = $this->input->post('kode');
            $no_antrian     = $this->input->post('nomor');

            // Validasi data
            if (empty($id_layanan) || empty($no_antrian)) {
                $response = [
                    'status'  => 'error',
                    'message' => 'Data tidak lengkap. Pastikan id_layanan dan nomor terisi.'
                ];
                echo json_encode($response);
                return;
            }

            // Data untuk disimpan
            $data = [
                'no_antrian'        => $kode_antrian.$no_antrian,
                'id_layanan'        => $id_layanan,
                'status'            => 'buat', // Default status
                'jenis_antrian'     => 'non_booking', // Default status
                'waktu_buat'        => date('Y-m-d H:i:s') // Timestamp
            ];

            // Simpan data ke database
            if ($this->M_Model->Insert_Data($data, 'tbl_antrian')) {
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

            // Set header JSON
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            // Jika bukan request POST, tampilkan error
            show_error('No direct script access allowed', 403);
        }
    }

    public function get_last_antrian() {
        $today = date('Y-m-d'); // Ambil tanggal hari ini
        $id_layanan = $this->input->post('id_layanan');
        $this->db->select('no_antrian');
        $this->db->from('tbl_antrian');
        $this->db->where('id_layanan', $id_layanan);
        $this->db->where('DATE(waktu_buat)', $today); // Filter berdasarkan tanggal hari ini
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
    
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            return $query->row()->no_antrian; // Ambil nilai no_antrian
        } else {
            return 0; // Jika tidak ada data, kembalikan 0
        }
    }

    public function get_last_antrian_() {
        $kode_layanan = $this->input->post('kode_layanan');
        $today = date('Y-m-d');
        
        $this->db->select('MAX(CAST(SUBSTRING(no_antrian, -3) AS UNSIGNED)) as last_number');
        $this->db->like('no_antrian', $kode_layanan, 'after');
        $this->db->where('DATE(waktu_buat)', $today);
        $query = $this->db->get('tbl_antrian');
        
        $result = $query->row();
        $last_number = $result ? $result->last_number : 0;
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'success',
                'last_number' => $last_number
            ]));
    }

    
}