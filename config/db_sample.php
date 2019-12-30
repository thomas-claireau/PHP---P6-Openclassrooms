<?php

$HOST = ''; // le host de votre projet
$DB_NAME = ''; // le nom de la base de donnée
$DB_USER = ''; // l'identifiant d'accès
$DB_PASS = ''; // le mot de passe d'accès
$DB_DSN = "mysql:host={$HOST};dbname={$DB_NAME}";

define('DB_DSN', $DB_DSN);
define('DB_USER', $DB_USER);
define('DB_PASS', $DB_PASS);

define('DB_OPTIONS', array(PDO::ATTR_DEFAULT_FETCH_MODE =>
    PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
