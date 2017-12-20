/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

'use strict';
$(() => {

    // Handle login submit
    $('#login-form').submit(event => {
        let validForm = true;
        event.preventDefault();

        // Check if inputs are filled
        if (!$('#login-username').val()) {
            $('#login-username').addClass('invalid');
            validForm = false;
        }

        if (!$('#login-password').val()) {
            $('#login-password').addClass('invalid');
            validForm = false;
        }

        if (validForm) {
            $.ajax({
                url: './actions/user_login.php',
                method: 'POST',
                data: $('#login-form').serialize(),
                beforeSend: () => {
                    $('#login-submit').prop('disabled', true);
                }
            })
                .done(data => {
                    console.log(data);

                    // Reload for convenience
                    location.reload();
                })
                .fail((jqXHR, textStatus, errorThrown) => {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);

                    // Display an error toast
                    Materialize.toast('Invalid login credentials!', 5000, 'rounded red');
                    $('#login-username').addClass('invalid');
                    $('#login-password').addClass('invalid');
                })
                .always(() => {
                    $('#login-submit').prop('disabled', false);
                });
        }
    });

    // Handle registration submit
    $('#register-form').submit(event => {
        let validForm = true;
        event.preventDefault();

        // Check if inputs are filled and passwords match
        if (!$('#register-username').val()) {
            $('#register-username').addClass('invalid');
            validForm = false;
        }

        if (!$('#register-password').val()) {
            $('#register-password').addClass('invalid');
            validForm = false;
        }

        if (!$('#register-password-confirm').val()) {
            $('#register-password-confirm').addClass('invalid');
            validForm = false;
        }

        if (!($('#register-password').val() == $('#register-password-confirm').val())) {
            $('#register-password').addClass('invalid');
            $('#register-password-confirm').addClass('invalid');
            validForm = false;
        }

        if (validForm) {
            $.ajax({
                url: './actions/user_register.php',
                method: 'POST',
                data: $('#register-form').serialize(),
                beforeSend: () => {
                    $('#register-submit').prop('disabled', true);
                }
            })
                .done(data => {
                    console.log(data);

                    // Reload for convenience
                    location.reload();
                })
                .fail((jqXHR, textStatus, errorThrown) => {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);

                    // Display an error toast
                    Materialize.toast('Choose another username!', 8000, 'rounded red');
                    $('#register-username').addClass('invalid');
                })
                .always(() => {
                    $('#register-submit').prop('disabled', false);
                });
        } else {
            // Display an error toast
            Materialize.toast('Invalid registration input. Passwords have to match and the fields can\'t be empty.', 5000, 'rounded red');
        }
    });

    // Handle logout
    $('#logout').click(event => {

        // Logout from Facebook
        FB.logout(response => {
            console.log(response);
        });

        // Logout from server
        $.ajax({
            url: './actions/user_logout.php',
            method: 'GET',
        })
            .done(data => {
                console.log(data);

                // Reload for convenience
                location.reload();
            })
            .fail((jqXHR, textStatus, errorThrown) => {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);

                // Display an error toast
                Materialize.toast('Logout failed! Try again.', 5000, 'rounded red');
            })
    });
});

// Handle Facebook login
function fbLogin() {
    console.log('Welcome!  Fetching your information.... ');

    // Fetch user information
    FB.api('/me', function(response) {
        console.log(response);
        console.log('Successful login for: ' + response.name);

        // Register/login user in local database
        if (response.name !== null && response.id !== null) {
            $.ajax({
                url: './actions/fb_login.php',
                method: 'POST',
                data: {
                    username: response.name,
                    fb_id: response.id
                }
            })
                .done(data => {
                    console.log(data);

                    // Reload for convenience
                    location.reload();
                })
                .fail((jqXHR, textStatus, errorThrown) => {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);

                    // Display an error toast
                    Materialize.toast('Facebook login failed! Try again.', 5000, 'rounded red');
                })
        }
    });
}
