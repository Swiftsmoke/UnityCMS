<?php
/***************************************************************************
 *                                index.php
 *                            -------------------
 *   Project              : NEScriptCMS
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
* Protect our files by setting a define to IN_NESCRIPT
*   This will prevent people from accessing files that we dont want them to access
*
* Usage: if ( ! defined( "IN_NESCRIPT" ) ) die( "Invalid access.." );
*/
define( "IN_NESCRIPT", true, true );

/**
* Load common file
*/
include 'include/common.php';

/**
* Generate $page
*/
$array = CMS::getPage();

/**
* Load the page
*/
include ROOT_DIR . 'include/header.php';

if ( file_exists( ROOT_DIR . 'include/pages/' . CMS::$page . '.php' ) )
  include ROOT_DIR . 'include/pages/' . CMS::$page . '.php';

include ROOT_DIR . 'include/footer.php';

/**
* Build the Page
*/
Header::Build();

if ( class_exists( "Page" ) )
  Page::Build();

Footer::Build();
?>