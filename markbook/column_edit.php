<?php 
/** 									column_edit.php
 */

$action='column_edit_action.php';

/* Make sure a column is checked*/
if(!isset($_POST{'checkmid'})){
	$action='class_view.php';
   	$result[]=get_string('pleasechooseamarkcolumn');
   	include('scripts/results.php');
   	include('scripts/redirect.php');
    exit;
	}

$checkmid=$_POST{'checkmid'};

if(sizeof($checkmid)>1){
		$action='class_view.php';
		$result[]=get_string('pleasechooseonlyonemarkcolumn');
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		} 

	$mid=$checkmid[0];
	$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
	$mark=mysql_fetch_array($d_mark,MYSQL_ASSOC);

/*	Make sure user has priviliges to edit*/	
	if($mark['author']!=$tid){
		$perm=getMarkPerm($mid, $respons);
		if($perm['w']!='1'){
			$result[]=get_string('youneedtobetheauthor');
			$action='class_view.php';
			include('scripts/results.php');
			include('scripts/redirect.php');
			exit;
			}
		}

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="left">
		<legend><?php print_string('classesthatusethismark',$book);?></legend>
<?php
/*	select the classes that already use this mark*/
	$oldcids=array();
   	$d_cids = mysql_query("SELECT class_id  FROM midcid WHERE mark_id='$mid' ORDER BY class_id");
    while($oldcid = mysql_fetch_array($d_cids,MYSQL_ASSOC)){$oldcids[]=$oldcid['class_id'];}

/*	select all possible classes to apply the mark to*/
	if($r>-1){
/*	   	either by current responsibility choice*/
	 	$rbid=$respons[$r]{'subject_id'};
		$rcrid=$respons[$r]{'course_id'};
		$ryid=$respons[$r]{'yeargroup_id'};
		if($ryid==''){$ryid='%';}
		$d_cids=mysql_query("SELECT DISTINCT id AS class_id FROM class WHERE
			(subject_id LIKE '$rbid' OR subject_id='%') AND (course_id
			LIKE '$rcrid' OR course_id='%') AND (yeargroup_id LIKE '$ryid'
				OR yeargroup_id='%') ORDER BY id");
		}
	else {	 
/*	by the subject of this class*/
		$cid=$cids[0];
		$d_bid = mysql_query("SELECT DISTINCT subject_id FROM class WHERE id='$cid'");
		$bid = mysql_result($d_bid,0);
		$d_cids = mysql_query("SELECT DISTINCT id AS class_id
				FROM class JOIN tidcid ON tidcid.class_id=class.id 
				WHERE tidcid.teacher_id='$tid' 
				AND class.subject_id='$bid' ORDER BY class_id");
		}

	$nocids=mysql_num_rows($d_cids)+1;
	if($nocids>14){$nocids=14;}
?>
	<label for="Used by Classes"><?php print_string('classes');?></label>
	<select class="required"  
		name="selcids[]" id="Used by Classes" size="10" multiple="multiple">
<?php
	$newcids=array();
	while($newcid = mysql_fetch_array($d_cids,MYSQL_ASSOC)) {
		$newcids[]=$newcid['class_id'];
		print '<option ';
		if(in_array($newcid['class_id'],$oldcids)){print 'selected="selected"';}
		print ' value="'.$newcid['class_id'].'">'.$newcid['class_id'].'</option>';
		}
?>			
		</select>
<?php
	for ($c=0;$c<sizeof($newcids);$c++){
?>
		<input type="hidden" name="newcids[]" value="<?php print $newcids[$c]; ?>" />
<?php
		}
?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('dateofmark',$book);?></legend>
	  <?php	$todate=$mark{'entrydate'}; include('scripts/jsdate-form.php');?>
	  </fieldset>

<?php

/*	Editing the outoftotal needs to be added to the edit scores screen,
 *	as each score row needs to be checked against the default value and updated appropriately
*/
	$total=$mark['total'];

/*************************************************/	
?>
	  <fieldset class="right">
		<legend><?php print_string('subjectcomponent');?></legend>
<?php
   	$selpid=$mark['component_id'];
	include('markbook/list_components.php');
?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('detailsofmark',$book);?></legend>
		<table width="100%">
		  <tr>
			<td>
			  <label for="Topic">
				<?php print_string('markstitleidentifyingname',$book);?>
			  </label>
			</td>
		  </tr>
		  <tr>
			<td>
			  <input class="required" type="text" id="Topic" 
				style="width:25em;" name="topic" 
				value="<?php print $mark{'topic'}; ?>" maxlength="38" pattern="alphanumeric" />
			</td>
		  </tr>
		  <tr>
			<td>
			  <label for="Comment">
				<?php print_string('optionalcomment',$book);?>
			  </label>
			</td>
		  </tr>
		  <tr>
			<td>
			  <input type="text" id="Comment" style="width:25em;" 
				name="comment" value="<?php print $mark{'comment'};?>" 
				maxlength="98" pattern="alphanumeric" />
			</td>
		  </tr>
		</table>
	  </fieldset>
<?php
/**********NOT USED
<fieldset class="rightmiddle"><legend>By default Column is:</legend>
	<label for="Hide or Show">Display:<br /></label>
  		<select name="hidden" id="Hide or Show" size="3">
			<option value="yes" <?php if($mark{'hidden'}=='yes'){print "selected";} ?>>Hidden</option>
			<option value="no" <?php if($mark{'hidden'}=='no'){print "selected";} ?> >Shown</option>
		</select>
</fieldset>
**************/
?>

	  <fieldset class="left">
		<legend><?php print_string('associatewithanassessment',$book);?></legend>
		<table width="100%">
		  <tr>
			<td>
<?php
  if($tid=='administrator'){
	if($mark{'assessment'}=='yes'){
		/*if already associated with an assessment then find it*/
		$d_eid=mysql_query("SELECT id FROM assessment JOIN
			eidmid ON assessment.id=eidmid.assessment_id WHERE eidmid.mark_id='$mid'");
		$eid=mysql_fetch_array($d_eid,MYSQL_ASSOC);
		/*find all possible assessments that could be associated with mark*/
		$d_assessment=mysql_query("SELECT * FROM assessment ORDER BY
		year DESC, id DESC");
?>
			  <label for="Assessment">
				<?php print_string('linkedtothisassessment',$book);?>
			  </label>	
			  <select name="eid" id="Assessment" size="1">
				<option value=""></option>
<?php
		while($assessment=mysql_fetch_array($d_assessment,MYSQL_ASSOC)){
			print '<option ';
			if($assessment['id']==$eid['id']){print 'selected="selected"';}
			print ' value="'.$assessment['id'].'">'.$assessment['course_id'].':'
					.$assessment['stage'].':'.$assessment['description'].' ('.$assessment['year'].')</option>';
			}
?>
				<option value='unassess'>
				</option>
			  </select>
			</td>
		  </tr><tr>
			<td>
			  <button name="assbut" value="Unassess">
				<?php print_string('changeassessmentstatus',$book);?>
			  </button>
<?php
		}
	else{
		print_string('notlinkedtoanassessment',$book);		
?>
			</td>
		  </tr>
		  <tr>
			<td>
			  <button name="assbut" value="Assess">
				<?php print_string('changeassessmentstatus',$book);?>
			  </button>
<?php
		}
  }
?>
			</td>
		  </tr>
		</table>
	  </fieldset>
	  <input type="hidden" name="mid" value="<?php print $mid; ?>" />
		<input type="hidden" name="total" value="<?php print $total; ?>" />
		  <input type="hidden" name="current" value="<?php print $action;?>" />
			<input type="hidden" name="choice" value="<?php print $choice;?>" />
			  <input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>	
  </div>



















































