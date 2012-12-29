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
if (!defined("IN_NESCRIPT"))
  die("Invalid access..");

?>

<div id = "news-top"><?php echo CMS::$PageName; ?></div>
<div id = "news-bg">
    <div align="center">
        <table cellspacing="10">
            <tr><th>Change Avatar</th><th>Change Password</th><th>Change Email</th></tr>
            <tr>
                <td><a href="<?php echo ROOT_URL; ?>?ucp=myaccount&avatar"><img src="<?php echo TMP_URL; ?>images/ucp/avatar.png"></a></td>
                <td><a href="<?php echo ROOT_URL; ?>?ucp=myaccount&password"><img src="<?php echo TMP_URL; ?>images/ucp/password.png"></a></td>
                <td><a href="<?php echo ROOT_URL; ?>?ucp=myaccount&email"><img src="<?php echo TMP_URL; ?>images/ucp/email.png"></a></td>
            </tr>
        
            <!--<tr><th colspan="6">&nbsp;<br /></th></tr>
            <tr><th>Edit Settings</th><th>Edit Profile Picture</th><th>Edit Family Settings</th></tr>
            <tr>
                <td><a href="<?php echo ROOT_URL; ?>?ucp=mysettings"><img src="<?php echo TMP_URL; ?>images/ucp/editprofile.png"></a></td>
                <td><a href="<?php echo ROOT_URL; ?>?ucp=mypicture"><img src="<?php echo TMP_URL; ?>images/ucp/profilepic.png"></a></td>
                <td><a href="<?php echo ROOT_URL; ?>?ucp=myfamily"><img src="<?php echo TMP_URL; ?>images/ucp/family.png"></a></td>
            </tr>-->
        </table>
    </div>
</div>
<div id = "news-bot"></div>