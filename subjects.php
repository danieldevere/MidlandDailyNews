           
<?php
/*
This file just returns the list of subjects that exist in the database.
*/
$config = include('upload/config.php');
$servername = $config['servername'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['dbname'];
$mysqli = new mysqli($servername, $username, $password, $dbname);
    if($mysqli->connect_errno) {
        echo "Connect failed" . $mysqli->connect_error;
    }
    $searchstring = 'SELECT DISTINCT subject FROM News';
    $results = $mysqli->query($searchstring);
    $subjects = [];
    while($row = $results->fetch_assoc()) {
        array_push($subjects, $row);
    }
    echo json_encode($subjects);    
?>