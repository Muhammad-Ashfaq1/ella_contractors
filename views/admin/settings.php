<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix"></div>
                        <h4 class="no-margin">
                            <i class="fa fa-cog"></i> EllaContractors Settings
                        </h4>
                        <hr class="hr-panel-heading" />

                        <!-- Google Calendar API Settings -->
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="bold">
                                    <i class="fa fa-google text-danger"></i> Google Calendar API Integration
                                </h4>
                                <p class="text-muted">Configure Google OAuth2 credentials for two-way calendar synchronization.</p>
                                <hr />
                            </div>
                        </div>

                        <?php echo form_open(admin_url('ella_contractors/settings/save'), ['id' => 'google-calendar-settings-form']); ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="google_calendar_client_id" class="control-label">
                                            <i class="fa fa-key"></i> Google Calendar Client ID
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               id="google_calendar_client_id" 
                                               name="google_calendar_client_id" 
                                               class="form-control" 
                                               value="<?php echo get_option('google_calendar_client_id'); ?>"
                                               required>
                                        <p class="help-block">
                                            Your Google OAuth 2.0 Client ID from <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="google_calendar_client_secret" class="control-label">
                                            <i class="fa fa-lock"></i> Google Calendar Client Secret
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" 
                                               id="google_calendar_client_secret" 
                                               name="google_calendar_client_secret" 
                                               class="form-control" 
                                               value="<?php echo get_option('google_calendar_client_secret'); ?>"
                                               required>
                                        <p class="help-block">
                                            Your Google OAuth 2.0 Client Secret
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="google_calendar_redirect_uri" class="control-label">
                                            <i class="fa fa-link"></i> Google Authorization Redirect URI
                                        </label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   id="google_calendar_redirect_uri" 
                                                   name="google_calendar_redirect_uri" 
                                                   class="form-control" 
                                                   value="<?php echo get_option('google_calendar_redirect_uri') ?: site_url('ella_contractors/google_callback'); ?>"
                                                   readonly>
                                            <span class="input-group-addon" onclick="copyToClipboard('#google_calendar_redirect_uri')" style="cursor: pointer;">
                                                <i class="fa fa-copy"></i> Copy
                                            </span>
                                        </div>
                                        <p class="help-block">
                                            <strong>Add this exact URI</strong> to your Google Cloud Console → APIs & Services → Credentials → OAuth 2.0 Client IDs → Authorized redirect URIs
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Setup Instructions -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <h5><i class="fa fa-info-circle"></i> Setup Instructions:</h5>
                                        <ol>
                                            <li>Go to <a href="https://console.cloud.google.com/" target="_blank"><strong>Google Cloud Console</strong></a></li>
                                            <li>Create a new project or select an existing one</li>
                                            <li>Enable the <strong>Google Calendar API</strong></li>
                                            <li>Go to <strong>APIs & Services → Credentials</strong></li>
                                            <li>Click <strong>Create Credentials → OAuth 2.0 Client ID</strong></li>
                                            <li>Choose <strong>Web application</strong></li>
                                            <li>Add the <strong>Authorized redirect URI</strong> shown above</li>
                                            <li>Copy the <strong>Client ID</strong> and <strong>Client Secret</strong></li>
                                            <li>Paste them in the fields above and click <strong>Save Settings</strong></li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <hr />
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Save Settings
                                    </button>
                                    <a href="<?php echo admin_url('ella_contractors/appointments'); ?>" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Back to Appointments
                                    </a>
                                </div>
                            </div>
                        <?php echo form_close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Define admin_url if not already defined (for standalone module pages)
    if (typeof admin_url === 'undefined') {
        var admin_url = '<?php echo admin_url(); ?>';
    }
    
    $(document).ready(function() {
        // Handle form submission via AJAX
        $('#google-calendar-settings-form').on('submit', function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            var btnText = $btn.html();
            
            $btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
            
            // Use .serialize() to get all form data including CSRF token
            var formData = $form.serialize();
            
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        alert_float('success', response.message || 'Settings saved successfully!');
                    } else {
                        alert_float('danger', response.message || 'Failed to save settings.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Save error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText,
                        statusCode: xhr.status
                    });
                    
                    var errorMsg = 'Failed to save settings.';
                    if (xhr.status === 403) {
                        errorMsg = 'Session expired. Please refresh the page and try again.';
                    } else if (xhr.responseText && xhr.responseText.indexOf('page has expired') > -1) {
                        errorMsg = 'Session expired. Refreshing page...';
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else if (xhr.responseText) {
                        try {
                            var resp = JSON.parse(xhr.responseText);
                            errorMsg = resp.message || errorMsg;
                        } catch (e) {
                            // Not JSON, use default message
                        }
                    }
                    
                    alert_float('danger', errorMsg);
                },
                complete: function() {
                    $btn.html(btnText).prop('disabled', false);
                }
            });
            
            return false;
        });
        
        // Show/hide password toggle
        $('#google_calendar_client_secret').after(
            '<button type="button" class="btn btn-default btn-xs" onclick="togglePasswordVisibility()" style="margin-top: 5px;">' +
            '<i class="fa fa-eye"></i> Show/Hide' +
            '</button>'
        );
    });
    
    function togglePasswordVisibility() {
        var $input = $('#google_calendar_client_secret');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
        } else {
            $input.attr('type', 'password');
        }
    }
    
    function copyToClipboard(element) {
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val($(element).val()).select();
        document.execCommand('copy');
        $temp.remove();
        alert_float('success', 'Copied to clipboard!');
    }
</script>

<?php init_tail(); ?>

