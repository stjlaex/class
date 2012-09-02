<?php
/**                                  comments_new.php    
 *
 */

$cancel='comments_list.php';
$action='comments_new_action.php';

if(isset($_POST['commentid'])){$commentid=$_POST['commentid'];}else{$commentid=-1;}
$Comment=(array)fetchComment(array('id'=>$commentid));
if($commentid==-1){
	if(isset($_GET['bid'])){$bid=$_GET['bid'];}
	}
else{
	/* Editing an existing order.*/
	$bid=$Comment['Subject']['value_db'];
	$todate=$Comment['EntryDate']['value'];
	$catid=$Comment['Categories']['Category'][0]['value'];
	$ratvalue=$Comment['Categories']['Category'][0]['rating']['value'];
	if(isset($Comment['incident_id_db'])){
		$incid=$Comment['incident_id_db'];
		$Incident=(array)fetchIncident(array('id'=>$incid));
		}
	elseif(isset($Comment['merit_id_db'])){
		$merid=$Comment['merit_id_db'];
		$Merit=(array)fetchMerit(array('id'=>$merid));
		}
	}

$yid=$Student['YearGroup']['value'];
$perm=getYearPerm($yid,$respons);

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('comments');?></label>
<?php
print $Student['Forename']['value'].' '.$Student['Surname']['value'];
print '('.$Student['RegistrationGroup']['value'].')';
?>
  </div>

  <div class="content">

	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">


	  <fieldset class="center">
		<div class="center">
		  <label for="Detail"><?php print_string('comments',$book);?></label>
		  <textarea tabindex="<?php print $tab++;?>" name="detail" class="required" id="Detail" rows="4" 
					cols="35"><?php print $Comment['Detail']['value'];?></textarea>
		</div>
		<div class="left">
<?php 
			$xmldate='Entrydate';
			$required='yes';
			include('scripts/jsdate-form.php'); 
?>
		</div>

		<div class="right">
<?php 
		$required='no';
		$listname='bid'; 
		$listid='subject';
		$listlabel='subject';
		$subjects=list_student_subjects($sid);
		include('scripts/set_list_vars.php');
		list_select_list($subjects,$listoptions,$book);
?>
	  </div>

	  <div class="right">
<?php 
				
if($CFG->emailguardiancomments=='yes' or ($CFG->emailguardiancomments=='epf' and $perm['x']==1)){
		$checkname='guardianemail';$checkcaption=get_string('sharewithguardian',$book);
		$checkalert=get_string('sharecommentalert',$book);
		include('scripts/check_yesno.php');
		unset($checkalert);
		}
?>
	  </div>

	</fieldset>

	<fieldset class="center">
	  <div class="left">
<?php
	list($ratingnames,$catdefs)=fetch_categorydefs('con','%',$secid);
	$listswitch='yes';
	$listname='ratvalue';
	$listid='rating';
	$listlabel='type';
	$required='yes';
	if(!isset($ratvalue)){$ratvalue='9999';}
	include('scripts/set_list_vars.php');
	list_select_list($ratingnames['con'],$listoptions,$book);
?>
	  </div>
	  <div class="right">
<?php 
		$listlabel='category'; 
		$listid='category';
		$listname='catid';
		$required='yes'; 
		include('scripts/set_list_vars.php');
		list_select_list($catdefs,$listoptions,$book);
?>
	  </div>

	  <div id="switchRating" class="center switcher">
	  </div>

	</fieldset>

		  
	<fieldset class="center divgroup">

	  <div class="left">
<?php 					 
	if($CFG->emailcomments=='yes'){
		$checkname='teacheremail';$checkcaption=get_string('emailtoteachers',$book);
		include('scripts/check_yesno.php'); 
		}
?>
	  </div>
	  <div class="right">
<?php 					 
	if($CFG->emailcomments=='yes'){
		$checkname='senemail';$checkcaption=get_string('emailtosensupport',$book);
		include('scripts/check_yesno.php'); 
		}
?>
	  </div>
	</fieldset>


	  <input type="hidden" name="commentid" value="<?php print $Comment['id_db'];?>" />
	  <input type="hidden" name="meritid" value="<?php print $Comment['merit_id_db'];?>" />
	  <input type="hidden" name="incidentid" value="<?php print $Comment['incident_id_db'];?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>


<div id="switchRating1"  class="hidden">
  <div id="formstatus-new" class="">
	<?php print get_string('add',$book). ' '.get_string('merits',$book);?>
  </div>
<div class="left">
<?php
	/* Offer a choice of activities or hide if only one. */
	list($ratingnames,$catdefs)=fetch_categorydefs('mer');
	if(sizeof($catdefs)>1){
		$required='no';
		$listlabel='activity';
		$listname='activity';
		$listid='activity';
		$tab=6;
		include('scripts/set_list_vars.php');
		list_select_list($catdefs,$listoptions,$book);
		}
	else{
		print '<input type="hidden" name="activity" value="'.array_pop(array_keys($catdefs)).'"/>';
		}
?>
  </div>
  <div class="right">
<?php 
		if(isset($Merit)){$points=$Merit['Points']['value_db'];}
		$listlabel='points';
		$required='no';
		$listname='points';
		$listid='points';
		asort($ratingnames['meritpoints']);
		include('scripts/set_list_vars.php');
		list_select_list($ratingnames['meritpoints'],$listoptions,$book);
?>
  </div>
</div>
<div id="switchRating0"  class="hidden">
  <div class="left">
  </div>
</div>
<div id="switchRating-1"  class="hidden">
  <div id="formstatus-edit" class="">
<?php
			if(!isset($Incident)){print_string('recordnewincident',$book);}
			else{print_string('editincident',$book);}

?>
  </div>
	  <div class="left">
<?php 
		if(isset($Incident['Sanction']['value'])){$sanction=$Incident['Sanction']['value_db'];}
		list($ratingnames,$sanctions)=fetch_categorydefs('inc');
		$listlabel='sanction';
		$listname='sanction';
		$listid='sanction';
		$catsecid=$secid;
		$cattype='inc';
		$required='no'; 
		$tab=6;
		include('scripts/set_list_vars.php');
		list_select_list($sanctions,$listoptions,$book);
?>
	  </div>
	  <div class="right">
<?php 
		$listlabel='incidentstatus'; 
		$required='yes'; 
		$listname='closed';
		if(isset($Incident['Closed']['value'])){
			$closed=$Incident['Closed']['value'];
			}
		else{
			$closed='Y';
			}
		include('scripts/set_list_vars.php');
		list_select_enum('closed',$listoptions,'infobook');
?>
	  </div>
	  <div class="center">
		<label for="Detail"><?php print_string('details',$book);?></label>
		<textarea name="sanctiondetail"  tabindex="<?php print $tab++;?>" 
		 id="Detail" rows="2" cols="32"><?php print $Incident['Detail']['value'];?></textarea>
	  </div>
</div>
