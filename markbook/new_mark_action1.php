<?php 
/**										new_mark_action1.php
 *
 *	Creates a new record in the table:mark 
 *	for one or more classes using table:midcid
 */

$action='new_mark_action2.php';

include('scripts/sub_action.php');

if(isset($_POST['def_name'])){$def_name=$_POST['def_name'];}else{$def_name='';}

if($def_name=='custom'){
		$action='define_mark.php';
		include('scripts/redirect.php');
		exit;
		}

elseif($def_name==''){
		$error[]='Please select a Mark-Type.';
		$action='new_mark.php';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

	$d_markdef=mysql_query("SELECT * FROM markdef WHERE name='$def_name';");
	$markdef=mysql_fetch_array($d_markdef, MYSQL_ASSOC);
	$scoretype=$markdef['scoretype'];

three_buttonmenu();
?>
  <div id="heading">
			  <label><?php print_string('newmark',$book); ?></label>
			<?php print_string('classwork',$book);?>
  </div>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" novalidate method="post"
		action="<?php print $host; ?>" > 

	  <fieldset class="left">
		<legend><?php print_string('classesthatusethismark',$book);?></legend>
	 	<select class="required" id="Classes" name="newcids[]" 
		  size="14"	multiple="multiple" tabindex="2">
<?php
		/* Select all possible classes to apply the mark to*/
	if($r>-1){
		/*either by current responsibility choice*/
		$rcrid=$respons[$r]['course_id'];
		$newclasses=list_course_classes($rcrid,$classes[$cids[0]]['bid'],'%','','taught');
		}
	else{
		/* or by select definitions by subjects of classes taught */
		$newclasses=list_teacher_classes($tid,'%',$classes[$cids[0]]['bid']);
		}


	foreach($newclasses as $newclass){
		print '<option ';
		if(in_array($newclass['id'],$cids)){print 'selected="selected"';}
		print ' value="'.$newclass['id'].'">'.$newclass['name'].'</option>';
		}
?>
		</select>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('subjectcomponent',$book);?></legend>
<?php
   	$selnewpid=$pid;
	$listname='newpid';$listlabel='subjectcomponent';$multi=1;
	include('scripts/set_list_vars.php');
	list_select_list($components,$listoptions,$book);
?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('dateofmark',$book);?></legend>
		<?php include('scripts/jsdate-form.php');?>
	  </fieldset>

	  <fieldset class="right">
		<legend><?php print_string('detailsofmark',$book);?></legend>
		<label for="Topic"><?php print_string('markstitleidentifyingname',$book);?></label>
		<input class="required" type="text" id="Topic" name="topic" maxlength="38" pattern="alphanumeric" />
		  <label for="Comment"><?php print_string('optionalcomment',$book);?></label>
		  <input type="text" id="Comment" name="comment" maxlength="98" pattern="alphanumeric" />
<?php 
		  if($scoretype=='percentage'){ 
?>
			<label for="Total"><?php print_string('outoftotal',$book);?></label>
			<input class="required" type="text" id="Total" name="total" maxlength="4" pattern="integer" />
<?php
			}
?>
	  </fieldset>

	  <input type="hidden" name="scoretype" value="<?php print $scoretype;?>" />
	  <input type="hidden" name="def_name" value="<?php print $def_name;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print 'new_mark.php';?>" />
	</form>
  </div>
