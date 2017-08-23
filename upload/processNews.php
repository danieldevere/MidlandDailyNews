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
		$stmtArticleTable = $conn->prepare("CREATE TABLE IF NOT EXISTS News (
			subject VARCHAR(100) NOT NULL,
			article VARCHAR(100) NOT NULL,
			page VARCHAR(10),
			articledate DATE NOT NULL,
			id int NOT NULL AUTO_INCREMENT,
			fileID int NOT NULL,
			CONSTRAINT uc_article UNIQUE (subject, article, articledate),
			PRIMARY KEY (id)
		)");
		$stmtArticleTable->execute();
		if(!empty($_POST['year'])) {
			$year = $_POST['year'];
		} else {
			throw new Exception("No filename sent");
		}
		$file = $year . '/articles' . $year . '.csv';
		$filename = basename($file, '.csv');
		$currentdir = getcwd();
		$filePath = $currentdir . '/uploads/' . $file;
		if(file_exists($filePath)) {
			$stmtFileInsert = $conn->prepare("INSERT INTO Files (filename, type, year) VALUES (:filename, :type, :year)");
			$stmtFileInsert->bindParam(':filename', $filename);
			$stmtFileInsert->bindParam(':type', $type);
			$stmtFileInsert->bindParam(':year', $year);
			$type = "Article";
			$stmtFileInsert->execute();
			$fileSelect = $conn->prepare('SELECT id FROM Files WHERE filename=:filename1');
			$fileSelect->bindParam(':filename1', $filename);
			$fileSelect->execute();
			$fileIDSearch = $fileSelect->fetch(PDO::FETCH_ASSOC);
			$stmtInsert = $conn->prepare("INSERT IGNORE INTO News (subject, article, page, articledate, fileID) VALUES (:subject, :article, :page, :articledate," . $fileIDSearch['id'] . ")");
			$stmtInsert->bindParam(':subject', $subject);
			$stmtInsert->bindParam(':article', $article);
			$stmtInsert->bindParam(':page', $page);
			$stmtInsert->bindParam(':articledate', $articledate);
			
			ini_set('auto_detect_line_endings', TRUE);
			$currentPercent = 0.0;
			if(($handle = fopen($filePath, "r")) !== FALSE) {
				$subjectReached = false;
				$fileLength = count(file($filePath));
				$percentPerRow = 100 / $fileLength;
				$count = 0;
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

						$stmtInsert->execute();
					}
				//	echo $data[0];
					if(strcasecmp(str_replace('"', "", $data[0]), 'subject') == 0) {
					//	echo 'subject reached';
						$subjectReached = true;
					}
					$count++;
				}
				fclose($handle);
			//	echo $count;
				if(!$subjectReached) {
					throw new Exception("Subject wasn't reached");
				}
			} else {
				throw new Exception("There was an error opening the file");
			}
			if($count+5 < $fileLength) {
				throw new Exception("Didn't reach end of file Count is: $count");
			}
			
			ini_set('auto_detect_line_endings', FALSE);

			/*$stmtFileInsert = $conn->prepare("INSERT INTO Files (filename, type) VALUES (:filename, :type)");
			$stmtFileInsert->bindParam(':filename', $filename);
			$stmtFileInsert->bindParam(':type', $type);
			$type = 'Article';
			$filename = basename($file, '.csv');
			$stmtFileInsert->execute();*/
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
