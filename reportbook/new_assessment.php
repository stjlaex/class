<?php
/**											new_assessment.php
 *
 */

$action='new_assessment_action.php';
$choice='new_assessment.php';

$toyear=get_curriculumyear();
if(isset($_POST['curryear']) and $_POST['curryear']!=''){$curryear=$_POST['curryear'];}
else{$curryear=$toyear;}
if(isset($_POST['profid']) and $_POST['profid']!=''){$profid=$_POST['profid'];}
else{$profid='';}

include('scripts/course_respon.php');

/* TODO: remove this fully or update?
 */
//$extrabuttons['importfromfile']=array('name'=>'current','value'=>'new_assessment_import.php');

three_buttonmenu($extrabuttons);
?>
  <div class="topform divgroup">
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
<?php
		include('scripts/list_resultstatus.php');
?>
	  </div>

	  <div class="right">
<?php
		$selstage='%';
 		include('scripts/list_stage.php');

		$selbid='%';
		include('scripts/list_subjects.php');
?>
		<div class="left">
<?php
			$selcomponentstatus='None';
		include('scripts/list_componentstatus.php');
?>
		</div>
		<div class="right">
<?php
			$selstrandstatus='None';
		include('scripts/list_strandstatus.php');
?>
		</div>
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
//include('scripts/list_method.php'); 
//include('scripts/list_resultqualifier.php'); 
		$required='no';
		include('scripts/list_gradescheme.php');
?>

		<label for="Derivation"><?php print_string('derivation',$book);?></label>
		<input type="text" id="Derivation" tabindex="<?php print $tab++;?>" 
				name="derivation" style="width:12em;" maxlength="59" value="" />
<?php 
		$listname='newprofid';
		$selnewprofid=$profid;
		$required='no';
		include('scripts/list_assessment_profile.php');
?>
	  </div>


	  <input type="text" style="display:none;" id="Id_db" name="id" value="" />
	  <input type="hidden" name="profid" value="<?php print $profid;?>" />
	  <input type="hidden" name="curryear" value="<?php print $curryear;?>" />
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
			<th colspan="2">
			  <div class="left">
<?php 
		$listname='curryear';
		$onchange='yes';
		include('scripts/list_calendar_year.php');
?>
			  </div>
			  <div class="right">
<?php 
		$listname='profid';
		$onchange='yes';
		include('scripts/list_assessment_profile.php');
?>
			  </div>
			</th>
			<th><?php print_string('stage');?></th>
			<th><?php print_string('subject');?></th>
			<th><?php print_string('status');?></th>
			<th><?php print_string('element',$book);?></th>
		  </tr>
		</thead>
<?php

	$imagebuttons=array();
	/*the rowaction buttons used within each assessments table row*/
	$imagebuttons['clicktodelete']=array('name'=>'current',
										 'value'=>'delete_assessment.php',
										 'title'=>'delete');
	$imagebuttons['clicktoedit']=array('name'=>'Edit',
									   'value'=>'',
									   'title'=>'edit');

	$cohort=array('id'=>'','course_id'=>$rcrid,'stage'=>'%','year'=>$curryear);
	$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$profid);
	foreach($AssDefs as $AssDef){
		$eid=$AssDef['id_db'];
		$AssCount=fetchAssessmentCount($eid);
		$rown=0;
		if(isset($AssDef['Derivation']['value'][0]) 
		   and $AssDef['Derivation']['value'][0]!=' '){$AssDef['ResultStatus']['value']='E';}
		else{$rowclass='';}
?>
		<tbody id="<?php print $eid;?>">
		  <tr class="rowplus"  
					onClick="clickToReveal(this)" id="<?php print $eid.'-'.$rown++;?>">
			<th>&nbsp</th>
			<td><?php print $AssDef['Description']['value']; ?></td>
					<td style="font-style:italic;"><?php print display_date($AssDef['Deadline']['value']); ?></td>
			<td><?php print $AssDef['Stage']['value']; ?></td>
			<td><?php print $AssDef['Subject']['value']; ?></td>
			<td class="<?php print $AssDef['ResultStatus']['value']; ?>"><?php print $AssDef['ResultStatus']['value']; ?></td>
			<td><?php print $AssDef['Element']['value']; ?></td>
		  </tr>
		  <tr class="hidden" id="<?php print $eid.'-'.$rown++;?>">
			<td colspan="7">
			  <p>
				<?php print_string('statistics',$book);?>
				<value id="<?php print $eid;?>-Statistics"><?php print
					$AssDef['Statistics']['value'];?></value>.&nbsp;
				<?php print_string('markbookcolumns',$book);?>
				<value id="<?php print $eid;?>-Markcount"><?php print
						 $AssCount['MarkCount']['value'];?></value>.&nbsp;
			<a href="reportbook.php?current=edit_scores.php&cancel=new_assessment.php&eid=<?php print $eid;?>&curryear=<?php print $curryear;?>&profid=<?php print $profid;?>&pid=&bid="><?php print_string('scoresentered',$book);?>				
				<value id="<?php print $eid;?>-Archivecount">
				  <?php print $AssCount['ArchiveCount']['value'];?></value>
<!-- With multiple MarkBook years this now misleading...
				(<value id="<?php print $eid;?>-Scorecount"> 
				  <?php print $AssCount['ScoreCount']['value'];?></value>).
-->
				</a>
			  </p>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $eid.'-'.$rown++;?>">
			<td colspan="7">
<?php
		$extrabuttons=array();
		$extrabuttons['generatecolumns']=array('name'=>'current',
											   'title'=>'generatecolumns',
											   'value'=>'generate_assessment_columns.php');
		$extrabuttons['deletecolumns']=array('name'=>'current',
											 'title'=>'deletecolumns',
											 'value'=>'delete_assessment_columns.php');
		if(!isset($AssDef['Derivation']['value'][0]) or (isset($AssDef['Derivation']['value'][0]) and ($AssDef['Derivation']['value'][0]==' ' or $AssDef['Derivation']['value'][0]==''))){
			$extrabuttons['statistics']=array('name'=>'current',
											  'title'=>'updatestatistics',
											  'value'=>'calculate_assessment_statistics.php');
			}
		elseif(isset($AssDef['Derivation']['value'][0]) and $AssDef['Derivation']['value'][0]=='R'){
			$extrabuttons['rank']=array('name'=>'current',
										'title'=>'updateranking',
										'value'=>'calculate_assessment_ranking.php');
			}

		/*Check user has permission to configure*/
		$perm=getCoursePerm($rcrid,$respons);
		$neededperm='x';
		if($perm["$neededperm"]==1){
			rowaction_buttonmenu($imagebuttons,$extrabuttons,$book);
			}
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
