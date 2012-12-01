<?php
/**                                  ents_list.php    
 */

$action='ents_list_action.php';
$cancel='student_view.php';

if(isset($_GET['tagname'])){$tagname=$_GET['tagname'];}
elseif(isset($_POST['tagname'])){$tagname=$_POST['tagname'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}

$Backgrounds=(array)fetchBackgrounds($sid);
$aperm=get_admin_perm('s',get_uid($tid));// special access to reserved information
$perm=getYearPerm($Student['YearGroup']['value'], $respons);

$extrabuttons=array();
$extrabuttons['addnew']=array('name'=>'current','value'=>'ents_new.php');
two_buttonmenu($extrabuttons);
?>
  <div id="heading">
	<label><?php print_string('student',$book);?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	<div class="center">
<?php

$imagebuttons=array();
$extrabuttons=array();
/*the rowaction buttons used within each assessments table row*/
$imagebuttons['clicktodelete']=array('name'=>'current',
									 'value'=>'delete_background.php',
									 'title'=>'delete');
$extrabuttons['edit']=array('name'=>'process',
							'value'=>'edit',
							'title'=>'edit');



$Entries=$Backgrounds["$tagname"];
$entryno=0;

if(is_array($Entries)){

	$currentyid='';
	foreach($Entries as $key => $entry){

		/* Display the entries grouped by year group. */
		if(!isset($startyid)){
			$startyid=$entry['YearGroup']['value'];
			$currentyid=$startyid;
			$opencontainer=uniqid();
			$containerno=0;
			}
		elseif($entry['YearGroup']['value']!=$currentyid){
			html_table_container_close(1);
			$opencontainer=uniqid();
			$containerno++;
			}
		else{
			$opencontainer=0;
			}

		if($containerno<1){$containerclass='rowminus';}
		else{$containerclass='rowplus';}
		if($containerclass=='rowplus'){$hidden='hidden';}
		else{$hidden='revealed';}

		html_table_container_open($opencontainer,$containerclass,get_yeargroupname($currentyid).' - '.get_string(strtolower($tagname),$book));

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
		  <tr class="<?php print $containerclass;?>" onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp;</th>
<?php 
		   if(isset($entry['EntryDate']['value'])){print '<td>'.display_date($entry['EntryDate']['value']).'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['Subject']['value'])){print '<td>'.$entry['Subject']['value'].'</td>';}
		   else{print'<td></td>';}
?>
		  </tr>
		  <tr class="<?php print $hidden;?>" id="<?php print $entryno.'-'.$rown++;?>">
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
			  <div class="listmenu fileupload">
<?php
		   require_once('lib/eportfolio_functions.php');
		   $files=(array)list_files($Student['EPFUsername']['value'],$tagname,$entry['id_db']);
		   html_document_list($files);
?>
			  </div>
<?php
		   if(($perm['x']==1 and !$restricted) or ($restricted and $aperm==1) or $entry['Teacher']['username']==$tid){
			   rowaction_buttonmenu($imagebuttons,$extrabuttons,$book);
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
if($tagname=='Background' and $CFG->enrol_assess=='yesssss'){
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

	html_table_container_close(1);
?>


	  <input type="hidden" name="tagname" value="<?php print $tagname;?>"/>
	  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	</form>
  </div>
