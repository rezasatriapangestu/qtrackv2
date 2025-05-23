<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserManagementController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model'); // Load model jika diperlukan
        check_admin();
    }

    public function index(){
      $data['title']      = 'Users Management';
      $data['conten']     = 'admin/page/user_managemen/view';
      $data['data_users'] = $this->M_Model->get_data('tbl_users');
      $this->load->view('admin/template/template',$data);
    }

    public function tambah(){
      $data['title']      = 'Tambah User Management';
      $data['conten']     = 'admin/page/user_managemen/tambah';
      $data['data_loket'] = $this->M_Model->get_data('tbl_loket');
      $this->load->view('admin/template/template',$data);
    }

    public function proses_tambah() {
        // Inisialisasi variabel response
        $response = array(
            'status'  => 'error',
            'message' => 'Terjadi kesalahan yang tidak diketahui.'
        );
    
        if ($this->input->post()) {
            // Ambil data dari form
            $nama_lengkap = $this->input->post('nama_lengkap');
            $no_tlp       = $this->input->post('no_tlp');
            $email        = $this->input->post('email');
            $password     = $this->input->post('password');
            $role         = $this->input->post('role');
            $loket        = $this->input->post('loket');
    
            // Validasi data
            if (empty($nama_lengkap) || empty($no_tlp) || empty($email) || empty($password) || empty($role)) {
                $response = array(
                    'status'  => 'error',
                    'message' => 'Semua field harus diisi, kecuali loket untuk role admin.'
                );
            } else {
                // Cek apakah email sudah terdaftar
                if ($this->M_Model->is_email($email)) {
                    $response = array(
                        'status'  => 'error',
                        'message' => 'Email sudah terdaftar.'
                    );
                } else {
                    // Jika role bukan admin, validasi loket
                    if ($role !== 'admin' && empty($loket)) {
                        $response = array(
                            'status'  => 'error',
                            'message' => 'Loket harus diisi untuk role selain admin.'
                        );
                    } else {
                        // Generate ID User
                        $id_user = $this->M_Model->generate_user_id();
    
                        // Generate Token Konfirmasi
                        $token = substr(bin2hex(random_bytes(3)), 0, 6);
    
                        // Siapkan data untuk disimpan
                        $data = array(
                            'id_user'       => $id_user,
                            'nama_lengkap'  => $nama_lengkap,
                            'email'         => $email,
                            'no_tlp'        => $no_tlp,
                            'password'      => password_hash($password, PASSWORD_BCRYPT),
                            'role'          => $role,
                            'token'         => $token,
                            'id_loket'      => ($role !== 'admin') ? $loket : NULL, // Jika admin, set id_loket ke NULL
                            'status'        => '1'
                        );
    
                        // Simpan data
                        if ($this->M_Model->Insert_Data($data, 'tbl_users')) {
                            $response = array(
                                'status'  => 'success',
                                'message' => 'Berhasil menyimpan data.'
                            );
                        } else {
                            $response = array(
                                'status'  => 'error',
                                'message' => 'Terjadi kesalahan saat menyimpan data.'
                            );
                        }
                    }
                }
            }
        } else {
            // Jika bukan request POST, tampilkan error
            $response = array(
                'status'  => 'error',
                'message' => 'No direct script access allowed.'
            );
        }
    
        // Kembalikan response dalam format JSON
        echo json_encode($response);
    }
    
    private function save_user_data($nama_lengkap, $no_tlp, $email, $password, $role, $loket) {
        // Generate ID User
        $id_user = $this->M_Model->generate_user_id();
    
        // Generate Token Konfirmasi
        $token = substr(bin2hex(random_bytes(3)), 0, 6);
    
        // Siapkan data untuk disimpan
        $data = array(
            'id_user'       => $id_user,
            'nama_lengkap'  => $nama_lengkap,
            'email'         => $email,
            'no_tlp'        => $no_tlp,
            'password'      => password_hash($password, PASSWORD_BCRYPT),
            'role'          => $role,
            'token'         => $token,
            'id_loket'      => ($role !== 'admin') ? $loket : null, // Jika admin, set id_loket ke null
            'status'        => '1'
        );
    
        // Simpan data
        if ($this->M_Model->Insert_Data($data, 'tbl_users')) {
            $response = array(
                'status'  => 'success',
                'message' => 'Berhasil menyimpan data.'
            );
        } else {
            $response = array(
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            );
        }
    
        echo json_encode($response);
    }

    public function edit($id_user) {
        // Ambil data user berdasarkan ID
        $data['user'] = $this->M_Model->get_data_by_id('tbl_users','id_user', $id_user);
    
        // Jika data tidak ditemukan, tampilkan error 404
        if (!$data['user']) {
            show_404();
        }
    
        // Ambil data loket untuk dropdown
        $data['data_loket'] = $this->M_Model->get_data('tbl_loket');
    
        // Set judul dan konten view
        $data['title']  = 'Edit User Management';
        $data['conten'] = 'admin/page/user_managemen/edit';
    
        // Load view
        $this->load->view('admin/template/template', $data);
    }
    
    public function proses_edit() {
        // Inisialisasi variabel response
        $response = array(
            'status'  => 'error',
            'message' => 'Terjadi kesalahan yang tidak diketahui.'
        );
    
        if ($this->input->post()) {
            // Ambil data dari form
            $id_user     = $this->input->post('id_user');
            $nama_lengkap = $this->input->post('nama_lengkap');
            $no_tlp      = $this->input->post('no_tlp');
            $email       = $this->input->post('email');
            $password    = $this->input->post('password');
            $role        = $this->input->post('role');
            $loket       = $this->input->post('loket');
    
            // Validasi data
            if (empty($nama_lengkap) || empty($no_tlp) || empty($email) || empty($role)) {
                $response = array(
                    'status'  => 'error',
                    'message' => 'Semua field harus diisi, kecuali password dan loket untuk role admin.'
                );
            } else {
                // Cek apakah email sudah digunakan oleh user lain (kecuali user yang sedang diedit)
                $existing_email = $this->M_Model->get_data_by_condition('tbl_users', array('email' => $email, 'id_user !=' => $id_user));
                if ($existing_email) {
                    $response = array(
                        'status'  => 'error',
                        'message' => 'Email sudah digunakan oleh user lain.'
                    );
                } else {
                    // Jika role bukan admin, validasi loket
                    if ($role !== 'admin' && empty($loket)) {
                        $response = array(
                            'status'  => 'error',
                            'message' => 'Loket harus diisi untuk role selain admin.'
                        );
                    } else {
                        // Jika role bukan admin, cek apakah id_loket sudah digunakan oleh user lain (kecuali user yang sedang diedit)
                        if ($role !== 'admin') {
                            $existing_loket = $this->M_Model->get_data_by_condition('tbl_users', array('id_loket' => $loket, 'id_user !=' => $id_user));
                            if ($existing_loket) {
                                $response = array(
                                    'status'  => 'error',
                                    'message' => 'Loket sudah digunakan oleh user lain.'
                                );
                            } else {
                                // Lanjutkan proses update
                                $this->update_user_data($id_user, $nama_lengkap, $no_tlp, $email, $password, $role, $loket);
                                return; // Keluar dari fungsi setelah mengembalikan response
                            }
                        } else {
                            // Jika role admin, lanjutkan proses update tanpa validasi loket
                            $this->update_user_data($id_user, $nama_lengkap, $no_tlp, $email, $password, $role, $loket);
                            return; // Keluar dari fungsi setelah mengembalikan response
                        }
                    }
                }
            }
        } else {
            // Jika bukan request POST, tampilkan error
            $response = array(
                'status'  => 'error',
                'message' => 'No direct script access allowed.'
            );
        }
    
        // Kembalikan response dalam format JSON
        echo json_encode($response);
    }
    
    private function update_user_data($id_user, $nama_lengkap, $no_tlp, $email, $password, $role, $loket) {
        // Siapkan data untuk diupdate
        $data = array(
            'nama_lengkap' => $nama_lengkap,
            'no_tlp'       => $no_tlp,
            'email'        => $email,
            'role'         => $role,
            'id_loket'     => ($role !== 'admin') ? $loket : null // Jika admin, set id_loket ke null
        );
    
        // Jika password diisi, update password
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }
    
        // Update data
        if ($this->M_Model->update_data('tbl_users', $data, array('id_user' => $id_user))) {
            $response = array(
                'status'  => 'success',
                'message' => 'Data berhasil diupdate.'
            );
        } else {
            $response = array(
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate data.'
            );
        }
    
        // Kembalikan response dalam format JSON
        echo json_encode($response);
    }

    public function hapus($id) {
      // Panggil fungsi hapus dari model
      $result = $this->M_Model->hapus('id_user', $id, 'tbl_users'); // Ganti 'nama_tabel' dengan nama tabel yang sesuai

      // Redirect ke halaman tertentu setelah penghapusan
      if ($result) {
          $this->session->set_flashdata('swal', [
              'icon' => 'success',
              'title' => 'Sukses!',
              'text' => 'Data berhasil dihapus.'
          ]);
      } else {
          $this->session->set_flashdata('swal', [
              'icon' => 'error',
              'title' => 'Gagal!',
              'text' => 'Gagal menghapus data.'
          ]);
      }
      redirect('UserManagementController');
    }
}
