<?php
/**			       		list_paymenttypes.php
 *
 */

$listoptions=array();
if(!isset($listname)){$listname='paytype';$listoptions['name']='paytype';}
else{$listoptions['name']=$listname;}
if(isset(${'sel'.$listname})){$listoptions['selectedvalue']=${'sel'.$listname};}
if(isset(${$listname})){$listoptions['selectedvalue']=${$listname};}
if(isset(${'new'.$listname})){$listoptions['selectedvalue']=${'new'.$listname};}

print '<table class="listmenu"><tr><td class="row">';
if(!isset($paymenttypes)){$paymenttypes=getEnumArray('paymenttype');}
foreach($paymenttypes as $value => $name){
	if(isset($listoptions['selectedvalue']) and $listoptions['selectedvalue']==$value){$checked='checked="yes"';$checkclass='checked';}else{$checked='';$checkclass='';}
	if($value==''){
		print '<div class="'.$checkclass.'"><label>'.get_string('uncheck','admin').'</label><input type="radio" name="'.$listoptions['name'].'" value="uncheck" ' .$checked.'/></div>';
		}
	else{
		print '<div class="'.$checkclass.'"><label>'.get_string($name,'admin').'</label><input type="radio" name="'.$listoptions['name'].'" value="'.$value.'" '.$checked.' /></div>';
		}
	}
print '</td></tr></table>';
unset($listoptions);
?>
