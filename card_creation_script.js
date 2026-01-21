// Clean Card Creation Script

document.addEventListener("DOMContentLoaded", function() {
    // Canvas elements
    const frontCanvas = document.getElementById("frontCanvas");
    const backCanvas = document.getElementById("backCanvas");
    const frontCtx = frontCanvas.getContext("2d");
    const backCtx = backCanvas.getContext("2d");
  
    // State variables
    let frontImage = null;
    let backImage = null;
    let currentCardType = "";
    let frontPhotoImage = null;
    let frontPhotoImage2 = null;
    let isFrontPhotoImage2 = false;
    let idNumber = null;
    let isDataSaved = false;
    // user uploaded photo to draw on front
    // To prevent single card double id generate. It is not for combo card
    let isIdGenerated = false;
  
    // Combo card toggle states (both cards are enabled by default)
    let comboToggleStates = {
      blueDog: true,
      handler: true,
    };
  
    // Front-side layout (percentages relative to canvas size)
    const FRONT_LAYOUT = {
      // Fixed-size photo; move 5px left and 10px lower
      photo: { xPct: 0.038, yPct: 0.14, wPx: 100, hPx: 140, radius: 12, offsetX: -5, offsetY: -45 },
      // Names: animal +3px lower, handler +2px higher - updated font sizes
      animal: { xPct: 0.375, yPct: 0.5, maxWidthPct: 0.36, baseFontPx: 12.47, minFontPx: 9, offsetX: 25, offsetY: -7 },
      handler: { xPct: 0.375, yPct: 0.62, maxWidthPct: 0.36, baseFontPx: 12.47, minFontPx: 9, offsetX: 25, offsetY: -2 },
    };
  
    // Initialize
    init();
  
    function init() {
      setupEventListeners();
      console.log("Card Creation Tool initialized - VERSION 6.5");
      console.log("Front canvas:", frontCanvas);
      console.log("Back canvas:", backCanvas);
    }
  
    function setupEventListeners() {
      // Card type selection
      // Once a card type is selected, call handleCardSelection to load the specific canvas depending on the card type
      document.getElementById("cardTypeSelect").addEventListener("change", handleCardSelection);
  
      // this for combo aditional input
  
      // Once a card type is selected, call handleCardSelection to load the specific canvas depending on the card type
      document.getElementById("addExtraInput").addEventListener("click", addOneMoreInput);
      document.getElementById("removeExtraInput").addEventListener("click", removeOneMoreInput);
      // Control buttons
      document.getElementById("generateIDBtn").addEventListener("click", generateRandomID);
      document.getElementById("addQRBtn").addEventListener("click", addQRCode);
      document.getElementById("saveBtn").addEventListener("click", saveCard);
      document.getElementById("downloadBtn").addEventListener("click", downloadCard);
      document.getElementById("resetBtn").addEventListener("click", resetCard);
  
      // Combo card toggle buttons
      const toggleBlueDogBtn = document.getElementById("toggleBlueDog");
      const toggleHandlerBtn = document.getElementById("toggleHandler");
  
      if (toggleBlueDogBtn) {
        toggleBlueDogBtn.addEventListener("click", function() {
          toggleComboCard("blueDog", toggleBlueDogBtn);
        });
      }
  
      if (toggleHandlerBtn) {
        toggleHandlerBtn.addEventListener("click", function() {
          toggleComboCard("handler", toggleHandlerBtn);
        });
      }
  
      // Photo upload & names inputs
      const photoInput = document.getElementById("photoInput");
      const photoInput2 = document.getElementById("photoInput2");
      const animalNameInput = document.getElementById("animalNameInput");
      const handlerNameInput = document.getElementById("handlerNameInput");
      const addressInput = document.getElementById("addressInput");
      const telephoneInput = document.getElementById("telephoneInput");
      const beneficiaryCountInput = document.getElementById("beneficiaryCountInput");
  
      // Child identification fields
      const childNameInput = document.getElementById("childNameInput");
      const childDOBInput = document.getElementById("childDOBInput");
      const childSexInput = document.getElementById("childSexInput");
      const childHairInput = document.getElementById("childHairInput");
      const childEyesInput = document.getElementById("childEyesInput");
      const childHeightInput = document.getElementById("childHeightInput");
      const childWeightInput = document.getElementById("childWeightInput");
      const childMomInput = document.getElementById("childMomInput");
      const childDadInput = document.getElementById("childDadInput");
      const childParentsInput = document.getElementById("childParentsInput");
      const childExpiryInput = document.getElementById("childExpiryInput");
      const childAdditionalInput = document.getElementById("childAdditionalInput");
      const childAddressInput2 = document.getElementById("childAddressInput2");
      const childBeneficiaryCountInput = document.getElementById("childBeneficiaryCountInput");
  
      // Autism card fields
      const autismNameInput = document.getElementById("autismNameInput");
      const autismContactInput = document.getElementById("autismContactInput");
      const autismNotesInput = document.getElementById("autismNotesInput");
      const autismBeneficiaryCountInput = document.getElementById("autismBeneficiaryCountInput");
  
      if (photoInput) {
        photoInput.addEventListener("change", handlePhotoUpload);
      }
  
      if (photoInput2) {
        photoInput2.addEventListener("change", handlePhotoUpload);
      }
      if (animalNameInput) {
        animalNameInput.addEventListener("input", function() {
          if (
            currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
            || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
          ) {
            redrawComboCanvases();
          } else {
            redrawFrontCanvas();
          }
        });
      }
      if (handlerNameInput) {
        handlerNameInput.addEventListener("input", function() {
          if (
            currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
            || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
          ) {
            redrawComboCanvases();
          } else {
            redrawFrontCanvas();
          }
        });
      }
      if (addressInput) {
        addressInput.addEventListener("input", function() {
          if (
            currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
            || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
          ) {
            redrawComboCanvases();
          } else {
            redrawFrontCanvas();
          }
        });
      }
  
      if (telephoneInput) {
        telephoneInput.addEventListener("input", function() {
          if (
            currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
            || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
          ) {
            redrawComboCanvases();
          } else {
            redrawFrontCanvas();
          }
        });
      }
  
      // Child field event listeners
      if (childNameInput) childNameInput.addEventListener("input", redrawFrontCanvas);
      if (childDOBInput) childDOBInput.addEventListener("input", redrawFrontCanvas);
      if (childSexInput) childSexInput.addEventListener("input", redrawFrontCanvas);
      if (childHairInput) childHairInput.addEventListener("input", redrawFrontCanvas);
      if (childEyesInput) childEyesInput.addEventListener("input", redrawFrontCanvas);
      if (childHeightInput) childHeightInput.addEventListener("input", redrawFrontCanvas);
      if (childWeightInput) childWeightInput.addEventListener("input", redrawFrontCanvas);
      if (childMomInput) childMomInput.addEventListener("input", redrawFrontCanvas);
      if (childDadInput) childDadInput.addEventListener("input", redrawFrontCanvas);
      if (childParentsInput) childParentsInput.addEventListener("input", redrawFrontCanvas);
      if (childExpiryInput) childExpiryInput.addEventListener("input", redrawBackCanvas);
      if (childAdditionalInput) childAdditionalInput.addEventListener("input", redrawBackCanvas);
      if (childAddressInput2) childAddressInput2.addEventListener("input", redrawFrontCanvas);
  
      // Autism card event listeners
      if (autismNameInput) autismNameInput.addEventListener("input", redrawFrontCanvas);
      if (autismContactInput) autismContactInput.addEventListener("input", redrawFrontCanvas);
      if (autismNotesInput) autismNotesInput.addEventListener("input", redrawFrontCanvas);
  
      // Emergency ID card event listeners
      const emergencyDOBInput = document.getElementById("emergencyDOBInput");
      const emergencyNameInput = document.getElementById("emergencyNameInput");
      const emergencyAddressInput = document.getElementById("emergencyAddressInput");
      const emergencyHeightInput = document.getElementById("emergencyHeightInput");
      const emergencyBloodTypeInput = document.getElementById("emergencyBloodTypeInput");
      const emergencyWeightInput = document.getElementById("emergencyWeightInput");
      const emergencyContactsInput = document.getElementById("emergencyContactsInput");
      const emergencyContactsInput2 = document.getElementById("emergencyContactsInput2");
      const emergencyContactsInputNumber = document.getElementById("emergencyContactsInputNumber");
      const emergencyContactsInputNumber2 = document.getElementById("emergencyContactsInputNumber2");
  
  
      const emergencyAllergiesInput = document.getElementById("emergencyAllergiesInput");
      const emergencyMedicalConcernsInput = document.getElementById("emergencyMedicalConcernsInput");
      const emergencyNotesInput = document.getElementById("emergencyNotesInput");
      const emergencyBeneficiaryCountInput = document.getElementById("emergencyBeneficiaryCountInput");
  
      if (emergencyDOBInput) emergencyDOBInput.addEventListener("input", redrawFrontCanvas);
      if (emergencyNameInput) emergencyNameInput.addEventListener("input", redrawFrontCanvas);
      if (emergencyAddressInput) emergencyAddressInput.addEventListener("input", redrawFrontCanvas);
      if (emergencyHeightInput) emergencyHeightInput.addEventListener("input", redrawFrontCanvas);
      if (emergencyBloodTypeInput) emergencyBloodTypeInput.addEventListener("input", redrawFrontCanvas);
      if (emergencyWeightInput) emergencyWeightInput.addEventListener("input", redrawFrontCanvas);
      if (emergencyContactsInput) emergencyContactsInput.addEventListener("input", redrawFrontCanvas);
      if (emergencyContactsInput2) emergencyContactsInput2.addEventListener("input", redrawFrontCanvas);
      if (emergencyAllergiesInput) emergencyAllergiesInput.addEventListener("input", redrawBackCanvas);
      if (emergencyMedicalConcernsInput) emergencyMedicalConcernsInput.addEventListener("input", redrawBackCanvas);
      if (emergencyNotesInput) emergencyNotesInput.addEventListener("input", redrawBackCanvas);
      if (emergencyContactsInputNumber) emergencyContactsInputNumber.addEventListener("input", redrawBackCanvas);
      if (emergencyContactsInputNumber2) emergencyContactsInputNumber2.addEventListener("input", redrawBackCanvas);
  
      // Canvas click events removed - no longer needed
      // frontCanvas.addEventListener('click', function(e) {
      //     if (frontImage) addTextToCanvas(e, 'front');
      // });
  
      // backCanvas.addEventListener('click', function(e) {
      //     if (backImage) addTextToCanvas(e, 'back');
      // });
  
      console.log("Event listeners setup complete");
    }
    // Extra Field
  
    function addOneMoreInput(event) {
      document.getElementById("photoInput2Label").style.display = "block";
      event.target.style.display = "none";
      document.getElementById("removeExtraInput").style.display = "block";
      isFrontPhotoImage2 = true;
    }
    function removeOneMoreInput(event) {
      document.getElementById("photoInput2Label").style.display = "none";
      event.target.style.display = "none";
      document.getElementById("addExtraInput").style.display = "block";
      isFrontPhotoImage2 = false;
      frontPhotoImage2 = null;
  
      redrawComboCanvases();
    }
  
    function handleCardSelection() {
          // If already once id generated later change tye card . to clear this id just isIdGenerated = false
          isIdGenerated = false;
  
          // If already save then reset the save status on change the card 
          isDataSaved = false;
  
          const selectElement = document.getElementById("cardTypeSelect");
          const selectedType = selectElement.value;
          
      // This is not organised code But if selected card type is child_identification_red then one field is hidden
      if (selectedType === "child_identification_red") {
  
          const childExpiryInput = document.getElementById('childExpiryInputRemove');
          const childAddressInput2 = document.getElementById('childAddressInputRemove2');
          const childAdditionalInputLabel = document.getElementById('childAdditionalInputLabel');
          const childAdditionalInput = document.getElementById('childAdditionalInput');
  
          // Hide expiry input, show address input
          childAddressInput2.style.display = "block";
          childExpiryInput.style.display = "none";
  
          // Update label and placeholder only (do not touch value)
          childAdditionalInputLabel.innerText = "Additional Information:";
          childAdditionalInput.value = null;
          childAdditionalInput.setAttribute("placeholder", "Additional Information:");
  
      } else {
  
          const childExpiryInput = document.getElementById('childExpiryInputRemove');
          const childAddressInput2 = document.getElementById('childAddressInputRemove2');
          const childAdditionalInputLabel = document.getElementById('childAdditionalInputLabel');
          const childAdditionalInput = document.getElementById('childAdditionalInput');
  
          // Hide second address input, show expiry input
          childAddressInput2.style.display = "block";
          childExpiryInput.style.display = "block";
  
          // Update label and placeholder only (do not touch value)
          // childAdditionalInputLabel.innerText = "Address";
          // childAdditionalInput.setAttribute("placeholder", "Address");
      }
  
  
      // This is not organised code But if selected 
  
  
          console.log("Card type selected:", selectedType);
          console.log("Selected type value:", selectedType);
  
          if (!selectedType) {
            resetCard();
            return;
          }
  
          currentCardType = selectedType;
          console.log("Current card type set to:", currentCardType);
  
          // Switch input fields based on card type
          switchInputFields(selectedType);
  
          if (
            selectedType === "combo_dog" || 
            selectedType === "combo_red_dog" || 
            selectedType === "combo_emotional_dog" || 
            selectedType === "combo_emotional_cat"
          ) {
            // Loading combo cards for: combo_dog, combo_red_dog, combo_emotional_dog, combo_emotional_cat
            loadComboCards();
          } else {
          // Loading card pairs for other card types specially that not combo
            loadCardPair(selectedType);
          }
    }
  
    function switchInputFields(cardType) {
      const animalFields = document.getElementById("animalFields");
      const childFields = document.getElementById("childFields");
      const autismFields = document.getElementById("autismFields");
      const emergencyFields = document.getElementById("emergencyFields");
  
      // Hide all fields first
      if (animalFields) animalFields.style.display = "none";
      if (childFields) childFields.style.display = "none";
      if (autismFields) autismFields.style.display = "none";
      if (emergencyFields) emergencyFields.style.display = "none";
  
      if (cardType === "child_identification" || cardType === "child_identification_red") {
        // Show child fields
        if (childFields) childFields.style.display = "block";
        console.log("Switched to child identification fields");
      } else if (cardType === "autism_card_infinity" || cardType === "autism_card_puzzle") {
        // Show autism fields
        if (autismFields) autismFields.style.display = "block";
        console.log("Switched to autism card fields");
      } else if (
        cardType === "combo_dog" || cardType === "combo_red_dog" || cardType === "combo_emotional_dog"
        || cardType === "combo_emotional_cat"
      ) {
        // Show animal fields for combo cards
        if (animalFields) animalFields.style.display = "block";
        console.log("Switched to combo card fields");
      } else if (cardType === "emergency_id_card") {
        // Show emergency fields
        if (emergencyFields) emergencyFields.style.display = "block";
        console.log("Switched to emergency ID card fields");
      } else {
        // Show animal fields (for all other card types)
        if (animalFields) animalFields.style.display = "block";
        console.log("Switched to animal card fields");
      }
    }
  
    async function loadCardPair(cardType) {
  
      clearAllCanvases();
  
      console.log("Loading card pair for:", cardType);
      console.log("Card type being processed:", cardType);
  
      // This function Load Card Display default is false;
      toggleCardDisplay();
  // Hidden Code Here
  /*
      // First, get all available cards from the server
      console.log("Fetching cards from server for card type:", cardType);
      await fetch("get_cards.php")
        .then(response => {
          console.log("Server response status:", response.status);
          return response.json();
        })
        .then(data => {
          console.log("Server response data:", data);
          if (data.success) {
            console.log("Available cards from server:", data.cards.map(c => c.name));
            findAndLoadMatchingCards(cardType, data.cards);
          } else {
            console.error("Failed to load cards from server");
  
            // Fallback to hardcoded paths
            loadWithHardcodedPaths(cardType);
          }
        })
        .catch(error => {
          console.error("Error fetching cards:", error);
          // Fallback to hardcoded paths
          loadWithHardcodedPaths(cardType);
        });
  */
  
  // Fallback to hardcoded paths
        loadWithHardcodedPaths(cardType);
    }
  
    function findAndLoadMatchingCards(cardType, availableCards) {
      console.log("Finding matching cards for:", cardType);
  
      let frontFile = null;
      let backFile = null;
  
      // Find front file based on card type
      if (cardType === "blue_dog") {
        frontFile = availableCards.find(card =>
          card.name.toLowerCase().includes("front")
          && card.name.toLowerCase().includes("dog")
          && card.name.toLowerCase().includes("blue")
        );
        backFile = availableCards.find(card =>
          card.name.toLowerCase().includes("back")
          && (card.name.toLowerCase().includes("dog") || card.name.toLowerCase().includes("emotional"))
        );
      } else if (cardType === "red_dog") {
        frontFile = availableCards.find(card =>
          card.name.toLowerCase().includes("front")
          && card.name.toLowerCase().includes("dog")
          && card.name.toLowerCase().includes("red")
        );
        backFile = availableCards.find(card =>
          card.name.toLowerCase().includes("back")
          && card.name.toLowerCase().includes("red")
        );
      } else if (cardType === "emotional_dog") {
        console.log("Processing emotional_dog card type...");
  
        // Look for emotional dog front side file - try exact match first
        frontFile = availableCards.find(card => {
          console.log("Checking file:", card.name, "against emotional_dog_front_side.jpg");
          return card.name.toLowerCase() === "emotional_dog_front_side.jpg";
        });
  
        console.log("Exact match result:", frontFile ? frontFile.name : "NOT FOUND");
  
        // If exact match not found, try partial match
        if (!frontFile) {
          console.log("Trying partial match...");
          frontFile = availableCards.find(card => {
            const name = card.name.toLowerCase();
            console.log("Checking partial match for:", name);
            return name.includes("emotional_dog_front_side");
          });
        }
  
        console.log("Emotional dog front file search result:", frontFile ? frontFile.name : "NOT FOUND");
  
        backFile = availableCards.find(card =>
          card.name.toLowerCase().includes("back")
          && card.name.toLowerCase().includes("emotional")
        );
      } else if (cardType === "blue_cat") {
        // Try to find the cat handler file specifically
        frontFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          return name.includes("front")
            && name.includes("cat")
            && name.includes("handler")
            && name.includes("blue");
        });
  
        // If handler file not found, try any cat blue front file
        if (!frontFile) {
          frontFile = availableCards.find(card => {
            const name = card.name.toLowerCase();
            return name.includes("front")
              && name.includes("cat")
              && name.includes("blue");
          });
        }
  
        console.log("Blue cat front file search result:", frontFile ? frontFile.name : "NOT FOUND");
  
        backFile = availableCards.find(card =>
          card.name.toLowerCase().includes("back")
          && card.name.toLowerCase().includes("emotional")
        );
      } else if (cardType === "combo_dog") {
        console.log("Processing combo_dog card type...");
  
        // For combo cards, we'll load the blue dog front and blue dog back initially
        // The emotional dog will be loaded separately
        frontFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for combo blue dog front");
          return name.includes("front")
            && name.includes("dog")
            && name.includes("blue");
        });
  
        console.log("Combo blue dog front file search result:", frontFile ? frontFile.name : "NOT FOUND");
  
        backFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for combo blue dog back");
          return name.includes("back")
            && name.includes("dog")
            && name.includes("blue");
        });
  
        console.log("Combo blue dog back file search result:", backFile ? backFile.name : "NOT FOUND");
      } else if (cardType === "red_cat") {
        frontFile = availableCards.find(card =>
          card.name.toLowerCase().includes("front")
          && card.name.toLowerCase().includes("cat")
          && card.name.toLowerCase().includes("red")
        );
        backFile = availableCards.find(card =>
          card.name.toLowerCase().includes("back")
          && (card.name.toLowerCase().includes("cat") || card.name.toLowerCase().includes("emotional"))
        );
      } else if (cardType === "child_identification") {
        console.log("Processing child_identification card type...");
  
        // Look for child identification front side file
        frontFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for child identification front");
          return name.includes("child_identification")
            && name.includes("front")
            && name.includes("blue");
        });
  
        console.log("Child identification front file search result:", frontFile ? frontFile.name : "NOT FOUND");
  
        // Look for child identification back side file
        backFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for child identification back");
          return name.includes("child_identification")
            && name.includes("back")
            && name.includes("blue");
        });
  
        console.log("Child identification back file search result:", backFile ? backFile.name : "NOT FOUND");
      } else if (cardType === "child_identification_red") {
        console.log("Processing child_identification_red card type...");
  
        // Look for red child identification front side file
        frontFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for red child identification front");
          return name.includes("child_identification")
            && name.includes("front")
            && name.includes("red");
        });
  
        console.log("Red child identification front file search result:", frontFile ? frontFile.name : "NOT FOUND");
  
        // Look for red child identification back side file
        backFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for red child identification back");
          return name.includes("child_identification")
            && name.includes("back")
            && name.includes("red");
        });
  
        console.log("Red child identification back file search result:", backFile ? backFile.name : "NOT FOUND");
      } else if (cardType === "autism_card_infinity") {
        console.log("Processing autism_card_infinity card type...");
  
        // Look for autism card front side file
        frontFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for autism card front");
          return name.includes("autism_card")
            && name.includes("front");
        });
  
        console.log("Autism card front file search result:", frontFile ? frontFile.name : "NOT FOUND");
  
        // Look for autism card back side file with infinity sign
        backFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for autism card back (infinity)");
          return name.includes("autism_card")
            && name.includes("back")
            && name.includes("infinity");
        });
  
        console.log("Autism card infinity back file search result:", backFile ? backFile.name : "NOT FOUND");
      } else if (cardType === "autism_card_puzzle") {
        console.log("Processing autism_card_puzzle card type...");
  
        // Look for autism card front side file
        frontFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for autism card front");
          return name.includes("autism_card")
            && name.includes("front");
        });
  
        console.log("Autism card front file search result:", frontFile ? frontFile.name : "NOT FOUND");
  
        // Look for autism card back side file with puzzle piece
        backFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for autism card back (puzzle)");
          return name.includes("autism_card")
            && name.includes("back")
            && name.includes("puzzle");
        });
  
        console.log("Autism card puzzle back file search result:", backFile ? backFile.name : "NOT FOUND");
      } else if (cardType === "emergency_id_card") {
        console.log("Processing emergency_id_card card type...");
  
        // Look for emergency ID card front side file
        frontFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for emergency ID card front");
          return name.includes("emergency")
            && name.includes("front");
        });
  
        console.log("Emergency ID card front file search result:", frontFile ? frontFile.name : "NOT FOUND");
  
        // Look for emergency ID card back side file
        backFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for emergency ID card back");
          return name.includes("emergency")
            && name.includes("back");
        });
  
        console.log("Emergency ID card back file search result:", backFile ? backFile.name : "NOT FOUND");
      } else if (cardType === "service_dog_handler") {
        console.log("Processing service_dog_handler card type...");
  
        // Look for service dog handler front side file - try exact match first
        frontFile = availableCards.find(card => {
          console.log("Checking file:", card.name, "against front side service dog handler blue.jpg");
          return card.name.toLowerCase() === "front side service dog handler blue.jpg";
        });
  
        console.log("Exact match result:", frontFile ? frontFile.name : "NOT FOUND");
  
        // If exact match not found, try partial match
        if (!frontFile) {
          console.log("Trying partial match...");
          frontFile = availableCards.find(card => {
            const name = card.name.toLowerCase();
            console.log("Checking partial match for:", name);
            return name.includes("front")
              && name.includes("service")
              && name.includes("dog")
              && name.includes("handler");
          });
        }
  
        console.log("Service dog handler front file search result:", frontFile ? frontFile.name : "NOT FOUND");
  
        // Look for service dog handler back side file (same as blue dog)
        backFile = availableCards.find(card => {
          const name = card.name.toLowerCase();
          console.log("Checking file:", card.name, "for service dog handler back");
          return name.includes("back")
            && (name.includes("dog") || name.includes("emotional"));
        });
  
        console.log("Service dog handler back file search result:", backFile ? backFile.name : "NOT FOUND");
      }
  
      console.log("Available cards:", availableCards.map(c => c.name));
      console.log("Looking for files for card type:", cardType);
      if (cardType === "emotional_dog") {
        console.log(
          "Files containing \"emotional\":",
          availableCards.filter(c => c.name.toLowerCase().includes("emotional")).map(c => c.name),
        );
        console.log(
          "Files containing \"dog\":",
          availableCards.filter(c => c.name.toLowerCase().includes("dog")).map(c => c.name),
        );
      } else if (cardType === "blue_cat") {
        console.log(
          "Files containing \"cat\":",
          availableCards.filter(c => c.name.toLowerCase().includes("cat")).map(c => c.name),
        );
        console.log(
          "Files containing \"blue\":",
          availableCards.filter(c => c.name.toLowerCase().includes("blue")).map(c => c.name),
        );
      } else if (cardType === "service_dog_handler") {
        console.log(
          "Files containing \"service\":",
          availableCards.filter(c => c.name.toLowerCase().includes("service")).map(c => c.name),
        );
        console.log(
          "Files containing \"handler\":",
          availableCards.filter(c => c.name.toLowerCase().includes("handler")).map(c => c.name),
        );
        console.log(
          "Files containing \"front\":",
          availableCards.filter(c => c.name.toLowerCase().includes("front")).map(c => c.name),
        );
      }
      console.log("Found front file:", frontFile ? frontFile.name : "NOT FOUND");
      console.log("Found back file:", backFile ? backFile.name : "NOT FOUND");
  
      // Update title
      let displayTitle = cardType.replace("_", " ").toUpperCase();
      document.getElementById("cardTitle").textContent = `Editing: ${displayTitle}`;
  
      // Load found files
      if (frontFile) {
        console.log("Loading front file:", frontFile.path || frontFile.name);
        loadImage(frontFile.path || frontFile.name, "front");
      } else {
        console.error("No front file found for:", cardType);
        console.log("Trying fallback to hardcoded paths...");
        // Fallback to hardcoded paths
        if (cardType === "blue_cat") {
          console.log("Loading blue cat fallback...");
          loadImage("./front side emotional support cat handler blue.jpg", "front");
        } else if (cardType === "emotional_dog") {
          console.log("Loading emotional dog fallback...");
          loadImage("emotional_dog_front_side.jpg", "front");
        } else if (cardType === "child_identification") {
          console.log("Loading child identification fallback...");
          loadImage("child_identification_id_card_blue_front_side.jpg", "front");
        }
      }
  
      if (backFile) {
        console.log("Loading back file:", backFile.path || backFile.name);
        loadImage(backFile.path || backFile.name, "back");
      } else {
        console.error("No back file found for:", cardType);
        // Try any back file as fallback
        const anyBack = availableCards.find(card => card.name.toLowerCase().includes("back"));
        if (anyBack) {
          console.log("Using fallback back file:", anyBack.name);
          loadImage(anyBack.path || anyBack.name, "back");
        }
      }
  
      // Enable controls
      enableControls();
    }
  
    // Special Note For LoadcomboCards. Load Combo CArd does not depend on LoadCardPair and it's dependended function . 
    // =========================================================================
    function loadComboCards() {
  
      // Clear Existing Canvases
      clearAllCanvases();
  
      // Check which combo card is calling this function
      console.log("Current card type:", currentCardType);
  
      // Determine which combo type we're loading
      const isRedCombo = currentCardType === "combo_red_dog";
      const isEmotionalCombo = currentCardType === "combo_emotional_dog";
      const isEmotionalCatCombo = currentCardType === "combo_emotional_cat";
  
      // Update title
      if (isRedCombo) {
        document.getElementById("cardTitle").textContent = "Editing: COMBO: SERVICE DOG (RED) + SERVICE DOG HANDLER RED";
        document.getElementById("comboDogTitle").innerHTML = "<i class=\"fas fa-dog\"></i> Service Dog (Red)";
        document.getElementById("comboHandlerTitle").innerHTML =
          "<i class=\"fas fa-user-tie\"></i> Service Dog Handler Red";
      } else if (isEmotionalCombo) {
        document.getElementById("cardTitle").textContent = "Editing: COMBO: EMOTIONAL DOG + EMOTIONAL SUPPORT DOG";
        document.getElementById("comboDogTitle").innerHTML = "<i class=\"fas fa-dog\"></i> Emotional Dog";
        document.getElementById("comboHandlerTitle").innerHTML = "<i class=\"fas fa-heart\"></i> Emotional Support Dog";
      } else if (isEmotionalCatCombo) {
        document.getElementById("cardTitle").textContent = "Editing: COMBO: EMOTIONAL SUPPORT CAT + CAT HANDLER";
        document.getElementById("comboDogTitle").innerHTML = "<i class=\"fas fa-cat\"></i> Emotional Support Cat";
        document.getElementById("comboHandlerTitle").innerHTML = "<i class=\"fas fa-user-tie\"></i> Cat Handler";
      } else {
        document.getElementById("cardTitle").textContent = "Editing: COMBO: SERVICE DOG (BLUE) + SERVICE DOG HANDLER";
        document.getElementById("comboDogTitle").innerHTML = "<i class=\"fas fa-dog\"></i> Service Dog (Blue)";
        document.getElementById("comboHandlerTitle").innerHTML = "<i class=\"fas fa-user-tie\"></i> Service Dog Handler";
      }
  
      // if you wanto show comob card display then pass true else default value false
      toggleCardDisplay(true);
  
      // Get combo canvas elements
      const comboBlueFrontCanvas = document.getElementById("comboBlueFrontCanvas");
      const comboBlueBackCanvas = document.getElementById("comboBlueBackCanvas");
      const comboEmotionalFrontCanvas = document.getElementById("comboEmotionalFrontCanvas");
      const comboEmotionalBackCanvas = document.getElementById("comboEmotionalBackCanvas");
  
      const comboBlueFrontCtx = comboBlueFrontCanvas.getContext("2d");
      const comboBlueBackCtx = comboBlueBackCanvas.getContext("2d");
      const comboEmotionalFrontCtx = comboEmotionalFrontCanvas.getContext("2d");
      const comboEmotionalBackCtx = comboEmotionalBackCanvas.getContext("2d");
  
      // Set image sources based on combo type
      let dogFrontSrc, dogBackSrc, handlerFrontSrc, handlerBackSrc;
  
      if (isRedCombo) {
        dogFrontSrc = "front side service dog red.jpg";
        dogBackSrc = "back side of service dog red.jpg";
        handlerFrontSrc = "front side service dog handler red.jpg";
        handlerBackSrc = "back side of service dog red.jpg";
      } else if (isEmotionalCombo) {
        dogFrontSrc = "emotional_dog_front_side.jpg";
        dogBackSrc = "back side emotional_dog.jpg";
        handlerFrontSrc = "emotional_dog_support.jpg";
        handlerBackSrc = "back side emotional_dog.jpg";
      } else if (isEmotionalCatCombo) {
        dogFrontSrc = "front side emotional support cat blue.jpg";
        dogBackSrc = "back side emotional_dog.jpg";
        handlerFrontSrc = "front side emotional support cat handler blue.jpg";
        handlerBackSrc = "back side emotional_dog.jpg";
      } else {
        dogFrontSrc = "front side service dog blue.jpg";
        dogBackSrc = "back side emotional_dog.jpg";
        handlerFrontSrc = "front side service dog handler blue.jpg";
        handlerBackSrc = "back side emotional_dog.jpg";
      }
  
      // Load Service Dog front
      const blueDogFrontImg = new Image();
  
      blueDogFrontImg.onload = function() {
        comboBlueFrontCtx.drawImage(blueDogFrontImg, 0, 0, comboBlueFrontCanvas.width, comboBlueFrontCanvas.height);
        comboBlueFrontCanvas.style.display = "block";
        comboBlueFrontCanvas.nextElementSibling.style.display = "none";
      };
      blueDogFrontImg.onerror = function() {
        console.error("Failed to load Dog front image:", dogFrontSrc);
        alert("Failed to load Dog front image. Please check if the file exists.");
      };
      blueDogFrontImg.src = dogFrontSrc;
      console.log("Loading Dog front image:", dogFrontSrc);
  
      // Load Service Dog back
      const blueDogBackImg = new Image();
      blueDogBackImg.onload = function() {
  
        comboBlueBackCtx.drawImage(blueDogBackImg, 0, 0, comboBlueBackCanvas.width, comboBlueBackCanvas.height);
        comboBlueBackCanvas.style.display = "block";
        comboBlueBackCanvas.nextElementSibling.style.display = "none";
      };
      blueDogBackImg.onerror = function() {
        console.error("Failed to load Dog back image:", dogBackSrc);
        alert("Failed to load Dog back image. Please check if the file exists.");
      };
      blueDogBackImg.src = dogBackSrc;
      console.log("Loading Dog back image:", dogBackSrc);
  
      // Load Service Dog Handler front
      const serviceDogHandlerFrontImg = new Image();
      serviceDogHandlerFrontImg.onload = function() {
        comboEmotionalFrontCtx.drawImage(
          serviceDogHandlerFrontImg,
          0,
          0,
          comboEmotionalFrontCanvas.width,
          comboEmotionalFrontCanvas.height,
        );
        comboEmotionalFrontCanvas.style.display = "block";
        comboEmotionalFrontCanvas.nextElementSibling.style.display = "none";
      };
      serviceDogHandlerFrontImg.onerror = function() {
        console.error("Failed to load Service Dog Handler front image:", handlerFrontSrc);
        alert("Failed to load Service Dog Handler front image. Please check if the file exists.");
      };
      serviceDogHandlerFrontImg.src = handlerFrontSrc;
      console.log("Loading Service Dog Handler front image:", handlerFrontSrc);
  
      // Load Service Dog Handler back
      const serviceDogHandlerBackImg = new Image();
      serviceDogHandlerBackImg.onload = function() {
        console.log("Service Dog Handler back image loaded successfully");
        comboEmotionalBackCtx.drawImage(
          serviceDogHandlerBackImg,
          0,
          0,
          comboEmotionalBackCanvas.width,
          comboEmotionalBackCanvas.height,
        );
        comboEmotionalBackCanvas.style.display = "block";
        comboEmotionalBackCanvas.nextElementSibling.style.display = "none";
      };
      serviceDogHandlerBackImg.onerror = function() {
        console.error("Failed to load Service Dog Handler back image:", handlerBackSrc);
        alert("Failed to load Service Dog Handler back image. Please check if the file exists.");
      };
      serviceDogHandlerBackImg.src = handlerBackSrc;
      console.log("Loading Service Dog Handler back image:", handlerBackSrc);
  
      // Store references for later use
      window.comboCanvases = {
        blueFront: {
          canvas: comboBlueFrontCanvas,
          ctx: comboBlueFrontCtx,
          img: blueDogFrontImg,
          qrImg: null,
          qrSize: 0,
          qrMargin: 0,
          idNumber: null,
        },
        blueBack: {
          canvas: comboBlueBackCanvas,
          ctx: comboBlueBackCtx,
          img: blueDogBackImg,
          qrImg: null,
          qrSize: 0,
          qrMargin: 0,
          idNumber: null,
        },
        emotionalFront: {
          canvas: comboEmotionalFrontCanvas,
          ctx: comboEmotionalFrontCtx,
          img: serviceDogHandlerFrontImg,
          qrImg: null,
          qrSize: 0,
          qrMargin: 0,
          idNumber: null,
        },
        emotionalBack: {
          canvas: comboEmotionalBackCanvas,
          ctx: comboEmotionalBackCtx,
          img: serviceDogHandlerBackImg,
          qrImg: null,
          qrSize: 0,
          qrMargin: 0,
          idNumber: null,
        },
      };
  
      // Redraw combo canvases with any existing form data
      setTimeout(() => {
        redrawComboCanvases();
      }, 100); // Small delay to ensure images are loaded
  
      // Enable controls
      enableControls();
    }
    // Special Note For LoadcomboCards. Load Combo CArd does not depend on LoadCardPair and it's dependended function . 
  
  
  
  
  
    function loadWithHardcodedPaths(cardType) {
  
  
      // Hardcoded fallback
      const cardFiles = {
        "blue_dog": {
          front: "front side service dog blue.jpg",
          back: "back side emotional_dog.jpg",
        },
        "red_dog": {
          front: "front side service dog red.jpg",
          back: "back side of service dog red.jpg",
        },
        "emotional_dog": {
          front: "emotional_dog_front_side.jpg",
          back: "back side emotional_dog.jpg",
        },
        "service_dog_handler": {
          front: "front side service dog handler blue.jpg",
          back: "back side emotional_dog.jpg",
        },
        "blue_cat": {
          front: "front side emotional support cat handler blue.jpg",
          back: "back side emotional_dog.jpg",
        },
        "combo_dog": {
          front: "front side service dog blue.jpg",
          back: "back side emotional_dog.jpg",
        },
        "combo_red_dog": {
          front: "front side service dog red.jpg",
          back: "back side of service dog red.jpg",
        },
        "combo_emotional_dog": {
          front: "emotional_dog_front_side.jpg",
          back: "back side emotional_dog.jpg",
        },
        "combo_emotional_cat": {
          front: "front side emotional support cat blue.jpg",
          back: "back side emotional_dog.jpg",
        },
  
        "child_identification": {
          front: "child_identification_id_card_blue_front_side.jpg",
          back: "child_identification_id_card_blue_back_side.jpg",
        },
        "child_identification_red": {
          front: "child_identification_id_card_red_front_side.jpg",
          back: "child_identification_id_card_red_back_side.jpg",
        },
        "autism_card_infinity": {
          front: "autism_card_front_side.jpg",
          back: "autism_card_back_side_infinity_sign.jpg",
        },
        "autism_card_puzzle": {
          front: "autism_card_front_side.jpg",
          back: "autism_card_back-side_puzzle_sign.jpg",
        },
        "emergency_id_card": {
          front: "emergency id cards red front side.jpg",
          back: "emergency id cards red back side.jpg",
        },
  
      };
  
      const files = cardFiles[cardType];
      
      if (files) {
        loadImage(files.front, "front");
        loadImage(files.back, "back");
        enableControls();
      }
    }
  
    function loadImage(filename, side) {
      console.log(`Loading ${side} image:`, filename);
  
      const img = new Image();
  
      img.onload = function() {
        console.log(`SUCCESS: ${side} image loaded:`, filename);
        console.log(`Image dimensions: ${img.width}x${img.height}`);
        console.log(`Canvas elements - frontCanvas:`, frontCanvas, "backCanvas:", backCanvas);
        console.log(`Canvas contexts - frontCtx:`, frontCtx, "backCtx:", backCtx);
  
        if (side === "front") {
          frontImage = img;
          console.log("Setting frontImage and calling redrawFrontCanvas...");
          redrawFrontCanvas();
          showCanvas("front");
          console.log("Front side displayed successfully");
        } else {
          backImage = img;
          console.log("Setting backImage and calling redrawBackCanvas...");
          redrawBackCanvas();
          showCanvas("back");
          console.log("Back side displayed successfully");
        }
  
        updateStatus();
      };
  
      img.onerror = function() {
        console.error(`ERROR: Failed to load ${side} image:`, filename);
        console.error("Trying direct path...");
  
        // Try without any path modifications
        const directImg = new Image();
        directImg.onload = function() {
          console.log(`SUCCESS with direct path: ${side} image loaded:`, filename);
  
          if (side === "front") {
            frontImage = directImg;
            redrawFrontCanvas();
            showCanvas("front");
          } else {
            backImage = directImg;
            redrawBackCanvas();
            showCanvas("back");
          }
          updateStatus();
        };
  
        directImg.onerror = function() {
          console.error(`Direct path also failed for ${side}:`, filename);
  
          // For back side, try alternatives
          if (side === "back") {
            if (currentCardType === "blue_cat") {
              tryAlternativeFile(["back side emotional_dog.jpg", "emotional_dog_support.jpg"], 0, side);
            } else {
              tryAlternativeFile(["emotional_dog_back_side.jpg", "emotional_dog_support.jpg"], 0, side);
            }
          }
        };
  
        directImg.src = "./" + filename;
      };
  
      img.src = filename;
    }
  
    function tryAlternativeFile(alternatives, index, side) {
      if (index >= alternatives.length) {
        console.error("All alternative files failed for", side);
        return;
      }
  
      const filename = alternatives[index];
      console.log(`Trying alternative ${side} file ${index + 1}:`, filename);
  
      const img = new Image();
      img.crossOrigin = "anonymous";
  
      img.onload = function() {
        console.log(`SUCCESS: Alternative ${side} file loaded:`, filename);
  
        if (side === "back") {
          backImage = img;
          redrawBackCanvas();
          showCanvas("back");
          console.log("Back side displayed with alternative file");
        }
  
        updateStatus();
      };
  
      img.onerror = function() {
        console.log(`Failed alternative file:`, filename);
        tryAlternativeFile(alternatives, index + 1, side);
      };
  
      img.src = filename;
    }
  
    function drawImageOnCanvas(canvas, ctx, image) {
      console.log("drawImageOnCanvas called with:", { canvas, ctx, image });
      if (!canvas || !ctx || !image) {
        console.error("Missing canvas, context, or image");
        return;
      }
  
      // Set canvas size to maintain aspect ratio
      // No developer what ever you have aspect ratio don't change the canvas size please
      const maxWidth = 2373;
      const maxHeight = 1491;
      const ratio = Math.min(maxWidth / image.width, maxHeight / image.height);
  
      console.log(`Setting canvas size: ${image.width * ratio}x${image.height * ratio} (ratio: ${ratio})`);
      canvas.width = image.width * ratio;
      canvas.height = image.height * ratio;
      canvas.style.borderRadius = "0px";
  
      // Draw image
      console.log("Clearing canvas and drawing image...");
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
  
      console.log(`Image drawn on ${canvas.id}: ${canvas.width}x${canvas.height}`);
  
      // No additional drawing here; use centralized redrawFrontCanvas()
    }
  
    function showCanvas(side) {
      const canvas = document.getElementById(side + "Canvas");
      const placeholder = document.getElementById(side + "Placeholder");
  
      if (canvas && placeholder) {
        canvas.style.display = "block";
        placeholder.style.display = "none";
        console.log(`${side} canvas shown, placeholder hidden`);
      } else {
        console.error(`Canvas or placeholder not found for ${side} side`);
      }
    }
  
    function hideCanvas(side) {
      const canvas = document.getElementById(side + "Canvas");
      const placeholder = document.getElementById(side + "Placeholder");
  
      if (canvas && placeholder) {
        canvas.style.display = "none";
        placeholder.style.display = "block";
        console.log(`${side} canvas hidden, placeholder shown`);
      }
    }
  
    // addTextToCanvas function removed - no longer needed
  
    // Toggle combo card editing
    function toggleComboCard(cardKey, buttonElement) {
      // Toggle the state
      comboToggleStates[cardKey] = !comboToggleStates[cardKey];
  
      // Get the card section (parent of the toggle container)
      const cardSection = buttonElement.closest(".combo-card-section");
      const canvasRow = cardSection ? cardSection.querySelector(".combo-canvas-row") : null;
  
      // Update button appearance and card visibility
      if (comboToggleStates[cardKey]) {
        buttonElement.classList.add("active");
        buttonElement.querySelector("i").className = "fas fa-toggle-on";
        buttonElement.querySelector("span").textContent = "Editing ON";
  
        // Show the card canvases
        if (canvasRow) {
          canvasRow.style.display = "flex";
        }
      } else {
        buttonElement.classList.remove("active");
        buttonElement.querySelector("i").className = "fas fa-toggle-off";
        buttonElement.querySelector("span").textContent = "Editing OFF";
  
        // Hide the card canvases
        if (canvasRow) {
          canvasRow.style.display = "none";
        }
      }
  
      console.log(`Toggle ${cardKey}: ${comboToggleStates[cardKey] ? "ON" : "OFF"}`);
    }
  
    // Handle photo upload
    function handlePhotoUpload(event) {
      if (
        currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
        || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
      ) {
        // For combo cards, we need to check if combo canvases are loaded
        if (!window.comboCanvases) {
          alert("Please select a card type first so the combo cards load.");
          event.target.value = "";
          return;
        }
      } else {
        if (!frontImage) {
          alert("Please select a card type first so the front side loads.");
          event.target.value = "";
          return;
        }
      }
  
      const file = event.target.files && event.target.files[0];
      if (!file) return;
  
      const reader = new FileReader();
      reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
          if (isFrontPhotoImage2 && event.target.id === "photoInput2") {
            frontPhotoImage2 = img;
          } else {
            frontPhotoImage = img;
          }
  
          if (
            currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
            || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
          ) {
            redrawComboCanvases();
          } else {
            redrawFrontCanvas();
          }
        };
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  
    // Redraw combo canvases with photo and text
    function redrawComboCanvases() {
      if (!window.comboCanvases) return;
  
      // Redraw front canvases based on toggle states
      if (comboToggleStates.blueDog) {
        redrawComboFrontCanvas("blue");
      }
      if (comboToggleStates.handler) {
        redrawComboFrontCanvas("emotional");
      }
    }
  
    // Redraw individual combo front canvas
    function redrawComboFrontCanvas(cardType) {
      const canvasData = window.comboCanvases[cardType + "Front"];
      if (!canvasData) return;
  
      const { canvas, ctx, img } = canvasData;
  
      // Clear canvas
      ctx.clearRect(0, 0, canvas.width, canvas.height);
  
      // Draw background image
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
  
      // Redraw QR code if it exists
      if (canvasData.qrImg && canvasData.qrSize > 0) {
        addQRToComboCanvases(canvasData.qrImg, canvasData.qrSize);
        // const margin = canvasData.qrMargin || 10;
        // const x = canvas.width - canvasData.qrSize - margin-150;
        // let y = canvas.height - canvasData.qrSize - margin -30; // 5px lower on front side
  
        // ctx.drawImage(canvasData.qrImg, x, y, canvasData.qrSize, canvasData.qrSize);
        // console.log(`QR code redrawn at (${x}, ${y}) on combo ${cardType} front side`);
      }
  
      if (canvasData.idNumber != null) {
        drawIDOnComboCanvases(canvasData.idNumber);
      }
  
      // Place photo if uploaded
      if (frontPhotoImage || frontPhotoImage2) {
        if (isFrontPhotoImage2 && frontPhotoImage2 && cardType == "emotional") {
          // To draw combo second  image check frontPhotoImage2 is true if true and card type emotional means bottom one then draw extra image else do as default
          // If you are developer please carfull because project is not organized
  
          placePhotoOnComboCanvas(canvas, ctx, frontPhotoImage2, cardType);
        } else {
          placePhotoOnComboCanvas(canvas, ctx, frontPhotoImage, cardType);
        }
      }
  
      // Draw text fields
      drawNamesOnComboCanvas(canvas, ctx, cardType);
    }
  
    // Place photo on combo canvas
    function placePhotoOnComboCanvas(canvas, ctx, img, cardType) {
      if (!canvas || !ctx || !img) return;
  
      // Skip photo drawing for autism cards (they don't use photos)
      if (currentCardType === "autism_card_infinity" || currentCardType === "autism_card_puzzle") return;
  
      // Photo target rectangle (left grey panel area)
      const canvasWidth = canvas.width;
      const canvasHeight = canvas.height;
  
      let photoX, photoY, photoW, photoH;
  
      // Use the same positioning as blue_dog for both combo cards, but with increased height
      photoX = (canvasWidth * FRONT_LAYOUT.photo.xPct) + (FRONT_LAYOUT.photo.offsetX || 0) - 5; // Moved 5px to the left
      photoY = (canvasHeight * FRONT_LAYOUT.photo.yPct) + (FRONT_LAYOUT.photo.offsetY || 0) - 7; // Moved 7px higher (2px + 5px more)
      // photoW = (FRONT_LAYOUT.photo.wPx || (canvasWidth * (FRONT_LAYOUT.photo.wPct || 0))) + 12; // Width increased by 12px
      // photoH = (FRONT_LAYOUT.photo.hPx || (canvasHeight * (FRONT_LAYOUT.photo.hPct || 0))) + 27; // Height increased by 27px (2px + 15px + 10px more)
      photoH = 850;
      photoW = 650;
      // Draw the photo with cover fit preserving aspect ratio
      const imgRatio = img.width / img.height;
      const rectRatio = photoW / photoH;
      let sx, sy, sWidth, sHeight;
      if (imgRatio > rectRatio) {
        // image is wider – crop sides
        sHeight = img.height;
        sWidth = sHeight * rectRatio;
        sx = (img.width - sWidth) / 2;
        sy = 0;
      } else {
        // image is taller – crop top/bottom
        sWidth = img.width;
        sHeight = sWidth / rectRatio;
        sx = 0;
        sy = (img.height - sHeight) / 2;
      }
  
      // No rounded rectangle mask for combo cards (same as blue_dog)
      ctx.drawImage(img, sx, sy, sWidth, sHeight, photoX, photoY, photoW, photoH);
    }
  
    // Draw names on combo canvas
    async function drawNamesOnComboCanvas(canvas, ctx, cardType) {
      const whichCombo = document.getElementById("cardTypeSelect").value.trim();
  
      if (!whichCombo) {
        alert("Please Select A Card Type");
        return;
      }
  
      if (!canvas || !ctx) return;
  
      const canvasWidth = canvas.width;
      const canvasHeight = canvas.height;
  
      // Get input values
      const animalNameInput = document.getElementById("animalNameInput");
      const handlerNameInput = document.getElementById("handlerNameInput");
      const addressInput = document.getElementById("addressInput");
      const telephoneInput = document.getElementById("telephoneInput");
  
      const animalName = animalNameInput ? animalNameInput.value.trim() : "";
      const handlerName = handlerNameInput ? handlerNameInput.value.trim() : "";
      const address = addressInput ? addressInput.value.trim() : "";
      const telephone = telephoneInput ? telephoneInput.value.trim() : "";
  
      const fillColor =
        whichCombo == "combo_dog" || whichCombo == "combo_emotional_dog" || whichCombo == "combo_emotional_cat"
          ? "#1c1b89"
          : "#000";
  
      // Here is the problem with Orginal Image that's why i have added little margin
      // Card type nothing special blue means top and Emotional means down one
  
      const adjustAnimalAddressPostion_ = whichCombo == "combo_emotional_dog" && cardType == "blue" ? 30 : 0;
      const adjustTelephonePostion = whichCombo == "combo_emotional_dog" && cardType == "blue" ? 30 : 0;
      if (animalName) {
        // Animal's Name - use same positioning as blue_dog
        const animalX = (canvasWidth * FRONT_LAYOUT.animal.xPct) + (FRONT_LAYOUT.animal.offsetX || 0) - 100
          - adjustAnimalAddressPostion_; // Moved 95px to the left
        const animalY = (canvasHeight * FRONT_LAYOUT.animal.yPct) + (FRONT_LAYOUT.animal.offsetY || 0) + 15; // Moved 15px lower
        await document.fonts.load("normal 90.9px GilmerMedium");
        // console.log(document.fonts.check('bold 85.68px Gilmer'));
        ctx.font = "normal 90.9px GilmerMedium";
        ctx.fillStyle = fillColor;
        ctx.textAlign = "left";
        ctx.textBaseline = "middle";
  
        // // Auto-fit text to available width
        // const maxWidth = canvasWidth * FRONT_LAYOUT.animal.maxWidthPct;
        // const fontSize = Math.min(FRONT_LAYOUT.animal.baseFontPx, maxWidth / (ctx.measureText(animalName).width / FRONT_LAYOUT.animal.baseFontPx));
        // ctx.font = `bold ${Math.max(fontSize, FRONT_LAYOUT.animal.minFontPx)}px Gilmer`;
  
        ctx.fillText(animalName, animalX, animalY);
      }
  
      if (handlerName) {
        // Handler's Name - use same positioning as blue_dog
        const handlerX = (canvasWidth * FRONT_LAYOUT.handler.xPct) + (FRONT_LAYOUT.handler.offsetX || 0) - 100
          - adjustAnimalAddressPostion_; // Moved 93px to the left
        const handlerY = (canvasHeight * FRONT_LAYOUT.handler.yPct) + (FRONT_LAYOUT.handler.offsetY || 0) + 14; // Moved 15px lower
  
        await document.fonts.load("normal 90.9px GilmerMedium");
        // console.log(document.fonts.check('bold 85.68px Gilmer'));
        ctx.font = "normal 90.9px GilmerMedium";
        ctx.fillStyle = fillColor;
        ctx.textAlign = "left";
        ctx.textBaseline = "middle";
        // Auto-fit text to available width
        // const maxWidth = canvasWidth * FRONT_LAYOUT.handler.maxWidthPct;
        // const fontSize = Math.min(FRONT_LAYOUT.handler.baseFontPx, maxWidth / (ctx.measureText(handlerName).width / FRONT_LAYOUT.handler.baseFontPx));
        // ctx.font = `bold ${Math.max(fontSize, FRONT_LAYOUT.handler.minFontPx)}px Gilmer`;
  
        ctx.fillText(handlerName, handlerX, handlerY);
      }
  
      // Address and Telephone fields
      if (address) {
        // Canvas width already dynamic
        const boxStartX = (canvasWidth * 0.369) - adjustAnimalAddressPostion_; // starting point of the box
        const boxWidth = 1275; // your box width
        const centerX = boxStartX + (boxWidth / 2);
  
        const addressY = (canvasHeight * 0.35) + 5 - 35;
  
        await document.fonts.load("50.2px GilmerMedium");
        ctx.font = "normal 50.2px GilmerMedium";
        ctx.fillStyle = "#000";
  
        ctx.textAlign = "center"; // center horizontally
        ctx.textBaseline = "middle"; // center vertically
  
        ctx.fillText(address, centerX, addressY);
      }
  
      if (telephone) {
        const telephoneX = (canvasWidth * 0.62) - adjustTelephonePostion + (currentCardType =='combo_emotional_cat' && cardType == "emotional" ? 50 : 5); // Right side, moved 60px to the left (70px - 10px = 10px more to the right)
        const telephoneY = (canvasHeight * 0.35)
          + (currentCardType == "combo_emotional_cat" || currentCardType == "combo_emotional_dog" ? 30 : 34); // Below address field
        await document.fonts.load("50.2px GilmerMedium");
        // console.log(document.fonts.check('bold 85.68px Gilmer'));
        ctx.font = "50.2px GilmerMedium";
        ctx.fillStyle = "#000";
        ctx.textAlign = "left";
        ctx.textBaseline = "middle";
        ctx.fillText(telephone, telephoneX, telephoneY);
      }
    }
  
    // Draw ID number on combo canvases
    function drawIDOnComboCanvases(randomID) {
      if (!window.comboCanvases) return;
  
      // Draw ID on front canvases based on toggle states
      if (comboToggleStates.blueDog) {
        window.comboCanvases.blueFront.idNumber = randomID;
        drawIDOnComboCanvas("blue", randomID);
      }
      if (comboToggleStates.handler) {
        window.comboCanvases.emotionalFront.idNumber = randomID;
        const isComboEmotionalCat = document.getElementById("cardTypeSelect").value.trim() == "combo_emotional_cat";
        drawIDOnComboCanvas("emotional", randomID, isComboEmotionalCat);
      }
    }
  
    // Draw ID number on individual combo canvas
    async function drawIDOnComboCanvas(cardType, randomID, isTrue = false) {
      const canvasData = window.comboCanvases[cardType + "Front"];
  
      const whichCombo = document.getElementById("cardTypeSelect").value.trim();
  
      const fontSize = whichCombo == "combo_emotional_dog" ? 70 : 70;
      const positionY = whichCombo == "combo_emotional_dog" && cardType == "blue" ? 10 : -5; // Due to Emotional Pack Image layout not wokay i have write this code adjust positoning
      if (!canvasData) return;
  
      const { canvas, ctx } = canvasData;
  
      const canvasWidth = canvas.width;
      const canvasHeight = canvas.height;
  
      // Use same positioning as blue_dog
      const idBoxX = canvasWidth * 0.185; // 18.5% from left edge (shifted left)
      const idBoxY = canvasHeight * 0.91; // 91% from top (slightly lower)
      const boxWidth = canvasWidth * 0.28; // 28% of canvas width
  
      // Forcefully wait to load font
      await document.fonts.load(`${fontSize}px ArialMTBold`);
  
      // Set text style for ID number
      ctx.font = `bold ${fontSize}px ArialMTBold`;
      ctx.fillStyle = "#FF0000"; // Red color
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";
  
      // Draw the random ID in the registry box (moved 8px to the left total, 3px higher)
      const textX = idBoxX + (boxWidth / 2) - 8; // Center of the box, moved 8px left
      let textY = idBoxY + 2 + positionY; // Moved 2px lower to fit better in box
  
      // Move ID number 2px lower for handler card
      if (cardType === "handler") {
        textY += 2; // Additional 2px lower for handler card
      }
  
      if (cardType == "emotional" && isTrue) {
        // alert(cardType, currentCardType);
        textY += 20;
      }
      ctx.fillText(randomID, textX, textY);
  
      console.log(`ID number "${randomID}" placed at (${textX}, ${textY}) for combo ${cardType} card`);
    }
  
    function placePhotoOnFront(img) {
      if (!frontCanvas || !frontCtx || !frontImage || !img) return;
  
      // Skip photo drawing for autism cards (they don't use photos)
      if (currentCardType === "autism_card_infinity" || currentCardType === "autism_card_puzzle") return;
  
      // Photo target rectangle (left grey panel area)
      const canvasWidth = frontCanvas.width;
      const canvasHeight = frontCanvas.height;
  
      let photoX, photoY, photoW, photoH;
  
      if (currentCardType === "child_identification") {
        // Blue child identification card photo positioning - moved 50px lower, width increased by 10px, height increased by 10px
  
        photoX = (canvasWidth * FRONT_LAYOUT.photo.xPct) + (FRONT_LAYOUT.photo.offsetX || 0) + 30;
        photoY = (canvasHeight * FRONT_LAYOUT.photo.yPct) + (FRONT_LAYOUT.photo.offsetY || 0) + 311; // 50px lower (40px + 10px)
        // photoW = (FRONT_LAYOUT.photo.wPx || (canvasWidth * (FRONT_LAYOUT.photo.wPct || 0))) + 10; // Width increased by 10px
        // photoH = (FRONT_LAYOUT.photo.hPx || (canvasHeight * (FRONT_LAYOUT.photo.hPct || 0))) + 10; // Height increased by 10px (was -5px, now +10px = +15px total increase)
  
        photoH = 760;
        photoW = 640;
      } else if (currentCardType === "child_identification_red") {
        // Red child identification card photo positioning - moved 31px lower (50px - 15px - 4px), width decreased by 5px, height increased by 25%
        const basePhotoW = FRONT_LAYOUT.photo.wPx || (canvasWidth * (FRONT_LAYOUT.photo.wPct || 0));
        const basePhotoH = FRONT_LAYOUT.photo.hPx || (canvasHeight * (FRONT_LAYOUT.photo.hPct || 0));
  
        photoX = (canvasWidth * FRONT_LAYOUT.photo.xPct) + (FRONT_LAYOUT.photo.offsetX || 0) + 3.9;
        photoY = (canvasHeight * FRONT_LAYOUT.photo.yPct) + (FRONT_LAYOUT.photo.offsetY || 0) + 160; // 31px lower (50px - 15px higher - 4px higher = 31px)
        // photoW = (basePhotoW * 1.25) - 5; // 25% larger width minus 5px
        // photoH = basePhotoH * 1.25; // 25% larger height
  
        photoH = 1024.5;
        photoW = 684.5;
      } else if (currentCardType === "autism_card_infinity" || currentCardType === "autism_card_puzzle") {
        // Autism card doesn't need photo positioning - set to 0
        photoX = 0;
        photoY = 0;
        photoW = 0;
        photoH = 0;
      } else if (currentCardType === "blue_dog" || currentCardType === "red_dog" || currentCardType === "combo_dog") {
        // Blue and Red dog cards photo positioning - moved 2px higher (5px - 3px lower), 5px left, width reduced by 18px, height reduced by 3px from top
        photoX = (canvasWidth * FRONT_LAYOUT.photo.xPct) + (FRONT_LAYOUT.photo.offsetX || 0) - 5; // Moved 5px to the left
        photoY = (canvasHeight * FRONT_LAYOUT.photo.yPct) + (FRONT_LAYOUT.photo.offsetY || 0) - 2; // Moved 2px higher (5px - 3px lower = 2px higher)
        photoW = (FRONT_LAYOUT.photo.wPx || (canvasWidth * (FRONT_LAYOUT.photo.wPct || 0))) + 12; // Width reduced by 18px (15px + 3px = 12px total increase)
        photoH = (FRONT_LAYOUT.photo.hPx || (canvasHeight * (FRONT_LAYOUT.photo.hPct || 0))) + 2; // Height reduced by 3px from top (5px - 3px = 2px total increase)
      } else if (currentCardType === "emotional_dog") {
        // Emotional dog card photo positioning - image 5px wider, 5px taller, and 3px higher
        photoX = (canvasWidth * FRONT_LAYOUT.photo.xPct) + (FRONT_LAYOUT.photo.offsetX || 0);
        photoY = (canvasHeight * FRONT_LAYOUT.photo.yPct) + (FRONT_LAYOUT.photo.offsetY || 0) - 3; // Moved 3px higher
        photoW = (FRONT_LAYOUT.photo.wPx || (canvasWidth * (FRONT_LAYOUT.photo.wPct || 0))) + 5; // Width increased by 5px
        photoH = (FRONT_LAYOUT.photo.hPx || (canvasHeight * (FRONT_LAYOUT.photo.hPct || 0))) + 5; // Height increased by 5px
      } else {
        // Regular animal card photo positioning
        photoX = (canvasWidth * FRONT_LAYOUT.photo.xPct) + (FRONT_LAYOUT.photo.offsetX || 0);
        photoY = (canvasHeight * FRONT_LAYOUT.photo.yPct) + (FRONT_LAYOUT.photo.offsetY || 0);
        photoW = FRONT_LAYOUT.photo.wPx || (canvasWidth * (FRONT_LAYOUT.photo.wPct || 0));
        photoH = FRONT_LAYOUT.photo.hPx || (canvasHeight * (FRONT_LAYOUT.photo.hPct || 0));
      }
  
      // Draw the photo with cover fit preserving aspect ratio
      const imgRatio = img.width / img.height;
      const rectRatio = photoW / photoH;
      let sx, sy, sWidth, sHeight;
      if (imgRatio > rectRatio) {
        // image is wider – crop sides
        sHeight = img.height;
        sWidth = sHeight * rectRatio;
        sx = (img.width - sWidth) / 2;
        sy = 0;
      } else {
        // image is taller – crop top/bottom
        sWidth = img.width;
        sHeight = sWidth / rectRatio;
        sx = 0;
        sy = (img.height - sHeight) / 2;
      }
  
      // Optional rounded rectangle mask (disabled for blue, red, and emotional dog cards)
      const r = (currentCardType === "blue_dog" || currentCardType === "red_dog" || currentCardType === "combo_dog"
          || currentCardType === "emotional_dog" || currentCardType === "child_identification_red")
        ? 0
        : FRONT_LAYOUT.photo.radius;
      if (r > 0) {
        frontCtx.save();
        frontCtx.beginPath();
        frontCtx.moveTo(photoX + r, photoY);
        frontCtx.lineTo(photoX + photoW - r, photoY);
        frontCtx.quadraticCurveTo(photoX + photoW, photoY, photoX + photoW, photoY + r);
        frontCtx.lineTo(photoX + photoW, photoY + photoH - r);
        frontCtx.quadraticCurveTo(photoX + photoW, photoY + photoH, photoX + photoW - r, photoY + photoH);
        frontCtx.lineTo(photoX + r, photoY + photoH);
        frontCtx.quadraticCurveTo(photoX, photoY + photoH, photoX, photoY + photoH - r);
        frontCtx.lineTo(photoX, photoY + r);
        frontCtx.quadraticCurveTo(photoX, photoY, photoX + r, photoY);
        frontCtx.closePath();
        frontCtx.clip();
      }
  
      frontCtx.drawImage(img, sx, sy, sWidth, sHeight, photoX, photoY, photoW, photoH);
  
      if (r > 0) {
        frontCtx.restore();
      }
  
      // names drawn by redrawFrontCanvas()
    }
  
    // Draw names based on card type (Animal or Child)
    async function drawNamesOnFront() {
      if (!frontCanvas || !frontCtx || !frontImage) return;
  
      const canvasWidth = frontCanvas.width;
      const canvasHeight = frontCanvas.height;
  
      // Shared styling
      frontCtx.fillStyle = "#000000";
      frontCtx.textAlign = "left";
      frontCtx.textBaseline = "middle";
  
      // Helper: fit text into max width by reducing font size down to min
      function drawFittedText(text, x, y, maxWidthPct, basePx, minPx) {
        const maxWidth = canvasWidth * maxWidthPct;
        let fontSize = basePx;
        frontCtx.font = `bold ${fontSize}px GilmerMedium`;
        frontCtx.fillStyle = "#0000FF"; // Changed to blue color
        while (frontCtx.measureText(text).width > maxWidth && fontSize > minPx) {
          fontSize -= 1;
          frontCtx.font = `bold ${fontSize}px Gilmer`;
        }
        frontCtx.fillText(text, x, y);
      }
  
      if (currentCardType === "child_identification" || currentCardType === "child_identification_red") {
        // Child Identification Card fields - positioned exactly as shown on template
        const childNameInput = document.getElementById("childNameInput");
        const childDOBInput = document.getElementById("childDOBInput");
        const childSexInput = document.getElementById("childSexInput");
        const childHairInput = document.getElementById("childHairInput");
        const childEyesInput = document.getElementById("childEyesInput");
        const childHeightInput = document.getElementById("childHeightInput");
        const childWeightInput = document.getElementById("childWeightInput");
        const childMomInput = document.getElementById("childMomInput");
        const childDadInput = document.getElementById("childDadInput");
        const childParentsInput = document.getElementById("childParentsInput");
        const childAdditionalInput = document.getElementById("childAdditionalInput");
        const childAddressInput2 = document.getElementById("childAddressInput2");
  
        const childName = childNameInput ? childNameInput.value.trim() : "";
        const dob = childDOBInput ? childDOBInput.value.trim() : "";
        const sex = childSexInput ? childSexInput.value.trim() : "";
        const hair = childHairInput ? childHairInput.value.trim() : "";
        const eyes = childEyesInput ? childEyesInput.value.trim() : "";
        const height = childHeightInput ? childHeightInput.value.trim() : "";
        const weight = childWeightInput ? childWeightInput.value.trim() : "";
        const mom = childMomInput ? childMomInput.value.trim() : "";
        const dad = childDadInput ? childDadInput.value.trim() : "";
        const parents = childParentsInput ? childParentsInput.value.trim() : "";
        const additionalInfo = childAdditionalInput ? childAdditionalInput.value.trim() : "";
        const childAddressRed = childAddressInput2 ? childAddressInput2.value.trim() : "";
        // Set smaller font for child ID card fields
  
  
        await document.fonts.load("78px GilmerRegular");
        frontCtx.font = "normal 78px GilmerRegular"; // Bigger font for name
        frontCtx.fillStyle = "#000000";
        const fillColor = currentCardType === "child_identification" ? "#1c1b89" : "#000";
        const ChildNameAdjsutPostionX = currentCardType === "child_identification_red" ? 45 : 0;
        const ChildNameAdjsutPostionY = currentCardType === "child_identification_red" ? 20 : 0;
        // Child Name - positioned at the top of the card (bigger, red, 10px lower, 30px left)
        if (childName && currentCardType === "child_identification") {
          await document.fonts.load("bold 127px GilmerBold");
          frontCtx.font = "bold 127px GilmerBold"; // Bigger font for name
          frontCtx.fillStyle = fillColor; // Red color
  
          const nameX = (canvasWidth * 0.35) - 30 + ChildNameAdjsutPostionX; // 30px to the left (20px + 10px)
          const nameY = (canvasHeight * 0.25) + 155 + ChildNameAdjsutPostionY; // Top area + 10px lower
  
          frontCtx.fillText(childName, nameX, nameY);
          frontCtx.font = "normal 75.83px GilmerRegular"; // Reset to smaller font
          frontCtx.fillStyle = "#000000"; // Reset to black color
        }
  
        if (idNumber && currentCardType === "child_identification") {
          isIdGenerated = true;
  
          await document.fonts.load("bold 83.86px GilmerMedium");
          frontCtx.font = "bold 83.86px GilmerMedium";
          frontCtx.fillStyle = "#fff";
          frontCtx.textAlign = "right";
          frontCtx.textBaseline = "bottom";
  
          const x = canvasWidth * 0.94;
          const y = canvasHeight * 0.975;
  
          frontCtx.fillText(`Card No: ${idNumber}`, x, y);
  
          frontCtx.fillStyle = "#000000";
          frontCtx.textAlign = "left";
          frontCtx.textBaseline = "middle";
          frontCtx.font = "normal 75.83px GilmerRegular"; // Reset to smaller font
          frontCtx.fillStyle = "#000000"; // Reset to black color
  
          console.log(`ID number placed at (${x}, ${y}) for child_identification`);
        }
  
        if (childName && currentCardType === "child_identification_red") {
          await document.fonts.load("bold 136px GilmerBold");
          frontCtx.font = "bold 127px GilmerBold"; // Bigger font for name
          frontCtx.fillStyle = fillColor; // Red color
          const nameX = (canvasWidth * 0.35) - 35 + ChildNameAdjsutPostionX; // 30px to the left (20px + 10px)
          const nameY = (canvasHeight * 0.25) - 5 + ChildNameAdjsutPostionY; // Top area + 10px lower
  
          frontCtx.fillText(childName, nameX, nameY);
          frontCtx.font = "normal 75.83px GilmerRegular"; // Reset to smaller font
          frontCtx.fillStyle = "#000000"; // Reset to black color
        }
        if (idNumber && currentCardType === "child_identification_red") {
          isIdGenerated = true;
  
          await document.fonts.load("bold 83.86px GilmerMedium");
          frontCtx.font = "bold 83.83px GilmerMedium";
          frontCtx.fillStyle = "#000";
          frontCtx.textAlign = "right";
          frontCtx.textBaseline = "bottom";
  
          const x = canvasWidth * 0.929;
          const y = canvasHeight * 0.212 - 10;
  
          frontCtx.fillText(idNumber, x, y);
  
          frontCtx.fillStyle = "#000000";
          frontCtx.textAlign = "left";
          frontCtx.textBaseline = "middle";
          frontCtx.font = "normal 75.83px GilmerRegular"; // Reset to smaller font
          frontCtx.fillStyle = "#000000"; // Reset to black color
  
          console.log(`ID number placed at (${x}, ${y}) for child_identification`);
        }
  
        if (childAddressRed && currentCardType === "child_identification_red") {
          frontCtx.font = "normal 78px GilmerRegular";
          frontCtx.fillStyle = "#000000";
  
          const nameX = (canvasWidth * 0.35) - 30 + ChildNameAdjsutPostionX;
          const nameY = (canvasHeight * 0.313) + 10 + ChildNameAdjsutPostionY;
  
          const maxWidth = 850;
          const lineHeight = 95;
  
          let lines = [];
          let current = "";
  
          for (let i = 0; i < childAddressRed.length; i++) {
            const testLine = current + childAddressRed[i];
            const testWidth = frontCtx.measureText(testLine).width;
  
            if (testWidth > maxWidth) {
              lines.push(current); // push finished part
              current = childAddressRed[i]; // start new line with current character
            } else {
              current = testLine;
            }
          }
  
          // push last part
          if (current.length > 0) {
            lines.push(current);
          }
  
          // draw lines
          lines.forEach((line, index) => {
            frontCtx.fillText(line, nameX, nameY + (index * lineHeight));
          });
        }
  
        // Left column fields - moved 30px to the left (20px + 10px)
        // DOB - positioned to match template
        if (dob && currentCardType === "child_identification") {
          const dobX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
  
          const dobY = (canvasHeight * 0.38) + (currentCardType === "child_identification" ? 100 : 125); // Adjusted vertical position
          frontCtx.fillText(dob, dobX, dobY);
        }else{
          const dobX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
  
          const dobY = (canvasHeight * 0.415) + (currentCardType === "child_identification" ? 100 : 125); // Adjusted vertical position
          frontCtx.fillText(dob, dobX, dobY);
        }
  
        // Hair - below DOB
        if (hair && currentCardType === "child_identification") {
          const hairX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
          const hairY = (canvasHeight * 0.44) + (currentCardType === "child_identification" ? 100 : 125); // Adjusted vertical position
          frontCtx.fillText(hair, hairX, hairY);
        }else{
          const hairX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
          const hairY = (canvasHeight * 0.475) + (currentCardType === "child_identification" ? 100 : 125); // Adjusted vertical position
          frontCtx.fillText(hair, hairX, hairY);
        }
  
        // Height - below hair
        if (height && currentCardType === "child_identification") {
          const heightX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
          const heightY = (canvasHeight * 0.5) + (currentCardType === "child_identification" ? 100 : 128);
          frontCtx.fillText(height, heightX, heightY);
        }else{
          const heightX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
          const heightY = (canvasHeight * 0.535) + (currentCardType === "child_identification" ? 100 : 128);
          frontCtx.fillText(height, heightX, heightY);
        }
  
        // Weight - below height
        if (weight && currentCardType === "child_identification") {
          const weightX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
          const weightY = (canvasHeight * 0.56) + (currentCardType === "child_identification" ? 100 : 128);
          frontCtx.fillText(weight, weightX, weightY);
        }else{
           const weightX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
          const weightY = (canvasHeight * 0.595) + (currentCardType === "child_identification" ? 100 : 128);
          frontCtx.fillText(weight, weightX, weightY);
        }
  
        // Mom - below weight (moved 7px lower, was 10px lower, now 3px higher)
        if (mom) {
          const momX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
          const momY = (currentCardType === "child_identification_red" ? (canvasHeight * 0.733) : (canvasHeight * 0.64))
          + (currentCardType === "child_identification" ? 103 : 145);
          // const momY = (canvasHeight * 0.64) + (currentCardType === "child_identification" ? 100 : 145);
          frontCtx.fillText(mom, momX, momY);
        }
  
        // Dad - below mom (moved 7px lower, was 10px lower, now 3px higher)
        if (dad) {
          const dadX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
          const dadY = (currentCardType === "child_identification_red" ? (canvasHeight * 0.79) : (canvasHeight * 0.70))
            + (currentCardType === "child_identification" ? 103 : 145);
          // const dadY = (canvasHeight * 0.70) + (currentCardType === "child_identification" ? 100 : 145);
          frontCtx.fillText(dad, dadX, dadY);
        }
  
        // Parents - below dad (moved 7px lower, was 10px lower, now 3px higher)
        if (parents) {
          const parentsX = (canvasWidth * 0.48) + (currentCardType === "child_identification" ? 0 : 20); // 30px to the left
          const parentsY =
            (currentCardType === "child_identification_red" ? (canvasHeight * 0.672) : (canvasHeight * 0.76))
            + (currentCardType === "child_identification" ? 103 : 145);
          //  const parentsY = (canvasHeight * 0.76) + (currentCardType === "child_identification" ? 100 : 145);
          frontCtx.fillText(parents, parentsX, parentsY);
        }
  
        // Right column fields - Sex and Eyes moved 10px to the left (was +20, now +10)
        // Sex - top right, same level as DOB
        if (sex && currentCardType === "child_identification") {
          const sexX = (canvasWidth * 0.78) + (currentCardType === "child_identification" ? 30 : 47); // 10px to the right (was 20px)
          const sexY = (canvasHeight * 0.38) + (currentCardType === "child_identification" ? 100 : 125);
          frontCtx.fillText(sex, sexX, sexY);
        }else{
         const sexX = (canvasWidth * 0.78) + (currentCardType === "child_identification" ? 30 : 47); // 10px to the right (was 20px)
          const sexY = (canvasHeight * 0.415) + (currentCardType === "child_identification" ? 100 : 125);
          frontCtx.fillText(sex, sexX, sexY);
        }
  
        // Eyes - below sex, same level as hair
        if (eyes && currentCardType === "child_identification") {
          const eyesX = (canvasWidth * 0.78) + (currentCardType === "child_identification" ? 30 : 47); // 10px to the right (was 20px)
          const eyesY = (canvasHeight * 0.44) + (currentCardType === "child_identification" ? 100 : 126);
          frontCtx.fillText(eyes, eyesX, eyesY);
        }else{
          const eyesX = (canvasWidth * 0.78) + (currentCardType === "child_identification" ? 30 : 47); // 10px to the right (was 20px)
          const eyesY = (canvasHeight * 0.475) + (currentCardType === "child_identification" ? 100 : 126);
          frontCtx.fillText(eyes, eyesX, eyesY);
        }
  
        if (additionalInfo && currentCardType == "child_identification_red") {
        //  await document.fonts.load("bold 100px GilmerMedium");
       //   frontCtx.font = "bold 100px GilmerMedium";
        //  frontCtx.fillStyle = "#ff0000";
      //    frontCtx.textAlign = "left";
       //   frontCtx.textBaseline = "bottom";
  
        //  const additionalX = canvasWidth * 0.0378; // Center horizontally
       //   const additionalY = canvasHeight * 0.98; // Bottom of card
  
       //   frontCtx.fillText(additionalInfo, additionalX, additionalY);
        }
      } else if (currentCardType === "autism_card_infinity" || currentCardType === "autism_card_puzzle") {
        // Autism Card fields - positioned according to template layout
        const autismNameInput = document.getElementById("autismNameInput");
        const autismContactInput = document.getElementById("autismContactInput");
        const autismNotesInput = document.getElementById("autismNotesInput");
  
        const autismName = autismNameInput ? autismNameInput.value.trim() : "";
        const autismContact = autismContactInput ? autismContactInput.value.trim() : "";
        const autismNotes = autismNotesInput ? autismNotesInput.value.trim() : "";
  
        // Set text styling for autism card (no bold)
        frontCtx.font = "80px GilmerRegular";
        frontCtx.fillStyle = "#000000";
        frontCtx.textAlign = "left";
        frontCtx.textBaseline = "top";
  
        // My Name - positioned after "My Name:" label (moved 10px right and 12px higher total)
        // if (autismName) {
        //   // Shift 2px to the left for better alignment with the template labels
        //   // const nameX = (canvasWidth * 0.1) + 8;
        //   // const nameY = (canvasHeight * 0.269) - 5;
  
        //    let nameX = (canvasWidth * 0.25) + 8;
        //    const nameY = (canvasHeight * 0.21) - 2;
  
        //   frontCtx.font = "normal 78px GilmerRegular";
        //   frontCtx.fillStyle = "#000000";
  
        //   let firstLine = "";
        //   let secondLine = "";
        //   let current = "";
        //   const max_width = 1900;
  
        //   // Build first line until width exceeds 200px
        //   for (let i = 0; i < autismName.length; i++) {
        //     const test = current + autismName[i];
        //     if (frontCtx.measureText(test).width > max_width) {
        //       firstLine = current; // finalize first line
        //       secondLine = autismName.slice(i); // rest goes to second line
        //       break;
        //     }
        //     current = test;
        //   }
  
        //   // If never exceeded 200px → everything stays on first line
        //   if (!firstLine) {
        //     firstLine = current;
        //   }{
        //      nameX = (canvasWidth * 0.1) + 8;
        //   }
  
        //   // Draw first line
        //   frontCtx.fillText(firstLine, nameX, nameY);
  
        //   // Draw second line (only if it exists)
        //   if (secondLine) {
        //     frontCtx.fillText(secondLine, nameX, nameY + 80);
        //   }
  
        //   console.log(`Autism name "${autismName}" placed at (${nameX}, ${nameY})`);
        // }
  
            if (autismName) {
        const firstX = (canvasWidth * 0.25) + 8; 
        const secondX = (canvasWidth * 0.1) + 8; 
        const nameY = (canvasHeight * 0.21) - 2;
  
        frontCtx.font = "normal 78px GilmerRegular";
        frontCtx.fillStyle = "#000000";
  
        const firstLineMaxWidth = 1560;   // 👈 your special first-line width
        const otherLineMaxWidth = 1900;   // 👈 existing width for remaining lines
        const lineHeight = 80;
  
        let lines = [];
        let current = "";
        let limit = firstLineMaxWidth; // first line limit first
  
        for (let i = 0; i < autismName.length; i++) {
          const test = current + autismName[i];
  
          if (frontCtx.measureText(test).width > limit) {
            lines.push(current);
            current = autismName[i];
  
            // After first line, switch to normal width
            limit = otherLineMaxWidth;
          } else {
            current = test;
          }
        }
  
        if (current) lines.push(current);
  
        // Draw lines
        for (let i = 0; i < lines.length; i++) {
          const x = (i === 0) ? firstX : secondX;  // first line uses firstX
          const y = nameY + (i * lineHeight);
          frontCtx.fillText(lines[i], x, y);
        }
  }
  
  
        // My Emergency Contact(s) - positioned after "My Emergency Contact(s):" label (moved 115px right total)
  
            if (autismContact) {
  
        // You can adjust these two X positions yourself ❤️
        const firstX = (canvasWidth * 0.50) -10 ;    // first line X
        const secondX = (canvasWidth * 0.1) + 0;   // other lines X (change if needed)
        const contactY = (canvasHeight * 0.50) -80;
        frontCtx.font = "normal 78px GilmerRegular";
        frontCtx.fillStyle = "#000000";
  
        const firstLineMaxWidth = 980;   // 👈 special max width for line 1 (adjust this)
        const otherLineMaxWidth = 1900;   // 👈 max width for lines 2,3,4...
  
        const lineHeight = 80;
  
        let lines = [];
        let current = "";
        let limit = firstLineMaxWidth; // first line uses special width
  
        // Build lines
        for (let i = 0; i < autismContact.length; i++) {
          const test = current + autismContact[i];
  
          if (frontCtx.measureText(test).width > limit) {
            lines.push(current);
            current = autismContact[i];
  
            // After first line → switch to the normal width
            limit = otherLineMaxWidth;
          } else {
            current = test;
          }
        }
  
        if (current) lines.push(current); // add last line
  
        // Draw the lines
        for (let i = 0; i < lines.length; i++) {
          const x = (i === 0) ? firstX : secondX;
          const y = contactY + (i * lineHeight);
          frontCtx.fillText(lines[i], x, y);
        }
  
        console.log(`Autism contact placed with ${lines.length} lines`);
      }
  
  
        if (autismNotes) {
  
    // You can adjust these yourself my love ❤️
    const firstX = (canvasWidth * 0.20);     // X for first line
    const secondX = (canvasWidth * 0.1) + 3;    // X for other lines (change if needed)
    const notesY = (canvasHeight * 0.73) - 60;
  
    frontCtx.font = "normal 78px GilmerRegular";
    frontCtx.fillStyle = "#000000";
  
    const firstLineWidth = 1630;       // 👈 special width for the FIRST line
    const otherLineWidth = 1900;       // 👈 width for lines AFTER first
    const lineHeight = 80;
  
    let lines = [];
    let current = "";
    let limit = firstLineWidth;        // start with first-line width
  
    // Build lines
    for (let i = 0; i < autismNotes.length; i++) {
      const test = current + autismNotes[i];
      if (frontCtx.measureText(test).width > limit) {
        lines.push(current);
        current = autismNotes[i];
  
        // After first line → switch to normal width
        limit = otherLineWidth;
      } else {
        current = test;
      }
    }
  
    if (current) lines.push(current);
  
    // Draw the lines
    for (let i = 0; i < lines.length; i++) {
      const x = (i === 0) ? firstX : secondX; // different X for line 1
      const y = notesY + (i * lineHeight);
  
      frontCtx.fillText(lines[i], x, y);
    }
  
    console.log(`Autism notes drawn in ${lines.length} lines.`);
  }
  
      } else if (currentCardType === "emergency_id_card") {
        // Emergency ID Card fields - positioned according to template layout
        const emergencyDOBInput = document.getElementById("emergencyDOBInput");
        const emergencyHeightInput = document.getElementById("emergencyHeightInput");
        const emergencyBloodTypeInput = document.getElementById("emergencyBloodTypeInput");
        const emergencyWeightInput = document.getElementById("emergencyWeightInput");
        const emergencyContactsInput = document.getElementById("emergencyContactsInput");
        const emergencyContactsInput2 = document.getElementById("emergencyContactsInput2");
        const emergencyAddressInput = document.getElementById("emergencyAddressInput");
        const emergencyNameInput = document.getElementById("emergencyNameInput");
        const emergencyNumberInput = document.getElementById("emergencyContactsInputNumber");
        const emergencyNumberInput2 = document.getElementById("emergencyContactsInputNumber2");
  
  
        const emergencyDOB = emergencyDOBInput ? emergencyDOBInput.value.trim() : "";
        const emergencyHeight = emergencyHeightInput ? emergencyHeightInput.value.trim() : "";
        const emergencyBloodType = emergencyBloodTypeInput ? emergencyBloodTypeInput.value.trim() : "";
        const emergencyWeight = emergencyWeightInput ? emergencyWeightInput.value.trim() : "";
        const emergencyContacts = emergencyContactsInput ? emergencyContactsInput.value.trim() : "";
        const emergencyContacts2 = emergencyContactsInput2 ? emergencyContactsInput2.value.trim() : "";
        const emergencyAddress = emergencyAddressInput ? emergencyAddressInput.value.trim() : "";
        const emmergencyName = emergencyNameInput ? emergencyNameInput.value.trim() : "";
        const emergencyNumber = emergencyNumberInput ? emergencyNumberInput.value.trim() : "";
        const emergencyNumber2 = emergencyNumberInput2 ? emergencyNumberInput2.value.trim() : "";
  
        // Name
        if (emmergencyName) {
          frontCtx.font = "bold 140px GilmerMedium";
          frontCtx.fillStyle = "#000000";
          frontCtx.textAlign = "center"; // Center the text horizontally
          frontCtx.textBaseline = "middle"; // Center vertically
  
          const nameX = canvasWidth / 2; // Center of the canvas
          const nameY = (canvasHeight * 0.21) + 50; // Middle section
  
          frontCtx.fillText(emmergencyName, nameX, nameY);
        }
  
        if (emergencyAddress) {
          frontCtx.font = "bold 100px GilmerMedium";
          frontCtx.fillStyle = "#000000d0";
          frontCtx.textAlign = "center"; // Center the text horizontally
          frontCtx.textBaseline = "middle"; // Center vertically
  
          const nameX = canvasWidth / 2; // Center of the canvas
          const nameY = (canvasHeight * 0.21) + 190; // Middle section
  
          frontCtx.fillText(emergencyAddress, nameX, nameY);
        }
  
        // Set text styling for emergency ID card
        frontCtx.font = "bold 100px GilmerMedium";
        frontCtx.fillStyle = "#000000d0";
        frontCtx.textAlign = "left";
        frontCtx.textBaseline = "middle";
  
        // DOB - left side
        if (emergencyDOB) {
          const dobX = (canvasWidth * 0.25) + 20; // Left side, 10px from center line
          const dobY = (canvasHeight * 0.45) - 10; // Middle section
          frontCtx.fillText(emergencyDOB, dobX, dobY);
        }
  
        // Height - left side
        if (emergencyHeight) {
          const heightX = (canvasWidth * 0.25) + 25; // Left side, 10px from center line
          const heightY = (canvasHeight * 0.55) - 39; // Middle section, below DOB
          frontCtx.fillText(emergencyHeight, heightX, heightY);
        }
  
        // Blood Type - right side
        if (emergencyBloodType) {
          const bloodTypeX = (canvasWidth * 0.76) + 25; // Right side, 10px from center line
          const bloodTypeY = (canvasHeight * 0.45) - 15; // Middle section
          frontCtx.fillText(emergencyBloodType, bloodTypeX, bloodTypeY);
        }
  
        // Weight - right side
        if (emergencyWeight) {
          const weightX = (canvasWidth * 0.76) + 25; // Right side, 10px from center line
          const weightY = (canvasHeight * 0.55) - 45; // Middle section, below blood type
          frontCtx.fillText(emergencyWeight, weightX, weightY);
        }
  
      
  
       if (emergencyContacts) {
          const contactsY = (canvasHeight * 0.75) + 30;
  
          frontCtx.fillStyle = "#000";
          frontCtx.textAlign = "center"; // Center alignment
  
          const maxWidth = canvasWidth * 0.40;
          const lineHeight = 120;
          const contactsX = (maxWidth / 2) + (canvasWidth * 0.05); // Center X position
  
          let currentLine = "";
          let lineStart = 0;
          let lineCount = 0;
  
          for (let i = 0; i < emergencyContacts.length; i++) {
            currentLine += emergencyContacts[i];
  
            if (frontCtx.measureText(currentLine).width > maxWidth || i === emergencyContacts.length - 1) {
              let lineEnd = (i === emergencyContacts.length - 1) ? i + 1 : i;
  
              // Set font size based on line number
              if (lineCount === 0) {
                frontCtx.font = "bold 110px GilmerMedium"; // First line
              } else {
                frontCtx.font = "normal 90px GilmerRegular"; // Second line and beyond
              }
  
              // Draw the current line
              frontCtx.fillText(
                emergencyContacts.slice(lineStart, lineEnd),
                contactsX,
                contactsY + lineHeight * lineCount,
              );
  
              lineCount++;
              lineStart = lineEnd;
              currentLine = "";
            }
          }
  
          frontCtx.textAlign = "left"; // Reset to default
        }
        if(emergencyNumber){
  
  
          const contactsY = (canvasHeight * 0.75) + 30;
  
          frontCtx.fillStyle = "#000";
          frontCtx.textAlign = "center"; // Center alignment
          frontCtx.font = "bold 100px GilmerMedium";
          const maxWidth = canvasWidth * 0.40;
          const lineHeight = 120;
          const contactsX = (maxWidth / 2) + (canvasWidth * 0.05); 
           frontCtx.fillText(
                emergencyNumber,
                contactsX,
                contactsY + lineHeight 
              );
        }
  
  
  
  
        if (emergencyContacts2) {
          const contactsY = (canvasHeight * 0.75) + 30;
  
          frontCtx.fillStyle = "#000";
          frontCtx.textAlign = "center"; // Center alignment
  
          const maxWidth = canvasWidth * 0.40;
          const lineHeight = 120;
          const contactsX = (canvasWidth / 2) + (canvasWidth * 0.25); // Right side position
  
          let currentLine = "";
          let lineStart = 0;
          let lineCount = 0;
  
          for (let i = 0; i < emergencyContacts2.length; i++) {
            currentLine += emergencyContacts2[i];
  
            if (frontCtx.measureText(currentLine).width > maxWidth || i === emergencyContacts2.length - 1) {
              let lineEnd = (i === emergencyContacts2.length - 1) ? i + 1 : i;
  
              // Set font size based on line number
              if (lineCount === 0) {
                frontCtx.font = "bold 110px GilmerMedium"; // First line
              } else {
                frontCtx.font = "normal 90px GilmerRegular"; // Second line and beyond
              }
  
              // Draw the current line
              frontCtx.fillText(
                emergencyContacts2.slice(lineStart, lineEnd),
                contactsX,
                contactsY + lineHeight * lineCount,
              );
  
              lineCount++;
              lineStart = lineEnd;
              currentLine = "";
            }
          }
  
          frontCtx.textAlign = "left"; // Reset to default
        }
  
        if(emergencyNumber2){
  
           const contactsY = (canvasHeight * 0.75) + 30;
  
          frontCtx.fillStyle = "#000";
          frontCtx.textAlign = "center"; // Center alignment
  frontCtx.font = "bold 100px GilmerMedium";
           const maxWidth = canvasWidth * 0.40;
          const lineHeight = 120;
          const contactsX = (canvasWidth / 2) + (canvasWidth * 0.25); // Right side position
           frontCtx.fillText(
                emergencyNumber2,
                contactsX,
                contactsY + lineHeight 
              );
        }
  
  
      } else {
        // Animal Card fields (existing logic)
        const animalInput = document.getElementById("animalNameInput");
        const handlerInput = document.getElementById("handlerNameInput");
        const animalName = animalInput ? animalInput.value.trim() : "";
        const handlerName = handlerInput ? handlerInput.value.trim() : "";
  
        // Animal's Name - moved 15px lower and 93px to the left
        const animalX = (canvasWidth * FRONT_LAYOUT.animal.xPct) + (FRONT_LAYOUT.animal.offsetX || 0) - 93; // Moved 93px to the left (95px - 2px right = 93px)
        const animalY = (canvasHeight * FRONT_LAYOUT.animal.yPct) + (FRONT_LAYOUT.animal.offsetY || 0) + 15; // Moved 15px lower (25px - 10px higher = 15px)
        if (animalName) {
          drawFittedText(
            animalName,
            animalX,
            animalY,
            FRONT_LAYOUT.animal.maxWidthPct,
            FRONT_LAYOUT.animal.baseFontPx,
            FRONT_LAYOUT.animal.minFontPx,
          );
        }
  
        // Handler's Name - moved 15px lower and 95px to the left for all cards
        let handlerX, handlerY;
        if (currentCardType === "blue_dog" || currentCardType === "red_dog" || currentCardType === "combo_dog") {
          // Blue and Red dog cards: Handler's name moved 15px lower and 93px to the left (95px - 2px right = 93px)
          handlerX = (canvasWidth * FRONT_LAYOUT.handler.xPct) + (FRONT_LAYOUT.handler.offsetX || 0) - 93; // Moved 93px to the left (95px - 2px right = 93px)
          handlerY = (canvasHeight * FRONT_LAYOUT.handler.yPct) + (FRONT_LAYOUT.handler.offsetY || 0) + 14; // Moved 15px lower (24px - 10px higher = 14px)
        } else {
          // Other animal cards: Moved 15px lower and 95px to the left
          handlerX = (canvasWidth * FRONT_LAYOUT.handler.xPct) + (FRONT_LAYOUT.handler.offsetX || 0) - 95; // Moved 95px to the left (97px - 2px right = 95px)
          handlerY = (canvasHeight * FRONT_LAYOUT.handler.yPct) + (FRONT_LAYOUT.handler.offsetY || 0) + 15; // Moved 15px lower (25px - 10px higher = 15px)
        }
  
        if (handlerName) {
          drawFittedText(
            handlerName,
            handlerX,
            handlerY,
            FRONT_LAYOUT.handler.maxWidthPct,
            FRONT_LAYOUT.handler.baseFontPx,
            FRONT_LAYOUT.handler.minFontPx,
          );
        }
  
        // Address and Telephone fields for dog cards and blue cat card only
        if (
          currentCardType === "blue_dog" || currentCardType === "red_dog" || currentCardType === "emotional_dog"
          || currentCardType === "blue_cat" || currentCardType === "combo_dog"
        ) {
          const addressInput = document.getElementById("addressInput");
          const telephoneInput = document.getElementById("telephoneInput");
          const address = addressInput ? addressInput.value.trim() : "";
          const telephone = telephoneInput ? telephoneInput.value.trim() : "";
  
          // Address - positioned above telephone field, moved 140px to the left total
          if (address) {
            const addressX = (canvasWidth * 0.75) + 10 - 140; // Right side, moved 140px to the left (100px + 40px more)
            const addressY = (canvasHeight * 0.35) + 5 - 10; // Above telephone field, moved 10px higher
            frontCtx.font = "10px Gilmer"; // Changed to Gilmer font
            frontCtx.fillStyle = "#000000";
            frontCtx.textAlign = "left";
            frontCtx.textBaseline = "middle";
            frontCtx.fillText(address, addressX, addressY);
          }
  
          // Telephone - positioned where "Telephone:" label is shown, moved 7px higher and 58px left
          if (telephone) {
            const telephoneX = (canvasWidth * 0.75) + 10 - 58; // Right side, moved 58px to the left (55px + 3px more)
            const telephoneY = (canvasHeight * 0.42) + 5 - 17; // Below address field, moved 7px higher (20px - 3px lower = 17px total)
            frontCtx.font = "10px Gilmer"; // Changed to Gilmer font
            frontCtx.fillStyle = "#000000";
            frontCtx.textAlign = "left";
            frontCtx.textBaseline = "middle";
            frontCtx.fillText(telephone, telephoneX, telephoneY);
          }
        }
      }
    }
  
    // Centralized redraw for front canvas
    function redrawFrontCanvas() {
      console.log(
        "redrawFrontCanvas called - frontCanvas:",
        frontCanvas,
        "frontCtx:",
        frontCtx,
        "frontImage:",
        frontImage,
      );
      if (!frontCanvas || !frontCtx || !frontImage) {
        console.error("Missing required elements for front canvas redraw");
        return;
      }
      console.log("Drawing image on front canvas...");
      drawImageOnCanvas(frontCanvas, frontCtx, frontImage);
  
      if (frontPhotoImage) {
        placePhotoOnFront(frontPhotoImage);
      }
  
      // if(idNumber){
  
      //   isIdGenerated = false;
      //   placeIDOnFrontCanvas(idNumber);
      // }
      drawNamesOnFront();
      console.log("Front canvas redraw completed");
    }
  
    // Centralized redraw for back canvas
    function redrawBackCanvas() {
      // for child_identification_red will trigger front names also
      redrawFrontCanvas();
  
      console.log("redrawBackCanvas called - backCanvas:", backCanvas, "backCtx:", backCtx, "backImage:", backImage);
      if (!backCanvas || !backCtx || !backImage) {
        console.error("Missing required elements for back canvas redraw");
        return;
      }
      console.log("Drawing image on back canvas...");
      drawImageOnCanvas(backCanvas, backCtx, backImage);
      drawBackSideText();
      console.log("Back canvas redraw completed");
    }
  
    // Draw text on back side (expiry date)
    async function drawBackSideText() {
      if (!backCanvas || !backCtx || !backImage) return;
  
      if (currentCardType === "emergency_id_card") {
        // Emergency ID Card back side fields
        const emergencyAllergiesInput = document.getElementById("emergencyAllergiesInput");
        const emergencyMedicalConcernsInput = document.getElementById("emergencyMedicalConcernsInput");
        const emergencyNotesInput = document.getElementById("emergencyNotesInput");
  
        const allergies = emergencyAllergiesInput ? emergencyAllergiesInput.value.trim() : "";
        const medicalConcerns = emergencyMedicalConcernsInput ? emergencyMedicalConcernsInput.value.trim() : "";
        const notes = emergencyNotesInput ? emergencyNotesInput.value.trim() : "";
  
        const canvasWidth = backCanvas.width;
        const canvasHeight = backCanvas.height;
  
        // Set text styling for emergency ID card back side
        backCtx.font = "bold 100px GilmerMedium";
        backCtx.fillStyle = "#000000";
        backCtx.textAlign = "left";
        backCtx.textBaseline = "middle";
  
        // Allergies - positioned after "Allergies" label
        if (allergies) {
          const allergiesX = (canvasWidth * 0.026) + 35;
          const allergiesY = (canvasHeight * 0.32) + 10;
          const maxWidth = 2200;
          const lineHeight = 100;
          let breakIndex = allergies.length;
          let currentText = "";
          for (let i = 0; i < allergies.length; i++) {
            currentText += allergies[i];
  
            if (backCtx.measureText(currentText).width > maxWidth) {
              breakIndex = i;
              break;
            }
          }
  
          const line1 = allergies.slice(0, breakIndex);
  
          const line2 = allergies.slice(breakIndex);
  
          backCtx.fillText(line1, allergiesX, allergiesY);
  
          if (line2.length > 0) {
            backCtx.fillText(line2, allergiesX, allergiesY + lineHeight);
          }
        }
  
        // Medical Concerns - positioned after "Medical Concerns" label
        if (medicalConcerns) {
          const concernsX = (canvasWidth * 0.026) + 35;
          const concernsY = (canvasHeight * 0.56) + 25;
  
          const maxWidth = 2200;
          const lineHeight = 100;
  
          let breakIndex = medicalConcerns.length;
          let currentText = "";
  
          // Find index where text exceeds max width
          for (let i = 0; i < medicalConcerns.length; i++) {
            currentText += medicalConcerns[i];
  
            if (backCtx.measureText(currentText).width > maxWidth) {
              breakIndex = i;
              break;
            }
          }
  
          // Break into two lines
          const line1 = medicalConcerns.slice(0, breakIndex);
          const line2 = medicalConcerns.slice(breakIndex);
  
          // Draw first line
          backCtx.fillText(line1, concernsX, concernsY);
  
          // Draw second line (if exists)
          if (line2.length > 0) {
            backCtx.fillText(line2, concernsX, concernsY + lineHeight);
          }
        }
  
        // Notes - positioned after "Notes" label
        if (notes) {
          const notesX = (canvasWidth * 0.026) + 35; // Left side
          const notesY = (canvasHeight * 0.84) + 5; // Lower section
  
          const maxWidth = 2200; // Max width for one line
          const lineHeight = 100; // Space between lines
  
          let currentLine = "";
          let lineStart = 0;
          let lineCount = 0; // Count lines drawn
  
          for (let i = 0; i < notes.length; i++) {
            currentLine += notes[i];
  
            if (backCtx.measureText(currentLine).width > maxWidth || i === notes.length - 1) {
              let lineEnd = (i === notes.length - 1) ? i + 1 : i;
  
              // Draw the current line
              backCtx.fillText(
                notes.slice(lineStart, lineEnd),
                notesX,
                notesY + lineHeight * lineCount,
              );
  
              lineCount++; // Move to next line
              lineStart = lineEnd;
              currentLine = "";
            }
          }
        }
      } else if (currentCardType === "child_identification" || currentCardType === "child_identification_red") {
        const childExpiryInput = document.getElementById("childExpiryInput");
        const childAdditionalInput = document.getElementById("childAdditionalInput");
        const expiryDate = childExpiryInput ? childExpiryInput.value.trim() : "";
        const additionalInfo = childAdditionalInput ? childAdditionalInput.value.trim() : "";
  
        if (expiryDate && currentCardType === "child_identification") {
          const canvasWidth = backCanvas.width;
          const canvasHeight = backCanvas.height;
          await document.fonts.load("normal 78px GilmerRegular");
          // Position expiry date in left bottom corner
          backCtx.font = "normal 78px GilmerRegular";
          backCtx.fillStyle = currentCardType === "child_identification" ? "#FF0000" : "#FF0000";
          backCtx.textAlign = "left";
          backCtx.textBaseline = "bottom";
  
          const expiryX = (canvasWidth * 0.15) + 35; // 25px to the right (20px + 5px)
          const expiryY = (canvasHeight * 0.88) - 20; // 20px higher
  
          backCtx.fillStyle = "#FF0000"; // Red color
          backCtx.fillText(expiryDate, expiryX, expiryY); // Just the date, no "Expires:" label
          console.log(`Expiry date "${expiryDate}" placed at (${expiryX}, ${expiryY}) on back side`);
        }
  
        if (additionalInfo) {
          const canvasWidth = backCanvas.width;
          const canvasHeight = backCanvas.height;
          if (currentCardType !== "child_identification_red") {
            // Position additional info at bottom of back side
            await document.fonts.load("normal 90px GilmerMedium");
            backCtx.font = "normal 90px GilmerMedium";
            backCtx.fillStyle = "#fff";
            backCtx.textAlign = "center";
            backCtx.textBaseline = "bottom";
  
            const additionalX = canvasWidth * 0.5; // Center horizontally
            const additionalY = canvasHeight * 0.97; // Bottom of card
  
            backCtx.fillText(additionalInfo, additionalX, additionalY);
            console.log(`Additional info "${additionalInfo}" placed at (${additionalX}, ${additionalY}) on back side`);
  
            return;
          }
  
          // Position additional info at bottom of back side
          await document.fonts.load("normal 90px GilmerRegular");
          backCtx.font = "normal 90px GilmerRegular";
          backCtx.fillStyle = "#000";
          backCtx.textAlign = "left";
          backCtx.textBaseline = "bottom";
  
          const additionalX = canvasWidth * 0.185; // Center horizontally
          const additionalY = canvasHeight * 0.80; // Bottom of card
  
          backCtx.fillText(additionalInfo, additionalX, additionalY);
  
          console.log(`Additional info "${additionalInfo}" placed at (${additionalX}, ${additionalY}) on back side`);
        }
      }
    }
  
    async function generateRandomID() {
      if (
        currentCardType === "autism_card_infinity" || currentCardType === "autism_card_puzzle"
        || currentCardType === "emergency_id_card"
      ) return;
      console.log("Generating random ID number...");
  
      if (
        currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
        || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
      ) {
        if (!window.comboCanvases) {
          alert("Please select a card type first and wait for combo cards to load");
          return;
        }
      } else {
        if (!frontImage) {
          alert("Please select a card type first and wait for front side to load");
          return;
        }
  
        if (!frontCanvas || !frontCtx) {
          console.error("Front canvas or context not available");
          alert("Front side not ready. Please try again.");
          return;
        }
      }
  
      console.log("Canvas ready, proceeding with ID generation");
  
      // Generate random ID number (format: XXXXXXXXXX - no dash)
      const part1 = Math.floor(Math.random() * 90000) + 10000; // 5 digit number
      const part2 = Math.floor(Math.random() * 90000) + 10000; // 5 digit number
      const randomID = `${part1}${part2}`;
  
      console.log("Generated ID:", randomID);
  
      // Store the generated ID for QR code and save functions
      window.currentCardUniqueId = randomID;
  
      if (
        (currentCardType === "combo_dog"
          || currentCardType === "combo_red_dog"
          || currentCardType === "combo_emotional_dog"
          || currentCardType === "combo_emotional_cat"
          || currentCardType === "combo_cat")
        && !isIdGenerated
        // && window.comboCanvases.blueFront.idNumber == null
        // && window.comboCanvases.emotionalFront.idNumber == null
      ) {
        isIdGenerated = true;
        drawIDOnComboCanvases(randomID);
        return;
      }
      placeIDOnFrontCanvas(randomID);
  
      // Show success message
      // showSuccessMessage(`Random ID Number Generated: ${randomID}`);
    }
  
  
    async function placeIDOnFrontCanvas(randomID) {
      if (!frontCanvas || !frontCtx) {
        console.error("Front canvas or context not available");
        alert("Front side not ready. Please try again.");
        return;
      }
  
      if (!isIdGenerated) idNumber = randomID;
  
      const canvasWidth = frontCanvas.width;
      const canvasHeight = frontCanvas.height;
  
      // ----------------------------------------
      // CHILD IDENTIFICATION (BLUE)
      // ----------------------------------------
      if (currentCardType === "child_identification" && !isIdGenerated) {
        isIdGenerated = true;
  
        await document.fonts.load("bold 90px GilmerMedium");
        frontCtx.font = "bold 90px Gilmer";
        frontCtx.fillStyle = "#fff";
        frontCtx.textAlign = "right";
        frontCtx.textBaseline = "bottom";
  
        const x = canvasWidth * 0.94;
        const y = canvasHeight * 0.975;
  
        frontCtx.fillText(`Card No: ${randomID}`, x, y);
  
        console.log(`ID number placed at (${x}, ${y}) for child_identification`);
        return;
      }
  
      // ----------------------------------------
      // CHILD IDENTIFICATION (RED)
      // ----------------------------------------
      if (currentCardType === "child_identification_red" && !isIdGenerated) {
        isIdGenerated = true;
  
        await document.fonts.load("bold 84px GilmerMedium");
        frontCtx.font = "bold 84px GilmerMedium";
        frontCtx.fillStyle = "#000";
        frontCtx.textAlign = "right";
        frontCtx.textBaseline = "bottom";
  
        const x = canvasWidth * 0.929;
        const y = canvasHeight * 0.212 - 10;
  
        frontCtx.fillText(randomID, x, y);
  
        console.log(`ID placed at (${x}, ${y}) for child_identification_red`);
        return;
      }
  
      // Prevent duplication for other cards
      if (isIdGenerated) return;
  
      // ----------------------------------------
      // SHARED POSITION FOR ALL OTHER CARDS
      // ----------------------------------------
      const idBoxX = canvasWidth * 0.205;
      const idBoxY = canvasHeight * 0.90;
      const boxWidth = canvasWidth * 0.28;
  
      let textX = idBoxX + boxWidth / 2;
      let textY = idBoxY;
  
      // Default styling
      frontCtx.font = "bold 10px GilmerMedium";
      frontCtx.fillStyle = "#FF0000";
      frontCtx.textAlign = "center";
      frontCtx.textBaseline = "middle";
  
      // ----------------------------------------
      // BLUE & RED DOG
      // ----------------------------------------
      if (currentCardType === "blue_dog" || currentCardType === "red_dog") {
        textX -= 8; // 8px left
        textY += 2; // 2px down
  
        frontCtx.fillText(randomID, textX, textY);
  
        console.log(`ID "${randomID}" placed for ${currentCardType} at (${textX}, ${textY})`);
        return;
      }
  
      // ----------------------------------------
      // EMOTIONAL DOG
      // ----------------------------------------
      if (currentCardType === "emotional_dog") {
        textX -= 4; // 4px left
        textY += 5; // 5px below
  
        frontCtx.fillText(randomID, textX, textY);
  
        console.log(`ID "${randomID}" placed for emotional_dog at (${textX}, ${textY})`);
        return;
      }
  
      // ----------------------------------------
      // OTHER ANIMAL CARDS (DEFAULT)
      // ----------------------------------------
      textX -= 2; // left
      textY += 2; // down
  
      frontCtx.fillText(randomID, textX, textY);
      console.log(`ID "${randomID}" placed at (${textX}, ${textY})`);
  
      // Set Direction
    }
  
    function addQRCode() {
      if (
        currentCardType === "autism_card_infinity" || currentCardType === "autism_card_puzzle"
        || currentCardType === "emergency_id_card"
      ) return;
      console.log("QR Code button clicked, currentCardType:", currentCardType);
  
      if (
        currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
        || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
      ) {
        console.log("Combo card detected, checking comboCanvases:", window.comboCanvases);
        if (!window.comboCanvases) {
          alert("Please select a card type first to add QR code");
          return;
        }
      } else {
        if (!backImage || !frontImage) {
          alert("Please select a card type first to add QR code");
          return;
        }
      }
  
      // Generate unique QR code URL (reuse existing ID if available)
      if (!window.currentCardUniqueId) {
        // Generate simple numeric ID (same format as generateRandomID)
        const part1 = Date.now().toString().slice(-5);
        const part2 = Math.floor(Math.random() * 90000) + 10000;
        window.currentCardUniqueId = `${part1}${part2}`;
      }
      const qrCodeUrl = window.currentCardUniqueId;
  
      // Store the unique ID for later use
      window.currentQRCodeUrl = qrCodeUrl;
  
      let qrSize = 350;
      let qrHeight = 350;
  
      const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${
        encodeURIComponent(qrCodeUrl)
      }&size=${qrSize}x${qrHeight}&margin=0`;
  
      console.log("Generating QR code with unique URL:", qrUrl);
  
      const qrImg = new Image();
      qrImg.crossOrigin = "anonymous";
  
      qrImg.onload = function() {
        console.log("QR code image loaded, placing on both sides, currentCardType:", currentCardType);
  
        if (
          currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
          || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
        ) {
          console.log("Adding QR code to combo canvases...");
          // For combo cards, add QR code to all 4 canvases
          addQRToComboCanvases(qrImg, qrSize);
        } else {
          // Regular cards
          // Position in bottom-right corner of back side (moved 15px higher)
          const margin = 10;
          let backX = backCanvas.width - qrSize - margin - 110;
          let backY = backCanvas.height - qrSize - margin - 260; // 15px higher
  
          if (currentCardType == "child_identification_red") {
            backX = backCanvas.width - qrSize - margin - 80;
            backY = backCanvas.height - qrSize - margin - 30; // 15px higher
            qrSize = 320;
          }
  
          backCtx.drawImage(qrImg, backX, backY, qrSize, qrSize);
  
          console.log(`QR code placed at (${backX}, ${backY}) on back side`);
  
          // Position in bottom-right corner of front side, moved 5px lower
          if (currentCardType != "child_identification" && currentCardType != "child_identification_red") {
            alert(currentCardType);
            const frontX = frontCanvas.width - qrSize - margin + 10;
            const frontY = frontCanvas.height - qrSize - margin; // Moved 5px lower
            frontCtx.drawImage(qrImg, frontX, frontY, qrSize, qrSize);
            console.log(`QR code placed at (${frontX}, ${frontY}) on front side`);
          }
        }
  
        showSuccessMessage("QR Code added to all card sides with unique URL!");
      };
  
      qrImg.onerror = function() {
        console.error("Failed to generate QR code");
        alert("Failed to generate QR code. Please try again.");
      };
  
      qrImg.src = qrUrl;
    }
  
    // Add QR code to combo canvases
    function addQRToComboCanvases(qrImg, qrSize) {
      if (!window.comboCanvases) {
        console.error("comboCanvases not available");
        return;
      }
  
      console.log("Adding QR code to combo canvases:", window.comboCanvases);
      const margin = 10;
  
      // Add to front and back canvases based on toggle states
      if (comboToggleStates.blueDog) {
        addQRToComboCanvas("blue", "Front", qrImg, qrSize, margin);
        addQRToComboCanvas("blue", "Back", qrImg, qrSize, margin);
      }
  
      if (comboToggleStates.handler) {
        addQRToComboCanvas("emotional", "Front", qrImg, qrSize, margin);
        addQRToComboCanvas("emotional", "Back", qrImg, qrSize, margin);
      }
    }
  
    // Add QR code to individual combo canvas
    function addQRToComboCanvas(cardType, side, qrImg, qrSize, margin) {
      const key = cardType + side; // e.g., 'blueFront', 'emotionalBack'
      const canvasData = window.comboCanvases[key];
      if (!canvasData) {
        console.error(`Canvas data not found for ${key}. Available keys:`, Object.keys(window.comboCanvases));
        return;
      }
  
      const canvas = canvasData.canvas;
      const ctx = canvasData.ctx;
  
      // Store QR code information for redrawing
      canvasData.qrImg = qrImg;
      canvasData.qrSize = qrSize;
      canvasData.qrMargin = margin;
  
      // Position in bottom-right corner
      const x = canvas.width - qrSize - margin - 135;
      let y = canvas.height - qrSize - margin - 30;
  
      // Adjust position based on side
      if (side === "Back") {
        y -= 15; // 15px higher on back side
      } else {
        y += 5; // 5px lower on front side
      }
  
      ctx.drawImage(qrImg, x, y, qrSize, qrSize);
      console.log(`QR code placed at (${x}, ${y}) on combo ${cardType} ${side} side`);
    }
  
    function saveCard() {
  
      if(isDataSaved) {
        alert("You already saved this card! You can't save it again.");
        return ;
      }
  
      // if (!frontImage && !backImage) {
      //   alert("Please select a card type first");
      //   return;
      // }
  
      console.log("Saving card...");
  
      // Generate unique ID if not already generated
      if (!window.currentCardUniqueId) {
        // Generate simple numeric ID (same format as generateRandomID)
        const part1 = Date.now().toString().slice(-5);
        const part2 = Math.floor(Math.random() * 90000) + 10000;
        window.currentCardUniqueId = `${part1}${part2}`;
      }
  
      console.log("Saving card with unique ID:", window.currentCardUniqueId);
  
      // Collect all form data
      const cardData = {
        cardType: currentCardType,
        uniqueId: window.currentCardUniqueId,
        qrCodeUrl: window.currentQRCodeUrl || "",
        fields: {},
      };
  
      // Collect field data based on card type
      if (currentCardType === "child_identification" || currentCardType === "child_identification_red") {
        const childNameInput = document.getElementById("childNameInput");
        const childAgeInput = document.getElementById("childAgeInput");
        const childAddressInput = document.getElementById("childAddressInput");
        const childAdditionalInput = document.getElementById("childAdditionalInput");
  
        if (childNameInput) cardData.fields.childName = childNameInput.value;
        if (childAgeInput) cardData.fields.childAge = childAgeInput.value;
        if (childAddressInput) cardData.fields.childAddress = childAddressInput.value;
        if (childAdditionalInput) cardData.fields.childAdditional = childAdditionalInput.value;
        if (childBeneficiaryCountInput) cardData.fields.childBeneficiaryCount = childBeneficiaryCountInput.value;
      } else if (currentCardType === "autism_card_infinity" || currentCardType === "autism_card_puzzle") {
        const autismNameInput = document.getElementById("autismNameInput");
        const autismContactInput = document.getElementById("autismContactInput");
        const autismNotesInput = document.getElementById("autismNotesInput");
  
        if (autismNameInput) cardData.fields.autismName = autismNameInput.value;
        if (autismContactInput) cardData.fields.autismContact = autismContactInput.value;
        if (autismNotesInput) cardData.fields.autismNotes = autismNotesInput.value;
        if (autismBeneficiaryCountInput) cardData.fields.autismBeneficiaryCount = autismBeneficiaryCountInput.value;
      } else if (currentCardType === "emergency_id_card") {
        const emergencyDOBInput = document.getElementById("emergencyDOBInput");
        const emergencyHeightInput = document.getElementById("emergencyHeightInput");
        const emergencyBloodTypeInput = document.getElementById("emergencyBloodTypeInput");
        const emergencyWeightInput = document.getElementById("emergencyWeightInput");
        const emergencyContactsInput = document.getElementById("emergencyContactsInput");
        const emergencyAllergiesInput = document.getElementById("emergencyAllergiesInput");
        const emergencyMedicalConcernsInput = document.getElementById("emergencyMedicalConcernsInput");
        const emergencyNotesInput = document.getElementById("emergencyNotesInput");
  
        if (emergencyDOBInput) cardData.fields.emergencyDOB = emergencyDOBInput.value;
        if (emergencyHeightInput) cardData.fields.emergencyHeight = emergencyHeightInput.value;
        if (emergencyBloodTypeInput) cardData.fields.emergencyBloodType = emergencyBloodTypeInput.value;
        if (emergencyWeightInput) cardData.fields.emergencyWeight = emergencyWeightInput.value;
        if (emergencyContactsInput) cardData.fields.emergencyContacts = emergencyContactsInput.value;
        if (emergencyAllergiesInput) cardData.fields.emergencyAllergies = emergencyAllergiesInput.value;
        if (emergencyMedicalConcernsInput) cardData.fields.emergencyMedicalConcerns = emergencyMedicalConcernsInput.value;
        if (emergencyNotesInput) cardData.fields.emergencyNotes = emergencyNotesInput.value;
        if (emergencyBeneficiaryCountInput) {
          cardData.fields.emergencyBeneficiaryCount = emergencyBeneficiaryCountInput.value;
        }
      } else if (
        currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
        || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
      ) {
        // Combo card fields - same data for both cards
        const animalNameInput = document.getElementById("animalNameInput");
        const handlerNameInput = document.getElementById("handlerNameInput");
        const addressInput = document.getElementById("addressInput");
        const telephoneInput = document.getElementById("telephoneInput");
  
        if (animalNameInput) cardData.fields.animalName = animalNameInput.value;
        if (handlerNameInput) cardData.fields.handlerName = handlerNameInput.value;
        if (addressInput) cardData.fields.address = addressInput.value;
        if (telephoneInput) cardData.fields.telephone = telephoneInput.value;
        if (beneficiaryCountInput) cardData.fields.beneficiaryCount = beneficiaryCountInput.value;
      } else {
        // Animal card fields
        const animalNameInput = document.getElementById("animalNameInput");
        const handlerNameInput = document.getElementById("handlerNameInput");
        const addressInput = document.getElementById("addressInput");
        const telephoneInput = document.getElementById("telephoneInput");
  
        if (animalNameInput) cardData.fields.animalName = animalNameInput.value;
        if (handlerNameInput) cardData.fields.handlerName = handlerNameInput.value;
        if (addressInput) cardData.fields.address = addressInput.value;
        if (telephoneInput) cardData.fields.telephone = telephoneInput.value;
        if (beneficiaryCountInput) cardData.fields.beneficiaryCount = beneficiaryCountInput.value;
      }
  
      // Convert canvases to base64
      const frontImageData = frontCanvas.toDataURL("image/png");
      const backImageData = backCanvas.toDataURL("image/png");
  
      if (
        currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
        || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
      ) {
        isDataSaved = true;
        // For combo cards, save both dog and service dog handler cards
        saveComboCards(cardData, frontImageData, backImageData);
      } else {
        if(isDataSaved != false){
          
           return ;
        }
        // Prepare data for server
        const saveData = {
          ...cardData,
          frontImage: frontImageData,
          backImage: backImageData,
        };
  
        // Save to database and folder
        fetch("save_card.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(saveData),
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              console.log("Card saved to database:", data);
  
              showSuccessMessage(`Card saved successfully! Unique ID: ${data.uniqueId}`);
            } else {
              console.error("Error saving card:", data.message);
              alert("Error saving card: " + data.message);
            }
  
             isDataSaved = true;
          })
          .catch(error => {
            console.error("Error:", error);
            alert("Error saving card: " + error.message);
          });
      }
    }
  
    function saveComboCards(cardData, frontImageData, backImageData) {
  
  
      // Determine combo type
      const isRedCombo = currentCardType === "combo_red_dog";
      const isEmotionalCombo = currentCardType === "combo_emotional_dog";
      const isEmotionalCatCombo = currentCardType === "combo_emotional_cat";
  
      let dogCardType, handlerCardType;
      if (isRedCombo) {
        dogCardType = "red_dog";
        handlerCardType = "service_dog_handler_red";
      } else if (isEmotionalCombo) {
        dogCardType = "emotional_dog";
        handlerCardType = "emotional_support_dog";
      } else if (isEmotionalCatCombo) {
        dogCardType = "blue_cat";
        handlerCardType = "emotional_cat_handler";
      } else {
        dogCardType = "blue_dog";
        handlerCardType = "service_dog_handler";
      }
  
      // Get combo canvas data
      let dogFrontImageData, dogBackImageData, handlerFrontImageData, handlerBackImageData;
  
      if (window.comboCanvases && window.comboCanvases.blueFront && window.comboCanvases.blueBack) {
        dogFrontImageData = window.comboCanvases.blueFront.canvas.toDataURL("image/png");
        dogBackImageData = window.comboCanvases.blueBack.canvas.toDataURL("image/png");
      } else {
        dogFrontImageData = frontImageData;
        dogBackImageData = backImageData;
      }
  
      if (window.comboCanvases && window.comboCanvases.emotionalFront && window.comboCanvases.emotionalBack) {
        handlerFrontImageData = window.comboCanvases.emotionalFront.canvas.toDataURL("image/png");
        handlerBackImageData = window.comboCanvases.emotionalBack.canvas.toDataURL("image/png");
      } else {
        handlerFrontImageData = frontImageData;
        handlerBackImageData = backImageData;
      }
  
      // Create two separate card data objects
      const dogData = {
        ...cardData,
        cardType: dogCardType,
        uniqueId: cardData.uniqueId, // Use the same ID for combo detection
        frontImage: dogFrontImageData,
        backImage: dogBackImageData,
        fileSuffix: "_dog", // Add suffix for unique file names
      };
  
      const serviceDogHandlerData = {
        ...cardData,
        cardType: handlerCardType,
        uniqueId: cardData.uniqueId, // Use the same ID for combo detection
        frontImage: handlerFrontImageData,
        backImage: handlerBackImageData,
        fileSuffix: "_handler", // Add suffix for unique file names
      };
  
      // Save dog card
      fetch("save_card.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(dogData),
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log("Dog card saved:", data);
  
            // Save service dog handler card
            return fetch("save_card.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify(serviceDogHandlerData),
            });
          } else {
            throw new Error("Failed to save dog card: " + data.message);
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log("Service dog handler card saved:", data);
            showSuccessMessage(
              `Combo cards saved successfully! Dog ID: ${dogData.uniqueId}, Service Dog Handler ID: ${serviceDogHandlerData.uniqueId}`,
            );
  
            // Download both cards locally
            downloadComboCardsLocally();
          } else {
            throw new Error("Failed to save service dog handler card: " + data.message);
          }
        })
        .catch(error => {
          console.error("Error saving combo cards:", error);
          alert("Error saving combo cards: " + error.message);
        });
    }
  
    function downloadComboCardsLocally() {
      // Download blue dog card
      if (frontImage) {
        frontCanvas.toBlob(function(blob) {
          const url = URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = `blue_dog_combo_front_${Date.now()}.png`;
          a.click();
          URL.revokeObjectURL(url);
        });
      }
  
      // Download emotional dog card (same front, different back)
      if (backImage) {
        backCanvas.toBlob(function(blob) {
          const url = URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = `emotional_dog_combo_back_${Date.now()}.png`;
          a.click();
          URL.revokeObjectURL(url);
        });
      }
    }
  
    function downloadComboCardsSmart() {
      console.log("Smart combo download - Toggle states:", comboToggleStates);
      console.log("Available combo canvases:", window.comboCanvases);
  
      const enabledCards = [];
      const timestamp = Date.now();
  
      // Check which cards are enabled and collect their canvas data
      if (comboToggleStates.blueDog && window.comboCanvases && window.comboCanvases.blueFront) {
        enabledCards.push({
          name: "Service_Dog",
          frontCanvas: window.comboCanvases.blueFront.canvas,
          backCanvas: window.comboCanvases.blueBack.canvas,
        });
      }
  
      if (comboToggleStates.handler && window.comboCanvases && window.comboCanvases.emotionalFront) {
        enabledCards.push({
          name: "Service_Dog_Handler",
          frontCanvas: window.comboCanvases.emotionalFront.canvas,
          backCanvas: window.comboCanvases.emotionalBack.canvas,
        });
      }
  
      console.log("Enabled cards for download:", enabledCards);
  
      if (enabledCards.length === 0) {
        alert("No cards are enabled for download. Please enable at least one card using the toggle buttons.");
        return;
      }
  
      // Always download as separate files (4 files when both enabled, fewer when some disabled)
      downloadComboCardsAsSeparateFiles(enabledCards, timestamp);
    }
  
    function downloadComboCardsAsSeparateFiles(cards, timestamp) {
      console.log("Downloading combo cards as separate files:", cards);
  
      let downloadCount = 0;
      const totalDownloads = cards.length * 2; // Each card has front and back
  
      function downloadNext() {
        if (downloadCount >= totalDownloads) {
          console.log("All combo card downloads completed");
          return;
        }
  
        const cardIndex = Math.floor(downloadCount / 2);
        const isBack = downloadCount % 2 === 1;
        const card = cards[cardIndex];
  
        if (!card) {
          downloadCount++;
          setTimeout(downloadNext, 100);
          return;
        }
  
        const canvas = isBack ? card.backCanvas : card.frontCanvas;
        const side = isBack ? "back" : "front";
        const filename = `${card.name}_${side}_${timestamp}.png`;
  
        if (canvas) {
          canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
  
            console.log(`Downloaded: ${filename}`);
            downloadCount++;
  
            // Small delay between downloads to avoid browser blocking
            setTimeout(downloadNext, 300);
          });
        } else {
          console.warn(`Canvas not found for ${card.name} ${side}`);
          downloadCount++;
          setTimeout(downloadNext, 100);
        }
      }
  
      // Start the download sequence
      downloadNext();
    }
  
    function downloadSingleComboCard(card, timestamp) {
      // Download front side
      if (card.frontCanvas) {
        card.frontCanvas.toBlob(function(blob) {
          const url = URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = `${card.name}_front_${timestamp}.png`;
          a.click();
          URL.revokeObjectURL(url);
        });
      }
  
      // Download back side with delay
      if (card.backCanvas) {
        setTimeout(() => {
          card.backCanvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `${card.name}_back_${timestamp}.png`;
            a.click();
            URL.revokeObjectURL(url);
          });
        }, 500);
      }
    }
  
    function downloadCombinedComboCards(cards, timestamp) {
      // Create a combined canvas for all cards
      const cardWidth = 400;
      const cardHeight = 300;
      const margin = 20;
      const cardsPerRow = 2;
  
      const totalRows = Math.ceil(cards.length * 2 / cardsPerRow); // *2 for front and back
      const combinedWidth = (cardWidth * cardsPerRow) + (margin * (cardsPerRow + 1));
      const combinedHeight = (cardHeight * totalRows) + (margin * (totalRows + 1));
  
      // Create combined canvas
      const combinedCanvas = document.createElement("canvas");
      combinedCanvas.width = combinedWidth;
      combinedCanvas.height = combinedHeight;
      const combinedCtx = combinedCanvas.getContext("2d");
  
      // Fill with white background
      combinedCtx.fillStyle = "white";
      combinedCtx.fillRect(0, 0, combinedWidth, combinedHeight);
  
      let currentRow = 0;
      let currentCol = 0;
  
      // Draw each card's front and back
      cards.forEach(card => {
        // Draw front side
        if (card.frontCanvas) {
          const x = margin + (currentCol * (cardWidth + margin));
          const y = margin + (currentRow * (cardHeight + margin));
          combinedCtx.drawImage(card.frontCanvas, x, y, cardWidth, cardHeight);
  
          // Add label
          combinedCtx.fillStyle = "#333";
          combinedCtx.font = "14px Arial";
          combinedCtx.fillText(`${card.name} - Front`, x, y - 5);
        }
  
        currentCol++;
        if (currentCol >= cardsPerRow) {
          currentCol = 0;
          currentRow++;
        }
  
        // Draw back side
        if (card.backCanvas) {
          const x = margin + (currentCol * (cardWidth + margin));
          const y = margin + (currentRow * (cardHeight + margin));
          combinedCtx.drawImage(card.backCanvas, x, y, cardWidth, cardHeight);
  
          // Add label
          combinedCtx.fillStyle = "#333";
          combinedCtx.font = "14px Arial";
          combinedCtx.fillText(`${card.name} - Back`, x, y - 5);
        }
  
        currentCol++;
        if (currentCol >= cardsPerRow) {
          currentCol = 0;
          currentRow++;
        }
      });
  
      // Download the combined image
      combinedCanvas.toBlob(function(blob) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `Combo_Cards_Combined_${timestamp}.png`;
        a.click();
        URL.revokeObjectURL(url);
      });
    }
  
    function downloadCardLocally() {
      // Download front side
      if (frontImage) {
        frontCanvas.toBlob(function(blob) {
          const url = URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = `${currentCardType}_front_${Date.now()}.png`;
          a.click();
          URL.revokeObjectURL(url);
        });
      }
  
      // Download back side if available
      if (backImage) {
        setTimeout(() => {
          backCanvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `${currentCardType}_back_${Date.now()}.png`;
            a.click();
            URL.revokeObjectURL(url);
          });
        }, 500);
      }
    }
  
    function saveComboCardsAndDownload() {
      console.log("Saving combo cards to database and then downloading...");
  
      // Generate unique ID if not already generated
      if (!window.currentCardUniqueId) {
        const part1 = Date.now().toString().slice(-5);
        const part2 = Math.floor(Math.random() * 90000) + 10000;
        window.currentCardUniqueId = `${part1}${part2}`;
      }
  
      // Get the dropdown card type name
      const cardTypeSelect = document.getElementById("cardTypeSelect");
      const dropdownCardTypeName = cardTypeSelect.options[cardTypeSelect.selectedIndex].text;
  
      // Collect all form data
      const cardData = {
        cardType: currentCardType,
        dropdownCardTypeName: dropdownCardTypeName,
        uniqueId: window.currentCardUniqueId,
        qrCodeUrl: window.currentQRCodeUrl || "",
        fields: {},
      };
  
      // Collect field data based on card type
      if (
        currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
        || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
      ) {
        // Combo card fields - same data for both cards
        const animalNameInput = document.getElementById("animalNameInput");
        const handlerNameInput = document.getElementById("handlerNameInput");
        const addressInput = document.getElementById("addressInput");
        const telephoneInput = document.getElementById("telephoneInput");
        const beneficiaryCountInput = document.getElementById("beneficiaryCountInput");
  
        if (animalNameInput) cardData.fields.animalName = animalNameInput.value;
        if (handlerNameInput) cardData.fields.handlerName = handlerNameInput.value;
        if (addressInput) cardData.fields.address = addressInput.value;
        if (telephoneInput) cardData.fields.telephone = telephoneInput.value;
        if (beneficiaryCountInput) cardData.fields.beneficiaryCount = beneficiaryCountInput.value;
      }
  
      // Convert canvases to base64
      const frontImageData = frontCanvas.toDataURL("image/png");
      const backImageData = backCanvas.toDataURL("image/png");
  
      // First save the combo cards to database
      if(isDataSaved === false) {
  
        saveComboCards(cardData, frontImageData, backImageData);
        isDataSaved = true;
      }
  
      // Then download the cards
      setTimeout(() => {
        downloadComboCardsSmart();
      }, 2000); // Wait 2 seconds for database save to complete
    }
  
    function saveCardAndDownload() {
      console.log("Saving regular card to database and then downloading...");
  
      // Generate unique ID if not already generated
      if (!window.currentCardUniqueId) {
        const part1 = Date.now().toString().slice(-5);
        const part2 = Math.floor(Math.random() * 90000) + 10000;
        window.currentCardUniqueId = `${part1}${part2}`;
      }
  
      // Get the dropdown card type name
      const cardTypeSelect = document.getElementById("cardTypeSelect");
      const dropdownCardTypeName = cardTypeSelect.options[cardTypeSelect.selectedIndex].text;
  
      // Collect all form data
      const cardData = {
        cardType: currentCardType,
        dropdownCardTypeName: dropdownCardTypeName,
        uniqueId: window.currentCardUniqueId,
        qrCodeUrl: window.currentQRCodeUrl || "",
        fields: {},
      };
  
      // Collect field data based on card type
      if (currentCardType === "child_identification" || currentCardType === "child_identification_red") {
        const childNameInput = document.getElementById("childNameInput");
        const dobInput = document.getElementById("dobInput");
        const sexInput = document.getElementById("sexInput");
        const hairInput = document.getElementById("hairInput");
        const eyesInput = document.getElementById("eyesInput");
        const heightInput = document.getElementById("heightInput");
        const weightInput = document.getElementById("weightInput");
        const momInput = document.getElementById("momInput");
        const dadInput = document.getElementById("dadInput");
        const parentsInput = document.getElementById("parentsInput");
        const expiryDateInput = document.getElementById("expiryDateInput");
        const additionalInfoInput = document.getElementById("additionalInfoInput");
  
        if (childNameInput) cardData.fields.childName = childNameInput.value;
        if (dobInput) cardData.fields.dob = dobInput.value;
        if (sexInput) cardData.fields.sex = sexInput.value;
        if (hairInput) cardData.fields.hair = hairInput.value;
        if (eyesInput) cardData.fields.eyes = eyesInput.value;
        if (heightInput) cardData.fields.height = heightInput.value;
        if (weightInput) cardData.fields.weight = weightInput.value;
        if (momInput) cardData.fields.mom = momInput.value;
        if (dadInput) cardData.fields.dad = dadInput.value;
        if (parentsInput) cardData.fields.parents = parentsInput.value;
        if (expiryDateInput) cardData.fields.expiryDate = expiryDateInput.value;
        if (additionalInfoInput) cardData.fields.additionalInfo = additionalInfoInput.value;
      } else if (currentCardType === "autism_card_infinity" || currentCardType === "autism_card_puzzle") {
        const childNameInput = document.getElementById("childNameInput");
        const dobInput = document.getElementById("dobInput");
        const sexInput = document.getElementById("sexInput");
        const hairInput = document.getElementById("hairInput");
        const eyesInput = document.getElementById("eyesInput");
        const heightInput = document.getElementById("heightInput");
        const weightInput = document.getElementById("weightInput");
        const momInput = document.getElementById("momInput");
        const dadInput = document.getElementById("dadInput");
        const parentsInput = document.getElementById("parentsInput");
        const expiryDateInput = document.getElementById("expiryDateInput");
        const additionalInfoInput = document.getElementById("additionalInfoInput");
  
        if (childNameInput) cardData.fields.childName = childNameInput.value;
        if (dobInput) cardData.fields.dob = dobInput.value;
        if (sexInput) cardData.fields.sex = sexInput.value;
        if (hairInput) cardData.fields.hair = hairInput.value;
        if (eyesInput) cardData.fields.eyes = eyesInput.value;
        if (heightInput) cardData.fields.height = heightInput.value;
        if (weightInput) cardData.fields.weight = weightInput.value;
        if (momInput) cardData.fields.mom = momInput.value;
        if (dadInput) cardData.fields.dad = dadInput.value;
        if (parentsInput) cardData.fields.parents = parentsInput.value;
        if (expiryDateInput) cardData.fields.expiryDate = expiryDateInput.value;
        if (additionalInfoInput) cardData.fields.additionalInfo = additionalInfoInput.value;
      } else if (currentCardType === "emergency_id_card") {
        const nameInput = document.getElementById("nameInput");
        const dobInput = document.getElementById("dobInput");
        const addressInput = document.getElementById("addressInput");
        const telephoneInput = document.getElementById("telephoneInput");
        const emergencyContactInput = document.getElementById("emergencyContactInput");
        const medicalInfoInput = document.getElementById("medicalInfoInput");
        const additionalInfoInput = document.getElementById("additionalInfoInput");
  
        if (nameInput) cardData.fields.name = nameInput.value;
        if (dobInput) cardData.fields.dob = dobInput.value;
        if (addressInput) cardData.fields.address = addressInput.value;
        if (telephoneInput) cardData.fields.telephone = telephoneInput.value;
        if (emergencyContactInput) cardData.fields.emergencyContact = emergencyContactInput.value;
        if (medicalInfoInput) cardData.fields.medicalInfo = medicalInfoInput.value;
        if (additionalInfoInput) cardData.fields.additionalInfo = additionalInfoInput.value;
      }
  
      // Convert canvases to base64
      const frontImageData = frontCanvas.toDataURL("image/png");
      const backImageData = backCanvas.toDataURL("image/png");
  
      // First save the card to database
  
      if(isDataSaved == false) {
        saveCard(cardData, frontImageData, backImageData);
        isDataSaved = true;
      }
      
  
      // Then download the card
      setTimeout(() => {
        downloadCardLocally();
      }, 2000); // Wait 2 seconds for database save to complete
    }
  
    function downloadCard() {
      if (
        currentCardType === "combo_dog" || currentCardType === "combo_red_dog"
        || currentCardType === "combo_emotional_dog" || currentCardType === "combo_emotional_cat"
      ) {
        // For combo cards, save to database first, then download
        saveComboCardsAndDownload();
      } else {
        // For regular cards, save to database first, then download
        saveCardAndDownload();
      }
    }
  
    function resetCard() {
      console.log("Resetting card...");
  
      // Clear images
      frontImage = null;
      backImage = null;
      currentCardType = "";
  
      // Switch back to regular display
      document.getElementById("regularCardDisplay").style.display = "flex";
      document.getElementById("comboCardDisplay").style.display = "none";
  
      // Clear canvases
      if (frontCtx && frontCanvas) {
        frontCtx.clearRect(0, 0, frontCanvas.width, frontCanvas.height);
      }
      if (backCtx && backCanvas) {
        backCtx.clearRect(0, 0, backCanvas.width, backCanvas.height);
      }
  
      // Clear combo canvases if they exist
      if (window.comboCanvases) {
        Object.values(window.comboCanvases).forEach(canvasData => {
          if (canvasData.ctx && canvasData.canvas) {
            canvasData.ctx.clearRect(0, 0, canvasData.canvas.width, canvasData.canvas.height);
            canvasData.canvas.style.display = "none";
            canvasData.canvas.nextElementSibling.style.display = "block";
          }
        });
      }
  
      // Hide canvases, show placeholders
      hideCanvas("front");
      hideCanvas("back");
  
      // Reset UI
      document.getElementById("cardTypeSelect").value = "";
      document.getElementById("cardTitle").textContent = "Select a card type to start editing";
  
      // Disable controls
      disableControls();
  
      updateStatus();
      console.log("Card reset complete");
    }
  
    function enableControls() {
      document.getElementById("generateIDBtn").disabled = false;
      document.getElementById("addQRBtn").disabled = false;
      document.getElementById("saveBtn").disabled = false;
      document.getElementById("downloadBtn").disabled = false;
      document.getElementById("resetBtn").disabled = false;
      console.log("Controls enabled");
    }
  
    function disableControls() {
      document.getElementById("generateIDBtn").disabled = true;
      document.getElementById("addQRBtn").disabled = true;
      document.getElementById("saveBtn").disabled = true;
      document.getElementById("downloadBtn").disabled = true;
      document.getElementById("resetBtn").disabled = true;
      console.log("Controls disabled");
    }
  
    function updateStatus() {
      const statusText = document.getElementById("statusText");
      if (statusText) {
        if (frontImage && backImage) {
          statusText.textContent = "Both sides loaded - ready for editing and QR code";
        } else if (frontImage || backImage) {
          statusText.textContent = "One side loaded - loading other side...";
        } else {
          statusText.textContent = "Ready to create cards - select a card type";
        }
      }
    }
  
    function showSuccessMessage(message) {
      const modal = document.getElementById("successModal");
      const messageElement = document.getElementById("successMessage");
  
      if (modal && messageElement) {
        messageElement.textContent = message;
        modal.style.display = "block";
      } else {
        alert(message);
      }
    }
  });
  
  // Global modal function
  function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.style.display = "none";
    }
  }
  
  // Latest Update 12/26/2025 - 5:45 PM
  // This for clear canvas to load new card
  function clearAllCanvases() {
    // Clear combo canvases
    const comboBlueFrontCanvas = document.getElementById("comboBlueFrontCanvas");
    const comboBlueBackCanvas = document.getElementById("comboBlueBackCanvas");
    const comboEmotionalFrontCanvas = document.getElementById("comboEmotionalFrontCanvas");
    const comboEmotionalBackCanvas = document.getElementById("comboEmotionalBackCanvas");
    
    if (comboBlueFrontCanvas) {
      const ctx = comboBlueFrontCanvas.getContext("2d");
      ctx.clearRect(0, 0, comboBlueFrontCanvas.width, comboBlueFrontCanvas.height);
    }
    if (comboBlueBackCanvas) {
      const ctx = comboBlueBackCanvas.getContext("2d");
      ctx.clearRect(0, 0, comboBlueBackCanvas.width, comboBlueBackCanvas.height);
    }
    if (comboEmotionalFrontCanvas) {
      const ctx = comboEmotionalFrontCanvas.getContext("2d");
      ctx.clearRect(0, 0, comboEmotionalFrontCanvas.width, comboEmotionalFrontCanvas.height);
    }
    if (comboEmotionalBackCanvas) {
      const ctx = comboEmotionalBackCanvas.getContext("2d");
      ctx.clearRect(0, 0, comboEmotionalBackCanvas.width, comboEmotionalBackCanvas.height);
    }
  
    // Clear regular/single card canvases (add your single card canvas IDs here)
    const frontCanvas = document.getElementById("frontCanvas"); // adjust ID as needed
    const backCanvas = document.getElementById("backCanvas"); // adjust ID as needed
    
    if (frontCanvas) {
      const ctx = frontCanvas.getContext("2d");
      ctx.clearRect(0, 0, frontCanvas.width, frontCanvas.height);
    }
    if (backCanvas) {
      const ctx = backCanvas.getContext("2d");
      ctx.clearRect(0, 0, backCanvas.width, backCanvas.height);
    }
  }
  
  /**
   * Toggle between regular and combo card displays
   * @param {boolean} isCombo - If true, shows combo display; if false, shows regular display (default: false)
   */
  function toggleCardDisplay(isCombo = false) {
    const regularDisplay = document.getElementById("regularCardDisplay");
    const comboDisplay = document.getElementById("comboCardDisplay");
  
    if (!regularDisplay) {
      console.error("Regular display element not found!");
      return;
    }
  
    if (!comboDisplay) {
      console.error("Combo display element not found!");
      return;
    }
  
    if (isCombo) {
      // Show combo display, hide regular display
      regularDisplay.style.display = "none";
      comboDisplay.style.display = "flex";
    } else {
      // Show regular display, hide combo display
      regularDisplay.style.display = "block"; // or "flex" depending on your layout
      comboDisplay.style.display = "none";
    }
  }