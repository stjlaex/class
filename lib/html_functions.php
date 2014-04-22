<?php
/**												lib/html_functions.php
 *
 * Generic functions for producing html elements.
 */


/**
 * Called by the buttonmenu functions to render the pages action
 * buttons sitting to the left of whatever generic buttons the
 * buttonmenu functions put in place. The onclick action will default
 * to processing the form, set this if they are to behave differently.
 */
function all_extrabuttons($extrabuttons,$book='',
						  $onclick='processContent(this)',$class=''){
	if(is_array($extrabuttons)){
		while(list($description,$attributes)=each($extrabuttons)){
			if(!isset($attributes['onclick'])){$attributes['onclick']=$onclick;}
			if(!isset($attributes['title'])){$attributes['title']=$description;}
			if(!isset($attributes['id'])){$buttonid='';}else{$buttonid=' id="'.$attributes['id'].'"';}
			if(!isset($attributes['display'])){$display='';}else{$display=' style="display:'.$attributes['display'].';" ';}
			if(!isset($attributes['class'])){$thisclass='';}else{$thisclass=' class="'.$attributes['class'].'" ';}
?>
  <button onClick="<?php print $attributes['onclick'];?>" 
	<?php print $buttonid;?>
	<?php print $display;?>
	tabindex="<?php // print $tab++;?>"
	<?php if($thisclass==''){print $class;}else{print $thisclass;}?> 
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

/**
 * 
 */
function three_buttonmenu($extrabuttons='',$book=''){
?>
<div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
	<button onClick="processContent(this);" name="sub" value="Submit"><?php print_string('submit');?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
</div>

<?php
	}

/**
 * 
 */
function two_buttonmenu($extrabuttons='',$book=''){
?>
  <div class="buttonmenu">
      <button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
  </div>
<?php
	}

/**
 * 
 */
function twoplus_buttonmenu($currentkey,$maxkey,$extrabuttons='',$book='',$minkey=0){
?>
  <div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
  	<button onClick="processContent(this);" <?php if($currentkey<=$minkey){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Previous"><?php print_string('previous',$book);?></button>
	<button onClick="processContent(this);" <?php if($currentkey>=($maxkey-1)){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Next"><?php print_string('next',$book);?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
  </div>
<?php
	}

/**
 * Allows for navigation using Previous and Next buttons which step
 * the key down or up by 1 at a time. A limit to moving forward is set
 * by maxkey.
 *
 */
function threeplus_buttonmenu($currentkey,$maxkey,$extrabuttons='',$book='',$usertype='student'){
	if($currentkey==''){$currentkey=1;}//Register only needs this
	if($usertype!="guardian"){$next=get_string('next',$book);$previous=get_string('previous',$book);}
	else{$next=get_string('nextcontact',$book);$previous=get_string('previouscontact',$book);}
?>
  <div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
  	<button onClick="processContent(this);" <?php if($currentkey==0){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Previous"><?php print_string('previous',$book);?></button>
	<button onClick="processContent(this);" <?php if($currentkey>=($maxkey-1)){print 'disabled="disabled" style="visibility:hidden;"';} ?> name="sub" value="Next"><?php print_string('next',$book);?></button>
	<button onClick="processContent(this);" name="sub" value="Submit"><?php print_string('submit');?></button>
	<button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
	<button onClick="processContent(this);" name="sub" value="Reset"><?php print_string('reset');?></button>
  </div>
<?php
	}


/**
 *
 * Fills in the little buttons which function on a specific record on
 * a row by row basis, usually as part of a listmenu table. The array
 * imagebuttons can contain a stack of buttons to be
 * generated. Each row in imagebuttons is indexed by its imageclass
 * (which is not really a class as its unique) and attributes of title,
 * name and value.
 *
 */
function rowaction_buttonmenu($imagebuttons,$extrabuttons='',$book=''){

	if(is_array($imagebuttons)){
		while(list($imageclass,$attributes)=each($imagebuttons)){
			if(!isset($attributes['onclick'])){$attributes['onclick']='clickToAction(this)';}
			if(!isset($attributes['id'])){$buttonid='';}else{$buttonid=' id="'.$attributes['id'].'" ';}
?>
	<!--span class="<?php print($imageclass);?> rowaction imagebutton" title="<?php print_string($attributes['title']);?>" <?php print $buttonid;?> name="<?php print $attributes['name'];?>" value="<?php print $attributes['value'];?>" onClick="<?php print $attributes['onclick'];?>"></span-->
	<span class="<?php print($imageclass);?>  rowaction imagebutton" title="<?php print_string($attributes['title'],$book);?>" <?php print $buttonid;?> name="<?php print $attributes['name'];?>" value="<?php print $attributes['value'];?>" onClick="<?php print $attributes['onclick'];?>"></span>
    <?php
	   }
	       }
   	    all_extrabuttons($extrabuttons,$book,'clickToAction(this)','class="rowaction" ');
	   }

/**
 * 
 */
function xmlarray_form($Array,$no='',$caption='',$tab=1,$book=''){
?>
  <table class="listmenu">
<?php

	if($caption!=''){print '<caption>'.get_string($caption,$book).'</caption>';}
	while(list($key,$val)=each($Array)){
		/* If the table_db attribute is omitted it indicates this is not */
		/* a field for entry by the user - this  may be because it is disabled or */
		/* because it is dependent on some other value - it will not appear in the form*/
		if(isset($val['inputtype']) and $val['inputtype']=='fixed'){
			unset($val['table_db']);
			}
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
?>
	<tr>
	  <td>
		<label for="<?php print $val['label'];?>"><?php print_string($val['label'],$book);?></label>
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


/**
 * Same as the xmlaray_form except it uses divs for formatting instead of a table. 
 *
 */
function xmlarray_divform($Array,$no='',$caption='',$tab=1,$book=''){

	if($caption!=''){print '<caption>'.get_string($caption,$book).'</caption>';}
	while(list($key,$val)=each($Array)){
		/* If the table_db attribute is omitted it indicates this is not */
		/* a field for entry by the user - this */
		/* may be because it is disabled or */
		/* because it is dependent on some */
		/* other value - it will not appear in the table*/
		if(isset($val['inputtype']) and $val['inputtype']=='fixed'){
			unset($val['table_db']);
			}
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
 *
 * Prints a single formatted div for inclusion in a form of the
 * xmlelement supplied as the xmlarray fragment $val.
 *
 */
function xmlelement_div($val,$no='',$tab=1,$position='center',$book=''){
	if(isset($val['inputtype']) and $val['inputtype']=='fixed'){
		unset($val['table_db']);
		}
	if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
?>
  <div class="<?php print $position;?>">
	<label for="<?php print $val['label'];?>">
	  <?php print_string($val['label'],$book);?>
	</label>
	<?php $tab=xmlelement_input($val,$no,$tab,$book);?>
  </div>
<?php
		}
	return $tab;
	}

/**
 * Prints one form element including its formatted value given in the
 * xml element $val.  Use $no if this is a repeat in the same form.
 *
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
		<textarea rows="2" cols="80"  id="<?php print $val['label'];?>" class="<?php if(isset($val['inputtype'])){print $val['inputtype'];}?>" name="<?php print $val['field_db'].$no; ?>" tabindex="<?php print $tab++;?>" ><?php print $setval; ?></textarea>
<?php
		}
	elseif(substr($val['type_db'],0,3)=='var' or substr($val['type_db'],0,3)=='cha'){
		/*TODO: use html5 validation, regex patterns needed*/
		//if($val['field_db']=='email'){$pattern='pattern="email"';}
		/* TODO: add these patterns for all possible inputs BUT js
		 needs to be UTF8 aware first!*/
		//else{$pattern='pattern="alphanumeric"';}
		$field_type=(array)explode('(', $val['type_db']);
		$field_type=(array)explode(')', $field_type[1]);
		if(isset($field_type[0])){$maxlength='maxlength="'.$field_type[0].'" ';}else{$maxlength=' ';}
?>
		<input type="text" id="<?php print $val['label'];?>" <?php print $pattern;?> <?php print $maxlength; ?> class="<?php if(isset($val['inputtype'])){print $val['inputtype'];}?>" name="<?php print $val['field_db'].$no; ?>" tabindex="<?php print $tab++;?>" value="<?php print $setval; ?>" />
<?php
		}
	elseif($val['type_db']=='smallint' or $val['type_db']=='decimal'){
		if($val['type_db']=='decimal'){$pattern='pattern="decimal"';$maxlength='20';}
		else{$pattern='pattern="integer"';$maxlength='4';}
		$field_type=(array)explode('(', $val['type_db']);
		if(isset($field_type[1])){$field_type=(array)explode(')', $field_type[1]);}
		if(isset($field_type[0])){$maxlength='maxlength="'.$field_type[0].'" ';}else{$maxlength=' ';}
?>
		<input type="text" id="<?php print $val['label'];?>" 
			<?php print $pattern;?> 
		  <?php print $maxlength; ?>
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
 * Prints one cell based on the xml element $val, formatted to fit a
 * listmenu table including the element's label and any preset value.
 */
function xmlelement_display($val,$book){
	print '<td>';
	print '<label>'.get_string($val['label'],$book).':</label> <strong>';
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
	print '</strong></td>';
	}


/**
 * Prints one cell designed for a listmenu table including the
 * element's label and its formatted value.
 */
function xmlattendance_display($Attendance){
?>
	  <td status="<?php print $Attendance['Status']['value'];?>" 
<?php 
	if($Attendance['Comment']['value']!=' '){
?>			
		title="">
		<span title="<?php print $Attendance['Code']['value'].':<br />'. 
		date('H:i',$Attendnace['Logtime']['value']). 
		  ' '.$Attendance['Comment']['value'];?>">
		  &nbsp;</span>
<?php 
		}
	else{print '>';}
?>
		&nbsp;</td>
<?php
	}


/**
 *
 *
 */
function emaillink_display($email){
	global $CFG;
	if($email!='' and $email!=' '){
?>
	<a href="mailto:<?php print $email;?>">
	  <span class="clicktoemail" title="<?php print_string('clicktoemail');?>"></span>
	</a>
<?php
		}
	}




/**
 *
 *
 */
function old_photo_img($epfu,$enrolno='',$access=''){
	global $CFG;
	$epfu=trim(strtolower($epfu));
	if(isset($_SERVER['HTTPS'])){
		$http='https';
		}
	else{
		$http='http';
		}
   print '<div class="icon">';
   if($access=='w'){
	   print '<a href="infobook.php?current=student_photo.php&cancel=student_view.php">';
	   }
?>
	 <img src="<?php print $http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory. 
				'/scripts/photo_display.php?epfu='.$epfu.'&enrolno='.$enrolno.'&size=maxi';?>" />
<?php
   if($access=='w'){
	   print '</a>';
	   }
   print '</div>';

	}


/**
 *
 *
 */
 function photo_img($epfu,$enrolno='',$access='',$type=''){
   global $CFG;
   $epfu=trim(strtolower($epfu));
   print '<div class="icon">';

   //For staff
   if($type=='staff') {
		if($access=='w'){
			print '<a href="admin.php?current=staff_photo.php&cancel=staff_details.php&seluid='.$enrolno.'">';
		}
?>
				<img src="<?php print 'scripts/photo_display.php?epfu='.$epfu.'&type='.$type.'&size=maxi';?>" />
<?php
		if($access=='w'){
			print '</a>';
		}
   }
   
   //For students
   else {
		if($access=='w'){
			print '<a href="infobook.php?current=student_photo.php&cancel=student_view.php">';
		}
?>
				 <img src="<?php print 'scripts/photo_display.php?epfu='.$epfu.'&enrolno='.$enrolno.'&size=maxi';?>" />
<?php
		if($access=='w'){
			print '</a>';
		}
   }
   print '</div>';

	}




/**
 *
 *
 */
function display_file($epfun,$foldertype,$linkedid='-1',$comment=''){

	global $CFG;
	$files=array();

	if($foldertype=='staff'){
		$folder_usertype='u';
		}
	else{
		$folder_usertype='s';
		}

	$epfuid=get_epfuid($epfun,$folder_usertype);
	if(strlen($epfuid)<1){$epfuid='-999999';}

	if($foldertype!='icon'){
		/* Could be passing both an id and some description from a linked comment. */
		if(is_array($linkedid)){
			$linked_description=$linkedid['detail'];
			$linkedid=$linkedid['id'];
			}

		if($linkedid>0){
			/* Looking only at the files attached to a single entry. */
			$attachment="file.other_id='$linkedid' AND ";
			}
		else{
			/* Looking only at all files dropped in this context. */
			$attachment='';
			}

		$d_f=mysql_query("SELECT file.id, title, description, location, originalname FROM file 
						JOIN file_folder ON file_folder.id=file.folder_id
						WHERE $attachment file.owner_id='$epfuid' AND file.owner='$folder_usertype' 
						AND file_folder.name='$foldertype';");
		while($file=mysql_fetch_array($d_f,MYSQL_ASSOC)){
			if($foldertype=='assessment'){
				/*
				 * The other_id is a comment_id and will have a descriptoin from there.
				 */
				$file['description']=$linked_description;
				}
			else{
				$file['description']=$file['description'];
				}
			$file['name']=$file['originalname'];
			$file['path']=$CFG->eportfolio_dataroot.'/'.$file['location'];
			$files[]=$file;
			}
		}

		if(isset($_SERVER['HTTPS'])){
			$http='https';
			}
		else{
			$http='http';
			}
		$filedisplay_url=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory.'/scripts/file_display.php';

		foreach($files as $file){
			if(!isset($file['id']) or $file['id']=='') $fileid=$file['name'];
			else $fileid=$file['id'];
			$fileparam_list='?fileid='.$fileid.'&location='.$file['location'].'&filename='.$file['name'];
			$path=$filedisplay_url.$fileparam_list;
			$ext=pathinfo($path);
?>
		  <!--span title="<?php echo $comment;?>">
				<button type="button" class="rowaction imagebutton">
				    <a href="<?php print $filedisplay_url.$fileparam_list;?>"-->
<?php
			if($ext['extension']=='pdf'){
?>
				<img class="displayfilepdf">
<?php
				}
			else{
?>
				<img class="displayfile">
<?php
				}
?>
					<!--span><?php echo $file['name']; ?></span>
				</button>
			</a>
		  </span-->

<?php
			if(strcasecmp($ext['extension'], 'jpg')==0 or strcasecmp($ext['extension'], 'png')==0 or strcasecmp($ext['extension'], 'gif')==0 or strcasecmp($ext['extension'], 'jpeg')==0){
?>
				<img src="<?php print $filedisplay_url.$fileparam_list;?>" style="height:70px;width:70px; cursor:pointer;" onclick="getElementById('preview').style.display='block';getElementById('shadow').style.display='block';getElementById('imgpreview').setAttribute('src','<?php print $filedisplay_url.$fileparam_list;?>');">
<?php
				}
?>
<?php
			}
	}

/**
 * 
 */
function selery_select_stick($choices,$choice='',$book=''){
?>
		<select class="selery" name="current" onchange="selerySelectSubmit(this)">
<?php
	while(list($page,$title)=each($choices)){
?>
		  <!--li onclick="selerySubmit(this)" <?php if($choice==$page){print 'class="checked" ';}?> >
			<input type="radio"<?php if($choice==$page){print 'checked="checked" ';} ?> value="<?php print $page;?>" name="current" >
			  <p><?php print_string($title,$book);?></p>
			</input>
		  </li-->
		  <option value="<?php echo $page;?>" <?php if($choice==$page){print 'selected="selected" ';}?> ><?php print_string($title,$book);?></option>
<?php
		}
?>
		</select>
<?php
	}


/**
 * 
 */
function selery_stick($choices,$choice='',$book=''){
?>
        <ul class="selery">
            <?php
            	while(list($page,$title)=each($choices)){
            ?>
		  <li onclick="selerySubmit(this)" <?php if($choice==$page){print 'class="checked" ';}?> >
			<input type="radio"<?php if($choice==$page){print 'checked="checked" ';} ?> value="<?php print $page;?>" name="current" >
			  <p><?php print_string($title,$book);?></p>
			</input>
		  </li>
            <?php
            	}
            ?>
        </ul>
<?php
	}



/**
 * Before calling this, include scripts/set_list_vars.php first to define all the options 
 * set in the $vars array (see in there how to over-ride defaults)
 * the $d_list should be the SELECT result from mysql with AS id and 
 * AS name used 
 */
function list_select_db($d_list,$vars,$book=''){
	$valuefield=$vars['valuefield'];
	$descriptionfield=$vars['descriptionfield'];
	$extraclass='';
	if($vars['label']!=''){
?>
  <!--label for="<?php print $vars['id'];?>">
	<?php print_string($vars['label'],$book);?>
  </label-->
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
	<?php if($vars['switch']!=''){
		  //print 'onChange="selerySwitch(\''.$vars['switch'].'\',this.value,'.$book.')"';
			$extraclass=' switcher';} ?>
	<?php if($vars['required']=='yes'){ print ' class="required'.$extraclass.'" ';}
		elseif($vars['required']=='eitheror'){ 
			print ' class="eitheror" eitheror="'.$vars['eitheror'].'" ';} ?>
	>
    <option value=""><?php print_string($vars['label'],$book);?></option>
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


/**
 * As for list_select_db except that the $list is not a mysql result
 * resource. It requires an array of 'id' and 'name' pairs to choose
 * from, often returned from one of the list_something_things()
 * functions. Always use the set_list_vars.php script first.
 *
 */
function list_select_list($list,$vars,$book=''){
	$valuefield=$vars['valuefield'];
	$descriptionfield=$vars['descriptionfield'];
	$extraclass='';
	if($vars['label']!=''){
?>
  <!--label for="<?php print $vars['id'];?>">
	<?php print_string($vars['label'],$book);?>
  </label-->
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
	<?php if($vars['switch']!=''){//print 'onChange="selerySwitch(\''.$vars['switch'].'\',this.value)"';
		$extraclass=' switcher';} ?>
	<?php if($vars['required']=='yes'){ print ' class="required'.$extraclass.'" ';}
		elseif($vars['required']=='eitheror'){ 
			print ' class="eitheror" eitheror="'.$vars['eitheror'].'" ';} ?>
	>
    <option value=""><?php print_string($vars['label'],$book);?></option>
<?php
	while(list($index,$item)=each($list)){
		if(!is_array($item)){
			/* If not passed id/name pairs then try to correct by using index/value */
			$temp=$item;
			$item=array();
			$item[$valuefield]=$index;
			$item[$descriptionfield]=$temp;
			}
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


/**
 * 
 *
 */
function list_select_enum($fieldname,$vars,$book=''){
	$vars['selectedvalue']=strtoupper($vars['selectedvalue']);
	if($vars['filter']!=''){
		$table=$vars['filter'];
		$d_t=mysql_query("SELECT DISTINCT $fieldname FROM $table ORDER BY $fieldname;");
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
  <!--label for="<?php print $vars['id'];?>">
	<?php print_string($vars['label'],$book);?>
  </label-->
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
	<?php if($vars['required']=='yes'){ print ' class="required" ';}
			elseif($vars['required']=='eitheror'){
				print 'class="eitheror" eitheror="'.$vars['eitheror'].'" ';} ?> 
	>
    <option value=""><?php print_string($vars['label'],$book);?></option>
<?php
			 while(list($inval,$description)=each($enum)){	
				 print '<option ';
				 if($vars['selectedvalue']==strtoupper($inval)){print ' selected="selected"';}
				 print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
				 }
?>
	</select>
<?php
	}


/**
 *  construct the redirect string
 */
function url_construct($params,$entrypage,$fullurl=false){

	global $CFG;

	if(isset($_SERVER['HTTPS'])){
		$http='https';
		}
	else{
		$http='http';
		}

	if($fullurl){$url=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory.'/';}
	else{$url='';}

	$fullurl=$url. $entrypage;

	foreach($params as $param => $value){
		if(!isset($joiner)){$joiner='?';}
		else{$joiner='&';}
		$fullurl=$fullurl . $joiner . $param . '=' . $value;
		}

	return $fullurl;
	}



/**
 *
 */
function html_message_transform($epfum,$transform){

	global $CFG;

	$xsl_filename=$transform.'.xsl';
	$imagepath='http://'.$CFG->siteaddress.$CFG->sitepath.'/images/';

	$xml=xmlpreparer('content',$xmlarray);
	$xml='<'.'?xml version="1.0" encoding="utf-8"?'.'>'.$xml;
	$html_message=xmlprocessor($xml,$xsl_filename);
	//$html_message=eregi_replace('../images/',$imagepath,$html_message);
	if(empty($html_message)){
		trigger_error('html message: xmlprocessor failed for '.$transform,E_USER_WARNING);
		$html_message=false;
		}

	return $html_message;
	}


/**
 *
 */
function html_table_container_open($containerno,$state='rowplus',$label){


	if($state!='' and $containerno!=0){

		if($state=='rowplus'){$hidden='hidden';}
		else{$hidden='revealed';}
?>
		<div id="<?php print $containerno;?>">
		  <div class="<?php print $state;?>" onClick="clickToReveal(this)" id="<?php print $containerno.'-0';?>" >
			<div><?php print $label;?></div>
		  </div>
		  <div class="<?php print $hidden;?>" id="<?php print $containerno.'-1';?>">
			<table class="listmenu">
                <?php 
		          }
                    return;
	               }

/**
 *
 */
function html_table_container_close($containerno,$xmltagname='',$entry=''){

	if($containerno!=0){
?>
			</table>
		  </div>
		</div>
<?php
		}

	return;
	}


    /**
     *
     * $ownertype defaults to student
     *
     */
    function html_document_drop($epfun,$context,$linked_id='-1',$lid='-1',$ownertype=''){
    	global $CFG;
    	if($context=='assessment'){$path='../../';}
    	else{$path='';}
    ?>
    <fieldset class="documentdrop">
        <div class="documentdrop">
	   <h5>
<?php 
            	 if($context=='icon'){ 
		   print_string('profilephotos');
		 }
		 else{
		   print_string('documents');
		 }
?>
	   </h5>

           <form id="formfiledelete" name="formfiledelete" method="post" action="<?php print $path;?>infobook/httpscripts/file_delete.php">
                <input type="hidden" id="FILEOWNER" name="FILEOWNER" value="<?php print $epfun;?>" />
                <input type="hidden" id="FILECONTEXT" name="FILECONTEXT" value="<?php print $context;?>" />
                <ul class="list-files">
                <?php
                    $files=(array)list_files($epfun,$context,$linked_id);
                    if(sizeof($files)>0){
                        html_document_list($files);
                        print '<li><span id="deletebutton" class="clicktodelete"></span></li>';
                        }
                ?>
                </ul>
            </form>
        </div>        
    </fieldset>

    <fieldset class="documentdrop">
        <div class="documentdrop">
        <h5><?php print_string('uploadfile');?> <span style="font-size:small;">(<?php print_string('max');?>: <?php echo ini_get("upload_max_filesize"); ?>)</span></h5>
		<form id="formdocumentdrop" name="formdocumentdrop" method="post" action="<?php print $path;?>infobook/httpscripts/file_upload.php" enctype="multipart/form-data" <?php if($context=='icon'){ print "onsubmit='return checkForm()'";} ?>>
    		<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="<?php print return_bytes(ini_get('upload_max_filesize'));?>" />
    		<input type="hidden" id="FILEOWNER" name="FILEOWNER" value="<?php print $epfun;?>" />
    		<input type="hidden" id="FILECONTEXT" name="FILECONTEXT" value="<?php print $context;?>" />
    		<input type="hidden" id="FILELINKEDID" name="FILELINKEDID" value="<?php print $linked_id;?>" />
    		<input type="hidden" id="FILESID" name="FILESID" value="<?php print $lid;?>" />
    		<input type="hidden" id="OWNERTYPE" name="OWNERTYPE" value="<?php print $ownertype;?>" />
    		<input type="hidden" id="DRAG" name="DRAG" value="false" />
    		<input type="hidden" id='upload_redirect' name='upload_redirect' value="<?php echo $_SERVER['REQUEST_URI'];?>">
    		<input type="hidden" id='maxpostsize' name='maxpostsize' value="<?php echo ini_get('upload_max_filesize'); ?>">
            <?php 
            	 if($context=='icon'){ 
            		 if($ownertype=='staff'){
            			 $d_book='admin';
            			 $d_current='staff_details';
            			 $d_id='seluid';
            			 }
            		 else{
            			 $d_book='infobook';
            			 $d_current='student_view';
            			 $d_id='sid';
            			 }
            ?>
			<!--crops parameters-->
			<input type="hidden" id="x1" name="x1" />
			<input type="hidden" id="y1" name="y1" />
			<input type="hidden" id="x2" name="x2" />
			<input type="hidden" id="y2" name="y2" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
	    <div class="boxdragdrop">
                <div id="filedrag">
                    <span></span>
                    <?php print_string('drophere');?>
                </div>
		<div class="error"></div>
	        <div class="step2">
			<div id="progress"></div>
			<div id="messages"></div>
			<div class="info" style="display:none">
    	       			<label><?php print_string('filesize');?></label> <input type="text" id="filesize" name="filesize" />
       				<label><?php print_string('filetype');?></label> <input type="text" id="filetype" name="filetype" />
       				<label><?php print_string('imagedimension');?></label> <input type="text" id="filedim" name="filedim" />
			</div>
	       		<div class="upload">
			       <input type="file" name="image_file" id="image_file" onchange="photoSelectHandler();"/>
			       <div style="position: absolute; top: 0px; left: 0px; z-index: 1;">
				   <button type="button" id="browsebutton"><?php print_string('browse');?></button>
				</div>
       			</div>
				<script>
					//TODO find a place for this 
					//Changes the value of the fake field with the filename
					$('#image_file').change(function() {
						$('#fake_field').val($(this).val());
					});
				</script>
		        <div class="submit">
				<button type="submit" id="submitbutton"><?php print_string('upload');?></button>
				<button type="button" id="dragbutton"><?php print_string('upload');?></button>
				<input type="hidden" name="fileselect" id="fileselect" onchange="photoSelectHandler();" />
			</div>
		</div>
            <?php 
                	}
		 else{
		   /* when $context != 'icon' */
            ?>
            
	    <div class="boxdragdrop">
                <div id="filedrag">
                    <span></span>
                    <?php print_string('drophere');?>
                </div>
                
                <div id="progress"></div>
                <div id="messages"></div>
				<div class="upload">
				   <input type="file" name="image_file" onchange="document.getElementById('messages').style.display='block';document.getElementById('messages').innerHTML=this.value;"/>
				   <button type="button" id="browsebutton"><?php print_string('browse');?></button>
				</div>
				<div class="submit">
				    <button type="submit" id="submitbutton"><?php print_string('upload');?></button>
    			    <input type="file" id="fileselect" name="fileselect[]" multiple="multiple"/>
    			</div>
            </div>
            <?php 
                } 
            ?>
        </form>
	</div>
    </fieldset>
    <?php 
    	if($context=='icon'){ 
    ?>
	<div style="margin: 0 0 0 2%;"><?php print_string('uploadbuttonabove');?></div>
	<div style="border-style:solid;border-width:5px;max-width:93.5%;max-height:90.5%;margin:0 0 4% 2%;background-color: #FFFFEE;overflow:auto;">
	   <center><img id="preview" /></center>
	</div>
    <?php 
    		} 
    	return;
    	}
        function html_document_list($files){
        	global $CFG;
        	if(isset($_SERVER['HTTPS'])){
        		$http='https';
        		}
        	else{
        		$http='http';
        		}
        	$filedisplay_url=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory.'/scripts/file_display.php';
        
        	foreach($files as $file){
        		if(!isset($file['id']) or $file['id']=='') $fileid=$file['name'];
        		else $fileid=$file['id'];
        		$fileparam_list='?fileid='.$fileid.'&location='.$file['location'].'&filename='.$file['name'];
        		print '<li id="filecontainer'.$fileid.'" class="document" title="'.$file['description'].'">';
        		print '<a href="'.$filedisplay_url. $fileparam_list.'" />'.$file['originalname'].'<span class="clicktodownload"></span></a>';
        		print '<input type="checkbox" name="fileids[]" value="'.$fileid.'" />';
        		print '<input type="hidden" id="fname" value="'.$fileid.'" />';
        		print '</li>';
        		}
        	return;
        	}
        function list_markbook_filters($profiles,$umnfilter,$currentprofile,$cid,$cidsno,$classes){
    ?>
	<select name="umnfilter" onchange="document.umnfilterchoice.submit();">
	    <option value="cw" <?php if ($umnfilter == 'cw') {print 'selected'; } ?> ><?php print_string('classwork',$book);?></label>
        <?php
    	   if($cidsno==1 and isset($cid) and !in_array($classes[$cid]['crid'],getEnumArray('nohomeworkcourses'))){
        ?>
    	<option value="hw" <?php if ($umnfilter == 'hw') {print 'selected'; } ?> ><?php print_string('homework',$book);?></label>
        <?php
        	}
        ?>
    	<option value="t" <?php if ($umnfilter == 't') {print 'selected'; } ?> ><?php print_string('reports',$book);?></label>
        <?php
        	if(sizeof($profiles)>0){
	?>

	<?php
        		foreach($profiles as $choiceprono => $choiceprofile){
        ?>
    	<option value="p<?php print $choiceprono; ?>" <?php if ($umnfilter == 'p' . $choiceprono) {print 'selected'; $currentprofile=$choiceprofile; } ?> ><?php print substr($choiceprofile['name'], 0, 30); ?></label>
        <?php
            	}
            }
        ?>
    	<option value="%" <?php if ($umnfilter=='%') {print 'selected'; }?> ><?php print_string('all'); ?></label>
	</select>
    <?php
    	return $currentprofile;
    	}
    ?>
