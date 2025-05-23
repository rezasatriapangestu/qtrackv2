<style>
.layanan-table-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
}
.layanan-table-card .card-header {
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
.layanan-table-card .card-body {
    padding: 24px 18px;
}
.layanan-table-card .table {
    background: #fff;
    margin-bottom: 0;
}
.layanan-table-card .table th {
    background: #f1f8e9;
    color: #2E7D32;
    font-weight: 600;
    border-top: none;
    border-bottom: 2px solid #e0e0e0;
    font-size: 1rem;
}
.layanan-table-card .table td {
    vertical-align: middle;
    font-size: 1rem;
}
.layanan-table-card .btn-success {
    border-radius: 12px;
    font-weight: 500;
    background: #2E7D32;
    border: none;
}
.layanan-table-card .btn-success:hover {
    background: #388e3c;
}
.layanan-table-card .btn-warning {
    border-radius: 12px;
    font-weight: 500;
}
.layanan-table-card .btn-danger {
    border-radius: 12px;
    font-weight: 500;
}
@media (max-width: 768px) {
    .layanan-table-card .card-header {
        font-size: 1rem;
        padding: 14px 10px;
    }
    .layanan-table-card .card-body {
        padding: 12px 4px;
    }
}
</style>

<div class="card shadow mb-4 layanan-table-card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span class="fs-5 fw-bold">Manajemen Layanan</span>
        <a href="<?=base_url('LayananController/tambah');?>" class="btn btn-sm btn-success" id="btn-tambah">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 5px;" class="text-center">NO</th>
                        <th class="text-center">KODE LAYANAN</th>
                        <th class="text-center">NAMA LAYANAN</th>
                        <th style="width: 20%;" class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no=1;
                    foreach ($data_layanan as $row) :
                    ?>
                    <tr>
                        <td class="text-center"><?=$no++;?></td>
                        <td><?= $row['kode'];?></td>
                        <td><?= $row['nama'];?></td>
                        <td class="text-center">
                            <a href="<?= base_url('LayananController/edit/').$row['id_layanan'];?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>  
                            <a href="<?= base_url('LayananController/hapus/').$row['id_layanan'];?>" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>  
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>