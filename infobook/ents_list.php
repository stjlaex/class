<?php
/**                                  ents_list.php    
 */

$action='ents_list_action.php';
$cancel='student_view.php';

if(isset($_GET['tagname'])){$tagname=$_GET['tagname'];}
elseif(isset($_POST['tagname'])){$tagname=$_POST['tagname'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}

$Backgrounds=(array)fetchBackgrounds($sid);
$aperm=get_admin_perm('s',get_uid($tid));
$perm=getYearPerm($Student['YearGroup']['value'], $respons);

$imagebuttons=array();
/*the rowaction buttons used within each assessments table row*/
$imagebuttons['clicktodelete']=array('name'=>'current',
									 'value'=>'delete_background.php',
									 'title'=>'delete');
$imagebuttons['clicktoedit']=array('name'=>'Edit',
								   'value'=>'',
								   'title'=>'edit');


three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('student',$book);?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>

  <div class="topform divgroup">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="left">
		<label for="Detail"><?php print_string('details',$book);?></label>
		<textarea name="detail" id="Detail" tabindex="<?php print $tab++;?>"
		  class="required" rows="5" cols="30"></textarea>
	  </div>
<?php
if($tagname=='Background'){
?>
	  <div class="right" >
<?php 
		$cattype='bac'; $required='yes'; $listlabel='source';
		$listname='catid'; $listid='Category'; include('scripts/list_category.php'); 
?>
	  </div>

	  <div class="right" >
		<?php $xmldate='Entrydate'; $required='yes'; include('scripts/jsdate-form.php'); ?>
	  </div>

	  <div class="right" >
		<?php $listid='yeargroup'; $newyid=$Student['YearGroup']['value']; include('scripts/list_year.php'); ?>
	  </div>
<?php
   	}
else{
?>
	  <div class="right">
		<label><?php print_string('subjectspecific');?></label>
<?php 
		$required='no'; $listname='bid'; $listid='subject';$listlabel='';
		$subjects=list_student_subjects($sid);
		include('scripts/set_list_vars.php');
		list_select_list($subjects,$listoptions,$book);
		unset($listoptions);
?>
	  </div>

	  <div class="right">
		<?php $xmldate='Entrydate'; $required='yes'; include('scripts/jsdate-form.php'); ?>
	  </div>
<?php 
	}
?>
	<input type="text" style="display:none;" id="Id_db" name="id_db" value="" />
	<input type="hidden" name="tagname" value="<?php print $tagname;?>"/>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
 	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
  </form>
  </div>

  <div class="content">
	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string(strtolower($tagname),$book);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print_string('yeargroup');?></th>
			<th><?php print_string('date');?></th>
			<th><?php print_string('subject');?></th>
		  </tr>
		</thead>
<?php
	$Entries=$Backgrounds["$tagname"];
	$entryno=0;
	if(is_array($Entries)){
	while(list($key,$entry)=each($Entries)){
		$restricted=false;
		if($tagname=='Background' and $entry['Categories']['Category'][0]['rating']['value']<0){$restricted=true;}

		if($restricted and $aperm!=1 and $entry['Teacher']['username']!=$tid){
			$entry['Detail']['value']='Confidential';$entry['Detail']['value_db']='Confidential';
			}

		if(is_array($entry)){
			$rown=0;
			$entryno=$entry['id_db'];
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp;</th>
<?php 
		   if(isset($entry['YearGroup']['value'])){print '<td>'.get_yeargroupname($entry['YearGroup']['value']).'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['EntryDate']['value'])){print '<td>'.display_date($entry['EntryDate']['value']).'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['Subject']['value'])){print '<td>'.get_subjectname($entry['Subject']['value']).'</td>';}
		   else{print'<td></td>';}
?>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="6">
			  <p>
<?php
		   if(isset($entry['Detail']['value_db'])){
					print $entry['Detail']['value_db'];
					}
		   elseif(isset($entry['Detail']['value'])){
					print $entry['Detail']['value'];
					}
		   if(isset($entry['Teacher']['value'])){print '  - '.$entry['Teacher']['value'];}
?>
			  </p>
<?php
		   if(($perm['x']==1 and !$restricted) or ($restricted and $aperm==1) or $entry['Teacher']['username']==$tid){
			   rowaction_buttonmenu($imagebuttons,'',$book);
			   }
?>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				xmlechoer("$tagname",$entry);
?>
		  </div>
		</tbody>
<?php
				}
			}
		}
	if($tagname=='Background' and $CFG->enrol_assess=='yes'){
		$entryno++;
		$rown=0;
		$EnrolNotes=fetchBackgrounds_Entries($sid,'ena');
		$EnrolAssDefs=fetch_enrolmentAssessmentDefinitions();
		$AssDef=$EnrolAssDefs[0];
		$Assessments=(array)fetchAssessments_short($sid,$AssDef['id_db'],'G');		
		if(sizeof($Assessments)>0){$result=$Assessments[0]['Result']['value'];}
		else{$result='';}
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" 
									id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp;</th>
			<td><?php print get_string('enrolment','admin'). 
			  ' '.get_string('assessment',$book).': '.$result;?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="6">
			  <p>
				 <?php if(isset($EnrolNotes[0])){print $EnrolNotes[0]['Detail']['value'];}?>
			  </p>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
		  </div>
		</tbody>
<?php
			}
?>

		<tr>
		  <td>
		  </td>
		</tr>
	  </table>
	</div>
  </div>
