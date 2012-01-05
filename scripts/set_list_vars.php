<?php
/**					scripts/set_list_vars.php
 *
 * Must have the select's name already defined in $listname
 * any option can be over-ridden by setting the value explicity first:
 * multi>1 to post back an array of values (the name is returned a plural)
 * required=yes or =eitheror to add the orange star
 * onsidechange=yes only if this is in the bookoptions on the leftside
 * onchange=yes if the form is to be submitted on value change
 * liststyle!='' if you need this element to have some inline css
 * listlabel!='' if you want to display a label different to the name
 * listname must be set and is the name of the returned post variable
 *				an 's' for an array is automatically added so beware 'ss'!
 * listid!='' if you want the id to be something special ie. not
 *				related to the listname, automatic iterations of the same id are
 *				done automatically, so this is only needed if your id must match an
 *				xml tagname for autofill or something (gets uppercase first letter)
 * selectedvalue!='' if a value is preselected
 */
$listoptions=array();
if(isset($multi) and $multi>1){$listoptions['multi']=$multi;$listname.='s';}else{$listoptions['multi']=1;}
$listoptions['name']=$listname;
if(isset($required)){$listoptions['required']=$required;unset($required);}else{$listoptions['required']='no';}
if(isset($onsidechange)){$listoptions['onsidechange']=$onsidechange;unset($onsidechange);}else{$listoptions['onsidechange']='no';}
if(isset($onchange)){$listoptions['onchange']=$onchange;unset($onchange);}else{$listoptions['onchange']='no';}
if(isset($liststyle)){$listoptions['style']='style="'.$liststyle.'"';unset($liststyle);}else{$listoptions['style']='';}
if(isset($listlabel)){$listoptions['label']=$listlabel;unset($listlabel);}else{$listoptions['label']=$listname;}
if(isset($listvaluefield)){$listoptions['valuefield']=$listvaluefield;}else{$listoptions['valuefield']='id';}
if(isset($listdescriptionfield)){$listoptions['descriptionfield']=$listdescriptionfield;}else{$listoptions['descriptionfield']='name';}
if(isset($listfilter)){$listoptions['filter']=$listfilter;unset($listfilter);}else{$listoptions['filter']='';}
if(isset($listswitch)){$listoptions['switch']=$listswitch;unset($listswitch);}else{$listoptions['switch']='';}
/*these can all still exist outside this list scripts so don't unset 'em*/
if(!isset($tab)){$tab=1;}
$listoptions['tab']=$tab++;
if(isset($key)){
	$listoptions['i']=$key;
	}
else{
	if(!isset(${'i'.$listname})){${'i'.$listname}='';}
	$listoptions['i']=${'i'.$listname}++;
	}
if($listoptions['required']=='eitheror'){$listoptions['eitheror']=$listeitheror;unset($listeitheror);}
if(isset($listid)){$listoptions['id']=ucfirst($listid);unset($listid);}else{$listoptions['id']=ucfirst($listname).$listoptions['i'];}
if(isset($multi) and $multi>1){$listoptions['selectedvalue']=array();unset($multi);}else{$listoptions['selectedvalue']='';}
if(isset(${'sel'.$listname})){$listoptions['selectedvalue']=${'sel'.$listname};}
if(isset(${$listname})){$listoptions['selectedvalue']=${$listname};}
if(isset(${'new'.$listname})){$listoptions['selectedvalue']=${'new'.$listname};}
//trigger_error('listselected '.$listname.'='.$listoptions['selectedvalue'],E_USER_WARNING);
unset($listname);
?>
