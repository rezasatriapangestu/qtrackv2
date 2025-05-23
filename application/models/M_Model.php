<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function generate_user_id() {
        $date = date('Ymd');
        $random = mt_rand(1000, 9999);
        return 'U' . $date . $random;
    }

    public function register_user($data) {
        return $this->db->insert('tbl_users', $data);
    }

    public function is_email($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('tbl_users');
        return $query->num_rows() > 0;
    }

    public function get_user_by_token($token) {
        $this->db->where('token', $token);
        return $this->db->get('tbl_users')->row();
    }

    public function activate_user($id_user) {
        $this->db->where('id_user', $id_user);
        $this->db->update('tbl_users', array('status' => '1'));
    }

    public function get_user_by_email($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('tbl_users');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    public function get_data($tables) {
        $this->load->database();
        if (empty($tables)) {
            return array();
        }
        $query = $this->db->get($tables);
        if ($query) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function get_data_by_id($table,$field, $id) {
        return $this->db->get_where($table, array($field => $id))->row();
    }

    public function Insert_Data($data, $table) {
        return $this->db->insert($table, $data);
    }

    public function update_data($table, $data, $conditions) {
        $this->db->where($conditions);
        return $this->db->update($table, $data);
    }

    public function hapus($pk, $id, $table) {
        if (empty($id)) {
            $this->session->set_flashdata('status', 'error');
            $this->session->set_flashdata('message', 'ID tidak valid.');
            return false;
        }
        $this->db->where($pk, $id);
        $delete = $this->db->delete($table);
        if ($delete) {
            $this->session->set_flashdata('status', 'success');
            $this->session->set_flashdata('message', 'Data berhasil dihapus.');
            return true;
        } else {
            $this->session->set_flashdata('status', 'error');
            $this->session->set_flashdata('message', 'Gagal menghapus data.');
            return false;
        }
    }

    public function is_data($field, $table, $data) {
        $this->db->where($field, $data);
        $query = $this->db->get($table);
        return $query->num_rows() > 0;
    }

    public function get_data_by_condition($table, $conditions) {
        return $this->db->get_where($table, $conditions)->row();
    }

    public function get_relation($table1, $joins = array(), $select = '*', $where = array(), $order_by = '', $limit = null, $offset = null) {
        // Mengatur kolom yang dipilih
        $this->db->select($select);
        $this->db->from($table1);
    
        // Loop untuk menambahkan join
        foreach ($joins as $join) {
            $this->db->join($join['table'], $join['condition'], $join['type'] ?? 'inner');
        }
    
        // Kondisi WHERE
        if (!empty($where)) {
            $this->db->where($where);
        }
    
        // Pengurutan
        if (!empty($order_by)) {
            $this->db->order_by($order_by);
        }
    
        // Limit dan Offset
        if ($limit !== null && $offset !== null) {
            $this->db->limit($limit, $offset);
        }
    
        return $this->db->get()->result_array();
    }

    public function GetWHERE($table, $where) {
        // Loop untuk menambahkan kondisi WHERE
        foreach ($where as $field => $value) {
            $this->db->where($field, $value);
        }
    
        // Eksekusi query
        $query = $this->db->get($table);
    
        // Cek apakah data ada
        return $query->num_rows() > 0;
    }


    public function get_waktu_booking_slot($id_layanan, $date) {
        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $date)) {
            return array('error' => 'Invalid date format. Use YYYY-MM-DD');
        }
    
        // Get current date and time
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
    
        // Get all time slots
        $this->db->select('*');
        $this->db->from('tbl_waktu_booking');
        
        // If checking for current date, only show future time slots
        if ($date == $current_date) {
            $this->db->where('waktu_akhir >', $current_time);
        }
        
        // Order by time
        $this->db->order_by('waktu_awal', 'ASC');
        $all_slots = $this->db->get()->result();
    
        $available_slots = array();
        
        foreach ($all_slots as $slot) {
            // Count existing bookings for this slot
            $this->db->where('id_layanan', $id_layanan);
            $this->db->where('DATE(waktu_buat)', $date);
            $this->db->where('id_waktu_booking', $slot->id_waktu_booking);
            $this->db->where('jenis_antrian', 'booking');
            // $this->db->where('status', 'buat');
            $booked_count = $this->db->count_all_results('tbl_antrian');
            
            // Check if slot has available quota
            if ($booked_count < $slot->maks_antrian) {
                $available_slots[] = array(
                    'id' => $slot->id_waktu_booking,
                    'waktu_awal' => $slot->waktu_awal,
                    'waktu_akhir' => $slot->waktu_akhir,
                    'label' => date('H:i', strtotime($slot->waktu_awal)) . ' - ' . date('H:i', strtotime($slot->waktu_akhir)),
                    'kuota_tersedia' => $slot->maks_antrian - $booked_count,
                    'total_kuota' => $slot->maks_antrian
                );
            }
        }
        
        return $available_slots;
    }

    public function cek_slot($id_layanan, $date, $id_waktu_booking) {
        // Cari slot waktu yang sesuai
        $this->db->where('id_waktu_booking', $id_waktu_booking);
        $slot = $this->db->get('tbl_waktu_booking')->row();
        
        if (!$slot) {
            return false;
        }
        
        // Hitung antrian yang sudah ada
        $this->db->where('id_layanan', $id_layanan);
        $this->db->where('waktu_buat', $date);
        $this->db->where('id_waktu_booking', $slot->id_waktu_booking);
        $this->db->where('status =', 'buat');
        $booked_count = $this->db->count_all_results('tbl_antrian');
        
        return $booked_count < $slot->maks_antrian;
    }

    public function generate_no_antrian_booking($id_layanan,$date) {
    
        // 1. Dapatkan kode layanan dari service_id
        $layanan = $this->db->get_where('tbl_layanan', ['id_layanan' => $id_layanan])->row();
        if (!$layanan) {
            return false;
        }
        
        // 2. Cari nomor urut terakhir untuk kode layanan ini
        $this->db->like('no_antrian', $layanan->kode, 'after');
        $this->db->like('no_antrian', '-B', 'before');
        $this->db->where('waktu_buat',$date);
        $this->db->order_by('no_antrian', 'DESC');
        $last_queue = $this->db->get('tbl_antrian', 1)->row();
        
        // 3. Generate nomor urut berikutnya
        if ($last_queue) {
            // Ekstrak angka dari nomor antrian terakhir (misal: CS005-B -> 5)
            $last_number = intval(substr($last_queue->no_antrian, strlen($layanan->kode), 3));
            $next_number = $last_number + 1;
        } else {
            // Jika belum ada antrian untuk layanan ini
            $next_number = 1;
        }
        
        // 4. Format nomor antrian dengan leading zero
        $queue_number = $layanan->kode . str_pad($next_number, 3, '0', STR_PAD_LEFT) . '-B';
        
        return $queue_number;
    }

    public function cek_antrian_user($id_user, $date) {
        $this->db->where('id_user', $id_user);
        $this->db->where('DATE(waktu_buat)', $date);
        $this->db->where_in('status', ['buat', 'panggil', 'proses']);
        $query = $this->db->get('tbl_antrian');
        
        return $query->row();
    }

    public function get_web_set($table) {
        return $this->db->get_where($table, array('id' => '0'))->row();
    }

    public function set_user_otp($id_user, $otp) {
        $this->db->where('id_user', $id_user);
        return $this->db->update('user', ['token' => $otp]);
    }

    public function update_user_password($id_user, $password_hash) {
        $this->db->where('id_user', $id_user);
        return $this->db->update('user', ['password' => $password_hash]);
    }
}


