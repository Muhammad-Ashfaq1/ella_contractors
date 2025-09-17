<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php if(has_permission('ella_contractors','','delete')){ ?>
             <a href="#" data-toggle="modal" data-table=".table-line-items" data-target="#line_items_bulk_actions" class="hide bulk-actions-btn table-btn"><?php echo _l('bulk_actions'); ?></a>
             <div class="modal fade bulk_actions" id="line_items_bulk_actions" tabindex="-1" role="dialog">
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
                  <!-- <hr class="mass_delete_separator" /> -->
                <?php } ?>
              </div>
              <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
               <a href="#" class="btn btn-info" onclick="line_items_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
             </div>
           </div>
           <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
       </div>
       <!-- /.modal -->
     <?php } ?>
     <?php hooks()->do_action('before_line_items_page_content'); ?>
     <?php if(has_permission('ella_contractors','','create')){ ?>
       <div class="_buttons">
        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#line_item_modal"><?php echo _l('new_line_item'); ?></a>
        <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal" data-target="#groups"><?php echo _l('item_groups'); ?></a>
      </div>
      <div class="clearfix"></div>
      <hr class="hr-panel-heading" />
    <?php } ?>
    <!-- Service Items Table -->
    <h5>Service Items</h5>
    <table class="table table-striped table-line-items">
        <thead>
            <tr>
                <?php if(has_permission('ella_contractors','','delete')){ ?>
                <th width="50">
                    <div class="checkbox mass_select_all_wrap">
                        <input type="checkbox" id="mass_select_all" data-to-table="line-items">
                        <label></label>
                    </div>
                </th>
                <?php } ?>
                <th>Image</th>
                <th>Name</th>
                <th>Group</th>
                <th>Description</th>
                <th>Cost</th>
                <th>Quantity</th>
                <th>Unit Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($line_items as $item): ?>
            <tr>
                <?php if(has_permission('ella_contractors','','delete')){ ?>
                <td>
                    <div class="checkbox">
                        <input type="checkbox" value="<?= $item['id']; ?>">
                        <label></label>
                    </div>
                </td>
                <?php } ?>
                <td>
                    <?php if($item['image']): ?>
                        <img src="<?= site_url('uploads/ella_line_items/' . $item['image']); ?>" 
                             alt="<?= htmlspecialchars($item['name']); ?>" 
                             class="img-thumbnail" 
                             style="width: 40px; height: 40px; object-fit: cover;">
                    <?php else: ?>
                        <div class="text-center" style="width: 40px; height: 40px; background: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                            <i class="fa fa-image text-muted"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="#" data-toggle="modal" data-target="#line_item_modal" data-id="<?= $item['id']; ?>">
                        <?= htmlspecialchars($item['name']); ?>
                    </a>
                    <div class="row-options">
                        <?php if(has_permission('ella_contractors','','edit')){ ?>
                        <a href="#" data-toggle="modal" data-target="#line_item_modal" data-id="<?= $item['id']; ?>">
                            <?= _l('edit'); ?>
                        </a>
                        <?php } ?>
                        <?php if(has_permission('ella_contractors','','delete')){ ?>
                        | <a href="<?= admin_url('ella_contractors/delete_line_item/' . $item['id']); ?>" 
                             class="text-danger _delete">
                            <?= _l('delete'); ?>
                        </a>
                        <?php } ?>
                    </div>
                </td>
                <td><?= htmlspecialchars($item['group_name'] ?? 'No Group'); ?></td>
                <td><?= htmlspecialchars(substr($item['description'], 0, 30)) . (strlen($item['description']) > 30 ? '...' : ''); ?></td>
                <td><?= $item['cost'] ? '$' . number_format($item['cost'], 2) : 'N/A'; ?></td>
                <td><?= number_format($item['quantity'], 2); ?></td>
                <td><?= htmlspecialchars($item['unit_type']); ?></td>
                <td>
                    <?php if($item['is_active']): ?>
                        <span class="label label-success">Active</span>
                    <?php else: ?>
                        <span class="label label-default">Inactive</span>
                    <?php endif; ?>
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
<?php $this->load->view('ella_contractors/line_item_modal'); ?>
<div class="modal fade" id="groups" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('item_groups'); ?>
        </h4>
      </div>
      <div class="modal-body">
        <?php if(has_permission('ella_contractors','','create')){ ?>
          <div class="input-group">
            <input type="text" name="item_group_name" id="item_group_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
            <span class="input-group-btn">
              <button class="btn btn-info p7" type="button" id="new-item-group-insert"><?php echo _l('new_item_group'); ?></button>
            </span>
          </div>
          <hr />
        <?php } ?>
        <div class="row">
         <div class="container-fluid">
          <table class="table dt-table table-items-groups" data-order-col="1" data-order-type="asc">
            <thead>
              <tr>
                <th><?php echo _l('id'); ?></th>
                <th><?php echo _l('item_group_name'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($groups as $group){ ?>
                <tr class="row-has-options" data-group-row-id="<?php echo $group['id']; ?>">
                  <td data-order="<?php echo $group['id']; ?>"><?php echo $group['id']; ?></td>
                  <td data-order="<?php echo $group['name']; ?>">
                    <span class="group_name_plain_text"><?php echo $group['name']; ?></span>
                    <div class="group_edit hide">
                     <div class="input-group">
                      <input type="text" class="form-control">
                      <span class="input-group-btn">
                        <button class="btn btn-info p8 update-item-group" type="button"><?php echo _l('submit'); ?></button>
                      </span>
                    </div>
                  </div>
                  <div class="row-options">
                    <?php if(has_permission('ella_contractors','','edit')){ ?>
                      <a href="#" class="edit-item-group">
                        <?php echo _l('edit'); ?>
                      </a>
                    <?php } ?>
                    <?php if(has_permission('ella_contractors','','delete')){ ?>
                      | <a href="<?php echo admin_url('ella_contractors/delete_group/'.$group['id']); ?>" class="delete-item-group _delete text-danger">
                        <?php echo _l('delete'); ?>
                      </a>
                    <?php } ?>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
  </div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
  $(function(){

    var notSortableAndSearchableItemColumns = [];
    <?php if(has_permission('ella_contractors','','delete')){ ?>
      notSortableAndSearchableItemColumns.push(0);
    <?php } ?>

    
    <?php if ($this->input->get('id')) { ?>
      var id = "<?php echo $this->input->get('id') ?>";
      if (typeof(id) !== 'undefined') {
        var $itemModal = $('#line_item_modal');
        $('input[name="itemid"]').val(id);
        requestGetJSON('ella_contractors/get_line_item_data/' + id).done(function(response) {
          $itemModal.find('input[name="name"]').val(response.name);
          $itemModal.find('textarea[name="description"]').val(response.description);
          $itemModal.find('input[name="cost"]').val(response.cost);
          $itemModal.find('input[name="quantity"]').val(response.quantity);
          $itemModal.find('input[name="unit_type"]').val(response.unit_type);
          $itemModal.find('#group_id').val(response.group_id);
          $itemModal.find('input[name="is_active"]').prop('checked', response.is_active == 1);

          init_selectpicker();
          init_color_pickers();
          init_datepicker();

          $itemModal.find('.add-title').addClass('hide');
          $itemModal.find('.edit-title').removeClass('hide');
          validate_line_item_form();
        });
        $itemModal.modal('show');
      }
    <?php } ?>

    // DataTable initialization removed - using direct table rendering
    
    // Select All functionality
    $('#mass_select_all').on('change', function() {
      var isChecked = $(this).prop('checked');
      $('.table-line-items tbody input[type="checkbox"]').prop('checked', isChecked);
      updateBulkActionsButton();
    });
    
    // Individual checkbox change
    $('.table-line-items tbody').on('change', 'input[type="checkbox"]', function() {
      updateBulkActionsButton();
    });
    
    function updateBulkActionsButton() {
      var checkedCount = $('.table-line-items tbody input[type="checkbox"]:checked').length;
      if (checkedCount > 0) {
        $('.bulk-actions-btn').removeClass('hide');
      } else {
        $('.bulk-actions-btn').addClass('hide');
      }
    }

    if(get_url_param('groups_modal')){
       // Set time out user to see the message
       setTimeout(function(){
         $('#groups').modal('show');
       },1000);
     }

     $('#new-item-group-insert').on('click',function(){
      var group_name = $('#item_group_name').val();
      if(group_name != ''){
        $.post(admin_url+'ella_contractors/add_group',{name:group_name}).done(function(){
         window.location.href = admin_url+'ella_contractors/line_items?groups_modal=true';
       });
      }
    });

     $('body').on('click','.edit-item-group',function(e){
      e.preventDefault();
      var tr = $(this).parents('tr'),
      group_id = tr.attr('data-group-row-id');
      tr.find('.group_name_plain_text').toggleClass('hide');
      tr.find('.group_edit').toggleClass('hide');
      tr.find('.group_edit input').val(tr.find('.group_name_plain_text').text());
    });

     $('body').on('click','.update-item-group',function(){
      var tr = $(this).parents('tr');
      var group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if(name != ''){
        $.post(admin_url+'ella_contractors/update_group/'+group_id,{name:name}).done(function(){
         window.location.href = admin_url+'ella_contractors/line_items';
       });
      }
    });
   });
  function line_items_bulk_action(event) {
    if (confirm_delete()) {
      var mass_delete = $('#mass_delete').prop('checked');
      var ids = [];
      var data = {};

      if(mass_delete == true) {
        data.mass_delete = true;
      }

      var rows = $('.table-line-items').find('tbody tr');
      $.each(rows, function() {
        var checkbox = $(this).find('input[type="checkbox"]');
        if (checkbox.prop('checked') === true) {
          ids.push(checkbox.val());
        }
      });
      
      if(ids.length === 0) {
        alert_float('warning', 'Please select at least one item');
        return;
      }
      
      data.ids = ids;
      $(event).addClass('disabled');
      setTimeout(function() {
        $.post(admin_url + 'ella_contractors/bulk_action', data).done(function() {
          window.location.reload();
        }).fail(function(data) {
          alert_float('danger', data.responseText);
        });
      }, 200);
    }
  }
 </script>
</body>
</html>