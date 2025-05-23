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
    @media (max-width: 500px) {
        .login-container {
            padding: 1.5rem 0.5rem;
            border-radius: 15px;
        }
    }
</style>

<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow" style="width: 100%; max-width: 400px; border-radius: 15px;">
        <div class="card-body p-4">
            <div class="text-center p-0 m-0 mb-3 form-section">
                <img src="<?= base_url('assets/img/qtrack.png'); ?>" alt="Logo" class="mb-2" style="max-height: 60px;">
                <div class="mb-3">
                    <span class="fw-bold"><?php echo get_web_info('nama_web'); ?></span>
                </div>
                <h4 class="mb-1 mt-3">PIN</h4>
                <div class="mb-3 text-muted" style="font-size: 15px;">
                    Enter your PIN to unlock access
                </div>
            </div>
            <form class="user" id="form-login">
            
                <!-- Input pin -->
                <div class="form-group mb-4">
                    <input type="pin" class="form-control form-control-user rounded-0 input-border-bottom"
                        id="pin" name="pin" placeholder="PIN" required>
                    <span id="pin-error" class="text-danger d-block" style="font-size: 12px;"></span>
                    <span id="pin-success" class="text-success d-block" style="font-size: 12px;"></span>
                </div>

                <!-- Tombol Login -->
                <button id="button-login" class="btn btn-login text-white btn-block w-100">
                    Submit
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#pin').on('keyup', function() {
            let pin = $(this).val();
            if (pin === "") {
                $('#pin-error').text('PIN is required.');
                $('#pin-success').text('');
            } else {
                $('#pin-error').text('');
                $('#pin-success').text('PIN is valid.');
            }
        });



        $('#button-login').on('click', function(e) {
            e.preventDefault();
            $('#email').trigger('keyup');
            $('#pin').trigger('keyup');;

            let hasError = (
                $('#email-error').text() !== "" ||
                $('#pin-error').text() !== ""
            );

            if (!hasError) {
                var dataForm = new FormData(document.getElementById('form-login'));
                $.ajax({
                    url: '<?= site_url('GetAntrian/proses_unlock'); ?>',
                    type: 'POST',
                    data: dataForm,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            // Show success SweetAlert and redirect to GetAntrian page
                            showSweetAlert(data.status, data.message, '<?= site_url('GetAntrian'); ?>');
                        } else {
                            // Show error SweetAlert without redirect
                            showSweetAlert(data.status, data.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Show SweetAlert for AJAX error
                        showSweetAlert('error', 'An error occurred: ' + error);
                    }
                });
            }
        });
    });
</script>
