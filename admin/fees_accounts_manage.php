<?php
/**                                  fees_accounts_manage.php
 */

$action='fees_accounts_manage_action.php';
$cancel='fees.php';

$feeyear=$_POST['feeyear'];

include('scripts/sub_action.php');

$extrabuttons['newaccount']=array('name'=>'current','value'=>'fees_accounts_manage_action.php');
two_buttonmenu($extrabuttons,$book);
?>
  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('bankaccounts',$book);?></caption>
<?php
		$access=$_SESSION['accessfees'];
		$entryno=0;
		$d_a=mysql_query("SELECT id, valid, CONCAT(AES_DECRYPT(accountname,'$access'), ' - ', AES_DECRYPT(bankname,'$access')) AS name, 
						AES_DECRYPT(accountname,'$access') AS accountname,
						AES_DECRYPT(bankname,'$access') AS bankname,
						AES_DECRYPT(banknumber,'$access') AS banknumber,
						AES_DECRYPT(bankcode,'$access') AS bankcode,
						AES_DECRYPT(bankbranch,'$access') AS bankbranch,
						AES_DECRYPT(bankcontrol,'$access') AS bankcontrol ,
						AES_DECRYPT(bankcountry,'$access') AS bankcountry,
						AES_DECRYPT(iban,'$access') AS iban ,
						AES_DECRYPT(bic,'$access') AS bic 
					FROM fees_account WHERE guardian_id=0 AND id>1 ORDER BY name;");
		while($a=mysql_fetch_array($d_a,MYSQL_ASSOC)){
			$accountid=$a['id'];
			$entryno++;
			$actionbuttons=array();
			$rown=0;

			$d_r=mysql_query("SELECT * FROM fees_remittance WHERE account_id='$accountid';");
			if(mysql_num_rows($d_r)>0){
				$disabled=" disabled='disabled' style='background-color:grey;cursor:default;' ";
				}
			else{$disabled="";}

			$extrabuttons=array();
			$extrabuttons['save']=array('name'=>'process',
									   'value'=>'save',
									   'title'=>'save');
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus <?php if($a['valid']=='0'){print 'lowlite';} ?>" 
			  onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++; ?>">
			<th>&nbsp</th>
			<td>
			  <?php print $a['name'];?>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="2">
			  <div>
			  	<div>
				  	<label><?php print_string('name',$book);?></label>
				  	<input type="text" name="accountname-<?php echo $accountid;?>" size="45" value="<?php echo $a['accountname'];?>">
				  	<label><?php print_string('bankname','infobook');?></label>
				  	<input type="text" name="bankname-<?php echo $accountid;?>" size="25" value="<?php echo $a['bankname'];?>">
				  	<label><?php print_string('country',$book);?></label>
					<input type="text" name="bankcountry-<?php echo $accountid;?>" size="2" value="<?php echo $a['bankcountry'];?>">
					<label><?php print_string('bankcode','infobook');?></label>
					<input type="text" name="bankcode-<?php echo $accountid;?>" size="4" value="<?php echo $a['bankcode'];?>">
					<label><?php print_string('branch','infobook');?></label>
					<input type="text" name="bankbranch-<?php echo $accountid;?>" size="4" value="<?php echo $a['bankbranch'];?>">
					<label><?php print_string('control','infobook');?></label>
					<input type="text" name="bankcontrol-<?php echo $accountid;?>" size="2" value="<?php echo $a['bankcontrol'];?>">
					<label><?php print_string('number',$book);?></label>
					<input type="text" name="banknumber-<?php echo $accountid;?>" size="20"value="<?php echo $a['banknumber'];?>">
					<label><?php print_string('iban',$book);?></label>
					<input type="text" name="iban-<?php echo $accountid;?>" size="35" value="<?php echo $a['iban'];?>">
					<label><?php print_string('bic',$book);?></label>
					<input type="text" name="bic-<?php echo $accountid;?>" size="10" value="<?php echo $a['bic'];?>">
				</div>
			</div>
				<?php all_extrabuttons($extrabuttons,$book,'clickToAction(this)'," class='rowaction' $disabled "); ?>
			</td>
			<div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				xmlechoer('Account',$a);
?>
			</div>

		  </tr>
		</tbody>
<?php
			}
?>
	  </table>
	</div>
	
	<input type="hidden" name="feeyear" value="<?php print $feeyear;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

