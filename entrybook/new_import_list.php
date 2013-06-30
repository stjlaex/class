<?php 
/**
 *									new_import.php	
 */
$choice='new_import.php';
$action='new_import.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}
if(isset($_POST['news'])){$news=(array)$_POST['news'];}else{$news=array();}

two_buttonmenu();


?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" 
	  method="post" enctype="multipart/form-data" action="<?php print $host;?>">

	  <fieldset class="center">
		<legend><?php print_string('students');?></legend>
		<table class="listmenu sidtable">

<?php 

foreach($sids as $sindex => $sid){

	$rowno=$sindex+1;
	$Student=(array)fetchStudent($sid);
	$Enrolment=(array)fetchEnrolment($sid);

	if($news[$sindex]=='new'){$rowclass='lolite';}
	else{$rowclass='nolite';}

	print '<tr class="'.$rowclass.'" >';
?>
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
			<?php print $rowno;?>
		  </td>
		  <td class="student" style="width:40%;">
			<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>"
			  target="viewinfobook" onclick="parent.viewBook('infobook');">
			  <?php print $Student['DisplayFullName']['value']; ?></a>
		  </td>
<?php
	print '<td>'.display_date($Student['DOB']['value']).'</td>';
	print '<td>'.display_yeargroupname($Enrolment['YearGroup']['value']).'</td>';
	print '<td>'.$news[$sindex].'</td>'.'</tr>';
	}

?>

		</table>
	  </fieldset>
	

	
 	<input type="hidden" name="current" value="<?php print $action;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>















