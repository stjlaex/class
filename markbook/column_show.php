<?php 
/* 									column_show.php
*/

$host="markbook.php";
$current="class_view.php";
$action=".php";
$choice="class_view.php";
$mid=$_POST{'mid'};

?>
<fieldset class="leftcenterrighttopmiddlebottom"><legend>Results</legend>

<br />	

<?php

if(!isset($mid))
	{
	print "No hidden column.";
	}
else
	{
   	$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
	$mark = mysql_fetch_array($d_mark,MYSQL_ASSOC);
	if($mark{'hidden'}=='yes')
		{
		print"Mark is hidden.<br />";
		$newvisible=$mark{'visible'}.";".$tid;
		}
	else{
		$visible=explode (";",$mark{'visible'});
		for($c=0;$c<sizeof($visible);$c++)
				{
				$atid=$visible[$c];
				if($atid!=$tid){$newvisible=";".$atid;}
				}
		}
   	if(mysql_query("UPDATE mark SET visible='$newvisible' WHERE id='$mid'"))
		{
		print "Column will be shown.<br />";
		}
		else
		{
		print "Failed!<br />";
		$error=mysql_error();
		print $error."<br />";
		}
	}

?>
</fieldset>
<script>setTimeout(2000);</script>
<?php
		include("scripts/redirect.php");

?>




















































