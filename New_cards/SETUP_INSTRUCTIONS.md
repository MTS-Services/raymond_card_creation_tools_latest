# Virtual ID Card System - Setup Instructions

## 🚀 New Features Added

### ✅ Database Integration
- **Card Storage**: All downloaded cards are automatically stored in the database
- **Unique QR Codes**: Each QR code contains a unique URL that links to the card display page
- **Card Display Page**: When QR codes are scanned, users see a beautiful card information page

### ✅ File Storage
- **Local Storage**: Cards are saved in the `stored_cards/` folder
- **Unique Filenames**: Each card gets a unique filename based on timestamp and random ID
- **Database Backup**: All card information is stored in MySQL database

## 📋 Setup Requirements

### 1. Database Setup
1. **Open phpMyAdmin** (usually at `http://localhost/phpmyadmin`)
2. **Import the database**:
   - Click "Import" tab
   - Choose file: `database_setup.sql`
   - Click "Go" to create the database and tables

### 2. File Permissions
1. **Create storage folder**:
   ```bash
   mkdir stored_cards
   chmod 777 stored_cards
   ```

### 3. Configuration
1. **Update `config.php`** if needed:
   - Database credentials (default: root, no password)
   - Base URL for your setup

## 🎯 How It Works

### 1. Card Creation
- User creates a card using the editor
- Adds QR code (automatically generates unique URL)
- Downloads/saves the card

### 2. Automatic Storage
- **Database**: Card data, fields, and metadata stored
- **Files**: Front and back images saved to `stored_cards/` folder
- **QR Code**: Contains unique URL like `view_card.php?id=CARD_1234567890_abc123`

### 3. QR Code Scanning
- When QR code is scanned, it opens the card display page
- Shows beautiful card information with front/back images
- Displays all form fields and card metadata

## 📁 File Structure
```
New Cards/
├── card_creation_image.php      # Main editor interface
├── card_creation_script.js       # JavaScript functionality
├── config.php                    # Database configuration
├── save_card.php                 # Card saving endpoint
├── view_card.php                 # Card display page
├── database_setup.sql            # Database structure
├── stored_cards/                 # Generated card images
└── SETUP_INSTRUCTIONS.md         # This file
```

## 🔧 Database Tables

### `cards` Table
- `id`: Primary key
- `card_type`: Type of card (child_identification, autism_card, etc.)
- `unique_id`: Unique identifier for the card
- `qr_code_url`: URL that the QR code points to
- `front_image_path`: Path to front image file
- `back_image_path`: Path to back image file
- `card_data`: JSON data with card information
- `created_at`: Timestamp when card was created
- `updated_at`: Timestamp when card was last updated

### `card_fields` Table
- `id`: Primary key
- `card_id`: Foreign key to cards table
- `field_name`: Name of the form field
- `field_value`: Value of the form field

## 🎨 Card Display Page Features

### Beautiful Design
- **Responsive Layout**: Works on desktop and mobile
- **Card Images**: Shows both front and back of the card
- **Form Data**: Displays all entered information
- **Metadata**: Shows card type, unique ID, creation date

### Security
- **Input Sanitization**: All data is properly escaped
- **Error Handling**: Graceful error messages
- **Validation**: Checks for valid card IDs

## 🚀 Usage

1. **Create Card**: Use the editor to design your card
2. **Add QR Code**: Click "Add QR Code" button (generates unique URL)
3. **Save/Download**: Click "Save Card" button
4. **Automatic Storage**: Card is saved to database and folder
5. **QR Code Scanning**: Scan QR code to view card information

## 🔍 Testing

1. **Create a test card** with some sample data
2. **Add QR code** and save the card
3. **Check database**: Verify data is stored in `cards` and `card_fields` tables
4. **Check files**: Verify images are saved in `stored_cards/` folder
5. **Test QR code**: Scan the QR code to see the display page

## 🛠️ Troubleshooting

### Database Connection Issues
- Check MySQL is running in WAMP
- Verify database credentials in `config.php`
- Ensure database exists (run `database_setup.sql`)

### File Permission Issues
- Make sure `stored_cards/` folder exists and is writable
- Check PHP has write permissions

### QR Code Issues
- Verify the base URL in `config.php` matches your setup
- Check that `view_card.php` is accessible via web browser

## 📞 Support

If you encounter any issues:
1. Check the browser console for JavaScript errors
2. Check the PHP error logs
3. Verify database connection and file permissions
4. Test each component individually

---

**Version 6.0** - Database Integration & QR Code System

