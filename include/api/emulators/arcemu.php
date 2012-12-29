<?php
/***************************************************************************
*                                arcemu.php
*                            -------------------
*   Project              : UnityCMS
*   Begin                : Monday, April 26, 2010
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

class Arcemu extends BaseAccount {

  /**
    * Constructors used to set the _con & parent name
    * 
    * @param result
    * @access private
    * @return void
    */
  function __construct() { $this->parent( "arcemu" ); }

  /**
  * Login using username with/without password
  * 
  * @param string Username
  * @param string Password
  * @access public
  * @return boolean
  */
  function login( $username, $password = '' ) {
    $this->load( $username, $password );

    if ( $this->userid() <= 0 )
      return false;

    User::setkey( "guest", false );
    User::setkey( "userid", $this->userid() );
    User::setkey( "username", $this->username() );
    User::setkey( "admin", $this->gm() );
    User::setkey( "ucp", true );
    User::update();
    return true;
  }

  /**
  * load
  *
  * @param string Username
  * @param string Password
  * @access public
  * @return boolean
  */
  function load( $username, $password = '' ) {
    if ( !$username )
      return;

    if ( is_array( $username ) )
      return;

    $username = Database::sql_safe( $username );

    if ( $password != "" ) {
      $password  = Database::sql_safe( $password );
      $encrypted = $this->encryptpass( $username, $password );
      $extra     = " AND `encrypted_password` = \"" . $encrypted . "\"";
    }

    Database::query( "SELECT * FROM `accounts` WHERE `login` = \"" . $username . "\" " . $extra . " LIMIT 1", $this->_con );

    if ( Result::RecordCount() != 1 )
      return;

    $this->userid( Result::GetField( 'acct' ) );
    $this->username( Result::GetField( 'login' ) );
    $this->password( Result::GetField( 'encrypted_password' ) );
    $this->gm( Result::GetField( 'gm' ) );

    $this->lastlogin( Result::GetField( 'lastlogin' ) );
    $this->lastip( Result::GetField( 'lastip' ) );
    $this->email( Result::GetField( 'email' ) );
    $this->muted( Result::GetField( 'muted' ) );

    $this->banned( Result::GetField( 'banned' ) );

    if ( CMS::$logintype->isforum )
      $this->forumacct( Result::GetField( 'forumacct' ) );
  }

  /**
  * Check if Username Exists
  * 
  * @param string username
  * @access public
  * @return boolean
  */
  function exists( $username ) {
    $username = Database::sql_safe( $username );
    Database::query( "SELECT count(*) AS `total` FROM `accounts` WHERE `login` = \"" . $username . "\" LIMIT 1", $this->_con );

    if ( Result::GetField( 'total' ) < 1 )
      return false;

    return true;
  }

  /**
  * Parse Array Into Arcemu Class
  * 
  * @param array
  * @access public
  * @return object Arcemu
  */
  function ParseArray( $array ) {
    Result::SetResult( array($array));

    $acct = new Arcemu();
    $acct->userid( Result::GetField( 'acct' ) );
    $acct->username( Result::GetField( 'login' ) );
    $acct->password( Result::GetField( 'encrypted_password' ) );
    $acct->gm( Result::GetField( 'gm' ) );

    $acct->lastlogin( Result::GetField( 'lastlogin' ) );
    $acct->lastip( Result::GetField( 'lastip' ) );
    $acct->email( Result::GetField( 'email' ) );
    $acct->muted( Result::GetField( 'muted' ) );
    $acct->banned( Result::GetField( 'banned' ) );

    if ( CMS::$logintype->isforum )
      $acct->forumacct( Result::GetField( 'forumacct' ) );

    return $acct;
  }

  /**
  * Update Password In Database
  * 
  * @param string new password
  * @access public
  * @return boolean
  */
  function UpdatePassword( $newpass ) {
    if ( $this->userid() <= 0 )
      return false;

    $this->encryptedpass( $this->encryptpass( $this->username(), $newpass ) );
    return Database::query( "UPDATE `accounts` SET `encrypted_password` = \"" . $this->encryptedpass() . "\" WHERE `acct` = '" . $this->userid() . "' LIMIT 1", $this->_con );
  }

  /**
  * Get Total Accounts
  * 
  * @access public
  * @return integer
  */
  function getTotalAccounts() {
    Database::query( "SELECT count(*) as `total` FROM `accounts`", $this->_con );
    return Result::GetField( "total" );
  }

  /**
  * Get Total Online Characters
  * 
  * @access public
  * @return integer
  */
  function getOnlineCount() {
    Database::query( "SELECT count(*) as `total` FROM `characters` WHERE `online` > 0", $this->_con );
    return Result::GetField( "total" );
  }

  /**
  * Get Online Characters
  * 
  * @access public
  * @return array
  */
  function getOnlineList() {
    Database::query( "SELECT * FROM `characters` WHERE `online` > 0", $this->_con );
    return Result::GetRows();
  }

  /**
  * Get Linked Accounts
  * 
  * @param integer account id
  * @param boolean javascript
  * @access public
  * @return void
  */
  function getLinkedAccounts( $acctid, $js = true ) {
    if ( $acctid <= 0 )
      return;

    $field = (CMS::$logintype->isforum) ? "forumacct" : "acct";
    Database::query( "SELECT * FROM `accounts` WHERE `" . $field . "` = '" . $acctid . "'", $this->_con );

    if ( Result::RecordCount() <= 0 )
      return;

    $array = Result::GetRows();

    foreach ( $array as $arr ) {
      $acct    = $this->ParseArray( $arr );
      $accts[] = $acct;
      if ( $js )
        $acct->generatejs();
    }

    return $accts;
  }

  /**
  * Link Account
  * 
  * @param integer account id
  * @access public
  * @return boolean
  */
  function LinkAccount( $acctid ) {
    if ( $this->userid() <= 0 )
      return false;

    return Database::query( "UPDATE `accounts` SET `forumacct` = '" . $acctid . "' WHERE `acct` = '" . $this->userid() . "' LIMIT 1", $this->_con );
  }

  /**
  * Save to Database
  * 
  * @access public
  * @return boolean
  */
  function SaveToDB() {
    $sql = "INSERT INTO `accounts` SET `login` = \"" . $this->username() . "\", `encrypted_password` = \"" . $this->encryptedpass() . "\", `email` = \"" . $this->email() . "\", banned = '0', password = \"\"";

    if ( CMS::$logintype->isforum ) { $sql .= ", `forumacct` = \"" . $this->forumacct() . "\""; }

    return Database::query( $sql, $this->_con );
  }
}
?>