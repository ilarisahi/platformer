<?php
/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

require('../db_config.php');
if (!session_id()) {
    session_start();
}

// Delete a score if the user is an admin
if ($_POST && isset($_SESSION['userRole']) && $_SESSION['userRole'] == 'admin') {
    $scoreId = $_POST['scoreId'];

    try {
        $stmt = $dbh->prepare('DELETE FROM game_scores WHERE id = ?');
        $stmt->execute(array($scoreId));
    } catch (PDOException $e) {
        // Throw an error
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array(message => $e->getMessage())));
    }

    print json_encode(array(msg => $scoreId . ' deleted.'));
}
