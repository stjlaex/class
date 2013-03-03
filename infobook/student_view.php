<?php
/**									student_view.php
 *
 *	A composite view of all informaiton for one sid	
 */

$action='student_view_action.php';


$house=get_student_house($sid);
$Siblings=array();
$field=fetchStudent_singlefield($sid,'Course');
$Student=array_merge($Student,$field);

twoplus_buttonmenu($sidskey,sizeof($sids));
?>
  <div id="heading">
	<label><?php print_string('student'); ?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">


	  <div class="center">
<?php
		photo_img($Student['EPFUsername']['value'],$Student['EnrolNumber']['value'],'w');
?>

		<table class="listmenu listinfo">
		  <caption>
			<a href="infobook.php?current=student_view_student.php&cancel=student_view.php">
			<img class="clicktoedit" title="<?php print_string('edit');?>" />
			<?php print_string('studentdetails',$book); ?>
			</a>
		  </caption>
		  <tr>
			<td>
			  <label><?php print_string($Student['Surname']['label'],$book); ?></label>
			  <?php print $Student['Surname']['value'];?>
			  <br />
			  <label><?php print_string($Student['Forename']['label'],$book); ?></label>
			  <?php print $Student['Forename']['value'];?>
			</td>
		  </tr>
		  <tr>
			<td>
			  <label><?php print_string($Student['DOB']['label'],$book);?></label>
			  <?php print display_date($Student['DOB']['value']);?>
			  <br />
			  <label><?php print_string('age',$book);?></label>
			  <?php print get_age($Student['DOB']['value']);?>
			  <br />
			  <label><?php print_string($Student['Gender']['label'],$book);?></label>
			  <?php print_string(displayEnum($Student['Gender']['value'],$Student['Gender']['field_db']),$book);?>
			</td>
		  </tr>
		  <tr>
			<td>
			  <label><?php print_string($Student['TutorGroup']['label'],$book);?></label>
			  <?php print $Student['TutorGroup']['value'];?>
			  <br />
			  <label><?php print_string('formtutor');?></label>
			  <?php 
				$tutoremails='';
				foreach($Student['RegistrationTutor'] as $Tutor){
					print $Tutor['value'].' ';
					$tutoremails.=$Tutor['email'].';';
					}
				emaillink_display($tutoremails);
			  ?>
			  <br />
			  <label><?php print_string($Student['Course']['label'],$book);?></label>
			  <?php print $Student['Course']['value'];?>
			</td>
		  </tr>
		  <tr>
			<td>
			  <label><?php print_string($Student['Nationality']['label'],$book);?></label> 
			  <?php print_string(displayEnum($Student['Nationality']['value'],$Student['Nationality']['field_db']),$book);?>
			  <br />
			  <label><?php print_string($Student['Language']['label'],$book);?></label>
			  <?php print_string(displayEnum($Student['Language']['value'],$Student['Language']['field_db']),$book);?>
			</td>
		  </tr>
		  <tr>
			<td>
			  <label><?php print_string($Student['EnrolNumber']['label'],$book);?></label> 
			  <?php if($_SESSION['role']!='support'){print $Student['EnrolNumber']['value'];}?>
			  <br />
			  <label><?php print_string($Student['EntryDate']['label'],$book);?></label>
			  <?php print display_date($Student['EntryDate']['value']);?>
			</td>
		  </tr>
<?php if($Student['MobilePhone']['value']!=''){ ?>
		  <tr>
			<td>
			  <label><?php print_string($Student['MobilePhone']['label'],$book);?></label>
			  <?php print $Student['MobilePhone']['value'];?>
			</td>
		  </tr>
<?php
			  }
?>
<?php if($house!=''){ ?>
		  <tr>
			<td>
			  <label><?php print_string('house',$book);?></label>
			  <?php print $house;?>
			</td>
		  </tr>
<?php
			  }
?>

		</table>
	  </div>
	
	  <div class="center">
		<table class="listmenu">
		  <caption><?php print_string('studenthistory',$book);?></caption>
		  <tr>
			<th>
			  <a href="infobook.php?current=student_attendance.php&cancel=student_view.php&sid=<?php print $sid;?>">
				<?php print_string('attendance'); ?>
			  </a> 
			</th>
			<td colspan="3">&nbsp;</td>
		  </tr>
		  <tr>
			<th>
			  <a href="infobook.php?current=student_reports.php&cancel=student_view.php">
				<?php print_string('subjectreports'); ?>
			  </a>
			</th>
			<td colspan="3">&nbsp;</td>
		  </tr>

<?php
	if($_SESSION['role']!='office' and $_SESSION['role']!='support'){
?>
		  <tr>
			<th>
			  <a href="infobook.php?current=student_scores.php&cancel=student_view.php&sid=<?php print $sid;?>">
				<?php print_string('assessments'); ?>
			  </a> 
			</th>
			<td colspan="3">&nbsp;</td>
		  </tr>
		  <tr>
			<th>
			  <a href="infobook.php?current=comments_list.php&cancel=student_view.php">
				<?php print_string('comments'); ?>
			  </a>
			</th>
<?php
	$date='';
	$Comments=(array)fetchComments($sid,$date,'');
	$Student['Comments']=$Comments;
	if(array_key_exists('Comment',$Comments)){
		$Comment=$Comments['Comment'][0];
		print '<td><label>'.display_date($Comment['EntryDate']['value']).'</label></td>';
		print '<td>'.substr($Comment['Detail']['value'],0,120).'...'.'</td>';
		}	
	else{
		print '<td colspan="3">&nbsp;</td>';
		}
?>
		  </tr>
		  <tr>
			<th>
			  <a href="infobook.php?current=incidents_list.php&cancel=student_view.php">
				<?php print_string('incidents'); ?>
			  </a>
			</th>
<?php
	$Incidents=(array)fetchIncidents($sid);
	$Student['Incidents']=$Incidents;
	if(array_key_exists(0,$Incidents['Incident'])){
		$Incident=$Incidents['Incident'][0];
		print '<td>'.display_date($Incident['EntryDate']['value']).'</td>';
		print '<td colspan="2">'.substr($Incident['Detail']['value'],0,60).'...'.'</td>';
		}
	else{
		print '<td colspan="3">&nbsp;</td>';
		}
?>
		  </tr>
		  <tr>
			<th>
			  <a href="infobook.php?current=targets_list.php&cancel=student_view.php"><?php print_string('targets',$book); ?>
			  </a>
			</th>
<?php
		$Targets=(array)fetchTargets($sid);
		$detail='';
		$date='';
		foreach($Targets['Target'] as $Target){
			if($Target['Detail']['value_db']!='' and strtotime($date) < strtotime($Target['EntryDate']['value'])){
				$detail=substr($Target['Detail']['value_db'],0,120);
				$date=display_date($Target['EntryDate']['value']);
				}
			}
		if($detail!=''){
			print '<td><label>'.$date.'</label></td>';
			print '<td colspan="2">'.$detail.'...'.'</td>';
			}
		else{
			print '<td colspan="3">&nbsp;</td>';
			}
?>
		  </tr>
<?php
	$Backgrounds=(array)fetchBackgrounds($sid);
	foreach($Backgrounds as $tagname => $Ents){
?>
		  <tr>
			<th>
			  <a href="infobook.php?current=ents_list.php&cancel=student_view.php&tagname=<?php print $tagname;?>"><?php print_string(strtolower($tagname),$book); ?>
			  </a>
			</th>
<?php
		/* Ensure private entries for Background are not displayed here! */
		if(array_key_exists(0,$Ents) and !($tagname=='Background' and $Ents[0]['Categories']['Category'][0]['rating']['value']<0)){
			$Ent=$Ents[0];
			print '<td><label>'.display_date($Ent['EntryDate']['value']).'</label></td>';
			print '<td colspan="2">'.substr($Ent['Detail']['value_db'],0,120).'...'.'</td>';
			}
		else{
			print '<td colspan="3">&nbsp;</td>';
			}
?>
		  </tr>
<?php
		}
	}
?>
		</table>
	  </div>

<?php
	$Contacts=(array)$Student['Contacts'];
?>
	  <div class="right">
		<div class="tinytabs" id="contact" style="">
		  <ul>
<?php
		$n=0;
		foreach($Contacts as $contactno => $Contact){
			if($Contact['id_db']!=' '){
				$gid=$Contact['id_db'];
				$relation=displayEnum($Contact['Relationship']['value'],'relationship');

				$Dependents=(array)fetchDependents($gid);
				$Dependents=(array)array_merge($Dependents['Dependents'],$Dependents['Others']);
				if(sizeof($Dependents)>0){
					foreach($Dependents as $depindex=>$Dependent){
						$r=$Dependent['Relationship']['value'];
						/* Check relationship to only list true siblings. */
						if($Dependent['id_db']!=$sid and ($r=='PAF' or $r=='PAM' or $r=='STP')){$Siblings[$Dependent['id_db']]=$Dependent['Student'];}
						}
					}
?>
			<li id="<?php print 'tinytab-contact-'.$relation. $contactno;?>"><p 
					 <?php if($n==0){ print ' id="current-tinytab" ';}?>
				class="<?php print $relation. $contactno;?>"
				onclick="parent.tinyTabs(this)"><?php print_string($relation,$book);?></p></li>

			<div class="hidden" id="tinytab-xml-contact-<?php print $relation. $contactno;?>">
			  <table class="listmenu">
				<tr>
				  <td colspan="2">
					<label><?php print_string($Contact['Order']['label'],$book);?></label>
					<?php print_string(displayEnum($Contact['Order']['value'],'priority'),$book);?>
				  </td>
				</tr>
				<tr>
				  <td style="width:50%;">
					<span title="<?php print $Contact['Note']['value'];?>">
					<a href="infobook.php?current=contact_details.php&cancel=student_view.php&contactno=<?php print $contactno;?>">
					  <img class="clicktoedit" title="<?php print_string('edit');?>" />
					  <?php print $Contact['DisplayFullName']['value'];?>
					</a>
					<?php emaillink_display($Contact['EmailAddress']['value']);?>
				  </td>
				  <td>
<?php
				$Phones=$Contact['Phones'];
				while(list($phoneno,$Phone)=each($Phones)){
					print '<label>'.get_string(displayEnum($Phone['PhoneType']['value'],$Phone['PhoneType']['field_db']),$book).'</label>'.$Phone['PhoneNo']['value'].'<br />';				
					}
?>
				  </td>
				</tr>
			  </table>
			</div>
<?php
				$n++;
				}
			}
		$relation='newcontact';
?>
			<li id="<?php print 'tinytab-contact-'.$relation;?>"><p 
				<?php if($n==0){ print ' id="current-tinytab" ';}?>
				class="<?php print $relation;?>"
				onclick="parent.tinyTabs(this)"><?php print_string($relation,$book);?></p></li>
			<div class="hidden" id="tinytab-xml-contact-<?php print $relation;?>">
			  <table class="listmenu">
				<tr>
				  <tr>
				  <td colspan="3">&nbsp
				  </td>
				</tr>
				  <td>&nbsp
				  </td>
				  <td>
					<a href="infobook.php?current=contact_details.php&cancel=student_view.php&contactno=-1">
					  <img class="clicktoedit" title="<?php print_string('edit');?>" />
					  <?php print_string('addnewcontact',$book);?>
					</a>
				  </td>
				</tr>
			  </table>
			</div>			
		  </ul>
		</div>
		<div id="tinytab-display-contact" class="tinytab-display">
		</div>
	  </div>



<?php
		if($_SESSION['role']!='support'){
?>
	  <div class="left">
		<fieldset class="left">
		  <legend>
			<a href="infobook.php?current=student_view_sen.php&cancel=student_view.php">
			  <img class="clicktoedit" title="<?php print_string('edit');?>" />
			  <?php print_string('sen','seneeds');?>
			</a>
		  </legend>
<?php	
		if($Student['SENFlag']['value']=='Y'){print_string('senprofile','seneeds');}
		else{print_string('noinfo',$book);}
?>
		</fieldset>
		
		<fieldset class="right">
		  <legend>
		  <a href="infobook.php?current=student_view_medical.php&cancel=student_view.php">
			<img class="clicktoedit" title="<?php print_string('edit');?>" />
		    <?php print_string('medical',$book);?>
		  </a>
		  </legend>	
<?php	
		if($Student['MedicalFlag']['value']=='Y'){print_string('infoavailable',$book);}
		else{print_string('noinfo',$book);}
?>
		</fieldset>
	  </div>
	  <div class="left">
<?php
		}
	if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
?>
		<fieldset class="right">
		  <legend>
			<a href="infobook.php?current=student_view_boarder.php&cancel=student_view.php">
			    <img class="clicktoedit" title="<?php print_string('edit');?>" />
				<?php print_string('boarder',$book);?>
			</a>
		  </legend>
<?php 
			if($Student['Boarder']['value']!='N' and $Student['Boarder']['value']!=''){ 
				print '<div>'.get_string(displayEnum($Student['Boarder']['value'],$Student['Boarder']['field_db']),$book).'</div>';}
			else{print_string('noinfo',$book);}
?>
		</fieldset>
<?php
		}

		$transport=display_student_transport($sid);
?>
		
		<fieldset class="left">
		  <legend>
			<a href="infobook.php?current=student_transport.php&cancel=student_view.php">
			  <img class="clicktoedit" title="<?php print_string('edit');?>" />
				<?php print_string('transport','admin');?>
			</a>
		  </legend>
		<div><?php print $transport;?></div>
		</fieldset>

		<fieldset class="right">
		  <legend>
			<a href="infobook.php?current=student_transport.php&cancel=student_view.php">
			  <img class="clicktoedit" title="<?php print_string('edit');?>" />
				<?php print_string('club','admin');?>
			</a>
		  </legend>
		<div><?php print get_student_club($sid);?></div>
		</fieldset>
<?php

/*		<fieldset class="right">
//		  <legend>
//			<a href="infobook.php?current=exclusions_list.php&cancel=student_view.php">
//			  <img class="clicktoedit" title="<?php print_string('edit');?>" />
//				<?php print_string('exclusions',$book);?>
//			</a>
//		  </legend>
//	
//		if(array_key_exists(0,$Student['Exclusions'])){print_string('infoavailable',$book);}
//		else{print_string('noinfo',$book);}
//
//		</fieldset>
*/
?>
  </div>

	  <div class="left">
		<fieldset class="left">
		  <legend>
			<a href="infobook.php?current=student_view_enrolment.php&cancel=student_view.php">
			  <img class="clicktoedit" title="<?php print_string('edit');?>" />
			  <?php print_string('enrolment','admin');?>
			</a>
		  </legend>
		  <div>
<?php 

	print '<label>'.get_string('status','admin').'</label> '. 
		  get_string(displayEnum($Student['EnrolmentStatus']['value'],$Student['EnrolmentStatus']['field_db']),$book);
?>
		  </div>
		</fieldset>
<?php

		if(empty($_SESSION['accessfees'])){
?>
	  <fieldset class="right">
		<legend>
		  <?php print_string('fees','admin');?>
		</legend>
		<input type="password" name="accesstest" maxlength="20" value="" />
		<input type="password" name="accessfees" maxlength="4" value="" />
<?php
			$buttons=array();
			$buttons['access']=array('name'=>'access','value'=>'access');
			all_extrabuttons($buttons,$book,'');
?>
	  </fieldset>
<?php
			}
		else{
			require_once('lib/fetch_fees.php');
			$Account=(array)fetchAccount($gid);
?>
		<fieldset class="right">
		  <legend>
			<a href="infobook.php?current=student_fees.php&cancel=student_view.php">
			  <img class="clicktoedit" title="<?php print_string('edit');?>" />
			  <?php print_string('fees','admin');?>
			</a>
		  </legend>
		  <p></p>
		</fieldset>
<?php
			}
?>

	  </div>




	  <div class="right">
		  <table class="listmenu listinfo">
			<caption><?php print_string('siblings',$book);?></caption>
			<tr>
			  <td>
			  </td>
			</tr>
<?php
		foreach($Siblings as $Sibling){
?>
					<tr>
					  <td style="padding:5px 2px 2px 6px;">
						  <a href="infobook.php?current=student_view.php&cancel=contact_list.php&sid=<?php print $Sibling['id_db'];?>&sids[]=<?php print $Sibling['id_db'];?>">
							<?php print $Sibling['DisplayFullName']['value']; ?></a><?php print ' ('.$Sibling['TutorGroup']['value'].')';?>
					  </td>
					</tr>
<?php
			}
?>
		  </table>
</div>
	  </div>



  <input type="hidden" name="current" value="<?php print $action;?>" />
  <input type="hidden" name="cancel" value="<?php print 'student_list.php';?>" />
  <input type="hidden" name="choice" value="<?php print $choice;?>" />
</form>
</div>

