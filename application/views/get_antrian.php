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
        .btn-custom {
            width: 100%;
            margin-top: 20px;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .video-container {
            text-align: center;
            margin-bottom: 30px;
        }

        video {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .header {
            background-color: #ffffff;
            padding: 10px;
            border-bottom: 1px solid #e3e6f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .footer {
            background-color: #ffffff;
            padding: 20px;
            border-top: 1px solid #e3e6f0;
            text-align: center;
            font-size: 14px;
            color: #6e707e;
            margin-top: 30px;
        }

        .address {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
            color: rgb(249, 191, 0);
        }

        .container {
            margin-top: 30px;
        }

        h2 {
            margin-bottom: 20px;
        }

        /* Style untuk area cetak */
        .printable {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .printable,
            .printable * {
                visibility: visible;
            }

            .printable {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                text-align: center;
                font-size: 24px;
            }
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="header">
        <h2 class="text-primary text-center"><?php echo get_web_info('nama_web'); ?></h2>
        <div class="address">
            <?php echo get_web_info('alamat'); ?>
        </div>
    </div>

    <button class="btn btn-danger btn-sm" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i> Keluar
    </button>
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 video-container">
                <h2 class="text-white">Video Layanan</h2>
                <video controls>
                    <source src="path_to_your_video.mp4" type="video/mp4">
                    Browser Anda tidak mendukung tag video.
                </video>
            </div>

            <div class="col-md-6">
                <h2 class="text-white text-center">Layanan</h2>
                <div class="row">
                    <?php foreach ($data_layanan as $dl) : ?>
                        <div class="col">
                            <button class="btn btn-success btn-custom" onclick="ambilAntrian('<?= $dl['id_layanan']; ?>', '<?= $dl['kode']; ?>', '<?= $dl['nama']; ?>')">
                                <?= $dl['nama']; ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
    <?php echo get_web_info('footer'); ?> | Designed by <a href="#" style="color:rgb(255, 191, 0);"><?php echo get_web_info('create_by'); ?></a>
    </div>

    <!-- Area yang akan dicetak -->
    <div class="printable" id="printableArea">
        <h2 style="font-weight: bold;">Nomor Antrian</h2>
        <p style="font-size: 40px; font-weight: bold;" id="nomorAntrian"></p>
        <p id="jenisLayanan"></p>
        <p>Terima kasih telah menggunakan layanan kami.</p>
    </div>

    <script>
        // Format nomor antrian dengan leading zeros
        function formatNomorAntrian(nomor) {
            return String(nomor).padStart(3, '0'); // Format ke 3 digit, contoh: 001, 002, dst.
        }

        // Fungsi untuk mendapatkan nomor antrian terakhir dari server
        function getLastAntrian(kode_layanan, callback) {
            $.ajax({
                url: '<?= base_url('GetAntrian/get_last_antrian_'); ?>',
                type: 'POST',
                data: {
                    kode_layanan: kode_layanan
                },
                success: function(response) {
                    if (response.status === 'success') {
                        callback(response.last_number || 0);
                    } else {
                        console.error('Gagal mendapatkan nomor antrian terakhir');
                        callback(0);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    callback(0);
                }
            });
        }

        function ambilAntrian(id_layanan, kode, jenis) {
            // Dapatkan nomor antrian terakhir dari server
            getLastAntrian(kode, function(lastNumber) {
                // Hitung nomor berikutnya
                const nextNumber = parseInt(lastNumber) + 1;
                const nomor = formatNomorAntrian(nextNumber);

                // Tampilkan nomor antrian di area printable
                document.getElementById('nomorAntrian').innerText = `${kode}${nomor}`;
                document.getElementById('jenisLayanan').innerText = jenis;

                // Kirim data ke server
                simpanAntrian(id_layanan, kode, jenis, nomor);
            });
        }

        function simpanAntrian(id_layanan, kode, jenis, nomor) {
            $.ajax({
                url: '<?= base_url('GetAntrian/simpan_antrian'); ?>',
                type: 'POST',
                data: {
                    id_layanan: id_layanan,
                    kode: kode,
                    jenis: jenis,
                    nomor: nomor
                },
                success: function(response) {
                    console.log('Response dari server:', response);

                    if (response.status === 'success') {
                        // Tampilkan area cetak
                        document.getElementById('printableArea').style.display = 'block';

                        // Cetak langsung
                        window.print();

                        // Sembunyikan area cetak setelah 1 detik
                        setTimeout(() => {
                            document.getElementById('printableArea').style.display = 'none';
                        }, 1000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menyimpan antrian: ' + response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim data ke server.'
                    });
                }
            });
        }

    </script>

    <script>
        // Add this function to handle logout
        function logout() {
            Swal.fire({
                title: 'Konfirmasi',
                text: "Anda yakin ingin keluar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('GetAntrian/keluar'); ?>';
                }
            });
        }

            // [Keep all your existing JavaScript functions]
        </script>

        <!-- INI UNTUK BLOUTOT -->
    <!-- <script>
        // Simpan nomor antrian untuk setiap layanan
        const nomorAntrian = {};

        // Format nomor antrian dengan leading zeros
        function formatNomorAntrian(nomor) {
            return String(nomor).padStart(3, '0'); // Format ke 3 digit, contoh: 001, 002, dst.
        }

        // Fungsi untuk mengambil antrian
        function ambilAntrian(id_layanan, kode, jenis) {
            // Inisialisasi nomor antrian jika belum ada
            if (!nomorAntrian[kode]) {
                nomorAntrian[kode] = 0;
            }

            // Tambahkan nomor antrian
            nomorAntrian[kode]++;
            const nomor = formatNomorAntrian(nomorAntrian[kode]);

            // Tampilkan nomor antrian di area printable
            document.getElementById('nomorAntrian').innerText = `${kode}${nomor}`;
            document.getElementById('jenisLayanan').innerText = jenis;

            // Kirim data ke server terlebih dahulu
            simpanAntrian(id_layanan, kode, jenis, nomor);
        }

        // Fungsi untuk menyimpan antrian dan mencetak
        function simpanAntrian(id_layanan, kode, jenis, nomor) {
            $.ajax({
                url: '<?= base_url('GetAntrian/simpan_antrian'); ?>',
                type: 'POST',
                data: {
                    id_layanan: id_layanan,
                    kode: kode,
                    jenis: jenis,
                    nomor: nomor
                },
                success: function(response) {
                    console.log('Response dari server:', response);

                    // Jika data berhasil disimpan, cetak nomor antrian
                    if (response.status === 'success') {
                        // Tampilkan area cetak
                        document.getElementById('printableArea').style.display = 'block';

                        // Cetak menggunakan Web Bluetooth API
                        cetakDenganBluetooth(kode, nomor, jenis);

                        // Sembunyikan area cetak setelah 1 detik
                        setTimeout(() => {
                            document.getElementById('printableArea').style.display = 'none';
                        }, 1000); // 1000 milidetik = 1 detik
                    } else {
                        // Jika gagal menyimpan, tampilkan pesan error
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menyimpan antrian: ' + response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim data ke server.'
                    });
                }
            });
        }

        // Fungsi untuk mencetak menggunakan Web Bluetooth API
        function cetakDenganBluetooth(kode, nomor, jenis) {
            // Pastikan browser mendukung Web Bluetooth API
            if (!navigator.bluetooth) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Browser tidak mendukung Web Bluetooth API.'
                });
                return;
            }

            // Minta perangkat Bluetooth
            navigator.bluetooth.requestDevice({
                filters: [{ services: ['serial_port'] }] // Filter untuk perangkat dengan layanan serial port
            })
            .then(device => {
                console.log('Connected to:', device.name);
                return device.gatt.connect();
            })
            .then(server => {
                return server.getPrimaryService('serial_port'); // Ganti dengan UUID layanan yang sesuai
            })
            .then(service => {
                return service.getCharacteristic('tx'); // Ganti dengan UUID karakteristik yang sesuai
            })
            .then(characteristic => {
                // Data yang akan dicetak
                const data = new TextEncoder().encode(
                    "=== Nomor Antrian ===\n" +
                    `${kode}${nomor}\n` +
                    `${jenis}\n` +
                    "Terima kasih telah menggunakan layanan kami.\n"
                );

                // Kirim data ke printer
                return characteristic.writeValue(data);
            })
            .then(() => {
                console.log('Data berhasil dikirim ke printer');
            })
            .catch(error => {
                console.error('Bluetooth error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal mencetak ke printer thermal. Pastikan printer terhubung dan mendukung Bluetooth.'
                });
            });
        }
    </script> -->
</body>

</html>