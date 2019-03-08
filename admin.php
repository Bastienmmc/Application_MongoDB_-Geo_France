<?php
   require_once ('composer.php');
   require_once ('adminapp.php');
   if (empty($_SESSION['login']) || $_SESSION['usertype'] != 'admin') {
    header("HTTP/1.1 303 See Other");    
   	header("Location:login.php");
   	exit();
   }
   else {
   	header('content-type: text/html; charset=utf-8');
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
      <title>Page d'administration</title>
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
                  <h6 class="dropdown-header">Menu:</h6>
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
                  <li class="breadcrumb-item active">Administration</li>
               </ol>
               <!-- Area Chart Example-->
               <div class="card mb-3">
                  <div class="card-header">
                     <i class="fas fa-edit"></i>
                     <?php echo isset($formVal['editnew']) ? htmlspecialchars($formVal['editnew']) : 'Modification de collection:'?>
                  </div>

                  <div class= row>
                    <div class="offset-2 col-8">
                      <!--modification ici-->
               <?php
                  // Vérification de la présence du choix dans l'url
                  if (!isset($_GET['choix_modif']))
                  	{
                  	echo print_formulaire1();
                    } 
                  // choix = departement ------------------------------------------------------------------------------
                  if (isset($_GET['choix_modif']) && $_GET['choix_modif'] == "departement")
                  		{		
                        $form= print_formulaire2($manager,$dbname,$collname_d);
                  			foreach($form as $ligne)
                  				{
                  				printf($ligne);
                  				} 
                        }
                   //Choix créer région ------------------------------------------------------------
                  if (isset($_GET['choix_modif']) && $_GET['choix_modif'] == "region_creer"){
                          echo print_formulaire3();
                          } 
                          // Choix Modification de région ---------------------------------------------------
                  if (isset($_GET['choix_modif']) &&$_GET['choix_modif'] == "region_modif") {
                  $form= print_formulaire4($manager,$dbname,$collname_d);
                  	foreach($form as $ligne)
                    {
                      printf($ligne);
                    }
                  }
                 ?>


              <!-- Modal -->
                <div class="modal fade" id="Modalmsg" tabindex="-1" role="dialog" aria-labelledby="ModalLabelmsg" aria-hidden="true">
                     <div class="modal-dialog" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="ModalLabelmsg">Message:</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                           </div>
                           <div class="modal-body"><p class="small text-center text-muted my-5">
                              <?php echo isset($_SESSION['success']) ? ($_SESSION['success']) : '';?> </p>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <!-- <button type="button" class="btn btn-primary">Save changes</button>-->
                           </div>
                        </div>
                     </div>
                  </div>
                  <?php
                  if(isset($_SESSION['success'])){
                    echo "<script type='text/javascript'>
                    $(document).ready(function(){
                    $('#Modalmsg').modal('show');
                    });
                    </script>";
                    unset ($_SESSION['success']);
                    } 
                  ?>
<!--modification ici-->
                    </div>
                  </div>

               
            </div>
        </div>
         <!-- /.content-wrapper -->
      </div>
      <!-- /#wrapper -->
      <!-- Scroll to Top Button-->
      <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
      </a>
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