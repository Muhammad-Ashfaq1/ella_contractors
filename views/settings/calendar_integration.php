<?php defined('BASEPATH') or exit('No direct script access allowed');

// Load current settings
$google_calendar_client_id = get_option('google_calendar_client_id');
$google_calendar_client_secret = get_option('google_calendar_client_secret');
$google_calendar_redirect_uri = get_option('google_calendar_redirect_uri');

$outlook_calendar_client_id = get_option('outlook_calendar_client_id');
$outlook_calendar_client_secret = get_option('outlook_calendar_client_secret');
$outlook_calendar_tenant_id = get_option('outlook_calendar_tenant_id');
$outlook_calendar_redirect_uri = get_option('outlook_calendar_redirect_uri');
?>

<div class="horizontal-scrollable-tabs">
    <div class="horizontal-tabs">
        <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
            <li role="presentation" class="active">
                <a href="#google_calendar" aria-controls="google_calendar" role="tab" data-toggle="tab">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="16" height="16" style="vertical-align: middle; margin-right: 6px;">
                        <rect width="22" height="22" x="13" y="13" fill="#fff"/>
                        <polygon fill="#1e88e5" points="25.68,20.92 26.688,22.36 28.272,21.208 28.272,29.56 30,29.56 30,18.616 28.56,18.616"/>
                        <path fill="#1e88e5" d="M22.943,23.745c0.625-0.574,1.013-1.37,1.013-2.249c0-1.747-1.533-3.168-3.417-3.168 c-1.602,0-2.972,1.009-3.33,2.453l1.657,0.421c0.165-0.664,0.868-1.146,1.673-1.146c0.942,0,1.709,0.646,1.709,1.44 c0,0.794-0.767,1.44-1.709,1.44h-0.997v1.728h0.997c1.081,0,1.993,0.751,1.993,1.64c0,0.904-0.866,1.64-1.931,1.64 c-0.962,0-1.784-0.61-1.914-1.418L17,26.802c0.262,1.636,1.81,2.87,3.6,2.87c2.007,0,3.64-1.511,3.64-3.368 C24.24,25.281,23.736,24.363,22.943,23.745z"/>
                        <polygon fill="#fbc02d" points="34,42 14,42 13,38 35,38"/>
                        <polygon fill="#4caf50" points="38,35 10,35 11,38 37,38"/>
                        <path fill="#1e88e5" d="M34,14l1-4l-1-4H9C7.343,6,6,7.343,6,9v25l4,1l4-1V16c0-1.105,0.895-2,2-2H34z"/>
                        <polygon fill="#e53935" points="34,34 34,42 42,34"/>
                        <path fill="#1565c0" d="M39,6h-5v8h8V9C42,7.343,40.657,6,39,6z"/>
                        <path fill="#1565c0" d="M9,42h5v-8H6v5C6,40.657,7.343,42,9,42z"/>
                    </svg>
                    Google Calendar
                </a>
            </li>
            <li role="presentation">
                <a href="#outlook_calendar" aria-controls="outlook_calendar" role="tab" data-toggle="tab">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="16" height="16" style="vertical-align: middle; margin-right: 6px;">
                        <path fill="#03A9F4" d="M24,4C13,4,4,13,4,24s9,20,20,20s20-9,20-20S35,4,24,4z"/>
                        <path fill="#FFF" d="M24,34.3c-5.7,0-10.3-4.6-10.3-10.3S18.3,13.7,24,13.7S34.3,18.3,34.3,24S29.7,34.3,24,34.3z M24,16.7 c-4,0-7.3,3.3-7.3,7.3s3.3,7.3,7.3,7.3s7.3-3.3,7.3-7.3S28,16.7,24,16.7z"/>
                        <path fill="#FFF" d="M24,28c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S26.2,28,24,28z M24,22c-1.1,0-2,0.9-2,2s0.9,2,2,2 s2-0.9,2-2S25.1,22,24,22z"/>
                    </svg>
                    Outlook Calendar
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content">
    <!-- Google Calendar Tab -->
    <div role="tabpanel" class="tab-pane active" id="google_calendar">
        <h4 class="bold mtop20">
            <i class="fa fa-google"></i> Google Calendar API Configuration
        </h4>
        <p class="text-muted">Configure Google Calendar API credentials for EllaContractors appointment synchronization</p>
        <hr />
        
        <div class="form-group">
            <label for="google_calendar_client_id">Google Client ID</label>
            <input type="text" 
                   id="google_calendar_client_id" 
                   name="settings[google_calendar_client_id]" 
                   class="form-control" 
                   value="<?php echo htmlspecialchars($google_calendar_client_id); ?>"
                   placeholder="123456789-abcdefg.apps.googleusercontent.com">
            <p class="text-muted mtop5"><small>From Google Cloud Console → APIs & Services → Credentials</small></p>
        </div>
        
        <div class="form-group">
            <label for="google_calendar_client_secret">Google Client Secret</label>
            <input type="text" 
                   id="google_calendar_client_secret" 
                   name="settings[google_calendar_client_secret]" 
                   class="form-control" 
                   value="<?php echo htmlspecialchars($google_calendar_client_secret); ?>"
                   placeholder="GOCSPX-xxxxxxxxxxxxxxxxxxxxx">
            <p class="text-muted mtop5"><small>Keep this secret secure</small></p>
        </div>
        
        <div class="form-group">
            <label for="google_calendar_redirect_uri">Google Redirect URI</label>
            <input type="text" 
                   id="google_calendar_redirect_uri" 
                   name="settings[google_calendar_redirect_uri]" 
                   class="form-control" 
                   value="<?php echo htmlspecialchars($google_calendar_redirect_uri); ?>"
                   placeholder="<?php echo site_url('ella_contractors/google_auth/callback'); ?>">
            <p class="text-muted mtop5"><small>Add this URL to "Authorized redirect URIs" in Google Cloud Console</small></p>
        </div>
        
        <div class="alert alert-info">
            <strong><i class="fa fa-info-circle"></i> Setup Instructions:</strong>
            <ol style="margin-bottom: 0; padding-left: 20px; margin-top: 10px;">
                <li>Go to <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a></li>
                <li>Create OAuth 2.0 Client ID (Application type: Web application)</li>
                <li>Add the Redirect URI above to "Authorized redirect URIs"</li>
                <li>Enable Google Calendar API in APIs & Services → Library</li>
                <li>Copy Client ID and Client Secret to the fields above</li>
                <li>Click <strong>Save</strong> button at bottom of this page</li>
            </ol>
        </div>
        
        <div class="alert alert-success">
            <strong><i class="fa fa-check-circle"></i> After Setup:</strong>
            <p style="margin-bottom: 0; margin-top: 10px;">Go to <strong>EllaContractor → Appointments</strong> and click the <strong>🚀 Integrations</strong> button to connect your Google Calendar.</p>
        </div>
    </div>
    
    <!-- Outlook Calendar Tab -->
    <div role="tabpanel" class="tab-pane" id="outlook_calendar">
        <h4 class="bold mtop20">
            <i class="fa fa-windows"></i> Outlook Calendar API Configuration
        </h4>
        <p class="text-muted">Configure Microsoft Azure AD credentials for Outlook Calendar synchronization</p>
        <hr />
        
        <div class="form-group">
            <label for="outlook_calendar_client_id">Outlook Client ID (Application ID)</label>
            <input type="text" 
                   id="outlook_calendar_client_id" 
                   name="settings[outlook_calendar_client_id]" 
                   class="form-control" 
                   value="<?php echo htmlspecialchars($outlook_calendar_client_id); ?>"
                   placeholder="12345678-1234-1234-1234-123456789abc">
            <p class="text-muted mtop5"><small>From Azure Portal → App Registrations → Application (client) ID</small></p>
        </div>
        
        <div class="form-group">
            <label for="outlook_calendar_client_secret">Outlook Client Secret</label>
            <input type="text" 
                   id="outlook_calendar_client_secret" 
                   name="settings[outlook_calendar_client_secret]" 
                   class="form-control" 
                   value="<?php echo htmlspecialchars($outlook_calendar_client_secret); ?>"
                   placeholder="abc~123XYZ...">
            <p class="text-muted mtop5"><small>From Azure Portal → Certificates & secrets → Client secrets</small></p>
        </div>
        
        <div class="form-group">
            <label for="outlook_calendar_tenant_id">Outlook Tenant ID</label>
            <input type="text" 
                   id="outlook_calendar_tenant_id" 
                   name="settings[outlook_calendar_tenant_id]" 
                   class="form-control" 
                   value="<?php echo htmlspecialchars($outlook_calendar_tenant_id); ?>"
                   placeholder="common">
            <p class="text-muted mtop5"><small>Use "common" for multi-tenant or your specific Tenant ID from Azure Portal → Overview</small></p>
        </div>
        
        <div class="form-group">
            <label for="outlook_calendar_redirect_uri">Outlook Redirect URI</label>
            <input type="text" 
                   id="outlook_calendar_redirect_uri" 
                   name="settings[outlook_calendar_redirect_uri]" 
                   class="form-control" 
                   value="<?php echo htmlspecialchars($outlook_calendar_redirect_uri); ?>"
                   placeholder="<?php echo site_url('ella_contractors/outlook_auth/callback'); ?>">
            <p class="text-muted mtop5"><small>Add this URL to "Redirect URIs" in Azure Portal → Authentication</small></p>
        </div>
        
        <div class="alert alert-warning">
            <strong><i class="fa fa-info-circle"></i> Setup Instructions:</strong>
            <ol style="margin-bottom: 0; padding-left: 20px; margin-top: 10px;">
                <li>Go to <a href="https://portal.azure.com/#blade/Microsoft_AAD_RegisteredApps/ApplicationsListBlade" target="_blank">Azure Portal - App Registrations</a></li>
                <li>Create new registration or use existing app</li>
                <li>Add the Redirect URI above to Authentication → Web → Redirect URIs</li>
                <li>Go to API permissions → Add: <code>Calendars.ReadWrite</code>, <code>User.Read</code>, <code>offline_access</code></li>
                <li>Grant admin consent for your organization</li>
                <li>Go to Certificates & secrets → Create new client secret</li>
                <li>Copy Application (client) ID and Client Secret to the fields above</li>
                <li>Click <strong>Save</strong> button at bottom of this page</li>
            </ol>
        </div>
        
        <div class="alert alert-success">
            <strong><i class="fa fa-check-circle"></i> After Setup:</strong>
            <p style="margin-bottom: 5px; margin-top: 10px;">Go to <strong>EllaContractor → Appointments</strong> and click the <strong>🚀 Integrations</strong> button to connect your Outlook Calendar.</p>
            <p style="margin-bottom: 0;"><strong>Required API Permissions:</strong> <code>Calendars.ReadWrite</code>, <code>User.Read</code>, <code>offline_access</code></p>
        </div>
    </div>
</div>

<style>
/* Tab styling to match Perfex CRM */
.nav-tabs-horizontal > li > a svg {
    vertical-align: middle;
}
</style>

