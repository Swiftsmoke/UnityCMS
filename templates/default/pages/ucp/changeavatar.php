<?php
/***************************************************************************
 *                                changeavatar.php
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
?>
<h2>Current Avatar</h2><br />
<img src="<?php echo ROOT_URL; ?>uploads/avatars/<?php echo Subpage::$myavatar; ?>" /><br /><br />
<form id="changeform" name="changeform" action="<?php echo ROOT_URL; ?>?ucp=myaccount&avatar" method="post">
    <h2>Choose Avatar: </h2>
    <br />
    <input type="hidden" id="selectedImg" name="selectedImg">
    <div id="myImgs" style="height: 200px;overflow-y:scroll;overflow-x: hidden;">
        <?php if ( Subpage::$avatars )
        {
            foreach ( Subpage::$avatars as $i => $a ) { 
                if (!is_float($i / 5))
                {
                    echo "<div style=\"padding-bottom:5px\"></div>";
                }
                echo "<span id=\"span" . $i . "\" onclick=\"setSelected(this)\"><img src=\"" . ROOT_URL . "uploads/avatars/" . $a . "\" id=\"" . $a . "\"></span>";
            }
        }
        ?>
    </div>
    <div align='center'><br /><input type='submit' value='Change' name='changeavatar' /></div>

<script type="text/javascript">
   function setSelectedImg(imgName){
         var imgObj = document.getElementById(imgName);

         if(imgObj != undefined){
               var containerObj = imgObj.parentNode;
               setSelected(containerObj);
         }
   }

 function setSelected(obj) {
      var mySpans = document.getElementById("myImgs").getElementsByTagName("span");
      for (var t=0,spn; spn=mySpans[t]; t++) {
           spn.style.border = "0";
      }
      obj.style.border = "3px solid #40DBF4";
      document.getElementById("selectedImg").value = obj.childNodes[0].id;
 }
</script>