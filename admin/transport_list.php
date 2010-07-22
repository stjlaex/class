<?php
/**                                  transport_list.php   
 * 
 *
 */

$action='transport_list_action.php';

include('scripts/sub_action.php');

$extrabuttons=array();

if((isset($_POST['busname']) and $_POST['busname']!='')){
		/* These are the three search terms */
	$busname=$_POST['busname'];
	$community=array('id'=>'','type'=>'transport','name'=>$busname);
	$students=(array)listin_community($community);
	}
elseif(isset($_POST['fid']) and $_POST['fid']!=''){
	$fid=$_POST['fid'];
	$community=array('id'=>'','type'=>'form','name'=>$fid);
	$students=(array)listin_community($community);
	}
else{
	}
//$Bus=fetchBus();

two_buttonmenu($extrabuttons,$book);

	if($buaname!=-1){
?>
  <div id="heading">
	<label><?php print_string('bus',$book);?></label>
<?php	print $Bus['Name']['value'].' ';?>
  </div>
<?php
		}
?>
  <div id="viewcontent" class="content">


  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('orders',$book);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th>&nbsp;</th>
		  </tr>
		</thead>
		<tr>
		</tr>
	  </table>
	</div>

	<input type="hidden" name="busname" value="<?php print $busname;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

