<?php
   // header("Cache-Control: no-cache, must-revalidate");
   $config = include('config.php');
    $servername = $config['servername'];
    $username = $config['username'];
    $password = $config['password'];
    $dbname = $config['dbname'];
    $mysqli = mysqli_connect($servername, $username, $password, $dbname);
    if($mysqli->connect_errno) {
        echo "Connect failed" . $mysqli->connect_error;
    }
    $mysqli->set_charset('utf8');
    $searchString = 'SELECT * FROM Files ORDER BY uploaddate DESC';
    $results = $mysqli->query($searchString);
    $articles = $results->fetch_all(MYSQLI_ASSOC);

    echo json_encode($articles);
    $mysqli->close();
?>
