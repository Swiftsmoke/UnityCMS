<?php
/***************************************************************************
 *                                index.php
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
* Protect our files by setting a define to IN_NESCRIPT
*   This will prevent people from accessing files that we dont want them to access
* 
* Usage: if ( ! defined( "IN_NESCRIPT" ) ) die( "Invalid access.." );
*/
define( "IN_NESCRIPT", true, true );

$isacp = true;
include( "../include/common.php" );
User::checkadmin();

if (filehandler::isExists("", "install"))
    $err = "Warning install directory needs renamed or deleted to prevent a security threat";
CMS::$PageName = "Administrator Control Panel";

$file          = ( isset( $_GET['m'] ) ? $_GET['m'] : "home" );

include( ACP_DIR . "include/header.php" );

if ( file_exists( ACP_DIR . "include/pages/" . $file . ".php" ) )
  include( ACP_DIR . "include/pages/" . $file . ".php" );
else {
  $err = "You need the full version of UnityCMS inorder to view this page.";
?>

  <div id = "rightcontent">
    <?php
      if ( isset( $err ) )
        echo "<div id=\"errorbox\">" . $err . "</div>";
      if ( isset( $msg ) )
        echo "<div id=\"messagebox\">" . $msg . "</div>";
    ?>

        <div id = "right">
            <div align = "center">
            </div>
        </div>
  </div>

<?php
}

include( ACP_DIR . "include/footer.php" );
?>