<?php
/**
 *                                  student_view_boarder.php
 */

$action='student_view_boarder_action.php';

three_buttonmenu();

	/*Check user has permission to view*/
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid,$respons);
	include('scripts/perm_action.php');

?>
  <div id="heading">
	<?php print $Student['Forename']['value'].' '.$Student['Surname']['value'];?>
  </div>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	 <fieldset class="center">
<?php 
	$selboarder=$Student['Boarder']['value'];
	$listname='boarder';$listlabel='boarder';$required='yes';
	include('scripts/set_list_vars.php');
	$tab=list_select_enum('boarder',$listoptions,$book);
?>
	  </fieldset>

	 <div class="center">
<?php 
	$Stays=fetchStays($sid);
	if(sizeof($Stays)>0){$Stay=$Stays[0];}
	else{$Stay=fetchStay();}
	if($Student['Boarder']['value']!='N' and $Student['Boarder']['value']!=''){
		$tab=xmlarray_form($Stay,'','stay',$tab,$book);
		}
?>
	  </div>

	<input type="text" style="display:none;" id="Id_db" name="id_db"
									value="<?php print $Stay['id_db']?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
</form>
</div>
