<?php
	$action='templates.php';
	$type='tmp';
	$content=$_POST['content'];
	$template_id=$_POST['template'];
	$name=$_POST['template_name'];

	if($template_id=='-1'){$d_t=mysql_query("SELECT * FROM categorydef WHERE type='$type' AND comment!='' and name='$name';");}
	else{$d_t=mysql_query("SELECT * FROM categorydef WHERE id='$template_id' and name='$name';");}

	if(mysql_num_rows($d_t)==0){mysql_query("INSERT INTO categorydef SET type='$type',name='$name',comment='".clean_text($content)."';");}
	else{
		if($template_id!='-1'){mysql_query("UPDATE categorydef SET comment='".clean_text($content)."' WHERE id='$template_id';");}
		else{mysql_query("UPDATE categorydef SET comment='".clean_text($content)."' WHERE type='$type' AND name='$name';");}
		}

	include('scripts/redirect.php');
?>
