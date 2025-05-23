<!-- Custom Sidebar Styles -->
<style>
    .sidebar {
        background: #fff !important;
        color: #222 !important;
        padding: 1rem 0.5rem;
        overflow-x: hidden;
        width: 250px;
        min-width: 250px;
        max-width: 100vw;
    }
    .sidebar .sidebar-brand {
        padding: 1.25rem 0.5rem 1.25rem 0.5rem !important;
        margin-bottom: 1rem;
        border-bottom: 1px solid #e0e0e0;
        background: #f8f9fa;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(40,167,69,0.07);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 110px; /* Tambahkan tinggi minimum agar muat logo & nama web */
    }
    .sidebar .sidebar-brand img {
        height: 60px;
        width: auto;
        margin-bottom: 0.5rem;
        display: block;
        max-width: 80%;
        object-fit: contain;
    }
    .sidebar .sidebar-brand-text {
        font-size: 0.8rem;
        font-weight: 700;
        color: #28a745 !important;
        text-align: center;
        letter-spacing: 0.5px;
        line-height: 1.2;
        text-shadow: 0 2px 8px rgba(40,167,69,0.08);
        max-width: 90%;
        word-break: break-word;
        white-space: normal;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
        min-height: 2.5em;
    }
    .sidebar .nav-link,
    .sidebar .sidebar-heading {
        color: #222 !important;
        font-size: 0.9rem; /* sedikit lebih besar */
    }
    .sidebar .nav-link {
        padding: 0.5rem 1.25rem;
        display: flex;
        align-items: center;
        width: 100%;
        box-sizing: border-box;
        border-radius: 0.75rem;
        min-width: 0;
        max-width: 100%;
    }
    .sidebar .nav-link i {
        font-size: 1.35rem; /* sedikit lebih besar */
        margin-right: 0.75rem;
        min-width: 1.5em;
        text-align: center;
        flex-shrink: 0;
        color: #222 !important;
        transition: color 0.2s;
    }
    .sidebar .nav-link span {
        font-size: 1.08rem; /* sedikit lebih besar */
        flex: 1 1 0%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sidebar .nav-link.active,
    .sidebar .nav-item.active > .nav-link {
        background: #28a745 !important;
        color: #fff !important;
        border-radius: 0.75rem !important;
    }
    .sidebar .nav-link.active i,
    .sidebar .nav-item.active > .nav-link i {
        color: #fff !important;
    }
    .sidebar .nav-link.active span,
    .sidebar .nav-item.active > .nav-link span {
        color: #fff !important;
    }
    .sidebar .nav-link:hover {
        background: #e6f4ea !important;
        color: #222 !important;
        border-radius: 0.75rem !important;
    }
    .sidebar .nav-link:hover i,
    .sidebar .nav-link:hover span {
        color: #222 !important;
    }
    .sidebar .sidebar-divider {
        border-color: #e0e0e0;
    }
</style>
<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand" href="index.html">
    <img src="<?= base_url('assets/img/qtrack-horizontal.png'); ?>" alt="QTrack Logo">
    <div class="sidebar-brand-text">
        <?php echo get_web_info('nama_web'); ?>
    </div>
</a>

<!-- Divider -->
<hr class="sidebar-divider my-0">

<!-- Nav Item - Home -->
<li class="nav-item <?= $this->uri->segment(1) == 'HomeController' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= base_url('HomeController');?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
</li>

<!-- Nav Item - Home -->
<li class="nav-item <?= $this->uri->segment(1) == 'AntrianController' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= base_url('AntrianController');?>">
        <i class="fas fa-microphone"></i>
        <span>Antrian</span>
    </a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<?php if($this->session->userdata('roles') === 'admin') : ?>
<!-- Heading -->
<div class="sidebar-heading">
    Management Sistem :
</div>

<!-- Nav Item - Home -->
<li class="nav-item <?= $this->uri->segment(1) == 'LoketController' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= base_url('LoketController');?>">
        <i class="fas fa-desktop"></i>
        <span>Loket</span>
    </a>
</li>

<!-- Nav Item - Home -->
<li class="nav-item <?= $this->uri->segment(1) == 'LayananController' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= base_url('LayananController');?>">
        <i class="fas fa-wrench"></i>
        <span>Layanan</span>
    </a>
</li>

<!-- Nav Item - Home -->
<li class="nav-item <?= $this->uri->segment(1) == 'BookingController' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= base_url('BookingController');?>">
        <i class="fas fa-clock"></i>
        <span>Booking</span>
    </a>
</li>

<!-- Nav Item - Home -->
<li class="nav-item <?= $this->uri->segment(1) == 'UserManagementController' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= base_url('UserManagementController');?>">
        <i class="fas fa-users"></i>
        <span>Users</span>
    </a>
</li>
<!-- Nav Item - Home -->
<li class="nav-item <?= $this->uri->segment(1) == 'WebController' ? 'active' : '' ?>">
    <a class="nav-link" href="<?= base_url('WebController');?>">
        <i class="fas fa-wrench"></i>
        <span>Web Setting</span>
    </a>
</li>
<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">
<?php endif;?>

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

</ul>