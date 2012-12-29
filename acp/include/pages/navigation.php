<?php
/***************************************************************************
 *                                navigation.php
 *                            -------------------
 *   Project              : UnityCMS
 *   Begin                : Thursday, September 16, 2010
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
  
if( isset($_POST) && isset($_POST['submit_new_page']))
{
	$ptitle = Database::sql_safe($_POST['page_title']);
	$pcontent = Database::sql_safe($_POST['page_content']);
	$plevel = Database::sql_safe($_POST['page_level']);
	$purl = Database::sql_safe($_POST['page_customurl']);
	if (!$ptitle || !$pcontent)
		$err = 'Error: Page Title and Content is required!';
	else
	{
		Database::query("INSERT INTO navigation SET title = \"".$ptitle."\", post = \"".$pcontent."\", level = '".$plevel."', author_name = \"".User::getstring('username')."\", postdate = \"" . time() ."\"", Config::$site->con());
		if (Database::affected_rows(Config::$site->con()) == 1)
			$msg = 'Success: Page has been created and is accessible here: <a href="' . ROOT_URL . '?page='.$ptitle.'">' . $ptitle . '</a>';
	}
}

Database::query("SELECT * FROM navigation", Config::$site->con());
$data = Result::GetRows();
if ($data)
{
foreach ( $data as $i => $d )
{
	// Guests Pages
	if ($d['level'] == 0)
	{
		$guests .= '<tr><td>' . $d['title'] . '</td><td>' . $d['post'] . '</td><td>' . $d['author_name'] . '</td><td>' . $d['custom_url'] . '</td></tr>';
	}
	
	// Members Pages
	if ($d['level'] == 1)
	{
		$members .= '<tr><td>' . $d['title'] . '</td><td>' . $d['post'] . '</td><td>' . $d['author_name'] . '</td><td>' . $d['custom_url'] . '</td></tr>';
	}
}
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
            <h2>Navigation</h2>

            <div id = "simpleForm">
                <form name = "navigation" method = "get" action = "<?php echo ACP_URL; ?>index.php?m=navigation">
                    <fieldset><legend>Guest Pages</legend>
                    <table cellspacing="5" cellpadding="5"><tr><th>Page Name</th><th>Content</th><th>Created By</th></tr>
                    <?php echo $guests; ?>
                    </table>
                    </fieldset>
                    <fieldset><legend>Member Pages</legend>
                    <table cellspacing="5" cellpadding="5"><tr><th>Page Name</th><th>Content</th><th>Created By</th></tr>
                    <?php echo $members; ?>
                    </table>
                    </fieldset>
                </form>
                
                <form name= "navigation1" method = "post" action = "<?php echo ACP_URL; ?>index.php?m=navigation">
                	<fieldset><legend>Create a page</legend>
                	<label for="page_title">Page Title</label><input type="text" id="page_title" name="page_title" class="large" 
                	onkeyup="system.newvalue('page_customurl', '?page=' + this.value);" 
                	onkeypress="system.newvalue('page_customurl', '?page=' + this.value);" 
                	onchange="system.newvalue('page_customurl', '?page=' + this.value);" /><br />
                	<label for="page_content">Page Content</label><textarea id="page_content" name="page_content" class="medium"></textarea><br />
                	<label for="page_level">Page Level</label><select id="page_level" name="page_level"><option value="0">Guests</option><option value="1">Members</option></select><br />
                	<label for="page_customurl">Page URL</label><input type="text" id="page_customurl" readonly="readonly" name="page_customurl" class="large" /><br />
                	<div style = "text-align: center;">
                		<input type = "submit" name = "submit_new_page" value = "Create Page" class = "button">
                	</div>
                	</fieldset>
                </form>
            </div>
        </div>
    </div>
</div>