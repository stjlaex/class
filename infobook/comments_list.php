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

$extrabuttons=array();
$extrabuttons['new']=array('name'=>'current','value'=>'comments_new.php');
two_buttonmenu($extrabuttons);
?>

  <div id="heading">
	<label><?php print_string('comments');?></label>
<?php
print $Student['Forename']['value'].' '.$Student['Surname']['value'];
print '('.$Student['RegistrationGroup']['value'].')';
?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	<div class="center">
	  <table class="listmenu">
		<thead>
		  <tr>
			<th></th>
			<th></th>
			<th><?php print_string('subject');?></th>
			<th colspan="2"><?php print_string('category');?></th>
		  </tr>
		</thead>
<?php
	if(is_array($Student['Comments']['Comment'])){
		foreach($Student['Comments']['Comment'] as $key => $entry){
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
		  <tr <?php if(!$shared and !isset($entry['incident_id_db']) and !isset($entry['merit_id_db'])){print 'class="rowplus" onClick="clickToReveal(this)"';}?> id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp</th>
<?php 

		   if(isset($entry['incident_id_db'])){
			   print '<td>'.get_string('incidents',$book).'</td>';
			   }
		   elseif(isset($entry['merit_id_db'])){
			   print '<td>'.get_string('merits',$book).'</td>';
			   }
		   else{print'<td></td>';}

		   if(isset($entry['Subject']['value'])){print '<td>'.$entry['Subject']['value'].'</td>';}
		   else{print'<td></td>';}
?>
			 <td>
<?php
		   foreach($entry['Categories']['Category'] as $category){
			   if($category['rating']['value']==-1){print '<div class="negative">';}
			   elseif($category['rating']['value']>0){print'<div class="positive">';}
			   else{print '<div>';}
			   print $category['label'].'</div>';
			   }
?>
			</td>
<?php
		   if(isset($entry['EntryDate']['value'])){print '<td>'.display_date($entry['EntryDate']['value']).'</td>';}
		   else{print'<td></td>';}
?>
		  </tr>
		  <tr <?php if($shared or isset($entry['incident_id_db']) or ($entry['merit_id_db'])){print 'class="revealed"';}else{print 'class="hidden"';}?> id="<?php print $entryno.'-'.$rown++;?>">
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
			   $extrabuttons=array();
			   $imagebuttons['clicktodelete']=array('name'=>'current',
													'value'=>'delete_comment.php',
													'title'=>'delete');
			   $extrabuttons['edit']=array('name'=>'process',
										   'value'=>'edit',
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


	  <input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	  <input type="hidden" name="current" value="<?php print $action; ?>">
	  <input type="hidden" name="choice" value="<?php print $choice; ?>">
	</form>
  </div>
