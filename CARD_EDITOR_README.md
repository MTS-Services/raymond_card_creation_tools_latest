# ShieldID Card Editor & Designer

## Overview
The Card Editor is a comprehensive image editing and design tool integrated into the ShieldID admin panel. It allows administrators to upload images from the "New Cards" folder, add text overlays, create custom designs, and export finished cards as downloadable images.

## Features

### 🖼️ Image Management
- **New Cards Integration**: Direct access to images in the "New Cards" folder
- **Upload System**: Upload custom images to the card_designs directory
- **Image Gallery**: Browse and select from available images with previews
- **Auto-scaling**: Images automatically scale to fit the canvas while maintaining aspect ratio

### ✏️ Text Editing Tools
- **Dynamic Text Addition**: Add text elements with customizable properties
- **Font Controls**: 
  - Font family selection (Arial, Times New Roman, Courier New, Georgia, Verdana, Impact)
  - Font size adjustment (8px to 72px)
  - Text color picker
  - Background color picker
  - Bold, Italic, and Underline formatting
- **Real-time Preview**: See changes instantly as you edit

### 🎨 Design Elements
- **Text Elements**: Add and customize text overlays
- **Shapes**: Add rectangles and circles for design elements
- **Layer Management**: Organize and manage multiple design elements
- **Drag & Drop**: Move elements around the canvas with mouse

### 🛠️ Advanced Features
- **Undo/Redo System**: 10-level undo/redo functionality
- **Element Selection**: Click to select and edit existing elements
- **Double-click Editing**: Double-click text elements to edit content
- **Layer Visibility**: See all elements in the layer management panel
- **Element Deletion**: Remove unwanted elements individually

### 💾 Export & Save
- **Download Functionality**: Export finished designs as high-quality PNG images
- **HTML2Canvas Integration**: High-resolution export with 2x scaling
- **Design Saving**: Save designs to database (placeholder functionality)

## How to Use

### 1. Accessing the Editor
1. Log into the ShieldID admin panel
2. Navigate to "Card Editor" in the sidebar
3. The editor interface will load with three main panels

### 2. Selecting an Image
1. **From New Cards Folder**:
   - Browse images in the left panel under "New Cards Folder"
   - Click on any image to load it into the editor
   
2. **Upload Custom Image**:
   - Use the upload form at the top of the left panel
   - Supported formats: JPG, JPEG, PNG, GIF
   - Images are automatically saved to `uploads/card_designs/`

### 3. Adding Text Elements
1. Click the "Add Text" button in the toolbar
2. Use the right panel to customize:
   - Text content in the "Text Content" field
   - Font size using the slider
   - Font family from the dropdown
   - Colors using the color pickers
   - Formatting options (bold, italic, underline)
3. Text elements appear on the canvas and can be moved by dragging

### 4. Adding Shapes
1. **Rectangle**: Click "Rectangle" button to add rectangular shapes
2. **Circle**: Click "Circle" button to add circular shapes
3. Shapes use the current color settings from the text controls

### 5. Editing Elements
1. **Select**: Click on any element to select it
2. **Move**: Drag selected elements around the canvas
3. **Edit Text**: Double-click text elements to edit content
4. **Modify Properties**: Use the right panel to change selected element properties
5. **Delete**: Use the delete button in the layer management panel

### 6. Layer Management
- View all elements in the "Layer Management" section
- See element types and content
- Delete elements using the trash button
- Elements are listed in order of creation

### 7. Exporting Your Design
1. Click the "Download" button
2. The design will be exported as a PNG file
3. File is automatically downloaded with the name "custom_card_design.png"

## Technical Details

### File Structure
```
admin/
├── card-editor.php          # Main editor interface
├── includes/
│   └── sidebar.php         # Updated sidebar with Card Editor link
uploads/
└── card_designs/           # Directory for uploaded custom images
```

### Dependencies
- **Bootstrap 5.3.0**: UI framework and styling
- **Font Awesome 6.0.0**: Icons and visual elements
- **HTML2Canvas 1.4.1**: High-quality image export
- **Custom CSS**: Responsive design and editor-specific styling

### Browser Compatibility
- **Modern Browsers**: Chrome, Firefox, Safari, Edge (latest versions)
- **Canvas Support**: Requires HTML5 Canvas support
- **JavaScript**: ES6+ compatible browsers

### Security Features
- **Session Validation**: Admin authentication required
- **File Upload Validation**: Only image files allowed
- **Path Sanitization**: Secure file handling
- **XSS Prevention**: All output properly escaped

## Customization Options

### Adding New Fonts
To add new font families:
1. Edit the `fontFamily` select element in the HTML
2. Add new `<option>` elements with desired fonts
3. Ensure fonts are available on the system

### Extending Shape Tools
To add new shape types:
1. Create new functions similar to `addRectangle()` and `addCircle()`
2. Add corresponding buttons to the toolbar
3. Update the `renderTextElements()` function to handle new types

### Custom Export Formats
To support different export formats:
1. Modify the `downloadDesign()` function
2. Add format selection options
3. Implement format-specific export logic

## Troubleshooting

### Common Issues

**Images Not Loading**
- Check file permissions on the "New Cards" folder
- Verify image file formats are supported
- Check browser console for JavaScript errors

**Text Elements Not Appearing**
- Ensure JavaScript is enabled
- Check for console errors
- Verify HTML2Canvas library is loaded

**Export Not Working**
- Check browser compatibility
- Ensure sufficient memory for large designs
- Verify HTML2Canvas library is loaded correctly

**Performance Issues**
- Reduce canvas size for large images
- Limit number of text elements
- Use smaller image files

### Error Messages

**"Image upload failed"**
- Check upload directory permissions
- Verify file size limits
- Ensure valid image format

**"Canvas not supported"**
- Update browser to latest version
- Enable JavaScript
- Check for browser compatibility

## Future Enhancements

### Planned Features
- **Template System**: Pre-designed card templates
- **Batch Processing**: Edit multiple cards simultaneously
- **Advanced Shapes**: More geometric and custom shapes
- **Text Effects**: Shadows, outlines, and gradients
- **Image Filters**: Apply effects and adjustments
- **Collaboration**: Multi-user editing capabilities

### Integration Possibilities
- **Database Storage**: Save designs to database
- **API Integration**: Connect with external design services
- **Mobile Support**: Responsive mobile interface
- **Print Integration**: Direct printing capabilities

## Support and Maintenance

### Regular Maintenance
- Monitor upload directory size
- Clean up temporary files
- Update external libraries
- Check browser compatibility

### Performance Optimization
- Optimize image loading
- Implement lazy loading for large galleries
- Cache frequently used elements
- Compress exported images

---

## Quick Start Checklist

- [ ] Access admin panel and navigate to Card Editor
- [ ] Select an image from New Cards folder or upload custom image
- [ ] Add text elements using the Add Text button
- [ ] Customize text properties (font, size, color, formatting)
- [ ] Position elements by dragging on the canvas
- [ ] Use layer management to organize elements
- [ ] Preview your design in real-time
- [ ] Download the finished design as PNG
- [ ] Save design if needed (placeholder functionality)

---

*This Card Editor tool provides a powerful and intuitive interface for creating custom ID card designs, making it easy for administrators to personalize cards according to specific requirements.*




