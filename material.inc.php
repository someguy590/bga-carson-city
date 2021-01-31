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
 * material.inc.php
 *
 * cssomeguy game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

$this->city_tile_type_ids = [
  'mountain' => 0,
  'house' => 1,
  'ranch' => 2,
  'mine' => 3,
  'bank' => 4,
  'drugstore' => 5,
  'hotel' => 6,
  'saloon' => 7,
  'prison' => 8,
  'church' => 9
];

$this->city_tiles = [
  $this->city_tile_type_ids['mountain'] => [
    'name' => clienttranslate('mountain'),
    'count' => 9,
    'is_building' => false
  ],
  $this->city_tile_type_ids['house'] => [
    'name' => clienttranslate('house'),
    'is_building' => false
  ],
  $this->city_tile_type_ids['ranch'] => [
    'name' => clienttranslate('ranch'),
    'count' => 4,
    'is_building' => true
  ],
  $this->city_tile_type_ids['mine'] => [
    'name' => clienttranslate('mine'),
    'count' => 4,
    'is_building' => true
  ],
  $this->city_tile_type_ids['bank'] => [
    'name' => clienttranslate('bank'),
    'count' => 4,
    'is_building' => true
  ],
  $this->city_tile_type_ids['drugstore'] => [
    'name' => clienttranslate('drugstore'),
    'count' => 4,
    'is_building' => true
  ],
  $this->city_tile_type_ids['hotel'] => [
    'name' => clienttranslate('hotel'),
    'count' => 3,
    'is_building' => true
  ],
  $this->city_tile_type_ids['saloon'] => [
    'name' => clienttranslate('saloon'),
    'count' => 3,
    'is_building' => true
  ],
  $this->city_tile_type_ids['prison'] => [
    'name' => clienttranslate('prison'),
    'count' => 2,
    'is_building' => true
  ],
  $this->city_tile_type_ids['church'] => [
    'name' => clienttranslate('church'),
    'count' => 2,
    'is_building' => true
  ],
];

$this->personality_ids = [
  'sheriff' => 1,
  'banker' => 2,
  'grocer' => 3,
  'coolie' => 4,
  'settler' => 5,
  'captain' => 6,
  'mercenary' => 7,
];

$this->personalities = [
  $this->personality_ids['sheriff'] => [
    'name' => clienttranslate('The Sheriff'),
  ],
  $this->personality_ids['banker'] => [
    'name' => clienttranslate('The Banker'),
  ],
  $this->personality_ids['grocer'] => [
    'name' => clienttranslate('The Grocer'),
  ],
  $this->personality_ids['coolie'] => [
    'name' => clienttranslate('The Chinese Coolie'),
  ],
  $this->personality_ids['settler'] => [
    'name' => clienttranslate('The Settler'),
  ],
  $this->personality_ids['captain'] => [
    'name' => clienttranslate('The Captain'),
    'pay_options' => [
      1 => 1,
      4 => 2,
      9 => 3
    ]
  ],
  $this->personality_ids['mercenary'] => [
    'name' => clienttranslate('The Mercenary'),
  ]
];

$this->action_ids = [
  'wages' => 0,
  'ammunition' => 1,
  'roads_3' => 2,
  'roads_1' => 3,
  'building_construction_12' => 4,
  'building_construction_10' => 5,
  'building_construction_8' => 6,
  'building_construction_6' => 7,
  'building_construction_5' => 8,
  'building_construction_4' => 9,
  'building_construction_3' => 10,
  'parcels_income' => 11,
  'cowboy_income' => 12,
  'gambling_income' => 13,
  'parcels_victory_points' => 14,
  'cowboy_victory_points' => 15,
  'estate_victory_points' => 16,
  'buying_victory_points_5' => 17,
  'buying_victory_points_4' => 18,
  'buying_victory_points_3' => 19,
  'buying_victory_points_2' => 20
];

$this->actions = [
  $this->action_ids['wages'] => ['name' => clienttranslate('wages')],
  $this->action_ids['ammunition'] => ['name' => clienttranslate('ammunition')],
  $this->action_ids['roads_3'] => ['name' => clienttranslate('3 Roads')],
  $this->action_ids['roads_1'] => ['name' => clienttranslate('1 Road')],
  $this->action_ids['building_construction_12'] => ['name' => clienttranslate('Building Construction $12')],
  $this->action_ids['building_construction_10'] => ['name' => clienttranslate('Building Construction $10')],
  $this->action_ids['building_construction_8'] => ['name' => clienttranslate('Building Construction $8')],
  $this->action_ids['building_construction_6'] => ['name' => clienttranslate('Building Construction $6')],
  $this->action_ids['building_construction_5'] => ['name' => clienttranslate('Building Construction $5')],
  $this->action_ids['building_construction_4'] => ['name' => clienttranslate('Building Construction $4')],
  $this->action_ids['building_construction_3'] => ['name' => clienttranslate('Building Construction $3')],
  $this->action_ids['parcels_income'] => ['name' => clienttranslate('Parcels Income')],
  $this->action_ids['cowboy_income'] => ['name' => clienttranslate('Cowboy Income')],
  $this->action_ids['gambling_income'] => ['name' => clienttranslate('Gambling Income')],
  $this->action_ids['parcels_victory_points'] => ['name' => clienttranslate('Parcels Victory Points')],
  $this->action_ids['cowboy_victory_points'] => ['name' => clienttranslate('Cowboy Victory Points')],
  $this->action_ids['estate_victory_points'] => ['name' => clienttranslate('Estate Victory Points')],
  $this->action_ids['buying_victory_points_5'] => ['name' => clienttranslate('Buying Victory Points $5')],
  $this->action_ids['buying_victory_points_4'] => ['name' => clienttranslate('Buying Victory Points $4')],
  $this->action_ids['buying_victory_points_3'] => ['name' => clienttranslate('Buying Victory Points $3')],
  $this->action_ids['buying_victory_points_2'] => ['name' => clienttranslate('Buying Victory Points $2')],
];

/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/
