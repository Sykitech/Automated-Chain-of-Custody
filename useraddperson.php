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

// Check if the form is submitted
if (isset($_POST['add_person'])) {
    // Get the submitted values
    $caseNumber = $_POST['case_number'];
    $businessName = $_POST['business_name'];

    // Check if the values are not empty
    if (!empty($caseNumber) && !empty($businessName)) {
        // Check if the case exists
        $checkCaseQuery = "SELECT * FROM cases WHERE case_number = ?";
        $stmtCheckCase = mysqli_prepare($conn_cases, $checkCaseQuery);
        mysqli_stmt_bind_param($stmtCheckCase, "s", $caseNumber);
        mysqli_stmt_execute($stmtCheckCase);
        $resultCheckCase = mysqli_stmt_get_result($stmtCheckCase);

        if ($rowCase = mysqli_fetch_assoc($resultCheckCase)) {
            // Case exists, store case details in session
            $_SESSION['person_details'] = [
                'case_number' => $caseNumber,
                'business_name' => $businessName,
            ];

            // Redirect to user_person.php
            header('location:user_person.php');
            exit();
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
        <title>Automated chain of custody add case</title>
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
                <li >
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
                            <a href="useraddcase.php"   class="dropdown-item">Case</a>
                            <a href="useraddperson.php" class="dropdown-item">Person</a>
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
                    <h2>Add person</h2>
                </div>
                <div class="user--info">
                    <div class="search--box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="find a case"/>
                </div>
            </div>
        </div>   
        <div class="add-case">
        <h2 class="form-title">Basic Info</h2>
<form action="" method="post" >
   <div class="flex">
      <div class="inputBox">
         <span>Case Number :</span><input type="" name="case_number" id= "case_number" placeholder="case_number" class="box">
         <span>Business Name:</span><input type="text" name="business_name"placeholder="--Enter Name"  class="box">
      </div>
   </div>
   <input type="submit" value="Next" name="add_person" class="btn">
</form>

</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="dashboard.js"></script>
</body>
</html>