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

                        <!-- Main Content -->
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <div class="well" style="padding: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                                    <h2 style="font-weight: 300; margin-bottom: 20px;"><?= $message ?></h2>
                                    <a href="<?= admin_url('ella_contractors') ?>" class="btn btn-light">
                                        <i class="fa fa-arrow-left"></i> Back to Dashboard
                                    </a>
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
