# Ella Contractors Module

A comprehensive contractor management module for Ella CRM with full CRUD functionality, PDF generation, and document management.

## 🚀 Features

### Core Functionality
- **Contractor Management**: Add, edit, delete, and manage contractor information
- **Contract Management**: Create and manage contracts with contractors
- **Project Management**: Track projects and their progress
- **Payment Management**: Handle invoices and payment tracking
- **Document Management**: Upload, organize, and share documents
- **PDF Generation**: Generate professional PDFs for contracts, invoices, and reports
- **Presentation Generation**: Create PowerPoint presentations for contractors and projects

### Technical Features
- **Full CRUD Operations**: Complete Create, Read, Update, Delete functionality
- **Search & Filtering**: Advanced search and filtering capabilities
- **Pagination**: Efficient data pagination for large datasets
- **Responsive Design**: Mobile-friendly Bootstrap-based interface
- **Database Integration**: Full database integration with proper relationships
- **File Upload**: Secure document upload and management
- **Library Integration**: TCPDF for PDF generation, PhpPresentation for PPTX

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- CodeIgniter 3.x or higher
- Composer (for dependency management)

## 🛠️ Installation

### 1. Module Installation
1. Copy the `ella_contractors` folder to your CRM's `modules` directory
2. The module will automatically register with your CRM system

### 2. Database Setup
1. Update the database configuration in `setup_database.php`
2. Run the setup script:
   ```bash
   cd ella_contractors
   php setup_database.php
   ```

### 3. Dependencies Installation
1. Install required PHP libraries:
   ```bash
   cd ella_contractors
   composer install
   ```

## 🗄️ Database Structure

The module creates the following tables:

- **`ella_contractors`**: Contractor information and details
- **`ella_contracts`**: Contract agreements and terms
- **`ella_projects`**: Project tracking and management
- **`ella_payments`**: Invoice and payment records
- **`ella_contractor_documents`**: Document storage and management
- **`ella_contractor_activity`**: Activity logging and audit trail
- **`ella_document_shares`**: Document sharing and access control

## 🎯 Usage

### Dashboard
- View key statistics and metrics
- Quick access to all major functions
- Recent activity overview

### Contractor Management
- **List View**: Browse all contractors with search and filtering
- **Add New**: Create new contractor profiles
- **Edit**: Update existing contractor information
- **Delete**: Remove contractors (with safety checks)

### Contract Management
- **Create Contracts**: Set up new agreements
- **Track Status**: Monitor contract progress
- **Generate PDFs**: Create professional contract documents

### Project Management
- **Project Tracking**: Monitor project progress and milestones
- **Budget Management**: Track project budgets and expenses
- **Status Updates**: Update project status and completion

### Payment Management
- **Invoice Creation**: Generate and track invoices
- **Payment Tracking**: Monitor payment status and due dates
- **Financial Reports**: Generate payment and revenue reports

### Document Management
- **File Upload**: Upload and organize documents
- **Gallery View**: Browse and search documents
- **Sharing**: Generate shareable links for documents
- **PDF Generation**: Convert documents to PDF format

## 🔧 Configuration

### Module Settings
The module automatically configures itself with default settings. Key configuration files:

- **`ella_contractors.php`**: Main module configuration
- **`config/routes.php`**: URL routing configuration
- **`config/config.php`**: Module-specific settings

### Customization
You can customize the module by:

1. **Modifying Views**: Edit files in the `views/` directory
2. **Customizing Models**: Extend functionality in the `models/` directory
3. **Adding Controllers**: Create new controller methods as needed
4. **Styling**: Modify CSS in the `assets/css/` directory

## 📱 API Endpoints

### Contractors
- `GET /ella_contractors/contractors` - List contractors
- `POST /ella_contractors/contractors/add` - Add new contractor
- `GET /ella_contractors/contractors/edit/{id}` - Edit contractor
- `DELETE /ella_contractors/contractors/delete/{id}` - Delete contractor

### Contracts
- `GET /ella_contractors/contracts` - List contracts
- `POST /ella_contractors/contracts/add` - Add new contract
- `GET /ella_contractors/contracts/edit/{id}` - Edit contract
- `DELETE /ella_contractors/contracts/delete/{id}` - Delete contract

### Projects
- `GET /ella_contractors/projects` - List projects
- `POST /ella_contractors/projects/add` - Add new project
- `GET /ella_contractors/projects/edit/{id}` - Edit project
- `DELETE /ella_contractors/projects/delete/{id}` - Delete project

### Payments
- `GET /ella_contractors/payments` - List payments
- `POST /ella_contractors/payments/add` - Add new payment
- `GET /ella_contractors/payments/edit/{id}` - Edit payment
- `DELETE /ella_contractors/payments/delete/{id}` - Delete payment

### Documents
- `GET /ella_contractors/documents/gallery/{contractor_id}` - View documents
- `POST /ella_contractors/documents/upload/{contractor_id}` - Upload document
- `GET /ella_contractors/documents/download/{id}` - Download document
- `DELETE /ella_contractors/documents/delete/{id}` - Delete document

### PDF Generation
- `GET /ella_contractors/pdf/contract/{id}` - Generate contract PDF
- `GET /ella_contractors/pdf/invoice/{id}` - Generate invoice PDF
- `GET /ella_contractors/pdf/report/{type}` - Generate report PDF

### Presentations
- `GET /ella_contractors/presentation/contractor/{id}` - Generate contractor presentation
- `GET /ella_contractors/presentation/project/{id}` - Generate project presentation

## 🔒 Security Features

- **Input Validation**: All user inputs are validated and sanitized
- **SQL Injection Protection**: Prepared statements and parameterized queries
- **File Upload Security**: Secure file handling with type validation
- **Access Control**: Session-based authentication and authorization
- **CSRF Protection**: Built-in CSRF token validation

## 📊 Reporting

The module provides comprehensive reporting capabilities:

- **Dashboard Statistics**: Real-time overview of key metrics
- **Contractor Performance**: Track contractor performance and reliability
- **Financial Reports**: Monitor revenue, payments, and outstanding amounts
- **Project Progress**: Track project completion and timeline adherence
- **Document Analytics**: Monitor document usage and sharing

## 🚀 Performance Optimization

- **Database Indexing**: Optimized database queries with proper indexing
- **Pagination**: Efficient data loading for large datasets
- **Caching**: Built-in caching for frequently accessed data
- **File Optimization**: Optimized file handling and storage

## 🐛 Troubleshooting

### Common Issues

1. **PDF Generation Fails**
   - Ensure TCPDF library is installed: `composer install`
   - Check file permissions for temporary directory

2. **Database Connection Errors**
   - Verify database credentials in `setup_database.php`
   - Ensure database exists and is accessible

3. **File Upload Issues**
   - Check directory permissions for upload folders
   - Verify file size limits in PHP configuration

4. **Route Not Found Errors**
   - Ensure routes are properly configured in `config/routes.php`
   - Check that the module is properly installed

### Debug Mode
Enable debug mode by setting:
```php
define('ELLA_CONTRACTORS_DEBUG', true);
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This module is licensed under the MIT License. See LICENSE file for details.

## 🆘 Support

For support and questions:

1. Check the troubleshooting section above
2. Review the code comments and documentation
3. Create an issue in the repository
4. Contact the development team

## 🔄 Version History

- **v1.0.0**: Initial release with basic CRUD functionality
- **v1.1.0**: Added PDF generation and document management
- **v1.2.0**: Enhanced reporting and analytics
- **v1.3.0**: Full dynamic functionality with database integration

---

**Ella Contractors Module** - Professional contractor management for modern businesses.


