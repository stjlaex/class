<?php 
/**								  errorlog.php
 *
 * Example to force a call would be:
 *    trigger_error('my message',E_USER_WARNING);
 */

require('../../school.php');

function tail_html($file,$tail_size){   
	$tail_string='';
	if(file_exists($file)){
        $file_contents=implode('',file($file));
        $lines=explode("\n", $file_contents);
        $line_count=count($lines)-1;
        for($i=$line_count;$i>($line_count-$tail_size);$i--) {
            $tail_string.='<p>'.$lines[$i].'</p>';
			}
		}
	else{
        $tail_string = 'couldn\'t find the log file';
		}    
	return $tail_string;
	}

function tail_xml($file,$tail_size){   
	$tail_string='';
	if(file_exists($file)){
        $file_contents=implode('',file($file));
        $lines=explode("\n", $file_contents);
        $line_count=count($lines);
        for($i=($line_count-$tail_size);$i<$line_count;$i++){
            $tail_string.=$lines[$i]."\n";
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
	<meta name="copyright" content="Copyright 2002-2007 Stuart Thomas Johnson. All trademarks acknowledged. All rights reserved." />
	<meta name="license" content="GNU General Public License version 2" />
	<link rel="stylesheet" type="text/css" href="../css/bookstyle.css" />
	<link rel="stylesheet" type="text/css" href="../css/logfile.css" />
  </head>

   <body>
	<script language="Javascript">
	function reload(){location="errorlog.php"}
	setTimeout("reload()", 2400000);
	</script>

	  <div class="externalbookframe">
		<div class="header">
		  <?php print 'Last update '.date('H:i:s');?>
		</div>
		<div id="sitelogo" class="fixed">
		  <img name="sitelogo" src="../images/orangelogo.png"  onclick="reload()"/>
		</div>
		<div class="content">
		  <div class="fullwidth" style="height:12%;">
			<?php echo tail_html($CFG->serverlog,'30');?>
		  </div>
		  <hr style="width:80%;"/>
		  <div id="classerrors" class="fullwidth" style="height:80%;">
			  <?php echo tail_xml($CFG->classlog,'400');?>
		  </div>
		</div>
	  </div>
	</body>
  </html>