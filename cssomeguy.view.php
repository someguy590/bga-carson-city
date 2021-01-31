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
 * cssomeguy.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in cssomeguy_cssomeguy.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */

require_once(APP_BASE_PATH . "view/common/game.view.php");

class view_cssomeguy_cssomeguy extends game_view
{
  function getGameName()
  {
    return "cssomeguy";
  }
  function build_page($viewArgs)
  {
    // Get players & players number
    $players = $this->game->loadPlayersBasicInfos();
    $player_count = count($players);

    /*********** Place your code below:  ************/
    $turn_tracker_scale = 25;
    $turn_tracker_offset = 12;
    $turn_tracker_x_start = 79;

    $current_turn_tracker_y_start = 33;
    $x_px = $turn_tracker_x_start;
    $this->page->begin_block($this->getGameName() . '_' . $this->getGameName(), 'current_turn_tracker');
    for ($i = 1; $i <= $player_count; $i++) {
      $this->page->insert_block('current_turn_tracker', [
        'TRACKER_ID' => $i,
        'LEFT' => $x_px,
        'TOP' => $current_turn_tracker_y_start
      ]);
      $x_px += $turn_tracker_scale + $turn_tracker_offset;
    }

    $pass_turn_tracker_y_start = 71;
    $x_px = $turn_tracker_x_start;
    $this->page->begin_block($this->getGameName() . '_' . $this->getGameName(), 'pass_turn_tracker');
    for ($i = 1; $i <= $player_count; $i++) {
      $this->page->insert_block('pass_turn_tracker', [
        'TRACKER_ID' => $i,
        'LEFT' => $x_px,
        'TOP' => $pass_turn_tracker_y_start
      ]);
      $x_px += $turn_tracker_scale + $turn_tracker_offset;
    }


    $this->page->begin_block($this->getGameName() . '_' . $this->getGameName(), 'action_square');
    $action_scale = 62;
    foreach ($this->game->actions as $action_id => $action) {
      $classes = '';
      $x_px = 0;
      $y_px = 0;

      if ($action_id >= 0 && $action_id <= 3) {
        $x_px = 336 + $action_id * $action_scale;
        $y_px = 34;
      }
      else if ($action_id >= 4 && $action_id <= 13) {
        $x_px = 593 - (($action_id - 4) * $action_scale);
        $y_px = 123;
      }
      else {
        $x_px = 112 + (($action_id - 14) * $action_scale);
        $y_px = 218;
      }

      $this->page->insert_block('action_square', [
        'ACTION_SQUARE_ID' => $action_id,
        'ACTION_SQUARE_CLASSES' => $classes,
        'LEFT' => $x_px,
        'TOP' => $y_px
      ]);
    }

    $city_x_start = 55;
    $city_y_start = 310;
    $city_square_x_scale = 74;
    $city_square_y_scale = 75;
    $this->page->begin_block($this->getGameName() . '_' . $this->getGameName(), 'city_square');
    for ($i = 0; $i < 64; $i++) {
      $x_px = ($i % 8) * $city_square_x_scale + $city_x_start;
      $y_px = intdiv($i, 8) * $city_square_y_scale + $city_y_start;
      $this->page->insert_block('city_square', [
        'CITY_SQUARE_ID' => $i,
        'LEFT' => $x_px,
        'TOP' => $y_px
      ]);
    }

    $placed_roads = $this->game->getObjectListFromDB("SELECT road_id FROM roads", true);

    $horizontal_road_row_size = 8;
    $horizontal_road_x_start = 59;
    $horizontal_road_y_start = $city_y_start;
    $horizontal_road_x_scale = 66;
    $horizontal_road_x_offset = 8;
    $horizontal_road_y_offset = 75;
    
    $vertical_road_row_size = 9;
    $vertical_road_x_start = $city_x_start;
    $vertical_road_y_start = 314;
    $vertical_road_y_scale = 66;
    $vertical_road_x_offset = 74;
    $vertical_road_y_offset = 8;

    $road_id = 0;
    $horizontal_road_y_px = $horizontal_road_y_start;
    $vertical_road_y_px = $vertical_road_y_start;
    $this->page->begin_block($this->getGameName() . '_' . $this->getGameName(), 'road');
    for ($row = 0; $row < 17; $row++) {
      if ($row % 2 == 0) {
        $horizontal_road_x_px = $horizontal_road_x_start;
        for ($road = 0; $road < $horizontal_road_row_size; $road++) {
          $next_road_id = $road_id++;
          $classes = 'horizontal_road_space';
          if (in_array($next_road_id, $placed_roads))
            $classes .= ' road_placed';
          $this->page->insert_block('road', [
            'ROAD_ID' => $next_road_id,
            'ROAD_CLASSES' => $classes,
            'LEFT' => $horizontal_road_x_px,
            'TOP' => $horizontal_road_y_px
          ]);
          $horizontal_road_x_px += $horizontal_road_x_scale + $horizontal_road_x_offset;
        }
        $horizontal_road_y_px += $horizontal_road_y_offset;
      }
      else {
        $vertical_road_x_px = $vertical_road_x_start;
        for ($road = 0; $road < $vertical_road_row_size; $road++) {
          $next_road_id = $road_id++;
          $classes = 'vertical_road_space';
          if (in_array($next_road_id, $placed_roads))
            $classes .= ' road_placed';
          $this->page->insert_block('road', [
            'ROAD_ID' => $next_road_id,
            'ROAD_CLASSES' => $classes,
            'LEFT' => $vertical_road_x_px,
            'TOP' => $vertical_road_y_px
          ]);
          $vertical_road_x_px += $vertical_road_x_offset;
        }
        $vertical_road_y_px += $vertical_road_y_scale + $vertical_road_y_offset;
      }
    }

    /*
        
        // Examples: set the value of some element defined in your tpl file like this: {MY_VARIABLE_ELEMENT}

        // Display a specific number / string
        $this->tpl['MY_VARIABLE_ELEMENT'] = $number_to_display;

        // Display a string to be translated in all languages: 
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::_("A string to be translated");

        // Display some HTML content of your own:
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::raw( $some_html_code );
        
        */

    /*
        
        // Example: display a specific HTML block for each player in this game.
        // (note: the block is defined in your .tpl file like this:
        //      <!-- BEGIN myblock --> 
        //          ... my HTML code ...
        //      <!-- END myblock --> 
        

        $this->page->begin_block( "cssomeguy_cssomeguy", "myblock" );
        foreach( $players as $player )
        {
            $this->page->insert_block( "myblock", array( 
                                                    "PLAYER_NAME" => $player['player_name'],
                                                    "SOME_VARIABLE" => $some_value
                                                    ...
                                                     ) );
        }
        
        */



    /*********** Do not change anything below this line  ************/
  }
}
