# 🌐 Server Deployment Guide - Client Portal

## 📋 Quick Overview

This guide provides **complete instructions** to deploy the Ella Contractors Client Portal on your production server. The portal will work immediately after deployment with **no additional configuration required**.

## 🚀 What You Get

- ✅ **10-Tab Client Portal** with professional design
- ✅ **Real-time Data** from your database
- ✅ **Shareable Links** that never expire
- ✅ **Responsive Design** for all devices
- ✅ **No Login Required** for clients
- ✅ **Admin Panel Integration** with Share Portal buttons

## 📁 Files to Deploy

### **New Files (Copy to Server)**
```
application/controllers/Client_portal.php          ← NEW
application/views/client_portal.php               ← NEW  
modules/ella_contractors/assets/css/client_portal.css ← NEW
```

### **Files to Update (Edit on Server)**
```
application/config/routes.php                     ← ADD ROUTES
modules/ella_contractors/config/routes.php        ← ADD ROUTES
modules/ella_contractors/views/contracts_table.php ← UPDATE
modules/ella_contractors/views/view_contract.php  ← UPDATE
```

## 🔧 Step-by-Step Deployment

### **Step 1: Copy New Files**

```bash
# 1. Copy controller
scp Client_portal.php user@server:/path/to/dist-crm/application/controllers/

# 2. Copy view
scp client_portal.php user@server:/path/to/dist-crm/application/views/

# 3. Copy CSS
scp client_portal.css user@server:/path/to/dist-crm/modules/ella_contractors/assets/css/

# 4. Create CSS directory if needed
ssh user@server "mkdir -p /path/to/dist-crm/modules/ella_contractors/assets/css"
```

### **Step 2: Update Main Routes**

Edit `application/config/routes.php` on server:

```php
/**
 * Ella Contractors Client Portal Routes (Public Access)
 */
$route['client-portal/(:num)/(:any)'] = 'client_portal/index/$1/$2';
$route['client-portal/default/(:any)'] = 'client_portal/index/0/$1';
$route['client-portal/(:any)'] = 'client_portal/index/0/$1';
```

### **Step 3: Update Module Routes**

Edit `modules/ella_contractors/config/routes.php` on server:

```php
// Client Portal Routes (Public Access)
$route['modules/ella_contractors/client-portal/(:num)/(:any)'] = 'modules/ella_contractors/public_access/client_portal/$1/$2';
$route['modules/ella_contractors/client-portal/default/(:any)'] = 'modules/ella_contractors/public_access/client_portal/0/$1';
```

### **Step 4: Set File Permissions**

```bash
chmod 644 /path/to/dist-crm/application/controllers/Client_portal.php
chmod 644 /path/to/dist-crm/application/views/client_portal.php
chmod 644 /path/to/dist-crm/modules/ella_contractors/assets/css/client_portal.css
```

## 🌐 Server Configuration

### **Apache (.htaccess)**
Your existing `.htaccess` should work, but if you have issues, add:

```apache
# Allow client portal routes
RewriteRule ^client-portal/(.*)$ index.php?client-portal/$1 [L,QSA]
```

### **Nginx**
Add to your server block:

```nginx
location /client-portal {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 🧪 Testing After Deployment

### **Test Admin Panel**
1. Go to **Contracts Management**
2. Look for **"Share Portal"** buttons
3. Click button → Should generate link
4. Copy link → Should work

### **Test Client Portal**
1. Open generated link in browser
2. Should see **10 tabs** in sidebar
3. Click each tab → Content should change
4. **No 500 errors** should appear

### **Test URLs**
```
https://yourdomain.com/client-portal/default/test123
https://yourdomain.com/client-portal/1/test123
```

## 🐛 Troubleshooting

### **500 Internal Server Error**
```bash
# Check PHP logs
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log

# Check file permissions
ls -la /path/to/dist-crm/application/controllers/Client_portal.php
```

### **CSS Not Loading**
```bash
# Check file exists
ls -la /path/to/dist-crm/modules/ella_contractors/assets/css/client_portal.css

# Test web access
curl -I https://yourdomain.com/modules/ella_contractors/assets/css/client_portal.css
```

### **Routes Not Working**
```bash
# Check .htaccess
cat /path/to/dist-crm/.htaccess

# Verify mod_rewrite
apache2ctl -M | grep rewrite
```

## 📱 What Clients Will See

### **Portal Features**
- **Professional Design** with modern UI
- **10 Information Tabs** covering everything
- **Real Contract Data** from your database
- **Media Files** (if available)
- **Responsive Design** for mobile/tablet
- **No Login Required** - just open link

### **Tab Contents**
1. **Overview** - Contract details & client info
2. **Proposals** - Contract versions & status
3. **Gallery** - Project photos & documentation
4. **Presentations** - Project presentations
5. **Documents** - PDFs, Word docs, Excel files
6. **Media** - All media files with metadata
7. **Appointments** - Scheduled meetings & statuses
8. **Notes** - Project documentation & timeline
9. **Dimensions** - Technical specifications
10. **Estimates** - Cost breakdown & payments

## 🔒 Security Information

### **Current Status**
- ⚠️ **Links NEVER expire** - Work forever
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

## 📊 Database Requirements

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

## 🚀 Performance Notes

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

## 📞 Support & Maintenance

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

## 🎯 Success Criteria

### **Deployment Complete When:**
- ✅ **All files copied** to server
- ✅ **Routes updated** in both config files
- ✅ **Admin panel** Share Portal buttons work
- ✅ **Client portal URLs** accessible
- ✅ **All 10 tabs** functional
- ✅ **CSS styling** applied correctly
- ✅ **No errors** in browser console
- ✅ **No 500 errors** in server logs

### **Expected Result**
**Professional client portal that works immediately with no additional configuration!** 🚀

---

## 📋 Quick Reference

**Files to Copy**: 3 new files
**Files to Edit**: 4 existing files  
**Time Required**: 15-30 minutes
**Testing Required**: 10-15 minutes
**Result**: Working client portal

**The portal will work immediately after deployment!** ✨
