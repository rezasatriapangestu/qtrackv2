<style>
.user-table-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
}
.user-table-card .card-header {
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
.user-table-card .card-body {
    padding: 24px 18px;
}
.user-table-card .table {
    background: #fff;
    margin-bottom: 0;
}
.user-table-card .table th {
    background: #f1f8e9;
    color: #2E7D32;
    font-weight: 600;
    border-top: none;
    border-bottom: 2px solid #e0e0e0;
    font-size: 1rem;
}
.user-table-card .table td {
    vertical-align: middle;
    font-size: 1rem;
}
.user-table-card .btn-success {
    border-radius: 12px;
    font-weight: 500;
    background: #2E7D32;
    border: none;
}
.user-table-card .btn-success:hover {
    background: #388e3c;
}
.user-table-card .btn-warning {
    border-radius: 12px;
    font-weight: 500;
}
.user-table-card .btn-danger {
    border-radius: 12px;
    font-weight: 500;
}
.user-table-card .btn-info {
    border-radius: 12px;
    font-weight: 500;
}
@media (max-width: 768px) {
    .user-table-card .card-header {
        font-size: 1rem;
        padding: 14px 10px;
    }
    .user-table-card .card-body {
        padding: 12px 4px;
    }
}
</style>

<div class="card shadow mb-4 user-table-card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span class="fs-5 fw-bold">Manajemen User</span>
        <a href="<?=base_url('UserManagementController/tambah');?>" class="btn btn-sm btn-success" id="btn-tambah">
            <i class="fas fa-plus"></i> Tambah
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">ID USER</th>
                        <th class="text-center">NAMA LENGKAP</th>
                        <th class="text-center">EMAIL</th>
                        <th class="text-center">ROLE</th>
                        <th class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no=1;
                    foreach ($data_users as $row) :
                    ?>
                    <tr>
                        <td class="text-center"><?=$no++;?></td>
                        <td><?= $row['id_user'];?></td>
                        <td><?= $row['nama_lengkap'];?></td>
                        <td><?= $row['email'];?></td>
                        <td><?= $row['role'];?></td>
                        <td class="text-center">
                            <a href="<?= base_url('UserManagementController/edit/').$row['id_user'];?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>  
                            <a href="<?= base_url('UserManagementController/detail/').$row['id_user'];?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>  
                            <a href="<?= base_url('UserManagementController/hapus/').$row['id_user'];?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>  
                        </td>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>