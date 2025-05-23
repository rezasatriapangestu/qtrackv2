

<!-- Start Header -->
<?php $this->load->view('admin/template/header');?>
<!-- End of Header -->



<!-- Start Sidebar -->
<?php $this->load->view('admin/template/sidebar');?>
<!-- End of Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
  
        <!-- Start Topbar -->
        <?php $this->load->view('admin/template/topbar');?>
        <!-- End of Topbar -->

        <div class="container-fluid">
          <div class="card mb-1">
            <div class="card-body">
              <!-- Baris untuk judul dan breadcrumb -->
                <div class="row align-items-center">
                  <h4 class="text-title mb-0"><?= $title;?></h4> <!-- Judul -->
                </div>
            </div>
          </div>
        <?php $this->load->view($conten);?>
        </div>

    </div>
    <!-- End of Main Content -->

<!-- Footer -->
<?php $this->load->view('admin/template/footer');?>
<!-- End of Footer -->