<?php
/**									completion_list.php
 *
 *   	Lists all completed registers for currentevent.
 */

$choice='completion_list.php';
if(isset($_POST['newsecid'])){$secid=$_POST['newsecid'];}

$registration_coms=array();

if($secid!='' and $secid>1){
	/* Limit list to just the year groups for this section. */
	$ygs=(array)list_yeargroups($secid);
	if(!isset($CFG->regtypes[$secid])){$type=$regtype;}
	else{$type=$CFG->regtypes[$secid];}
	foreach($ygs as $yg){
		$coms=(array)list_communities($type,'',$yg['id']);
		$registration_coms=array_merge($registration_coms,$coms);
		}
	}
else{
	/* Give the whole school when no section is selected. */
	$sections=list_sections();
	foreach($sections as $section){
		$secid=$section['id'];
		$ygs=(array)list_yeargroups($secid);
		if(!isset($CFG->regtypes[$secid])){$type=$regtype;}
		else{$type=$CFG->regtypes[$secid];}
		foreach($ygs as $yg){
			$coms=(array)list_communities($type,'',$yg['id']);
			$registration_coms=array_merge($registration_coms,$coms);
			}
		}
	$secid='';
	}


$eveid=$currentevent['id'];


include('scripts/sub_action.php');

$extrabuttons=array();
$extrabuttons['notice']=array('name'=>'current',
							  'title'=>'notice',
							  'xmlcontainerid'=>'notice',
							  'value'=>'register_notice.php');
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
		  <th>
			<label id="checkall">
			  <?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
			</label>
		  </th>
		  <th><?php print_string('registrationgroup',$book); ?></th>
		  <th><?php print_string('status',$book);?></th>
		  <th><?php print_string('present',$book);?></th>
		  <th><?php print_string('absent',$book);?></th>
		</tr>
<?php

	foreach($registration_coms as $com){
		list($nosids,$nop,$noa)=check_community_attendance($com,$currentevent);
		if($nosids>0){
			$getparam='newcomid='.$com['id'];
			$getparam.='&newcid=';
			if(isset($com['yeargroup_id'])){$getparam.='&yid='.$com['yeargroup_id'];}
			if(($nop+$noa)==$nosids and $nosids!=0){$status='complete';$cssclass='';}
			else{$status='incomplete';$cssclass='vspecial';}
			$totalnop+=$nop;
			$totalnoa+=$noa;
			$totalnosids+=$nosids;

			/* Called sids for convenience of js but the checkbox is really comids */
?>
		<tr>
		  <td>
			<input type="checkbox" name="comids[]" value="<?php print $com['id']; ?>" />
		  </td>
		  <td>
			<a onclick="parent.viewBook('register');" target="viewregister"  
			  href='register.php?current=register_list.php&cancel=completion_list.php&<?php print $getparam;?>&checkeveid=0&startday=&nodays=8'><?php print $com['displayname'];?></a>
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
		</tr>
		</table>

		<div id="xml-print" style="display:none;">
		  <params>
			<checkname>comids</checkname>
			<eveid><?php print $currentevent['id'];?></eveid>
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
