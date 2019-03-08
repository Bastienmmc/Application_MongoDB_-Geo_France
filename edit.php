<?php
   require_once ('composer.php');
   if (empty($_SESSION['login'])) {
    header("HTTP/1.1 303 See Other");
   	header("Location:login.php");
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
      <title>Page d'édition</title>
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
         <a class="navbar-brand mr-1" href="index.php">Cartographie avec LDNR</a>
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
                  <li class="breadcrumb-item active">Editeurs</li>
               </ol>
               <!-- Area Chart Example-->
               <!--modification ici-->
               <div class="card mb-3">
                  <div class="card-header">
                     <i class="fas fa-table"></i>
                     <?php echo isset($formVal['editnew']) ? htmlspecialchars($formVal['editnew']) : 'Modification des informations de la ville :'?>
                  </div>
                  
                  <div class="card-body">
                    <div class="row">
                      <div class="offset-2 col-8">

               <?php
                $ajoutecp=isset($_POST['ajoutecp'])? $_POST['ajoutecp']     :0;

                  if (isset($_POST['AjouterCp'])) {
                    //echo 'ui;';
                    $ajoutecp++;
                    unset($_POST['AjouterCp']);
                  }
            
                 // verification de la validation du formulaire
                  try{ 

                  
                  if (isset($_POST['submit'])) {

                      $id = intval($_POST['id']); // récupère la valeur de l'id en integer

                      $msg=[];
                      // Vérification de la saisie cp
                      if (isset($_POST['arrayCp'])) {
                        $cps = $_POST['arrayCp'];

                        $strCps='';
                        $flag=true;
                        for ($i=0; $i<count($cps); $i++){
                          //echo $cps[$i];
                          if (!controlecp($cps[$i])&& !empty($cps[$i])){
                            $msg[]= sprintf("<div class='alert alert-danger' role='alert'>Code postal incorrect numero %d n'est pas correct.</div>", $i+1);
                            $flag=false;
                          }else{
                            $strCps.= $cps[$i];
                            if(isset($cps[$i+1])&&!empty($cps[$i+1])){
                              $strCps.= '-';
                            }
                            //$strCps.= )?'-':'';  //preparo il salvataggio

                          }
                        }
                        if($flag==true){
                          if(strcmp ( $strCps , (string)$_POST['currentcp'])!=0){
                            $rec  = new MongoDB\Driver\BulkWrite;
                            $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
                            $arrayval=['cp'=>$strCps]; // valeur à modifier
                            $rec->update(['_id' =>$id], ['$set' =>$arrayval], ['multi' => false, 'upsert' => false]);
                            $result = $manager->executeBulkWrite($dbname.'.'.$collname_v, $rec, $writeConcern);
                            $ajoutecp=0;
                            
                            $msg[]= '<div class="alert alert-success" role="alert">La modification du code postal est exécuté.</div>';
                           
                          }
                  
                            

                        }
                    } else {
                      $msg[]= '<div class="alert alert-warning" role="alert">Veuillez saisir un code postal valide.</div>';
                    }

                      // Vérification de la saisie pop
                      if (!empty($_POST['population'])) {
                          if (!controlepop($_POST['population'])){
                            $msg[]= '<div class="alert alert-primary" role="alert">Population incorrect./div>';
                          } else {
                              $rec  = new MongoDB\Driver\BulkWrite;
                              $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
                              $arrayval=['pop'=>$_POST['population']]; // valeur à modifier
                              $rec->update(['_id' =>$id], ['$set' =>$arrayval], ['multi' => false, 'upsert' => false]);
                              $result = $manager->executeBulkWrite($dbname.'.'.$collname_v, $rec, $writeConcern);
                              if (!$result->getInsertedCount()) {
                                $msg[]= '<div class="alert alert-success" role="alert">La modification de la population est exécuté.</div>';
                              } else {
                                $msg[]= '<div class="alert alert-primary" role="alert">Echec de la modification de la population.</div>';
                              }
                          }
                      } else {
                        $msg[]= '<div class="alert alert-warning" role="alert">Veuillez saisir une population valide.</div>';
                      }
                  }
                }catch(Exception $e) {
                      $msg=$e->getMessage();
                      $msg[]= sprintf("<div class=\"alert alert-success\" role=\"danger\">%s</div>", htmlspecialchars($msg) );
                  exit;
                }
                  function controlecp ($cp) {
                      if(!preg_match('#^[0-9]{5,5}$#',$cp)){
                          return false;
                       }
                       return true;
 
                  }
                  
                  function controlepop ($pop) {
                      if(!preg_match('#^[0-9]{1,7}$#',$pop)){
                          return false;
                       }
                      return true;
                          
                  }

                  // récupération de la variable get:id
                  $idVille = isset($_GET['id'])? intval($_GET['id']) : 1;
                  
                  // test de l'id
                  if (!empty($idVille)) {
                      // récupérer les infos de la ville dans la bdd
                      $filtre = ['_id' => (int)$idVille]; // obligé l'integer
                               
                      $options = ['projection' => ['_id' => 0]];    // on veut tout sauf les identifiants
                      $query = new MongoDB\Driver\Query($filtre, $options);
                      $ville = $manager->executeQuery($dbname.'.'.$collname_v, $query); // = geo_france.ville
                  
                      // Afficher les infos à l'écran
                  
                      if (!$ville->isDead()) { // si la query rapporte un résultat alors on imprime
                          $ville=$ville->toArray()[0];
                         // echo "<p>Vous modifiez la ville : ".$ville->{'nom'}.", code postal : ".$ville->{'cp'}."<br></p>";
                      } else {
                          echo "<p>erreur</p>";
                      }
                  } else {
                    $msg[]= '<div class="alert alert-warning" role="alert">L\'id n\'existe pas ou n\'est pas conforme.</div>';
                  }
                  
                
                  ?>

               <!-- Formulaire de modification CP et Pop -->
               <form action="" method="post">
               <?php
                  printf( "<h4>Vous modifiez la ville : <strong>%s</strong> </h4><br>\n" ,  htmlspecialchars($ville->{'nom'}) ) ;
                  printf( "<p>Code postal : %s </p>\n" ,  htmlspecialchars($ville->{'cp'})) ;
                  if(isset($ville->{'pop'})){
                     printf( "<p>Population : %s </p><br>\n" ,  htmlspecialchars($ville->{'pop'})) ;
                  }
                 
                   echo "<h5>Mise à jour du code postal: </h5>\n" ;
                          echo "<div class='row'>\n";
                          $cps= explode ( '-' , $ville->{'cp'});
                      
                          $i=0;
                          for ($y=0 ; $y <  count($cps)+$ajoutecp; $y++){
                            $val;
                            if (isset($_POST['arrayCp'][$y])&& !empty($_POST['arrayCp'][$y])){
                              $val=$_POST['arrayCp'][$y];
                            }else{
                              if(isset($cps[$y])){
                                $val=$cps[$y];
                              }else{
                                $val='';
                              }
                            }
                            printf ("<input type=\"number\" name='arrayCp[]' maxlength=\"5\" class=\"form-control col-sm-4 col-md-4 col-lg-2  inputCap\" value='%s'><br/><br/>",  
                            htmlspecialchars($val));
                            }
                            echo "</div>\n";
                          printf("<input type='hidden' name='ajoutecp' value='%d'  class='form-control'>", $ajoutecp);
                          printf("<input type='hidden' name='currentcp' value='%s'  class='form-control'>", htmlspecialchars($ville->cp));
                          echo '<input type="submit" class="btn btn-primary" name="AjouterCp" value="Ajouter un Code Postal">  ';
                          echo ' <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">Modifier le Code Postal</button>';
                     
                     ?>
                     
                     <br/>
                     <br/><h5>Mise à jour de la population : </h5>
                     <input type="number" name="population" class="form-control">
                  
                  <input type="hidden" name="id" value="<?php echo htmlspecialchars($idVille) ?>"  class="form-control">
                  <br>
                  <!-- Button trigger modal -->
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                  Valider
                  </button>
                  <p></p>
                
                  <a href="index.php"><button class="btn btn-primary">Nouvelle Recherche</button></a><br>
                  <!-- Modal -->
                  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                     <div class="modal-dialog" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                           </div>
                           <div class="modal-body">
                              Êtes-vous sur de vouloir valider ces modifications ? 
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Retour</button>
                              <input type="submit" class="btn btn-primary" name="submit" value="Valider"> 
                              <br>
                           </div>
                        </div>
                     </div>
                  </div>
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
                  </div>
               <!--modification ici-->
            </div>
            <!-- /.container-fluid -->
            
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
               <span>Cartographie chez LDNR 2019</span>
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