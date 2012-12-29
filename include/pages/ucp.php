<?php
/***************************************************************************
 *                                ucp.php
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
  static function Build() {
    $isguest = User::getbool( "guest" ) ? true : false;

    if ( CMS::$logintype->isforum && !$isguest ) { return User::redirect( ROOT_URL ); }

    if ( isset( $_GET['ucp'] ) && strlen( $_GET['ucp'] ) > 0 ) {
      if ( file_exists( 'include/pages/ucp/' . $_GET['ucp'] . '.php' ) ) {
        include 'include/pages/ucp/' . $_GET['ucp'] . '.php';
        Subpage::Build();
        return;
      }
    }

    CMS::loadPage( 'ucp' );
  }
}
?>