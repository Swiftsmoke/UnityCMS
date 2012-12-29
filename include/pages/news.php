<?php
/***************************************************************************
 *                                news.php
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

class Page {
  static $news  = array();
  static $total = 0;
  static $page  = 0;

  static function Build() {
    self::$news = self::News();
    CMS::loadPage( 'news' );
  }
  static function News() {
    $news = array();

    $news[0] = array("tid"     => 0,
                     "author"  => "ThePingue.com",
                     "uid"     => -1,
                     "replies" => 0,
                     "date"    =>@date( "d:m:Y" ),
                     "title"   => "Initial Installation",
                     "body"    =>
                         "Thank you for installing this CMS, we have tried our best to suit your needs and hope that you are satisfied with the result, we are offering you 1 months free tech support should you experience any issues please contact our dev team.");

    self::$total = count( $news );
    self::$page  = ( $_GET && $_GET['page'] ? $_GET['page'] : 0 );
    self::$page  = ( self::$page <= 1 ? 0 : self::$page - 1 );

    if ( is_callable( array(CMS::$newstype, 'gettotalthreads')) ) {
      self::$total = CMS::$newstype->gettotalthreads( CMS::$newsforum );
      $news        = CMS::$newstype->getthreads( CMS::$newsforum, self::$page, CMS::$newslimit );
    }

    return $news;
  }
}
?>