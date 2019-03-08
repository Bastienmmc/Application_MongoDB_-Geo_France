<?php

$dsn='mongodb://localhost:27017';
$dbname = 'geo_france';
$manager = new MongoDB\Driver\Manager($dsn);
$tableuser= 'users';
$collname_d = 'departements';
$collname_v = 'villes';
$collname_r = 'regions';
$collname_m = 'message';
?>