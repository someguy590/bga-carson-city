<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * cssomeguy implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * states.inc.php
 *
 * cssomeguy game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

if (!defined('STATE_END_GAME')) { // ensure this block is only invoked once, since it is included multiple times
    define('INITIAL_PARCEL_CLAIMS', 2);
    define('INITIAL_PARCEL_CLAIMED', 3);
    define('CHOOSE_PERSONALITY', 4);
    define('PERSONALITY_CHOSEN', 5);
    define('PLACE_COWBOY', 6);
    define('COWBOY_PLACED', 7);

    define('GROCER_CHOSEN', 8);
    define('SETTLER_CHOSEN', 9);
    define('CAPTAIN_CHOSEN', 10);

    define('STATE_END_GAME', 99);
}


$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array("" => INITIAL_PARCEL_CLAIMS)
    ),

    INITIAL_PARCEL_CLAIMS => [
        'name' => 'initialParcelClaims',
        'description' => clienttranslate('${actplayer} must claim a parcel of land, it can include a mountain or the center of Carson City'),
        'descriptionmyturn' => clienttranslate('${you} must claim a parcel of land, it can include a mountain or the center of Carson City'),
        'type' => 'activeplayer',
        'possibleactions' => ['claimParcel'],
        'transitions' => ['parcelClaimed' => INITIAL_PARCEL_CLAIMED]
    ],

    INITIAL_PARCEL_CLAIMED => [
        'name' => 'initialParcelClaimed',
        'description' => '',
        'descriptionmyturn' => '',
        'type' => 'game',
        'action' => 'stInitialParcelClaimed',
        'transitions' => ['nextParcelClaim' => INITIAL_PARCEL_CLAIMS, 'choosePersonality' => CHOOSE_PERSONALITY]
    ],

    CHOOSE_PERSONALITY => [
        'name' => 'choosePersonality',
        'description' => clienttranslate('${actplayer} must choose a personality'),
        'descriptionmyturn' => clienttranslate('${you} must choose a personality'),
        'type' => 'activeplayer',
        'possibleactions' => ['choosePersonality'],
        'transitions' => [
            'personalityChosen' => PERSONALITY_CHOSEN,
            'grocerChosen' => GROCER_CHOSEN,
            'settlerChosen' => SETTLER_CHOSEN,
            'captainChosen' => CAPTAIN_CHOSEN
        ]
    ],

    PERSONALITY_CHOSEN => [
        'name' => 'personalityChosen',
        'description' => '',
        'descriptionmyturn' => '',
        'type' => 'game',
        'action' => 'stPersonalityChosen',
        'transitions' => ['nextPersonalityChoice' => CHOOSE_PERSONALITY, 'placeCowboy' => PLACE_COWBOY]
    ],

    GROCER_CHOSEN => [
        'name' => 'grocerChosen',
        'description' => clienttranslate('${actplayer} must choose a benefit from ${personality_name}'),
        'descriptionmyturn' => '', // will have buttons to describe options
        'type' => 'activeplayer',
        'args' => 'argGrocerChosen',
        'possibleactions' => ['chooseGrocerBenefit'],
        'transitions' => ['personalityChosen' => PERSONALITY_CHOSEN]
    ],

    SETTLER_CHOSEN => [
        'name' => 'settlerChosen',
        'description' => clienttranslate('${actplayer} must claim a parcel of land'),
        'descriptionmyturn' => clienttranslate('${you} must claim a parcel of land'),
        'type' => 'activeplayer',
        'possibleactions' => ['claimParcel'],
        'transitions' => ['parcelClaimed' => PERSONALITY_CHOSEN]
    ],
    
    CAPTAIN_CHOSEN => [
        'name' => 'captainChosen',
        'description' => clienttranslate('${actplayer} must choose a benefit from ${personality_name}'),
        'descriptionmyturn' => '', // will have buttons to describe options
        'type' => 'activeplayer',
        'args' => 'argCaptainChosen',
        'possibleactions' => ['chooseCaptainBenefit'],
        'transitions' => ['personalityChosen' => PERSONALITY_CHOSEN]
    ],

    PLACE_COWBOY => [
        'name' => 'placeCowboy',
        'description' => clienttranslate('${actplayer} must place a cowboy or pass'),
        'descriptionmyturn' => clienttranslate('${you} must place a cowboy or pass'),
        'type' => 'activeplayer',
        'possibleactions' => ['placeCowboy', 'pass'],
        'transitions' => ['cowboyPlaced' => COWBOY_PLACED]
    ],

    COWBOY_PLACED => [
        'name' => 'cowboyPlaced',
        'description' => '',
        'descriptionmyturn' => '',
        'type' => 'game',
        'action' => 'stCowboyPlaced',
        'transitions' => ['nextPlayer' => PLACE_COWBOY]
    ],

    /*
    Examples:
    
    2 => array(
        "name" => "nextPlayer",
        "description" => '',
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,   
        "transitions" => array( "endGame" => 99, "nextPlayer" => 10 )
    ),
    
    10 => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must play a card or pass'),
        "descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
        "type" => "activeplayer",
        "possibleactions" => array( "playCard", "pass" ),
        "transitions" => array( "playCard" => 2, "pass" => 2 )
    ), 

*/

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);
