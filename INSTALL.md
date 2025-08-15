# Ella Contractors Module - Installation Guide

## 🚀 Quick Installation

The Ella Contractors module follows the same installation pattern as other CRM modules (like the Twilio Dialer module). It integrates seamlessly with your existing CRM system without affecting other modules or databases.

## 📋 Installation Steps

### 1. Copy Module Files
```bash
# Copy the ella_contractors folder to your CRM's modules directory
cp -r ella_contractors /path/to/your/crm/modules/
```

### 2. Install Dependencies
```bash
cd modules/ella_contractors
composer install
```

### 3. Activate Module in CRM
1. Log into your CRM admin panel
2. Go to **Setup > Modules**
3. Find "Ella Contractors" in the list
4. Click **Activate**

### 4. Database Tables (Automatic)
✅ **No manual database setup required!**

The module automatically creates its tables when activated:
- `tblella_contractors` - Contractor management
- `tblella_contracts` - Contract management  
- `tblella_projects` - Project tracking
- `tblella_payments` - Payment management
- `tblella_contractor_documents` - Document storage

## 🔒 Safety Features

- **Isolated Tables**: Uses `tblella_` prefix to avoid conflicts
- **CRM Integration**: Uses existing CRM database connection
- **No Data Loss**: Won't affect your existing data
- **Rollback Safe**: Can be deactivated without data loss

## 🎯 What Happens During Activation

1. **Tables Created**: All necessary database tables are created automatically
2. **Sample Data**: Basic sample contractors are added for testing
3. **Menu Integration**: Module appears in your CRM sidebar
4. **Permissions**: Basic permissions are set up

## 🧪 Testing the Installation

After activation, you can:

1. **Check Dashboard**: Navigate to "Ella Contractors > Dashboard"
2. **View Contractors**: See sample contractors in the system
3. **Test PDF Generation**: Generate sample PDFs and presentations
4. **Add New Data**: Create your first contractor

## 🚨 Troubleshooting

### Module Not Appearing
- Check if the module folder is in the correct location
- Verify file permissions (755 for folders, 644 for files)
- Check CRM error logs

### Database Errors
- Ensure your CRM database user has CREATE TABLE permissions
- Check if tables already exist (module won't overwrite existing data)

### PDF Generation Issues
- Verify Composer dependencies are installed
- Check file permissions for upload directories

## 🔄 Deactivation

To deactivate the module:
1. Go to **Setup > Modules**
2. Find "Ella Contractors"
3. Click **Deactivate**

**Note**: Deactivation only disables the module. Your data remains safe in the database.

## 📚 Next Steps

After successful installation:

1. **Customize Settings**: Configure module preferences
2. **Add Contractors**: Start building your contractor database
3. **Create Contracts**: Set up your first contracts
4. **Generate Documents**: Test PDF and presentation generation

## 🆘 Support

If you encounter issues:

1. Check this installation guide
2. Review the main README.md file
3. Check CRM error logs
4. Verify file permissions and database access

---

**The Ella Contractors module is designed to integrate seamlessly with your existing CRM system, just like the Twilio Dialer module!** 🎉
