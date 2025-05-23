<style>
    .custom-card {
        border: none;
        border-radius: 32px;
        box-shadow: 0 8px 24px rgba(46, 125, 50, 0.12);
        transition: transform 0.3s cubic-bezier(.4,2,.6,1), box-shadow 0.3s;
        background: #fff;
        overflow: hidden;
        position: relative;
    }

    .custom-card:hover {
        transform: translateY(-8px) scale(1.03);
        box-shadow: 0 16px 32px rgba(46, 125, 50, 0.18);
    }

    .custom-card-header {
        color: #2E7D32;
        border-radius: 32px 32px 0 0;
        padding: 24px 18px 10px 18px;
        background: none;
        box-shadow: none;
        position: relative;
        border-bottom: none;
    }

    .custom-card-header .badge-antrian-jenis {
        display: inline-block;
        font-size: 0.95rem;
        padding: 6px 18px;
        border-radius: 16px;
        letter-spacing: 1px;
        margin-top: 10px;
        margin-bottom: 0;
        box-shadow: 0 2px 8px rgba(46, 125, 50, 0.10);
    }

    .custom-card-footer {
        background: #f1f8e9;
        border-radius: 0 0 32px 32px;
        padding: 16px 10px;
        border-top: 1px solid #e0e0e0;
    }

    .custom-card .card-title {
        font-weight: 600;
        color: #2E7D32;
        margin-bottom: 8px;
        letter-spacing: 1px;
    }

    .custom-card .display-4 {
        font-size: 3.2rem;
        font-weight: 700;
        color: #2E7D32;
        margin-bottom: 8px;
        letter-spacing: 2px;
        text-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
    }

    .custom-card .badge {
        font-size: 1rem;
        border-radius: 16px;
        padding: 6px 18px;
        margin-top: 8px;
    }

    .btn-antrian {
        border-radius: 16px !important;
        font-weight: 500;
        margin: 0 2px;
        min-width: 80px;
        box-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
        transition: background 0.2s, color 0.2s;
    }

    .btn-antrian:active, .btn-antrian:focus {
        outline: none !important;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.18) !important;
    }

    .table-responsive {
        margin-top: 20px;
    }

    /* Hapus border-radius pada tabel, th, td */
    .table, .table th, .table td {
        border-radius: 0 !important;
    }

    .card, .card-header, .card-footer {
        border-radius: 24px !important;
    }
</style>

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
            <div class="card-footer custom-card-footer text-center">
                <button class="btn btn-sm btn-danger mt-2 btn-antrian btn-batal" id="btn-batal-<?=$row['id_loket'];?>" data-id-antrian="" disabled>Batal</button>
                <button class="btn btn-sm btn-success mt-2 btn-antrian btn-panggil" id="btn-panggil-<?=$row['id_loket'];?>" data-id-antrian="" disabled>Panggil</button>
                <button class="btn btn-sm btn-secondary mt-2 btn-antrian btn-ulang" id="btn-ulang-<?=$row['id_loket'];?>" data-id-antrian="" disabled>Ulang</button>
                <button class="btn btn-sm btn-info mt-2 btn-antrian btn-dilayani" id="btn-dilayani-<?=$row['id_loket'];?>" data-id-antrian="" disabled>Dilayani</button>
                <button class="btn btn-sm btn-warning mt-2 btn-antrian btn-selesai" id="btn-selesai-<?=$row['id_loket'];?>" data-id-antrian="" disabled>Selesai</button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Tabel Daftar Antrian Hari Ini -->
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="text-uppercase text-center">Daftar Antrian Booking</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="daftar-antrian-booking" class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No. Antrian</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th>Waktu Buat</th>
                            </tr>
                        </thead>
                        <tbody >
                            <!-- Data antrian akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4 class="text-uppercase text-center">Daftar Antrian Non Booking</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="daftar-antrian-non-booking" class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No. Antrian</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th>Waktu Buat</th>
                            </tr>
                        </thead>
                        <tbody >
                            <!-- Data antrian akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let currentUtterance = null; // Variabel untuk menyimpan objek SpeechSynthesisUtterance yang sedang berjalan
    let isSpeaking = false; // Flag untuk menandai status pembicaraan

    // Fungsi untuk membaca teks dengan suara
    function speak(text) {
        if ('speechSynthesis' in window) {
            // Hentikan suara yang sedang berjalan (jika ada)
            if (currentUtterance) {
                window.speechSynthesis.cancel();
            }

            // Buat objek SpeechSynthesisUtterance baru
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID'; // Set bahasa ke Indonesia
            utterance.rate = 1; // Kecepatan bicara (1 = normal)
            utterance.pitch = 1; // Tinggi nada (1 = normal)

            // Event handler untuk saat pembicaraan selesai
            utterance.onend = function() {
                isSpeaking = false;
                currentUtterance = null;
            };

            // Event handler untuk saat pembicaraan dimulai
            utterance.onstart = function() {
                isSpeaking = true;
            };

            // Simpan objek utterance ke variabel global
            currentUtterance = utterance;

            // Mulai berbicara
            window.speechSynthesis.speak(utterance);
        } else {
            console.error('Browser tidak mendukung Web Speech API');
        }
    }

    // Fungsi untuk memanggil antrian
    function panggilAntrian(id_loket, id_antrian) {
        console.log(id_antrian);
        if (isSpeaking) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Sedang memanggil antrian lain, tunggu hingga selesai'
            });
            return;
        }

        $.ajax({
            url: '<?= base_url("AntrianController/update_status_antrian"); ?>',
            method: 'POST',
            data: {
                id_antrian: id_antrian,
                status: 'panggil',
                id_loket: id_loket
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var nomorAntrian = $('#nomor_antrian_' + id_loket).text();
                    var jenisLayanan = $('#jenis_layanan_' + id_loket).text();

                    var textToSpeak = `Nomor antrian ${nomorAntrian}, silakan menuju loket ${jenisLayanan}.`;
                    speak(textToSpeak);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Antrian berhasil dipanggil',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Update display setelah berhasil mengupdate status
                    updateAntrianDisplay();
                    reloadDataTables();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memanggil antrian: ' + response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat memanggil antrian.'
                });
            }
        });
    }

    // Fungsi untuk mengulang panggilan antrian
    function ulangPanggilan(id_loket) {
        if (isSpeaking) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Sedang memanggil antrian lain, tunggu hingga selesai'
            });
            return;
        }

        var nomorAntrian = $('#nomor_antrian_' + id_loket).text();
        var jenisLayanan = $('#jenis_layanan_' + id_loket).text();
        var textToSpeak = `Nomor antrian ${nomorAntrian}, silakan menuju loket ${jenisLayanan}.`;
        speak(textToSpeak);
    }

    // Fungsi untuk mengupdate status antrian
    function updateStatusAntrian(id_loket, id_antrian, status) {
        const $card = $(this);
        $.ajax({
            url: '<?= base_url("AntrianController/update_status_antrian"); ?>',
            method: 'POST',
            data: {
                id_loket: id_loket,
                id_antrian: id_antrian,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Status antrian berhasil diupdate',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $card.find('.btn-antrian').each(function() {
                        $(this).removeAttr('data-id-antrian');
                    });
                    // Update display setelah berhasil mengupdate status
                    updateAntrianDisplay();
                    reloadDataTables();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengupdate status antrian: ' + response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat mengupdate status antrian.'
                });
            }
        });
    }

    // Fungsi untuk mendapatkan warna badge berdasarkan status
    function getBadgeColor(status) {
        const statusColors = {
            'buat': 'secondary',
            'panggil': 'success',
            'proses': 'info',
            'selesai': 'warning',
            'batal': 'danger'
        };
        return statusColors[status] || 'secondary';
    }

    // Inisialisasi DataTables
    let tableBooking, tableNonBooking;

    function initDataTables() {
        tableBooking = $('#daftar-antrian-booking').DataTable({
            ajax: {
                url: '<?= base_url("AntrianController/get_daftar_antrian_booking"); ?>',
                type: 'GET',
                dataSrc: ''
            },
            columns: [
                { data: 'no_antrian', title: 'No. Antrian' },
                { data: 'nama_layanan', title: 'Layanan' },
                { 
                    data: 'status', 
                    title: 'Status',
                    render: function(data) {
                        const badgeColor = getBadgeColor(data);
                        return `<span class="badge badge-${badgeColor}">${data}</span>`;
                    }
                },
                { 
                    data: 'waktu_booking', 
                    title: 'Waktu Booking'
                }
            ],
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });

        tableNonBooking = $('#daftar-antrian-non-booking').DataTable({
            ajax: {
                url: '<?= base_url("AntrianController/get_daftar_antrian_non_booking"); ?>',
                type: 'GET',
                dataSrc: ''
            },
            columns: [
                { data: 'no_antrian', title: 'No. Antrian' },
                { data: 'nama_layanan', title: 'Layanan' },
                { 
                    data: 'status', 
                    title: 'Status',
                    render: function(data) {
                        const badgeColor = getBadgeColor(data);
                        return `<span class="badge badge-${badgeColor}">${data}</span>`;
                    }
                },
                { 
                    data: 'waktu_buat', 
                    title: 'Waktu Buat'
                }
            ],
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    }

    // Fungsi untuk memperbarui tampilan antrian
    function updateAntrianDisplay() {
        $('.custom-card').each(function() {
            const $card = $(this);
            const id_loket = $card.data('id-loket');
            const jenis_loket = $card.data('jenis-loket');
            const id_layanan = $card.data('id-layanan');
            const $display = $card.find('.display-4');
            const $status_antrian = $card.find('#status_antrian');
            
            const $btnBatal = $card.find('.btn-batal');
            const $btnPanggil = $card.find('.btn-panggil');
            const $btnUlang = $card.find('.btn-ulang');
            const $btnDilayani = $card.find('.btn-dilayani');
            const $btnSelesai = $card.find('.btn-selesai');
            
            $.ajax({
                url: '<?= base_url("AntrianController/get_antrian");?>',
                method: 'POST',
                data: { 
                    id_layanan: id_layanan, 
                    id_loket: id_loket,
                    jenis_loket: jenis_loket 
                },
                dataType: 'json',
                success: function(data) {
                    if (data && data.length > 0 && data[0].no_antrian !== undefined) {
                        const nomor_antrian = data.map(item => item.no_antrian).join(', ');
                        const status_antrian = data.map(item => item.status_antrian);
                        const id_antrians = data.map(item => item.id);

                        $card.find('.btn-antrian').each(function() {
                            $(this).attr('data-id-antrian', id_antrians[0]);
                        });
                        
                        let sa, badge_color;
                        if (status_antrian.every(status => status === 'buat')) {
                            sa = 'menunggu';
                            badge_color = 'badge-secondary';
                            $btnPanggil.prop('disabled', false);
                            $btnUlang.prop('disabled', false);
                            $btnBatal.prop('disabled', false);
                            $btnDilayani.prop('disabled', true);
                            $btnSelesai.prop('disabled', true);
                        } else if (status_antrian.some(status => status === 'panggil')) {
                            sa = 'Sedang Memanggil';
                            badge_color = 'badge-success';
                            $btnPanggil.prop('disabled', true);
                            $btnUlang.prop('disabled', false);
                            $btnBatal.prop('disabled', false);
                            $btnDilayani.prop('disabled', false);
                            $btnSelesai.prop('disabled', true);
                        } else if (status_antrian.some(status => status === 'proses')) {
                            sa = 'Sedang Melayani';
                            badge_color = 'badge-info';
                            $btnPanggil.prop('disabled', true);
                            $btnUlang.prop('disabled', true);
                            $btnBatal.prop('disabled', false);
                            $btnDilayani.prop('disabled', true);
                            $btnSelesai.prop('disabled', false);
                        } else if (status_antrian.some(status => status === 'selesai')) {
                            sa = 'Antrian Selesai';
                            badge_color = 'badge-warning';
                            $btnPanggil.prop('disabled', true);
                            $btnUlang.prop('disabled', false);
                            $btnBatal.prop('disabled', true);
                            $btnDilayani.prop('disabled', true);
                            $btnSelesai.prop('disabled', true);
                        } else {
                            sa = 'Antrian dibatalkan';
                            badge_color = 'badge-danger';
                            $card.find('.btn-antrian').prop('disabled', true);
                        }

                        $display.text(nomor_antrian);
                        $status_antrian.text(sa).removeClass().addClass('badge ' + badge_color);
                    } else {
                        $display.text('0');
                        $status_antrian.text('Tidak ada antrian').removeClass().addClass('badge badge-danger');
                        // PERBAIKAN: Hapus data-id-antrian dari semua tombol di card ini
                        $card.find('.btn-antrian').each(function() {
                            $(this).removeAttr('data-id-antrian');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });
    }

    // Fungsi untuk reload DataTables
    function reloadDataTables() {
        if (tableBooking) {
            tableBooking.ajax.reload(null, false);
        }
        if (tableNonBooking) {
            tableNonBooking.ajax.reload(null, false);
        }
    }

    
    // Inisialisasi DataTables saat pertama kali load
    initDataTables();
    
    // Update display saat pertama kali load
    updateAntrianDisplay();

    // Event delegation untuk tombol
    $(document).on('click', '[id^="btn-"]', function() {

        const $btn = $(this);
        const id_loket = $btn.closest('.card').data('id-loket');
        const id_antrian = $btn.data('id-antrian');
        const action = $btn.text().trim().toLowerCase();

        
        const actions = {
            'panggil': () => panggilAntrian(id_loket, id_antrian),
            'dilayani': () => updateStatusAntrian(id_loket, id_antrian, 'proses'),
            'selesai': () => updateStatusAntrian(id_loket, id_antrian, 'selesai'),
            'batal': () => updateStatusAntrian(id_loket, id_antrian, 'batal'),
            'ulang': () => ulangPanggilan(id_loket)
        };

        if (actions[action]) {
            actions[action]();
            
        }
    });
});

</script>