<!-- Tambahkan style untuk mempercantik tampilan -->
<style>
.loket-table-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
}
.loket-table-card .card-header {
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
.loket-table-card .card-body {
    padding: 24px 18px;
}
.loket-table-card .table {
    background: #fff;
    margin-bottom: 0;
}
.loket-table-card .table th {
    background: #f1f8e9;
    color: #2E7D32;
    font-weight: 600;
    border-top: none;
    border-bottom: 2px solid #e0e0e0;
    font-size: 1rem;
}
.loket-table-card .table td {
    vertical-align: middle;
    font-size: 1rem;
}
.loket-table-card .badge-success {
    background: #43a047;
    color: #fff;
    font-weight: 500;
    letter-spacing: 1px;
    border-radius: 12px;
    padding: 6px 16px;
}
.loket-table-card .badge-danger {
    background: #e53935;
    color: #fff;
    font-weight: 500;
    letter-spacing: 1px;
    border-radius: 12px;
    padding: 6px 16px;
}
.loket-table-card .btn-success {
    border-radius: 12px;
    font-weight: 500;
    background: #2E7D32;
    border: none;
}
.loket-table-card .btn-success:hover {
    background: #388e3c;
}
.loket-table-card .btn-warning {
    border-radius: 12px;
    font-weight: 500;
}
.loket-table-card .btn-danger {
    border-radius: 12px;
    font-weight: 500;
}
@media (max-width: 768px) {
    .loket-table-card .card-header {
        font-size: 1rem;
        padding: 14px 10px;
    }
    .loket-table-card .card-body {
        padding: 12px 4px;
    }
}
</style>

<div class="card shadow mb-4 loket-table-card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span class="fs-5 fw-bold">Manajemen Loket</span>
        <a href="<?=base_url('LoketController/tambah');?>" class="btn btn-sm btn-success" id="btn-tambah">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5px;" class="text-center">NO</th>
                        <th class="text-center">NAMA LOKET</th>
                        <th class="text-center">NAMA LAYANAN</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">JENIS</th>
                        <th style="width: 20%;" class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no=1;
                    foreach ($data_loket as $row) :
                    ?>
                    <tr>
                        <td class="text-center"><?=$no++;?></td>
                        <td><?= $row['nama'];?></td>
                        <td><?= $row['nama_layanan'];?></td>
                        <td>
                            <?= ($row['status'] == '1') 
                                ? '<span class="badge badge-success">Aktif</span>' 
                                : '<span class="badge badge-danger">Tidak Aktif</span>';?>
                        </td>
                        <td>
                            <?= ($row['jenis'] == '1') 
                                ? '<span class="badge badge-success">Booking</span>' 
                                : '<span class="badge badge-danger">Non Booking</span>';?>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('LoketController/edit/').$row['id_loket'];?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>  
                            <a href="<?= base_url('LoketController/hapus/').$row['id_loket'];?>" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>  
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>