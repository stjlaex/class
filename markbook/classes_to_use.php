<?php
/**										markbook/classes_to_use.php
 *	Selects classes to use when defining marks, levels, etc., 
 */
	$crids=array();
	$bids=array();
    for($c=0;$c<(sizeof($cids));$c++){
		$cid=$cids[$c];
		$d_class=mysql_query("SELECT subject_id, course_id FROM class
											WHERE id='$cid'");
		$bid=mysql_result($d_class,0,0);
		$crid=mysql_result($d_class,0,1);
   		if(!in_array($bid,$bids)){$bids[]=$bid;}
   		if(!in_array($crid,$crids)){$crids[]=$crid;}
		}
    for($c=0;$c<(sizeof($respons)-1);$c++){
		$crid=$respons[$c]{'course_id'};
   		if(!in_array($crid,$crids)){$crids[]=$crid;}
		$bid=$respons[$c]{'subject_id'};
   		if(!in_array($bid,$bids)){$bids[]=$bid;}
		}
?>
  <legend><?php print_string('classesthatusethismark',$book);?></legend>
  <div class="left">
	<label for="Classes by course"><?php print_string('course',$book);?></label>
	<select class="required" name="crid" id="Classes by course" size="8">
<?php
    for($c=0;$c<(sizeof($crids)); $c++){	
		$out=$crids[$c];
		print "<option value='".$out."'";
		if($out=='%'){$out='all';};
		print ">".$out."</option>";
		}
?>
	</select>
  </div>

  <div class="right">
	<label for="Classes by subject"><?php print_string('subject',$book);?></label>
	<select class="required" name="bid" id="Classes by subject" size="8">
<?php    
    for($c=0;$c<(sizeof($bids));$c++){	
		$out=$bids[$c];
		print "<option value='".$out."'";
		if($out=='%'){$out='all';};
		print ">".$out."</option>";
		}
?>
	</select>
  </div>