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
	tabindex="<?php // print $tab++;?>"
	<?php print $class;?> 
	<?php if(isset($attributes['pathtoscript'])){print '
						  pathtoscript="'.$attributes['pathtoscript'].'" ';}?>
	<?php if(isset($attributes['xmlcontainerid'])){print '
						  xmlcontainerid="'.$attributes['xmlcontainerid'].'" ';}?>
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

function twoplus_buttonmenu($currentkey,$maxkey,$extrabuttons='',$book=''){
?>
  <div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
  	<button onClick="processContent(this);" <?php if($currentkey==0){print 'disabled="disabled"
	style="visibility:hidden;"';} ?> name="sub" value="Previous"><?php print_string('previous',$book);?></button>
	<button onClick="processContent(this);" <?php if($currentkey==($maxkey-1)){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Next"><?php print_string('next',$book);?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
  </div>

<?php
	}

function threeplus_buttonmenu($currentkey,$maxkey,$extrabuttons='',$book=''){
	if($currentkey==''){$currentkey=1;}//Register only needs this
?>
  <div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
  	<button onClick="processContent(this);" <?php if($currentkey==0){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Previous"><?php print_string('previous',$book);?></button>
	<button onClick="processContent(this);" <?php if($currentkey==($maxkey-1)){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Next"><?php print_string('next',$book);?></button>
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
		/* If the table_db attribute is omitted it indicates this is not */
		/* a field for entry by the user - this */
		/* may be becuase it is disabled or */
		/* because it is dependent on some */
		/* other value - it will not appear in the table*/
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
		$tab=xmlelement_input($val,$no,$tab,$book);
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


function xmlarray_divform($Array,$no='',$caption='',$tab=1,$book=''){

	if($caption!=''){print '<caption>'.get_string($caption,$book).'</caption>';}
	while(list($key,$val)=each($Array)){
		/* If the table_db attribute is omitted it indicates this is not */
		/* a field for entry by the user - this */
		/* may be because it is disabled or */
		/* because it is dependent on some */
		/* other value - it will not appear in the table*/
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
?>
  <div class="center">
	<label for="<?php print $val['label'];?>">
	  <?php print_string($val['label'],$book);?>
	</label>
	<?php $tab=xmlelement_input($val,$no,$tab,$book);?>
  </div>
<?php
			}
		}

	return $tab;
	}

/**
 * Prints one form element including the
 * element's label and its formatted value.
 */
function xmlelement_input($val,$no,$tab,$book){
	$pattern='';
	if(($val['value']=='' or $val['value']==' ') and isset($val['default_value'])){
		$setval=$val['default_value'];
		}
	else{$setval=$val['value'];}

	if($val['type_db']=='enum'){
		$setval=strtoupper($setval);
		$enum=getEnumArray($val['field_db']);
		print '<select name="'.$val['field_db'].$no.'" ';
		print ' tabindex="'.$tab++.'" ';
		if(isset($val['inputtype'])){print ' class="'.$val['inputtype'].'" ';}
		print ' id="'.$val['label'].'" size="1">';
		print '<option value=""></option>';
		while(list($inval,$description)=each($enum)){	
			print '<option ';
			if($setval==$inval){print 'selected="selected"';}
			print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
			}
		print '</select>';
		}
	elseif($val['type_db']=='date'){
		if(isset($val['inputtype']) and $val['inputtype']=='required'){$required='yes';}
		else{$required='no';}
		$todate=$setval;
		$xmldate=$val['field_db'].$no;
		$todate=$setval;
		include('scripts/jsdate-form.php');
		}
	elseif($val['type_db']=='text'){
?>
		<textarea rows="2" cols="80"  id="<?php print $val['label'];?>" 
			class="<?php if(isset($val['inputtype'])){print $val['inputtype'];}?>" 
				name="<?php print $val['field_db'].$no; ?>" 
					tabindex="<?php print $tab++;?>" ><?php print $setval; ?></textarea>
<?php
		}
	elseif(substr($val['type_db'],0,3)=='var' or substr($val['type_db'],0,3)=='cha'){
		if($val['field_db']=='email'){$pattern='pattern="email"';}
		/* TODO: add these patterns for all possible inputs BUT js
		 needs to be UTF8 aware first!*/
		//else{$pattern='pattern="alphanumeric"';}
		$field_type=split('[()]', $val['type_db']);
		$maxlength=$field_type[1];
?>
		<input type="text" id="<?php print $val['label'];?>" 
			<?php print $pattern;?> 
		  maxlength="<?php print $maxlength; ?>"
		  class="<?php if(isset($val['inputtype'])){print $val['inputtype'];}?>" 
		  name="<?php print $val['field_db'].$no; ?>" 
		  tabindex="<?php print $tab++;?>" value="<?php print $setval; ?>" />
<?php
		}
	elseif($val['type_db']=='smallint' or $val['type_db']=='decimal'){
		if($val['type_db']=='decimal'){$pattern='pattern="decimal"';}
		else{$pattern='pattern="integer"';}
		$field_type=split('[()]', $val['type_db']);
		$maxlength=$field_type[1];
?>
		<input type="text" id="<?php print $val['label'];?>" 
			<?php print $pattern;?> 
		  maxlength="<?php print $maxlength; ?>"
		  class="<?php if(isset($val['inputtype'])){print $val['inputtype'];}?>" 
		  name="<?php print $val['field_db'].$no; ?>" 
		  tabindex="<?php print $tab++;?>" value="<?php print $setval; ?>" />
<?php
		}
	else{
?>
		<input type="text" id="<?php print $val['label'];?>" 
		  class="<?php if(isset($val['inputtype'])){print $val['inputtype'];}?>" 
		  name="<?php print $val['field_db'].$no; ?>" 
		  tabindex="<?php print $tab++;?>" value="<?php print $setval; ?>" />
<?php
		}
	return $tab;
	}

/**
 * Prints one cell designed for a listmenu table including the
 * element's label and its formatted value.
 */
function xmlelement_display($val,$book){
	print '<td>';
	print '<label>'.get_string($val['label'],$book).'</label>';
	if($val['type_db']=='enum'){
		print_string(displayEnum($val['value'],$val['field_db']),$book);
		}
	elseif($val['type_db']=='date'){
		print display_date($val['value']);
		}
	/*	elseif($val['type_db']=='text'){
				 }
	elseif(substr($val['type_db'],0,3)=='var' or substr($val['type_db'],0,3)=='cha'){
				}
	*/
	else{
		print $val['value'];
		}
	print '</td>';
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

/* Before calling this, include scripts/set_list_vars.php first to define all the options */
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
	$vars['selectedvalue']=strtoupper($vars['selectedvalue']);
	if($vars['filter']!=''){
		$table=$vars['filter'];
		$d_t=mysql_query("SELECT DISTINCT $fieldname FROM $table ORDER BY $fieldname");
		$enum=array();
		while($field=mysql_fetch_array($d_t,MYSQL_ASSOC)){
			$enum[$field[$fieldname]]=displayEnum($field[$fieldname],$fieldname);
			}
		}
	else{
		$enum=getEnumArray($fieldname);
		}
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