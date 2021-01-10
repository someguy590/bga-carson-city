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
  ],
  $this->personality_ids['mercenary'] => [
    'name' => clienttranslate('The Mercenary'),
  ]
];

/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/
