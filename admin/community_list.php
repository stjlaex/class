<?php 
/**										 community_list.php
 */

$action='community_list_action.php';

if(isset($_GET['comid'])){$comid=$_GET['comid'];}
if(isset($_GET['date'])){$date=$_GET['date'];}else{$date='';}
if(isset($_GET['enrolyear'])){$enrolyear=$_GET['enrolyear'];}
if(isset($_GET['yid'])){$yid=$_GET['yid'];}
if(isset($_GET['enrolstage'])){$enrolstage=$_GET['enrolstage'];}else{$enrolstage='E';}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}
if(isset($_POST['date'])){$date=$_POST['date'];}
if(isset($_POST['enrolstage'])){$enrolstage=$_POST['enrolstage'];}

	if($comid!=-1){
		$com=get_community($comid);
		$coms[]=$com;
		$comtype=$com['type'];
		}
	else{
		$comtype='allapplied';
		$rowcells=list_enrolmentsteps();
		while(list($index,$enrolstatus)=each($rowcells)){ 
			if($enrolstatus=='EN'){$type='enquired';}
			elseif($enrolstatus=='AC'){$type='accepted';}
			else{$type='applied';}
			$coms[]=array('id'=>'','type'=>$type, 
					   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
			}
		}

	if($comtype=='applied' or $comtype=='enquired' or 
	   $comtype=='accepted' or $comtype=='allapplied'){

		$students=array();
		reset($coms);
		while(list($index,$com)=each($coms)){
			$comstudents=listin_community($com);
			trigger_error(' '.$com['name']. ' '.sizeof($comstudents),E_USER_WARNING);
			$students=array_merge($students,$comstudents);
			$AssDefs=fetch_enrolmentAssessmentDefinitions($com);
			}
		$description=display_yeargroupname($yid).' ('.display_curriculumyear($enrolyear).')';
		$infobookcurrent='student_view_enrolment.php';

		/*Check user has permission to edit*/
		$perm=getYearPerm($yid);
		$neededperm='r';
		include('scripts/perm_action.php');
		}
	elseif($comtype=='accomodation'){
		$boarder=$com['name'];
		$infobookcurrent='student_view_boarder.php';
		if($date!=''){
			$students=(array)listin_community($com,$date);
			$description=' '.$boarder.' ('.display_date($date).')';
			}
		else{
			$startdate='2000-01-01';
			$enddate='2010-01-01';
			$students=(array)listin_community($com,$enddate,$startdate);
			$description=' '.$boarder.' (overall)';
			}
		}

	three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div class="center" id="viewcontent">
		<table class="listmenu" id="sidtable">
		  <caption>
			<?php print_string($comtype,$book);?>
		  </caption>
		  <tr>
			<th style="width:40%;"><?php print $description;?></th>
			<th style="width:15%;"><?php print_string('dateofbirth','infobook');?></th>
			<th style="width:15%;"><?php print_string('schoolstartdate','infobook');?></th>
<?php
			if($comtype!='accomodation'){
				reset($AssDefs);
				while(list($index,$AssDef)=each($AssDefs)){
				print '<th>'.get_coursename($AssDef['Course']['value']).'<br />'. 
						$AssDef['Description']['value'].'</th>';
					}
				print '<th>';
				$required='no';$multi='1';
				if($comtype=='allapplied'){
					print_string('enrolstatus','infobook');
					}
				else{
					include('scripts/list_enrolstatus.php');
					}
				}
?>
			</th>
		  </tr>
<?php
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		$Enrolment=fetchEnrolment($sid);
?>
		  <tr id="sid-<?php print $sid;?>">
			<td>
			  <span title="<?php print $Enrolment['EnrolmentNotes']['value'];?>">
<?php
		if($perm['w']==1){
?>
			  <a href="infobook.php?current=<?php print
				$infobookcurrent;?>&cancel=student_view.php&sid=<?php print
				$sid;?>&sids[]=<?php print $sid;?>" target="viewinfobook"
				  onClick="parent.viewBook('infobook');"><?php print
				   $student['surname']. ', '.$student['forename']. 
				   ' '.$student['preferredforename']. 
				   ' ('.$Enrolment['EnrolNumber']['value'].')';?>
				</a>
<?php
			}
		else{
			print $student['surname']. ', '.$student['forename']. 
				   ' '.$student['preferredforename']. 
				   ' ('.$Enrolment['EnrolNumber']['value'].')';
			}
?>
			  </span>
			</td>
			<td>
			  <?php print display_date($student['dob']);?>
			</td>
			<td>
			  <?php print display_date($Enrolment['EntryDate']['value']);?>
			</td>
<?php
			if($comtype!='accomodation'){
				reset($AssDefs);
				while(list($index,$AssDef)=each($AssDefs)){
					$eid=$AssDef['id_db'];
					$Assessments=(array)fetchAssessments_short($sid,$eid,'G');
					/*assumes only one score allowed per enrolment
						assessment per student - no problem unless
						subject specific assessments are allowed - which
						bid='G' ensures they are not but could be in future*/
					if(sizeof($Assessments)>0){$result=$Assessments[0]['Result']['value'];}
					else{$result='&nbsp;';}
					print '<td>'.$result.'</td>';
					}
				}
			if($comtype=='allapplied'){
?>
			<td>
			  <?php print_string(displayEnum($Enrolment['EnrolmentStatus']['value'],$Enrolment['EnrolmentStatus']['field_db']),'admin');?>
			</td>
<?php
				}
			else{
?>
			<td>
			  <input type="checkbox"  
				name="sids[]" value="<?php print $sid;?>" />
			</td>
<?php
				}
?>
		  </tr>
<?php
		}
?>
		</table>
	  </div>

	<input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" /> 
	<input type="hidden" name="date" value="<?php print $date;?>" /> 
	<input type="hidden" name="comid" value="<?php print $comid;?>" /> 
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
