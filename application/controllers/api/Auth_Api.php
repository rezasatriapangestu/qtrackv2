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

class Auth_api extends RestController {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model');
        $this->load->library(['form_validation', 'session']);

        // Set header CORS untuk API
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, Cookie');
        header('Access-Control-Allow-Credentials: true');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }

    // ========== REGISTER ==========
    public function register_post() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            return $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan atau format JSON tidak valid.'
            ], 400);
        }

        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'required');
        $this->form_validation->set_rules('no_tlp', 'Nomor Telepon', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validasi gagal. Silakan lengkapi data dengan benar.',
                'errors' => $this->form_validation->error_array()
            ], 400);
        }

        if ($this->M_Model->is_email($input['email'])) {
            return $this->response([
                'status' => false,
                'message' => 'Email sudah terdaftar. Silakan gunakan email lain atau login.'
            ], 409);
        }

        $id_user = $this->M_Model->generate_user_id();
        $token = substr(bin2hex(random_bytes(3)), 0, 6);

        $data = [
            'id_user'      => $id_user,
            'nama_lengkap' => $input['nama_lengkap'],
            'email'        => $input['email'],
            'no_tlp'       => $input['no_tlp'],
            'password'     => password_hash($input['password'], PASSWORD_BCRYPT),
            'role'         => 'user',
            'token'        => $token,
            'status'       => '0',
            'foto_profile' => 'default.png'
        ];

        if ($this->M_Model->register_user($data)) {
            if ($this->_kirim_email_konfirmasi($data['email'], $token)) {
                return $this->response([
                    'status' => true,
                    'message' => 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.'
                ], 201);
            } else {
                return $this->response([
                    'status' => false,
                    'message' => 'Registrasi berhasil, namun gagal mengirim email verifikasi. Silakan hubungi admin.'
                ], 500);
            }
        }

        return $this->response([
            'status' => false,
            'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
        ], 500);
    }

    // ========== LOGIN ==========
    public function login_post() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            return $this->response([
                'status' => false,
                'message' => 'Data tidak ditemukan atau format JSON tidak valid.'
            ], 400);
        }

        $this->form_validation->set_data($input);
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            return $this->response([
                'status' => false,
                'message' => 'Validasi gagal. Silakan lengkapi data dengan benar.',
                'errors' => $this->form_validation->error_array()
            ], 400);
        }

        $email = $input['email'];
        $password = $input['password'];
        $user = $this->M_Model->get_user_by_email($email);

        if ($user && password_verify($password, $user->password)) {
            if ($user->status != '1') {
                return $this->response([
                    'status' => false,
                    'message' => 'Akun Anda belum aktif. Silakan cek email untuk verifikasi.'
                ], 403);
            }

            $token = bin2hex(random_bytes(32));
            $this->session->set_userdata([
                'user_id' => $user->id_user,
                'token'   => $token
            ]);

            return $this->response([
                'status' => true,
                'message' => 'Login berhasil. Selamat datang kembali!',
                'data' => [
                    'user_id'      => $user->id_user,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email'        => $user->email,
                    'role'         => $user->role,
                    'token'        => $token,
                    'foto_profile' => isset($user->foto_profile) ? $user->foto_profile : null,
                    'no_tlp'       => isset($user->no_tlp) ? $user->no_tlp : null
                ]
            ], 200);
        }

        return $this->response([
            'status' => false,
            'message' => 'Email atau password salah. Silakan cek kembali data Anda.'
        ], 401);
    }

    // ========== KONFIRMASI TOKEN ==========
    public function konfirmasi_post() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['token']) || !isset($input['email'])) {
            return $this->response([
                'status' => false,
                'message' => 'Email dan kode konfirmasi diperlukan.'
            ], RestController::HTTP_BAD_REQUEST);
        }

        $token = $input['token'];
        $email = $input['email'];

        $user = $this->M_Model->get_user_by_token($token);

        if ($user) {
            if ($user->email != $email) {
                return $this->response([
                    'status' => false,
                    'message' => 'Kode konfirmasi tidak sesuai dengan email yang diberikan.'
                ], RestController::HTTP_UNAUTHORIZED);
            }

            if ($user->status == '0') {
                $this->M_Model->activate_user($user->id_user);
                return $this->response([
                    'status' => true,
                    'message' => 'Akun Anda berhasil diaktifkan. Silakan login.'
                ], RestController::HTTP_OK);
            } else {
                return $this->response([
                    'status' => true,
                    'message' => 'Akun Anda sudah aktif. Silakan login.'
                ], RestController::HTTP_OK);
            }
        } else {
            return $this->response([
                'status' => false,
                'message' => 'Kode konfirmasi tidak valid atau sudah kadaluarsa.'
            ], RestController::HTTP_NOT_FOUND);
        }
    }

    // ========== FORGOT PASSWORD (Request OTP) ==========
    public function forgot_password_post() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['email'])) {
            return $this->response([
                'status' => false,
                'message' => 'Email diperlukan.'
            ], 400);
        }

        $user = $this->M_Model->get_user_by_email($input['email']);
        if (!$user) {
            return $this->response([
                'status' => false,
                'message' => 'Email tidak ditemukan.'
            ], 404);
        }

        $otp = substr(bin2hex(random_bytes(3)), 0, 6);
        $this->M_Model->set_user_otp($user->id_user, $otp);

        if ($this->_kirim_email_otp($user->email, $otp)) {
            return $this->response([
                'status' => true,
                'message' => 'Kode OTP telah dikirim ke email Anda.'
            ], 200);
        } else {
            return $this->response([
                'status' => false,
                'message' => 'Gagal mengirim OTP. Coba lagi.'
            ], 500);
        }
    }

    // ========== RESET PASSWORD VIA OTP ==========
    public function reset_password_post() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['email'], $input['otp'], $input['new_password'])) {
            return $this->response([
                'status' => false,
                'message' => 'Email, OTP, dan password baru diperlukan.'
            ], 400);
        }

        $user = $this->M_Model->get_user_by_email($input['email']);
        if (!$user || $user->token !== $input['otp']) {
            return $this->response([
                'status' => false,
                'message' => 'OTP tidak valid.'
            ], 400);
        }

        $this->M_Model->update_user_password($user->id_user, password_hash($input['new_password'], PASSWORD_BCRYPT));
        // Hapus OTP/token setelah digunakan
        $this->M_Model->set_user_otp($user->id_user, null);

        return $this->response([
            'status' => true,
            'message' => 'Password berhasil diubah. Silakan login.'
        ], 200);
    }

    // ========== CHANGE PASSWORD (with old password) ==========
    public function change_password_post() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['email'], $input['old_password'], $input['new_password'])) {
            return $this->response([
                'status' => false,
                'message' => 'Email, password lama, dan password baru diperlukan.'
            ], 400);
        }

        $user = $this->M_Model->get_user_by_email($input['email']);
        if (!$user || !password_verify($input['old_password'], $user->password)) {
            return $this->response([
                'status' => false,
                'message' => 'Password lama salah.'
            ], 400);
        }

        $this->M_Model->update_user_password($user->id_user, password_hash($input['new_password'], PASSWORD_BCRYPT));
        return $this->response([
            'status' => true,
            'message' => 'Password berhasil diubah.'
        ], 200);
    }

    // ========== KIRIM EMAIL OTP UNTUK RESET PASSWORD ==========
    private function _kirim_email_otp($email, $otp) {
        $this->load->library('email');
        $config = [
            'protocol'    => 'smtp',
            'smtp_host'   => 'mail.bitap.my.id',
            'smtp_port'   => 465,
            'smtp_user'   => 'qtrack@bitap.my.id',
            'smtp_pass'   => 'Semangat123*',
            'smtp_crypto' => 'ssl',
            'mailtype'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n"
        ];
        $this->email->initialize($config);
        $this->email->from('qtrack@bitap.my.id', 'QTRACK');
        $this->email->to($email);
        $this->email->subject('OTP Reset Password');
        $message = "
            <h2>Permintaan Reset Password</h2>
            <p>Kode OTP Anda untuk reset password:</p>
            <b>$otp</b>
            <p>Jangan berikan kode ini ke siapapun.</p>
        ";
        $this->email->message($message);
        return $this->email->send();
    }

    // ========== KIRIM EMAIL ==========
    private function _kirim_email_konfirmasi($email, $token) {
        $this->load->library('email');

        $config = [
            'protocol'    => 'smtp',
            'smtp_host'   => 'mail.bitap.my.id',
            'smtp_port'   => 465,
            'smtp_user'   => 'qtrack@bitap.my.id',
            'smtp_pass'   => 'Semangat123*', // GANTI DI PRODUKSI!
            'smtp_crypto' => 'ssl',
            'mailtype'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n"
        ];

        $this->email->initialize($config);
        $this->email->from('qtrack@bitap.my.id', 'QTRACK');
        $this->email->to($email);
        $this->email->subject('Konfirmasi Registrasi');

        $message = "
            <h2>Terima kasih telah mendaftar!</h2>
            <p>Ini adalah email otomatis untuk memberikan kode OTP yang diperlukan untuk mengaktifkan akun Anda.</p>
            <p>Kode OTP Anda:</p>
            <b>$token</b>
        ";

        $this->email->message($message);
        return $this->email->send();
    }

    public function logout_post() {
        $this->session->unset_userdata(['user_id', 'token']);
        $this->session->sess_destroy();
        return $this->response([
            'status' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }
}

