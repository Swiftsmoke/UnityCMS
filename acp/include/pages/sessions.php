<?php
/***************************************************************************
 *                                sessions.php
 *                            -------------------
 *   Project              : UnityCMS
 *   Begin                : Tuesday, April 20, 2010
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

$sessions = array();

$sessions["site"] = "Current Time <b>" . @date( "d F Y - h:i A" ) . "</b><br />";
$sessions["site"] .= "<fieldset><legend>Currently Browsing Site</legend><table cellspacing=\"5\" cellpadding=\"5\"><tr><th>Name</th><th>Location</th><th>Time</th><th>IP Address</th></tr>";
//Database::query("ALTER TABLE `session_data` ADD COLUMN `userid` INT(10) NOT NULL DEFAULT 0");
$data = session::get_sessions();

foreach ( $data as $i => $d ) {
  $sessions["site"] .= "<tr><td>";

  if ( $d["user"] ) { $sessions["site"] .= CMS::profilelink( $d["userid"], $d["user"] ); }
  else { $sessions["site"] .= "Guest"; }

  $sessions["site"] .= "</td><td>";
  $sessions["site"] .= "<a href='" . $d["location"] . "'>";

  if ( strstr( $d["location"], "=" ) )
    $sessions["site"] .= str_replace( '=', '', strchr( $d["location"], "=" ) );
  else
    $sessions["site"] .= $d["location"];

  $sessions["site"] .= "</a>";
  $sessions["site"] .= "</td><td>";
  $sessions["site"] .= @date( "h:i A", $d["session_time"] );
  $sessions["site"] .= "</td><td>";
  $sessions["site"] .= $d["ipaddress"];
  $sessions["site"] .= "</td></tr>";
}

$sessions["site"] .= "</table></fieldset>";

if ( is_callable( array(CMS::$logintype, "sessionlist")) ) {
  $data              = CMS::$logintype->sessionlist();
  $sessions["forum"] = "<fieldset><legend>Currently Browsing Forums</legend><table cellspacing=\"5\" cellpadding=\"5\"><tr><th>Name</th><th>Location</th><th>Time</th><th>IP Address</th></tr>";

  foreach ( $data as $i => $d ) {
    /// array(26) { ["id"]=> string(32) "f3c9eafaf38cc3fec2dab6a6a4d3810b" ["member_name"]=> string(0) "" ["seo_name"]=> string(0) "" ["member_id"]=> string(1) "0" ["ip_address"]=> string(14) "75.137.244.128" ["browser"]=> string(88) "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7" ["running_time"]=> string(10) "1263825087" ["login_type"]=> string(1) "0" ["location"]=> string(0) "" ["member_group"]=> string(1) "2" ["in_error"]=> string(1) "0" ["location_1_type"]=> string(5) "topic" ["location_1_id"]=> string(2) "55" ["location_2_type"]=> string(5) "forum" ["location_2_id"]=> string(1) "0" ["location_3_type"]=> string(0) "" ["location_3_id"]=> string(1) "0" ["current_appcomponent"]=> string(6) "forums" ["current_module"]=> string(6) "forums" ["current_section"]=> string(6) "topics" ["uagent_key"]=> string(7) "firefox" ["uagent_version"]=> string(1) "3" ["uagent_type"]=> string(7) "browser" ["uagent_bypass"]=> string(1) "0" ["search_thread_id"]=> string(1) "0" ["search_thread_time"]=> string(1) "0" }
    $sessions["forum"] .= "<tr><td>";

    // Name
    if ( $d["id"] != 0 && $d["name"] ) { $sessions["forum"] .= CMS::profilelink( $d["id"], $d["name"] ); }
    else { $sessions["forum"] .= "Guest"; }

    $sessions["forum"] .= "</td><td>";
    $sessions["forum"] .= $d["location"];
    $sessions["forum"] .= "</td><td>";
    $sessions["forum"] .= date( "d F Y - h:i A", $d["start"] );
    $sessions["forum"] .= "</td><td>";
    $sessions["forum"] .= $d["ip"];
    $sessions["forum"] .= "</td></tr>";
  }
  $sessions["forum"] .= "</table></fieldset>";
}
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
            <h2>Sessions</h2>

            <div id = "simpleForm">
                <form name = "sessions" method = "get" action = "<?php echo ACP_URL; ?>index.php?m=sessions">
                    <?php
                    echo $sessions["site"];

                    if ( isset( $sessions["forum"] ) )
                      echo $sessions["forum"];
                    ?>
                </form>
            </div>
        </div>
    </div>
</div>