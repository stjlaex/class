<?php
/**						scripts/sub_action.php
 *
 * Returns the value of the submit button in $sub
 * and redirects if $sub='Cancel' or if $sub is blank
 * simply reloads the current page (with some new post value presumably)
 */

	if(isset($_POST['sub'])){
		$sub=$_POST['sub'];
		if($sub=='Cancel'){
			if($cancel==''){$action='';$choice='';}
			else{$action=$cancel;}
			include('scripts/redirect.php');
			exit;
			}
		}
	else{$sub='';}
?>