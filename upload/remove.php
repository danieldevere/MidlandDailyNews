<?php

   /* set_time_limit(600);
	file_put_contents('progress.json', json_encode(array('percentComplete'=>0)));
	$servername = 'localhost';
	$username = 'root';
	$password = '';
	$dbname = 'obits2';

    try{
        $conn = new PDO("mysql:charset=utf8;host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(!empty($_POST['data'])) {
            $files = json_decode($_POST['data']);
        } else {
            throw new Exception("Didn't send data");
        }
        ini_set('auto_detect_line_endings', TRUE);        
        $currentPercent = 0.0;
        $percentPerFile = 100 / count($files);
        for($j=0;$j<count($files);$j++) {
            $currentdir = getcwd();
            $filepath = $currentdir . '/uploads/' . $files[$j]->filename . '.csv';
            $fileLength = count(file($filepath));
            $percentPerRow = $percentPerFile / $fileLength;
            if(($handle = fopen($filepath, "r")) !== FALSE) {
                if($files[$j]->type == 'Obituary') {
                    $stmt = $conn->prepare("DELETE FROM Obituaries WHERE lastname=:lastname AND firstname=:firstname AND birthdate=:birthdate AND deathdate=:deathdate AND obitdate=:obitdate AND page=:page");
                    $stmt->bindParam(':lastname', $lastname);
                    $stmt->bindParam(':firstname', $firstname);
                    $stmt->bindParam(':birthdate', $birthdate);
                    $stmt->bindParam(':deathdate', $deathdate);
                    $stmt->bindParam(':obitdate', $obitdate);
                    $stmt->bindParam(':page', $page);
                    $lastnameReached = false;
                    while (($data = fgetcsv($handle, ',')) !== FALSE) {
                        $currentPercent = $currentPercent + $percentPerRow;
				        $roundedPercent = ceil($currentPercent);
				        if($roundedPercent % 2 == 0) {
					        file_put_contents('progress.json', json_encode(array('percentComplete'=>$roundedPercent)));
				        }
                        if($lastnameReached && $data[0] != "") {
                            $lastname = $data[0];
                            $firstname = $data[1];
                            $birthdate = date("Y-m-d", strtotime($data[2]));
                            $deathdate = date("Y-m-d", strtotime($data[3]));
                            $obitdate = date("Y-m-d", strtotime($data[4]));
                            $page = $data[5];
                            $stmt->execute();
                        }
                        if(strcasecmp($data[0], 'Last Name')) {
                            $lastnameReached = true;
                        }
                    }
                } elseif($files[$j]->type == 'Article') {
                    $stmt = $conn->prepare("DELETE FROM News WHERE subject=:subject AND article=:article AND page=:page AND articledate=:articledate");
                    $stmt->bindParam(':subject', $subject);
                    $stmt->bindParam(':article', $article);
                    $stmt->bindParam(':page', $page);
                    $stmt->bindParam(':articledate', $articledate);
                    $subjectReached = false;
                    while (($data = fgetcsv($handle, ',')) !== FALSE) {
                        $currentPercent = $currentPercent + $percentPerRow;
				        $roundedPercent = ceil($currentPercent);
				        if($roundedPercent % 2 == 0) {
					        file_put_contents('progress.json', json_encode(array('percentComplete'=>$roundedPercent)));
				        }
                        if($subjectReached && $data[0] != "") {
                            $data[0] = str_replace('-', ' - ', $data[0]);	
                            $data[0] = str_replace(':', ' : ', $data[0]);
                            $data[1] = str_replace('...', ' ', $data[1]);
                            $subject = $data[0];
                            $article = $data[1];
                            $page = $data[2];
                            $articledate = date("Y-m-d", strtotime($data[3]));
                            $stmt->execute();
                        }
                        if(strcasecmp($data[0], 'subject') == 0) {
                            $subjectReached = true;
                        }
                    }
                } else {
                    $stmt = $conn->prepare("DELETE FROM Weddings WHERE lastname=:lastname AND firstname=:firstname AND announcement=:announcement AND weddingdate=:weddingdate AND articledate=:articledate AND page=:page");
                    $stmt->bindParam(':lastname', $lastname);
                    $stmt->bindParam(':firstname', $firstname);
                    $stmt->bindParam(':announcement', $announcement);
                    $stmt->bindParam(':weddingdate', $weddingdate);
                    $stmt->bindParam(':articledate', $articledate);
                    $stmt->bindParam(':page', $page);
                    $lastNameReached = false;
                    while (($data = fgetcsv($handle, ',')) !== FALSE) {
                        $currentPercent = $currentPercent + $percentPerRow;
				        $roundedPercent = ceil($currentPercent);
				        if($roundedPercent % 2 == 0) {
					        file_put_contents('progress.json', json_encode(array('percentComplete'=>$roundedPercent)));
				        }
                        if($lastNameReached && $data[0] != "") {
                            $lastname = $data[0];
                            $firstname = $data[1];
                            $announcement = $data[2];
                            $weddingdate = date("Y-m-d", strtotime($data[3]));
                            $articledate = date("Y-m-d", strtotime($data[4]));
                            $page = $data[5];
                            $stmt->execute();
                        }
                        if(strcasecmp($data[0], 'last name') == 0) {
                            $lastNameReached = true;
                        }
                    }
                }
                fclose($handle);
                ini_set('auto_detect_line_endings', FALSE);
                $filestmt = $conn->prepare("DELETE FROM Files WHERE filename = :filename");
                $filestmt->bindParam(':filename', $filename);
                $filename = $files[$j]->filename;
                $filestmt->execute();
            } else {
                throw new Exception("There was an error opening the file");
            }
        }
    }
    catch(PDOException $e) {
        echo 'mySQL error: ' . $e->getMessage();
    }
    catch(Exception $e) {
        echo $e->getMessage();
    }*/

    /*
    }
    $mysqli->close();
    session_destroy();
    }


    session_start();
    $_SESSION['progress'] = 0.0;
    session_write_close();


    $mysqli = mysqli_connect("localhost", "root", "", "obits2");


    if($mysqli->connect_errno) {
     //   echo "Connect failed" . $mysqli->connect_error;
     //   echo "<script type='text/javascript'>alert('there was a problem connecting');</script>";
    }
    if(isset($_POST['data'])) {
        $files = json_decode($_POST['data']);
    }

    if(isset($files) && count($files) > 0) {
        
        ini_set('auto_detect_line_endings', TRUE);        
        $currentPercent = 0.0;
        $percentPerFile = 100 / count($files);
        for($j=0;$j<count($files);$j++) {
            $currentdir = getcwd();
            $target = $currentdir . '/uploads/' . $files[$j]->filename . '.csv';
            /*$file = new SplFileObject($target, 'r');
            $file->seek(PHP_INT_MAX);
            $fileLength = $file->key() + 1;
        //    echo $fileLength;
            $fileLength = count(file($target));
            $percentPerRow = $percentPerFile / $fileLength;
            if(($handle = fopen($target, "r")) !== FALSE) {
            //    echo 'before prepare';
                if($files[$j]->type == 'Obituary') {
                    $stmt = $mysqli->prepare("DELETE FROM Obituaries WHERE lastname=? AND firstname=? AND birthdate=? AND deathdate=? AND obitdate=? AND page=?");
                    $stmt->bind_param("ssssss", $lastname, $firstname, $birthdate, $deathdate, $obitdate, $page);
                    $lastnameReached = false;
                    while (($data = fgetcsv($handle, ',')) !== FALSE) {
                        $currentPercent += $percentPerRow;
                        if(intval($currentPercent) % 5 == 0) {
                            session_start();
                            $_SESSION["progress"] = $currentPercent;
                            session_write_close();
                        }
                //      send_message($files[$j]->filename, 'working', $currentPercent);
                        if($lastnameReached && $data[0] != "") {
                            for($c = 0; $c < 6; $c++) {
                                $data[$c] = str_replace('"', '', $data[$c]);
                                $data[$c] = str_replace("'", "", $data[$c]);
                                if($c < 2) {
                                    $data[$c] = ucwords(strtolower($data[$c]));
                                }
                            }
                            $lastname = $data[0];
                            $firstname = $data[1];
                            $birthdate = date("Y-m-d", strtotime($data[2]));
                    //		$deathyear = $data[4];
                            $deathdate = date("Y-m-d", strtotime($data[3]));
                        //	$obityear = $data[6];
                            $obitdate = date("Y-m-d", strtotime($data[4]));
                            $page = $data[5];
                            $stmt->execute();
                    //      echo $lastname . ' ';
                        }
                        if(strcasecmp($data[0], 'Last Name')) {
                            $lastnameReached = true;
                        }
                    }
                } elseif($files[$j]->type == 'Article') {
                    $stmt = $mysqli->prepare("DELETE FROM News WHERE subject=? AND article=? AND page=? AND articledate=?");
                    $stmt->bind_param("ssss", $subject, $article, $page, $articledate);
                    $subjectReached = false;
                    while (($data = fgetcsv($handle, ',')) !== FALSE) {
                        $currentPercent += $percentPerRow;
                        if(intval($currentPercent) % 5 == 0) {
                            session_start();
                            $_SESSION["progress"] = $currentPercent;
                            session_write_close();
                        }
                    //    send_message($files[$j]->filename, 'working', $currentPercent);
                        if($subjectReached && $data[0] != "") {
                            for($c=0; $c < 4; $c++) {
                                $data[$c] = str_replace('"', '', $data[$c]);
                                $data[$c] = str_replace("'", "", $data[$c]);
                                if($c < 1) {
                                    $data[$c] = str_replace('-', ' - ', $data[$c]);
                                }
                            }	
                            $subject = $data[0];
                            $article = $data[1];
                            $page = $data[2];
                            $articledate = date("Y-m-d", strtotime($data[3]));
                        //	$year = $data[4];
                            $stmt->execute();
                        }
                        if(strcasecmp($data[0], 'subject') == 0) {
                            $subjectReached = true;
                        }
                    }
                } else {
                    $stmt = $mysqli->prepare("DELETE FROM Weddings WHERE lastname=? AND firstname=? AND announcement=? AND weddingdate=? AND articledate=? AND page=?");
                    $stmt->bind_param("ssssss", $lastname, $firstname, $announcement, $weddingdate, $articledate, $page);
                    $lastNameReached = false;
                    while (($data = fgetcsv($handle, ',')) !== FALSE) {
                        $currentPercent += $percentPerRow;
                        if(intval($currentPercent) % 5 == 0) {
                            session_start();
                            $_SESSION["progress"] = $currentPercent;
                            session_write_close();
                        }
                //      send_message($files[$j]->filename, 'working', $currentPercent);
                        if($lastNameReached && $data[0] != "") {
                            for($c=0; $c < 6; $c++) {
                                $data[$c] = str_replace('"', '', $data[$c]);
                                $data[$c] = str_replace("'", "", $data[$c]);
                            }	
                            $lastname = $data[0];
                            $firstname = $data[1];
                            $announcement = $data[2];
                            $weddingdate = date("Y-m-d", strtotime($data[3]));
                            $articledate = date("Y-m-d", strtotime($data[4]));
                            $page = $data[5];
                        //	$year = $data[4];
                            $stmt->execute();
                        }
                        if(strcasecmp($data[0], 'last name') == 0) {
                            $lastNameReached = true;
                        }
                    }
                }
                fclose($handle);
                ini_set('auto_detect_line_endings', FALSE);
            } else {
            //   echo "There was an error opening the file.";
            }
            $filestmt = $mysqli->prepare("DELETE FROM Files WHERE filename = ?");
            $filestmt->bind_param("s", $name);
            $name = $files[$j]->filename;
            $filestmt->execute();
    }
  //  send_message('CLOSE', 'finished');
    }
    $mysqli->close();
    session_destroy();
*/
?>