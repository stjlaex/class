<?php
/**									report_incidents_list.php
 *
 *	Finds and lists students identified as having incidents.
 */

$action='report_incidents.php';

$startdate=$_POST['date0'];
$enddate=$_POST['date1'];
if(isset($_POST['bid']) and $_POST['bid']!=''){$bid=$_POST['bid'];}else{$bid='%';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid']) and $_POST['formid']!=''){$comid=$_POST['formid'];}
elseif(isset($_POST['houseid'])  and $_POST['houseid']!=''){$comid=$_POST['houseid'];}else{$comid='';}

include('scripts/sub_action.php');

	if($comid!=''){
		if($yid!=''){
			$d_incidents=mysql_query("SELECT * FROM incidents WHERE
							incidents.entrydate >= '$startdate' AND incidents.entrydate<='$enddate' 
							AND incidents.subject_id LIKE '$bid' 
							AND incidents.student_id=ANY(SELECT student.id FROM student JOIN comidsid AS a ON comidsid.student_id=student.id
							WHERE student.yeargroup_id='$yid' a.community_id='$comid' 
							AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL));");
			}
		else{
			$d_incidents=mysql_query("SELECT * FROM incidents JOIN
					comidsid AS a ON a.student_id=incidents.student_id WHERE
					a.community_id='$comid' AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL)
					AND incidents.entrydate >= '$startdate' AND incidents.entrydate<='$enddate' 
					AND incidents.subject_id LIKE '$bid';");
			}
		}
	elseif($yid!=''){
		$d_incidents=mysql_query("SELECT * FROM incidents JOIN
		student ON student.id=incidents.student_id WHERE
		incidents.entrydate > '$startdate' AND student.yeargroup_id LIKE
		'$yid' ORDER BY student.surname");
		}
	elseif($bid!=''){
		$d_incidents=mysql_query("SELECT * FROM incidents WHERE entrydate > '$startdate' AND subject_id LIKE '$bid'");
		}
	else{
		if($rcrid=='%'){
			/*User has a subject not a course responsibility selected*/
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN component ON component.course_id=cohort.course_id WHERE
				component.subject_id='$rbid' AND component.id='' AND cohort.stage='$stage' AND cohort.year='$year'");
			$rcrid=mysql_result($d_course,0);
			}

		$d_community=mysql_query("SELECT community_id FROM cohidcomid JOIN
				cohort ON cohidcomid.cohort_id=cohort.id WHERE
			    cohort.stage='$stage' AND cohort.year='$year' AND
				cohort.course_id='$rcrid' LIMIT 1");
		$comid=mysql_result($d_community,0);
		$d_incidents=mysql_query("SELECT * FROM incidents JOIN
				comidsid ON comidsid.student_id=incidents.student_id
				WHERE incidents.entrydate > '$startdate' AND comidsid.community_id='$comid'");
		}

	if(mysql_num_rows($d_incidents)==0){
		$error[]=get_string('nonefound',$book);
		$action='report_incidents.php';
    	include('scripts/results.php');
	    include('scripts/redirect.php');
		exit;
		}

	$summary=array();
	$sids=array();
	while($incident=mysql_fetch_array($d_incidents,MYSQL_ASSOC)){
		$sid=$incident['student_id'];
		if($incident['subject_id']=='%'){$incident['subject_id']='G';}
		$bids=array();
		$closeds=array();
		if(in_array($sid,$sids)){
			$bids=$summary[$sid]['bids'];
			$closeds=$summary[$sid]['closeds'];
			$bids[]=$incident['subject_id'];
			if($incident['closed']=='N'){$closeds[]=$incident['closed'];}
			}
		else{
			$sids[]=$sid;
			$bids[]=$incident['subject_id'];
			if($incident['closed']=='N'){$closeds[]=$incident['closed'];}
			}
		$summary[$sid]['bids']=$bids;
		$summary[$sid]['closeds']=$closeds;
		}

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'value'=>'report_incidents_print.php',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons,$book);
?>

<div id="viewcontent" class="content">

	  <div id="xml-checked-action" style="display:none;">
		<period>
		  <startdate><?php print $startdate;?></startdate>
		  <enddate><?php print $enddate;?></enddate>
		</period>
	  </div>

<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
	  <table class="listmenu sidtable">
		<tr>
		  <th>
			<label id="checkall"><?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll	(this);" />
			</label>
		  </th>
		  <th colspan="2"><?php print_string('student');?></th>
		  <th><?php print_string('formgroup');?></th>
		  <th><?php print_string('areasforincidents',$book);?></th>
		</tr>
<?php
	while(list($index,$sid)=each($sids)){
		$Student=fetchStudent_short($sid);
		if(sizeof($summary[$sid]['closeds'])>1){$styleclass=' class="hilite"';}
		elseif(sizeof($summary[$sid]['closeds'])>0){$styleclass=' class="midlite"';}
		else{$styleclass='';}
?>
		<tr <?php print $styleclass;?>>
		  <td>
			<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
		  </td>
		  <td>&nbsp;</td>
		  <td>
			<a href="infobook.php?current=incidents_list.php&sid=<?php
			  print $sid;?>&sids[]=<?php print $sid;?>"  target="viewinfobook"
			  onclick="parent.viewBook('infobook');"> 
			  <?php print $Student['DisplayFullName']['value']; ?>
			</a>
		  </td>
		  <td>
			<?php print $Student['RegistrationGroup']['value']; ?>
		  </td>
		  <td>
<?php
		for($c=0;$c<sizeof($summary[$sid]['bids']);$c++){
			print $summary[$sid]['bids'][$c].'&nbsp;';
			}
?>
		  </td>
		</tr>
<?php	
		}
	reset($sids);
?>
	  </table>
	</fieldset>

 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>
