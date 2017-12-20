<?php
/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

require('../db_config.php');

// Get the scoreboard from database
try {    
    $stmt = $dbh->prepare('SELECT game_users.username, game_scores.score, game_scores.id FROM game_scores
        JOIN game_users ON game_scores.user_id = game_users.id ORDER BY game_scores.score DESC LIMIT 10');
    $stmt->execute();
} catch (PDOException $e) {
    // Throw an error
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array(message => $e->getMessage())));
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
print json_encode($result);
