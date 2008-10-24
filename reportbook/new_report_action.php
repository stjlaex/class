<?php
/**                    new_report_action.php
 */

$action='new_report.php';

/* The rcrid decides wether its a report binder or a subject report*/
if($r>-1){$rcrid=$respons[$r]['course_id'];}
else{$rcrid='';}
include('scripts/sub_action.php');

if($sub!='Submit'){
	$action='new_report_action.php';

	if(isset($_POST['recordid'])){
		$oldrid=$_POST['recordid'];
		}
	else{$oldrid=-1;}
	$RepDef=fetchReportDefinition($oldrid);


three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">
		<legend><?php print_string('identityofreport',$book);?></legend>

<?php
	$tab=xmlelement_div($RepDef['Title'],'',$tab,'left','reportbook');
 	$tab=xmlelement_div($RepDef['PublishedDate'],'',$tab,'left','reportbook');
 	$tab=xmlelement_div($RepDef['Deadline'],'',$tab,'right','reportbook');
?>

<?php
		if($rcrid!=''){
?>
		<div class="left">
<?php 
			$selstage=$RepDef['Stage']['value'];
			include('scripts/list_stage.php')
;?>
		</div>

		<div class="right">
<?php 
			$selcomponentstatus=$RepDef['ComponentStatus']['value'];
			include('scripts/list_componentstatus.php'); 
?>
		</div>
<?php
			}
?>

	  </fieldset>

<?php
		if($rcrid!=''){
?>
	  <fieldset class="left">
		<legend><?php print_string('includeassessmentscores',$book);?></legend>
<?php 
			$seleids=array();
			while(list($assindex,$eid)=each($RepDef['eids'])){
				$seleids[]=$eid;
				}
			$required='no';
			include('scripts/list_assessment.php');
?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('commentsandratingboxes',$book);?></legend>
<?php 
			$checkchoice=$RepDef['CategoriesOn']['value'];
			$checkcaption=get_string('addcategories',$book);
			$checkname='addcategory'; include('scripts/check_yesno.php');

			$checkchoice=$RepDef['CommentsOn']['value'];
			$checkcaption=get_string('allowsubjectcomments',$book);
			$checkname='reptype'; include('scripts/check_yesno.php');

			$checkchoice=$RepDef['CommentsCompulsory']['value'];
			$checkcaption=get_string('commentsarecompulsory',$book);
			$checkname='commentcomp'; include('scripts/check_yesno.php');

			$tab=xmlelement_div($RepDef['CommentsLength'],'',$tab,'center','reportbook');
?>
	  </fieldset>

<?php
   		}
   	else{
		/**
		 * This is a wrapper which binds the following subject reports
		 * together.
		 */
?>
	  <fieldset class="center">
		<legend><?php print_string('subjectreports',$book);?></legend>
<?php
		$selrids=array();
		while(list($repindex,$rep)=each($RepDef['reptable']['rep'])){
			$selrids[]=$rep['id_db'];
			}
		include('scripts/list_report.php');
?>
	  </fieldset>

<?php
		/**
		 * The rest covers the summary matter like signature boxes, form
		 * and section level comments and attendance.
		 */
		$selcatids=array();
		while(list($sumindex,$catdef)=each($RepDef['summaries'])){
			$selcatids[]=$catdef['id'];
			}
?>

	  <fieldset class="center">
		<legend><?php print_string('summarymatter',$book);?></legend>

	  <div class="left">
		<label for="Summary comments"><?php print_string('summarycomment',$book);?></label>
		<select style="width:25em;" id="Summary comments" type="text" name="catdefids[]"
			class="required" size="3" multiple="multiple" tabindex="<?php print $tab++;?>" >
		<option 
<?php
		if(sizeof($selcatids)==0){print ' selected="selected" ';}
?>
			value="-100">
			<?php print_string('none');?>
		</option>
<?php 
		$d_categorydef=mysql_query("SELECT id, name, subject_id FROM
			categorydef WHERE type='com' AND (course_id LIKE '$rcrid' 
			OR course_id='%') ORDER BY rating");
		while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
?>
		<option 
<?php
		if(in_array($catdef['id'], $selcatids)){print ' selected="selected" ';}
?>
			value="<?php print $catdef['id'];?>">
			<?php print $catdef['name'];?>
		</option>
<?php
		   	}
?>
		</select>
	  </div>

	  <div class="right">
		<label for="Summary comments"><?php print_string('summarysignature',$book);?></label>
		<select style="width:25em;" id="Summary signatures" type="text" name="catdefids[]"
			class="required" size="3" multiple="multiple" tabindex="<?php print $tab++;?>" >
		<option value="-100">
			<?php print_string('none');?>
		</option>
<?php 
		$d_categorydef=mysql_query("SELECT id, name, subject_id FROM
			categorydef WHERE type='sig' AND (course_id LIKE '$rcrid' 
			OR course_id='%') ORDER BY rating;");
		while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
?>
		<option 
<?php
		if(in_array($catdef['id'], $selcatids)){print ' selected="selected" ';}
?>
		value="<?php print $catdef['id'];?>">
			<?php print $catdef['name'];?>
		</option>
<?php
			}
?>
		</select>
		</div>
	  </fieldset>

<?php
		}
?>

	  <fieldset class="center">
		<legend><?php print_string('nameoftemplate',$book);?></legend>
		<?php 
		$seltemplate=$RepDef['Template']['value']; 
		include('scripts/list_template.php');
		?>
	  </fieldset>


		<input type="hidden" name="oldrid" value="<?php print $oldrid;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice; ?>"/>
		<input type="hidden" name="cancel" value="<?php print $choice; ?>"/>
	</form>
  </div>
<?php

	   	exit;
		}

elseif($sub=='Submit'){

	if($rcrid==''){$crid='wrapper';}
	else{$crid=$rcrid;}	
	$oldrid=$_POST['oldrid'];//-1 if this is a new report
	if($oldrid==-1){
		mysql_query("INSERT INTO report (course_id) VALUES ('$crid');");
		$rid=mysql_insert_id();
		}
	else{
		$rid=$oldrid;
		}


	$title=$_POST['title'];
	$date=$_POST['date'];
	$deadline=$_POST['deadline'];
	$transform=$_POST['template'];
	if(isset($_POST['style'])){$style=$_POST['style'];}else{$style='portrait';}

	mysql_query("UPDATE report SET title='$title', 
				date='$date', deadline='$deadline',  
				style='$style', transform='$transform' WHERE id='$rid';");

	if($crid!='wrapper'){
		/*** This is a subject report ****/
		$compstatus=$_POST['componentstatus'];
		$stage=$_POST['stage'];
		$reptype=$_POST['reptype0'];
		$addcategory=$_POST['addcategory0'];
		$commentcomp=$_POST['commentcomp0'];
		if(isset($_POST['commentlength'])){$commentlength=$_POST['commentlength'];}
		else{$commentlength='0';}
		mysql_query("UPDATE report SET component_status='$compstatus',
				addcomment='$reptype', commentlength='$commentlength', 
				commentcomp='$commentcomp', stage='$stage', 
				addcategory='$addcategory' WHERE id='$rid';");


		/*entry in rideid to link new report with chosen assessments*/
		mysql_query("DELETE FROM rideid WHERE report_id='$rid';");
		$eids=(array)$_POST['eids'];
		foreach($eids as $eid){ 
			mysql_query("INSERT INTO rideid 
		     (report_id, assessment_id) VALUES ('$rid', '$eid')");
			}
		
		mysql_query("DELETE FROM ridcatid WHERE report_id='$rid';");
		if($addcategory=='yes'){
			$d_catdef=mysql_query("SELECT id FROM categorydef WHERE
						type='rep' AND (course_id='%' OR course_id
							LIKE '$crid')");
			while($d_catid=mysql_fetch_array($d_catdef,MYSQL_NUM)){
				$catid=$d_catid[0];
				mysql_query("INSERT INTO ridcatid (report_id,
							categorydef_id, subject_id) VALUES
							('$rid', '$catid', '%')");
				}
			}
		}
	else{
		/*** This is a wrapper for subject reports ***/

		/* Summary matter goes into ridcatid with subject_id='summary' 
		 */
		mysql_query("DELETE FROM ridcatid WHERE report_id='$rid' 
						AND subject_id='summary';");
		$catdefids=(array)$_POST['catdefids'];
		while(list($index,$catid)=each($catdefids)){
			if($catid!='-100'){
				mysql_query("INSERT INTO ridcatid (report_id,
							categorydef_id, subject_id) VALUES
							('$rid', '$catid', 'summary')");
				}
			}

		/*wrapper report goes into ridcatid with subject_id='wrapper'*/
		mysql_query("DELETE FROM ridcatid WHERE report_id='$rid' 
						AND subject_id='wrapper';");
		$rids=(array)$_POST['rids'];
		foreach($rids as $wraprid){ 
			mysql_query("INSERT INTO ridcatid (report_id,
							categorydef_id, subject_id) VALUES
							('$rid', '$wraprid', 'wrapper')");
			}
		}
	}

include('scripts/redirect.php');
?>