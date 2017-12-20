<?php
/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

require('../db_config.php');

// Handle Facebook login
if ($_POST) {
    $username = $_POST['username'];    
    $fb_id = $_POST['fb_id'];

    // Check if this user is already created
    try {
        $stmt = $dbh->prepare('SELECT * FROM game_users WHERE username = ? AND fb_id = ? LIMIT 1');
        $stmt->execute(array($username, $fb_id));
    } catch (PDOException $e) {
        sendError($e, 'User select failed');
    }

    $user = $stmt->fetch();
    $dateNow = date('Y.m.d H:i:s');

    if (!session_id()) {
        session_start();
    }

    // If user is already created, update the last_login value
    if ($user != null) {
        // Update session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['userId'] = $user['id'];
        $_SESSION['userRole'] = $user['role'];

        try {
            $stmt = $dbh->prepare('UPDATE game_users SET last_login = ? WHERE id = ?');
            $stmt->execute(array($dateNow, $user['id']));
        } catch (PDOException $e) {
            sendError($e, 'Last login update failed');
        }
        
        print json_encode(array(id => $user['id']));
    } else {
        // Create a new user for this Facebook user
        try {
            $stmt = $dbh->prepare('INSERT INTO game_users (username, fb_id, role, created, last_login) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute(array($username, $fb_id, 'user', $dateNow, $dateNow));
        } catch (PDOException $e) {
            sendError($e, 'Fb user creation failed');
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

}

// Error thrower helper
function sendError($e, $msg) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array(message => $msg)));
}
