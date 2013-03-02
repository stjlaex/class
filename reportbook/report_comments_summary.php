<?php
/**								report_comments_summary.php
 *
 *
 */

$choice='report_comments.php';
$action='report_comments.php';

include('scripts/sub_action.php');

two_buttonmenu();

$year=get_curriculumyear();
$time=mktime(0,0,0,8,0,$year-1);
$date=date('Y-m-d',$time);
$todate=date('Y-m-d');

?>

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
<?php
		$d_h=mysql_query("SELECT COUNT(*) FROM comments WHERE entrydate>'$date'");
		$nocoms=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*) FROM incidents WHERE entrydate>'$date'");
		$noins=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*) FROM background WHERE entrydate>'$date'");
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
		  <th><?php print_string('numberofstudents','admin');?></th>
		  <th><?php print_string('averageperstudent','admin');?></th>
		</tr>
<?php
    $yeargroups=(array)list_yeargroups();
	foreach($yeargroups as $yeargroup){
		$yid=$yeargroup['id'];
		$nosids=countin_community(array('type'=>'year','name'=>$yid));
		$d_h=mysql_query("SELECT COUNT(*) FROM comments WHERE entrydate>'$date' AND yeargroup_id='$yid' AND eidsid_id='0'");
		$nocoms=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*) FROM incidents WHERE entrydate>'$date' AND yeargroup_id='$yid'");
		$noins=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*) FROM background WHERE entrydate>'$date' AND yeargroup_id='$yid'");
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

<br />

	  <table class="listmenu">
		<tr>
		  <th><?php print_string('month','register');?></th>
		  <th colspan="3" style="text-align:center;"><?php print_string('comments',$book);?></th>
		</tr>
		<tr>
		  <th></th>
		  <th class="negative">-ve</th>
		  <th></th>
		  <th class="positive">+ve</th>
		</tr>
<?php



	$time=mktime(0,0,0,8,0,$year-1);
	$date1=date('Y-m-d',$time);
	for($month=1;$month<12;$month++){

		$date0=$date1;
		$time=mktime(0,0,0,8+$month,0,$year-1);
		$monthdisplay=date('M',$time);
		$date1=date('Y-m-d',$time);

		$d_h=mysql_query("SELECT COUNT(*) FROM comments WHERE entrydate>'$date0' AND entrydate <'$date1' AND category LIKE '%:-1;' AND eidsid_id='0';");
		$non=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*) FROM comments WHERE entrydate>'$date0' AND entrydate <'$date1' AND category LIKE '%:0;'  AND eidsid_id='0';");
		$no=mysql_result($d_h,0);
		$d_h=mysql_query("SELECT COUNT(*) FROM comments WHERE entrydate>'$date0' AND entrydate <'$date1' AND category LIKE '%:1;'  AND eidsid_id='0';");
		$nop=mysql_result($d_h,0);
?>
		<tr>
		  <td><?php print $monthdisplay;?></td>
		  <td><?php print $non;?></td>
		  <td><?php print $no;?></td>
		  <td><?php print $nop;?></td>
		</tr>
<?php
		}
?>
		</table>


	</fieldset>




	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>
