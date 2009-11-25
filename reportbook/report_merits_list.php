<?php
/**									report_merits_list.php
 *
 *
 *	Produce a lists students with merits awarded in the given time frame.
 *
 */

$action='report_merits.php';

$startdate=$_POST['date0'];
$enddate=$_POST['date1'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='%';}
if($bid==''){$bid='%';}
if(isset($_POST['activity'])){$activity=$_POST['activity'];}else{$activity='%';}
if($activity==''){$activity='%';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['newyid'])){$yid=$_POST['newyid'];}else{$yid='';}
if(isset($_POST['newfid'])){$fid=$_POST['newfid'];}else{$fid='';}
list($ratingnames,$catdefs)=fetch_categorydefs('mer');
$curryear=get_curriculumyear();


include('scripts/sub_action.php');

	if($yid!=''){
		$d_m=mysql_query("SELECT * FROM merits JOIN
			student ON student.id=merits.student_id WHERE
			merits.date>='$startdate' AND merits.date<='$enddate' 
			AND student.yeargroup_id LIKE '$yid' AND merits.subject_id LIKE '$bid'  
			AND merits.activity LIKE '$activity' AND merits.year='$curryear' ORDER BY student.surname ;");
		}
	elseif($fid!=''){
		$d_m=mysql_query("SELECT * FROM merits JOIN
			student ON student.id=merits.student_id WHERE
			merits.date >= '$startdate' AND
			merits.date<='$enddate' AND student.form_id LIKE
			'$fid' AND merits.subject_id LIKE '$bid' 
			AND merits.activity LIKE '$activity' AND merits.year='$curryear' ORDER BY student.surname;");
		}
	else{
		if($rcrid=='%'){
			/* User has a subject not a course responsibility selected. */
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN cridbid ON cridbid.course_id=cohort.course_id WHERE
				cridbid.subject_id='$rbid' AND cohort.stage='$stage' AND cohort.year='$year';");
			$rcrid=mysql_result($d_course,0);
			}
		$d_community=mysql_query("SELECT community_id FROM cohidcomid JOIN
				cohort ON cohidcomid.cohort_id=cohort.id WHERE
			    cohort.stage='$stage' AND cohort.year='$year' AND
				cohort.course_id='$rcrid' LIMIT 1;");
		$comid=mysql_result($d_community,0);
		$d_m=mysql_query("SELECT * FROM merits JOIN
				comidsid ON comidsid.student_id=merits.student_id
				WHERE merits.date >= '$startdate' AND
				merits.date<='$enddate' AND
				merits.subject_id LIKE '$bid' AND merits.activity LIKE '$activity' 
				AND merits.year='$curryear' AND comidsid.community_id='$comid';");
		}

	if(mysql_num_rows($d_m)==0){
		$error[]=get_string('nomeritsfound',$book);
		$action='report_merits.php';
    	include('scripts/results.php');
	    include('scripts/redirect.php');
		exit;
		}

	$summarys=array();
	$sids=array();
	$range=0;
	while($merit=mysql_fetch_array($d_m,MYSQL_ASSOC)){
		$sid=$merit['student_id'];
		if(!in_array($sid,$sids)){
			$sids[]=$sid;
			$summary=array('sid'=>$sid);
			}
		else{
			$summary=$summarys[$sid];
			}
		$cat=$merit['activity'];
		$value=$merit['value'];
		if(isset($summary[$cat]['value'])){$summary[$cat]['value']+=$value;}
		else{$summary[$cat]['value']=$value;}
		if(isset($summary['total'])){$summary['total']+=$value;}
		else{
			$summary['total']=$value;
			}
		if($summary['total']>$range){$range=$summary['total'];}
		$summarys[$sid]=$summary;
		}

$sort_array[0]['name']="total";
$sort_array[0]['sort']='DESC';
$sort_array[0]['case']=FALSE;
sortx($summarys, $sort_array);
$range=$range*0.4;

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'value'=>'report_merits_print.php',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons,$book);
?>
<div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <div id="xml-checked-action" style="display:none;">
		<period>
		  <startdate><?php print $startdate;?></startdate>
		  <enddate><?php print $enddate;?></enddate>
		</period>
	  </div>

	  <table class="listmenu sidtable">
		<tr id="sid-<?php print $sid;?>">
		  <th colspan="1">
			<label id="checkall"><?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll	(this);" />
			</label>
		  </th>
		  <th colspan="2" style="width:30%;"><?php print_string('student');?></th>
		  <th>
<?php 
		print get_string('sum').'<br />'. '('. 
			display_date($startdate).')';
?>
		  </th>
		  <th>
			<?php print get_string('total').'<br />'.get_string('year');?>
		  </th>
<?php
		reset($catdefs);
		while(list($catid,$catdef)=each($catdefs)){
			print '<th>'.$catdef['name'].'</th>';
			}
?>
		</tr>
<?php
	$rown=0;
	while(list($index,$summary)=each($summarys)){
		$rown++;
		$sid=$summary['sid'];
		$Student=fetchStudent_short($sid);
		$house=get_student_house($sid);
		$Merits=fetchMerits($sid,1,$bid,'%',$curryear);
?>
		<tr>
		  <td>
			<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
			<?php print $rown;?>
		  </td>
		  <td>
		<?php print $house;?>
		  </td>
		  <td class="student">
			<a href="infobook.php?current=merits_list.php&sid=<?php
			  print $sid;?>&sids[]=<?php print $sid;?>"  target="viewinfobook"
			  onclick="parent.viewBook('infobook');"> 
			  <?php print $Student['DisplayFullSurname']['value'] .' ('.$Student['RegistrationGroup']['value'].')'; ?>
			</a>
		  </td>
		  <td>
			<?php print $summary['total'];?>
		  </td>
		  <td>
			<?php print $Merits['Total']['Sum']['value'];?>
		  </td>
<?php
		reset($catdefs);
		while(list($catid,$catdef)=each($catdefs)){
			if(!isset($summary[$catid]['value'])){
				$colourclass='';$summary[$catid]['count']='';
				$summary[$catid]['value']='';
				}
			elseif($summary[$catid]['value']==0){$colourclass='nolite';}
			elseif($summary[$catid]['value']<-($range)){$colourclass='hilite';}
			elseif($summary[$catid]['value']<0){$colourclass='midlite';}
			elseif($summary[$catid]['value']>$range){$colourclass='golite';}
			elseif($summary[$catid]['value']>0){$colourclass='gomidlite';}
			print '<td class="'.$colourclass.'">&nbsp;'. 
					$summary[$catid]['value'].'</td>';
			}
?>
		</tr>
<?php	
		}
?>
	  </table>

	</fieldset>

 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>
