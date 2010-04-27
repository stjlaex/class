<?php
/**									completion_list.php
 *
 *   	Lists all completed registers for currentevent.
 */

$choice='completion_list.php';

if(!isset($CFG->registrationtype) or $CFG->registrationtype=='form'){
	$registration_coms=list_communities('form');
	}
else{
	$registration_coms=list_communities($CFG->registrationtype);
	}

$eveid=$currentevent['id'];


include('scripts/sub_action.php');

$extrabuttons=array();
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


	while(list($index,$com)=each($registration_coms)){
		list($nosids,$nop,$noa)=check_communityAttendance($com,$eveid);
		if($nosids>0){
			if(!isset($CFG->registrationtype) or $CFG->registrationtype=='form'){
				$getparam='newfid='.$com['name'];
				}
			else{
				$getparam='newcomid='.$com['id'];
				}
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
