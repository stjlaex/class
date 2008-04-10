<?php
/**								server_test.php
 */

$host='admin.php';
$choice='server_test.php';
$current='server_test.php';


function list_system_locales(){
    ob_start();
    system('locale -a');
    $str = ob_get_contents();
    ob_end_clean();
    return split("\\n", trim($str));
	}

$locales = list_system_locales();

if(!isset($_SESSION['username'])){exit;}
?>

  <div class="content">

	<div class="center divgroup">
	  <p><?php print 'Server '. $HTTP_SERVER_VARS["SERVER_NAME"]; ?></p>
	  <p><?php print 'Client '. $HTTP_SERVER_VARS["HTTP_USER_AGENT"]; ?></p>
	</div>
	<div class="center divgroup">
	  <p><?php print 'Current system locale: '; ?></p>
	  <p><?php print system('locale'); ?></p>
	  <p>
<?php
while(list($index,$locale)=each($locales)){
	print $locale;
	}
?>
	  </p>
	  <p>
<?php
$locale='en_GB';
if(in_array($locale, $locales)){
	print $locale.' is available.';
	}
else{
	print $locale.' is not available.';
	}
?>
	  </p>
	  <p>
<?php 
	setlocale(LC_CTYPE, 'en_GB');
	$test=utf8_encode(chr(209)); 
	print 'Test '. $test. ' '. iconv('UTF-8', 'ASCII//TRANSLIT', $test); 
?>
	  </p>
	</div>

	  <p><?php echo phpinfo(); ?></p>

  </div>