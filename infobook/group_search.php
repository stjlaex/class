<?php 
/**										group_search.php
 *
 * Using the value of $choice to direct the group_search results back
 * to where this was called from.
 *
 */

//$action='group_search_action.php';

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
two_buttonmenu($extrabuttons,$book);
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
	while(list($index,$year)=each($yeargroups)){
		print '<tr><td><input type="checkbox" name="yids[]" value="'.$year['id'].'">'.$year['name'].'</input></td></tr>';
		}
?>
	  </table>
	</div>

	  <input type="hidden" name="groupsearch" value="yes" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>

	</div>
