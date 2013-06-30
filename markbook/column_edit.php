<?php 
/** 									column_edit.php
 */

$action='column_edit_action.php';

/* Make sure a column is checked*/
if(!isset($_POST['checkmid'])){
	$action='class_view.php';
   	$result[]=get_string('pleasechooseamarkcolumn');
   	include('scripts/results.php');
   	include('scripts/redirect.php');
    exit;
	}

$checkmids=(array)$_POST['checkmid'];

if(sizeof($checkmids)>1){
		$action='class_view.php';
		$result[]=get_string('pleasechooseonlyonemarkcolumn');
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		} 

/* Can only edit one mark at a time so... */
$mid=$checkmids[0];
$mark=get_mark($mid);

if($mark['marktype']=='hw'){$marktype='homework';}
elseif($mark['marktype']=='score' and $mark['assessment']=='no' ){$marktype='classwork';}
else{$marktype='assessment';}

/*	Make sure user has priviliges to edit */
	if($mark['author']!=$tid){
		$perm=getMarkPerm($mid,$respons);
		if($perm['w']!='1'){
			$result[]=get_string('youneedtobetheauthor');
			$action='class_view.php';
			include('scripts/results.php');
			include('scripts/redirect.php');
			exit;
			}
		}

$tab=1;
three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('edit',$book); ?></label>
			<?php print get_string($marktype,$book).': '.$mark['def_name'];?>
  </div>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" novalidate method="post" action="<?php print $host;?>">

	  <fieldset class="center">
		<legend><?php print_string('detailsofmark',$book);?></legend>
		<div class="center">
		  <label for="Topic">
			<?php print_string('markstitleidentifyingname',$book);?>
		  </label>
		  <input class="required" type="text" id="Topic" tabindex="<?php print $tab++;?>" 
				 style="width:25em;" name="topic" 
				 value="<?php print $mark{'topic'}; ?>" maxlength="38" pattern="alphanumeric" />
		</div>
		<div class="left">
		  <label for="Comment">
			<?php print_string('optionalcomment',$book);?>
		  </label>
		  <input type="text" id="Comment" style="width:25em;" tabindex="<?php print $tab++;?>" 
		  name="comment" value="<?php print $mark{'comment'};?>" 
		  maxlength="98" pattern="alphanumeric" />
		</div>
		<div class="right">
		  <label for="Date"><?php print_string('datedue',$book);?></label>
		  <?php	$todate=$mark{'entrydate'}; include('scripts/jsdate-form.php');?>
		</div>
	  </fieldset>




	  <fieldset class="left">
		<legend><?php print_string('classesthatusethismark',$book);?></legend>
<?php
	/* select the classes that already use this mark */
	$oldcids=list_mark_cids($mid);

	/* select all possible classes to apply the mark to */
	$bid=$classes[$cids[0]]['bid'];
	$crid=$classes[$cids[0]]['crid'];
	if($r>-1){
		/* either by current responsibility choice */
		$rcrid=$respons[$r]['course_id'];
		$d_cids=mysql_query("SELECT class.id, class.name FROM class JOIN cohort ON class.cohort_id=cohort.id
							WHERE cohort.course_id LIKE '$rcrid' AND cohort.year='$curryear'
							AND subject_id LIKE '$bid' ORDER BY class.name;");
		}
	else{	 
		/* or limit by the teacher's own subject class */
		$d_cids = mysql_query("SELECT class.id, class.name
				FROM class JOIN tidcid ON tidcid.class_id=class.id 
				WHERE tidcid.teacher_id='$tid' AND class.subject_id='$bid' 
				AND cohort_id=ANY(SELECT id FROM cohort WHERE year='$curryear') ORDER BY class.id");
		}

	$nocids=mysql_num_rows($d_cids)+1;
	if($nocids>14){$nocids=14;}
?>
	<label for="Used by Classes"><?php print get_subjectname($bid).' '.get_string('classes');?></label>
	<select class="required" style="width:50%;" tabindex="<?php print $tab++;?>"
		name="selcids[]" id="Used by Classes" size="10" multiple="multiple">
<?php
	$newcids=array();
	while($newcid = mysql_fetch_array($d_cids,MYSQL_ASSOC)){
		$newcids[]=$newcid['id'];
		print '<option ';
		if(in_array($newcid['id'],$oldcids)){print 'selected="selected"';}
		print ' value="'.$newcid['id'].'">'.$newcid['name'].'</option>';
		}
?>	
		</select>
<?php
	for($c=0;$c<sizeof($newcids);$c++){
?>
		<input type="hidden" name="newcids[]" value="<?php print $newcids[$c]; ?>" />
<?php
		}
?>
	  </fieldset>

<?php
if(sizeof($components)>0){
?>
	  <fieldset class="right">
		<legend><?php print_string('subjectcomponent');?></legend>
<?php
   	$selnewpid=$mark['component_id'];
	$listname='newpid';$listlabel='subjectcomponent';$multi=1;
	include('scripts/set_list_vars.php');
	list_select_list($components,$listoptions,$book);
?>
	  </fieldset>

<?php
}
?>



<?php
if($tid=='administrator'){
	$cohort=array('id'=>'','course_id'=>$crid,'stage'=>'%','year'=>get_curriculumyear($crid));
	$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,'%');
	foreach($AssDefs as $AssDef){
		$asses[]=array('id'=>$AssDef['id_db'],'name'=>$AssDef['Description']['value']);
		}

	list($selneweid,$eidbid,$eidpid)=get_mark_assessment($mid);

?>
	  <fieldset class="right">
		<legend><?php print_string('assessment');?></legend>
<?php
	$listname='neweid';$listlabel='assessment';$multi=1;
	include('scripts/set_list_vars.php');
	list_select_list($asses,$listoptions,$book);
?>
	  </fieldset>
<?php
}
?>




<?php
 /** 
  * Editing the outoftotal should not be done here. It needs to be added to 
  * the edit scores screen, as each score row needs to be checked against the default
  * value and updated appropriately.
  *
  *	$total=$mark['total'];
  */
?>

	    <input type="hidden" name="mid" value="<?php print $mid; ?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>	
  </div>
