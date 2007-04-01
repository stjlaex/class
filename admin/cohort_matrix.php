<?php
/**											cohort_matrix.php
 *
 *	Select which student groups make up a cohort
 */

$action='cohort_matrix_action.php';
$choice='cohort_matrix.php';

list($crid,$bid,$error)=checkCurrentRespon($r,$respons,'course');
if($error!=''){include('scripts/results.php');exit;}

three_buttonmenu();

/*keeping things simple by fixing season and year to a single value*/
/*to sophisticate in the future*/
$currentseason='S';
$currentyear=get_curriculumyear($crid);
$d_cohort=mysql_query("SELECT id, stage FROM cohort WHERE
							course_id='$crid' AND year='$currentyear'
							AND season='$currentseason' AND stage!='END'");
$cohorts=array();
while($cohort=mysql_fetch_array($d_cohort,MYSQL_ASSOC)){
	$cohorts[]=$cohort;
	}
?>
  <div id="heading">
			  <label><?php print_string('cohort',$book); ?></label>
			<?php print $crid.':'.$currentyear.':'.$currentseason;?>
  </div>

  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
	  <table class="listmenu">
		<tr>
		  <th>
			<?php print_string('stage',$book); ?>
		  </th>
		  <th>
			<?php print_string('communities',$book); ?>
		  </th>
		  <th>
			&nbsp
		  </th>
		</tr>
<?php
	while(list($index,$cohort)=each($cohorts)){
		$cohid=$cohort['id'];
		$d_cohidcomid=mysql_query("SELECT community_id FROM cohidcomid WHERE
							cohort_id='$cohid'");
		$newcomids=array();
		while($comid=mysql_fetch_array($d_cohidcomid,MYSQL_ASSOC)){
			$newcomids[]=$comid['community_id'];
			}

  		print '<tr><th>'.$cohort['stage'].'</th>';
		print '<input type="hidden" name="cohids[]" value="'.$cohid.'" />'
?>
		  <td>
<?php  $multi=4; $type='year'; include('scripts/list_community.php'); ?>
		  </td>
		  <td>
<?php  $multi=4; $type='academic'; include('scripts/list_community.php'); ?>
		  </td>
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
