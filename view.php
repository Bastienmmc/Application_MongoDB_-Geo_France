<?php
require_once ('composer.php');
header('content-type: text/html; charset=utf-8');
$ville=[];
if (isset($_SESSION['idview'])) {    
    $errMsg = [];
    $id= intval($_SESSION['idview']);
try {
		if ($id<=0) {
            throw new Exception('Veuillez entrer un ville valide');
		}			
      $filtre = ['_id' => $id];
      $options = ['projection' => ['_id' => 0], // on veut tout sauf les identifiants
      ];
      $query = new MongoDB\Driver\Query($filtre, $options);
      $rows = $manager->executeQuery($dbname . '.' .$collname_v, $query);
				if ($rows->isDead()) { //se non c'è un user
					throw new Exception('Veuillez entrer un ville valide');
                }
                
				else { //se ho trovato l'user
          $ville = (array)$rows->toArray() [0];          
          $filtre = ['_id' => $ville['_id_dept'] ];
          $options = ['projection' => ['_id' => 0], // on veut tout sauf les identifiants
          ];
          $query = new MongoDB\Driver\Query($filtre, $options);
          $rows = $manager->executeQuery($dbname . '.' .$collname_d, $query);
        if ($rows->isDead()) { //se non c'è un user
          throw new Exception('Veuillez entrer un ville valide dep non trouvee');
        }
          $rows = $rows->toArray() [0];    
          $ville= array('dep'=>$rows->nom) + $ville;

          $filtre = ['_id' => $rows->_id_region ];
          $options = ['projection' => ['_id' => 0], // on veut tout sauf les identifiants
          ];
          $query = new MongoDB\Driver\Query($filtre, $options);
          $rows = $manager->executeQuery($dbname . '.' .$collname_r, $query);
        if ($rows->isDead()) { //se non c'è un user
          throw new Exception('Veuillez entrer un ville valide deregion non trouvee');
        }
          $rows = $rows->toArray() [0];
          $ville= array('reg'=>$rows->nom) + $ville;
        }
      }  
   
			catch(Exception $e) {
        unset($_SESSION['idview'] );
        $msg='Erreur : ' . $e->getMessage();
        echo ("<script>window.alert($msg);</script>");
        echo ("<script>setTimeout(function(){location.href ='./index.php'}, 2000);</script>");
				exit();
			}
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
      <title>Résultat de la recherche</title>
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
                           printf('<a class="dropdown-item" href="./edit.php">Editeurs/a>');
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
                     <a href="#">Panneau de contrôle</a>
                  </li>
                  <li class="breadcrumb-item active">Résultat de la recherche</li>
               </ol>
               <!-- DataTables Example -->
               <div class="card mb-3">
                  <div class="card-header">
                     <i class="fas fa-search"></i>
                     Ville trouvée:
                  </div>
                  <div class="row">
                    <div class="offset-2 col-8">

                     <div class="card-body">
                     <div >
                     
                        <?php
                          
                          if(!empty($ville)){  
                        
$fieldset = <<<EOC
<div class="row">                                
<div class=col-lg-6 col-sm-12>                                
<strong>Ville : %s</strong><br>                                
Departement : %s<br>                                
Region : %s<br>                                
Code postal : %s<br>                                
%s                                
Longitude : %s<br>                                
Latitude : %s<br><br>                                
<a href="edit.php?id=%s"><button class="btn btn-primary">Modifier des informations</button></a> 
<br/><br/>                               
</div>                                
<div class=col-lg-6 col-sm-12>
<a href='cartesvg.php' ><object type="image/svg+xml" data="cartesvg.php">
</object></a>
                    
</div>                                
</div>
EOC;

                        printf($fieldset, 
                        htmlspecialchars($ville['nom']) ,
                        htmlspecialchars($ville['dep']),
                        htmlspecialchars($ville['reg']),
                        htmlspecialchars($ville['cp']) ,
                        (isset($ville['pop'])?('Population :'.htmlspecialchars($ville['pop']).'<br>') :''),
                        htmlspecialchars($ville['lon']), 
                        htmlspecialchars($ville['lat']),
                      $id);
//echo $id;
}
                         
                           ?>
                           <a href="index.php"><button class="btn btn-primary">Nouvelle Recherche</button></a>
                            
                     </div>
                  </div>
                      

                    </div>
                  </div>
                  
               </div>
         </div>         
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
