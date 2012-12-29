<?php
/***************************************************************************
 *                                wowsignup.php
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
  die ("Invalid access..");
?>
<b><?php echo Subpage::$err; ?></b><br />
<div align="center">
<form name="myaccount" id="myaccount" method="post">
<table>
    <tr><th>Account&nbsp;</th><td><input type = "text" value = "" id = "acctname" name = "acctname"/></td></tr>
    <tr><th>Password&nbsp;</th><td><input type = "password" value = "" id = "acctpass" name = "acctpass"/></td></tr>
    <?php 
    if (isset($_GET['password']))
    {
        echo '<tr><th>New Password&nbsp;</th><td><input type = "password" value = "" id = "acctnewpass" name = "acctnewpass"/></td></tr>';
    }
    else if (isset($_GET['email']))
    {
        echo '<tr><th>New Email&nbsp;</th><td><input type = "text" value = "" id = "acctnewemail" name = "acctnewemail"/></td></tr>';
    }
    else
    {
        echo '<tr><th>Email Address&nbsp;</th><td><input type = "text" value = "" id = "acctemail" name = "acctemail"/></td></tr>';
    }
    ?>
    
    <?php if (CMS::$logintype->isforum) : ?>
    <tr>
        <th>
            Realm&nbsp;
        </th>

        <td>
            <select id = "realm" name = "realm" width = "100%">
            <?php
              foreach( Config::$accounts as $id => $a )
              {
                  echo "<option value='" . $a->id . "'>" . $a->name . "</option>";
              }
            ?>
            </select>
        </td>
    </tr>
    <?php endif; ?>
    <tr><th colspan = "3"><img onclick = "this.src='<?php echo TMP_URL; ?>images/btnOkClick.png';system.postform('myaccount');" src = "<?php echo TMP_URL; ?>images/btnOkNormal.png" onmouseover = "this.src='<?php echo TMP_URL; ?>images/btnOkHover.png'" onmouseout = "this.src='<?php echo TMP_URL; ?>images/btnOkNormal.png'" border = "0"/></th></tr>
</table>
</form>
</div>