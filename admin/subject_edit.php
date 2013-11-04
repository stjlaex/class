<?php
$action='subject_edit.php';
$choice='subject_edit.php';

include('scripts/sub_action.php');

if(isset($_GET['subjectname'])){$subjectname=$_GET['subjectname'];}
if(isset($_POST['subjectname'])){$subjectname=$_POST['subjectname'];}
if(isset($_GET['subjectid'])){$subjectid=$_GET['subjectid'];}
if(isset($_POST['subjectid'])){$subjectid=$_POST['subjectid'];}
if(isset($_GET['action'])){$action=$_GET['action'];}
if(isset($_POST['action'])){$action=$_POST['action'];}

function list_all_subjects(){
	$subjects=array();
	$d_s=mysql_query("SELECT id,name FROM subject ORDER BY id ASC;");
	while($subject=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$subjects[$subject['id']]=$subject;
		}
	return $subjects;
	}

function get_subject($id,$name=''){
	$subject=array();
	if($name!=''){
		$d_s=mysql_query("SELECT id, name FROM subject WHERE name='$name';");
		$subject=mysql_fetch_array($d_s,MYSQL_ASSOC);
		}
	elseif($id!=' ' and $id!='' and $id!=-1){
		$d_s=mysql_query("SELECT name FROM subject WHERE id='$id';");
		$subject=mysql_fetch_array($d_s,MYSQL_ASSOC);
		}
	return $subject;
	}

$subjects=list_all_subjects();

if($action!='edit' and $action!='update'){
	if(isset($subjectname) and $subjectname!='' and isset($subjectid) and $subjectid!=''){
		$exists=false;
		foreach($subjects as $subject){
			if($subject['name']==$subjectname or $subject['id']==$subjectid){
				$exists=true;
				if($subject['id']==$subjectid){$message=print_string('duplicatesubjectid',$book).": ".$subject['name'].". ";}
				if($subject['name']==$subjectname){$message.=print_string('duplicatesubject',$book);}
				}
			}
		if(!$exists){
			mysql_query("INSERT INTO subject SET id='$subjectid',name='$subjectname';");
			}
		}

	$extrabuttons['add']=array('name'=>'current','value'=>'subject_edit.php');
	}

if($action=='update'){
	if(isset($subjectname) and $subjectname!=''){
		$exists=false;
		foreach($subjects as $subject){
			if($subject['name']==$subjectname){
				$exists=true;
				if($subject['name']==$subjectname){$message=print_string('duplicatesubject',$book);}
				}
			}
		if(!$exists){
			mysql_query("UPDATE subject SET name='$subjectname' WHERE id='$subjectid';");
			$extrabuttons['add']=array('name'=>'current','value'=>'subject_edit.php');
			}
		else{$action='edit';}
		}
	}
if($action=='edit'){
	if(isset($subjectid) and $subjectid!=''){
		$subject=get_subject($subjectid);
		$name=$subject['name'];
		$id=$subjectid;
		}
	$extrabuttons['update']=array('name'=>'current','value'=>'subject_edit.php');
	}
two_buttonmenu($extrabuttons,$book);

?>

  <div class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
  	<fieldset class='right'>
		  <legend><?php print_string('newsubject',$book);?></legend>
			<div class="center">
				<label for="subjectname"><?php print_string('subjectname',$book);?></label>
				<img class="required">
		  		<input type='text' id='subjectname' name='subjectname' value="<?php echo $name;?>" >
		  	</div>
			<div class="center">
				<label for="subjectid"><?php print_string('subjectid',$book);?></label>
				<img class="required">
		  		<input type='text' <?php if($action=='edit'){echo "readonly='readonly'";} ?> id='subjectid' name='subjectid' value="<?php echo $id;?>" >
		  	</div>
		<div><?php echo $message;?></div>
		</fieldset>
	<div id="viewcontent" class="left">
	  <table class="listmenu sidtable">
		<tr>
		  <th colspan="2"><?php print_string('subjects',$book);?></th>
		</tr>
<?php
		$subjects=list_all_subjects();
		foreach($subjects as $subject){
?>
		<tr>
		  <td>
<?php
				print '<a href="admin.php?current=subject_edit.php&action=edit&subjectid='.$subject['id'].'">'.
					'<img class="clicktoconfigure" style="float:left;padding:8px 8px;" title="'.get_string('edit','admin').'" />'.
					$subject['name'].' </a>';
?>
		  </td>
		  <td>
<?php
			print $subject['id'];
?>
		  </td>
<?php
		}
?>
		<tr>
		</table>
	</div>
<?php
	if($action=='edit'){
?>
	<input type="hidden" name="subjectid" value="<?php print $subjectid;?>" />
	<input type="hidden" name="action" value="update" />
<?php
	}
?>
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>
  </div>

