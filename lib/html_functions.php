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
</div>

<?php
	}

/**
 * 
 */
function two_buttonmenu_submit($extrabuttons='',$book=''){
?>
<div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
	<button onClick="processContent(this);" name="sub" value="Submit"><?php print_string('submit');?></button>
</div>

<?php
	}

/**
 * 
 */
function submit_update($extrabuttons='',$book=''){
?>
<div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
	<button onClick="clickToUpdate(this);" name="current" value="Submit"><?php print_string('submit');?></button>
</div>

<?php
	}

/**
 * 
 */
function two_buttonmenu($extrabuttons='',$book=''){
?>
  <div class="buttonmenu">
<?php
		 all_extrabuttons($extrabuttons,$book);
?>
      <button onClick="processContent(this);" name="sub" value="Cancel"><?php print_string('cancel');?></button>
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
			if(!isset($attributes['onclick']) and isset($attributes['id'])){$attributes['onclick']='clickToAction(document.getElementById(\''.$attributes['id'].'\'))';}
			elseif(!isset($attributes['onclick']) and !isset($attributes['id'])){$attributes['onclick']='clickToAction(this)';}
			if(!isset($attributes['id'])){$buttonid='';}else{$buttonid=' id="'.$attributes['id'].'" ';}
?>
	  <span class="<?php print($imageclass);?> rowaction imagebutton" title="<?php print_string($attributes['title'],$book);?>" value="<?php print $attributes['value'];?>" onClick="<?php print $attributes['onclick'];?>"></span>
	
	<input type="hidden" title="<?php print_string($attributes['title']);?>" <?php print $buttonid;?> name="<?php print $attributes['name'];?>" value="<?php print $attributes['value'];?>" onClick="<?php print $attributes['onclick'];?>"/>
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
<?php
		if(!$val['label_not_translate']){
?>
		<label for="<?php print $val['label'];?>"><?php print_string($val['label'],$book);?></label>
<?php
			}
		else{
?>
		<label for="<?php print $val['label'];?>"><?php print $val['label'];?></label>
<?php
		}
?>
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
		if(isset($val['enumname']) and $val['enumname']!=''){$enumname=$val['enumname'];}else{$enumname=$val['field_db'];}
		$enum=getEnumArray($enumname);
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

   if($type=='staff' and $access==''){$class='profilepicture';}
   else{$class='icon';}

   print '<div class="'.$class.'">';

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
				 <img src="<?php print 'scripts/photo_display.php?epfu='.$epfu.'&size=maxi';?>" />
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
	if($vars['label']!='' and $vars['labelstyle']=='external'){
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
	<?php if($vars['switch']!=''){
		  //print 'onChange="selerySwitch(\''.$vars['switch'].'\',this.value,'.$book.')"';
			$extraclass=' switcher';} ?>
	<?php if($vars['required']=='yes'){ print ' class="required'.$extraclass.'" ';}
		elseif($vars['required']=='eitheror'){ 
			print ' class="eitheror" eitheror="'.$vars['eitheror'].'" ';} ?>
	>
<?php
	if($vars['label']!='' and ($vars['labelstyle']=='' or $vars['labelstyle']=='internal')){
?>
    <option value="" selected="selected" disabled="disabled"><?php print_string($vars['label'],$book);?></option>
<?php
		}
	if($vars['defaultvalue']=='yes'){
?>
    <option value="default"><?php print_string('default',$book);?></option>
<?php
		}
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
	if($vars['label']!='' and ($vars['labelstyle']=='external' or $vars['labelstyle']=='eternal')){
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
	<?php if($vars['onchange']=='no' and $vars['onchangeaction']!=''){print ' onChange="'.$vars['onchangeaction'].'"';}?>
	<?php if($vars['switch']!=''){//print 'onChange="selerySwitch(\''.$vars['switch'].'\',this.value)"';
		$extraclass=' switcher';} ?>
	<?php if($vars['required']=='yes'){ print ' class="required'.$extraclass.'" ';}
		elseif($vars['required']=='eitheror'){ 
			print ' class="eitheror" eitheror="'.$vars['eitheror'].'" ';} ?>
	>
<?php
	if($vars['label']!='' and ($vars['labelstyle']=='' or $vars['labelstyle']=='internal'  or $vars['labelstyle']=='eternal')){
?>
    <option value="" selected="selected" disabled="disabled"><?php print_string($vars['label'],$book);?></option>
<?php
		}
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
	if($vars['label']!='' and $vars['labelstyle']=='external'){
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
<?php
			if($vars['required']=='yes'){print ' class="required" ';}
			elseif($vars['required']=='eitheror'){
				print 'class="eitheror" eitheror="'.$vars['eitheror'].'" ';} 
?> 
	>
<?php
	if($vars['label']!='' and ($vars['labelstyle']=='' or $vars['labelstyle']=='internal')){
?>
		<option value="" selected="selected" disabled="disabled"><?php print_string($vars['label'],$book);?></option>
<?php
		}

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


/**reportbook/httpscripts/comment_writer_box.php
 *
 * $ownertype defaults to student
 *
 */
function html_document_drop($epfun,$context,$linked_id='-1',$lid='-1',$ownertype='',$listfiles=true,$upload_redirect=''){

	if($upload_redirect==''){$upload_redirect=$_SERVER['REQUEST_URI'];}

	global $CFG;
	if($context=='assessment' or $context=='comment' or $context=='reports'){$path='../../';}
	else{$path='';}

	if($listfiles){
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
		if($context=="enrolment"){
			foreach($files as $file){
				$d_ff=mysql_query("SELECT name FROM file_folder WHERE id='".$file['folder_id']."';");
				$ffname=mysql_result($d_ff,0);
				$files_types[$ffname][]=$file;
				}
			foreach($files_types as $type=>$files){
				print "<fieldset>";
				print "<legend>".$type."</legend>";
				if($type=="enrolment"){
					print '<fieldset class="right"><select name="sharearea" id="sharearea">
						<option>Select area to share</option>
						<option value="medical">Medical</option>
						<option value="report">Subject Reports</option>
						<option value="assessments">Assessments</option>
					</select>';
					print '<button id="sharebutton" class="" 
						type="button" title="share" name="current" 
						value=""/>Share</button></fieldset>';
					}
				print "<fieldset class='left'>";
				html_document_list($files);
				print "</fieldset>";
				print "</fieldset>";
				}
			}
		else{
			html_document_list($files);
			}

		print '<li><span id="deletebutton" class="clicktodelete"></span></li>';
		}
?>
			</ul>
		</form>
<?php
		if($context=="enrolment"){
?>
		  </form>
		  <form id="formfileshare" name="formfileshare" method="post" action="<?php print $path;?>infobook/httpscripts/file_share.php">
			<input type="hidden" id="FILECONTEXT" name="FILECONTEXT" value="<?php print $context;?>" />
			<input type="hidden" id="filesharearea" name="sharearea" value="" />
			<input type="hidden" id='upload_redirect' name='upload_redirect' value="<?php echo $upload_redirect;?>">
		  </form>
<?php
			}
?>
			</div>
		</fieldset>
<?php
		}
?>
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
				<input type="hidden" id='upload_redirect' name='upload_redirect' value="<?php echo $upload_redirect;?>">
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
				<!--crop parameters-->
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
							<button type="button" id="browsebutton"><?php print_string('browse');?></button>
						</div>
						<div class="submit">
							<button type="button" id="dragbutton"><?php print_string('upload');?></button>
							<button type="submit" id="submitbutton" style="z-index:2000;"><?php print_string('upload');?></button>
							<input type="hidden" name="fileselect" id="fileselect" onchange="photoSelectHandler();" />
						</div>
					</div>
				</div>
<?php 
			}
		else{
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

/*
 *
 */
function html_document_list($files){
	global $CFG;
	$http=getHTTPType();
	$filedisplay_url=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory.'/scripts/file_display.php';

	foreach($files as $file){
		if(!isset($file['id']) or $file['id']==''){$fileid=$file['name'];}
		else{$fileid=$file['id'];}
		$fileparam_list='?fileid='.$fileid.'&location='.$file['location'].'&filename='.$file['name'];
		print '<li id="filecontainer'.$fileid.'" class="document" title="'.$file['description'].'">';
		print '<a href="'.$filedisplay_url. $fileparam_list.'" />'.$file['originalname'].'<span class="clicktodownload"></span></a>';
		print '<input type="checkbox" name="fileids[]" value="'.$fileid.'" />';
		print '<input type="hidden" id="fname" value="'.$fileid.'" />';
		print '</li>';
		}
	return;
	}

/*
 *
 */
function list_markbook_filters($profiles,$umnfilter,$currentprofile,$cid,$cidsno,$classes){
?>
	<select name="umnfilter" onchange="document.umnfilterchoice.submit();">
		<option value="%" <?php if ($umnfilter=='%') {print 'selected'; }?> ><?php print_string('all'); ?></label>
		<option value="cw" <?php if ($umnfilter=='cw') {print 'selected'; } ?> ><?php print_string('classwork',$book);?></label>
<?php
	if($cidsno==1 and isset($cid) and !in_array($classes[$cid]['crid'],getEnumArray('nohomeworkcourses'))){
?>
		<option value="hw" <?php if ($umnfilter=='hw'){print 'selected'; } ?> ><?php print_string('homework',$book);?></label>
<?php
		}
?>
		<option value="t" <?php if ($umnfilter=='t'){print 'selected'; } ?> ><?php print_string('reports',$book);?></label>
<?php
	if(sizeof($profiles)>0){
		foreach($profiles as $choiceprono=>$choiceprofile){
?>
		<option value="p<?php print $choiceprono; ?>" <?php if($umnfilter=='p'.$choiceprono){print 'selected'; $currentprofile=$choiceprofile; } ?> ><?php print substr($choiceprofile['name'], 0, 30); ?></label>
<?php
			}
		}
?>
	</select>
<?php
	return $currentprofile;
	}


/*
 *
 */
function html_files_preview($epfun,$eid,$displaythiseid=true,$pid='',$foldertype='assessment'){
	global $CFG;
	if($foldertype=='comment'){
		$files=array_merge(list_files($epfun,'assessment'),list_files($epfun,'comment'));
		}
	else{
		$files=list_files($epfun,$foldertype);
		}
	if(sizeof($files)>0){

		if(isset($_SERVER['HTTPS'])){
			$http='https';
			}
		else{
			$http='http';
			}
		$filedisplay_root=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->applicationdirectory;
		$filedisplay_url=$filedisplay_root.'/scripts/file_display.php';

		foreach($files as $file){
			if(!isset($file['id']) or $file['id']==''){$fileid=$file['name'];}
			else{$fileid=$file['id'];}
			$fileparam_list='?fileid='.$fileid.'&location='.$file['location'].'&filename='.$file['name'];
			$path=$filedisplay_url.$fileparam_list;
			$otherid=$file['other_id'];
			$ext=pathinfo($path);
			$display=true;
			$d_c=mysql_query("SELECT subject_id FROM comments WHERE id='$otherid';");
			if(mysql_num_rows($d_c)>0 and $pid!=''){
				$d_p=mysql_query("SELECT subject_id FROM component WHERE id='$pid';");
				$skill_subject=mysql_result($d_p,0);
				$com_subject=mysql_result($d_c,0,'subject_id');
				if($skill_subject!=$com_subject){$display=false;}
				}
			if($display){
?>
		<div style="height:100px;float:left;">
<?php
			if(($displaythiseid and $eid==$file['other_id']) or (!$displaythiseid and $eid!=$file['other_id'])){
				if((strcasecmp($ext['extension'], 'jpg')==0 or strcasecmp($ext['extension'], 'png')==0 or strcasecmp($ext['extension'], 'gif')==0 or strcasecmp($ext['extension'], 'jpeg')==0)){
					$mini=$CFG->eportfolio_dataroot."/".$file['location']."_mini";
					if(!file_exists($mini)){
						copy($CFG->eportfolio_dataroot."/".$file['location'],$mini);
						resize_image($mini,$max_width=70,$max_height=70);
						}
					$fileparam_list='?fileid='.$fileid.'&location='.$file['location'].'_mini&filename='.$file['name'];
					$src=$filedisplay_url.$fileparam_list;
					}
				else{
					$src=$filedisplay_root."/images/file-generic.png";
					}
?>
				<img src="<?php print $src;?>" style="height:70px;width:auto;float:left;cursor:pointer;margin:2px;" 
					onclick="if(document.getElementById('file-<?php echo $fileid;?>')){this.style.border=0;removeHiddenInput($(this).closest('form').attr('id'),'file-<?php echo $fileid;?>');}else{this.style.border='1px solid #0000FF';appendHiddenInput($(this).closest('form').attr('id'),'files[]','file-<?php echo $fileid;?>','<?php echo $fileid;?>');}"
					title="<?php print $file['name'];?>">
<?php
				}
?>
		</div>
<?php
				}
			}
		}
	}
//TODO tidyup & generalise this function currently used for mulit-comment-writer, copied from newcomment_writer
function comment_box_form($commentdataObj, $bid, $pid, $entryn, $reportdefs, $isJson=false){
    $rid=$commentdataObj['rid'];
	$sid=$commentdataObj['sid'];
	if($rid!=-1){
        //$reportdef=fetch_reportdefinition($rid)
        /*TODO: per subject comment lengths */
        if($reportdefs['report']['commentlength']>0 and is_array($subject_lengths)){
            $reportdefs['report']['commentlength']=$subject_lengths["$bid$pid"];
            }
        /**/
        if($reportdefs[$index]['report']['commentlength']=='0'){$commentlength='';$maxtextlen=0;}
        else{$commentlength=' maxlength="'.$reportdefs['report']['commentlength'].'"';$maxtextlen=$reportdefs['report']['commentlength'];}
        $subs=(array)get_report_categories($rid,$bid,$pid,'sub');
        /* This allows a comment to be split into sub-sections and each gets
         *  its own entry box. A special type of fixed sub-comment is not for
         *  editing so is filtered out here.
         */
        $subcomments_no=0;
        $subcomments=array();
        foreach($subs as $sindex => $sub){
            if($sub['subtype']=='pro'){$subcomments_fix=1;}
            else{$subcomments_no++;$subcomments[]=$sub;$submaxtextlen=400;}
            }
        }
    elseif($bid=='targets'){
        $d_c=mysql_query("SELECT name FROM categorydef WHERE type='tar' ORDER BY rating;");
        $subcomments_no=0;
        $subcomments=array();
        while($sub=mysql_fetch_array($d_c,MYSQL_ASSOC)){
            $subcomments_no++;
            $subcomments[]=$sub;
            }
        }
    $tabindex=0;
    
    //$subcomments_fix=1;
    $Report['Comments']=fetchReportEntry($reportdefs, $sid, $bid, $pid);
    if(!isset($Report['Comments']['Comment'])  or sizeof($Report['Comments']['Comment'])==0 
       or $entryn==sizeof($Report['Comments']['Comment'])){
		
        /*This is a fresh comment so can do a few extra things*/
        $Comment=array('Text'=>array('value'=>'','value_db'=>''),
                       'Teacher'=>array('value'=>'ADD NEW ENTRY'));
        $inmust='yes';
        }
    else{
        /*Re-editing an existing comment.*/
        $texts=array();
    /*TODO: the xmlid must have the real entryn not the index!!!!*/
        $Comment=$Report['Comments']['Comment'][$entryn];
        $inmust=$Comment['id_db'];
        if($subcomments_no>0){
            $texts=explode(':::',$Comment['Text']['value_db']);
            }
        else{
            $texts[]=$Comment['Text']['value_db'];
            }
        }
    
    /*TODO: categories are only handled by the comment writer for rpeort summaries. */
    if($reportdefs['report']['addcategory']=='yes' and $bid=='summary'){
        $catdefs=get_report_categories($rid,$bid,$pid,'cat');
        $ratingname=get_report_ratingname($reportdefs,$bid);
        $ratings=get_ratings($ratingname);
        }
    
    ?>
    
            <form id="formtoprocess" name="formtoprocess" method="post" 
                                        action="newcomment_writer_action.php">
    <?php
    
    
    if($reportdefs['report']['addcategory']=='yes' and $bid=='summary'){
    ?>
            <div class="content center" style="margin:5px 60px 5px 50px;">
                <table class="listmenu hidden">
    <?php
                if(isset($Comment['Categories'])){$Categories=$Comment['Categories'];}
                else{
                    $Categories['Category']=array();
                    $Categories['ratingname']=$ratingname;
                    }
    
                           //$ratings=$reportdefs[0]['ratings'][$Categories['ratingname']];
    
                while(list($catindex,$catdef)=each($catdefs)){
                    $catid=$catdefs[$catindex]['id'];
                    $catname=$catdefs[$catindex]['name'];
                    print '<tr class="revealed"><td class="row" style="background-color:#fff;"><div style="width:100%;"><p>'.$catname.'</p></div></td></tr>';
    
                    /* Find any previously recorded value for this catid,
                       make a first guess that they will have been
                       recorded in the same order as the cats are
                       defined. But any blanks or changes will have
                       scuppered this.
                     */
                    $setcat_value=-1000;
                    if(isset($Categories['Category'][$catindex]) 
                       and $Categories['Category'][$catindex]['id_db']==$catid){
                        $setcat_value=$Categories['Category'][$catindex]['value'];
                        }
                    else{
                        foreach($Categories['Category'] as $Category){
                            if($Category['id_db']==$catid){
                                $setcat_value=$Category['value'];
                                }
                            }
                        }
                    if(($setcat_value==' ' or $setcat_value=='') and $setcat_value!='0'){
                        $setcat_value=-1000;
                        }
    
                    print '<tr class="revealed"><td class="boundary row" style="padding-left:40px;">';
                    $divwidth=round(90/sizeof($ratings));
                    foreach($ratings as $value=>$descriptor){
                        $checkclass='';
                        if($setcat_value==$value){$checkclass='checked';}
    
                        print '<div class="'.$checkclass.'" style="width:'.$divwidth.'%;"><label>'.$descriptor.'</label>';
                        print '<input onclick="checkRadioIndicator(this)" type="radio" name="incat'.$catid.'"
                            tabindex="'.$tabindex++.'" value="'.$value.'" '.$checkclass;
                        print ' /></div>';
                        }
                    print '</td></tr>';
                    }
    ?>
                </table>
            </div>
    <?php
        }
    ?>
    
    <?php
		if($subcomments_no==0){$subcomments[]['name']='Comment';$subcomments_no=1;}
		$subcomments_no=1;
		$commentheight=600;
        $commentheight=($commentheight/$subcomments_no)-25*$subcomments_no;/*in px*/
		//error_log('number', $subcomments_no);
        if($commentheight<90){$commentheight=80;}
        if($commentheight>250){$commentheight=250;}
        for($c=0;$c<$subcomments_no;$c++){
            if($c==0){$htmleditor='htmleditorarea';}
            else{
                $htmleditor='subeditorarea';
                $maxtextlen=$submaxtextlen;
                }
            $commentlabel=$subcomments[$c]['name'];
    ?>
    
                <div class="center" style="border-top:0px;">
				<div class="label" style="height:30px;margin-top:20px;">
					<label style="display:inline-block;padding:2px 6px;width:50%;">
						<?php print $commentlabel;?>
					</label>
					<label class="subject-title" style="font-weight:600;">
						<?php print $commentdataObj['title'];?>
					</label>
					<label class="flash-message" style="float:right;font-weight:600;padding:2px 6px;">
						<span style="display:none" class="saving"><?php print_string('saving')?></span>
					</label>
					<input id="maxtextlenincom<?php print $c;?>" name="maxtextlenincom<?php print $c;?>" type="hidden" value="<?php print $maxtextlen;?>"/>
					<input id="textlenincom<?php print $c;?>" name="textlenincom<?php print $c;?>" size="3" type="input" readonly="readonly" tabindex="10000"  style="float:right;padding:0px 2px;margin:10px 28px 0 0;"/>
				</div>
                <div id="<?php print $commentdataObj['openid'];?>" class="<?php print $htmleditor;?>"
                  style="height:<?php print $commentheight-20;?>px;"  
                  tabindex="<?php print $tabindex++;?>"  
                  name="incom<?php print $c;?>" > <?php if(isset($texts[$c])){print $texts[$c];};?></div>
                </div>
                <input type="hidden" name="incom<?php print $c;?>" value="<?php if(isset($texts[$c])){print $texts[$c];};?>"/>
    <?php
                }
		if($isJson){
	?>
			<input type="hidden" name="jsonresponse" value="true"/>
	<?php
			}
    ?>
            
            <input type="hidden" name="inno" value="<?php print $subcomments_no;?>"/>
            <input type="hidden" name="inmust" value="<?php print $inmust;?>"/>
            <input type="hidden" name="addcategory" value="<?php print $reportdefs['report']['addcategory'];?>"/>
            <input type="hidden" name="sid" value="<?php print $sid; ?>"/> 
            <input type="hidden" name="rid" value="<?php print $rid; ?>"/>
            <input type="hidden" name="bid" value="<?php print $bid; ?>"/>
            <input type="hidden" name="pid" value="<?php print $pid; ?>"/>
            <input type="hidden" name="openid" value="<?php print $commentdataObj['openid']; ?>"/>
			<input type="hidden" name="sub" value="Submit"/>
		</form>
<?php
	}
?>
