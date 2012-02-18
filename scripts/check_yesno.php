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
if(isset($checkalert)){$doublecheck='onClick="alert(\''.$checkalert.'\');"';}else{$doublecheck='';}
?>
  <table class="listmenu">
	<tr>
	  <th><label><?php print $checkcaption; ?></label></th>
	  <td>
<?php
		if($checkchoice=='yes'){$checkclass='checked';}
		else{$checkclass='notchecked';}
		print '<div class="'.$checkclass.'"><label>'.get_string('yes').'</label>';
		print '<input type="radio" name="'.$checkname. $$icheckname.'" '.$doublecheck.'
						tabindex="'.$tab++.'" value="yes" '.$checkclass.' /></div>';
?>
		</td>
		<td>
<?php
		if($checkchoice=='no'){$checkclass='checked';}
		else{$checkclass='notchecked';}
		print '<div class="'.$checkclass.'"><label>'.get_string('no').'</label>';
		print '<input type="radio" name="'.$checkname. $$icheckname.'" 
						tabindex="'.$tab++.'" value="no" '.$checkclass.' /></div>';
?>
		</td>
	  </tr>
	</table>
<?php
unset($checkcaption);
unset($checkname);
unset($checkchoice);
?>