<style>
.booking-form-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
    margin-bottom: 32px;
}
.booking-form-card .card-body {
    padding: 32px 24px;
}
.booking-form-card .form-group label {
    font-weight: 500;
    color: #2E7D32;
}
.booking-form-card .form-control {
    border-radius: 14px;
    border: 1px solid #c8e6c9;
}
.booking-form-card .btn-success {
    border-radius: 14px;
    font-weight: 500;
    background: #2E7D32;
    border: none;
}
.booking-form-card .btn-success:hover {
    background: #388e3c;
}
.booking-form-card .btn-danger {
    border-radius: 14px;
    font-weight: 500;
}
</style>
<div class="card shadow mb-4 booking-form-card">
    <div class="card-body">
        <div class="container">
            <form id="form-edit">
                <!-- Input Hidden untuk ID -->
                <input type="hidden" id="id_waktu_booking" name="id_waktu_booking" value="<?= $waktu_booking->id_waktu_booking; ?>">

                <!-- Input Kode Layanan -->
                <div class="form-group">
                    <label for="waktu_awal">Waktu Awal</label>
                    <input type="text" class="form-control" id="waktu_awal" placeholder=" " name="waktu_awal" value="<?= $waktu_booking->waktu_awal; ?>" required>
                    <span id="waktu_awal-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="waktu_awal-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Input Kode Layanan -->
                <div class="form-group">
                    <label for="waktu_akhir">Waktu Akhir</label>
                    <input type="text" class="form-control" id="waktu_akhir" placeholder=" " name="waktu_akhir" value="<?= $waktu_booking->waktu_akhir; ?>" required>
                    <span id="waktu_akhir-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="waktu_akhir-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Input Nama Layanan -->
                <div class="form-group">
                    <label for="maks_antrian">Maksimal Antrian</label>
                    <input type="number" class="form-control" id="maks_antrian" placeholder=" " name="maks_antrian" value="<?= $waktu_booking->maks_antrian; ?>" required>
                    <span id="maks_antrian-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="maks_antrian-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Tombol Simpan dan Kembali -->
                <button id="button-simpan" class="btn btn-success btn-sm mt-4">
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
    // Validasi Kode Layanan
    $('#waktu_awal').on('keyup', function() {
        let waktu_awal = $(this).val();
        if (waktu_awal === "") {
            $('#waktu_awal-error').text('Waktu awal harus diisi.');
            $('#waktu_awal-success').text('');
        } else {
            $('#waktu_awal-error').text('');
            $('#waktu_awal-success').text('Waktu awal valid.');
        }
    });

    // Validasi Kode Layanan
    $('#waktu_akhir').on('keyup', function() {
        let waktu_akhir = $(this).val();
        if (waktu_akhir === "") {
            $('#waktu_akhir-error').text('Waktu awal harus diisi.');
            $('#waktu_akhir-success').text('');
        } else {
            $('#waktu_akhir-error').text('');
            $('#waktu_akhir-success').text('Waktu awal valid.');
        }
    });

    // Validasi Nama Layanan
    $('#maks_antrian').on('keyup', function() {
        let maks_antrian = $(this).val();
        if (maks_antrian === "") {
            $('#maks_antrian-error').text('Maksimal antrian harus diisi.');
            $('#maks_antrian-success').text('');
        } else {
            $('#maks_antrian-error').text('');
            $('#maks_antrian-success').text('Maksimal antrian valid.');
        }
    });

    // Tombol Simpan
    $('#button-simpan').on('click', function(e) {
        e.preventDefault();

        // Trigger validasi semua field
        $('#waktu_awal').trigger('keyup');
        $('#waktu_akhir').trigger('keyup');
        $('#maks_antrian').trigger('keyup');

        // Cek apakah ada error
        let hasError = (
            $('#waktu_awal-error').text() !== "" || $('#waktu_akhir-error').text() !== "" || $('#maks_antrian-error').text() !== ""
        );

        // Jika tidak ada error, kirim data form
        if (!hasError) {
            var dataForm = new FormData(document.getElementById('form-edit'));

            $.ajax({
                url: '<?= site_url('BookingController/proses_edit'); ?>', // URL untuk proses update
                type: 'POST',
                data: dataForm,
                processData: false,
                contentType: false,
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        // Tampilkan SweetAlert sukses dan redirect
                        showSweetAlert(data.status, data.message, '<?= site_url('BookingController'); ?>');
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
        window.location.href = '<?= site_url('BookingController'); ?>'; // Redirect ke halaman utama
    });
});

</script>