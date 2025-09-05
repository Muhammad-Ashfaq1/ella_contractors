<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-6">
                <h4><?php echo _l('estimate_name'); ?>: <?= htmlspecialchars($estimate->estimate_name); ?></h4>
                <?php if($estimate->description): ?>
                  <p><strong><?php echo _l('estimate_description'); ?>:</strong> <?= htmlspecialchars($estimate->description); ?></p>
                <?php endif; ?>
              </div>
              <div class="col-md-6 text-right">
                <div class="btn-group">
                  <a href="<?= admin_url('ella_contractors/estimates'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Back to Estimates
                  </a>
                  <?php if(has_permission('ella_contractors','','edit')){ ?>
                  <a href="#" class="btn btn-info" data-toggle="modal" data-target="#estimate_modal" data-id="<?= $estimate->id; ?>">
                    <i class="fa fa-edit"></i> Edit Estimate
                  </a>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <hr>
            
            <div class="row">
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-blue"><i class="fa fa-file-text-o"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Status</span>
                    <span class="info-box-number">
                      <?php
                      $status_class = '';
                      switch($estimate->status) {
                          case 'draft': $status_class = 'label-default'; break;
                          case 'sent': $status_class = 'label-info'; break;
                          case 'accepted': $status_class = 'label-success'; break;
                          case 'rejected': $status_class = 'label-danger'; break;
                          case 'expired': $status_class = 'label-warning'; break;
                      }
                      ?>
                      <span class="label <?= $status_class; ?>"><?= ucfirst($estimate->status); ?></span>
                    </span>
                  </div>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-green"><i class="fa fa-list"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Line Items</span>
                    <span class="info-box-number"><?= $estimate->line_items_count; ?></span>
                  </div>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-yellow"><i class="fa fa-cubes"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Total Quantity</span>
                    <span class="info-box-number"><?= number_format($estimate->total_quantity, 2); ?></span>
                  </div>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-red"><i class="fa fa-dollar"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Total Amount</span>
                    <span class="info-box-number">$<?= number_format($estimate->total_amount, 2); ?></span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <h5><strong>Client:</strong> <?= $estimate->client_name ? htmlspecialchars($estimate->client_name) : 'Not assigned'; ?></h5>
              </div>
              <div class="col-md-6">
                <h5><strong>Lead:</strong> <?= $estimate->lead_name ? htmlspecialchars($estimate->lead_name) : 'Not assigned'; ?></h5>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <h5><strong>Created By:</strong> <?= htmlspecialchars($estimate->created_by_name); ?></h5>
              </div>
              <div class="col-md-6">
                <h5><strong>Last Updated:</strong> <?= _dt($estimate->updated_at); ?></h5>
              </div>
            </div>
            
            <hr>
            
            <div class="row">
              <div class="col-md-12">
                <h4><?php echo _l('estimate_line_items'); ?></h4>
                
                <?php if(has_permission('ella_contractors','','edit')){ ?>
                <div class="mb-3">
                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add_line_item_modal">
                    <i class="fa fa-plus"></i> <?php echo _l('add_line_item'); ?>
                  </button>
                </div>
                <?php } ?>
                
                <?php if(empty($estimate_line_items)): ?>
                  <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> <?php echo _l('no_line_items'); ?>
                  </div>
                <?php else: ?>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Image</th>
                          <th>Name</th>
                          <th>Group</th>
                          <th>Description</th>
                          <th>Unit Type</th>
                          <th>Quantity</th>
                          <th>Unit Price</th>
                          <th>Total Price</th>
                          <?php if(has_permission('ella_contractors','','edit')){ ?>
                          <th>Actions</th>
                          <?php } ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach($estimate_line_items as $item): ?>
                        <tr>
                          <td>
                            <?php if($item['image']): ?>
                              <img src="<?= site_url('uploads/ella_line_items/' . $item['image']); ?>" 
                                   alt="<?= htmlspecialchars($item['line_item_name']); ?>" 
                                   class="img-thumbnail" 
                                   style="width: 40px; height: 40px; object-fit: cover;">
                            <?php else: ?>
                              <div class="text-center" style="width: 40px; height: 40px; background: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-image text-muted"></i>
                              </div>
                            <?php endif; ?>
                          </td>
                          <td><?= htmlspecialchars($item['line_item_name']); ?></td>
                          <td><?= htmlspecialchars($item['group_name'] ?? 'No Group'); ?></td>
                          <td><?= htmlspecialchars(substr($item['line_item_description'], 0, 50)) . (strlen($item['line_item_description']) > 50 ? '...' : ''); ?></td>
                          <td><?= htmlspecialchars($item['unit_type']); ?></td>
                          <td><?= number_format($item['quantity'], 2); ?></td>
                          <td>$<?= number_format($item['unit_price'], 2); ?></td>
                          <td><strong>$<?= number_format($item['total_price'], 2); ?></strong></td>
                          <?php if(has_permission('ella_contractors','','edit')){ ?>
                          <td>
                            <div class="btn-group">
                              <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#edit_line_item_modal" 
                                      data-id="<?= $item['id']; ?>" 
                                      data-quantity="<?= $item['quantity']; ?>" 
                                      data-unit-price="<?= $item['unit_price']; ?>">
                                <i class="fa fa-edit"></i>
                              </button>
                              <a href="<?= admin_url('ella_contractors/remove_estimate_line_item/' . $item['id']); ?>" 
                                 class="btn btn-xs btn-danger _delete">
                                <i class="fa fa-trash"></i>
                              </a>
                            </div>
                          </td>
                          <?php } ?>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                      <tfoot>
                        <tr style="background-color: #f5f5f5; font-weight: bold;">
                          <td colspan="5">Total</td>
                          <td><?= number_format($estimate->total_quantity, 2); ?></td>
                          <td></td>
                          <td><strong>$<?= number_format($estimate->total_amount, 2); ?></strong></td>
                          <?php if(has_permission('ella_contractors','','edit')){ ?>
                          <td></td>
                          <?php } ?>
                        </tr>
                      </tfoot>
                    </table>
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

<!-- Add Line Item Modal -->
<?php if(has_permission('ella_contractors','','edit')){ ?>
<div class="modal fade" id="add_line_item_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo _l('add_line_item'); ?></h4>
      </div>
      <?php echo form_open('admin/ella_contractors/add_line_item_to_estimate'); ?>
      <?php echo form_hidden('estimate_id', $estimate->id); ?>
      <div class="modal-body">
        <div class="form-group">
          <label for="line_item_id" class="control-label"><?php echo _l('select_line_item'); ?></label>
          <select class="selectpicker display-block" data-width="100%" name="line_item_id" data-none-selected-text="Select Line Item" required>
            <option value="">Select Line Item</option>
            <?php foreach($line_items as $item): ?>
              <option value="<?= $item['id']; ?>" data-cost="<?= $item['cost']; ?>">
                <?= htmlspecialchars($item['name']); ?> - $<?= number_format($item['cost'], 2); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="quantity" class="control-label"><?php echo _l('quantity'); ?></label>
              <input type="number" id="quantity" name="quantity" class="form-control" step="0.01" min="0" value="1" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="unit_price" class="control-label"><?php echo _l('unit_price'); ?></label>
              <input type="number" id="unit_price" name="unit_price" class="form-control" step="0.01" min="0" required>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-success"><?php echo _l('add_line_item'); ?></button>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

<!-- Edit Line Item Modal -->
<div class="modal fade" id="edit_line_item_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Line Item</h4>
      </div>
      <?php echo form_open('admin/ella_contractors/update_estimate_line_item'); ?>
      <?php echo form_hidden('id'); ?>
      <?php echo form_hidden('estimate_id', $estimate->id); ?>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="edit_quantity" class="control-label"><?php echo _l('quantity'); ?></label>
              <input type="number" id="edit_quantity" name="quantity" class="form-control" step="0.01" min="0" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="edit_unit_price" class="control-label"><?php echo _l('unit_price'); ?></label>
              <input type="number" id="edit_unit_price" name="unit_price" class="form-control" step="0.01" min="0" required>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info">Update Line Item</button>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<!-- Estimate Edit Modal -->
<?php $this->load->view('ella_contractors/estimate_modal'); ?>

<?php init_tail(); ?>
<script>
$(document).ready(function() {
    // Auto-fill unit price when line item is selected
    $('select[name="line_item_id"]').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var cost = selectedOption.data('cost');
        if (cost) {
            $('#unit_price').val(cost);
        }
    });
    
    // Edit line item modal
    $('#edit_line_item_modal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var quantity = button.data('quantity');
        var unitPrice = button.data('unit-price');
        
        var modal = $(this);
        modal.find('input[name="id"]').val(id);
        modal.find('input[name="quantity"]').val(quantity);
        modal.find('input[name="unit_price"]').val(unitPrice);
    });
});
</script>
</body>
</html>

