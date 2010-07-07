<?php
/**									new_inovice.php
 */

$action='new_invoice_action.php';

$ordid=$_POST['ordid'];
$budid=$_POST['budid'];

three_buttonmenu();

$Invoice=fetchInvoice();
$Order=fetchOrder($ordid);
$Invoice['Currency']['value']=$Order['Currency']['value'];
?>

  <div id="heading">
	<label><?php print_string('newinvoice',$book);?></label>
	<?php print get_string('order',$book).' '.$Order['Reference']['value'];?>
  </div>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="center divgroup">
		<div class="center">
		  <ul>
<?php
   	 while(list($index,$Material)=each($Order['Materials']['Material'])){
	   	print '<li>'.$Material['Detail']['value'].'  &nbsp; (' 
	 			 .$Material['Quantity']['value']. 
	  			 ' x '.$Material['Unitcost']['value']. ' '. 
	   			 displayEnum($Order['Currency']['value'],$Order['Currency']['field_db']). 
	   			 ')</li>';
		}
?>
		  </ul>
		</div>
	  </fieldset>

	  <div class="center">
		  <?php $tab=xmlarray_form($Invoice,'','newinvoice',$tab,$book); ?>
	  </div>

		<input type="hidden" name="ordid" value="<?php print $ordid;?>" />
		<input type="hidden" name="budid" value="<?php print $budid;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
