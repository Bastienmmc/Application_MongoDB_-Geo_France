<?php
session_start();
unset($_SESSION["login"]);
unset($_SESSION["usertype"]);
unset($_SESSION["loggedin_time"]);

$url = "index.php";

header("Location:$url");






        