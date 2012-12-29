<?php
/***************************************************************************
*                                common.php
*                            -------------------
*   Project              : UnityCMS
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

# Prevent duplicate loading of the file
if ( defined( "__COMMON" ) )
  return;

define( "__COMMON", 1 );

/**
*   The autoload function is introduced in php v5, and is called whenever a new class is initiated.
*   We have set this function to automatically run the function init() on any class thats loaded
*
* @param string Class Name
* @return void
* @access public
* @version 1.0.0
*/
function __autoload( $className ) {
  $className = strtolower( $className );

  $directories = array('include/api/', 'include/api/emulators/', 'include/api/forums/', 'include/database/');
  $fileNameFormats = array('%s.php', '%s.class.php', 'class.%s.php', '%s.inc.php');

  foreach ( $directories as $directory ) {
    foreach ( $fileNameFormats as $fileNameFormat ) {
      $path = ROOT_DIR . $directory . sprintf( $fileNameFormat, $className );
      if ( file_exists( $path ) ) {
        require $path;

        if ( method_exists( $className, "init" ) )
          call_user_func( array($className, "init") );
        return;
      }
    }
  }
}

/**
* Main CMS Class
*/
class CMS {
  static $paypalemail = 'wowunity@live.com';
  static $debug = '1';
  static $sqlextension = 'mysql';
  static $votesites = array('Xtremetop100' => 'http://xtremetop100.com',);
  static $logintype = 'Trinity';
  static $tableprefix = '';
  static $newstype = 'basenews';
  static $useseo = '';
  static $forumurl = 'http://thepingue.com/community';
  static $realmstats = '0';
  static $numberofrealms = '0';
  static $site_message = 'Installation needs to be run before this page can be loaded';
  static $siteenabled = '1';
  static $showlatest = 'None';
  static $newsforum = '0';
  static $news_approved = '0';
  static $newslimit = '1';
  static $isacp = false;
  static $siteloadtime = '1';
  static $sitename = 'UnityCMS';
  static $PageName = '';
  static $page  = '';
  static $starttime = 0;

  /**
  * CMS::init()
  *     The init function is used to setup all the necessary database connections for
  *     the news type and the login type
  *
  * @return void
  * @access public
  * @version 1.0.40 (beta)
  */
  static function init() {
    set_error_handler("Log::error");
    ob_start();
    date_default_timezone_set("GMT");

    if ( self::$siteenabled == '0' ) {
        $err = 'Site is currently in maintenance mode.';
        if ( self::$isacp != true )
            die( self::$site_message );
    }

    self::$starttime = self::get_microtime();

    include ROOT_DIR . "config.php";
    include ROOT_DIR . "include/api/loader.php";
    Config::Build();
    session::gc( 0 );

    $newstype         = new self::$newstype;
    $newstype->con    = ( $newstype->isforum ? Config::$forum->con() : Config::$site->con());
    $newstype->prefix = self::$tableprefix;

    if ( self::$newstype != self::$logintype ) {
      $logintype = Config::findcore( self::$logintype );

      if ( !$logintype)
      {
        $className         = ucfirst( self::$logintype );
        $logintype         = new $className;
        $logintype->_con   = Config::$forum->con();
      }
      $logintype->prefix = self::$tableprefix;
    }
    else
      $logintype = &$newstype;

    self::$newstype  = &$newstype;
    self::$logintype = &$logintype;
  }

  /**
  *     This function is used to return CMS::$starttime
  *
  * @access static
  * @return microtime
  * @version 1.0.40 (beta)
  */
  static function get_starttime()
  {
      return self::$starttime;
  }

  /**
  * CMS::profilelink()
  *     The profilelink function is a necessary call to generate the links for the profile system
  *     If theres a specific news type (ipb, php, vb, etc) then it automatically generates the urls to their forum profiles
  *
  * @param integer userid
  * @param string username
  * @return string
  * @access static
  * @version 1.0.0 (beta)
  */
  static function profilelink( $uid, $name ) {
    if ( is_callable( array(self::$logintype, "profilelink")) )
      return self::$logintype->profilelink( $uid, $name, self::$useseo );

    if ( !isset($uid) || !is_numeric($uid) || $uid <= 0 )
      return $name;

    return "<a href='" . ROOT_URL . "index.php?profile=" . $uid . "'>" . $name . "</a>";
  }

  /**
  * CMS::newmessages()
  *     The new messages function is to generate the inbox total for the default theme
  *
  * @param boolean guest
  * @return boolean
  * @access static
  * @version 1.0.0 (beta)
  */
  static function newmessages( $guest = false ) {
    if ( is_callable( array(self::$logintype, "newmessages")) )
      return self::$logintype->newmessages( $guest );

    return;
  }

  /**
  * CMS::reglink()
  *     The reglink is used to generate the necessary registration link
  *     if its a forum login type then it sends you to the forum login
  *
  * @param boolean guest
  * @return string
  * @access static
  * @version 1.0.0 (beta)
  */
  static function reglink( $guest = false ) {
    if ( is_callable( array(self::$logintype, "reglink")) )
      return self::$logintype->reglink( $guest );

    return ROOT_URL . "index.php?ucp=myaccount";
  }

  /**
  * CMS::get_microtime()
  *     Gets the current microtime, useful for generating the page load time
  *
  * @return double microtime
  * @access static
  * @version 1.0.0 (beta)
  */
  static function get_microtime() {
    $mtime = microtime();
    $mtime = explode( " ", $mtime );
    $mtime = (double)( $mtime[1] ) + (double)( $mtime[0] );
    return ( $mtime );
  }

  /**
  * CMS::getServerURL()
  *     The getServerURL will generate the base url aswell as the necessary port
  *     example: CMS::getServerURL() will return http://localhost or https://localhost if https is enabled
  *
  * @return string
  * @access static
  * @version 1.0.0 (beta)
  */
  static function getServerURL() {
    $serverName = 'localhost';

    if ( isset( $_SERVER['SERVER_NAME'] ) ) { $serverName = $_SERVER['SERVER_NAME']; }
    elseif ( isset( $_SERVER['HTTP_HOST'] ) ) { $serverName = $_SERVER['HTTP_HOST']; }
    elseif ( isset( $_SERVER['SERVER_ADDR'] ) ) { $serverName = $_SERVER['SERVER_ADDR']; }

    $serverProtocol = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https' : 'http';
    $serverPort     = NULL;

    if ( isset( $_SERVER['SERVER_PORT'] ) && !strpos( $serverName, ':' ) && ( ( $serverProtocol == 'http' && $_SERVER['SERVER_PORT'] != 80 ) || ( $serverProtocol == 'https' && $_SERVER['SERVER_PORT'] != 443 ) ) )
      $serverPort = ':' . $_SERVER['SERVER_PORT'];

    return $serverProtocol . '://' . $serverName . $serverPort;
  }

  /**
  * CMS::getBaseDir()
  *     The getBaseDir will generate the base directory that will be used to open every file
  *     example: CMS::getBaseDir() will return c:\myfile\cms/
  *
  * @return string
  * @access static
  * @version 1.0.0 (beta)
  */
  static function getBaseDir() {
    $serverDir = $_SERVER['DOCUMENT_ROOT'];

    if ( $serverDir ) {
      $length = strlen( $serverDir ) - 1;

      if ( $serverDir[$length] == '/' )
        $serverDir[$length] = '';
      $serverDir = trim( $serverDir );
    }

    $scriptPath = NULL;

    if ( isset( $_SERVER['SCRIPT_NAME'] ) ) {
      $scriptPath = $_SERVER['SCRIPT_NAME'];
      $scriptPath = ( $scriptPath == '/' ) ? '' : dirname( $scriptPath );
      $scriptPath = str_replace( "/acp", "", $scriptPath );
      $scriptPath = trim( $scriptPath );
    }

    $length = strlen( $scriptPath ) - 1;

    if ( strlen($scriptPath) > 0 && $scriptPath[$length] == '/' )
      $scriptPath[$length] = '';

    $scriptPath = trim( $scriptPath );

    $baseDIR    = $serverDir . $scriptPath . '/';
    return $baseDIR;
  }

  /**
  * CMS::getBaseURL()
  *     This function uses the CMS::getServerURL() to generate the base url, how this is done is by combining
  *     the server url with the current script_name to generate the directory root, this allows for the following to work
  *     http://localhost:8080/unitycms/beta/ or http://localhost/
  *
  * @return string
  * @access static
  * @version 1.0.0 (beta)
  */
  static function getBaseURL() {
    $serverURL  = self::getServerURL();
    $scriptPath = NULL;

    if ( isset( $_SERVER['SCRIPT_NAME'] ) ) {
      $scriptPath = $_SERVER['SCRIPT_NAME'];
      $scriptPath = ( $scriptPath == '/' ) ? '' : dirname( $scriptPath );

      if ( $scriptPath == '\\' )
        $scriptPath = '';

      $scriptPath = str_replace( "/acp", "", $scriptPath );
      $scriptPath = trim( $scriptPath );
    }

    $baseURL = $serverURL . $scriptPath . '/';
    return $baseURL;
  }

  /**
  * CMS::getPage()
  *     Is used to generate the $page and $PageName
  *
  * @return void
  * @access static
  * @version 1.0.0 (beta)
  */
  static function getPage() {
    self::$PageName = self::$sitename . " :: ";
    self::$page     = "news";

    $titles = array("news" => "Home", "ucp" => "User Control Panel", "connecting" => "How to Connect", "rules" => "Server Rules", "applications" => "How To Apply", "donate" => "Donate", "vote" => "Vote");

    foreach ( $_GET as $key => $value ) {
      if ( $key && $key != "logout" ) {
        self::$page = $key;

        if ( isset($titles[$key]) )
          self::$PageName .= $titles[$key];
        return;
      }
    }

    self::$PageName .= "Home";
    return;
  }

  /**
  * CMS::loadPage()
  *     Used to load the template file, this needs improved more
  *
  * @param string file name
  * @access static
  * @version 1.0.0 (beta)
  */
  static function loadPage( $name ) {
    if ( file_exists( TMP_DIR . 'pages/' . $name . '.php' ) )
      include( TMP_DIR . 'pages/' . $name . '.php' );
  }

  /**
  * CMS::pagination()
  *     The system of numbering pages
  *     example: << Previous, 1, 2, 3, Next >>
  *
  * @param string url
  * @param integer current page
  * @param integer top page
  * @return string
  * @access static
  * @version 1.0.0 (beta)
  */
  static function pagination( $url, $currentpage = 0, $toppage = 1 ) {
    $pagelinks   = "";
    $theprevpage = $currentpage - 1;
    $thenextpage = $currentpage + 1;

    $pagelinks .= "<span>";

    if ( $currentpage > 1 ) {
      $pagelinks .= "<a href='$url&page=1' title='first page'>First</a>&nbsp;";
      $pagelinks .= "<a href='$url&page=$theprevpage'>&laquo; Previous</a>&nbsp;";
    }

    $counter    = 0;
    $lowercount = $currentpage - 5;

    if ( $lowercount <= 0 )
      $lowercount = 1;

    while ( $lowercount < $currentpage ) {
      $pagelinks .= "<a href='$url&page=$lowercount'>$lowercount</a>&nbsp;";
      $lowercount++;
      $counter++;
    }

    $pagelinks .= "<strong>&nbsp;$currentpage </strong>&nbsp;";

    $uppercounter = $currentpage + 1;

    while ( ( $uppercounter < $currentpage + 10 - $counter ) && ( $uppercounter <= $toppage ) ) {
      $pagelinks .= "<a href='$url&page=$uppercounter'>$uppercounter</a>&nbsp;";
      $uppercounter++;
    }

    if ( $currentpage < $toppage ) {
      $pagelinks .= "<a href='$url&page=$thenextpage'>Next &raquo;</a>&nbsp;";
      $pagelinks .= "<a href='$url&page=$toppage' title='last page'>Last</a>&nbsp;";
    }

    $pagelinks .= "</span>";
    return $pagelinks;
  }
}

define( "ROOT_DIR", CMS::getBaseDir() );
define( "ROOT_URL", CMS::getBaseURL() );
CMS::$isacp = isset( $isacp ) ? $isacp : false;
CMS::init();
define( "TMP_DIR", ROOT_DIR . 'templates/' . $template . '/' );
define( "TMP_URL", ROOT_URL . 'templates/' . $template . '/' );
define( "ACP_DIR", ROOT_DIR . "acp/" );
define( "ACP_URL", ROOT_URL . "acp/" );
define( "FORUM_ADDR", CMS::$forumurl );
?>