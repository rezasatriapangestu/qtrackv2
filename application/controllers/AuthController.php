<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model');
    }

    public function index() {
        if($this->session->userdata('is_login') === TRUE){
            redirect('HomeController');
        }
        $data['page']   = 'login';
        $data['title']  = 'Login';
        $this->load->view('auth/template.php', $data);
    }

    public function proses_login(){
        if($this->input->post()){
            $email      = $this->input->post('email');
            $password   = $this->input->post('password');

            $user = $this->M_Model->get_user_by_email($email);
            if($user){
                if (password_verify($password, $user->password)) {
                    // Set session data
                    $this->session->set_userdata('user_id', $user->id_user);
                    $this->session->set_userdata('is_login', TRUE);
                    $this->session->set_userdata('user_email', $user->email);
                    $this->session->set_userdata('username', $user->nama_lengkap);
                    $this->session->set_userdata('roles', $user->role);
                    $response = array(
                        'status'    => 'success',
                        'message'   => 'Login berhasil. Selamat datang kembali!'
                    );
                } else {
                    $response = array(
                        'status'    => 'error',
                        'message'   => 'Password yang Anda masukkan salah. Silakan coba lagi.'
                    );
                }
            }else{
                $response = array(
                    'status'    => 'error',
                    'message'   => 'Email tidak ditemukan. Silakan daftar terlebih dahulu.'
                );
            }
            echo json_encode($response);
        }
    }

    public function register() {
        $data['page']   = 'register';
        $data['title']  = 'Register';
        $this->load->view('auth/template.php', $data);
    }

    public function proses_register() {
        if ($this->input->post()) {
            $nama_lengkap = $this->input->post('nama_lengkap');
            $no_tlp       = $this->input->post('no_tlp');
            $email        = $this->input->post('email');
            $password     = $this->input->post('password');
    
            if ($this->M_Model->is_email($email)) {
                $response = array(
                    'status'    => 'error',
                    'message'   => 'Email sudah terdaftar. Silakan gunakan email lain atau login.'
                );
            } else {
                $id_user = $this->M_Model->generate_user_id();
    
                // Generate Token Konfirmasi
                $token = substr(bin2hex(random_bytes(3)), 0, 6);
                $data   = array(
                    'id_user'       => $id_user,
                    'nama_lengkap'  => $nama_lengkap,
                    'email'         => $email,
                    'no_tlp'        => $no_tlp,
                    'password'      => password_hash($password, PASSWORD_BCRYPT),
                    'role'          => 'user',
                    'token'         => $token,
                    'status'        => '0',
                    'foto_profile'  => 'default.png'
                );
    
                if ($this->M_Model->register_user($data)) {
                    if ($this->kirim_email_konfirmasi($email, $token)) {
                        $response = array(
                            'status'    => 'success',
                            'message'   => 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.'
                        );
                    } else {
                        $response = array(
                            'status'    => 'error',
                            'message'   => 'Registrasi berhasil, namun gagal mengirim email verifikasi. Silakan hubungi admin.'
                        );
                    }
                } else {
                    $response = array(
                        'status'    => 'error',
                        'message'   => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
                    );
                }
            }
    
            echo json_encode($response);
        } else {
            show_error('No direct script access allowed', 403);
        }
    }
    
    // Fungsi untuk mengirim email konfirmasi
    private function kirim_email_konfirmasi($email, $token) {
        $this->load->library('email'); // Load library email
    
        $config = array(
            'protocol'      => 'smtp',
            'smtp_host'     => 'mail.bitap.my.id', 
            'smtp_port'     => 465, 
            'smtp_user'     => 'qtrack@bitap.my.id', 
            'smtp_pass'     => 'Semangat123*', 
            'smtp_crypto'   => 'ssl', 
            'mailtype'      => 'html', 
            'charset'       => 'utf-8', 
            'newline'       => "\r\n" 
        );
    
        $this->email->initialize($config);
        $this->email->from('qtrack@bitap.my.id', get_web_info('nama_web')); 
        $this->email->to($email);
        $this->email->subject('Konfirmasi Registrasi');
        
        // Buat link konfirmasi
        $link_konfirmasi = site_url("Konfirmasi?token=" . urlencode(trim($token)));

        // Isi email
        $message = "
            <h2>Terima kasih telah mendaftar!</h2>
            <p>Ini adalah email otomatis untuk memberikan kode OTP yang diperlukan untuk mengaktifkan akun Anda.</p>
            <p>Kode OTP Anda:</p>
            <b>".$token."</b>
        ";
    
        $this->email->message($message);
    
        // Kirim email
        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger(); // Tampilkan debug informasi
            log_message('error', 'Gagal mengirim email: ' . $this->email->print_debugger());
            return false;
        }
    }

    public function konfirmasi(){
        $data['page']   = 'konfirmasi';
        $data['title']  = 'Konfirmasi';
        $this->load->view('auth/template.php', $data);
    }

    public function proses_konfirmasi(){
        $token = $this->input->post('token');
        $email = $this->input->post('email');
        if (empty($token) || empty($email)) {
            $response = array(
                'status'    => 'error',
                'message'   => 'Email dan kode konfirmasi diperlukan.'
            );
        } else {
            $user = $this->M_Model->get_user_by_token($token);

            if ($user) {
                if ($user->email != $email) {
                    $response = array(
                        'status'    => 'error',
                        'message'   => 'Kode konfirmasi tidak sesuai dengan email yang diberikan.'
                    );
                } else if ($user->status == '0') {
                    $this->M_Model->activate_user($user->id_user);
                    $response = array(
                        'status'    => 'success',
                        'message'   => 'Akun Anda berhasil diaktifkan. Silakan login.'
                    );
                } else {
                    $response = array(
                        'status'    => 'info',
                        'message'   => 'Akun Anda sudah aktif. Silakan login.'
                    );
                }
            } else {
                $response = array(
                    'status'    => 'error',
                    'message'   => 'Kode konfirmasi tidak valid atau sudah kadaluarsa.'
                );
            }
        }
        echo json_encode($response);
    }

    public function logout() {
        // $this->load->library('session');
        // Hapus semua data session
        $this->session->unset_userdata('is_login');
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_email');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('roles');
    
        // Hancurkan session
        $this->session->sess_destroy();

    
        // Redirect ke halaman login
        redirect('AuthController');
        // echo "Logout";
    }

    
}
