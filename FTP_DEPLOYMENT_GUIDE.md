# 🚀 **FTP DEPLOYMENT GUIDE - Client Portal**

## 📋 **Quick Overview**

This guide provides **complete step-by-step instructions** to deploy the Ella Contractors Client Portal to your production server using FTP. The portal will work immediately after deployment with **no additional configuration required**.

---

## 🎯 **What You're Deploying**

### **✅ Complete Client Portal System**
- **10-Tab Professional Interface** with modern design
- **Real-time Data Integration** from your database
- **Shareable Links** that never expire
- **Responsive Design** for all devices
- **No Login Required** for clients
- **Admin Panel Integration** with Share Portal buttons

### **✅ What Clients Will See**
1. **Overview** - Contract details & client information
2. **Proposals** - Contract versions & status
3. **Gallery** - Project photos & documentation
4. **Presentations** - Project presentations
5. **Documents** - PDFs, Word docs, Excel files
6. **Media** - All media files with metadata
7. **Appointments** - Scheduled meetings & statuses
8. **Notes** - Project documentation & timeline
9. **Dimensions** - Technical specifications
10. **Estimates** - Cost breakdown & payments

---

## 📁 **FILES TO UPLOAD TO SERVER (3 NEW FILES)**

### **1. 🎮 Client Portal Controller**
**Local File**: `application/controllers/Client_portal.php`
**Server Path**: `application/controllers/Client_portal.php`
**Action**: Upload this file to your server

### **2. 🎨 Client Portal View**
**Local File**: `application/views/client_portal.php`
**Server Path**: `application/views/client_portal.php`
**Action**: Upload this file to your server

### **3. 🎨 Client Portal CSS**
**Local File**: `modules/ella_contractors/assets/css/client_portal.css`
**Server Path**: `modules/ella_contractors/assets/css/client_portal.css`
**Action**: Upload this file to your server

---

## 🔧 **FILES TO UPDATE ON SERVER (4 EXISTING FILES)**

### **1. 📍 Main Application Routes**
**File**: `application/config/routes.php`
**Action**: Add these lines to your server's routes file

### **2. 📍 Module Routes**
**File**: `modules/ella_contractors/config/routes.php`
**Action**: Add these lines to your server's module routes file

### **3. 📍 Contracts Table View**
**File**: `modules/ella_contractors/views/contracts_table.php`
**Action**: Update the JavaScript functions in this file

### **4. 📍 View Contract View**
**File**: `modules/ella_contractors/views/view_contract.php`
**Action**: Update the JavaScript functions in this file

---

## 🚀 **STEP-BY-STEP FTP DEPLOYMENT**

### **Step 1: Upload New Files via FTP**

#### **1.1 Upload Client Portal Controller**
```
Source: application/controllers/Client_portal.php
Destination: /path/to/your/server/application/controllers/Client_portal.php
```

#### **1.2 Upload Client Portal View**
```
Source: application/views/client_portal.php
Destination: /path/to/your/server/application/views/client_portal.php
```

#### **1.3 Upload Client Portal CSS**
```
Source: modules/ella_contractors/assets/css/client_portal.css
Destination: /path/to/your/server/modules/ella_contractors/assets/css/client_portal.css
```

**Note**: Create the `assets/css` directory if it doesn't exist on your server.

---

### **Step 2: Update Routes Configuration**

#### **2.1 Update Main Application Routes**
**File**: `application/config/routes.php` on your server

**Add these lines** (you can add them anywhere in the file, preferably near the end):

```php
/**
 * Ella Contractors Client Portal Routes (Public Access)
 */
$route['client-portal/(:num)/(:any)'] = 'client_portal/index/$1/$2';
$route['client-portal/default/(:any)'] = 'client_portal/index/0/$1';
$route['client-portal/(:any)'] = 'client_portal/index/0/$1';
```

#### **2.2 Update Module Routes**
**File**: `modules/ella_contractors/config/routes.php` on your server

**Add these lines**:

```php
// Client Portal Routes (Public Access)
$route['modules/ella_contractors/client-portal/(:num)/(:any)'] = 'modules/ella_contractors/public_access/client_portal/$1/$2';
$route['modules/ella_contractors/client-portal/default/(:any)'] = 'modules/ella_contractors/public_access/client_portal/0/$1';
```

---

### **Step 3: Update Admin Views**

#### **3.1 Update Contracts Table View**
**File**: `modules/ella_contractors/views/contracts_table.php` on your server

**Find and REPLACE** the existing `copyShareableLink` function with:

```javascript
function copyShareableLink(contractId) {
    var shareableUrl = site_url('client-portal') + '/' + contractId + '/' + generateContractHash(contractId);
    
    // Copy to clipboard
    navigator.clipboard.writeText(shareableUrl).then(function() {
        // Show success message
        Swal.fire({
            title: 'Success!',
            text: 'Shareable client portal link copied to clipboard!',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        // Fallback: show the URL
        Swal.fire({
            title: 'Shareable Link',
            html: '<p>Copy this link:</p><input type="text" value="' + shareableUrl + '" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0;">',
            icon: 'info',
            confirmButtonText: 'Copy',
            showCancelButton: true,
            cancelButtonText: 'Close'
        });
    });
}
```

**Find and REPLACE** the existing `copyDefaultMediaLink` function with:

```javascript
function copyDefaultMediaLink() {
    var url = site_url("client-portal/default") + '/' + generateDefaultMediaHash();
    
    navigator.clipboard.writeText(url).then(function() {
        Swal.fire({
            title: 'Success!',
            text: 'Default client portal link copied to clipboard!',
            icon: 'success',
            confirmButtonText: 'OK',
            showCancelButton: true,
            cancelButtonText: 'Open Portal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Do nothing, just close
            } else {
                // Open the portal
                window.open(url, '_blank');
            }
        });
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        Swal.fire({
            title: 'Default Portal Link',
            html: '<p>Copy this link:</p><input type="text" value="' + url + '" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0;">',
            icon: 'info',
            confirmButtonText: 'Copy',
            showCancelButton: true,
            cancelButtonText: 'Open Portal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Do nothing, just close
            } else {
                // Open the portal
                window.open(url, '_blank');
            }
        });
    });
}
```

#### **3.2 Update View Contract View**
**File**: `modules/ella_contractors/views/view_contract.php` on your server

**Find and REPLACE** the existing `copyShareableLink` function with:

```javascript
function copyShareableLink(contractId) {
    var shareableUrl = site_url('client-portal') + '/' + contractId + '/' + generateContractHash(contractId);
    
    navigator.clipboard.writeText(shareableUrl).then(function() {
        Swal.fire({
            title: 'Success!',
            text: 'Shareable client portal link copied to clipboard!',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        Swal.fire({
            title: 'Shareable Link',
            html: '<p>Copy this link:</p><input type="text" value="' + shareableUrl + '" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0;">',
            icon: 'info',
            confirmButtonText: 'Copy',
            showCancelButton: true,
            cancelButtonText: 'Close'
        });
    });
}
```

---

### **Step 4: Set File Permissions**

**Run these commands** on your server (via SSH or file manager):

```bash
chmod 644 /path/to/your/server/application/controllers/Client_portal.php
chmod 644 /path/to/your/server/application/views/client_portal.php
chmod 644 /path/to/your/server/modules/ella_contractors/assets/css/client_portal.css
```

---

## 🧪 **TESTING AFTER DEPLOYMENT**

### **Test 1: Admin Panel**
1. **Go to** Contracts Management in your admin panel
2. **Look for** "Share Portal" buttons on contracts
3. **Click** any "Share Portal" button
4. **Verify** a link is generated and copied

### **Test 2: Client Portal**
1. **Open** the generated link in a new browser tab
2. **Verify** the portal loads without errors
3. **Check** all 10 tabs are visible in sidebar
4. **Click** each tab to verify content changes
5. **Test** responsive design on mobile/tablet

### **Test 3: URLs**
**Test these URLs** (replace with your actual domain):

```
https://yourdomain.com/client-portal/default/test123
https://yourdomain.com/client-portal/1/test123
```

**Expected Result**: Both URLs should open the client portal without errors.

---

## 🐛 **TROUBLESHOOTING COMMON ISSUES**

### **Issue 1: 500 Internal Server Error**
**Symptoms**: White page or 500 error when accessing client portal

**Solutions**:
```bash
# Check PHP error logs
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log

# Verify file permissions
ls -la /path/to/your/server/application/controllers/Client_portal.php

# Check if file uploaded completely
wc -l /path/to/your/server/application/controllers/Client_portal.php
```

### **Issue 2: CSS Not Loading**
**Symptoms**: Portal loads but looks unstyled/plain

**Solutions**:
```bash
# Check if CSS file exists
ls -la /path/to/your/server/modules/ella_contractors/assets/css/client_portal.css

# Test web access to CSS file
curl -I https://yourdomain.com/modules/ella_contractors/assets/css/client_portal.css

# Verify file permissions
chmod 644 /path/to/your/server/modules/ella_contractors/assets/css/client_portal.css
```

### **Issue 3: Routes Not Working**
**Symptoms**: 404 errors when accessing client portal URLs

**Solutions**:
```bash
# Check if routes file was updated
grep "client-portal" /path/to/your/server/application/config/routes.php

# Verify .htaccess file
cat /path/to/your/server/.htaccess

# Check if mod_rewrite is enabled
apache2ctl -M | grep rewrite
```

### **Issue 4: Tabs Not Switching**
**Symptoms**: Portal loads but clicking tabs doesn't change content

**Solutions**:
1. **Check browser console** for JavaScript errors
2. **Verify Bootstrap JS** is loading
3. **Test with different browsers**
4. **Clear browser cache** and cookies

---

## 🌐 **SERVER CONFIGURATION**

### **Apache (.htaccess)**
Your existing `.htaccess` should work, but if you have issues, add:

```apache
# Allow client portal routes
RewriteRule ^client-portal/(.*)$ index.php?client-portal/$1 [L,QSA]
```

### **Nginx**
If using Nginx, add to your server block:

```nginx
location /client-portal {
    try_files $uri $uri/ /index.php?$query_string;
}
```

---

## 🔒 **SECURITY INFORMATION**

### **Current Security Status**
- ⚠️ **Links NEVER expire** - Work indefinitely
- ⚠️ **No user authentication** required
- ⚠️ **Anyone with link** can access
- ⚠️ **Admin can't revoke** access

### **How It Works**
1. **Admin generates link** from admin panel
2. **Client receives link** via email/SMS
3. **Client opens link** in any browser
4. **Portal displays** all contract information
5. **Client shares link** with team/consultants
6. **Link works indefinitely** until admin manually removes

---

## 📊 **DATABASE REQUIREMENTS**

### **Required Tables**
```sql
-- Should already exist in your system
tblproposals          ← Contract information
ella_contractor_media ← Media files
```

### **No Database Changes Required**
- **Existing tables** work immediately
- **No new columns** need to be added
- **No data migration** required
- **Portal works** with current data

---

## 🚀 **PERFORMANCE NOTES**

### **Optimizations**
- **External CSS** - Faster loading
- **Minimal JavaScript** - Lightweight
- **Efficient queries** - Database optimized
- **Responsive images** - Mobile friendly

### **Server Requirements**
- **PHP 7.4+** (recommended)
- **MySQL 5.7+** (should work)
- **mod_rewrite** enabled (Apache)
- **File permissions** correct

---

## 📞 **SUPPORT & MAINTENANCE**

### **After Deployment**
- **Monitor error logs** for issues
- **Test on different devices** and browsers
- **Verify admin panel** functionality
- **Check client portal** accessibility

### **Regular Maintenance**
- **Keep CodeIgniter updated**
- **Monitor security patches**
- **Backup database regularly**
- **Check file permissions**

---

## 🎯 **DEPLOYMENT SUCCESS CRITERIA**

### **Deployment Complete When:**
- ✅ **All 3 new files uploaded** to server
- ✅ **Routes updated** in both config files
- ✅ **Admin views updated** with new JavaScript
- ✅ **Share Portal buttons** work in admin panel
- ✅ **Client portal URLs** accessible without errors
- ✅ **All 10 tabs** functional and responsive
- ✅ **CSS styling** applied correctly
- ✅ **No errors** in browser console
- ✅ **No 500 errors** in server logs

### **Expected Result**
**Professional client portal that works immediately with no additional configuration!** 🚀

---

## 📋 **QUICK REFERENCE**

**Files to Upload**: 3 new files
**Files to Edit**: 4 existing files  
**Time Required**: 15-30 minutes
**Testing Required**: 10-15 minutes
**Result**: Working client portal

**The portal will work immediately after deployment!** ✨

---

## 🆘 **GETTING HELP**

If you encounter issues:

1. **Check error logs** first (PHP and web server)
2. **Verify file permissions** are correct
3. **Test with different browsers** and devices
4. **Check server configuration** (Apache/Nginx)
5. **Use the deployment checklist** for verification

**Remember**: The client portal is designed to work immediately after deployment with no additional configuration required!

---

## 🎉 **CONGRATULATIONS!**

Once you complete all the steps above, you'll have a **fully functional, professional client portal** that:

- ✅ **Replaces the old media library**
- ✅ **Provides comprehensive contract information**
- ✅ **Works on all devices**
- ✅ **Requires no client login**
- ✅ **Generates shareable links**
- ✅ **Integrates with your admin panel**

**Your clients will love the new professional interface!** 🚀
