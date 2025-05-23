<style>
.user-form-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
    margin-bottom: 32px;
}
.user-form-card .card-body {
    padding: 32px 24px;
}
.user-form-card .form-group label {
    font-weight: 500;
    color: #2E7D32;
}
.user-form-card .form-control {
    border-radius: 14px;
    border: 1px solid #c8e6c9;
}
.user-form-card .btn-success {
    border-radius: 14px;
    font-weight: 500;
    background: #2E7D32;
    border: none;
}
.user-form-card .btn-success:hover {
    background: #388e3c;
}
.user-form-card .btn-danger {
    border-radius: 14px;
    font-weight: 500;
}
</style>
<div class="card shadow mb-4 user-form-card">
    <div class="card-body">
        <div class="container">
            <form id="form-edit">
                <!-- Input Hidden untuk ID -->
                <input type="hidden" id="id_user" name="id_user" value="<?= $user->id_user; ?>">

                <!-- Input Nama Lengkap -->
                <div class="form-group">
                    <label for="fullname" class="">Nama Lengkap</label>
                    <input type="text" class="form-control" id="fullname" placeholder=" " name="nama_lengkap" value="<?= $user->nama_lengkap; ?>" required>
                    <span id="fullname-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="fullname-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Input Nomor Telepon -->
                <div class="form-group">
                    <label for="phoneNumber" class="">Nomor Telepon</label>
                    <input type="tel" class="form-control" id="phoneNumber" placeholder=" " name="no_tlp" value="<?= $user->no_tlp; ?>" required>
                    <span id="phoneNumber-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="phoneNumber-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Input Email -->
                <div class="form-group">
                    <label for="email" class="">Alamat Email</label>
                    <input type="email" class="form-control" id="email" placeholder=" " name="email" value="<?= $user->email; ?>" required>
                    <span id="email-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="email-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Input Password (Opsional) -->
                <div class="form-group">
                    <label for="password" class="">Password (Biarkan kosong jika tidak ingin mengubah)</label>
                    <input type="password" class="form-control" id="password" placeholder=" " name="password">
                    <span id="password-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="password-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Input Konfirmasi Password (Opsional) -->
                <div class="form-group">
                    <label for="confirmPassword" class="">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="confirmPassword" placeholder=" ">
                    <span id="confirmPassword-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="confirmPassword-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Select Option Role -->
                <div class="form-group">
                    <label for="role" class="">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="" disabled>Pilih Role</option>
                        <option value="admin" <?= ($user->role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="operator" <?= ($user->role == 'operator') ? 'selected' : ''; ?>>Operator</option>
                    </select>
                    <span id="role-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="role-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Select Option Loket -->
                <div class="form-group">
                    <label for="loket" class="">Loket</label>
                    <select class="form-control" id="loket" name="loket" <?= ($user->role == 'admin') ? '' : 'required'; ?>>
                        <option value="" selected>--Pilih--</option>
                        <?php foreach ($data_loket as $dl) : ?>
                            <option value="<?= $dl['id_loket']; ?>" <?= ($dl['id_loket'] == $user->id_loket) ? 'selected' : ''; ?>>
                                <?= $dl['nama'] . ' - ' . ($dl['jenis'] == 1 ? 'Booking' : 'Non Booking'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span id="loket-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="loket-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Tombol Update dan Kembali -->
                <button id="button-update" class="btn btn-success btn-sm mt-4">
                    Simpan
                </button>
                <button id="button-back" class="btn btn-danger btn-sm mt-4">
                    Kembali
                </button>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validasi Nama Lengkap
    $('#fullname').on('keyup', function() {
        let fullname = $(this).val();
        if (fullname === "") {
            $('#fullname-error').text('Nama lengkap harus diisi.');
            $('#fullname-success').text('');
        } else {
            $('#fullname-error').text('');
            $('#fullname-success').text('Nama lengkap valid.');
        }
    });

    // Validasi Nomor Telepon
    $('#phoneNumber').on('keyup', function() {
        let phoneNumber = $(this).val();
        if (phoneNumber === "") {
            $('#phoneNumber-error').text('Nomor telepon harus diisi.');
            $('#phoneNumber-success').text('');
        } else if (!/^\d+$/.test(phoneNumber)) {
            $('#phoneNumber-error').text('Nomor telepon harus berupa angka.');
            $('#phoneNumber-success').text('');
        } else {
            $('#phoneNumber-error').text('');
            $('#phoneNumber-success').text('Nomor telepon valid.');
        }
    });

    // Validasi Email
    $('#email').on('keyup', function() {
        let email = $(this).val();
        if (email === "") {
            $('#email-error').text('Email harus diisi.');
            $('#email-success').text('');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#email-error').text('Format email tidak valid.');
            $('#email-success').text('');
        } else {
            $('#email-error').text('');
            $('#email-success').text('Email valid.');
        }
    });

    // Validasi Password (Opsional)
    $('#password').on('keyup', function() {
        let password = $(this).val();
        if (password !== "" && password.length < 8) {
            $('#password-error').text('Password minimal 8 karakter.');
            $('#password-success').text('');
        } else {
            $('#password-error').text('');
            $('#password-success').text('Password valid.');
        }
    });

    // Validasi Konfirmasi Password (Opsional)
    $('#confirmPassword').on('keyup', function() {
        let confirmPassword = $(this).val();
        let password = $('#password').val();
        if (confirmPassword !== "" && confirmPassword !== password) {
            $('#confirmPassword-error').text('Konfirmasi password tidak sesuai.');
            $('#confirmPassword-success').text('');
        } else {
            $('#confirmPassword-error').text('');
            $('#confirmPassword-success').text('Konfirmasi password valid.');
        }
    });

    // Validasi Role
    $('#role').on('change', function() {
        let role = $(this).val();
        if (role === "") {
            $('#role-error').text('Role harus dipilih.');
            $('#role-success').text('');
        } else {
            $('#role-error').text('');
            $('#role-success').text('Role valid.');

            // Jika role adalah admin, nonaktifkan validasi loket
            if (role === 'admin') {
                $('#loket').removeAttr('required');
                $('#loket-error').text('');
                $('#loket-success').text('Loket tidak diperlukan untuk admin.');
            } else {
                $('#loket').attr('required', true);
                $('#loket-error').text('');
                $('#loket-success').text('');
            }
        }
    });

    // Validasi Loket
    $('#loket').on('change', function() {
        let loket = $(this).val();
        if ($('#role').val() !== 'admin' && loket === "") {
            $('#loket-error').text('Loket harus dipilih.');
            $('#loket-success').text('');
        } else {
            $('#loket-error').text('');
            $('#loket-success').text('Loket valid.');
        }
    });

    // Tombol Update
    $('#button-update').on('click', function(e) {
        e.preventDefault();

        // Trigger validasi semua field
        $('#fullname').trigger('keyup');
        $('#phoneNumber').trigger('keyup');
        $('#email').trigger('keyup');
        $('#password').trigger('keyup');
        $('#confirmPassword').trigger('keyup');
        $('#role').trigger('change');
        $('#loket').trigger('change');

        // Cek apakah ada error
        let hasError = (
            $('#fullname-error').text() !== "" ||
            $('#phoneNumber-error').text() !== "" ||
            $('#email-error').text() !== "" ||
            ($('#password').val() !== "" && $('#password-error').text() !== "") ||
            ($('#confirmPassword').val() !== "" && $('#confirmPassword-error').text() !== "") ||
            $('#role-error').text() !== "" ||
            ($('#role').val() !== 'admin' && $('#loket-error').text() !== "")
        );

        // Jika tidak ada error, kirim data form
        if (!hasError) {
            var dataForm = new FormData(document.getElementById('form-edit'));

            $.ajax({
                url: '<?= site_url('UserManagementController/proses_edit'); ?>', // URL untuk proses update
                type: 'POST',
                data: dataForm,
                processData: false,
                contentType: false,
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        // Tampilkan SweetAlert sukses dan redirect
                        showSweetAlert(data.status, data.message, '<?= site_url('UserManagementController'); ?>');
                    } else {
                        // Tampilkan SweetAlert error tanpa redirect
                        showSweetAlert(data.status, data.message);
                    }
                },
                error: function(xhr, status, error) {
                    // Tampilkan SweetAlert untuk error AJAX
                    showSweetAlert('error', 'Terjadi kesalahan: ' + error);
                }
            });
        }
    });

    // Tombol Kembali
    $('#button-back').on('click', function(e) {
        e.preventDefault();
        window.location.href = '<?= site_url('UserManagementController'); ?>';
    });
});

</script>