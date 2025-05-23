<style>
        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            background-color: #f5f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 950px;
            height: 85vh;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        .image-section {
            background: linear-gradient(135deg, #28a745, #007bff);
            display: flex;
            flex-direction: column;
            justify-content: center; /* Center vertically */
            align-items: center;
            padding: 2.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .image-section .logo img {
            max-height: 250px;
            transition: transform 0.3s ease;
        }
        .image-section .logo img:hover {
            transform: scale(1.05);
        }
        .image-section .footer-text {
            font-size: 0.9rem;
            letter-spacing: 1px;
            opacity: 0.9;
            font-weight: 500;
            position: absolute;
            bottom: 2rem;
            left: 0;
            width: 100%;
            text-align: center;
        }
        .form-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
            border-left: 1px solid rgba(0, 0, 0, 0.1);
        }
        .form-section h4 {
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        .form-section h5 {
            font-weight: 500;
            color: #333;
            margin-bottom: 1rem;
        }
        .form-control {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 0.85rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.2);
            outline: none;
        }
        .btn-login {
            background-color: #2E7D32;
            border: none;
            border-radius: 10px;
            padding: 0.85rem;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
            background-color: #1b5e20;
        }
        .register-link {
            color: #28a745;
            font-weight: 500;
            text-decoration: none;
        }
        .register-link:hover {
            color: #1b5e20;
            text-decoration: underline;
        }
        .text-info {
            color: #17a2b8 !important;
        }
        @media (max-width: 768px) {
            .image-section {
                display: none;
            }
            .form-section {
                width: 100%;
                padding: 1.5rem;
                border-left: none;
            }
            .login-container {
                height: auto;
                margin: 1rem auto;
                border-radius: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container row m-0">
        <!-- Left Image Section -->
        <div class="col-12 col-md-6 image-section d-none d-md-flex">
            <div class="logo">
                <img src="<?= base_url('assets/img/qtrack-with-text.png'); ?>" alt="Logo">
            </div>
            <div class="footer-text fw-bold">by BITap Team</div>
        </div>

        <!-- Right Form Section -->
        <div class="col-12 col-md-6 form-section">
            <div class="text-center mb-4">
                <img src="<?= base_url('assets/img/qtrack.png'); ?>" alt="Logo" class="mb-3" style="max-height: 75px;">
                <div class="mb-4">
                    <span class="fw-bold"><?php echo get_web_info('nama_web'); ?></span>
                </div>
                <h4 class="mb-1">Login</h4>
            </div>
            <h5 class="text-start ms-2">Welcome Back!</h5>
            <div class="mb-3 text-start ms-2">
                <span>Login to your account</span>
            </div>
            <form class="user" id="form-login">
                <!-- Input Email -->
                <div class="form-group mb-3">
                <input type="email" class="form-control" id="email" placeholder="Email Address" name="email" required>
                    <span id="email-error" class="text-danger d-block" style="font-size: 12px;"></span>
                    <span id="email-success" class="text-success d-block" style="font-size: 12px;"></span>
                </div>
                <!-- Input Password -->
                <div class="form-group mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <span id="password-error" class="text-danger d-block" style="font-size: 12px;"></span>
                    <span id="password-success" class="text-success d-block" style="font-size: 12px;"></span>
                </div>
                <!-- Login Button -->
                <button id="button-login" class="btn btn-login text-white btn-block w-100">Login</button>
            </form>
            <div class="text-center mt-3">
                <p class="text-muted small mb-0">Don't have an account? <a href="<?=base_url('AuthController/register');?>" class="register-link">Register</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#email').on('keyup', function() {
                let email = $(this).val();
                if (email === "") {
                    $('#email-error').text('Email is required.');
                    $('#email-success').text('');
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    $('#email-error').text('Invalid email format.');
                    $('#email-success').text('');
                } else {
                    $('#email-error').text('');
                    $('#email-success').text('Valid email.');
                }
            });

            $('#password').on('keyup', function() {
                let password = $(this).val();
                if (password === "") {
                    $('#password-error').text('Password is required.');
                    $('#password-success').text('');
                } else {
                    $('#password-error').text('');
                    $('#password-success').text('Valid password.');
                }
            });

            $('#button-login').on('click', function(e) {
                e.preventDefault();
                $('#email').trigger('keyup');
                $('#password').trigger('keyup');

                let hasError = (
                    $('#email-error').text() !== "" ||
                    $('#password-error').text() !== ""
                );

                if (!hasError) {
                    var dataForm = new FormData(document.getElementById('form-login'));
                    $.ajax({
                        url: '<?= site_url('AuthController/proses_login'); ?>',
                        type: 'POST',
                        data: dataForm,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            var data = JSON.parse(response);
                            if (data.status === 'success') {
                                showSweetAlert(data.status, data.message, '<?= site_url('HomeController'); ?>');
                            } else {
                                showSweetAlert(data.status, data.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            showSweetAlert('error', 'An error occurred: ' + error);
                        }
                    });
                }
            });
        });
    </script>

