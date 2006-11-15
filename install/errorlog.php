<?php 
/**								  myerrors.php
 */

function tail($file,$tail_size){   
	$tail_string='';
	if(file_exists($file)) {
        $file_contents = implode('',file($file));
        $lines = explode("\n", $file_contents);
        $line_count = count($lines);
        for($i=($line_count-$tail_size);$i < $line_count;$i++) {
            $tail_string .= $lines[$i]."\n";
			}
		} 
	else{
        $tail_string = 'couldn\'t find the log file';
		}    
	return $tail_string;
	}

?>

<html>
  <head>
    <title>devClaSS error log</title>
	<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
	<meta name="copyright" content="Copyright 2002, 2003, 2004, 2005, 2006
		Stuart Thomas Johnson. All trademarks acknowledged. All rights reserved." />
	<meta name="license" content="GNU General Public License version 2" />
	<link rel="stylesheet" type="text/css" href="../css/viewstyle.css" />
	<link rel="stylesheet" type="text/css" href="../css/logfile.css" />
  </head>

   <body>
	<script language="Javascript">
	function reload(){location='errorlog.php'}
	setTimeout("reload()", 30000);
	</script>

	<div class="externalbookframe">
	  <div class="content">
		<div class="center" style="height:10%;">
		  <?php echo tail('/var/www/myerrors.html','4');?>
		</div>
		<hr style="width:80%;"/>
		  <div class="center" style="height:80%;">
		<?php echo tail('/var/www/classerrors.xml','155');?>
		  </div>
	  </div>
	</div>
  </body>
</html>