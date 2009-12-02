<?php 
/**										group_search.php
 *
 *
 */

$action='group_search_action.php';

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('search',$book);?></label>
  </div>

  <div id="viewcontent" class="content">

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
