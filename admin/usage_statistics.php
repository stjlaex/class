<?php
/**								usage_statistics.php
 *	a simple log counter
 */

$choice='usage.php';
$action='usage.php';

include('scripts/sub_action.php');

$extrabuttons['usagestatistics']=array('name'=>'current','value'=>'usage_statistics.php');
two_buttonmenu($extrabuttons);

$year=get_curriculumyear();
$time=mktime(0,0,0,8,0,$year-1);
$date=date('Y-m-d',$time);

?>
  <div id="heading">
	<label><?php print_string(''); ?></label>
	<?php print 'Usage statistics since '.$date;?>
  </div>

  <div id="viewcontent" class="content">

	<fieldset class="center">
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
		if($count>0){$ave=round($sum/$count);}
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


	<fieldset class="center">
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
		  <td><?php print $totalrequests;?></td>
		</tr>
		<tr>
		  <th><?php print_string('uniquepagesserved',$book);?></th>
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



	<fieldset class="center">
	  <legend><?php print_string('reportstoparents',$book);?></legend>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('course',$book);?></th>
		  <th><?php print_string('nameofreport',$book);?></th>
		  <th><?php print_string('date',$book);?></th>
		  <th><?php print_string('numberofwrittensubjectreports',$book);?></th>
		</tr>
<?php
	$d_c=mysql_query("SELECT id, name FROM course ORDER BY sequence");
	$tot1=0;
	$tot2=0;
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
		  <td colspan="2">&nbsp</td>
		</tr>
<?php
		while($report=mysql_fetch_array($d_r,MYSQL_ASSOC)){
			$rid=$report['id'];
			$d_u=mysql_query("SELECT COUNT(*) FROM
							reportentry WHERE report_id='$rid'");
			$nocomments=mysql_result($d_u,0);
			$tot2+=$nocomments;
?>
		<tr>
		  <td></td>
		  <td><?php print $report['title'];?></td>
		  <td><?php print $report['deadline'];?></td>
		  <td><?php print $nocomments;?></td>
		</tr>
<?php
			}
		}
?>
		<tr>
		  <td><?php print_string('total',$book);?><?php print ': '.$tot1;?></td>
		  <td>&nbsp</td>
		  <td>&nbsp</td>
		  <td><?php print $tot2;?></td>
		</tr>
	  </table>
	</fieldset>

	<fieldset class="center">
	  <legend><?php print_string('assessmentscores',$book);?></legend>
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
		$tot1+=$no;
		$nosids=0;
		$stages=list_course_stages($crid);
		while(list($index,$stage)=each($stages)){
			$sids=listin_cohort(array('id'=>'','course_id'=>$crid,'stage'=>$stage));
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
	  </table>
	</fieldset>


 	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>
