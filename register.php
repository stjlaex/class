<?php 
 /**		   											register.php
  *
  *	This is the hostpage for the register.
  *
  * The group of students being registered could either be identified
  * by newcomid or newcid - pastoral or academic.
  *
  */

$host='register.php';
$book='register';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('newcid','newcomid','startday','checkeveid','secid','nodays','yid');
include('scripts/set_book_session_vars.php');
if(!isset($CFG->registrationtype)){$CFG->registrationtype='form';}

if($nodays==''){$nodays=8;}

  if($newcid!=''){
	  $secid=get_class_section($newcid);
	  $community=array('id'=>'','type'=>'class','name'=>$newcid);
	  $newcomid='';
	  }
  elseif($newcomid!=''){
	  //TODO: no relation between sections and other community groups
	  $community=(array)get_community($newcomid);
	  $section=get_section($community['name'],$community['type']);
	  $secid=$section['id'];
	  $newcid='';
	  }

  if(isset($community) and is_array($community)){
	  $comid=$community['id'];
	  }
  else{
	  /* On first load select the teacher's formgroup by default. */
	  $pastorals=(array)list_pastoral_respon();
	  $rfids=$pastorals['forms'];
	  $ryids=$pastorals['years'];
	  $rhids=$pastorals['houses'];
	  if(sizeof($rfids)!=0){
		  $section=get_section($rfids[0],'form');
		  $secid=$section['id'];
		  $community=array('id'=>'','type'=>'form','name'=>$rfids[0]);
		  $comid=(array)update_community($community);
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
			href="register.php?current=register_list.php&newcid=<?php print $newcid;?>&newcomid=<?php print $newcomid;?>&nodays=<?php print $nodays;?>&checkeveid=0&startday=" 
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
<!--

TODO: list_sections here:

	  <fieldset class="register">
		<legend><?php print_string('section',$book);?></legend>

		<form id="registerchoice" name="registerchoice" method="post" action="register.php" target="viewregister">
<?php
//			$onsidechange='yes';
//			$listtype='section';
//			$listlabel='';
//			include('scripts/list_community.php');
?>
		<input type="hidden" name="newcid" value="" />
		<input type="hidden" name="current" value="register_list.php" />
		<input type="hidden" name="nodays" value="8" />
		</form>
	  </fieldset>

	  <br />
	  <br />
-->

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
