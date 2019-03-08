<?php
   require_once ('composer.php');

   	header('content-type: text/html; charset=utf-8');
  
  
     require_once ('composer.php');
     require_once ('Carte.php');
     try
     {
         $map = <<<EOJSBOXMAP
     function() {
     var res = [180, -180, 90, -90];
     for (i=0; i<this.poly.length; i++) {
     if (res[0] > this.poly[i][0]) res[0] = this.poly[i][0];
     if (res[1] < this.poly[i][0]) res[1] = this.poly[i][0];
     if (res[2] > this.poly[i][1]) res[2] = this.poly[i][1];
     if (res[3] < this.poly[i][1]) res[3] = this.poly[i][1];
     }
     emit(1, res);
     }
EOJSBOXMAP;
         $reduce = <<<EOJSBOXRED
     function(key, vals) {
     var res = [180, -180, 90, -90];
     vals.forEach(function(val) {
     if (res[0] > val[0]) res[0] = val[0];
     if (res[1] < val[1]) res[1] = val[1];
     if (res[2] > val[2]) res[2] = val[2];
     if (res[3] < val[3]) res[3] = val[3];
     });
     return {minmax: res};
     }
EOJSBOXRED;
         // préparation de la commande et récupération des résultats dans l'unique valeur de retour
         $boxCmd = new MongoDB\Driver\Command(['mapreduce' => 'contours', 'map' => $map, 'reduce' => $reduce, 'out' => ['inline' => 1]]);
         $rows = $manager->executeCommand($dbname, $boxCmd)->toArray() [0];
         $minmax = $rows->results[0]
             ->value->minmax;
         list($lon_min, $lon_max, $lat_min, $lat_max) = $minmax;
         //acque territoriali francesi
         // dessin de la France avec les eaux territoriales (Corse comprise)
         // commande de récupération de tous les poly de contours
         $query = new MongoDB\Driver\Query(['_id' => ['$gt' => 0]]); // astuce car commande 'find' pas suportée par anciennes bases de données
         $rows = $manager->executeQuery($dbname . '.contours', $query);
         //$command = new MongoDB\Driver\Command(['find' => 'contours']); // ces lignes correspondens à l'utilisation de 'find'
         //$curseur = $manager->executeCommand($dbname, $command);
         if ($rows->isDead())
         { //se non c'è un user
             throw new Exception('Veuillez entrer un ville valide');
         }
         $cartes = '';
         $i = 0;
         
         foreach ($rows as $doc)
         {
             $contour[] = $doc->poly;
         }
         $carte = new Carte($contour, 800, $lon_min, $lon_max, $lat_min, $lat_max, [37, 64, 88]);
         $header = $carte->getHeader();
         $footer = $carte->getFooter();
         $cartes .= $carte->getPolygon();
         $filtre = ['contours' => ['$exists' => true]];
         $options = ['projection' => ['contours' => 1, '_id_region' => 1], // on ne veut que ces colonnes sans l'identifiant
         'sort' => ['contours' => 1], // triées selon le nombre de contours ordre croissant
         ];
         $query = new MongoDB\Driver\Query($filtre, $options);
         $rows = $manager->executeQuery($dbname . '.' . $collname_d, $query);
         if ($rows->isDead())
         { //se non c'è un user
             throw new Exception('Veuillez entrer un ville valide');
         }
         else
         { //se ho trovato dei contorni
             foreach ($rows as $row)
             {
                 $idr = $row->_id_region;
                 $idd = $row->_id;
                 //echo $idr ;exit;
                 if (empty($colr[$idr]))
                 {
                     $colr[$idr] = [rand(5, 14) , rand(5, 14) , rand(5, 14) ];
                 }
                 $carte = new Carte($row->contours, 800, 'Toulouse', 10, 10, $lon_min, $lon_max, $lat_min, $lat_max, $colr[$idr], $idd); //double constructor!!!!
                 $cartes .= $carte->getPolygon();
             }
         }
         //header("Content-type: image/svg+xml");
         $cartes= $header . $cartes . $footer;
     }
     catch(Exception $e)
     {
         $msg = 'Erreur : ' . $e->getMessage();
         echo ("<script>window.alert($msg);</script>");
         echo ("<script>setTimeout(function(){location.href ='./index.php'}, 2000);</script>");
         exit();
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
      <title>Carte de France</title>
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
                  <li class="breadcrumb-item active">Carte de France</li>
               </ol>
               <!-- Area Chart Example-->
               <div class="card mb-3">
                  <div class="card-header">
                     <i class="fas fa-map-marked-alt"></i>
                     <?php echo isset($formVal['editnew']) ? htmlspecialchars($formVal['editnew']) : 'Carte de France des Départements et Régions '?>
                  </div>

                  <div class= row>
                 
                 
                  <div class="card-body">
                  <h5 style="text-align:center">Cliquez sur un département pour voir la liste des villes:</h5>
                  <p></p>
                     <div class="col-12">
                     
                        <?php
                          //printf ('<object style="width:1000px;height:1000px;" type="image/svg+xml" data="cartefrancesvg.php"></object>');
                          //printf (data="cartefrancesvg.php");
                          //<image x="0" y="0" xlink:href="data:image/svg+xml;base64,[base64 of nested svg]"></image>
                          //<image x="0" y="0" width="1500px" height="1900px" xlink:href="./cartefrancesvg.php" />
                           ?>
                          <svg style="width:100%;height:1900px;"class="container-fluid" >
  <?php echo $cartes; ?>
                          
                          </svg>
                            
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