<?php
/**									report_comments_list.php
 *
 *	Finds and lists students identified as having concerns.
 */

$action='report_comments.php';

$startdate=$_POST['date0'];
$enddate=$_POST['date1'];
if(isset($_POST['bid']) and $_POST['bid']!=''){$bid=$_POST['bid'];}else{$bid='%';}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='%';}
if($ratvalue==''){$ratvalue='%';}
else{$ratvalue='%:'.$ratvalue.';%';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid']) and $_POST['formid']!=''){$comid=$_POST['formid'];}
elseif(isset($_POST['houseid'])  and $_POST['houseid']!=''){$comid=$_POST['houseid'];}else{$comid='';}
list($ratingnames,$catdefs)=fetch_categorydefs('con');

include('scripts/sub_action.php');

	if($comid!=''){
		if($yid!=''){
			$d_comments=mysql_query("SELECT * FROM comments WHERE
							comments.entrydate >= '$startdate' AND comments.entrydate<='$enddate' 
							AND comments.subject_id LIKE '$bid' AND comments.category LIKE '$ratvalue'
							AND comments.student_id=ANY(SELECT student.id FROM student JOIN comidsid AS a ON comidsid.student_id=student.id
							WHERE student.yeargroup_id='$yid' AND a.community_id='$comid' 
							AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL));");
			}
		else{
			$d_comments=mysql_query("SELECT * FROM comments JOIN
					comidsid AS a ON a.student_id=comments.student_id WHERE
					a.community_id='$comid' AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL)
					AND comments.entrydate >= '$startdate' AND comments.entrydate<='$enddate' 
							AND comments.subject_id LIKE '$bid' AND comments.category LIKE '$ratvalue';");
			}
		}
	elseif($yid!=''){
		$d_comments=mysql_query("SELECT * FROM comments JOIN
			student ON student.id=comments.student_id WHERE comments.entrydate >= '$startdate' AND
			comments.entrydate<='$enddate' AND student.yeargroup_id='$yid' AND comments.subject_id LIKE '$bid' 
			AND comments.category LIKE '$ratvalue' ORDER BY student.surname;");
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
				cohort.course_id='$rcrid' LIMIT 1;");
		$comid=mysql_result($d_community,0);
		$d_comments=mysql_query("SELECT * FROM comments JOIN
				comidsid ON comidsid.student_id=comments.student_id
				WHERE comments.entrydate >= '$startdate' AND
				comments.entrydate<='$enddate' AND
				comments.subject_id LIKE '$bid' AND comments.category LIKE '$ratvalue' 
				AND comidsid.community_id='$comid';");
		}

	if(mysql_num_rows($d_comments)==0){
		$error[]=get_string('nonefound',$book);
		$action='report_comments.php';
    	include('scripts/results.php');
	    include('scripts/redirect.php');
		exit;
		}

	$summarys=array();
	$sids=array();
	while($comment=mysql_fetch_array($d_comments,MYSQL_ASSOC)){
		$sid=$comment['student_id'];
		if($comment['subject_id']=='%'){$comment['subject_id']='G';}
		if(!in_array($sid,$sids)){
			$sids[]=$sid;
			$summary=array();
			}
		else{
			$summary=$summarys[$sid];
			}
		$pairs=explode(';',$comment['category']);
		while(list($pairindex,$pair)=each($pairs)){
			list($cat,$value)=explode(':',$pair);
			if(isset($summary[$cat]['value'])){$summary[$cat]['value']+=$value;}
			else{$summary[$cat]['value']=$value;}
			if(isset($summary[$cat]['count'])){$summary[$cat]['count']++;}
			else{$summary[$cat]['count']=1;}
			$summarys[$sid]=$summary;
			}
		}

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'value'=>'report_comments_print.php',
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
		<tr>
		  <th>
			<label id="checkall"><?php print_string('checkall');?>
			  <input type="checkbox" name="checkall" value="yes" onChange="checkAll	(this);" />
			</label>
		  </th>
		  <th colspan="2"><?php print_string('student');?></th>
		  <th class="smalltable"><?php print_string('formgroup');?></th>
<?php
		reset($catdefs);
		while(list($catid,$catdef)=each($catdefs)){
			print '<th class="smalltable">'.$catdef['name'].'</th>';
			}
?>
		</tr>
<?php
	while(list($index,$sid)=each($sids)){
		$Student=fetchStudent_short($sid);
?>
		<tr>
		  <td>
			<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
		  </td>
		  <td>&nbsp;
		  </td>
		  <td>
			<a href="infobook.php?current=comments_list.php&sid=<?php
			  print $sid;?>&sids[]=<?php print $sid;?>"  target="viewinfobook"
			  onclick="parent.viewBook('infobook');"> 
			  <?php print $Student['DisplayFullName']['value']; ?>
			</a>
		  </td>
		  <td>
			<?php print $Student['RegistrationGroup']['value']; ?>
		  </td>
<?php
		$summary=$summarys[$sid];
		reset($catdefs);
		while(list($catid,$catdef)=each($catdefs)){
			if(!isset($summary[$catid]['value'])){$colourclass='';$summary[$catid]['count']='';}
			elseif($summary[$catid]['value']==0){$colourclass='nolite';}
			elseif($summary[$catid]['value']<-1){$colourclass='hilite';}
			elseif($summary[$catid]['value']<0){$colourclass='midlite';}
			elseif($summary[$catid]['value']>1){$colourclass='golite';}
			elseif($summary[$catid]['value']>0){$colourclass='gomidlite';}
			print '<td class="'.$colourclass.'">&nbsp;'. 
					$summary[$catid]['count'].'</td>';
			}
?>
		</tr>
<?php	
		}
	reset($sids);
?>
	  </table>

	</fieldset>

 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
	</form>
  </div>
