<?php
/***************************************************************************
 *                                myaccount.php
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

class Subpage {
  static $err     = "";
  static $avatars = array();
  static $myavatar = "";

  static function Build() {
    if ( isset( $_GET['password'] ) ) {
      self::$err = self::ChangePassword();
      CMS::loadPage( 'ucp/wowsignup' );
      return;
    }

    if ( isset( $_GET['email'] ) ) {
      self::$err = self::ChangeEmail();
      CMS::loadPage( 'ucp/wowsignup' );
      return;
    }

    if ( isset( $_GET['avatar'] ) && User::getbool( "guest" ) == false ) {
      self::$err = self::ChangeAvatar();
      CMS::loadPage( 'ucp/changeavatar' );
      return;
    }

    CMS::loadPage( 'ucp/myaccount' );

    if ( CMS::$logintype->isforum && User::getbool( "guest" ) == true )
      return;

    if ( !CMS::$logintype->isforum && User::getbool( "guest" ) == false )
      return;

    self::$err = self::CreateAccount();
    CMS::loadPage( 'ucp/wowsignup' );
  }

  static function GetAccounts() {
    $accounts = array();

    if ( CMS::$logintype->isforum ) {
      foreach ( Config::$accounts as $a ) {
        $tmp        = $a->getcore();
        $accounts[] = $tmp->getLinkedAccounts( User::getint( "forumid" ) );
      }
    }
    else if ( User::getbool( "guest" ) == false ) { $accounts = CMS::$logintype->getLinkedAccounts( User::getint( "userid" ) ); }

    return $accounts;
  }

  static function CreateAccount() {
    if ( !isset( $_POST['acctname'] ) || !isset( $_POST['acctpass'] ) || ( CMS::$logintype->isforum && !isset( $_POST['realm'] )) )
      return;

    $acctname = Database::sql_safe( $_POST['acctname'] );
    $acctpass = Database::sql_safe( $_POST['acctpass'] );
    $email    = Database::sql_safe( $_POST['acctemail'] );

    if ( CMS::$logintype->isforum ) {
      if ( !isset( $_POST['realm'] ) )
        return User::redirect( ROOT_URL );
      $tmp = Config::$accounts[$_POST['realm']]->getcore();
    }
    else { $tmp = CMS::$logintype; }

    if ( $tmp->exists( $acctname ) )
      return 'Error: That account name is already in use..';

    $tmp->username( $acctname );
    $pass = $tmp->encryptpass( $acctname, $acctpass );
    $tmp->encryptedpass( $pass );
    $tmp->email( $email );

    if ( CMS::$logintype->isforum )
      $tmp->setforumacct( User::getint( "forumid" ) );

    $tmp->SaveToDB();

    // Auto Login
    if ( !CMS::$logintype->isforum ) {
      User::login( $acctname, $acctpass );
      return User::redirect( ROOT_URL );
    }
  }

  static function ChangePassword() {
    if ( !isset( $_POST['acctname'] ) || !isset( $_POST['acctpass'] ) || !isset( $_POST['acctnewpass'] ) || ( CMS::$logintype->isforum && !isset( $_POST['realm'] )) )
      return;

    $acctname    = Database::sql_safe( $_POST['acctname'] );
    $acctpass    = Database::sql_safe( $_POST['acctpass'] );
    $acctnewpass = Database::sql_safe( $_POST['acctnewpass'] );

    if ( CMS::$logintype->isforum ) {
      if ( !$_POST['realm'] )
        return User::redirect( ROOT_URL );
      $tmp = Config::$accounts[$_POST['realm']]->getcore();
    }
    else { $tmp = CMS::$logintype; }

    if ( !$tmp->exists( $acctname ) )
      return 'Error: That account name does not exist...';

    $tmp->load( $acctname, $acctpass );

    if ( $tmp->userid() <= 0 )
      return 'Error: That account name & password does not match...';

    $tmp->UpdatePassword( $acctnewpass );
    return 'Successfully updated password.';
  }

  static function ChangeEmail() {
    if ( !isset( $_POST['acctname'] ) || !isset( $_POST['acctpass'] ) || !isset( $_POST['acctnewemail'] ) || ( CMS::$logintype->isforum && !isset( $_POST['realm'] )) )
      return;

    $acctname     = Database::sql_safe( $_POST['acctname'] );
    $acctpass     = Database::sql_safe( $_POST['acctpass'] );
    $acctnewemail = Database::sql_safe( $_POST['acctnewemail'] );

    if ( CMS::$logintype->isforum ) {
      if ( !$_POST['realm'] )
        return User::redirect( ROOT_URL );
      $tmp = Config::$accounts[$_POST['realm']]->getcore();
    }
    else { $tmp = CMS::$logintype; }

    if ( !$tmp->exists( $acctname ) )
      return 'Error: That account name does not exist...';

    $tmp->load( $acctname, $acctpass );

    if ( $tmp->userid() <= 0 )
      return 'Error: That account name & password does not match...';

    $tmp->UpdateEmail( $acctnewemail );
    return 'Successfully updated email address.';
  }
  static function ChangeAvatar() {
    # Get Users Current Avatar
    self::$myavatar = User::getavatar();

    # Show Avatar List
    $allowed_types = array('png', 'jpg', 'jpeg', 'gif');

    $dir = @opendir( ROOT_DIR . "uploads/avatars" );

    if ( !$dir )
      trigger_error( "Unable to open avatar directory..", E_USER_ERROR );

    $avatararray = array();

    while ( $imgfile = readdir( $dir ) ) {
      if ( !in_array( strtolower( substr( $imgfile, -3 ) ), $allowed_types ) )
        continue;
      $avatararray[] = $imgfile;
    }

    sort( $avatararray );
    self::$avatars = $avatararray;

    # Form Submitted -- Upload Avatar
    if ( isset( $_POST["newavatar"] ) ) {
      $uploadedimage = $_FILES['newavatar']['tmp_name'];
      if ( $uploadedimage != "" ) {
        $theavatar    = User::getint( "userid" );
        $theimageinfo = getimagesize( $uploadedimage );

        switch ( $theimageinfo[2] )
        {
            case 1:
            $theavatar .= ".gif";
            break;

            case 2:
            $theavatar .= ".jpg";
            break;

            case 3:
            $theavatar .= ".png";
            break;

            default: return "Invalid avatar type";
        }


        # Delete old avatar
        if ( $customavatar == "1" ) {
          if ( $avatar != "" ) { unlink( $avatar ); }
        }

        move_uploaded_file( $_FILES['newavatar']['tmp_name'], ROOT_DIR . "uploads/avatars/" . $theavatar );
        if ( file_exists( ROOT_DIR . "uploads/avatars/" . $theavatar ) ) {
          // Update in database.. $theavatar & custom = 1
          $field = CMS::$logintype->isforum ? "forumid" : "userid";
          Database::query( "UPDATE `member_settings` SET avatar = \"" . $theavatar . "\" WHERE `" . $field . "` = '" . User::getint( $field ) . "' LIMIT 1", Config::$site->con() );
          self::$myavatar = $theavatar;
        }
      }
    }

    # Form Submitted -- Change Avatar
    if ( isset( $_POST["changeavatar"] ) ) {
      $selectedavatar = $_POST["selectedImg"];

      # No Selected Avatar
      if ( $selectedavatar == "" )
        $selectedavatar = "noavatar.gif";

      // Update in database $selectedavatar
      $field = CMS::$logintype->isforum ? "forumid" : "userid";
      Database::query( "UPDATE `member_settings` SET avatar = \"" . $selectedavatar . "\" WHERE `" . $field . "` = '" . User::getint( $field ) . "' LIMIT 1", Config::$site->con() );
      self::$myavatar = $selectedavatar;
    }
  }
}
?>