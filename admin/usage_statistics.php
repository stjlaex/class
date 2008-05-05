<?php
/**								usage_statistics.php
 *
 *
 */

$choice='usage.php';
$action='usage.php';

include('scripts/sub_action.php');

$extrabuttons['usagestatistics']=array('name'=>'current','value'=>'usage_statistics.php');
two_buttonmenu($extrabuttons,$book);

$year=get_curriculumyear();
$time=mktime(0,0,0,8,0,$year-1);
$date=date('Y-m-d',$time);
$todate=date('Y-m-d');

?>
  <div id="heading">
	<label><?php print_string(''); ?></label>
	<?php print 'Usage statistics since '.display_date($date);?>
  </div>

  <div id="viewcontent" class="content">

  <fieldset class="right divgroup">
<?php 
	print 'Statistics for '.$CFG->sitename;
	if(isset($CFG->client)){
		print '  Client ID '.$CFG->client;
		}
?>
	  <br />
	  <?php print 'Between '.display_date($date) .' and '.display_date($todate);?>
  </fieldset>

	<fieldset class="center divgroup">
	  <legend><?php print_string('userlogins',$book);?></legend>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('role',$book);?></th>
		  <th><?php print_string('numberofactiveusers',$book);?></th>
		  <th><?php print_string('numberoflogins',$book);?></th>
		  <th><?php print_string('averageperuser',$book);?></th>
		</tr>
<?php

	$roles=$CFG->roles;
	$tot1=0;
	$tot2=0;
	while(list($index,$role)=each($roles)){
		$d_u=mysql_query("SELECT COUNT(uid) FROM users WHERE
						nologin='0' AND role='$role'");
		$count=mysql_result($d_u,0);
		$d_u=mysql_query("SELECT SUM(logcount) FROM users WHERE
						nologin='0' AND role='$role'");
		$sum=mysql_result($d_u,0);
		if($count>0){$ave=round($sum/$count);}else{$ave=0;}
		$tot1+=$count;
		$tot2+=$sum;

?>
		<tr>
		  <td><?php print_string($role,$book);?></td>
		  <td><?php print $count;?></td>
		  <td><?php print $sum;?></td>
		  <td><?php print $ave;?></td>
		</tr>
<?php
		}
	$ave=round($tot2/$tot1);
?>
		<tr>
		  <th><?php print_string('total',$book);?></th>
		  <td><?php print $tot1;?></td>
		  <td><?php print $tot2;?></td>
		  <td><?php print $ave;?></td>
		</tr>
	  </table>
  </fieldset>

	<fieldset class="center divgroup">
	  <legend><?php print_string('pagesaccessed',$book);?></legend>
<?php
	$tot1=0;
	$tot2=0;
	$d_h=mysql_query("SELECT COUNT(*)
		FROM history WHERE UNIX_TIMESTAMP(time)>'$time'");
	$totalrequests=mysql_result($d_h,0);
	$d_h=mysql_query("SELECT DISTINCT page
		FROM history WHERE UNIX_TIMESTAMP(time)>'$time'");
	$totalpages=mysql_num_rows($d_h);
?>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('totalnumberofpagesserved',$book);?></th>
		  <th><?php print_string('uniquepagesserved',$book);?></th>
		</tr>
		<tr>
		  <td><?php print $totalrequests;?></td>
		  <td><?php print $totalpages;?></td>
		</tr>
	  </table>

<br />

	  <table class="listmenu">
		<tr>
		  <th><?php print_string('toptwentypages',$book);?></th>
		  <th><?php print_string('numberofrequests',$book);?></th>
		  <th><?php print_string('numberofusers',$book);?></th>
		  <th><?php print_string('averageperuser',$book);?></th>
		</tr>
<?php
	$toppages=array();
	$topsums=array();
	$index=0;
	while($page=mysql_fetch_array($d_h,MYSQL_ASSOC)){
		$pagename=$page['page'];
		$d_page=mysql_query("SELECT COUNT(*)
			FROM history WHERE UNIX_TIMESTAMP(time)>$time AND page='$pagename'");
		$sum=mysql_result($d_page,0);
		$d_page=mysql_query("SELECT COUNT(DISTINCT uid)
			FROM history WHERE UNIX_TIMESTAMP(time)>$time AND page='$pagename'");
		$count=mysql_result($d_page,0);
		$toppages[$index]=array('sum'=>$sum,'count'=>$count,'name'=>$pagename);
		$topsums[$sum]=$index;
		$index++;
		}

    ksort($topsums);
	for($c=0;$c<20;$c++){
		$index=array_pop($topsums);
		$page=$toppages[$index];
		if($page['count']>0){$ave=round($page['sum']/$page['count']);}else{$ave='';}
?>
		<tr>
		  <td><?php print $page['name'];?></td>
		  <td><?php print $page['sum'];?></td>
		  <td><?php print $page['count'];?></td>
		  <td><?php print $ave;?></td>
		</tr>
<?php
		}
?>
	  </table>
	</fieldset>


	<fieldset class="center divgroup">
	  <legend><?php print_string('register','register');?></legend>

	  <table class="listmenu">
		<tr>
		  <th><?php print_string('yeargroup',$book);?></th>
		  <th><?php print_string('registrationsession','register');?></th>
		  <th><?php print get_string('absences','register'). 
					' '.get_string('noreasonyetprovided','register');?></th>
		</tr>
<?php
	$d_y=mysql_query("SELECT id, name FROM yeargroup ORDER BY sequence");
	while($yeargroup=mysql_fetch_array($d_y,MYSQL_ASSOC)){
		$yid=$yeargroup['id'];
		$d_e=mysql_query("SELECT COUNT(DISTINCT event_id)
						FROM attendance JOIN student ON
						student.id=attendance.student_id WHERE student.yeargroup_id='$yid'
						AND attendance.logtime>'$date';");
		$nosess=mysql_result($d_e,0);
		$d_e=mysql_query("SELECT COUNT(DISTINCT (CONCAT(event_id, student_id)))
						FROM attendance JOIN student ON
						student.id=attendance.student_id WHERE student.yeargroup_id='$yid'
						AND attendance.logtime>'$date' AND
						attendance.status='a' AND attendance.code='O';");
		$noabs=mysql_result($d_e,0);
?>
		<tr>
		  <td><?php print $yeargroup['name'];?></td>
		  <td><?php print $nosess;?></td>
		  <td><?php print $noabs;?></td>
		</tr>
<?php
		}
?>
		</table>
	</fieldset>


	<fieldset class="center divgroup">
	  <legend><?php print_string('reportstoparents',$book);?></legend>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('course',$book);?></th>
		  <th><?php print_string('report',$book);?></th>
		  <th><?php print_string('date',$book);?></th>
		  <th><?php print_string('numberofreports',$book);?></th>
		  <th><?php print_string('numberofwrittensubjectcomments',$book);?></th>
		</tr>
<?php
	$d_c=mysql_query("SELECT id, name FROM course ORDER BY sequence");
	$tot1=0;
	$tot2=0;
	$tot3=0;
	while($course=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$crid=$course['id'];
		$d_r=mysql_query("SELECT id, title, deadline FROM report WHERE
					course_id='$crid' AND deadline>'$date' ORDER BY deadline");
		$noreports=mysql_num_rows($d_r);
		$tot1+=$noreports;
?>
		<tr>
		  <td><?php print $crid.': '.$noreports.' reports';?></td>
		  <td>&nbsp</td>
		  <td colspan="3">&nbsp</td>
		</tr>
<?php
		while($report=mysql_fetch_array($d_r,MYSQL_ASSOC)){
			$rid=$report['id'];
			$d_e=mysql_query("SELECT COUNT(DISTINCT student_id) FROM
							eidsid JOIN rideid ON
							rideid.assessment_id=eidsid.assessment_id 
							WHERE rideid.report_id='$rid'");
			$nosids=mysql_result($d_e,0);
			$tot3+=$nosids;
			$d_u=mysql_query("SELECT COUNT(*) FROM
							reportentry WHERE report_id='$rid'");
			$nocomments=mysql_result($d_u,0);
			$tot2+=$nocomments;
?>
		<tr>
		  <td></td>
		  <td><?php print $report['title'];?></td>
		  <td><?php print $report['deadline'];?></td>
		  <td><?php print $nosids;?></td>
		  <td><?php print $nocomments;?></td>
		</tr>
<?php
			}
		}
?>
		<tr>
		  <th><?php print_string('total',$book);?></th>
		  <td><?php print 'reports: '.$tot1;?></td>
		  <td>&nbsp</td>
		  <td><?php print $tot3;?></td>
		  <td><?php print $tot2;?></td>
		</tr>
	  </table>
	</fieldset>

	<fieldset class="center divgroup">
	  <legend><?php print_string('assessmentandtrackingscores',$book);?></legend>

	  <table class="listmenu">
		<tr>
		  <th><?php print_string('course',$book);?></th>
		  <th><?php print_string('numberofscoresrecorded',$book);?></th>
		  <th><?php print_string('averageperstudent',$book);?></th>
		</tr>
<?php

	$d_c=mysql_query("SELECT id, name FROM course ORDER BY sequence");
	$tot1=0;
	$tot2=0;
	while($course=mysql_fetch_array($d_c,MYSQL_ASSOC)){
		$noscores=0;
		$crid=$course['id'];
		$d_a=mysql_query("SELECT COUNT(*) FROM eidsid JOIN
					assessment ON assessment.id=eidsid.assessment_id WHERE
					assessment.course_id='$crid' AND assessment.deadline>'$date'");
		$noscores=mysql_result($d_a,0);
		$tot1+=$noscores;
		$nosids=0;
		$stages=(array)list_course_stages($crid);
		while(list($index,$stage)=each($stages)){
			$sids=listin_cohort(array('id'=>'','course_id'=>$crid,'stage'=>$stage['id']));
			$nosids+=sizeof($sids);
			}
		if($nosids>0){$ave=round($noscores/$nosids);}else{$ave=0;}
?>
		<tr>
		  <td><?php print $crid;?></td>
		  <td><?php print $noscores;?></td>
		  <td><?php print $ave;?></td>
		</tr>
<?php
		}
?>
		<tr>
		  <th><?php print_string('total',$book);?></th>
		  <td><?php print $tot1;?></td>
		  <td>&nbsp</td>
		</tr>
	  </table>
	</fieldset>



	<fieldset class="center divgroup">
	  <legend><?php print_string('pastoralandacademicmonitoring',$book);?></legend>
<?php
		$d_h=mysql_query("SELECT COUNT(*)
				FROM comments WHERE entrydate>'$date'");
		$nocoms=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*)
				FROM incidents WHERE entrydate>'$date'");
		$noins=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*)
				FROM background WHERE entrydate>'$date'");
		$noents=mysql_result($d_h,0);
?>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('comments',$book);?></th>
		  <th><?php print_string('incidents',$book);?></th>
		  <th><?php print_string('other',$book);?></th>
		</tr>
		<tr>
		  <td><?php print $nocoms;?></td>
		  <td><?php print $noins;?></td>
		  <td><?php print $noents;?></td>
		</tr>
	  </table>

<br />

	  <table class="listmenu">
		<tr>
		  <th><?php print_string('yeargroup',$book);?></th>
		  <th><?php print_string('comments',$book);?></th>
		  <th><?php print_string('incidents',$book);?></th>
		  <th><?php print_string('other',$book);?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		  <th><?php print_string('averageperstudent',$book);?></th>
		</tr>
<?php
	$d_y=mysql_query("SELECT id, name FROM yeargroup ORDER BY sequence");
	while($yeargroup=mysql_fetch_array($d_y,MYSQL_ASSOC)){
		$yid=$yeargroup['id'];
		$nosids=countin_community(array('type'=>'year','name'=>$yid));
		$d_h=mysql_query("SELECT COUNT(*)
			FROM comments WHERE entrydate>'$date' AND yeargroup_id='$yid'");
		$nocoms=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*)
			FROM incidents WHERE entrydate>'$date' AND yeargroup_id='$yid'");
		$noins=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*)
			FROM background WHERE entrydate>'$date' AND yeargroup_id='$yid'");
		$noents=mysql_result($d_h,0);
		if($nosids>0){$ave=round(($noents+$noins+$nocoms)/$nosids);}else{$ave=0;}

?>
		<tr>
		  <td><?php print $yeargroup['name'];?></td>
		  <td><?php print $nocoms;?></td>
		  <td><?php print $noins;?></td>
		  <td><?php print $noents;?></td>
		  <td><?php print $nosids;?></td>
		  <td><?php print $ave;?></td>
		</tr>
<?php
		}
?>
		</table>
	</fieldset>


	<fieldset class="center divgroup">
	  <legend><?php print_string('specialneedsandsupport',$book);?></legend>
<?php
		$d_h=mysql_query("SELECT COUNT(*) 
				FROM senhistory WHERE reviewdate>'$date' OR reviewdate IS NULL");
		$noieps=mysql_result($d_h,0);

		$d_h=mysql_query("SELECT COUNT(*) FROM sencurriculum 
				JOIN senhistory ON senhistory.id=sencurriculum.senhistory_id
				WHERE (senhistory.reviewdate>'$date' OR senhistory.reviewdate IS NULL)
				AND sencurriculum.categorydef_id!='0'");
		$nosups=mysql_result($d_h,0);
?>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('studentswithiep',$book);?></th>
		  <th><?php print_string('receivingextrasupport',$book);?></th>
		</tr>
		<tr>
		  <td><?php print $noieps;?></td>
		  <td><?php print $nosups;?></td>
		</tr>
	  </table>
	</fieldset>

	<fieldset class="center divgroup">
	  <legend><?php print_string('eportfolios',$book);?></legend>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('role',$book);?></th>
		  <th><?php print_string('numberofactiveusers',$book);?></th>
		  <th><?php print_string('numberoflogins',$book);?></th>
		  <th><?php print_string('averageperuser',$book);?></th>
		</tr>
<?php

	$dbepf='';
	if($CFG->eportfolio_db!=''){
		$dbepf=db_connect($CFG->eportfolio_db);
		mysql_query("SET NAMES 'utf8'");
		}
	if($dbepf!=''){
		$userstable=$CFG->eportfolio_db_prefix.'users';
		$historytable=$CFG->eportfolio_db_prefix.'history';
		$roles=array('contacts'=>'Default_Guardian','students'=>'Default_Student');
		$tot1=0;
		$tot2=0;
		while(list($index,$role)=each($roles)){
			$d_u=mysql_query("SELECT COUNT(ident) FROM $userstable WHERE
						active='yes' AND template_name='$role' AND last_action!='0';");
			$count=mysql_result($d_u,0);
			$d_u=mysql_query("SELECT count(time) FROM $historytable
						JOIN $userstable ON $userstable.ident=$historytable.uid WHERE
						active='yes' AND template_name='$role';");
			$sum=mysql_result($d_u,0);
			if($count>0){$ave=round($sum/$count);}else{$ave=0;}
			$tot1+=$count;
			$tot2+=$sum;
?>
		<tr>
		  <td><?php print_string($index,'infobook');?></td>
		  <td><?php print $count;?></td>
		  <td><?php print $sum;?></td>
		  <td><?php print $ave;?></td>
		</tr>
<?php
			}
		$ave=round($tot2/$tot1);
		}
?>
		<tr>
		  <th><?php print_string('total',$book);?></th>
		  <td><?php print $tot1;?></td>
		  <td><?php print $tot2;?></td>
		  <td><?php print $ave;?></td>
		</tr>
	  </table>
  </fieldset>



	<form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>
