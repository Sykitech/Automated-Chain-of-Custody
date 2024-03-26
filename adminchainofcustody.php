<?php

@include 'config.php';

session_start();


if(!isset($_SESSION['admin_name'])){
    echo "Redirecting to loginform.php";
    header('location:login_form.php');
    exit();
}
// Fetch case details from session if available
if (isset($_SESSION['case_information'])) {
    $defaultCaseNumber = $_SESSION['case_information']['case_number'];
 
} else {
    // Default values if no case details in session
    $defaultCaseNumber = '';
   
}
$evidenceid = isset($_GET['evidence_id']) ? $_GET['evidence_id'] : null;

// Retrieve the evidence ID from the URL
$evidenceid = isset($_GET['evidence_id']) ? $_GET['evidence_id'] : null;

// Store the evidence ID in the session
$_SESSION['evidence_id'] = $evidenceid;
// Your PHP code here to fetch necessary data from the database
$transactionId = ''; // Initialize variables with empty values
$checkoutTime = '';
$senderName = '';
$receiverName = '';
$checkoutReason = '';

// Assuming you have fetched the necessary data from the database
// Assign the fetched values to the variables
if (isset($row['checkout_id'])) {
    $transactionId = $row['checkout_id'];
    $checkoutTime = $row['checkout_time'];
    $senderName = $row['sender_name'];
    $receiverName = $row['receiver_name'];
    $checkoutReason = $row['checkout_reason'];
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
}

            .received-evidence-message {
    background-color: #ffc107; /* Yellow background color */
    color: #333; /* Dark text color */
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
}
.download-pdf-btn {
    background-color:var(--blue);
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
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
                <li class="active">
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
                <li>
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
                    <h2>Chain Of Custody</h2>
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
                <h2 class="casenumber">Case <span><?php echo $defaultCaseNumber; ?></span></h2>
                <h2 class="casenumber">Item  <span><?php echo  $evidenceid; ?></span></h2>
                <div class="cases--menu" id="cases-menu">
                    <ul class="cases--list">
                    <li class="cases--item">
                            <a href="adminitemview.php?evidence_id=<?php echo $evidenceid; ?>" class="cases-link">Basic Info</a>
                        </li>
                        <li class="cases--item">
                            <a href="adminitemcase.php" class="cases-link">Cases</a>
                        </li>
                        <li class="cases--item">
                            <a href="adminmedia.php" class="cases-link">Media</a>
                        </li>
                        <li class="cases--item">
                            <a href="adminnotes.php" class="cases-link">Notes</a>
                        </li>
                        <li class="casesactive">
                            <a href="#" class="cases-link">Chain of Custody</a>
                        </li>
                        <li class="cases--item">
                            <a href="admincaseshistory.php" class="cases-link">History</a>
                        </li>
                    </ul>
                </div>
        </div>
        <div class="table-body">
    <table class="table-bordered">
        <thead>
            <tr class="table-headers">
                <td>Transaction ID</td>
                <td>Date</td>
                <td>Issued From</td>
                <td>Issued To</td>
                <td>checkout Reason</td>
              
               
            </tr>
        </thead>
        <tbody>
        <?php
       

       $query = 
       "SELECT c.checkout_id, c.checkout_time, 
                     sender.name AS sender_name, receiver.name AS receiver_name, 
                     c.checkout_reason
              FROM checkout c
              JOIN user_db.user_form sender ON c.sender_id = sender.id
              JOIN user_db.user_form receiver ON c.receiver_id = receiver.id
              WHERE c.evidence_id = ?";
    
           // Prepare the statement
    $stmt = mysqli_prepare($conn_cases, $query);
    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "i", $evidenceid);
    // Execute the query
    mysqli_stmt_execute($stmt);
    // Get the result
    $result = mysqli_stmt_get_result($stmt);
    // Check if there are rows returned
    if (mysqli_num_rows($result) > 0) {
        // Loop through the result set
        while ($row = mysqli_fetch_assoc($result)) {
            // Output the data for each row
            ?>
            <tr>
                <td><?php echo $row['checkout_id']; ?></td>
                <td><?php echo $row['checkout_time']; ?></td>
                <td><?php echo $row['sender_name']; ?></td>
                <td><?php echo $row['receiver_name']; ?></td>
                <td><?php echo $row['checkout_reason']; ?></td>
            </tr>
            <?php
        }
    } else {
        // No rows found
        echo "Item not found";
    }
    ?>
                
        </tbody>
    </table>

        </div>
        
        <button  id="downloadPdfBtn" class="btn download-pdf-btn"> Download PDF </button>
    
    <!-- Button to print -->
   <button id="printBtn" class="btn download-pdf-btn">Print</button>
        </div>

       
        </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="dashboard.js"></script>
        <script>
        // Function to generate and download PDF
    function downloadPdf() {
      // Check if jsPDF is defined
      if (typeof jsPDF !== "undefined") {
        // Create a new jsPDF instance
        var doc = new jsPDF();

        // Define content for the PDF
        var content = `
          Chain of Custody Report
          
          Transaction ID: <?php echo $transactionId; ?>
          Date: <?php echo $checkoutTime; ?>
          Issued From: <?php echo $senderName; ?>
          Issued To: <?php echo $receiverName; ?>
          Checkout Reason: <?php echo $checkoutReason; ?>
        `;

        // Add content to the PDF
        doc.text(content, 10, 10);

        // Save the PDF with a specific name
        doc.save("chain_of_custody.pdf");
      } else {
        // Handle the case when jsPDF is not defined
        console.error("jsPDF library is not loaded.");
      }
    }

    // Attach click event listener to the button
    document.getElementById("downloadPdfBtn").addEventListener("click", downloadPdf);
 </script>
        
    </body>
</html>