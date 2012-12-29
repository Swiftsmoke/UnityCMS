<?php
/***************************************************************************
 *                                mysqli.php
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

class mysqli_extension extends Database {
  static function _connect( $url, $user, $pass, $newlink = false ) { return mysqli_connect( $url, $user, $pass, $newlink ); }

  static function _ping( $con ) { return mysqli_ping( $con ); }

  static function _disconnect( $con ) { return mysqli_close( $con ); }

  static function _database( $database, $con ) { return mysqli_select_db( $con, $database ); }

  static function _query( $query, $con ) { return mysqli_query( $con, $query ); }

  static function _freeresult( $result ) { return mysqli_free_result( $result ); }

  static function _insertid( $con ) { return mysqli_insert_id( $con ); }

  static function _fetcharray( $result ) { return mysqli_fetch_array( $result, MYSQL_ASSOC ); }

  static function _numrows( $result ) { return mysqli_num_rows( $result ); }

  static function _affectedrows( $result ) { return mysqli_affected_rows( $result ); }

  static function _escape( $con, $string ) { return mysqli_escape_string( $con, $string ); }
  static function _error( $con ) { return mysqli_error( $con ); }
}
?>