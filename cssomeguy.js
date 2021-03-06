/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * cssomeguy implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * cssomeguy.js
 *
 * cssomeguy user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo", "dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
    function (dojo, declare) {
        return declare("bgagame.cssomeguy", ebg.core.gamegui, {
            constructor: function () {
                console.log('cssomeguy constructor');

                // Here, you can init the global variables of your user interface
                // Example:
                // this.myGlobalValue = 0;

            },

            /*
                setup:
                
                This method must set up the game user interface according to current game situation specified
                in parameters.
                
                The method is called each time the game interface is displayed to a player, ie:
                _ when the game starts
                _ when a player refreshes the game page (F5)
                
                "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
            */

            setup: function (gamedatas) {
                console.log("Starting game setup");

                // Setting up player boards
                dojo.query('.fa.fa-star').removeClass('fa fa-star').addClass('counter_icon vp_icon');
                this.counters = {};
                for (let [playerId, player] of Object.entries(gamedatas.players)) {
                    dojo.place(this.format_block('jstplPeg', {
                        pegType: 'turn_tracker',
                        playerId: playerId,
                        color: player.color
                    }), 'tiles');
                    this.placeOnObject(`peg_turn_tracker_${playerId}`, 'current_turn_tracker_' + player.turnOrder);

                    if (player.personality != null) {
                        dojo.place(this.format_block('jstplPeg', {
                            pegType: 'personality',
                            playerId: playerId,
                            color: player.color
                        }), 'tiles');
                        this.placeOnObject(`peg_personality_${playerId}`, 'personality_' + player.personality);
                    }

                    // TODO: Setting up players boards if needed
                    let playerBoardDiv = $('player_board_' + playerId);
                    dojo.place(this.format_block('jstplPlayerBoard', { playerId, color: player.color }), playerBoardDiv);

                    let playerCounter = {};
                    playerCounter.cowboys = new ebg.counter();
                    playerCounter.cowboys.create('cowboy_count_' + playerId);
                    playerCounter.cowboys.setValue(player.cowboys);

                    playerCounter.money = new ebg.counter();
                    playerCounter.money.create('money_count_' + playerId);
                    playerCounter.money.setValue(player.money);

                    playerCounter.revolverTokens = new ebg.counter();
                    playerCounter.revolverTokens.create('revolver_token_count_' + playerId);
                    playerCounter.revolverTokens.setValue(player.revolverTokens);

                    playerCounter.roads = new ebg.counter();
                    playerCounter.roads.create('road_count_' + playerId);
                    playerCounter.roads.setValue(player.roads);

                    playerCounter.propertyTiles = new ebg.counter();
                    playerCounter.propertyTiles.create('property_tile_count_' + playerId);
                    playerCounter.propertyTiles.setValue(player.propertyTiles);

                    this.counters[playerId] = playerCounter;

                    if (player.personality == gamedatas.personalityIds.sheriff && player.isUsingPersonalityBenefit == 0) {
                        dojo.place(this.format_block('jstplSheriffCowboy', {
                        }), 'inventory_' + playerId);
                    }
                }

                // TODO: Set up your game interface here, according to "gamedatas"
                dojo.place(this.format_block('jstplRoundTrackerToken', {}), 'tiles');
                this.placeOnObject('round_tracker_token', 'initial_round_tracker_position');

                for (let token of gamedatas.tokens) {
                    if (token.locationType == 'action') {
                        if (token.type == 'cowboy') {
                            dojo.place(this.format_block('jstplCowboy', {
                                cowboyId: token.id,
                                playerId: token.ownerId,
                                color: this.gamedatas.players[token.ownerId].color
                            }), `${token.locationType}_square_${token.locationId}`);
                        }
                        else if (token.type == 'tile') {
                            dojo.place(this.format_block('jstplCityTile', {
                                cityTileId: token.id,
                                cityTileTypeId: token.typeId
                            }), 'tiles');
                            this.placeOnObject('city_tile_' + token.id, token.locationType + '_square_' + token.locationId);
                        }
                    }
                    else if (token.locationType == 'city') {
                        if (token.type == 'parcel') {
                            dojo.place(this.format_block('jstplParcel', {
                                parcelId: token.locationId,
                                playerId: token.ownerId,
                                color: this.gamedatas.players[token.ownerId].color
                            }), 'tiles');
                            this.placeOnObject(`parcel_${token.locationId}_${token.ownerId}`, token.locationType + '_square_' + token.locationId);
                        }
                        else if (token.type == 'tile') {
                            dojo.place(this.format_block('jstplCityTile', {
                                cityTileId: token.id,
                                cityTileTypeId: token.typeId
                            }), 'tiles');
                            this.placeOnObject(token.locationType + '_tile_' + token.id, token.locationType + '_square_' + token.locationId);
                        }
                        else if (token.type == 'cowboy') {
                            dojo.place(this.format_block('jstplCowboy', {
                                cowboyId: token.id,
                                playerId: token.ownerId,
                                color: this.gamedatas.players[token.ownerId].color
                            }), `${token.locationType}_square_${token.locationId}`);
                        }
                    }
                }

                // Setup game notifications to handle (see "setupNotifications" method below)
                this.setupNotifications();

                console.log("Ending game setup");
            },


            ///////////////////////////////////////////////////
            //// Game & client states

            // onEnteringState: this method is called each time we are entering into a new game state.
            //                  You can use this method to perform some user interface changes at this moment.
            //
            onEnteringState: function (stateName, args) {
                console.log('Entering state: ' + stateName);

                switch (stateName) {
                    case 'initialParcelClaims':
                    case 'settlerChosen':
                        this.connectClass('city_square', 'onclick', 'onClaimParcel');
                        break;

                    case 'choosePersonality':
                        this.connectClass('personality', 'onclick', 'onChoosePersonality');
                        break;

                    case 'placeCowboy':
                        this.connectClass('action_square', 'onclick', 'onPlaceCowboy');
                        this.connectClass('city_square', 'onclick', 'onPlaceCowboy');
                        break;

                    /* Example:
                    
                    case 'myGameState':
                    
                        // Show some HTML block at this game state
                        dojo.style( 'my_html_block_id', 'display', 'block' );
                        
                        break;
                   */


                    case 'dummmy':
                        break;
                }
            },

            // onLeavingState: this method is called each time we are leaving a game state.
            //                 You can use this method to perform some user interface changes at this moment.
            //
            onLeavingState: function (stateName) {
                console.log('Leaving state: ' + stateName);

                switch (stateName) {
                    case 'initialParcelClaims':
                    case 'settlerChosen':
                        this.disconnectAll();
                        break;
                    case 'choosePersonality':
                        this.disconnectAll();
                        break;
                    case 'placeCowboy':
                        this.disconnectAll();
                        break;

                    /* Example:
                    
                    case 'myGameState':
                    
                        // Hide the HTML block we are displaying only during this game state
                        dojo.style( 'my_html_block_id', 'display', 'none' );
                        
                        break;
                   */


                    case 'dummmy':
                        break;
                }
            },

            // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
            //                        action status bar (ie: the HTML links in the status bar).
            //        
            onUpdateActionButtons: function (stateName, args) {
                console.log('onUpdateActionButtons: ' + stateName);

                if (this.isCurrentPlayerActive()) {
                    switch (stateName) {
                        case 'grocerChosen':
                            this.addActionButton('money', _('Receive $8'), 'onChooseGrocerBenefit');
                            this.addActionButton('income', _('Decide later to receive $8 or double your income from 1 building type'), 'onChooseGrocerBenefit');
                            break;
                        case 'captainChosen':
                            for (let [moneyAmount, cowboyAmount] of Object.entries(args.payOptions)) {
                                let msg = '';
                                if (cowboyAmount == 1) {
                                    msg = dojo.string.substitute(_('Spend $${moneyAmount} for ${cowboyAmount} cowboy'), {
                                        moneyAmount: moneyAmount,
                                        cowboyAmount: cowboyAmount
                                    });
                                }
                                else {
                                    msg = dojo.string.substitute(_('Spend $${moneyAmount} for ${cowboyAmount} cowboys'), {
                                        moneyAmount: moneyAmount,
                                        cowboyAmount: cowboyAmount
                                    });
                                }
                                this.addActionButton('spend_' + moneyAmount, msg, 'onChooseCaptainBenefit');
                            }
                            break;
                        /*               
                                         Example:
                         
                                         case 'myGameState':
                                            
                                            // Add 3 action buttons in the action status bar:
                                            
                                            this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                                            this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                                            this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                                            break;
                        */
                    }
                }
            },

            ///////////////////////////////////////////////////
            //// Utility methods

            /*
            
                Here, you can defines some utility methods that you can use everywhere in your javascript
                script.
            
            */


            ///////////////////////////////////////////////////
            //// Player's action

            /*
            
                Here, you are defining methods to handle player's action (ex: results of mouse click on 
                game objects).
                
                Most of the time, these methods:
                _ check the action is possible at this game state.
                _ make a call to the game server
            
            */

            onClaimParcel: function (e) {
                // Preventing default browser reaction
                dojo.stopEvent(e);
                if (!this.checkAction('claimParcel'))
                    return;

                let parcelId = e.target.id.split('_')[2];

                this.ajaxcall("/cssomeguy/cssomeguy/claimParcel.html", {
                    lock: true,
                    parcelId: parcelId
                }, this, function (result) { }, function (is_error) { });
            },

            onChoosePersonality: function (e) {
                // Preventing default browser reaction
                dojo.stopEvent(e);
                if (!this.checkAction('choosePersonality'))
                    return;

                let personalityId = e.target.id.split('_')[1];

                this.ajaxcall("/cssomeguy/cssomeguy/choosePersonality.html", {
                    lock: true,
                    personalityId: personalityId
                }, this, function (result) { }, function (is_error) { });
            },

            onChooseGrocerBenefit: function (e) {
                // Preventing default browser reaction
                dojo.stopEvent(e);
                if (!this.checkAction('chooseGrocerBenefit'))
                    return;

                let isReceivingMoney = e.target.id == 'money';
                this.ajaxcall("/cssomeguy/cssomeguy/chooseGrocerBenefit.html", {
                    lock: true,
                    isReceivingMoney: isReceivingMoney,
                }, this, function (result) { }, function (is_error) { });
            },

            onChooseCaptainBenefit: function (e) {
                // Preventing default browser reaction
                dojo.stopEvent(e);
                if (!this.checkAction('chooseCaptainBenefit'))
                    return;

                let amountSpent = e.target.id.split('_')[1];
                this.ajaxcall("/cssomeguy/cssomeguy/chooseCaptainBenefit.html", {
                    lock: true,
                    amountSpent: amountSpent,
                }, this, function (result) { }, function (is_error) { });
            },

            onPlaceCowboy: function (e) {
                // Preventing default browser reaction
                dojo.stopEvent(e);
                if (!this.checkAction('placeCowboy'))
                    return;

                let locationType = e.currentTarget.id.split('_')[0];
                let locationId = e.currentTarget.id.split('_')[2];
                this.ajaxcall("/cssomeguy/cssomeguy/placeCowboy.html", {
                    lock: true,
                    locationType: locationType,
                    locationId: locationId,
                }, this, function (result) { }, function (is_error) { });
            },

            /* Example:
            
            onMyMethodToCall1: function( evt )
            {
                console.log( 'onMyMethodToCall1' );
                
                // Preventing default browser reaction
                dojo.stopEvent( evt );
    
                // Check that this action is possible (see "possibleactions" in states.inc.php)
                if( ! this.checkAction( 'myAction' ) )
                {   return; }
    
                this.ajaxcall( "/cssomeguy/cssomeguy/myAction.html", { 
                                                                        lock: true, 
                                                                        myArgument1: arg1, 
                                                                        myArgument2: arg2,
                                                                        ...
                                                                     }, 
                             this, function( result ) {
                                
                                // What to do after the server call if it succeeded
                                // (most of the time: nothing)
                                
                             }, function( is_error) {
    
                                // What to do after the server call in anyway (success or failure)
                                // (most of the time: nothing)
    
                             } );        
            },        
            
            */


            ///////////////////////////////////////////////////
            //// Reaction to cometD notifications

            /*
                setupNotifications:
                
                In this method, you associate each of your game notifications with your local method to handle it.
                
                Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                      your cssomeguy.game.php file.
            
            */
            setupNotifications: function () {
                console.log('notifications subscriptions setup');

                dojo.subscribe('parcelClaimed', this, 'notifParcelClaimed');
                dojo.subscribe('personalityChosen', this, 'notifPersonalityChosen');
                dojo.subscribe('allPersonalitesChosen', this, 'notifResetCurrentTurnTracker');
                dojo.subscribe('updateResources', this, 'notifUpdateResources');
                dojo.subscribe('cowboyPlaced', this, 'notifCowboyPlaced');

                // TODO: here, associate your game notifications with local methods

                // Example 1: standard notification handling
                // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );

                // Example 2: standard notification handling + tell the user interface to wait
                //            during 3 seconds after calling the method in order to let the players
                //            see what is happening in the game.
                // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
                // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
                // 
            },

            // TODO: from this point and below, you can write your game notifications handling methods
            notifParcelClaimed: function (notif) {
                let parcelId = notif.args.parcelId;
                let playerId = this.getActivePlayerId();
                dojo.place(this.format_block('jstplParcel', {
                    parcelId: parcelId,
                    playerId: playerId,
                    color: this.gamedatas.players[playerId].color
                }), 'tiles');

                let parcelDivId = `parcel_${parcelId}_${playerId}`;
                this.placeOnObject(parcelDivId, 'overall_player_board_' + playerId);
                this.slideToObject(parcelDivId, 'city_square_' + parcelId).play();

                this.counters[playerId].propertyTiles.incValue(-1);
            },

            notifPersonalityChosen: function (notif) {
                let playerId = this.getActivePlayerId();
                let personalityId = notif.args.personalityId;
                let resourcesChanged = notif.args.resourcesChanged;

                dojo.place(this.format_block('jstplPeg', {
                    pegType: 'personality',
                    playerId: playerId,
                    color: this.gamedatas.players[playerId].color
                }), 'tiles');

                let pegDivId = `peg_personality_${playerId}`;
                this.placeOnObject(pegDivId, 'overall_player_board_' + playerId);
                this.slideToObject(pegDivId, 'personality_' + personalityId).play();

                let sheriffId = this.gamedatas.personalityIds.sheriff;
                if (personalityId == sheriffId) {
                    let sheriffCowboyWrapperDivId = 'sheriff_cowboy_wrapper';
                    dojo.place(`<div id="${sheriffCowboyWrapperDivId}" style="position: relative"></div>`, 'inventory_' + playerId);
                    dojo.place(this.format_block('jstplSheriffCowboy', {
                    }), sheriffCowboyWrapperDivId);

                    this.placeOnObject('sheriff_cowboy', 'personality_' + sheriffId);
                    this.slideToObject('sheriff_cowboy', sheriffCowboyWrapperDivId).play();
                }

                for (let [resource, amount] of Object.entries(resourcesChanged))
                    this.counters[playerId][resource].incValue(amount);
            },

            notifResetCurrentTurnTracker: function (notif) {
                let newTurnOrder = notif.args.newTurnOrder;
                for (let i = 1; i <= this.gamedatas.playerorder.length; i++)
                    this.slideToObject(`peg_turn_tracker_${newTurnOrder[i]}`, 'current_turn_tracker_' + i).play();
            },

            notifUpdateResources: function (notif) {
                let playerId = this.getActivePlayerId();
                let resourcesChanged = notif.args.resourcesChanged;
                for (let [resource, amount] of Object.entries(resourcesChanged))
                    this.counters[playerId][resource].incValue(amount);
            },

            notifCowboyPlaced: function (notif) {
                let playerId = this.getActivePlayerId();
                let { cowboyId, locationType, locationId } = notif.args;

                let cowboyDivId = `cowboy_${cowboyId}_${playerId}`;
                dojo.place(`<div id="${cowboyDivId}_wrapper" class="cowboy_wrapper"></div>`, `${locationType}_square_${locationId}`);
                dojo.place(this.format_block('jstplCowboy', {
                    cowboyId: cowboyId,
                    playerId: playerId,
                    color: this.gamedatas.players[playerId].color
                }), `${cowboyDivId}_wrapper`);


                this.placeOnObject(cowboyDivId, 'overall_player_board_' + playerId);
                this.slideToObject(cowboyDivId, `${cowboyDivId}_wrapper`).play();

                this.counters[playerId].cowboys.incValue(-1);
            },

            /*
            Example:
            
            notif_cardPlayed: function( notif )
            {
                console.log( 'notif_cardPlayed' );
                console.log( notif );
                
                // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
                
                // TODO: play the card in the user interface.
            },    
            
            */
        });
    });
