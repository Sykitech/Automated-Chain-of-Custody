// get_cases.php

<?php
@include 'config.php';

if (isset($_POST['get_cases'])) {
    $query = "SELECT case_number FROM cases";
    $result = mysqli_query($conn_cases, $query);

    $cases = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $cases[] = $row['case_number'];
    }

    echo json_encode($cases);
    exit();
}
?>
