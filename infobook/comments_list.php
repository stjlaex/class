<?php
/**                                  comments_list.php    
 *
 */

$cancel='student_view.php';
$action='comments_list_action.php';

if(isset($_GET['bid'])){$bid=$_GET['bid'];}
$Comments=fetchComments($sid,'','');
$Student['Comments']=$Comments;
$yid=$Student['YearGroup']['value'];
$perm=getYearPerm($yid,$respons);

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('comments');?></label>
<?php
print $Student['Forename']['value'].' '.$Student['Surname']['value'];
print '('.$Student['RegistrationGroup']['value'].')';
?>
  </div>

  <div class="topform divgroup">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="left">
		<label for="Detail"><?php print_string('details',$book);?></label>
		<textarea  tabindex="<?php print $tab++;?>"
		  name="detail" class="required" id="Detail" rows="3" cols="35"></textarea>
	  </div>
	  <div class="right">
		<label for="Subject"><?php print_string('subjectspecificoptional',$book);?></label>
<?php 
		$required='no'; $listname='bid'; $listid='subject';$listlabel='';
		$subjects=list_student_subjects($sid);
		include('scripts/set_list_vars.php');
		list_select_list($subjects,$listoptions,$book);
		unset($listoptions);
?>
	  </div>
	  <div class="right">
		<?php 
		$listlabel='category'; $listid='category';
		$required='yes'; 
		include('scripts/list_category.php');
		?>
	  </div>
	  <div class="left">
<?php 
			$xmldate='Entrydate'; 
			$required='yes'; 
			include('scripts/jsdate-form.php'); 
?>
	  </div>

	  <div class="left">
<?php 
				
	if(($CFG->emailguardiancomments=='yes' or $CFG->emailguardiancomments=='epf') and $perm['x']==1){
		$checkname='guardianemail';$checkcaption=get_string('sharewithguardian',$book);
		$checkalert=get_string('sharecommentalert',$book);
		include('scripts/check_yesno.php');
		unset($checkalert);
		}
?>
	  </div>

	  <div class="right">
<?php 
	  												 
	if($CFG->emailcomments=='yes'){
		$checkname='teacheremail';$checkcaption=get_string('emailtoteachers',$book);
		include('scripts/check_yesno.php'); 
		}
?>
	  </div>

	  <div class="right">
		<?php $newyid=$Student['YearGroup']['value']; // include('scripts/list_year.php'); ?>
	  </div>

	  <input type="text" style="display:none;" id="Id_db" name="id_db" value="" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'student_view.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>

  <div id="viewcontent" class="content">
	<div class="center">
	  <table class="listmenu">
	  <caption><?php print_string('entries',$book);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print_string('yeargroup');?></th>
			<th><?php print_string('date');?></th>
			<th><?php print_string('subject');?></th>
			<th colspan="2"><?php print_string('category');?></th>
		  </tr>
		</thead>
<?php
	if(is_array($Student['Comments']['Comment'])){
		reset($Student['Comments']['Comment']);
		while(list($key,$entry)=each($Student['Comments']['Comment'])){
			if(is_array($entry)){
				$rown=0;
				$entryno=$entry['id_db'];
				if(isset($entry['Shared']['value']) and $entry['Shared']['value']=='1'){
					$shared=true;
					}
				else{
					$shared=false;
					}

?>
		<tbody id="<?php print $entryno;?>">
				<tr <?php if(!$shared){print 'class="rowplus" onClick="clickToReveal(this)"';}?> id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp</th>
<?php 
		   if(isset($entry['YearGroup']['value'])){print '<td>'.$entry['YearGroup']['value'].'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['EntryDate']['value'])){print '<td>'.$entry['EntryDate']['value'].'</td>';}
		   else{print'<td></td>';}
		   if(isset($entry['Subject']['value'])){print '<td>'.$entry['Subject']['value'].'</td>';}
		   else{print'<td></td>';}
?>
			 <td>
<?php
		   foreach($entry['Categories']['Category'] as $category){
			   if($category['rating']['value']==-1){print '<div class="negative">';}
			   else{print'<div style="float:left;padding:0 6px 0 6px;" class="positive">';}
			   print $category['label'].'</div>';
			   }
?>
			</td>
		  </tr>
		  <tr <?php if($shared){print 'class="revealed"';}else{print 'class="hidden"';}?> id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="6">
			  <p>
<?php
		   if($shared){
			   print '<label>'.get_string('sharedwithguardians',$book).': </label>';
			   }
		   if(isset($entry['Detail']['value'])){
			   print $entry['Detail']['value'];
			   }
		   if(isset($entry['Teacher']['value'])){
			   print '  - '.$entry['Teacher']['value'];
			   }
?>
			  </p>

<?php
		   if($shared){
			   /* TODO: display parent's cofirmation*/
			   print '<p><br /></p><br />';
			   }
		   else{
			   $imagebuttons=array();
			   $imagebuttons['clicktodelete']=array('name'=>'current',
													'value'=>'delete_comment.php',
													'title'=>'delete');
			   $imagebuttons['clicktoedit']=array('name'=>'Edit',
												  'value'=>'',
												  'title'=>'edit');
				rowaction_buttonmenu($imagebuttons,$extrabuttons,$book);
			   }
?>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				xmlechoer('Comment',$entry);
?>
			</div>
		  </tbody>
<?php
				}
			}
		}
?>
	  </table>
	</div>
  </div>
