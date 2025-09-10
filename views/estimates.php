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
                <th><?php echo _l('created_date'); ?></th>
                <th><?php echo _l('last_updated'); ?></th>
                <th><?php echo _l('options'); ?></th>
            </tr>
        </thead>
        <tbody>
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
    // Initialize DataTable for estimates (using main route for table only)
    initDataTable('#custom-estimates-table', admin_url + 'estimates/table', [0], [0], [], [[0, 'desc']]);
    
    // Bulk actions function
    window.estimates_bulk_action = function(button) {
        var ids = [];
        var table = $('#custom-estimates-table');
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
                $.post(admin_url + 'ella_contractors/estimates/estimates_bulk_action', {
                    ids: ids,
                    mass_delete: true
                }).done(function(response) {
                    alert_float('success', response);
                    $('#custom-estimates-table').DataTable().ajax.reload();
                });
            }
        }
    };
});
</script>

</body>
</html>

