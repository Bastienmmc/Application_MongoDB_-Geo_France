<?php
require_once ('composer.php');


if (empty($_SESSION['login']) || $_SESSION['usertype'] != 'admin') {
	header("HTTP/1.1 303 See Other");    
	header("Location: index.php");
	exit();
}
$type='';
$flag = '';
$err='';
try {
	$id = isset($_POST['id']) ? ($_POST['id']) : 0;

	// if(isset($id)){
	// controllo le variabili

	$name = isset($_POST['name']) ? validatorUsername($_POST['name']) : NULL;
	$username = isset($_POST['username']) ? validatorUsername($_POST['username']) : NULL;

	// se modifico la password...

	if (isset($_POST['password'])) {
		if (validatorPassword($_POST['password'])) {
			$password = hashPassword($_POST['password']);
		}
	}

	$email = isset($_POST['email']) ? validatorEmail($_POST['email']) : NULL;
	$usertype = isset($_POST['usertype']) ? ($_POST['usertype']) : NULL;
	if (is_null($name) || is_null($username) || (isset($password) && is_null($password)) || is_null($email) || is_null($usertype)) {
		if (is_null($name)) {
			$flag.= "\nIncorrect name<br/>";
		}

		if (is_null($username)) {
			$flag.= "\nIncorrect username<br/>";
		}

		if (isset($password) && is_null($password)) {
			$flag.= "\nIncorrect password<br/>";
		}

		if (is_null($email)) {
			$flag.= "\nIncorrect email<br/>";
		}

		if (is_null($usertype)) {
			$flag.= "\nIncorrect usertype<br/>";
		}
		throw new Exception($flag);

	}
	else {
			$filtre = ['username' => $username];
			$options = ['projection' => ['_id' => 0]];
			$query = new MongoDB\Driver\Query($filtre, $options);
			$rows = $manager->executeQuery($dbname . '.' . $tableuser, $query);
			$rows=$rows->toArray();
			$cont1= count($rows);
		

			$filtre = ['email' => $email];
			$options = ['projection' => ['_id' => 0]];
			$query = new MongoDB\Driver\Query($filtre, $options);
			$rows = $manager->executeQuery($dbname . '.' . $tableuser, $query);
			$rows=$rows->toArray();
			$cont2= count($rows);



		$rec = new MongoDB\Driver\BulkWrite;
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);

		if (isset($password)) {
			$arrayval = ['name' => $name, 'username' => $username, 'password' => $password, 'email' => $email, 'usertype' => $usertype];
		}
		else {
			$arrayval = ['name' => $name, 'username' => $username, 'email' => $email, 'usertype' => $usertype];
		}

		if ($id != 0) { //update
			if($cont1>1){
				throw new Exception('This username already exists. ');
			}
			if($cont2>1){
				throw new Exception('This email already exists. ');
			}

			$type='Update';
			$rec->update(['_id' => new MongoDB\BSON\ObjectID($id) ], ['$set' => $arrayval], ['multi' => false, 'upsert' => false]);
		}
		else { //insert
			if($cont1>0){
				throw new Exception('This username already exists. ');
			}
			if($cont2>0){
				throw new Exception('This email already exists. ');
			}
			$type='Insert';
			$rec->insert($arrayval);
		}
	
		$result = $manager->executeBulkWrite($dbname . '.' . $tableuser, $rec, $writeConcern);
		$err = ' successfully';
	}
	
	$_SESSION['msgUpdate'] = $type . $err;
	header("Location: adminuser.php");
	exit;
}

catch(Exception $e) {
	$_SESSION['msgUpdate'] = $e->getMessage();
	header("Location: adminuser.php");
	exit;
}

