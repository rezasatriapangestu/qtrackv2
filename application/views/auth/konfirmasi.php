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
        max-width: 400px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        background: #fff;
        padding: 2.5rem 2rem;
    }
    .form-section h4 {
        font-weight: 700;
        color: black;
        margin-bottom: 0.5rem;
    }
    .form-section h5 {
        font-weight: 500;
        color: black;
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
    @media (max-width: 500px) {
        .login-container {
            padding: 1.5rem 0.5rem;
            border-radius: 15px;
        }
    }
</style>
<body>
    <div class="login-container">
        <div class="text-center mb-4 form-section">
            <img src="<?= base_url('assets/img/qtrack.png'); ?>" alt="Logo" class="mb-2" style="max-height: 75px;">
            <div class="mb-1">
                <span class="fw-bold"><?php echo get_web_info('nama_web'); ?></span>
            </div>
            <h4 class="mb-4 mt-4">Confirmation</h4>
            <h5 class="text-start"">Enter Confirmation Code</h5>
            <div class="mb-1 text-start">
                <span>The code was sent to your email</span>
            </div>
        </div>
        <form class="user" id="form-konfirmasi">
            <div class="form-group mb-4">
                <input type="text" class="form-control" id="token" placeholder="Confirmation Code" name="token" required>
                <span id="token-error" class="text-danger d-block" style="font-size: 12px;"></span>
                <span id="token-success" class="text-success d-block" style="font-size: 12px;"></span>
            </div>
            <button id="button-konfirmasi" class="btn btn-login text-white btn-block w-100">Confirm</button>
        </form>
    </div>
    <script>
        $(document).ready(function() {

        $('#token').on('keyup', function() {
            let token = $(this).val();
            if (token === "") {
                $('#token-error').text('Confirmation code is required.');
                $('#token-success').text('');
            } else {
                $('#token-error').text('');
                $('#token-success').text('Confirmation code is valid.');
            }
        });

        
        $('#button-konfirmasi').on('click', function(e) {
            e.preventDefault();

            $('#token').trigger('keyup');
        
            let hasError = ($('#token-error').text() !== "");

            if (!hasError) {
                var dataForm = new FormData(document.getElementById('form-konfirmasi'));

                // Fungsi untuk menampilkan SweetAlert
                $.ajax({
                    url: '<?= site_url('AuthController/proses_konfirmasi'); ?>',
                    type: 'POST',
                    data: dataForm,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var data = JSON.parse(response);
                        console.log(data);

                        if (data.status === 'success') {
                            // Tampilkan SweetAlert sukses dan redirect ke halaman AuthController
                            showSweetAlert(data.status, data.message, '<?= site_url('AuthController/index'); ?>');
                        } else if (data.status === 'info') {
                            // Tampilkan SweetAlert error tanpa redirect
                            showSweetAlert(data.status, data.message,'<?= site_url('AuthController/index'); ?>');
                        }else{
                            showSweetAlert(data.status, data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Tampilkan SweetAlert untuk error AJAX
                        showSweetAlert('error', 'An error occurred: ' + error);
                    }
                });
            }
        });
    });
    </script>
</body>