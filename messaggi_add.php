<?php
require_once ('composer.php');
//|| $_SESSION['usertype'] != 'admin'
/*if (empty($_SESSION['login']) ) {
	header("HTTP/1.1 303 See Other");    
	header("Location: index.php");
	exit();
}*/

$_SESSION['user_id']='Fabio';
$_SESSION['contentMsg']='Prova';
$_SESSION['typeMsg']='Update';
$_SESSION['table']='villes';
$_SESSION['old_val']='10';
$_SESSION['new_val']='15';



$type='';
$flag = '';
$err='';
try {
    if (isset($_SESSION['contentMsg'])){

        
            $user_id = isset($_SESSION['user_id']) ? ($_SESSION['user_id']) : NULL;
	        $contentMsg = isset($_SESSION['contentMsg']) ? ($_SESSION['contentMsg']) : NULL;
            $typeMsg = isset($_SESSION['typeMsg']) ? ($_SESSION['typeMsg']) : NULL;
            $table = isset($_SESSION['table']) ? ($_SESSION['table']) : NULL;
            $old_val = isset($_SESSION['old_val']) ? ($_SESSION['old_val']) : NULL;
            $new_val = isset($_SESSION['new_val']) ? ($_SESSION['new_val']) : NULL;





            unset($_SESSION['user_id']);
            unset ($_SESSION['contentMsg']);
            unset ($_SESSION['typeMsg']);
            unset ($_SESSION['table']);
            unset ($_SESSION['old_val']);
            unset ($_SESSION['new_val']);

            echo $user_id. '<br/>';
            echo $contentMsg. '<br/>';
            echo $typeMsg .'<br/>';
            echo $table .'<br/>';
            echo $old_val. '<br/>';
            echo $new_val .'<br/>';


            if (is_null($user_id) || is_null($contentMsg) || (isset($typeMsg)) || (isset($table)) || (isset($old_val)) || (isset($new_val))) {
               if(is_null($user_id)) {
                    $flag.= "\nIncorrect userid<br/>";
                }
        
                if (is_null($contentMsg)) {
                    $flag.= "\nIncorrect contentMsg<br/>";
                }
        
                if (is_null($typeMsg)) {
                    $flag.= "\nIncorrect typeMsg<br/>";
                }
                if (is_null($table)) {
                    $flag.= "\nIncorrect table<br/>";
                }
                if (is_null($old_val)) {
                    $flag.= "\nIncorrect old_Val<br/>";
                }
                if (is_null($new_val)) {
                    $flag.= "\nIncorrect new_val<br/>";
                }
                
              
                throw new Exception($flag);
            }
            else {  echo 'ok'.$flag;
                $rec = new MongoDB\Driver\BulkWrite;
                $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        
                
                $arrayval = ['_user_id' => $user_id, 'message' => $contentMsg, 'typeMsg' => $typeMsg, 'table' => $table, 'old_val' => $old_val, 'new_val' => $new_val];
                
                //if ($id != 0) { //update
                    $type='Update';
                    $rec->insert($arrayval);
                   // $rec->insert(['_id' => new MongoDB\BSON\ObjectID($id) ], ['$set' => $arrayval], ['multi' => false, 'upsert' => false]);
                }
                /*else { //insert
                    $filtre = ['username' => $username];
                    $options = ['projection' => ['_id' => 0]];
                    $query = new MongoDB\Driver\Query($filtre, $options);
                    $rows = $manager->executeQuery($dbname . '.' . $tableuser, $query);
                    if (!$rows->isDead()) {
                        throw new Exception('This username already exists. ');
                    }
        
                    $filtre = ['email' => $email];
                    $options = ['projection' => ['_id' => 0]];
                    $query = new MongoDB\Driver\Query($filtre, $options);
                    $rows = $manager->executeQuery($dbname . '.' . $tableuser, $query);
                    if (!$rows->isDead()) {
                        throw new Exception('This email already exists. ');
                    }
        
                    $type='Insert';
                    $rec->insert($arrayval);
                }*/
        
                $result = $manager->executeBulkWrite($dbname . '.' . $collname_m, $rec, $writeConcern);
                if (!$result->getInsertedCount()) {
                    $err = ' successfully';
                }
                else {
                    $err = ' unsuccessfully';
                }
            }
            echo 'Qui';
            //$_SESSION['msgUpdate'] = $type . $err;
           // header("Location: adminuser.php");
            exit;








    }
	



catch(Exception $e) {
    echo 'qau';
	//$_SESSION['msgUpdate'] = $e->getMessage();
	//header("Location: adminuser.php");
	exit;
}
