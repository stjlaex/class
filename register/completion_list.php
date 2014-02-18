<?php
/**									completion_list.php
 *
 *   	Lists all completed registers for currentevent.
 */

$choice='completion_list.php';

$registration_coms=array();
$registration_ids=array();

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
		<tr>
		  <th  style="width:1em;">
			<label id="checkall">
			  <?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
			</label>
		  </th>
		  <th><?php print_string('registrationgroup',$book); ?></th>
		  <th><?php print_string('status',$book);?></th>
		  <th><?php print_string('inschool',$book);?></th>
		  <th><?php print_string('absent',$book);?></th>
		  <th><?php print_string('late',$book);?></th>
		  <th><?php print_string('latebeforeregisterclosed',$book);?></th>
		  <th><?php print_string('signedout',$book);?></th>
		</tr>
<?php

	$totalnop=0;
	$totalnoa=0;
	$totalnol=0;
	$totalnopl=0;
	$totalnoso=0;
	$totalnosids=0;

	foreach($registration_coms as $com){
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
?>
		<tr>
		  <td>
			<input type="checkbox" name="comids[]" value="<?php print $com['id'].':::'.$com['yeargroup_id']; ?>" />
		  </td>
		  <td>
			<span style="margin-right:4px;" title="<?php print $title;?>">
			<a onclick="parent.viewBook('register');" target="viewregister"  
			  href="register.php?current=register_list.php&cancel=completion_list.php&<?php print $getparam;?>&checkeveid=0&startday=&nodays=8"><?php print $com['displayname'];?></a>
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
		</tr>
<?php
			}
		}
?>
		<tr>
		  <td>Total:
		  </td>
		  <td>
			&nbsp;
		  </td>
		  <td>
			<?php print $totalnosids;?>
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
