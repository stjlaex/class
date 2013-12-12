<?php
	$action='templates.php';
	$content=$_POST['content'];
	$name=$_POST['template_name'];
	$d_t=mysql_query("SELECT * FROM categorydef WHERE type='tmp' AND comment!='' and name='$name';");
	if(mysql_num_rows($d_t)==0){mysql_query("INSERT INTO categorydef SET type='tmp',name='$name',comment='".clean_text($content)."';");}
	else{mysql_query("UPDATE categorydef SET comment='".clean_text($content)."' WHERE type='tmp' AND name='$name';");}
	include('scripts/redirect.php');
?>
