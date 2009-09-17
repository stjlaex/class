<?php
/**                    httpscripts/merit_adder.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sid'])){$sid=$_GET['sid'];}
elseif(isset($_POST['sid'])){$sid=$_POST['sid'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}
elseif(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}
elseif(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}

$curryear=get_curriculumyear();

$Student=fetchStudent_short($sid);
$Merits=fetchMerits($sid,6,$bid,$pid,$curryear);
$BlankMerit=fetchMerit();
$book='infobook';
$tab=0;
$inmust='yes';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Merits</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2009 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="GNU General Public License version 2" />
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/infobook.css" />
<script src="../../js/bookfunctions.js" type="text/javascript"></script>
<script src="../../js/qtip.js" type="text/javascript"></script>
</head>
<body onload="loadRequired();">

	<div id="bookbox" class="infocolor">
	<?php three_buttonmenu(); ?>

	<div id="heading">
		<label><?php print_string('student'); ?></label>
			<?php print $Student['DisplayFullName']['value'];?>
	</div>



	<div id="topform" class="topform divgroup">
		<form id="formtoprocess" name="formtoprocess" method="post" 
									action="merit_adder_action.php">


	  <div class="left">
		<label for="Detail"><?php print_string('details',$book);?></label>
		<textarea name="detail"   tabindex="<?php print $tab++;?>" 
		  id="Detail" rows="4" cols="35"></textarea>
	  </div>


	  <div class="right">
<?php 
		$listlabel='activity'; $listname='activity'; $listid='activity'; 
		list($ratingnames,$catdefs)=fetch_categorydefs('mer');
		$required='yes';
		include('../../scripts/set_list_vars.php');
		list_select_list($catdefs,$listoptions,$book);
?>
	  </div>

	  <div class="right">
<?php 
		$listlabel='points'; $required='yes'; $listname='points';
		$ratings=$ratingnames['meritpoints']; asort($ratings);
//$d_rating=mysql_query("SELECT descriptor AS name, value AS id FROM rating WHERE
//	        name='$rating_name' ORDER BY value;");
		include('../../scripts/set_list_vars.php');
		list_select_list($ratings,$listoptions,$book);
?>
	  </div>

		<input type="hidden" name="inmust" value="<?php print $inmust;?>"/>
		<input type="hidden" name="sid" value="<?php print $sid; ?>"/>
	    <input type="hidden" name="bid" value="<?php print $bid; ?>"/>
		<input type="hidden" name="pid" value="<?php print $pid; ?>"/>
		<input type="hidden" name="openid" value="<?php print $openid; ?>"/>
		</form>


	</div>


	<div id="viewcontent" class="content">

	  <table class="listmenu center">
		<caption><?php print_string('recentmerits',$book);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th></th>
			<th><?php print_string('points',$book);?></th>
		  </tr>
		</thead>
<?php
	if(array_key_exists('Merit',$Merits) and is_array($Merits['Merit'])){
		//reset($Student['Comments']['Comment']);
		while(list($key,$entry)=each($Merits['Merit'])){
			if(is_array($entry)){
				$rown=0;
				$entryno=$entry['id_db'];
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++;?>">
			<th>&nbsp</th>
			 <td>
				<?php print $entry['Activity']['value']. '  ('.display_date($entry['Date']['value']).')';?>
   			</td>
			<td>
				<?php print $entry['Points']['value'];?>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="3">
			  <p>
<?php		   if(isset($entry['Detail']['value'])){
					print $entry['Detail']['value'];
					}
				if(isset($entry['Teacher']['value'])){print
				'  - '.$entry['Teacher']['value'];}
?>
			  </p>
<?php
				/*TODO
			  <button class="rowaction" title="Delete"
				name="current" value="delete_merit.php" onClick="clickToAction(this)">
				<img class="clicktodelete" />
			  </button>
			  <button class="rowaction" title="Edit" name="Edit" onClick="clickToAction(this)">
				<img class="clicktoedit" />
			  </button>
				*/
?>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
				xmlechoer('Merit',$entry);
?>
			</div>
		  </tbody>
<?php
				}
			}
		}
?>
	  </table>

	  	<fieldset class="listmenu left">
			<div class="left">
			<label><?php print_string('house'); ?></label>
			<?php print $Merits['Total']['House']['value']; ?>
			</div>
	   	</fieldset>

	  	<fieldset class="listmenu right">
			<div class="right">
			<label><?php print get_string('total').' '.get_string('points',$book); ?></label>
			<?php print $Merits['Total']['Sum']['value']; ?>
			</div>
	   	</fieldset>

	</div>

	</div>
</body>
</html>