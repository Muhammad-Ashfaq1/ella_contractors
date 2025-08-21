<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<script>
// Ensure jQuery is loaded before any CSRF setup
if (typeof jQuery === 'undefined') {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"><\/script>');
}

// Override the problematic CSRF function to prevent errors
window.csrf_jquery_ajax_setup = function() {
    // Do nothing - prevent the error from general_helper.php
    return false;
};
</script>

<?php init_head(); ?>

<!-- Include module CSS -->
<link rel="stylesheet" href="<?php echo base_url('modules/ella_contractors/assets/css/ella_contractors.css'); ?>">

    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        
                        <!-- Page Header -->
                        <div class="row">
                        <div class="col-md-12">
                                <h4 class="customer-profile-group-heading"><?= $title ?></h4>
                                <hr class="hr-panel-heading" />
                                        </div>
                                    </div>

                        <!-- Dashboard Content -->
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="well dashboard-gradient" style="padding: 60px;">
                                    <h1 class="dashboard-title">Hello from Contractor Module</h1>
                                    <p class="dashboard-subtitle">Welcome to the Ella Contractors Module Dashboard</p>
                                    
                                    <!-- Quick Navigation -->
                                    <div class="row" style="margin-top: 2rem;">
                                        <div class="col-md-6">
                                            <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-light btn-lg quick-nav-btn">
                                                <i class="fa fa-users"></i><br>Contractors
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-light btn-lg quick-nav-btn">
                                                <i class="fa fa-file-contract"></i><br>Contracts
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="<?= admin_url('ella_contractors/projects') ?>" class="btn btn-light btn-lg quick-nav-btn">
                                                <i class="fa fa-project-diagram"></i><br>Projects
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="<?= admin_url('ella_contractors/payments') ?>" class="btn btn-light btn-lg quick-nav-btn">
                                                <i class="fa fa-dollar-sign"></i><br>Payments
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <?php if (is_super_admin()): ?>
                                    <div style="margin-top: 1rem;">
                                        <a href="<?= admin_url('ella_contractors/activate') ?>" class="btn btn-warning">
                                            <i class="fa fa-database"></i> Activate Module
                                        </a>
                                        <a href="<?= admin_url('ella_contractors/settings') ?>" class="btn btn-outline-light">
                                            <i class="fa fa-cog"></i> Settings
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php init_tail(); ?>
