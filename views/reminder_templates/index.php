<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php if (has_permission('ella_contractors', '', 'edit')): ?>
                                    <a href="<?php echo admin_url('ella_contractors/reminder_templates/edit/new'); ?>" class="btn btn-info">
                                        <i class="fa fa-plus" style="margin-right: 2% !important;"></i> New Template
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-reminder-templates">
                                <thead>
                                    <tr>
                                        <th class="text-center"><?php echo _l('id'); ?></th>
                                        <th class="text-center" style="min-width: 200px;">Template Name</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Stage</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Created By</th>
                                        <th class="text-center">Last Updated</th>
                                        <th class="text-center" width="120px"><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($templates) && is_array($templates)): ?>
                                        <?php foreach ($templates as $template): ?>
                                            <?php if (isset($template['id'])): ?>
                                            <tr>
                                                <td class="text-center"><?php echo $template['id']; ?></td>
                                                <td class="text-center">
                                                    <a href="<?php echo admin_url('ella_contractors/reminder_templates/edit/' . $template['id']); ?>">
                                                        <?php echo htmlspecialchars($template['template_name'] ?? ''); ?>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <span class="label label-<?php echo (isset($template['template_type']) && $template['template_type'] == 'email') ? 'info' : 'success'; ?>">
                                                        <?php echo strtoupper($template['template_type'] ?? ''); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                    $stage_labels = [
                                                        'client_instant' => 'Client Instant',
                                                        'client_48h' => 'Client 48h',
                                                        'staff_48h' => 'Staff 48h'
                                                    ];
                                                    $stage = $template['reminder_stage'] ?? '';
                                                    echo $stage_labels[$stage] ?? $stage;
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if (has_permission('ella_contractors', '', 'edit')): ?>
                                                        <a href="javascript:void(0);" 
                                                           onclick="toggleTemplateStatus(<?php echo $template['id']; ?>)" 
                                                           class="label label-<?php echo (isset($template['is_active']) && $template['is_active']) ? 'success' : 'default'; ?>">
                                                            <?php echo (isset($template['is_active']) && $template['is_active']) ? 'Active' : 'Inactive'; ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="label label-<?php echo (isset($template['is_active']) && $template['is_active']) ? 'success' : 'default'; ?>">
                                                            <?php echo (isset($template['is_active']) && $template['is_active']) ? 'Active' : 'Inactive'; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?php echo htmlspecialchars($template['created_by_name'] ?? 'N/A'); ?></td>
                                                <td class="text-center"><?php echo isset($template['updated_at']) ? time_ago($template['updated_at']) : 'N/A'; ?></td>
                                                <td class="text-center">
                                                    <div class="row-options">
                                                        <a href="javascript:void(0);" 
                                                           onclick="previewTemplate(<?php echo $template['id']; ?>)" 
                                                           class="btn btn-default btn-icon" 
                                                           data-toggle="tooltip" 
                                                           title="Preview">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <?php if (has_permission('ella_contractors', '', 'edit')): ?>
                                                            <a href="<?php echo admin_url('ella_contractors/reminder_templates/edit/' . $template['id']); ?>" 
                                                               class="btn btn-default btn-icon" 
                                                               data-toggle="tooltip" 
                                                               title="Edit">
                                                                <i class="fa fa-pencil-square-o"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if (has_permission('ella_contractors', '', 'delete')): ?>
                                                            <a href="javascript:void(0);" 
                                                               onclick="deleteTemplate(<?php echo $template['id']; ?>)" 
                                                               class="btn btn-danger btn-icon _delete" 
                                                               data-toggle="tooltip" 
                                                               title="Delete">
                                                                <i class="fa fa-remove"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No templates found. <a href="<?php echo admin_url('ella_contractors/reminder_templates/edit/new'); ?>">Create one now</a>.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Template Preview</h4>
            </div>
            <div class="modal-body">
                <div id="previewSubject" style="font-weight: bold; margin-bottom: 15px;"></div>
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<style>
/* Ensure row-options buttons are always visible */
.table-reminder-templates .row-options {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.table-reminder-templates .row-options .btn-icon {
    display: inline-block;
    margin: 0 2px;
}
</style>
<script>
$(document).ready(function() {
    // Initialize tooltips
    if ($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
});

function toggleTemplateStatus(id) {
    if (!confirm('Are you sure you want to toggle this template status?')) {
        return;
    }
    
    $.post(admin_url + 'ella_contractors/reminder_templates/toggle_active', {
        id: id,
        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
    }, function(response) {
        response = typeof response === 'string' ? JSON.parse(response) : response;
        if (response.success) {
            alert_float('success', response.message);
            location.reload();
        } else {
            alert_float('danger', response.message);
        }
    });
}

function deleteTemplate(id) {
    if (!confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
        return;
    }
    
    $.post(admin_url + 'ella_contractors/reminder_templates/delete', {
        id: id,
        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
    }, function(response) {
        response = typeof response === 'string' ? JSON.parse(response) : response;
        if (response.success) {
            alert_float('success', response.message);
            location.reload();
        } else {
            alert_float('danger', response.message);
        }
    });
}

function previewTemplate(id) {
    $.post(admin_url + 'ella_contractors/reminder_templates/preview', {
        id: id,
        <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
    }, function(response) {
        response = typeof response === 'string' ? JSON.parse(response) : response;
        if (response.success) {
            $('#previewSubject').html('<strong>Subject:</strong> ' + (response.subject || 'N/A (SMS Template)'));
            $('#previewContent').html(response.content);
            $('#previewModal').modal('show');
        } else {
            alert_float('danger', response.message);
        }
    });
}
</script>

