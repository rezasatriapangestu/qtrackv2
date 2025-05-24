<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Tambahan: pastikan RestController sudah ter-load
if (!class_exists('\chriskacerguis\RestServer\RestController')) {
    // Coba require dari vendor jika pakai composer
    if (file_exists(APPPATH . '../vendor/autoload.php')) {
        require_once APPPATH . '../vendor/autoload.php';
    }
    // Jika tidak pakai composer, coba dari libraries
    elseif (file_exists(APPPATH . 'libraries/RestController.php')) {
        require_once APPPATH . 'libraries/RestController.php';
    }
}

use chriskacerguis\RestServer\RestController;

class User_Api extends RestController {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model');
        $this->load->library(['form_validation', 'session']);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, Cookie');
        header('Access-Control-Allow-Credentials: true');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    // GET: /api/user_api/profile
    public function profile_get() {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return $this->response(['status' => false, 'message' => 'Unauthorized'], 401);
        }
        $user = $this->M_Model->get_user_by_id($user_id);
        if ($user) {
            return $this->response(['status' => true, 'data' => $user], 200);
        }
        return $this->response(['status' => false, 'message' => 'User not found'], 404);
    }

    // POST/PUT: /api/user_api/profile
    public function profile_post() {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return $this->response(['status' => false, 'message' => 'Unauthorized'], 401);
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            return $this->response(['status' => false, 'message' => 'Invalid input'], 400);
        }
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('no_tlp', 'Nomor Telepon', 'required');
        // Email optional, password optional
        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $this->form_validation->error_array()
            ], 400);
        }
        $update = [
            'nama_lengkap' => $input['nama_lengkap'],
            'no_tlp' => $input['no_tlp']
        ];
        if (!empty($input['email'])) $update['email'] = $input['email'];
        if (!empty($input['foto_profile'])) $update['foto_profile'] = $input['foto_profile'];
        // Hapus logic update password dari sini

        $this->db->where('id_user', $user_id);
        $result = $this->db->update('tbl_users', $update);
        if ($result) {
            return $this->response(['status' => true, 'message' => 'Profil berhasil diupdate'], 200);
        }
        return $this->response(['status' => false, 'message' => 'Gagal update profil'], 500);
    }

    // POST: /api/user_api/update_password
    public function update_password_post() {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return $this->response(['status' => false, 'message' => 'Unauthorized'], 401);
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            return $this->response(['status' => false, 'message' => 'Invalid input'], 400);
        }
        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('password_lama', 'Password Lama', 'required');
        $this->form_validation->set_rules('password_baru', 'Password Baru', 'required|min_length[6]');
        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $this->form_validation->error_array()
            ], 400);
        }

        // Ambil password lama dari database
        $user = $this->M_Model->get_user_by_id($user_id);
        if (!$user || !isset($user['password'])) {
            return $this->response(['status' => false, 'message' => 'User tidak ditemukan'], 404);
        }
        if (!password_verify($input['password_lama'], $user['password'])) {
            return $this->response(['status' => false, 'message' => 'Password lama salah'], 400);
        }

        $new_password_hash = password_hash($input['password_baru'], PASSWORD_BCRYPT);
        $this->db->where('id_user', $user_id);
        $result = $this->db->update('tbl_users', ['password' => $new_password_hash]);
        if ($result) {
            return $this->response(['status' => true, 'message' => 'Password berhasil diupdate'], 200);
        }
        return $this->response(['status' => false, 'message' => 'Gagal update password'], 500);
    }

    // POST: /api/user_api/upload_foto
    public function upload_foto_post() {
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return $this->response(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        if (empty($_FILES['foto_profile']['name'])) {
            return $this->response(['status' => false, 'message' => 'Tidak ada file yang diupload'], 400);
        }

        $config['upload_path']   = './assets/img/profile/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 2048; // 2MB
        $config['file_name']     = 'profile_' . $user_id . '_' . time();

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('foto_profile')) {
            return $this->response([
                'status' => false,
                'message' => $this->upload->display_errors('', '')
            ], 400);
        }

        $upload_data = $this->upload->data();
        $file_name = $upload_data['file_name'];

        // Resize image setelah upload agar ukuran file lebih kecil
        $config_resize['image_library'] = 'gd2';
        $config_resize['source_image'] = $upload_data['full_path'];
        $config_resize['maintain_ratio'] = TRUE;
        $config_resize['width'] = 400; // atur lebar maksimal
        $config_resize['height'] = 400; // atur tinggi maksimal
        $config_resize['quality'] = '70%'; // kompresi kualitas

        $this->load->library('image_lib', $config_resize);

        if (!$this->image_lib->resize()) {
            // Jika resize gagal, hapus file dan return error
            @unlink($upload_data['full_path']);
            return $this->response([
                'status' => false,
                'message' => 'Gagal memperkecil gambar: ' . $this->image_lib->display_errors('', '')
            ], 500);
        }
        $this->image_lib->clear();

        // Update nama file di database
        $this->db->where('id_user', $user_id);
        $this->db->update('tbl_users', ['foto_profile' => $file_name]);

        return $this->response([
            'status' => true,
            'message' => 'Foto profile berhasil diupload',
            'file_name' => $file_name,
            'url' => base_url('assets/img/profile/' . $file_name)
        ], 200);
    }
}
