<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="QTrack - Sistem Antrian Online">
    <meta name="author" content="">

    <title><?php echo get_web_info('nama_web'); ?> - <?= $title; ?></title>

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
            background: linear-gradient(90deg, #e3f0ff 0%, #f8faf8 100%);
            padding: 24px 0 10px 0;
            border-bottom: none;
            box-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
            text-align: center;
            border-radius: 0 0 28px 28px;
        }

        .header h2 {
            color: #2E7D32;
            font-weight: 700;
            margin-bottom: 0;
            font-size: 2rem;
            letter-spacing: 1px;
        }

        .address {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
            color: #222;
            font-weight: 500;
        }

        .container-body {
            flex: 1;
            margin-top: 30px;
            padding-bottom: 20px;
        }

        .footer {
            background: linear-gradient(90deg, #e3f0ff 0%, #f8faf8 100%);
            padding: 20px;
            border-top: none;
            text-align: center;
            font-size: 15px;
            color: #2E7D32;
            font-weight: 600;
            border-radius: 28px 28px 0 0;
            box-shadow: 0 -2px 8px rgba(46, 125, 50, 0.08);
        }
        .footer a {
            color: #388e3c;
            font-weight: 700;
        }

        .custom-card {
            border: none;
            border-radius: 28px;
            box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
            transition: transform 0.3s cubic-bezier(.4,2,.6,1), box-shadow 0.3s;
            background: #fff;
            overflow: hidden;
        }

        .custom-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 16px 32px rgba(46, 125, 50, 0.18);
        }

        .custom-card-header {
            color: #2E7D32;
            border-radius: 28px 28px 0 0;
            padding: 24px 18px 10px 18px;
            background: none;
            box-shadow: none;
            position: relative;
            border-bottom: none;
        }

        .custom-card-header .badge-antrian-jenis {
            display: inline-block;
            font-size: 0.95rem;
            padding: 6px 18px;
            border-radius: 16px;
            letter-spacing: 1px;
            margin-top: 10px;
            margin-bottom: 0;
            box-shadow: 0 2px 8px rgba(46, 125, 50, 0.10);
        }

        .custom-card-footer {
            background: #f1f8e9;
            border-radius: 0 0 28px 28px;
            padding: 16px 10px;
            border-top: 1px solid #e0e0e0;
        }

        .custom-card .card-title {
            font-weight: 600;
            color: #2E7D32;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .custom-card .display-4 {
            font-size: 3.2rem;
            font-weight: 700;
            color: #2E7D32;
            margin-bottom: 8px;
            letter-spacing: 2px;
            text-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
        }

        .custom-card .badge {
            font-size: 1rem;
            border-radius: 16px;
            padding: 6px 18px;
            margin-top: 8px;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table, .table th, .table td {
            border-radius: 0 !important;
        }

        .card, .card-header, .card-footer {
            border-radius: 24px !important;
        }

        /* Navbar Modern */
        .navbar {
            background: linear-gradient(90deg, #2E7D32 60%, #43a047 100%);
            box-shadow: 0 4px 6px rgba(46, 125, 50, 0.10);
            border-radius: 18px;
            margin-top: 18px;
        }

        .navbar-nav .nav-link {
            color: #fff !important;
            font-weight: 600;
            margin: 0 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 1.05rem;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #2E7D32 !important;
            background-color: #e3f0ff !important;
            font-weight: 700;
            transform: translateY(-2px) scale(1.04);
        }

        .navbar-toggler {
            border: none;
            outline: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(46,125,50,0.8)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }
    </style>
</head>

<body>
    <div class="header">
        <h2><?php echo get_web_info('nama_web'); ?></h2>
        <div class="address">
            <?php echo get_web_info('alamat'); ?>
        </div>
    </div>

    <!-- Navbar Modern -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav ml-auto w-100 justify-content-center">
                    <a class="nav-item nav-link" href="<?=base_url('UserController');?>">Home</a>
                    <a class="nav-item nav-link" href="<?=base_url('UserController/ambil_antrian');?>">Ambil Antrian</a>
                    <a class="nav-item nav-link" href="<?=base_url('UserController/histori');?>">Histori</a>
                    <a class="nav-item nav-link" href="<?=base_url('UserController/profile');?>">Profile</a>
                    <a class="nav-item nav-link" href="<?=base_url('AuthController/logout');?>">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-body d-flex flex-column">
        <?php $this->load->view($conten); ?>
    </div>

    <script>
        function showSweetAlert(icon, title, redirectUrl = null) {
            Swal.fire({
                icon: icon, // 'success', 'error', 'warning', 'info', 'question'
                title: icon,
                text: title,
                showConfirmButton: false, // Disable the OK button
                backdrop: 'rgba(0,0,0,0.5)', // Semi-transparent background
                timer: 3000, // Timer (3 seconds)
                timerProgressBar: true, // Show progress bar
                allowOutsideClick: false, // Can't close by clicking outside
                allowEscapeKey: false, // Can't close with ESC key
                customClass: {
                    popup: 'animated bounceIn', // Entrance animation (bounceIn)
                    title: 'swal2-title-custom', // Custom class for title
                    content: 'swal2-content-custom', // Custom class for content
                }
            }).then(() => {
                if (redirectUrl) {
                    // Redirect to specified page if redirectUrl is provided
                    window.location.href = redirectUrl;
                }
            });
        }
        
        $(document).ready(function() {
            var currentUrl = "<?= uri_string(); ?>"; // Ambil URI segment saat ini

            $('.navbar-nav .nav-link').each(function() {
                var linkUrl = $(this).attr('href').split('/').pop(); // Ambil segment terakhir dari URL

                if (currentUrl === linkUrl) {
                    $(this).addClass('active'); // Tambahkan class 'active' jika cocok
                }
            });
        });
    </script>

    <div class="footer">
        <?php echo get_web_info('footer'); ?> | Designed by <a href="#"><?php echo get_web_info('create_by'); ?></a>
    </div>
</body>
</html>