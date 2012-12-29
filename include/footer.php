<?php
/***************************************************************************
 *                                footer.php
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

class Footer {
  static function Build() {
    CMS::loadPage( 'footer' );
    session_commit();
  }

  static function getLatest() {
    if ( is_callable( array(CMS::$newstype, 'getlatest')) && CMS::$showlatest != 'None' ) {
      if ( $latest = CMS::$newstype->getlatest( CMS::$showlatest ) ) { return array('showlatest' => CMS::$showlatest, 'latest' => $latest); }
    }
  }

  static function realmStats() {
    $s = array();

    foreach ( Config::$characters as $c ) {
      $tmp = $c->getcore();

      if ( !$tmp )
        continue;

      $online = $tmp->getOnlineCount();

      if ( $online < 1 )
        $online = 0;

      $count = ( $c->maxplayers > 0 ? ( $online * 100 ) / $c->maxplayers : 0 );
      $s[] = array('online' => $online, 'count' => $count, 'rname' => $c->name, 'max_players' => $c->maxplayers);
    }

    return $s;
  }

  static function GetOnlineStats() { echo session::get_users_online() . " Users browsing this site."; }
  static function LoadTime() {
    if ( CMS::$siteloadtime )
      return sprintf( "Loaded in %.4f seconds", CMS::get_microtime() - CMS::get_starttime() );

    return "";
  }
}
?>