<?php
//connection to user_db
$conn = mysqli_connect('localhost','root','','user_db');

//connection to cases_db
$conn_cases = mysqli_connect('localhost','root','','cases_db');

if (!$conn_cases) {
    die('Connection Error: ' . mysqli_connect_error());
}


?>