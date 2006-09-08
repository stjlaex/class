<?php
/**								server_test.php
 */

$host='admin.php';
$choice='server_test.php';
$current='server_test.php';

if(!isset($_SESSION['username'])){exit;}
?>

<div class="content">

<div class="center">
<p><?php echo $HTTP_SERVER_VARS["SERVER_NAME"]; ?></p>
<p><?php echo $HTTP_SERVER_VARS["HTTP_USER_AGENT"]; ?></p>
</div>

<p><?php echo phpinfo(); ?></p>

</div>