<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model'); // Load model jika diperlukan
        check_admin();
    }

    public function index(){
      $data['title']      = 'Booking Management';
      $data['conten']     = 'admin/page/booking_management/view';
      $data['data_waktu'] = $this->M_Model->get_data('tbl_waktu_booking');
      $this->load->view('admin/template/template',$data);
    }

    public function tambah(){
      $data['title']      = 'Tambah Booking Management';
      $data['conten']     = 'admin/page/booking_management/tambah';
      $this->load->view('admin/template/template',$data);
    }

    public function proses_tambah(){
      if ($this->input->post()) {
          $waktu_awal   = $this->input->post('waktu_awal');
          $waktu_akhir  = $this->input->post('waktu_akhir');
          $maks_antrian = $this->input->post('maks_antrian');
          if ($this->M_Model->is_data('waktu_awal','tbl_waktu_booking',$waktu_awal)) {
              $response = array(
                  'status'    => 'error',
                  'message'   => 'Waktu booking sudah ada didatabase.'
              );
          } else {
            
              $data   = array(
                  'waktu_awal'      => $waktu_awal,
                  'waktu_akhir'     => $waktu_akhir,
                  'maks_antrian'    => $maks_antrian
              );

              if ($this->M_Model->Insert_Data($data,'tbl_waktu_booking')) {
                  $response = array(
                    'status'    => 'success',
                    'message'   => 'Berhasil menyimpan data.'
                );
              } else {
                  $response = array(
                      'status'    => 'error',
                      'message'   => 'Terjadi kesalahan saat menyimpan data.'
                  );
              }
          }

          echo json_encode($response);
      } else {
          show_error('No direct script access allowed', 403);
      }
    }

    public function edit($id){
        // Mengambil data layanan berdasarkan ID
        $data['waktu_booking'] = $this->M_Model->get_data_by_id('tbl_waktu_booking','id_waktu_booking', $id);
        
        // Menyiapkan data untuk view
        $data['title']      = 'Edit Booking Management';
        $data['conten']    = 'admin/page/booking_management/edit'; // Path ke view edit
        $this->load->view('admin/template/template', $data);
    }

    public function proses_edit(){
        // Memeriksa apakah request adalah POST
        if ($this->input->post()) {
            // Mengambil data dari form
            $id_waktu_booking       = $this->input->post('id_waktu_booking'); // ID data yang akan diedit
            $waktu_awal             =  $this->input->post('waktu_awal');
            $waktu_akhir            =  $this->input->post('waktu_akhir');
            $maks_antrian           = $this->input->post('maks_antrian');
    
            // Validasi data
            if (empty($waktu_awal) || empty($waktu_akhir) || empty($maks_antrian)) {
                $response = array(
                    'status'    => 'error',
                    'message'   => 'Field tidak boleh kosong.'
                );
            } else {
                // Memeriksa apakah kode sudah digunakan oleh data lain (kecuali data yang sedang diedit)
                $existing_data = $this->M_Model->get_data_by_condition('tbl_waktu_booking', array('waktu_awal' => $waktu_awal, 'waktu_akhir' => $waktu_akhir));
                if ($existing_data) {
                    $response = array(
                        'status'    => 'error',
                        'message'   => 'Waktu booking sudah digunakan oleh data lain.'
                    );
                } else {
                    // Menyiapkan data untuk diedit
                    $data = array(
                        'waktu_awal'    => $waktu_awal,
                        'waktu_akhir'   => $waktu_akhir,
                        'maks_antrian'  => $maks_antrian
                    );
    
                    // Melakukan update data
                    if ($this->M_Model->update_data('tbl_waktu_booking', $data, array('id_waktu_booking' => $id_waktu_booking))) {
                        $response = array(
                            'status'    => 'success',
                            'message'   => 'Data berhasil diedit.'
                        );
                    } else {
                        $response = array(
                            'status'    => 'error',
                            'message'   => 'Terjadi kesalahan saat mengedit data.'
                        );
                    }
                }
            }
    
            // Mengembalikan response dalam format JSON
            echo json_encode($response);
        } else {
            // Jika bukan request POST, tampilkan error
            show_error('No direct script access allowed', 403);
        }
    }

    public function hapus($id) {
      // Panggil fungsi hapus dari model
      $result = $this->M_Model->hapus('id_waktu_booking', $id, 'tbl_waktu_booking'); // Ganti 'nama_tabel' dengan nama tabel yang sesuai

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
      redirect('BookingController');
    }
}
