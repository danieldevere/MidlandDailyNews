<?php
if($_FILES['xlsfile']['name'])
{
    if(!$_FILES['xlsfile']['error'])
    {
        $valid_file = true;
        $new_file_name = strtolower($_FILES['xlsfile']['tmp_name']);
        if($_FILES['xlsfile']['size'] > (10240000))
        {
            $valid_file = false;
            $message = 'The file is too large';
        }
        if($valid_file)
        {
            $currentdir = getcwd();
            $target = $currentdir . '/uploads/' . basename($_FILES['xlsfile']['name']);
            echo $target;
            move_uploaded_file($_FILES['xlsfile']['tmp_name'], $target);
            include 'Classes/PHPExcel.php';

            try{
                if(!empty($_POST['year'])) {
                    $year = $_POST['year'];
                } else {
                    throw new Exception("Year isn't set");
                }
                $uploadFolder = $currentdir . '/uploads/';
                mkdir($uploadFolder . $year);
                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                $objReader->setLoadSheetsOnly('Annual Article Index');
                $objArticle = $objReader->load($target);
                $objReader->setLoadSheetsOnly('Obituaries');
                $objObits = $objReader->load($target);
                $objReader->setLoadSheetsOnly('Weddings-Anniversaries');
                $objWedding = $objReader->load($target);
                $objWriter = new PHPExcel_Writer_CSV($objArticle);
                $objWriter->setUseBOM(true);
                $objWriter->save($uploadFolder . $year . "/articles" . $year . '.csv');
                if(!empty($objObits->getActiveSheet()->getCell('A10')->getValue())) {
                    $objWriter = new PHPExcel_Writer_CSV($objObits);
                    $objWriter->setUseBOM(true);
                    $objWriter->save($uploadFolder . $year . "/obituaries" . $year . '.csv');
                }
                if(!empty($objWedding->getActiveSheet()->getCell('A10')->getValue())) {
                    $objWriter = new PHPExcel_Writer_CSV($objWedding);
                    $objWriter->setUseBOM(true);
                    $objWriter->save($uploadFolder . $year . "/weddings-anniversaries" . $year . '.csv');
                }
                
                echo $year;
            }
            catch(Exception $e) {
                echo $e->getMessage();
            }
            $message = 'Upload successful';
        }
    }
    else
    {
        $message = 'Your upload triggered the following error: '.$_FILES['xlsfile']['error'];
    }
}
else 
{
    $message = 'Something went wrong.  We didnt get the file';
}
echo $message;
?>