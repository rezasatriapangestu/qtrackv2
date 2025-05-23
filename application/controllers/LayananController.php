<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LayananController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model'); // Load model jika diperlukan
        check_admin();
    }

    public function index(){
      $data['title']      = 'Layanan Management';
      $data['conten']     = 'admin/page/layanan_management/view';
      $data['data_layanan'] = $this->M_Model->get_data('tbl_layanan');
      $this->load->view('admin/template/template',$data);
    }

    public function tambah(){
      $data['title']      = 'Tambah Layanan Management';
      $data['conten']     = 'admin/page/layanan_management/tambah';
      $this->load->view('admin/template/template',$data);
    }

    public function proses_tambah(){
      if ($this->input->post()) {
          $kode = $this->input->post('kode');
          $nama = $this->input->post('name');
          if ($this->M_Model->is_data('kode','tbl_layanan',$nama)) {
              $response = array(
                  'status'    => 'error',
                  'message'   => 'Layanan sudah ada didatabase.'
              );
          } else {
            
              $data   = array(
                  'kode'  => $kode,
                  'nama'  => $nama
              );

              if ($this->M_Model->Insert_Data($data,'tbl_layanan')) {
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
        $data['layanan'] = $this->M_Model->get_data_by_id('tbl_layanan','id_layanan', $id);
        
        // Menyiapkan data untuk view
        $data['title']      = 'Edit Layanan Management';
        $data['conten']    = 'admin/page/layanan_management/edit'; // Path ke view edit
        $this->load->view('admin/template/template', $data);
    }

    public function proses_edit(){
        // Memeriksa apakah request adalah POST
        if ($this->input->post()) {
            // Mengambil data dari form
            $id_layanan   = $this->input->post('id_layanan'); // ID data yang akan diedit
            $kode =  $this->input->post('kode');
            $nama = $this->input->post('name');
    
            // Validasi data
            if (empty($kode) || empty($nama)) {
                $response = array(
                    'status'    => 'error',
                    'message'   => 'Kode dan Nama layanan tidak boleh kosong.'
                );
            } else {
                // Memeriksa apakah kode sudah digunakan oleh data lain (kecuali data yang sedang diedit)
                $existing_data = $this->M_Model->get_data_by_condition('tbl_layanan', array('kode' => $kode, 'id_layanan !=' => $id_layanan));
                if ($existing_data) {
                    $response = array(
                        'status'    => 'error',
                        'message'   => 'Kode layanan sudah digunakan oleh data lain.'
                    );
                } else {
                    // Menyiapkan data untuk diedit
                    $data = array(
                        'kode'  => $kode,
                        'nama'  => $nama
                    );
    
                    // Melakukan update data
                    if ($this->M_Model->update_data('tbl_layanan', $data, array('id_layanan' => $id_layanan))) {
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
      $result = $this->M_Model->hapus('id_layanan', $id, 'tbl_layanan'); // Ganti 'nama_tabel' dengan nama tabel yang sesuai

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
      redirect('LayananController');
    }
}
