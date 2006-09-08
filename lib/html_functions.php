<?php
/*												lib/html_functions.php
 *
 * Generic functions for producing html entities.
 */

function three_buttonmenu($extrabuttons=''){
?>
<div class="buttonmenu">
<?php
	if(is_array($extrabuttons)){
		 while(list($description,$value)=each($extrabuttons)){
?>
	<button onClick="processContent(this);" name="<?php print $value['name'];?>" 
	  value="<?php print $value['value'];?>"><?php print_string($description);?></button>
<?php
			 }
		}
?>
	<button onClick="processContent(this);" name="sub"  style="margin-left:1em;"
	  value="Submit"><?php print_string('submit');?></button>
	<button onClick="processContent(this);" name="sub" 
	  value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" 
	  value="Reset"><?php print_string('reset');?></button>
</div>

<?php
	}

function two_buttonmenu($extrabuttons=array()){
?>
  <div class="buttonmenu">
<?php
		 while(list($description,$value)=each($extrabuttons)){
?>
	<button onClick="processContent(this);" name="<?php print $value['name'];?>" 
	  value="<?php print $value['value'];?>"><?php print_string($description);?></button>
<?php
			 }
?>
	<button onClick="processContent(this);" name="sub"  style="margin-left:1em;"
	  value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" 
	  value="Reset"><?php print_string('reset');?></button>
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

function xmlarray_form($Array,$no='',$caption='',$tab=1){
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
	  <td>
		<label for="<?php print $val['label'];?>">
		  <?php print_string($val['label'],'infobook');?>
		</label>
	  </td>
	  <td>
<?php																	   
			if($val['type_db']=='enum'){
				$enum=getEnumArray($val['field_db']);
				print '<select name="'.$val['field_db'].$no.'" ';
				print ' tabindex="'.$tab++.'" ';
				print ' class="'.$val['inputtype'].'" ';
				print ' id="'.$val['label'].'" size="1">';
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
		<input type="text" id="<?php print $val['label'];?>" 
			class="<?php print $val['inputtype'];?>" name="<?php print $val['field_db'].$no; ?>" 
					tabindex="<?php print $tab++;?>" value="<?php print $val['value']; ?>" />
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

	return $tab;
	}

?>