<?php
/**												lib/language.php
 * Originally taken from moodlelib.php
 * Changes are ongoing to entirely ClaSSify it.
 */

/// STRING TRANSLATION  ////////////////////////////////////////

/**
 * Returns the code for the current language
 *
 * @uses $CFG
 * @return string
 */
function current_language(){
	global $CFG;
	if(!empty($_SESSION['lang'])){
		// Session language chosen by user
		return $_SESSION['lang'];
		}
	else {
		return $CFG->sitelang;
		}
	}

/* Return the code of the current charset, currently always UTF-8!
 * @return string
 */
function current_charset($ignorecache = false){
	$currentcharset='UTF-8';
    return $currentcharset;
	}

/**
 * Prints out a translated string.
 *
 * Prints out a translated string using the return value from the {@link get_string()} function.
 *
 * Example usage of this function when the string is in the class.php file:<br>
 * <code>
 * echo '<strong>';
 * print_string('wordforstudent');
 * echo '</strong>';
 * </code>
 *
 * Example usage of this function when the string is not in the class.php file:<br>
 * <code>
 * echo '<h1>';
 * print_string('typecourse', 'calendar');
 * echo '</h1>';
 * </code>
 *
 * @param string $identifier The key identifier for the localized string
 * @param string $book The module where the key identifier is stored. 
 * @param mixed $a An object, string or number that can be used
 * within translation strings
 */
function print_string($identifier, $book='', $a=NULL){
    echo get_string($identifier, $book, $a);
	}

/**
 * fix up the optional data in get_string()/print_string() etc
 * ensure possible sprintf() format characters are escaped correctly
 * needs to handle arbitrary strings and objects
 * @param mixed $a An object, string or number that can be used
 * @return mixed the supplied parameter 'cleaned'
 */
function clean_getstring_data( $a ){
    if (is_string($a)) {
        return str_replace( '%','%%',$a );
    }
    elseif (is_object($a)) {
        $a_vars = get_object_vars( $a );
        $new_a_vars = array();
        foreach ($a_vars as $fname => $a_var) {
            $new_a_vars[$fname] = clean_getstring_data( $a_var );
        }
        return (object)$new_a_vars;
    } 
    else {
        return $a;
    }
}

/**
 * Returns a localized string.
 *
 * Returns the translated string specified by $identifier as
 * for $book.  Uses the same format files as STphp.
 * $a is an object, string or number that can be used
 * within translation strings
 *
 * eg "hello \$a->firstname \$a->lastname"
 * or "hello \$a"
 *
 * If you would like to directly echo the localized string use
 * the function {@link print_string()}
 *
 * Example usage of this function involves finding the string you would
 * like a local equivalent of and using its identifier and module information
 * to retrive it.<br>
 * If you open class/lang/en/class.php and look near line 1031
 * you will find a string to prompt a user for their word for student
 * <code>
 * $string['wordforstudent'] = 'Your word for Student';
 * </code>
 * So if you want to display the string 'Your word for student'
 * in any language that supports it on your site
 * you just need to use the identifier 'wordforstudent'
 * <code>
 * $mystring = '<strong>'. get_string('wordforstudent') .'</strong>';
or
 * </code>
 * If the string you want is in another file you'd take a slightly
 * different approach. Looking in class/lang/en/calendar.php you find
 * around line 75:
 * <code>
 * $string['typecourse'] = 'Course event';
 * </code>
 * If you want to display the string "Course event" in any language
 * supported you would use the identifier 'typecourse' and the module 'calendar'
 * (because it is in the file calendar.php):
 * <code>
 * $mystring = '<h1>'. get_string('typecourse', 'calendar') .'</h1>';
 * </code>
 *
 * As a last resort, should the identifier fail to map to a string
 * the returned string will be [[ $identifier ]]
 *
 * @uses $CFG
 * @param string $identifier The key identifier for the localized string
 * @param string $book The book where the key identifier is stored. If
 * none is specified then class.php is used.
 * @param mixed $a An object, string or number that can be used
 * within translation strings
 * @return string The localized string.
 */
function get_string($identifier, $book='', $a=NULL) {

    global $CFG;

    $lang=current_language();

    // if $a happens to have % in it, double it so sprintf() doesn't break
    if($a){
        $a=clean_getstring_data( $a );
		}

	/// Define the two or three major locations of language strings for this book
	$langfiles=langfile_locations($lang,$book);

	/// First check all the normal locations for the string in the current language
    foreach($langfiles as $langfile){
        if(file_exists($langfile)){
            if($result=get_string_from_file($identifier, $langfile, "\$resultstring")){
                eval($result);
                return $resultstring;
				}
			}
		}

	/// If the preferred language was English we can abort now
    if($lang=='en'){
        return '[['. $identifier .']]';
		}

	/// Is a parent language defined?  If so, try to find this string in a parent language file

    foreach($langfiles as $langfile){
        if(file_exists($langfile)){
            if($result=get_string_from_file('parentlanguage', $langfile, "\$parentlang")) {
                eval($result);
                if(!empty($parentlang)){   // found it!
					$parentlangfiles=langfile_locations($parentlang,$book);
                    $langfile=$parentlangfiles[0];
                    if(file_exists($langfile)){
                        if($result=get_string_from_file($identifier,$langfile,"\$resultstring")){
                            eval($result);
                            return $resultstring;
							}
						}
					}
				}
			}
		}

/// Our only remaining option is to try English
	$langfiles=langfile_locations('en',$book);
    foreach($langfiles as $langfile){
        if(file_exists($langfile)){
            if($result=get_string_from_file($identifier,$langfile,"\$resultstring")){
                eval($result);
                return $resultstring;
				}
			}
		}

    return '[['.$identifier.']]';  // Last resort
}

function langfile_locations($lang,$book=''){
	global $CFG;
	$langfiles[]=$CFG->dirroot .'/lang/'.$lang.'/class.php';
    if($book!=''){
		$langfiles[]=$CFG->dirroot .'/lang/'.$lang.'/'.$book.'.php';
		$langfiles[]=$CFG->dirroot .'/'.$book.'/lang/'.$lang.'/'.$book.'.php';
		}
	return $langfiles;
}

/**
 * This function is only used from {@link get_string()}.
 *
 * @internal Only used from get_string, not meant to be public API
 * @param string $identifier ?
 * @param string $langfile ?
 * @param string $destination ?
 * @return string|false ?
 * @staticvar array $strings Localized strings
 * @access private
 * @todo Finish documenting this function.
 */
function get_string_from_file($identifier, $langfile, $destination) {

    static $strings;    // Keep the strings cached in memory.

    if(empty($strings[$langfile])){
        $string=array();
        include($langfile);
		if(file_exists('../schoollang.php')){include('../schoollang.php');}
        $strings[$langfile]=$string;
		}
	else{
        $string=&$strings[$langfile];
		}

    if(!isset($string[$identifier])){
        return false;
		}

    return $destination .'= sprintf("'. $string[$identifier] .'");';
	}

/**
 * Converts an array of strings to their localized value.
 *
 * @param array $array An array of strings
 * @param string $book The language book that these strings can be found in.
 * @return string
 */
function get_strings($array, $book='') {

   $string = NULL;
   foreach($array as $item){
       $string->$item = get_string($item, $book);
   }
   return $string;
}

/**
 * Returns a list of language codes and their full names
 * @return array An associative array with contents in the form of LanguageCode => LanguageName
 */
function get_list_of_languages(){
    global $CFG;

	require_once($CFG->dirroot.'/lang/include.php');

    foreach($languages as $lang=>$language){
		if(file_exists($CFG->dirroot.'/lang/'. $lang .'/class.php')){
			include($CFG->dirroot.'/lang/'. $lang .'/class.php');
			$okaylanguages[$lang]=$string['thislanguage'];
			unset($string);
			}
		}
    return $okaylanguages;
	}


/**
 * Can include a given document file (depends on second
 * parameter) or just return info about it.
 *
 * @uses $CFG
 * @param string $file ?
 * @param boolean $include ?
 * @return ?
 * @todo Finish documenting this function
 */
function document_file($file, $include=true) {
    global $CFG;

    $file = clean_filename($file);

    if (empty($file)) {
        return false;
    }

    $langs = array(current_language(), get_string('parentlanguage'), 'en');

    foreach ($langs as $lang) {
        $info->filepath = $CFG->dirroot .'/lang/'. $lang .'/docs/'. $file;
        $info->urlpath  = $CFG->wwwroot .'/lang/'. $lang .'/docs/'. $file;

        if (file_exists($info->filepath)) {
            if ($include) {
                include($info->filepath);
            }
            return $info;
        }
    }

    return false;
}

function update_user_language($langchoice){
	global $CFG;
	if($_SESSION['uid']!=0 and $langchoice!=''){
		$username=$_SESSION['username'];
		if(mysql_query("UPDATE users SET
				 language='$langchoice' WHERE username='$username';")){}
		else {print mysql_error(); exit;}
		}
	$_SESSION['lang']=$langchoice;
}
?>