<?php
/**									check_yesno.php
 *
 */

if(!isset($checkcaption)){$checkcaption=get_string('readytocontinue');}
if(!isset($checkchoice)){$checkchoice='no';}
if(!isset($checkname)){$checkname='answer';}
if(!isset($required)){$required='no';}
if(!isset($iyesno)){$iyesno=0;}else{$iyesno++;}

?>
  <table class="listmenu">
	<tr>
	  <th><label><?php print $checkcaption; ?></label></th>
	  <td>
	<label for="yes<?php print $iyesno; ?>"><?php print_string('yes');?></label>
	<input type="radio" name="<?php print $checkname;?>" 
		  title="yes" id="yes<?php print $iyesno; ?>" 
		  value="yes" <?php if($checkchoice=='yes'){print 'checked';}?> />
	  </td>
	  <td>
	<label for="no<?php print $iyesno; ?>"><?php print_string('no');?></label>
	<input type="radio" name="<?php print $checkname;?>" 
		  title="no" id="no<?php print $iyesno; ?>"
		  value="no" <?php if($checkchoice=='no'){print 'checked';}?> />
	  </td>
	</tr>
 </table>
<?php
unset($checkcaption);
unset($checkname);
unset($checkchoice);
unset($required);
?>