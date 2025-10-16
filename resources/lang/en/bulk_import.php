<?php

return [
    // Page title
    'page_title' => 'Bulk User Import',
    'page_subtitle' => 'Import multiple users at once using Excel spreadsheet',

    // Header buttons
    'company_users' => 'Company Users',
    'my_profile' => 'My Profile',

    // Instructions section
    'how_to_use' => 'How to Use Bulk Import',

    // Step 1
    'step_1_title' => 'Step 1: Download Template',
    'step_1_1' => 'Click the "Download Excel Template" button below',
    'step_1_2' => 'Open the file in Excel or LibreOffice',
    'step_1_3' => 'Review the sample data and instructions included',

    // Step 2
    'step_2_title' => 'Step 2: Fill in User Data',
    'step_2_1' => '<strong>Delete the sample data row</strong> (row 2)',
    'step_2_2' => 'Add your users starting from row 3',
    'step_2_3' => 'Fill in required fields: Name, Surname, Email, Percentage',
    'step_2_4' => 'Optional fields: Phone, Mobile, Date of Birth, etc.',

    // Step 3
    'step_3_title' => 'Step 3: Upload File',
    'step_3_1' => 'Save your Excel file',
    'step_3_2' => 'Select the target company from the dropdown',
    'step_3_3' => 'Upload the file using the form below',
    'step_3_4' => 'Review import results',

    // Important notes
    'important_notes' => 'Important Notes:',
    'note_password' => 'All new users will have default password: <strong>"password"</strong>',
    'note_status' => 'Users will be created with status: <strong>"Parked"</strong> (pending STA approval)',
    'note_unique_email' => 'Email addresses must be unique in the system',
    'note_max_size' => 'Maximum file size: 10MB',

    // Download template section
    'download_template_title' => 'Step 1: Download Template',
    'excel_template' => 'Excel Import Template',
    'template_description' => 'Download a pre-formatted Excel file with headers, sample data, and instructions.',

    'feature_1' => '<strong>12 columns</strong> with clear headers',
    'feature_2' => 'Sample data row for reference',
    'feature_3' => 'Detailed instructions included',
    'feature_4' => 'Properly formatted and styled',

    'download_button' => 'Download Excel Template',
    'file_format' => 'File format: .xlsx',

    // Upload section
    'upload_title' => 'Step 2: Upload Filled Template',
    'upload_errors' => 'Upload Errors',
    'import_errors' => 'Import Errors ({count} rows skipped)',

    'select_company' => 'Select Target Company',
    'choose_company' => 'Choose company...',
    'company_info' => 'Users will be added to the selected company',

    'upload_excel' => 'Upload Excel File',
    'accepted_formats' => 'Accepted formats: .xlsx, .xls | Max size: 10MB',

    // Before uploading
    'before_uploading' => 'Before uploading:',
    'check_1' => 'Make sure you\'ve <strong>deleted the sample data</strong> row',
    'check_2' => 'Check that all <strong>required fields</strong> are filled (Name, Surname, Email, Percentage)',
    'check_3' => 'Verify all <strong>email addresses are valid</strong> and unique',
    'check_4' => 'Ensure <strong>percentage values are between 1-100</strong>',

    // Buttons
    'upload_import_button' => 'Upload and Import Users',
    'cancel_button' => 'Cancel',

    // Template columns reference
    'columns_reference_title' => 'Excel Template Columns Reference',
    'column_header' => 'Column Name',
    'description_header' => 'Description',
    'required_header' => 'Required',
    'example_header' => 'Example',

    'required_badge' => 'Required',
    'optional_badge' => 'Optional',

    // Column descriptions
    'col_name' => 'Name',
    'col_name_desc' => 'First name of the user',
    'col_surname' => 'Surname',
    'col_surname_desc' => 'Last name of the user',
    'col_email' => 'Email',
    'col_email_desc' => 'Valid email address (must be unique)',
    'col_phone' => 'Phone',
    'col_phone_desc' => 'Phone number',
    'col_mobile' => 'Mobile',
    'col_mobile_desc' => 'Mobile phone number',
    'col_dob' => 'Date of Birth',
    'col_dob_desc' => 'Format: YYYY-MM-DD',
    'col_pob' => 'Place of Birth',
    'col_pob_desc' => 'City or location of birth',
    'col_country' => 'Country',
    'col_country_desc' => 'Country name',
    'col_gender' => 'Gender',
    'col_gender_desc' => 'Must be: male, female, or other',
    'col_cf' => 'CF (Codice Fiscale)',
    'col_cf_desc' => 'Italian tax code (max 16 characters)',
    'col_address' => 'Address',
    'col_address_desc' => 'Full address',
    'col_percentage' => 'Company Percentage',
    'col_percentage_desc' => 'Ownership percentage (1-100)',

    // After import
    'after_import' => 'After Import:',
    'after_import_1' => 'All users will have the default password: <code>password</code>',
    'after_import_2' => 'Users should change their password on first login',
    'after_import_3' => 'Users will be created with <strong>"Parked"</strong> status',
    'after_import_4' => 'STA Manager must approve users before they can access the system',
    'after_import_5' => 'All users will be assigned the <strong>"End User"</strong> role',
];
