<?php
/*                                  ents_list.php    
*/
$current='ents_list.php';
$action='ents_list_action1.php';
$host='infobook.php';

$table=$_GET{'table'};
$title=$_GET{'title'};
if(isset($_GET{'bid'})){$bid=$_GET{'bid'};}

three_buttonmenu();
?>

  <div id="heading">
	<label><?php print_string($table);?></label>
<?php
	print $Student['Forename']['value'].' '.$Student['Surname']['value'];
	print '('.$Student['RegistrationGroup']['value'].')';
?>
  </div>

  <div class="topform">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="left">
		<label for="Category"><?php print_string('category',$book);?></label>
		<?php include('scripts/list_category.php');?>
	  </div>
	  <div class="right" >
		<?php include('scripts/jsdate-form.php'); ?>
	  </div>
	  <div class="right">
		<label for="Details"><?php print_string('details',$book);?></label>
		<textarea name="detail" id="Details" 
		<?php if($table!='background'){print ' maxlength="248" ';}?> 
		  rows="5" cols="40"> </textarea>
	  </div>
<?php 
if($table=='concerns'){
		$date='';
		$Concerns=fetchConcerns($sid,$date,'');
		$Student['Comments']=$Concerns;
?>


	  <div class="left" >
		<label for="Subject">Subject Specific (optional):</label>
		<?php $required='no'; include('scripts/list_studentsubjects.php');?>
	  </div>
	  <div class="left" >
		<?php $yid=$Student['NCyearActual']['id_db']; include('scripts/list_year.php'); ?>
	  </div>

<?php
	}
elseif($table=='background'){
?>
	  <div class="right" >
		<label for="Category">Source:</label>
		<?php $cattype='bac'; include('scripts/list_category.php'); ?>
	  </div>

	  <div class="left" >
		<?php include('scripts/jsdate-form.php'); ?>
	  </div>

	  <div class="left" >
		<?php $yid=$Student['NCyearActual']['id_db']; include('scripts/list_year.php'); ?>
	  </div>

<?php
   	}
elseif($table=='exclusions'){
?>
	  <div class="right">
		<label>Category:</label>
		<select id="category" name="category">
		  <option value=''></option	
<?php
		$enum=getEnumArray('category');
		while(list($inval,$description)=each($enum)){	
			print "<option value='".$inval."'>".$description."</option>";
			}
?>
		</select>
	  </div>

	  <div class="right">
		<label>Starts</label>
		<?php $idate=1; include('scripts/jsdate-form.php'); ?>
	  </div>

	  <div class="right">
		<label>Ends</label>
				<?php $idate=2; include('scripts/jsdate-form.php'); ?>
	  </div>
<?php
	}

else{
?>

	  <div class="right">
		<label>Subject Specific (optional):</label>
			   <?php $required="no"; include('scripts/list_studentsubjects.php');?>
	  </div>

	  <div class="left">
				<?php include('scripts/jsdate-form.php'); ?>
	  </div>
<?php 
	}
?>
	<input type="hidden" name="table" value="<?php print $table;?>"/>
	<input type="hidden" name="title" value="<?php print $title;?>"/>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
 	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
  </form>
  </div>

  <div class="content">
	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('entries',$book);?></caption>
<?php

$yid=$Student['NCyearActual']['id_db'];
$perm=getYearPerm($yid, $respons);

	$list=$Student["$title"];
	$entryno=0;
	if(is_array($list)){
	while(list($key,$entry)=each($list)){
		if($title=='Backgrounds' and $entry['Categories']['Category'][0]['rating']=='-1'
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
	  </table>
	</div>
  </div>


