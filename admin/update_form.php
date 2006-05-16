<?php

if(mysql_query("INSERT INTO form (id, teacher_id, yeargroup_id) VALUES ('$newfid', '$newtid', '$newyid')")){
				print "Created form ".$newfid." ".$newtid." ".$newyid."<br>";
				}
			else {
/*form already exists*/
				if($d_form=mysql_query("SELECT teacher_id FROM form WHERE id='$newfid'")){ 
					$oldtid=mysql_fetch_array($d_form,MYSQL_ASSOC);			
					print "Teacher id ".$oldtid{'teacher_id'}." exists for form ".$newfid.", changing to ".$newtid."<br>";
					mysql_query("UPDATE form SET teacher_id='$newtid' WHERE id='$newfid'");
					}
	   	else {print "Failed on ".$newfid." with teacher ".$newtid." !<br>";
	   			print mysql_error()."<br>";
	   			}	
				}

?>
				