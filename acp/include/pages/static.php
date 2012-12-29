<?php
/***************************************************************************
 *                                static.php
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

$page = ( isset( $_GET['page'] )) ? $_GET['page'] : 'connecting.php';

$edit = NULL;

if ( isset( $_GET['a'] ) && $_GET['a'] == 'edit' && $page ) { $edit = true; }

if ( ( isset( $_POST['submit_static'] ) || isset( $_POST['static_content'] )) && $page ) {
  $file_path = TMP_DIR . "pages/" . $page;
  if ( file_exists( $file_path ) && is_file( $file_path ) && is_writable( $file_path ) ) {
    $handle = fopen( $file_path, 'w' );
    fwrite( $handle, $_POST['static_content'] );
    fclose ($handle);
    $msg = 'Static page was successfully updated!';
  }
  else { $err = 'Static page not found or not writable (' . $file_path . ')!'; }
}
?>

<div id = "rightcontent">
    <?php
    if ( isset( $err ) )
      echo "<div id=\"errorbox\">" . $err . "</div>";

    if ( isset( $msg ) )
      echo "<div id=\"messagebox\">" . $msg . "</div>";
    ?>

    <script type = "text/javascript">
        function LoadPreview(page)
            {
            system.newbody('page_title', page + " <a href='<?php echo ACP_URL; ?>index.php?m=static&a=edit&page=" + page + "'><b>Edit</b></a>");
            system.load('<?php echo TMP_URL."pages/"; ?>' + page, '', 'static', '', 'GET');
            system.show('static_page');
            }
    </script>

    <div id = "right">
        <div align = "center">
            <h2>Static Pages</h2>

            <table width = "60%" cellspacing = "2" cellpadding = "2" border = "0">
                <tr class = "view">
                    <?php
                    if ( $handle = opendir( TMP_DIR . "pages" ) ) {
                      $i = 0;

                      while ( false !== ( $file = readdir( $handle )) ) {
                        if ( is_dir( TMP_DIR . "pages/" . $file ) )
                          continue;

                        $i++;

                        if ( floor( $i / 5 ) ) {
                          echo "</tr><tr>";
                          $i = 0;
                        }
                        echo "<td align='center'><a href='#' onclick = \"LoadPreview('" . $file . "');\"><b>" . str_replace( '.php', '', $file ) . "</b></td>";
                      }
                      closedir ($handle);
                    }
                    ?>
                </tr>
            </table>

            <small>

            <br>You may add php to the static pages however it may cause the preview not to load</small>
        </div>
    </div>

    <br>
    <div id = "static_page" style = "display:none;">
        <b>Preview:</b>

        <label id = "page_title">connecting.php</label>

        <br>
        <div class = "static" id = "static">
        </div>
    </div>

    <br/>

    <?php
    if ( $edit ) :
    ?>

    <div id = "right" style = "display: block;">
        <div align = "center">
            <h2>Editing: <?php echo $page; ?></h2>

            <div id = "simpleForm">
                <form name = "static_edit" method = "POST" action = "<?php echo ACP_URL; ?>index.php?m=static&page=<?php echo $page; ?>" style = "width: 96%;" enctype = "multipart/form-data">
                    <fieldset>
                        <legend>Edit Static Page</legend>

                        <textarea name = "static_content" id = "static_content" rows = "25" class = "adv"><?php echo file_get_contents( TMP_DIR . "pages/" . $page ); ?></textarea>

                        <br>
                    </fieldset>

                    <div style = "text-align: center;">
                        <input type = "submit" name = "submit_static" value = "Update Static Page" class = "button">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <br>
    <?php
    endif;
    ?>
</div>