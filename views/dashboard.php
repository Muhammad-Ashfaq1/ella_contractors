<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
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
                                <div class="well" style="padding: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                                    <h1 style="font-size: 2.5rem; font-weight: 300; margin-bottom: 1rem;">Hello from Contractor Module</h1>
                                    <p style="font-size: 1.2rem; opacity: 0.9; margin-bottom: 2rem;">Welcome to the Ella Contractors Module Dashboard</p>
                                    
                                    <!-- Quick Navigation -->
                                    <div class="row" style="margin-top: 2rem;">
                                        <div class="col-md-6" style="margin-bottom: 1rem;">
                                            <a href="<?= admin_url('ella_contractors/contractors') ?>" class="btn btn-light btn-lg" style="width: 100%; padding: 20px;">
                                                <i class="fa fa-users"></i><br>Contractors
                                            </a>
                                        </div>
                                        <div class="col-md-6" style="margin-bottom: 1rem;">
                                            <a href="<?= admin_url('ella_contractors/contracts') ?>" class="btn btn-light btn-lg" style="width: 100%; padding: 20px;">
                                                <i class="fa fa-file-contract"></i><br>Contracts
                                            </a>
                                        </div>
                                        <div class="col-md-6" style="margin-bottom: 1rem;">
                                            <a href="<?= admin_url('ella_contractors/projects') ?>" class="btn btn-light btn-lg" style="width: 100%; padding: 20px;">
                                                <i class="fa fa-project-diagram"></i><br>Projects
                                            </a>
                                        </div>
                                        <div class="col-md-6" style="margin-bottom: 1rem;">
                                            <a href="<?= admin_url('ella_contractors/payments') ?>" class="btn btn-light btn-lg" style="width: 100%; padding: 20px;">
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
</div>
<?php init_tail(); ?>
