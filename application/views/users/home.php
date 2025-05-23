<div class="container">
  <div class="row justify-content-center mt-5">
      <?php foreach ($data_loket as $row) : ?>
      <div class="col-md-4 mb-4">
          <div class="card custom-card" id="loket-antrian-<?= $row['id_layanan']; ?>" data-id-loket="<?= $row['id_loket'];?>" data-jenis-loket="<?= $row['jenis'];?>" data-id-layanan="<?= $row['id_layanan']; ?>">
              <div class="card-header custom-card-header text-center">
                  <h4 class="text-uppercase text-center mb-2" id="jenis_layanan_<?=$row['id_loket'];?>" style="font-weight:700; letter-spacing:1px;"><?= $row['nama']; ?></h4>
                  <div>
                      <span class="badge-antrian-jenis badge badge-<?= ($row['jenis'] == '1') ? 'success' : 'danger';?>">
                          <?= ($row['jenis'] == '1') ? 'BOOKING' : 'NON BOOKING';?>
                      </span>
                  </div>
              </div>
              <div class="card-body text-center" style="padding: 32px 12px 18px 12px;">
                  <h5 class="card-title">Antrian</h5>
                  <p class="display-4" id="nomor_antrian_<?=$row['id_loket'];?>"></p>
                  <span class="badge text-center" id="status_antrian"></span>
              </div>
          </div>
      </div>
      <?php endforeach; ?>
  </div>
</div>
<script>
  $(document).ready(function() {
  // Set interval untuk mengambil data antrian setiap 1 detik
      setInterval(function() {
          $('.custom-card').each(function() {
              var id_loket = $(this).data('id-loket'); // Ambil id_loket dari data attribute
              var jenis_loket     = $(this).data('jenis-loket'); // Ambil id_loket dari data attribute
              var id_layanan = $(this).data('id-layanan'); // Ambil id_layanan dari data attribute
              var $display = $(this).find('.display-4'); // Temukan elemen untuk menampilkan nomor antrian
              var $status_antrian = $(this).find('#status_antrian'); // Temukan elemen untuk menampilkan status antrian
              $.ajax({
                  url: '<?= base_url("AntrianController/get_antrian");?>', // Ganti dengan path ke file PHP Anda
                  method: 'POST',
                  data: { id_layanan: id_layanan, id_loket: id_loket,jenis_loket:jenis_loket }, // Kirim id_layanan dan id_loket sebagai parameter
                  dataType: 'json',
                  success: function(data) {
                      // Memeriksa apakah data yang diterima tidak kosong
                      if (data.length > 0 && data[0].no_antrian !== undefined) {
                          // Mengambil semua nomor antrian dan status antrian dari data
                          var nomor_antrian = data.map(function(item) {
                              return item.no_antrian; // Mengambil nomor antrian dari setiap objek
                          }).join(', '); // Menggabungkan nomor antrian menjadi string

                          var kode_layanan = data.map(function(item) {
                              return item.kode_layanan; // Mengambil kode layanan dari setiap objek
                          }).join(', '); // Menggabungkan kode layanan menjadi string

                          var status_antrian = data.map(function(item) {
                              return item.status_antrian; // Mengambil status antrian dari setiap objek
                          });
                          var id_antrians = data.map(function(item) {
                              return item.id; // Mengambil id antrian dari setiap objek
                          });

                          $('[id^="btn-' + id_loket + '"]').attr('data-id-antrian', id_antrians);
                          
                          // Inisialisasi variabel sa dan badge_color
                          var sa;
                          var badge_color;

                          // Memeriksa status antrian
                          if (status_antrian.every(function(status) { return status === 'buat'; })) {
                              sa = 'menunggu';
                              badge_color = 'badge-secondary';
                          } else if (status_antrian.some(function(status) { return status === 'panggil'; })) {
                              sa = 'Sedang Memanggil';
                              badge_color = 'badge-success';
                          } else if (status_antrian.some(function(status) { return status === 'proses'; })) {
                              sa = 'Sedang Melayani';
                              badge_color = 'badge-info';
                          } else if (status_antrian.some(function(status) { return status === 'selesai'; })) {
                              sa = 'Antrian Selesai';
                              badge_color = 'badge-warning';
                          } else {
                              sa = 'Antrian dibatalkan'; // Status lain yang tidak terduga
                              badge_color = 'badge-danger';
                          }

                          // Memperbarui tampilan
                          $display.text(nomor_antrian); // Memperbarui nomor antrian di dalam elemen
                          $status_antrian.text(sa); // Memperbarui status antrian di dalam elemen
                          $status_antrian.removeClass().addClass('badge ' + badge_color); // Mengatur kelas badge
                          // Memperbarui data-id-antrian pada semua tombol
                      

                      } else {
                          $display.text('0'); // Menampilkan pesan jika tidak ada antrian
                          $status_antrian.text('Tidak ada antrian'); // Memperbarui status antrian di dalam elemen
                          $status_antrian.removeClass().addClass('badge badge-danger'); // Mengatur kelas badge
                      }
                  },
                  error: function(xhr, status, error) {
                      console.error('Error fetching data:', error);
                  }
              });
          });
      }, 1000); // 1000 ms = 1 detik
  });
</script>