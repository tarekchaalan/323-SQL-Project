<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
        function connectToDb($host, $username, $pass, $dbName)
        {
            
            $link = mysqli_connect($host, $username, $pass, $dbName);
            if(!$link)
                echo("could not connect<br>");
            
            else
                echo("Connection established<br>");
            
            return $link;
        }

        function getProfCourseInfo($sql, $ssn)
        {
            $query = "SELECT c.title, s.classRoom, s.meetingDays, s.beginning, s.endTime
                      FROM COURSES c, SECTION s, PROFESSOR p
                      WHERE p.ssn = {$ssn} AND p.ssn = s.ssn AND s.cNum = c.courseNum";
                      $result = $sql->query($query);
            
            return $result;
        }

        function getGradeCount($sql, $courseNumber, $sectionNumber)
        {
            $query = "SELECT grade, COUNT(*)
            FROM ENROLLMENT_RECORDS
            WHERE cNum = {$courseNumber} AND secNum = {$sectionNumber}
            GROUP BY grade";
            $result = $sql->query($query);

            return $result;
        }

        function getCourseInfo($sql, $courseNumber)
        {
            $query = "SELECT DISTINCT s.secNum, s.classRoom, s.meetingDays, s.beginning, s.endTime, COUNT(e.id)
            FROM SECTION s, COURSES c, ENROLLMENT_RECORDS e
            WHERE c.courseNum = {$courseNumber} AND c.courseNum = s.cNum AND e.secNum = s.secNum
            GROUP BY e.secNum";

            $result = $sql->query($query);

            return $result;
        }

        function getStudentEnrollmentRecords($sql, $id)
        {
            $query = "SELECT DISTINCT s.cNum, e.grade
            FROM COURSES c, SECTION s, ENROLLMENT_RECORDS e, STUDENT s1
            WHERE s1.id = {$id} AND e.id = s1.id AND e.secNum = s.secNum";
            $result = $sql->query($query);

            return $result;
        }

        

        function outputProfCourseInfo($result)
        {
            $x = $result->num_rows;
            echo("<br>query 1: Get professor course info<br>");
            echo("<table border = '1'>
            <tr>
            <th>Title</th>
            <th>Classoom</th>
            <th>Meeting Days</th>
            <th>Start</th>
            <th>End</th>
            </tr>");

            for($i = 0; $i < $x; $i++)
            {
                echo("<tr>");
                $row = $result->fetch_assoc();
                echo("<td>" . $row['title'] . "</td>");
                echo("<td>" . $row['classRoom'] . "</td>");
                echo("<td>" . $row['meetingDays'] . "</td>");
                echo("<td>" . $row['beginning'] . "</td>");
                echo("<td>" . $row['endTime'] . "</td>");
                echo("<tr>");
            }

            echo "</table><br><br>";

            $result->free_result();
        }

        function outputGradeCount($result)
        {
            $x = $result->num_rows;
            echo("query 2: Count distinct grades given course and section number<br>");
            echo("<table border = '1'>
            <tr>
            <th>Grade</th>
            <th>Count</th>
            </tr>");

            for($i = 0; $i < $x; $i++)
            {
                echo("<tr>");
                $row = $result->fetch_assoc();
                echo("<td>" . $row['grade'] . "</td>");
                echo("<td>" . $row['COUNT(*)'] . "</td>");
                echo("<tr>");
            }

            echo "</table><br><br>";

            $result->free_result();
        }

        function outputCourseInfo($result)
        {

            $x = $result->num_rows;
            echo("query 3: Given course number, display information about course<br>");
            echo("<table border = '1'>
            <tr>
            <th>Section Number</th>
            <th>Classroom</th>
            <th>Meeting Days</th>
            <th>Start</th>
            <th>End</th>
            <th>Currently Enrolled</th>
            </tr>");

            for($i = 0; $i < $x; $i++)
            {
                echo("<tr>");
                $row = $result->fetch_assoc();
                echo("<td>" . $row['secNum'] . "</td>");
                echo("<td>" . $row['classRoom'] . "</td>");
                echo("<td>" . $row['meetingDays'] . "</td>");
                echo("<td>" . $row['beginning'] . "</td>");
                echo("<td>" . $row['endTime'] . "</td>");
                echo("<td>" . $row['COUNT(e.id)'] . "</td>");
                echo("<tr>");
            }

            echo "</table><br><br>";

            $result->free_result();

        }

        function outputStudentEnrollmentRecords($result)
        {
            $x = $result->num_rows;
            echo("query 4: Given student CWID, display their enrollment records<br>");
            echo("<table border = '1'>
            <tr>
            <th>Course Number</th>
            <th>Grade</th>
            </tr>");

            for($i = 0; $i < $x; $i++)
            {
                echo("<tr>");
                $row = $result->fetch_assoc();
                echo("<td>" . $row['cNum'] . "</td>");
                echo("<td>" . $row['grade'] . "</td>");
                echo("<tr>");
            }

            echo "</table><br><br>";

            $result->free_result();
        }

    require __DIR__ . '/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $host = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];
    $dbName = $_ENV['DB_NAME'];
 
    $conn = new mysqli($host, $username, $password, $dbName);
        
        //query 1
        $ssn = '222222222';
        $result = getProfCourseInfo($sql, $ssn);
        outputProfCourseInfo($result);

        //query 2
        $courseNumber = 301;
        $sectionNumber = 20;

        $result = getGradeCount($sql, $courseNumber, $sectionNumber);
        outputGradeCount($result);

        //query 3
        $courseNumber = 371;
        $result = getCourseInfo($sql, $courseNumber);
        outputCourseInfo($result);

        //query 4
        $id = '12345678';
        $result = getStudentEnrollmentRecords($sql, $id);
        outputStudentEnrollmentRecords($result);

        

        $sql->close();
        
    ?>  
</body>
</html>