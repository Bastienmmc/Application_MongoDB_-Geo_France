<?php
/**controlla validità email**/
function validatorEmail($str){
    $motif='/^
    (       #bla.ble.ble@bli.blo.blo.domain
        [a-zA-Z][a-zA-Z0-9]*    #bla
        ([_.-][a-zA-Z0-9]+)*    #ble
        
    )@(                         # @
        [a-z][a-z0-9]*          #bli
        ([.-][a-z0-9]+)*        #blo
        \.([a-z])*)     #domain
        $/x';
    if (preg_match($motif, $str, $regs)) {
        return  $str;
    }else return NULL;  

}

/**controlla validità numero telefono**/
function validatorTelephone($str){
    $motif='%^([0-9]{2})([\./_ :-]?)([0-9]{2})\2([0-9]{2})\2([0-9]{2})\2([0-9]{2})$%';
    if (preg_match($motif, $str, $regs)) {
        return  $str;
    } else return NULL; 
}

/**controlla validità numero username**/
function validatorUsername($str){
   // ^(?=.{8,20}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$
    $motif='%^(?=.{5,20}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$%';
    if (preg_match($motif, $str, $regs)) {
        return  $str;
    } else return NULL; 
}

/**controlla validità numero pqssword**/
function validatorPassword($str){
    //Minimum eight and maximum 100 characters, at least one uppercase letter, one lowercase letter, one number and one special character:
     $motif='%^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!\%*?&])[A-Za-z\d@!\%*?&]{8,100}$%';
     $motif='%^(?=.*[A-Za-z])(?=.*\\d)[A-Za-z\\d]{5,}$%';
     if (preg_match($motif, $str, $regs)) {
         return  $str;
     } else return NULL; 
 }
 
 function hashPassword($str){
      return password_hash($str, PASSWORD_DEFAULT);
 }


 /**controlla validità region**/
function validatorRegion($str){
    // ^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$
     //$motif='%^[a-zA-Z]+(([\',. -][a-zA-Z ])?[a-zA-Z]*)*$%';
    // if (preg_match($motif, $str, $regs)) {
         return  $str;
  //   } else return NULL; 
 }