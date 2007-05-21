<?php
/**									scripts/set_book_vars.php
 */
$current='';
$choice='';
$action='';
$cancel='';
if(isset($_SESSION[$book.'current'])){$current=$_SESSION[$book.'current'];}
if(isset($_SESSION[$book.'choice'])){$choice=$_SESSION[$book.'choice'];}
if(isset($_GET['current'])){$current=$_GET['current'];}
if(isset($_GET['choice'])){$choice=$_GET['choice'];}
if(isset($_GET['cancel'])){$cancel=$_GET['cancel'];}
if(isset($_POST['current'])){$current=$_POST['current'];}
if(isset($_POST['choice'])){$choice=$_POST['choice'];}
if(isset($_POST['cancel'])){$cancel=$_POST['cancel'];}
$_SESSION[$book.'current']=$current;
$_SESSION[$book.'choice']=$choice;
?>