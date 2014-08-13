<?php
/**									completion_list.php
 *
 *   	Lists all completed registers for currentevent.
 */

$choice='completion_list.php';

$registration_coms=array();
$registration_ids=array();

$today=date('Y-m-d 00:00:00');

if($secid!='' and $secid>1){
	/* Limit list to just the year groups for this section. */
	$ygs=(array)list_yeargroups($secid);
	if(!isset($CFG->regtypes[$secid])){$type=$regtype;}
	else{$type=$CFG->regtypes[$secid];}
	foreach($ygs as $yg){
		$coms=(array)list_communities($type,'',$yg['id']);
		foreach($coms as $com){
			if(!in_array($com['id'],$registration_ids) or $com['type']=='house'){
				$registration_coms[]=$com;
				$registration_ids[]=$com['id'];
				}
			}
		}
	}
else{
	/* Give the whole school when no section is selected. */
	if(sizeof($_SESSION['srespons'])>0){
		$sections=list_sections(false,$_SESSION['srespons']);
		}
	else{
		$sections=list_sections();
		}
	foreach($sections as $section){
		/* different sections may have different regtypes so can't
		 * just list all yeargroups in one go?
		 */
		$secid=$section['id'];
		$ygs=(array)list_yeargroups($secid);
		if(!isset($CFG->regtypes[$secid])){$type=$regtype;}
		else{$type=$CFG->regtypes[$secid];}
		foreach($ygs as $yg){
			$coms=(array)list_communities($type,'',$yg['id']);
			foreach($coms as $com){
				if(!in_array($com['id'],$registration_ids) or $com['type']=='house'){$registration_coms[]=$com;$registration_ids[]=$com['id'];}
				}
			}
		}
	$secid='';
	}


$eveid=$currentevent['id'];


include('scripts/sub_action.php');

$extrabuttons=array();
/*
$extrabuttons['message']=array('name'=>'current',
							   'title'=>'message',
							   'value'=>'message_absences.php',
							   'onclick'=>'processContent(this)'
							   );
*/
$extrabuttons['weekprint']=array('name'=>'current',
							  'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/register/',
							  'xmlcontainerid'=>'print',
							  'value'=>'register_week_print.php',
							  'onclick'=>'checksidsAction(this)');
$extrabuttons['reminder']=array('name'=>'current',
							  'title'=>'notice',
							  'xmlcontainerid'=>'print',
							  'value'=>'completion_reminder.php');
$extrabuttons['notice']=array('name'=>'current',
							  'title'=>'notice',
							  'xmlcontainerid'=>'notice',
							  'value'=>'register_notice.php');
$extrabuttons['summary']=array('name'=>'current',
							  'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/register/',
							  'xmlcontainerid'=>'print',
							  'value'=>'register_summary.php',
							  'onclick'=>'checksidsAction(this)');
$extrabuttons['previewselected']=array('name'=>'current',
							  'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/register/',
							  'xmlcontainerid'=>'print',
							  'value'=>'register_print.php',
							  'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons);
?>
  <div id="heading">
	<label><?php print_string('registersthissession',$book);?></label>
  </div>
  <div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		
		<table class="listmenu">
		    <thead>
		<tr>
		  <th class="checkall" style="width:1em;">
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
		  </th>
		  <th><?php print get_string('registrationgroup',$book) .' ('.get_string('students',$book).')'; ?></th>
		  <th><?php print_string('status',$book);?></th>
		  <th style="width:13%;"><?php print_string('inschool',$book);?></th>
		  <th style="width:13%;"><?php print_string('absent',$book);?></th>
		  <th style="width:13%;"><?php print_string('late',$book);?></th>
		  <th style="width:13%;"><?php print_string('latebeforeregisterclosed',$book);?></th>
		  <th style="width:13%;"><?php print_string('signedout',$book);?></th>
<?php
		$d_a=mysql_query("SELECT COUNT(logtime) FROM attendance WHERE logtime>='".$today."' ORDER BY logtime DESC;");
		$attno=mysql_result($d_a,0);
		if($attno>0){
?>
		  <th style="width:13%;"><?php print_string('lastupdate',$book);?></th>
<?php
			}
?>
		</tr>
		</thead>
<?php

	$totalnop=0;
	$totalnoa=0;
	$totalnol=0;
	$totalnopl=0;
	$totalnoso=0;
	$totalnosids=0;

	foreach($registration_coms as $com){
		$lastupdate="";
		list($nosids,$nop,$noa,$nol,$nopl,$noso)=check_community_attendance($com,$currentevent);
		if($nosids>0){
			$getparam='newcomid='.$com['id'];
			$getparam.='&newcid=';
			if(isset($com['yeargroup_id'])){
				$getparam.='&yid='.$com['yeargroup_id'];
				$tutor_users=(array)list_community_users($com,array('r'=>1,'w'=>1,'x'=>1),$com['yeargroup_id']);
				$title='<label>'.get_string('formtutor').'</label>';
				foreach($tutor_users as $uid => $tutor_user){
					$title.='<div>'.$tutor_user['forename'][0].'&nbsp;'. $tutor_user['surname'].'<div>';
					}
				}
			else{
				$tutor_users=array();
				$title='';
				}
			if(($nop+$noa+$nol+$noso)==$nosids and $nosids!=0){$status='complete';$cssclass='complete';}
			else{$status='incomplete';$cssclass='vspecial';}
			/*The number present in school is nop (present) + nol (late after register)*/
			$nop+=$nol;
			$totalnop+=$nop;
			$totalnoa+=$noa;
			$totalnol+=$nol;
			$totalnopl+=$nopl;
			$totalnoso+=$noso;
			$totalnosids+=$nosids;
			$d_a=mysql_query("SELECT MAX(logtime) FROM attendance JOIN comidsid ON attendance.student_id=comidsid.student_id JOIN community ON community.id=comidsid.community_id JOIN event ON  event.id=attendance.event_id WHERE community_id='".$com['id']."' AND (leavingdate='0000-00-00' OR leavingdate IS NULL) AND community.year='0000' AND community.type='form' AND event.date='$today' ORDER BY logtime DESC LIMIT 1;");
			$lastupdate=mysql_result($d_a,0);
			if($lastupdate!=''){
				$lastupdate=explode(" ",$lastupdate);
				$lastupdate=$lastupdate[1];
				}
?>
		<tr>
		  <td>
			<input type="checkbox" name="comids[]" value="<?php print $com['id'].':::'.$com['yeargroup_id']; ?>" />
		  </td>
		  <td>
			<span style="margin-right:4px;" title="<?php print $title;?>">
			    
			     <a onclick="parent.viewBook('register');" target="viewregister" href="register.php?current=register_list.php&cancel=completion_list.php&<?php print $getparam;?>&checkeveid=0&startday=&nodays=8"><?php print '<strong>'.$com['displayname'].'</strong>'. ' &nbsp; &nbsp;('.$nosids.') ';?></a>
		  </span>
		  </td>
		  <td class="<?php print $cssclass;?>">
			<?php print_string($status,$book);?>
		  </td>
		  <td>
			<?php print $nop;?>
		  </td>
		  <td>
			<?php print $noa;?>
		  </td>
		  <td>
			<?php print $nol;?>
		  </td>
		  <td>
			<?php print $nopl;?>
		  </td>
		  <td>
			<?php print $noso;?>
		  </td>
<?php
		if($attno>0){
?>
		  <td>
			<?php print $lastupdate;?>
		  </td>
<?php
			}
?>
		</tr>
<?php
			}
		}
?>
		<tr>
		  <td>
			<?php print_string('total',$book);?>
		  </td>
		  <td>
			&nbsp;
			<?php print ' &nbsp;'.$totalnosids.' ';?>
		  </td>
		  <td>
		  </td>
		  <td>
			<?php print $totalnop;?>
		  </td>
		  <td>
			<?php print $totalnoa;?>
		  </td>
		  <td>
			<?php print $totalnol;?>
		  </td>
		  <td>
			<?php print $totalnopl;?>
		  </td>
		  <td>
			<?php print $totalnoso;?>
		  </td>
		  <td>
		  </td>
		</tr>
		</table>

		<div id="xml-checked-action" style="display:none;">
		  <params>
			<checkname>comids</checkname>
		  </params>
		</div>
		<div id="xml-print" style="display:none;">
		  <params>
			<checkname>comids</checkname>
			<eveid><?php print $currentevent['id'];?></eveid>
			<evedate><?php print $currentevent['date'];?></evedate>
		  </params>
		</div>
		<div id="xml-notice" style="display:none;">
		  <params>
			<checkname>comids</checkname>
		  </params>
		</div>


		<input type="hidden" name="date" value="<?php print $currentevent['date'];?>" />
		<input type="hidden" name="session" value="<?php print $currentevent['session'];?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </form>
  </div>
