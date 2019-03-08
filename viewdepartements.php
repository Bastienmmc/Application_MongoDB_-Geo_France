<?php
   require_once ('composer.php');
   try {
   $id = isset($_GET['id']) ? intval($_GET['id']) : 31;

      $filtre = ['_id'=> $id];
      $options=[];
      $query = new MongoDB\Driver\Query($filtre, $options);
      $rows = $manager->executeQuery($dbname . '.' . $collname_d, $query);
      if ($rows->isDead()) { //se non c'è un user
          throw new Exception('Vérifiez vos identifiants de connexion.'); 
      }
      else { //se ho trovato l'user
          $departement = $rows->toArray() [0];
        }
      }
      catch(Exception $e) {
        $msg= $e->getMessage();
        return array(sprintf('<div class="alert alert-danger" role="alert">Erreur %s.</div>', $msg ));
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
      <title>Réglages Utilisateurs</title>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
      <!-- Bootstrap core CSS-->
      <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <!-- Custom fonts for this template-->
      <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
      <!-- Page level plugin CSS-->
      <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
      <!-- Custom styles for this template-->
      <link href="css/sb-admin.css" rel="stylesheet">
      <link href="css/style.css" rel="stylesheet">
   </head>
   <body id="page-top">
      <nav class="navbar navbar-expand navbar-dark bg-dark static-top">
         <a class="navbar-brand mr-1" href="index.php">Cartographie GEO FRANCE</a>
         <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
         <i class="fas fa-bars"></i>
         </button>
        
         <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item dropdown no-arrow">
               <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
               <i class="fas fa-user-circle fa-fw"></i>
               <?php echo isset($_SESSION['login'])?' '.$_SESSION['name']:'';  ?>
               </a>
               <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                  <?php 
                     if (isset($_SESSION["login"])){
                       if($_SESSION["usertype"]=='admin'){
                           printf('<a class="dropdown-item" href="./admin.php">Administrateurs</a>');
                           printf('<a class="dropdown-item" href="./edit.php">Editeurs</a>');
                           printf('<a class="dropdown-item" href="./adminuser.php">Utilisateurs</a>');
                       }
                       if($_SESSION["usertype"]=='edit'){
                         printf('<a class="dropdown-item" href="./edit.php">Modifications</a>');
                       }
                       
                       //print logout
                       printf(' <div class="dropdown-divider"></div>');
                       printf('<a class="dropdown-item" href="./logout.php">Déconnexion</a>');
                     }else{  //print login 
                       printf('<a class="dropdown-item" href="./login.php">Connexion</a>');
                     }
                     
                     ?>
               </div>
            </li>
         </ul>
      </nav>
      <div id="wrapper">
         <!-- Sidebar -->
         <ul class="sidebar navbar-nav">
            <li class="nav-item active">
               <a class="nav-link" href="index.php">
               <i class="fas fa-fw fa-tachometer-alt"></i>
               <span>Panneau de contrôle</span>
               </a>
            </li>
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
               <i class="fas fa-atlas"></i>
               <span>Pages</span>
               </a>
               <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                  <h6 class="dropdown-header">Pages</h6>
                  <a class="dropdown-item" href="index.php">Accueil</a>
                  <a class="dropdown-item" href="cartedefrance.php">Carte de France</a>
                  <div class="dropdown-divider"></div>
               </div>
            </li>
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
               <i class="fas fa-clipboard-list"></i>
               <span>Modifications</span>
               </a>
               <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                  <h6 class="dropdown-header">Modifications</h6>
                  <?php 
                     if (isset($_SESSION["login"])){
                       if($_SESSION["usertype"]=='admin'){
                           printf('<a class="dropdown-item" href="./admin.php">Administrateurs</a>');
                           printf('<a class="dropdown-item" href="./edit.php">Editeurs</a>');
                           printf('<a class="dropdown-item" href="./adminuser.php">Utilisateurs</a>');
                       }
                       if($_SESSION["usertype"]=='edit'){
                         printf('<a class="dropdown-item" href="./edit.php">Modifications</a>');
                     
                       }
                       
                       //print logout
                       printf(' <div class="dropdown-divider"></div>');
                       printf('<a class="dropdown-item" href="./logout.php">Déconnexion</a>');
                     }else{  //print login 
                       printf('<a class="dropdown-item" href="./login.php">Connexion</a>');
                     }
                     
                     ?>
               </div>
            </li>
         </ul>
         <div id="content-wrapper">

<div class="container-fluid">

  <!-- Breadcrumbs-->
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="index.php">Panneau de contrôle</a>

    </li>
    <li class="breadcrumb-item active">Liste des villes</li>
  </ol>

  <!-- DataTables Example -->
  <div class="card mb-3">
    <div class="card-header">
      
      <?php echo '<p><i class="fas fa-list-ul"></i>  Departement : <strong>'. htmlspecialchars($departement->nom) . "</strong> - Code postal : <strong>".  htmlspecialchars($departement->code)  ."</strong></p> "; ?></div>
    <div class="card-body">
      <div class="table-responsive">
      <table class="table table-bordered "  id="dataTable1" width="100%" cellspacing="0">
              <?php
              $options = ['projection' => ['_id' => 0, '_id_dept' => 0,'contours' => 0]];
             // $options = ['limit' => 490, 'projection' => ['_id' => 0, 'contours' => 0]];
              $array=preparaTableDep($dbname,  $collname_v, $manager,['_id_dept'=>intval($id) ], $options);          
              foreach($array as $arr) {
                echo $arr;
                           }
                ?>

      </div>
    </div>
    <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
  </div>

  <p class="small text-center text-muted my-5">
    <em>More table examples coming soon...</em>
  </p>

</div>
<!-- /.container-fluid -->
      <!-- Sticky Footer -->
      <footer class="sticky-footer">
         <div class="container my-auto">
            <div class="copyright text-center my-auto">
               <span>Cartographie GEO FRANCE - LDNR 2019</span>
            </div>
         </div>
      </footer>
      </div>
      <!-- /.content-wrapper -->
      </div>
      <!-- /#wrapper -->
      <!-- Scroll to Top Button-->
      <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
      </a>
     
      <!-- Bootstrap core JavaScript-->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- Core plugin JavaScript-->
      <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
      <!-- Page level plugin JavaScript-->
      <script src="vendor/datatables/jquery.dataTables.js"></script>
      <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
      <!-- Custom scripts for all pages-->
      <script src="js/sb-admin.min.js"></script>
      <!-- Demo scripts for this page-->
      <script src="js/demo/datatables-demo.js"></script>
   </body>
</html>
