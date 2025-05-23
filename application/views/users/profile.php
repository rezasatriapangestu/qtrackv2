<style>
.profile-card {
    border-radius: 28px;
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.10);
    background: #fff;
    overflow: hidden;
    border: none;
    margin-bottom: 32px;
}
.profile-card .card-header {
    border-radius: 28px 28px 0 0;
    background: linear-gradient(90deg, #e3f0ff 0%, #f8faf8 100%);
    color: #2E7D32;
    padding: 24px 28px 18px 28px;
    border: none;
    font-weight: 700;
    font-size: 1.35rem;
    letter-spacing: 1px;
    box-shadow: 0 2px 8px rgba(46, 125, 50, 0.08);
    text-align: center;
}
.profile-card .card-header h4 {
    font-weight: 700;
    color: #2E7D32;
    margin-bottom: 0;
    font-size: 1.35rem;
    letter-spacing: 1px;
}
.profile-card .card-body {
    padding: 32px 24px;
}
.profile-avatar {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid #2E7D32;
    background: #f1f8e9;
    box-shadow: 0 2px 8px rgba(46, 125, 50, 0.10);
}
.profile-table th {
    width: 40%;
    color: #2E7D32;
    font-weight: 600;
    background: none !important;
    border: none !important;
    font-size: 1.05rem;
}
.profile-table td {
    color: #333;
    background: none !important;
    border: none !important;
    font-size: 1.05rem;
}
@media (max-width: 768px) {
    .profile-card .card-body {
        padding: 18px 8px;
    }
    .profile-card .card-header {
        padding: 16px 10px 10px 10px;
        font-size: 1.1rem;
    }
}
</style>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card profile-card">
                <div class="card-header">
                    <h4 class="mb-0">Profil Pengguna</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <?php
                        $foto = !empty($user['foto_profile']) && file_exists(FCPATH . 'assets/uploads/foto_profile/' . $user['foto_profile'])
                            ? base_url('assets/uploads/foto_profile/' . htmlspecialchars($user['foto_profile']))
                            : base_url('assets/img/default-profile.png');
                        ?>
                        <img src="<?= $foto; ?>" alt="Foto Profile" class="profile-avatar">
                    </div>
                    <table class="table table-borderless profile-table">
                        <tr>
                            <th>User ID</th>
                            <td><?= htmlspecialchars($user['id_user']); ?></td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td><?= htmlspecialchars($user['nama_lengkap']); ?></td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td><?= htmlspecialchars($user['no_tlp']); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td><?= htmlspecialchars($user['role']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
