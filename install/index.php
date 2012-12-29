<?php
/***************************************************************************
 *                                installer.php
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

session_start();

class Install {
  static $step = 0;

  static function Run() {
    if ( isset( $_GET['step'] ) )
      self::$step = $_GET['step'];

    if ( isset( $_POST ) ) {
      foreach ( $_POST as $k => $v )
        $_SESSION[$k] = $v;
    }

    if ( self::$step == 0 )
      session_unset();

    self::BuildStep( self::$step );
  }

  //This babe needs a major rewrite. Too much
  //repetitive code. But oh well, I did my best
  //for a first draft :)
  static function BuildStep( $step ) {
    $cores = array('arcemu', 'mangos', 'trinity');

    $error = false;
    $next  = $step + 1;

    if ( $step == 0 ) {
      self::showMessage( "Welcome to the NeScript CMS Installation<br />", "blue" );
      self::showMessage( "Over the next few pages you will be asked to fill in some information that will help with the running of the site.", "black" );
    }

    //Step 1
    if ( $step == 1 ) {
      $chmod = substr( sprintf( '%o', fileperms( "../logs" ) ), -4 );
      if ($chmod != '0777')
      {
          self::showMessage( "We need to be able to write to the folder `logs` which is located in the directory, please chmod the folder to 0777.", "red" );
          return;
      }

      if ( !is_writable( "../config.php" ) ) {
        if ( !@chmod( "../config.php", 0777 ) ) {
          self::showMessage( "We need to be able to write to the file `config.php` which is located in the directory, please chmod the file to 0777.", "red" );
          return;
        }
      }

      if ( !is_writable( "../include/common.php" ) ) {
        if ( !@chmod( "../include/common.php", 0777 ) ) {
          self::showMessage( "We need to be able to write to the file `common.php` which is located in the directory include, please chmod the file to 0777.", "red" );
          return;
        }
      }

      self::showMessage( "All necessary file checks have been completed, we will now proceed to the next step", "green" );
      self::redirect( "index.php?step=$next", 2 );
      return;
    }

    //Step 2
    if ( $step == 2 ) {
      if ( isset( $_POST['submit'] ) ) {
        $site_host = $_POST['site_host'];
        $site_user = $_POST['site_user'];
        $site_pass = $_POST['site_pass'];
        $site_db   = $_POST['site_db'];

        $con       = @mysql_connect( $site_host, $site_user, $site_pass );

        if ( mysql_error() ) {
          self::showMessage( "Could not open a connection to the mysql service<br />Please double check the information you provided..", "red" );
          self::redirect( "index.php?step=$step", 2 );
          return;
        }

        $db = @mysql_select_db( $site_db, $con );

        if ( mysql_error() ) {
          mysql_close( $con );
          self::showMessage( "Could not open a connection to the database<br />Please double check the information you provided..", "red" );
          self::redirect( "index.php?step=$step", 2 );
          return;
        }

        self::showMessage( "Successfully opened a connection to the mysql service.", "green" );

        mysql_query( "SET AUTOCOMMIT=0" );
        mysql_query( "START TRANSACTION" );
        mysql_query( "DROP TABLE IF EXISTS `news`", $con );
        mysql_query( "DROP TABLE IF EXISTS `session_data`", $con );
        mysql_query( "DROP TABLE IF EXISTS `member_settings`", $con );
        $q2 = mysql_query(
                  "CREATE TABLE `news` (`id` int(11) NOT NULL AUTO_INCREMENT,`author_name` tinytext NOT NULL,`author_id` int(11) NOT NULL,`post` longtext NOT NULL,`title` tinytext NOT NULL,`postdate` int(20) NOT NULL DEFAULT 0,`replies` int(5) NOT NULL DEFAULT 0,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1",
                  $con );
        $q4 = mysql_query(
                  "CREATE TABLE `session_data` (`session_id` varchar(250) NOT NULL default '',`http_user_agent` varchar(250) default NULL,`session_data` text,`session_time` int(20) default NULL,`session_expire` int(20) default NULL,`user` varchar(200) NOT NULL default 'Guest',`userid` int(10) NOT NULL default '0', `location` text,`ipaddress` varchar(25) NOT NULL DEFAULT '', PRIMARY KEY  (`session_id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1",
                  $con );
        $q6 = mysql_query(
                  "CREATE TABLE `member_settings` (`forumid` int(10) NOT NULL DEFAULT 0,`userid` int(10) NOT NULL DEFAULT 0,`avatar` text,`language` varchar(10) NOT NULL DEFAULT 'en',`firstname` varchar(255) NOT NULL DEFAULT '',`lastname` varchar(255) NOT NULL DEFAULT '',`gender` tinyint(1) NOT NULL DEFAULT 0,`url` text,`country` text,`aboutme` text) ENGINE=MyISAM DEFAULT CHARSET=latin1",
                  $con );
        if ( $q2 && $q4 && $q6 ) {
          mysql_query( "COMMIT" );
          mysql_query( "SET AUTOCOMMIT=1" );
          mysql_close( $con );
          self::showMessage( "<br />Successfully created necessary tables.", "green" );
          self::redirect( "index.php?step=$next", 2 );
          return;
        }
        else {
          mysql_query( "ROLLBACK" );
          mysql_query( "SET AUTOCOMMIT=1" );
          mysql_close( $con );
          self::showMessage( "<br />Failed to create necessary tables.. mysql returned the following: " . mysql_error(), "green" );
          return;
        }
      }
      else {
        echo '<h1>Site Database Connection information</h1>';
        echo '<table><form method=\'post\' action=\'?step=2\'>';

        self::inputCreate( 'Hostname', 'text', 'site_host' );
        self::inputCreate( 'Username', 'text', 'site_user' );
        self::inputCreate( 'Password', 'password', 'site_pass' );
        self::inputCreate( 'Database', 'text', 'site_db' );

        echo '</table><input type=\'submit\' name=\'submit\' value=\'Continue\'></form><br>';
        return;
      }
    }

    //Step 3
    if ( $step == 3 ) {
      if ( isset( $_POST ) && $_POST['totalrealms'] ) {
        self::redirect( "index.php?step=$next", 0 );
        return;
      }

      echo '<h1>Account\'s Database Connection information</h1>';
      echo '<table><form method=\'post\' action=\'?step=3\'>';

      self::inputCreate( 'Hostname', 'text', 'accounts_host' );
      self::inputCreate( 'Username', 'text', 'accounts_user' );
      self::inputCreate( 'Password', 'password', 'accounts_pass' );
      self::inputCreate( 'Database', 'text', 'accounts_db' );
      echo '<tr><td>Database Structure</td><td><select name=\'accounts_core\'>';

      foreach ( $cores as $core ) { echo '<option value=\'' . $core . '\'>' . $core . '</option>'; }

      echo '</select></td></tr>';
      self::inputCreate( 'Number of Realms', 'text', 'totalrealms', 1 );

      echo '</table><input type=\'submit\' name=\'submit\' value=\'Continue\'></form><br>';
      return;
    }

    //Step 4
    if ( $step == 4 ) {
      $realmid = 0;

      if ( isset( $_POST ) && isset( $_POST['realmid'] ) ) {
        $realmid = $_POST['realmid'];
        unset( $_SESSION['submit'] );
        unset( $_SESSION['realmid'] );

        foreach ( $_POST as $k => $v ) {
          $_SESSION[$k . "_" . $realmid] = $v;
          unset( $_SESSION[$k] );
        }

        $realmid += 1;
        if ( $realmid >= $_SESSION['totalrealms'] ) {
          self::redirect( "index.php?step=$next", 0 );
          return;
        }
      }

      echo '<h1>Character\'s ' . ( $realmid + 1 ) . ' Database Connection information</h1>';
      echo '<table><form method=\'post\' action=\'?step=4\'>';

      self::inputCreate( 'Realm Name', 'text', 'characters_name' );
      self::inputCreate( 'Hostname', 'text', 'characters_host' );
      self::inputCreate( 'Username', 'text', 'characters_user' );
      self::inputCreate( 'Password', 'password', 'characters_pass' );
      self::inputCreate( 'Database', 'text', 'characters_db' );
      echo '<tr><td>Database Structure</td><td><select name=\'characters_core\'>';

      foreach ( $cores as $core ) { echo '<option value=\'' . $core . '\'>' . $core . '</option>'; }

      echo '</select></td></tr>';
      self::inputCreate( 'Max Player Count', 'text', 'characters_pcount', 0 );
      self::inputCreate( '', 'hidden', 'realmid', $realmid );

      echo '</table><input type=\'submit\' name=\'submit\' value=\'Continue\'></form><br>';
      return;
    }

    //Step 5
    if ( $step == 5 ) {
      $error = true;

      if ( isset( $_POST ) && isset( $_POST['sitename'] ) ) {
        self::redirect( "index.php?step=$next", 0 );
        return;
      }

      echo '<h1>Site Information</h1>';
      echo '<table><form method=\'post\' action=\'?step=5\'>';

      self::inputCreate( 'Site Name', 'text', 'sitename' );

      echo '</table><input type=\'submit\' name=\'submit\' value=\'Continue\'></form><br>';
    }

    //Step 6
    //this is your fault swift. Urgh,
    //that's why you use generic config
    //array vars :P
    if ( $step == 6 ) {

      // Save to config
      $os = strtolower( $_SERVER['OS'] );

      if ( strstr( $os, "windows" ) )
        $ln = "\r\n";
      else if ( strstr( $os, "mac" ) )
        $ln = "\r";
      else
        $ln = "\n";

      unset( $_SESSION['submit'] );

      $string = "<?php" . $ln;
      $string .= "/*********************************************************************************" . $ln;
      $string .= " *                                config.php                                     *" . $ln;
      $string .= " *                            -------------------                                *" . $ln;
      $string .= " *   Project              : UnityCMS                                             *" . $ln;
      $string .= " *   Begin                : Friday, April 16, 2010                               *" . $ln;
      $string .= " *   Copyright            : (C) 2012 Robert Lambert ( dibble1989@hotmail.co.uk ) *" . $ln;
      $string .= " *                                                                               *" . $ln;
      $string .= " *      The copyright to the computer program(s) herein                          *" . $ln;
      $string .= " *      is the property of Robert Lambert                                        *" . $ln;
      $string .= " *      The program(s) may be used and/or copied only with                       *" . $ln;
      $string .= " *      the written permission of Robert Lambert                                 *" . $ln;
      $string .= " *      or in accordance with the terms and conditions                           *" . $ln;
      $string .= " *      stipulated in the agreement/contract under which                         *" . $ln;
      $string .= " *      the program(s) have been supplied.                                       *" . $ln;
      $string .= " *                                                                               *" . $ln;
      $string .= " ********************************************************************************/" . $ln . $ln;
      $string .= "/**" . $ln;
      $string .= "* Protect this file with required define IN_NESCRIPT" . $ln;
      $string .= "*   This will prevent people from accessing this file remotely" . $ln;
      $string .= "*/" . $ln;
      $string .= "if ( !defined( \"IN_NESCRIPT\" ) )" . $ln;
      $string .= "  die( \"Invalid access..\" );" . $ln . $ln;
      $string .= "///////////////////////////////////////////////////////////////////////" . $ln;
      $string .= "///     BASIC SETTINGS                                              ///" . $ln;
      $string .= "///                                                                 ///" . $ln;
      $string .= "///////////////////////////////////////////////////////////////////////" . $ln;
      $string .= "global \$accounts, \$characters, \$forum, \$site, \$template;" . $ln;

      foreach ( $_SESSION as $k => $v ) {
        $z = explode( "_", $k );
        if ( count( $z ) > 1 ) {
          if ( $z[0] == "submit" || $z[0] == "realmid" )
            continue;

          if ( count( $z ) == 2 )
            $string .= "\$" . $z[0] . '["' . $z[1] . '"] = "' . $v . '";' . $ln;
          if ( count( $z ) == 3 )
            $string .= "\$" . $z[0] . '[' . $z[2] . ']["' . $z[1] . '"] = "' . $v . '";' . $ln;
        }
        else
          $string .= "\$" . $k . ' = "' . $v . '";' . $ln;
      }

      $string .= "\$template = 'default';" . $ln . "?>" . $ln;
      $fh = fopen( "../config.php", "w" );
      fwrite( $fh, $string );
      fclose( $fh );
      @chmod("../config.php", 0644);
      if ( is_writable( "../config.php" ) ) {
          self::showMessage( "The config.php file no longer needs to be chmod 0777, we recommend you change it to 0644 to prevent others from editting the file.", "red" );
      }
      self::redirect( "index.php?step=$next", 5 );
      return;
    }

    //Step 7
    if ( $step == '7' ) {
      // Installation complete
      self::showMessage( "Installation is now complete, you will now be redirected to the admin control panel", "blue" );
      self::showMessage( "<br />You will automatically be logged in as Superadmin", "green" );
      $_SESSION["admin"]    = true;
      $_SESSION["username"] = "Superadmin";
      $_SESSION["userid"]   = 0;
      self::redirect( "../acp/index.php", 5 );
      return;
    }

    if ( !$error ) { echo '<br><a href=\'?step=' . $next . '\'>You may continue to the next step</a>'; }
  }

  //I'm lazy, so I made these >: )
  static function inputCreate( $label, $type, $name, $value = '' ) {
    echo "<tr>";
    echo "<td>" . $label . "</td><td><input type='" . $type . "' name='" . $name . "' value='" . $value . "'></td>";
    echo "</tr>";
  }

  static function showMessage( $message, $color ) { echo '<b style=\'color:' . $color . '\'>' . $message . '</b><br>'; }

  static function redirect( $url, $timer = 0 ) {
    if ( $timer < 0 )
      $timer = 0;

    if ( !headers_sent() && $timer <= 0 )
      header( 'Location: ' . $url );

    echo "
        <script type=\"text/javascript\">
            setTimeout('location = \'" . $url . "\'', ($timer * 1000));
        </script>
        <noscript><meta http-equiv=\"refresh\" content=\"$timer;url=" . $url . "\" /></noscript>";

    echo "<br /><br />Now redirecting you<br />";
    echo "<a href='$url'>Click here if you do not wish to wait</a>";
  }
  static function Secure( $s ) {
    $s = htmlentities( strip_tags( $s ) );

    if ( ini_get( 'magic_quotes_gpc' ) ) { $s = stripslashes( $s ); }

    return $s;
  }
}
?>

<html>
    <head>
        <title>NeScript Installer</title>

        <meta http-equiv = "Content-Type" content = "text/html;charset=utf-8">
        <link href = "default.css" rel = "stylesheet" type = "text/css"/>
    </head>

    <body>
        <div id = "container">
            <div id = "header">
                <h1>&nbsp; NeScript Installation Panel</h1>

                <div id = "menu">
                    <ul>
                        <li><a href = "index.php?step=0">Restart Installer</a></li>

                        <li><a href = "../index.php">Cancel Installer</a></li>
                    </ul>
                </div>
            </div>

            <div id = "content" align = "center">
                <br/>

                <br/>

                <?php
                Install::Run();
                ?>
            </div>

            <div id = "footer">NeScript &copy; 2012

                <br/>Powered by <a href = "http://thepingue.com/unitycms">NeScript CMS</a>

                <br/>
            </div>
        </div>
    </body>
</html>