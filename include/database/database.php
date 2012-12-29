<?php
/***************************************************************************
 *                                database.php
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

class Database {
  /**
  * Connection Variables
  *
  * @var array
  */
  var $connect_vars   = array();

  /**
  * Database Connection IDS
  * @var array
  */
  var $connection_ids = array();

  /**
  * Database Current Connection ID
  *
  * @var integer
  */
  var $connection_id  = 0;

  /**
  * Setting object array
  *
  * @var array
  */
  var $obj            = array();

  /**
  * Cache array
  *
  * @var array
  */
  var $cache          = array();

  /**
  * Current Query String
  *
  * @var string
  */
  var $cur_query      = '';

  /**
  * Last Query Ran Result ID
  *
  * @var integer
  */
  var $query_id;

  /**
  * Total Insert Queries
  *
  * @var integer
  */
  var $query_insert   = 0;

  /**
  * Total Update Queries
  *
  * @var integer
  */
  var $query_update   = 0;

  /**
  * Total Select Queries
  *
  * @var integer
  */
  var $query_select   = 0;

  /**
  * Total Queries
  *
  * @var integer
  */
  var $querycount     = 0;

  /**
  * Current Result Row
  *
  * @var array
  */
  var $record_row     = array();

  static function init() {
    global $con;

    if ( !$con ) {
      $className = "Database";

      if ( CMS::$sqlextension ) { $className = CMS::$sqlextension . "_extension"; }

      $con                    = new $className;
      $con->connection_ids[0] = null;
    }
  }

  /**
  * Open Database Connection
  *
  * @return integer
  */
  static function connect( $id = "site", $array = NULL ) {
    global $con;
    self::init();

    $id = strtoupper( $id );

    if ( $array && is_array( $array ) ) {
      $con->connect_vars['sql_host']             = $array[0];
      $con->connect_vars['sql_user']             = $array[1];
      $con->connect_vars['sql_pass']             = $array[2];
      $con->connect_vars['sql_db']               = $array[3];
      $con->connect_vars['force_new_connection'] = true;
    }

    $con->connection_ids[] = $con->connection_id = $con->_connect( $con->connect_vars['sql_host'], $con->connect_vars['sql_user'], $con->connect_vars['sql_pass'], $con->connect_vars['force_new_connection'] ? true : false );

    if ( !$con->connection_id ) {
    // E_USER_ERROR will kill the page
    trigger_error( "SQL " . $id . ": Unable to open a connection to sql server... " . $con->_error(), E_USER_ERROR ); }

    $con->setdatabase();

    return $con->connection_id;
  }

  static function ping( $conid ) {
    global $con;

    if ( !$conid )
      $conid = $con->connection_id;

    $con->_ping( $conid );
  }

  static function getconnection() {
    global $con;
    self::init();
    return $con->connection_id;
  }

  /**
  * Set Connection ID
  *
  * @param integer
  * @return void
  */
  static function setconnection( $identifier ) {
    global $con;
    self::init();
    $con->connection_id = $con->connection_ids[$identifier];
  }

  /**
  * Select Database
  *
  * @param string
  * @return boolean
  */
  static function setdatabase( $newdb = "" ) {
    global $con;
    self::init();

    if ( !$newdb && !$con->connect_vars['sql_db'] ) { trigger_error( "SQL Connection: No database selected", E_USER_ERROR ); }

    if ( !$newdb )
      $newdb = $con->connect_vars['sql_db'];

    if ( !@$con->_database( $newdb, $con->connection_id ) ) {
      trigger_error( "SQL Connection: Unable to open a link to the database... " . $con->_error($con->connection_id), E_USER_ERROR );
      return false;
    }

    return true;
  }

  static function _query( $the_query, $conid ) { /* PlaceHolder */ return null; }

  /**
  * Run Query
  *
  * @param string Query
  * @return integer Query ID
  */
  static function query( $the_query, $conid = null ) {
    global $con, $querycount;
    Result::FreeResult();

    if ( $conid == null || gettype( $conid ) != 'resource' ) {
      self::init();
      $conid = $con->connection_id;
    }

    $con->query_id = $con->_query( $the_query, $conid );
    $error         = $con->_error( $conid );

    if ( $error )
      trigger_error( "SQL Query: Failed to execute " . $error, E_USER_ERROR );

    $con->thequery = $the_query;
    $querycount++;

    if ( ( strpos( $the_query, "INSERT INTO" ) !== false ) OR ( strpos( $the_query, "REPLACE INTO" ) !== false ) ) {
      $con->query_insert++;
      return $con->affected_rows( $conid );
    }
    else if ( ( strpos( $the_query, "UPDATE" ) !== false ) OR ( strpos( $the_query, "DELETE FROM" ) !== false ) ) {
      $con->query_update++;
      return $con->affected_rows( $conid );
    }
    else {
      $con->query_select++;
      Result::SetResult( $con->fetch_rows() );
      $con->_freeresult( $con->query_id );
      return;
    }
  }

  /**
  * Get Previous Insert ID
  *
  * @return integer Insert ID
  */
  static function getinsertid() {
    global $con;
    self::init();
    return $con->_insertid( $con->connection_id );
  }

  /**
  * Fetch Row From Result
  *
  * @param integer Query ID
  * @return array
  */
  static function fetch_row( $query_id = "" ) {
    global $con;
    self::init();

    if ( $query_id == "" )
      $query_id = $con->query_id;

    $con->record_row = $con->_fetcharray( $query_id, MYSQL_ASSOC );

    return $con->record_row;
  }

  /**
  * Fetch All Rows From Result
  *
  * @param integer Query ID
  * @return array
  */
  static function fetch_rows( $query_id = "" ) {
    global $con;
    self::init();

    if ( $query_id == "" )
      $query_id = $con->query_id;

    $return = array();

    while ( $row = $con->fetch_row( $query_id ) ) { $return[] = $row; }

    return $return;
  }

  /**
  * Number Of Rows In Result
  *
  * @param integer Query ID
  * @return integer
  */
  static function num_rows( $query_id = "" ) {
    global $con;
    self::init();

    if ( $query_id == "" ) { $query_id = $con->query_id; }

    return $con->_numrows( $query_id );
  }

  /**
  * Number Of Affected Rows
  *
  * @return integer
  */
  static function affected_rows( $conid ) {
    global $con;
    self::init();
    return $con->_affectedrows( $conid );
  }

  /**
  * Close Database
  *
  * @return boolean
  */
  static function disconnect( $all = false ) {
    global $con;
    self::init();

    if ( $all ) {
      foreach ( $con->connection_ids as $id )
        @$con->_disconnect( $id );

      unset ( $con->connection_ids );

      $con->connection_ids = array();

      $con->connection_id = null;
      return;
    }

    if ( $con->connection_id ) {
      $id                 = $con->connection_id;
      $con->connection_id = null;
      @$con->_disconnect( $id );
    }
  }

  /**
  * Make String SQl Safe
  *
  * @return string;
  */
  static function sql_safe( $string ) {
    global $con;
    self::init();
    return self::escape( htmlentities( strip_tags( $string ) ) );
  }

  /**
  * Escape Slashes
  *
  * @return string;
  */
  static function escape( $string ) {
    global $con;
    self::init();

    if ( ini_get( 'magic_quotes_gpc' ) )
      $string = stripslashes( $string );

    if ( $con->connection_id )
      $string = @$con->_escape( $con->connection_id, $string );

    return $string;
  }
}

/**
* Result Class
*
* Usage:
*    Result::SetResult( self::fetch_rows() );
*
*/
class Result {
  var $row  = 0;
  var $size = 0;
  var $EOF  = false;
  var $fields;
  var $result = array();

  static function NewResult() {
    global $result;

    if ( !$result )
      $result = new Result();
  }

  /**
  * Set Result
  *
  * @param array MySQL Fetch Array Result
  */
  static function SetResult( $res ) {
    global $result;

    Result::NewResult();
    Result::FreeResult();

    if ( !$res ) {
      $result->EOF = true;
      return false;
    }

    foreach ( $res as $rowid => $row ) { $result->result[$result->size++] = $row; }

    $result->fields = $result->result[$result->row];
  }

  /**
  * Move To Next Row
  *
  */
  static function MoveNext() {
    global $result;

    if ( !$result )
      Result::NewResult();

    $result->row++;

    if ( $result->row == $result->size ) {
      $result->EOF = true;
      return false;
    }

    $result->fields = $result->result[$result->row];

    return true;
  }

  /**
  * Get All Rows
  *
  * @param integer
  */
  static function GetRows() {
    global $result;

    if ( !$result )
      Result::NewResult();

    return count($result->result) == 0 ? array() : $result->result;
  }

  static function GetField( $name = "" ) {
    global $result, $con;

    if ( !$result )
      Result::NewResult();

    if ( !$name )
      return $result->fields;

    if ( isset( $result->fields[$name] ) )
      return $result->fields[$name];

    return null;
  }

  /**
  * Free Result
  *
  */
  static function FreeResult() {
    global $result;

    if ( !$result )
      Result::NewResult();

    $result->EOF  = true;
    unset ( $result->result );
    unset ( $result->fields );
    $result->row  = 0;
    $result->size = 0;
  }

  /**
  * Number Of Records
  *
  * @param integer
  */
  static function RecordCount() {
    global $result;

    if ( !$result )
      Result::NewResult();

    return $result->size;
  }

  /**
  * Number Of Fields
  *
  * @param integer
  */
  static function FieldSize() {
    global $result;

    if ( !$result )
      Result::NewResult();

    return count( $result->fields );
  }
}
?>
