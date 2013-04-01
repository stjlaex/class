<?php
/**                                  ents_new.php    
 */

$action='ents_new_action.php';
$cancel='ents_list.php';

if(isset($_GET['tagname'])){$tagname=$_GET['tagname'];}
elseif(isset($_POST['tagname'])){$tagname=$_POST['tagname'];}
if(isset($_POST['entid'])){$entid=$_POST['entid'];}else{$entid=-1;}

$Ent=(array)fetchBackgrounds_Entry(array('id'=>$entid));

if($entid!=-1){
	/* Editing an existing entry.*/
	$bid=$Ent['Subject']['value_db'];
	$todate=$Ent['EntryDate']['value'];
	$catid=$Ent['Categories']['Category'][0]['value_db'];
	$ratvalue=$Ent['Categories']['Category'][0]['rating']['value'];
	}

$aperm=get_admin_perm('s',get_uid($tid));// special access to reserved information
$perm=getYearPerm($Student['YearGroup']['value'], $respons);

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('student',$book);?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>

  <div class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	<fieldset class="center">
	  <div class="center">
		<label for="Detail"><?php print_string('details',$book);?></label>
		<textarea name="detail" id="Detail" tabindex="<?php print $tab++;?>"
		  class="required" rows="5" cols="35"><?php print $Ent['Detail']['value_db'];?></textarea>
	  </div>

	  <div class="right">
<?php 
		$xmldate='Entrydate'; 
		$required='yes'; 
		include('scripts/jsdate-form.php'); 
?>
	  </div>

  </fieldset>

  <fieldset class="center">

<?php
if($tagname=='Background'){
?>
	  <div class="left" >
<?php 
		$cattype='bac';
		$required='yes';
		$listlabel='source';
		$listname='catid'; 
		$listid='Category';
		include('scripts/list_category.php'); 
?>
	  </div>


	  <div class="right" >
<?php 
		$listid='yeargroup';
		$newyid=$Student['YearGroup']['value'];
		include('scripts/list_year.php'); 
?>
	  </div>
<?php
   	}
else{
?>
	  <div class="right">
		<label><?php print_string('subjectspecific');?></label>
<?php 
		$required='no'; $listname='bid'; $listid='subject';$listlabel='';
		$subjects=list_student_subjects($sid);
		include('scripts/set_list_vars.php');
		list_select_list($subjects,$listoptions,$book);
		unset($listoptions);
?>
	  </div>

<?php 
	}
?>
  </fieldset>


	<input type="text" style="display:none;" id="Id_db" name="id_db" value="<?php print $Ent['id_db'];?>" />
	<input type="hidden" name="tagname" value="<?php print $tagname;?>"/>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
 	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
  </form>

<?php
	require_once('lib/eportfolio_functions.php');
	html_document_drop($Student['EPFUsername']['value'],'background',$entid);
?>
  </div>
