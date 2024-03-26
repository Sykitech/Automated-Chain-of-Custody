<?php

@include 'config.php';
session_start();

if (!isset($_SESSION['admin_name'])) {
    echo "Redirecting to loginform.php";
    header('location:login_form.php');
    exit();
}

// Check if the add request form is submitted
if (isset($_POST['add_request'])) {
    // Get the search query
    $search_query = mysqli_real_escape_string($conn, $_POST['search_user']);

    // Query to search for users
    $search_query = "SELECT name FROM `user_form` WHERE `name` LIKE '%$search_query%'";
    $search_result = mysqli_query($conn, $search_query);

    if ($search_result) {
        // Display search results
        while ($user = mysqli_fetch_assoc($search_result)) {
            echo '<p>' . $user['name'] . '</p>';
            // Add the logic to send outbound request with user data
            // ...
        }
    } else {
        echo "Error in search query: " . mysqli_error($conn);
    }
}
?>
