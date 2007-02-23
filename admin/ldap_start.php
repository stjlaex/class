<?php 
/*								ldap_start.php
*/

$host='admin.php';
$current='ldap_start.php';
$choice='ldap_start.php';
$action='ldap_start.php';

if(isset($_POST['seluid'])){$seluid=$_POST['seluid'];}
else{$seluid=$_SESSION['uid'];}
//$users=list_responsible_users($tid,$respons,$r);

require_once('lib/moodle/moodlelib.php');
require_once('lib/moodle/weblib.php');
require_once('lib/moodle/ldap_lib.php');

three_buttonmenu();

?>
<div class="topform">
<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
<?php
	
$d_users=mysql_query("SELECT uid,
			   	username, passwd, forename, surname, email, nologin 
				FROM users WHERE username LIKE '%'");
while($newuser=mysql_fetch_object($d_users,MYSQL_ASSOC)){
	auth_user_create($newuser,$newuser->passwd);
	}



//$users=auth_get_users();
$users=(array)auth_get_userlist();
//$users=(array)auth_get_userinfo('admin');

?>

	<label for="Staff">Staff Username</label>
		  <select name="newuid" size="4" onChange="processContent(this);">
<?php
   foreach($users as $user){
	 $name=$user['username'];
		print '<option ';
		//		if($uid==$seluid){print 'selected="selected"';}
		print	' value="'.'">'.$user.'</option>';
		}
?>
		  </select>
</div>
</form>

<div  class="content">
<?php
//$config=$CFG;
//include('lib/moodle/config.html');
?>
</div>

</div>


















