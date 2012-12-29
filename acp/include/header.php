<?php
/***************************************************************************
 *                                header.php
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
?>

<html>
    <head>
        <title><?php echo CMS::$PageName; ?></title>

        <meta http-equiv = "Content-Type" content = "text/html;charset=utf-8">
        <link href = "<?php echo ACP_URL; ?>admin.css" rel = "stylesheet" type = "text/css"/>

        <script type = "text/javascript" src = "<?php echo ROOT_URL; ?>js/system.js"></script>
    </head>

    <body>
        <div id = "container">
            <div id = "header">
                <h1>&nbsp; <?php echo CMS::$sitename; ?> Administration Panel</h1>

                <div id = "menu">
                    <ul>
                        <li><a href = "<?php echo ROOT_URL; ?>">Home</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php"><?php echo ( isset( $file ) && $file == "home" ? "<span>ACP Home</span>" : "ACP Home" ); ?></a></li>

                        <li><a href = "<?php echo ROOT_URL; ?>?logout=true">Logout</a></li>
                    </ul>
                </div>
            </div>

            <div id = "content">
                <?php
                if ( User::getbool( "admin" ) != true )
                  return;
                ?>

                <div id = "leftnav">
                    <h2><img src = "<?php echo ACP_URL; ?>images/icons/advertise.png" align = "absmiddle"> Content</h2>

                    <ul>
                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=news">Manage News</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=navigation">Manage Navigation</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=static">Static Pages</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=modules">Modules</a></li>
                    </ul>

                    <h2><img src = "<?php echo ACP_URL; ?>images/icons/settings.png" align = "absmiddle"> Settings</h2>

                    <ul>
                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=home">Site Settings</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=realms">Realm Settings</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=check">System Check</a></li>
                    </ul>

                    <h2><img src = "<?php echo ACP_URL; ?>images/icons/security.png" align = "absmiddle"> Security</h2>

                    <ul>
                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=permissions">Permissions</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=sessions">Sessions</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=bans">Bans</a></li>
                    </ul>

                    <h2><img src = "<?php echo ACP_URL; ?>images/icons/packages.png" align = "absmiddle"> Packages</h2>

                    <ul>
                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=donation&items">Donation Items</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=donation&packs">Donation Packs</a></li>
                    </ul>

                    <h2><img src = "<?php echo ACP_URL; ?>images/icons/users.png" align = "absmiddle"> Users</h2>

                    <ul>
                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=users">Find User</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=users&a=edit">Edit User</a></li>
                    </ul>

                    <h2><img src = "<?php echo ACP_URL; ?>images/icons/groups.png" align = "absmiddle"> Groups</h2>

                    <ul>
                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=groups">View Groups</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=groups&a=edit">Edit Groups</a></li>
                    </ul>

                    <h2><img src = "<?php echo ACP_URL; ?>images/icons/emails.png" align = "absmiddle"> Mail Settings</h2>

                    <ul>
                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=mail">Mail Configuration</a></li>

                        <li><a href = "<?php echo ACP_URL; ?>index.php?m=readmail">View Messages</a></li>
                    </ul>
                </div>