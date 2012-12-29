<?php
// Invision Power Board
class ipb {
  var $prefix = "ipb_";
  var $con;
  var $isforum = true;

  function code_box($text) { return "<div class=\"code\"><h6>Code</h6>\\1</div>"; }

  function quote($text) { return "<blockquote><h6>Quote:</h6>\\1</blockquote>"; }

  function parse($text) {
    $text = str_replace("<#EMO_DIR#>", "default", $text);
    $text = " " . $text;

    // First: If there isn't a "[" and a "]" in the message, don't bother.
    if (!(strpos($text, "[") && strpos($text, "]")))
      return $text;

    $text = stripslashes($text);
    $text = preg_replace("/\\[b\\](.+?)\[\/b\]/is", '<b>\1</b>', $text);
    $text = preg_replace("/\\[center\\](.+?)\[\/center\]/is", '<span align="center">\1</span>', $text);
    $text = preg_replace("/\\[i\\](.+?)\[\/i\]/is", '<i>\1</i>', $text);
    $text = preg_replace("/\\[u\\](.+?)\[\/u\]/is", '<u>\1</u>', $text);
    $text = preg_replace("/\[s\](.+?)\[\/s\]/is", '<s>\1</s>', $text);
    $text = preg_replace("/\[list\](.+?)\[\/list\]/is", '<ul>\1</ul>', $text);
    $text = preg_replace("/\[list=(.+?)\](.+?)\[\/list\]/is", '<ol type="\1" style="padding-left: 25px">\2</ol>', $text);
    $text = preg_replace("/\[\*\](.*)/", '<li>\1</li>', $text);
    $text = preg_replace("/\[code\](.+?)\[\/code\]/is", "" . $this->code_box('\\1') . "", $text);
    $text = preg_replace("/\[quote\](.+?)\[\/quote\]/is", "" . $this->quote('\\1') . "", $text);
    $text = eregi_replace("\\[img]([^\\[]*)\\[/img\\]", "<img src=\"\\1\">", $text);
    $text = eregi_replace("\\[font=([^\\[]*)\\]([^\\[]*)\\[/font\\]", "<font style=\"font-family:\\1\">\\2</font>", $text);
    $text = eregi_replace("\\[color=([^\\[]*)\\]([^\\[]*)\\[/color\\]", "<font color=\"\\1\">\\2</font>", $text);
    $text = eregi_replace("\\[size=([^\\[]*)\\]([^\\[]*)\\[/size\\]", "<font size=\"\\1px\">\\2</font>", $text);
    $text = eregi_replace("\\[url=([^\\[]*)\\]([^\\[]*)\\[/url\\]", "<a href=\"\\1\">\\2</a>", $text);
    return $text;
  }

  function getlatestthreads() {
    Database::query("SELECT a.`title`, b.`author_id`, b.`author_name` 
            FROM `" . $this->prefix . "topics` a, `" . $this->prefix . "posts` b 
            WHERE a.`topic_firstpost` = b.`pid` 
            ORDER BY a.`start_date` DESC LIMIT 5", $this->con);

    if (Result::RecordCount() <= 0)
      return false;

    return true;
  }

  function getlatestposts() {
    Database::query("SELECT a.`title`, b.`author_id`, b.`author_name` 
            FROM `" . $this->prefix . "topics` a, `" . $this->prefix . "posts` b 
            WHERE  a.`tid` = b.`topic_id` 
            GROUP BY b.`topic_id`  
            ORDER BY b.`post_date` DESC LIMIT 5", $this->con);

    if (Result::RecordCount() <= 0)
      return false;

    return true;
  }

  function getlatest($type, $format = "%s<br>by %s<img src='images/separator.png' width='163' height='9' alt='separator' /><br>") {
    global $cms;
    if (!$type)
        return "";
    $bool = call_user_func(array($this, "getlatest" . strtolower($type)));

    if ($bool == false)
      return "";

    $return = "";

    do { $return .= sprintf($format, Result::GetField("title"), $this->profilelink(Result::GetField("author_id"), Result::GetField("author_name")));
    } while (Result::MoveNext());

    return $return;
  }

  function gettotalthreads($forumid) {
    Database::query("SELECT COUNT(*) as `total` 
            FROM `" . $this->prefix . "topics` 
            WHERE `forum_id` = '" . $forumid . "'", $this->con);

    return Result::GetField("total");
  }
  function getthreads($forumid, $start, $end) {
    Database::query("SELECT a.`title`, a.`topic_firstpost`, a.`tid`, a.`posts`, a.`start_date`, b.`post`, b.`author_id`, b.`author_name` 
            FROM `" . $this->prefix . "topics` a, `" . $this->prefix . "posts` b 
            WHERE a.`forum_id` = '" . $forumid . "' AND a.`topic_firstpost` = b.`pid` 
            ORDER BY a.`start_date` DESC 
            LIMIT " . $start . ", " . $end, $this->con);

    if (Result::RecordCount() <= 0)
      return false;

    $return = array();

    do {
      $return[] = array("tid"     => Result::GetField("tid"),       "author" => Result::GetField("author_name"), "uid" => Result::GetField("author_id"), "title" => Result::GetField("title"), "body" => $this->parse(Result::GetField("post")),
                        "replies" => Result::GetField("posts"),     "date"   => date("d:m:Y", Result::GetField("start_date")));
    } while (Result::MoveNext());

    return $return;
  }
  
  function profilelink($uid, $name, $seo = false)
  {
      if ($seo)
        return "<a href='" . FORUM_ADDR . "/index.php?/user/" . $uid . "-" . $name . "/' title='View Profile'>" . $name . "</a>";

    return "<a href='" . FORUM_ADDR . "/index.php?showuser=" . $uid . "' title='View Profile'>" . $name . "</a>";
  }
  
  function newslink($tid, $topicname, $name, $seo = false)
  {
      if ($seo)
        return "<a href='" . FORUM_ADDR . "/index.php?/topic/" . $tid . "-" . $topicname . "/'>" . $name . "</a>";

      return "<a href='" . FORUM_ADDR . "/index.php?showtopic=" . $tid . "'>" . $name . "</a>";
  }
  
  function reglink($guest = false)
  {
      if ($guest)
        return FORUM_ADDR . "/index.php?app=core&module=global&section=register";
        
      return CMS::getBaseURL() . "index.php?ucp=myaccount";
  }
  
  function login($username, $password)
  {
      global $user;
      if (!$user)
        return false;
      $password = md5($password);
      Database::query("
        SELECT a.`member_id`, a.`name`, a.`member_group_id`, b.`g_is_supmod`, b.`g_access_cp` 
        FROM `" . $this->prefix . "members` a, `" . $this->prefix . "groups` b 
        WHERE a.`name` = \"" . $username . "\" AND MD5( CONCAT(MD5(a.`members_pass_salt`), \"" . $password . "\") ) = a.`members_pass_hash` AND a.`member_group_id` = b.`g_id` LIMIT 1", $this->con);
      if (Result::RecordCount() == 1) {
        User::setkey("guest", false);
        User::setkey("username", Result::GetField("name"));
        User::setkey("forumid", Result::GetField("member_id"));
        User::setkey("admin", (Result::GetField("g_access_cp") ? true : false));
        User::setkey("ucp", true);
        User::setkey("moderator", (Result::GetField("g_is_supmod") ? true : false));
        User::update();
        return true;
      }
      return false;
  }
  
  function newmessages($guest = false)
  {
      global $user;
      if (!$user || $guest)
        return "";
        
      Database::query("SELECT COUNT(*) as `count` FROM `" . $this->prefix . "message_topic_user_map` WHERE `map_has_unread` = '" . $read . "' AND `map_user_id` = '" . User::getint("forumid") . "'", $this->con);
        
      return ", you have (<a href='" . FORUM_ADDR . "/index.php?app=members&module=messaging'>" . Result::GetField("count") . "</a>) new message(s).";
  }
  
  function totalmembers()
  {
      Database::query("SELECT count(*) AS `totalmembers` FROM `" . $this->prefix . "members`", $this->con);
      return Result::GetField("totalmembers");
  }
  
  function sessionlist()
  {
      Database::query("SELECT a.`member_id` as `id`, a.`member_name` as `name`, a.`ip_address` as `ip`, a.`location_1_type` as `location`, a.`location_1_id` as `locationid`, a.`running_time` as `start` FROM `" . $this->prefix . "sessions` a ORDER BY a.`running_time` DESC", $this->con);
      $return = Result::GetRows();
      foreach ($return as $id => &$info)
      {
          if ($info["location"] == "topic")
          {
              Database::query("SELECT `title` FROM `" . $this->prefix . "topics` WHERE `tid` = '" . $info["locationid"] . "'", $this->con);
              $info["location"] = "Viewing Topic: " . $this->newslink($info["locationid"], Result::GetField("title"));
          }
          else if ($info["location"] == "forum")
          {                                     
              Database::query("SELECT `name` FROM `" . $this->prefix . "forums` WHERE `id` = '" . $info["locationid"] . "'", $this->con);
              $info["location"] = "<a href='" . FORUM_ADDR . "/index.php?showforum=" . $info["locationid"] . "'>" . Result::GetField("name") . "</a>";
          }
          else
          {
              $info["location"] = "Viewing Board Index";
          }
      }
      return $return;
  }
}
/*
if (is_integer($fuser)) { Database::query("SELECT a.`member_id`, a.`name`, a.`member_group_id`, b.`g_is_supmod`, b.`g_access_cp` 
        FROM `" . TABLE_PREFIX . "members` a, `" . TABLE_PREFIX . "groups` b 
        WHERE a.`member_id` = '" . $fuser . "' LIMIT 1"); }
    else if (is_string($fuser)) { Database::query("SELECT a.`member_id`, a.`name`, a.`member_group_id`, b.`g_is_supmod`, b.`g_access_cp` 
        FROM `" . TABLE_PREFIX . "members` a, `" . TABLE_PREFIX . "groups` b 
        WHERE a.`name` = \"" . Database::sql_safe($fuser) . "\" LIMIT 1"); }
*/
?>