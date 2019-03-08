<?php
   require_once ('composer.php');
   
   if (empty($_SESSION['login']) || $_SESSION['usertype'] != 'admin') {
    header("HTTP/1.1 303 See Other");    
   	header("Location:login.php");
   	exit();
   }
   else {
   	header('content-type: text/html; charset=utf-8');
   }
   
   
   
   $id = isset($_GET['id']) ? ($_GET['id']) : '0';
   
   if (isset($id) && $id != 0) { //si modifico
   
   	// récupérer les infos du lieux dans la bdd
   
   	$filtre = ['_id' => new MongoDB\BSON\ObjectID($id) ];
   
   	// echo  $filtre;
   
   	$options = ['projection' => ['_id' => 0]]; // on veut tout sauf les identifiants
   	$query = new MongoDB\Driver\Query($filtre, $options);
   	$rows = $manager->executeQuery($dbname . '.' . $tableuser, $query);
   	if (!$rows->isDead()) {
   		$rows = $rows->toArray() [0];
   		$formVal = (array)$rows;
   		$formVal = array(
   			'id' => $id,
   			'editnew' => 'Edit:',
   			'modifPassword' => 'disabled',
   			'checked' => 'checked'
   		) + $formVal;
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
                     <a href="#">Panneau de contrôle</a>
                  </li>
                  <li class="breadcrumb-item active">Gestion des utilisateurs</li>
               </ol>
               <!-- DataTables Example -->
               <div class="card mb-3">
                  <div class="card-header">
                     <i class="fas fa-users"></i>
                     Utilisateurs enregistrés:
                  </div>
                  <div class="card-body">
                     <div class="table-responsive">
                        <?php
                           $array=preparaTable($dbname,  $tableuser, $manager);
                           //$errMsg= $array[1];
                           //$array=$array[0];
                           foreach($array as $arr) {
                             echo $arr;
                           }
                           ?>
                     </div>
                  </div>
               </div>
               <!-- DataTables Example -->
               <div class="card mb-3">
                  <div class="card-header">
                     <i class="fas fa-user-plus"></i>
                     <?php echo isset($formVal['editnew']) ? htmlspecialchars($formVal['editnew']) : 'Ajouter un utilisateur:'?>
                  </div>
                  <div class="card-body">
                     <div class="table-responsive">
                        <!--
                           <div class="container">-->
                        <form action="./adminuser_add.php" method="post" id="formEdit">
                           <div class="row">
                              <!--<div class="card-header">Login</div>-->
                              <div class="col-sm-12 offset-sm-0 col-md-12 offset-md-0 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
                                 <div class="card-header">
                                    <?php echo isset($formVal['editnew']) ? htmlspecialchars($formVal['editnew']) : 'Nouveau:'?>
                                 </div>
                              </div>
                              <div class="col-sm-12 offset-sm-0 col-md-12 offset-md-0 col-lg-5 offset-lg-1 col-xl-4 offset-xl-2">
                                 <div class="card-body">
                                    <div class="form-group">
                                       <div class="form-label-group">
                                          <input type="text" name="name" id="inputName" class="form-control" placeholder="Nom" value="<?php echo isset($formVal['name']) ? htmlspecialchars($formVal['name']) : ''?>"
                                             required="required" autofocus="autofocus">
                                          <label for="inputName">Nom</label>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <div class="form-label-group">
                                          <input type="text" name="username" id="inputUsername" class="form-control" placeholder="Username"
                                             value="<?php echo isset($formVal['username']) ? htmlspecialchars($formVal['username']) : ''?>"
                                             required="required">
                                          <label for="inputUsername">Nom d'utilisateur</label>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <div class="form-label-group">
                                          <input class="" type="radio" name="usertype" value="admin" id="inputUsertypeAdmin" class="form-control"
                                             <?php echo (isset($formVal['usertype']) && $formVal['usertype']=='admin' ) ? "checked=\"
                                                checked\"" : '' ;?> > Administrateur
                                       </div>
                                       <div class="form-label-group">
                                          <input class="" type="radio" name="usertype" value="edit" id="inputUsertypeEditeur" class="form-control"
                                             <?php echo ((isset($formVal['usertype']) && $formVal['usertype']=='edit' ) ? "checked=\"
                                                checked\"" : '' );?>> Editeur
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <!--col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-0-->
                              <div class="col-sm-12 offset-sm-0 col-md-12 offset-md-0 col-lg-5 offset-lg-0 col-xl-4 offset-xl-0">
                                 <div class="card-body">
                                    <!-- <form action="" method="post" id="formLogin" >-->
                                    <div class="form-group">
                                       <div class="form-label-group">
                                          <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address"
                                             value="<?php echo isset($formVal['email']) ? htmlspecialchars($formVal['email']) : '';?>"
                                             required="required" autofocus="autofocus">
                                          <label for="inputEmail">Adresse Email</label>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <div class="form-label-group">
                                          <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password"
                                             <?php echo isset($formVal['modifPassword']) ? 'disabled' : '' ;?>
                                             value="<?php echo isset($formVal['password']) ? htmlspecialchars($formVal['password']) : '';?>"
                                             required="required">
                                          <label for="inputPassword">Mot de passe</label>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <div class="form-label-group">
                                       <input type="checkbox" name="checkboxpass" id="checkboxpass" value="false" <?php echo
                                          isset($formVal['checked']) ? '' : "checked=\" checked\"";?>
                                          <?php echo	isset($formVal['checked']) ? '' : "disabled";?> > Modifier le mot de passe
                                       <input type='hidden' name='id' id='id' value="<?php echo isset($formVal['id']) ? htmlspecialchars($formVal['id']) : 0;?>" />
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-sm-12 offset-sm-0 col-md-12 offset-md-0 col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
                              <div class="card-body">
                                 <a class="btn btn-primary" href="javascript:{}" onclick="document.getElementById('formEdit').submit();"> 
                                  <?php echo isset($formVal['editnew']) ? 'Modifier' : 'Créer'?> </a>
                              </div>
                           </div>
                     </div>
                  </div>
                  </form>
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
                              <?php echo (isset($_SESSION['msgUpdate']) ? ($_SESSION['msgUpdate']) : '');?> </p>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <!-- <button type="button" class="btn btn-primary">Save changes</button>-->
                           </div>
                        </div>
                     </div>
                  </div>
                  <?php
                     if(isset($_SESSION['msgUpdate'])){
                       echo "<script type='text/javascript'>
                       $(document).ready(function(){
                       $('#Modalmsg').modal('show');
                       });
                       </script>";
                     
                       unset ($_SESSION['msgUpdate']);
                     
                     } 
                     
                     ?>
                  <script>
                     // deux functions simple en js
                     
                     document.getElementById("checkboxpass").onclick = function() {

                     document.getElementById("inputPassword").value='';
                     document.getElementById("inputPassword").disabled=false;
                     document.getElementById("checkboxpass").value=true;
                     document.getElementById("checkboxpass").disabled = true;
                     
                     }
                     
                  </script>
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
