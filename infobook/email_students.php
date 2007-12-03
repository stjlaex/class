<?php
/**								   email_students.php
 */

$action='student_list.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}

include('scripts/sub_action.php');

if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

$recipients=array();
/*cycle through the student rows*/
while(list($index,$sid)=each($sids)){

	$Student=fetchStudent($sid);
	if($CFG->emailoff!='yes'){
		if($Student['EmailAddress']['value']!=''){
			$recipients[]=$Student['EmailAddress']['value'];
			}
		}

	}
?>
  <form name="redirect1" method="post" action="<?php print $host;?>" target="_self">
	<input type="hidden" name="current" value="<?php if(isset($action)){print $action;}?>" />
	<input type="hidden" name="cancel" value="<?php if(isset($cancel)){print $cancel;}?>" />
	<input type="hidden" name="choice" value="<?php if(isset($choice)){print $choice;}?>" />
<?php
$action_post_vars=array('sids');
if(isset($action_post_vars)){
	include('scripts/set_action_post_vars.php');
	}
?>
  </form>
  <form name="redirect" method="post" action="webmail.php" target="viewwebmail">
<?php
$action_post_vars=array('recipients');
if(isset($action_post_vars)){
	include('scripts/set_action_post_vars.php');
	}
?>
  </form>
  <script>setTimeout("parent.viewBook('webmail');",10);</script>
  <script>setTimeout('document.redirect.submit()',10);</script>
  <script>setTimeout('document.redirect1.submit()',10);</script>
