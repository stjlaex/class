<?php
/**									check_yesno.php
 *
 * simple radio checkboxes which return either 'yes' or 'no'
 * name defaults to 'answer0' but can be set by $checkname
 * it does not support required but one check box is always checked anyway
 */

if(!isset($checkcaption)){$checkcaption=get_string('yes');}
if(!isset($checkcaptionno)){$checkcaptionno=get_string('no');}
if(!isset($checkchoice)){$checkchoice='no';}
if(!isset($checkname)){$checkname='answer';}
$icheckname='i'.$checkname;
if(!isset($$icheckname)){$$icheckname=0;}else{$$icheckname++;}
if(isset($checkalert)){$doublecheck='onClick="alert(\''.$checkalert.'\');"';}else{$doublecheck='';}
if($checkchoice=='yes'){$checkclass='checked';}
else{$checkclass='notchecked';}

		print '<div style="margin-left:20px;" class="'.$checkclass.'"><label>'.$checkcaption.'</label>';
		print '<input type="radio" name="'.$checkname. $$icheckname.'" '.$doublecheck.' tabindex="'.$tab++.'" value="yes" '.$checkclass.' /></div>';
?>
<?php
		if($checkchoice=='no'){$checkclass='checked';}
		else{$checkclass='notchecked';}
		print '<div style="margin-left:30px;" class="'.$checkclass.'"><label>'.$checkcaptionno.'</label>';
		print '<input type="radio" name="'.$checkname. $$icheckname.'" tabindex="'.$tab++.'" value="no" '.$checkclass.' /></div>';
?>
<?php
unset($checkcaption);
unset($checkname);
unset($checkchoice);
?>