<?php
/**									student_view.php
 *
 *	A composite view of all informaiton given one sid	
 */

$action='student_view_action.php';

include('scripts/sub_action.php');
twoplus_buttonmenu($sidskey,sizeof($sids));
?>
  <div id="heading">
			  <label><?php print_string('student'); ?></label>
			<?php print $Student['Forename']['value'].' '. $Student['Surname']['value'].' '.
			  $Student['MiddleNames']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="center">
		<table class="listmenu">
		  <caption>
			<a href="infobook.php?current=student_view_student.php&cancel=student_view.php">
			<img class="clicktoedit" title="<?php print_string('edit');?>" />
			</a>
			<?php print_string('studentdetails',$book); ?>
		  </caption>
		  <tr>
			<td>
			  <label><?php print_string($Student['Forename']['label'],$book); ?></label>
			<?php print $Student['Forename']['value'].' '. $Student['Surname']['value'].' '.
			  $Student['MiddleNames']['value'];?>
			</td>
			<td>
			<label><?php print_string($Student['RegistrationGroup']['label'],$book);?></label>
			  <?php print $Student['RegistrationGroup']['value'];?>
			</td>
		  </tr>
		  <tr>
			<td><label><?php print_string($Student['DOB']['label'],$book);?></label>
			  <?php print $Student['DOB']['value'];?>
			</td>
			<td>
			<label><?php print_string($Student['EntryDate']['label'],$book);?></label>
			  <?php print $Student['EntryDate']['value'];?>
			</td>
		  </tr>
		  <tr>
			<td>
			  <label><?php print_string($Student['TransportMode']['label'],$book);?></label> 
		  <?php displayEnum($Student['TransportMode']['value'],$Student['TransportMode']['field_db']);?>
			</td>
			<td>
			  <label><?php print_string($Student['TransportRoute']['label'],$book);?></label>
			  <?php print $Student['TransportRoute']['value'];?>
			</td>
		  </tr>
<?php if ($Student['Boarder']['value']!='N'){ ?>
			<tr><td><label><?php print_string($Student['Boarder']['label'],$book);?></label></td></tr>
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
			  <a href="infobook.php?current=comments_list.php&cancel=student_view.php">
				<?php print_string('comments'); ?>
			  </a>
			</th>
<?php
	$date='';
	$Comments=fetchComments($sid,$date,'');
	$Student['Comments']=$Comments;
	$no=sizeof($Comments);
	if(is_array($Comments[0])){
		print '<td>'.$no.'</td>';
		$Comment=$Comments[0];
		print '<td>'.$Comment['EntryDate']['value'].'</td>';
		$out=substr($Comment['Detail']['value'],0,30).'...';
		print '<td>'.$out.'</td>';
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
	$Incidents=$Student['Incidents'];
	$no=sizeof($Incidents);
	
	if(is_array($Incidents[0])){
		print '<td>'.$no.'</td>';
		$Incident=$Incidents[0];
		print '<td>'.$Incident['EntryDate']['value'].'</td>';
		$out=substr($Incident['Detail']['value'],0,40).'...';
		print '<td>'.$out.'</td>';
		}
?>
		  </tr>
		  <tr>
			<th>
			  <a href="infobook.php?current=student_reports.php&cancel=student_view.php">
				<?php print_string('subjectreports'); ?>
			  </a>
			</th>
<?php
	$date='';
/******************this is to be written!!!!!**
	$Reports=fetchReports($sid,$date);
	$Student['Comments']=$Comments;
	$no=sizeof($Comments);
	if(is_array($Comments[0])){
		print "<td>".$no."</td>";
		$Comment=$Comments[0];
		print "<td>".$Comment['EntryDate']['value']."</td>";
		$out=substr($Comment['Detail']['value'],0,30)."...";
		print "<td>".$out."</td>";
		}
*/
?>
		  </tr>
		  <tr>
			<th>
			  <a href="infobook.php?current=ents_list.php&table=exclusions&title=Exclusions&cancel=student_view.php">
				<?php print_string('exclusions',$book); ?>
			  </a>
			</th>
<?php	
	$Exclusions=$Student['Exclusions'];
	$no=sizeof($Exclusions);
	if(is_array($Exclusions[0])){
		print '<td>'.$no.'</td>';
		$Exclusion=$Exclusions[0];
		print '<td>'.$Exclusion['StartDate']['value'].'</td>';
		$out=substr($Exclusion['Reason']['value'],0,30).'...';
		print '<td>'.$out.'</td>';
		}
?>
		  </tr>
		  <tr>
			<th>
			  <a href="infobook.php?current=ents_list.php&cancel=student_view.php&table=prizes&title=Prizes">
				<?php print_string('prizes',$book); ?>
			  </a>
			</th>
<?php	
	$Prizes=$Student['Prizes'];
	$no=sizeof($Prizes);
	if(is_array($Prizes[0])){
		print '<td>'.$no.'</td>';
		$Prize=$Prizes[0];
		print '<td>'.$Prize['NCyear']['value'].'</td>';
		$out=substr($Prize['Detail']['value'],0,30).'...';
		print '<td>'.$out.'</td>';
		}
?>
		  </tr>
		  <tr>
			<th>
			  <a href="infobook.php?current=ents_list.php&cancel=student_view.php&table=fails&title=Fails"> 
				<?php print_string('fails',$book); ?>
			  </a>
			</th>
<?php	
	$Fails=$Student['Fails'];
	$no=sizeof($Fails);
	if(is_array($Fails[0])){
		print '<td>'.$no.'</td>';
		$Fail=$Fails[0];
		print '<td>'.$Fail['NCyear']['value'].'</td>';
		print '<td>'.$Fail['Subject']['value'].'</td>';
		}
?>
		  </tr>
		</table>
	  </div>


<?php 	
	$Contacts=(array)$Student['Contacts'];
?>
	  <div class="right">
		<table class="listmenu">
		  <caption><?php print_string('contacts',$book);?></caption>
<?php
	while(list($contactno,$Contact)=each($Contacts)){
		$gid=$Contact['id_db'];
?>
		  <tr>
			<td>
			  <label><?php print_string($Contact['Order']['label'],$book);?></label>
			       <?php print $Contact['Order']['value'];?>
			</td>
			<td>
			  <a href="infobook.php?current=student_view_contact.php&cancel=student_view.php&contactno=<?php print $contactno;?>">
				<img class="clicktoedit" title="<?php print_string('edit');?>" />
			  </a>
		<?php print $Contact['Forename']['value'].' '. $Contact['Surname']['value'];?>
			</td>
			<td>
					<?php print displayEnum($Contact['Relationship']['value'],$Contact['Relationship']['field_db']);?>
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
<?php
		}
?>
		</table>
	  </div>


	  <fieldset class="left">
		<legend>
		  <a href="infobook.php?current=ents_list.php&cancel=student_view.php&table=background&title=Backgrounds">
			<img class="clicktoedit" title="<?php print_string('edit');?>" />
		  </a>
		  <?php print_string('background',$book);?>
		</legend>
		<table>
		  <tr>
<?php	
	$Backgrounds=$Student['Backgrounds'];
	$no=sizeof($Backgrounds);
	if(is_array($Backgrounds[0])){
		$Background=$Backgrounds[0];
		print '<td>Last entry<br />'.$Background['EntryDate']['value'].'</td>';
		}
	else{print_string('noinfo',$book);}
?>
		  </tr>
		</table>
	  </fieldset>

	  <fieldset class="left">
		<legend>
		  <a href="infobook.php?current=student_view_medical.php&cancel=student_view.php">
			<img class="clicktoedit" title="<?php print_string('edit');?>" />
		  </a>
		  <?php print_string('medical',$book);?>
		</legend>	
<?php	
		if($Student['MedicalFlag']['value']=='Y'){print_string('infoavailable',$book);}
		else{print_string('noinfo',$book);}
?>
	  </fieldset>

	  <fieldset class="left">
		<legend>
		  <a href="infobook.php?current=student_view_sen.php&cancel=student_view.php">
			<img class="clicktoedit" title="<?php print_string('edit');?>" />
		  </a>
		<?php print_string('sen',$book);?></legend>
<?php	
		if($Student['SENFlag']['value']=='Y'){print_string('infoavailable',$book);}
		else{print 'Not SEN';}
?>
	  </fieldset>


	  <input type="hidden" name="current" value="<?php print $action;?>">
	  <input type="hidden" name="cancel" value="<?php print 'student_list.php';?>">
	  <input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>

