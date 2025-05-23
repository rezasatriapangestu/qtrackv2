<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');

use chriskacerguis\RestServer\RestController;

class Queue_Api extends RestController {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model');
        $this->load->library(['form_validation', 'session']);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    // GET: /api/queue_api/loket
    public function loket_get() {
        // Ambil semua loket aktif
        $this->db->select('
            tbl_loket.id_loket,
            tbl_loket.nama,
            tbl_loket.jenis,
            tbl_loket.status,
            tbl_loket.id_layanan,
            tbl_layanan.nama as nama_layanan
        ');
        $this->db->from('tbl_loket');
        $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_loket.id_layanan', 'left');
        $this->db->where('tbl_loket.status', '1');
        $loket_list = $this->db->get()->result_array();

        $tanggal_hari_ini = date('Y-m-d');
        $result = [];
        $menungguCount = 0;

        foreach ($loket_list as $loket) {
            // Cari antrian terbaru untuk layanan ini (status buat/panggil/proses hari ini)
            $this->db->select('no_antrian, status as status_antrian');
            $this->db->from('tbl_antrian');
            $this->db->where('id_layanan', $loket['id_layanan']);
            $this->db->where_in('status', ['buat', 'panggil', 'proses']);
            $this->db->where('DATE(waktu_buat)', $tanggal_hari_ini);
            $this->db->order_by("FIELD(status, 'panggil', 'proses', 'buat')", '', false); // Prioritaskan panggil > proses > buat
            $this->db->order_by('waktu_buat', 'ASC');
            $this->db->limit(1);
            $antrian = $this->db->get()->row_array();

            $row = $loket;
            if ($antrian) {
                $row['no_antrian'] = $antrian['no_antrian'];
                switch ($antrian['status_antrian']) {
                    case 'buat':
                        $row['status_antrian'] = 'Menunggu';
                        $menungguCount++;
                        break;
                    case 'panggil':
                        $row['status_antrian'] = 'Panggil';
                        break;
                    case 'proses':
                        $row['status_antrian'] = 'Proses';
                        break;
                    default:
                        $row['status_antrian'] = 'Tidak ada antrian';
                }
            } else {
                $row['no_antrian'] = '-';
                $row['status_antrian'] = 'Tidak ada antrian';
            }
            $result[] = $row;
        }

        log_message('debug', "Jumlah loket dengan status 'Menunggu': $menungguCount");

        return $this->response([
            'status' => true,
            'data' => $result
        ], RestController::HTTP_OK);
    }

    public function layanan_get() {
        $this->db->select('id_layanan, kode AS kode_layanan, nama AS nama_layanan');
        $this->db->from('tbl_layanan');
        $query = $this->db->get();
        $layanan = $query->result_array();

        return $this->response([
            'status' => true,
            'data' => $layanan
        ], RestController::HTTP_OK);
    }


     // POST: /api/queue_api/booking?id_user=xxx
    public function booking_post() {
        $json = json_decode(file_get_contents('php://input'), true);
        
        $id_layanan       = $json['id_layanan'] ?? null;
        $kode_layanan     = $json['kode_layanan'] ?? null;
        $id_user          = $json['id_user'] ?? null;
        $id_waktu_booking = $json['id_waktu_booking'] ?? null;
        
        $tanggal_hari_ini = date('Y-m-d'); // untuk validasi tanggal
        $datetime_now     = date('Y-m-d H:i:s'); // waktu lengkap untuk disimpan
        
        // Validasi input
        if (empty($id_layanan) || empty($kode_layanan) || empty($id_user) || empty($id_waktu_booking)) {
            $this->response([
                'status' => 'warning',
                'message' => 'Harap lengkapi semua data.'
            ], RESTController::HTTP_BAD_REQUEST);
            return;
        }

        // Cek antrian aktif hari ini
        $existing_antrian = $this->M_Model->cek_antrian_user($id_user, $tanggal_hari_ini);
        if ($existing_antrian) {
            $this->response([
                'status'  => 'warning',
                'message' => 'Anda sudah memiliki antrian aktif hari ini.'
            ], RESTController::HTTP_OK);
            return;
        }

        // Cek slot tersedia
        if (!$this->M_Model->cek_slot($id_layanan, $tanggal_hari_ini, $id_waktu_booking)) {
            $this->response([
                'status' => 'warning',
                'message' => 'Slot booking tidak tersedia. Silakan pilih waktu lain.'
            ], RESTController::HTTP_OK);
            return;
        }

        // Simpan booking
        $booking_data = [
            'no_antrian'        => $this->M_Model->generate_no_antrian_booking($id_layanan, $tanggal_hari_ini),
            'id_layanan'        => $id_layanan,
            'status'            => 'buat',
            'waktu_buat'        => $datetime_now,
            'id_waktu_booking'  => $id_waktu_booking,
            'jenis_antrian'     => 'booking',
            'id_user'           => $id_user
        ];

        if ($this->M_Model->Insert_Data($booking_data, 'tbl_antrian')) {
            $this->response([
                'status'  => 'success',
                'message' => 'Berhasil menyimpan data antrian.'
            ], RESTController::HTTP_OK);
        } else {
            $this->response([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            ], RESTController::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




    // GET: /api/queue_api/histori?id_user=xxx
    public function histori_get() {
        try {
            // Log request untuk debugging
            log_message('debug', 'Memproses request histori untuk id_user: ' . $this->get('id_user'));

            // Ambil id_user dari query parameter
            $id_user = $this->get('id_user');

            // Validasi id_user
            if (!$id_user) {
                log_message('error', 'ID user tidak disediakan.');
                return $this->response([
                    'status' => false,
                    'message' => 'ID user wajib diisi.'
                ], RestController::HTTP_BAD_REQUEST);
            }

            // Validasi keberadaan pengguna di tbl_users
            $this->db->select('id_user, nama_lengkap');
            $this->db->from('tbl_users');
            $this->db->where('id_user', $id_user);
            $query = $this->db->get();

            if ($query === FALSE) {
                log_message('error', 'Query validasi pengguna gagal untuk id_user: ' . $id_user);
                throw new Exception('Gagal menjalankan query validasi pengguna.');
            }

            $user = $query->row();
            if (!$user) {
                log_message('error', 'Pengguna dengan id_user ' . $id_user . ' tidak ditemukan.');
                return $this->response([
                    'status' => false,
                    'message' => 'Pengguna tidak ditemukan.'
                ], RestController::HTTP_BAD_REQUEST);
            }

            // Ambil data riwayat antrian
            $this->db->select('
                tbl_antrian.id, tbl_antrian.no_antrian, tbl_antrian.status as status_antrian,
                tbl_antrian.waktu_buat, tbl_antrian.waktu_panggil, tbl_antrian.waktu_proses,
                tbl_antrian.waktu_selesai, tbl_antrian.waktu_batal, tbl_antrian.jenis_antrian,
                tbl_antrian.id_user, tbl_users.nama_lengkap,
                tbl_layanan.nama as nama_layanan, tbl_layanan.kode as kode_layanan,
                CONCAT(TIME_FORMAT(tbl_waktu_booking.waktu_awal, "%H:%i"), " - ", TIME_FORMAT(tbl_waktu_booking.waktu_akhir, "%H:%i")) as waktu_booking,
                tbl_loket.nama as nama_loket
            ');
            $this->db->from('tbl_antrian');
            $this->db->join('tbl_users', 'tbl_users.id_user = tbl_antrian.id_user', 'left');
            $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_antrian.id_layanan', 'left');
            $this->db->join('tbl_waktu_booking', 'tbl_waktu_booking.id_waktu_booking = tbl_antrian.id_waktu_booking', 'left');
            $this->db->join('tbl_loket', 'tbl_loket.id_antrian = tbl_antrian.id', 'left');
            $this->db->where('tbl_antrian.id_user', $id_user);
            $this->db->order_by('tbl_antrian.waktu_buat', 'DESC');
            $this->db->limit(100); // Batasi untuk performa
            $query = $this->db->get();

            if ($query === FALSE) {
                log_message('error', 'Query riwayat antrian gagal untuk id_user: ' . $id_user);
                throw new Exception('Gagal menjalankan query riwayat antrian.');
            }

            $data_histori = $query->result_array();

            // Peta status untuk tampilan user-friendly
            $status_map = [
                'buat' => 'Menunggu',
                'panggil' => 'Dipanggil',
                'proses' => 'Sedang Diproses',
                'selesai' => 'Selesai',
                'batal' => 'Dibatalkan'
            ];

            // Proses data untuk tampilan
            foreach ($data_histori as &$row) {
                // Format status antrian
                $row['status_antrian'] = $status_map[$row['status_antrian']] ?? $row['status_antrian'];

                // Tentukan waktu_status berdasarkan status
                $row['waktu_status'] = '-';
                if ($row['status_antrian'] === 'Dipanggil' && !empty($row['waktu_panggil'])) {
                    $row['waktu_status'] = $row['waktu_panggil'];
                } elseif ($row['status_antrian'] === 'Sedang Diproses' && !empty($row['waktu_proses'])) {
                    $row['waktu_status'] = $row['waktu_proses'];
                } elseif ($row['status_antrian'] === 'Selesai' && !empty($row['waktu_selesai'])) {
                    $row['waktu_status'] = $row['waktu_selesai'];
                } elseif ($row['status_antrian'] === 'Dibatalkan' && !empty($row['waktu_batal'])) {
                    $row['waktu_status'] = $row['waktu_batal'];
                }

                // Pastikan kolom tidak null
                $row['id_user'] = $row['id_user'] ?? '-';
                $row['nama_lengkap'] = $row['nama_lengkap'] ?? '-';
                $row['no_antrian'] = $row['no_antrian'] ?? '-';
                $row['nama_layanan'] = $row['nama_layanan'] ?? '-';
                $row['kode_layanan'] = $row['kode_layanan'] ?? '-';
                $row['waktu_booking'] = $row['waktu_booking'] ?: '-';
                $row['nama_loket'] = $row['nama_loket'] ?? '-';
                $row['jenis_antrian'] = $row['jenis_antrian'] ?? '-'; // Gunakan booking atau non_booking
            }
            unset($row);

            return $this->response([
                'status' => true,
                'data' => $data_histori
            ], RestController::HTTP_OK);
        } catch (Exception $e) {
            // Log error untuk debugging
            log_message('error', 'Exception di histori_get: ' . $e->getMessage());
            return $this->response([
                'status' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], RestController::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // GET: /api/queue_api/waktu_booking?id_layanan=xxx
    public function waktu_booking_get() {
        $id_layanan = $this->get('id_layanan');
        $date = $this->get('date') ?: date('Y-m-d');
        $only_available = $this->get('only_available') === 'true';

        if (!$id_layanan) {
            return $this->response([
                'status' => false,
                'message' => 'ID layanan wajib diisi.'
            ], RestController::HTTP_BAD_REQUEST);
        }

        // Validasi id_layanan
        $this->db->select('id_layanan');
        $this->db->from('tbl_layanan');
        $this->db->where('id_layanan', $id_layanan);
        if (!$this->db->get()->row()) {
            return $this->response([
                'status' => false,
                'message' => 'Layanan tidak ditemukan.'
            ], RestController::HTTP_BAD_REQUEST);
        }

        // Ambil waktu saat ini untuk filter
        $current_datetime = date('Y-m-d H:i:s');

        $this->db->select('
            tbl_waktu_booking.id_waktu_booking,
            tbl_waktu_booking.waktu_awal,
            tbl_waktu_booking.waktu_akhir,
            tbl_waktu_booking.maks_antrian,
            COUNT(tbl_antrian.id) as jumlah_antrian
        ');
        $this->db->from('tbl_waktu_booking');
        $this->db->join(
            'tbl_antrian',
            'tbl_antrian.id_waktu_booking = tbl_waktu_booking.id_waktu_booking AND tbl_antrian.id_layanan = ' . $this->db->escape($id_layanan) . ' AND DATE(tbl_antrian.waktu_buat) = ' . $this->db->escape($date),
            'left'
        );
        // Filter jadwal yang belum selesai (waktu_akhir >= current_datetime)
        $this->db->where('CONCAT(' . $this->db->escape($date) . ', " ", tbl_waktu_booking.waktu_akhir) >=', $current_datetime);
        $this->db->group_by('tbl_waktu_booking.id_waktu_booking');
        $query = $this->db->get();
        $slots = $query->result_array();

        $filtered_slots = [];
        foreach ($slots as &$slot) {
            $slot['waktu'] = date('H:i', strtotime($slot['waktu_awal'])) . ' - ' . date('H:i', strtotime($slot['waktu_akhir']));
            $slot['kuota_tersisa'] = $slot['maks_antrian'] - $slot['jumlah_antrian'];
            if (!$only_available || $slot['kuota_tersisa'] > 0) {
                $filtered_slots[] = $slot;
            }
        }

        return $this->response([
            'status' => true,
            'data' => $filtered_slots
        ], RestController::HTTP_OK);
    }

    // GET: /api/queue_api/pantau_antrian?id_antrian=xxx or ?id_user=xxx
    public function pantau_antrian_get() {
        $id_antrian = $this->get('id_antrian');
        $id_user = $this->get('id_user');
        $date = date('Y-m-d');

        if (!$id_antrian && !$id_user) {
            return $this->response([
                'status' => false,
                'message' => 'ID antrian atau ID user wajib diisi.'
            ], RestController::HTTP_BAD_REQUEST);
        }

        // Validasi id_user
        if ($id_user) {
            $this->db->select('id_user');
            $this->db->from('tbl_users');
            $this->db->where('id_user', $id_user);
            if (!$this->db->get()->row()) {
                return $this->response([
                    'status' => false,
                    'message' => 'Pengguna tidak ditemukan.'
                ], RestController::HTTP_BAD_REQUEST);
            }
        }

        // Tentukan ID antrian
        if ($id_user && !$id_antrian) {
            $this->db->select('id');
            $this->db->from('tbl_antrian');
            $this->db->where('id_user', $id_user);
            $this->db->where_in('status', ['buat', 'panggil', 'proses']);
            $this->db->where('DATE(waktu_buat)', $date);
            $this->db->order_by('waktu_buat', 'DESC');
            $this->db->limit(1);
            $antrian = $this->db->get()->row();
            if (!$antrian) {
                return $this->response([
                    'status' => false,
                    'message' => 'Tidak ada antrian aktif untuk pengguna ini.'
                ], RestController::HTTP_NOT_FOUND);
            }
            $id_antrian = $antrian->id;
        }

        // Ambil detail antrian
        $this->db->select('
            tbl_antrian.id,
            tbl_antrian.no_antrian,
            tbl_antrian.status,
            tbl_antrian.id_layanan,
            tbl_layanan.nama as nama_layanan
        ');
        $this->db->from('tbl_antrian');
        $this->db->join('tbl_layanan', 'tbl_layanan.id_layanan = tbl_antrian.id_layanan', 'left');
        $this->db->where('tbl_antrian.id', $id_antrian);
        $antrian = $this->db->get()->row();

        if (!$antrian) {
            return $this->response([
                'status' => false,
                'message' => 'Antrian tidak ditemukan.'
            ], RestController::HTTP_NOT_FOUND);
        }

        // Ubah status 'buat' menjadi 'Menunggu'
        $status_antrian = $antrian->status === 'buat' ? 'Menunggu' : $antrian->status;

        // Cari loket yang melayani antrian ini
        $this->db->select('tbl_loket.nama as nama_loket');
        $this->db->from('tbl_loket');
        $this->db->where('tbl_loket.id_antrian', $id_antrian);
        $loket = $this->db->get()->row();
        $nama_loket = $loket ? $loket->nama_loket : '-';

        // Hitung jumlah antrian sebelumnya
        $this->db->from('tbl_antrian');
        $this->db->where('id_layanan', $antrian->id_layanan);
        $this->db->where('status', 'buat');
        $this->db->where('no_antrian <', $antrian->no_antrian);
        $this->db->where('DATE(waktu_buat)', $date);
        $jumlah_menunggu = $this->db->count_all_results();

        // Cari antrian yang sedang dilayani
        $this->db->select('tbl_antrian.no_antrian');
        $this->db->from('tbl_antrian');
        $this->db->join('tbl_loket', 'tbl_loket.id_antrian = tbl_antrian.id', 'inner');
        $this->db->where('tbl_antrian.id_layanan', $antrian->id_layanan);
        $this->db->where_in('tbl_antrian.status', ['panggil', 'proses']);
        $this->db->where('DATE(tbl_antrian.waktu_buat)', $date);
        $this->db->order_by('tbl_antrian.waktu_panggil', 'DESC');
        $this->db->limit(1);
        $antrian_sedang_dilayani = $this->db->get()->row();
        $no_antrian_sedang_dilayani = $antrian_sedang_dilayani ? $antrian_sedang_dilayani->no_antrian : '-';

        // Log untuk debugging
        log_message('debug', 'Pantau Antrian Query (antrian): ' . $this->db->last_query());
        log_message('debug', 'Pantau Antrian - ID: ' . $id_antrian . ', No Antrian: ' . $antrian->no_antrian . ', Status: ' . $status_antrian);

        return $this->response([
            'status' => true,
            'message' => 'Status antrian ditemukan.',
            'data' => [
                'id_antrian' => $antrian->id,
                'no_antrian' => $antrian->no_antrian,
                'status_antrian' => $status_antrian,
                'nama_layanan' => $antrian->nama_layanan,
                'nama_loket' => $nama_loket,
                'no_antrian_sedang_dilayani' => $no_antrian_sedang_dilayani,
                'jumlah_antrian_sebelum' => $jumlah_menunggu
            ]
        ], RestController::HTTP_OK);
    }
}