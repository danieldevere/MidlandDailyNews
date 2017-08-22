<?php
// Check if inputs are set properly
function checkInputs() {
    if(empty($_GET['searchtype'])) {
        throw new Exception("Search type wasn't set.");
    }
    if(!empty($_GET['lastname'])) {
        return true;
    }
    throw new Exception("Last name is not set.");
}
// Sanitize user input
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
 //   $input = htmlspecialchars($input);
    return $input;
}
    

    try {
        checkInputs();
        // Get user input
        if(isset($_GET['searchtype'])) {
            $searchType = sanitizeInput($_GET['searchtype']);
        }
        if(isset($_GET['lastname'])) {
            $lastname = sanitizeInput($_GET['lastname']);
        }
        if(isset($_GET['firstname'])) {
            $firstname = sanitizeInput($_GET['firstname']);
        }
        // SQL Login info
        $config = include('upload/config.php');
        $servername = $config['servername'];
        $username = $config['username'];
        $password = $config['password'];
        $dbname = $config['dbname'];

        $conn = new PDO("mysql:charset=utf8;host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // set up prepared search string
        if($searchType == 'obituaries') {
            $searchString = "SELECT * FROM Obituaries WHERE";
        } elseif($searchType == 'weddings') {
            $searchString = "SELECT * FROM Weddings WHERE";
        }
        if(isset($lastname) && $lastname != "") {
            $searchString .= " lastname = :lastname";
            if(isset($firstname) && $firstname != "") {
                $searchString .= " AND (firstname LIKE :firstname0 OR firstname LIKE :firstname1)";
            }
            $searchString .= " ORDER BY lastname, firstname ASC";
        } else {
            echo "Last Name field must not be empty";
        }
        
        $stmt = $conn->prepare($searchString);
        // Bind search parameters
        if(isset($lastname) && $lastname != "") {
            $stmt->bindParam(':lastname', $lastname);
        }
        if(isset($firstname) && $firstname != "") {
            $firstname0 = $firstname . '%';
            $firstname1 = '% ' . $firstname . '%';
            $stmt->bindParam(':firstname0', $firstname0);
            $stmt->bindParam(':firstname1', $firstname1);
        }
        $result = $stmt->execute();
        // Create result array
        if(isset($result)) {
            $resultArray = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if($searchType == 'obituaries') {
                    if($row['birthdate'] != "") {
                        $row['birthdate'] = date("m/d/Y", strtotime($row['birthdate']));
                    }
                    if($row['deathdate'] != "") {
                        $row['deathdate'] = date("m/d/Y", strtotime($row['deathdate']));
                    }
                    $row['obitdate'] = date("m/d/Y", strtotime($row['obitdate']));
                } else {
                    if($row['articledate'] != "") {
                        $row['articledate'] = date("m/d/Y", strtotime($row['articledate']));
                    }
                    if($row['weddingdate'] != "") {
                        $row['weddingdate'] = date("m/d/Y", strtotime($row['weddingdate']));
                    }
                }
                
                array_push($resultArray, $row);
            }
            echo json_encode($resultArray);
        }
        $conn = null;
    } catch(PDOException $e) {
        echo 'There was a problem connecting to the database.';
    }
    catch(Exception $e) {
        echo $e->getMessage();
    }

?>