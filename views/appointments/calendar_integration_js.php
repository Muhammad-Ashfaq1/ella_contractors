<script>
// ========================================
// CALENDAR INTEGRATION (DYNAMIC)
// Supports: Google Calendar, Outlook Calendar
// ========================================

/**
 * Calendar providers configuration
 */
var calendarProviders = {
    google: {
        name: 'Google Calendar',
        authUrl: 'ella_contractors/google_auth',
        setupUrl: 'https://console.cloud.google.com/',
        setupGroup: 'google',
        windowName: 'GoogleAuth'
    },
    outlook: {
        name: 'Outlook Calendar',
        authUrl: 'ella_contractors/outlook_auth',
        setupUrl: 'https://portal.azure.com/',
        setupGroup: 'outlook',
        windowName: 'outlookCalendarAuth'
    }
};

/**
 * Check calendar connection status (dynamic)
 * @param {string} provider - 'google' or 'outlook'
 */
function checkCalendarStatus(provider) {
    var config = calendarProviders[provider];
    if (!config) {
        console.error('Invalid calendar provider:', provider);
        return;
    }

    $.ajax({
        url: admin_url + config.authUrl + '/status',
        type: 'GET',
        dataType: 'json',
        data: {
            [csrf_token_name]: csrf_hash
        },
        success: function(response) {
            if (!response) {
                console.error(config.name + ': Invalid response from server');
                updateCalendarUI(provider, false);
                return;
            }

            // Check for error message
            if (response.error && response.message !== 'Not configured') {
                console.warn(config.name + ' status check:', response.error);
            }

            updateCalendarUI(provider, response.connected);
        },
        error: function(xhr, status, error) {
            console.error(config.name + ' status check failed:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            updateCalendarUI(provider, false);
        }
    });
}

/**
 * Update calendar UI elements (dynamic)
 * @param {string} provider - 'google' or 'outlook'
 * @param {boolean} connected - Connection status
 */
function updateCalendarUI(provider, connected) {
    var prefix = '#' + provider + '-calendar-';
    
    if (connected) {
        $(prefix + 'connect-item').hide();
        $(prefix + 'connected-item').show();
        $(prefix + 'sync-item').show();
        $(prefix + 'divider').show();
        $(prefix + 'disconnect-item').show();
    } else {
        $(prefix + 'connect-item').show();
        $(prefix + 'connected-item').hide();
        $(prefix + 'sync-item').hide();
        $(prefix + 'divider').hide();
        $(prefix + 'disconnect-item').hide();
    }
}

/**
 * Legacy wrapper for Google Calendar
 */
function checkGoogleCalendarStatus() {
    checkCalendarStatus('google');
}

/**
 * Legacy wrapper for Outlook Calendar
 */
function checkOutlookCalendarStatus() {
    checkCalendarStatus('outlook');
}

/**
 * Store popup references
 */
var calendarAuthPopups = {
    google: null,
    outlook: null
};

/**
 * Connect calendar button click (dynamic)
 */
$(document).on('click', '[id$="-calendar-connect-btn"]', function(e) {
    e.preventDefault();
    
    // Extract provider from button ID (google-calendar-connect-btn → google)
    var buttonId = $(this).attr('id');
    var provider = buttonId.replace('-calendar-connect-btn', '');
    var config = calendarProviders[provider];
    
    if (!config) {
        console.error('Invalid calendar provider from button:', buttonId);
        return;
    }
    
    // Check if credentials are configured
    $.ajax({
        url: admin_url + config.authUrl + '/status',
        type: 'GET',
        dataType: 'json',
        data: {
            [csrf_token_name]: csrf_hash
        },
        success: function(response) {
            if (!response) {
                console.warn(config.name + ': Invalid response, attempting connection anyway');
                openCalendarAuthPopup(provider);
                return;
            }

            if (response && response.error && response.message === 'Not configured') {
                // Credentials not configured
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: config.name + ' Not Configured',
                        html: 'Please configure ' + config.name + ' API credentials in <strong>Setup → Settings → ' + config.setupGroup.charAt(0).toUpperCase() + config.setupGroup.slice(1) + '</strong> first.<br><br><strong>Setup Steps:</strong><br>1. Go to <a href="' + config.setupUrl + '" target="_blank">' + (provider === 'google' ? 'Google Cloud Console' : 'Azure Portal') + '</a><br>2. Create OAuth 2.0 application<br>3. Add credentials in Settings<br><br><a href="' + admin_url + 'settings?group=' + config.setupGroup + '" class="btn btn-sm btn-primary" style="color: white; margin-top: 10px;"><i class="fa fa-cog"></i> Go to Settings</a>',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        width: '600px'
                    });
                } else {
                    alert('Please configure ' + config.name + ' API credentials in Setup → Settings → ' + config.setupGroup.charAt(0).toUpperCase() + config.setupGroup.slice(1) + ' first.');
                }
            } else {
                // Credentials configured - proceed to OAuth
                openCalendarAuthPopup(provider);
            }
        },
        error: function(xhr, status, error) {
            console.error(config.name + ' status check failed:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            
            // Show warning but allow user to proceed
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Warning',
                    text: 'Unable to verify ' + config.name + ' configuration. Do you want to proceed anyway?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Continue',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        openCalendarAuthPopup(provider);
                    }
                });
            } else {
                if (confirm('Unable to verify ' + config.name + ' configuration. Do you want to proceed anyway?')) {
                    openCalendarAuthPopup(provider);
                }
            }
        }
    });
});

/**
 * Open calendar OAuth popup (dynamic)
 * @param {string} provider - 'google' or 'outlook'
 */
function openCalendarAuthPopup(provider) {
    var config = calendarProviders[provider];
    if (!config) {
        console.error('Invalid calendar provider:', provider);
        return;
    }

    var authUrl = admin_url + config.authUrl + '/connect';
    var width = 600;
    var height = 700;
    var left = (screen.width / 2) - (width / 2);
    var top = (screen.height / 2) - (height / 2);
    
    calendarAuthPopups[provider] = window.open(
        authUrl,
        config.windowName,
        'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes'
    );
    
    if (!calendarAuthPopups[provider]) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Popup Blocked',
                text: 'Please allow popups for this site to connect to ' + config.name + '.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            alert('Please allow popups for this site to connect to ' + config.name + '.');
        }
    } else if (!calendarAuthPopups[provider].closed) {
        calendarAuthPopups[provider].focus();
    }
}

/**
 * Legacy wrappers for backward compatibility
 */
var googleAuthPopup = null;
function openGoogleAuthPopup() {
    openCalendarAuthPopup('google');
    googleAuthPopup = calendarAuthPopups.google;
}

var outlookAuthPopup = null;
function openOutlookAuthPopup() {
    openCalendarAuthPopup('outlook');
    outlookAuthPopup = calendarAuthPopups.outlook;
}

/**
 * Listen for OAuth callback messages (dynamic - handles both Google and Outlook)
 */
window.addEventListener('message', function(event) {
    // Verify origin for security
    if (event.origin !== window.location.origin) {
        return;
    }
    
    if (!event.data || !event.data.type) {
        return;
    }
    
    // Extract provider from message type (google_calendar_auth_success → google)
    var messageType = event.data.type;
    var provider = null;
    
    if (messageType.indexOf('google_calendar') === 0) {
        provider = 'google';
    } else if (messageType.indexOf('outlook_calendar') === 0) {
        provider = 'outlook';
    }
    
    if (!provider) {
        return;
    }
    
    var config = calendarProviders[provider];
    var isSuccess = messageType.indexOf('_success') !== -1;
    var isError = messageType.indexOf('_error') !== -1;
    
    // Handle success
    if (isSuccess) {
        console.log(config.name + ' authentication successful');
        
        // Close popup if still open
        if (calendarAuthPopups[provider] && !calendarAuthPopups[provider].closed) {
            calendarAuthPopups[provider].close();
        }
        
        // Refresh connection status
        setTimeout(function() {
            checkCalendarStatus(provider);
        }, 1000);
        
        // Show success message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Success!',
                text: event.data.message || (config.name + ' connected successfully. Your appointments will now sync automatically.'),
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert_float('success', event.data.message || (config.name + ' connected successfully!'));
        }
    }
    
    // Handle error
    if (isError) {
        console.error(config.name + ' authentication failed:', event.data.message);
        
        // Close popup if still open
        if (calendarAuthPopups[provider] && !calendarAuthPopups[provider].closed) {
            calendarAuthPopups[provider].close();
        }
        
        // Show error message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Authentication Failed',
                text: event.data.message || ('Failed to connect ' + config.name + '. Please try again.'),
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            alert_float('danger', event.data.message || ('Failed to connect ' + config.name));
        }
    }
}, false);

/**
 * Sync Now button click (dynamic - handles both Google and Outlook)
 */
$(document).on('click', '[id$="-calendar-sync-now"]', function(e) {
    e.preventDefault();
    
    // Extract provider from button ID (google-calendar-sync-now → google)
    var buttonId = $(this).attr('id');
    var provider = buttonId.replace('-calendar-sync-now', '');
    var config = calendarProviders[provider];
    
    if (!config) {
        console.error('Invalid calendar provider from button:', buttonId);
        return;
    }
    
    var $btn = $(this);
    var originalHtml = $btn.html();
    $btn.html('<i class="fa fa-spinner fa-spin"></i> Syncing...');
    $btn.parent().addClass('disabled');
    
    $.ajax({
        url: admin_url + config.authUrl + '/sync_now',
        type: 'POST',
        dataType: 'json',
        data: {
            [csrf_token_name]: csrf_hash
        },
        success: function(response) {
            if (response && response.success) {
                var message = response.message || 'Sync completed successfully';
                if (response.synced !== undefined) {
                    message += ' (' + response.synced + ' synced';
                    if (response.failed > 0) {
                        message += ', ' + response.failed + ' failed';
                    }
                    message += ')';
                }
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Sync Complete',
                        text: message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    alert_float('success', message);
                }
            } else {
                var errorMsg = response && response.message ? response.message : 'Failed to sync appointments';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Sync Failed',
                        text: errorMsg,
                        icon: 'error'
                    });
                } else {
                    alert_float('danger', errorMsg);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error(config.name + ' sync failed:', error);
            var errorMsg = 'An error occurred during sync. Please try again.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Sync Error',
                    text: errorMsg,
                    icon: 'error'
                });
            } else {
                alert_float('danger', errorMsg);
            }
        },
        complete: function() {
            $btn.html(originalHtml);
            $btn.parent().removeClass('disabled');
        }
    });
});

/**
 * Disconnect calendar button click (dynamic - handles both Google and Outlook)
 */
$(document).on('click', '[id$="-calendar-disconnect"]', function(e) {
    e.preventDefault();
    
    // Extract provider from button ID (google-calendar-disconnect → google)
    var buttonId = $(this).attr('id');
    var provider = buttonId.replace('-calendar-disconnect', '');
    var config = calendarProviders[provider];
    
    if (!config) {
        console.error('Invalid calendar provider from button:', buttonId);
        return;
    }
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Disconnect ' + config.name + '?',
            text: 'This will remove your ' + config.name + ' connection and stop syncing appointments. Existing calendar events will not be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#95a5a6',
            confirmButtonText: 'Yes, Disconnect',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                disconnectCalendar(provider);
            }
        });
    } else {
        if (confirm('Are you sure you want to disconnect ' + config.name + '? This will stop syncing appointments.')) {
            disconnectCalendar(provider);
        }
    }
});

/**
 * Disconnect calendar (dynamic - handles both Google and Outlook)
 * @param {string} provider - 'google' or 'outlook'
 */
function disconnectCalendar(provider) {
    var config = calendarProviders[provider];
    if (!config) {
        console.error('Invalid calendar provider:', provider);
        return;
    }

    $.ajax({
        url: admin_url + config.authUrl + '/disconnect',
        type: 'POST',
        dataType: 'json',
        data: {
            [csrf_token_name]: csrf_hash
        },
        success: function(response) {
            if (response && response.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Disconnected',
                        text: response.message || (config.name + ' disconnected successfully'),
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert_float('success', response.message || (config.name + ' disconnected successfully'));
                }
                
                // Refresh UI
                setTimeout(function() {
                    checkCalendarStatus(provider);
                }, 1000);
            } else {
                var errorMsg = response && response.message ? response.message : 'Failed to disconnect';
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: errorMsg,
                        icon: 'error'
                    });
                } else {
                    alert_float('danger', errorMsg);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error(config.name + ' disconnect failed:', error);
            var errorMsg = 'An error occurred while disconnecting. Please try again.';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Error',
                    text: errorMsg,
                    icon: 'error'
                });
            } else {
                alert_float('danger', errorMsg);
            }
        }
    });
}

/**
 * Open calendar OAuth popup (dynamic)
 * @param {string} provider - 'google' or 'outlook'
 */
function openCalendarAuthPopup(provider) {
    var config = calendarProviders[provider];
    if (!config) {
        console.error('Invalid calendar provider:', provider);
        return;
    }

    var authUrl = admin_url + config.authUrl + '/connect';
    var width = 600;
    var height = 700;
    var left = (screen.width / 2) - (width / 2);
    var top = (screen.height / 2) - (height / 2);
    
    calendarAuthPopups[provider] = window.open(
        authUrl,
        config.windowName,
        'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes'
    );
    
    if (!calendarAuthPopups[provider]) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Popup Blocked',
                text: 'Please allow popups for this site to connect to ' + config.name + '.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            alert('Please allow popups for this site to connect to ' + config.name + '.');
        }
    } else if (!calendarAuthPopups[provider].closed) {
        calendarAuthPopups[provider].focus();
    }
}

/**
 * Legacy wrappers for backward compatibility
 */
function disconnectGoogleCalendar() {
    disconnectCalendar('google');
}

function disconnectOutlookCalendar() {
    disconnectCalendar('outlook');
}

// ========================================
// END CALENDAR INTEGRATION (DYNAMIC)
// ========================================
</script>


