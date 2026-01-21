# Professional Card Editor

A professional web-based tool for editing card images with text overlays, built with PHP and JavaScript.

## Features

- **Image Management**: Browse and select from available card images
- **Text Editing**: Add, edit, and customize text overlays on cards
- **Professional Controls**: Font selection, size, color, weight, alignment, and shadow effects
- **Real-time Preview**: See changes instantly on the canvas
- **Save & Download**: Save edited cards to server and download locally
- **Responsive Design**: Works on desktop and mobile devices
- **Search Functionality**: Quickly find specific cards

## File Structure

```
├── index.html          # Main HTML interface
├── styles.css          # Professional styling and responsive design
├── script.js           # JavaScript functionality for card editing
├── get_cards.php       # PHP backend to scan and serve available cards
├── save_card.php       # PHP backend to save edited cards
├── thumbnails/         # Generated thumbnail images (auto-created)
├── edited_cards/       # Saved edited cards (auto-created)
└── README.md           # This file
```

## Requirements

- **Web Server**: Apache, Nginx, or any PHP-compatible server
- **PHP**: Version 7.0 or higher with GD extension enabled
- **Browser**: Modern browser with HTML5 Canvas support
- **Images**: JPG, PNG, or GIF format card images

## Installation

1. **Upload Files**: Place all files in your web server directory
2. **Set Permissions**: Ensure the web server can write to the current directory for thumbnails and edited cards
3. **Access Tool**: Open `index.html` in your web browser

## Usage

### 1. Select a Card
- Browse available cards in the left sidebar
- Use the search box to find specific cards
- Click on a card to load it into the editor

### 2. Add Text
- Click anywhere on the canvas to add a new text element
- Double-click on existing text to edit it
- Drag text elements to reposition them

### 3. Customize Text
- **Content**: Type your text in the text area
- **Font**: Choose from available font families
- **Size**: Adjust font size using the slider (12px - 72px)
- **Color**: Pick any color using the color picker
- **Weight**: Select font weight (normal, bold, or numeric values)
- **Alignment**: Choose left, center, or right alignment
- **Shadow**: Enable text shadow with customizable color and blur

### 4. Save or Download
- **Save**: Save the edited card to the server
- **Download**: Download the edited card to your computer
- **Reset**: Reset the card to its original state

## Technical Details

### Frontend
- **HTML5 Canvas**: For image manipulation and text rendering
- **Vanilla JavaScript**: No external dependencies
- **Responsive CSS**: Tailwind-inspired design system
- **Font Awesome Icons**: Professional icon set

### Backend
- **PHP GD Extension**: For image processing and thumbnail generation
- **JSON API**: RESTful endpoints for card operations
- **File Management**: Automatic thumbnail generation and file organization

### Image Processing
- **Thumbnail Generation**: Automatic 60x40px thumbnails for card previews
- **Canvas Rendering**: Client-side text overlay rendering
- **Quality Preservation**: Maintains original image quality
- **Format Support**: JPG, PNG, and GIF formats

## Browser Support

- **Chrome**: 60+
- **Firefox**: 55+
- **Safari**: 12+
- **Edge**: 79+

## Troubleshooting

### Common Issues

1. **Images Not Loading**
   - Check file permissions
   - Ensure PHP GD extension is enabled
   - Verify image file formats are supported

2. **Cannot Save Cards**
   - Check directory write permissions
   - Ensure `edited_cards/` directory exists and is writable
   - Check PHP upload limits in `php.ini`

3. **Text Not Displaying**
   - Ensure JavaScript is enabled
   - Check browser console for errors
   - Verify canvas element is properly initialized

### Performance Tips

- **Large Images**: Consider resizing very large images before editing
- **Multiple Text Elements**: Limit the number of text elements for better performance
- **Browser Cache**: Clear browser cache if experiencing issues

## Customization

### Adding New Fonts
Edit the `fontFamily` select options in `index.html` to include additional fonts.

### Changing Canvas Size
Modify the canvas dimensions in `script.js` to accommodate different image sizes.

### Styling Changes
Customize the appearance by modifying `styles.css` to match your brand colors and design preferences.

## Security Considerations

- **File Uploads**: Only image files are accepted
- **File Validation**: Server-side validation of uploaded content
- **Directory Traversal**: Protected against path manipulation attacks
- **CORS**: Configured for local development (adjust for production)

## License

This tool is provided as-is for educational and commercial use. Modify and distribute as needed.

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review browser console for JavaScript errors
3. Check server error logs for PHP issues
4. Ensure all requirements are met

---

**Note**: This tool is designed for professional card editing and text overlay applications. It provides a user-friendly interface for non-technical users while maintaining professional output quality.


