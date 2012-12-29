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
if (!defined("IN_NESCRIPT"))
  die ("Invalid access..");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv = "Content-Type" content = "text/html; charset=utf-8"/>
        <title><?php echo CMS::$PageName; ?></title>
        <link href = "<?php echo TMP_URL; ?>style/main.css" rel = "stylesheet" type = "text/css"/>
        <script type = "text/javascript" src = "<?php echo ROOT_URL; ?>js/system.js"></script>
    </head>

    <body>
        <div id = "top">
            <?php
            if (isset($_POST['loginsubmit']))
              $err = User::login($_POST['loginuname'], $_POST['loginpword']);
            else if (isset($_GET['logout']))
              User::logout();
              
            echo "<div id='fg-left'>Welcome back " . CMS::profilelink((CMS::$logintype->isforum ? User::getint("forumid") : User::getint("userid")), User::getstring("username"));
            echo CMS::newmessages(User::getbool("guest"));
            echo "</div>";
            ?>
            <div id = "fg-right">
                <?php
                if (User::getbool("admin")) { echo "<a href='" . ACP_URL . "'>Admin Control Panel</a> | "; }
                if (User::getbool("ucp")) { echo "<a href='" . ROOT_URL . "?ucp'>User Control Panel</a> | "; }
                if (User::getbool("guest") == false) { echo "<a href='" . ROOT_URL . "?logout=true'>Logout</a>"; }
                ?>
            </div>
            <div style = "clear:both;"></div>
        </div>

        <div id = "content">
            <div id = "navi-area">
                <a href = "<?php echo ROOT_URL; ?>"><div id = "navi1"></div></a>
                <a href = "<?php echo FORUM_ADDR; ?>"><div id = "navi2"></div></a>
                <?php echo "<a href = \"" . CMS::reglink(User::getbool("guest")) . "\"><div id = \"navi3\"></div></a>";?>
                <a href = "<?php echo ROOT_URL;?>?connecting"><div id = "navi4"></div></a>
                <?php echo "<a href = \"" . CMS::reglink(User::getbool("guest")) . "\"><div id = \"navi5\"></div></a>";?>
                <a href = "<?php echo ROOT_URL; ?>?rules"><div id = "navi6"></div></a>
                <a href = "<?php echo ROOT_URL; ?>?donate"><div id = "navi7"></div></a>
                <a href = "<?php echo ROOT_URL; ?>?applications"><div id = "navi8"></div></a>
                <a href = "<?php echo ROOT_URL; ?>?vote"><div id = "navi9"></div></a>
                
                <div style = "clear:both;"></div>
            </div>
            <div id = "banner"></div>
            <?php
            if (isset($err))
                echo "<div align='center' style='background-color: #FFC0C0;'>".$err."</div>";
            ?>

            <div id = "columns">
                <div id = "l-col">
                <?php
                if (User::getbool("guest")) {
                  echo "<div id='mod-top'>Login</div>";
                  echo "<div id='mod-bg'>";
                  echo "<form action='" . ROOT_URL . "' method='post' name='loginform'>";
                  echo "<input type='text' value='username...' onfocus='if (this.value == \"username...\") this.value = \"\";' onblur='if (!this.value) this.value = \"username...\";' name='loginuname' /><br />";
                  echo "<input type='password' value='password...' onfocus='if (this.value == \"password...\") this.value = \"\";' onblur='if (!this.value) this.value = \"password...\";' name='loginpword' /><br />";
                  echo "<input type='submit' name='loginsubmit' id='button' value='Login' /><br />";
                  echo "</form></div><div id='mod-bot'></div>";
                }
                ?>

                <div id = "mod-top">Navigation</div>
                <div id = "mod-bg">
                    <div class = "mini-nav">
                        <a href = "<?php echo ROOT_URL; ?>">Home</a><img src = "<?php echo TMP_URL; ?>images/separator.png" width = "163" height = "9" alt = "separator"/>
                        <a href = "<?php echo FORUM_ADDR; ?>">Forums</a><img src = "<?php echo TMP_URL; ?>images/separator.png" width = "163" height = "9" alt = "separator"/>
                        <?php echo "<a href = \"" . CMS::reglink(User::getbool("guest")) . "\">" . (User::getbool("guest") ? "Register" : "Account") . "</a><img src = \"" . TMP_URL . "images/separator.png\" width = \"163\" height = \"9\" alt = \"separator\" />"; ?>
                        <?php Database::query("SELECT * FROM navigation", Config::$site->con());
			                $nav = Result::GetRows();
			                foreach ( $nav as $i => $d )
			                {
                				echo '<a href = "' . ROOT_URL . '?page=' . $d['title'].'">' . $d['title'] . '</a><img src = "' . TMP_URL . 'images/separator.png" width = "163" height = "9" alt = "separator"/>';
							}
		                ?>
                        <a href = "<?php echo ROOT_URL; ?>?connecting">Connecting</a><img src = "<?php echo TMP_URL; ?>images/separator.png" width = "163" height = "9" alt = "separator"/>
                        <a href = "<?php echo ROOT_URL; ?>?rules">Server Rules</a><img src = "<?php echo TMP_URL; ?>images/separator.png" width = "163" height = "9" alt = "separator"/>
                        <a href = "<?php echo ROOT_URL; ?>?donate">Donate</a><img src = "<?php echo TMP_URL; ?>images/separator.png" width = "163" height = "9" alt = "separator"/>
                        <a href = "<?php echo ROOT_URL; ?>?applications">Applications</a><img src = "<?php echo TMP_URL; ?>images/separator.png" width = "163" height = "9" alt = "separator"/>
                        <a href = "<?php echo ROOT_URL; ?>?vote">Voting Sites</a>
                    </div>
                </div>

                <div id = "mod-bot"></div>
            </div>

            <div id = "m-col">