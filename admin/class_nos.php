<?php
/**											class_nos.php
 *
 *	Select which subjects are taught for which courses
 */

$action='class_nos.php';
$choice='class_nos.php';

list($crid,$bid,$error)=checkCurrentRespon($r,$respons,'course');
if($error!=''){include('scripts/results.php');exit;}

	$d_classes=mysql_query("SELECT DISTINCT stage FROM classes WHERE
							course_id='$crid'");
	$d_subject=mysql_query("SELECT DISTINCT subject_id FROM cridbid 
							WHERE course_id='$crid' ORDER BY subject_id");
	$bids=array();
   	while($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
   		$bids[]=$subject['subject_id'];
		}

two_buttonmenu($extrabuttons);
?>
  <div class="content" id="viewcontent">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
	  <table class="listmenu">
		<tr>
		  <th></th>
<?php
	$stages=array();
	while($stage=mysql_fetch_array($d_classes,MYSQL_ASSOC)){
   		$stages[]=$stage['stage'];
  		print '<th>'.$stage['stage'].'</th>';
		}
?>
		</tr>
<?php
	for($c2=0; $c2<sizeof($bids); $c2++){
   		print '<tr id="'.$c2.'" >';
   		print '<th>'.$crid.':'.$bids[$c2].'</th>';
	
   		for($c=0; $c<sizeof($stages); $c++){	
   			$d_classes=mysql_query("SELECT many,generate FROM classes WHERE
				subject_id='$bids[$c2]' AND stage='$stages[$c]' AND course_id='$crid'");
   			$d_class=mysql_query("SELECT id FROM class WHERE
				subject_id='$bids[$c2]' AND stage='$stages[$c]' AND course_id='$crid'");
   			$classes=mysql_fetch_array($d_classes,MYSQL_ASSOC);
   			$many=$classes['many'];
   			$generate=$classes['generate'];
			$nosids=0;
			$nocids=0;
			while($class=mysql_fetch_array($d_class,MYSQL_ASSOC)){
				$cid=$class['id'];
				$d_cidsid=mysql_query("SELECT COUNT(student_id) AS no FROM cidsid
								WHERE class_id='$cid'");
				$no=mysql_result($d_cidsid,0);
				if($no>0){
					$nosids+=$no;
					$nocids++;
					}
				}
?>
		  <td>
			<table>
			  <tr>
				<td style="width:10em;">&nbsp <?php print $nocids.' '.$generate;?></td>
				<td style="width:10em;">&nbsp <?php print 'Ave. '.round($nosids/$nocids);?></td>	
				<td style="width:10em;text-align:left;">&nbsp <?php print 'Tot. '.$nosids;?></td>
			  </tr>
			</table>
		  </td>
<?php
	  		}
?>
		</tr>
<?php
	   	}
?>
	  </table>

	  <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>
