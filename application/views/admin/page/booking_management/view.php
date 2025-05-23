<style>
.booking-table-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
}
.booking-table-card .card-header {
    border-radius: 28px 28px 0 0;
    background: linear-gradient(90deg, #2E7D32 60%, #43a047 100%);
    color: #fff;
    padding: 20px 24px;
    border: none;
    font-weight: 600;
    font-size: 1.2rem;
    letter-spacing: 1px;
    box-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
}
.booking-table-card .card-body {
    padding: 24px 18px;
}
.booking-table-card .table {
    background: #fff;
    margin-bottom: 0;
}
.booking-table-card .table th {
    background: #f1f8e9;
    color: #2E7D32;
    font-weight: 600;
    border-top: none;
    border-bottom: 2px solid #e0e0e0;
    font-size: 1rem;
}
.booking-table-card .table td {
    vertical-align: middle;
    font-size: 1rem;
}
.booking-table-card .btn-success {
    border-radius: 12px;
    font-weight: 500;
    background: #2E7D32;
    border: none;
}
.booking-table-card .btn-success:hover {
    background: #388e3c;
}
.booking-table-card .btn-warning {
    border-radius: 12px;
    font-weight: 500;
}
.booking-table-card .btn-danger {
    border-radius: 12px;
    font-weight: 500;
}
@media (max-width: 768px) {
    .booking-table-card .card-header {
        font-size: 1rem;
        padding: 14px 10px;
    }
    .booking-table-card .card-body {
        padding: 12px 4px;
    }
}
</style>

<div class="card shadow mb-4 booking-table-card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span class="fs-5 fw-bold">Manajemen Booking</span>
        <a href="<?=base_url('BookingController/tambah');?>" class="btn btn-sm btn-success" id="btn-tambah">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5px;" class="text-center">NO</th>
                        <th class="text-center">WAKTU AWAL</th>
                        <th class="text-center">WAKTU AKHIR</th>
                        <th class="text-center">MAKSIMAL ANTRIAN</th>
                        <th style="width: 20%;" class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no=1;
                    foreach ($data_waktu as $row) :
                    ?>
                    <tr>
                        <td class="text-center"><?=$no++;?></td>
                        <td><?= $row['waktu_awal'];?></td>
                        <td><?= $row['waktu_akhir'];?></td>
                        <td><?= $row['maks_antrian'];?></td>
                        <td class="text-center">
                            <a href="<?= base_url('BookingController/edit/').$row['id_waktu_booking'];?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>  
                            <a href="<?= base_url('BookingController/hapus/').$row['id_waktu_booking'];?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>  
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>