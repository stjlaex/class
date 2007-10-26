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

/* If the classes selection has changed then need to refresh some of */
/* the session data for components*/
if(isset($_POST['cids'])){
	$pids=array();
	$classes=array();
	if($_SESSION['cids']!=$_POST['cids']){
	$_SESSION['cids']=$_POST['cids'];
	$_SESSION['umnrank']='surname';}
	if($displaymid==0){$displaymid=-1;}

	foreach($_SESSION['cids'] as $index => $cid){
		/*this is used to describe the class*/
		$d_c=mysql_query("SELECT detail, subject_id AS bid, course_id
					AS crid	FROM class WHERE id='$cid';");
		$classes[$cid]=mysql_fetch_array($d_c,MYSQL_ASSOC);
		/*grab the class's subject components*/
		$components=list_subject_components($classes[$cid]['bid'],$classes[$cid]['crid']);
		while(list($index,$component)=each($components)){
			if(!in_array($component['id'],$pids)){
				$pids[]=$component['id'];
				/*and the subject component's components ie. strands*/
				$strands=list_subject_components($component['id'],$classes[$cid]['crid']);
				while(list($index,$strand)=each($strands)){
					if(!in_array($strand['id'],$pids)){
						$pids[]=$strand['id'];
						}
					}
				}
			}
		}
	$_SESSION['pids']=$pids;
	$_SESSION['classes']=$classes;

	/* Tries to recall a tid's previous choice of pid for this class*/
	if(!in_array($_SESSION['pid'],$pids)){
		$etid=$tid;
		$d_component=mysql_query("SELECT component_id FROM tidcid 
						WHERE class_id='$cid' AND teacher_id='$tid'");
		if(mysql_num_rows($d_component)>0){
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
	foreach($_SESSION['cids'] as $index => $cid){
		$d_component=mysql_query("UPDATE tidcid SET component_id='$pid' 
						WHERE class_id='$cid' AND teacher_id='$tid'");
		}
	$_SESSION['umnrank']='surname';}
	if($displaymid==0){$displaymid=-1;}
	}

/* If the column-type filter has changed then update*/
if(isset($_POST['umntype'])){
	if($_SESSION['umntype']!=$_POST['umntype']){
	$_SESSION['umntype']=$_POST['umntype'];
	$umntype=$_SESSION['umntype'];}
	if($displaymid==0){$displaymid=-1;}
	}

/* Now initialise all of the variables from the session data*/
if(!isset($_SESSION['cids'])){$_SESSION['cids']=array('','');}
$cids=$_SESSION['cids'];
$cidsno=sizeof($cids);
if(!isset($_SESSION['classes'])){$_SESSION['classes']=array();}
$classes=$_SESSION['classes'];
if(!isset($_SESSION['pids'])){$_SESSION['pids']=array();}
$pids=$_SESSION['pids'];
if(!isset($_SESSION['pid'])){$_SESSION['pid']='';}
$pid=$_SESSION['pid'];
if(!isset($_SESSION['umntype']) or 
	($cidsno>1 and $_SESSION['umntype']=='hw')){$_SESSION['umntype']='%';}
$umntype=$_SESSION['umntype'];
if(!isset($_SESSION['umnrank'])){$_SESSION['umnrank']='surname';}
$umnrank=$_SESSION['umnrank'];
$attdate=date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y')));
?>

<div class="markcolor" id="bookbox">
<?php
	if($current!=''){
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
   foreach($pids as $index => $spid){
		print '<option ';
		if($spid==$pid){print 'selected="selected"';}
		print ' value="'.$spid.'">'.$spid.'</option>';
		}
?>
			</select>
<?php
	}
?>
	  </form>
	  <form id="gradechoice" name="gradechoice"  method="post" 
		action="markbook.php" target="viewmarkbook">
		<select id="mids" name="mids[]" size="12" multiple="multiple"  
		   onChange="changeMarkDisplay(this.form);">
<?php
if(isset($umns)){
   	for($col=0;$col<sizeof($umns);$col++){
	   	if($umns[$col]['component']==$pid or $pid==''){
			print '<option value="'.$umns[$col]['id'].'" id="sel-'.$umns[$col]['id'].'">';
			if($umns[$col]['component']!=''){print $umns[$col]['component'].': ';}
			print $umns[$col]['topic'].' ('.$umns[$col]['entrydate'].')</option>';
			}
		}
	}
?>
		</select>
	  </form>
	  <div class="neat">
		<form id="umntypechoice" name="umntypechoice" method="post" 
		  action="markbook.php" target="viewmarkbook">
		  <input name="tid" type="hidden" value="<?php print $tid;?>">
			<input name="current" type="hidden" value="class_view.php">		
			<label>&nbsp;CW</label>
			  <input  title="<?php print_string('classwork',$book);?>" 
				type="radio" name="umntype"
				value="cw" <?php if($umntype=='cw'){print 'checked';}?>
				onchange="document.umntypechoice.submit();" />
<?php
		if($cidsno==1){
?>
			<label>&nbsp;HW</label>
				<input title="<?php print_string('homework',$book);?>" 
				  type="radio" name="umntype"
				  value="hw" <?php if($umntype=='hw'){print 'checked';}?>
				  onchange="document.umntypechoice.submit();" />
<?php
			}
?>
			<label>&nbsp;T</label>
				<input  title="<?php print_string('formalassessments',$book);?>" 
					type="radio" name="umntype" 
					value="t" <?php if($umntype=='t'){print 'checked';}?>
					onchange="document.umntypechoice.submit();" />
				<br />
			<div><?php print_string('filterlist');?></div>
			<label><?php print_string('all');?></label>
				<input  title="<?php print_string('all');?>" type="radio" name="umntype"
				  value="%" <?php if($umntype=='%'){print 'checked';}?>
				  onchange="document.umntypechoice.submit();" />
		</form>
	  </div>

	</fieldset>
  </div>
<?php
include('scripts/end_options.php');
?>