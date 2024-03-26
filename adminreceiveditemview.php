<?php

@include 'config.php';

session_start();


if(!isset($_SESSION['admin_name'])){
    echo "Redirecting to loginform.php";
    header('location:login_form.php');
    exit();
}

$evidenceid = isset($_GET['evidence_id']) ? $_GET['evidence_id'] : null;
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the note content and evidence ID from the form
    $noteContent = $_POST['note_content'];
    $evidenceId = $_POST['evidence_id']; // Assuming evidence ID is passed through the form
    $receiverId = $_POST['id'];
    // Sanitize the input data to prevent SQL injection
    $noteContent = mysqli_real_escape_string($conn_cases, $noteContent);
    $evidenceId = mysqli_real_escape_string($conn_cases, $evidenceId);
    $receiverId = mysqli_real_escape_string($conn_cases, $receiverId);

    // Insert the note into the database
    $query = "INSERT INTO evidence_notes (evidence_id, receiver_id, note_content) VALUES ('$evidenceId', '$receiverId', '$noteContent')";
    $result = mysqli_query($conn_cases, $query);

    if ($result) {
        // Note insertion successful
        echo "Note added successfully!";
    } else {
        // Note insertion failed
        echo "Error adding note: " . mysqli_error($conn_cases);
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
}
.note-section {
    margin-top: 20px; /* Adjust spacing as needed */
    padding: 20px;
    background-color: #f5f5f5; /* Light background color */
    border: 1px solid #ccc; /* Border */
    border-radius: 5px; /* Rounded corners */
}

.note-section h4 {
    margin-top: 0;
    margin-bottom: 10px;
    font-size: 18px;
    color: #333; /* Dark text color */
}

.note-section textarea {
    width: 100%;
    height: 100px; /* Adjust height as needed */
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc; /* Border */
    border-radius: 5px; /* Rounded corners */
    resize: vertical; /* Allow vertical resizing */
    font-size: 14px;
}

.note-section textarea:focus {
    outline: none; /* Remove outline when focused */
    border-color: #007bff; /* Change border color on focus */
}

.note-section button {
    padding: 10px 20px;
    font-size: 14px;
    color: #fff;
    background-color: var(--blue); /* Button background color */
    border: none;
    border-radius: 5px; /* Rounded corners */
    cursor: pointer;
    transition: background-color 0.3s; /* Smooth transition on hover */
}

.note-section button:hover {
    background-color: #0056b3; /* Darker background color on hover */
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
                <li >
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
                    <h2>Item View</h2>
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
                <h2 class="casenumber">Item  <span><?php echo  $evidenceid; ?></span></h2>
                <div class="cases--menu" id="cases-menu">
                    <ul class="cases--list">
                        <li class="casesactive" >
                            <a href="#" class="cases-link">Basic info</a>
                        </li>
                        
                        <li class="cases--item">
                            <a href="adminnotes.php" class="cases-link">Notes</a>
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
                <td>Status</td>
                <td>Primary Case#</td>
                <td>Evidence id</td>
                <td>Category</td>
                <td>Recovered by</td>
                <td>Recovered at</td>
                <td>Recovery time</td>
                <td>Description</td>
              
               
            </tr>
        </thead>
        <tbody>
        <?php
       $evidenceId = isset($_GET['evidence_id']) ? $_GET['evidence_id'] : 0;


         $query = "SELECT `status`, `case_number`, `evidence_id`,`category`, `recovered_by`, `recovered_at`, `recovering_time`, `description` FROM `evidence` WHERE `evidence_id` = $evidenceId";
       
             
           $result = mysqli_query($conn_cases, $query);
           if($result){

            $status = '';
            $caseNumber = '';
            $evidenceId='';
            $category = '';
            $recoveredby = '';
            $recoveredat = '';
            $recoveringtime = '';
            $description= '';

            $row = mysqli_fetch_assoc($result);
            if($row){
               $status = $row['status'];
               $caseNumber = $row['case_number'];
               $evidenceId= $row['evidence_id'];
               $category = $row['category'];
               $recoveredby =$row['recovered_by'];
               $recoveredat = $row['recovered_at'];
               $recoveringtime = $row['recovering_time'];
               $description = $row['description'];
               
            }else {
                echo "Item not found";
            }
        
              ?>
                <tr>
                    <td><?php echo $status;?></td>
                    <td><?php echo $caseNumber ; ?></td>
                    <td><?php echo  $evidenceId; ?></td>
                    <td><?php echo $category; ?></td>
                    <td><?php echo $recoveredby; ?></td>
                    <td><?php echo $recoveredat; ?></td>
                    <td><?php echo $recoveringtime ; ?></td>
                    <td><?php echo $description ; ?></td>
                   
                </tr>
                <?php
           }
            
               ?>
                
        </tbody>
    </table>
    <form action="" method="post" id="notesForm">
    <div class="note-section">
    <h4>Add Note</h4>
    <textarea id="note_content" placeholder="Enter your note here"></textarea>
    <input type="hidden" name="evidence_id" value="<?php echo $evidenceid; ?>">
    <input type="hidden" name="receiver_id" value="<?php echo $_SESSION['admin_name']; ?>"> <!-- Assuming the admin ID is used as the receiver ID -->    
    <button type="submit" id="addNoteBtn">Add Note</button>
</div>
    </form>
</div>

        </div>
         </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.46.0/apexcharts.min.js"></script>
        <script src="dashboard.js"></script>
        <script> 
        document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('addNoteBtn').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission
        document.getElementById('notesForm').submit(); // Submit the form
    });
});

    </script>
    </body>
</html>