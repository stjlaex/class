<?php
/**												lib/html_functions.php
 *
 * Generic functions for producing html elements.
 */

function all_extrabuttons($extrabuttons,$book='',
						  $onclick='processContent(this)',$class=''){
	if(is_array($extrabuttons)){
		while(list($description,$attributes)=each($extrabuttons)){
			if(!isset($attributes['onclick'])){$attributes['onclick']=$onclick;}
			if(!isset($attributes['title'])){$attributes['title']=$description;}
?>
  <button onClick="<?php print $attributes['onclick'];?>" 
	<?php print $class;?> 
	title="<?php print_string($attributes['title'],$book);?>" 
	name="<?php print $attributes['name'];?>" 
	value="<?php print $attributes['value'];?>">
	<?php print_string($description,$book);?>
  </button>
<?php
			 }
		}
	}

function three_buttonmenu($extrabuttons='',$book=''){
?>
<div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
	<button onClick="processContent(this);" name="sub" style="margin-left:1em;"
	  value="Submit"><?php print_string('submit');?></button>
	<button onClick="processContent(this);" name="sub" 
	  value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" 
	  value="Reset"><?php print_string('reset');?></button>
</div>

<?php
	}

function two_buttonmenu($extrabuttons='',$book=''){
?>
  <div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
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
  	<button onClick="processContent(this);" <?php if($currentkey==0){print 'disabled="disabled"
	style="visibility:hidden;"';} ?> name="sub" value="Previous"><?php print_string('previous');?></button>
	<button onClick="processContent(this);" <?php if($currentkey==($maxkey-1)){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Next"><?php print_string('next');?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
  </div>

<?php
	}

function threeplus_buttonmenu($currentkey,$maxkey){
	if($currentkey==''){$currentkey=1;}//Register only needs this
?>
  <div class="buttonmenu">
  	<button onClick="processContent(this);" <?php if($currentkey==0){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Previous"><?php print_string('previous');?></button>
	<button onClick="processContent(this);" <?php if($currentkey==($maxkey-1)){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Next"><?php print_string('next');?></button>
	<button onClick="processContent(this);" name="sub"  style="margin-left:1em;"
	  value="Submit"><?php print_string('submit');?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
  </div>

<?php
	}

function twoplusprint_buttonmenu($extrabuttons='',$book=''){
?>
  <div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
	<button onClick="processContent(this);" name="sub" 
		value="Print"><?php print_string('printselected');?></button>
	<button onClick="processContent(this);" name="sub" style="margin-left:1em;" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
  </div>

<?php
	}

function rowaction_buttonmenu($imagebuttons,$extrabuttons='',$book=''){

	if(is_array($imagebuttons)){
		while(list($imageclass,$attributes)=each($imagebuttons)){
?>
  <button class="rowaction" 
	title="<?php print_string($attributes['title']);?>" 
	name="<?php print $attributes['name'];?>" 
	value="<?php print $attributes['value'];?>" onClick="clickToAction(this)">
	<img class="<?php print($imageclass);?>" />
  </button>
<?php
			 }
		}
   	all_extrabuttons($extrabuttons,$book,'clickToAction(this)','class="rowaction" ');
	}

function xmlarray_form($Array,$no='',$caption='',$tab=1,$book=''){
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
		  <?php print_string($val['label'],$book);?>
		</label>
	  </td>
	  <td>
<?php																	   
			if($val['type_db']=='enum'){
				$enum=getEnumArray($val['field_db']);
				print '<select name="'.$val['field_db'].$no.'" ';
				print ' tabindex="'.$tab++.'" ';
				if(isset($val['inputtype'])){print ' class="'.$val['inputtype'].'" ';}
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
				$required='no';$todate='';$xmldate=$val['field_db'].$no;
				$todate=$val['value'];
				include('scripts/jsdate-form.php');
				}
			else{
?>
		<input type="text" id="<?php print $val['label'];?>" 
			class="<?php if(isset($val['inputtype'])){print $val['inputtype'];}?>" 
				name="<?php print $val['field_db'].$no; ?>" 
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

function selery_stick($choices,$choice='',$book=''){
?>
		<ul class="selery">
<?php
	while(list($page,$title)=each($choices)){
?>
		  <li onclick="selerySubmit(this)" 
			<?php if($choice==$page){print 'class="checked" ';}?> >
			<input type="radio"
				<?php if($choice==$page){print 'checked="checked" ';} ?>
			  value="<?php print $page;?>" name="current" >
			  <p><?php print_string($title,$book);?></p>
			</input>
		  </li>
<?php
		}
?>
		</ul>
<?php
	}

/* include scripts/set_list_variables.php first to define all the options */
/* set in the $vars array (see in there how to over-ride defaults)*/
/* the $d_list should be the SELECT result from mysql with AS id and */
/* AS name used */
function list_select_db($d_list,$vars,$book=''){
	$valuefield=$vars['valuefield'];
	$descriptionfield=$vars['descriptionfield'];
	if($vars['label']!=''){
?>
  <label for="<?php print $vars['id'];?>">
	<?php print_string($vars['label'],$book);?>
  </label>
<?php
		  }
?>
  <select 
	id="<?php print $vars['id'];?>" 
<?php 
	if($vars['multi']>1){print ' name="'.$vars['name'].$vars['i'].'[]" multiple="multiple" ';}
	else{print ' name="'.$vars['name'].$vars['i'].'" ';}
?>
	tabindex="<?php print $vars['tab'];?>"  
	size="<?php print $vars['multi'];?>"
	<?php print $vars['style'];?>
	<?php if($vars['onsidechange']=='yes'){print ' onChange="document.'.$book.'choice.submit();"';}?>
	<?php if($vars['onchange']=='yes'){print ' onChange="processContent(this);"';}?>
	<?php if($vars['required']=='yes'){ print ' class="required" ';} ?>
	>
    <option value=""></option>
<?php
	while($item=mysql_fetch_array($d_list,MYSQL_ASSOC)){
		print '<option ';
		if($vars['multi']==1){
			if($vars['selectedvalue']==$item[$valuefield]){print 'selected="selected"';}
			}
		elseif(in_array($item[$valuefield],$vars['selectedvalue'])){print 'selected="selected"';}
		print	' value="'.$item[$valuefield].'"> '.$item[$descriptionfield].'</option>';
		}
?>
  </select>
<?php
	}

/* as for list_select_db except that the $list is not a mysql result resource*/
function list_select_list($list,$vars,$book=''){
	$valuefield=$vars['valuefield'];
	$descriptionfield=$vars['descriptionfield'];
	if($vars['label']!=''){
?>
  <label for="<?php print $vars['id'];?>">
	<?php print_string($vars['label'],$book);?>
  </label>
<?php
		}
?>  
<select id="<?php print $vars['id'];?>" 
<?php 
	if($vars['multi']>1){print ' name="'.$vars['name'].$vars['i'].'[]" multiple="multiple" ';}
	else{print ' name="'.$vars['name'].$vars['i'].'" ';}
?>
	tabindex="<?php print $vars['tab'];?>"  
	size="<?php print $vars['multi'];?>"
	<?php print $vars['style'];?>
	<?php if($vars['onsidechange']=='yes'){print ' onChange="document.'.$book.'choice.submit();"';}?>
	<?php if($vars['onchange']=='yes'){print ' onChange="processContent(this);"';}?>
	<?php if($vars['required']=='yes'){ print ' class="required" ';} ?>
	>
    <option value=""></option>
<?php
	while(list($index,$item)=each($list)){
		print '<option ';
		if($vars['multi']==1){
			if($vars['selectedvalue']==$item[$valuefield]){print  ' selected="selected"';}
			}
		elseif(in_array($item[$valuefield],$vars['selectedvalue'])){print ' selected="selected"';}
		print	' value="'.$item[$valuefield].'">'.$item[$descriptionfield].'</option>';
		}
?>
  </select>
<?php
	}


function list_select_enum($fieldname,$vars,$book=''){
	$enum=getEnumArray($fieldname);
	if($vars['label']!=''){
?>
  <label for="<?php print $vars['id'];?>">
	<?php print_string($vars['label'],$book);?>
  </label>
<?php 
		}
?>
  <select 
	id="<?php print $vars['id'];?>" 
<?php 
	if($vars['multi']>1){print ' name="'.$vars['name'].$vars['i'].'[]" multiple="multiple" ';}
	else{print ' name="'.$vars['name'].$vars['i'].'" ';}
?>
	tabindex="<?php print $vars['tab'];?>"  
	size="<?php print $vars['multi'];?>"
	<?php print $vars['style'];?>
	<?php if($vars['onsidechange']=='yes'){print ' onChange="document.'.$book.'choice.submit();"';}?>
	<?php if($vars['onchange']=='yes'){print ' onChange="processContent(this);"';}?>
	<?php if($vars['required']=='yes'){ print ' class="required" ';} ?>
	>
    <option value=""></option>
<?php
			 while(list($inval,$description)=each($enum)){	
				 print '<option ';
				 if($vars['selectedvalue']==$inval){print ' selected="selected"';}
				 print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
				 }
?>
	</select>
<?php
	}
?>