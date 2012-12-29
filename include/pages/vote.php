<?php
/***************************************************************************
 *                                vote.php
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

class Page {
  static function Build() {
    if ( isset( $_POST['site_name'] ) ) {
      $site = $_POST['site_name'];
      $url  = CMS::$votesites[$site];
      if ( $url ) { User::redirect( $url, true ); }
    }

    echo "Voting Sites";
    echo "<div class='vote'>";
    echo "<form method='post' name='voteform'>";
    echo "<input type=\"hidden\" name=\"site_name\" id=\"site_name\" value=\"\" />";

    foreach ( CMS::$votesites as $name => $url ) { echo "<input type=\"button\" style=\"cursor: pointer;\" onclick=\"document.getElementById('site_name').value='" . $name . "';document.voteform.submit();\" value=\"" . $name . "\" /><br /><br />"; }

    echo "</form></div>";
  }
}
?>