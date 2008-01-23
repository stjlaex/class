<?php
/**									portfolio_accounts_action.php
 *
 *
 */

$action='portfolio_accounts.php';
require_once('lib/eportfolio_functions.php');

include('scripts/sub_action.php');

	/*get all ClaSS data first*/
	$yearcoms=(array)list_communities('year');
	$formcoms=(array)list_communities('form');
	$classes=(array)list_course_classes();
	$allteachers=(array)list_teacher_users();
	$Students=array();
	$yearusers=array();
	$epf_contacts=array();
	while(list($yearindex,$com)=each($yearcoms)){
		$yid=$com['name'];
		$yearusers[$yid]=array();
		$yearperms=array('r'=>1,'w'=>1,'x'=>1);/*heads of year only*/
		$owners=(array)list_pastoral_users($yid,$yearperms);
		while(list($uid,$user)=each($owners)){
			if($user['role']!='office' and $user['role']!='admin'){
				$yearusers[$yid][]=$user['username'];
				}
			}
		$students=listin_community($com);
		while(list($studentindex,$student)=each($students)){
			$sid=$student['id'];
			$Students[$sid]=fetchStudent_short($sid);
			$Email=fetchStudent_singlefield($sid,'EmailAddress');
			$Students[$sid]['EmailAddress']['value']=$Email['EmailAddress']['value'];
			$Contacts=fetchContacts($sid);
			while(list($contactindex,$Contact)=each($Contacts)){
				$mailing=$Contact['ReceivesMailing']['value'];
				if($mailing=='0' or $mailing=='1' or $mailing=='2'){
					if(!array_key_exists($Contact['id_db'],$epf_contacts)){
						$epf_contacts[$Contact['id_db']]['sids']=array();
						$epf_contacts[$Contact['id_db']]['Contact']=$Contact;
						}
					$epf_contacts[$Contact['id_db']]['sids'][]=$sid;
					}
				}
			}
		}

	$formusers=array();
	while(list($index,$com)=each($formcoms)){
		$fid=$com['name'];
		$d_form=mysql_query("SELECT teacher_id FROM form WHERE id='$fid'");
		$formusers[]=mysql_result($d_form,0);
		}
	reset($formcoms);



	/* Now insert into elgg*/
	elgg_refresh();
	$staff=array();

	while(list($index,$user)=each($allteachers)){
		$Newuser['id_db']=$user['uid'];
		$Newuser['Surname']['value']=$user['surname'];
		if($user['title']!=''){
			$Newuser['Forename']['value']=get_string(displayEnum($user['title'],'title'),'infobook');
			}
		else{
			$Newuser['Forename']['value']=$user['forename'];
			}
		$Newuser['EmailAddress']['value']=$user['email'];
		$Newuser['Username']['value']=$user['username'];
		$Newuser['Password']['value']=$user['passwd'];
		$epfuid=elgg_newUser($Newuser,'staff');
		$staff[$user['username']]=$epfuid;
		}

	reset($yearcoms);
	while(list($yearindex,$com)=each($yearcoms)){
		$yid=$com['name'];
		$epfcomid=elgg_update_community($com);
		$com['epfcomid']=$epfcomid;
		$yearepfcomids[$yid]=$epfcomid;
		$comowners=$yearusers[$yid];
		while(list($index,$tid)=each($comowners)){
			$epfuid=$staff[$tid];
			elgg_join_community($epfuid,$com);
			}
		/*Only one can be the owner and this makes it the last in the list.*/
		elgg_update_community($com,$com,$epfuid);
		}

	reset($formcoms);
	while(list($formindex,$com)=each($formcoms)){
		$fid=$com['name'];
		$tid=$formusers[$formindex];
		$epfuid=$staff[$tid];
		$epfcomid=elgg_update_community($com,$com,$epfuid);
		$com['epfcomid']=$epfcomid;
		$formepfcomids[$fid]=$epfcomid;
		elgg_join_community($epfuid,$com);
		}

	reset($Students);
	while(list($sid,$Student)=each($Students)){
		$epfuid=elgg_newUser($Student,'student');
		$Students[$sid]['epfuid']=$epfuid;
		$fid=$Student['RegistrationGroup']['value'];
		$com=array('epfcomid'=>$formepfcomids[$fid],'type'=>'form','name'=>'');
		elgg_join_community($epfuid,$com);
		$yid=$Student['YearGroup']['value'];
		$com=array('epfcomid'=>$yearepfcomids[$yid],'type'=>'year','name'=>'');
		elgg_join_community($epfuid,$com);
		$group=array('epfgroupid'=>'','owner'=>$epfuid,'name'=>'family','access'=>'');
		$epfgroupid=elgg_update_group($group);
		elgg_new_folder($epfuid,$name='Reports',$access='group'.$epfgroupid);
		elgg_new_folder($epfuid,$name='Portfolio Work',$access='group'.$epfgroupid);
		$Students[$sid]['epfgroupid']=$epfgroupid;
		}

	while(list($gid,$epf_contact)=each($epf_contacts)){
		$Contact=$epf_contact['Contact'];
		$epfuid=elgg_newUser($Contact,'guardian');
		$sids=$epf_contact['sids'];
		while(list($index,$sid)=each($sids)){
			/*joining a family community involves simply an entry in
				friends and an access group, a family does not have a community of
				its own*/
			elgg_join_community($epfuid,array('epfcomid'=>$Students[$sid]['epfuid']));
			elgg_join_group($epfuid,array('epfgroupid'=>$Students[$sid]['epfgroupid'],'name'=>'family','owner'=>$Students[$sid]['epfuid'],'access'=>''));
			}
		}

	/* Now do teaching groups */
	while(list($index,$class)=each($classes)){
		$cid=$class['id'];
		$epfcid=str_replace('/','-',$cid);
		$com=array('epfcomid'=>'','type'=>'class','name'=>$epfcid);
		$epfcomid=elgg_update_community($com);
		$com['epfcomid']=$epfcomid;
		$d_t=mysql_query("SELECT teacher_id FROM tidcid WHERE class_id='$cid';");
		while($t=mysql_fetch_array($d_t, MYSQL_ASSOC)){
			elgg_join_community($staff[$t['teacher_id']],$com);
			}
		$d_student=mysql_query("SELECT b.id FROM cidsid a, student b 
				WHERE a.class_id='$cid' AND b.id=a.student_id ORDER BY b.surname");
		while($student=mysql_fetch_array($d_student, MYSQL_ASSOC)){
			$sid=$student['id'];
			elgg_join_community($Students[$sid]['epfuid'],$com);
			}
		}


include('scripts/results.php');
include('scripts/redirect.php');
exit;

?>
