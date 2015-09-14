<?php
/**									fees_accounts_manage_action.php
 */

$action='fees_accounts_manage.php';
$cancel='fees_accounts_manage.php';
$feeyear=$_POST['feeyear'];
$action_post_vars=array('feeyear');

if(isset($_POST['recordid'])){
	$accountid=$_POST['recordid'];
	}
else{$accountid='';}

include('scripts/sub_action.php');

if(isset($_POST['accountname-'.$accountid]) and $_POST['accountname-'.$accountid]!=""){$accountname=$_POST['accountname-'.$accountid];}else{$accountname="";}
if(isset($_POST['bankname-'.$accountid]) and $_POST['bankname-'.$accountid]!=""){$bankname=$_POST['bankname-'.$accountid];}else{$bankname="";}
if(isset($_POST['bankcountry-'.$accountid]) and $_POST['bankcountry-'.$accountid]!=""){$bankcountry=$_POST['bankcountry-'.$accountid];}else{$bankcountry="";}
if(isset($_POST['bankcode-'.$accountid]) and $_POST['bankcode-'.$accountid]!=""){$bankcode=$_POST['bankcode-'.$accountid];}else{$bankcode="";}
if(isset($_POST['bankbranch-'.$accountid]) and $_POST['bankbranch-'.$accountid]!=""){$bankbranch=$_POST['bankbranch-'.$accountid];}else{$bankbranch="";}
if(isset($_POST['bankcontrol-'.$accountid]) and $_POST['bankcontrol-'.$accountid]!=""){$bankcontrol=$_POST['bankcontrol-'.$accountid];}else{$bankcontrol="";}
if(isset($_POST['banknumber-'.$accountid]) and $_POST['banknumber-'.$accountid]!=""){$banknumber=$_POST['banknumber-'.$accountid];}else{$banknumber="";}
if(isset($_POST['iban-'.$accountid]) and $_POST['iban-'.$accountid]!=""){$iban=$_POST['iban-'.$accountid];}else{$iban="";}
if(isset($_POST['bic-'.$accountid]) and $_POST['bic-'.$accountid]!=""){$bic=$_POST['bic-'.$accountid];}else{$bic="";}
if(isset($_POST['code-'.$accountid]) and $_POST['code-'.$accountid]!=""){$code=$_POST['code-'.$accountid];}else{$code="";}

$access=$_SESSION['accessfees'];


if($sub=='Submit' and (($banknumber!="" and $bankcontrol!="") or $iban!="")){
	mysql_query("INSERT INTO fees_account SET guardian_id=0,
					accountname=AES_ENCRYPT('$accountname','$access'),
					bankname=AES_ENCRYPT('$bankname','$access'),
					banknumber=AES_ENCRYPT('$banknumber','$access'),
					bankcode=AES_ENCRYPT('$bankcode','$access'),
					bankbranch=AES_ENCRYPT('$bankbranch','$access'),
					bankcontrol=AES_ENCRYPT('$bankcontrol','$access'),
					bankcountry=AES_ENCRYPT('$bankcountry','$access'),
					iban=AES_ENCRYPT('$iban','$access'),
					bic=AES_ENCRYPT('$bic','$access'),
					code=AES_ENCRYPT('$code','$access');");
	include('scripts/sub_action.php');
	include('scripts/redirect.php');
	}
elseif($sub=="save"){
	if(($banknumber!="" and $bankcontrol!="") or $iban!=""){
		mysql_query("UPDATE fees_account SET
						accountname=AES_ENCRYPT('$accountname','$access'),
						bankname=AES_ENCRYPT('$bankname','$access'),
						banknumber=AES_ENCRYPT('$banknumber','$access'),
						bankcode=AES_ENCRYPT('$bankcode','$access'),
						bankbranch=AES_ENCRYPT('$bankbranch','$access'),
						bankcontrol=AES_ENCRYPT('$bankcontrol','$access'),
						bankcountry=AES_ENCRYPT('$bankcountry','$access'),
						iban=AES_ENCRYPT('$iban','$access'),
						bic=AES_ENCRYPT('$bic','$access'),
						code=AES_ENCRYPT('$code','$access')
					WHERE id='$accountid';");
		}
	include('scripts/sub_action.php');
	include('scripts/redirect.php');
	}
else{
	$action='fees_accounts_manage_action.php';
	three_buttonmenu();
?>
<div id="viewcontent" class="content">

	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

		<div class="center">
		  <fieldset>
		    <legend><?php print_string('newaccount',$book);?></legend>
			<label><?php print_string('name',$book);?></label>
		  	<input type="text" name="accountname-" size="45" value="">
		  	<label><?php print_string('bankname','infobook');?></label>
		  	<input type="text" name="bankname-" size="25" value="">
		  	<label><?php print_string('country',$book);?></label>
			<input type="text" name="bankcountry-" size="2" value="">
			<label><?php print_string('bankcode','infobook');?></label>
			<input type="text" name="bankcode-" size="4" value="">
			<label><?php print_string('branch','infobook');?></label>
			<input type="text" name="bankbranch-" size="4" value="">
			<label><?php print_string('control','infobook');?></label>
			<input type="text" name="bankcontrol-" size="2" value="">
			<label><?php print_string('number',$book);?></label>
			<input type="text" name="banknumber-" size="20"value="">
			<label><?php print_string('iban',$book);?></label>
			<input type="text" name="iban-" size="35" value="">
			<label><?php print_string('bic',$book);?></label>
			<input type="text" name="bic-" size="10" value="">
			<label><?php print_string('code',$book);?></label>
			<input type="text" name="code-" size="10" value="">
		  </fieldset>
		</div>
	</form>
</div>
<?php
	}
?>
