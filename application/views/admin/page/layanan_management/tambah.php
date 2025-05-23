<style>
.layanan-form-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
    margin-bottom: 32px;
}
.layanan-form-card .card-body {
    padding: 32px 24px;
}
.layanan-form-card .form-group label {
    font-weight: 500;
    color: #2E7D32;
}
.layanan-form-card .form-control {
    border-radius: 14px;
    border: 1px solid #c8e6c9;
}
.layanan-form-card .btn-success {
    border-radius: 14px;
    font-weight: 500;
    background: #2E7D32;
    border: none;
}
.layanan-form-card .btn-success:hover {
    background: #388e3c;
}
.layanan-form-card .btn-danger {
    border-radius: 14px;
    font-weight: 500;
}
</style>
<div class="card shadow mb-4 layanan-form-card">
    <div class="card-body">
        <div class="container">
            <form id="form-tambah">
                <!-- Input Nama Lengkap -->
                <div class="form-group ">
                    <label for="kode" class="">Kode Layanan</label>
                    <input type="text" class="form-control" id="kode" placeholder=" " name="kode" required>
                    <span id="kode-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="kode-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>
                <div class="form-group ">
                    <label for="name" class="">Nama Layanan</label>
                    <input type="text" class="form-control" id="name" placeholder=" " name="name" required>
                    <span id="name-error" class="text-danger" style="font-size: 12px !important;"></span>
                    <span id="name-success" class="text-success" style="font-size: 12px !important;"></span>
                </div>

                <!-- Tombol Simpan -->
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
    // Validasi Nama Lengkap
    $('#kode').on('keyup', function() {
        let kode = $(this).val();
        if (kode === "") {
            $('#kode-error').text('Kode layanan harus diisi.');
            $('#kode-success').text('');
        } else {
            $('#kode-error').text('');
            $('#kode-success').text('Kode layanan valid.');
        }
    });
    $('#name').on('keyup', function() {
        let name = $(this).val();
        if (name === "") {
            $('#name-error').text('Nama layanan harus diisi.');
            $('#name-success').text('');
        } else {
            $('#name-error').text('');
            $('#name-success').text('Nama layanan valid.');
        }
    });

   
    // Tombol Simpan
    $('#button-simpan').on('click', function(e) {
        e.preventDefault();

        // Trigger validasi semua field
        $('#name').trigger('keyup');
       

        // Cek apakah ada error
        let hasError = (
            $('#name-error').text() !== "" || $('#kode-error').text() !== ""
        );

        // Jika tidak ada error, kirim data form
        if (!hasError) {
            var dataForm = new FormData(document.getElementById('form-tambah'));

            $.ajax({
                url: '<?= site_url('LayananController/proses_tambah'); ?>',
                type: 'POST',
                data: dataForm,
                processData: false,
                contentType: false,
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        // Tampilkan SweetAlert sukses dan redirect
                        showSweetAlert(data.status, data.message, '<?= site_url('LayananController'); ?>');
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

    $('#button-back').on('click', function(e) {
        e.preventDefault();
        window.location.href = '<?= site_url('LayananController'); ?>'; // Redirect ke halaman utama
    });
});
</script>