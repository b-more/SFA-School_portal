# Parent Guardian Import/Export Guide

## Overview
You can now bulk import parent guardian data using Excel or CSV files at:
https://portal.stfrancisofassisizm.com/admin/parent-guardians

## Features Added
1. **Import** - Bulk import parent guardians from CSV/Excel files
2. **Export** - Export existing parent guardians to CSV/Excel files (useful for creating templates)

## Import Instructions

### Step 1: Prepare Your Data File
Create a CSV or Excel file with the following columns:

#### Required Fields:
- **name** - Full name of the parent/guardian
- **phone** - Primary contact phone number (format: 260972266217)
- **relationship** - Must be one of: `father`, `mother`, `guardian`, or `other`
- **address** - Physical address

#### Optional Fields:
- **email** - Email address
- **alternate_phone** - Alternative phone number
- **nrc** - National Registration Card number (format: 123456/78/9)
- **nationality** - Nationality (default: Zambian)
- **occupation** - Parent's occupation

### Step 2: Format Your Data
- Use the column names exactly as shown above
- For relationship, use lowercase: `father`, `mother`, `guardian`, or `other`
- Phone numbers should include country code (e.g., 260972266217)
- Leave optional fields empty if not available

### Step 3: Sample CSV Format
```csv
name,email,phone,alternate_phone,relationship,nrc,nationality,occupation,address
John Doe,john.doe@example.com,260972266217,260972266218,father,123456/78/9,Zambian,Teacher,"123 Main Street, Lusaka"
Jane Smith,jane.smith@example.com,260971234567,260971234568,mother,987654/32/1,Zambian,Nurse,"456 Church Road, Kitwe"
```

A sample file is available at: `storage/app/public/parent_guardian_import_sample.csv`

### Step 4: Import Process
1. Go to https://portal.stfrancisofassisizm.com/admin/parent-guardians
2. Click the **"Import"** button (green button with upload icon)
3. Upload your CSV or Excel file
4. Map the columns if needed (Filament will auto-detect if column names match)
5. Review and confirm the import
6. Wait for the import to complete

### Step 5: Review Results
- After import completes, you'll see a notification with:
  - Number of successful imports
  - Number of failed imports (if any)
- Failed rows will be highlighted with error messages
- You can download a report of any failures

## Export Instructions

### Creating a Template
1. Go to https://portal.stfrancisofassisizm.com/admin/parent-guardians
2. Click the **"Export"** button (blue button with download icon)
3. Select your preferred format (CSV or Excel)
4. The system will export all current parent guardians
5. Use this as a template for future imports

### Exporting All Data
- The export includes all fields from the database
- Use filters before exporting to export specific groups
- Export can be used for backups or data analysis

## Important Notes

### Duplicate Prevention
- The system uses **phone number** as the unique identifier
- If a parent with the same phone number exists, their data will be **updated** instead of creating a duplicate
- This allows you to update existing records by reimporting

### Validation Rules
- All required fields must be provided
- Email must be valid email format
- Relationship must be one of the accepted values
- Phone numbers are used as unique identifiers

### User Account Creation
**Note:** The import process creates parent guardian records only. User accounts for portal access need to be created separately or through the parent guardian creation form which includes SMS notifications.

### Best Practices
1. **Test with a small file first** - Import 2-3 records to verify format
2. **Use the export feature** to get the correct format
3. **Keep backups** of your import files
4. **Review failed imports** and correct errors before reimporting
5. **Use consistent formatting** for phone numbers and NRC

### Troubleshooting

#### Common Import Errors:
1. **"The relationship field is invalid"** - Use lowercase: father, mother, guardian, or other
2. **"The phone field is required"** - Ensure phone column is not empty
3. **"The email field must be a valid email address"** - Check email format
4. **"Column not found"** - Ensure column names match exactly (case-sensitive)

#### If Import Fails:
1. Download the failed rows report
2. Fix the errors in your source file
3. Re-import only the failed rows

## Support
For questions or issues with imports, contact the system administrator.
