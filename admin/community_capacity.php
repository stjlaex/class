<?php 
/**						   			  community_capacity.php
 */

$action='community_capacity_action.php';

if(isset($_GET['comid'])){$comid=$_GET['comid'];}else{$comid='';}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}

if(isset($_GET['enrolyear'])){$enrolyear=$_GET['enrolyear'];}else{$enrolyear='';}
if(isset($_POST['enrolyear'])){$enrolyear=$_POST['enrolyear'];}

	$community=get_community($comid);
	if($community['detail']!=''){$displayname=$community['detail'];}
	else{
		list($status,$yid)=split(':',$community['name']);
		$displayname=get_yeargroupname($yid);
		}

	three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div class="center">
		<table class="listmenu">
		  <caption>
			<?php print_string('capacity',$book);?>
		  </caption>

		  <tr>
			<th><?php print display_curriculumyear($enrolyear);?></th>
			<th><?php print $displayname;?></th>
			<td>
			  <input type="text" name="capacity"
				value="<?php print $community['capacity'];?>"
			  />
			</td>
		  </tr>

		</table>
	  </div>

	<input type="hidden" name="comid" value="<?php print $comid;?>" /> 
	<input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" /> 
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
