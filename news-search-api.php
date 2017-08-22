 <?php
 // Error checking functions
 function checkInputs() {
     if(isset($_GET['subjects'])) {
         $subjects = json_decode($_GET['subjects'], true);
         if(count($subjects) > 0) {
             return true;
         }
     } 
     if(isset($_GET['headline']) && $_GET['headline'] != '') {
         return true;
     }
     if(isset($_GET['fromDate']) && $_GET['fromDate'] != '') {
         return true;
     }
     if(isset($_GET['toDate']) && $_GET['toDate'] != '') {
         return true;
     }
     throw new Exception("No search terms entered.");
 }
 // Sanitize user input
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
 //   $input = htmlspecialchars($input);
    return $input;
}

try {
    // Check if inputs are empty
    checkInputs();
    // Initialize the SQL string and begin it
    $searchString = "SELECT * FROM News WHERE ";
    // Check for subjects and add to a separate subject section of prepared statement
    if(isset($_GET['subjects'])) {
        $subjects = json_decode($_GET['subjects'], true);
        $numSubjects = count($subjects);
        if($numSubjects > 0) {
            $subjectString = "";    
            $subjectString .= "(";
            for($i = 0; $i < $numSubjects; $i++) {
                $subjects[$i] = sanitizeInput($subjects[$i]);
                if($i > 0) {
                    $subjectString .= " OR ";
                }
                $subjectString .= "subject = :subject" . $i;
            }
            $subjectString .= ")";
        }       
    }
    // Check for headline and add to separate headline section of prepared statement
    if(isset($_GET['headline'])) {
        $headline = sanitizeInput($_GET['headline']);
        if($headline != "") {
            $headlineString = "";
            if(isset($subjectString)) {
                $headlineString .= " AND (article LIKE :article0";
            } else {
                $headlineString .= "(article LIKE :article0";
            }
            $headline = explode(' ', $headline);
            for($i=1; $i<count($headline); $i++) {
                $headlineString .= ' AND article LIKE :article' . $i;
            }
            $headlineString .= ')';
        }
    }
    // Check for from and to dates and add to separate from and to date strings
    if(isset($_GET['fromDate'])) {
        $tempFromDate = sanitizeInput($_GET['fromDate']);
        if($tempFromDate != "") {
            $fromDate = $tempFromDate;
        }
    }
    if(isset($_GET['toDate'])) {
        $tempToDate = sanitizeInput($_GET['toDate']);
        if($tempToDate != "") {
            $toDate = $tempToDate;    
        }
    }
    // Switch for formatting the date search
    if(isset($fromDate) || isset($toDate)) {
        $dateString = "";
        if(isset($subjectString) || isset($headlineString)) {
            $dateString .= " AND ";
        }
        if(isset($fromDate) && isset($toDate)) {
            $dateString .= "articledate BETWEEN :fromDate AND :toDate";
        } elseif(isset($fromDate)) {
            $dateString .= "articledate > :fromDate";
        } else {
            $dateString .= "articledate < :toDate";
        }
    }
    // Database credentials
    $config = include('upload/config.php');
    $servername = $config['servername'];
    $username = $config['username'];
    $password = $config['password'];
    $dbname = $config['dbname'];
    $conn = new PDO("mysql:charset=utf8;host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Construct the prepared search string
    if(isset($subjectString)) {
        $searchString .= $subjectString;
    }
    if(isset($headlineString)) {
        $searchString .= $headlineString;
    }
    if(isset($dateString)) {
        $searchString .= $dateString;
    }
    $searchString .= ' ORDER BY subject, articledate ASC';
    $stmt = $conn->prepare($searchString);
    // Bind subjects
    if(isset($subjectString)) {
        for($j = 0; $j < count($subjects); $j++) {
            $stmt->bindParam(':subject' . $j, $subjects[$j]);
        }
    }
    // Bind headline
    if(isset($headlineString)) {
        for($i=0; $i<count($headline); $i++) {
            $headline[$i] = "%" . $headline[$i] . "%";
            $stmt->bindParam(':article' . $i, $headline[$i]);
        }
    }
    // Bind dates
    if(isset($dateString)) {
        if(isset($fromDate)) {
            $stmt->bindParam(':fromDate', $fromDate);
        }
        if(isset($toDate)) {
            $stmt->bindParam(':toDate', $toDate);
        }
    } 
    // Execute statement
    $result = $stmt->execute();
    if(isset($result)) {
        $resultArray = [];
        // Format date and create result array
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['articledate'] = date("m/d/Y", strtotime($row['articledate']));
            array_push($resultArray, $row);
        }
        echo json_encode($resultArray);
    } else {
        echo "No results";
    }
    $conn = null;     
} 
// Catch errors
catch(PDOException $e) {
    echo 'There was a problem connecting to the database.';
}
catch(Exception $e) {
    echo $e->getMessage();
}
?>