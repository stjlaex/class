<?php
/**					set_list_variables.php
 *
 * must have the select's name already defined in $listname
 * any option can be overridden by setting the value explicity first
 * 
 */
$listoptions=array();
$listoptions['name']=$listname;
if(isset($multi)){$listoptions['multi']=$multi;unset($multi);}else{$listoptions['multi']=1;}
if(isset($required)){$listoptions['required']=$required;unset($required);}else{$listoptions['required']='no';}
if(isset($onsidechange)){$listoptions['onsidechange']=$onsidechange;unset($onsidechange);}else{$listoptions['onsidechange']='no';}
if(isset($onchange)){$listoptions['onchange']=$onchange;unset($onchange);}else{$listoptions['onchange']='no';}
if(isset($listsyle)){$listoptions['style']='style="'.$liststyle.'"';unset($liststyle);}else{$listoptions['style']='';}
if(isset($listlabel)){$listoptions['label']=$listlabel;unset($listlabel);}else{$listoptions['label']=$listname;}
/*these can all still exist outside this list scripts so don't unset 'em*/
if(isset($tab)){$listoptions['tab']=$tab++;}else{$listoptions['tab']='';}
if(isset($key)){
	$listoptions['i']=$key;
	}
else{
	if(!isset(${'i'.$listname})){${'i'.$listname}='';}
	$listoptions['i']=${'i'.$listname}++;
	}
if(isset($listid)){$listoptions['id']=ucfirst($listid);unset($listid);}else{$listoptions['id']=ucfirst($listname).$listoptions['i'];}
$listoptions['selectedvalue']='';
if(isset(${'sel'.$listname})){$listoptions['selectedvalue']=${'sel'.$listname};}
if(isset(${$listname})){$listoptions['selectedvalue']=${$listname};}
if(isset(${'new'.$listname})){$listoptions['selectedvalue']=${'new'.$listname};}
unset($listname);
//trigger_error('listselected '.$listoptions['name'].'='.$listoptions['selectedvalue'],E_USER_WARNING);
?>
