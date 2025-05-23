<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoketController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Model'); // Load model jika diperlukan
        check_admin();
    }

    public function index(){
      $data['title']      = 'Loket Management';
      $data['conten']     = 'admin/page/loket_management/view';
      $joins = array(
            array(
                'table' => 'tbl_layanan', // Tabel yang di-join
                'condition' => 'tbl_loket.id_layanan = tbl_layanan.id_layanan', // Kondisi join
                'type' => 'inner' // Tipe join
            )
        );
        $select = 'tbl_loket.*, tbl_layanan.nama AS nama_layanan';
        // Mengambil data dengan relasi
        $data['data_loket'] = $this->M_Model->get_relation(
            'tbl_loket', // Tabel utama
            $joins,
            $select
        );
        $this->load->view('admin/template/template',$data);
    }

    public function tambah(){
      $data['title']        = 'Tambah Loket Management';
      $data['data_layanan'] = $this->M_Model->get_data('tbl_layanan');
      $data['conten']       = 'admin/page/loket_management/tambah';
      $this->load->view('admin/template/template',$data);
    }

    public function proses_tambah(){
      if ($this->input->post()) {
          $nama         = $this->input->post('name');
          $id_layanan   = $this->input->post('id_layanan');
          $status       = $this->input->post('status');
          $jenis        = $this->input->post('jenis');

            $table = 'tbl_loket';
            $where = array(
                'nama'      => $nama,
                'jenis'     => $jenis
            );

          if ($this->M_Model->GetWHERE($table, $where)) {
              $response = array(
                  'status'    => 'error',
                  'message'   => 'Loket sudah ada didatabase.'
              );
          } else {
            
              $data   = array(
                  'nama'        => $nama,
                  'id_layanan'  => $id_layanan,
                  'status'      => $status,
                  'jenis'       => $jenis
              );

              if ($this->M_Model->Insert_Data($data,'tbl_loket')) {
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
        $data['loket'] = $this->M_Model->get_data_by_id('tbl_loket','id_loket', $id);
        $data['data_layanan'] = $this->M_Model->get_data('tbl_layanan');
        
        // Menyiapkan data untuk view
        $data['title']      = 'Edit Loket Management';
        $data['conten']    = 'admin/page/loket_management/edit'; // Path ke view edit
        $this->load->view('admin/template/template', $data);
    }

    public function proses_edit() {
        // Memeriksa apakah request adalah POST
        if ($this->input->post()) {
            // Mengambil data dari form
            $id_loket    = $this->input->post('id_loket'); // ID data yang akan diedit
            $nama        = $this->input->post('name');
            $id_layanan  = $this->input->post('id_layanan');
            $status      = $this->input->post('status');
            $jenis       = $this->input->post('jenis');
    
            // Validasi data
            if (empty($nama) || empty($id_layanan) || $status == '' || $jenis == '') {
                $response = array(
                    'status'    => 'error',
                    'message'   => 'Semua field harus diisi.'
                );
            } else {
                // Memeriksa apakah data sudah ada di database (kecuali data yang sedang diedit)
                $table = 'tbl_loket';
                $where = array(
                    'nama'      => $nama,
                    'jenis'     => $jenis,
                    'id_loket !=' => $id_loket // Kecualikan data yang sedang diedit
                );
    
                if ($this->M_Model->GetWHERE($table, $where)) {
                    $response = array(
                        'status'    => 'error',
                        'message'   => 'Loket dengan nama dan jenis yang sama sudah ada di database.'
                    );
                } else {
                    // Menyiapkan data untuk diupdate
                    $data = array(
                        'nama'        => $nama,
                        'id_layanan'  => $id_layanan,
                        'status'      => $status,
                        'jenis'       => $jenis
                    );
    
                    // Melakukan update data
                    if ($this->M_Model->update_data('tbl_loket', $data, array('id_loket' => $id_loket))) {
                        $response = array(
                            'status'    => 'success',
                            'message'   => 'Data berhasil diupdate.'
                        );
                    } else {
                        $response = array(
                            'status'    => 'error',
                            'message'   => 'Terjadi kesalahan saat mengupdate data.'
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
      $result = $this->M_Model->hapus('id_loket', $id, 'tbl_loket'); // Ganti 'nama_tabel' dengan nama tabel yang sesuai

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
      redirect('LoketController');
    }
}
