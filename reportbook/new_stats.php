<?php
/*									 new_stats.php
*/

$action='new_stats_action.php';
$choice='new_stats.php';

include('scripts/course_respon.php');

$extrabuttons['importfromfile']=array('name'=>'current','value'=>'new_stats_import.php');
three_buttonmenu($extrabuttons);
?>

<div class="topform">
	<form id="formtoprocess" name="formtoprocess" 
	  enctype="multipart/form-data" method="post" action="<?php print $host;?>">

	  <fieldset class="divgroup">
	  <div class="left"> 
		<label for="Description"><?php print_string('description');?></label>
		<input class="required" type="text" id="Description"
				tabindex="<?php print $tab++;?>" 
				name="description"  style="width:20em;" length="20" maxlength="59" />
	  </div>

	  <div class="left">
<?php $required='no'; $multi='1'; include('scripts/list_assessment.php');?>
	  </div>

	  <div class="right">
<?php $required='no'; $multi='1'; include('scripts/list_assessment.php');?>
	  </div>

 	<input type="hidden" name="cancel" value="<?php print ''; ?>">
 	<input type="hidden" name="current" value="<?php print $action; ?>">
 	<input type="hidden" name="choice" value="<?php print $current; ?>">
	</form>
	</fieldset>

  </div>

  <div class="content">
	<table class="listmenu">
	  <caption><?php print_string('estimates',$book);?></caption>
<?php
   	$d_stats=mysql_query("SELECT DISTINCT id, description FROM stats
			   WHERE (course_id LIKE '$rcrid' OR course_id='%') ORDER BY id DESC");
	$statids=array();
	$stats=array();
	while($stat=mysql_fetch_array($d_stats,MYSQL_ASSOC)){
		$statids[]=$stat['id'];
		$stats[$stat['id']]=$stat;
		}

	while(list($index, $statid) = each($statids)){
		$stat=$stats[$statid];
?>
	  <tr>
		<td>
		  <input type="checkbox" name="statids[]" value="<?php print $statid; ?>" />
		</td>
		<td>
		  <?php print $stat['description']; ?>
		</td>
	  </tr>
<?php
		}
?>
	</table>
  </div>

