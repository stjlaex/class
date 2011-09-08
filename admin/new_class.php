<?php 
/**				   	   				   new_class.php
 */

$action='new_class_action.php';
$cancel=$choice;

if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}

list($rcrid,$rbid,$error)=checkCurrentRespon($r,$respons,'course');
if(sizeof($error)>0){include('scripts/results.php');exit;}

three_buttonmenu();

$maxclassno=20;
$manys=array();
for($c=0;$c<$maxclassno;$c++){
	$manys[]=array('id'=>$c,'name'=>$c);
	}
$stages=list_course_stages($rcrid);
$subjects=list_subjects($rcrid);
$nonsubjects=list_subjects($rcrid,false);
?>
  <div id="heading">
	<label><?php print_string('editsubjectclasses',$book);?></label>
  </div>

  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
								action="<?php print $host; ?>">

	  <fieldset class="center divgroup">
		<div class="left">
<?php
	$listname='bid';
	$listlabel='subject';
	$onchange='yes';
	include('scripts/set_list_vars.php');
	list_select_list($subjects,$listoptions);
?>
		</div>


		<div class="center">
		  <table class="listmenu">
			<tr>
			  <th><?php print_string('stage',$book)?></th>
			  <th><?php print_string('number',$book)?></th>
			  <th><?php print_string('type',$book)?></th>
			</tr>
<?php

		foreach($stages as $stage){
			$stagename=$stage['name'];
			$classdef=get_subjectclassdef($rcrid,$bid,$stagename);

   			$many=$classdef['many'];
   			$generate=$classdef['generate'];
?>
			<tr>
			  <td>
<?php
			print $stagename;
?>
			  </td>
			  <td>
<?php
			$liststyle='width:10em;';
			$listname=$stagename.'-m';
			$listlabel='';
			${'sel'.$listname}=$many;
			include('scripts/set_list_vars.php');
			list_select_list($manys,$listoptions);
?>
			  </td>
			  <td>
				<select name="<?php print $stagename;?>-g" 
				  tabindex="<?php print $tab++;?>" >
				  <option value="sets" <?php if($generate=="sets"){print "selected='selected'";}?>>sets</option>	
				  <option value="forms" <?php if($generate=="forms"){print "selected='selected'";}?>>forms</option>	
				</select>
			  </td>
			</tr>
<?php
			}
?>
		  </table>
		</div>


		<div class="right">
<?php 
		if($_SESSION['role']=='admin'){
			$checkcaption='Overwrite exisiting (may lose MarkBook data!)';
			$checkname='overwrite';
			include('scripts/check_yesno.php');
			}
?>
		</div>

	  </fieldset>


	  <fieldset class="left divgroup">
	    <legend>New subject</legend>
		<div class="center">
<?php
	$listname='newbid';
	$listlabel='';
	$onchange='yes';
	include('scripts/set_list_vars.php');
	list_select_list($nonsubjects,$listoptions,$book);
?>
		</div>
	  </fieldset>

	<input type="hidden" name="crid" value="<?php print $rcrid;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>

