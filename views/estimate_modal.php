<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="estimate_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('edit_estimate'); ?></span>
                    <span class="add-title"><?php echo _l('new_estimate'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/ella_contractors/manage_estimate', array('id' => 'estimate_form')); ?>
            <?php echo form_hidden('estimate_id'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('estimate_name', 'estimate_name', '', 'text', array('required' => true)); ?>
                        <?php echo render_textarea('description', 'estimate_description'); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id" class="control-label"><?php echo _l('client'); ?></label>
                                    <select class="selectpicker display-block" data-width="100%" name="client_id" data-none-selected-text="Select Client">
                                        <option value="">Select Client</option>
                                        <?php foreach($clients as $client): ?>
                                            <option value="<?= $client['userid']; ?>"><?= htmlspecialchars($client['company']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lead_id" class="control-label"><?php echo _l('lead'); ?></label>
                                    <select class="selectpicker display-block" data-width="100%" name="lead_id" data-none-selected-text="Select Lead">
                                        <option value="">Select Lead</option>
                                        <?php foreach($leads as $lead): ?>
                                            <option value="<?= $lead['id']; ?>"><?= htmlspecialchars($lead['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="status" class="control-label"><?php echo _l('status'); ?></label>
                            <select class="selectpicker display-block" data-width="100%" name="status" data-none-selected-text="Select Status" required>
                                <option value="">Select Status</option>
                                <option value="draft">Draft</option>
                                <option value="sent">Sent</option>
                                <option value="accepted">Accepted</option>
                                <option value="rejected">Rejected</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        
                        <hr>
                        <h5><strong><?php echo _l('estimate_line_items'); ?></strong></h5>
                        
                        <!-- Current Line Items Display -->
                        <div id="current_line_items_display" style="display: block;">
                            <h6><strong>Current Line Items:</strong></h6>
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Group</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="current_line_items_table">
                                        <tr id="no_line_items_row">
                                            <td colspan="6" class="text-center text-muted">
                                                <i class="fa fa-info-circle"></i> No line items found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                        </div>
                        
                        <!-- Add New Line Items -->
                        <h6><strong>Add Line Items:</strong></h6>
                        <div id="line_items_container">
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
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success btn-sm" id="add_line_item_btn">
                                    <i class="fa fa-plus"></i> Add Line Item
                                </button>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><strong>Total Quantity: <span id="total_quantity">0.00</span></strong></h5>
                            </div>
                            <div class="col-md-6">
                                <h5><strong>Total Amount: $<span id="total_amount">0.00</span></strong></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
// Wait for DOM and jQuery to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit more for jQuery to be available
    setTimeout(function() {
        if (typeof jQuery !== 'undefined') {
            initEstimateModal();
        } else {
            console.error('jQuery not available');
        }
    }, 500);
});

function manage_estimate(form) {
    var data = $(form).serialize();
    var url = form.action;
    
    $.post(url, data).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
            // Show success message
            alert_float('success', response.message || 'Estimate saved successfully');
            
            // Close modal
            $('#estimate_modal').modal('hide');
            
            // Refresh the estimates table
            refreshEstimatesTable();
        } else {
            alert_float('danger', response.message || 'Failed to save estimate');
        }
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

function refreshEstimatesTable() {
    // Simple page reload to refresh the estimates list
    window.location.reload();
}


function initEstimateModal() {
    init_estimate_js();
    
    var lineItemIndex = 0;
    var lineItemOptions = [];
    
    function fillAllSelects() {
        console.log('=== fillAllSelects called ===');
        console.log('lineItemOptions:', lineItemOptions);
        console.log('lineItemOptions type:', typeof lineItemOptions);
        console.log('lineItemOptions length:', lineItemOptions ? lineItemOptions.length : 'null/undefined');
        console.log('Found select elements:', $('.line-item-select').length);
        
        if (!$('.line-item-select').length) {
            console.log('No select elements found, trying again in 100ms');
            setTimeout(fillAllSelects, 100);
            return;
        }
        
        $('.line-item-select').each(function(index) {
            var $this = $(this);
            var initialVal = $this.data('initial-value') || '';
            console.log('Processing select #' + index + ', initial value:', initialVal);
            
            $this.empty();
            $this.append('<option value="">Select Line Item</option>');
            
            if (lineItemOptions && lineItemOptions.length > 0) {
                console.log('Adding ' + lineItemOptions.length + ' options to select #' + index);
                lineItemOptions.forEach(function(opt, optIndex) {
                    console.log('Adding option #' + optIndex + ':', opt);
                    var option = $('<option></option>').val(opt.value).text(opt.text).attr('data-cost', opt.cost);
                    $this.append(option);
                });
            } else {
                console.log('No line item options available for select #' + index);
            }
            
            $this.val(initialVal);
            
            // Try to refresh selectpicker if available
            if (typeof $this.selectpicker === 'function') {
                try {
                    $this.selectpicker('refresh');
                    console.log('Selectpicker refreshed for select #' + index);
                } catch(e) {
                    console.log('Selectpicker refresh failed for select #' + index + ':', e);
                }
            } else {
                console.log('Selectpicker not available for select #' + index);
            }
            
            // Trigger change to set unit price if initial
            if (initialVal) {
                $this.trigger('change');
            }
        });
        console.log('=== fillAllSelects completed ===');
    }
    
    $("body").on('show.bs.modal', '#estimate_modal', function (event) {
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
                    $estimateModal.find('input[name="estimate_name"]').val(response.estimate_name);
                    $estimateModal.find('textarea[name="description"]').val(response.description);
                    $estimateModal.find('select[name="client_id"]').selectpicker('val', response.client_id);
                    $estimateModal.find('select[name="lead_id"]').selectpicker('val', response.lead_id);
                    $estimateModal.find('select[name="status"]').selectpicker('val', response.status);
                    
                    // Show current line items if they exist
                    console.log('Line items response:', response.line_items);
                    if (response.line_items && response.line_items.length > 0) {
                        console.log('Showing current line items, count:', response.line_items.length);
                        $('#current_line_items_display').show();
                        populateCurrentLineItems(response.line_items);
                    } else {
                        console.log('No line items found, hiding display');
                        $('#current_line_items_display').hide();
                    }
                    
                    // Populate line items for editing
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
                    
                    // Initialize selectpicker if available
                    if (typeof init_selectpicker === 'function') {
                        init_selectpicker();
                    } else if (typeof $().selectpicker === 'function') {
                        $('.selectpicker').selectpicker();
                    }
                });
        }
        
        // Load line item options
        console.log('About to load line items from:', admin_url + 'ella_contractors/get_line_items_ajax');
        
        $.get(admin_url + 'ella_contractors/get_line_items_ajax')
        .done(function(options) {
            console.log('Line items AJAX response:', options);
            console.log('Response type:', typeof options);
            console.log('Response length:', options ? options.length : 'null/undefined');
            
            if (options && Array.isArray(options)) {
                lineItemOptions = options;
                fillAllSelects();
                calculateTotals();
            } else {
                console.error('Invalid response format:', options);
                alert_float('warning', 'Invalid line items response format');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Failed to load line items:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
            alert_float('danger', 'Failed to load line items: ' + error);
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
            
            // Initialize selectpicker if available
            if (typeof init_selectpicker === 'function') {
                init_selectpicker();
            } else if (typeof $().selectpicker === 'function') {
                $('.selectpicker').selectpicker();
            }
            
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
    
    function populateCurrentLineItems(lineItems) {
        console.log('Populating current line items:', lineItems);
        var tbody = $('#current_line_items_table');
        tbody.empty();
        
        if (!lineItems || lineItems.length === 0) {
            console.log('No line items to populate');
            tbody.append(`
                <tr id="no_line_items_row">
                    <td colspan="6" class="text-center text-muted">
                        <i class="fa fa-info-circle"></i> No line items found
                    </td>
                </tr>
            `);
            return;
        }
        
        lineItems.forEach(function(item) {
            console.log('Adding line item:', item);
            var row = `
                <tr>
                    <td>${item.line_item_name || 'N/A'}</td>
                    <td>${item.group_name || 'No Group'}</td>
                    <td>${parseFloat(item.quantity).toFixed(2)}</td>
                    <td>$${parseFloat(item.unit_price).toFixed(2)}</td>
                    <td><strong>$${parseFloat(item.total_price).toFixed(2)}</strong></td>
                    <td>
                        <button type="button" class="btn btn-xs btn-danger remove-current-line-item" data-id="${item.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
        console.log('Finished populating line items table');
    }
    
    // Remove current line item
    $(document).on('click', '.remove-current-line-item', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to remove this line item?')) {
            $.post(admin_url + 'ella_contractors/remove_estimate_line_item/' + id).done(function(response) {
                row.remove();
                alert_float('success', 'Line item removed successfully');
                
                // Hide display if no more items
                if ($('#current_line_items_table tr').length === 0) {
                    $('#current_line_items_display').hide();
                }
            }).fail(function() {
                alert_float('danger', 'Failed to remove line item');
            });
        }
    });
}
</script>