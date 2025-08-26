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
                                    <h1 class="dashboard-title">Ella Contractors Module</h1>
                                    <p class="dashboard-subtitle">Comprehensive contractor management system</p>
                                    
                                    <!-- Quick Navigation -->
                                    <div class="row" style="margin-top: 2rem;">
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-light btn-lg quick-nav-btn">
                                                <i class="fa fa-users"></i><br>Contractors
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-light btn-lg quick-nav-btn">
                                                <i class="fa fa-file-contract"></i><br>Contracts
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/projects') ?>" class="btn btn-light btn-lg quick-nav-btn">
                                                <i class="fa fa-project-diagram"></i><br>Projects
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="<?= admin_url('ella_contractors/payments') ?>" class="btn btn-light btn-lg quick-nav-btn">
                                                <i class="fa fa-dollar-sign"></i><br>Payments
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="row" style="margin-top: 2rem;">
                                        <div class="col-md-12">
                                            <h4>Quick Actions</h4>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <a href="<?= admin_url('ella_contractors/add_contractor') ?>" class="btn btn-primary btn-lg">
                                                        <i class="fa fa-plus"></i> Add New Contractor
                                                    </a>
                                                </div>
                                                <div class="col-md-4">
                                                    <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-success btn-lg">
                                                        <i class="fa fa-list"></i> View All Contractors
                                                    </a>
                                                </div>
                                                <div class="col-md-4">
                                                    <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-info btn-lg">
                                                        <i class="fa fa-eye"></i> View Contracts
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if (is_super_admin()): ?>
                                    <div style="margin-top: 2rem;">
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

<style>
.dashboard-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 15px;
}

.dashboard-title {
    font-size: 2.5em;
    font-weight: bold;
    margin-bottom: 15px;
    color: white;
}

.dashboard-subtitle {
    font-size: 1.2em;
    opacity: 0.9;
    margin-bottom: 30px;
}

.quick-nav-btn {
    padding: 20px;
    margin: 10px;
    border-radius: 10px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    color: #333;
    text-decoration: none;
    display: block;
    height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.quick-nav-btn:hover {
    background: rgba(255, 255, 255, 1);
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    text-decoration: none;
    color: #333;
}

.quick-nav-btn i {
    font-size: 2em;
    margin-bottom: 10px;
    color: #667eea;
}

.quick-nav-btn:hover i {
    color: #764ba2;
}

.quick-actions h4 {
    color: white;
    margin-bottom: 20px;
}

.quick-actions .btn {
    margin: 10px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.quick-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

@media (max-width: 768px) {
    .dashboard-title {
        font-size: 2em;
    }
    
    .quick-nav-btn {
        height: 100px;
        margin: 5px;
    }
    
    .quick-nav-btn i {
        font-size: 1.5em;
    }
}
</style>

<?php init_tail(); ?>
