
-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- cssomeguy implementation : © <Your name here> <Your email address here>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- This is the file where you are describing the database schema of your game
-- Basically, you just have to export from PhpMyAdmin your table structure and copy/paste
-- this export here.
-- Note that the database itself and the standard tables ("global", "stats", "gamelog" and "player") are
-- already created and must not be created here

-- Note: The database schema is created from this file when the game starts. If you modify this file,
--       you have to restart a game to see your changes in database.

ALTER TABLE `player` ADD `cowboys` INT UNSIGNED NOT NULL DEFAULT '3';
ALTER TABLE `player` ADD `money` INT UNSIGNED NOT NULL DEFAULT '15';
ALTER TABLE `player` ADD `revolvers` INT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `player` ADD `revolver_tokens` INT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `player` ADD `roads` INT UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `player` ADD `property_tiles` INT UNSIGNED NOT NULL DEFAULT '12';
ALTER TABLE `player` ADD `turn_order` INT UNSIGNED NOT NULL;
ALTER TABLE `player` ADD `personality` INT UNSIGNED;
ALTER TABLE `player` ADD `is_using_personality_benefit` BIT(1);

CREATE TABLE IF NOT EXISTS `city_tiles` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(21) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `roads` (
  `road_id` int unsigned NOT NULL,
  PRIMARY KEY (`road_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `parcels` (
  `parcel_id` int unsigned NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`parcel_id`),
  FOREIGN KEY (`owner_id`) REFERENCES player(`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cowboys` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cowboy_id` int unsigned NOT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `location_type` varchar(10) NOT NULL,
  `location_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`owner_id`) REFERENCES player(`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- Example 1: create a standard "card" table to be used with the "Deck" tools (see example game "hearts"):

-- CREATE TABLE IF NOT EXISTS `card` (
--   `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
--   `card_type` varchar(16) NOT NULL,
--   `card_type_arg` int(11) NOT NULL,
--   `card_location` varchar(16) NOT NULL,
--   `card_location_arg` int(11) NOT NULL,
--   PRIMARY KEY (`card_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- Example 2: add a custom field to the standard "player" table
-- ALTER TABLE `player` ADD `player_my_custom_field` INT UNSIGNED NOT NULL DEFAULT '0';

-- buildings
-- grid location (optional as could be in building row or player stock)
-- row location (part of its deck attribute, but optional as could be in grid or player stock)

-- roads locations