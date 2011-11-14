<?php
/**                                  sen_view.php
 */

/*Have to be careful to check current as this can be called from the */
/* InfoBook too.*/
if($current=='sen_view.php'){$action='sen_view_action.php';}
if(!isset($cancel)){$cancel='';}
if(!isset($selbid)){$selbid='G';}
?>
  <div id="heading"><label><?php print_string('senprofile',$book);?></label>
  <?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>
<?php 

	three_buttonmenu(array('removesen'=>array('name'=>'sub','value'=>'senstatus')));
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <fieldset class="left divgroup">
		<legend><?php print get_string('internal',$book).' '.get_string('assessment',$book);?></legend>

		<div class="center">
		  <label for="Start Date">
			<?php print_string($SEN['StartDate']['label'],$book);?>
		  </label>
<?php
		$todate=$SEN['StartDate']['value'];
		include('scripts/jsdate-form.php');
?>
		</div>

		<div class="center">
		  <label>
			<?php print_string($SEN['NextReviewDate']['label'],$book);?>
		  </label>
<?php
		$todate=$SEN['NextReviewDate']['value'];
		include('scripts/jsdate-form.php');
?>
		</div>


		<ol>
<?php
	/* Allow up to 3 records with blanks for new entries*/
	while(sizeof($SEN['SENinternaltypes']['SENtype'])<3){
		$SEN['SENinternaltypes']['SENtype'][]=fetchSENtype();
		}
	$asscode='I';
	foreach($SEN['SENinternaltypes']['SENtype'] as $index => $SENtype){
		$entryn=$index+1;
		$enum=getEnumArray($SENtype['SENtype']['field_db']);
		print '<li><select id="Type"  tabindex="'.$tab++.'"
			name="'.$asscode. $SENtype['SENtype']['field_db'].$entryn.'">';
		print '<option value=""></option>';
		while(list($inval,$description)=each($enum)){ 
			print '<option ';
			if($SENtype['SENtype']['value']==$inval){print 'selected="selected" ';}
			print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
			}
		print '</select>';

		$enum=getEnumArray($SENtype['SENtypeRank']['field_db']);
		print '<select id="Rank"  tabindex="'.$tab++.'" 
			name="'.$asscode. $SENtype['SENtypeRank']['field_db'].$entryn.'" size="1">';
		print '<option value=""></option>';		
		while(list($inval,$description)=each($enum)){	
			print '<option ';
			if($SENtype['SENtypeRank']['value']==$inval){print "selected='selected'";}
			print " value='".$inval."'>".$description."</option>";
			}
		print '</select></li><br />';
		}
?>
		</ol>

	  </fieldset>


	  <div class="right">
		<div class="tinytabs" id="sen">
		  <ul>
<?php
	$key=-1;
	$keybids=array();
	foreach($SEN['Curriculum'] as $key => $Subject){
		if(is_array($Subject)){
			$keybids[$Subject['Subject']['value_db']]=$key;
			}
		}

	if(array_key_exists($selbid,$keybids)){$selkey=$keybids[$selbid];$selbid='';}
	else{$selkey=0;}

	foreach($SEN['Curriculum'] as $key => $Subject){
		if(is_array($Subject)){
?>
			<li id="<?php print 'tinytab-sen-'.$Subject['Subject']['value'];?>"><p 
					 <?php if($key==$selkey){ print ' id="current-tinytab" ';}?>
				class="<?php print $Subject['Subject']['value'];?>"
				onclick="tinyTabs(this)"><?php print $Subject['Subject']['value'];?></p>
			</li>

			<div class="hidden" id="tinytab-xml-sen-<?php print $Subject['Subject']['value'];?>">
			  <table>
				<tr>
				  <td>
<?php
				$cattype='sen';$required='no';
				$listname='extrasupport';
				$selextrasupport=$Subject['ExtraSupport']['value_db'];
				$listlabel=$Subject['ExtraSupport']['label'];
				include('scripts/list_category.php');
?>
				  </td>
				</tr>
				<tr>
				  <td>
				  <label for="Strengths">
					<?php print_string($Subject['Strengths']['label'],$book); ?>
				  </label>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Stengths" 
				  wrap="on" rows="5" tabindex="<?php print $tab++;?>" 
				  name="<?php print $Subject['Strengths']['field_db'].$key;?>" 
				  ><?php print $Subject['Strengths']['value']; ?></textarea>
				  </td>
				</tr>
				<tr>
				  <td>
				  <label for="Weaknesses">
					<?php print_string($Subject['Weaknesses']['label'],$book); ?>
				  </label>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Weaknesses" 
				  wrap="on" rows="5" tabindex="<?php print $tab++;?>"
				  name="<?php print $Subject['Weaknesses']['field_db'].$key;?>" 
				  ><?php print $Subject['Weaknesses']['value']; ?></textarea>
				  </td>
				</tr>
				<tr>
				  <td>
				  <label for="Strategies">
				  <?php print_string($Subject['Strategies']['label'],$book); ?> 
				  </label>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Strategies" 
				  wrap="on" rows="5" tabindex="<?php print $tab++;?>"
				  name="<?php print $Subject['Strategies']['field_db'].$key;?>" 
				  ><?php print $Subject['Strategies']['value']; ?></textarea>
				  </td>
				</tr>
				<tr>
				  <td>
				  <label for="Targets">
				  <?php print_string($Subject['Targets']['label'],$book); ?> 
				  </label>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Targets" 
				  wrap="on" rows="5" tabindex="<?php print $tab++;?>"
				  name="<?php print $Subject['Targets']['field_db'].$key;?>" 
				  ><?php print $Subject['Targets']['value']; ?></textarea>
				  </td>
				</tr>
			  </table>
			</div>
<?php
			}
		}
		$subject='addsubject';
?>
			<li id="<?php print 'tinytab-sen-'.$subject;?>"><p 
					 <?php if($key==-1){ print ' id="current-tinytab" ';}?>
				class="<?php print $subject;?>"
				onclick="tinyTabs(this)"><?php print_string($subject,$book);?></p></li>

			<div class="hidden" id="tinytab-xml-sen-<?php print $subject;?>">
			  <table>
				<tr>
				  <td>
				  </td>
				</tr>
				<tr>
				  <td>
					<button class="rowaction" 
					  name="ncmod" 
					  value="-1" 
					  onClick="processContent(this);">
					  <?php print_string('addsubject',$book);?>
					</button>
<?php 
   	$d_class=mysql_query("SELECT DISTINCT subject_id, course_id FROM
				class JOIN cidsid ON class.id=cidsid.class_id WHERE
				cidsid.student_id='$sid'");
	$subjects=array();
	while($subject=mysql_fetch_array($d_class,MYSQL_ASSOC)){
		$subbid=$subject['subject_id'];
		$subcrid=$subject['course_id'];
		if(!array_key_exists($subbid,$keybids)){
			$d_subject=mysql_query("SELECT name FROM subject WHERE id='$subbid'");
			$subjectname=mysql_result($d_subject,0);
			$subjects[]=array('id'=>$subbid,'name'=>$subjectname);
			}
		$d_subject=mysql_query("SELECT component.id, subject.name FROM
			  subject JOIN component ON component.id=subject.id WHERE 
			component.subject_id='$subbid' AND 
			component.course_id='$subcrid' ORDER BY subject.name");
		while($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
			if(!array_key_exists($subject['id'],$keybids)){
				$subjects[]=array('id'=>$subject['id'],'name'=>$subject['name']);
				}
			}
		}
	$listname='bid';$listlabel='subject';
	include('scripts/set_list_vars.php');
	list_select_list($subjects,$listoptions,$book);
	unset($listoptions);
?>

				  </td>
				</tr>
				<tr>
				  <td>
				  </td>
				</tr>
			  </table>
			</div>

		  </ul>
		</div>
		<div id="tinytab-display-sen" class="tinytab-display">
		</div>
	  </div>


	  <fieldset class="left divgroup">
		<legend><?php print get_string('external',$book).' '.get_string('assessment',$book);?></legend>

		<div class="center">
		  <label for="Date">
			<?php print_string($SEN['AssessmentDate']['label'],$book);?>
		  </label>
<?php
		$todate=$SEN['AssessmentDate']['value'];
		$required='no';
		include('scripts/jsdate-form.php');
?>
		</div>

		<ol>
<?php
	/* Allow up to 3 records with blanks for new entries*/
	while(sizeof($SEN['SENtypes']['SENtype'])<3){
		$SEN['SENtypes']['SENtype'][]=fetchSENtype();
		}

	$asscode='E';
	foreach($SEN['SENtypes']['SENtype'] as $index => $SENtype){
		$entryn=$index+1;
		$enum=getEnumArray($SENtype['SENtype']['field_db']);
		print '<li><select id="Type"  tabindex="'.$tab++.'"
			name="'.$asscode. $SENtype['SENtype']['field_db'].$entryn.'">';
		print '<option value=""></option>';
		while(list($inval,$description)=each($enum)){ 
			print '<option ';
			if($SENtype['SENtype']['value']==$inval){print 'selected="selected" ';}
			print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
			}
		print '</select>';

		$enum=getEnumArray($SENtype['SENtypeRank']['field_db']);
		print '<select id="Rank"  tabindex="'.$tab++.'" 
			name="'.$asscode. $SENtype['SENtypeRank']['field_db'].$entryn.'" size="1">';
		print '<option value=""></option>';		
		while(list($inval,$description)=each($enum)){	
			print '<option ';
			if($SENtype['SENtypeRank']['value']==$inval){print "selected='selected'";}
			print " value='".$inval."'>".$description."</option>";
			}
		print '</select></li><br />';
		}
?>
		</ol>
	  </fieldset>






 	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="choice" value="<?php print $current;?>"/>
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
	</form>
  </div>
