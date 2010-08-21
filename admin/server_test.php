<?php
/**								server_test.php
 */

$choice='server_test.php';
$current='server_test.php';

function list_system_locales(){
    ob_start();
    system('locale -a');
    $str = ob_get_contents();
    ob_end_clean();
    return explode("\\n", trim($str));
	}

$locales = list_system_locales();

if(!isset($_SESSION['username'])){exit;}
?>

  <div id="viewcontent" class="content">

	<div class="center divgroup">
	  <p><?php print 'Server '. $HTTP_SERVER_VARS["SERVER_NAME"]; ?></p>
	  <p><?php print 'Client '. $HTTP_SERVER_VARS["HTTP_USER_AGENT"]; ?></p>
	</div>
	<div class="center divgroup">
	  <p><?php print 'Current system locale: '; ?></p>
	  <p><?php print system('locale'); ?></p>
	  <p>Available locales: 
<?php
while(list($index,$locale)=each($locales)){
	print $locale;
	}
?>
	  </p>
	</div>

	<?php echo phpinfo(); ?>

  </div>
