<?php
require_once ('composer.php');
try {
if (empty($_SESSION['login'])) {
  header('content-type: text/html; charset=utf-8');

	if (isset($_POST['login'])) {
		// login and password sent from Form

		$login = (isset($_POST['login'])) ? $_POST['login'] : NULL;
    $pass = (isset($_POST['pass'])) ? $_POST['pass'] : NULL;
    $login= validatorEmail($login);
    $pass= validatorPassword($pass);

		if (!$login) {
      throw new Exception('Vérifiez vos identifiants de connexion.'); 
		}

		if (!$pass) {
      throw new Exception('Vérifiez vos identifiants de connexion.'); 
		}
				$filtre = ['email' => $login];
				$options = ['projection' => ['_id' => 0], // on veut tout sauf les identifiants
				];
				$query = new MongoDB\Driver\Query($filtre, $options);
				$rows = $manager->executeQuery($dbname . '.' . $tableuser, $query);
        if ($rows->isDead()) { //se non c'è un user
          throw new Exception('Vérifiez vos identifiants de connexion.'); 
				}
				else { //se ho trovato l'user
					$rows = $rows->toArray() [0];
					/*echo '<br />qui  ';
					echo '<br />qui  ' . $rows->{'username'};
					echo '<br />qui  ' . $rows->{'password'};*/
					if (password_verify($pass, $rows->{'password'})) {
            $_SESSION['login'] = $login;
            $_SESSION['name'] = $rows->{'name'};
						$_SESSION['usertype'] = $rows->{'usertype'};
            $_SESSION['loggedin_time'] = time();
            echo ("<script>setTimeout(function(){location.href ='./index.php'}, 100);</script>");
            exit();
						exit();
					}
					else {
            throw new Exception('Vérifiez vos identifiants de connexion.'); 
					}
				}
			

		
  }
  
  }else {
      echo 'You are logged in. <a href="./index.php">Click here</a>.';
      $msg = '';
      echo ("<script>setTimeout(function(){location.href ='./index.php'}, 2000);</script>");
      exit();
}
  

}	
catch(Exception $e) {
      $msg=$e->getMessage();
      $msg= array(sprintf('<div class="alert alert-warning" role="alert">%s</div>',$msg) );  
}

?>
<!DOCTYPE html>
<html lang="fr">

  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin - Login</title>

    <!-- Bootstrap core CSS-->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin.css" rel="stylesheet">

  </head>

  <body class="bg-dark">

    <div class="container">
      <div class="card card-login mx-auto mt-5">
        <div class="card-header">Login</div>
        <div class="card-body">
          <form action="" method="post" id="formLogin" >
            <div class="form-group">
              <div class="form-label-group">
                <input type="email" name="login"  id="inputEmail" class="form-control" placeholder="Email address" required="required" autofocus="autofocus">
                <label for="inputEmail">Email address</label>
              </div>
            </div>
            <div class="form-group">
              <div class="form-label-group">
                <input type="password"  name="pass" id="inputPassword" class="form-control" placeholder="Password" required="required">
                <label for="inputPassword">Password</label>
              </div>
            </div>
            <a class="btn btn-primary btn-block" href="javascript:{}" onclick="document.getElementById('formLogin').submit();">Login</a>
          </form>
          <?php 
                if (!empty($msg)){
                    foreach($msg as $ms){
                        echo $ms;   
                    }
                    unset($msg);
                }
            ?>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  </body>

</html>
