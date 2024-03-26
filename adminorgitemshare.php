<?php

@include 'config.php';

session_start();


if(!isset($_SESSION['admin_name'])){
    echo "Redirecting to loginform.php";
    header('location:login_form.php');
    exit();
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
         /* Style for the subheader */
.subheader {
    display: grid;
    grid-template-columns:1fr 1fr ;
    gap: 20px;
    padding: 10px;
    border-bottom: .5px solid #191C40;
   
}

/* Style for the left and right sub containers */
.activeheader{
    background-color:  #d2d2d2 ; /* Whitish background color */
    padding: 5px 10px; /* Adjust padding as needed */
    border-radius: 5px; /* Add border-radius for rounded corners */

}
.rightsub {
    background-color: #191C40 ; /* White background color */
    padding: 5px 10px; /* Adjust padding as needed */
    border-radius: 5px; /* Add border-radius for rounded corners */
    
}

/* Text color in blue */
.subtittle {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 600;
}
.sub-headers {
    display: flex;
    gap: 20px;
    padding: 10px;
   
}

/* Style for the left and right sub containers */
.sublinks{
    background-color:  #fff; /* White background color */
    padding: 5px 10px; /* Adjust padding as needed */
    border-radius: 5px; /* Add border-radius for rounded corners */

}


/* Text color in blue */
.linktitle {
    color: #191C40;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 500;
}
.sublinks:hover {
    background-color: #404472; /* Change text color on hover */

}   
.subactive{
    color: #fff;
    background-color: #191C40;
    padding: 5px 10px; /* Adjust padding as needed */
    border-radius: 5px;
}
.activetitle{
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 500
}
.itemsharing {
    background-color: #fff; /* White background color */
    padding: 10px; /* Adjust padding as needed */
    border-radius: 5px;
    
}

.sharingtitle h2 {
    color: #191C40; /* Blue text color */
    font-size: 20px; /* Adjust font size as needed */
    font-weight: 600; /* Adjust font weight as needed */
    margin: 0;
   
}
#toggleButton {
    background-color: #d2d2d2; 
    color: white; /* White text color */
    border: none; /* Remove border */
    padding: 10px 20px; /* Add padding */
    text-align: center; /* Center text */
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 10px; /* Add border-radius for rounded corners */
    margin-left: 150vh;
}

  /* Style for the button when it's in the "On" state */
  
  #powerIcon {
    margin-right: 5px; /* Add margin to separate the icon from the text (if any) */
}
#editButton {
    background-color: #191C40;
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    font-size: 16px;
    cursor: pointer;
    border-radius: 10px;
    margin-top: 10px; /* Add space between the buttons */
    margin-right: 150vh;
}
.edit-section {
    display: none; /* Hide the edit section by default */
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-top: 10px;
}
.addButton {
    background-color: #191C40;
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    font-size: 16px;
    cursor: pointer;
    border-radius: 10px;
    margin-top: 10px; /* Add space between the buttons */
    margin-right: 150vh;
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
                <li>
                    <a href="adimreceivedevidence.php">
                        <i class="fas fa-tasks"></i>
                        <span>Tasks</span>
                    </a>
                </li>
                <li class="active">
                    <a href="#">
                        <i class="fas fa-cogs"></i>
                        <span class="dropdown-btn" onclick="toggleDropdown('settingsDropdown')">Settings</span>
                    </a>
                    <div  id="settingsDropdown" class="dropdown-content">
                            <a href="#"   class="dropdown-item">Organisations</a>
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
                    <h2>Organisation settings</h2>
                </div>
                <div class="user--info">
                    <div class="search--box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="find a case"/>
                </div>
            </div>
        </div>
        <div class="subheader">
            
            <div class="activeheader">
            <a href="#"  class="subtittle"> General</a>
            </div>
        
            <div class="rightsub">
            <a href="formsetting.php"  class="subtittle"> Field and form setting</a>
                

            </div>

        </div>
        <div class="sub-headers">
            <div class="sublinks">
                <a href="#" class="linktitle">Org Settings</a>
            </div>
            <div class="subactive">
                <a href="#" class="activetitle">Org Item sharing</a>
            </div>
            <div class="sublinks">
                <a href="#" class="linktitle">Translation</a>
            </div>
            <div class="sublinks">
                <a href="#" class="linktitle">Auto Disposal</a>
            </div>

        </div>
        <div class="itemsharing">
            <div class="sharingtitle"><h2>Guardian Trail item sharing</h2>
            </div><br><br>
            <h2 style="color: #191C40;">Item Sharing</h2>
            <button id="toggleButton" onclick="toggleItemSharing()"><i id="powerIcon" class="fas fa-power-off"></i>
        </button>
        <button id="editButton" onclick="toggleEditSection()">Edit</button>
        <div id="editSection" class="edit-section">
        <h2 style="color: #191C40;">Select the org/person to share item with</h2><br>
        <div class="search-container">    
        <form action="#" method="post">
    <input type="text" name="search_user" placeholder="Search for a person">
    <button type="submit" name="add_request" class="addButton">Add</button>
     </form>
<!-- Additional elements for selecting a person can be added here -->
        </div>
        <div class="request-section" id="requestsection">
            <div class="request-header" id="requestheader">Outbound Request</div>

        </div>
       </div>
        </div>
        </div>
        <script>
          let isItemSharingOn = false;

// Function to toggle item sharing
function toggleItemSharing() {
    // Toggle the state
    isItemSharingOn = !isItemSharingOn;

    // Update the button and icon classes
    const button = document.getElementById('toggleButton');
    const powerIcon = document.getElementById('powerIcon');

    if (isItemSharingOn) {
        // If it's on, change the color of the power icon to green
        powerIcon.style.color = 'green';
    } else {
        // If it's off, change the color of the power icon to red
        powerIcon.style.color = 'red';
    }

    // Your logic to perform actions based on the state goes here
    if (isItemSharingOn) {
        // If it's on, perform actions for turning it on
        alert('Item Sharing is turned ON');
    } else {
        // If it's off, perform actions for turning it off
        alert('Item Sharing is turned OFF');
    }
}
function toggleEditSection() {
    var editSection = document.getElementById('editSection');
    editSection.style.display = (editSection.style.display === 'block') ? 'none' : 'block';
}

        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.46.0/apexcharts.min.js"></script>
        <script src="dashboard.js"></script>
        
    </body>
</html>