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
 * cssomeguy.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */


require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');


class cssomeguy extends Table
{
    function __construct()
    {
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();

        $this->initGameStateLabels([
            'isNextInitialParcelClaimClockwise' => 10
        ]);

        $this->city_tiles_deck = self::getNew("module.common.deck");
        $this->city_tiles_deck->init('city_tiles');
    }

    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "cssomeguy";
    }

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame($players, $options = array())
    {
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach ($players as $player_id => $player) {
            $color = array_shift($default_colors);
            $values[] = "('" . $player_id . "','$color','" . $player['player_canal'] . "','" . addslashes($player['player_name']) . "','" . addslashes($player['player_avatar']) . "')";
        }
        $sql .= implode($values, ',');
        self::DbQuery($sql);
        self::reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
        self::reloadPlayersBasicInfos();

        /************ Start the game initialization *****/

        // Init global values with their initial values
        $this->setGameStateInitialValue('isNextInitialParcelClaimClockwise', 1);

        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // TODO: setup the initial game situation here
        $this->setupCity();
        $this->setupBuildings();

        // turn order
        $sql = "UPDATE player SET turn_order=player_no";
        $this->DbQuery($sql);

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $data = [];

        // Get information about players
        $sql = "SELECT player_id id, player_no playerNumber, player_score score, cowboys, money, revolvers, revolver_tokens revolverTokens, roads, property_tiles propertyTiles, turn_order turnOrder, personality, is_using_personality_benefit isUsingPersonalityBenefit FROM player";
        $data['players'] = $this->getCollectionFromDb($sql);

        // TODO: Gather all information about current game situation (visible by player $current_player_id).
        $data['buildingConstructionSquares'] = $this->city_tiles_deck->getCardsInLocation('building_construction');

        $sql = "SELECT parcel_id parcelId, owner_id ownerId FROM parcels";
        $data['parcels'] = $this->getObjectListFromDB($sql);

        $data['cityTiles'] = $this->city_tiles_deck->getCardsInLocation('city');

        $sql = "SELECT cowboy_id cowboyId, owner_id playerId, location_type locationType, location_id locationId FROM cowboys";
        $data['cowboys'] = $this->getObjectListFromDB($sql);

        $data['personalityIds'] = $this->personality_ids;

        return $data;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Utility functions
    ////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */

    function setupCity()
    {
        $occupied_locations = [];

        // city center
        do {
            $center_x_location = bga_rand(1, 6);
            $center_y_location = bga_rand(1, 6);
            $center_location = $center_y_location * 8 + $center_x_location;
        } while (in_array($center_location, $occupied_locations));
        $occupied_locations[] = $center_location;
        $house_tile_type_id = $this->city_tile_type_ids['house'];
        $sql = "INSERT INTO city_tiles (card_id, card_type, card_type_arg, card_location, card_location_arg) VALUES (0, $house_tile_type_id , -1, 'city', $center_location)";
        $this->DbQuery($sql);

        // roads
        $horizontal_road_row_size = 8;
        $top_horizontal_road_id = $center_y_location * 17 + $center_x_location;
        $bottom_horizontal_road_id = ($center_y_location + 1) * 17 + $center_x_location;
        $left_vertical_road_id = $center_y_location * 17 + $center_x_location + $horizontal_road_row_size;
        $right_vertical_road_id = $left_vertical_road_id + 1;
        $sql = "INSERT INTO roads (road_id) VALUES ($top_horizontal_road_id), ($bottom_horizontal_road_id), ($left_vertical_road_id), ($right_vertical_road_id)";
        $this->DbQuery($sql);

        // mountains
        $mountain_tile_type_id = $this->city_tile_type_ids['mountain'];
        $sql = "INSERT INTO city_tiles (card_type, card_type_arg, card_location, card_location_arg) VALUES ";
        $values = [];
        for ($i = 0; $i < $this->city_tiles[$this->city_tile_type_ids['mountain']]['count']; $i++) {
            do {
                $mountain_x_location = bga_rand(1, 6);
                $mountain_y_location = bga_rand(1, 6);
                $mountain_location = $mountain_y_location * 8 + $mountain_x_location;
            } while (in_array($mountain_location, $occupied_locations));
            $occupied_locations[] = $mountain_location;
            $values[] = "($mountain_tile_type_id, -1, 'city', $mountain_location)";
        }
        $sql .= implode(',', $values);
        $this->DbQuery($sql);
    }

    function setupBuildings()
    {
        $buildings = [];
        foreach ($this->city_tiles as $city_tile_type_id => $city_tile) {
            if ($city_tile['is_building'])
                $buildings[] = ['type' => $city_tile_type_id, 'type_arg' => -1, 'nbr' => $city_tile['count']];
        }
        $this->city_tiles_deck->createCards($buildings);
        $this->city_tiles_deck->shuffle('deck');

        $ranch_tile_type_id = $this->city_tile_type_ids['ranch'];
        $mine_tile_type_id = $this->city_tile_type_ids['mine'];
        $building_location_3 = $this->action_ids['building_construction_3'];
        $building_location_4 = $this->action_ids['building_construction_4'];
        $building_location_10 = $this->action_ids['building_construction_10'];
        $building_location_12 = $this->action_ids['building_construction_12'];

        $sql = "INSERT INTO city_tiles (card_type, card_type_arg, card_location, card_location_arg) VALUES ";
        $values = [];
        $values[] = "($ranch_tile_type_id, -1, 'building_construction', $building_location_3)";
        $values[] = "($mine_tile_type_id, -1, 'building_construction', $building_location_4)";
        $values[] = "($ranch_tile_type_id, -1, 'building_construction', $building_location_10)";
        $values[] = "($mine_tile_type_id, -1, 'building_construction', $building_location_12)";
        $sql .= implode(',', $values);
        $this->DbQuery($sql);

        $this->city_tiles_deck->pickCardForLocation('deck', 'building_construction', $this->action_ids['building_construction_5']);
        $this->city_tiles_deck->pickCardForLocation('deck', 'building_construction', $this->action_ids['building_construction_6']);
        $this->city_tiles_deck->pickCardForLocation('deck', 'building_construction', $this->action_ids['building_construction_8']);
        
    }

    function getCitySquareCoordinates($city_square_id): array
    {
        $x = $city_square_id % 8;
        $y = intdiv($city_square_id, 8);
        return [$x, $y];
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in cssomeguy.action.php)
    */

    function claimParcel($parcel_id)
    {
        $this->checkAction('claimParcel');

        $sql = "SELECT parcel_id FROM parcels WHERE parcel_id=$parcel_id";
        $is_parcel_claimed = !is_null($this->getUniqueValueFromDB($sql));

        if ($is_parcel_claimed)
            throw new BgaUserException($this->_('Parcel already claimed'));

        $player_id = $this->getActivePlayerId();

        $sql = "UPDATE player SET property_tiles=property_tiles-1 WHERE player_id=$player_id";
        $this->DbQuery($sql);

        $sql = "INSERT INTO parcels (parcel_id, owner_id) VALUES ($parcel_id, $player_id)";
        $this->DbQuery($sql);

        [$x, $y] = $this->getCitySquareCoordinates($parcel_id);

        $this->notifyAllPlayers('parcelClaimed', clienttranslate('${player_name} claims parcel (${x}, ${y})'), [
            'player_name' => $this->getActivePlayerName(),
            'x' => $x,
            'y' => $y,
            'parcelId' => $parcel_id
        ]);

        $this->gamestate->nextState('parcelClaimed');
    }

    function choosePersonality($personality_id)
    {
        $this->checkAction('choosePersonality');

        $sql = "SELECT personality FROM player WHERE personality=$personality_id";
        $is_personality_chosen = !is_null($this->getUniqueValueFromDB($sql));

        if ($is_personality_chosen)
            throw new BgaUserException($this->_('Personality already claimed'));

        $player_id = $this->getActivePlayerId();
        $resources_changed = [];
        $sql = "UPDATE player SET personality=$personality_id";
        if ($personality_id == $this->personality_ids['sheriff']) {
            $sql .=  ",is_using_personality_benefit=false";
        }
        else if ($personality_id == $this->personality_ids['banker']) {
            $sql .=  ",money=money+9";
            $resources_changed['money'] = 9;
        }
        else if ($personality_id == $this->personality_ids['coolie']) {
            $sql .=  ",roads=roads+2";
            $resources_changed['roads'] = 2;
        }
        $sql .= " WHERE player_id=$player_id";
        $this->DbQuery($sql);

        $this->notifyAllPlayers('personalityChosen', clienttranslate('${player_name} chooses ${personality}'), [
            'player_name' => $this->getActivePlayerName(),
            'personality' => $this->personalities[$personality_id]['name'],
            'personalityId' => $personality_id,
            'resourcesChanged' => $resources_changed
        ]);

        if ($personality_id == $this->personality_ids['grocer']) {
            $this->gamestate->nextState('grocerChosen');
            return;
        }
        else if ($personality_id == $this->personality_ids['settler']) {
            $this->gamestate->nextState('settlerChosen');
            return;
        }
        else if ($personality_id == $this->personality_ids['captain']) {
            $this->gamestate->nextState('captainChosen');
            return;
        }

        $this->gamestate->nextState('personalityChosen');
    }

    function chooseGrocerBenefit($is_receiving_money)
    {
        $this->checkAction('chooseGrocerBenefit');

        $player_id = $this->getActivePlayerId();
        if ($is_receiving_money) {
            $sql = "UPDATE player SET money=money+8, is_using_personality_benefit=true WHERE player_id=$player_id";
            $this->DbQuery($sql);

            $notification_type = 'updateResources';
            $msg = clienttranslate('${player_name} receives $8');
            $resources_changed['money'] = 8;
        }
        else {
            $notification_type = 'log';
            $msg = clienttranslate('${player_name} decides to later receive $8 or receive double income from 1 building type during the building income phase');
            $resources_changed = [];
        }

        $this->notifyAllPlayers($notification_type, $msg, [
            'player_name' => $this->getActivePlayerName(),
            'resourcesChanged' => $resources_changed
        ]);

        $this->gamestate->nextState('personalityChosen');
    }

    function chooseCaptainBenefit($amount_spent)
    {
        $this->checkAction('chooseCaptainBenefit');

        $player_id = $this->getActivePlayerId();
        $sql = "SELECT money, cowboys FROM player WHERE player_id=$player_id";
        $current_resources = $this->getObjectFromDB($sql);

        if ($amount_spent > $current_resources['money'])
            throw new BgaUserException($this->_('Not enough money'));

        $cowboys_gaining = $this->personalities[$this->personality_ids['captain']]['pay_options'][$amount_spent];

        if ($cowboys_gaining + $current_resources['cowboys'] > 10)
            $cowboys_gaining -= $cowboys_gaining + $current_resources['cowboys'] - 10;

        $sql = "UPDATE player SET cowboys=cowboys+$cowboys_gaining, money=money-$amount_spent WHERE player_id=$player_id";
        $this->DbQuery($sql);

        $resources_changed['cowboys'] = $cowboys_gaining;
        $resources_changed['money'] = -$amount_spent;
        $this->notifyAllPlayers(
            'updateResources',
            '${player_name} pays $${money} to get ${cowboys_gaining} cowboy(s)',
            [
                'player_name' => $this->getActivePlayerName(),
                'money' => $amount_spent,
                'cowboys_gaining' => $cowboys_gaining,
                'resourcesChanged' => $resources_changed
            ]
        );

        $this->gamestate->nextState('personalityChosen');
    }

    function placeCowboy($location_type, $location_id)
    {
        $this->checkAction('placeCowboy');

        $player_id = $this->getActivePlayerId();

        $sql = "SELECT cowboys FROM player WHERE player_id=$player_id";
        $cowboys = $this->getUniqueValueFromDB($sql);

        if ($cowboys == 0)
            throw new BgaUserException($this->_('You have no more cowboys left'));

        if ($location_type == 'city') {
            $sql = "SELECT owner_id FROM parcels WHERE parcel_id=$location_id";
            $is_parceled = !is_null($this->getUniqueValueFromDB($sql));
            if ($is_parceled) {
                $is_built_on = empty($this->city_tiles_deck->getCardsInLocation($location_type, $location_id));
                if ($is_built_on)
                    throw new BgaUserException($this->_('You cannot place a cowboy on an empty parcel'));
            }
        }

        if ($location_type == 'city' || ($location_type == 'action' && $this->actions[$location_id]['is_duel_zone'])) {
            $sql = "SELECT owner_id FROM cowboys WHERE location_type='$location_type' AND location_id=$location_id AND owner_id=$player_id";
            $is_cowboy_already_placed = !is_null($this->getUniqueValueFromDB($sql));
    
            if ($is_cowboy_already_placed)
                throw new BgaUserException($this->_('You already placed a cowboy here'));
        }
            
        $sql = "UPDATE player SET cowboys=cowboys-1 WHERE player_id=$player_id";
        $this->DbQuery($sql);

        $sql = "INSERT INTO cowboys (cowboy_id, owner_id, location_type, location_id) VALUES ($cowboys, $player_id, '$location_type', $location_id)";
        $this->DbQuery($sql);

        [$x, $y] = $this->getCitySquareCoordinates($location_id);

        $this->notifyAllPlayers('cowboyPlaced', clienttranslate('${player_name} places cowboy on city square (${x}, ${y})'), [
            'player_name' => $this->getActivePlayerName(),
            'x' => $x,
            'y' => $y,
            'cowboyId' => $cowboys,
            'locationType' => $location_type,
            'locationId' => $location_id
        ]);

        $this->gamestate->nextState('cowboyPlaced');
    }

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */


    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    ////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    function argGrocerChosen()
    {
        return [
            'personality_name' => $this->personalities[$this->personality_ids['grocer']]['name']
        ];
    }

    function argCaptainChosen()
    {
        $pay_options = $this->personalities[$this->personality_ids['captain']]['pay_options'];
        return [
            'personality_name' => $this->personalities[$this->personality_ids['captain']]['name'],
            'payOptions' => $pay_options
        ];
    }

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    function stInitialParcelClaimed()
    {
        $first_player_id = $this->getNextPlayerTable()[0];
        $sql = "SELECT property_tiles FROM player WHERE player_id=$first_player_id";
        if ($this->getUniqueValueFromDB($sql) == 10) {
            $this->gamestate->nextState('choosePersonality');
            return;
        }

        $prev_player_id = $this->getActivePlayerId();
        $last_player_no = $this->getPlayersNumber();
        $sql = "SELECT player_id FROM player WHERE player_no=$last_player_no";
        $last_player_id = $this->getUniqueValueFromDB($sql);

        $is_next_initial_parcel_claim_clockwise = 1 == $this->getGameStateValue('isNextInitialParcelClaimClockwise');

        // last player gets to place his 2nd parcel right after his first
        if ($prev_player_id == $last_player_id && $is_next_initial_parcel_claim_clockwise) {
            $this->setGameStateValue('isNextInitialParcelClaimClockwise', 0);
            $this->giveExtraTime($prev_player_id);
            $this->gamestate->nextState('nextParcelClaim');
            return;
        }

        if ($is_next_initial_parcel_claim_clockwise)
            $next_player_id = $this->activeNextPlayer();
        else
            $next_player_id = $this->activePrevPlayer();
        $this->giveExtraTime($next_player_id);
        $this->gamestate->nextState('nextParcelClaim');
    }

    function stPersonalityChosen()
    {
        // determine next player to choose personality
        $sql = "SELECT player_id FROM player WHERE personality IS NULL ORDER BY turn_order ASC LIMIT 1";
        $player_id = $this->getUniqueValueFromDB($sql);

        // all players picked a personality
        if (is_null($player_id)) {
            $sql = "SET @new_turn_order=0";
            $this->DbQuery($sql);

            $sql =
            "UPDATE player
            INNER JOIN 
            (SELECT (@new_turn_order:=@new_turn_order+1) AS new_turn_order, player_id FROM player ORDER BY personality ASC) new_turn_orders
            ON player.player_id = new_turn_orders.player_id
            SET turn_order=new_turn_order";
            $this->DbQuery($sql);

            $sql = "SELECT turn_order, player_id FROM player";
            $new_turn_order = $this->getCollectionFromDB($sql, true);
            $this->notifyAllPlayers('allPersonalitesChosen', '', [
                'newTurnOrder' => $new_turn_order
            ]);

            $first_player_id = $new_turn_order[1];

            $this->gamestate->changeActivePlayer($first_player_id);
            $this->giveExtraTime($first_player_id);
            $this->gamestate->nextState('placeCowboy');
            return;
        }

        $this->gamestate->changeActivePlayer($player_id);
        $this->giveExtraTime($player_id);
        $this->gamestate->nextState('nextPersonalityChoice');
    }

    function stCowboyPlaced()
    {
        $prev_player_id = $this->getActivePlayerId();
        $sql = "SELECT player_id FROM player ORDER BY turn_order ASC";
        $turn_order_ids = $this->getObjectListFromDB($sql, true);

        $next_player_table = $this->createNextPlayerTable($turn_order_ids);
        $next_player_id = $next_player_table[$prev_player_id];

        $this->gamestate->changeActivePlayer($next_player_id);
        $this->giveExtraTime($next_player_id);
        $this->gamestate->nextState('nextPlayer');
    }

    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */

    //////////////////////////////////////////////////////////////////////////////
    //////////// Zombie
    ////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn($state, $active_player)
    {
        $statename = $state['name'];

        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState("zombiePass");
                    break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive($active_player, '');

            return;
        }

        throw new feException("Zombie mode not supported at this game state: " . $statename);
    }

    ///////////////////////////////////////////////////////////////////////////////////:
    ////////// DB upgrade
    //////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */

    function upgradeTableDb($from_version)
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345

        // Example:
        //        if( $from_version <= 1404301345 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        if( $from_version <= 1405061421 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        // Please add your future database scheme changes here
        //
        //


    }
}
