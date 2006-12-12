<?php 
/**		   											register.php
 *	This is the hostpage for the register
 */

$host='register.php';
$book='register';

include('scripts/head_options.php');

include('scripts/book_variables.php');

if(!isset($_SESSION['registergroup'])){$_SESSION['registergroup']='';}

$community=$_SESSION['registergroup'];
if(isset($_POST['newfid'])){
	$community=array('id'=>'','type'=>'form','name'=>$_POST['newfid']);
	}
elseif(isset($_POST['newyid'])){
	$community=array('id'=>'','type'=>'year','name'=>$_POST['newyid']);
	}
if(is_array($community)){
	if($community['type']=='form'){$fid=$community['name'];}
	elseif($community['type']=='year'){$yid=$community['name'];}
	elseif($community['type']=='reg'){$comid=$community['id'];}
	}
else{
	$pastorals=listPastoralRespon($respons);
	$rfids=$pastorals['forms'];
	$ryids=$pastorals['years'];
	if(sizeof($rfids)!=0){
		$fid=$rfids[0];
		$community=array('id'=>'','type'=>'form','name'=>$fid);
		}
	}
?>
  <div id="bookbox" class="registercolor">
<?php
	$currentevent=currentEvent();
	if($current!=''){
		include($book.'/'.$current);
		$_SESSION['registergroup']=$community;
		}
	elseif(is_array($community)){
		$current='register_list.php';
		include($book.'/'.$current);
		}
	else{
		//include($book.'/'.$current);
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">	

	<form id="registerchoice" name="registerchoice" method="post" 
							action="register.php" target="viewregister">
	  <fieldset class="register">
		<legend><?php print_string('takeregister',$book);?></legend>
		<label>
		  <?php print get_string('currentsession',$book).':';?><br />
		  <?php print $currentevent['period'].' '.$currentevent['date'];?><br />
			<?php print date('H:i:s');?>
		</label>
		<br />
		<br />
<?php		$onsidechange='yes'; include('scripts/list_form.php');?>
	  </fieldset>
	  <input type="hidden" name="current" value="register_list.php" />
	</form>

	<br />
	<br />

	<form id="registerchoicesel" name="registerchoicesel" method="post" 
		  action="register.php" target="viewregister">
	  <fieldset class="register selery">
		<legend><?php print_string('list',$book);?></legend>
<?php
		$choices=array('absence_list.php' => 'absencelists'
					   //,'late_list.php' => 'lates'
					   //,'completions.php' => 'completions'
					   );
		selery_stick($choices,$choice,$book);
?>
	  </fieldset>
  </form>

  </div>
<?php
	  include('scripts/end_options.php'); 
?>
