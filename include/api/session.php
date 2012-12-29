<?php
/***************************************************************************
 *                                session.php
 *                            -------------------
 *   Project              : UnityCMS
 *   Begin                : Tuesday, April 20, 2010
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

class session {
  static $lifetime = 0;

  static function init() {
    // get session lifetime
    self::$lifetime = get_cfg_var( "session.gc_maxlifetime" );
    self::$lifetime = 600;

    session_set_save_handler( array("session", "open"), array("session", 'close'), array("session", 'read'), array("session", 'write'), array("session", 'destroy'), array("session", 'gc'));

    // Start Session
    session_start();
  }

  static function end() { session_write_close(); }

  /**
   *  Get the number of online users
   *
   *  This is not 100% accurate. It depends on how often the garbage collector is run
   *
   *  @return integer     approximate number of users curently online
   */
  static function get_users_online() {
    // counts the rows from the database
    Database::query( "SELECT COUNT(`session_id`) AS `count` FROM `session_data`", Config::$site->con() );

    // return the number of found rows
    return Result::GetField( "count" );
  }

  static function get_guests_online() {
    Database::query( "SELECT count(`session_id`) AS `count` FROM `session_data` WHERE userid <= 0", Config::$site->con() );
    return Result::GetField( "count" );
  }

  static function get_members_online() {
    Database::query( "SELECT count(`session_id`) AS `count` FROM `session_data` WHERE userid > 0 GROUP BY userid", Config::$site->con() );
    return Result::GetField( "count" );
  }

  static function get_sessions() {
    Database::query( "SELECT * FROM session_data ORDER BY session_time DESC", Config::$site->con() );
    return Result::GetRows();
  }

  /**
   *  Custom open() function
   *
   *  @access private
   */
  static function open( $save_path, $session_name ) { return true; }

  /**
   *  Custom close() function
   *
   *  @access private
   */
  static function close() { return true; }

  /**
   *  Custom read() function
   *
   *  @access private
   */
  static function read( $session_id ) {

    // reads session data associated with the session id
    // but only if the HTTP_USER_AGENT is the same as the one who had previously written to this session
    // and if session has not expired
    Database::query( "SELECT session_data FROM session_data WHERE session_id = '" . $session_id . "' AND http_user_agent = '" . $_SERVER["HTTP_USER_AGENT"] . "' AND session_expire > '" . time() . "'", Config::$site->con() );

    // if anything was found
    if ( Result::RecordCount() > 0 ) { return Result::GetField( "session_data" ); }

    // if there was an error return an epmty string - this HAS to be an empty string
    return "";
  }

  /**
   *  Custom write() function
   *
   *  @access private
   */
  static function write( $session_id, $session_data ) {
    // first checks if there is a session with this id
    Database::query( "SELECT * FROM session_data WHERE session_id = \"" . $session_id . "\"", Config::$site->con() );

    $location = $_SERVER['REQUEST_URI'];

    if ( !$location || $location == '/' )
      $location = ROOT_URL;

    $username = ( isset( $_SESSION['username'] ) ? $_SESSION['username'] : "" );
    $userid   = 0;

    if ( CMS::$logintype->isforum ) {
      if ( isset( $_SESSION['forumid'] ) )
        $userid = $_SESSION['forumid'];
    }
    else if ( isset( $_SESSION['userid'] ) )
      $userid = $_SESSION['userid'];
      
    $location = Database::sql_safe($location);
    $session_date = Database::sql_safe($session_data);

    // if there is
    if ( Result::RecordCount() > 0 ) {

      // update the existing session's data
      // and set new expiry time
      $updated = Database::query(
                     "UPDATE session_data SET location = \"" . $location . "\", session_time = '" . time() . "', ipaddress = \"" . $_SERVER['REMOTE_ADDR'] . "\", user = \"" . $username . "\", userid = '" . (int)$userid . "', session_data = '"
                         . $session_data . "', session_expire = '" . ( time() + self::$lifetime ) . "' WHERE session_id = \"" . $session_id . "\"",
                     Config::$site->con() );

      // if anything happened
      if ( $updated ) { return true; }

    // if this session id is not in the database
    }
    else {

      // insert a new record
      $inserted = Database::query(
                      "REPLACE INTO session_data (location, session_time, session_id, ipaddress, http_user_agent, session_data, session_expire, user, userid) VALUES (\"" . $location . "\", '" . time() . "', '" . $session_id . "', \""
                          . $_SERVER['REMOTE_ADDR'] . "\", '" . $_SERVER["HTTP_USER_AGENT"] . "', '" . $session_data . "', '" . ( time() + self::$lifetime ) . "', \"" . $username . "\", '" . (int)$userid . "')",
                      Config::$site->con() );

      // if anything happened
      if ( $inserted ) { return ""; }
    }

    // if something went wrong, return false
    return false;
  }

  /**
   *  Custom destroy() function
   *
   *  @access private
   */
  static function destroy( $session_id ) {

    // deletes the current session id from the database
    $deleted = Database::query( "DELETE FROM session_data WHERE session_id = '" . $session_id . "'", Config::$site->con() );

    // if anything happened
    if ( $deleted ) { return true; }

    // if something went wrong, return false
    return false;
  }

  /**
   *  Custom gc() function (garbage collector)
   *
   *  @access private
   */
  static function gc( $maxlifetime ) { Database::query( "DELETE FROM session_data WHERE session_expire < '" . ( time() - self::$lifetime ) . "'", Config::$site->con() ); }
}
?>