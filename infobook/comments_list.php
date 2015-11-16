<?php
/**                                  comments_list.php    
 *
 */

$cancel='student_view.php';
$action='comments_list_action.php';

if(isset($_GET['bid'])){$bid=$_GET['bid'];}
/* To display all comments for the student */
$d_ed=mysql_query("SELECT entrydate FROM comments WHERE student_id='$sid' ORDER BY entrydate ASC LIMIT 1;");
$firstentrydate=mysql_result($d_ed,0);
$Comments=fetchComments($sid,$firstentrydate,'');
$Student['Comments']=$Comments;
$yid=$Student['YearGroup']['value'];
$perm=getFormPerm($Student['RegistrationGroup']['value'],$respons);

$extrabuttons=array();
$extrabuttons['addnew']=array('name'=>'current','value'=>'comments_new.php');
two_buttonmenu($extrabuttons);
?>
    <div id="heading">
        <h4>
        	<label><?php print_string('comments');?></label>
            <?php
                print $Student['Forename']['value'].' '.$Student['Surname']['value'];
                print '('.$Student['RegistrationGroup']['value'].')';
            ?>
        </h4>
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
			<th colspan="3"><?php print_string('category');?></th>
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
			   $Incident=(array)fetchIncident(array('id'=>$entry['incident_id_db']));
			   if($Incident['Closed']['value']=='N'){$styleclass=' class="hilite" ';}
			   else{$styleclass='';}
			   print '<td '.$styleclass.'><label>'.get_string('incidents',$book).'</label></td>';
			   }
		   elseif(isset($entry['merit_id_db'])){
			   print '<td><label>'.get_string('merits',$book).'</label></td>';
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
			<td>
<?php 
			   if(isset($entry['incident_id_db'])){
				   print get_string('sanction','infobook').': '.$Incident['Sanction']['value'];
				   }
?>
			</td>
<?php
		   if(isset($entry['EntryDate']['value'])){print '<td>'.display_date($entry['EntryDate']['value']).'</td>';}
		   else{print'<td></td>';}
?>
		  </tr>
		  <tr <?php if($shared or isset($entry['incident_id_db']) or isset($entry['merit_id_db'])){print 'class="revealed"';}else{print 'class="hidden"';}?> id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="7">
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
			   $sharedcomment="sharedcomment";
			   }
		   else{$sharedcomment="comment";}

		   $imagebuttons=array();
		   $extrabuttons=array();
		   $imagebuttons['clicktodelete']=array('name'=>'current',
		   										'id'=>'delete'.$entryno,
												'value'=>'delete_comment.php',
												'title'=>'delete');
		   $extrabuttons['edit']=array('name'=>'process',
		   								'id'=>'edit'.$entryno,
									   'value'=>'edit',
									   'title'=>'edit');
		   $imagebuttons['clicktoload']=array('name'=>'Attachment',
										 'onclick'=>"clickToAttachFile($sid,$entryno,'','','$sharedcomment','comment')", 
										 'class'=>'clicktoload',
										 'value'=>'category_editor.php',
										 'title'=>'clicktoattachfile');
		   if(isset($entry['incident_id_db']) and $Incident['Closed']['value']=='N'){
			   $extrabuttons['newaction']=array('name'=>'process',
												'value'=>'newaction',
												'title'=>'newaction');
			   }

		   if($perm['w']=='1' or $entry['Teacher']['username']==$tid){
			   rowaction_buttonmenu($imagebuttons,$extrabuttons,$book);
			   }
		   require_once('lib/eportfolio_functions.php');
		   display_file($Student['EPFUsername']['value'],'assessment',$entryno);
		   display_file($Student['EPFUsername']['value'],'comment',$entryno);
?>			</td>
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


  <div id="preview" style="display:none; width:80%;margin-left:10%;z-index:1000;position:absolute;float:left;">
		<img id="imgpreview" src="" alt="Preview" style="display:block;width:auto;height:auto;max-width:100%;max-height:100%;padding-top:3%;padding-bottom:3%;margin-left:auto;margin-right:auto; float:none;" onclick="getElementById('preview').style.display='none';getElementById('shadow').style.display='none';">
  </div>
  <div id="shadow" style="display:none; width:100%;height:100%;background-color:black;z-index:999;position:fixed;opacity:0.4;" onclick="getElementById('preview').style.display='none';getElementById('shadow').style.display='none';"><div>
