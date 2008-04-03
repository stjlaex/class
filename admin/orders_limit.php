<?php 
/**						   			  orders_limit.php
 */

$action='orders_limit_action.php';

if(isset($_GET['budid'])){$budid=$_GET['budid'];}else{$budid='';}
if(isset($_POST['budid'])){$budid=$_POST['budid'];}

	$Budget=fetchBudget($budid);

	three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div class="center">
		<table class="listmenu">
		  <caption>
			<?php print_string('limit',$book);?>
		  </caption>

		  <tr>
			<th>&nbsp</th>
			<th><?php print $Budget['Name']['value'];?></th>
			<td>
			  <input type="text" name="limit"
				value="<?php print $Budget['Limit']['value'];?>"
			  />
			</td>
		  </tr>

		</table>
	  </div>

	<input type="hidden" name="budid" value="<?php print $budid;?>" /> 
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
