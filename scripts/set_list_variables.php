<?php
/**					set_list_variables.php
 *
 * must have the select's name already defined in $listname
 * any option can be overridden by setting the value explicity first:
 * multi>1 to post back an array of values
 * required=yes to add the orange star
 * onsidechange=yes only if this is in the bookoptions on the leftside
 * onchange=yes if the form is to be submitted on value change
 * liststyle!='' if you need this element to have a css class
 * listlabel!='' if you want to display a label different to the name
 * listname must be set and is the name of the returned post variable
 * listid!='' if you want the id to be something special ie. not
 *				related to the listname, automatic iterations of the same id are
 *				done automatically, so this is only needed if your id must match an
 *				xml tagname for autofill or something (gets uppercase first letter)
 * selectedvalue!='' if a value is preselected
 */
$listoptions=array();
$listoptions['name']=$listname;
if(isset($multi)){$listoptions['multi']=$multi;unset($multi);}else{$listoptions['multi']=1;}
if(isset($required)){$listoptions['required']=$required;unset($required);}else{$listoptions['required']='no';}
if(isset($onsidechange)){$listoptions['onsidechange']=$onsidechange;unset($onsidechange);}else{$listoptions['onsidechange']='no';}
if(isset($onchange)){$listoptions['onchange']=$onchange;unset($onchange);}else{$listoptions['onchange']='no';}
if(isset($listsyle)){$listoptions['style']='style="'.$liststyle.'"';unset($liststyle);}else{$listoptions['style']='';}
if(isset($listlabel)){$listoptions['label']=$listlabel;unset($listlabel);}else{$listoptions['label']=$listname;}
if(isset($listvaluefield)){$listoptions['valuefield']=$listvaluefield;}else{$listoptions['valuefield']='id';}
if(isset($listdescriptionfield)){$listoptions['descriptionfield']=$listdescriptionfield;}else{$listoptions['descriptionfield']='name';}
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
