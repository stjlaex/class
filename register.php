<?php 
 /**		   											register.php
  *
  *	This is the hostpage for the register.
  *
  * The group of students being registered could either be identified
  * by newfid or newcid or newcomid - pastoral or academic or specific registration group.
  *
  */

$host='register.php';
$book='register';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('newfid','newcid','newcomid','startday','checkeveid','secid','nodays');
include('scripts/set_book_session_vars.php');

if($nodays==''){$nodays=8;}

  if($newfid!=''){
	  $section=get_section($newfid,'form');
	  $secid=$section['id'];
	  $community=array('id'=>'','type'=>'form','name'=>$newfid);
	  $newcid='';$newcomid='';
	  }
  elseif($newcid!=''){
	  $secid=get_class_section($newcid);
	  $community=array('id'=>'','type'=>'class','name'=>$newcid);
	  $newfid='';$newcomid='';
	  }
  elseif($newcomid!=''){
	  //TODO: no relation between sections and other community groups
	  //$secid=get_class_section('7Y');
	  $community=array('id'=>$newcomid,'type'=>$CFG->registrationtype,'name'=>'');
	  $newfid='';$newcid='';$secid='1';
	  }

  if(isset($community) and is_array($community)){
	  if($community['type']=='form'){$newfid=$community['name'];}
	  elseif($community['type']==$CFG->registrationtype or $community['type']=='class'){$comid=$community['id'];}
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
	elseif(isset($community) and is_array($community)){
		$current='register_list.php';
		include($book.'/'.$current);
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">

	  <fieldset class="register">
		<legend><?php print get_string('currentsession',$book);?></legend>
		<div style="background-color:#666655;font-size:x-small;padding:2px;">
		 <a style="color:#fff;"
			href="register.php?current=register_list.php&newfid=<?php print $newfid;?>&newcid=<?php print $newcid;?>&newcomid=<?php print $newcomid;?>&nodays=<?php print $nodays;?>&checkeveid=0&startday=" 
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

	  <fieldset class="register">
		<legend><?php print_string('takeregister',$book);?></legend>

		<form id="registerchoice" name="registerchoice" method="post" 
							action="register.php" target="viewregister">
<?php
				if(!isset($CFG->registrationtype) or $CFG->registrationtype=='form'){
					$onsidechange='yes'; 
					include('scripts/list_form.php');
					}
				else{
					$onsidechange='yes'; $listtype=$CFG->registrationtype; $listlabel=''; 
					include('scripts/list_community.php');
					}
?>
		<input type="hidden" name="newcid" value="" />
		<input type="hidden" name="current" value="register_list.php" />
		<input type="hidden" name="nodays" value="8" />
		</form>
	  </fieldset>

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
					   ,'statistics.php' => 'statistics'
					   );
		selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	</form>

  </div>
<?php
	  include('scripts/end_options.php'); 
?>
