<footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>
                        <?php echo get_web_info('footer'); ?> | Designed by <a href="#" style="color:rgb(255, 191, 0);"><?php echo get_web_info('create_by'); ?></a>
                        </span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin anda keluar sistem ?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Silahkan tekan tombol logout, untuk keluar sistem.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="<?= base_url('AuthController/logout');?>">Logout</a>
                </div>
            </div>
        </div>
    </div>

   <script>
        function showSweetAlert(icon, title, redirectUrl = null) {
            Swal.fire({
                icon: icon, // 'success', 'error', 'warning', 'info', 'question'
                title: icon,
                text: title,
                showConfirmButton: false, // Disable the OK button
                backdrop: 'rgba(0,0,0,0.5)', // Semi-transparent background
                timer: 3000, // Timer (3 seconds)
                timerProgressBar: true, // Show progress bar
                allowOutsideClick: false, // Can't close by clicking outside
                allowEscapeKey: false, // Can't close with ESC key
                customClass: {
                    popup: 'animated bounceIn', // Entrance animation (bounceIn)
                    title: 'swal2-title-custom', // Custom class for title
                    content: 'swal2-content-custom', // Custom class for content
                }
            }).then(() => {
                if (redirectUrl) {
                    // Redirect to specified page if redirectUrl is provided
                    window.location.href = redirectUrl;
                }
            });
        }
   </script>
   <?php if ($this->session->flashdata('swal')): ?>
        <script>
            Swal.fire({
                icon: '<?= $this->session->flashdata('swal')['icon'] ?>',
                title: '<?= $this->session->flashdata('swal')['title'] ?>',
                text: '<?= $this->session->flashdata('swal')['text'] ?>',
                showConfirmButton: false, // Disable the OK button
                backdrop: 'rgba(0,0,0,0.5)', // Semi-transparent background
                timer: 2000, // Timer (3 seconds)
                timerProgressBar: true, // Show progress bar
                allowOutsideClick: false, // Can't close by clicking outside
                allowEscapeKey: false, // Can't close with ESC key
                customClass: {
                    popup: 'animated bounceIn', // Entrance animation (bounceIn)
                    title: 'swal2-title-custom', // Custom class for title
                    content: 'swal2-content-custom', // Custom class for content
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>