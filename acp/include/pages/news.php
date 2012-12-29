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
  die ( "Invalid access.." );

if ( isset( $_GET['add'] ) && $_POST['submit_news'] ) {
  $newstitle = Database::sql_safe( $_POST['news_title'] );
  $newsbody  = Database::sql_safe( $_POST['news_body'] );
  $newsbody  = str_replace( '\r\n', '<br />', $newsbody );
  $bool      = CMS::$newstype->addnews( $newstitle, $newsbody, User::getstring( "username" ), User::getint( "userid" ) );
  if ( $bool )
    $msg = "Successfully added news";
  else
    $err = "Failed to add news..";
}

if ( isset( $_GET['del'] ) && ( isset( $_POST['del_id'] ) || isset( $_GET['del_id'] )) ) {
  $id   = ( isset( $_GET['del_id'] ) ? $_GET['del_id'] : $_POST['del_id'] );
  $bool = CMS::$newstype->delnews( $id );
  if ( $bool )
    $msg = "Successfully deleted news";
  else
    $err = "Failed to delete news..";
}

if ( isset( $_GET['edit'] ) && $_GET['edit_id'] ) {
  if ( isset( $_POST['submit_edit'] ) ) {
    $newstitle = Database::sql_safe( $_POST['news_title'] );
    $newsbody  = Database::sql_safe( $_POST['news_body'] );
    $newsbody  = str_replace( '\r\n', '<br />', $newsbody );
    $bool      = CMS::$newstype->updatenews( $_GET['edit_id'], $newstitle, $newsbody );
    if ( $bool )
      $msg = "Successfully editted news";
    else
      $err = "Failed to edit news";
  }

  $post = CMS::$newstype->getnews( $_GET['edit_id'] );
  if ( !$post )
    $err = "Invalid news id..";
}
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
            <h2>News</h2>

            <div id = "simpleForm">
                <?php
                if ( isset( $_GET['edit'] ) && $_GET['edit_id'] && $post ) {
                ?>

                  <form name = "news_process" method = "POST" action = "<?php echo ACP_URL; ?>index.php?m=news&edit&edit_id=<?php echo $post['tid']; ?>">
                      <fieldset><legend>Edit News</legend>

                          <label for = "news_title">Title:</label>

                          <input type = "text" id = "news_title" name = "news_title" class = "large" value = "<?php echo $post['title']; ?>"/>

                          <br/>

                          <label for = "news_body">Content:</label><textarea id = "news_body" name = "news_body" class = "small" rows = "5" cols = "10"><?php echo str_replace( '<br />', '
', $post['body'] ); ?></textarea>

                          <br/>

                          <div style = "text-align: center;">
                              <input type = "submit" name = "submit_edit" value = "Process" class = "button">
                          </div>
                  </form>

                <?php
                }
                else {
                ?>

                  <form name = "news_process" method = "POST" action = "<?php echo ACP_URL; ?>index.php?m=news&add">
                      <fieldset>
                          <legend>Add News</legend>

                          <label for = "news_title">Title:</label>

                          <input type = "text" id = "news_title" name = "news_title" class = "large"/>

                          <br/>

                          <label for = "news_body">Content:</label><textarea id = "news_body" name = "news_body" class = "small" rows = "5" cols = "10"></textarea>

                          <br/>

                          <label for = "news_author">Author:</label>

                          <input type = "text" id = "news_author" name = "news_author" value = "<?php echo User::getstring("username"); ?>" readonly = "readonly"/>

                          <br/>
                      </fieldset>

                      <div style = "text-align: center;">
                          <input type = "submit" name = "submit_news" value = "Process" class = "button">
                      </div>
                  </form>

                  <br/>

                  <br/>

                  <br/>

                <?php
                }

                $news = CMS::$newstype->getthreads( 0, 0, 50 );
                ?>

                <form>
                    <fieldset>
                        <legend>Previous News</legend>

                        <?php
                        if ( $news ) {
                          foreach ( $news as $post ) {
                            echo "<div align=\"center\">" . $post['title'] . " <br /><small>Posted by: " . CMS::profilelink( $post['uid'],
                                                                                                                             $post['author'] ) . "</small><br /><a href=\"" . ACP_URL . "index.php?m=news&del&del_id=" . $post['tid']
                                     . "\">remove</a> | ";
                            echo "<a href=\"" . ACP_URL . "index.php?m=news&edit&edit_id=" . $post['tid'] . "\">edit</a></div>";
                          }
                        }
                        ?>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>