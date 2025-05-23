<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="QTrack - Sistem Antrian Online">
    <meta name="author" content="">

    <title><?php echo get_web_info('nama_web'); ?> - Antrian</title>

    <!-- Custom fonts -->
    <link href="<?= base_url('assets/'); ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles -->
    <link href="<?= base_url('assets/'); ?>css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/'); ?>vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap core JavaScript -->
    <script src="<?= base_url('assets/'); ?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url('assets/'); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript -->
    <script src="<?= base_url('assets/'); ?>vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts -->
    <script src="<?= base_url('assets/'); ?>js/sb-admin-2.min.js"></script>
    <script src="<?= base_url('assets/'); ?>vendor/sweetalert2/sweetalert2.all.min.js"></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .header {
            background-color: #ffffff;
            padding: 10px;
            border-bottom: 1px solid #e3e6f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header h2 {
            color: #4e73df;
            margin-bottom: 0;
        }

        .address {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
            color: rgb(249, 191, 0);
        }

        .container {
            flex: 1;
            margin-top: 30px;
            padding-bottom: 20px; /* Memberikan jarak antara konten dan footer */
        }

        .footer {
            background-color: #ffffff;
            padding: 20px;
            border-top: 1px solid #e3e6f0;
            text-align: center;
            font-size: 14px;
            color: #6e707e;
        }

        .custom-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .custom-card-header {
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 15px;
        }

        .custom-card-footer {
            background: #f8f9fa;
            border-radius: 0 0 15px 15px;
            padding: 10px;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table th, .table td {
            text-align: center;
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="header">
        <h2><?php echo get_web_info('nama_web'); ?></h2>
        <div class="address">
        <?php echo get_web_info('alamat'); ?>
        </div>
    </div>

    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center mt-5">
            <?php foreach ($data_loket as $row) : ?>
            <div class="col-md-4 mb-4">
                <div class="card custom-card" id="loket-antrian-<?= $row['id_layanan']; ?>" data-id-loket="<?= $row['id_loket'];?>"  data-jenis-loket="<?= $row['jenis'];?>" data-id-layanan="<?= $row['id_layanan']; ?>">
                    <div class="card-header custom-card-header bg-info text-center">
                        <h4 class="text-uppercase text-center" id="jenis_layanan_<?=$row['id_loket'];?>"><?= $row['nama']; ?></h4>
                        <span class="badge badge-<?= ($row['jenis'] == '1') ? 'success' : 'danger';?> text-center"><?= ($row['jenis'] == '1') ? 'BOOKING' : 'NON BOOKING';?></span>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title">Antrian</h5>
                        <p class="display-4" id="nomor_antrian_<?=$row['id_loket'];?>"></p>
                        <span class="badge text-center" id="status_antrian"></span>
                    </div>
                   
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
        // Set interval untuk mengambil data antrian setiap 1 detik
            setInterval(function() {
                $('.custom-card').each(function() {
                    var id_loket = $(this).data('id-loket'); // Ambil id_loket dari data attribute
                    var jenis_loket     = $(this).data('jenis-loket'); // Ambil id_loket dari data attribute
                    var id_layanan = $(this).data('id-layanan'); // Ambil id_layanan dari data attribute
                    var $display = $(this).find('.display-4'); // Temukan elemen untuk menampilkan nomor antrian
                    var $status_antrian = $(this).find('#status_antrian'); // Temukan elemen untuk menampilkan status antrian
                    $.ajax({
                        url: '<?= base_url("AntrianController/get_antrian");?>', // Ganti dengan path ke file PHP Anda
                        method: 'POST',
                        data: { id_layanan: id_layanan, id_loket: id_loket,jenis_loket:jenis_loket }, // Kirim id_layanan dan id_loket sebagai parameter
                        dataType: 'json',
                        success: function(data) {
                            // Memeriksa apakah data yang diterima tidak kosong
                            if (data.length > 0) {
                                // Mengambil semua nomor antrian dan status antrian dari data
                                var nomor_antrian = data.map(function(item) {
                                    return item.no_antrian; // Mengambil nomor antrian dari setiap objek
                                }).join(', '); // Menggabungkan nomor antrian menjadi string

                                var kode_layanan = data.map(function(item) {
                                    return item.kode_layanan; // Mengambil kode layanan dari setiap objek
                                }).join(', '); // Menggabungkan kode layanan menjadi string

                                var status_antrian = data.map(function(item) {
                                    return item.status_antrian; // Mengambil status antrian dari setiap objek
                                });
                                var id_antrians = data.map(function(item) {
                                    return item.id; // Mengambil id antrian dari setiap objek
                                });

                                $('[id^="btn-' + id_loket + '"]').attr('data-id-antrian', id_antrians);
                                
                                // Inisialisasi variabel sa dan badge_color
                                var sa;
                                var badge_color;

                                // Memeriksa status antrian
                                if (status_antrian.every(function(status) { return status === 'buat'; })) {
                                    sa = 'menunggu';
                                    badge_color = 'badge-secondary';
                                } else if (status_antrian.some(function(status) { return status === 'panggil'; })) {
                                    sa = 'Sedang Memanggil';
                                    badge_color = 'badge-success';
                                } else if (status_antrian.some(function(status) { return status === 'proses'; })) {
                                    sa = 'Sedang Melayani';
                                    badge_color = 'badge-info';
                                } else if (status_antrian.some(function(status) { return status === 'selesai'; })) {
                                    sa = 'Antrian Selesai';
                                    badge_color = 'badge-warning';
                                } else {
                                    sa = 'Antrian dibatalkan'; // Status lain yang tidak terduga
                                    badge_color = 'badge-danger';
                                }

                                // Memperbarui tampilan
                                $display.text(nomor_antrian); // Memperbarui nomor antrian di dalam elemen
                                $status_antrian.text(sa); // Memperbarui status antrian di dalam elemen
                                $status_antrian.removeClass().addClass('badge ' + badge_color); // Mengatur kelas badge
                                // Memperbarui data-id-antrian pada semua tombol
                            

                            } else {
                                $display.text('0'); // Menampilkan pesan jika tidak ada antrian
                                $status_antrian.text('Tidak ada antrian'); // Memperbarui status antrian di dalam elemen
                                $status_antrian.removeClass().addClass('badge badge-danger'); // Mengatur kelas badge

                                
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching data:', error);
                        }
                    });
                });
            }, 1000); // 1000 ms = 1 detik
        });
    </script>

    <div class="footer">
    <?php echo get_web_info('footer'); ?> | Designed by <a href="#" style="color:rgb(255, 191, 0);"><?php echo get_web_info('create_by'); ?></a>
    </div>
</body>


</html>