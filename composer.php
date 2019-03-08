<?php
    require_once('connection.php');
    require_once('validatorValeurs.php');
    require_once('user.php'); 
    
    
    @session_start();

/*
    $_SESSION['login']='fabio.pomarelli@gmail.com';
    $_SESSION['name']='fabio ATTENZIONE';
    $_SESSION['usertype'] = 'admin';
    $_SESSION['loggedin_time'] = time();*/

    
    if(isset($_SESSION["login"]) && isLoginSessionExpired()) {
        header("Location:logout.php");
    }    

   
