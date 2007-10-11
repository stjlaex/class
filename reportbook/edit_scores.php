<?php
/**                   edit_scores.php
 *
 * TO DO: handle pids and non-grade scores
 */

$action='edit_scores_action.php';
$choice='new_assessment.php';

if(isset($_GET['eid'])){$eid=$_GET['eid'];}
elseif(isset($_POST['eid'])){$eid=$_POST['eid'];}
if(isset($_GET['bid'])){$selbid=$_GET['bid'];}
elseif(isset($_POST['bid'])){$selbid=$_POST['bid'];}
if(isset($_GET['pid'])){$selpid=$_GET['pid'];}
elseif(isset($_POST['pid'])){$selpid=$_POST['pid'];}

	$AssDef=fetchAssessmentDefinition($eid);
	$crid=$AssDef['Course']['value'];
	$stage=$AssDef['Stage']['value'];
	$year=$AssDef['Year']['value'];
	$students=array();
	if($stage!='%'){
		$cohorts[]=array('id'=>'','course_id'=>$crid,'stage'=>$stage,'year'=>$year);
		}
	else{
		$cohorts=(array)list_course_cohorts($crid,$year);
		}
	while(list($index,$cohort)=each($cohorts)){
		$students=array_merge($students,listin_cohort($cohort));
		}

	$compstatus=$AssDef['ComponentStatus']['value'];
	$resq=$AssDef['ResultQualifier']['value'];
	$deadline=$AssDef['Deadline']['value'];

	$grading_grades=$AssDef['GradingScheme']['grades'];
	if($grading_grades!='' and $grading_grades!=' '){
		$pairs=explode (';',$grading_grades);
		}
	else{
		}

	if($deadline!='0000-00-00'){$entrydate=$deadline;}
	else{$entrydate=date('Y').'-'.date('n').'-'.date('j');}

	/*make a list of subjects*/
	$subjects=array();
	if($AssDef['Subject']['value']!='%'){
		$subjects[]=array('id'=>$AssDef['Subject']['value'],
						  'name'=>get_subjectname($AssDef['Subject']['value']));
		}
	else{
		$d_cridbid=mysql_query("SELECT id, name FROM subject
					JOIN cridbid ON cridbid.subject_id=subject.id
					WHERE cridbid.course_id LIKE '$crid' ORDER BY subject.id");
		while($subject=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
			$bid=$subject['id'];
			$subjects[]=array('id'=>$subject['id'],
							  'name'=>$subject['name']);
			}
		}

$extrabuttons['importscores']=array('name'=>'current','value'=>'new_assessment_scores.php');
three_buttonmenu($extrabuttons);
?>

  <div id="heading">
	<label><?php print_string('assessment'); ?></label>
	<?php print $AssDef['Description']['value'];?>
  </div>

  <div id="viewcontent" class="content">

	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu sidtable" id="sidtable">
		<tr>
		  <th colspan="1">&nbsp</th>
		  <th><?php print_string('student'); ?></th>
		  <th class="edit">
<?php
	if($selbid==''){
		$selbid=$subjects[0]['id'];
		$selnewbid=$selbid;
		}
	else{
		$selnewbid=$selbid;
		}
	$listname='newbid';$listlabel='subject';$onchange='yes';$multi=1;
	include('scripts/set_list_vars.php');
	list_select_list($subjects,$listoptions,$book);

	if($compstatus=='A'){$compstatus='%';}
	$d_component=mysql_query("SELECT component.id AS id, subject.name
					AS name FROM component JOIN subject ON subject.id=component.id
					WHERE component.course_id='$crid' AND component.subject_id='$selbid'
					AND component.status LIKE '$compstatus'");
	if(mysql_num_rows($d_component)>0){
		$components=array();
		while($component=mysql_fetch_array($d_component,MYSQL_ASSOC)){
			$components[]=$component;
			}
		if($selpid==''){
			$selpid=$components[0]['id'];
			$selnewpid=$selpid;
			}
		else{
			$selnewpid=$selpid;
			}
		$listname='newpid';$listlabel='subjectcomponent';$onchange='yes';$multi=1;
		include('scripts/set_list_vars.php');
		list_select_list($components,$listoptions,$book);
		}
	else{
		$selpid='';
		}
?>
		  </th>
		</tr>
<?php
	$rown=1;
	reset($students);
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		$Student=fetchStudent_short($sid);
		$Assessments=(array)fetchAssessments_short($sid,$eid,$selbid,$selpid);
?>
		  <tr id="sid-<?php print $sid;?>">
			<td><?php print $rown++;?></td>
			<td>
			<a href="infobook.php?current=student_scores.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
			<?php print $Student['DisplayFullName']['value']; ?></a>
			</td>
		  <td id="edit-<?php print $sid;?>" class="edit">

<?php 
		if(sizeof($Assessments)>0){$value=$Assessments[0]['Value']['value'];}
		else{$value='';}
		if($grading_grades!='' and $grading_grades!=' '){
?>
		  <select tabindex='<?php print $tab++;?>' name='<?php print $sid;?>'>
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
				  name="<?php print $sid;?>" value="<?php print $value;?>"/>
<?php
			}
?>

		  </td>
<?php
		}
?>
		</tr>
		</table>


	  <input type="hidden" name="bid" value="<?php print $selbid; ?>"/>
	  <input type="hidden" name="pid" value="<?php print $selpid; ?>"/>
	  <input type="hidden" name="eid" value="<?php print $eid; ?>"/>
	  <input type="hidden" name="cancel" value="<?php print $choice;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>
