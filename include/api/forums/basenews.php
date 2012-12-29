<?php
/***************************************************************************
 *                                basenews.php
 *                            -------------------
 *   Project              : UnityCMS
 *   Begin                : Sunday, May 09, 2010
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

class basenews {
  var $prefix  = "";
  var $con;
  var $isforum = false;

  function code_box( $text ) { return "<div class=\"code\"><h6>Code</h6>\\1</div>"; }

  function quote( $text ) { return "<blockquote><h6>Quote:</h6>\\1</blockquote>"; }

  function parse( $text ) {
    $text = str_replace( "<#EMO_DIR#>", "default", $text );
    $text = " " . $text;

    // First: If there isn't a "[" and a "]" in the message, don't bother.
    if ( !( strpos( $text, "[" ) && strpos( $text, "]" )) )
      return $text;

    $text = stripslashes( $text );
    $text = preg_replace( "/\\[b\\](.+?)\[\/b\]/is", '<b>\1</b>', $text );
    $text = preg_replace( "/\\[center\\](.+?)\[\/center\]/is", '<span align="center">\1</span>', $text );
    $text = preg_replace( "/\\[i\\](.+?)\[\/i\]/is", '<i>\1</i>', $text );
    $text = preg_replace( "/\\[u\\](.+?)\[\/u\]/is", '<u>\1</u>', $text );
    $text = preg_replace( "/\[s\](.+?)\[\/s\]/is", '<s>\1</s>', $text );
    $text = preg_replace( "/\[list\](.+?)\[\/list\]/is", '<ul>\1</ul>', $text );
    $text = preg_replace( "/\[list=(.+?)\](.+?)\[\/list\]/is", '<ol type="\1" style="padding-left: 25px">\2</ol>', $text );
    $text = preg_replace( "/\[\*\](.*)/", '<li>\1</li>', $text );
    $text = preg_replace( "/\[code\](.+?)\[\/code\]/is", "" . $this->code_box( '\\1' ) . "", $text );
    $text = preg_replace( "/\[quote\](.+?)\[\/quote\]/is", "" . $this->quote( '\\1' ) . "", $text );
    $text = eregi_replace( "\\[img]([^\\[]*)\\[/img\\]", "<img src=\"\\1\">", $text );
    $text = eregi_replace( "\\[font=([^\\[]*)\\]([^\\[]*)\\[/font\\]", "<font style=\"font-family:\\1\">\\2</font>", $text );
    $text = eregi_replace( "\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]", "<font color=\"\\1\">\\2</font>", $text );
    $text = eregi_replace( "\\[size=([^\\[]*)\\]([^\\[]*)\\[/size\\]", "<font size=\"\\1px\">\\2</font>", $text );
    $text = eregi_replace( "\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]", "<a href=\"\\1\">\\2</a>", $text );
    return $text;
  }

  function gettotalthreads( $forumid ) {
    Database::query( "SELECT COUNT(*) as `total` FROM `news`", $this->con );

    return Result::GetField( "total" );
  }

  function getthreads( $forumid, $start, $end ) {
    Database::query( "SELECT `title`, `id`, `post`, `postdate`, `author_id`, `author_name` 
            FROM `news` ORDER BY `postdate` DESC LIMIT " . $start . ", " . $end, $this->con );

    if ( Result::RecordCount() <= 0 )
      return false;

    $return = array();

    do {
      $return[] = array("tid"     => Result::GetField( "id" ), "author" => Result::GetField( "author_name" ), "uid" => Result::GetField( "author_id" ), "title" => Result::GetField( "title" ), "body" => $this->parse( Result::GetField( "post" ) ),
                        "replies" => 0,                    "date"       =>@date( "d:m:Y", Result::GetField( "postdate" ) ));
    } while ( Result::MoveNext() );

    return $return;
  }

  function addnews( $title, $post, $author, $authorid ) {
    $title = trim( $title );
    $post  = trim( $post );
    return Database::query( "INSERT INTO `news`(`title`, `post`, `postdate`, `author_id`, `author_name`) VALUES (\"" . $title . "\", \"" . $post . "\", \"" . time() . "\", \"" . $authorid . "\", \"" . $author . "\")", $this->con );
  }

  function getnews( $id ) {
    Database::query( "SELECT `title`, `id`, `post`, `postdate`, `author_id`, `author_name` 
        FROM `news` WHERE id = '" . $id . "'", $this->con );

    if ( Result::RecordCount() <= 0 )
      return false;

    $return = array("tid"  => Result::GetField( "id" ), "author" => Result::GetField( "author_name" ), "uid" => Result::GetField( "author_id" ), "title" => Result::GetField( "title" ), "body" => Result::GetField( "post" ), "replies" => 0,
                    "date" =>@date( "d:m:Y", Result::GetField( "postdate" ) ));

    return $return;
  }

  function updatenews( $id, $newtitle, $newbody ) {
    $newtitle = trim( $newtitle );
    $newbody  = trim( $newbody );
    return Database::query( "UPDATE `news` SET `title` = \"" . $newtitle . "\", `post` = \"" . $newbody . "\" WHERE id = '" . $id . "'", $this->con );
  }

  function delnews( $id ) { return Database::query( "DELETE FROM `news` WHERE `id` = '" . $id . "'", $this->con ); }

  function profilelink( $uid, $name, $seo = false ) { return $name; }

  function newslink( $tid, $topicname, $name, $seo = false ) { return $name; }

  function reglink( $guest = false ) { return ROOT_URL . "index.php?ucp=myaccount"; }
  function newmessages( $guest = false ) {
    global $user;

    if ( !$user || $guest )
      return "";

    Database::query( "SELECT COUNT(*) as `count` FROM `support` WHERE `unread` = 1 AND `userid` = '" . User::getint( "userid" ) . "'", $this->con );

    return ", you have (" . Result::GetField( "count" ) . ") new message(s).";
  }
}
?>