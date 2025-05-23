<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WebController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model'); // Load model jika diperlukan
        check_admin();
    }

    public function index(){
      $data['title']      = 'Web Setting';
      $data['conten']     = 'admin/page/web_setting/view';
      $data['web']        = $this->M_Model->get_web_set('tbl_web_set');
        $this->load->view('admin/template/template',$data);
    }

    public function simpan(){
        if ($this->input->post()) {
          // Mengambil data dari form
          $nama_web   = $this->input->post('nama_web'); // ID data yang akan diedit
          $alamat     = $this->input->post('alamat'); // ID data yang akan diedit
          $footer     = $this->input->post('footer'); // ID data yang akan diedit
          $kode_akses =  $this->input->post('kode_akses');
          $create_by  = $this->input->post('create_by');
          $id         = $this->input->post('id');

          // Validasi data
          if (empty($nama_web) || empty($alamat) || empty($footer) || empty($kode_akses) || empty($create_by)) {
              $response = array(
                  'status'    => 'error',
                  'message'   => 'semua input tidak boleh kosong.'
              );
          } else {            
                  // Menyiapkan data untuk diedit
                  $data = array(
                      'nama_web'    => $nama_web,
                      'alamat'      => $alamat,
                      'footer'      => $footer,
                      'kode_akses'  => $kode_akses,
                      'create_by'   => $create_by,
                  );

                  // Melakukan update data
                  if ($this->M_Model->update_data('tbl_web_set', $data, array('id' => $id))) {
                      $response = array(
                          'status'    => 'success',
                          'message'   => 'Data berhasil disimpan.'
                      );
                  } else {
                      $response = array(
                          'status'    => 'error',
                          'message'   => 'Terjadi kesalahan saat mengedit data.'
                      );
                  }
          }

          // Mengembalikan response dalam format JSON
          echo json_encode($response);
        } else {
            // Jika bukan request POST, tampilkan error
            show_error('No direct script access allowed', 403);
        }
    }
}