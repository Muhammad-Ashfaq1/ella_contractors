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

$(document).ready(function() {
    init_estimate_js();
    
    var lineItemIndex = 0;
    var lineItemOptions = [];
    
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
            });
        }
        
        // Load line item options
        $.get(admin_url + 'ella_contractors/get_line_items_ajax').done(function(options) {
            lineItemOptions = options;
            fillAllSelects();
            calculateTotals();
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
                var option = $('<option></option>').val(opt.value).text(opt.text).attr('data-cost', opt.cost);
                $(this).append(option);
            });
            $(this).val(initialVal).selectpicker('refresh');
            
            // Trigger change to set unit price if initial
            if (initialVal) {
                $(this).trigger('change');
            }
        });
    }
});
</script>
