<?php 
/**										group_search.php
 *
 * Using the value of $choice to direct the group_search results back
 * to where this was called from.
 *
 */

$action='group_search_action.php';

$extrabuttons=array();
$extrabuttons['message']=array('name'=>'current',
							   'title'=>'message',
							   'value'=>'message.php');
$extrabuttons['addresslabels']=array('name'=>'current',
									 'title'=>'printaddresslabels',
									 'value'=>'print_labels.php');
$extrabuttons['exportstudentrecords']=array('name'=>'current',
											'title'=>'exportstudentrecords',
											'value'=>'export_students.php');
three_buttonmenu($extrabuttons,$book);
?>

  <div id="viewcontent" class="content">

<?php
	if($choice=='message.php' or $choice=='print_labels.php' ){
?>
<div class="divgroup center">
  <div class="center">
<p>		<?php print_string('selectfromthegroups',$book);?></p>
  </div>
</div>
<?php
		}
?>

	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">

	  <div class="left">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('section',$book);?></th>
		</tr>
<?php
	$sections=list_sections();
	while(list($index,$section)=each($sections)){
		print '<tr><td><input type="checkbox" name="secids[]" value="'.$section['id'].'">'.$section['name'].'</input></td></tr>';
		}
?>
	  </table>
	</div>


	<div class="right">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('yeargroups',$book);?></th>
		</tr>
<?php
	$yeargroups=list_yeargroups();
	foreach($yeargroups as $year){
		print '<tr><td><input type="checkbox" name="yids[]" value="'.$year['id'].'">'.$year['name'].'</input></td></tr>';
		}
?>
	  </table>
	</div>

	  <div class="left">
	  <table class="listmenu">
		<tr>
		  <th colspan="4"><?php print_string('applications',$book);?></th>
		</tr>
<?php
	$application_steps=array('EN','AP','AT','RE','CA','WL','ACP','AC');
	foreach($application_steps as $enrolstatus){
		print '<tr><td colspan="4"><input type="checkbox" name="enrolstatuses[]" value="'.$enrolstatus.'">'.get_string(displayEnum($enrolstatus,'enrolstatus'),$book).'</input></td></tr>';
		}
?>
		<tr>
		  <th colspan="4"><?php print get_string('applications',$book).' '. get_string('year',$book);?></th>
		</tr>
		<tr>
<?php
		$currentyear=get_curriculumyear();
		for($nextyear=0;$nextyear<4;$nextyear++){
			$enrolyear=$currentyear+$nextyear;
			print '<td><input type="radio" name="enrolyear" value="'.$enrolyear.'">'.display_curriculumyear($enrolyear).'</input></td>';
			}
?>
		</tr>

	  </table>
	</div>

	  <div class="left">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('reenrolments','admin');?></th>
		</tr>
<?php
	$application_steps=array('C'=>'reenroling','P'=>'pending','L'=>'leavers');
	foreach($application_steps as $value => $enrolstatus){
		print '<tr><td><input type="radio" name="enrolstatuses[]" value="'.$value.'">'.get_string($enrolstatus,$book).'</input></td></tr>';
		}
	print '<tr><td><input type="radio" name="enrolstatuses[]" value="uncheck" checked="yes">'.get_string('uncheck',$book).'</input></td></tr>';
?>
	  </table>
	</div>

	  <div class="right">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('siblings','infobook');?></th>
		</tr>
<?php
$sibling_steps=array('Y'=>'youngest','E'=>'eldest');
	foreach($sibling_steps as $value => $enrolstatus){
		print '<tr><td><input type="radio" name="limit" value="'.$value.'">'.get_string($enrolstatus,$book).'</input></td></tr>';
		}
	print '<tr><td><input type="radio" name="limit" value="uncheck" checked="yes">'.get_string('uncheck',$book).'</input></td></tr>';
?>
	  </table>
	</div>

	<div class="center">
	  <table class="listmenu">
		<tr>
		<th colspan="3"><?php print_string('enrolments',$book);?></th>
		</tr>
		<tr>
<?php
		print '<td><input type="radio" name="enroldate" value="start">'.get_string('schoolstartdate',$book).'</input></td>';
		print '<td><input type="radio" name="enroldate" value="leave">'.get_string('schoolleavingdate',$book).'</input></td>';
		print '<td><input type="radio" name="enroldate" value="uncheck" checked="yes">'.get_string('uncheck',$book).'</input></td>';

?>
		</tr>
		<tr>
<?php
$Enrolment['EntryDate1']=array('label' => 'after', 
							   'table_db' => 'info', 
							   'field_db' => 'enroldate1', 
							   'type_db' =>'date', 
							   'value' => ''
							   );
$Enrolment['EntryDate2']=array('label' => 'before', 
							   'table_db' => 'info', 
							   'field_db' => 'enroldate2', 
							   'type_db' =>'date', 
							   'value' => ''
							   );
$tab=xmlarray_form($Enrolment,'','',$tab,$book);
?>
		</tr>
	  </table>
	</div>



	  <input type="hidden" name="groupsearch" value="yes" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>

	</div>
