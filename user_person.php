<?php

@include 'config.php';

session_start();

if (!isset($_SESSION['user_name'])) {
    echo "Redirecting to loginform.php";
    header('location:login_form.php');
    exit();
}

// Fetch case details from session if available
if (isset($_SESSION['person_details'])) {
    $defaultCaseNumber = $_SESSION['person_details']['case_number'];
    $defaultBusinessName = $_SESSION['person_details']['business_name'];
} else {
    // Default values if no case details in session
    $defaultCaseNumber = '';
    $defaultBusinessName = '';
}

if (isset($_POST['submit_person_details'])) {
    // Get the submitted values
    $caseNumber = $_POST['case_number'];
    $businessName = $_POST['business_name'];
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'];
    $lastName = $_POST['last_name'];
    $alias = $_POST['alias'];
    $driverLicense = $_POST['driver_license'];
    $race = $_POST['race'];
    $gender = $_POST['gender'];
    $dateOfBirth = $_POST['dob'];
    $mobilePhone = $_POST['phone_no'];
    $otherPhone = $_POST['other_no'];
    $email = $_POST['email'];
    $personType = $_POST['person_type'];
    $note = $_POST['note'];

    // Check if the values are not empty
    if (!empty($caseNumber) && !empty($businessName)) {
        // Check if the case exists
        $checkCaseQuery = "SELECT * FROM cases WHERE case_number = ?";
        $stmtCheckCase = mysqli_prepare($conn_cases, $checkCaseQuery);
        mysqli_stmt_bind_param($stmtCheckCase, "s", $caseNumber);
        mysqli_stmt_execute($stmtCheckCase);
        $resultCheckCase = mysqli_stmt_get_result($stmtCheckCase);

        if ($rowCase = mysqli_fetch_assoc($resultCheckCase)) {
            // Case exists, proceed with adding person
            $insertPersonQuery = "INSERT INTO person (case_number, business_name, first_name, middle_name, last_name, alias, driver_license, race, gender, dob, phone_no, other_no, email, person_type, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtInsertPerson = mysqli_prepare($conn_cases, $insertPersonQuery);
            mysqli_stmt_bind_param($stmtInsertPerson, "sssssssssssssss", $caseNumber, $businessName, $firstName, $middleName, $lastName, $alias, $driverLicense, $race, $gender, $dateOfBirth, $mobilePhone, $otherPhone, $email, $personType, $note);

            // Execute the statement
            if (!mysqli_stmt_execute($stmtInsertPerson)) {
                echo "Error: " . mysqli_error($conn_cases);
            }

            // Redirect to useraddperson.php or another appropriate page
            header('location:useraddperson.php');

            // Close the statement
            mysqli_stmt_close($stmtInsertPerson);
        } else {
            // Case doesn't exist
            echo "Error: Case with number " . $caseNumber . " does not exist.";
        }

        // Close the statement
        mysqli_stmt_close($stmtCheckCase);
    } else {
        echo "Please fill in both Case Number and Business Name.";
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
            $select = "SELECT * FROM `user_form` WHERE name = '" . $_SESSION['user_name'] . "'"
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
            <h5><?php echo $_SESSION['user_name'] ?></h5>
            <ul class="menu">
                <li >
                    <a href="userdashboard.php" >
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="user_profile.php">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="#">
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
                            <a href="useraddcase.php"  class="dropdown-item">Case</a>
                            <a href="useraddperson.php"  class="dropdown-item">Person</a>
                            <a href="useradditem.php"  class="dropdown-item">Item</a>
                        </div>
                </li>
                
                <li>
                    <a href="#">
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
                            <a href="usernorganisation.php"   class="dropdown-item">Organisations</a>
                            <a href="useroffices.php" class="dropdown-item">Offices</a>
                            <a href="userpermissions.php"  class="dropdown-item">Permissions</a>
                            <a href="userusergroups.php"   class="dropdown-item">User Groups</a>
                            <a href="useruseradmin.php" class="dropdown-item">User Admin</a>
                            <a href="usersessions.php"  class="dropdown-item">Sessions</a>
                        
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
                    <h3>Welcome <span><?php echo $_SESSION['user_name'] ?></span></h3>
                    <h2>Add Person/ Add</h2>
                </div>
                <div class="user--info">
                    <div class="search--box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="find a case"/>
                </div>
            </div>
        </div>
        <div class="container3">
    <h1 class="form-name">Person Details</h1>
    
    <form action="" method="post">
        
            <div class="flex2">
                <span>Business Name:</span>
                <input type="text" name="business_name" value="<?php echo $defaultBusinessName; ?>" readonly class="box">
                
                <span>First Name:</span>
                <input type="text" name="first_name" placeholder="Enter first Name"  class="box">
                
                <span>Middle Name:</span>
                <input type="text" name="middle_name" placeholder="Enter middle name" class="box">
                
                <span>Last Name:</span>
                <input type="text" name="last_name" placeholder="Enter last name" class="box">
                
                <span>Alias Name:</span>
                <input type="text" name="alias" placeholder="Enter Alias" class="box">
                
                <span>Drivers License:</span>
                <input type="text" name="driver_license" placeholder="Enter driver lisence" class="box">

                <span>Race Type:</span>
                <input type="text" name="race" placeholder="Enter race" class="box">

                <span>Gender Type:</span>
                <input type="text" name="gender" placeholder="Enter gender" class="box">

                <span>Date of Birth:</span>
                <input type="datetime-local" name="dob" class="box">

                <span>Mobile phone:</span>
                <input type="" name="phone_no" placeholder="Enter phone no" class="box">

                <span>Other phone:</span>
                <input type="" name="other_no" placeholder="Enter other phone" class="box">

                <span>Email holder:</span>
                <input type="email" name="email" placeholder="Enter Email" class="box">

            </div><br><br><br>

            <h1 class="form-name">Add to Case</h1>

            <div class="flex3">

                <span>Add to case:</span>
                <input type="text" name="case_number" value="<?php echo $defaultCaseNumber; ?>" readonly class="box">
                
                <span>Person Type:</span>
                <input  id="personTypeInput" class="dropdown-mbtn" onclick="toggleDropdown('mydropdown')" type="text" name="person_type" required class="box"> 
                <div  id="mydropdown" class="dropdown-mcontent">
                           <div class="dropdown-mitem" onclick="selectDropdownItem(this)">Arrestee</div>
                           <div class="dropdown-mitem" onclick="selectDropdownItem(this)">Assessor</div>
                           <div class="dropdown-mitem" onclick="selectDropdownItem(this)">Custodian</div>
                           <div class="dropdown-mitem" onclick="selectDropdownItem(this)">Deceased</div>
                           <div class="dropdown-mitem" onclick="selectDropdownItem(this)">Found By</div>
                           <div class="dropdown-mitem" onclick="selectDropdownItem(this)">Neighbour</div>
                           <div class="dropdown-mitem" onclick="selectDropdownItem(this)">Reporting party</div>
                           <div class="dropdown-mitem"onclick="selectDropdownItem(this)">Suspect</div>
                           <div class="dropdown-mitem" onclick="selectDropdownItem(this)">Victim</div>
                           <div class="dropdown-mitem" onclick="selectDropdownItem(this)">Witness</div>
                        </div>
                
                <span>Note:</span>
                <textarea name="note" class="box"></textarea>
                
            </div>
        
        <input type="submit" value="save" name="submit_person_details" class="btn">
        & <input type="" name="" placeholder="" class="box">
    </form>
        </div>
        </div>
       
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.46.0/apexcharts.min.js"> </script>
        <script src="dashboard.js"></script>
    </body>
</html>