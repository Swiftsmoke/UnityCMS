<?php
/***************************************************************************
 *                                myaccount.php
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
  
if ($accounts = SubPage::GetAccounts())
{
    function parseAcct( Trinity $i )
    {        
        echo "<fieldset>";
        echo "<label for='myuser'>Account Name: </label> " . $i->username() . "<br />";
        echo "<label for='mypass'>Password: </label> " . $i->encryptedpass() . "<br />";
        echo "<label for='myemail'>Email: </label> " . $i->email() . "<br />";
        echo "<label for='joindate'>Joined: </label> " . $i->joindate() . "<br />";
        echo "<label for='lastlogin'>Last Seen: </label> " . $i->lastlogin() . "<br />";
        echo "<label for='lastip'>Last IP: </label> " . $i->lastip() . "<br />";
        echo "</fieldset>";
    }
    
    foreach ( $accounts as $acct )
    {
        if (is_array($acct))
        {
            foreach ( $acct as $i )
            {
                parseAcct($i);
            }
        }
        else
            parseAcct($acct);
    }
}
?>