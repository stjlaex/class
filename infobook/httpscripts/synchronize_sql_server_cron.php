#! /usr/bin/php -q
<?php
include('../../lib/fetch_student.php');
include('../../lib/community_functions.php');
require_once('../../../dbh_connect.php');

class Extract {
    private $user;
    private $password;
    private $dsn;
    private $connection;

    public static $TABLES = array(
        // "STG-LM",
        // "STG-AL",
        // "STG-MA",
        // "STG-MD",
        "STG-SE",
        //"STG-MD-TEST"
    );

    function __construct($user, $password, $dsn="mssql"){
        $this->user = $user;
        $this->password = $password;
        $this->dsn = $dsn;

        $this->connection = odbc_connect($this->dsn, $this->user, $this->password);
    }

    public function allTables(){
        $tables = array();

        foreach(Extract::$TABLES as $name) {
            $tables[$name] = $this->table($name);
        }

        return $tables;
    }

    public function table($name){
        $cursor = odbc_exec($this->connection, "select * from \"$name\"");
        $data = array();

        while(odbc_fetch_row($cursor)){
            $columns = array();

            for($i = 1; $i <= odbc_num_fields($cursor); $i++){
                $column = odbc_field_name($cursor, $i);
                $columns[$column] = odbc_result($cursor, $i);
            }

            $data []= $columns;
        }

        return $data;
    }
}

function getFormId($courseCode) {
    $formId = "";
    $splittedCourse = split(" ", $courseCode);

    if (count($splittedCourse) > 1) {
        $formId = $splittedCourse[1];
    }

    return $formId;
}

function joinCommunity($studentId, $formId){
    join_community($studentId,
        array(
            "id" => '',
            "type"=> "year",
            "name" => getYearGroup($formId)
        )
    );
}

function replace($old, $new) {
    if ($new == null || $new == "") {
        return $old;
    }

    return $new;
}

function getYearGroup($formId){
    $group = array(
        "NUR" => -1,
        "REC" => 0,
        "Y1" => 1,
        "Y2" => 2,
        "Y3" => 3,
        "Y4" => 4,
        "Y5" => 5,
        "Y6" => 6,
        "Y7" => 7,
        "Y8" => 8,
        "Y9" => 9,
        "Y10" => 10,
        "Y11" => 11,
        "Y12" => 12,
        "Y13" => 13,
    );

    return $group[$formId];
}

function createStudent($info){
    $GENDER = array(1 => "M", 2 => "F");
    $splittedName = split(',', $info["Name"]);

    $surname = $splittedName[0];
    $name = trim($splittedName[1]);

    $splittedCourse = split(" ", $info["CourseCode"]);

    $formId = "";

    if (count($splittedCourse) > 1) {
        $formId = $splittedCourse[1];
    }

    if(array_key_exists("BirthDate", $info)){
        $dob = replace($student["dob"], $info["BirthDate"]);
    } else {
        $dob = replace($student["dob"], "");
    }

    $query = "
        INSERT INTO
            student (forename, surname, gender, dob)
        VALUES (
            '$name',
            '$surname',
            '" . $GENDER[$info["Sex"]] . "',
            '$dob'
        );
    ";

    if(mysql_query($query)){
        $studentId = mysql_insert_id();

        joinCommunity($studentId, $formId);

        $query = "
            INSERT INTO
                info (
                    student_id,
                    formerupn,
                    enrolstatus,
                    nationality,
                    birthplace,
                    countryoforigin,
                    language
                )
            VALUES (
                '$studentId',
                '" . $info["CLI"] . "',
                'C',
                '" . $info["Nationality"] . "',
                '" . $info["PlaceOfBirth"] . "',
                '" . $info["CountryOfBirth"] . "',
                '" . $info["NativeLanguage"] . "'
            );
        ";

        return mysql_query($query);
    }

    return false;
}

function updateStudent($student, $info) {
    $GENDER = array(1 => "M", 2 => "F");
    $splittedName = split(',', $info["Name"]);

    $surname = $splittedName[0];
    $name = trim($splittedName[1]);

    $formId = getFormId($info["CourseCode"]);

    $name = replace($student["forename"], $name);
    $surname = replace($student["surname"], $surname);
    $gender = replace($student["gender"], $GENDER[$info["Sex"]]);

    if(array_key_exists("BirthDate", $info)){
        $dob = replace($student["dob"], $info["BirthDate"]);
    } else {
        $dob = replace($student["dob"], "");
    }

    $query = "
        UPDATE
            student
        SET
            forename = '$name',
            surname = '$surname',
            gender = '$gender',
            dob = '$dob'
        WHERE
            id = " . $student["id"] . ";";

    mysql_query($query);

    $formerUpn = $info["CLI"];
    $studentInfo = fetchStudentInfoByFormerUpn($formerUpn);

    $nationality = replace($studentInfo["nationality"], $info["Nationality"]);
    $birthPlace = replace($studentInfo["birthplace"], $info["PlaceOfBirth"]);
    $countryOrigin = replace($studentInfo["countryoforigin"], $info["CountryOfBirth"]);

    if(array_key_exists("NativeLanguage", $info)){
        $language = replace($studentInfo["language"], $info["NativeLanguage"]);
    } else {
        $language = replace($studentInfo["language"], "");
    }


    $query = "
        UPDATE
            info
        SET
            formerupn = '$formerUpn',
            nationality = '$nationality',
            birthplace = '$birthPlace',
            countryoforigin = '$countryOrigin',
            language = '$language'
        WHERE
            student_id = " . $student["id"] . ";";

    joinCommunity($student["id"], $formId);

    return mysql_query($query);
}


$user = "edustg";
$password = "S14t22g@";

$ex = new Extract($user, $password);
$tables = $ex->allTables();

$db=db_connect();

foreach ($tables as $tableName => $table) {
    $updated = 0;
    $created = 0;

    foreach ($table as $info) {
        $student = fetchStudentByFormerUpn($info["CLI"]);

        if($student) {
            if(updateStudent($student, $info)){
                $updated++;
                echo "Student " . $info["CLI"] . " was updated\n";
            } else {
                echo "Was not possible to update " . $info["CLI"] . "\n";
            }
        } else {
            if(createStudent($info)){
                $created++;
                echo "Student " . $info["CLI"] . " was created\n";
            } else {
                echo "Was not possible to create " . $info["CLI"] . "\n";
            }
        }
    }

    echo "Table: $tableName - Created: $created - Updated: $updated\n";
}

?>