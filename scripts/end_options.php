<?php
/**							end_options.php
 */
?>
    <script src="js/jquery-1.8.2.min.js"></script>
    <script language="JavaScript" type="text/javascript">
        var pathtobook= "<?php print $CFG->sitepath.'/'.$CFG->applicationdirectory.'/'.$book.'/'; ?>";
        var pathtoapplication = "<?php print $CFG->sitepath.'/'.$CFG->applicationdirectory.'/'; ?>";
        var book = "<?php print $book; ?>";
    </script>
<?php
if ($CFG->debug == 'dev' or !file_exists("js/appbook.min.js")) {
    print '<script language="JavaScript" type="text/javascript" src="js/qtip.js"></script>
    <script language="JavaScript" type="text/javascript" src="lib/jscalendar/calendar.js"></script>
    <script language="JavaScript" type="text/javascript" src="lib/jscalendar/calendar-setup.js"></script>
    <script language="Javascript" type="text/javascript" src="js/jquery.uniform.min.js"></script>
    <script language="Javascript" type="text/javascript" src="js/jquery.table_sort.js"></script>
    <script language="Javascript" type="text/javascript" src="js/documentdrop.js"></script>
    <script language="Javascript" type="text/javascript" src="js/jcrop/jquery.Jcrop.min.js"></script>
    <script language="Javascript" type="text/javascript" src="js/crop.js"></script>
    <script language="JavaScript" type="text/javascript" src="js/book.js"></script>';
} else {
    print '<script src="js/appbook.min.' . str_replace('.', '', $CFG->version) . '.js"></script>';
    }
?>
    <script language="JavaScript" type="text/javascript" src="lib/jscalendar/lang/calendar-<?php  print_string('shortlocale'); ?>.js"></script>
        <?php
if($book=='infobook' or $book=='admin'){
?>

        <?php
}
?>
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
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php echo $CFG -> api_key; ?>&sensor=false"></script>
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
$ip=$_SERVER['REMOTE_ADDR'];
$browser=$_SERVER['HTTP_USER_AGENT'];
mysql_query("INSERT INTO history SET uid='$uid', page='$current',classis_version='$CFG->version',browser_version='$browser',ip='$ip';");
}
?>
<?php
    if($current=='register_list.php' || 'new_student.php'){
    }
?>
<script>
 //   $("input").uniform()
    $(function(){
        $(".sidtable").tableSort();
        $(".sidtable a.sortable").on("click", function(event) {
            uniformifyCheckboxes();
            event.preventDefault()
            });
    });
</script>
</body>
</html>
