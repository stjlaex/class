<?php
$action='meals.php';
$choice='meals.php';

include('scripts/sub_action.php');

if(isset($_GET['mealname'])){$mealname=$_POST['mealname'];}
if(isset($_POST['mealname'])){$mealname=$_POST['mealname'];}else{$mealname='';}
if(isset($_GET['mealdetails'])){$mealdetails=$_GET['mealdetails'];}
if(isset($_POST['mealdetails'])){$mealdetails=$_POST['mealdetails'];}else{$mealdetails='';}

$meals=list_meals();

if(isset($mealname) and $mealname!=''){
	$exists=false;
	foreach($meals as $meal){
		if($meal['name']==$mealname){$exists=true;}
		}
	if(!$exists){mysql_query("INSERT INTO meals_list SET name='$mealname', type='meal', detail='$mealdetails', day='%', time='';");}
	}

$extrabuttons['add']=array('name'=>'current','value'=>'meals_add.php');
two_buttonmenu($extrabuttons,$book);
?>

  <div class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
  	<fieldset class='right'>
		  <legend>New meal</legend>
			<div class="center">
				<label for="mealname">Name</label>
				<img class="required">
		  		<input type='text' id='mealname' name='mealname' >
		  	</div>
			<div class="center">
				<label for="mealdetails">Details</label>
		  		<input type='text' id='mealdetails' name='mealdetails' >
		  	</div>
		</fieldset>
	<div id="viewcontent" class="left">
	  <table class="listmenu sidtable">
		<tr>
		  <th><?php print_string('meals',$book);?></th>
		</tr>
<?php
		$meals=list_meals();
		foreach($meals as $meal){
			if($meal['name']!='NOT LUNCHING'){
?>
		<tr>
		  <td>
<?php
				print ''.$meal['name'].'';
?>
		  </td>
<?php
		  }
		}
?>
		<tr>
		</table>
	</div>

	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>
  </div>

