<?php
/**											scripts/redirect.php
 *
 */
if(!isset($pausetime)){$pausetime=150;}
?>

<form name="redirect" method="post" action="<?php print $host;?>" target="_self">
	<input type="hidden" name="current" value="<?php if(isset($action)){print $action;}?>" />
	<input type="hidden" name="cancel" value="<?php if(isset($cancel)){print $cancel;}?>" />
	<input type="hidden" name="choice" value="<?php if(isset($choice)){print $choice;}?>" />
	<input type="hidden" name="cid" value="<?php if(isset($cid)){print $cid;}?>" />
	<input type="hidden" name="sid" value="<?php if(isset($sid)){print $sid;}?>" />
	<input type="hidden" name="checkmid[]" value="<?php if(isset($mid)){print $mid;}?>" />
	<input type="hidden" name="mid" value="<?php if(isset($mid)){print $mid;}?>" />
	<input type="hidden" name="yid" value="<?php if(isset($yid)){print $yid;}?>" />
	<input type="hidden" name="fid" value="<?php if(isset($fid)){print $fid;}?>" />
	<input type="hidden" name="bid" value="<?php if(isset($bid)){print $bid;}?>" />
<?php
if(isset($rids)){
	while(list($index, $rid) = each($rids)){
?>
	 	<input type="hidden" name="rids[]" value="<?php print $rid;?>">
<?php
		}
	}

if(isset($coversheet)){
?>
	 	<input type="hidden" name="coversheet" value="<?php print $coversheet;?>">
<?php
	}
if(isset($date)){
?>
	 	<input type="hidden" name="date" value="<?php print $date;?>">
<?php
	}
if(isset($entrydate)){
?>
	 	<input type="hidden" name="entrydate" value="<?php print $entrydate;?>">
<?php
	}
if(isset($date0)){
?>
	 	<input type="hidden" name="date0" value="<?php print $date0;?>">
<?php
	}
if(isset($date1)){
?>
	 	<input type="hidden" name="date1" value="<?php print $date1;?>">
<?php
	}
if(isset($selbid)){
?>
	 	<input type="hidden" name="selbid" value="<?php print $selbid;?>">
<?php
	}
if(isset($tagname)){
?>
	 	<input type="hidden" name="tagname" value="<?php print $tagname;?>">
<?php
	}
if(isset($selcrid)){
?>
	 	<input type="hidden" name="selcrid" value="<?php print $selcrid;?>">
<?php
	}
if(isset($seluid)){
?>
	 	<input type="hidden" name="seluid" value="<?php print $seluid;?>">
<?php
	}
if(isset($displaymid)){
?>
	 	<input type="hidden" name="displaymid" value="<?php print $displaymid;?>">
<?php
	}
if(isset($gena)){
?>
	 	<input type="hidden" name="gena" value="<?php print $gena;?>">
<?php
	}
if(isset($lena)){
?>
	 	<input type="hidden" name="lena" value="<?php print $lena;?>">
<?php
	}
if(isset($comment)){
?>
	 	<input type="hidden" name="comment" value="<?php print $comment;?>">
<?php
	}
if(isset($grades)){
?>
	 	<input type="hidden" name="grades" value="<?php print $grades;?>">
<?php
	}
if(isset($newcid)){
?>
	 	<input type="hidden" name="newcid" value="<?php print $newcid;?>">
<?php
	}
if(isset($newtid)){
?>
	 	<input type="hidden" name="newtid" value="<?php print $newtid;?>">
<?php
	}
if(isset($newfid)){
?>
	 	<input type="hidden" name="newfid" value="<?php print $newfid;?>">
<?php
	}
if(isset($newyid)){
?>
	 	<input type="hidden" name="newyid" value="<?php print $newyid;?>">
<?php
	}
if(isset($comid)){
?>
	 	<input type="hidden" name="comid" value="<?php print $comid;?>">
<?php
	}
if(isset($newcomid)){
?>
	 	<input type="hidden" name="newcomid" value="<?php print $newcomid;?>">
<?php
	}
if(isset($newcomtype)){
?>
	 	<input type="hidden" name="newcomtype" value="<?php print $newcomtype;?>">
<?php
	}
if(isset($comtype)){
?>
	 	<input type="hidden" name="comtype" value="<?php print $comtype;?>">
<?php
	}
if(isset($contactno)){
?>
	 	<input type="hidden" name="contactno" value="<?php print $contactno;?>">
<?php
	}
if(isset($checkeveid)){
?>
	 	<input type="hidden" name="checkeveid" value="<?php print $checkeveid;?>">
<?php
	}
?>

</form>

<script>setTimeout('document.redirect.submit()', <?php print $pausetime;?>);</script>
