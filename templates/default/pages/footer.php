<?php
/***************************************************************************
 *                                footer.php
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
</div>
<div id = "r-col">
    <?php
    if ($latest = Footer::getLatest())
    {
        ?>
        <div id = 'mod-top'>Latest Forum <?php echo $latest['showlatest']; ?></div>
        <div id = 'mod-bg'><?php echo $latest['latest']; ?></div>
        <div id = 'mod-bot'></div>
    <?php
    }
    if (CMS::$realmstats) {
    ?>
      <div id = "mod-top">Realm Status</div>
      <div id = "mod-bg">
        <?php
        {
            if ($sarray = Footer::realmStats())
            {
			    foreach ($sarray as $stats)
                {
                    echo ($stats['online']) ? "<span class='online-r'>" : "<span class='offline-r'>";
                    echo ucwords($stats['rname']) . "</span><br />";
                    echo "<div class='realmstatus'><div class='ponline' style='width: ".$stats['count']."%;'></div></div><br />";
                    echo $stats['online'] . "/" . $stats['max_players'] . "<br /><br />";
                }
            }
        }
        ?>
      </div>
      <div id = "mod-bot"></div>
    <?php
    }
    ?>
    <div id="mod-top">Browsing The Site</div>
    <div id="mod-bg">
    <?php Footer::GetOnlineStats(); ?>
    </div>
    <div id="mod-bot"></div>
</div>
<div style = "clear:both;"></div>
</div>
<div id = "heighter"></div>
<div id = "footer"><?php echo CMS::$sitename; ?> &copy; 2010<br />Powered by <a href = "http://thepingue.com/unitycms">Unity CMS</a><br/>
<?php
    echo Footer::LoadTime();
?>
</div>
</div>
</body>
</html>