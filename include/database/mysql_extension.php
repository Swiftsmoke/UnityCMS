<?php
/***************************************************************************
 *                                mysql.php
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
  die( "Invalid access.." );

class mysql_extension extends Database {
  static function _connect( $url, $user, $pass, $newlink = false ) { return mysql_connect( $url, $user, $pass, $newlink ); }

  static function _ping( $con ) { return mysql_ping( $con ); }

  static function _disconnect( $con ) { return mysql_close( $con ); }

  static function _database( $database, $con ) { return mysql_select_db( $database, $con ); }

  static function _query( $query, $con ) { return mysql_query( $query, $con ); }

  static function _freeresult( $result ) { return mysql_free_result( $result ); }

  static function _insertid( $con ) { return mysql_insert_id( $con ); }

  static function _fetcharray( $result ) { return mysql_fetch_array( $result, MYSQL_ASSOC ); }

  static function _numrows( $result ) { return mysql_num_rows( $result ); }

  static function _affectedrows( $result ) { return mysql_affected_rows( $result ); }

  static function _escape( $con, $string ) { return mysql_escape_string( $string ); }
  static function _error( $con ) { return mysql_error( $con ); }
}
?>