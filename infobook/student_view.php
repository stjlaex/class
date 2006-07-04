<?php
/**									student_view.php
 *
 *	A composite view of all informaiton for one sid	
 */

$action='student_view_action.php';

include('scripts/sub_action.php');
twoplus_buttonmenu($sidskey,sizeof($sids));
?>
  <div id="heading">
			  <label><?php print_string('student'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
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
			  <label><?php print_string($Student['DisplayFullName']['label'],$book); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
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
<?php
	$Backgrounds=(array)$Student['Backgrounds'];
	while(list($tagname,$Ents)=each($Backgrounds)){
?>
		  <tr>
			<th>
			  <a
	  href="infobook.php?current=ents_list.php&cancel=student_view.php&tagname=<?php print $tagname;?>"><?php print_string(strtolower($tagname),$book); ?>
			  </a>
			</th>
<?php
		$no=sizeof($Ents);
		if(is_array($Ents[0])){
			print '<td>'.$no.'</td>';
			$Ent=$Ents[0];
			print '<td>'.$Ent['EntryDate']['value'].'</td>';
			$out=substr($Ent['Detail']['value'],0,30).'...';
			print '<td>'.$out.'</td>';
			}
?>
		  </tr>
<?php
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
		while(list($contactno,$Contact)=each($Contacts)){
			$gid=$Contact['id_db'];
			$relation=displayEnum($Contact['Relationship']['value'],$Contact['Relationship']['field_db']);
?>
			<li id="<?php print 'tinytab-contact-'.$relation;?>"><p 
		<?php if($n==0){ print ' id="current-tinytab" ';}?>
		class="<?php print $relation;?>"
		onclick="tinyTabs(this)"><?php print $relation;?></p></li>

			<div class="hidden" id="tinytab-xml-contact-<?php print $relation;?>">
			  <table class="listmenu">
				<tr>
				  <td>
					<label><?php print_string($Contact['Order']['label'],$book);?></label>
					<?php print $Contact['Order']['value'];?>
				  </td>
				  <td>
			  <a href="infobook.php?current=contact_details.php&cancel=student_view.php&contactno=<?php print $contactno;?>">
				<img class="clicktoedit" title="<?php print_string('edit');?>" />
			  </a>
		<?php print $Contact['Forename']['value'].' '. $Contact['Surname']['value'];?>
				  </td>
				  <td>
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
		$relation='newentry';
?>
			<li id="<?php print 'tinytab-contact-'.$relation;?>"><p 
				<?php if($n==0){ print ' id="current-tinytab" ';}?>
				class="<?php print $relation;?>"
				onclick="tinyTabs(this)"><?php print $relation;?></p></li>
			<div class="hidden" id="tinytab-xml-contact-<?php print $relation;?>">
			  <table class="listmenu">
				<tr>
				  <td>&nbsp
				  </td>
				  <td>
			  <a href="infobook.php?current=contact_details.php&cancel=student_view.php&contactno=-1">
				<img class="clicktoedit" title="<?php print_string('edit');?>" />
			  </a>
				  </td>
				  <td>
					<?php print_string('addnewcontact',$book);?>
				  </td>
				</tr>
			  </table>
			</div>

		  </ul>
		</div>
		<div id="tinytab-display-contact" class="tinytab-display">
		</div>
	  </div>

	  <div class="left">
		<fieldset class="left">
		  <legend>
			<?php print_string('reports',$book);?>
		  </legend>
		  <table>
			<tr>
			  <a href="infobook.php?current=student_reports.php&cancel=student_view.php">
				<?php print_string('subjectreports'); ?>
			  </a>
			</tr>
		  </table>
		</fieldset>

		<fieldset class="right">
		  <legend>
		  <a href="infobook.php?current=exclusions_list.php&cancel=student_view.php">
			<img class="clicktoedit" title="<?php print_string('edit');?>" />
		  </a>
			  <?php print_string('exclusions',$book);?>
		  </legend>
<?php	
		if(is_array($Student['Exclusions'][0])){print_string('infoavailable',$book);}
		else{print_string('noinfo',$book);}
?>
		</fieldset>
	  </div>

	  <div class="left">
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
		
		<fieldset class="right">
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
	  </div>

	  <input type="hidden" name="current" value="<?php print $action;?>">
	  <input type="hidden" name="cancel" value="<?php print 'student_list.php';?>">
	  <input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>