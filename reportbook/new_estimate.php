<?php
/*									 new_estimate.php
*/

$action='new_estimate_action.php';
$choice='new_estimate.php';

include('scripts/course_respon.php');

three_buttonmenu();
?>
  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="divgroup">
	  <div class="left"> 
		<label><?php print_string('populatethisemptyassessmentwithestimatedscores',$book);?></label>
  <?php $required='yes'; $multi='1'; include('scripts/list_assessment.php');?>
	  </div>

	  <div class="left">
		<?php print_string('usethisregressionlinestatistics',$book);?>
	  <?php $required='yes'; $multi='1'; include('scripts/list_stats.php');?>
	  </div>

	  <div class="right">
		<?php print_string('usethesebaselinescores',$book);?>
	  <?php $required='yes'; $multi='1'; include('scripts/list_assessment.php');?>
	  </div>

 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $current; ?>">
 	<input type="hidden" name="cancel" value="<?php print ''; ?>">
	</form>
  </fieldset>
  </div>

  <div class="content">
	<table class="listmenu">
	  <caption><?php print_string('estimates',$book);?></caption>
	  <thead>
		<tr>
		  <th></th>
		  <th><?php print_string('year',$book);?></th>
		  <th><?php print_string('description',$book);?></th>
		</tr>
	  </thead>
<?php
   	$d_assessment=mysql_query("SELECT DISTINCT id, description, year FROM assessment
			   WHERE (course_id LIKE '$crid' OR course_id='%') AND resultstatus='E' ORDER
			   BY year DESC, id DESC");
	$eids=array();
	$assessments=array();
	while($assessment=mysql_fetch_array($d_assessment,MYSQL_ASSOC)){
		$eids[]=$assessment['id'];
		$assessments[$assessment['id']]=$assessment;
		}

	while(list($index, $eid) = each($eids)){
		$assessment=$assessments[$eid];
?>
	  <tbody>
		<tr>
		  <td>
			<input type="checkbox" name="eids[]" value="<?php print $eid; ?>" />
		  </td>
		  <td>
		<?php print $assessment['year']; ?>
		  </td>
		  <td>
		<?php print $assessment['description']; ?>
		  </td>
		</tr>
	  </tbody>
<?php
		}
?>
	</table>
  </div>
