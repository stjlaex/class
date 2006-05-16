<?php
/*												lib/html_functions.php
 *
 * Generic functions for producing html entities. 
 * 
 */

function three_buttonmenu($extrabuttons=array()){
?>
<div class="buttonmenu">
<?php
		 while(list($description,$value)=each($extrabuttons)){
?>
	<button onClick="processContent(this);" name="<?php print $value['name'];?>" value="<?php
	print $value['value'];?>"><?php print_string($description);?></button>
<?php
			 }
?>
	<button onClick="processContent(this);" name="sub" value="Submit"><?php print_string('submit');?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
</div>

<?php
	}

function two_buttonmenu($extrabuttons=array()){
?>
<div class="buttonmenu">
<?php
		 while(list($description,$value)=each($extrabuttons)){
?>
	<button onClick="processContent(this);" name="<?php print $value['name'];?>" value="<?php
	print $value['value'];?>"><?php print_string($description);?></button>
<?php
			 }
?>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
</div>

<?php
	}

function twoplus_buttonmenu($currentkey,$maxkey){
?>
<div class="buttonmenu">
  	<button onClick="processContent(this);" <?php
	if($currentkey==0){print 'disabled="disabled"
	style="visibility:hidden;"';} ?> name="sub" value="Previous"><?php print_string('previous');?></button>
	<button onClick="processContent(this);" <?php
	if($currentkey==($maxkey-1)){print 'disabled="disabled"
	style="visibility:hidden;"';} ?> name="sub" value="Next"><?php print_string('next');?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
</div>

<?php
	}

function twoplusprint_buttonmenu(){
?>
<div class="buttonmenu">
	<button onClick="processContent(this);" name="sub" 
		value="Print"><?php print_string('printselected');?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
</div>

<?php
	}
?>