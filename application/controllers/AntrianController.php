<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AntrianController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('M_Model'); // Load model jika diperlukan
        // check_admin();
    }

    public function index() {
      // Set judul halaman dan konten view
      $data['title']  = 'Antrian';
      $data['conten'] = 'admin/page/antrian/view';
  
      // Lakukan join antara tbl_loket, tbl_layanan, dan tbl_users
      $this->db->select('tbl_loket.*, tbl_layanan.nama as nama_layanan, tbl_users.nama_lengkap'); // Pilih kolom yang ingin diambil
      $this->db->from('tbl_loket'); // Tabel utama
      $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_loket.id_layanan', 'left'); // Join dengan tbl_layanan
      $this->db->join('tbl_users', 'tbl_users.id_loket = tbl_loket.id_loket', 'left'); // Join dengan tbl_users
  
      // Tambahkan kondisi WHERE berdasarkan status loket
      $this->db->where('tbl_loket.status', '1'); // Hanya ambil data dengan status '1' (aktif)
      $this->db->order_by('tbl_loket.jenis', 'ASC');
    //   $this->db->order_by('waktu_awal', 'ASC');
  
      // Tambahkan kondisi WHERE khusus untuk operator
      if ($this->session->userdata('roles') == "operator") {
          $this->db->where('tbl_users.id_user', $this->session->userdata('user_id')); // Hanya ambil data loket yang terkait dengan operator
      }
  
      // Eksekusi query
      $query = $this->db->get();
  
      // Ambil hasil query
      $data['data_loket'] = $query->result_array();
  
      // Tampilkan view dengan data yang sudah diambil
      $this->load->view('admin/template/template', $data);
  }


  public function get_antrian() {
        $id_loket       = $this->input->post('id_loket');
        $jenis_loket    = $this->input->post('jenis_loket');
        $id_layanan     = $this->input->post('id_layanan');
        $today          = date('Y-m-d');
        $data_jam       = $this->get_active_booking_id();

        // Cek apakah loket ada dan id_antrian terisi
        $this->db->select('id_antrian');
        $this->db->from('tbl_loket');
        $this->db->where('id_loket', $id_loket);
        $cek_loket = $this->db->get();
        $loket_data = $cek_loket->row_array();
        $id_antrian = isset($loket_data['id_antrian']) ? (int)$loket_data['id_antrian'] : 0;

        // 1. Cek antrian status 'panggil' atau 'proses' (yang sedang berjalan di loket ini)
        if ($id_antrian > 0) {
            $this->db->select('tbl_antrian.*, tbl_layanan.kode as kode_layanan, tbl_antrian.status as status_antrian');
            $this->db->from('tbl_antrian');
            $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_antrian.id_layanan', 'left');
            $this->db->where('tbl_antrian.id', $id_antrian);
            if ($jenis_loket == '1') {
                $this->db->where('tbl_antrian.jenis_antrian', 'booking');
                $this->db->where('tbl_antrian.id_waktu_booking', $data_jam);
            } else {
                $this->db->where('tbl_antrian.jenis_antrian', 'non_booking');
                $this->db->where('DATE(tbl_antrian.waktu_buat)', $today);
            }
            $this->db->where_in('tbl_antrian.status', ['panggil', 'proses']);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result());
                return;
            }
        }

        // 2. Jika tidak ada, ambil antrian status 'buat' (antrian berikutnya)
        $this->db->select('tbl_antrian.*, tbl_layanan.kode as kode_layanan, tbl_antrian.status as status_antrian');
        $this->db->from('tbl_antrian');
        $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_antrian.id_layanan', 'left');
        $this->db->where('tbl_antrian.status', 'buat');
        if ($jenis_loket == '1') {
            $this->db->where('tbl_antrian.jenis_antrian', 'booking');
            $this->db->where('tbl_antrian.id_waktu_booking', $data_jam);
        } else {
            $this->db->where('tbl_antrian.jenis_antrian', 'non_booking');
            $this->db->where('DATE(tbl_antrian.waktu_buat)', $today);
        }
        $this->db->where('tbl_antrian.id_layanan', $id_layanan);
        $this->db->order_by('tbl_antrian.no_antrian', 'ASC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result());
        } else {
            echo json_encode([]);
        }
    }

    public function get_active_booking_id() {
        // Ambil data dari database
        $data_jam = $this->M_Model->get_data('tbl_waktu_booking');
        
        // Waktu sekarang
        $current_time = date('H:i');
        $ts_now = strtotime($current_time);

        foreach ($data_jam as $waktu) {
            // Asumsi struktur tabel memiliki: id, jam_mulai, jam_selesai
            $ts_start = strtotime($waktu['waktu_awal']);
            $ts_end = strtotime($waktu['waktu_akhir']);

            if ($ts_now >= $ts_start && $ts_now <= $ts_end) {
                return $waktu['id_waktu_booking']; // Kembalikan ID langsung
            }
        }

        return null; // Tidak ada yang aktif
    }

    public function update_status_antrian() {
        // Ambil data dari input POST
        $id_antrian = $this->input->post('id_antrian');
        $status = $this->input->post('status'); // Status baru: panggil, proses, selesai, batal
        $id_loket = $this->input->post('id_loket'); // ID loket yang terkait

        // Validasi input
        if (empty($id_antrian)) {
            echo json_encode(['status' => 'error', 'message' => 'ID Antrian tidak boleh kosong']);
            return;
        }

        if (empty($status)) {
            echo json_encode(['status' => 'error', 'message' => 'Status tidak boleh kosong']);
            return;
        }

        if (empty($id_loket)) {
            echo json_encode(['status' => 'error', 'message' => 'ID Loket tidak boleh kosong']);
            return;
        }

        // Mulai transaksi database
        $this->db->trans_start();

        // Update tabel tbl_loket berdasarkan status
        if ($status == 'batal' || $status == 'selesai') {
            // Jika status adalah batal atau selesai, set id_antrian di tbl_loket menjadi 0
            $this->db->where('id_loket', $id_loket);
            $this->db->update('tbl_loket', ['id_antrian' => 0]);
        } else {
            // Jika status adalah panggil atau proses, update id_antrian di tbl_loket
            $this->db->where('id_loket', $id_loket);
            $this->db->update('tbl_loket', ['id_antrian' => $id_antrian]);
        }

        // Update status antrian dan waktu terkait di tabel tbl_antrian
        $update_data = ['status' => $status];

        // Tambahkan kolom waktu berdasarkan status
        switch ($status) {
            case 'panggil':
                $update_data['waktu_panggil'] = date('Y-m-d H:i:s'); // Waktu saat ini
                break;
            case 'proses':
                $update_data['waktu_proses'] = date('Y-m-d H:i:s'); // Waktu saat ini
                break;
            case 'selesai':
                $update_data['waktu_selesai'] = date('Y-m-d H:i:s'); // Waktu saat ini
                break;
            case 'batal':
                $update_data['waktu_batal'] = date('Y-m-d H:i:s'); // Waktu saat ini
                break;
        }

        $this->db->where('id', $id_antrian);
        $this->db->update('tbl_antrian', $update_data);

        // Selesaikan transaksi
        $this->db->trans_complete();

        // Cek apakah transaksi berhasil
        if ($this->db->trans_status() === FALSE) {
            // Jika transaksi gagal, kembalikan pesan error
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate status antrian']);
        } else {
            // Jika transaksi berhasil, kembalikan pesan sukses
            echo json_encode(['status' => 'success', 'message' => 'Status antrian berhasil diupdate']);
        }
    }

    public function get_daftar_antrian_booking() {
        $today = date('Y-m-d'); // Ambil tanggal hari ini

        $this->db->select('tbl_antrian.*, tbl_layanan.kode as kode_layanan, tbl_layanan.nama as nama_layanan,tbl_antrian.waktu_buat,CONCAT(DATE_FORMAT(tbl_antrian.waktu_buat, "%d-%m-%Y")," ",TIME_FORMAT(tbl_waktu_booking.waktu_awal, "%H:%i"), " - ", TIME_FORMAT(tbl_waktu_booking.waktu_akhir, "%H:%i")) AS waktu_booking');
        $this->db->from('tbl_antrian');
        $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_antrian.id_layanan', 'left');
        $this->db->join('tbl_waktu_booking', 'tbl_waktu_booking.id_waktu_booking = tbl_antrian.id_waktu_booking', 'left');
        $this->db->where('DATE(tbl_antrian.waktu_buat)', $today); // Filter berdasarkan tanggal hari ini
        $this->db->where('jenis_antrian', 'booking'); // Filter berdasarkan tanggal hari ini
        $this->db->order_by('tbl_antrian.waktu_buat', 'ASC');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result());
        } else {
            echo json_encode([]);
        }
    }

    public function get_daftar_antrian_non_booking() {
        $today = date('Y-m-d'); // Ambil tanggal hari ini

        $this->db->select('tbl_antrian.*, tbl_layanan.kode as kode_layanan, tbl_layanan.nama as nama_layanan');
        $this->db->from('tbl_antrian');
        $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_antrian.id_layanan', 'left');
        $this->db->where('DATE(tbl_antrian.waktu_buat)', $today); // Filter berdasarkan tanggal hari ini
        $this->db->where('jenis_antrian', 'non_booking'); // Filter berdasarkan tanggal hari ini
        $this->db->order_by('tbl_antrian.waktu_buat', 'ASC');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result());
        } else {
            echo json_encode([]);
        }
    }
  
}
