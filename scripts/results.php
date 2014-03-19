<?php
/**							scripts/results.php
 */

if(isset($result) or isset($error)){

	$pausetime=2000;
?>
<div class="content">
	<fieldset class="divgroup">
	  <h4>Results</h4>
<?php
	if(isset($result)){
		for($c=0;$c<sizeof($result);$c++){
			print '<p class="success">'.$result[$c].'</p>';
			$pausetime=$pausetime+200;
			}
		}

	if(isset($error)){
		for($c=0;$c<sizeof($error);$c++){
			print '<p class="warn">'.$error[$c].'</p>';
			$pausetime=$pausetime+1000;
			}
		}
?>
	</fieldset>
  </div>
<?php
	}
	$error=array();
	$result=array();
?>