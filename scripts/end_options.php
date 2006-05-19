<?php    
/**							end_options.php
 */
?>
<script>parent.loadBookOptions("<?php print $book;?>")</script>
<div id="helpcontent" class="hidden">
<?php print $book.' current='.$current.' - action='.$action.' -	cancel='.$cancel;?>
</div>
<?php
if($current!=''){
	if($book=='markbook'){
?>
        <script>parent.updateMarkDisplay(<?php print $displaymid;?>);</script>
<?php
		}
	$uid=$_SESSION['uid'];
	mysql_query("INSERT INTO history SET uid='$uid', page='$current'");
	}
?>
</body>
</html>
