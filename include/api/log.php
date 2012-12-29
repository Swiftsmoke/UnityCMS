<?php
/***************************************************************************
 *                                log.php
 *                            -------------------
 *   Project              : UnityCMS
 *   Begin                : Sunday, May 09, 2010
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

class Log extends filehandler {
  static $enabled = true;

  static function enable( $bool = true ) { self::$enabled = $bool; }

  static function logit( $log ) {
    $filename = "log_" . @date( "d_m_Y" ) . ".txt";
    $bool     = false;

    if ( !parent::isExists( $filename, "logs" ) )
      $bool = parent::write( $filename, $log, "logs" );

    if ( $bool )
      return;

    if ( parent::checkpermission( $filename, 0777, "logs" ) )
      $bool = parent::append( $filename, $log, "logs" );

    if ( !$bool ) {
      ob_clean();
      die( "An error has occurred and we have failed to log it, if you have access to the site please chmod the logs folder to 0777 otherwise contact the site administrator" );
    }
  }

  static function arraytostring( $array ) {
    $string = "";

    if ( is_array( $array ) && count( $array ) < 20 ) {
      $x = 0;
      foreach ( $array as $k => $v ) {
        if ( !is_integer( $k ) )
          $string .= "$k =>";

        if ( is_array( $v ) || is_object( $v ) ) { $string .= "array(" . self::arraytostring( (array)$v ) . ")"; }
        else
          $string .= $v;

        $x++;
        if ( $x < count( $array ) )
          $string .= ", ";
      }
    }

    return $string;
  }

  static function error( $level, $message, $file, $line, $context ) {
    if ( self::$enabled == false )
      return;

    switch ( $level )
    {
        default:
        self::minor( $message, $file, $line, $context );
        break;

        case E_USER_ERROR:
        case E_USER_NOTICE:
        case E_WARNING:
        case E_COMPILE_ERROR:
        case E_CORE_ERROR:
        case E_ERROR:
        case E_ALL:
        self::major( $message, $context );
        break;
    }
  }

  static function minor( $message, $file, $line, $args ) {
    $os = strtolower( $_SERVER['OS'] );

    if ( strstr( $os, "windows" ) )
      $ln = "\r\n";
    else if ( strstr( $os, "mac" ) )
      $ln = "\r";
    else
      $ln = "\n";

    $string = "Function            Source File" . $ln;
    $string .= $message . "(";

    if ( $args ) {
      if ( is_array( $args ) ) { $string .= self::arraytostring( $args ); }
      else
        $string .= $args;
    }

    $string .= ")     " . $file;

    if ( $line )
      $string .= " line " . $line;

    $string .= $ln . $ln . $ln;

    self::logit( $string );
  }
  static function major( $message = '', $args = '' ) {
    $os = strtolower( $_SERVER['OS'] );

    if ( strstr( $os, "windows" ) )
      $ln = "\r\n";
    else if ( strstr( $os, "mac" ) )
      $ln = "\r";
    else
      $ln = "\n";

    $debug  = @debug_backtrace();
    $string = "Version: " . Config::$version . $ln;
    $string .= "OS: " . $os . $ln;
    $string .= "Date " . @date( "d:m:Y" ) . " ";
    $string .= "Time " . @date( "G:i" ) . $ln;
    $string .= "========================" . $ln;

    if ( isset( $message ) ) {
      $string .= $ln . "Error Report: " . $message . $ln . $ln;

      if ( isset( $args ) && is_array( $args ) )
        $string .= self::arraytostring( $args ) . $ln;
      $string .= "========================" . $ln;
    }

    $string .= "Call Stack:" . $ln;
    $string .= "Function            Source File" . $ln;

    for ( $x = 0; $x < count( $debug ); $x++ ) {
      $string .= $debug[$x]['class'] . $debug[$x]['type'] . $debug[$x]['function'];

      if ( $debug[$x]['args'] ) {
        $string .= "( ";

        if ( is_array( $debug[$x]['args'] ) )
          $string .= self::arraytostring( $debug[$x]['args'] );//$string .= implode(", ", $debug[$x]['args']);
        else
          $string .= $debug[$x]['args'];
        $string .= " )";
      }

      $string .= "    ";

      if ( $debug[$x]['file'] )
        $string .= $debug[$x]['file'];

      if ( $debug[$x]['line'] )
        $string .= " line " . $debug[$x]['line'];
      $string .= $ln;
    }

    $string .= $ln . $ln;
    self::logit( $string );

    ob_clean();
    print_r($string);
    echo "<div align='center'>A critical error has occurred, information regarding your session has been saved for a member of the team to look over, sorry for the inconvience... Please try again shortly.</div>";
    ob_flush();
    exit;
  }
}
?>