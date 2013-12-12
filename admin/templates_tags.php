<?php
	$action="templates_tags.php";
	$ac=$_GET['action'];
	/*add*/
	if(isset($_POST['var']) and $_POST['var']!=''){$var='{{'.$_POST['var'].'}}';}else{$var='';}
	if(isset($_POST['var2']) and $_POST['var2']!='' and $var==''){$var=$_POST['var2'];}else{$var='';}
	if(isset($_POST['taf']) and $_POST['tag']!=''){$tag=$_POST['tag'];}else{$tag='';}
	if($_POST['action']!='update' and $var!='' and $tag!=''){addTag($var,$tag);}
	/*update*/
	if(isset($_POST['tg']) and $_POST['tg']!=''){$tg=$_POST['tg'];}else{$tg='';}
	if(isset($_POST['vl']) and $_POST['vl']!=''){$vl=$_POST['vl'];}else{$vl='';}
	if(isset($_GET['tagid']) and $_GET['tagid']!=''){$tagid=$_GET['tagid'];}else{$tagid='';}
	if($_POST['action']=='update' and $vl!='' and $tg!=''){addTag($vl,$tg,true,$_POST['tagid']);}

	$extrabuttons['save']=array('name'=>'current','value'=>'templates_tags.php');
	two_buttonmenu($extrabuttons);

	$tags=getTags(false);
?>
<div id="viewcontent" class="content">

	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
		<fieldset class="center divgroup" id="viewcontent" style="background: none repeat scroll 0 0 #666666 !important;">
			<legend><?php print_string('newrenametag',$book);?></legend>
			<div style="float:left">
				<label><?php print_string('selectfield',$book);?></label>
				<select id="tags" name='var'>
					<option value=""></option>
					<?php
						list_tags_name($tags);
					?>
				</select>
			</div>
			<div style="float:left">
				&nbsp;&nbsp;&nbsp;<?php print_string('or',$book);?>&nbsp;&nbsp;&nbsp;
			</div>
			<div style="float:left">
				<textarea name="var2" style="width:400px;"></textarea>
			</div>
			<div style="float:left">
				<label><?php print_string('insertnewtag',$book);?></label>
				<input name="tag" value="">
			</div>
		</fieldset>
		<br>
<?php
		if($ac=='edit'){
?>
		<fieldset class="center divgroup" id="viewcontent" style="background: none repeat scroll 0 0 #666666 !important;">
<?php
			$tag=getTag($tagid);
			echo '<label>Tag</label>
					<textarea name="tg" style="width:400px;">'.$tag["name"].'</textarea>
				<label>Value</label>
					<textarea name="vl" rows="20" style="width:800px;">'.$tag["value"].'</textarea>
					<input type="hidden" value="update" name="action">
					<input type="hidden" value="'.$tag["id"].'" name="tagid">
					<button name="update" type="submit"> Update</button></a>';
			}
?>
		</fieldset>
		<br>
		<fieldset class="center divgroup" id="viewcontent" style="background: none repeat scroll 0 0 #666666 !important;">
			<legend><?php print_string('taglist',$book);?></legend>
			<br>Use <span style="color: #f57900;font-weight:bold;">{{tag}}</span> to be replaced by a real value from next list in your template<br>
			<table class="listmenu sidtable">
				<tr>
				  <th colspan="3"><?php print_string('tags',$book);?></th>
				</tr>
<?php
				$tags=getTag();
				foreach($tags as $tag){
					echo '<tr>
							<td>
								<a href="admin.php?current=templates_tags.php&action=edit&tagid='.$tag['id'].'">
									<img class="clicktoconfigure" style="float:left;padding:8px 8px;" title="'.get_string('edit','admin').'" />
								</a>
							</td>
							<td><span style="color: #f57900;font-weight:bold;">{{'.$tag["name"].'}}</span></td>
							<td>'.$tag["value"].'</td>
						</tr>';
					}
?>
			</table>
		</fieldset>
	</form>

</div>
