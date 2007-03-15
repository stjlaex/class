<?php
/**											new_assessment.php
 */

$action='new_assessment_action.php';
$choice='new_assessment.php';

include('scripts/course_respon.php');

$extrabuttons['importfromfile']=array('name'=>'current','value'=>'new_assessment_import.php');
$extrabuttons['importscores']=array('name'=>'current','value'=>'new_assessment_scores.php');
three_buttonmenu($extrabuttons);
?>
  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" 
	enctype="multipart/form-data" method="post" action="<?php print $host;?>">

	  <div class="left"> 
		<label for="Description"><?php print_string('description');?></label>
		<input class="required" type="text" id="Description" tabindex="<?php print $tab++;?>" 
				name="description"  style="width:20em;" maxlength="59" value="" />

		<label for="Element"><?php print_string('element',$book);?></label>
		<input class="required" type="text" id="Element" tabindex="<?php print $tab++;?>" 
				name="element"  style="width:3em;" maxlength="3" value="" />

		<label for="Printlabel"><?php print_string('printlabel',$book);?></label>
		<input class="required" type="text" id="Printlabel" tabindex="<?php print $tab++;?>" 
				name="printlabel"  style="width:8em;" maxlength="59" value="" />
	  </div>

	  <div class="right">
		<?php 		include('scripts/list_stage.php'); ?>
		<?php 		include('scripts/list_calendar_year.php');?>
<?php 
		include('scripts/list_subjects.php'); 
		include('scripts/list_componentstatus.php'); 
?>
	  </div>

	  <div class="left">
		<label><?php print_string('create',$book);?></label>
		<?php $xmldate='Creation'; $required='no'; 
			$todate='0000-00-00'; include('scripts/jsdate-form.php');?>

		<label><?php print_string('deadlineforcompletion');?></label>
		<?php $xmldate='Deadline'; $required='yes'; include('scripts/jsdate-form.php');?>
	  </div>

	  <div class="right">
<?php 
		$required='no';
		include('scripts/list_gradescheme.php'); 
		include('scripts/list_method.php'); 
		include('scripts/list_resultqualifier.php'); 
?>
	  </div>

	  <div class="left">
		<label for="Derivation"><?php print_string('derivation',$book);?></label>
		<input type="text" id="Derivation" tabindex="<?php print $tab++;?>" 
				name="derivation" style="width:12em;" maxlength="59" value="" />
	  </div>

	  <input type="text" style="display:none;" id="Id_db" name="id" value="" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $current;?>" />
	</form>
  </div>

  <div class="content">
	<div class="center">
	  <table class="listmenu" name="listmenu">
		<caption><?php print_string('assessments');?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print get_string('curriculumyear').' ('.get_string('season').')';?></th>
			<th><?php print_string('stage');?></th>
			<th><?php print_string('subject');?></th>
			<th><?php print_string('status');?></th>
			<th><?php print_string('description');?></th>
			<th><?php print_string('element',$book);?></th>
		  </tr>
		</thead>
<?php
	/*the rowaction buttons used within each assessments table row*/
    $imagebuttons=array();
	$imagebuttons['clicktodelete']=array('name'=>'current',
										 'value'=>'delete_assessment.php',
										 'title'=>'delete');
	$imagebuttons['clicktoedit']=array('name'=>'Edit',
									   'value'=>'',
									   'title'=>'edit');
    $extrabuttons=array();
	$extrabuttons['editscores']=array('name'=>'current',
									  'title'=>'editscores',
									  'value'=>'edit_scores.php');
   	$extrabuttons['generatecolumns']=array('name'=>'current',
										   'title'=>'generatecolumns',
										   'value'=>'generate_assessment_columns.php');
   	$extrabuttons['deletecolumns']=array('name'=>'current',
										 'title'=>'deletecolumns',
										 'value'=>'delete_assessment_columns.php');

   	$d_assessment=mysql_query("SELECT id FROM assessment
			   WHERE (course_id LIKE '$rcrid' OR course_id='%') ORDER
					BY year DESC, id DESC");
	while($assessment=mysql_fetch_array($d_assessment,MYSQL_ASSOC)){
	    unset($AssDef);
		$eid=$assessment['id'];
		$AssDef=fetchAssessmentDefinition($eid);
		$rown=0;
?>
		<tbody id="<?php print $eid;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $eid.'-'.$rown++;?>">
			<th>&nbsp</th>
			<td><?php print $AssDef['Year']['value'].'('.$AssDef['Season']['value'].')'; ?></td>
			<td><?php print $AssDef['Stage']['value']; ?></td>
			<td><?php print $AssDef['Subject']['value']; ?></td>
			<td class='<?php print $AssDef['ResultStatus']['value']; ?>'><?php print $AssDef['ResultStatus']['value']; ?></td>
			<td><?php print $AssDef['Description']['value']; ?></td>
			<td><?php print $AssDef['Element']['value']; ?></td>
		  </tr>
		  <tr class="hidden" id="<?php print $eid.'-'.$rown++;?>">
			<td colspan="7">
			  <p>
				<value id="<?php print $eid;?>-Markcount"><?php print
						 $AssDef['MarkCount']['value'];?></value> 
				<?php print_string('markbookcolumns',$book);?>
				<?php print_string('scoresentered',$book);?>
				<value id="<?php print $eid;?>-Archivecount">
				  <?php print $AssDef['ArchiveCount']['value'];?></value>
				(<value id="<?php print $eid;?>-Scorecount"> 
				  <?php print $AssDef['ScoreCount']['value'];?></value>).
			  </p>

<?php
		rowaction_buttonmenu($imagebuttons,$extrabuttons,$book);
?>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$eid;?>" style="display:none;">
<?php
	xmlechoer('AssessmentDefinition',$AssDef);
?>
		  </div>
		</tbody>
<?php
		}
?>
	  </table>
	</div>
  </div>
