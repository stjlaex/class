<?php
/**									check_yesno.php
 *
 * simple radio checkboxes which return either 'yes' or 'no'
 * name defaults to 'answer0' but can be set by $checkname
 * it does not support required but one check box is always checked anyway
 */

if(!isset($checkcaption)){$checkcaption=get_string('readytocontinue');}
if(!isset($checkchoice)){$checkchoice='no';}
if(!isset($checkname)){$checkname='answer';}
$icheckname='i'.$checkname;
if(!isset($$icheckname)){$$icheckname=0;}else{$$icheckname++;}
?>
  <table class="listmenu">
	<tr>
	  <th><label><?php print $checkcaption; ?></label></th>
	  <td>
	<label for="yes<?php print $iyesno; ?>"><?php print_string('yes');?></label>
	<input type="radio" name="<?php print $checkname;?><?php print $$icheckname; ?>" 
		  title="yes" id="yes<?php print $$icheckname; ?>" 
		  value="yes" <?php if($checkchoice=='yes'){print 'checked';}?> />
	  </td>
	  <td>
	<label for="no<?php print $iyesno; ?>"><?php print_string('no');?></label>
	<input type="radio" name="<?php print $checkname;?><?php print $$icheckname; ?>" 
		  title="no" id="no<?php print $$icheckname; ?>"
		  value="no" <?php if($checkchoice=='no'){print 'checked';}?> />
	  </td>
	</tr>
 </table>
<?php
unset($checkcaption);
unset($checkname);
unset($checkchoice);
?>