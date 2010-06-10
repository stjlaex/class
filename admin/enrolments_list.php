<?php 
/**										 enrolments_list.php
 */

$action='enrolments_list_action.php';

if(isset($_GET['comid'])){$comid=$_GET['comid'];}
if(isset($_GET['enrolyear'])){$enrolyear=$_GET['enrolyear'];}
if(isset($_GET['yid'])){$yid=$_GET['yid'];}
if(isset($_GET['enrolstage'])){$enrolstage=$_GET['enrolstage'];}else{$enrolstage='E';}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}
if(isset($_POST['enrolstage'])){$enrolstage=$_POST['enrolstage'];}

	/**
	 * Four possible types of table: current yeargroup for selecting
	 * leavers (enrolstage=C), current yeargroup for re-enrolment
	 * (enrolstage=RE), one of the enrolment groups (enrolstage=E), or
	 * displaying all enrolment groups in one go.
	 */
	if($enrolstage=='C'){
		$application_steps=array('C','P');
		}
	else{
		$application_steps=array('EN','AP','AT','ATD','RE','CA','WL','ACP','AC');
		}
	if($comid!=-1){
		$com=get_community($comid);
		$coms[]=$com;
		$comtype=$com['type'];
		if($comtype=='year'){
			$yid=$com['name'];
			$current_enrolstatus='C';
			}
		//		elseif($comtype=='alumni'){
		//	$yid=$com['name'];
		//	$current_enrolstatus='P';
		//	}
		else{
			list($current_enrolstatus,$yid)=split(':',$com['name']);
			}
		}
	else{
		$comtype='allapplied';
		while(list($index,$enrolstatus)=each($application_steps)){ 
			if($enrolstatus=='EN'){$type='enquired';}
			elseif($enrolstatus=='AC'){$type='accepted';}
			else{$type='applied';}
			$coms[]=array('id'=>'','type'=>$type, 
					   'name'=>$enrolstatus.':'.$yid,'year'=>$enrolyear);
			}
		}

	if($enrolstage=='RE'){
		$AssDefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
		$pairs=explode (';', $AssDefs[0]['GradingScheme']['grades']);
		$grades=array();
		while(list($index,$pair)=each($pairs)){
			list($grade['result'], $grade['value'])=split(':',$pair);
			$grades[]=$grade;
			$$grade['value']=0;/*used for a running total*/
			}
		$eid=$AssDefs[0]['id_db'];
		$description=display_yeargroupname($yid).' ('.display_curriculumyear($enrolyear-1).')';
		}
	else{
		$description=display_yeargroupname($yid).' ('.display_curriculumyear($enrolyear).')';
		}

	$students=array();
	reset($coms);
	while(list($index,$com)=each($coms)){
		$comstudents=listin_community($com);
		$students=array_merge($students,$comstudents);
		if($enrolstage=='E'){
			$AssDefs=fetch_enrolmentAssessmentDefinitions($com);
			}
		}

	$infobookcurrent='student_view_enrolment.php';

	/*Check user has permission to edit*/
	$perm=getYearPerm($yid,$respons);
	$neededperm='r';
	include('scripts/perm_action.php');

	$extrabuttons=array();
   	$extrabuttons['addresslabels']=array('name'=>'current',
										 'title'=>'printaddresslabels',
										 'onclick'=>'checksidsAction(this)',
										 'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/infobook/',
										 'value'=>'contact_labels_print.php');
	three_buttonmenu($extrabuttons,'infobook');
?>
  <div id="heading">
<?php
	$listname='filtervalue';$listlabel='';
	$listdescriptionfield='result';$listvaluefield='value';
	include('scripts/set_list_vars.php');
	list_select_list($grades,$listoptions,$book);
	$button['filterlist']=array('name'=>'filter','value'=>$enrolstage);
	all_extrabuttons($button,'entrybook','sidtableFilter(this)');
?>
  </div>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div  class="fullwidth" id="viewcontent">
		<table class="listmenu sidtable" id="sidtable">
		  <caption>
			<?php print_string($comtype,$book);?>
		  </caption>
		  <tr>
			<th colpsan="2"><?php print_string('checkall'); ?>
			  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
			</th>
			<th style="width:40%;"><?php print $description;?></th>
<?php
		if($current_enrolstatus=='AC'){
			print '<th>'.get_string('schoolstartdate','infobook').'</th>';
			}

		$required='no';$multi='1';
		   	if($comtype=='allapplied' or 
			   $enrolstatus=='year' or $enrolstatus='alumni'){
				print '<th colspan="2">'.get_string('enrolstatus','infobook').'</th>';
				}
			elseif($enrolstage=='RE'){
				print '<th colspan="2">'.get_string('reenroling','infobook').'</th>';
				}
		   	else{
		   		reset($AssDefs);
		   		while(list($index,$AssDef)=each($AssDefs)){
		   			print '<th>'.get_coursename($AssDef['Course']['value']).'<br />'. 
		   					$AssDef['Description']['value'].'</th>';
		   			}
				print '<th>'.get_string('enrolstatus','infobook').'</th>';
		   		}
?>
			</th>
		  </tr>
<?php
	$rown=1;
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		$Enrolment=fetchEnrolment($sid);
?>
		  <tr id="sid-<?php print $sid;?>">
			<td>
			  <input type="checkbox" name="sids[]" value="<?php print $sid;?>" />
			  <?php print $rown++;?>
			</td>
			<td>
<?php
		/*TODO: add a little turnover corner for notes*/
		if($Enrolment['ApplicationNotes']['value']!='' and $Enrolment['ApplicationNotes']['value']!=' '){
?>
			  <span title="<?php print display_date($student['dob']). 
				' <br />'.$Enrolment['ApplicationNotes']['value']. ' - '.
					display_date($Enrolment['ApplicationDate']['value']);?>">
<?php
			}
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

		if($Enrolment['ApplicationNotes']['value']!='' and $Enrolment['ApplicationNotes']['value']!=' '){
?>
			  </span>
<?php
			}
?>
			</td>
<?php
		if($current_enrolstatus=='AC'){
			print '<td>'.display_date($Enrolment['EntryDate']['value']).'</td>';
			}
?>
			<td class="row">
<?php
			if($comtype=='allapplied'){
?>
			  <?php print_string(displayEnum($Enrolment['EnrolmentStatus']['value'],$Enrolment['EnrolmentStatus']['field_db']),'admin');?>
<?php
				}
		   	elseif($enrolstage=='C'){
				reset($application_steps);
				while(list($index,$value)=each($application_steps)){
					$checkclass='';
					if($value==$current_enrolstatus){$checkclass='checked';}
					print '<div class="'.$checkclass.'"><label>'.$value.'</label>';
					print '<input type="radio" name="C'.$sid.'" tabindex="'. 
						$tab++.'" value="'.$value.'" '.$checkclass;
					print '/></div>';
					}
				}
			elseif($enrolstage=='RE'){
				$Assessments=(array)fetchAssessments_short($sid,$eid,'G');
				if(sizeof($Assessments)>0){$value=$Assessments[0]['Value']['value'];}
				else{$value='';}
				reset($grades);
				while(list($index,$grade)=each($grades)){
					$checkclass='';
					if($value!='' and $value==$grade['value']){
						$checkclass='checked';
						$$grade['value']++;
						}
					print '<div class="'.$checkclass.'"><label>' 
							.$grade['result'].'</label>';
					print '<input type="radio" name="RE'.$sid.'"
						tabindex="'.$tab++.'" value="'.$grade['value'].'" '.$checkclass;
					print '/></div>';
					}
				}
			else{
				reset($AssDefs);
				while(list($index,$AssDef)=each($AssDefs)){
					$eid=$AssDef['id_db'];
					$Assessments=(array)fetchAssessments_short($sid,$eid,'G');
					/*	Assumes only one score allowed per enrolment
					 *	assessment per student - no problem unless
					 *	subject specific assessments are allowed -
					 *	which bid='G' ensures they are not but could
					 *	be in future.
					 */
					if(sizeof($Assessments)>0){$result=$Assessments[0]['Result']['value'];}
					else{$result='&nbsp;';}
					print $result.'</td><td class="row">';
					}

				reset($application_steps);
				while(list($index,$value)=each($application_steps)){
					$checkclass='';
					if($current_enrolstatus!='' and $value==$current_enrolstatus){
						$checkclass='checked';
						}
					print '<div class="'.$checkclass.'"><label>' 
							.$value.'</label>';
					print '<input type="radio" name="E'.$sid.'"
						tabindex="'.$tab++.'" value="'.$value.'" '.$checkclass;
					print '/></div>';
					}
				}
?>
			  <input type="hidden"  
				name="sids[]" value="<?php print $sid;?>" />
			</td>
		  </tr>
<?php
		}
	if($enrolstage=='RE'){
?>
		<tr>
		  <th colspan="2">
			<?php print_string('total',$book);?>
		  </th>
		  <td class="row">
<?php
				reset($grades);
				while(list($index,$grade)=each($grades)){
					print '<div class=""><label>' 
							.$grade['result'].'</label>';
					print $$grade['value'];
					print '</div>';
					}
?>
		  </td>
		</tr>
<?php
		}
?>
		</table>
	  </div>

	<input type="hidden" name="enrolstage" value="<?php print $enrolstage;?>" /> 
	<input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" /> 
	<input type="hidden" name="comid" value="<?php print $comid;?>" /> 
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
