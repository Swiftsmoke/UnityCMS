<?php
/***************************************************************************
 *                                check.php
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

function filepermissionCheck() {
  $checkfiles = array('config.php' => "readonly", 'include/common.php' => "writeable", 'include/pages/connecting.php' => "writeable", 'include/pages/applications.php' => "writeable", 'include/pages/rules.php' => "writeable");

  $msg = "";
  $err = "";

  // Common file & config file
  foreach ( $checkfiles as $file => $type ) {
    if ( !filehandler::isExists( $file ) ) {
      $err .= "<tr><td>$file</td><td>File is missing</td></tr>";
      continue;
    }

    $iswriteable = is_writeable( ROOT_DIR . $file );

    if ( $type == "writeable" && !$iswriteable ) {
      if ( !filehandler::checkpermission( $file, 0777 ) ) {
        $err .= "<tr><td>$file</td><td>can not be edited by the server, chmod the file to 0777</td></tr>";
        continue;
      }
    }
    else if ( $type == "readonly" && $iswriteable ) {
      if ( !filehandler::checkpermission( $file, 0644 ) ) {
        $err .= "<tr><td>$file</td><td>is editable by the server and it should not be, chmod the file to 0644</td></tr>";
        continue;
      }
    }
    $msg .= "<tr><td>$file</td><td>No Issues</td></tr>";
  }

  if ( $msg )
    $msg = "<table align='center' cellspacing='5'><tr><th>File</th><th>Issue</th></tr>" . $msg . "</table>";

  if ( $err )
    $err = "<table align='center' cellspacing='5'><tr><th>File</th><th>Issue</th></tr>" . $err . "</table>";

  return array($err, $msg);
}

//-----------------------------------------
// Check memory
//-----------------------------------------

$total_memory = "0 MB";
$avail_memory = "0 MB";

if ( strpos( strtolower( PHP_OS ), 'win' ) === 0 ) {
  $mem = shell_exec( 'systeminfo' );
  if ( isset( $mem ) ) {
    $server_reply = explode( "\n", str_replace( "\r", "", $mem ) );
    if ( count( $server_reply ) ) {
      foreach ( $server_reply as $info ) {
        if ( strstr( $info, "Total Physical Memory" ) ) { $total_memory = trim( str_replace( ":", "", strrchr( $info, ":" ) ) ); }
        if ( strstr( $info, "Available Physical Memory" ) ) { $avail_memory = trim( str_replace( ":", "", strrchr( $info, ":" ) ) ); }
      }
    }
  }
}
else {
  $mem          = @shell_exec( "free -m" );
  $server_reply = explode( "\n", str_replace( "\r", "", $mem ) );
  $mem          = array_slice( $server_reply, 1, 1 );
  $mem          = preg_split( "#\s+#", $mem[0] );

  $total_memory = ( $mem[1] ) ? $mem[1] . ' MB' : '--';
  $avail_memory = ( $mem[3] ) ? $mem[3] . ' MB' : '--';
}

$php_version        = phpversion() . " (" . @php_sapi_name() . ") ";
$cms_version        = Config::$version;
$server_software    = @php_uname();

$disabled_functions = @ini_get( 'disable_functions' ) ? str_replace( ",", ", ",@ini_get( 'disable_functions' ) ) : "";
$extensions         = get_loaded_extensions();
sort( $extensions, SORT_STRING );

$sextensions = "";

foreach ( $extensions as $ext )
  $sextensions .= $ext . "\n";

// Generate Stats
if ( is_callable( array(CMS::$logintype, "totalmembers")) ) { $stats['total_members'] = CMS::$logintype->totalmembers(); }
else { $stats['total_members'] = CMS::$logintype->getTotalAccounts(); }
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
            <h2>System Check</h2>

            <div id = "simpleForm">
                <form name = "system_settings" method = "get" action = "<?php echo ACP_URL; ?>index.php?m=check">
                    <fieldset>
                        <legend>System Info</legend>

                        <label for = "cms_version">CMS Version</label>

                        <input type = "text" class = "medium" readonly = "readonly" value = "<?php echo $cms_version; ?>" name = "cms_version">

                        <br>
                        <label for = "php_version">PHP Version</label>

                        <input type = "text" class = "medium" readonly = "readonly" value = "<?php echo $php_version; ?>" name = "php_version">

                        <br>
                        <label for = "server_software">Server Software</label>

                        <textarea name = "server_software" readonly = "readonly"><?php echo $server_software; ?></textarea>

                        <label for = "total_memory">Total Memory</label>

                        <input type = "text" class = "medium" readonly = "readonly" value = "<?php echo $total_memory; ?>" name = "total_memory">

                        <br>
                        <label for = "avail_memory">Available Memory</label>

                        <input type = "text" class = "medium" readonly = "readonly" value = "<?php echo $avail_memory; ?>" name = "avail_memory">

                        <br>
                        <label for = "extensions">Available Extensions</label>

                        <textarea name = "extensions" readonly = "readonly" rows = "<?php echo sizeof($extensions)/10; ?>"><?php echo $sextensions; ?></textarea>
                    </fieldset>

                    <br>
                    <fieldset>
                        <legend>Permission Checker</legend>

                        <?php
                        $perms = filepermissionCheck();

                        if ( isset( $perms[0] ) ) { echo "<div id=\"errorbox\">" . $perms[0] . "</div><br>"; }

                        if ( isset( $perms[1] ) ) { echo "<div id=\"messagebox\">" . $perms[1] . "</div><br>"; }
                        ?>
                    </fieldset>

                    <br>
                    <fieldset>
                        <legend>Server Statistics</legend>

                        <table cellspacing = "5" align = "center">
                            <tr><th>Total Members</th>
                            </tr>

                            <tr><td align = "center"><?php echo $stats['total_members']; ?></td>
                            </tr>

                            <?php
                            if ( isset( $stats['accounts'] ) ) {
                              foreach ( $stats['accounts'] as $name => $total )
                                echo "<tr><th>" . $name . "</th></tr><tr><td align=\"center\">" . $total . "</td></tr>";
                            }
                            ?>
                        </table>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>