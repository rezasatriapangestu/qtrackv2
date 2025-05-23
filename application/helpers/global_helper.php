<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('check_login')) {
    /**
     * Memeriksa status login dan roles pengguna.
     * Jika tidak login, redirect ke AuthController.
     * Jika login tetapi roles tidak sesuai, redirect ke UserController.
     */
    function check_login() {
        $CI =& get_instance(); // Mendapatkan instance CI

        // Periksa apakah pengguna sudah login
        if (!$CI->session->userdata('is_login')) {
            redirect('AuthController'); // Redirect ke halaman login
        }

        // Periksa roles pengguna
        $roles = $CI->session->userdata('roles');
        $allowed_roles = ['admin', 'operator']; // Daftar roles yang diizinkan

        // Jika roles tidak ada dalam daftar yang diizinkan, redirect ke UserController
        if (!in_array($roles, $allowed_roles)) {
            redirect('UserController'); // Redirect ke halaman user
        }
    }

    /**
     * Memeriksa apakah pengguna adalah user biasa.
     * Jika tidak login, redirect ke AuthController.
     * Jika login tetapi bukan user, redirect ke HomeController.
     */
    function check_user() {
        $CI =& get_instance();

        // Periksa apakah pengguna sudah login
        if (!$CI->session->userdata('is_login')) {
            redirect('AuthController'); // Redirect ke halaman login
        }

        // Periksa roles pengguna
        $roles = $CI->session->userdata('roles');
        $allowed_roles = 'user'; // Role yang diizinkan

        // Jika roles bukan user, redirect ke HomeController
        if ($roles != $allowed_roles) {
            redirect('HomeController'); // Redirect ke halaman home
        }
    }

    /**
     * Memeriksa apakah pengguna adalah admin.
     * Jika tidak login, redirect ke AuthController.
     * Jika login tetapi bukan admin, redirect ke UserController.
     */
    function check_admin() {
        $CI =& get_instance();

        // Periksa apakah pengguna sudah login
        if (!$CI->session->userdata('is_login')) {
            redirect('AuthController'); // Redirect ke halaman login
        }

        // Periksa roles pengguna
        $roles = $CI->session->userdata('roles');
        $allowed_roles = 'admin'; // Role yang diizinkan

        // Jika roles bukan admin, redirect ke UserController
        if ($roles != $allowed_roles) {
            redirect('HomeController'); // Redirect ke halaman user
        }
    }

    /**
     * Memeriksa apakah pengguna adalah operator.
     * Jika tidak login, redirect ke AuthController.
     * Jika login tetapi bukan operator, redirect ke UserController.
     */
    function check_operator() {
        $CI =& get_instance();

        // Periksa apakah pengguna sudah login
        if (!$CI->session->userdata('is_login')) {
            redirect('AuthController'); // Redirect ke halaman login
        }

        // Periksa roles pengguna
        $roles = $CI->session->userdata('roles');
        $allowed_roles = 'operator'; // Role yang diizinkan

        // Jika roles bukan operator, redirect ke UserController
        if ($roles != $allowed_roles) {
            redirect('HomeController'); // Redirect ke halaman user
        }
    }
}

if (!function_exists('get_web_info')) {
    /**
     * Mengambil informasi website dari database
     * 
     * @param string $field Kolom yang ingin diambil (nama_web, alamat, footer, etc)
     * @return mixed Nilai dari field yang diminta
     */
    function get_web_info($field = 'nama_web') {
        $CI =& get_instance();
        
        // Load cache driver jika belum
        if (!isset($CI->cache)) {
            $CI->load->driver('cache', array('adapter' => 'file', 'backup' => 'apc'));
        }
        
        // Coba ambil dari cache dulu
        $cache_key = 'web_info_data';
        $web_info = $CI->cache->get($cache_key);
        
        if ($web_info === FALSE) {
            // Jika tidak ada di cache, ambil dari database
            $CI->db->select('*');
            $CI->db->from('tbl_web_set'); // Sesuaikan dengan nama tabel Anda
            $CI->db->limit(1);
            $query = $CI->db->get();
            
            if ($query->num_rows() > 0) {
                $web_info = $query->row();
                // Simpan ke cache untuk 1 jam
                $CI->cache->save($cache_key, $web_info, 5);
            } else {
                return null;
            }
        }
        
        // Return field tertentu atau semua data jika tidak ditentukan
        if ($field === 'all') {
            return $web_info;
        }
        
        return isset($web_info->$field) ? $web_info->$field : null;
    }
}
