<?php
function modal(){
    return $modal = <<<EOC
               <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h5 class="modal-title" id="exampleModalLabel">Veuillez confirmer</h5>
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                           </button>
                        </div>
                        <div class="modal-body">
                           Êtes-vous sûr de vouloir valider ces modifications ? 
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Retour</button>
                           <input type="submit" class="btn btn-primary" name="submit" value="Valider">
                           <p></p>
                        </div>
                     </div>
                  </div>
               </div>
EOC;
};

function print_formulaire1(){
$form =<<<EOC
    <form action="" method="GET"><br>
    <p>Vous souhaitez:</p>
    <select name="choix_modif" class="form-control">
    <option value="departement" name="departement">Modifier un département</option>
    <option value="region_creer" name="region">Créer une nouvellle région</option>
    <option value="region_modif" name="region">Modifier une région existante</option>
    </select><br>
    <input type="submit" class="btn btn-primary" name="submit" value="Confirmer">
    <p></p>
    </form>
EOC;
return  $form;
}

function print_formulaire2($manager,$dbname,$collname_d){

    $form=[];
    $filtre = [];
    $options = ['projection' => ['nom' => 1, '_id' => 0]]; // on veut que le nom
    $query = new MongoDB\Driver\Query($filtre, $options);
    $rows = $manager->executeQuery($dbname . '.' . $collname_d, $query);
    $form[]= "<form method='post' action='modif_dept.php'>\n<br>";
    $form[]= "<p>Rattacher le département :<br>\n    <select name='dept_a_modif' class='form-control'>\n";
    foreach($rows as $doc)
        { // parcours du curseur retourné par la recherche
        foreach($doc as $key => $val)
            {
                $form[]=sprintf("        <option value='%s'>%s</option>\n", htmlspecialchars($val) , htmlspecialchars($val));
            }
        }    
    $form[]= "  </select> \n<br>A la région :\n";
    $regions = "regions";
    $rows2 = $manager->executeQuery($dbname . '.' . $regions, $query);
    $form[]= "  <select name='region_choisie' class='form-control'>\n";
    foreach($rows2 as $doc)
        { // parcours du curseur retourné par la recherche
        foreach($doc as $key => $val)
            {
                $form[]=sprintf("        <option value='%s'>%s</option>\n", htmlspecialchars($val) , htmlspecialchars($val));
            }
        }    
    $form[]= "  </select>\n</p>\n";
    $form[]= "<input type='submit' class='btn btn-secondary' name='annuler' value='Retour'> ";
    $form[]= "<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#exampleModal'>Valider</button><p></p>";        
    $form[]=modal();        
    $form[]= "<br />\n</form>\n";
   return  $form;
}

function print_formulaire3(){
    $form = <<<EOC
    <p><br>Entrez le nom de la nouvelle région: </p>\n
    <form method='post' action='modif_region_creer.php'>\n
    <input type='text' class='form-control' name='region_cree'><br>\n
    <input type='submit' name='annuler' class='btn btn-secondary' value='Retour'>
    <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#exampleModal'>Valider</button>
    %s    
    </form>
    <p></p>\n
EOC;
return  sprintf($form,modal());
}

function print_formulaire4($manager,$dbname,$collname_d){
    $form=[];
    $form[]= "<p><br>Sélectionnez la région à modifier </h3>\n";
    $form[]= "<form method='post' action='modif_region_nom.php'>\n";        
    $filtre = [];
    $options = ['projection' => ['nom' => 1, '_id' => 0]]; // on veut que le nom
    $query = new MongoDB\Driver\Query($filtre, $options);
    $regions = "regions";
    $rows2 = $manager->executeQuery($dbname . '.' . $regions, $query);
    $form[]="  <select name='region_a_modifier' class='form-control'>\n";
    foreach($rows2 as $doc)
    { // parcours du curseur retourné par la recherche
        foreach($doc as $key => $val)
            {
                $form[]=sprintf("        <option value='%s'>%s</option>\n", htmlspecialchars($val) , htmlspecialchars($val));
            }
    }

    $form[]= "  </select><br>\n";
    $form[]= "<p>Nouveau nom:</p>";        
    $form[]= "<input type='text' class='form-control' name='region_modifiee'><br>\n";
    $form[]= "<input type='submit' name='annuler' class='btn btn-secondary' value='Retour'> ";
    $form[]= "<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#exampleModal'>Valider</button><p></p>";             
    $form[]=modal();
    $form[]= "<br />\n</form>\n";
return  $form;
}
