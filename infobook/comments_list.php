<?php
/**                                  comments_list.php    
 *
 */

$action='comments_list_action.php';

if(isset($_GET['bid'])){$bid=$_GET['bid'];}
$Comments=fetchComments($sid,'','');
$Student['Comments']=$Comments;

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string('comments');?></label>
<?php
	print $Student['Forename']['value'].' '.$Student['Surname']['value'];
	print '('.$Student['RegistrationGroup']['value'].')';
?>
  </div>

  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="left">
		<label for="Detail"><?php print_string('details',$book);?></label>
		<textarea name="detail" class="required" id="Detail"  maxlength="250"  
		  rows="5" cols="40"></textarea>
	  </div>
	  <div class="right" >
		<label for="Subject">Subject Specific (optional):</label>
		<?php $required='no'; include('scripts/list_studentsubjects.php');?>
	  </div>
	  <div class="right">
		<label for="Category"><?php print_string('category',$book);?></label>
		<?php include('scripts/list_category.php');?>
	  </div>
	  <div class="left" >
		<?php $xmldate='Entrydate'; $required='yes'; include('scripts/jsdate-form.php'); ?>
	  </div>
	  <div class="left" >
		<?php $yid=$Student['YearGroup']['value']; include('scripts/list_year.php'); ?>
	  </div>
	  <input type="text" style="display:none;" id="Id_db" name="id_db" value="" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'student_view.php';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>

  <div class="content">
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
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);
	if(is_array($Student['Comments'])){
		reset($Student['Comments']);
		while(list($key,$entry)=each($Student['Comments'])){
			if(is_array($entry)){
				$rown=0;
				$entryno=$entry['id_db'];
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++;?>">
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
		   while(list($index,$category)=each($entry['Categories']['Category'])){
			   if($category['rating']['value']==-1){print '<div class="negative">';}
			   else{print'<div style="float:left;padding:0 6px 0 6px;" class="positive">';}
			   print $category['label'].'</div>';
			   }
?>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="6">
			  <p>
<?php		   if(isset($entry['Detail']['value'])){
					print $entry['Detail']['value'];}
?>
			  </p>
			  <button class="rowaction" title="Delete this comment"
				name="current" value="delete_comment.php" onClick="clickToAction(this)">
				<img class="clicktodelete" />
			  </button>
			  <button class="rowaction" title="Edit" name="Edit" onClick="clickToAction(this)">
				<img class="clicktoedit" />
			  </button>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				xmlpreparer('Comment',$entry);
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


