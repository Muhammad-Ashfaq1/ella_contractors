<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php if(has_permission('ella_contractors','','delete')){ ?>
             <a href="#" data-toggle="modal" data-table=".table-estimates" data-target="#estimates_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
             <div class="modal fade bulk_actions" id="estimates_bulk_actions" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
               <div class="modal-content">
                <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                 <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
               </div>
               <div class="modal-body">
                 <?php if(has_permission('ella_contractors','','delete')){ ?>
                   <div class="checkbox checkbox-danger">
                    <input type="checkbox" name="mass_delete" id="mass_delete">
                    <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                  </div>
                <?php } ?>
              </div>
              <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
               <a href="#" class="btn btn-info" onclick="estimates_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
             </div>
           </div>
         </div>
       </div>
     <?php } ?>
     <?php hooks()->do_action('before_estimates_page_content'); ?>
     <?php if(has_permission('ella_contractors','','create')){ ?>
       <div class="_buttons">
        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#estimate_modal"><?php echo _l('new_estimate'); ?></a>
      </div>
      <div class="clearfix"></div>
      <hr class="hr-panel-heading" />
    <?php } ?>
    <!-- Estimates Table -->
    <h5><?php echo _l('estimates'); ?></h5>
    <table class="table table-striped table-estimates" id="custom-estimates-table">
        <thead>
            <tr>
                <?php if(has_permission('ella_contractors','','delete')){ ?>
                <th width="50">
                    <div class="checkbox mass_select_all_wrap">
                        <input type="checkbox" id="mass_select_all" data-to-table="estimates">
                        <label></label>
                    </div>
                </th>
                <?php } ?>
                <th><?php echo _l('estimate_name'); ?></th>
                <th><?php echo _l('client'); ?></th>
                <th><?php echo _l('lead'); ?></th>
                <th><?php echo _l('status'); ?></th>
                <th><?php echo _l('line_items_count'); ?></th>
                <th><?php echo _l('total_quantity'); ?></th>
                <th><?php echo _l('total_amount'); ?></th>
                <th><?php echo _l('created_by'); ?></th>
                <th><?php echo _l('last_updated'); ?></th>
                <th><?php echo _l('options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($estimates as $estimate): ?>
            <tr>
                <?php if(has_permission('ella_contractors','','delete')){ ?>
                <td>
                    <div class="checkbox">
                        <input type="checkbox" value="<?= $estimate['id']; ?>">
                        <label></label>
                    </div>
                </td>
                <?php } ?>
                <td>
                    <a href="<?= admin_url('ella_contractors/view_estimate/' . $estimate['id']); ?>">
                        <?= htmlspecialchars($estimate['estimate_name']); ?>
                    </a>
                    <?php if($estimate['description']): ?>
                        <br><small class="text-muted"><?= htmlspecialchars(substr($estimate['description'], 0, 50)) . (strlen($estimate['description']) > 50 ? '...' : ''); ?></small>
                    <?php endif; ?>
                </td>
                <td><?= $estimate['client_name'] ? htmlspecialchars($estimate['client_name']) : '-'; ?></td>
                <td><?= $estimate['lead_name'] ? htmlspecialchars($estimate['lead_name']) : '-'; ?></td>
                <td>
                    <?php
                    $status_class = '';
                    switch($estimate['status']) {
                        case 'draft': $status_class = 'label-default'; break;
                        case 'sent': $status_class = 'label-info'; break;
                        case 'accepted': $status_class = 'label-success'; break;
                        case 'rejected': $status_class = 'label-danger'; break;
                        case 'expired': $status_class = 'label-warning'; break;
                    }
                    ?>
                    <span class="label <?= $status_class; ?>"><?= ucfirst($estimate['status']); ?></span>
                </td>
                <td>
                    <span class="badge"><?= $estimate['line_items_count']; ?></span>
                </td>
                <td><?= number_format($estimate['total_quantity'], 2); ?></td>
                <td>
                    <strong>$<?= number_format($estimate['total_amount'], 2); ?></strong>
                </td>
                <td><?= htmlspecialchars($estimate['created_by_name']); ?></td>
                <td><?= _dt($estimate['updated_at']); ?></td>
                <td>
                    <div class="row-options">
                        <a href="<?= admin_url('ella_contractors/view_estimate/' . $estimate['id']); ?>">
                            <?= _l('view'); ?>
                        </a>
                        <?php if(has_permission('ella_contractors','','edit')){ ?>
                        | <a href="#" data-toggle="modal" data-target="#estimate_modal" data-id="<?= $estimate['id']; ?>">
                            <?= _l('edit'); ?>
                        </a>
                        <?php } ?>
                        <?php if(has_permission('ella_contractors','','delete')){ ?>
                        | <a href="<?= admin_url('ella_contractors/delete_estimate/' . $estimate['id']); ?>" 
                             class="text-danger _delete">
                            <?= _l('delete'); ?>
                        </a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
  </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php $this->load->view('ella_contractors/estimate_modal'); ?>
<?php init_tail(); ?>
<script>
$(document).ready(function() {
    // Prevent DataTable initialization on our custom estimates table
    // Override the global DataTable initialization to skip our custom table
    window.originalInitDataTable = window.initDataTable;
    window.initDataTable = function(selector, url, notSortable, notSearchable, serverParams, order) {
        // Skip DataTable initialization for our custom estimates table
        if (selector === '.table-estimates' || $(selector).hasClass('table-estimates') || selector === '#custom-estimates-table') {
            console.log('Skipping DataTable initialization for custom estimates table');
            return;
        }
        // Call original function for other tables
        if (typeof window.originalInitDataTable === 'function') {
            return window.originalInitDataTable(selector, url, notSortable, notSearchable, serverParams, order);
        }
    };
    
    // Bulk actions function
    window.estimates_bulk_action = function(button) {
        var ids = [];
        var table = $('.table-estimates');
        var checkbox = table.find('tbody input[type="checkbox"]:checked');
        
        if (checkbox.length === 0) {
            alert_float('warning', 'No items selected');
            return;
        }
        
        checkbox.each(function() {
            ids.push($(this).val());
        });
        
        if ($('#mass_delete').is(':checked')) {
            if (confirm('Are you sure you want to delete the selected items?')) {
                $.post(admin_url + 'ella_contractors/estimates_bulk_action', {
                    ids: ids,
                    mass_delete: true
                }).done(function(response) {
                    alert_float('success', response);
                    window.location.reload();
                });
            }
        }
    };
});
</script>

</body>
</html>

