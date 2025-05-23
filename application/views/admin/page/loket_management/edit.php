<style>
.loket-form-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
    margin-bottom: 32px;
}
.loket-form-card .card-body {
    padding: 32px 24px;
}
.loket-form-card .form-group label {
    font-weight: 500;
    color: #2E7D32;
}
.loket-form-card .form-control {
    border-radius: 14px;
    border: 1px solid #c8e6c9;
}
.loket-form-card .btn-success {
    border-radius: 14px;
    font-weight: 500;
    background: #2E7D32;
    border: none;
}
.loket-form-card .btn-success:hover {
    background: #388e3c;
}
.loket-form-card .btn-danger {
    border-radius: 14px;
    font-weight: 500;
}
</style>
<div class="card shadow mb-4 loket-form-card">
    <div class="card-body">
        <div class="container">
            <form id="form-edit">
                <!-- Input Hidden untuk ID -->
                <input type="hidden" id="id_loket" name="id_loket" value="<?= $loket->id_loket; ?>">

                <!-- Input Nama Loket -->
                <div class="form-group">
                    <label for="name" class="">Nama Loket</label>
                    <input type="text" class="form-control" id="name" placeholder=" " name="name" value="<?= $loket->nama; ?>" required>
                    <span id="name-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="name-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Input Nama Layanan -->
                <div class="form-group">
                    <label for="id_layanan" class="">Nama Layanan</label>
                    <select class="form-control" id="id_layanan" name="id_layanan" required>
                        <option value="" selected>--Pilih--</option>
                        <?php foreach ($data_layanan as $l) : ?>
                            <option value="<?= $l['id_layanan']; ?>" <?= ($l['id_layanan'] == $loket->id_layanan) ? 'selected' : ''; ?>>
                                <?= $l['nama']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span id="id_layanan-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="id_layanan-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Input Status -->
                <div class="form-group">
                    <label for="status" class="">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="" selected>--Pilih--</option>
                        <option value="1" <?= ($loket->status == 1) ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?= ($loket->status == 0) ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                    <span id="status-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="status-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Input Jenis -->
                <div class="form-group">
                    <label for="jenis" class="">Jenis</label>
                    <select class="form-control" id="jenis" name="jenis" required>
                        <option value="" selected>--Pilih--</option>
                        <option value="1" <?= ($loket->jenis == 1) ? 'selected' : ''; ?>>Booking</option>
                        <option value="0" <?= ($loket->jenis == 0) ? 'selected' : ''; ?>>Tidak Booking</option>
                    </select>
                    <span id="jenis-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="jenis-success" class="text-success" style="font-size: 12px !important;"></span>
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
    // Validasi Nama Loket
    $('#name').on('keyup', function() {
        let name = $(this).val();
        if (name === "") {
            $('#name-error').text('Nama loket harus diisi.');
            $('#name-success').text('');
        } else {
            $('#name-error').text('');
            $('#name-success').text('Nama loket valid.');
        }
    });

    // Validasi Nama Layanan
    $('#id_layanan').on('change', function() {
        let id_layanan = $(this).val();
        if (id_layanan === "") {
            $('#id_layanan-error').text('Nama layanan harus dipilih.');
            $('#id_layanan-success').text('');
        } else {
            $('#id_layanan-error').text('');
            $('#id_layanan-success').text('Nama layanan valid.');
        }
    });

    // Validasi Status
    $('#status').on('change', function() {
        let status = $(this).val();
        if (status === "") {
            $('#status-error').text('Status harus dipilih.');
            $('#status-success').text('');
        } else {
            $('#status-error').text('');
            $('#status-success').text('Status valid.');
        }
    });

    // Validasi Jenis
    $('#jenis').on('change', function() {
        let jenis = $(this).val();
        if (jenis === "") {
            $('#jenis-error').text('Jenis harus dipilih.');
            $('#jenis-success').text('');
        } else {
            $('#jenis-error').text('');
            $('#jenis-success').text('Jenis valid.');
        }
    });

    // Tombol Update
    $('#button-update').on('click', function(e) {
        e.preventDefault();

        // Trigger validasi semua field
        $('#name').trigger('keyup');
        $('#id_layanan').trigger('change');
        $('#status').trigger('change');
        $('#jenis').trigger('change');

        // Cek apakah ada error
        let hasError = (
            $('#name-error').text() !== "" || 
            $('#id_layanan-error').text() !== "" ||
            $('#status-error').text() !== "" ||
            $('#jenis-error').text() !== ""
        );

        // Jika tidak ada error, kirim data form
        if (!hasError) {
            var dataForm = new FormData(document.getElementById('form-edit'));

            $.ajax({
                url: '<?= site_url('LoketController/proses_edit'); ?>', // URL untuk proses update
                type: 'POST',
                data: dataForm,
                processData: false,
                contentType: false,
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        // Tampilkan SweetAlert sukses dan redirect
                        showSweetAlert(data.status, data.message, '<?= site_url('LoketController'); ?>');
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
        window.location.href = '<?= site_url('LoketController'); ?>';
    });
});

// Fungsi untuk menampilkan SweetAlert
function showSweetAlert(icon, title, redirectUrl = null) {
    Swal.fire({
        icon: icon,
        title: title,
        showConfirmButton: false,
        timer: 1500
    }).then(() => {
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
}
</script>