<?php 
/**								  		orders.php
 *
 * This is the entry page to the Order book - it lives within Admin
 * but has its own lib/fetch_orders.php functions and is essentialy a
 * set of self-contained scripts.
 *
 *
 */

$choice='orders.php';
$action='orders_action.php';

$currentyear=get_budgetyear();
if(isset($_GET['budgetyear'])){$budgetyear=$_GET['budgetyear'];}
else{$budgetyear=$currentyear;}
if(isset($_POST['budgetyear']) and $_POST['budgetyear']!=''){$budgetyear=$_POST['budgetyear'];}

$aperm=get_admin_perm('b',get_uid($tid));

$extrabuttons=array();
if($_SESSION['role']=='admin' or $aperm==1){
	$extrabuttons['newbudget']=array('name'=>'current','value'=>'new_budget.php');
	}
if($_SESSION['role']=='admin' or $aperm==1 or $_SESSION['role']=='office'){
	$extrabuttons['suppliers']=array('name'=>'current','value'=>'suppliers_list.php');
	$extrabuttons['export']=array('name'=>'current','value'=>'orders_export.php');
	}
twoplus_buttonmenu($budgetyear,$currentyear+2,$extrabuttons,$book);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post"
	  action="<?php print $host; ?>" >

		<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>


	<form id="formtoprocess2" name="formtoprocess2" method="post"
	  action="<?php print $host; ?>" >

	  <fieldset class="left">
		<legend><?php print_string('ordersearch',$book);?></legend>		
		<div class="center">
		  <div class="left">
		  <label for="Ordernumber"><?php print_string('ordernumber',$book);?></label>
		  <input tabindex="<?php print $tab++;?>" 
			type="text" id="Ordernumber" name="ordernumber" maxlength="30"/>
		  </div>
		  <div class="right">
			<button type="submit" name="sub" value="search">
			  <?php print_string('search');?>
			</button>
		  </div>
		</div>
		<div class="center">

		  <div class="left">
<?php 
		$orderstatus='-1';
		$listlabel='status';
		$listname='orderstatus';
		include('scripts/set_list_vars.php');
		list_select_enum('action',$listoptions,$book);
?>
		  </div>
		  <div class="right">
<?php 
		$orderstatus='-1';
		$listlabel='supplier';
		$liststyle='width:8em;';
		$listname='ordersupid';
		$d_sup=mysql_query("SELECT id, name FROM ordersupplier ORDER BY name;");
		include('scripts/set_list_vars.php');
		list_select_db($d_sup,$listoptions,$book);
?>
		  </div>
		</div>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('invoicesearch',$book);?></legend>		
		<div class="center">
		  <div class="left">
		  <label for="Invoicenumber"><?php print_string('reference',$book);?></label>
		  <input tabindex="<?php print $tab++;?>" 
			type="text" id="Invoicenumber" name="invoicenumber" maxlength="30"/>
		  </div>
		  <div class="right">
			<button type="submit" name="sub" value="search">
			  <?php print_string('search');?>
			</button>
		  </div>
		</div>
	  </fieldset>

		<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
		<input type="hidden" name="current" value="orders_list.php" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

	<fieldset class="center divgroup" id="viewcontent">
	  <legend><?php print get_string('budgets',$book).' - '.display_curriculumyear($budgetyear);?></legend>

	  <table class="listmenu smalltable">
		<tr>
		  <th><?php print_string('name');?></th>
		  <th><?php print_string('limit',$book);?></th>
		  <th><?php print_string('currentbalance',$book);?></th>
		  <th><?php print_string('projectedbalance',$book);?></th>
		</tr>
<?php
		$budgets=list_user_budgets($tid,$budgetyear);
		while(list($index,$overbudget)=each($budgets)){
			while(list($subindex,$budget)=each($overbudget['subbudgets'])){
				if($index==$budget['id']){
					$rowclass='gomidlite';
					}
				else{
					$rowclass='';
					}
?>
		<tr class="<?php print $rowclass;?>">
		  <td>
<?php

			if($budget['r']){
				print '<a  href="admin.php?current=orders_list.php&cancel='.$choice.'&choice='.$choice.'&budid='.$budget['id'].'&budgetyear='.$budgetyear.'">'.$budget['name'].'</a>';
				}
			else{
				print $budget['name'];
				}
?>
		  </td>
		  <td>
<?php 
			/* Restrict access to budget managers, x perms*/
			if($budget['x']){
				print '<a href="admin.php?current=orders_limit.php&cancel='.
							$choice.'&choice='. $choice.'&budid='. $budget['id'].'&budgetyear='.$budgetyear.'">' 
							.round($budget['costlimit'],0).'</a>';
				}
			else{
				print $budget['costlimit'];
				}
?>
		  </td>
		  <td><?php print get_budget_current($budget['id']);?></td>
		  <td><?php print get_budget_projected($budget['id']);?></td>
		</tr>
<?php
				}
			}
?>

	  </table>
	</fieldset>
  </div>
