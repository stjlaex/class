<?php 

?>

<h2>Creating a new report</h2>
<?php
	if (!isset($cycle)):
?>

<form method="post" action="<?=$PHP_SELF?>">
  Name of report cycle :<input type="Text" name="cycle" size='20'><br>

  Commentary :<input type="Text" name="comment" size='100'><br>
<?php
	print "Deadline ";
	include ("scripts/date-form.php");

	print "<br>"; 
?>   
  <input type="Submit" name="submit" value="Enter information">
</form>
<p>

<?php

else:

		$deadline =	$YEAR."-".$MONTH."-".$DAY;
		if (mysql_query("INSERT INTO report (cycle, deadline, comment) VALUES ('$cycle', '$deadline', '$comment')"))	
		{$rows_affected = mysql_affected_rows($dbh);
 	   print "Successfully created new report entry.<br>";
 	   $d = mysql_query("SELECT MAX(id) FROM report");
 	   $new_id = mysql_result($d,0);
 	   }
	   else 
	   {print "Failed! Report may already exist.<br>";}

		$report_table="report".$new_id;
		if (mysql_query("CREATE TABLE $report_table (
      		student_id		int not null,
       		class_id			varchar(10) not null,
       		grade	 			enum(\"A*\",\"A\",\"B+\",\"B\",\"C\",\"D\"),
       		grade2	 		enum(\"A*\",\"A\",\"B+\",\"B\",\"C\",\"D\"),
       		comment		 	blob,
       		primary key	(class_id, student_id)  )"))	
		{$rows_affected = mysql_affected_rows($dbh);
 	   print "Successfully created new report table.<br>";}
	   else 
	   {print "Failed new table! $new_id, $report_table<br>";}
		
endif;

//		include("scripts/end_options.php");
?>




















