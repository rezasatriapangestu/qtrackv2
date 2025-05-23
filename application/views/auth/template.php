<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>QTrack - <?= $title;?></title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('assets/');?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/');?>css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/');?>vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url('assets/');?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url('assets/');?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url('assets/');?>vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('assets/');?>js/sb-admin-2.min.js"></script>

    <script src="<?= base_url('assets/');?>vendor/sweetalert2/sweetalert2.all.min.js"></script>

    <style>
        
        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .floating-label__text {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            color: #999;
            font-size: 1rem;
        }

        .floating-label input:focus ~ .floating-label__text,
        .floating-label input:not(:placeholder-shown) ~ .floating-label__text {
            top: -20px;
            font-size: 0.8rem;
            color: #007bff;
        }

        .input-border-bottom {
            border: none;
            border-bottom: 1px solid #ccc;
            border-radius: 0;
            padding-left: 0;
            padding-right: 0;
        }

        .input-border-bottom:focus {
            outline: none;
            border-bottom: 2px solid #007bff;
        }

      .btn-user {
            background-color: #007bff; /* Button background color */
            border: none; /* Remove border */
            transition: background-color 0.3s, transform 0.3s; /* Smooth transition for background color */
        }

        .btn-user:hover {
            background-color: #0056b3; /* Darker background on hover */
            transform: scale(1.05); /* Slightly scale up on hover */
        }
    </style>
</head>

<body style="background-image: url('<?= base_url('assets/img/background-login.jpg'); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">

    <?php $this->load->view('auth/'.$page); ?>

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
        // JavaScript untuk memastikan label tetap di atas saat input terisi
        document.querySelectorAll('.floating-label input').forEach(input => {
            input.addEventListener('input', () => {
                if (input.value.trim() !== "") {
                    input.classList.add('has-value');
                } else {
                    input.classList.remove('has-value');
                }
            });
        });
    </script>

</body>

</html>