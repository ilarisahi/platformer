<?php
/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

require('../db_config.php');
if (!session_id()) {
    session_start();
}

// Insert a new score to the database
if ($_POST && isset($_SESSION['userId'])) {
    $score = $_POST['score'];
    $dateNow = date('Y.m.d H:i:s');

    try {
        $stmt = $dbh->prepare('INSERT INTO game_scores (user_id, score, date_time) VALUES (?, ?, ?)');
        $stmt->execute(array($_SESSION['userId'], $score, $dateNow));
    } catch (PDOException $e) {
        // Throw an error
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array(message => $e->getMessage())));
    }

    print json_encode(array(id => $dbh->lastInsertId(), score => $score));
}
