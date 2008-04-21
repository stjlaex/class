s<?php
/**											manage_homework.php
 */

$action='manage_homework.php';
$choice='manage_homework.php';

include('scripts/sub_action.php');

include('scripts/course_respon.php');

two_buttonmenu();

$stages=list_course_stages($rcrid);
$subjects=list_course_subjects($rcrid);
$tomonth=date('n');
$today=date('j');
$toyear=date('Y');
$todate=$toyear.'-'.$tomonth.'-'.$today;
$time=mktime(0,0,0,$tomonth,$today-14,$toyear);
$lastfortnight=date('Y-m-d',$time);
$time=mktime(0,0,0,8,0,$toyear-1);
$lastyear=date('Y-m-d',$time);

$noyearhws=array();
$noweekhws=array();
while(list($index,$stage)=each($stages)){
	$stagid=$stage['id'];
	/*count for the whole academic year*/
	$d_m=mysql_query("SELECT COUNT(mark.id) AS no, midcid.class_id AS cid,
						author AS tid FROM mark JOIN midcid ON
						mark.id=midcid.mark_id WHERE
						midcid.class_id=ANY(SELECT id FROM class WHERE
						course_id='$rcrid' AND stage='$stagid') 
						AND entrydate>'$lastyear' AND
						marktype='hw' GROUP BY midcid.class_id;");
	while($hw=mysql_fetch_array($d_m,MYSQL_ASSOC)){
		if(!isset($noyearhws[$hw['tid']][$stagid])){$noyearhws[$hw['tid']][$stagid]=array();}
		$noyearhws[$hw['tid']][$stagid][]=$hw;
		}
	/*count for the last two weeks*/
	$d_m=mysql_query("SELECT COUNT(mark.id) AS no, author AS tid
						FROM mark JOIN midcid ON mark.id=midcid.mark_id WHERE
						midcid.class_id=ANY(SELECT id FROM class WHERE
						course_id='$rcrid' AND stage='$stagid') 
						AND entrydate>'$lastfortnight' AND
						marktype='hw' GROUP BY midcid.class_id;");
	while($hw=mysql_fetch_array($d_m,MYSQL_ASSOC)){
		if(!isset($noweekhws[$hw['tid']][$stagid])){$noweekhws[$hw['tid']][$stagid]=0;}
		$noweekhws[$hw['tid']][$stagid]+=$hw['no'];
		}
	}

$nosubjecthws=array();
reset($stages);
while(list($index,$stage)=each($stages)){
	$stagid=$stage['id'];
	reset($subjects);
	while(list($index,$subject)=each($subjects)){
		$bid=$subject['id'];
		$d_m=mysql_query("SELECT COUNT(mark.id) AS no
						FROM mark JOIN midcid ON mark.id=midcid.mark_id WHERE
						midcid.class_id=ANY(SELECT id FROM class WHERE
						course_id='$rcrid' AND stage='$stagid' AND subject_id='$bid') 
						AND entrydate>'$lastyear' AND
						marktype='hw' GROUP BY midcid.class_id;");
		while($hw=mysql_fetch_array($d_m,MYSQL_ASSOC)){
			if(!isset($nosubjecthws[$stagid][$bid])){
				$nosubjecthws[$stagid][$bid]=array('year'=>0,'week'=>0);
				}
			$nosubjecthws[$stagid][$bid]['year']+=$hw['no'];
			}
		$d_m=mysql_query("SELECT COUNT(mark.id) AS no
						FROM mark JOIN midcid ON mark.id=midcid.mark_id WHERE
						midcid.class_id=ANY(SELECT id FROM class WHERE
						course_id='$rcrid' AND stage='$stagid' AND subject_id='$bid') 
						AND entrydate>'$lastfortnight' AND
						marktype='hw' GROUP BY midcid.class_id;");
		while($hw=mysql_fetch_array($d_m,MYSQL_ASSOC)){
			$nosubjecthws[$stagid][$bid]['week']+=$hw['no'];
			}
		}
	}
?>
  <div class="content" id="viewcontent">

	<fieldset class="center divgroup">
	  <legend><?php print_string('homework',$book);?></legend>
	  <table class="listmenu">
		<thead>
		  <tr>
			<th><?php print_string('teacher');?></th>

<?php
		reset($stages);
		while(list($index,$stage)=each($stages)){
?>
			<th><?php print $stage['name'];?></th>
<?php
			}
?>
			<th><?php print_string('total');?></th>
			<th><?php print_string('average');?></th>
		  </tr>
		</thead>
<?php
	while(list($tid,$hws)=each($noyearhws)){
		$tot=0;
		$cidno=0;
?>
		<tr>
		  <td><?php print get_teachername($tid);?></td>
<?php
		reset($stages);
		while(list($index,$stage)=each($stages)){
			$stagid=$stage['id'];
			$stagetot=0;
			$stagelist='';
?>
		  <td>
<?php
			if(isset($hws[$stagid])){
				if(!isset($noweekhws[$tid][$stagid])){$weektot=0;}
				else{$weektot=$noweekhws[$tid][$stagid];}
				while(list($index,$hw)=each($hws[$stagid])){
					$tot+=$hw['no'];
					$stagetot+=$hw['no'];
					$cidno++;
					$stagelist.= $hw['cid'].' - '.$hw['no'].' <br />';
					}
?>
			<span title="<?php print $stagelist;?>">
			  <?php print $weektot.'  ( '.$stagetot.' )';?>
			</span>
<?php
				}
?>
		  </td>
<?php
			}
?>
		  <td><?php print $tot;?></td>
		  <td><?php print round($tot/$cidno);?></td>
		</tr>
<?php
		}
?>
	  </table>
	</fieldset>



	<fieldset class="center divgroup">
	  <legend><?php print get_string('subject').' '.get_string('homework');?></legend>
	  <table class="listmenu">
		<thead>
		  <tr>
			<th style="width:40%;"><?php print_string('subject',$book);?></th>

<?php
		reset($stages);
		while(list($index,$stage)=each($stages)){
?>
			<th><?php print $stage['name'];?></th>
<?php
			}
?>
		  </tr>
		</thead>
<?php
	reset($subjects);
	while(list($index,$subject)=each($subjects)){
		$bid=$subject['id'];
		$tot=0;
?>
		<tr>
		  <td><?php print $subject['name'];?></td>
<?php
		reset($stages);
		while(list($index2,$stage)=each($stages)){
			$stagid=$stage['id'];
			$stagetot=0;
			$stagelist='';
?>
		  <td>
<?php
			if(isset($nosubjecthws[$stagid][$bid])){
				print $nosubjecthws[$stagid][$bid]['week'].'  ( '.$nosubjecthws[$stagid][$bid]['year'].' )';
				}
?>
		  </td>
<?php
			}
?>
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
