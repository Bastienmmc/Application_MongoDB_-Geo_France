<?php
require_once ('composer.php');
require_once ('Carte.php');
//26362
//1446,

$ville=[];
$contours=[];
//$_SESSION['idview']=1446;
if (isset($_SESSION['idview'])) {    
    $errMsg = [];
    $id= intval($_SESSION['idview']);
   // $_SESSION['idview']=1446;
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
        
          $contours = $rows->contours;
          
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
        $msg='Erreur : ' . $e->getMessage();
        echo ("<script>window.alert($msg);</script>");
        echo ("<script>setTimeout(function(){location.href ='./index.php'}, 2000);</script>");
				exit();
			}
		}
    header("Content-type: image/svg+xml");
    $carte=new Carte($contours, 200,$ville['nom'],$ville['lon'],$ville['lat'] );
    $carte->echoPolygon();
    exit;
