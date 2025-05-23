<?php
// Database connection
$servername = "localhost";
$username = "bitapmyi_bitapfest";
$password = "Wewokdetok123*";
$dbname = "bitapmyi_qtrackfest";

// Inisialisasi variabel agar tidak undefined jika koneksi gagal
$totalAntrian = 0;
$antrianSelesai = 0;
$antrianDibatalkan = 0;
$antrianProses = 0;
$pieData = [];
$dailyData = [];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Total Antrian (semua record)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tbl_antrian");
    $stmt->execute();
    $totalAntrian = $stmt->fetchColumn();

    // Antrian Selesai (status = 'selesai')
    $stmt = $conn->prepare("SELECT COUNT(*) as selesai FROM tbl_antrian WHERE status = 'selesai'");
    $stmt->execute();
    $antrianSelesai = $stmt->fetchColumn();

    // Antrian Dibatalkan (status = 'batal')
    $stmt = $conn->prepare("SELECT COUNT(*) as dibatalkan FROM tbl_antrian WHERE status = 'batal'");
    $stmt->execute();
    $antrianDibatalkan = $stmt->fetchColumn();

    // Antrian Dalam Proses (sisa dari total antrian)
    $antrianProses = $totalAntrian - $antrianSelesai - $antrianDibatalkan;

    // Pie Chart Data
    $pieData = [
        ['label' => 'Antrian Selesai', 'value' => $antrianSelesai, 'color' => '#28a745'],
        ['label' => 'Antrian Dibatalkan', 'value' => $antrianDibatalkan, 'color' => '#dc3545'],
        ['label' => 'Antrian Dalam Proses', 'value' => $antrianProses, 'color' => '#007bff']
    ];

    // Antrian Harian (7 hari terakhir berdasarkan status 'selesai')
    $dailyData = [];
    $currentDate = new DateTime('now', new DateTimeZone('Asia/Jakarta')); // WIB time zone
    for ($i = 6; $i >= 0; $i--) {
        $date = clone $currentDate;
        $date->modify("-$i days");
        $dayName = $date->format('l');
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_antrian WHERE DATE(waktu_selesai) = ? AND status = 'selesai'");
        $stmt->execute([$date->format('Y-m-d')]);
        $dailyData[$dayName] = $stmt->fetchColumn() ?: 0;
    }

} catch (PDOException $e) {
    // Variabel sudah diinisialisasi di atas, tidak perlu diisi ulang
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8faf8;
            font-family: 'Poppins', sans-serif;
            padding: 20px;
        }
        .dashboard-container {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
            padding: 32px 24px;
            margin-bottom: 32px;
        }
        .alert-custom {
            border-left: 5px solid #007bff;
            border-radius: 10px;
            background-color: #e9f1fe;
        }
        .welcome-banner {
            display: flex;
            align-items: center;
            gap: 18px;
            background: linear-gradient(90deg, #e3f0ff 0%, #f8faf8 100%);
            border-radius: 24px;
            padding: 28px 32px;
            margin-bottom: 32px;
            box-shadow: 0 2px 8px rgba(0,123,255,0.06);
        }
        .welcome-banner .icon {
            font-size: 3rem;
            color: #2E7D32;
            background: #e9f1fe;
            border-radius: 50%;
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .welcome-banner .welcome-text {
            flex: 1;
        }
        .welcome-banner .welcome-text h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #2E7D32;
            margin-bottom: 0.3rem;
        }
        .welcome-banner .welcome-text p {
            font-size: 1.1rem;
            color: #444;
            margin-bottom: 0;
        }
        .stat-card {
            background-color: #ffffff;
            border-left: 5px solid #2E7D32;
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: default;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.08);
        }
        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 24px rgba(46, 125, 50, 0.13);
        }
        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2E7D32;
            margin-bottom: 0;
        }
        .stat-card p {
            font-size: 1rem;
            color: #6c757d;
            font-weight: 500;
        }
        .chart-container {
            background-color: #ffffff;
            border-radius: 18px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.08);
        }
        .chart-container canvas {
            width: 100% !important;
            height: 320px !important;
            max-height: 320px;
            display: block;
            margin: 0 auto;
        }
        .btn-save {
            background-color: #2E7D32;
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-save:hover {
            background-color: #388e3c;
        }
        .card, .card-header, .card-footer {
            border-radius: 24px !important;
        }
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 12px 4px;
            }
            .welcome-banner {
                flex-direction: column;
                align-items: flex-start;
                padding: 18px 12px;
            }
            .welcome-banner .icon {
                font-size: 2.2rem;
                padding: 10px;
            }
            .welcome-banner .welcome-text h2 {
                font-size: 1.3rem;
            }
            .stat-card {
                margin-bottom: 15px;
            }
            .chart-container {
                margin-top: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid dashboard-container">
        <!-- Welcome Banner -->
        <div class="welcome-banner mb-4">
            <div class="icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="welcome-text">
                <h2>Selamat Datang, <?= $this->session->userdata('username'); ?>!</h2>
                <p>Dashboard ini membantu Anda memantau aktivitas antrian dengan mudah dan cepat.</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mt-2">
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <h3><?php echo $totalAntrian; ?></h3>
                    <p>Total Antrian</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <h3><?php echo $antrianSelesai; ?></h3>
                    <p>Antrian Selesai</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <h3><?php echo $antrianDibatalkan; ?></h3>
                    <p>Antrian Dibatalkan</p>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <h5>Pie Chart</h5>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="chart-container">
                    <h5>Antrian Harian</h5>
                    <div class="d-flex justify-content-end mb-2">
                        <button class="btn-save">Simpan Laporan</button>
                    </div>
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabel Daftar Antrian Hari Ini -->
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white" style="border-radius: 24px 24px 0 0;">
                        <h4 class="text-uppercase text-center">Daftar Antrian Booking</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="daftar-antrian-booking" class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No. Antrian</th>
                                        <th>Layanan</th>
                                        <th>Status</th>
                                        <th>Waktu Buat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data antrian akan diisi oleh JavaScript/DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white" style="border-radius: 24px 24px 0 0;">
                        <h4 class="text-uppercase text-center">Daftar Antrian Non Booking</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="daftar-antrian-non-booking" class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>No. Antrian</th>
                                        <th>Layanan</th>
                                        <th>Status</th>
                                        <th>Waktu Buat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data antrian akan diisi oleh JavaScript/DataTables -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- JavaScript for Charts -->
    <script>
        // Pie Chart
        const pie = document.getElementById('pieChart');
        if (pie) {
            const pieCtx = pie.getContext('2d');
            new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode(array_column($pieData ?? [], 'label')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($pieData ?? [], 'value')); ?>,
                        backgroundColor: <?php echo json_encode(array_column($pieData ?? [], 'color')); ?>
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { enabled: true }
                    }
                }
            });
        }

        // Line Chart
        const line = document.getElementById('lineChart');
        if (line) {
            const lineCtx = line.getContext('2d');
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_keys($dailyData ?? [])); ?>,
                    datasets: [{
                        label: 'Antrian Harian',
                        data: <?php echo json_encode(array_values($dailyData ?? [])); ?>,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        // DataTables untuk daftar antrian booking & non booking
        $(document).ready(function() {
            $('#daftar-antrian-booking').DataTable({
                ajax: {
                    url: '<?= base_url("AntrianController/get_daftar_antrian_booking"); ?>',
                    type: 'GET',
                    dataSrc: ''
                },
                columns: [
                    { data: 'no_antrian', title: 'No. Antrian' },
                    { data: 'nama_layanan', title: 'Layanan' },
                    { 
                        data: 'status', 
                        title: 'Status',
                        render: function(data) {
                            const statusColors = {
                                'buat': 'secondary',
                                'panggil': 'success',
                                'proses': 'info',
                                'selesai': 'warning',
                                'batal': 'danger'
                            };
                            const badgeColor = statusColors[data] || 'secondary';
                            return `<span class="badge badge-${badgeColor}">${data}</span>`;
                        }
                    },
                    { data: 'waktu_booking', title: 'Waktu Booking' }
                ],
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });

            $('#daftar-antrian-non-booking').DataTable({
                ajax: {
                    url: '<?= base_url("AntrianController/get_daftar_antrian_non_booking"); ?>',
                    type: 'GET',
                    dataSrc: ''
                },
                columns: [
                    { data: 'no_antrian', title: 'No. Antrian' },
                    { data: 'nama_layanan', title: 'Layanan' },
                    { 
                        data: 'status', 
                        title: 'Status',
                        render: function(data) {
                            const statusColors = {
                                'buat': 'secondary',
                                'panggil': 'success',
                                'proses': 'info',
                                'selesai': 'warning',
                                'batal': 'danger'
                            };
                            const badgeColor = statusColors[data] || 'secondary';
                            return `<span class="badge badge-${badgeColor}">${data}</span>`;
                        }
                    },
                    { data: 'waktu_buat', title: 'Waktu Buat' }
                ],
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                }
            });
        });
    </script>
</body>
</html>