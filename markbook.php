<?php 
/**														markbook.php
 *	This is the hostpage for the markbook
 *	The classes being viewed is set by $cids 
 */

$host='markbook.php';
$book='markbook';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');

if(!isset($_POST['displaymid'])){$displaymid=0;}//means no change to marks displayed
else{$displaymid=$_POST['displaymid'];}//new mark created by previous script

 /**
  * If the classes selection has changed then need to refresh some of 
  * the session data for components and stuff (but don't want ot do 
  * this for every page load.
  */
if(isset($_POST['cids'])){
	$pids=array();
	$components=array();
	$classes=array();
	$profiles=array();
	if($_SESSION['cids']!=$_POST['cids']){
		$_SESSION['cids']=$_POST['cids'];
		$_SESSION['umnrank']='surname';
		}
	if($displaymid==0){$displaymid=-1;}

	foreach($_SESSION['cids'] as $cid){
		/*this is used to describe the class*/
		$d_c=mysql_query("SELECT detail, subject_id AS bid, course_id
					AS crid, stage	FROM class WHERE id='$cid';");
		$classes[$cid]=mysql_fetch_array($d_c,MYSQL_ASSOC);
		/* Grab the class's subject components, will only only exlcude those which are status=U (unused) */
		$comps=list_subject_components($classes[$cid]['bid'],$classes[$cid]['crid']);
		foreach($comps as $component){
			if(!in_array($component['id'],$pids)){
				$components[]=$component;
				$pids[]=$component['id'];
				/* Grab the subject component's components ie. strands. Restrict to AV (for all validating) */
				$strands=list_subject_components($component['id'],$classes[$cid]['crid'],'AV');
				foreach($strands as $strand){
					if(!in_array($strand['id'],$pids)){
						$strand['name']='&nbsp;&nbsp;'.$strand['name'];
						$pids[]=$strand['id'];
						$components[]=$strand;
						}
					}
				}
			}
		}
	$profiles=(array)list_assessment_profiles($classes[$cid]['crid'],$classes[$cid]['bid']);

	$_SESSION['pids']=$pids;
	$_SESSION['components']=$components;
	$_SESSION['classes']=$classes;
	$_SESSION['profiles']=$profiles;



	/* Tries to recall a tid's previous choice of pid for this class*/
	if(!in_array($_SESSION['pid'],$pids)){
		//$etid=$tid;
		$d_component=mysql_query("SELECT component_id FROM tidcid 
						WHERE class_id='$cid' AND teacher_id='$tid';");
		if(mysql_num_rows($d_component)>0 and in_array(mysql_result($d_component,0),$pids)){
			$_SESSION['pid']=mysql_result($d_component,0);
			}
		else{
			$_SESSION['pid']='';
			}
		}
	}


/* If the component selection has changed then update*/
if(isset($_POST['pid'])){
	if($_SESSION['pid']!=$_POST['pid']){
	$_SESSION['pid']=$_POST['pid'];
	$pid=$_SESSION['pid'];
	foreach($_SESSION['cids'] as $cid){
		$d_component=mysql_query("UPDATE tidcid SET component_id='$pid' 
						WHERE class_id='$cid' AND teacher_id='$tid'");
		}
	$_SESSION['umnrank']='surname';}
	if($displaymid==0){$displaymid=-1;}
	}

/* If the column-type filter has changed then update*/
if(isset($_POST['umnfilter'])){
	if($_SESSION['umnfilter']!=$_POST['umnfilter']){
	$_SESSION['umnfilter']=$_POST['umnfilter'];
	$umnfilter=$_SESSION['umnfilter'];}
	if($displaymid==0){$displaymid=-1;}
	}


/* Now initialise all of the variables from the session data*/
if(!isset($_SESSION['cids'])){$_SESSION['cids']=array();}
$cids=$_SESSION['cids'];
$cidsno=sizeof($cids);
if(!isset($_SESSION['classes'])){$_SESSION['classes']=array();}
$classes=$_SESSION['classes'];
if(!isset($_SESSION['profiles'])){$_SESSION['profiles']=array();}
$profiles=$_SESSION['profiles'];
if(!isset($_SESSION['pids'])){$_SESSION['pids']=array();$_SESSION['components']=array();}
$pids=$_SESSION['pids'];$components=$_SESSION['components'];
if(!isset($_SESSION['pid'])){$_SESSION['pid']='';}
$pid=$_SESSION['pid'];
if(!isset($_SESSION['umnfilter']) or 
	($cidsno>1 and $_SESSION['umnfilter']=='hw')){$_SESSION['umnfilter']='%';}
$umnfilter=$_SESSION['umnfilter'];
if(sizeof($profiles)==0 and substr($umnfilter,0,1)=='p'){
	/* If a profile was previously selected but no profiles now available... */
	$umnfilter='%';
	$_SESSION['umnfilter']=$umnfilter;
	if($displaymid==0){$displaymid=-1;}
	}
if(!isset($_SESSION['umnrank'])){$_SESSION['umnrank']='surname';}
$umnrank=$_SESSION['umnrank'];
/* Current date for attendance. */
$attdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
/* Previous lesson attendance displayed. */
$lessonatt=0;
if($cidsno==1){
	if(isset($_GET['lessonatt'])){$lessonatt=$_GET['lessonatt'];}
	elseif(isset($_SESSION['lessonatt'])){$lessonatt=$_SESSION['lessonatt'];}
	}
$_SESSION['lessonatt']=$lessonatt;
?>

<div class="markcolor" id="bookbox">
<?php
	if($current!='' and $cidsno>0){
		include($book.'/'.$current);
		}
?>
</div>

<div style="visibility:hidden;" id="hiddenbookoptions">
	<fieldset class="markbook">
	  <legend><?php print_string('classesandmarks');?></legend>
	  <form id="classchoice" name="classchoice" method="post" 
		action="markbook.php" target="viewmarkbook">
<?php	include('scripts/list_class.php');?>
	  </form>
	  <form id="componentchoice" name="componentchoice" method="post" 
		action="markbook.php" target="viewmarkbook">
		<input name="tid" type="hidden" value="<?php print $tid;?>">
		  <input name="current" type="hidden" value="class_view.php">		
<?php
if(sizeof($pids)>0){
?>
			<select name="pid" size="1" onchange="document.componentchoice.submit();">
			  <option value=""><?php print_string('allcomponents');?></option>
<?php
   foreach($components as $index => $component){
		print '<option ';
		if($component['id']==$pid){print 'selected="selected"';}
		print ' value="'.$component['id'].'">'.$component['name'].'</option>';
		}
?>
			</select>
<?php
	}
?>
	  </form>
	  <form id="gradechoice" name="gradechoice"  method="post" 
		action="markbook.php" target="viewmarkbook">
		<select id="mids" name="mids[]" size="14" multiple="multiple"  
		   onChange="changeMarkDisplay(this.form);">
<?php
if(isset($umns)){
   	for($col=0;$col<sizeof($umns);$col++){
	   	if($umns[$col]['component']==$pid or $pid==''){
			print '<option class="'.$umns[$col]['displayclass'].'" value="'.$umns[$col]['id'].'" id="sel-'.$umns[$col]['id'].'">';
			if($umns[$col]['component']!=''){print $umns[$col]['component'].': ';}
			print $umns[$col]['topic'].' ('.$umns[$col]['entrydate'].')</option>';
			}
		}
	}
?>
		</select>
	  </form>
	  <div class="neat">
		<form id="umnfilterchoice" name="umnfilterchoice" method="post" 
		  action="markbook.php" target="viewmarkbook">
		  <input name="tid" type="hidden" value="<?php print $tid;?>">
			<input name="current" type="hidden" value="class_view.php">		
			<label>&nbsp;CW</label>
			  <input title="<?php print_string('classwork',$book);?>" 
				type="radio" name="umnfilter"
				value="cw" <?php if($umnfilter=='cw'){print 'checked';}?>
				onchange="document.umnfilterchoice.submit();" />
<?php
				/* Only display HW for a single class and only for courses
				 *	which do do homework
				 */
		if($cidsno==1 and isset($cid) and 
		   !in_array($classes[$cid]['crid'],getEnumArray('nohomeworkcourses'))){
?>
			<label>&nbsp;HW</label>
				<input title="<?php print_string('homework',$book);?>" 
				  type="radio" name="umnfilter"
				  value="hw" <?php if($umnfilter=='hw'){print 'checked';}?>
				  onchange="document.umnfilterchoice.submit();" />
<?php
			}
?>
			<label>&nbsp;R</label>
				<input title="<?php print get_string('reports',$book).' & '.get_string('assessments',$book);?>" 
					type="radio" name="umnfilter" 
					value="t" <?php if($umnfilter=='t'){print 'checked';}?>
					onchange="document.umnfilterchoice.submit();" />
				<br />
			<div><?php print_string('filterlist');?></div>
<?php
		if(sizeof($profiles)>0){
			foreach($profiles as $choiceprono => $choiceprofile){
?>
				<label>&nbsp;<?php print substr($choiceprofile['name'],0,2);?>P</label>
				<input title="<?php print $choiceprofile['name'].' '.get_string('assessmentprofile',$book);?>" 
					type="radio" name="umnfilter" 
					value="p<?php print $choiceprono;?>" 
					<?php if($umnfilter=='p'.$choiceprono){print 'checked';$currentprofile=$choiceprofile;}?>
					onchange="document.umnfilterchoice.submit();" />
<?php
				}
			}
?>
			<label><?php print_string('all');?></label>
				<input  title="<?php print_string('all');?>" type="radio" name="umnfilter"
				  value="%" <?php if($umnfilter=='%'){print 'checked';}?>
				  onchange="document.umnfilterchoice.submit();" />
		</form>
	  </div>

<br />
<?php
		if(isset($currentprofile) and $currentprofile['transform']!=''){
?>
	  <div id="<?php print $currentprofile['id'];?>" class="neat sidebuttons">
		<button name="chart" onclick="window.frames['viewmarkbook'].clickToAction(this);" 
			style="background-color:#666;"
			value="report_profile_print.php">
			<img alt="Chart" src="images/charter.png"/>
		</button>
			<label style="font-weight:600;">&nbsp;<?php print $currentprofile['name'];?></label>
			<div id="<?php print 'xml-'.$currentprofile['id'];?>" style="display:none;">
<?php 
						 /*TODO: should bid and pid be past here? Seems to stop report_profile_print from working*/
			//$currentprofile['bid']=$bid[0];
			//$currentprofile['pid']=$pid;
			$currentprofile['stage']=$classes[$cid]['stage'];
			$currentprofile['classes']='';
			foreach($cids as $cindex => $cid){
				$currentprofile['classes'].=$cid.' ';
				}
			xmlechoer('Profile',$currentprofile);
?>
		  </div>
	  </div>
<?php

			}
?>


	</fieldset>
  </div>
<?php
include('scripts/end_options.php');
?>
