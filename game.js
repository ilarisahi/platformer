/** CT30A3202 WWW-sovellukset harjoitustyö
 * Tekijä: Ilari Sahi
 * Päivämäärä: 20.12.2017
 */

'use strict';
$(() => {
    var game;

    // Check Canvas compability
    var e = document.createElement('canvas');
    if(!!(e.getContext && e.getContext('2d'))) {
        initGame();
    } else {
        Materialize.toast('Canvas is not supported in your browser.', 10000, 'rounded red');
    }

    // Initialise Phaser game
    function initGame() {
        game = new Phaser.Game('100%', '100%', Phaser.CANVAS, 'game');

        // This state is used when player is in the menus
        var bootState = {
            preload: () => {
                game.load.image('background', './assets/bg.png');
                game.load.image('floorCenter', './assets/grassMid.png');
                game.load.image('floorLeft', './assets/grassLeft.png');
                game.load.image('floorRight', './assets/grassRight.png');
            },
            create: () => {
                for (let i = 0; i < timers.length; i++) {
                    console.log('Clearing timer: ' + i);
                    clearTimeout(timers[i]);
                }
                goFullScreen();
                game.add.tileSprite(0,0, game.width, game.height, 'background');
        
                floors = game.add.group();
                initFloor();
            },
            update: () => {
            },
            resize: () => {
                onResize();
            }
        };

        // Main game state
        var mainState = {
            preload: () => {
                onPreload();
            },
            create: () => {
                onCreate();
            },
            update: () => {
                onUpdate();
            },
            resize: () => {
                onResize();
            }
        };
    
        game.state.add('boot', bootState);
        game.state.start('boot');
        game.state.add('main', mainState);
    }    

    var floors;
    var coins;
    var player;
    var timers = [];
    var score = 0;
    var coinTypes = [];
    var scoreText;

    function onPreload() {
        game.load.image('background', './assets/bg.png');
        game.load.image('floorCenter', './assets/grassMid.png');
        game.load.image('floorLeft', './assets/grassLeft.png');
        game.load.image('floorRight', './assets/grassRight.png');
        game.load.image('coinBronze', './assets/coinBronze.png');
        game.load.image('coinSilver', './assets/coinSilver.png');
        game.load.image('coinGold', './assets/coinGold.png');

        game.load.spritesheet('player', './assets/player.png', 73, 97);

        coinTypes.push('coinBronze');
        coinTypes.push('coinSilver');
        coinTypes.push('coinGold');
    }

    function onCreate() {
        game.physics.startSystem(Phaser.Physics.ARCADE);
        game.input.mouse.capture = true;

        goFullScreen();
        console.log(game.width);
        console.log(game.height);
        game.add.tileSprite(0,0, game.width, game.height, 'background');

        floors = game.add.group();
        floors.enableBody = true;

        coins = game.add.group();
        coins.enableBody = true;

        initFloor();

        player = game.add.sprite(game.width * 0.1, 0, 'player');
        game.physics.arcade.enable(player);
        player.body.bounce.y = 0.2;
        player.body.gravity.y = 300;
        player.animations.add('run', [0, 1, 2, 3, 4, 5], 7, true);
        player.animations.add('jump', [13], 10, true);
        player.animations.play('run');

        game.time.events.loop(Phaser.Timer.SECOND, addCoin, this);

        score = 0;
        $('.game-score').text('SCORE: ' + score);
        for (let i = 0; i < timers.length; i++) {
            console.log('Clearing timer: ' + i);
            clearTimeout(timers[i]);
        }
    }

    function onUpdate() {
        game.physics.arcade.collide(player, floors);
        player.body.x = game.width * 0.1;

        if ((game.input.pointer1.isDown || game.input.activePointer.leftButton.isDown)
            && player.body.touching.down) {
            player.body.velocity.y = -250;
        }

        if (player.y > game.height) {
            showMenu();
        }

        game.physics.arcade.overlap(player, coins, collectCoin, null, this);
    }

    function restartGame() {
        // Reset score
        score = 0;
        $('.game-score').text('SCORE: ' + score);
        game.state.start('main');
        for (let i = 0; i < timers.length; i++) {
            console.log('Clearing timer: ' + i);
            clearTimeout(timers[i]);
        }
    }

    function showMenu() {
        game.state.start('boot');
        addScore(score);
    }

    // Handle new score
    function addScore() {
        $.ajax({
            url: './actions/add_score.php',
            method: 'POST',
            data: { score: score },
            beforeSend: () => {
                $('#game-hud').fadeOut('fast');
                $('#game-menu').css('display', 'flex');
                $('#game-menu').fadeIn('fast');
                $('#last-score').text('Last score: ' + score);
                $('#last-score').fadeIn('fast');
            }
        })
            .done(data => {
                console.log(data);
            })
            .fail((jqXHR, textStatus, errorThrown) => {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            })
    }

    function onResize() {
        game.add.tileSprite(0,0, game.width, game.height, 'background');
    }

    function goFullScreen() {
        game.stage.backgroundColor = '#555555';
        game.scale.pageAlignHorizontally = true;
        game.scale.pageAlignVertically = true;
        game.scale.scaleMode = Phaser.ScaleManager.RESIZE;
    }

    function addCoin() {
        let coinType = coinTypes[Math.floor(Math.random() * coinTypes.length)];
        console.log('Adding coin: ' + coinType);
        let max = game.height - 160;
        let min = game.height - 360;
        let coinHeight = randomNumberBetween(min, max);
        console.log(coinHeight);

        let coin = game.add.sprite(game.width, coinHeight, coinType);
        coin.checkWorldBounds = true;
        game.physics.arcade.enable(coin);
        coin.body.immovable = true;
        coin.body.velocity.x = -200;
        coin.outOfBoundsKill = true;
        coins.add(coin);

        let tween = game.add.tween(coin).to({ y: randomNumberBetween(min, max) }, 2000, Phaser.Easing.Cubic.InOut, true, 0, -1, true);
    }

    function collectCoin(p, c) {
        let multiplier = coinTypes.indexOf(c.key) + 1;
        score += 5 * multiplier;
        c.destroy();

        $('.game-score').text('SCORE: ' + score);
    }

    function addFloor(x, y, position) {
        let floor = game.add.sprite(x, y, position);
        floors.add(floor);
        game.physics.arcade.enable(floor);
        floor.body.immovable = true;
        floor.body.velocity.x = -200;
        floor.checkWorldBounds = true;
        return floor;
    }

    function initFloor() {
        var tempFloors = [];

        tempFloors.push(addFloor(0, game.height - 70, 'floorLeft'));
        var i = 0;
        for (i = 70; i < game.width * 1.5; i += 70) {
            tempFloors.push(addFloor(i, game.height - 70, 'floorCenter'));
        }
        let lastFloor = addFloor(i, game.height - 70, 'floorRight');

        var outOfBoundsCounter = 0;
        lastFloor.events.onEnterBounds.add(() => {
            console.log('Inside bounds!');
            let timeOut = randomNumberBetween(800, 1500);
            timers.push(setTimeout(addFloorPlatform, timeOut));
        }, this);

        lastFloor.events.onOutOfBounds.add(() => {
            console.log('Out of bounds!');
            outOfBoundsCounter++;
            if (outOfBoundsCounter === 2) {
                for (let j = 1; j < tempFloors.length; j++) {
                    tempFloors[j].kill();
                }
            }
        }, this);
    }

    function addFloorPlatform() {
        timers.shift();
        var tempFloors = [];

        let length = Math.floor(Math.random() * 10) + 1;
        console.log('Floor length: ' + length);

        tempFloors.push(addFloor(game.width, game.height - 70, 'floorLeft'));
        var i = 1;
        for (i = 1; i < length + 1; i++) {
            tempFloors.push(addFloor(game.width + i * 70, game.height - 70, 'floorCenter'));
        }
        let lastFloor = addFloor(game.width + i * 70, game.height - 70, 'floorRight');

        var outOfBoundsCounter = 0;
        lastFloor.events.onEnterBounds.add(() => {
            console.log('Inside bounds!');
            let timeOut = randomNumberBetween(800, 1500);
            timers.push(setTimeout(addFloorPlatform, timeOut));
        }, this);

        lastFloor.events.onOutOfBounds.add(() => {
            console.log('Out of bounds!');
            outOfBoundsCounter++;
            if (outOfBoundsCounter === 2) {
                for (let j = 1; j < tempFloors.length; j++) {
                    tempFloors[j].kill();
                }
            }
        }, this);
    }

    function randomNumberBetween(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
    }

    $('#game-hud').fadeOut('fast');

    // Handle game start click transition
    $('#game-button-play').click(() => {        
        game.state.start('main');
        $('#game-menu').fadeOut('fast', () => {
            $('#game-menu').css('display', 'none');
            $('#game-hud').fadeIn('fast');
        });
    });

    // Handle registration click transition
    $('#login-register-button').click(() => {
        $('.game-menu-buttons').fadeOut('fast', () => {
            $('#login-register-card').fadeIn('fast');
        });        
    });

    // Handle back click transition
    $('.back-button').click(event => {
        $(event.target).closest('.card').fadeOut('fast', () => {
            $('.game-menu-buttons').fadeIn('fast');
        });
    })

    // Initialise variable used in PDF scores
    var gamePdfScores = [];

    // Handle scoreboard display
    $('#game-button-scoreboard').click(() => {
        $('.game-menu-buttons').fadeOut('fast', () => {
            $('#scoreboard-card').fadeIn('fast');
            $('#score-body').empty();

            // Get scores from database
            $.ajax({
                url: './actions/get_scoreboard.php',
                method: 'GET',
                beforeSend: () => {
                    $('#score-loader').fadeIn('fast');
                }
            })
                .done(data => {
                    $('#score-loader').fadeOut('fast', () => {
                        let result = JSON.parse(data);

                        // Check if there's an admin column
                        let isAdmin = $("table").find("tr:first th").length > 3;

                        gamePdfScores = [];
                        for (let i = 0; i < result.length; i++) {
                            gamePdfScores.push([i + 1, result[i].username, result[i].score]);

                            let tr = $('<tr>');
                            tr.append($('<td>').text(i + 1));
                            tr.append($('<td>').text(result[i].username));
                            tr.append($('<td>').text(result[i].score));
                            if (isAdmin) {
                                console.log(result[i].id);
                                tr.append($('<td>').append($('<i>').text('delete').addClass('material-icons').attr('data-scoreid', result[i].id)));
                            }
                            $('#score-body').append(tr);
                        }
                    });
                    console.log(data);                    
                })
                .fail((jqXHR, textStatus, errorThrown) => {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                })
        });  
    });

    // Handle score delete click
    $('#score-body').on('click', 'i', event => {
        $.ajax({
            url: './actions/delete_score.php',
            method: 'POST',
            data: {
                scoreId: $(event.target).attr('data-scoreid')
            }
        })
            .done(data => {
                $(event.target).closest('tr').remove();
                console.log(data);
            })
            .fail((jqXHR, textStatus, errorThrown) => {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            })
    });

    // Open print dialog
    $('#print-button').click(event => {
        window.print();
    });

    // Download a PDF file of the scoreboard
    $('#pdf-button').click(event => {
        let columns = ['#', 'Username', 'Score'];

        let doc = new jsPDF('p', 'pt', 'a4');
        doc.autoTable(columns, gamePdfScores);
        doc.save('game-scores.pdf');
    });
});
