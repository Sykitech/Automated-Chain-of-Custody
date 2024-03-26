<?php
@include 'config.php';

session_start();

if (!isset($_SESSION['admin_name'])) {
    echo "Redirecting to loginform.php";
    header('location:login_form.php');
    exit();
}

// Fetch case details from session if available
if (isset($_SESSION['item_details'])) {
    $defaultCaseNumber = $_SESSION['item_details']['case_number'];
    $defaultCategory = $_SESSION['item_details']['category'];
} else {
    // Default values if no case details in session
    $defaultCaseNumber = '';
    $defaultCategory = '';
}

// Fetch the count of items for the given case_number
$caseNumber = $defaultCaseNumber; // Move this line here
$countItemsQuery = "SELECT COUNT(*) AS item_count FROM evidence WHERE case_number = ?";
$stmtCountItems = mysqli_prepare($conn_cases, $countItemsQuery);
mysqli_stmt_bind_param($stmtCountItems, "s", $caseNumber);
mysqli_stmt_execute($stmtCountItems);
$resultCountItems = mysqli_stmt_get_result($stmtCountItems);
$rowCountItems = mysqli_fetch_assoc($resultCountItems);

// Increment the item count for the new item
$newItemNumber = $rowCountItems['item_count'] + 1;

// Initialize $newItemNumber with a default value
$newItemNumber = 1;

if (isset($_POST['submit_item_details'])) {
    // Get the submitted values
    $caseNumber = $_POST['case_number'];
    $category = $_POST['category'];
    $newItemNumber = $_POST['item_no'];
    $recoveredby = $_POST['recovered_by'];
    $recoveredat = $_POST['recovered_at'];
    $recoverytime = $_POST['recovering_time'];
    $description = $_POST['description'];
    $custodyreason = $_POST['custody_reason'];
    $belongto = $_POST['belong_to'];
    $tags = $_POST['tags'];
    
    // Set the default value for status to "Checked In" if not provided by the user
    $status = isset($_POST['status']) ? $_POST['status'] : 'Checked In';

    $storagelocation = $_POST['storage_location'];

    // Check if the values are not empty
    if (!empty($caseNumber) && !empty($category)) {
        // Check if the case exists
        $checkCaseQuery = "SELECT * FROM cases WHERE case_number = ?";
        $stmtCheckCase = mysqli_prepare($conn_cases, $checkCaseQuery);
        mysqli_stmt_bind_param($stmtCheckCase, "s", $caseNumber);
        mysqli_stmt_execute($stmtCheckCase);
        $resultCheckCase = mysqli_stmt_get_result($stmtCheckCase);

        if ($rowCase = mysqli_fetch_assoc($resultCheckCase)) {
            // Use $newItemNumber as the item number for the new item
            // Proceed with adding the item
            $insertItemQuery = "INSERT INTO evidence (case_number, category, item_no, recovered_by, recovered_at, recovering_time, description, custody_reason, belong_to, tags, status, storage_location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsertItem = mysqli_prepare($conn_cases, $insertItemQuery);
            mysqli_stmt_bind_param($stmtInsertItem, "ssssssssssss", $caseNumber, $category, $newItemNumber, $recoveredby, $recoveredat, $recoverytime, $description, $custodyreason, $belongto, $tags, $status, $storagelocation);

            // Execute the statement
            if (!mysqli_stmt_execute($stmtInsertItem)) {
                echo "Error: " . mysqli_error($conn_cases);
            }

            // Redirect to useraddperson.php or another appropriate page
            header('location:adminadditem.php');

            // Close the statement
            mysqli_stmt_close($stmtInsertItem);
        } else {
            // Case doesn't exist
            echo "Error: Case with number " . $caseNumber . " does not exist.";
        }

        // Close the statement
        mysqli_stmt_close($stmtCheckCase);
    } else {
        echo "Please fill in both Case Number and Category.";
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
                <li class="active">
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
                    <h2>Add Item/ Add</h2>
                </div>
                <div class="user--info">
                    <div class="search--box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="find a case"/>
                </div>
            </div>
        </div>
        <div class="container3">
    <h1 class="form-name">Item Details</h1>
    
    <form action="" method="post">
        
            <div class="flex2">

                <span>Case Number:</span>
                <input type="text" name="case_number" value="<?php echo $defaultCaseNumber; ?>" readonly class="box">
                
                <span>Category:</span>
                <input type="text" name="category" value="<?php echo $defaultCategory; ?>" readonly class="box">

                <span>Item Number:</span>
                <input type="number" name="item_no" value="<?php echo  $newItemNumber; ?>"  class="box">

                
                <span>Recovered By:</span>
                <input type="text" name="recovered_by" placeholder="Enter Names"  required class="box">
                
                <span>recovered at:</span>
                <input type="text" name="recovered_at" placeholder="Enter location" required class="box">
                
                <span>Recovering time:</span>
                <input type="datetime-local" name="recovering_time" placeholder="Enter Time and date" required class="box">
                
                <span>Description:</span>
                <textarea type="text" name="description" placeholder="Enter description" required class="box"></textarea>
                
                <span>Custody Reason:</span>
                <input id="custodyReasonInput" class="dropdown-cbtn" onclick="toggleDropdown('custodydropdown')" type="text" name="custody_reason"  required class="box">
                
                <div  id="custodydropdown" class="dropdown-ccontent">
                           <div class="dropdown-citem" onclick="selectDropdownItem(this)">Approved to destroy</div>
                           <div class="dropdown-citem" onclick="selectDropdownItem(this)">Evidence</div>
                           <div class="dropdown-citem" onclick="selectDropdownItem(this)">Found property</div>
                           <div class="dropdown-citem" onclick="selectDropdownItem(this)">Safe keeping</div>
                           <div class="dropdown-citem" onclick="selectDropdownItem(this)">Search Warrant</div>
                           </div>

                <span>Belongs to:</span>
                <input type="text" name="belong_to" id="belongs_to" placeholder="Enter name" class="box">

                <span>Tags code:</span>
                <input type="text" name="tags" placeholder="Enter tags" class="box">

               
               <span>Current Status:</span>
               <input type="text" name="status" value="Checked In" class="box" readonly>

                <span>Storage Location:</span>
                <input type="text" name="storage_location" placeholder="Enter storage location" class="box">
                
            </div>
        
        <input type="submit" value="save" name="submit_item_details" class="btn">
        & <input type="" name="" placeholder="" class="box">
    </form>
        </div>
        </div>
       
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.46.0/apexcharts.min.js"> </script>
        <script src="dashboard.js"></script>
    </body>
</html>