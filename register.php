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
if(isset($CFG->regtypes[1])){$regtype=$CFG->regtypes[1];}
else{$regtype='form';}


if($nodays==''){$nodays=8;}

	if($newcid!=''){
	  $secid=get_class_section($newcid);
	  $community=array('id'=>'','type'=>'class','name'=>$newcid);
	  $newcomid='';
	  }
	elseif($newcomid!=''){
	  //TODO: no relation between sections and other community groups
	  $community=(array)get_community($newcomid);
	  /* The yid should always be set, but how depends on to the community type. TODO: simplfy!*/ 
	  if($community['type']=='year'){$yid=$community['name'];}
	  elseif($yid==''){$yid=array_pop(array_keys($community['groups']));}
	  $section=get_section($yid);
	  $secid=$section['id'];
	  $newcid='';
	  }

	/* If a community is already selected (by passing newcomid) then
	 * stick with that. Otherwise try to choose a relevant community to
	 * display by default from the users pastoral responsibilities. 
	 */
	if(!isset($community) and $current==''){
	  /* On first load select the teacher's pastoral group by default.
	   * This is made tricky because of different regtypes for
	   * different sections may or may not apply.
	   */
	  $pastorals=(array)list_pastoral_respon();
	  if(sizeof($pastorals['houses'])>0 and array_search('house',$CFG->regtypes)!==false){
		  $yid=$pastorals['houses'][0]['yeargroup_id'];
		  $section=get_section($yid);
		  if((array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[$section['id']]=='house') or (!array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[1]=='house')){
			  $community=array('id'=>$pastorals['houses'][0]['community_id'],'type'=>'house','name'=>$pastorals['houses'][0]['name']);
			  $secid=$section['id'];
			  }
		  }
	  if(!isset($community) and sizeof($pastorals['regs'])>0 and array_search('reg',$CFG->regtypes)!==false){
		  $yid=$pastorals['regs'][0]['yeargroup_id'];
		  $section=get_section($yid);
		  $community=array('id'=>$pastorals['regs'][0]['community_id'],'type'=>'reg','name'=>$pastorals['regs'][0]['name']);
			  $secid=$section['id'];
		  }
	  if(!isset($community) and sizeof($pastorals['forms'])>0 and array_search('form',$CFG->regtypes)!==false){
		  $yid=$pastorals['forms'][0]['yeargroup_id'];
		  $section=get_section($yid);
		  if((array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[$section['id']]=='form') or (!array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[1]=='form')){
			  $community=array('id'=>$pastorals['forms'][0]['community_id'],'type'=>'form','name'=>$pastorals['forms'][0]['name']);
			  $secid=$section['id'];
			  }
		  }
	  if(!isset($community) and sizeof($pastorals['years'])>0 and array_search('year',$CFG->regtypes)!==false){
		  foreach($pastorals['years'] as $yeargroup_id){
			  $section=get_section($yeargroup_id);
			  if((array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[$section['id']]=='year') or (!array_key_exists($section['id'],$CFG->regtypes) and $CFG->regtypes[1]=='year')){
				  $community=array('id'=>'','type'=>'year','name'=>$yeargroup_id);
				  $secid=$section['id'];
				  $yid=$yeargroup_id;
				  }
			  }
		  }

	  //trigger_error('DEFAULT: '.$community['type'].' : '.$yid.' : '.$secid.' : '.$newcomid.' .... '.$current,E_USER_WARNING);
		}

	/* If a community has been set then don't lose it. But careful of class communities which are not yet communities! */
	if(isset($community) and is_array($community) and $community['type']!='class'){
	  $newcomid=update_community($community);
	  $community=(array)get_community($newcomid);
	  $_SESSION[$book.'newcomid']=$newcomid;
	  $_SESSION[$book.'yid']=$yid;
	  }

?>
  <div id="bookbox" class="registercolor">
<?php

	if(empty($secid)){$secid=1;}
	$currentevent=get_currentevent($secid);

	if($current=='register_list.php' and empty($community)){
		$current='completion_list.php';
		}

	if(!empty($current)){
		include($book.'/'.$current);
		}
	elseif(isset($community) and is_array($community)){
		$current='register_list.php';
		include($book.'/'.$current);
		}
//trigger_error('CURRENT: '.$community['type'].' : '.$yid.' : '.$secid.' : '.$newcomid.' .... '.$current,E_USER_WARNING);

?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">

	  <fieldset class="register">
		<legend><?php print get_string('currentsession',$book);?></legend>
		<div class="register-session">
		 <a href="register.php?current=register_list.php&newcid=<?php print $newcid;?>&newcomid=<?php print $newcomid;?>&nodays=<?php print $nodays;?>&checkeveid=0&startday=" target="viewregister" onclick="parent.viewBook('register');">
<?php 
			print ''.display_date($currentevent['date']).' | ';
			print get_string('period',$book).': '.$currentevent['session'];
?>
		</a>
		</div>	  
	  </fieldset>
	  <fieldset class="register">
		<legend><?php print_string('section',$book);?></legend>

		<form id="registerchoice" name="registerchoice" method="post" action="register.php" target="viewregister">
<?php
	$onsidechange='yes';
	$listtype='section';
	$listname='secid';
	$listlabel='';
	include('scripts/list_section.php');
?>
		<input type="hidden" name="newcid" value="" />
		<input type="hidden" name="newcomid" value="" />
		<input type="hidden" name="nodays" value="8" />
<?php
if($current!='register_list.php'){
	$nextpage=$current;
	}
else{
	$nextpage='completion_list.php';
	}
?>
		<input type="hidden" name="current" value="<?php print $nextpage;?>" />
		</form>
	  </fieldset>



	<form id="registerchoicesel" name="registerchoicesel" method="post" action="register.php" target="viewregister">
	  <fieldset class="register selery">
		<legend><?php print '<br />';?></legend>
<?php
		$choices=array('absence_list.php' => 'absencelists'
					   ,'signedout_list.php' => 'signedout'
					   ,'completion_list.php' => 'completedregisters'
					   ,'statistics.php' => 'statistics'
					   );
		selery_stick($choices,$choice,$book);
?>
	  </fieldset>
	  <input type="hidden" name="secid" value="<?php print $secid;?>" />
	  <input type="hidden" name="newcid" value="" />
	  <input type="hidden" name="newcomid" value="" />
	  <input type="hidden" name="nodays" value="8" />
	</form>

  </div>
<?php
	/* This is for the pop-up Register notices. */
	if(isset($notice) and $notice!=''){
		print '<div id="notice" class="hidden"><div class="rowaction"><button name="close" value="close" onclick="closeAlert();">CLOSE</button></div><div class="content"><div class="center"></div>'.$notice.'</div>';
?>
<?php
		}
	  include('scripts/end_options.php'); 
?>
