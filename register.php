<?php 
/**		   											register.php
 *	This is the hostpage for the register
 */

$host='register.php';
$book='register';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('group','newfid','startday','checkeveid');
include('scripts/set_book_session_vars.php');

$community=$group;
if($newfid!=''){
	$community=array('id'=>'','type'=>'form','name'=>$newfid);
	}

if(is_array($community)){
	if($community['type']=='form'){$newfid=$community['name'];}
	elseif($community['type']=='reg'){$comid=$community['id'];unset($newfid);}
	}
else{
	/*on first load select teachers formgorup by default*/
	$pastorals=list_pastoral_respon($respons);
	$rfids=$pastorals['forms'];
	$ryids=$pastorals['years'];
	if(sizeof($rfids)!=0){
		$newfid=$rfids[0];
		$community=array('id'=>'','type'=>'form','name'=>$newfid);
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
					   ,'completion_list.php' => 'completedregisters'
					   );
		selery_stick($choices,$choice,$book);
?>
	  </fieldset>
  </form>

  </div>
<?php
	  include('scripts/end_options.php'); 
?>
