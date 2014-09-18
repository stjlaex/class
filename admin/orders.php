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
	$extrabuttons['inventory']=array('name'=>'current','value'=>'inventory_list.php');
	$extrabuttons['export']=array('name'=>'current','value'=>'orders_export.php');
	}
twoplus_buttonmenu($budgetyear,$currentyear+2,$extrabuttons,$book);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
		<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>


	<form id="formtoprocess2" name="formtoprocess2" method="post" action="<?php print $host; ?>" >
	  <div class="left">
	  <fieldset class="divgroup">
		<h5><?php print_string('ordersearch',$book);?></h5>		
		<div class="center">
		  <label for="Ordernumber"><?php print_string('ordernumber',$book);?></label><br />
		  <input tabindex="<?php print $tab++;?>" type="text" id="Ordernumber" name="ordernumber" maxlength="30"/>
			<button style="float:right;"  type="submit" name="sub" value="search">
			  <?php print_string('search');?>
			</button>
		</div>
		<div class="center">

        <?php 
            $orderstatus='-1';
            $listlabel='status';
            $listname='orderstatus';
            include('scripts/set_list_vars.php');
            list_select_enum('action',$listoptions,$book);
        ?>
        <?php 
            $orderstatus='-1';
            $listlabel='supplier';
            $listname='ordersupid';
            $d_sup=mysql_query("SELECT id, name FROM ordersupplier ORDER BY name;");
            include('scripts/set_list_vars.php');
            list_select_db($d_sup,$listoptions,$book);
        ?>
		</div>
	  </fieldset>
        </div>
        <div class="right">
	  <fieldset class="divgroup">
		<h5><?php print_string('invoicesearch',$book);?></h5>		
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
</div>
		<input type="hidden" name="budgetyear" value="<?php print $budgetyear;?>" />
		<input type="hidden" name="current" value="orders_list.php" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
<div class="center">
	<fieldset class="divgroup" id="viewcontent">
	  <h5><?php print get_string('budgets',$book).' - '.display_curriculumyear($budgetyear);?></h5>

	  <table class="listmenu smalltable">
		<tr>
		  <th><?php print_string('name');?></th>
		  <th><?php print_string('limit',$book);?></th>
		  <th><?php print_string('currentbalance',$book);?></th>
		  <th><?php print_string('projectedbalance',$book);?></th>
		</tr>
<?php
		$budgets=list_user_budgets($tid,$budgetyear);
	/**
	 * Checking if this is a new budget year no previously accessed -
	 * replicate the existing budget structure if it is.
	 */
	if(sizeof($budgets)==0 and $budgetyear>$currentyear and $aperm==1){
		include('new_budget_year.php');
		}

		while(list($index,$overbudget)=each($budgets)){
			while(list($subindex,$budget)=each($overbudget['subbudgets'])){
				if($index==$budget['id']){
					$rowclass='parents';
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

			/* Restrict access to budget managers, x perms*/
			if($budget['x']){
				print '<a href="admin.php?current=orders_limit.php&cancel='. $choice.'&choice='. $choice.'&budid='. $budget['id'].'&budgetyear='.$budgetyear.'">
						     <span class="clicktoconfigure" title="'.get_string('clicktoconfigure','admin').'"></span>
               </a>';
				}
?>
		  </td>
		  <td><?php print round($budget['costlimit'],0);?></td>
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
  </div>
