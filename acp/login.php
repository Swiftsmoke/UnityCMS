<?php
/***************************************************************************
 *                                login.php
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
include ( "../include/common.php" );
include ( ACP_DIR . "include/header.php" );

if ( isset( $_POST['submit_login'] ) ) { $err = User::login( $_POST['username'], $_POST['password'] ); }
if ( !isset($err) || !$err )
{
	if ( User::checkadmin( false ) )
  		return User::redirect( ACP_URL );
  	else if ( User::getbool('guest') == false )
  		$err = 'Error: You do not have permission to access this section.';
}
?>

<div id = "login_errors" align = "center">
    <?php
    if ( isset( $err ) )
      echo "<div id=\"errorbox\">" . $err . "</div>";
    ?>
</div>

<div id = "login" align = "center">
    <div id = "simpleForm">
        <form name = "logig" method = "POST" action = "login.php">
            <label for = "username">Username:</label>

            <input name = "username" type = "text" class = "large" style = "width: 65%;">

            <br>
            <label for = "password">Password:</label>

            <input name = "password" type = "password" class = "large" style = "width: 65%;">

            <div style = "text-align: center;">
                <input type = "submit" name = "submit_login" value = "Login" class = "button">
            </div>
        </form>
    </div>
</div>

</div>

</div>

<?php
include ACP_DIR . "include/footer.php";
?>