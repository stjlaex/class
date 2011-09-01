<?php
/**									completion_list.php
 *
 *   	Lists all completed registers for currentevent.
 */

$choice='completion_list.php';

if(!isset($CFG->registrationtype)){$CFG->registrationtype[1]=='form';}

	$registration_coms=array();
	foreach($CFG->registrationtype as $secid => $type){
		trigger_error($type.' '.' '.$secid,E_USER_WARNING);
		$ygs=(array)list_yeargroups($secid);
		foreach($ygs as $yg){
			trigger_error($type.' '.$yg['id']. ' '.$secid,E_USER_WARNING);
			$coms=(array)list_communities($type,'',$yg['id']);
			$registration_coms=array_merge($registration_coms,$coms);
			}
		}

$eveid=$currentevent['id'];


include('scripts/sub_action.php');

$extrabuttons=array();
$extrabuttons['notice']=array('name'=>'current',
							  'title'=>'notice',
							  'value'=>'register_notice.php');
$extrabuttons['previewselected']=array('name'=>'current',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/register/',
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
			<input type="checkbox" name="sids[]" value="<?php print $com['id']; ?>" />
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

		<div id="xml-checked-action" style="display:none;">
		  <params>
			<sids><?php print $sid;?></sids>
			<selectname>wrapper_rid</selectname>
		  </params>
		</div>


		<input type="hidden" name="date" value="<?php print $currentevent['date'];?>" />
		<input type="hidden" name="session" value="<?php print $currentevent['session'];?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	    <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  </form>
  </div>
