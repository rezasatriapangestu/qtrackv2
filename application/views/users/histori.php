<style>
.user-histori-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
    margin-bottom: 32px;
}
.user-histori-card .card-header {
    border-radius: 28px 28px 0 0;
    background: linear-gradient(90deg, #e3f0ff 0%, #f8faf8 100%);
    color: #222;
    padding: 24px 28px;
    border: none;
    font-weight: 700;
    font-size: 1.35rem;
    letter-spacing: 1px;
    box-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
    display: flex;
    align-items: center;
}
.user-histori-card .card-header .judul-halaman {
    font-weight: 700;
    color: #222;
    font-size: 1.35rem;
    letter-spacing: 1px;
}
.user-histori-card .card-body {
    padding: 24px 18px;
}
.user-histori-card .table {
    background: #fff;
    margin-bottom: 0;
}
.user-histori-card .table th {
    background: #f1f8e9;
    color: #2E7D32;
    font-weight: 600;
    border-top: none;
    border-bottom: 2px solid #e0e0e0;
    font-size: 1rem;
}
.user-histori-card .table td {
    vertical-align: middle;
    font-size: 1rem;
}
</style>
<div class="container">
  <div class="card user-histori-card">
      <div class="card-header">
          <span class="judul-halaman">Histori Antrian Booking</span>
      </div>
      <div class="card-body">
          <div class="table-responsive">
              <table id="historiAntrianTable" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                      <tr>
                          <th>NO</th>
                          <th>No. Antrian</th>
                          <th>Layanan</th>
                          <th>Tanggal</th>
                          <th>Waktu Booking</th>
                          <th>Status</th>
                      </tr>
                  </thead>
                  <tbody>
                        <?php 
                        $no = 1;
                        foreach ($data_histori as $h) : ?>
                            <tr>
                                <td><?= $no++;?></td>
                                <td><?= $h['no_antrian'];?></td>
                                <td><?= $h['nama_layanan'];?></td>
                                <td><?= date('d-m-Y',strtotime($h['waktu_buat']));?></td>
                                <td><?= $h['waktu_booking'];?></td>
                                <td>
                                    <?php
                                    // Determine status and badge color based on $h['status_antrian']
                                    if ($h['status_antrian'] == 'buat') {
                                        $sa = 'Menunggu Dipanggil';
                                        $badge_color = 'badge-secondary';
                                    } elseif ($h['status_antrian'] == 'panggil') {
                                        $sa = 'Sedang Dipanggil';
                                        $badge_color = 'badge-primary';
                                    } elseif ($h['status_antrian'] == 'proses') {
                                        $sa = 'Sedang Dilayani';
                                        $badge_color = 'badge-info';
                                    } elseif ($h['status_antrian'] == 'selesai') {
                                        $sa = 'Selesai';
                                        $badge_color = 'badge-success';
                                    } elseif ($h['status_antrian'] == 'batal') {
                                        $sa = 'Dibatalkan';
                                        $badge_color = 'badge-danger';
                                    } else {
                                        $sa = 'Status Tidak Dikenali';
                                        $badge_color = 'badge-dark';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_color; ?>"><?php echo $sa; ?></span>
                                </td>
                            </tr>
                        <?php endforeach;?>
                  </tbody>
              </table>
          </div>
      </div>
  </div>
</div>