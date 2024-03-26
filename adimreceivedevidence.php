<?php

@include 'config.php';

session_start();


if(!isset($_SESSION['admin_name'])){
    echo "Redirecting to loginform.php";
    header('location:login_form.php');
    exit();
}
if(isset($_SESSION['admin_name'])) {
    // Get the admin name from the session
    $adminName = $_SESSION['admin_name'];
// Fetch user information from the database
$query = "SELECT id, organisation FROM user_form WHERE name = '$adminName'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $userDetails = mysqli_fetch_assoc($result);
    $userID = $userDetails['id'];
    $organisation = $userDetails['organisation'];
} else {
    // Handle the case where user details or organization information is not found
    $userID = 'N/A'; // Set a default value or show an error message
    $organisation = 'N/A';
}
} else {
// Handle the case where admin_name is not set in the session
$organisation = 'N/A'; // Set a default value or show an error message
}


// Check if the form is submitted for item checkout
if (isset($_POST['checkout_item'])) {

    checkoutItem();
}

// Function to perform the item checkout
function checkoutItem() {
    global $conn_cases, $conn, $userID;

    // Get the checked-out details from the form
    $checkedTo = mysqli_real_escape_string($conn_cases, $_POST['checked_to']);
    $checkoutReason = mysqli_real_escape_string($conn_cases, $_POST['checkout_reason']);
    $notes = mysqli_real_escape_string($conn_cases, $_POST['notes']);
    $expectedReturn = $_POST['expected_return'];

    // Query to fetch the ID of the receiver based on the name
    $queryReceiverId = "SELECT id FROM user_form WHERE name = ?";
    $stmtReceiverId = mysqli_prepare($conn, $queryReceiverId);
    mysqli_stmt_bind_param($stmtReceiverId, 's', $checkedTo);
    mysqli_stmt_execute($stmtReceiverId);
    $resultReceiverId = mysqli_stmt_get_result($stmtReceiverId);

    // Check if a user with the given name exists
    if ($resultReceiverId && mysqli_num_rows($resultReceiverId) > 0) {
        $receiverDetails = mysqli_fetch_assoc($resultReceiverId);
        $checkedToId = $receiverDetails['id'];

        // Check if selectedItems key is set in $_POST
        if (isset($_POST['selectedItems']) && is_array($_POST['selectedItems'])) {
            // Get the selected items from the checkboxes
            $selectedItems = $_POST['selectedItems'];

            foreach ($selectedItems as $evidenceId) {
                // Proceed with the checkout
                // Insert checkout details into the new checkout table using a prepared statement
                $insertQuery = "INSERT INTO checkout (evidence_id, sender_id, receiver_id, checked_to, checkout_reason, notes, expected_return) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn_cases, $insertQuery);
                mysqli_stmt_bind_param($stmt, 'iiissss', $evidenceId, $userID, $checkedToId, $checkedTo, $checkoutReason, $notes, $expectedReturn);
                $result = mysqli_stmt_execute($stmt);

                if (!$result) {
                    // Checkout successful for the current item
                    // Notify the user or perform any other actions
                    echo "Error: Item checkout failed for ID $evidenceId.";
                } 

                // Close the statement
                mysqli_stmt_close($stmt);
            }
        } elseif (isset($_POST['evidence_ids']) && is_array($_POST['evidence_ids'])) {
            // Get the selected items from the hidden inputs
            $selectedItems = $_POST['evidence_ids'];
        } else {
            // selectedItems key is not set or not an array
            echo "Error: No items selected for checkout.";
        }
    } else {
        // Handle the case where the user IDs don't exist
        echo "Error: Invalid user ID(s).";
    }
}
if (isset($receivedItems) && is_array($receivedItems)){
if ($receivedItems !== null) {
    foreach ($receivedItems as $item) {
        // Display information about each received item
        echo "Item ID: {$item['evidence_id']}, Description: {$item['description']} <br>";
        // Add other details or links as needed
    }
} else {
    // Handle the case where $receivedItems is null
    echo "No received items found.";
}
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <title>Automated chain of custody Dashboard</title>
         <!--font awesome cdn link-->
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!--custom css file link-->
         <link rel="stylesheet" href="dashboard.css">
         <style>
            /* Style for the cases header */
.casesheader {
    background-color: white;
    padding: 10px;
    border-radius: 10px;
}

/* Style for the cases menu */
.cases--menu {
    background-color: white;
    border-bottom: 1px solid #ccc;
   
}

.cases--list {
    list-style-type: none;
    margin: 0;
    padding: 0;
    display: flex;
}
.casesactive {
    background-color: #e0dfdf;
    
}


.cases-link {
    text-decoration: none;
    color:#191C40;
    padding: 10px;
    font-size: 12px;
    display: block;
    transition: background-color 0.3s, color 0.3s;
}

.cases-link:hover{
    background-color: #e0dfdf;
    color:#191C40;
}
.casesnavcontainer .action-btn{
    margin-top: 1rem;
     display: inline-block;
     padding: .9rem 3rem;
     font-size: 12px;
     color: #fff;
     background: var(--blue);
     cursor: pointer;
     border: 1px solid transparent; 
 
}
.casesnavcontainer .action-btn:hover{
    letter-spacing: .2rem;
    
}
.casesnavcontainer .action-content {
    display: none;
    position: absolute;
    background-color: #eee; /* Adjust the background color to match your sidebar theme */
    min-width: 160px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
    border-radius: 8px; /* Add border-radius for rounded corners */
}
.action-content.show {
        display: block;
    }

/* Style for the dropdown items */
.casesnavcontainer .action-item {
    padding: 12px;
    display: block;
    text-decoration: none;
    cursor: pointer;
    color:var(--blue); /* Text color for dropdown items */
    font-size: 12px; /* Adjust the font size */
    transition: background-color 0.3s ease; /* Smooth transition on hover */
}

/* Change color on hover */
.casesnavcontainer .action-item:hover {
    background-color: var(--white); /* Change the background color on hover to match the active state in the sidebar */
}

.table-body {
        background-color: #f5f5f5; /* Off-white background color */
        color: #191C40; /* Blue text color */
        padding: 15px; /* Add padding for better visibility */
        font-size: 12px;
    }

    .table-bordered {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px; /* Adjust as needed */
    }

    .table-bordered th, .table-bordered td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .table-headers {
        background-color: #b29758;
        color: #fff;
    }    

    .table-body .table-bordered .btn {
    margin-top: 1rem;
     display: inline-block;
     padding: .9rem 3rem;
     font-size: 12px;
     color: #fff;
     background: var(--blue);
     cursor: pointer;
     border: 1px solid transparent; 
 }
 .table-body .table-bordered .btn:hover {
    letter-spacing: .2rem;
    /* Darker background color on hover */
}#checkoutModal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
   z-index: 1000;
    overflow: auto;
}

#checkoutModal .modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%; /* Adjust as needed */
    max-width: 600px; /* Set a maximum width if desired */
    background-color: #eee;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    padding: 20px;
    font-size: 12px;
}

#checkoutModal label {
    margin-bottom: 5px;
}

#checkoutModal .box {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    box-sizing: border-box;
    background-color: #f5f5f5;
}

#checkoutModal .popup {
    display: flex;
    flex-direction: column; /* Align content vertically */
}

#checkoutModal .btn {
    background-color: #191C40;
    color: #fff;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

#checkoutModal .btn:hover {
    background-color: #191C40;
}

/* Additional styling for the close button */
#checkoutModal .close {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 20px;
    cursor: pointer;
}


         </style>
    </head>
    <body>
    
        <div class="sidebar">
            <div class="logo"></div>
            <?php 
            $select = "SELECT * FROM `user_form` WHERE name = '" . $_SESSION['admin_name'] . "'"
            or die('query failed');
             $result = mysqli_query($conn, $select);
    
             
             
             if(mysqli_num_rows($result) > 0){
                $fetch = mysqli_fetch_assoc($result);
            
            }
            if($fetch['image'] == ''){
            echo '<img src="images/default-avatar.png">';
         }else{
            echo '<img src="uploaded_img/'.$fetch['image'].'">';
         }
        ?>
        <h5><?php echo $_SESSION['admin_name'] ?></h5>
            <ul class="menu">
                <li >
                    <a href="admindashboard.php" >
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="admin_profile.php">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="admin_viewcases.php">
                        <i class="fas fa-folder"></i>
                        <span>Recent Cases</span>
                    </a>
                </li>
                <li>
                    <a href="#"  >
                        <i class="fas fa-plus"></i>
                        <span class="dropdown-btn" onclick="toggleDropdown('myDropdown')">Add</span>
                    </a>
                    <div  id="myDropdown" class="dropdown-content">
                            <a href="adminaddcase.php"  class="dropdown-item">Case</a>
                            <a href="adminaddperson.php"  class="dropdown-item">Person</a>
                            <a href="adminadditem.php"  class="dropdown-item">Item</a>
                        </div>
                </li>
                <li class="active">
                    <a href="adimreceivedevidence.php">
                        <i class="fas fa-tasks"></i>
                        <span>Tasks</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-cogs"></i>
                        <span class="dropdown-btn" onclick="toggleDropdown('settingsDropdown')">Settings</span>
                    </a>
                    <div  id="settingsDropdown" class="dropdown-content">
                            <a href="adminorganisation.php"   class="dropdown-item">Organisations</a>
                            <a href="adminoffices.php" class="dropdown-item">Offices</a>
                            <a href="adminpermissions.php"  class="dropdown-item">Permissions</a>
                            <a href="adminusergroups.php"   class="dropdown-item">User Groups</a>
                            <a href="adminuseradmin.php" class="dropdown-item">User Admin</a>
                            <a href="adminsessions.php"  class="dropdown-item">Sessions</a>
                        
                        </div>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-tools"></i>
                        <span>Tools</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-wrench"></i>
                        <span>Systems</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>User Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-child"></i>
                        <span>Help</span>
                    </a>
                </li>
                <li class="logout">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
</div>
<div class="main--content">
            <div class="header--wrapper">
                <div class="header--title">
                    <h3>Welcome <span><?php echo $_SESSION['admin_name'] ?></span></h3>
                    <h2>Tasks</h2>
                </div>
                <div class="user--info">
                    <div class="search--box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="find a case"/>
                </div>
            </div>
        </div>
       
        
        <div class="casesheader" id="casesheader">
            <div class="casesnavcontainer">
                <h1>Review the folowing tasks.</h1>
                <span id="actionsdropdown" class="action-btn" onclick="toggleActionDropdown('actionsDropdown')" value="">Actions</span>

    <div id="actionsDropdown" class="action-content">
    <a href="#" class="action-item" onclick="toggleCheckoutModal()">Check out</a>
        <a href="#" class="action-item">Check in</a>
        <a href="#" class="action-item">Move item</a>
        <a href="#" class="action-item">Duplicate</a>
        <a href="#" class="action-item">Split</a>
        <a href="#" class="action-item">Add to Task</a>
    </div>
    <br><br>
                <div class="cases--menu" id="cases-menu">
                    <ul class="cases--list">
                        <li class="casesactive" >
                            <a href="#" class="cases-link">Item</a>
                        </li>
                        <li class="cases--item">
                            <a href="adminnotes.php" class="cases-link">Notes</a>
                        </li>
                
                    </ul>
                </div>
        </div>
 
        <div class="table-body">
        <form action="" method="post" id="checkboxForm">
        <div id="checkoutModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeCheckoutModal()">&times;</span>
    <!-- Add your checkout form or content here -->
    <h2>Checkout Items</h2>
   
            <div class="popup">
                <div>
                    <label for="organisation">Organisation:</label>
                    <input type="text" id="organisation" name="organisation" class="box" required>
                </div>

                <div>
                    <label for="checked_to">Checked out to:</label>
                    <input type="text" id="checked_to" name="checked_to" class="box" required>
                </div>

                <div>
                    <label for="checkout_reason">Reason:</label>
                    <input type="text" id="checkout_reason" name="checkout_reason" class="box" required>
                </div>

                <div>
                    <label for="notes">Notes:</label>
                    <textarea id="notes" name="notes" placeholder="Enter note" required class="box"></textarea>
                </div>

                <div>
                    <label for="expected_return">Expected return:</label>
                    <input type="datetime-local" id="expected_return" name="expected_return" required class="box">
                </div>

                <input type="submit" value="Checkout" name="checkout_item" id="checkoutLink" class="btn">
            </div>
        
    <!-- Add form or any other content as needed -->
    <button onclick="performCheckout()">Confirm Checkout</button>
  </div>
</div>
        <table class="table-bordered">
            <thead>
                <tr class="table-headers">
                    <td>Select</td>
                    <td>View item</td>
                    <td>Organization</td>
                    <td>Primary Case#</td>
                    <td>Category</td>
                    <td>Item#</td>
                    <td>Description</td>
                    <td>Status</td>
                    <td>Storage Location</td>
                    <td>Checkout reason</td>
                </tr>
            </thead>
            <tbody>
            <?php
            
              $userIDQuery = "SELECT id FROM user_db.user_form WHERE name = '$adminName'";
              $userIDResult = mysqli_query($conn, $userIDQuery);
              
              // Check if the query was successful
              if ($userIDResult) {
                  $userIDRow = mysqli_fetch_assoc($userIDResult);
              
                  // Check if the user ID was found
                  if ($userIDRow) {
                      $userID = $userIDRow['id'];
              
                      // Your query to fetch received items
                      $query = "SELECT c.evidence_id, c.checked_to, c.checkout_reason, c.notes, c.expected_return, 
                                      e.case_number, e.category, e.description, e.status, e.storage_location
                               FROM checkout c
                               JOIN evidence e ON c.evidence_id = e.evidence_id
                               WHERE c.receiver_id = '$userID'
                               ORDER BY c.expected_return DESC";
              
                      $result = mysqli_query($conn_cases, $query);
              
                      if ($result) {
                          while ($row = mysqli_fetch_assoc($result)) {
                              $evidenceid = $row['evidence_id'];
                              $checkedTo = $row['checked_to'];
                              $checkoutReason = $row['checkout_reason'];
                              $notes = $row['notes'];
                              $expectedReturn = $row['expected_return'];
                              $caseNumber = $row['case_number'];
                              $category = $row['category'];
                              $description = $row['description'];
                              $status = $row['status'];
                              $storagelocation = $row['storage_location'];
                              // ... (rest of your code)
                              ?>
                              <tr>
                                  <td>
                                  <input type="checkbox" name="selectedItems[]" value="<?php echo $evidenceid; ?>">
                                  <input type="hidden" name="evidence_ids[]" value="<?php echo $evidenceid; ?>">
                                  </td>
                                  <td>
                                  <a href="adminreceiveditemview.php?evidence_id=<?php echo $evidenceid; ?>" class="btn"> View </a>
                                  </td>
                                  <td><?php echo $checkedTo; ?></td>
                                  <td><?php echo $caseNumber; ?></td>
                                  <td><?php echo $category; ?></td>
                                  <td><?php echo $evidenceid; ?></td>
                                  <td><?php echo $description; ?></td>
                                  <td><?php echo $status; ?></td>
                                  <td><?php echo $storagelocation; ?></td>
                                  <td><?php echo $checkoutReason; ?></td>
                              </tr>
                              <?php
                          }
                      } else {
                          // Handle the case where the query failed
                          echo "Error: " . mysqli_error($conn_cases);
                      }
                  } else {
                      // Handle the case where user ID was not found
                      echo "Error: User ID not found.";
                  }
              } else {
                  // Handle the case where the query failed
                  echo "Error: " . mysqli_error($conn);
              }
              ?>
            </tbody>
        </table>
        
        </form>
</div>
        </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.46.0/apexcharts.min.js"></script>
        <script src="dashboard.js"></script>
        <script>
function toggleActionDropdown(dropdownId) {
    var dropdown = document.getElementById(dropdownId);
    // Toggle the display property with 'block' and 'none'
    dropdown.style.display = (dropdown.style.display === 'block' || dropdown.style.display === '') ? 'none' : 'block';
}

// Close the dropdown if the user clicks outside of it
window.onclick = function (event) {
    if (!event.target.matches('.action-btn')) {
        var dropdowns = document.getElementsByClassName("action-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.style.display === 'block') {
                openDropdown.style.display = 'none';
            }
        }
    }
}

// Function to open the checkout modal
function openCheckoutModal() {
  var modal = document.getElementById('checkoutModal');
  modal.style.display = 'block';
}

// Function to close the checkout modal
function closeCheckoutModal() {
  var modal = document.getElementById('checkoutModal');
  modal.style.display = 'none';
}
//function to perform checkout
function performCheckout() {
    console.log("Performing checkout...");
    

    // Get the selected items from the checkboxes
    var selectedItems = document.querySelectorAll('input[name="selectedItems[]"]:checked');

    // Check if any items are selected
    if (selectedItems.length > 0) {
        // Prepare an array to store the selected item values
        var selectedItemsArray = [];

        // Loop through the selected items and add their values to the array
        selectedItems.forEach(function (item) {
            selectedItemsArray.push(item.value);
        });

        // Create an input element for selectedItems
        var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", "selectedItems");
        input.setAttribute("value", JSON.stringify(selectedItemsArray));

        // Append the input element to the form
        document.getElementById('checkboxForm').appendChild(input);


        // Notify the user or perform any other actions
        console.log("Items checked out successfully!");
    } else {
        // No items selected for checkout
        alert ("Error: No items selected.");
    }

    // Close the modal after checkout
    closeCheckoutModal();
}


// Add an event listener to the "Check out" link
document.getElementById('checkoutLink').addEventListener('click', function() {
  openCheckoutModal();
});
function toggleCheckoutModal() {
        var modal = document.getElementById('checkoutModal');
        modal.style.display = (modal.style.display === 'block' || modal.style.display === '') ? 'none' : 'block';
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }

    function performCheckout() {
        // Add your logic for checkout
        // ...
        // Close the modal after checkout
        closeCheckoutModal();
    }
</script>
        
    </body>
</html>