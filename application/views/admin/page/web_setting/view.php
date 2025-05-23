<style>
.web-setting-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
    margin-bottom: 32px;
}
.web-setting-card .card-body {
    padding: 32px 24px;
}
.web-setting-card .form-group label {
    font-weight: 500;
    color: #2E7D32;
}
.web-setting-card .form-control {
    border-radius: 14px;
    border: 1px solid #c8e6c9;
}
.web-setting-card .btn-success {
    border-radius: 14px;
    font-weight: 500;
    background: #2E7D32;
    border: none;
}
.web-setting-card .btn-success:hover {
    background: #388e3c;
}
.web-setting-card .btn-danger {
    border-radius: 14px;
    font-weight: 500;
}
</style>
<div class="card shadow mb-4 web-setting-card">
    <div class="card-header py-3 d-flex align-items-center" style="border-radius:28px 28px 0 0; background:linear-gradient(90deg,#2E7D32 60%,#43a047 100%); color:#fff; font-weight:600; font-size:1.2rem; letter-spacing:1px;">
        <span class="fs-5 fw-bold">Pengaturan Web</span>
    </div>
    <div class="card-body">
        <div class="container">
          <form id="form-edit">
              <!-- Input Hidden untuk ID -->
              <input type="hidden" id="id" name="id" value="<?= $web->id; ?>">

              <!-- Input Nama Web -->
              <div class="form-group">
                  <label for="nama_web" class="">Nama Web</label>
                  <input type="text" class="form-control" id="nama_web" placeholder=" " name="nama_web" value="<?= $web->nama_web; ?>" required>
                  <span id="nama_web-error" class="text-danger" style="font-size: 12px !important;"></span>
                  <span id="nama_web-success" class="text-success" style="font-size: 12px !important;"></span>
              </div>

              <div class="form-group">
                  <label for="alamat" class="">Alamat</label>
                  <input type="text" class="form-control" id="alamat" placeholder=" " name="alamat" value="<?= $web->alamat; ?>" required>
                  <span id="alamat-error" class="text-danger" style="font-size: 12px !important;"></span>
                  <span id="alamat-success" class="text-success" style="font-size: 12px !important;"></span>
              </div>

              <div class="form-group">
                  <label for="footer" class="">Footer</label>
                  <input type="text" class="form-control" id="footer" placeholder=" " name="footer" value="<?= $web->footer; ?>" required>
                  <span id="footer-error" class="text-danger" style="font-size: 12px !important;"></span>
                  <span id="footer-success" class="text-success" style="font-size: 12px !important;"></span>
              </div>

              <div class="form-group">
                  <label for="kode_akses" class="">Kode Akses</label>
                  <input type="text" class="form-control" id="kode_akses" placeholder=" " name="kode_akses" value="<?= $web->kode_akses; ?>" required>
                  <span id="kode_akses-error" class="text-danger" style="font-size: 12px !important;"></span>
                  <span id="kode_akses-success" class="text-success" style="font-size: 12px !important;"></span>
              </div>

              <div class="form-group">
                  <label for="create_by" class="">Dev</label>
                  <input type="text" class="form-control" id="create_by" placeholder=" " name="create_by" value="<?= $web->create_by; ?>" required>
                  <span id="create_by-error" class="text-danger" style="font-size: 12px !important;"></span>
                  <span id="create_by-success" class="text-success" style="font-size: 12px !important;"></span>
              </div>
          </form>
          <!-- Tombol Update dan Kembali -->
          <button id="button-update" class="btn btn-success btn-sm mt-4">
              Simpan
          </button>
          <button id="button-back" class="btn btn-danger btn-sm mt-4">
              Kembali
          </button>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    // Validasi Nama Loket
    $('#nama_web').on('keyup', function() {
        let nama_web = $(this).val();
        if (nama_web === "") {
            $('#nama_web-error').text('Nama Web harus diisi.');
            $('#nama_web-success').text('');
        } else {
            $('#nama_web-error').text('');
            $('#nama_web-success').text('Nama Web valid.');
        }
    });

    $('#alamat').on('keyup', function() {
        let alamat = $(this).val();
        if (alamat === "") {
            $('#alamat-error').text('Alamat harus diisi.');
            $('#alamat-success').text('');
        } else {
            $('#alamat-error').text('');
            $('#alamat-success').text('Alamat valid.');
        }
    });
    $('#footer').on('keyup', function() {
        let footer = $(this).val();
        if (footer === "") {
            $('#footer-error').text('Footer harus diisi.');
            $('#footer-success').text('');
        } else {
            $('#footer-error').text('');
            $('#footer-success').text('Footer valid.');
        }
    });
    $('#kode_akses').on('keyup', function() {
        let kode_akses = $(this).val();
        if (kode_akses === "") {
            $('#kode_akses-error').text('Kode akses harus diisi.');
            $('#kode_akses-success').text('');
        } else {
            $('#kode_akses-error').text('');
            $('#kode_akses-success').text('Kode Akses valid.');
        }
    });
    $('#create_by').on('keyup', function() {
        let create_by = $(this).val();
        if (create_by === "") {
            $('#create_by-error').text('Dev harus diisi.');
            $('#create_by-success').text('');
        } else {
            $('#create_by-error').text('');
            $('#create_by-success').text('Dev valid.');
        }
    });

  
    // Tombol Update
    $('#button-update').on('click', function(e) {
        e.preventDefault();

        // Trigger validasi semua field
        $('#nama_web').trigger('keyup');
        $('#alamat').trigger('keyup');
        $('#footer').trigger('change');
        $('#kode_akses').trigger('change');
        $('#create_by').trigger('change');

        // Cek apakah ada error
        let hasError = (
            $('#nama_web-error').text() !== "" || 
            $('#alamat-error').text() !== "" ||
            $('#footer-error').text() !== "" ||
            $('#kode_akses-error').text() !== "" ||
            $('#create_by-error').text() !== ""
        );

        // Jika tidak ada error, kirim data form
        if (!hasError) {
            var dataForm = new FormData(document.getElementById('form-edit'));

            $.ajax({
                url: '<?= site_url('WebController/Simpan'); ?>', // URL untuk proses update
                type: 'POST',
                data: dataForm,
                processData: false,
                contentType: false,
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        // Tampilkan SweetAlert sukses dan redirect
                        showSweetAlert(data.status, data.message, '<?= site_url('WebController'); ?>');
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
        window.location.href = '<?= site_url('WebController'); ?>';
    });
});

</script>