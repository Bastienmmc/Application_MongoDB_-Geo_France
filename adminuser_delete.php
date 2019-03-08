<?php
require_once ('composer.php');

if (empty($_SESSION['login']) || $_SESSION['usertype'] != 'admin') {
	header("HTTP/1.1 303 See Other");    
	header("Location: index.php");
	exit();
}
try{
	$id = isset($_GET['id']) ? ($_GET['id']) : NULL;
	$flag = 0;


	if ($id) {
		$delRec = new MongoDB\Driver\BulkWrite;
		$delRec->delete(['_id' => new MongoDB\BSON\ObjectID($id) ], ['limit' => 1]);
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $manager->executeBulkWrite($dbname . '.' . $tableuser, $delRec, $writeConcern);
		if (is_null($result->getDeletedCount())) {
			throw new Exception('Can\'t delete user account.');
			
		}

		$_SESSION['msgUpdate'] = 'Deleted user account';
		header("Location: adminuser.php");
		exit;	
	}
	throw new Exception('Can\'t delete user account.');
	



}catch(Exception $e) {
	$_SESSION['msgUpdate'] = $e->getMessage();
	header("Location: adminuser.php");
	exit;
}

