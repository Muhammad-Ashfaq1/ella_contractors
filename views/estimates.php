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
    <table class="table table-striped table-estimates">
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
(function($) {
    console.log('Estimates JS loaded');
    
    function manage_estimate(form) {
        var data = $(form).serialize();
        var url = form.action;
        
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                window.location.reload();
            }
            $('#estimate_modal').modal('hide');
        }).fail(function (data) {
            alert_float('danger', data.responseText);
        });
        return false;
    }

    function init_estimate_js() {
        appValidateForm($('#estimate_form'), {
            estimate_name: 'required',
            status: 'required'
        }, manage_estimate);
    }

    function calculateLineItemTotal(row) {
        var quantity = parseFloat(row.find('.line-item-quantity').val()) || 0;
        var unitPrice = parseFloat(row.find('.line-item-unit-price').val()) || 0;
        var total = quantity * unitPrice;
        row.find('.line-item-total').val(total.toFixed(2));
        calculateTotals();
    }

    function calculateTotals() {
        var totalQuantity = 0;
        var totalAmount = 0;
        $('.line-item-row').each(function() {
            totalQuantity += parseFloat($(this).find('.line-item-quantity').val()) || 0;
            totalAmount += parseFloat($(this).find('.line-item-total').val()) || 0;
        });
        $('#total_quantity').text(totalQuantity.toFixed(2));
        $('#total_amount').text(totalAmount.toFixed(2));
    }

    function fillAllSelects() {
        $('.line-item-select').each(function() {
            var initialVal = $(this).data('initial-value') || '';
            $(this).empty();
            $(this).append('<option value="">Select Line Item</option>');
            lineItemOptions.forEach(function(opt) {
                $(this).append(`<option value="${opt.value}" data-cost="${opt.cost}">${opt.text}</option>`);
            });
            $(this).val(initialVal).selectpicker('refresh');
            if (initialVal) {
                $(this).trigger('change');
            }
        });
    }

    console.log('Functions defined');

    $(document).ready(function() {
        console.log('Document ready executed');
        init_estimate_js();
        
        var lineItemIndex = 0;
        var lineItemOptions = [];
        
        $("body").on('show.bs.modal', '#estimate_modal', function (event) {
            console.log('Modal opening...');
            
            var $estimateModal = $('#estimate_modal');
            $estimateModal.find('input').not('input[type="hidden"]').val('');
            $estimateModal.find('textarea').val('');
            $estimateModal.find('select').selectpicker('val', '').selectpicker('refresh');
            $estimateModal.find('.add-title').removeClass('hide');
            $estimateModal.find('.edit-title').addClass('hide');
            
            // Reset line items to initial state
            $('#line_items_container').html(`
                <div class="line-item-row" style="margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo _l('select_line_item'); ?></label>
                                <select class="selectpicker display-block line-item-select" data-width="100%" name="line_items[0][line_item_id]" data-none-selected-text="Select Line Item">
                                    <option value="">Select Line Item</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><?php echo _l('quantity'); ?></label>
                                <input type="number" class="form-control line-item-quantity" name="line_items[0][quantity]" step="0.01" min="0" value="1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><?php echo _l('unit_price'); ?></label>
                                <input type="number" class="form-control line-item-unit-price" name="line_items[0][unit_price]" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><?php echo _l('total_price'); ?></label>
                                <input type="text" class="form-control line-item-total" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm remove-line-item" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            lineItemIndex = 0;
            
            var id = $(event.relatedTarget).data('id');
            if (typeof (id) !== 'undefined') {
                $estimateModal.find('.add-title').addClass('hide');
                $estimateModal.find('.edit-title').removeClass('hide');
                $('input[name="estimate_id"]').val(id);
                
                $.get(admin_url + 'ella_contractors/get_estimate_data/' + id).done(function(response) {
                    console.log('Estimate data loaded:', response);
                    $estimateModal.find('input[name="estimate_name"]').val(response.estimate_name);
                    $estimateModal.find('textarea[name="description"]').val(response.description);
                    $estimateModal.find('select[name="client_id"]').selectpicker('val', response.client_id);
                    $estimateModal.find('select[name="lead_id"]').selectpicker('val', response.lead_id);
                    $estimateModal.find('select[name="status"]').selectpicker('val', response.status);
                    
                    // Populate line items
                    $('#line_items_container').empty();
                    lineItemIndex = -1;
                    response.line_items.forEach(function(item) {
                        lineItemIndex++;
                        var lineItemHtml = `
                            <div class="line-item-row" style="margin-bottom: 10px;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo _l('select_line_item'); ?></label>
                                            <select class="selectpicker display-block line-item-select" data-width="100%" name="line_items[${lineItemIndex}][line_item_id]" data-none-selected-text="Select Line Item" data-initial-value="${item.line_item_id}">
                                                <option value="">Select Line Item</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo _l('quantity'); ?></label>
                                            <input type="number" class="form-control line-item-quantity" name="line_items[${lineItemIndex}][quantity]" step="0.01" min="0" value="${item.quantity}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo _l('unit_price'); ?></label>
                                            <input type="number" class="form-control line-item-unit-price" name="line_items[${lineItemIndex}][unit_price]" step="0.01" min="0" value="${item.unit_price}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label"><?php echo _l('total_price'); ?></label>
                                            <input type="text" class="form-control line-item-total" readonly value="${(item.quantity * item.unit_price).toFixed(2)}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;</label>
                                            <button type="button" class="btn btn-danger btn-sm remove-line-item" style="width: 100%;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#line_items_container').append(lineItemHtml);
                    });
                    
                    init_selectpicker();
                }).fail(function(error) {
                    console.error('Failed to load estimate data:', error);
                });
            }
            
            // Load line item options
            $.get(admin_url + 'ella_contractors/get_line_items_ajax').done(function(options) {
                console.log('Line items loaded:', options);
                lineItemOptions = options;
                fillAllSelects();
                calculateTotals();
            }).fail(function(error) {
                console.error('Failed to load line items:', error);
                alert_float('danger', 'Failed to load line items. Check console for details.');
            });
        });
        
        // Add line item button
        $('#add_line_item_btn').on('click', function() {
            lineItemIndex++;
            var lineItemHtml = `
                <div class="line-item-row" style="margin-bottom: 10px;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?php echo _l('select_line_item'); ?></label>
                                <select class="selectpicker display-block line-item-select" data-width="100%" name="line_items[${lineItemIndex}][line_item_id]" data-none-selected-text="Select Line Item">
                                    <option value="">Select Line Item</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><?php echo _l('quantity'); ?></label>
                                <input type="number" class="form-control line-item-quantity" name="line_items[${lineItemIndex}][quantity]" step="0.01" min="0" value="1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><?php echo _l('unit_price'); ?></label>
                                <input type="number" class="form-control line-item-unit-price" name="line_items[${lineItemIndex}][unit_price]" step="0.01" min="0" value="">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"><?php echo _l('total_price'); ?></label>
                                <input type="text" class="form-control line-item-total" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm remove-line-item" style="width: 100%;">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#line_items_container').append(lineItemHtml);
            init_selectpicker();
            fillAllSelects();
            calculateTotals();
        });
        
        // Remove line item
        $(document).on('click', '.remove-line-item', function() {
            $(this).closest('.line-item-row').remove();
            calculateTotals();
        });
        
        // Auto-fill unit price
        $(document).on('change', '.line-item-select', function() {
            var selectedOption = $(this).find('option:selected');
            var cost = selectedOption.data('cost');
            if (cost) {
                $(this).closest('.line-item-row').find('.line-item-unit-price').val(cost);
            }
            calculateLineItemTotal($(this).closest('.line-item-row'));
        });
        
        // Calculate line total
        $(document).on('input', '.line-item-quantity, .line-item-unit-price', function() {
            calculateLineItemTotal($(this).closest('.line-item-row'));
        });
    });
})(jQuery);
</script>

</body>
</html>

