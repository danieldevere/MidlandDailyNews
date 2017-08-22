<?php
session_start();
try{
    if(!empty($_POST['data'])) {
        $prints = json_decode($_POST['data'], true);
        $_SESSION['prints'] = $prints;
        echo 'Added to session';
    } else {
        if(!empty($_SESSION['prints']['articles']) || !empty($_SESSION['prints']['obits']) || !empty($_SESSION['prints']['weddings'])) {
            $prints = $_SESSION['prints'];
            echo json_encode(array('hasPrints'=>true, 'articles'=>$prints['articles'], 'obits'=>$prints['obits'], 'weddings'=>$prints['weddings']));
        } else {
            $prints = json_encode(array('hasPrints'=>false));
            echo $prints;
        }
    }
}
catch(Exception $e) {
    echo $e->getMessage();
}
?>