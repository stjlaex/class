<?php 
 /**		   											register.php
  *
  *	This is the hostpage for the register.
  *
  */

$host='register.php';
$book='register';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('newfid','newcid','startday','checkeveid','secid','nodays');
include('scripts/set_book_session_vars.php');

//$community=$group;
  if($newfid!=''){
	$section=get_section($newfid,'form');
	$secid=$section['id'];
	$community=array('id'=>'','type'=>'form','name'=>$newfid);
	$nodays=8;
	}
  elseif($newcid!=''){
	$secid=-1;
	$community=array('id'=>'','type'=>'class','name'=>$newcid);
	}

  if(is_array($community)){
	if($community['type']=='form'){$newfid=$community['name'];}
	elseif($community['type']=='reg' or $community['type']=='class'){$comid=$community['id'];unset($newfid);}
	}
  else{
	/* On first load select the teacher's form group by default. */
	$pastorals=list_pastoral_respon($respons);
	$rfids=$pastorals['forms'];
	$ryids=$pastorals['years'];
	if(sizeof($rfids)!=0){
		$newfid=$rfids[0];
		$section=get_section($newfid,'form');
		$secid=$section['id'];
		$community=array('id'=>'','type'=>'form','name'=>$newfid);
		}
	}
?>
  <div id="bookbox" class="registercolor">
<?php
 
	if(!isset($secid)){$secid=1;}
	$currentevent=get_currentevent($secid);

	if($current!=''){
		include($book.'/'.$current);
		}
	elseif(is_array($community)){
		$current='register_list.php';
		include($book.'/'.$current);
		}
//		unset($newfid);
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">

	  <fieldset class="register">
		<legend><?php print get_string('currentsession',$book);?></legend>
		<div style="background-color:#666655;font-size:x-small;padding:2px;">
		 <a style="color:#fff;"
			href="register.php?current=register_list.php&newfid=<?php print $newfid;?>&newcid=<?php print $newcid;?>&nodays=<?php print $nodays;?>&startday=" 
			target="viewregister" onclick="parent.viewBook('register');">
<?php 
			print ''.display_date($currentevent['date']).'<br />';
			print ''.date('H:i').' '.get_string('period',$book).': '.$currentevent['session'];
?>
		</a>
		</div>	  
	  </fieldset>

		<br />
		<br />
<?php
if($community['type']=='class'){
?>
	<form id="registerchoice" name="registerchoice" method="post" 
							action="register.php" target="viewregister">
	  <fieldset class="register">
		<legend><?php print_string('takeregister',$book);?></legend>
<?php		$onsidechange='yes'; include('scripts/list_form.php');?>
	  </fieldset>
	  <input type="hidden" name="current" value="register_list.php" />
	</form>


<?php
	}
else{
?>
	<form id="registerchoice" name="registerchoice" method="post" 
							action="register.php" target="viewregister">
	  <fieldset class="register">
		<legend><?php print_string('takeregister',$book);?></legend>
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
					   //,'late_list.php' => 'lates' TODO
					   ,'completion_list.php' => 'completedregisters'
					   );
		selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>
<?php
	}
?>

  </div>
<?php
	  include('scripts/end_options.php'); 
?>
