<?php
/***************************************************************************
 *                                home.php
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
  die( "Invalid access.." );

function CreateForm( $array, &$cms ) {
  $form = "";

  foreach ( $array as $legend => $a ) {
    $form .= "<fieldset>";
    $form .= "<legend>" . $legend . "</legend>";

    foreach ( $a as $key => $b ) {
      $form .= "<label for=\"" . $key . "\">" . $b["label"] . ": </label>";

      switch ( $b["input"] )
      {
          case "textarea":
          {
              $form .= "<textarea name=\"" . $key . "\"";
              if ( isset( $b["extra"] ) )
                $form .= " " . $b["extra"];
              if ( isset( $b["rows"] ) )
                $form .= " rows=\"" . $b["rows"] . "\"";
              if ( !isset( $b["value"] ) && is_array( CMS::$$key ) ) {
                $b["value"] = "";
                foreach ( CMS::$$key as $site => $url ) { $b["value"] .= "$site $url\n"; }
              }
              $form .= ">" . ( isset( $b["value"] ) ? $b["value"] : CMS::$$key );
              $form .= "</textarea>";
          }
          break;

          case "select":
          {
              $form .= "<select name=\"" . $key . "\">";
              foreach ( $b["values"] as $val => $text ) {
                $form .= "<option value=\"" . $val . "\"";

                if ( $b["default"] == $val )
                  $form .= " selected";
                if ( is_array( $text ) ) { $form .= " disabled>" . $text[0] . " - " . $text[1] . "</option>"; }
                else { $form .= ">" . $text . "</option>"; }
              }
              $form .= "</select>";
          }
          break;

          default:
          {
              $form .= "<input type=\"" . $b["input"] . "\" name=\"" . $key . "\"";
              if ( isset( $b["value"] ) )
                $form .= " value=\"" . $b["value"] . "\"";
              else
                $form .= " value=\"" . CMS::$$key . "\"";
              if ( isset( $b["class"] ) )
                $form .= " class=\"" . $b["class"] . "\"";
              if ( $b["input"] == "checkbox" && CMS::$$key )
                $form .= " checked";
              if ( isset( $b["onclick"] ) )
                $form .= " onclick=\"" . $b["onclick"] . "\"";
              $form .= " />";
          }
          break;
      }
      $form .= "<br>";
    }
    $form .= "</fieldset>";
  }

  return $form;
}

$systemsettings = array();

$systemsettings["Site Settings"] = array("sitename"     => array("label" => "Site Name", "input" => "text", "class" => "medium",),
                                         "siteenabled"  => array("label" => "Enabled", "input" => "checkbox", "class" => "checkbox", "value" => "siteenabled",),
                                         "site_message" => array("label" => "Offline Message", "input" => "textarea",),
                                         "debug"        => array("label" => "Debugging Mode", "input" => "checkbox", "class" => "checkbox", "value" => "debug",),
                                         "siteloadtime" => array("label" => "Show Load Time", "input" => "checkbox", "class" => "checkbox", "value" => "siteloadtime",),
                                         "logintype"    => array("label"   => "Login Method",
                                                                 "input"   => "select",
                                                                 "values"  => array("arcemu" => "Arcemu",                                    "mangos" => "Mangos", "trinity" => "Trinity", "ipb" => array("Invision Power Board", "not installed"),
                                                                                    "phpbb"  => array("PHP Bulletin Board", "not installed"), "vb"    => array("vBulletin", "not installed"),),
                                                                 "default" => strtolower( str_replace( "Forums", "", get_class( CMS::$logintype ) ) ),),
                                         "forumurl"     => array("label" => "Forum Address", "input" => "text", "class" => "large",),
                                         "sqlextension" => array("label" => "Database Type", "input" => "select", "values" => array("mysql" => "MySQL", "mysqli" => "MySQLI",), "default" => CMS::$sqlextension,),
                                         "paypalemail"  => array("label" => "Paypal Email Address", "input" => "text",),);

$systemsettings["News Settings"] = array(
    "newstype"
                  => array("label"   => "News Type",
                           "input"   => "select",
                           "values"  => array("cms" => "Built In News", "ipb" => "Invision Power Board", "phpbb" => array("PHP Bulletin Board", "not installed"), "vb" => array("vBulletin", "not installed"),),
                           "default" => strtolower( str_replace( "Forums", "", get_class( CMS::$newstype ) ) ),),
    "useseo"      => array("label" => "Use SEO Links", "input" => "checkbox", "value" => "useseo",),
    "tableprefix" => array("label" => "Table Prefix", "input" => "text", "class" => "small",),
    "newsforum"   => array("label" => "News Forum Id", "input" => "text", "class" => "small",),
    "newslimit"   => array("label" => "News Per Page", "input" => "text", "class" => "small",),
    "showlatest"  => array("label" => "Show Latest", "input" => "select", "values" => array("None" => "None", "Posts" => "Posts", "Threads" => "Threads",), "default" => CMS::$showlatest,),);

$systemsettings["Vote Settings"] = array("votesites" => array("label" => "Vote Sites<br><small>Place each site on a seperate line <br>(Format: Name Url)</small>", "input" => "textarea", "rows" => 5,),);

/**
* Adds Configuration Variable to common.php
* 
* @param mixed $id
* @param mixed $val
*/
function addConfigVar( $settings, $key, $value, $add = true ) {
  # Modify the Settings File
  $start = strpos( $settings, "static \$$key" );

  # Found it!
  if ( $start !== false ) {
    $end = ( strpos( $settings, ';', $start ) - $start ) + 1;
    if ( strpos( $value, "array" ) !== false )
      $settings = substr_replace( $settings, "static \$$key = $value;", $start, $end );
    else
      $settings = substr_replace( $settings, "static \$$key = '$value';", $start, $end );
  }
  # Not Found It So Add It!
  else if ( $add ) {
    $start = strpos( $settings, '{' ) + 1;
    $end   = 1;
    if ( strpos( $value, "array" ) !== false )
      $settings = substr_replace( $settings, "\n  static \$$key = $value;\n", $start, $end );
    else
      $settings = substr_replace( $settings, "\n  static \$$key = '$value';\n", $start, $end );
  }

  return $settings;
}

if ( isset( $_POST['submit_settings'] ) ) {
  $settings = filehandler::read( "common.php", "include" );

  foreach ( $systemsettings as $setting ) {
    foreach ( $setting as $key => $val ) {
      if ( !isset( $_POST[$key] ) )
        continue;

      $var[$key] = Database::sql_safe( trim( $_POST[$key] ) );
      if ( $val["input"] == "checkbox" )
        $var[$key] = (bool)$var[$key];
    }
  }

  $votesites        = explode( "\n", trim( $_POST['votesites'] ) );

  $var['votesites'] = "array(";

  for ( $x = 0; $x < sizeof( $votesites ); $x++ ) {
    $site    = explode( " ", $votesites[$x] );
    $site[1] = str_replace( "\n", "", trim( $site[1] ) );
    if (!isset($site[0]) || !isset($site[1]))
    	continue;
    $var['votesites'] .= "'" . Database::sql_safe( $site[0] ) . "' => '" . Database::sql_safe( $site[1] ) . "'";
    if ( $x < sizeof( $votesites ) )
      $var['votesites'] .= ", ";
  }

  $var['votesites'] .= ")";

  foreach ( $var as $key => $value ) { $settings = addConfigVar( $settings, $key, $value, true ); }

  $access = filehandler::checkpermission( "common.php", 0755, "include" );
  if ( $access ) {
    $bool = filehandler::write( "common.php", $settings, "include" );
    if ( $bool )
      $msg = "Successfully updated settings";
    else
      $err = "failed to update settings";
  }
  else
    $err = "Unable to write to file: common.php";
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
            <h2>Site Settings</h2>

            <div id = "simpleForm">
                <form name = "system_settings" method = "POST" action = "<?php echo ACP_URL; ?>index.php?m=home">
                    <?php echo CreateForm( $systemsettings, $cms ); ?>

                    <div style = "text-align: center;">
                        <input type = "submit" name = "submit_settings" value = "Update System Settings" class = "button">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>