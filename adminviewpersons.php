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
.table-body {
        background-color: #f5f5f5; /* Off-white background color */
        color: #191C40; /* Blue text color */
        padding: 15px; /* Add padding for better visibility */
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
        background-color: #404472;
        color: #fff;
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
                <li>
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
                    <h2>Case View</h2>
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
                <div class="cases--menu" id="cases-menu">
                    <ul class="cases--list">
                        <li  >
                            <a href="adminviewcases.php" class="cases-link">Basic info</a>
                        </li>
                        <li class="casesactive">
                            <a href="#" class="cases-link">People</a>
                        </li>
                        <li >
                            <a href="adminviewitem.php" class="cases-link">Item</a>
                        </li>
                        <li >
                            <a href="adminmedia.php" class="cases-link">Media</a>
                        </li>
                        <li >
                            <a href="adminnotes.php" class="cases-link">Notes</a>
                        </li>
                        <li >
                            <a href="admintasks.php" class="cases-link">Tasks</a>
                        </li>
                        <li >
                            <a href="admincaseshistory.php" class="cases-link">History</a>
                        </li>
                        <li >
                            <a href="admincasespermission.php" class="cases-link">Permissions</a>
                        </li>
                    </ul>
                </div>
        </div>
        <div class="table-body">
            <table class="table-bordered">
                <thead>
                <tr class="table-headers">
                <td><input type="checkbox" name="selectedCases[]" value=""></td>
                <td>View person</td>
                    <td>Primary Case#</td>
                    <td>business Name</td>
                    <td>First Name</td>
                    <td>Last Name</td>
                    <td>Person Type</td>
                    <td>Note</td>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                    
                <?php
          $query = "SELECT case_number, business_name, first_name, last_name, person_type, note FROM person WHERE case_number = '$defaultCaseNumber'";

          $result = mysqli_query($conn_cases, $query);
               if($result){
               while($row = mysqli_fetch_assoc($result)){
               $caseNumber = $row['case_number'];
               $businessname= $row['business_name'];
               $firstname =$row['first_name'];
               $lastname = $row['last_name'];
               $persontype = $row['person_type'];
               $note = $row['note'];
              ?>
                <tr>
                <td><input type="checkbox" name="selectedCases[]" value=""></td>
                <td><input type="submit" value="View" name="view_item" class="btn"></td>
                    <td><?php echo $caseNumber ; ?></td>
                    <td><?php echo  $businessname; ?></td>
                    <td><?php echo  $firstname; ?></td>
                    <td><?php echo $lastname; ?></td>
                    <td><?php echo $persontype; ?></td>
                    <td><?php echo  $note ; ?></td>
                    <td></td>
                </tr>
                <?php
               }
            }
               ?>
                
        </tbody>

               
            </table>
        </div>
    
        </div>   
        
         </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.46.0/apexcharts.min.js"></script>
        <script src="dashboard.js"></script>
        
    </body>
</html>