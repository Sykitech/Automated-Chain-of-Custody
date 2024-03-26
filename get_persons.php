<?php
@include 'config.php';

if (isset($_POST['get_persons'])) {
    $query = "SELECT business_name FROM person"; // Adjust column name if needed
    $result = mysqli_query($conn_cases, $query);

    $persons = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $persons[] = $row['business_name'];
    }

    echo json_encode($persons);
    exit();
}
?>
