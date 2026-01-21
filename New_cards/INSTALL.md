# Card Editor Installation Guide

## Quick Start

1. **Upload Files**: Place all files in your web server directory
2. **Test Setup**: Open `test.php` in your browser to verify everything works
3. **Start Editing**: Open `index.html` to begin using the Card Editor

## Detailed Installation

### Prerequisites

- **Web Server**: Apache, Nginx, or any PHP-compatible server
- **PHP**: Version 7.0 or higher
- **PHP Extensions**: GD extension (for image processing)
- **Browser**: Modern browser with HTML5 Canvas support

### Step 1: Server Setup

#### For Apache (WAMP/XAMPP)
1. Place files in your `www` or `htdocs` directory
2. Ensure Apache and PHP are running
3. Verify GD extension is enabled in `php.ini`

#### For Nginx
1. Place files in your web root directory
2. Configure Nginx to handle PHP files
3. Ensure PHP-FPM is running

#### For Shared Hosting
1. Upload files via FTP/SFTP
2. Ensure PHP GD extension is available
3. Check file permissions (755 for directories, 644 for files)

### Step 2: File Permissions

Set appropriate permissions for your server:

```bash
# For Linux/Unix servers
chmod 755 .
chmod 755 thumbnails/
chmod 755 edited_cards/
chmod 644 *.php
chmod 644 *.html
chmod 644 *.css
chmod 644 *.js
```

### Step 3: Configuration

1. **Review `config.php`**: Modify settings as needed for your environment
2. **Security**: In production, change `ALLOWED_ORIGINS` from `['*']` to specific domains
3. **Debug Mode**: Set `DEBUG_MODE` to `false` in production

### Step 4: Testing

1. **System Test**: Open `test.php` in your browser
2. **Verify Results**: All tests should show green (success)
3. **Check Images**: Ensure your card images are visible
4. **Test Functionality**: Try loading and editing a card

## Troubleshooting

### Common Issues

#### Images Not Loading
- Check file permissions
- Verify PHP GD extension is enabled
- Check browser console for errors

#### Cannot Save Cards
- Ensure `edited_cards/` directory is writable
- Check PHP upload limits in `php.ini`
- Verify file size limits in `config.php`

#### Text Not Displaying
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify canvas element is properly initialized

### PHP Configuration

Check these settings in your `php.ini`:

```ini
; Enable file uploads
file_uploads = On

; Maximum file upload size
upload_max_filesize = 10M

; Maximum POST data size
post_max_size = 10M

; Maximum execution time
max_execution_time = 300

; Memory limit
memory_limit = 256M

; Enable GD extension
extension=gd
```

### Browser Compatibility

- **Chrome**: 60+
- **Firefox**: 55+
- **Safari**: 12+
- **Edge**: 79+

## Security Considerations

### Production Deployment

1. **HTTPS**: Use HTTPS in production
2. **File Validation**: Server-side validation is already implemented
3. **CORS**: Restrict `ALLOWED_ORIGINS` to your domain
4. **Error Reporting**: Set `DEBUG_MODE` to `false`
5. **File Permissions**: Restrict write access to necessary directories only

### File Upload Security

- Only image files are accepted
- File type validation on both client and server
- File size limits enforced
- Secure filename generation

## Performance Optimization

### For Large Images

1. **Resize Images**: Consider resizing very large images before editing
2. **Thumbnail Generation**: Thumbnails are automatically generated and cached
3. **Canvas Size**: Adjust `CANVAS_MAX_WIDTH` and `CANVAS_MAX_HEIGHT` in `config.php`

### Browser Performance

1. **Text Elements**: Limit the number of text elements for better performance
2. **Image Quality**: Balance between quality and file size
3. **Caching**: Enable browser caching for static assets

## Customization

### Adding New Fonts

Edit the font options in `index.html`:

```html
<select id="fontFamily">
    <option value="Arial">Arial</option>
    <option value="Helvetica">Helvetica</option>
    <!-- Add your custom fonts here -->
</select>
```

### Changing Canvas Size

Modify the canvas dimensions in `script.js`:

```javascript
// Set canvas size
const maxWidth = 800;  // Change this value
const maxHeight = 600; // Change this value
```

### Styling Changes

Customize the appearance by modifying `styles.css`:

```css
/* Change primary colors */
.btn-primary {
    background: #your-color;
}

/* Modify layout dimensions */
.sidebar {
    width: 350px; /* Change from 300px */
}
```

## Support

### Getting Help

1. **Check Test Results**: Run `test.php` to identify issues
2. **Browser Console**: Check for JavaScript errors
3. **Server Logs**: Review PHP error logs
4. **File Permissions**: Verify directory and file permissions

### Common Solutions

- **Permission Denied**: Check file/directory permissions
- **GD Extension Missing**: Contact your hosting provider
- **Upload Fails**: Check file size limits and directory permissions
- **Images Not Displaying**: Verify image file formats and paths

## Updates and Maintenance

### Regular Maintenance

1. **Clean Thumbnails**: Remove unused thumbnail files periodically
2. **Monitor Storage**: Check disk space usage
3. **Backup Edited Cards**: Regularly backup the `edited_cards/` directory
4. **Update Dependencies**: Keep PHP and web server updated

### Version Updates

1. **Backup**: Always backup your current installation
2. **Test**: Test updates in a development environment first
3. **Deploy**: Update files during low-traffic periods
4. **Verify**: Test functionality after updates

---

**Note**: This installation guide covers the most common scenarios. For specific server configurations or advanced setups, consult your hosting provider's documentation or server administrator.

































