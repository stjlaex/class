<?php 
/**									scripts/list_books.php
 *
 */

$books=$CFG->books;

if(!isset($required)){$required='yes';}
?>

  <label for="book"><?php print_string('book');?></label>
  <select name="book" id="book" size="1"
  <?php if($required=='yes'){ print ' class="required" ';} ?>
	>
   	<option value=""></option>
   	<option value="General"><?php print_string('general');?></option>
<?php
	$role=$_SESSION['role'];
	$showbooks=$books["$role"];
	foreach($showbooks as $key => $bookname){
		print '<option ';
		if(isset($book)){if($book==$key){print 'selected="selected"';}}
		print	' value="'.$key.'">'.$bookname.'</option>';
		}
?>
  </select>

 





















