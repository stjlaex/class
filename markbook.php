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
else{$displaymid = $_POST['displaymid'];}//new mark created by previous script

/**
 * Which year's MarkBook are we viewing?
 *
 */
if(isset($_POST['curryear']) and $_POST['curryear']!=''){
	if($_SESSION['markbookcurryear']!=$_POST['curryear']){
		$_SESSION['markbookcurryear'] = $_POST['curryear'];
		if ($displaymid == 0) {$displaymid = -1;}
		unset($_SESSION['cids']);
		unset($_SESSION['components']);
		unset($_SESSION['pids']);
		unset($_SESSION['profiles']);
		unset($_SESSION['umnfilter']);
		}
	}
else{
	if(!isset($_SESSION['markbookcurryear'])) {
		$_SESSION['markbookcurryear'] = get_curriculumyear();
		}
	}
$curryear=$_SESSION['markbookcurryear'];
$current_curryear=get_curriculumyear();

/**
 * If the classes selection has changed then need to refresh some of
 * the session data for components and stuff (but don't want ot do
 * this for every page load.
 */
if(isset($_POST['cids'])){
	$pids=array();
	$components = array();
	$classes = array();
	$profiles = array();
	if ($_SESSION['cids'] != $_POST['cids']) {
		$_SESSION['cids'] = $_POST['cids'];
		$_SESSION['umnrank'] = 'surname';
		}
	if ($displaymid == 0) {$displaymid = -1;}

	foreach ($_SESSION['cids'] as $cid) {
		/*this is used to describe the class*/
		$d_c = mysql_query("SELECT class.name, class.detail, class.subject_id AS bid, cohort.course_id AS crid, 
					   	cohort.stage FROM class, cohort WHERE class.id='$cid' AND cohort.id=class.cohort_id;");
		$classes[$cid] = mysql_fetch_array($d_c, MYSQL_ASSOC);
		/* Grab the class's subject components, will only only exlcude those which are status=U (unused) */
		$comps = list_subject_components($classes[$cid]['bid'], $classes[$cid]['crid']);
		foreach ($comps as $component) {
			if (!in_array($component['id'], $pids)) {
				$components[] = $component;
				$pids[] = $component['id'];
				/* Grab the subject component's components ie. strands. Restrict to AV (for all validating) */
				$strands = list_subject_components($component['id'], $classes[$cid]['crid'], 'AV');
				foreach ($strands as $strand) {
					if (!in_array($strand['id'], $pids)) {
						$strand['name'] = '&nbsp;&nbsp;' . $strand['name'];
						$pids[] = $strand['id'];
						$components[] = $strand;
						}
					}
				}
			}
		}
	$profiles = (array)list_assessment_profiles($classes[$cid]['crid'], $classes[$cid]['bid']);

	$_SESSION['pids'] = $pids;
	$_SESSION['components'] = $components;
	$_SESSION['classes'] = $classes;
	$_SESSION['profiles'] = $profiles;

	/* Tries to recall a tid's previous choice of pid for this class*/
	if (!in_array($_SESSION['pid'], $pids)) {
		//$etid=$tid;
		$d_component = mysql_query("SELECT component_id FROM tidcid 
						WHERE class_id='$cid' AND teacher_id='$tid';");
		if (mysql_num_rows($d_component) > 0 and in_array(mysql_result($d_component, 0), $pids)) {
			$_SESSION['pid'] = mysql_result($d_component, 0);
			}
		else {
			$_SESSION['pid'] = '';
			}
		}
	}

/* If the component selection has changed then update */
if (isset($_POST['pid'])) {
	if ($_SESSION['pid'] != $_POST['pid']) {
		$_SESSION['pid'] = $_POST['pid'];
		$pid = $_SESSION['pid'];
		foreach ($_SESSION['cids'] as $cid) {
			$d_component = mysql_query("UPDATE tidcid SET component_id='$pid' 
						WHERE class_id='$cid' AND teacher_id='$tid'");
			}
		$_SESSION['umnrank'] = 'surname';
		}
	if ($displaymid==0){$displaymid=-1;}
	}

/* If the column-type filter has changed then update */
if (isset($_POST['umnfilter'])) {
	if ($_SESSION['umnfilter'] != $_POST['umnfilter']) {
		$_SESSION['umnfilter'] = $_POST['umnfilter'];
		$umnfilter = $_SESSION['umnfilter'];
			}
	if ($displaymid == 0) {$displaymid = -1;}
	}

/* Now initialise all of the variables from the session data*/
if (!isset($_SESSION['cids'])) {$_SESSION['cids'] = array();}
$cids = $_SESSION['cids'];
$cidsno = sizeof($cids);
if (!isset($_SESSION['classes'])) {$_SESSION['classes'] = array();}
$classes = $_SESSION['classes'];
if (!isset($_SESSION['profiles'])) {$_SESSION['profiles'] = array();}
$profiles = $_SESSION['profiles'];
if (!isset($_SESSION['pids'])) {
	$_SESSION['pids'] = array();
	$_SESSION['components'] = array();
	}
$pids=$_SESSION['pids'];
$components = $_SESSION['components'];
if (!isset($_SESSION['pid'])) {$_SESSION['pid']='';}
$pid=$_SESSION['pid'];
if (!isset($_SESSION['umnfilter']) or ($cidsno > 1 and $_SESSION['umnfilter'] == 'hw')) {$_SESSION['umnfilter'] = '%';}
$umnfilter = $_SESSION['umnfilter'];
if (sizeof($profiles) == 0 and $umnfilter[0] == 'p') {
	/* If a profile was previously selected but no profiles now available... */
	$umnfilter='%';
	$umnfilterno='-1';
	$_SESSION['umnfilter'] = $umnfilter;
	if ($displaymid == 0) {$displaymid = -1;}
	}
elseif($umnfilter[0] == 'p') {
	$umnfilterno = substr($umnfilter, 1);
	}
if (!isset($_SESSION['umnrank'])) {$_SESSION['umnrank'] = 'surname';}
$umnrank = $_SESSION['umnrank'];
/* Current date for attendance. */
$attdate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
/* Previous lesson attendance displayed. */
$lessonatt = 0;
if ($cidsno == 1) {
	if (isset($_GET['lessonatt'])) {$lessonatt = $_GET['lessonatt'];}
	elseif (isset($_SESSION['lessonatt'])) {$lessonatt = $_SESSION['lessonatt'];}
	}
$_SESSION['lessonatt'] = $lessonatt;
?>

	<div class="markcolor" id="bookbox">
<?php
	if ($current != '' and $cidsno > 0) {
		include ($book . '/' . $current);
		}
?>
	</div>

	<div style="visibility:hidden;" id="hiddenbookoptions">
		<form id="classchoice" name="classchoice" method="post" action="markbook.php" target="viewmarkbook">
			<fieldset class="markbook">
				<legend><?php print_string('classes'); ?></legend>  
<?php
			include ('scripts/list_class.php');
?>
			</fieldset>
		</form>
<?php
	if(isset($umns) and isset($cid)){
?>
		<form id="gradechoice" name="gradechoice"  method="post" action="markbook.php" target="viewmarkbook">
			<fieldset class="markbook">
				<legend><?php print_string('markcolumns');?></legend>
				<select id="mids" name="mids[]" size="14" multiple="multiple"  title="<?php print_string('choose');?>" onChange="changeMarkDisplay(this.form);">
<?php
		for ($col = 0; $col < sizeof($umns); $col++) {
			if ($umns[$col]['component'] == $pid or $pid == '') {
				print '<option class="' . $umns[$col]['displayclass'] . '" value="' . $umns[$col]['id'] . '" id="sel-' . $umns[$col]['id'] . '">';
				if ($umns[$col]['component'] != '') {print $umns[$col]['component'] . ': ';}
				print $umns[$col]['topic'] . ' (' . $umns[$col]['entrydate'] . ')</option>';
				}
			}
?>
				</select>
			</fieldset>
		</form>

		<form id="umnfilterchoice" name="umnfilterchoice" method="post" action="markbook.php" target="viewmarkbook">
			<fieldset class="markbook markbook-filter">
				<legend>
<?php 
		print_string('filterlist');
		/**
		 * This is the chart button which present for certain filter options which are defined as a profile
		 */
		if(!empty($umnfilter) and $umnfilterno>-1 and isset($cid)){
			$currentprofile=$profiles[$umnfilterno];
?>
				<div id="<?php print $currentprofile['id']; ?>" title="<?php print_string('chart');?>" class="sidebuttons" style="display:inline;padding:0 0 0 15px;margin:0;">
					<button type="button" name="chart" onclick="window.frames['viewmarkbook'].clickToAction(this);" value="report_profile_print.php">
						<img alt="Chart" src="images/charter.png"/>
					</button>
					<div id="<?php print 'xml-' . $currentprofile['id']; ?>" style="display:none;">
<?php
				/*TODO: should bid and pid be past as params here? Seems to stop report_profile_print from working*/
				$currentprofile['bid'] = $bid[0];
				$currentprofile['pid'] = $pid;
				$currentprofile['stage'] = $classes[$cid]['stage'];
				$currentprofile['classes'] = '';
				unset($currentprofile['component_status']);
				unset($currentprofile['celldisplay']);
				unset($currentprofile['rating_name']);
				foreach ($cids as $cindex => $cid) {
					$currentprofile['classes'] .= $classes[$cid]['name'] . ' ';
					}
				xmlechoer('Profile', $currentprofile);
?>
					</div>
				</div>
<?php
			}
?>
				</legend>
<?php

	if($currentprofile==""){
		$d_p=mysql_query("SELECT * FROM categorydef WHERE type='pro' AND subtype='NP';");
		if(mysql_num_rows($d_p)>0){
			$currentprofile['id']=mysql_result($d_p,0,'id');
			$currentprofile['name']=mysql_result($d_p,0,'name');
			$currentprofile['stage']=mysql_result($d_p,0,'stage');
			$currentprofile['bid']=mysql_result($d_p,0,'subject_id');
			$currentprofile['course_id']=mysql_result($d_p,0,'course_id');
			}
		}
	list_markbook_filters($profiles,$umnfilter,$currentprofile,$cid,$cidsno,$classes);
?>
				<input name="tid" type="hidden" value="<?php print $tid; ?>">
				<input name="current" type="hidden" value="class_view.php">
			</fieldset>
		</form>
<?php
	}
?>

		<form id="componentchoice" name="componentchoice" method="post" action="markbook.php" target="viewmarkbook">
			<fieldset class="markbook">
				<legend>&nbsp;</legend>
				<input name="tid" type="hidden" value="<?php print $tid; ?>">
				<input name="current" type="hidden" value="class_view.php">
<?php
		if(sizeof($pids)>0){
?>
				<select name="pid" size="1" onchange="document.componentchoice.submit();">
					<option value="" <?php if($pid==''){print 'selected="selected"';} ?>><?php print_string('allcomponents'); ?></option>
<?php
			foreach ($components as $index => $component) {
				print '<option ';
				if($component['id']==$pid){print 'selected="selected"';}
				print ' value="' . $component['id'] . '">' . $component['name'] . '</option>';
				}
?>
				</select>
<?php
			}
?>
			</fieldset>
		</form>

		<form id="markbookchoice" name="markbookchoice" method="post" action="markbook.php" target="viewmarkbook" style="float:right;">
			<fieldset class="markbook">
				<legend><?php print_string('curriculumyear'); ?></legend>
<?php
			$onsidechange = 'yes';
			include ('scripts/list_curriculum_year.php');
?>
			</fieldset>
		</form>
	</div>
<?php
include ('scripts/end_options.php');
?>
