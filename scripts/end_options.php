<?php    
/**							end_options.php
 */
?>
<script>parent.loadRequired("<?php print $book;?>");parent.loadBookOptions("<?php print $book;?>");</script>
<div id="helpcontent" class="hidden">
<?php 
/**
 * TODO: decide if helpcontent will ever be served up...
print $book;
if(isset($current)){print ' current='.$current.' - action='.$action.' - cancel='.$cancel;}
else{$current='';}
*/
?>
</div>
<?php
if($current!=''){
	if($book=='markbook'){
?>
        <script>parent.updateMarkDisplay(<?php print $displaymid;?>);</script>
<?php
		}
	elseif($book=='register' and isset($notice) and $notice!=''){
?>
        <script>parent.openAlert('<?php print $notice;?>');</script>
<?php
		}

	/* This flags a change of student/parent details or status 
	 * and logs to the update_event table. 
	 */
	if(isset($update_flag) and $update_flag and !empty($sid) and $sid!=-1){
		set_update_event($sid);
		}

	$uid=$_SESSION['uid'];
	mysql_query("INSERT INTO history SET uid='$uid', page='$current'");
	}
?>
</body>
</html>
