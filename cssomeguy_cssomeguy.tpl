{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- cssomeguy implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    cssomeguy_cssomeguy.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->

<div id="carson_city_play_area">
    <div id="personalities">
        <div id="personality_1" class="personality personality_sheriff"></div>
        <div id="personality_2" class="personality personality_banker"></div>
        <div id="personality_3" class="personality personality_grocer"></div>
        <div id="personality_4" class="personality personality_coolie"></div>
        <div id="personality_5" class="personality personality_settler"></div>
        <div id="personality_6" class="personality personality_captain"></div>
        <div id="personality_7" class="personality personality_mercenary"></div>
    </div>
    <div id="board">
        <!-- BEGIN current_turn_tracker -->
        <div id="current_turn_tracker_{TRACKER_ID}" class="turn_tracker" style="left: {LEFT}px; top: {TOP}px;"></div>
        <!-- END current_turn_tracker -->

        <!-- BEGIN pass_turn_tracker -->
        <div id="pass_turn_tracker_{TRACKER_ID}" class="turn_tracker" style="left: {LEFT}px; top: {TOP}px;"></div>
        <!-- END pass_turn_tracker -->

        <!-- BEGIN action_square -->
        <div id="action_square_{ACTION_SQUARE_ID}" class="action_square {ACTION_SQUARE_CLASSES}" style="left: {LEFT}px; top: {TOP}px;"></div>
        <!-- END action_square -->

        <div id="initial_round_tracker_position"></div>

        <!-- BEGIN city_square -->
        <div id="city_square_{CITY_SQUARE_ID}" class="city_square" style="left: {LEFT}px; top: {TOP}px;"></div>
        <!-- END city_square -->

        <!-- BEGIN road -->
        <div id="road_{ROAD_ID}" class="road_space {ROAD_CLASSES}" style="left: {LEFT}px; top: {TOP}px;"></div>
        <!-- END road -->
    </div>
    <div id="tiles"></div>
</div>


<script type="text/javascript">

// Javascript HTML templates
var jstplPeg = '<div id="peg_${pegId}_${playerId}" class="player_piece peg peg_${color}"></div>';

var jstplRoundTrackerToken = '<div id="round_tracker_token" class="game_token round_tracker_token"></div>';

var jstplCityTile = '<div class="city_tile city_tile_${cityTileTypeId}" id="city_tile_${cityTileId}"></div>';

var jstplParcel = '<div id="parcel_${parcelId}_${playerId}" class="parcel" style="background-color: #${color}"></div>';

var jstplPlayerBoard = '\
    <div class="cp_board">\
        <div class="counter_icon cowboy_icon"></div><span id="cowboy_count_${playerId}" class="resource_counter"></span>\
        <div class="counter_icon money_icon"></div><span id="money_count_${playerId}" class="resource_counter"></span>\
        <div class="counter_icon revolver_token_icon"></div><span id="revolver_token_count_${playerId}" class="resource_counter"></span>\
        <div class="counter_icon road_icon"></div><span id="road_count_${playerId}" class="resource_counter"></span>\
        <div class="counter_icon property_tile_icon" style="background-color: #${color}"></div><span id="property_tile_count_${playerId}" class="resource_counter"></span>\
    </div>';

    /*
    // Example:
    var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${MY_ITEM_ID}"></div>';
    
    */

</script>

{OVERALL_GAME_FOOTER}