<?php
$action='meals.php';
$choice='meals.php';

include('scripts/sub_action.php');

if(isset($_GET['mealname'])){$mealname=$_GET['mealname'];}
if(isset($_POST['mealname'])){$mealname=$_POST['mealname'];}else{$mealname='';}
if(isset($_GET['mealdetails'])){$mealdetails=$_GET['mealdetails'];}
if(isset($_POST['mealdetails'])){$mealdetails=$_POST['mealdetails'];}else{$mealdetails='';}
if(isset($_GET['action'])){$action=$_GET['action'];}
if(isset($_POST['action'])){$action=$_POST['action'];}
if(isset($_GET['mealid'])){$mealid=$_GET['mealid'];}
if(isset($_POST['mealid'])){$mealid=$_POST['mealid'];}

$meals=list_meals();
if($action!='edit' and $action!='update'){
	if(isset($mealname) and $mealname!=''){
		$exists=false;
		foreach($meals as $meal){
			if($meal['name']==$mealname){$exists=true;}
			}
		if(!$exists){mysql_query("INSERT INTO meals_list SET name='$mealname', type='meal', detail='$mealdetails', day='%', time='';");}
		}

	$extrabuttons['add']=array('name'=>'current','value'=>'meals_add.php');
	}
if($action=='update'){
	if(isset($mealname) and $mealname!=''){
		mysql_query("UPDATE meals_list SET name='$mealname', type='meal', detail='$mealdetails', day='%', time='' WHERE id='$mealid';");
		}
	$extrabuttons['add']=array('name'=>'current','value'=>'meals_add.php');
	}
if($action=='edit'){
	if(isset($mealid) and $mealid!=''){
		$meal=get_meal($mealid);
		$name=$meal['name'];
		$details=$meal['detail'];
		}
	$extrabuttons['update']=array('name'=>'current','value'=>'meals_add.php');
	}
two_buttonmenu($extrabuttons,$book);

?>

  <div class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
  	<fieldset class='divgroup'>
		  <h5>New meal</h5>
				<label for="mealname">Name</label>
		  		<input type='text' id='mealname' name='mealname' value="<?php echo $name;?>" class="required">

				<label for="mealdetails">Details</label>
		  		<input type='text' id='mealdetails' name='mealdetails' value="<?php echo $details;?>" >
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
				print '<a href="admin.php?current=meals_add.php&action=edit&mealid='.$meal['id'].'">'.
					'<img class="clicktoconfigure" style="float:left;padding:8px 8px;" title="'.get_string('edit','admin').'" />'.
					$meal['name'].'</a>';
?>
		  </td>
<?php
		  }
		}
?>
		<tr>
		</table>
	</div>
<?php
	if($action=='edit'){
?>
	<input type="hidden" name="mealid" value="<?php print $mealid;?>" />
	<input type="hidden" name="action" value="update" />
<?php
	}
?>
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>
  </div>

