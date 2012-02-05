<?php
/**                    httpscripts/category_editor.php
 *
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['type'])){$type=$_GET['type'];}
elseif(isset($_POST['type'])){$type=$_POST['type'];}
if(isset($_GET['rid'])){$rid=$_GET['rid'];}
elseif(isset($_POST['rid'])){$rid=$_POST['rid'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}
elseif(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='';}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}
elseif(isset($_POST['pid'])){$pid=$_POST['pid'];}else{$pid='';}
if(isset($_GET['stage'])){$stage=$_GET['stage'];}
elseif(isset($_POST['stage'])){$stage=$_POST['stage'];}else{$stage='';}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}


/* The categories and rating details */
/* TODO: generalise to all catdef types */
if($type=='cat'){
	$catdefs=get_report_categories($rid,$bid,$pid,'cat',$stage);
	}
else{
	$catdefs=array();
	}

if($rid!=''){
	$istage=1;
	$d_r=mysql_query("SELECT course_id, stage FROM report WHERE id='$rid';");
	$report=mysql_fetch_array($d_r,MYSQL_ASSOC);
	$stages=array();
	$stages[]=array('id'=>'%','name'=>get_string('allstages','reportbook'));
	$extrastages=(array)list_course_stages($report['course_id']);
	$stages=array_merge($stages,$extrastages);
	//$stage=$report['stage'];//Make the default
	}

$maxcatn=30;/*allow a max of 30 categories*/
if($pid==''){$subject=get_subjectname($bid);}
else{$subject=get_subjectname($pid);}

$Categoryblank['id_db']=-1;
$Categoryblank['Type']=array('label'=>'type',
							 'table_db'=>'categorydef', 
							 'field_db'=>'type',
							 'type_db'=>'char(3)',
							 'value'=>''
							 );
$Categoryblank['Name']=array('label'=>'name',
							 'table_db'=>'categorydef', 
							 'field_db'=>'name',
							 'type_db'=>'varchar(240)',
							 'value'=>''
							 );
$Categoryblank['Subject']=array('label'=>'subject',
								'table_db'=>'categorydef', 
								'field_db'=>'subject_id',
								'type_db'=>'varchar(10)',
								'value_db'=>'',
								'value'=>''
								);
$Categoryblank['Stage']=array('label'=>'stage',
							  'table_db'=>'categorydef', 
							  'field_db'=>'stage',
							  'type_db'=>'char(3)',
							  'value_db'=>'',
							  'value'=>''
							  );

$Categorys=array();
$Categorys['Category']=array();
while(list($cindex,$catdef)=each($catdefs)){
	$Category=array();
	$Category['id_db']=$catdef['id'];
	$Category['Type']=array('label'=>'type',
							'table_db'=>'categorydef', 
							'field_db'=>'type',
							'type_db'=>'char(3)',
							'value'=>$type
							);

	$Category['Name']=array('label'=>'name',
							'table_db'=>'categorydef', 
							'field_db'=>'name',
							'type_db'=>'varchar(240)',
							'value'=>$catdef['name']
							);
	if($catdef['bid']=='%'){$displaysub='General';}
	else{$displaysub=$subject;}
	$Category['Subject']=array('label'=>'subject',
							   'table_db'=>'categorydef', 
							   'field_db'=>'subject_id',
							   'type_db'=>'varchar(10)',
							   'value_db'=>$catdef['subject_id'],
							   'value'=>$displaysub);
	if($catdef['stage']=='%'){$displaystage='All';}
	else{$displaystage=$catdef['stage'];}
	$Category['Stage']=array('label'=>'stage',
							 'table_db'=>'categorydef', 
							 'field_db'=>'stage',
							 'type_db'=>'char(3)',
							 'value_db'=>$catdef['stage'],
							 'value'=>$displaystage);

	$Categorys['Category'][]=$Category;
	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Category Editor</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="Affero General Public License version 3" />
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script src="../../js/editor.js" type="text/javascript"></script>
<script src="../../js/book.js?version=1013" type="text/javascript"></script>
<script src="../../js/qtip.js" type="text/javascript"></script>
</head>
<body onload="loadRequired('<?php print $book;?>');">

	<div id="bookbox">
	<?php three_buttonmenu(); ?>

<div id="heading">
	<label><?php print get_string('configure','reportbook').' '.get_string('categories','reportbook'); ?></label>
</div>

	
	<div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" 
		action="category_editor_action.php">

	  <div class="center">
		<table class="listmenu">
		  <th>&nbsp;</th>
		  <th><?php print_string($Categoryblank['Name']['label'],'reportbook');?></th>
		  <th><?php print_string($Categoryblank['Subject']['label'],'reportbook');?></th>
		  <th><?php print_string($Categoryblank['Stage']['label'],'reportbook');?></th>
<?php

		$catno=sizeof($Categorys['Category']);
		if($catno<$maxcatn){
			for($catn=$catno;$catn<$maxcatn;$catn++){
				$Categorys['Category'][]=$Categoryblank;
				}
			  }

	$tab=$maxcatn*2;
	while(list($index,$Category)=each($Categorys['Category'])){
			$catn=$index+1;
			if($catn==($catno+1)){$tab=1;}/*start tab at first blank*/
?>
		  <tr>
			<td><?php print $catn;?><input type="hidden" name="catid<?php print $catn;?>" value="<?php print $Category['id_db']; ?>" /></td>
			<td><?php $tab=xmlelement_input($Category['Name'],$catn,$tab,'reportbook');?></td>
			<td>
<?php 
			if($Category['Subject']['value']!=''){print $Category['Subject']['value'];}
			else{print $subject;}
?>
			</td>
			<td>
<?php
			if(!isset($listname)){$listname='stage';}
			if(!isset($listlabel)){$listlabel='stage';}
			if($Category['Stage']['value_db']!=''){$newstage=$Category['Stage']['value_db'];}
			else{$newstage=$stage;}
			include('../../scripts/set_list_vars.php');
			list_select_list($stages,$listoptions,'reportbook');
			unset($listopions);
?>
			</td>
		  </tr>
<?php
			}
?>
		</table>
	  </div>


	<input type="hidden" name="stage" value="<?php print $stage; ?>"/>
	<input type="hidden" name="type" value="<?php print $type; ?>"/>
	<input type="hidden" name="rid" value="<?php print $rid; ?>"/>
	<input type="hidden" name="bid" value="<?php print $bid; ?>"/>
	<input type="hidden" name="pid" value="<?php print $pid; ?>"/>
	<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
	</form>
	</div>

</body>
</html>
