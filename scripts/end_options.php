<?php    
/**							end_options.php
 */
?>
<script>parent.loadBookOptions("<?php print $book;?>")</script>
<div id="helpcontent" class="hidden">
<?php 
print $book;
if(isset($current)){print ' current='.$current.' - action='.$action.' - cancel='.$cancel;}
else{$current='';}
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

	$uid=$_SESSION['uid'];
	mysql_query("INSERT INTO history SET uid='$uid', page='$current'");
	}
?>
</body>
</html>
