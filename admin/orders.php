<?php 
/**								  		orders.php
 */

$choice='orders.php';
$action='orders_action.php';

if($_SESSION['role']=='admin'){
	$extrabuttons['newbudget']=array('name'=>'current','value'=>'new_budget.php');
	$extrabuttons['suppliers']=array('name'=>'current','value'=>'list_supplier.php');
	}
else{
	$extrabuttons=array();
	}
two_buttonmenu($extrabuttons,$book);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post"
	  action="<?php print $host; ?>" >
	  
	  <fieldset class="center">
		<legend><?php print_string('ordersearch',$book);?></legend>
		
		<div class="center">
		  <div class="left">
		  <label for="Ordernumber"><?php print_string('ordernumber',$book);?></label>
		  <input tabindex="<?php print $tab++;?>" 
			type="text" id="Ordernumber" name="ordernumber" maxlength="30"/>
<?php 
		$orderstatus='';
		$listlabel='status';
		$listname='orderstatus';
		include('scripts/set_list_vars.php');
		list_select_enum('action',$listoptions,$book);
?>
		  </div>
		  <div class="right">
			<button type="submit" name="sub" value="search">
			  <?php print_string('search');?>
			</button>
		  </div>
		</div>

	  </fieldset>
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

	<fieldset class="center">
	  <legend><?php print_string('budgets',$book);?></legend>
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('name');?></th>
		  <th><?php print_string('limit',$book);?></th>
		  <th><?php print_string('currentbalance',$book);?></th>
		  <th><?php print_string('projectedbalance',$book);?></th>
		</tr>
<?php
		$budgets=list_user_budgets($tid);
		while(list($index,$budget)=each($budgets)){
?>
		<tr>
		  <td>
<?php
			print '<a href="admin.php?current=orders_list.php&cancel='.$choice.'&choice='.$choice.'&budid='.$budget['id'].'">'.$budget['name'].'</a>';
?>
		  </td>
		  <td>
<?php
		print $budget['costlimit'];
?>
		  </td>
		  <td><?php print get_budget_current($budget['id']);?></td>
		  <td><?php print get_budget_projected($budget['id']);?></td>
		</tr>
<?php
		}
?>

	  </table>
	</fieldset>
  </div>
