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

<style>
/* Fix sidebar overlap issues */
.content {
    margin-left: 250px !important;
    padding: 20px !important;
    min-height: calc(100vh - 60px);
}

@media (max-width: 768px) {
    .content {
        margin-left: 0 !important;
        padding: 15px !important;
    }
}

/* Ensure proper spacing for panels */
.panel_s {
    margin-bottom: 20px;
}

/* Page specific styling */
.well {
    margin-top: 20px;
}
</style>

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

                        <!-- Main Content -->
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="well simple-page-well">
                                    <h2 class="simple-page-title"><?= $message ?></h2>
                                    <a href="<?= admin_url('ella_contractors/dashboard') ?>" class="btn btn-light">
                                        ← BACK TO DASHBOARD
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php init_tail(); ?>
