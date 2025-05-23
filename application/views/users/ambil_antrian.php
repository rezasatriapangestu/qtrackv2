<style>
  .ambil-antrian-header {
    background: linear-gradient(90deg, #e3f0ff 0%, #f8faf8 100%);
    border-radius: 28px 28px 0 0;
    color: #222;
    padding: 24px 28px 10px 28px;
    margin-bottom: 24px;
    font-weight: 700;
    font-size: 1.35rem;
    letter-spacing: 1px;
    box-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
    text-align: center;
  }
  .btn-custom {
    width: 100%;
    margin-top: 20px;
    padding: 15px;
    font-size: 18px;
    font-weight: bold;
    border-radius: 18px;
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.08);
    transition: background 0.2s, color 0.2s, box-shadow 0.2s;
    min-height: 60px;
    min-width: 180px;
    max-width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    white-space: normal;
    text-align: center;
  }
  .btn-custom.btn-success {
    background: #2E7D32;
    border: none;
  }
  .btn-custom.btn-success:hover {
    background: #388e3c;
    color: #fff;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.13);
  }
  .video-container {
    text-align: center;
    margin-bottom: 30px;
  }
  video {
    width: 100%;
    height: auto;
    border-radius: 18px;
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.10);
  }
  .modal-booking {
      display: none;
      position: fixed;
      z-index: 1050;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.9);
      animation: fadeIn 0.3s;
  }
  .modal-content-booking {
      background-color: #fff;
      margin: 0;
      padding: 20px;
      border: none;
      width: 100%;
      height: 100%;
      border-radius: 0;
      box-shadow: none;
      display: flex;
      flex-direction: column;
  }
  @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
  }
  .modal-header {
      padding: 15px;
      border-bottom: 1px solid #eee;
      display: flex;
      justify-content: space-between;
      align-items: center;
  }
  .modal-header h3 {
      font-weight: 700;
      color: #2E7D32;
      margin-bottom: 0;
      font-size: 1.3rem;
      letter-spacing: 1px;
  }
  .modal-body {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
  }
  .modal-footer {
      padding: 15px;
      border-top: 1px solid #eee;
      text-align: right;
  }
  .close {
      color: #aaa;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
  }
  .close:hover {
      color: #333;
      transform: rotate(90deg);
  }
  .time-slots {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 15px;
      margin-top: 20px;
  }
  .time-slot {
      padding: 15px;
      background: #f8f9fa;
      border: 1px solid #ddd;
      border-radius: 12px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 16px;
      box-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
  }
  .time-slot:hover {
      background: #e3f0ff;
      transform: translateY(-3px) scale(1.04);
      box-shadow: 0 4px 12px rgba(46, 125, 50, 0.10);
  }
  .time-slot.selected {
      background: #2E7D32;
      color: white;
      border-color: #2E7D32;
  }
  .btn-submit-booking {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      font-size: 18px;
      font-weight: bold;
      border-radius: 12px;
      background: #2E7D32;
      border: none;
      color: #fff;
      transition: background 0.2s;
  }
  .btn-submit-booking:hover {
      background: #388e3c;
  }
  @media (min-width: 768px) {
      .modal-content-booking {
          width: 80%;
          height: 90%;
          margin: 5% auto;
          border-radius: 18px;
      }
      .time-slots {
          grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      }
  }
  .layanan-btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    justify-content: center;
    align-items: stretch;
  }
  .layanan-btn-group .col {
    flex: 1 1 180px;
    max-width: 220px;
    min-width: 180px;
    display: flex;
    align-items: stretch;
    padding: 0 6px;
  }
</style>

<div class="ambil-antrian-header">
    Ambil Antrian Layanan
</div>
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-6 video-container">
      <h2 class="text-success mb-3" style="font-weight:700;">Video Layanan</h2>
      <video controls>
        <source src="path_to_your_video.mp4" type="video/mp4">
        Browser Anda tidak mendukung tag video.
      </video>
    </div>
    <div class="col-md-6">
      <h2 class="text-success text-center mb-4" style="font-weight:700;">Layanan</h2>
      <div class="row layanan-btn-group">
        <?php foreach ($data_layanan as $dl) : ?>
          <div class="col d-flex align-items-stretch">
            <button class="btn btn-success btn-custom btn-service" 
                    data-id-layanan="<?= $dl['id_layanan'] ?>" 
                    data-kode-layanan="<?= $dl['kode'] ?>" 
                    data-nama-layanan="<?= $dl['nama'] ?>">
              <?= $dl['nama'] ?>
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Booking Modal Full Screen -->
<div id="bookingModal" class="modal-booking">
  <div class="modal-content-booking">
    <div class="modal-header">
      <h3 class="text-center" id="modalServiceName">Pilih Waktu Booking</h3>
      <span class="close">&times;</span>
    </div>
    <div class="modal-body">
      <input type="hidden" id="selected_id_layanan">
      <input type="hidden" id="selected_kode_layanan">
      <div class="form-group">
        <label class="h5">Pilih Waktu Booking</label>
        <div id="timeSlotInfo" class="alert alert-info mb-3">
          <i class="fas fa-info-circle"></i> Silakan pilih slot waktu yang tersedia
        </div>
        <div class="time-slots" id="id_waktu_booking">
          <!-- Time slots will be generated here -->
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-success btn-submit-booking" id="btnSubmitBooking">
        <i class="fas fa-calendar-check"></i> Booking Sekarang
      </button>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    // Buka modal dengan animasi
    function openBookingModal() {
        $('#bookingModal').fadeIn(300);
        $('.modal-content-booking').css({
            'transform': 'scale(0.9)',
            'opacity': '0'
        }).animate({
            'transform': 'scale(1)',
            'opacity': '1'
        }, 300);
    }

    // Tutup modal dengan animasi
    function closeBookingModal() {
        $('.modal-content-booking').animate({
            'transform': 'scale(0.9)',
            'opacity': '0'
        }, 200, function() {
            $('#bookingModal').fadeOut(200);
        });
        selectedSlot = null;
    }

    // Buka modal saat tombol layanan diklik
    $(document).on('click', '.btn-service', function() {
        const id_layanan = $(this).data('id-layanan');
        const kode_layanan = $(this).data('kode-layanan');
        const nama_layanan = $(this).data('nama-layanan');
        
        current_id_layanan = id_layanan;
        $('#selected_id_layanan').val(id_layanan);
        $('#selected_kode_layanan').val(kode_layanan);
        $('#modalServiceName').text(nama_layanan + ' - Pilih Waktu Booking');
        
        // Tampilkan loading
        $('#id_waktu_booking').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3">Memuat waktu tersedia...</p>
            </div>
        `);
        
        // Ambil waktu booking dari server
        $.ajax({
            url: '<?= site_url("UserController/get_waktu_booking") ?>',
            type: 'POST',
            dataType: 'json',
            data: { id_layanan: id_layanan },
            success: function(slots) {
                $('#id_waktu_booking').empty();
                
                if (slots.length === 0) {
                    $('#id_waktu_booking').html(`
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-circle"></i> Tidak ada slot waktu tersedia untuk layanan ini.
                        </div>
                    `);
                    return;
                }
                
                // Buat grid time slots
                $.each(slots, function(index, slot) {
                    const timeSlot = $(`
                        <div class="time-slot" data-slot-id="${slot.id}">
                            <h6 class="slot-time">${slot.waktu_awal}-${slot.waktu_akhir}</h6>
                            <div class="slot-info">
                                <span class="badge badge-success">Kuota: ${slot.kuota_tersedia}</span>
                            </div>
                        </div>
                    `).click(function() {
                        $('.time-slot').removeClass('selected');
                        $(this).addClass('selected');
                        selectedSlot = slot;
                        $('#timeSlotInfo').html(`
                            <i class="fas fa-check-circle"></i> Slot dipilih: <strong>${slot.waktu_awal}-${slot.waktu_akhir}</strong>
                        `).removeClass('alert-info').addClass('alert-success');
                    });
                    
                    $('#id_waktu_booking').append(timeSlot);
                });
            },
            error: function() {
                $('#id_waktu_booking').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-times-circle"></i> Gagal memuat waktu booking. Silakan coba lagi.
                    </div>
                `);
            }
        });
        
        openBookingModal();
    });

    // Tutup modal saat tombol close diklik
    $('.close').click(closeBookingModal);

    // Tutup modal saat klik di luar modal
    $(window).click(function(event) {
        if ($(event.target).is('#bookingModal')) {
            closeBookingModal();
        }
    });

    // Submit booking
    $('#btnSubmitBooking').click(function() {
        if (!selectedSlot) {
            showSweetAlert('warning', 'Peringatan', 'Harap pilih waktu booking terlebih dahulu');
            return;
        }
        
        const btn = $(this);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);
        
        $.ajax({
            url: '<?= site_url("UserController/simpan_booking") ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                id_layanan: $('#selected_id_layanan').val(),
                kode_layanan: $('#selected_kode_layanan').val(),
                id_waktu_booking: selectedSlot.id
            },
            success: function(response) {
                btn.html('<i class="fas fa-calendar-check"></i> Booking Sekarang').prop('disabled', false);
                
                if (response.status == 'success') {
                    closeBookingModal();
                    showSweetAlert(response.status, response.message, '<?= site_url('HomeController'); ?>');
                    // showSweetAlert(
                    //     'success', 
                    //     'Booking Berhasil', 
                    //     'Nomor antrian Anda: ' + response.data.no_antrian,
                    //     'booking/detail/' + response.data.id_booking
                    // );
                } else {
                    showSweetAlert(response.status, response.message);
                    $('.btn-service[data-id-layanan="' + current_id_layanan + '"]').click();
                }
            },
            error: function() {
                btn.html('<i class="fas fa-calendar-check"></i> Booking Sekarang').prop('disabled', false);
                showSweetAlert('error', 'Terjadi kesalahansaat memproses booking');
            }
        });
    });
});
</script>