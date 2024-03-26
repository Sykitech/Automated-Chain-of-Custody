<?php

@include 'config.php';

session_start();

if(!isset($_SESSION['admin_name'])){
    echo "Redirecting to loginform.php";
    header('location:login_form.php');
    exit();
}
// Fetch case details from session if available
if (isset($_SESSION['case_details'])) {
    $defaultCaseNumber = $_SESSION['case_details']['case_number'];
    $defaultOffenceType = $_SESSION['case_details']['offence_type'];
} else {
    // Default values if no case details in session
    $defaultCaseNumber = '';
    $defaultOffenceType = '';
}

if (isset($_POST['submit_case_details'])) {
    // Get the submitted values
    $caseNumber = $_POST['case_number'];
    $offenceType = $_POST['offence_type'];
    $caseOfficers = $_POST['case_officers'];
    $offenceLocation = $_POST['offence_location'];
    $offenceTime = $_POST['offence_time'];
    $offenceDescription = $_POST['offence_description'];

    // Check if the values are not empty
    if (!empty($caseNumber) && !empty($offenceType)) {
        // Prepare the SQL statement with placeholders
        $insertQuery = "INSERT INTO cases (case_number, offence_type, case_officers, offence_location, offence_time, offence_description) VALUES (?, ?, ?, ?, ?, ?)";

        // Create a prepared statement
        $stmt = mysqli_prepare($conn_cases, $insertQuery);

        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ssssss", $caseNumber, $offenceType, $caseOfficers, $offenceLocation, $offenceTime, $offenceDescription);

        // Execute the statement
        if (!mysqli_stmt_execute($stmt)) {
            echo "Error: " . mysqli_error($conn_cases);
        }
        // Redirect to user_case.php
        header('location:adminaddcase.php');

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Please fill in both Case Number and Offence Type.";
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
                    <h2>Add case/ Add</h2>
                </div>
                <div class="user--info">
                    <div class="search--box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="find a case"/>
                </div>
            </div>
        </div>
        <div class="container3">
    <h1 class="form-name">Case Details</h1>
    
    <form action="" method="post">
        
            <div class="flex2">
                <span>Case Number :</span>
                <input type="text" name="case_number" value="<?php echo $defaultCaseNumber; ?>" readonly class="box">
                
                <span>Offence type:</span>
                <input type="text" name="offence_type" value="<?php echo $defaultOffenceType; ?>" readonly class="box">
                
                <span>Case Officers:</span>
                <input type="text" name="case_officers" placeholder="Enter case officers" class="box">
                
                <span>Location:</span>
                <input type="text" name="offence_location" placeholder="Enter offence location" class="box">
                
                <span>Offence time:</span>
                <input type="datetime-local" name="offence_time" class="box">
                
                <span>Description:</span>
                <textarea name="offence_description" placeholder="Enter offence description" class="box"></textarea>
            </div>
        
        <input type="submit" value="save" name="submit_case_details" class="btn">
        & <input type="text" name="" placeholder="" class="box">
    </form>
        </div>
        </div>
       
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.46.0/apexcharts.min.js"> </script>
        <script src="dashboard.js"></script>
    </body>
</html>