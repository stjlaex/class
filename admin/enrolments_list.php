<?php 
/**										 enrolments_list.php
 */

$action='enrolments_list_action.php';

if(isset($_GET['comid'])){$comid=$_GET['comid'];}else{$comid=-1;}
if(isset($_GET['enrolyear'])){$enrolyear=$_GET['enrolyear'];}
if(isset($_GET['enrolstatus'])){$enrolstatus=$_GET['enrolstatus'];}
if(isset($_GET['yid'])){$yid=$_GET['yid'];}
if(isset($_GET['enrolstage'])){$enrolstage=$_GET['enrolstage'];}else{$enrolstage='E';}
if(isset($_GET['startdate'])){$startdate=$_GET['startdate'];}
if(isset($_GET['boarder'])){$boarder=$_GET['boarder'];}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}
if(isset($_POST['enrolstage'])){$enrolstage=$_POST['enrolstage'];}
if(isset($_POST['enrolstatus'])){$enrolstatus=$_POST['enrolstatus'];}
if(isset($_POST['startdate'])){$startdate=$_POST['startdate'];}
	/**
	 * Four possible types of table: current yeargroup for selecting
	 * leavers (enrolstage=C), current yeargroup for re-enrolment
	 * (enrolstage=RE), one of the enrolment groups (enrolstage=E), or
	 * displaying all enrolment groups in one go.
	 */
	if($enrolstage=='C' or $enrolstage=='P'){
		$application_steps=array('C','P');
		$AssDefs=array();
		}
	else{
		$application_steps=array('EN','AP','AT','RE','CA','WL','ACP','AC');
		$AssDefs=fetch_enrolmentAssessmentDefinitions();
		}

	/**
	 * Put together an array of communities (coms) which we are listing students for.
	 */
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
			list($current_enrolstatus,$yid)=explode(':',$com['name']);
			}
		}
	elseif($enrolstage=='C' and isset($yid) and $yid>-100){
		$comid=update_community(array('id'=>'','name'=>$yid,'type'=>'year'));
		$com=(array)get_community($comid);
		$coms[]=$com;
		$comtype='year';
		$current_enrolstatus='C';
		}
	elseif(!empty($enrolstatus) and in_array($enrolstatus,$application_steps)){
		$yeargroups=list_yeargroups();
		foreach($yeargroups as $yeargroup){ 
			if($enrolstatus=='EN'){$type='enquired';}
			elseif($enrolstatus=='AC'){$type='accepted';}
			else{$type='applied';}
			$coms[]=array('id'=>'','type'=>$type, 
					   'name'=>$enrolstatus.':'.$yeargroup['id'],'year'=>$enrolyear);
			}
		$comtype=$type;
		$current_enrolstatus=$enrolstatus;
		}
	elseif($enrolstage=='P'){
		$comtype='allapplied';
		$yeargroups=list_yeargroups();
		$type='alumni';
		foreach($yeargroups as $yeargroup){
			$coms[]=array('id'=>'','type'=>$type, 
						  'name'=>'P:'.$yeargroup['id'],'year'=>$enrolyear);
			}
		}
	elseif($enrolstage=='C'){
		$comtype='allapplied';
		$yeargroups=list_yeargroups();
		$type='year';
		foreach($yeargroups as $yeargroup){
			$coms[]=array('id'=>'','type'=>$type,'name'=>$yeargroup['id']);
			}
		}
	else{
		$comtype='allapplied';
		if($yid>-100){
			foreach($application_steps as $es){ 
				if($es=='EN'){$type='enquired';}
				elseif($es=='AC'){$type='accepted';}
				else{$type='applied';}
				$coms[]=array('id'=>'','type'=>$type, 
							  'name'=>$es.':'.$yid,'year'=>$enrolyear);
				}
			}
		else{
			$yeargroups=list_yeargroups();
			foreach($application_steps as $es){ 
				foreach($yeargroups as $yeargroup){ 
					if($es=='EN'){$type='enquired';}
					elseif($es=='AC'){$type='accepted';}
					else{$type='applied';}
					$coms[]=array('id'=>'','type'=>$type, 
								  'name'=>$es.':'.$yeargroup['id'],'year'=>$enrolyear);
					}
				}
			}
		}

	if($enrolstage=='RE'){
		$AssDefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
		$pairs=explode (';', $AssDefs[0]['GradingScheme']['grades']);
		$grades=array();
		foreach($pairs as $pair){
			list($grade['result'], $grade['value'])=explode(':',$pair);
			$grades[]=$grade;
			$$grade['value']=0;/*used for a running total*/
			}
		$eid=$AssDefs[0]['id_db'];
		$description=display_yeargroupname($yid).' ('.display_curriculumyear($enrolyear-1).')';

		if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
			$reenrol_boarder_eid=$AssDefs[1]['id_db'];
			$pairs=explode (';', $AssDefs[1]['GradingScheme']['grades']);
			$boarder_grades=array();
			foreach($pairs as $pair){
				list($grade['result'], $grade['value'])=explode(':',$pair);
				$boarder_grades[$grade['value']]=$grade['result'];
				}
			}
		}
	else{
		$description=display_yeargroupname($yid).' ('.display_curriculumyear($enrolyear).')';
		}

	$students=array();
	foreach($coms as $com){
		if(isset($startdate)){
			$comstudents=(array)listin_community_new($com,$startdate);
			}
		else{
			$comstudents=(array)listin_community($com);
			}
		$students=array_merge($students,$comstudents);
		if($enrolstage=='E'){
			//$AssDefs=array_merge($AssDefs,fetch_enrolmentAssessmentDefinitions($com));
			}
		}

	if(!empty($boarder)){
		$boardercom=array('id'=>'','type'=>'accomodation','name'=>$boarder);
		$boarders=(array)listin_community($boardercom);
		$boarder_students=array();
		foreach($boarders as $student){
			$boarder_students[]=$student['id'];
			}
		}
	elseif(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
		$boardercoms=(array)list_communities('accomodation');
		$boarder_students=array();
		foreach($boardercoms as $boardercom){
			$boarders=(array)listin_community($boardercom);
			foreach($boarders as $student){
				$boarder_students[]=$student['id'];
				}
			}
		}

	$infobookcurrent='student_view_enrolment.php';

	/*Check user has permission to edit*/
	$perm=getYearPerm($yid);
	$neededperm='r';
	include('scripts/perm_action.php');

	$sort_types='';

	$extrabuttons=array();
/*   	$extrabuttons['addresslabels']=array('name'=>'current',
										 'title'=>'printaddresslabels',
										 'onclick'=>'checksidsAction(this)',
										 'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/infobook/',
										 'value'=>'contact_labels_print.php');
*/

	if($comtype!='allapplied'){
		three_buttonmenu($extrabuttons,'infobook');
		}
	else{
		two_buttonmenu($extrabuttons,'infobook');
		}


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
		  <thead>
		  <tr>
			<th colpsan="2"><?php print_string('checkall'); ?>
			  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
			</th>
			<th style="width:40%;"><?php print $description;?></th>
			<th><?php print_string('yeargroup','infobook');?></th>
			<th><?php print_string('schoolstartdate','infobook');?></th>
<?php

		$required='no';$multi='1';
		$colspan=2 + sizeof($AssDefs);
		if($enrolstage=='RE'){
			foreach($AssDefs as $AssDef){
				print '<th>'.$AssDef['Description']['value'].'</th>';
				}
			}
		elseif($comtype=='allapplied' or 
			   $enrolstatus=='year' or $enrolstatus=='alumni'){
				print '<th colspan="'.$colspan.'">'.get_string('enrolstatus','infobook').'</th>';
			}
		else{
			foreach($AssDefs as $ano => $AssDef){
				if($AssDef['Course']['value']!='%'){$coursename=get_coursename($AssDef['Course']['value']);}
				else{$coursename='';}
				$sortno=$ano+4;
				$sort_types.=",'s'";
?>
		<th> 
				<?php print $AssDef['Description']['value'];?>
				<div class="rowaction">
				  <input class="underrow" type='button' name='action' value='v' onClick='tsDraw("<?php print $sortno;?>A", "sidtable");' />
				  <input class="underrow"  type='button' name='action' value='-' onClick='tsDraw("<?php print $sortno;?>U", "sidtable");' />
				  <input class="underrow"  type='button' name='action' value='^' onClick='tsDraw("<?php print $sortno;?>D", "sidtable");' />
				</div>
		</th>
<?php
				}
			print '<th>'.get_string('enrolstatus','infobook').'</th>';
			}
?>
		  </tr>
		</thead>
<?php
	$rown=1;
	foreach($students as $student){
		$sid=$student['id'];
		$Enrolment=fetchEnrolment($sid);
		if(empty($boarder) or in_array($sid,$boarder_students)){
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
		print '<td>'.get_yeargroupname($Enrolment['YearGroup']['value']).' ('.$student['form_id'].')</td>';
		print '<td>'.display_date($Enrolment['EntryDate']['value']).'</td>';
?>
			<td class="row">
<?php
			if($comtype=='allapplied'){
				print_string(displayEnum($Enrolment['EnrolmentStatus']['value'],$Enrolment['EnrolmentStatus']['field_db']),'admin');
				}
			elseif($enrolstage=='C'){
				foreach($application_steps as $value){
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
				foreach($grades as $grade){
					$checkclass='';
					if($value!='' and $value==$grade['value']){
						$checkclass='checked';
						$$grade['value']++;
						}
					print '<div class="'.$checkclass.'"><label>'.$grade['result'].'</label>';
					print '<input type="radio" name="RE'.$sid.'"
						tabindex="'.$tab++.'" value="'.$grade['value'].'" '.$checkclass;
					print '/></div>';
					}
				if(isset($reenrol_boarder_eid)){
					$listname='ACRE'.$sid;$listlabel='';
					$Assessments=(array)fetchAssessments_short($sid,$reenrol_boarder_eid,'G');
					if(sizeof($Assessments)>0){${'sel'.$listname}=$Assessments[0]['Value']['value'];}
					elseif(in_array($sid,$boarder_students)){${'sel'.$listname}='1';}
					print '</td><td>';
					include('scripts/set_list_vars.php');
					list_select_list($boarder_grades,$listoptions,$book);
					}
				}
			else{
				foreach($AssDefs as $AssDef){
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
				foreach($application_steps as $value){
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
			  <input type="hidden" name="sids[]" value="<?php print $sid;?>" />
			  <input type="hidden" name="yid<?php print $sid;?>" value="<?php print $Enrolment['YearGroup']['value'];?>" />
			</td>
		  </tr>
<?php
			}
		}
	if($enrolstage=='RE'){
?>
		<tr>
		  <th colspan="4">
			<?php print_string('total',$book);?>
		  </th>
		  <td class="row">
<?php
				foreach($grades as $grade){
					print '<div class=""><label>' 
							.$grade['result'].'</label>';
					print $$grade['value'];
					print '</div>';
					}
?>
		  </td>
<?php
		if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
			print '<th></th>';
			}
?>
		</tr>
<?php
	}
?>
		</table>
	  </div>

	<input type="hidden" name="enrolstatus" value="<?php print $enrolstatus;?>" /> 
	<input type="hidden" name="enrolstage" value="<?php print $enrolstage;?>" /> 
	<input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" /> 
	<input type="hidden" name="comid" value="<?php print $comid;?>" /> 
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>


<script type="text/javascript">
	var TSort_Data = new Array ('sidtable', '', '', ''<?php print $sort_types;?>);
		tsRegister();
</script> 
