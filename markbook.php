<?php 
/**														markbook.php
 *	This is the hostpage for the markbook
 *	The classes being viewed is set by $cids 
 */
$host='markbook.php';
$book='markbook';
$current='';
$choice='';
$cancel='';

include('scripts/head_options.php');

if(!isset($_POST['displaymid'])){$displaymid=0;}//means no change to marks displayed
else{$displaymid=$_POST['displaymid'];}//new mark created by previous script

$pids=array();
if(isset($_POST{'cids'})){
	/*If the classes selection has changed then*/
	if($_SESSION{'cids'}!=$_POST{'cids'}){
	$_SESSION{'cids'}=$_POST{'cids'};
	$_SESSION{'umnrank'}='surname';}
	if($displaymid==0){$displaymid=-1;}
	foreach($_SESSION{'cids'} as $key => $cid){
		$d_components=mysql_query("SELECT component.id FROM component JOIN
		class ON component.course_id=class.course_id AND component.subject_id=class.subject_id
		WHERE class.id='$cid' ORDER BY component.id");
		while($component=mysql_fetch_array($d_components,MYSQL_ASSOC)){
			if(!in_array($component['id'],$pids)){$pids[]=$component['id'];}
			}
		}
	$_SESSION{'pids'}=$pids;
	if(!in_array($_SESSION{'pid'},$pids)){
		$etid=$tid;
		$d_component=mysql_query("SELECT component_id FROM tidcid 
						WHERE class_id='$cid' AND teacher_id='$tid'");
		if(mysql_num_rows($d_component)>0){
			$_SESSION{'pid'}=mysql_result($d_component,0);
			}
		else{
			$_SESSION{'pid'}='';
			}
		}
	}

if(isset($_POST{'pid'})){
	/*If the component selection has changed then*/
	if($_SESSION{'pid'}!=$_POST{'pid'}){
	$_SESSION{'pid'}=$_POST{'pid'};
	$pid=$_SESSION{'pid'};
	foreach($_SESSION{'cids'} as $key => $cid){
		$d_component=mysql_query("UPDATE tidcid SET component_id='$pid' 
						WHERE class_id='$cid' AND teacher_id='$tid'");
		}
	$_SESSION{'umnrank'}='surname';}
	if($displaymid==0){$displaymid=-1;}
	}

if(!isset($_SESSION{'cids'})){$_SESSION{'cids'}=array('','');}
if(!isset($_SESSION{'pid'})){$_SESSION{'pid'}='';}
if(!isset($_SESSION{'umnrank'})){$_SESSION{'umnrank'}='surname';}

$cids=$_SESSION{'cids'};
$pids=$_SESSION{'pids'};
$pid=$_SESSION{'pid'};
$umnrank=$_SESSION{'umnrank'};

if(isset($_GET{'current'})){$current=$_GET{'current'};}
if(isset($_GET{'choice'})){$choice=$_GET{'choice'};}
if(isset($_GET{'cancel'})){$choice=$_GET{'cancel'};}
if(isset($_POST{'current'})){$current=$_POST{'current'};}
if(isset($_POST{'choice'})){$choice=$_POST{'choice'};}
if(isset($_POST{'cancel'})){$cancel=$_POST{'cancel'};}
?>

<div class="markcolor" id="bookbox">
<?php
	if($current!=''){
		$view = 'markbook/'.$current;
		include($view);
		}
?>
</div>

<div style="visibility:hidden;" id="hiddenbookoptions">
<fieldset class="markbook"><legend><?php print_string('classesandmarks');?></legend>
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
   foreach($pids as $key => $spid){
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
	  <form id="gradechoice" name="gradechoice" method="post" 
		action="markbook.php" target="viewmarkbook">
		<select id="mids" name="mids[]" size="12"
		  multiple="multiple" onChange="changeMarkDisplay(this.form);">
<?php
if(isset($umns)){
   	for($col=0;$col<sizeof($umns);$col++){
	   	if($umns[$col]['component']==$pid or $pid==''){
			print "<option value='".$umns[$col]['id']."' id='sel-".$umns[$col]['id']."'>";
			if($umns[$col]['component']!=''){print $umns[$col]['component'].": ";}
			print $umns[$col]['topic']." (".$umns[$col]['entrydate'].")</option>";
			}
		}
	}
?>
		</select>
	  </form>
	</fieldset>
  </div>
<?php
include('scripts/end_options.php');
?>
