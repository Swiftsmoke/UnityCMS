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
if (!defined("IN_NESCRIPT"))
  die ("Invalid access..");

if (is_array(Page::$news))
{
	foreach (Page::$news as $post)
	{
		echo '<div id=\'news-top\'>' . $post['title'] . '</div>';
		echo '<div id=\'news-bg\'>' . $post['body'] . '</div>';
		echo '<div id=\'news-bot\'>';
        echo 'Posted by ' . CMS::profilelink($post['uid'], $post['author']) . ' | ';
		$post['date'] = explode(":", $post['date']);

		for ($x = 0; $x < 3; $x++)
		{
			echo sprintf("<span style='color:#f7b536;'>%u</span>%s", $post['date'][$x], ($x < 2 ? "/" : ""));
		}

      echo ' | (' . CMS::$newstype->newslink($post['tid'], $post['title'], (int)$post['replies']) . ') Replies';
      if (User::getbool("admin") && !CMS::$newstype->isforum)
            echo ' <a href="#" onclick="system.load(\'' . ACP_URL . 'index.php?m=news&del&del_id=' . $post['tid'] . '\', \'\', {onComplete:location=\'' . ROOT_URL . '\'});">Delete</a>';
      echo '</div>';
    }
    echo CMS::pagination(ROOT_URL . 'index.php?news', Page::$page+1, ceil(Page::$total / CMS::$newslimit));
}
?>