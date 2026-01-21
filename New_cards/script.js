// Card Editor JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('cardCanvas');
    const ctx = canvas.getContext('2d');
    let currentCard = null;
    let originalImage = null;
    let textElements = [];
    let selectedTextElement = null;
    let isDragging = false;
    let dragStart = { x: 0, y: 0 };

    // Initialize
    init();
    
    function init() {
        setupEventListeners();
        loadCards();
    }

    function setupEventListeners() {
        // Search
        document.getElementById('searchCards').addEventListener('input', filterCards);
        
        // Text input
        document.getElementById('textInput').addEventListener('input', updateText);
        
        // Font controls
        document.getElementById('fontFamily').addEventListener('change', updateFont);
        document.getElementById('fontSize').addEventListener('input', updateFontSize);
        document.getElementById('fontColor').addEventListener('change', updateFont);
        
        // Buttons
        document.getElementById('resetBtn').addEventListener('click', resetCard);
        document.getElementById('saveBtn').addEventListener('click', saveCard);
        document.getElementById('downloadBtn').addEventListener('click', downloadCard);
        
        // Canvas
        canvas.addEventListener('mousedown', handleMouseDown);
        canvas.addEventListener('mousemove', handleMouseMove);
        canvas.addEventListener('mouseup', handleMouseUp);
        canvas.addEventListener('dblclick', handleDoubleClick);
    }

    async function loadCards() {
        try {
            const response = await fetch('get_cards.php');
            const data = await response.json();
            if (data.success) {
                displayCards(data.cards);
            }
        } catch (error) {
            console.error('Error loading cards:', error);
        }
    }

    function displayCards(cards) {
        const cardList = document.getElementById('cardList');
        cardList.innerHTML = '';

        cards.forEach(function(card) {
            const cardItem = document.createElement('div');
            cardItem.className = 'card-item';
            cardItem.innerHTML = `
                <img src="${card.thumbnail}" alt="${card.name}">
                <div class="card-info">
                    <div class="card-name">${card.name}</div>
                    <div class="card-size">${card.size}</div>
                </div>
            `;

            cardItem.addEventListener('click', function() {
                selectCard(card);
            });

            cardList.appendChild(cardItem);
        });
    }

    function filterCards(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cardItems = document.querySelectorAll('.card-item');
        
        cardItems.forEach(function(item) {
            const cardName = item.querySelector('.card-name').textContent.toLowerCase();
            item.style.display = cardName.includes(searchTerm) ? 'flex' : 'none';
        });
    }

    async function selectCard(card) {
        try {
            // Update UI
            document.querySelectorAll('.card-item').forEach(item => item.classList.remove('active'));
            event.currentTarget.classList.add('active');
            document.getElementById('currentCardName').textContent = card.name;
            
            // Load image
            await loadCardImage(card.path);
            currentCard = card;
            
            // Enable controls
            document.getElementById('resetBtn').disabled = false;
            document.getElementById('saveBtn').disabled = false;
            document.getElementById('downloadBtn').disabled = false;
            
        } catch (error) {
            console.error('Error selecting card:', error);
        }
    }

    function loadCardImage(imagePath) {
        return new Promise(function(resolve, reject) {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            
            img.onload = function() {
                originalImage = img;
                
                // Set canvas size
                const maxWidth = 800;
                const maxHeight = 600;
                const ratio = Math.min(maxWidth / img.width, maxHeight / img.height);
                
                canvas.width = img.width * ratio;
                canvas.height = img.height * ratio;
                
                // Draw image
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                
                // Reset
                textElements = [];
                selectedTextElement = null;
                updateCanvasOverlay();
                
                resolve();
            };
            
            img.onerror = reject;
            img.src = imagePath;
        });
    }

    function handleMouseDown(e) {
        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const clickedElement = getTextElementAt(x, y);
        if (clickedElement) {
            selectTextElement(clickedElement);
            isDragging = true;
            dragStart = { x: x - clickedElement.x, y: y - clickedElement.y };
            return;
        }

        createTextElement(x, y);
    }

    function handleMouseMove(e) {
        if (!isDragging || !selectedTextElement) return;

        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        selectedTextElement.x = x - dragStart.x;
        selectedTextElement.y = y - dragStart.y;
        updateCanvasOverlay();
    }

    function handleMouseUp(e) {
        isDragging = false;
    }

    function handleDoubleClick(e) {
        if (selectedTextElement) {
            document.getElementById('textInput').focus();
        }
    }

    function getTextElementAt(x, y) {
        for (let i = textElements.length - 1; i >= 0; i--) {
            const element = textElements[i];
            if (x >= element.x && x <= element.x + element.width &&
                y >= element.y && y <= element.y + element.height) {
                return element;
            }
        }
        return null;
    }

    function createTextElement(x, y) {
        const element = {
            id: Date.now(),
            x: x,
            y: y,
            width: 200,
            height: 50,
            text: 'New Text',
            fontFamily: 'Arial',
            fontSize: 24,
            fontColor: '#000000',
            fontWeight: 'normal',
            textAlign: 'left'
        };

        textElements.push(element);
        selectTextElement(element);
        updateCanvasOverlay();
        document.getElementById('textInput').focus();
    }

    function selectTextElement(element) {
        selectedTextElement = element;
        updateTextControls();
        updateCanvasOverlay();
    }

    function updateTextControls() {
        if (!selectedTextElement) return;

        document.getElementById('textInput').value = selectedTextElement.text;
        document.getElementById('fontFamily').value = selectedTextElement.fontFamily;
        document.getElementById('fontSize').value = selectedTextElement.fontSize;
        document.getElementById('fontSizeValue').textContent = selectedTextElement.fontSize + 'px';
        document.getElementById('fontColor').value = selectedTextElement.fontColor;
    }

    function updateText() {
        if (selectedTextElement) {
            selectedTextElement.text = document.getElementById('textInput').value;
            updateCanvasOverlay();
        }
    }

    function updateFont() {
        if (selectedTextElement) {
            selectedTextElement.fontFamily = document.getElementById('fontFamily').value;
            selectedTextElement.fontColor = document.getElementById('fontColor').value;
            updateCanvasOverlay();
        }
    }

    function updateFontSize() {
        if (selectedTextElement) {
            selectedTextElement.fontSize = parseInt(document.getElementById('fontSize').value);
            document.getElementById('fontSizeValue').textContent = selectedTextElement.fontSize + 'px';
            updateCanvasOverlay();
        }
    }

    function updateCanvasOverlay() {
        const overlay = document.getElementById('canvasOverlay');
        overlay.innerHTML = '';

        textElements.forEach(function(element) {
            const textDiv = document.createElement('div');
            textDiv.className = 'text-element';
            textDiv.style.left = element.x + 'px';
            textDiv.style.top = element.y + 'px';
            textDiv.style.width = element.width + 'px';
            textDiv.style.height = element.height + 'px';

            if (element === selectedTextElement) {
                textDiv.style.display = 'block';
            } else {
                textDiv.style.display = 'none';
            }

            overlay.appendChild(textDiv);
        });
    }

    async function saveCard() {
        if (!currentCard || textElements.length === 0) return;

        try {
            showLoadingModal('Saving...');

            const tempCanvas = document.createElement('canvas');
            const tempCtx = tempCanvas.getContext('2d');
            
            tempCanvas.width = canvas.width;
            tempCanvas.height = canvas.height;

            tempCtx.drawImage(originalImage, 0, 0, tempCanvas.width, tempCanvas.height);

            textElements.forEach(function(element) {
                drawTextOnCanvas(tempCtx, element);
            });

            tempCanvas.toBlob(async function(blob) {
                const formData = new FormData();
                formData.append('image', blob, currentCard.name);
                formData.append('cardData', JSON.stringify({
                    cardName: currentCard.name,
                    textElements: textElements
                }));

                const response = await fetch('save_card.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    hideLoadingModal();
                    showSuccessModal('Card saved!');
                } else {
                    throw new Error(result.message || 'Failed to save');
                }
            }, 'image/jpeg', 0.9);

        } catch (error) {
            console.error('Error saving card:', error);
            hideLoadingModal();
        }
    }

    async function downloadCard() {
        if (!currentCard || textElements.length === 0) return;

        try {
            showLoadingModal('Preparing download...');

            const tempCanvas = document.createElement('canvas');
            const tempCtx = tempCanvas.getContext('2d');
            
            tempCanvas.width = canvas.width;
            tempCanvas.height = canvas.height;

            tempCtx.drawImage(originalImage, 0, 0, tempCanvas.width, tempCanvas.height);

            textElements.forEach(function(element) {
                drawTextOnCanvas(tempCtx, element);
            });

            tempCanvas.toBlob(function(blob) {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `edited_${currentCard.name}`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                hideLoadingModal();
            }, 'image/jpeg', 0.9);

        } catch (error) {
            console.error('Error downloading card:', error);
            hideLoadingModal();
        }
    }

    function drawTextOnCanvas(ctx, element) {
        ctx.save();
        
        ctx.font = `${element.fontWeight} ${element.fontSize}px ${element.fontFamily}`;
        ctx.fillStyle = element.fontColor;
        ctx.textAlign = element.textAlign;
        ctx.textBaseline = 'top';

        let x = element.x;
        if (element.textAlign === 'center') {
            x += element.width / 2;
        } else if (element.textAlign === 'right') {
            x += element.width;
        }

        ctx.fillText(element.text, x, element.y);
        
        ctx.restore();
    }

    function resetCard() {
        if (!currentCard) return;

        textElements = [];
        selectedTextElement = null;
        updateCanvasOverlay();
        updateTextControls();
        loadCardImage(currentCard.path);
    }

    function showLoadingModal(message) {
        const modal = document.getElementById('loadingModal');
        modal.querySelector('p').textContent = message;
        modal.style.display = 'block';
    }

    function hideLoadingModal() {
        document.getElementById('loadingModal').style.display = 'none';
    }

    function showSuccessModal(message) {
        const modal = document.getElementById('successModal');
        modal.querySelector('#successMessage').textContent = message;
        modal.style.display = 'block';
    }
});

// Global modal function
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
