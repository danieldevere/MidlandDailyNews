<?php
	set_time_limit(150);
	file_put_contents('progress.json', json_encode(array('percentComplete'=>0)));
	$config = include('config.php');
	$servername = $config['servername'];
	$username = $config['username'];
	$password = $config['password'];
	$dbname = $config['dbname'];
	try{
		$conn = new PDO("mysql:charset=utf8;host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmtFileTable = $conn->prepare("CREATE TABLE IF NOT EXISTS Files (
			filename VARCHAR(100) NOT NULL,
			id INT NOT NULL AUTO_INCREMENT,
			type VARCHAR(50) NOT NULL,
			uploaddate DATETIME DEFAULT CURRENT_TIMESTAMP,
			year int NOT NULL,
			CONSTRAINT uc_file UNIQUE (filename, type),
			PRIMARY KEY (id)
		)");
		$stmtFileTable->execute();
		$stmtWeddingTable = $conn->prepare("CREATE TABLE IF NOT EXISTS Weddings (
			lastname VARCHAR(100) NOT NULL,
			firstname VARCHAR(100) NOT NULL,
			announcement VARCHAR(100),
			weddingdate DATE NOT NULL,
			articledate DATE NOT NULL,
			page VARCHAR(10),
			id int NOT NULL AUTO_INCREMENT,
			fileID int NOT NULL,
			CONSTRAINT uc_wedding UNIQUE (lastname, firstname, articledate),
			PRIMARY KEY (id)
		)");
		$stmtWeddingTable->execute();
		if(!empty($_POST['year'])) {
			$year = $_POST['year'];
		} else {
			throw new Exception("The file wasn't sent");
		}
		$file = $year . '/weddings-anniversaries' . $year . '.csv';
		$filename = basename($file, '.csv');
		$currentdir = getcwd();
		$filePath = $currentdir . '/uploads/' . $file;
		if(file_exists($filePath)) {
			$stmtFileInsert = $conn->prepare("INSERT INTO Files (filename, type, year) VALUES (:filename, :type, :year)");
			$stmtFileInsert->bindParam(':filename', $filename);
			$stmtFileInsert->bindParam(':type', $type);
			$stmtFileInsert->bindParam(':year', $year);
			$type = "Weddings-Anniversaries";
			$stmtFileInsert->execute();
			$fileSelect = $conn->prepare('SELECT id FROM Files WHERE filename=:filename1');
			$fileSelect->bindParam(':filename1', $filename);
			$fileSelect->execute();
			$fileIDSearch = $fileSelect->fetch(PDO::FETCH_ASSOC);
			$stmtInsert = $conn->prepare("INSERT IGNORE INTO Weddings 
				(lastname, firstname, announcement, weddingdate, articledate, page, fileID) 
				VALUES (:lastname, :firstname, :announcement, :weddingdate, :articledate, :page," . $fileIDSearch['id'] . ")"
			);
			$stmtInsert->bindParam(':lastname', $lastname);
			$stmtInsert->bindParam(':firstname', $firstname);
			$stmtInsert->bindParam(':announcement', $announcement);
			$stmtInsert->bindParam(':weddingdate', $weddingdate);
			$stmtInsert->bindParam(':articledate', $articledate);
			$stmtInsert->bindParam(':page', $page);
			
			ini_set('auto_detect_line_endings', TRUE);
			$currentPercent = 0.0;
			if(($handle = fopen($filePath, "r")) !== FALSE) {
				$lastNameReached = false;
				$fileLength = count(file($filePath));
				$percentPerRow = 100 / $fileLength;
				$count = 0;
				while (($data = fgetcsv($handle, ',')) !== FALSE) {
					$currentPercent = $currentPercent + $percentPerRow;
					$roundedPercent = ceil($currentPercent);
					if($roundedPercent % 2 == 0) {
						file_put_contents('progress.json', json_encode(array('percentComplete'=>$roundedPercent)));
					}
					if($lastNameReached && !empty($data[0])) {
						$lastname = $data[0];
						$firstname = $data[1];
						$announcement = $data[2];
						$weddingdate = date("Y-m-d", strtotime($data[3]));
						$articledate = date("Y-m-d", strtotime($data[4]));
						$page = $data[5];
						$stmtInsert->execute();
					}
					if(strcasecmp($data[0], 'last name') == 0) {
						$lastNameReached = true;
					}
					$count++;
				}
			} else {
				throw new Exception("There was a problem opening the file");
			}
			if($count + 5 < $fileLength) {
				throw new Exception("Stopped before end of file count is: $count");
			}
			fclose($handle);
			ini_set('auto_detect_line_endings', FALSE);
	/*		$filename = basename($file, '.csv');
			$stmtFileInsert = $conn->prepare("INSERT INTO Files (filename, type) VALUES (:filename, :type)");
			$stmtFileInsert->bindParam(':filename', $filename);
			$stmtFileInsert->bindParam(':type', $type);
			$filename = basename($file, '.csv');
			$type = "Weddings-Anniversaries";
			if($lastNameReached) {
				$stmtFileInsert->execute();
			}*/
		}
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
	catch(Exception $e) {
		echo $e->getMessage();
	}
	$conn = null;
	exit(0);
?>
