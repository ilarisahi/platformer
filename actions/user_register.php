<?php
/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

require('../db_config.php');

if ($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordConfirm = $_POST['password-confirm'];
    $dateNow = date('Y.m.d H:i:s');

    // Check if passwords match
    if ($password != $passwordConfirm) {
        // Throw an error
        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array(message => 'Passwords don\'t match.', code => 400)));
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user to the database
    try {
        $stmt = $dbh->prepare('INSERT INTO game_users (username, role, hash, created, last_login) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(array($username, 'user', $hash, $dateNow, $dateNow));
    } catch (PDOException $e) {
        // Throw an error
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array(message => $e->getMessage())));
    }

    $userId = $dbh->lastInsertId();

    if (!session_id()) {
        session_start();
    }

    // Update session variables
    $_SESSION['username'] = $username;
    $_SESSION['userId'] = $userId;    
    $_SESSION['userRole'] = 'user';

    print json_encode(array(id => $userId));
}
