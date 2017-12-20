<!-- CT30A3202 WWW-sovellukset harjoitustyö
     Tekijä: Ilari Sahi
     Päivämäärä: 20.12.2017
-->

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8">
        <title>Extreme Coin Gathering Game</title>
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div id="fb-root"></div>
        <script src="fb_config.js"></script>
        <script>
            'use strict';
            // Load Facebook SDK for JavaScript
            window.fbAsyncInit = () => {
                FB.init({
                appId            : appId,
                autoLogAppEvents : false,
                xfbml            : true,
                version          : 'v2.11'
                });

                var fbStatusChanged = response => {
                    console.log(response);
                    if (response.status === 'connected') {
                        fbLogin();
                    }
                }

                // Subscribe for FB status changes
                FB.Event.subscribe('auth.statusChange', fbStatusChanged);

                <?php
                    if (!session_id()) {
                        session_start();
                    }
                    // Used if userId is not set, but user is logged through FB
                    if (!isset($_SESSION['userId'])) {
                        echo 'function getLoginStatus() {
                                    FB.getLoginStatus(response => {
                                        console.log(response);
                                        if (response.status === "connected") {
                                            fbLogin();
                                        }
                                    });
                                }';
                    }
                ?>

                // Fire up the function if it exists
                if (typeof getLoginStatus === 'function') {
                    console.log('Getting FB login status');
                    getLoginStatus();
                }
            };

            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <div class="container">
            <main>
                <div id="game-menu">
                    <div class="container menu-container">
                        <div class="row menu-row">
                            <div class="game-menu-buttons center-align">
                                <div class="chip" id="last-score">Last score: 0</div>
                                <a class="waves-effect waves-light btn-large main-button blue" id="game-button-play"><i class="material-icons left">send</i>PLAY</a>
                                <a class="waves-effect waves-light btn-large main-button blue" id="game-button-scoreboard"><i class="material-icons left">assessment</i>SCOREBOARD</a>
                                <!-- Show registration/login or logout button depending on user state -->
                                <?php
                                    if (!session_id()) {
                                        session_start();
                                    }
                                    if (isset($_SESSION['userId'])) {
                                        print '<a class="btn-large waves-effect waves-light red darken-4 main-button" id="logout">
                                            <i class="material-icons left">vpn_key</i>Logout</a>';
                                    } else {
                                        print '<a class="waves-effect waves-light btn-large main-button blue" id="login-register-button">
                                            <i class="material-icons left">vpn_key</i>LOGIN/REGISTER</a>';
                                    }
                                ?>        
                            </div>    
                            <div class="card blue center-align" id="scoreboard-card">
                                <i class="material-icons left-align back-button">keyboard_backspace</i>
                                <div class="card-content white-text">Top 10 scoreboard</div>
                                <div class="card-content grey lighten-4">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Username</th>
                                                <th>Score</th>
                                                <!-- Show admin column depending on user role -->
                                                <?php
                                                    if (isset($_SESSION['userRole']) && $_SESSION['userRole'] == 'admin') {
                                                        echo '<th>Admin</th>';
                                                    }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody id="score-body">
                                        </tbody>
                                    </table>
                                    <div class="row" id="score-loader">
                                        <div class="progress blue">
                                            <div class="indeterminate blue lighten-2"></div>
                                        </div>
                                    </div>                            
                                </div>
                                <div class="card-action grey lighten-4">
                                    <a id="print-button" class="btn-floating waves-effect waves-light blue"><i class="material-icons">print</i></a>
                                    <a id="pdf-button" class="btn-floating waves-effect waves-light blue"><i class="material-icons">picture_as_pdf</i></a>
                                </div>
                            </div>
                            <div class="card blue center-align" id="login-register-card">
                                <i class="material-icons left-align back-button">keyboard_backspace</i>
                                <div class="card-content white-text">
                                Login or register to reach the top of the scoreboard!
                                </div>
                                <div class="card-tabs">
                                    <ul class="tabs tabs-fixed-width tabs-transparent">
                                        <li class="tab"><a class="active" href="#facebook-tab">Facebook</a></li>
                                        <li class="tab"><a class="active" href="#login-tab">Login</a></li>
                                        <li class="tab"><a href="#register-tab">Register</a></li>
                                    </ul>
                                </div>
                                <div class="card-content grey lighten-4">
                                <div id="facebook-tab">
                                    <div class="fb-login-button"
                                        data-max-rows="1"
                                        data-size="large"
                                        data-button-type="continue_with"
                                        data-show-faces="false"
                                        data-auto-logout-link="false"
                                        data-use-continue-as="true"
                                        data-scope="public_profile,email"></div>
                                </div>
                                <div id="login-tab">
                                    <form id="login-form" method="post" action="./actions/user_login.php">
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <i class="material-icons prefix">account_box</i>
                                                <input id="login-username" name="username" type="text" class="validate">
                                                <label for="login-username">Username</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                        <div class="input-field col s12">
                                            <i class="material-icons prefix">lock</i>
                                            <input id="login-password" name="password" type="password" class="validate">
                                            <label for="login-password">Password</label>
                                        </div>
                                    </div>
                                    <button class="btn waves-effect waves-light blue darken-2" type="submit" id="login-submit" name="submit">Login
                                        <i class="material-icons right">send</i>
                                    </button>
                                    </form>
                                </div>
                                <div id="register-tab">
                                    <form id="register-form" method="post" action="./actions/user_register.php">
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <i class="material-icons prefix">account_box</i>
                                            <input id="register-username" name="username" type="text" class="validate">
                                            <label for="register-username">Username</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <i class="material-icons prefix">lock</i>
                                            <input id="register-password" name="password" type="password" class="validate">
                                            <label for="register-password">Password</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <i class="material-icons prefix">lock</i>
                                            <input id="register-password-confirm" name="password-confirm" type="password" class="validate">
                                            <label for="register-password-confirm">Confirm password</label>
                                        </div>
                                    </div>
                                    <button class="btn waves-effect waves-light blue darken-2" type="submit" id="register-submit" name="submit">Register
                                        <i class="material-icons right">send</i>
                                    </button>
                                    </form>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="game-hud">
                    <h3 class="game-score">SCORE: 0</p>
                </div>
                <div id="game"></div>
            </main>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.debug.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.3.2/jspdf.plugin.autotable.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/phaser/2.6.2/phaser.min.js"></script>
        <script src="user.js"></script>
        <script src="game.js"></script>
    </body>
</html>
