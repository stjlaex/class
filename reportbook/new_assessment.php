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
	  </div>

	  <div class="right">
		<?php 		include('scripts/list_stage.php'); ?>
		<?php 		include('scripts/list_calendar_year.php');?>
	  </div>

	  <div class="left">
		<label><?php print_string('create',$book);?></label>
		<?php $xmldate='Creation'; $required='no'; 
			$todate='0000-00-00'; include('scripts/jsdate-form.php');?>
	  </div>
	  <div class="right">
		<label><?php print_string('deadlineforcompletion');?></label>
		<?php $xmldate='Deadline'; $required='no'; include('scripts/jsdate-form.php');?>
	  </div>

	  <div class="left"> 
		<label for="Printlabel"><?php print_string('printlabel',$book);?></label>
		<input class="required" type="text" id="Printlabel" tabindex="<?php print $tab++;?>" 
				name="printlabel"  style="width:20em;" maxlength="59" value="" />
	  </div>


	  <div class="right">
<?php 
		include('scripts/list_subjects.php'); 
?>
	  </div>

	  <div class="right">
<?php 
		include('scripts/list_componentstatus.php'); 
?>
	  </div>


	  <div class="left">
<?php 
		$required='no';
		include('scripts/list_gradescheme.php'); 
?>
	  </div>

	  <div class="left">
<?php
		include('scripts/list_method.php'); 
?>
	  </div>

	  <div class="right">
<?php
		include('scripts/list_resultqualifier.php'); 
?>
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
			<th><?php print_string('status');?></th>
			<th><?php print_string('description');?></th>
			<th><?php print_string('elementid',$book);?></th>
		  </tr>
		</thead>
<?php
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
			<td class='<?php print $AssDef['ResultStatus']['value']; ?>'><?php print $AssDef['ResultStatus']['value']; ?></td>
			<td><?php print $AssDef['Description']['value']; ?></td>
			<td><?php print $AssDef['Element']['value']; ?></td>
		  </tr>
		  <tr class="hidden" id="<?php print $eid.'-'.$rown++;?>">
			<td colspan="6">
			  <p>
				<value id="<?php print $eid;?>-MarkCount"><?php print
						 $AssDef['MarkCount']['value'];?></value> 
				<?php print_string('markbookcolumns',$book);?>
				<value id="<?php print $eid;?>-ScoreCount"> 
				  <?php print $AssDef['ScoreCount']['value'];?></value>. 
				<?php print_string('scoresentered',$book);?>
				<value id="<?php print $eid;?>-ArchiveCount">
				  <?php print $AssDef['ArchiveCount']['value'];?></value>
			  </p>
			  <button class="rowaction" title="Delete this assessment"
				name="current" value="delete_assessment.php" onClick="clickToAction(this)">
				<img class="clicktodelete" />
			  </button>
			  <button class="rowaction" title="Edit" name="Edit" onClick="clickToAction(this)">
				<img class="clicktoedit" />
			  </button>
			  <button class="rowaction" title="Delete MarkBook columns" name="current" 
				value="delete_assessment_columns.php" onClick="clickToAction(this)">
				<?php print_string('deletecolumns',$book);?>
			  </button>
			  <button class="rowaction" title="Generate MarkBook columns" name="current" 
				value="generate_assessment_columns.php" onClick="clickToAction(this)">
				<?php print_string('generatecolumns',$book);?>
			  </button>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$eid;?>" style="display:none;">
<?php
	xmlpreparer('AssessmentDefinition',$AssDef);
?>
		  </div>
		</tbody>
<?php
		}
?>
	  </table>
	</div>
  </div>	
