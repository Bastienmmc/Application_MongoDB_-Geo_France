<?php
require_once ('composer.php');

if (empty($_SESSION['login']) || $_SESSION['usertype'] != 'admin') {
    header("HTTP/1.1 303 See Other");    
	header("Location: index.php");
	exit();
}

//L'appui sur annuler renvoie sur la page précédente
if (isset($_POST['annuler'])) {
	header('location:admin.php');
}
// Option Création d'une nouvelle région ----------------------------------------------------------
else if (isset ($_POST['region_cree'])) {
    if(is_null(validatorRegion($_POST['region_cree']))){
        $_SESSION['success']='Invalid Input';
        header('location:admin.php');
        exit;
    };
	$nouvelle_region = htmlspecialchars($_POST['region_cree']);

	//pour compter le nombre de région
    $nb_regions = 1;
    try {
    // préparation de la requete
	$filtre = [];
	//$collname = 'regions';
	$options = ['projection' => []];
    $query = new MongoDB\Driver\Query($filtre, $options);
    
    //Exécution de la requete
	$rows = $manager->executeQuery($dbname . '.' . $collname_r, $query);
        foreach($rows as $doc) {
            $nb_regions+= 1;// compte le nb de régions
            foreach($doc as $key => $val) {
                if (strtolower($nouvelle_region) == strtolower($val)) {// vérifie si région existe
                    throw new exception('Cette région existe déjà');
                }
            }
        }
    //Si la région n'existe pas, création d'une nouvelle
	
        // Effectue la création dans la base
        
		$rec = new MongoDB\Driver\BulkWrite();
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$arrayval = ['_id' => $nb_regions + 1, 'nom' => $nouvelle_region];
		$rec->insert($arrayval);
        $result = $manager->executeBulkWrite($dbname . '.' . $collname_r, $rec, $writeConcern);

        //Création de la variable de session avec les informations de confirmation
        $_SESSION['success'] = ' Modification confirmée, la région '.$nouvelle_region. ' a été créée.';

        
	}

	catch(exception $exep) {
        $_SESSION['success'] = htmlspecialchars($exep->getMessage());
        header('location:admin.php');
        exit;
    }
}
header('location:admin.php');