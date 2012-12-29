<?php
/***************************************************************************
 *                                baseaccount.php
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

class BaseAccount {
  /**
  * Database Connection ID
  *
  * @var resource
  * @access public
  */
  var $_con;
  var $isforum = false;

  /**
  * Banned
  *
  * @var boolean
  * @access private
  * @see banned()
  */
  var $_banned = false;

  function banned( $ban = -1 ) {
    if ( $ban != -1 )
      $this->_banned = $ban;

    return $this->_banned;
  }

  /**
  * Banned Until
  *
  * @var array
  * @access private
  * @see banneduntil()
  */
  var $_banneduntil = array();

  function banneduntil( $time = -1 ) {
    if ( $time != -1 )
      $this->_banneduntil = $time;

    return $this->_banneduntil;
  }

  /**
  * Banned Reason
  *
  * @var string
  * @access private
  * @see banreason
  */
  var $_banreason = "";

  function banreason( $reason = "" ) {
    if ( $reason != "" )
      $this->_banreason = $reason;

    return $this->_banreason;
  }

  /**
  * Email
  *
  * @var string
  * @access private
  * @see email()
  */
  var $_email = "";

  function email( $email = "" ) {
    if ( $email != "" )
      $this->_email = $email;

    return $this->_email;
  }

  /**
  * Login Encrypted Password
  *
  * @var string
  * @access private
  * @see encryptedpass()
  */
  var $_encryptedpass = "";

  function encryptedpass( $pass = -1 ) {
    if ( $pass != -1 )
      $this->_encryptedpass = $pass;

    return $this->_encryptedpass;
  }

  /**
  * Forum Account ID
  *
  * @var integer
  * @access private
  * @see forumacct()
  */
  var $_forumacct = 0;

  function forumacct( $acct = -1 ) {
    if ( $acct != -1 )
      $this->_forumacct = $acct;

    return $this->_forumacct;
  }

  /**
  * GM Flags
  *
  * @var string
  * @access private
  * @see gm()
  */
  var $_gm = "";

  function gm( $gm = -1 ) {
    if ( $gm != -1 )
      $this->_gm = $gm;

    return $this->_gm;
  }

  /**
  * Join Date
  *
  * @var date
  * @access private
  * @see joindate()
  */
  var $_joindate = '0000-00-00 00:00:00';

  function joindate( $date = -1 ) {
    if ( $date != -1 )
      $this->_joindate = $date;

    return $this->_joindate;
  }

  /**
  * Last IP
  *
  * @var string
  * @access private
  * @see lastip()
  */
  var $_lastip = "";

  function lastip( $ip = -1 ) {
    if ( $ip != -1 )
      $this->_lastip = $ip;

    return $this->_lastip;
  }

  /**
  * Last Login
  *
  * @var date
  * @access private
  * @see lastlogin()
  */
  var $_lastlogin = '0000-00-00 00:00:00';

  function lastlogin( $last = -1 ) {
    if ( $last != -1 )
      $this->_lastlogin = $last;

    return $this->_lastlogin;
  }

  /**
  * Unique Login Username
  *
  * @var string
  * @access private
  * @see username()
  */
  var $_username = "";

  function username( $name = "" ) {
    if ( $name != "" )
      $this->_username = $name;

    return $this->_username;
  }

  /**
  * Muted
  *
  * @var boolean
  * @access private
  * @see muted()
  */
  var $_muted = false;

  function muted( $muted = -1 ) {
    if ( $muted != -1 )
      $this->_muted = $muted;

    return $this->_muted;
  }

  /**
  * Muted Reason
  *
  * @var string
  * @access private
  * @see mutedreason()
  */
  var $_mutedreason = "";

  function mutedreason( $newreason = "" ) {
    if ( $newreason != "" )
      $this->_mutedreason = $newreason;

    return $this->_mutedreason;
  }

  /**
  * Muted Until
  *
  * @var array
  * @access private
  * @see muteduntil()
  */
  var $_muteduntil = array();

  function muteduntil( $newtime = -1 ) {
    if ( $newtime != -1 )
      $this->_muteduntil = $newtime;

    return $this->_muteduntil;
  }

  /**
  * Parent Class Name
  *
  * @var string
  * @access private
  * @see parent()
  */
  var $_parent;

  function parent( $newparent = "" ) {
    if ( $newparent != "" )
      $this->_parent = $newparent;

    return $this->_parent;
  }

  /**
  * Login Password
  *
  * @var string
  * @access private
  * @see password()
  */
  var $_password = "";

  function password( $newpass = "" ) {
    if ( $newpass != "" )
      $this->_password = $newpass;

    return $this->_password;
  }

  /**
  * Unique User ID
  *
  * @var integer
  * @access private
  * @see userid()
  */
  var $_userid;

  function userid( $newid = -1 ) {
    if ( $newid != -1 )
      $this->_userid = $newid;

    return $this->_userid;
  }

  /**
  * Encrypt Password with sha1 encryption
  * 
  * @param string username
  * @param string password
  * @access public
  * @return string encrypted password
  */
  function encryptpass( $username, $password ) {
    if ( $password == $this->encryptedpass() )
      return $password;

    $new_sha_pass_hash = sha1( strtoupper( $username ) . ":" . strtoupper( $password ) );
    return $new_sha_pass_hash;
  }
}
?>