<?php
/**                                  ents_list.php    
 */

$current='ents_list.php';
$action='ents_list_action.php';
$host='infobook.php';

$tagname=$_GET{'type'};

if(isset($_GET{'bid'})){$bid=$_GET{'bid'};}

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('student',$book);?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>

  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="right">
		<label for="Details"><?php print_string('details',$book);?></label>
		<textarea name="detail" id="Details" rows="5" cols="40"></textarea>
	  </div>
<?php
if($tagname=='Background'){
?>
	  <div class="left" >
		<label for="Category"><?php print_string('source',$book);?></label>
		<?php $cattype='bac'; include('scripts/list_category.php'); ?>
	  </div>

	  <div class="left" >
		<?php include('scripts/jsdate-form.php'); ?>
	  </div>

	  <div class="right" >
		<?php $yid=$Student['NCyearActual']['id_db']; include('scripts/list_year.php'); ?>
	  </div>
<?php
   	}
else{
?>
	  <div class="right">
		<label><?php print_string('subjectspecific');?></label>
			   <?php $required="no"; include('scripts/list_studentsubjects.php');?>
	  </div>

	  <div class="left">
				<?php include('scripts/jsdate-form.php'); ?>
	  </div>
<?php 
	}
?>
	<input type="hidden" name="type" value="<?php print $type;?>"/>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
 	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
  </form>
  </div>

  <div class="content">
	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string(strtolower($tagname),$book);?></caption>
<?php
	$perm=getYearPerm($yid, $respons);
	$Entries=$Student['Backgrounds']["$tagname"];
	$entryno=0;
	if(is_array($Entries)){
	while(list($key,$entry)=each($Entries)){
		if($tagname=='Background' and $entry['Categories']['Category'][0]['rating']=='-1'
					and $perm['r']!=1){$entry['Comment']['value']='Confidential';}
		if(is_array($entry)){
            print '<tr>';
        	while(list($key,$val)=each($entry)){
               if(isset($val['value']) & is_array($val)){
?>	
	<td>
					<?php print $val['value']; ?>
	</td>
<?php		  }
				else {print '<td></td>';}
                }
            print '</tr>';
			$entryno++;	
			}
		}
	}
?>
<tr><td></td></tr>
	  </table>
	</div>
  </div>


