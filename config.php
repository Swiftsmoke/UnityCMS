<?php
/*********************************************************************************
 *                                config.php                                     *
 *                            -------------------                                *
 *   Project              : UnityCMS                                             *
 *   Begin                : Friday, April 16, 2010                               *
 *   Copyright            : (C) 2012 Robert Lambert ( dibble1989@hotmail.co.uk ) *
 *                                                                               *
 *      The copyright to the computer program(s) herein                          *
 *      is the property of Robert Lambert                                        *
 *      The program(s) may be used and/or copied only with                       *
 *      the written permission of Robert Lambert                                 *
 *      or in accordance with the terms and conditions                           *
 *      stipulated in the agreement/contract under which                         *
 *      the program(s) have been supplied.                                       *
 *                                                                               *
 ********************************************************************************/

/**
* Protect this file with required define IN_NESCRIPT
*   This will prevent people from accessing this file remotely
*/
if ( !defined( "IN_NESCRIPT" ) )
  die( "Invalid access.." );

///////////////////////////////////////////////////////////////////////
///     BASIC SETTINGS                                              ///
///                                                                 ///
///////////////////////////////////////////////////////////////////////
global $accounts, $characters, $forum, $site, $template;
$site["host"] = "localhost";
$site["user"] = "root";
$site["pass"] = "root";
$site["db"] = "unitycms";
$forum = $site;
$accounts["host"] = "localhost";
$accounts["user"] = "root";
$accounts["pass"] = "root";
$accounts["db"] = "pantheon_accounts";
$accounts["core"] = "trinity";
$totalrealms = "1";
$characters[0]["name"] = "Realm 1";
$characters[0]["host"] = "localhost";
$characters[0]["user"] = "root";
$characters[0]["pass"] = "root";
$characters[0]["db"] = "realm1_world";
$characters[0]["core"] = "trinity";
$characters[0]["pcount"] = "50";
$sitename = "Test Site";
$template = 'default';
?>