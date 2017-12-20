<?php
/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

// Database connection configuration
date_default_timezone_set('Europe/Helsinki');
setlocale(LC_TIME, 'fi_FI');

$servername = 'localhost';
$database = 'database';
$username = 'username';
$password = 'password';

try {
    $dbh = new PDO('mysql:host=' . $servername . ';dbname=' . $database . ';charset=utf8', $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
