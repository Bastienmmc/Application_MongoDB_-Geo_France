<?php
require_once ('composer.php');

if (empty($_SESSION['login']) || $_SESSION['usertype'] != 'admin') {
    header("Location: index.php");
    header("HTTP/1.1 303 See Other");     
	exit();
}

if (isset($_POST['annuler'])) {
	header('location:admin.php');
} 

// Option Modification Région ---------------------------------------------------------------------------
else if (isset($_POST['region_a_modifier'])){
    if(is_null(validatorRegion($_POST['region_a_modifier']))){
        $_SESSION['success']='Invalid Input';
        header('location:admin.php');
        exit;
    };          
    $region_choisie = htmlspecialchars($_POST['region_a_modifier']);
    $nouveau_nom = htmlspecialchars($_POST['region_modifiee']);
    
    try {
        $rec = new MongoDB\Driver\BulkWrite();
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        //$collname = 'regions';
        $arrayval = ['nom' => $nouveau_nom];
        $rec->update(['nom'=> $region_choisie],['$set' =>$arrayval], ['multi' => false, 'upsert' => false]); 
        $result = $manager->executeBulkWrite($dbname . '.' . $collname_r, $rec, $writeConcern); 
        $success = '';
        
        //stockage des infos de modifications dans la variable de session
        $_SESSION['success'] = ' Modification confirmée, '.$region_choisie. ' a été modifée en : '. $nouveau_nom;
        
    }

    catch(exception $exep) {
        $_SESSION['success'] = htmlspecialchars($exep->getMessage());
        header('location:admin.php');
        exit;
    }
}


header('location:admin.php');