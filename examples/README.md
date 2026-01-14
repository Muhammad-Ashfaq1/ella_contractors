# Ella Contractors Module - JSON Example Data

This directory contains realistic JSON example data files for all major entities in the Ella Contractors module. These files can be used for:

- API development and testing
- Data import/export functionality
- Documentation and examples
- Integration with external systems
- Development and staging environments

## Files Overview

### 1. `service_items.json`
Contains example service items (line items) that can be used in estimates and proposals.

**Structure:**
- `service_items`: Array of service items with fields like `id`, `group_id`, `group_name`, `name`, `description`, `unit`, `cost`, `is_active`
- `line_item_groups`: Array of service item groups/categories

**Use Cases:**
- Populating service item catalogs
- Creating estimates with line items
- Testing pricing calculations

### 2. `presentations.json`
Contains example presentation files that can be attached to appointments.

**Structure:**
- `presentations`: Array of presentation records with file metadata including `file_name`, `original_name`, `file_type`, `file_size`, `description`, `public_url`

**Use Cases:**
- Testing presentation upload/download
- Attaching presentations to appointments
- Presentation management workflows

### 3. `appointments.json`
Contains example appointment records with all related data.

**Structure:**
- `appointments`: Array of appointment records with complete details including contact info, dates, times, status, notes
- `appointment_reminders`: Array of reminder configurations for appointments

**Use Cases:**
- Testing appointment creation and management
- Calendar integration testing
- Reminder system testing

### 4. `notes.json`
Contains example notes attached to appointments.

**Structure:**
- `notes`: Array of note records with `rel_type`, `rel_id`, `description`, `dateadded`, `addedfrom`, `staff_name`, `time_ago`

**Use Cases:**
- Testing note creation and retrieval
- Timeline/activity log functionality
- Communication history tracking

### 5. `measurements.json`
Contains example measurement records with nested measurement items.

**Structure:**
- `measurement_records`: Array of measurement records, each containing:
  - Record metadata (id, rel_type, rel_id, appointment_id, tab_name)
  - `measurement_items`: Array of individual measurements with name, value, unit, sort_order

**Use Cases:**
- Testing measurement capture and storage
- Generating measurement reports
- Calculating material requirements

### 6. `estimates.json`
Contains example estimate/proposal records with line items.

**Structure:**
- `estimates`: Array of proposal/estimate records with complete details
- Each estimate includes `proposal_items`: Array of line items with quantities, rates, descriptions
- `estimate_statuses`: Array of possible estimate statuses

**Use Cases:**
- Testing estimate creation and management
- Proposal generation
- Status workflow testing

### 7. `attachments.json`
Contains example attachment files linked to appointments.

**Structure:**
- `attachments`: Array of attachment records with file metadata including `file_name`, `original_name`, `file_type`, `file_size`, `description`, `public_url`, `uploaded_by`

**Use Cases:**
- Testing file upload/download
- Attachment management
- Document sharing workflows

## Data Characteristics

All example data includes:
- **Realistic values**: Based on actual contractor/bathroom/kitchen remodeling scenarios
- **Proper relationships**: IDs and foreign keys are consistent across files
- **Complete fields**: All required and optional fields are populated
- **Real-world scenarios**: Data represents typical customer interactions and project details

## Usage Examples

### Importing Data
```php
// Example: Import service items
$service_items = json_decode(file_get_contents('examples/service_items.json'), true);
foreach ($service_items['service_items'] as $item) {
    $this->line_items_model->add($item);
}
```

### API Response Format
```php
// Example: Return appointments in API
$appointments = json_decode(file_get_contents('examples/appointments.json'), true);
header('Content-Type: application/json');
echo json_encode($appointments);
```

### Testing
```javascript
// Example: Use in frontend testing
fetch('/api/appointments')
  .then(response => response.json())
  .then(data => {
    // Use example data structure for testing
  });
```

## Field Descriptions

### Common Fields Across Entities
- `id`: Unique identifier
- `rel_type`: Relationship type (e.g., "appointment", "lead", "client")
- `rel_id`: Related entity ID
- `org_id`: Organization ID for multi-tenant support
- `created_at` / `dateadded`: Creation timestamp
- `updated_at`: Last update timestamp
- `created_by` / `addedfrom`: Staff member who created the record

### Status Fields
- `appointment_status`: "scheduled", "cancelled", "complete"
- `estimate_status`: Numeric status (1=Draft, 2=Sent, 3=Open, etc.)
- `is_active`: Boolean for active/inactive records

### File Fields
- `file_name`: Stored filename on server
- `original_name`: Original filename from upload
- `file_type`: MIME type
- `file_size`: Size in bytes
- `public_url`: Full URL to access the file

## Notes

- All timestamps are in `YYYY-MM-DD HH:MM:SS` format
- Currency values are in USD (currency ID: 1)
- File sizes are in bytes
- All IDs are sequential integers starting from 1
- Relationships between entities are maintained (e.g., appointment_id in estimates matches appointment id)

## Integration Points

These JSON files align with:
- Database table structures defined in `ella_contractors.php`
- Model classes in `models/` directory
- Controller methods in `controllers/` directory
- API endpoints (if implemented)

## Updates

When database schema changes, update these example files to reflect:
- New fields
- Changed field types
- New relationships
- Updated validation rules
