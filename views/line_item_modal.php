<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="line_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('edit_line_item'); ?></span>
                    <span class="add-title"><?php echo _l('add_line_item'); ?></span>
                </h4>
            </div>
            <?php echo form_open_multipart('admin/ella_contractors/manage_line_item',array('id'=>'line_item_form')); ?>
            <?php echo form_hidden('itemid'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','line_item_name','','text',array('required'=>true)); ?>
                        <?php echo render_textarea('description','line_item_description'); ?>
                        <div class="form-group">
                        <label for="cost" class="control-label">
                            <?php echo _l('cost'); ?> ($)</label>
                            <input type="number" id="cost" name="cost" class="form-control" step="0.01" min="0" value="">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                             <div class="form-group">
                                <label class="control-label" for="quantity"><?php echo _l('quantity'); ?></label>
                                <input type="number" id="quantity" name="quantity" class="form-control" step="0.01" min="0" value="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                         <div class="form-group">
                            <label class="control-label" for="unit_type"><?php echo _l('unit_type'); ?></label>
                            <select class="selectpicker display-block" data-width="100%" name="unit_type" data-none-selected-text="<?php echo _l('select_unit_type'); ?>">
                                <option value=""></option>
                                <option value="sq ft">Square Feet</option>
                                <option value="sq m">Square Meters</option>
                                <option value="inch">Inch</option>
                                <option value="ft">Feet</option>
                                <option value="m">Meters</option>
                                <option value="piece">Piece</option>
                                <option value="each">Each</option>
                                <option value="hour">Hour</option>
                                <option value="day">Day</option>
                                <option value="lb">Pound</option>
                                <option value="kg">Kilogram</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="clearfix mbot15"></div>
                <div class="form-group">
                    <label for="image" class="control-label"><?php echo _l('image'); ?></label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">JPG, PNG, GIF (Max 2MB)</small>
                </div>
                <div id="custom_fields_line_items">
                    <?php echo render_custom_fields('line_items'); ?>
                </div>
                <?php echo render_select('group_id',$groups,array('id','name'),'item_group'); ?>


                <div class="form-group select-placeholder" >
                    <label for="is_active"> Status </label>
                    <select name="is_active" id="is_active" class="selectpicker" data-width="100%" data-live-search="true">
                        <option value="1"><?php echo _l('active'); ?></option>
                        <option value="0"><?php echo _l('inactive'); ?></option>
                    </select>
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
    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    if(typeof(jQuery) != 'undefined'){
        init_line_item_js();
    } else {
     window.addEventListener('load', function () {
       var initLineItemsJsInterval = setInterval(function(){
            if(typeof(jQuery) != 'undefined') {
                init_line_item_js();
                clearInterval(initLineItemsJsInterval);
            }
         }, 1000);
     });
  }
// Line items add/edit
function manage_line_items(form) {
    var data = $(form).serialize();

    var url = form.action;
    $.post(url, data).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
            // Reload page to show updated data
            window.location.reload();
        }
        $('#line_item_modal').modal('hide');
    }).fail(function (data) {
        alert_float('danger', data.responseText);
    });
    return false;
}
function init_line_item_js() {
    // Line items modal show action
    $("body").on('show.bs.modal', '#line_item_modal', function (event) {

        var $itemModal = $('#line_item_modal');
        $('input[name="itemid"]').val('');
        $itemModal.find('input').not('input[type="hidden"]').val('');
        $itemModal.find('textarea').val('');
        $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
        $itemModal.find('input[name="is_active"]').prop('checked', true);
        $itemModal.find('.add-title').removeClass('hide');
        $itemModal.find('.edit-title').addClass('hide');

        var id = $(event.relatedTarget).data('id');
        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {

            $('input[name="itemid"]').val(id);

            requestGetJSON('ella_contractors/get_line_item_data/' + id).done(function (response) {
                $itemModal.find('input[name="name"]').val(response.name);
                $itemModal.find('textarea[name="description"]').val(response.description);
                $itemModal.find('input[name="cost"]').val(response.cost);
                $itemModal.find('input[name="quantity"]').val(response.quantity);
                $('select[name="unit_type"]').selectpicker('val', response.unit_type).change();
                $itemModal.find('#group_id').selectpicker('val', response.group_id);
                $itemModal.find('select[name="is_active"]').selectpicker('val', response.is_active);
                $('#custom_fields_line_items').html(response.custom_fields_html);

                init_selectpicker();
                init_color_pickers();
                init_datepicker();

                $itemModal.find('.add-title').addClass('hide');
                $itemModal.find('.edit-title').removeClass('hide');
                validate_line_item_form();
            });

        }
    });

    $("body").on("hidden.bs.modal", '#line_item_modal', function (event) {
        // Reset form
    });

   validate_line_item_form();
}
function validate_line_item_form(){
    // Set validation for line item form
    appValidateForm($('#line_item_form'), {
        name: 'required',
        unit_type: 'required',
        group_id: 'required'
    }, manage_line_items);
}
</script>
