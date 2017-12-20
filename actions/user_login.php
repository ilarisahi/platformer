<?php
/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

require('../db_config.php');

if ($_POST) {
    $username = $_POST['username'];    
    $password = $_POST['password'];

    // Fetch the user from database
    try {
        $stmt = $dbh->prepare('SELECT * FROM game_users WHERE username = ? AND fb_id IS NULL LIMIT 1');
        $stmt->execute(array($username));
    } catch (PDOException $e) {
        sendError($e, 'User select failed');
    }

    $user = $stmt->fetch();

    // Check if passwords match
    if (!password_verify($password, $user['hash'])) {
        sendError($e, 'Password verification failed');
    }

    $dateNow = date('Y.m.d H:i:s');

    // Update last_login value
    try {
        $stmt = $dbh->prepare('UPDATE game_users SET last_login = ? WHERE id = ?');
        $stmt->execute(array($dateNow, $user['id']));
    } catch (PDOException $e) {
        sendError($e, 'Last login update failed');
    }

    if (!session_id()) {
        session_start();
    }

    // Update session variables
    $_SESSION['username'] = $user['username'];
    $_SESSION['userId'] = $user['id'];    
    $_SESSION['userRole'] = $user['role'];

    print json_encode(array(id => $user['id']));
}

// Error thrower helper
function sendError($e, $msg) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array(message => $msg)));
}
