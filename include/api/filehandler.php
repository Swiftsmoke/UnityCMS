<?php
/***************************************************************************
 *                                filehandler.php
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
  die ( "Invalid access.." );

class filehandler {
  static function getFilename( $filename, $directory = null ) {
    if ( $directory ) { $filename = ROOT_DIR . $directory . "/" . $filename; }
    else { $filename = ROOT_DIR . $filename; }

    return $filename;
  }

  static function isExists( $filename, $directory = null ) { return file_exists( self::getFilename( $filename, $directory ) ); }

  static function delete( $filename, $directory = null ) { unlink ( self::getFilename( $filename, $directory )); }

  static function read( $filename, $directory = null ) {
    $filename = self::getFilename( $filename, $directory );

    if ( !is_readable( $filename ) )
      return false;

    @$fd = fopen( $filename, "r" );

    if ( !$fd ) { return false; }

    $buffer = "";

    while ( !feof( $fd ) ) { $buffer .= fgets( $fd, 4096 ); }

    fclose ( $fd );
    return $buffer;
  }

  static function getModificationTime( $filename, $directory = null ) { return filemtime( self::getFilename( $filename, $directory ) ); }

  static function checkpermission( $filename, $needed = 0999, $directory = null ) {
    $filename = self::getFilename( $filename, $directory );
    $chmod    = substr( sprintf( '%o', fileperms( $filename ) ), -4 );

    if ( $needed == $chmod )
      return true;

    Log::enable( false );
    $try = chmod( $filename, $needed );
    Log::enable( true );

    if ( $try )
      return true;

    return false;
  }

  static function append( $filename, $content, $directory = null ) {
    $filename = self::getFilename( $filename, $directory );

    @$fd      = fopen( $filename, "a+" );
    fwrite( $fd, $content );
    fclose ( $fd );
    return true;
  }

  static function write( $filename, $content, $directory = null ) {
    $filename = self::getFilename( $filename, $directory );

    @$fd      = fopen( $filename, "w" );

    if ( !$fd )
      return false;

    fwrite( $fd, $content );
    fclose ( $fd );
    return true;
  }

  static function getDir( $directory = "", $get_dirs = false, $mask = null ) {
    $filename = self::getFilename( "", $directory );

    if ( $handle = opendir( $filename ) ) {
      while ( false !== ( $file = readdir( $handle )) ) {
        if ( !eregi( "^\.", $file ) ) {
          if ( $get_dirs ) {
            if ( is_dir( $filename . "/" . $file ) ) {
              if ( ( $mask and ( eregi( $mask, $file )) ) or ( !$mask ) ) { $files[] = $file; }
            }
          }
          else {
            if ( !is_dir( $filename . "/" . $file ) ) {
              if ( ( $mask and ( eregi( $mask, $file )) ) or ( !$mask ) ) { $files[] = $file; }
            }
          }
        }
      }
      closedir ( $handle );
    }

    if ( isset( $files ) ) {
      sort ( $files );
      return $files;
    }
    else { return false; }
  }

  static function saveHttp( $filename, $http_name, $directory = null ) {
    $filename = self::getFilename( $filename, $directory );

    if ( self::isExists( $filename ) )
      return false;

    copy( $_FILES[$http_name]["tmp_name"], $filename );
    return true;
  }

  static function makeDir( $filename, $directory = null ) { mkdir( self::getFilename( $filename, $directory ), 0777 ); }

  static function removeDir( $filename, $directory = null ) {
    $filename = self::getFilename( $filename, $directory );
    $content  = self::getDir( $filename );

    foreach ( $content as $fn ) { self::delete( $fn, $filename ); }

    rmdir ( $filename );
  }
  static function renameDir( $old_filename, $new_filename, $directory = null ) {
    $old_filename = self::getFilename( $old_filename, $directory );
    $new_filename = self::getFilename( $new_filename, $directory );
    rename( $old_filename, $new_filename );
  }
}
?>