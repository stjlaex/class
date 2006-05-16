<?php 
/* 								column_hide.php
	Expects 
	Optional 
	Returns 
*/

$host="markbook.php";
$current="class_view.php";
$action="";
$choice="class_view.php";

/* Make sure a column is checked*/
if(!isset($_POST{'checkmid'})){$current="class_view.php";
			include("scripts/redirect.php");
			exit;}

$umns=$_SESSION{'umns'};
$checkmid=$_POST{'checkmid'};

?>
<fieldset class="leftcenterrighttopmiddlebottom"><legend>Results</legend>

<br />	

<?php	
	
	for ($c=0; $c < sizeof($checkmid); $c++){
		if ($umnrank==$checkmid[$c]){
			print "It is not possible to hide the column selected to rank by!";
?>
			</fieldset>
			<script>setTimeout(2000);</script>
<?php
			include("scripts/redirect.php");
			exit;
			}
			
		$d_mark=mysql_query("SELECT visible, hidden FROM mark WHERE	id='$checkmid[$c]'");
		$mark = mysql_fetch_array($d_mark,MYSQL_ASSOC);
		if($mark{'hidden'}=='yes'){
			$visible=explode (";",$mark{'visible'});
			for($c2=0;$c2<sizeof($visible);$c2++){
				$atid=$visible[$c2];
				if($atid!=$tid){$newvisible=";".$atid;}
				}
			}
		else{
			$newvisible=$mark{'visible'}.";".$tid;
			}
		if(mysql_query("UPDATE mark SET visible='$newvisible' WHERE id='$checkmid[$c]'")){
		print "Column will be hidden.<br />";
		}
		else{print "Failed! "; $error=mysql_error(); print $error."<br />";}
		}
	
?>
</fieldset>
<script>setTimeout(2000);</script>
<?php
			include("scripts/redirect.php");

?>




















































