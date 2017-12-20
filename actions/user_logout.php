<?php
/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

if (!session_id()) {
    session_start();
}

// Destroy current session
if (session_destroy()) {
    print json_encode(array(msg => 'Session destroyed.'));
}
