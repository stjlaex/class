<?php
/**
 *                                  student_view_enrolment.php
 */

$action='student_view_enrolment_action.php';

three_buttonmenu();

	/*Check user has permission to view*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid,$respons);
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
	$Enrolment=fetchEnrolment($sid);
	$enrolstatus=$Enrolment['EnrolmentStatus']['value'];
	$listname='enrolstatus';$listlabel='enrolstatus';$required='yes';
	include('scripts/set_list_vars.php');
	list_select_enum('enrolstatus',$listoptions,$book);
?>
		</div>
		<div class="right" >
<?php
	$listname='enrolyear';$listlabel='year';$required='yes';
	$enrolyear=$Enrolment['Year']['value'];
	include('scripts/list_calendar_year.php');
?>
		</div>
	  </fieldset>

	  <fieldset class="center listmenu">
		<table class="listmenu">
		  <tr>
			<td>
			  &nbsp
			</td>
			<td>
<?php 
	$listname='enrolyid';$listlabel='yeargroup';$required='yes';
	$enrolyid=$Enrolment['YearGroup']['value'];
	include('scripts/list_year.php');
?>
			</td>
		  </tr>
		</table>

<?php 	$tab=xmlarray_form($Enrolment,'','',$tab,'infobook');?>

	  </fieldset>

	  <fieldset class="center listmenu">
		<table class="listmenu">
<?php 
	$com=get_community($Enrolment['Community']['id_db']);
	$AssDefs=fetch_enrolmentAssessmentDefinitions($com);
	reset($AssDefs);
	$input_elements='';
	while(list($index,$AssDef)=each($AssDefs)){
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
		print '<label>'.get_coursename($AssDef['Course']['value']).'<br />'. 
						$AssDef['Description']['value'].'</label>';
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
				list($level_grade, $level)=split(':',$pairs[$c3]);
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
</div>
