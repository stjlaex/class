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

function check_yesno($name='answer',$choice='no'){
?>
  <table class="listmenu">
	<caption><?php print_string('readytocontinue'); ?></caption>
	<tr>
	  <td>
	<label for="yes"><?php print_string('yes');?></label>
	<input type="radio" name="<?php print $name;?>" title="yes" id="yes" 
	  value="yes" <?php if($choice=='yes'){print 'checked';}?> />
	  </td>
	  <td>
	<label for="no"><?php print_string('no');?></label>
	<input type="radio" name="<?php print $name;?>" title="no" id="no"
	  value="no" <?php if($choice=='no'){print 'checked';}?> />
	  </td>
	</tr>
 </table>
<?php
	}

function xmlarray_form($Array,$no='',$caption=''){
	if("$Array"=='Student'){$book='infobook';}
	else{$book='infobook';}
?>
  <table class="listmenu">
<?php
	if($caption!=''){print '<caption>'.get_string($caption,$book).'</caption>';}
	while(list($key,$val)=each($Array)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
?>
	<tr>
	  <td><label><?php print_string($val['label'],'infobook'); ?></label></td>
	  <td>
<?php																	   
			if($val['type_db']=='enum'){
				$enum=getEnumArray($val['field_db']);
				print '<select name="'.$val['field_db'].$no.'" size="1">';
				print '<option value=""></option>';
				while(list($inval,$description)=each($enum)){	
					print '<option ';
					if($val['value']==$inval){print 'selected="selected"';}
					print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
					}
				print '</select>';
				}
			elseif($val['type_db']=='date'){
				$required='no';$todate=''; $xmldate=$val['field_db'].$no;
				include('scripts/jsdate-form.php');
				}
			else{
?>
		<input type="text" name="<?php print $val['field_db'].$no; ?>" 
							value="<?php print $val['value']; ?>" />
<?php
				 }
?>
	  </td>
	</tr>
<?php
			}
		}
?>
  </table>
<?php

	}

?>