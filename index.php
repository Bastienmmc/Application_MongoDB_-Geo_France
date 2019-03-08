<?php
   require_once ('composer.php');
   	header('content-type: text/html; charset=utf-8');
   ?>
<!DOCTYPE html>
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="">
      <meta name="author" content="">
      <title>Accueil</title>
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
                  <li class="breadcrumb-item active">Recherche</li>
               </ol>
               <!-- Area Chart Example-->
               <div class="card mb-3">
                  <div class="card-header">
                     <i class="fas fa-search"></i>
                     <?php echo isset($formVal['editnew']) ? htmlspecialchars($formVal['editnew']) : 'Recherche par ville:'?>
                  </div>

                  <div class= row>
                    <div class="offset-2 col-8">
                      <!--modification ici-->
                      <?php
                      try{
                        
                        
                        $choix=(isset($_POST['choix']))? htmlspecialchars($_POST['choix']):0;
                        $regionObjet=[];
                        //je prepare les regions
                        $filtre = [];
                        $options = ['projection' => [], // on veut tout sauf les identifiants
                        ];
                        $query = new MongoDB\Driver\Query($filtre, $options);
                        $regions = $manager->executeQuery($dbname . '.' .$collname_r, $query);

                        if ($regions->isDead()) { //se non c'è un user
                          //throw new Exception('Veuillez entrer un ville valide deregion non trouvee');
                        } else {
                          $select='<select name="choix" class="form-control"><option value="" selected="selected">Selectionnez une région</option>';
                          $selectV="<option value=\"%s\">%s</option>";
                          foreach ($regions as $document) {
                              $select.=sprintf($selectV ,$document->{'_id'},$document->{'nom'}  );
                              $regionObjet[]= ['id'=>  $document->{'_id'}, 'nom'=>  $document->{'nom'}];
                          }
                          $select.='</select>';
                        }

                        $nom_ville=(isset($_POST['nom_ville'])) ?$_POST['nom_ville']:'';
                        $nom_departement=(isset($_POST['nom_departement']))? $_POST['nom_departement']:'';

                        if(!empty($nom_ville) &&       is_null(validatorRegion($nom_ville))){
                          $nom_ville='';
                          throw new Exception('Nom de ville incorrect.');
                        }
                        if(!empty($nom_departement) &&              is_null(validatorRegion($nom_departement))){
                          $nom_departement='';
                          throw new Exception('Nom de département incorrect.'); 
                        }else{


                        }


                        if(isset($_POST['valider']) && empty($nom_ville)){
                          $nom_ville='';
                          throw new Exception('Nom de ville requis');
                        }else{

                        }


                      }

                      catch(Exception $e) {
                        $nom_departement='';
                        $nom_ville='';
                            $_SESSION['msgUpdate']=$e->getMessage();
                             echo "<script type='text/javascript'>
                             $(document).ready(function(){
                             $('#Modalmsg').modal('show');
                             });
                             </script>";
                           } 




                      $form=<<<EOF
                      <form method="post" action="">
                            <br>
                            Ville<br/>
                            <input type="text" class='form-control' name="nom_ville" value="%s"/><br/>
                            <br/> 
                            Département<br/>
                            <input type="text" name="nom_departement" class='form-control' value="%s" /><br/>
                            <br/>
                            %s       
                            <br>
                            <br>
                            <input type="submit" value="valider" class="btn btn-primary" name="valider">
                            <br><br>
                          </form>
EOF;

                      printf($form, $nom_ville, $nom_departement, $select );

                      if (isset($_POST['sub_ville']) && $_POST['sub_ville'] !== ''){
                        $_SESSION['idview']= $_POST['radio'];
                          echo ("<script>setTimeout(function(){location.href ='./view.php'}, 100);</script>");
                      }
                      if (isset($_POST['valider']) && $_POST['nom_ville'] !== '') {
                        //echo $nom_ville;    // On crée notre array pour le match de l'aggregation


                          $nom_ville = ucfirst(strtolower($nom_ville));


                          $matchVille = ['nom' => $nom_ville];
                          $matchDep=[];
                          if (!empty($nom_departement)){
                            $nom_departement = ucwords(strtolower($nom_departement));
                            $matchDep['dep.nom']=  $nom_departement;
                          }
                          if (intval($choix)!=0){
                              $matchDep['dep._id_region'] = intval($choix);
                          }   
                          if(empty($matchDep)){
                            $command = new MongoDB\Driver\Command([
                              'aggregate' => $collname_v,
                              'pipeline' => [
                                  ['$match' => $matchVille],
                                  ['$lookup' => [
                                    'from' => $collname_d,
                                    'localField' => '_id_dept',
                                    'foreignField' => '_id',
                                    'as' => 'dep'],], 
                                ],
                              'cursor' => new stdClass
                            ]);
                          } else {
                            $command = new MongoDB\Driver\Command([
                              'aggregate' => $collname_v,
                              'pipeline' => [
                                  ['$match' => $matchVille],
                                  ['$lookup' => [
                                    'from' => $collname_d,
                                    'localField' => '_id_dept',
                                    'foreignField' => '_id',
                                    'as' => 'dep'],],
                                  ['$match' => $matchDep], 
                                ],
                              'cursor' => new stdClass
                            ]);
                          }
                          $cursor = $manager->executeCommand($dbname, $command)->toArray();
                          if (count($cursor) === 0) {
                            echo '<div class="alert alert-warning" role="alert">
                            Aucune ville ne correspond. Vérifiez les informations.
                          </div>';
                            //echo "Aucune ville ne correspond. Vérifiez les informations.";
                          } 
                          if (count($cursor) === 1) {
                            $cursor = $cursor[0];
                            $_SESSION['idview']=  $cursor->_id;
                            echo ("<script>setTimeout(function(){location.href ='./view.php'}, 10);</script>");
                            exit();
                          } 
                          if (count($cursor) > 1) {  //plusieur villes
                            //echo "<br><h4>Plusieurs résultats trouvés, veuillez préciser :</h4>"    ;
                            echo '<div class="alert alert-primary" role="alert">
                            Plusieurs résultats trouvés, veuillez préciser :
                          </div>';
                                echo '<form method="post" action="">';
                              foreach($cursor as $ville) {
                                  $selectedRegion = null;
                                  foreach($regionObjet as $currentRegion) {
                                      if ($ville->dep[0]->_id_region === $currentRegion['id']) {
                                          $selectedRegion = $currentRegion['nom'];
                                          break;
                                      }
                                  }             
                                  $fieldset=<<<EOF
                                  <br>
                                  <div class="row border ">
                                  <div class="col-1">
                                  <input type="radio" class='form-control' name="radio" value="%s">   
                                  </div>         
                                  <div class="col-8">                Ville :%s <br>          
                                  Departement : %s <br>
                                  Region : %s <br>
                                  </div>
                                  </div>
                                  
                                  
EOF;

                                  printf( $fieldset,$ville->_id, $ville->nom, $ville->dep[0]->nom,$selectedRegion );
                                } 
                                
                                echo'<br><input type="submit" class="btn btn-primary" name="sub_ville" value="Valider"></form><p></p>';
                          }
                        }
                      ?>
                      
                    </div>
                  </div>
            </div>

             <!-- Modal -->
             <div class="modal fade" id="Modalmsg" tabindex="-1" role="dialog" aria-labelledby="ModalLabelmsg" aria-hidden="true">
                           <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabelmsg">Attention</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                 </div>
                                 <div class="modal-body"><p class="small text-center text-muted my-5">
                                    <?php echo isset($_SESSION['msgUpdate']) ? ($_SESSION['msgUpdate']) : '';unset($_SESSION['msgUpdate']);?> </p>
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <!-- <button type="button" class="btn btn-primary">Save changes</button>-->
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