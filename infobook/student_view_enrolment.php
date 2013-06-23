<?php
/**
 *                                  student_view_enrolment.php
 */

$action='student_view_enrolment_action.php';

$Enrolment=fetchEnrolment($sid);
$enrolstatus=$Enrolment['EnrolmentStatus']['value'];

$extrabuttons=array();

/**
 * Under certain circumstances allow a delete of the student: really
 * only if tey have never been accepted on roll.
 */
if($_SESSION['role']=='admin' and ($enrolstatus=='CA' or $enrolstatus=='EN' or $enrolstatus=='RE' or $enrolstatus=='WL')){
	$extrabuttons['delete']=array('name'=>'current',
										 'value'=>'student_delete.php'
										 );
	}
three_buttonmenu($extrabuttons);

	/*Check user has permission to view*/
$perm=get_section_perm($student_secid);
if($perm['r']==1){
	$perm=getFormPerm($Student['RegistrationGroup']['value']);
	if($perm['r']!=1){$perm=getSENPerm($Student['YearGroup']['value']);}
	}
include('scripts/perm_action.php');

?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center listmenu">
		<div class="left">
<?php 
	$listname='enrolstatus';$listlabel='enrolstatus';$required='yes';
	include('scripts/set_list_vars.php');
	list_select_enum('enrolstatus',$listoptions,$book);
?>
		</div>
		<div class="left" >
<?php
	$listname='enrolyear';$listlabel='enrolmentyear';$required='yes';
	$enrolyear=$Enrolment['Year']['value'];
	include('scripts/list_calendar_year.php');
?>
		</div>
		<div class="right" >
<?php 
	$listname='enrolyid';$listlabel='yeargroup';$required='yes';
	$enrolyid=$Enrolment['YearGroup']['value'];
	include('scripts/list_year.php');
?>
		</div>
	  </fieldset>

	  <fieldset class="center listmenu">
<?php 	$tab=xmlarray_form($Enrolment,'','',$tab,'infobook');?>

	  </fieldset>

	  <fieldset class="center listmenu">
		<legend><?php print_string('assessments',$book);?></legend>
		<table class="listmenu">
<?php 
	$EnrolAssDefs=array();
	$com=get_community($Enrolment['Community']['id_db']);
	$EnrolAssDefs=array_merge(fetch_enrolmentAssessmentDefinitions(),fetch_enrolmentAssessmentDefinitions($com));
	$input_elements='';
	foreach($EnrolAssDefs as $index => $AssDef){
		$eid=$AssDef['id_db'];
		$input_elements.=' <input type="hidden" name="eids[]" value="'.$eid.'" />';
		$gena=$AssDef['GradingScheme']['value'];
		$Assessments=(array)fetchAssessments_short($sid,$eid,'G');
		if(sizeof($Assessments)>0){$value=$Assessments[0]['Value']['value'];}
		else{$value='';}
?>
		  <tr>
			  <td>
<?php 
		print '<label>'.$AssDef['Description']['value'].'</label>';
		if($gena!='' and $gena!=' '){
			$input_elements.=' <input type="hidden" name="scoretype'.$eid.'" value="grade" />';
			$pairs=explode (';',$AssDef['GradingScheme']['grades']);
?>
				<select tabindex='<?php print $tab++;?>' name='<?php print $eid;?>'>
<?php 
			print '<option value="" ';
			if($value==''){print 'selected';}	
			print ' ></option>';
			for($c3=0; $c3<sizeof($pairs); $c3++){
				list($level_grade, $level)=explode(':',$pairs[$c3]);
				print '<option value="'.$level.'" ';
				if($value==$level){print 'selected';}	
				print '>'.$level_grade.'</option>';
				}
?>
				</select>
<?php
			}
		else{
?>
				<input tabindex="<?php print $tab++;?>" 
				  name="<?php print $eid;?>" value="<?php print $value;?>"/>
<?php
			}
?>
			</td>
			<td>
<?php 
		if($index==0 and $CFG->enrol_assess=='yes'){
			$EnrolNotes=(array)fetchBackgrounds_Entries($sid,'ena');
			print '<label>'.get_string('notes',$book).'</label>';
?>
			  <input type="text" style="display:none;"
					name="enaid" value="<?php print $EnrolNotes[0]['id_db'];?>" />
			  <textarea name="enadetail" id="Detail"   
				tabindex="<?php print $tab++;?>" rows="3" cols="30" 
				  ><?php print $EnrolNotes[0]['Detail']['value_db'];?></textarea>
<?php 
		  }
?>
			</td>
		  </tr>
<?php 
		}
?>
		</table>

	  </fieldset>

	  <?php print $input_elements;?>
	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>

<?php
	require_once('lib/eportfolio_functions.php');
	html_document_drop($Student['EPFUsername']['value'],'enrolment','%');
?>
</div>
