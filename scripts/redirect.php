<?php
/**											scripts/redirect.php
 *
 * Fill the array $action_post_vars with any variable names to be posted
 */

if(!isset($pausetime)){$pausetime=150;}
?>
<form name="redirect" method="post" action="<?php print $host;?>" target="_self">
	<input type="hidden" name="current" value="<?php if(isset($action)){print $action;}?>" />
	<input type="hidden" name="cancel" value="<?php if(isset($cancel)){print $cancel;}?>" />
	<input type="hidden" name="choice" value="<?php if(isset($choice)){print $choice;}?>" />
<?php
if(isset($action_post_vars)){
	include('scripts/set_action_post_vars.php');
	}
?>
</form>
<script>setTimeout('document.redirect.submit()', <?php print $pausetime;?>);</script>
