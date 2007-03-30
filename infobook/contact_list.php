<?php
/**									contact_list.php
 *
 *   	Lists guardians identified in array gids.
 */

$action='contact_list.php';
$choice='contact_list.php';

if(!isset($_SESSION['infogid'])){$_SESSION['infogid']='';}
if(!isset($_SESSION['infogids'])){$_SESSION['infogids']=array();}

if(isset($_GET['gid'])){
	if($_SESSION['infogid']!=$_GET['gid']){
		$_SESSION['infogid']=$_GET['gid']; 
		$_SESSION['umnrank']='surname';
		}
	}
if(isset($_POST['gid'])){
	if($_SESSION['infogid']!=$_POST['gid']){
		$_SESSION['infogid']=$_POST['gid']; 
		$_SESSION['umnrank']='surname';
		}
	}

$gids=$_SESSION['infogids'];
$gid=$_SESSION['infogid'];

include('scripts/sub_action.php');

/*
$displayfields=array();
$displayfields[]='RegistrationGroup';$displayfields[]='Gender';$displayfields[]='DOB';
if(isset($_POST['displayfield'])){$displayfields[0]=$_POST['displayfield'];}
if(isset($_POST['displayfield1'])){$displayfields[1]=$_POST['displayfield1'];}
if(isset($_POST['displayfield2'])){$displayfields[2]=$_POST['displayfield2'];}
*/

$extrabuttons='';
/*
if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
     	$extrabuttons['exportstudentrecords']=array('name'=>'current',
										 'title'=>'exportstudentrecords',
										 'value'=>'export_students.php');
	}
*/
two_buttonmenu($extrabuttons,$book);
?>

<div id="viewcontent" class="content">
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
<table class="listmenu sidtable">
	<th><?php print_string('checkall'); ?><input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" /></th>
	<th><?php print_string('contacts',$book); ?></th>
<?php
	/*
	while(list($index,$displayfield)=each($displayfields)){
?>
		<th><?php include('scripts/list_studentfield.php');?></th>
<?php
		}
	*/

	$rown=1;
	while(list($index,$gid)=each($gids)){
		$Contact=fetchContact(array('guardian_id'=>$gid));
?>
		<tr>
		  <td>
			<input type="checkbox" name="gids[]" value="<?php print $gid;?>" />
			<?php print $rown++;?>
		  </td>
		  <td>
			<a href="infobook.php?current=contact_details.php&cancel=contact_list.php&gid=<?php print $gid;?>">
			  <?php print $Contact['Surname']['value']; ?>,
			  <?php print ' '.$Contact['Forename']['value']; ?>
			</a>
		  </td>
<?php
					  /*
	reset($displayfields);
	while(list($index,$displayfield)=each($displayfields)){
		if(array_key_exists($displayfield,$Student)){
			print '<td>'.$Student[$displayfield]['value'].'</td>';
			}
		else{
			$field=fetchStudent_singlefield($sid,$displayfield);
			print '<td>'.$field[$displayfield]['value'].'</td>';
			}
		}
					  */
?>
		</tr>
<?php
		}
	reset($gids);
?>
	  </table>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '$choice';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>