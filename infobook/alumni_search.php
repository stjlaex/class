<?php 
/**										group_search.php
 *
 * Using the value of $choice to direct the group_search results back
 * to where this was called from.
 *
 */

$action='alumni_search_action.php';

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



	<div class="center">
	  <table class="listmenu">
		<tr>
		  <th>
			<?php print_string('year',$book);?>
		  </th>
		</tr>
<?php
	$d_y=mysql_query("SELECT id,name FROM community WHERE type='year' AND name>2000 ORDER BY name DESC;");
	while($year=mysql_fetch_array($d_y,MYSQL_ASSOC)){
		echo "<tr><td><input type='checkbox' name='comids[]' value='".$year['id']."'>".$year['name']."</td></tr>";
		}
?>
	  </table>
	</div>



	  <input type="hidden" name="alumnisearch" value="yes" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>

	</div>
