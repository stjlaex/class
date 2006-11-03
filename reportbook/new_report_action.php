<?php
/**                    new_report_action.php
 */

$action='new_report.php';

$rcrid=$respons[$r]['course_id'];
include('scripts/course_respon.php');

include('scripts/sub_action.php');
print $sub;

if($sub!='Submit'){

$action='new_report_action.php';
$choice='new_report.php';

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">
		<legend><?php print_string('identityofreport',$book);?></legend>

		<div class="left">
		  <label for="Title"><?php print_string('title');?></label>
		  <input class="required" type="text" id="Title" name="title"
			tabindex="<?php print $tab++;?>" length="40" maxlength="60" />
		</div>
		<div class="right">
		  <label><?php print_string('publisheddate');?></label>
		  <?php include('scripts/jsdate-form.php'); ?>
		</div>
		<div class="left">
		  <label for="Comment"><?php print_string('description');?></label>
		  <input type="text" id="Comment" name="comment" length="40" maxlength="250"
				tabindex="<?php print $tab++;?>" />
		</div>
		<div class="right">
		  <label><?php print_string('deadlineforcompletion',$book);?></label>
		  <?php include('scripts/jsdate-form.php'); ?>
		</div>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('includeassessmentscores',$book);?></legend>
		<?php include('scripts/list_assessment.php');?>
	  </fieldset>


	  <fieldset class="right">
		<legend><?php print_string('writtencomments',$book);?></legend>

		  <?php $checkcaption=get_string('addcategories',$book);
		        $checkname='addcategory'; include('scripts/check_yesno.php');?>
		  <?php $checkcaption=get_string('allowsubjectcomments',$book);
		        $checkname='reptype'; include('scripts/check_yesno.php');?>
		  <?php $checkcaption=get_string('commentsarecompulsory',$book);
		        $checkname='commentcomp'; include('scripts/check_yesno.php');?>

		<table class="listmenu"><tr>
			<th><label for="Comment Length">
			<?php print_string('restrictcommentscharacterlength',$book);?>
			  </label></th>
			<td><input type="text" pattern="integer" id="Comment Length"
				name="commentlength" maxlength="5" length="4" tabindex="<?php print $tab++;?>" />
			</td></tr></table>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('summarycomment',$book);?></legend>
		<label for="Summary comments"><?php print_string('summarycomment',$book);?></label>
		<select style="width:25em;" id="Summary comments" type="text" name="catdefids[]"
			class="required" size="3" multiple="multiple" tabindex="<?php print $tab++;?>" >
		<option value="-100">
			<?php print_string('none');?>
		</option>
<?php 
	$d_categorydef=mysql_query("SELECT id, name, subject_id FROM
		categorydef WHERE type='com' AND (course_id LIKE '$rcrid' 
		OR course_id='%') ORDER BY rating");
	while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
?>   				
		<option value="<?php print $catdef['id'];?>">
			<?php print $catdef['name'];?>
		</option>
<?php
	   	}
?>
		</select>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('properties',$book);?></legend>
		<div>
		  <?php include('scripts/list_stage.php') ;?>
		</div>

		<div class="left">
		  <?php include('scripts/list_componentstatus.php'); ?>
		</div>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('summarysignature',$book);?></legend>
		<label for="Summary comments"><?php print_string('summarysignature',$book);?></label>
		<select style="width:25em;" id="Summary signatures" type="text" name="catdefids[]"
			class="required" size="3" multiple="multiple" tabindex="<?php print $tab++;?>" >
		<option value="-100">
			<?php print_string('none');?>
		</option>
<?php 
	$d_categorydef=mysql_query("SELECT id, name, subject_id FROM
		categorydef WHERE type='sig' AND (course_id LIKE '$rcird' 
		OR course_id='%') ORDER BY rating");
	while($catdef=mysql_fetch_array($d_categorydef,MYSQL_ASSOC)){
?>   				
		<option value="<?php print $catdef['id'];?>">
			<?php print $catdef['name'];?>
		</option>
<?php
	   	}
?>
		</select>
	  </fieldset>

	  <fieldset class="left">
		<legend><?php print_string('nameoftemplate',$book);?></legend>
		<?php include('scripts/list_template.php');?>
	  </fieldset>


		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice; ?>"/>
		<input type="hidden" name="cancel" value="<?php print ''; ?>"/>
	</form>
  </div>
<?php
	   	exit;
		}
elseif($sub=='Submit'){
		$title=$_POST['title'];
		$comment=$_POST['comment'];
		$compstatus=$_POST['componentstatus'];
		$stage=$_POST['stage'];
		$reptype=$_POST['reptype0'];
		$addcategory=$_POST['addcategory0'];
		$commentcomp=$_POST['commentcomp0'];
		if(isset($_POST['commentlength'])){$commentlength=$_POST['commentlength'];}
		else{$commentlength='0';}
		if(isset($_POST['template'])){$style=$_POST['template'];}else{$style='';}
		if(isset($_POST['template'])){$transform=$_POST['template'];}else{$template='';}
		if(isset($_POST['catdefids'])){$catdefids=(array)$_POST['catdefids'];}
		else{$catdefids=array();}
		$date=$_POST['date0'];
		$deadline=$_POST['date1'];

		mysql_query("INSERT INTO report (title, comment, course_id,
				stage, component_status,
				date, deadline, addcomment, commentlength,
					commentcomp, addcategory, style, transform) VALUES
				('$title', '$comment', '$rcrid', '$stage', '$compstatus',
				'$date', '$deadline', '$reptype', '$commentlength',
					'$commentcomp', '$addcategory', '$style', '$transform');");	

		/*entry in rideid to link new report with chosen assessments*/
		$eids=(array)$_POST{'eids'};
		foreach($eids as $eid){ 
  			mysql_query("INSERT INTO rideid 
		     (report_id, assessment_id) VALUES ('$rid', '$eid')");
			}

		if($addcategory=='yes'){
			$d_catdef=mysql_query("SELECT id FROM categorydef WHERE
						type='rep' AND (course_id='%' OR course_id
							LIKE '$rcrid')");
			while($d_catid=mysql_fetch_array($d_catdef,MYSQL_NUM)){
				$catid=$d_catid[0];
				mysql_query("INSERT INTO ridcatid (report_id,
							categorydef_id, subject_id) VALUES
							('$rid', '$catid', '%')");
				}
			}

		while(list($index,$catid)=each($catdefids)){
			if($catid!='-100'){
				mysql_query("INSERT INTO ridcatid (report_id,
							categorydef_id, subject_id) VALUES
							('$rid', '$catid', 'summary')");
				}
			}
		}

include('scripts/redirect.php');
?>