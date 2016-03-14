<?php	
/**										lib/fetch_templates.php
 *
 *	@package		Class
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2016
 *
 **/

/**
 *
 * Returns a tag name and a value from db
 *
 * @param integer $tagid
 * @return array
 *
 **/
function getTag($tagid='-1'){
	if($tagid!='-1'){$id="AND id='".$tagid."'";}
	else{$id="";}
	$d_t=mysql_query("SELECT * FROM categorydef WHERE type='tag' AND comment!='' $id;");
	if($tagid!='-1'){
		$t=mysql_fetch_row($d_t);
		$tag_values=explode(':::',$t[6]);
		$tag['id']=$t[0];
		$tag['name']=$tag_values[1];
		$tag['value']=$tag_values[0];
		return $tag;
		}
	else{
		while($t=mysql_fetch_array($d_t,MYSQL_ASSOC)){
			$content=explode(":::",$t['comment']);
			$tags[$t['id']]['id']=$t['id'];
			$tags[$t['id']]['name']=$content[1];
			$tags[$t['id']]['value']=$content[0];
			}
		return $tags;
		}
	}

/**
 *
 * Add a new tag to db
 *
 * @param string $value
 * @param string $tagname
 * @param boolean $update
 * @param integer $tagid
 *
 **/
function addTag($value,$tagname,$update=false,$tagid='-1'){
	if($value!='' and $tagname!=''){
		if(!$update){mysql_query("INSERT INTO categorydef SET type='tag',name='tags',comment='".$value.":::".$tagname."';");}
		else{mysql_query("UPDATE categorydef SET comment='".$value.":::".$tagname."' WHERE type='tag' AND id=".$tagid.";");}
		}
	}


/**
 *
 * Returns tags and values from the database or users fields
 *
 * @param boolean $db
 * @param string $utype	default,student,contact or user
 * @param array $uid	array containing sid, gid and/or uid
 * @return array
 *
 **/
function getTags($db=true,$utype='default',$uid=array('student_id'=>'-1','guardian_id'=>'-1','user_id'=>'-1')){
	global $CFG;
	$Tags=array();$studentTags=array();$contactsTags=array();$userTags=array();
	if($utype=='default' or $utype=='student'){
		$Student=(array)fetchStudent($uid['student_id']);
		foreach($Student as $key=>$value){
			if($value['type_db']=='enum'){
				$value['value']=get_string(displayEnum($value['value'],$value['field_db']),'infobook');
				}
			elseif($value['type_db']=='date'){
				$value['value']=display_date($value['value']);
				}
			if($key=='Gender'){
				if($Student['Gender']['value']=='M'){
					$possessive='his';//~
					$pronoun='he';//^
					$objectpronoun='him';//*
					}
				else{
					$possessive='her';
					$pronoun='she';
					$objectpronoun='her';
					}
				$studentTags['{{~}}']=$possessive;
				$studentTags['{{^}}']=$pronoun;
				$studentTags['{{*}}']=$objectpronoun;
				}
			
			$studentTags['{{Student'.$key.'}}']=$value['value'];
			if($key=='id_db'){$studentTags['{{Student'.$key.'}}']=$value;}
			}
		}
	if($utype=='default' or $utype=='contact' or $utype=='student'){
		if($uid['student_id']!='-1' and $uid['guardian_id']!='-1'){
			$Contacts=(array)fetchContact(array('guardian_id'=>$uid['guardian_id'],'student_id'=>$uid['student_id']));
			$rel=mysql_result(mysql_query("SELECT relationship FROM gidsid WHERE guardian_id='".$uid['guardian_id']."' AND student_id='".$uid['student_id']."';"),0);
			$Contacts['Relationship']['value']=$rel;
			}
		elseif($uid['student_id']=='-1' and $uid['guardian_id']!='-1'){
			$Contacts=(array)fetchContact(array('guardian_id'=>$uid['guardian_id']));
			}
		elseif($uid['student_id']=='-1' and $uid['contact_id']=='-1'){
			$Contacts=(array)fetchContact();
			}
		foreach($Contacts as $key=>$value){
			if($value['type_db']=='enum'){
				$value['value']=get_string(displayEnum($value['value'],$value['field_db']),'infobook');
				}
			elseif($value['type_db']=='date'){
				$value['value']=display_date($value['value']);
				}
			$contactsTags['{{Contact'.$key.'}}']=$value['value'];
			if($key=='id_db'){$contactsTags['{{Contact'.$key.'}}']=$value;}
			}
		}
	if($utype=='default' or $utype=='staff'){
		$User=(array)fetchUser($uid['user_id']);
		foreach($User as $key=>$value){
			if($value['type_db']=='enum'){
				$value['value']=get_string(displayEnum($value['value'],$value['field_db']));
				}
			elseif($value['type_db']=='date'){
				$value['value']=display_date($value['value']);
				}
			$userTags['{{User'.$key.'}}']=$value['value'];
			if($key=='id_db'){$userTags['{{User'.$key.'}}']=$value;}
			}
		}
	if($db){
		$d_t=mysql_query("SELECT * FROM categorydef WHERE type='tag' AND comment!='';");
		while($ts=mysql_fetch_array($d_t,MYSQL_ASSOC)){
			$values=explode(":::",$ts['comment']);
			if($uid!='-1'){
				foreach($Student as $key=>$value){
					if($values[0]=='Student'.$key){$values[0]=$value['value'];}
					}
				foreach($Contacts as $key=>$value){
					if($values[0]=='Contact'.$key){$values[0]=$value['value'];}
					}
				foreach($User as $key=>$value){
					if($values[0]=='User'.$key){$values[0]=$value['value'];}
					}
				}
			$dbTags['{{'.$values[1].'}}']=$values[0];
			}
		}
	else{$dbTags=array();}

	if($uid['student_id']!='-1'){
		$busnameam='';
		$busnamepm='';
		$stopnameam='';
		$stopnamepm='';
		$date=date("Y-m-d");
		$buses=list_buses();
		$bookings=list_student_journey_bookings($uid['student_id'],$date);
		if(count($bookings)>0){
			foreach($bookings as $booking){
				$busid=$booking['bus_id'];
				$busin=(array)get_bus($busid,'','I');
				$buses[$busin['id']]['stops']=list_bus_stops($busin['id']);
				$busout=(array)get_bus($busid,'','O');
				$buses[$busout['id']]['stops']=list_bus_stops($busout['id']);
				if($booking['direction']=='I'){
					$busnameam=$buses[$booking['bus_id']]['name'];
					$stopnameam=$buses[$busin['id']]['stops'][$booking['stop_id']]['name'];
					}
				elseif($booking['direction']=='O'){
					$busnamepm=$buses[$booking['bus_id']]['name'];
					$stopnamepm=$buses[$busout['id']]['stops'][$booking['stop_id']]['name'];
					}
				}
			}
		$classisTags['{{bookings}}']=print_r($bookings,true);
		$classisTags['{{busnameam}}']=$busnameam;
		$classisTags['{{busnamepm}}']=$busnamepm;
		$classisTags['{{busamstop}}']=$stopnameam;
		$classisTags['{{buspmstop}}']=$stopnamepm;
		if($stopnameam!=''){
			$classisTags['{{journeyam}}']='take the bus <strong>'.$busnameam.'</strong> at stop <strong>'.$stopnameam.'</strong> AM';
			$classisTags['{{journeyames}}']='la <strong>'.$busnameam.'</strong> con la parada <strong>'.$stopnameam.'</strong> por la mañana';
			}
		else{
			$classisTags['{{journeyam}}']='';
			$classisTags['{{journeyames}}']='';
			}
		if($stopnamepm!=''){
			$classisTags['{{journeypm}}']='leave the bus <strong>'.$busnamepm.'</strong> at stop <strong>'.$stopnamepm.'</strong> PM';
			$classisTags['{{journeypmes}}']='la <strong>'.$busnamepm.'</strong> con la parada  <strong>'.$stopnamepm.'</strong> por la tarde';
			}
		else{
			$classisTags['{{journeypm}}']='';
			$classisTags['{{journeypmes}}']='';
			}
		}

	$classisTags['{{schoollogolink}}']='http://'.$CFG->siteaddress.$CFG->sitepath.'/images/'.$CFG->schoollogo;
	$classisTags['{{schoollogo}}']='<img id="schoollogo" src="'.$classisTags["{{schoollogolink}}"].'" style="display:block;margin:0 auto;max-width:180px;padding:2%;">';
	$classisTags['{{schoolname}}']=$CFG->schoolname;
	$classisTags['{{footer}}']=get_string('guardianemailfooterdisclaimer');

	if(count($contactsTags)>0){$Tags=array_merge($Tags,$contactsTags);}
	if(count($studentTags)>0){$Tags=array_merge($Tags,$studentTags);}
	if(count($userTags)>0){$Tags=array_merge($Tags,$userTags);}
	if(count($dbTags)>0){$Tags=array_merge($Tags,$dbTags);}
	$Tags=array_merge($Tags,$classisTags);
	return $Tags;
	}

/**
 *
 * List option values with tags
 *
 * @param array $tags
 *
 **/
function list_tags_name($tags){
	foreach($tags as $key=>$value){
		$key=str_replace('{{','',$key);
		$key=str_replace('}}','',$key);
		$tag=$key;
		$key=ltrim(preg_replace('/[A-Z]/', ' $0', $key));
		echo '<option value="'.$tag.'">'.$key.'</option>';
		}
	}

/**
 *
 * Returns a message with all containing tags replaced by given tags
 *
 * @param array $tags			array('{{content}}'=>'value')
 * @param string $template_name	main template to apply
 * @param integer $message_id		optional: if the content has different messages (e.g.:enrolment status)
 * @return string
 *
 **/
function getMessage($tags,$message='',$template_name='default',$message_id='-1'){
	if($template_name!='false'){
		$d_t=mysql_query("SELECT * FROM categorydef WHERE type='tmp' AND name='$template_name';");
		$t=mysql_fetch_row($d_t);
		$template=$t['6'];

		if($message_id!='-1'){
			$d_c=mysql_query("SELECT * FROM categorydef WHERE id='".$content_id."';");
			$m=mysql_fetch_row($d_c);
			$content['{{content}}']=$m['6'];
			$message=strtr($template,$content);
			}
		else{$message=$template;}
		}

	$f=preg_match('/\{\{footer(.*?)\}\}/',$message,$matches);
	if(!$f or $tags[$matches[0]]==''){
		$message.=$tags['{{footer}}'];
		}

	if(preg_match('/\{\{(.*?)\}\}/',$message)){
		$message=strtr($message,$tags);
		if(preg_match('/\{\{(.*?)\}\}/',$message)){
			$message=strtr($message,$tags);
			if(preg_match('/\{\{(.*?)\}\}/',$message)){$message=preg_replace('/\{\{(.*?)\}\}/','',$message);}
			}
		}

	return $message;
	}

/**
 *
 * Returns all templates or messages from db
 *
 * @param string $type	tmp or mes
 * @param string $name	the template or message name
 * @return array
 *
 **/
function getTemplates($type='tmp',$name=''){
	if($name!=''){$name=" AND name='$name' ";}
	else{$name="";}
	$d_t=mysql_query("SELECT * FROM categorydef WHERE type='$type' AND comment!='' $name;");
	while($ts=mysql_fetch_array($d_t,MYSQL_ASSOC)){
		$templates[]=$ts;
		}
	return $templates;
	}
?>
