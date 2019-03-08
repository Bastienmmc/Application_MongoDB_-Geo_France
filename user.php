<?php

function isLoginSessionExpired()
{
	$login_session_duration = 60 * 60; //60minutes
	$current_time = time();
	if (isset($_SESSION['loggedin_time']) and isset($_SESSION["login"])) {
		if (((time() - $_SESSION['loggedin_time']) > $login_session_duration)) {
			return true;
		}
	}
	return false;
}


//funzione per preparare la tabella della user admin table
function preparaTable($dbname , $collname ,$manager,  $filtre = [],$options = ['projection' => ['password' => 0]]){
  $table=[];

    try {
    //$filtre = [];
    //$options = ['projection' => ['password' => 0], // on veut tout sauf les identifiants
    //];
    $query = new MongoDB\Driver\Query($filtre, $options);
    $rows = $manager->executeQuery($dbname . '.' . $collname, $query);
    if ($rows->isDead()) { //se non c'è un user
        throw new Exception('Vérifiez vos identifiants de connexion.'); 
    }
    else { //se ho trovato l'user
        $table[]=("<table class=\"table table-bordered\" id=\"dataTable\">\n") ;
        $i = true;
        foreach($rows as $doc) { // parcours du curseur retourné par la recherche
            $arraydoc = (array)$doc;

            if ($i) {
                $table[]=("<thead>\n");
                $table[]=("<tr>\n");
                $table[]= (("<th>". implode("</th><th>",array_keys($arraydoc))) . "</th>");
                $table[]=( "<th>Select a maintenance user</th>\n");
                $table[]=( "</tr>\n");
                $table[]=("</thead>\n");
                $i = false;
                $table[]=("</tbody>\n");
            }
            

            
            $table[]=( "<tr>\n");
            $table[]=( '<td>'.implode('</td><td>',  $arraydoc) . '</td>');

            $editdelete = <<<EOFORM
                <td>
                <a class='editlink' href='?id=0'>New</a> 
                    |
                <a class='editlink' href='?id=%s'>Edit</a> 
                    |
                    <a class='editlink' onClick ='return confirm("Do you want to remove this record?");' href='./adminuser_delete.php?id=%s'>Delete</td>
EOFORM;
            $table[]=(sprintf($editdelete,  htmlspecialchars($arraydoc['_id']), htmlspecialchars( $arraydoc['_id'])));
            $table[]=("</tr>\n");
            
        }  
        $table[]=("</tbody>\n");
        $table[]=("</table>\n");
    }
    return $table;
}

catch(Exception $e) {
    $msg= $e->getMessage();
    return array(sprintf('<div class="alert alert-danger" role="alert">Erreur %s.</div>', $msg ));
}  
}


//funzione per preparare la tabella departament 
function preparaTableDep($dbname , $collname ,$manager,  $filtre = [],$options = ['projection' => ['_id' => 0]]){
    $table=[];
  
      try {
      //$filtre = [];
      //$options = ['projection' => ['password' => 0], // on veut tout sauf les identifiants
      //];
      $query = new MongoDB\Driver\Query($filtre, $options);
      $rows = $manager->executeQuery($dbname . '.' . $collname, $query);
      if ($rows->isDead()) { //se non c'è un user
          throw new Exception('Vérifiez vos identifiants de connexion.'); 
      }
      else { //se ho trovato l'user

       // $table[]= "<table class=\"table table-bordered dataTable\" id=\"dataTable\" width=\"100%\" cellspacing=\"0\" role=\"grid\" aria-describedby=\"dataTable_info\" style=\"width: 100%;\">";
          //$table[]=("<table class=\"table table-bordered\" id=\"dataTable\" width=\"100%\" cellspacing=\"0\">") ;
          $i = true;
          foreach($rows as $doc) { // parcours du curseur retourné par la recherche
              $arraydoc = (array)$doc;
              if (!isset($arraydoc['pop'])){   //se la popolazione non è presente
                $lon =array_pop ( $arraydoc );
                $lat= array_pop ( $arraydoc );
                $arraydoc['pop']='';
                $arraydoc['lat']=$lat;
                $arraydoc['lon']=$lon;
         
              } 


              if ($i) {
                //faccio il thead
                $table[]=("<thead>\n");
                $table[]=("<tr>\n");
                $table[]= (("<th>". implode("</th><th>",array_keys($arraydoc))) . "</th>");
                $table[]=( "</tr>\n");
                $table[]=("</thead>\n");
    
    
                //apro la balise body
                $table[]=("</tbody>\n");
                $i = false;
            }

              
              $table[]=( "<tr>\n");
              $table[]=( '<td>'.implode('</td><td>',  $arraydoc) . '</td>');
  
              /*$editdelete = <<<EOFORM
                  <td>
                  <a class='editlink' href='?id=0'>New</a> 
                      |
                  <a class='editlink' href='?id=%s'>Edit</a> 
                      |
                      <a class='editlink' onClick ='return confirm("Do you want to remove this record?");' href='./adminuser_delete.php?id=%s'>Delete</td>
EOFORM;
              $table[]=(sprintf($editdelete,  htmlspecialchars($arraydoc['_id']), htmlspecialchars( $arraydoc['_id'])));*/
              $table[]=("</tr>\n");
              
          }  
        $table[]=("</tbody>\n");
          $table[]=("</table>\n");
      }
      return $table;
  }
  
  catch(Exception $e) {
      $msg= $e->getMessage();
      return array(sprintf('<div class="alert alert-danger" role="alert">Erreur %s.</div>', $msg ));
  }  
  }
  