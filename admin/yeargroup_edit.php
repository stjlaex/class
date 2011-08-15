<?php 
/**			   						   yeargroup_edit.php
 */

$action='yeargroup_edit_action.php';

if(isset($_GET['comtype'])){$comtype=$_GET['comtype'];}else{$comtype='year';}
if(isset($_GET['comname'])){$comname=$_GET['comname'];}else{$comname='';}
if(isset($_GET['enrolyear'])){$enrolyear=$_GET['enrolyear'];}else{$enrolyear='';}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}


	if($newcomid!=''){
		$newcommunity=get_community($newcomid);
		}
	if($comid!=''){
		$currentcommunity=get_community($comid);
		$comtype=$currentcommunity['type'];
		$comname=$currentcommunity['name'];
		}
	else{
		$currentcommunity=array('type'=>$comtype,'name'=>$comname);
		$comid=update_community($currentcommunity);
		}

	if($comtype=='year'){
		/*Check user has permission to edit*/
		$perm=getYearPerm($comname);
		$neededperm='r';
		include('scripts/perm_action.php');
	
		$d_year=mysql_query("SELECT name FROM yeargroup WHERE id='$comname'");
		$displayname=mysql_result($d_year,0);
		if($newcomid==''){
			/*default to newly accepted students for this yeargroup*/
			$year=get_curriculumyear();
			$newcommunity=array('type'=>'accepted','name'=>'AC:'.$comname,'year'=>$year);
			}
		}
	elseif($comtype=='alumni'){
		$displayname=get_string($comtype,'infobook');
		if($newcomid==''){$newcommunity=array('type'=>'year','name'=>'none');}
		}
	else{
		$displayname=get_string($comtype,'infobook').' '.$comname;
		}


	$oldstudents=listin_community($currentcommunity);
	$newstudents=listin_union_communities($currentcommunity,$newcommunity);


	three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div style="width:48%;float:left;"  id="viewcontent">
		<table class="listmenu" id="sidtable">
		  <caption>
			<?php print_string('current');?>
			<?php print_string('yeargroup');?>
		  </caption>
		  <tr>
			<th colspan="4">
			  <h2>
			  <?php print $displayname;?>
			  </h2>
			</th>
			<th>
			  <?php print_string('remove');?><br />
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
				<?php print_string('checkall'); ?>
			</th>
		  </tr>
<?php
	$rown=1;
	foreach($oldstudents as $student){
		if($_SESSION['role']=='admin' or $_SESSION['role']=='office'
		   or $_SESSION['role']=='district'){
			$Enrolment=fetchEnrolment($student['id']);
			$extra=$Enrolment['EnrolNumber']['value'];
			}
		else{$extra='&nbsp;';}
		$sid=$student['id'];
		print '<tr id="sid-'.$sid.'">';
		print '<td>'.$rown++.'</td>';
		print '<td>'.$student['surname']. ', '.$student['forename'].' '.$student['middlenames'].' '.$student['preferredforename'].'</td><td>'.$student['form_id'].
												'</td><td>'.$extra.'</td>';
		print '<td><input type="checkbox" name="sids[]" value="'.$sid.'" /></td>';
		print '</tr>';
		}
?>
		</table>
	  </div>

	  <div style="width:50%;float:right;">
		<fieldset class="center">
		<legend><?php print_string('changegroup',$book);?></legend>
		  <div class="center">
<?php
			$onchange='yes';
			if($newcomid==''){
				/*the selected community*/
				$newcomid=update_community($newcommunity);
				}
			$selcomids=array($newcomid);
			$listtype='yeargroups';
			include('scripts/list_community.php');
?>
		  </div>
		</fieldset>

		<fieldset class="center">
		<legend><?php print_string('choosestudentstoadd',$book);?></legend>
		<div class="center">
		  <label><?php print_string('studentsnotin',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">
<?php
	foreach($newstudents['scab'] as $student){
		print '<option ';
		print	'value="'.$student['student_id'].'">'. 
		$student['surname'].', '.$student['forename'].' '. 
		$student['middlenames'].' '.$student['preferredforename'].' ('.$student['form_id'].')</option>';
		}
?>
		  </select>
		</div>

		</fieldset>
	  </div>

	<input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" /> 
	<input type="hidden" name="comid" value="<?php print $comid;?>" /> 
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
