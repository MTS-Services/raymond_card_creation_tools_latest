// PSD Card Editor JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // State variables
    let currentTemplate = 'psd-card';
    let currentSide = 'front'; // 'front' or 'back'
    let currentCardId = null;
    let uploadedPhoto = null;
    let editableElements = [];
    
    // DOM elements
    const templateCards = document.querySelectorAll('.template-card');
    const cardPreview = document.getElementById('cardPreview');
    const cardIdInput = document.getElementById('cardId');
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const photoUpload = document.getElementById('photoUpload');
    const statusText = document.getElementById('statusText');
    
    // Template configurations with editable areas for your PSD files
    const templateConfigs = {
        'psd-card': {
            front: {
                background: 'psd_files/Front.jpg',
                editableAreas: [
                    // Main name (Charles Rossen)
                    { type: 'text', id: 'mainName', x: 863, y: 330, width: 400, height: 35, placeholder: 'Charles Rossen', fontSize: '28px', color: '#1e3a8a', fontWeight: 'bold' },
                    // DOB field
                    { type: 'text', id: 'dob', x: 863, y: 368, width: 200, height: 25, placeholder: '01.08.2010', fontSize: '16px', color: '#dc2626' },
                    // Sex field
                    { type: 'text', id: 'sex', x: 1085, y: 368, width: 80, height: 25, placeholder: 'M', fontSize: '16px', color: '#333' },
                    // Full Name field (right side)
                    { type: 'text', id: 'fullName', x: 1142, y: 387, width: 200, height: 25, placeholder: 'Blue', fontSize: '16px', color: '#333' },
                    // Weight field
                    { type: 'text', id: 'weight', x: 863, y: 415, width: 100, height: 25, placeholder: '40LBS', fontSize: '16px', color: '#dc2626' },
                    // Title/Role field
                    { type: 'text', id: 'titleRole', x: 863, y: 426, width: 200, height: 20, placeholder: 'Title/Role', fontSize: '12px', color: '#666' },
                    // ID Number field
                    { type: 'text', id: 'idNumber', x: 1020, y: 468, width: 150, height: 25, placeholder: '6789', fontSize: '16px', color: '#333' },
                    // Dad contact
                    { type: 'text', id: 'dadContact', x: 935, y: 494, width: 200, height: 25, placeholder: '012 345 6789', fontSize: '16px', color: '#333' },
                    // Parents field
                    { type: 'text', id: 'parents', x: 950, y: 520, width: 300, height: 25, placeholder: 'Ashley and Robert', fontSize: '16px', color: '#333' },
                    // Photo area (left side)
                    { type: 'photo', id: 'photo', x: 680, y: 320, width: 170, height: 200 },
                    // QR Code area
                    { type: 'qr', id: 'qr', x: 1130, y: 520, width: 60, height: 60 }
                ]
            },
            back: {
                background: 'psd_files/Back.jpg',
                editableAreas: [
                    // Adjust these positions based on your back PSD design
                    { type: 'text', id: 'emergency', x: 50, y: 100, width: 300, height: 25, placeholder: 'Emergency Contact', fontSize: '14px', color: '#333' },
                    { type: 'text', id: 'phone', x: 50, y: 130, width: 200, height: 25, placeholder: 'Phone Number', fontSize: '14px', color: '#333' },
                    { type: 'text', id: 'address', x: 50, y: 160, width: 350, height: 25, placeholder: 'Address', fontSize: '12px', color: '#666' },
                    { type: 'text', id: 'notes', x: 50, y: 200, width: 350, height: 60, placeholder: 'Additional Notes', fontSize: '12px', color: '#666' },
                    { type: 'qr', id: 'qr_back', x: 450, y: 300, width: 60, height: 60 }
                ]
            }
        }
    };
    
    // Initialize
    init();
    
    function init() {
        setupEventListeners();
        generateCardId();
        loadTemplate(currentTemplate); // Auto-load the PSD template
        updateStatus('PSD template loaded - Click on text areas to edit');
        console.log('PSD Card Editor initialized');
    }
    
    function setupEventListeners() {
        // Template selection
        templateCards.forEach(card => {
            card.addEventListener('click', () => selectTemplate(card.dataset.template));
        });
        
        // Front/Back toggle
        document.getElementById('showFront').addEventListener('click', () => switchSide('front'));
        document.getElementById('showBack').addEventListener('click', () => switchSide('back'));
        
        // Photo upload
        photoInput.addEventListener('change', handlePhotoUpload);
        photoUpload.addEventListener('click', () => photoInput.click());
        
        // Control buttons
        document.getElementById('generateQR').addEventListener('click', generateQRCode);
        document.getElementById('saveCard').addEventListener('click', saveCard);
        document.getElementById('downloadCard').addEventListener('click', downloadCard);
        document.getElementById('resetCard').addEventListener('click', resetCard);
    }
    
    function selectTemplate(templateId) {
        // Remove active class from all templates
        templateCards.forEach(card => card.classList.remove('active'));
        
        // Add active class to selected template
        const selectedCard = document.querySelector(`[data-template="${templateId}"]`);
        if (selectedCard) {
            selectedCard.classList.add('active');
        }
        
        currentTemplate = templateId;
        loadTemplate(templateId);
        updateStatus(`Template "${templateId}" loaded - Click on text areas to edit`);
    }
    
    function loadTemplate(templateId) {
        const config = templateConfigs[templateId];
        if (!config) return;
        
        // Load the current side (front or back)
        loadSide(currentSide);
    }
    
    function switchSide(side) {
        currentSide = side;
        
        // Update button states
        const frontBtn = document.getElementById('showFront');
        const backBtn = document.getElementById('showBack');
        
        if (side === 'front') {
            frontBtn.className = 'btn btn-primary';
            backBtn.className = 'btn btn-secondary';
        } else {
            frontBtn.className = 'btn btn-secondary';
            backBtn.className = 'btn btn-primary';
        }
        
        loadSide(side);
        updateStatus(`Switched to ${side} side - Click on areas to edit`);
    }
    
    function loadSide(side) {
        const config = templateConfigs[currentTemplate];
        if (!config || !config[side]) return;
        
        const sideConfig = config[side];
        
        // Set background image
        cardPreview.style.backgroundImage = `url('${sideConfig.background}')`;
        
        // Clear existing editable elements
        cardPreview.innerHTML = '';
        editableElements = [];
        
        // Create editable areas for this side
        sideConfig.editableAreas.forEach(area => {
            createElement(area);
        });
    }
    
    function createElement(area) {
        let element;
        
        switch (area.type) {
            case 'text':
                element = createTextElement(area);
                break;
            case 'photo':
                element = createPhotoElement(area);
                break;
            case 'qr':
                element = createQRElement(area);
                break;
        }
        
        if (element) {
            cardPreview.appendChild(element);
            editableElements.push({ element, config: area });
        }
    }
    
    function createTextElement(config) {
        const textElement = document.createElement('div');
        textElement.className = 'editable-text';
        textElement.contentEditable = true;
        textElement.textContent = config.placeholder;
        textElement.style.left = config.x + 'px';
        textElement.style.top = config.y + 'px';
        textElement.style.width = config.width + 'px';
        textElement.style.height = config.height + 'px';
        textElement.style.fontSize = config.fontSize;
        textElement.style.color = config.color || '#333';
        textElement.style.fontWeight = config.fontWeight || 'normal';
        textElement.style.lineHeight = config.height + 'px';
        textElement.dataset.id = config.id;
        
        // Add event listeners
        textElement.addEventListener('focus', () => {
            textElement.classList.add('editing');
            // Always clear the text when clicked/focused
            textElement.textContent = '';
        });
        
        textElement.addEventListener('click', () => {
            // Clear text immediately when clicked
            textElement.textContent = '';
            textElement.focus();
        });
        
        textElement.addEventListener('blur', () => {
            textElement.classList.remove('editing');
            if (textElement.textContent.trim() === '') {
                textElement.textContent = config.placeholder;
            }
        });
        
        textElement.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                textElement.blur();
            }
        });
        
        return textElement;
    }
    
    function createPhotoElement(config) {
        const photoElement = document.createElement('div');
        photoElement.className = 'editable-photo';
        photoElement.style.left = config.x + 'px';
        photoElement.style.top = config.y + 'px';
        photoElement.style.width = config.width + 'px';
        photoElement.style.height = config.height + 'px';
        photoElement.dataset.id = config.id;
        
        // Create placeholder
        const placeholder = document.createElement('div');
        placeholder.className = 'placeholder-photo';
        placeholder.innerHTML = '<i class="fas fa-camera"></i><br>Click to add photo';
        photoElement.appendChild(placeholder);
        
        // Add click event
        photoElement.addEventListener('click', () => {
            photoInput.click();
        });
        
        return photoElement;
    }
    
    function createQRElement(config) {
        const qrElement = document.createElement('div');
        qrElement.className = 'qr-code';
        qrElement.style.left = config.x + 'px';
        qrElement.style.top = config.y + 'px';
        qrElement.style.width = config.width + 'px';
        qrElement.style.height = config.height + 'px';
        qrElement.dataset.id = config.id;
        qrElement.innerHTML = '<i class="fas fa-qrcode"></i><br>QR Code';
        
        return qrElement;
    }
    
    function handlePhotoUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            uploadedPhoto = e.target.result;
            
            // Update photo preview in sidebar
            photoPreview.src = uploadedPhoto;
            photoPreview.style.display = 'block';
            
            // Update photo in card if photo element exists
            const photoElement = document.querySelector('.editable-photo');
            if (photoElement) {
                photoElement.innerHTML = `<img src="${uploadedPhoto}" alt="Uploaded photo">`;
            }
            
            updateStatus('Photo uploaded successfully');
        };
        reader.readAsDataURL(file);
    }
    
    function generateCardId() {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 1000);
        currentCardId = `${timestamp}${random}`.slice(-10);
        cardIdInput.value = currentCardId;
    }
    
    function generateQRCode() {
        if (!currentCardId) {
            generateCardId();
        }
        
        // Simple QR code generation (you can integrate a real QR code library)
        const qrElement = document.querySelector('.qr-code');
        if (qrElement) {
            qrElement.innerHTML = `
                <div style="background: white; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 8px;">
                    QR: ${currentCardId}
                </div>
            `;
            updateStatus('QR Code generated with ID: ' + currentCardId);
        } else {
            updateStatus('No QR code area found in current template');
        }
    }
    
    
    function resetCard() {
        if (currentTemplate) {
            loadTemplate(currentTemplate);
            updateStatus('Card reset to template defaults');
        }
    }
    
    function saveCard() {
        if (!currentTemplate) {
            updateStatus('Please select a template first');
            return;
        }
        
        // Collect all data
        const cardData = {
            template: currentTemplate,
            cardId: currentCardId,
            elements: [],
            photo: uploadedPhoto
        };
        
        // Collect text data
        const textElements = document.querySelectorAll('.editable-text');
        textElements.forEach(element => {
            if (element.textContent && element.textContent !== element.dataset.placeholder) {
                cardData.elements.push({
                    type: 'text',
                    id: element.dataset.id,
                    content: element.textContent,
                    x: parseInt(element.style.left),
                    y: parseInt(element.style.top)
                });
            }
        });
        
        // Here you would send cardData to your server
        console.log('Card data to save:', cardData);
        updateStatus('Card saved successfully!');
        
        // You can integrate with your existing save_card.php here
        // fetch('save_card.php', { method: 'POST', body: JSON.stringify(cardData) })
    }
    
    function downloadCard() {
        if (!currentTemplate) {
            updateStatus('Please select a template first');
            return;
        }
        
        // Create a canvas to render the final card
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 600;
        canvas.height = 400;
        
        // Load background image
        const bgImg = new Image();
        bgImg.onload = function() {
            ctx.drawImage(bgImg, 0, 0, canvas.width, canvas.height);
            
            // Draw text elements
            const textElements = document.querySelectorAll('.editable-text');
            textElements.forEach(element => {
                if (element.textContent && element.textContent !== element.dataset.placeholder) {
                    ctx.font = element.style.fontSize + ' Arial';
                    ctx.fillStyle = '#333';
                    ctx.fillText(
                        element.textContent,
                        parseInt(element.style.left),
                        parseInt(element.style.top) + 20
                    );
                }
            });
            
            // Download the canvas as image
            const link = document.createElement('a');
            link.download = `card_${currentCardId}.png`;
            link.href = canvas.toDataURL();
            link.click();
            
            updateStatus('Card downloaded successfully!');
        };
        
        const config = templateConfigs[currentTemplate];
        bgImg.src = config.background;
    }
    
    function updateStatus(message) {
        statusText.textContent = message;
        console.log('Status:', message);
    }
});
