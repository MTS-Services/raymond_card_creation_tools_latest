<?php 
 require_once '../Middleware/Authentication.php';
 $auth = new Authentication ;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Card Creation Tool</title>
  <link rel="icon" href="../favicon.png" type="image/x-icon">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <!-- All the css has been removed to another file in the location at the top of directory /public/assets/style.css Just link it up -->
  <link rel="stylesheet" href="../public/assets/style.css" />
  <!-- End linking Style -->
   <style>
    .card-design {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    border: 2px solid #e9ecef;
  }
   </style>
</head>

<body>
  <div class="container">
    <header class="header">
      <h1><i class="fas fa-id-card"></i> Card Creation Tool</h1>
      <p>Select a card type and edit both front and back sides</p>
      <div style="margin-top: 20px">
        <a href="view_all_cards.php" style="
              display: inline-block;
              padding: 10px 20px;
              background: rgba(255, 255, 255, 0.2);
              color: white;
              text-decoration: none;
              border-radius: 20px;
              border: 2px solid rgba(255, 255, 255, 0.3);
              transition: all 0.3s ease;
              margin: 0 10px;
            ">
          <i class="fas fa-list"></i> View All Cards
        </a>
        <a href="../admin/dashboard.php" style="
              display: inline-block;
              padding: 10px 20px;
              background: rgba(255, 255, 255, 0.2);
              color: white;
              text-decoration: none;
              border-radius: 20px;
              border: 2px solid rgba(255, 255, 255, 0.3);
              transition: all 0.3s ease;
              margin: 0 10px;
            ">
          <i class="fas fa-home"></i> Home
        </a>
      </div>
    </header>

    <div class="main-content">
      <!-- Sidebar -->
      <div class="sidebar">
        <h3><i class="fas fa-layer-group"></i> Card Selection</h3>

        <div class="dropdown-section">
          <label for="cardTypeSelect">Choose Card Type:</label>
          <select id="cardTypeSelect" class="card-dropdown">
            <option value="">Select a card type...</option>
            <!-- <option value="blue_dog">Service Dog Card/Handler (Blue)</option>
                    <option value="red_dog">Service Dog Card/Handler (Red)</option>
                    <option value="service_dog_handler">Service Dog Handler</option>
                    <option value="emotional_dog">Emotional Dog Card/Handler</option>
                    <option value="blue_cat">Emotional Cat Card/Handler</option> -->
            <option value="combo_dog">
              Service Dog (Blue) + Service Dog Handler
            </option>
            <option value="combo_red_dog">
              Service Dog (Red) + Service Dog Handler
            </option>
            <option value="combo_emotional_dog">
              Emotional Dog + Emotional Support Dog
            </option>
            <option value="combo_emotional_cat">
              Emotional Support Cat + Cat Handler
            </option>
            <option value="child_identification">
              Child Identification Card (Blue)
            </option>
            <option value="child_identification_red">
              Child Identification Card (Red)
            </option>
            <option value="autism_card_infinity">
              Autism Card (Infinity Sign)
            </option>
            <option value="autism_card_puzzle">
              Autism Card (Puzzle Piece)
            </option>
            <option value="emergency_id_card">Emergency ID Card</option>
          </select>
        </div>

        <div class="controls-section">
          <h4>Front Photo & Details</h4>
          <label for="photoInput" style="
                display: block;
                margin: 8px 0 6px;
                color: #495057;
                font-weight: 600;
              ">Upload Photo</label>
          <!-- <div id="photoInputContainer" style="display:flex; flex-direction: row;  justify-content: center;  margin-bottom: 12px;">
              <input
              id="photoInput"
              type="file"
              accept="image/*"
              style="
                width: 100%;
                padding: 10px;
                border: 2px solid #e9ecef;
                border-radius: 6px;
                display: none;
                background: white;
              "
            />
            <label for="photoInput" class="btn btn-trasnparent">Choose Photo</label>
            <button class="btn btn-danger display-inline border border-dotted" id="addExtraInput">+</button>
          </div> -->
          <div id="photoInputContainer"
            style="display:flex; align-items:center; gap:10px; justify-content:space-between; margin-bottom:12px;">

            <input id="photoInput" type="file" accept="image/*" style="display:none;">
            <label for="photoInput"
              style="padding:10px 25px;border:2px dotted #4f46e5;border-radius:6px;width:80%; background:transparent;color:#4f46e5;font-weight:500;cursor:pointer;transition:all 0.3s ease;"
              onmouseover="this.style.background='#f0f0ff';">Choose Photo</label>
            <button id="addExtraInput"
              style="padding:15px 15px;background-color:#4f46e5;color:#fff;border:none;border-radius:6px;font-size:14px;font-weight:bold;cursor:pointer;transition:all 0.3s ease;"
              onmouseover="this.style.background='#3730a3';" onmouseout="this.style.background='#4f46e5';">+</button>
          </div>

          <div id="photoInputContainer"
            style="display:flex; align-items:center; gap:10px; justify-content:space-between; margin-bottom:12px;">
            <input type="file" id="photoInput2" accept="image/*" style="width: 100%; display: none;">
            <label for="photoInput2" id="photoInput2Label"
              style="padding:10px 25px;border:2px dotted #4f46e5;border-radius:6px;width:80%; background:transparent;color:#4f46e5;font-weight:500;cursor:pointer;transition:all 0.3s ease; display: none;"
              onmouseover="this.style.background='#f0f0ff';">Choose Photo</label>
            <button
              style="padding:15px 15px;background-color:#4f46e5;color:#fff;border:none;border-radius:6px;display: none; font-size:14px;font-weight:bold;cursor:pointer;transition:all 0.3s ease;"
              onmouseover="this.style.background='#3730a3';" onmouseout="this.style.background='#4f46e5';"
              id="removeExtraInput">X</button>
          </div>


          <!-- Animal Card Fields (shown by default) -->
          <div id="animalFields">
            <label for="animalNameInput" style="
                  display: block;
                  margin: 8px 0 6px;
                  color: #495057;
                  font-weight: 600;
                
                ">Animal's Name</label>
            <input id="animalNameInput" type="text" placeholder="Enter Animal's Name" style="
                  width: 100%;
                  padding: 10px;
                  border: 2px solid #e9ecef;
                  border-radius: 6px;
                  margin-bottom: 10px;
                " />

            <label for="handlerNameInput" style="
                  display: block;
                  margin: 8px 0 6px;
                  color: #495057;
                  font-weight: 600;
                ">Handler's Name</label>
            <input id="handlerNameInput" type="text" placeholder="Enter Handler's Name" style="
                  width: 100%;
                  padding: 10px;
                  border: 2px solid #e9ecef;
                  border-radius: 6px;
                  margin-bottom: 10px;
                " />

            <label for="addressInput" style="
                  display: block;
                  margin: 8px 0 6px;
                  color: #495057;
                  font-weight: 600;
                ">Address</label>
            <input id="addressInput" type="text" placeholder="Enter Address" style="
                  width: 100%;
                  padding: 10px;
                  border: 2px solid #e9ecef;
                  border-radius: 6px;
                  margin-bottom: 10px;
                " />

            <label for="telephoneInput" style="
                  display: block;
                  margin: 8px 0 6px;
                  color: #495057;
                  font-weight: 600;
                ">Telephone</label>
            <input id="telephoneInput" type="text" placeholder="Enter Telephone Number" style="
                  width: 100%;
                  padding: 10px;
                  border: 2px solid #e9ecef;
                  border-radius: 6px;
                  margin-bottom: 10px;
                " />

            <label for="beneficiaryCountInput" style="
                  display: block;
                  margin: 8px 0 6px;
                  color: #495057;
                  font-weight: 600;
                ">Number of Beneficiary People</label>
            <input id="beneficiaryCountInput" type="number" placeholder="Enter number of beneficiaries" min="1" style="
                  width: 100%;
                  padding: 10px;
                  border: 2px solid #e9ecef;
                  border-radius: 6px;
                  margin-bottom: 10px;
                " />
          </div>

          <!-- Child Identification Card Fields (hidden by default) -->
          <div id="childFields" style="display: none">
            <div style="margin-bottom: 10px">
              <label for="childNameInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Child Name</label>
              <input id="childNameInput" type="text" placeholder="Enter Child's Full Name" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 10px">
              <div style="flex: 1">
                <label for="childDOBInput" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">DOB</label>
                <input id="childDOBInput" type="text" placeholder="MM/DD/YYYY" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                    " />
              </div>
              <div style="flex: 1">
                <label for="childSexInput" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Sex</label>
                <input id="childSexInput" type="text" placeholder="M/F" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                    " />
              </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 10px">
              <div style="flex: 1">
                <label for="childHairInput" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Hair</label>
                <input id="childHairInput" type="text" placeholder="Hair Color" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                    " />
              </div>
              <div style="flex: 1">
                <label for="childEyesInput" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Eyes</label>
                <input id="childEyesInput" type="text" placeholder="Eye Color" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                    " />
              </div>
            </div>

            <div style="display: flex; gap: 10px; margin-bottom: 10px">
              <div style="flex: 1">
                <label for="childHeightInput" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Height</label>
                <input id="childHeightInput" type="text" placeholder='e.g., 4`6"' style=" width:100%;padding:8px;border:2px solid #e9ecef;border-radius:4px;font-size:12px;" />
              </div>
              <div style="flex: 1">
                <label for="childWeightInput" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Weight</label>
                <input id="childWeightInput" type="text" placeholder="e.g., 75 lbs" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                    " />
              </div>
            </div>



            <div style="margin-bottom: 10px" id="childAddressInputRemove2">
              <label for="childAddressInput2" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Address</label>
              <input id="childAddressInput2" type="text" placeholder="Address " style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>





            <div style="display: flex; gap: 10px; margin-bottom: 10px">
              <div style="flex: 1">
                <label for="childMomInput" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Mom's number</label>
                <input id="childMomInput" type="text" placeholder="Mom's number" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                    " />
              </div>
              <div style="flex: 1">
                <label for="childDadInput" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Dad's number</label>
                <input id="childDadInput" type="text" placeholder="Dad's number" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                    " />
              </div>
            </div>

            <div style="margin-bottom: 10px">
              <label for="childParentsInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Parent's name</label>
              <input id="childParentsInput" type="text" placeholder="Parent's name " style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div style="margin-bottom: 10px" id="childExpiryInputRemove">
              <label for="childExpiryInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Expiry Date</label>
              <input id="childExpiryInput" type="text" placeholder="MM/DD/YYYY" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <!-- <div>
                <label
                  for="childAddressInput2"
                  style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  "
                  >Address</label
                >
                <input
                  id="childAddressInput2"
                  type="text"
                  placeholder="Address"
                  style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  "
                />
              </div> -->

            <div>
              <label for="childAdditionalInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  " id="childAdditionalInputLabel">Additional Information</label>
              <input id="childAdditionalInput" type="text" placeholder="Additional Details" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div style="margin-top: 10px">
              <label for="childBeneficiaryCountInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Number of Beneficiary People</label>
              <input id="childBeneficiaryCountInput" type="number" placeholder="Enter number of beneficiaries" min="1"
                style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>
          </div>
        </div>

        <div id="autismFields" class="controls-section" style="display: none">
          <h4>Autism Card Details</h4>
          <div style="display: flex; flex-direction: column; gap: 10px">
            <div>
              <label for="autismNameInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">My Name</label>
              <input id="autismNameInput" type="text" placeholder="Enter name" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <label for="autismContactInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">My Emergency Contact(s)</label>
              <input id="autismContactInput" type="text" placeholder="Emergency contact information" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <label for="autismNotesInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Notes</label>
              <input id="autismNotesInput" type="text" placeholder="Additional notes" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <label for="autismBeneficiaryCountInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Number of Beneficiary People</label>
              <input id="autismBeneficiaryCountInput" type="number" placeholder="Enter number of beneficiaries" min="1"
                style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>
          </div>
        </div>

        <div id="emergencyFields" class="controls-section" style="display: none">
          <h4>Emergency ID Card Details</h4>
          <div style="display: flex; flex-direction: column; gap: 10px">

            <div>
              <label for="emergencyNameInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Name</label>
              <input id="emergencyNameInput" type="text" placeholder="Name" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <label for="emergencyDOBInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Date of Birth</label>
              <input id="emergencyDOBInput" type="text" placeholder="MM/DD/YYYY" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <label for="emergencyHeightInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Height</label>
              <input id="emergencyHeightInput" type="text" placeholder='e.g., 4`6"' style=" width:100%;padding:8px;border:2px solid #e9ecef;border-radius:4px;font-size:12px;" />
            </div>

            <div>
              <label for="emergencyBloodTypeInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Blood Type</label>
              <input id="emergencyBloodTypeInput" type="text" placeholder="e.g., A+, B-, O+" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <label for="emergencyAddressInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Address</label>
              <input id="emergencyAddressInput" type="text" placeholder="Address" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <label for="emergencyWeightInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Weight</label>
              <input id="emergencyWeightInput" type="text" placeholder="e.g., 150 lbs" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <div>
                <label for="emergencyContactsInput" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Emergency Contact's Name</label>
                <input id="emergencyContactsInput" placeholder="Name" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                      resize: vertical;
                    "/>
              </div>
              <div>
                <label for="emergencyContactsInputNumber" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Emergency Contact's Number</label>
                <input id="emergencyContactsInputNumber" placeholder="Number" rows="3" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                      resize: vertical;
                    "/>
              </div>
            </div>
            <div>
              <div>
                <label for="emergencyContactsInput2" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Emergency Contacts Name 2</label>
                <input id="emergencyContactsInput2" placeholder="Name" rows="3" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                      resize: vertical;
                    "/>
              </div>
              <div>
                <label for="emergencyContactsInputNumber2" style="
                      display: block;
                      margin: 4px 0 2px;
                      color: #495057;
                      font-weight: 600;
                      font-size: 12px;
                    ">Emergency Contacts Number 2</label>
                <input id="emergencyContactsInputNumber2" placeholder="Number 2 " rows="3" style="
                      width: 100%;
                      padding: 8px;
                      border: 2px solid #e9ecef;
                      border-radius: 4px;
                      font-size: 12px;
                      resize: vertical;
                    "/>
              </div>
            </div>

            <div>
              <label for="emergencyAllergiesInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Allergies</label>
              <input id="emergencyAllergiesInput" type="text" placeholder="List any allergies" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <label for="emergencyMedicalConcernsInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Medical Concerns</label>
              <input id="emergencyMedicalConcernsInput" type="text" placeholder="Medical conditions or concerns" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>

            <div>
              <label for="emergencyNotesInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Notes</label>
              <textarea id="emergencyNotesInput" placeholder="Additional medical notes" rows="2" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                    resize: vertical;
                  "></textarea>
            </div>

            <div>
              <label for="emergencyBeneficiaryCountInput" style="
                    display: block;
                    margin: 4px 0 2px;
                    color: #495057;
                    font-weight: 600;
                    font-size: 12px;
                  ">Number of Beneficiary People</label>
              <input id="emergencyBeneficiaryCountInput" type="number" placeholder="Enter number of beneficiaries"
                min="1" style="
                    width: 100%;
                    padding: 8px;
                    border: 2px solid #e9ecef;
                    border-radius: 4px;
                    font-size: 12px;
                  " />
            </div>
          </div>
        </div>

        <div class="controls-section">
          <h4>Actions</h4>
          <button id="generateIDBtn" class="btn btn-primary" disabled
            style="background: #17a2b8; border-color: #17a2b8">
            <i class="fas fa-random"></i> Generate ID Number
          </button>
          <button id="addQRBtn" class="btn btn-warning" disabled>
            <i class="fas fa-qrcode"></i> Add QR Code
          </button>
          <button id="saveBtn" class="btn btn-primary" disabled>
            <i class="fas fa-save"></i> Save Card
          </button>
          <button id="downloadBtn" class="btn btn-success" disabled>
            <i class="fas fa-download"></i> Download
          </button>
          <button id="resetBtn" class="btn btn-danger" disabled>
            <i class="fas fa-undo"></i> Reset
          </button>
        </div>
      </div>

      <!-- Editor Area -->
      <div class="editor-area">
        <div class="editor-header">
          <h2 id="cardTitle">Select a card type to start editing</h2>
        </div>

        <!-- Regular card display -->
        <div class="card-display card-design" id="regularCardDisplay">
          <!-- Front Side -->
          <div class="card-side" id="frontSide">
            <h3><i class="fas fa-id-card"></i> Front Side</h3>
            <canvas id="frontCanvas" class="card-canvas" width="2373" height="1491"></canvas>
            <div class="placeholder" id="frontPlaceholder">
              Select a card type to load front side
            </div>
          </div>

          <!-- Back Side -->
          <div class="card-side" id="backSide" style="margin-bottom: 40px">
            <h3><i class="fas fa-id-card-alt"></i> Back Side</h3>
            <canvas id="backCanvas" class="card-canvas" width="2373" height="1491"></canvas>
            <div class="placeholder" id="backPlaceholder">
              Select a card type to load back side
            </div>
          </div>
        </div>

        <!-- Combo card display (hidden by default) -->
        <div class="combo-card-display" id="comboCardDisplay" style="display: none">
          <!-- Service Dog Section -->
          <div class="combo-card-section">
            <h3 id="comboDogTitle"><i class="fas fa-dog"></i> Service Dog</h3>
            <div class="combo-canvas-row">
              <div class="card-side">
                <h4><i class="fas fa-id-card"></i> Front Side</h4>
                <canvas id="comboBlueFrontCanvas" class="card-canvas" width="2373" height="1491"></canvas>
                <div class="placeholder">
                  Loading Service Dog front side...
                </div>
              </div>
              <div class="card-side">
                <h4><i class="fas fa-id-card-alt"></i> Back Side</h4>
                <canvas id="comboBlueBackCanvas" class="card-canvas" width="2373" height="1491"></canvas>
                <div class="placeholder">
                  Loading Service Dog back side...
                </div>
              </div>
            </div>
            <!-- Toggle Button for Service Dog -->
            <div class="combo-toggle-container">
              <button id="toggleBlueDog" class="combo-toggle-btn active">
                <i class="fas fa-toggle-on"></i> <span>Editing ON</span>
              </button>
            </div>
          </div>

          <!-- Service Dog Handler Section -->
          <div class="combo-card-section">
            <h3 id="comboHandlerTitle">
              <i class="fas fa-user-tie"></i> Service Dog Handler
            </h3>
            <div class="combo-canvas-row">
              <div class="card-side">
                <h4><i class="fas fa-id-card"></i> Front Side</h4>
                <canvas id="comboEmotionalFrontCanvas" class="card-canvas" width="2373" height="1491"></canvas>
                <div class="placeholder">
                  Loading Service Dog Handler front side...
                </div>
              </div>
              <div class="card-side">
                <h4><i class="fas fa-id-card-alt"></i> Back Side</h4>
                <canvas id="comboEmotionalBackCanvas" class="card-canvas" width="2373" height="1491"></canvas>
                <div class="placeholder">
                  Loading Service Dog Handler back side...
                </div>
              </div>
            </div>
            <!-- Toggle Button for Service Dog Handler -->
            <div class="combo-toggle-container">
              <button id="toggleHandler" class="combo-toggle-btn active">
                <i class="fas fa-toggle-on"></i> <span>Editing ON</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="status-bar">
      <span id="statusText">Ready to create cards</span>
    </div>
  </div>

  <script src="card_creation_script.js?v=12.11"></script>
</body>

</html>