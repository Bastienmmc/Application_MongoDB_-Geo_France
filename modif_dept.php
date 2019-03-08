<?php
require_once ('composer.php');

if (empty($_SESSION['login']) || $_SESSION['usertype'] != 'admin') {
    header("HTTP/1.1 303 See Other");    
	header("Location: index.php");
	exit();
}

if (isset($_POST['annuler'])) {
	header('location:admin.php');
}
// Option Modification departement -----------------------------------------------------
else if (isset($_POST['dept_a_modif'])){

    //R2cupération des infos du formulaire
    $departement = htmlspecialchars($_POST['dept_a_modif']);
    $region = htmlspecialchars($_POST['region_choisie']);
    
    try {
        // aller chercher le numero de la region à rattacher et stocker dans la variable $id_region
        // Préparation de la requete
        $filtre = ['nom' => $region];
        
        $options = ['projection' => ['_id' => 1]];
        $query = new MongoDB\Driver\Query($filtre, $options);
        // Exécution de la requete
        $rows = $manager->executeQuery($dbname . '.' . $collname_r, $query);
            
            foreach($rows as $doc) {  // parcours du curseur retourné par la recherche (1 seul élément)
                foreach ($doc as $key => $val) {
                    
                    $id_region=$val;
                } 
            }
            
        //Vérification si le département n'est pas déjà rattaché à cette région
        $filtre = ['nom' => $departement];
        $options = ['projection' => ['_id_region' => 1],['_id' => 0]];
        $query = new MongoDB\Driver\Query($filtre, $options);
        // Exécution de la requete
        $rows = $manager->executeQuery($dbname . '.' . $collname_d, $query);      

            foreach($rows as $doc) {  // parcours du curseur retourné par la recherche (1 seul élément)
                foreach ($doc as $key => $vals) {
                    if (($vals == $id_region) AND ($key == '_id_region')){
                        throw new Exception('Le département '.$departement. ' est déjà rattaché à la région '.$region. ' .');
                    }
                } 
            }     

        // modifier le numéro de la région à rattacher
		$rec = new MongoDB\Driver\BulkWrite();
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        //$collname = 'departements';
		$arrayval = ['_id_region' => $id_region];
		$rec->update(['nom'=> $departement],['$set' =>$arrayval], ['multi' => false, 'upsert' => false]); 
        $result = $manager->executeBulkWrite($dbname . '.' . $collname_d, $rec, $writeConcern); 

        //Stockage des infos de modification dans la variable de session
        $_SESSION['success'] = ' Modification confirmée, le département '.$departement. ' a été rattaché à la région: '. $region;
	}

	catch(exception $exep) {
        $_SESSION['success'] = htmlspecialchars($exep->getMessage());
        header('location:admin.php');
        exit;
        
    }
} 
header('location:admin.php');