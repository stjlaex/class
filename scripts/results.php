<?php
/**							scripts/results.php
 */
if(!isset($result)){$result=array();}
if(!isset($error)){$error=array();}
$pausetime=2000;
?>
<div class="content">
	<fieldset class="center">
	  <legend>Results</legend>
<?php
for($c=0;$c<sizeof($result);$c++){
	print '<p class="success">'.$result[$c].'</p>';
	$pausetime=$pausetime+200;
	}

for($c=0;$c<sizeof($error);$c++){
	print '<p class="warn">'.$error[$c].'</p>';
	$pausetime=$pausetime+1000;
	}
?>
	</fieldset>
  </div>
<?php
	$error=array();
	$result=array();
?>