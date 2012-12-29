<?php
/***************************************************************************
 *                                donate.php
 *                            -------------------
 *   Project              : UnityCMS
 *   Begin                : Friday, April 16, 2010
 *   Copyright            : (C) 2012 Robert Lambert ( dibble1989@hotmail.co.uk )
 *
 *      The copyright to the computer program(s) herein
 *      is the property of Robert Lambert
 *      The program(s) may be used and/or copied only with
 *      the written permission of Robert Lambert
 *      or in accordance with the terms and conditions
 *      stipulated in the agreement/contract under which
 *      the program(s) have been supplied.
 *
 ***************************************************************************/

/**
* Protect this file with required define IN_NESCRIPT
*   This will prevent people from accessing this file remotely
*/
if ( !defined( "IN_NESCRIPT" ) )
  die ( "Invalid access.." );

class Page {
  static $custom   = NULL;
  static $business = NULL;
  static $sitename = NULL;
  static $notify   = NULL;

  static function Build() {
    self::Prepare();

    if ( is_bool( self::$notify ) ) {
      echo "<font style='color: #00f00'>Paypal Instant Payment Notification has been completed</font>";
      return;
    }

    if ( isset( $_GET['success'] ) ) {
      echo "<font style='color: #00FF00'>Successfully completed your donation<br>Thank you for donating.<br></font>";
      echo "<a href='" . ROOT_URL . "'>Return to homepage</a>";
      return;
    }

    if ( User::getbool( "guest" ) != false ) {
      echo "<font style='color: #FF0000'>You need to login to view this page.</font>";
      return;
    }

    CMS::loadPage( 'donate' );
  }
  static function Prepare() {
    self::$custom   = ( CMS::$logintype->isforum ? User::getint( 'forumid' ) : User::getint( 'userid' ));
    self::$business = CMS::$paypalemail;
    self::$sitename = CMS::$sitename;

    // Paypal Instant Payment Notification
    if ( file_exists( ROOT_DIR . "include/api/paypal.php" ) ) {
      #self::$notify = Paypal::NotifyUrl();
      if ( isset( $_GET['notify'] ) ) {
        // Paypal will complete the necessary calls to make sure its valid
        if ( Paypal::Complete() )
          self::$notify = true;
      }
    }
  }
}
?>