<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * cssomeguy implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * cssomeguy.action.php
 *
 * cssomeguy main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/cssomeguy/cssomeguy/myAction.html", ...)
 *
 */


class action_cssomeguy extends APP_GameAction
{
  // Constructor: please do not modify
  public function __default()
  {
    if (self::isArg('notifwindow')) {
      $this->view = "common_notifwindow";
      $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
    } else {
      $this->view = "cssomeguy_cssomeguy";
      self::trace("Complete reinitialization of board game");
    }
  }

  // TODO: defines your action entry points there
  public function claimParcel()
  {
    $this->setAjaxMode();

    $parcel_id = $this->getArg('parcelId', AT_int, true);
    $this->game->claimParcel($parcel_id);

    $this->ajaxResponse();
  }

  public function choosePersonality()
  {
    $this->setAjaxMode();

    $personality_id = $this->getArg('personalityId', AT_int, true);
    $this->game->choosePersonality($personality_id);

    $this->ajaxResponse();
  }

  public function chooseGrocerBenefit()
  {
    $this->setAjaxMode();

    $is_receiving_money = $this->getArg('isReceivingMoney', AT_bool, true);
    $this->game->chooseGrocerBenefit($is_receiving_money);

    $this->ajaxResponse();
  }

  public function chooseCaptainBenefit()
  {
    $this->setAjaxMode();

    $amount_spent = $this->getArg('amountSpent', AT_int, true);
    $this->game->chooseCaptainBenefit($amount_spent);

    $this->ajaxResponse();
  }

  public function placeCowboy()
  {
    $this->setAjaxMode();

    $location_type = $this->getArg('locationType', AT_alphanum, true);
    $location_id = $this->getArg('locationId', AT_int, true);
    $this->game->placeCowboy($location_type, $location_id);

    $this->ajaxResponse();
  }

  /*
    
    Example:
  	
    public function myAction()
    {
        self::setAjaxMode();     

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }
    
    */
}
