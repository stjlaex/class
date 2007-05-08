<?php
/**											scripts/redirect.php
 *
 * Fill the array $action_post_vars with any variable names to be posted
 *
 */
if(!isset($pausetime)){$pausetime=150;}
?>

<form name="redirect" method="post" action="<?php print $host;?>" target="_self">
	<input type="hidden" name="current" value="<?php if(isset($action)){print $action;}?>" />
	<input type="hidden" name="cancel" value="<?php if(isset($cancel)){print $cancel;}?>" />
	<input type="hidden" name="choice" value="<?php if(isset($choice)){print $choice;}?>" />

	<input type="hidden" name="cid" value="<?php if(isset($cid)){print $cid;}?>" />
	<input type="hidden" name="sid" value="<?php if(isset($sid)){print $sid;}?>" />
	<input type="hidden" name="checkmid[]" value="<?php if(isset($mid)){print $mid;}?>" />
	<input type="hidden" name="mid" value="<?php if(isset($mid)){print $mid;}?>" />
	<input type="hidden" name="yid" value="<?php if(isset($yid)){print $yid;}?>" />
	<input type="hidden" name="fid" value="<?php if(isset($fid)){print $fid;}?>" />
	<input type="hidden" name="bid" value="<?php if(isset($bid)){print $bid;}?>" />
<?php
if(isset($rids)){
	while(list($index, $rid)=each($rids)){
?>
	 	<input type="hidden" name="rids[]" value="<?php print $rid;?>">
<?php
		}
	}
if(isset($displaymid)){
?>
	 	<input type="hidden" name="displaymid" value="<?php print $displaymid;?>">
<?php
	}
if(isset($date)){
?>
	 	<input type="hidden" name="date" value="<?php print $date;?>">
<?php
	}
if(isset($entrydate)){
?>
	 	<input type="hidden" name="entrydate" value="<?php print $entrydate;?>">
<?php
	}
if(isset($date0)){
?>
	 	<input type="hidden" name="date0" value="<?php print $date0;?>">
<?php
	}
if(isset($date1)){
?>
	 	<input type="hidden" name="date1" value="<?php print $date1;?>">
<?php
	}

if(isset($action_post_vars)){
	include('scripts/set_action_post_vars.php');
	}
?>

</form>

<script>setTimeout('document.redirect.submit()', <?php print $pausetime;?>);</script>
