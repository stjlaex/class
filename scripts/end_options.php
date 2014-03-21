<?php
/**							end_options.php
 */
?>
<script src="js/jquery.uniform.min.js"></script>
<script src="js/jquery.table_sort.js"></script>

<script>
parent.loadRequired("<?php print $book; ?>");parent.loadBookOptions("<?php print $book; ?>");</script>
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
<script>
parent.updateMarkDisplay(<?php print $displaymid; ?>);</script>
<?php
}
if($current=='contact_details.php' and $CFG->enrol_geocode_off=='no'){
?>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?php echo $CFG -> api_key; ?>&sensor=false"></script>
<script type="text/javascript">
    destination="<?php echo '(' . $CFG -> sitelatlng[0] . ',' . $CFG -> sitelatlng[1] . ')'; ?>";</script>
<script type="text/javascript" src="js/geocoding.js"></script>
<script>initAddressMap();</script>
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
<?php
    if($current=='register_list.php' || 'new_student.php'){
?>
<script>
    $('.edit select, #formtoprocess select, #formtoprocess2 select').uniform({ wrapperClass : "registerEdit" }); 
</script>
<?php
}
?>
<script>
    $(":checkbox, :radio").not('.hidden').uniform();
</script>

<script>
    $(function(){
    $(".sidtable").tableSort();
});
</script>




</body>
</html>

