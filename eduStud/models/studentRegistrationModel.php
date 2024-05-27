<?php
include "../dbconnection.php";
class studentRegistrationModel
{
    public function getLastRegistrationNumber()
    {
        global $conn;
        $sql = "SELECT registrationNumber FROM student ORDER BY studentId DESC";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function checkRegistrationNumberDuplication($typedRegNo)
    {
        global $conn;
        $sql = "SELECT COUNT(*) AS count FROM student WHERE registrationNumber = '$typedRegNo'";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function setStudent($registrationNumber, $imageUrl, $firstName, $lastName, $fathersName, $mothersName, $dob, 
                                $mobile, $address, $countryId, $stateId, $cityId, $pinCode, $email, $gender)
    {
        global $conn;
        $sql = "INSERT INTO `student`(registrationNumber, imageUrl, firstName, lastName, fathersName, mothersName, dob, mobile, address, countryId, stateId, cityId, pinCode, email, gender) 
        VALUES('$registrationNumber', '$imageUrl', '$firstName', '$lastName', 
        '$fathersName', '$mothersName', '$dob', '$mobile', '$address', 
        $countryId, $stateId, $cityId, $pinCode, '$email', '$gender')";
        $result = mysqli_query($conn, $sql);
        $lastId=mysqli_insert_id($conn);
        return $lastId;
    }

    public function setHobbies($studentId, $reading, $music, $sports, $travel)
    {
        global $conn;
        $sql = "INSERT INTO `hobbies`(studentId,reading,music,sports,travel)
        VALUES($studentId, $reading, $music, $sports, $travel)";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function setQualifications($studentId, $examination, $board, $percentage, $yop)
    {
        global $conn;
        $sql = "INSERT INTO `qualifications`(studentId,examination,board,percentage,yop)
        VALUES($studentId, '$examination', '$board', '$percentage', $yop)";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function getCountries()
    {
        global $conn;
        $sql = "SELECT * FROM `countries`";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function getStates($countryId)
    {
        global $conn;
        $sql = "SELECT * FROM `states` WHERE countryId=$countryId";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function getCities($searchText, $stateId)
    {
        global $conn;
        $sql = "SELECT * FROM `cities` WHERE cityName LIKE '$searchText%' AND stateId=$stateId";
        $result = mysqli_query($conn,$sql);
        return $result;
    }
    
    public function getStudents()
    {
        global $conn;
        $sql = "SELECT * FROM `student` WHERE status=1";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function updateStudentDetailsWithImage($registrationNumber, $uploadImage, $firstName, $lastName, $fathersName, $mothersName, $dob, 
                                                    $mobile, $address, $country, $state, $city, $pincode, $email, $gender, $studentUpdateId)
    {
        global $conn;
        $sql = "UPDATE `student`
        SET registrationNumber='$registrationNumber',imageUrl='$uploadImage',
        firstName='$firstName',lastName='$lastName',
        fathersName='$fathersName',mothersName='$mothersName',
        dob='$dob',mobile='$mobile',address='$address',
        countryId=$country,stateId=$state,cityId=$city,
        pincode='$pincode',email='$email',gender='$gender' 
        WHERE studentId=$studentUpdateId";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function updateStudentDetailsWithoutImage($registrationNumber, $firstName, $lastName, $fathersName, $mothersName, $dob,
                                                        $mobile, $address, $country, $state, $city, $pincode, $email, $gender, $studentUpdateId)
    {
        global $conn;
        $sql = "UPDATE `student`
        SET registrationNumber='$registrationNumber',
        firstName='$firstName',lastName='$lastName',
        fathersName='$fathersName',mothersName='$mothersName',
        dob='$dob',mobile='$mobile',address='$address',
        countryId=$country,stateId=$state,cityId=$city,
        pincode='$pincode',email='$email',gender='$gender' 
        WHERE studentId=$studentUpdateId";
        $result = mysqli_query($conn,$sql);
        return $result;
    }

    public function updateHobbies($reading, $music, $sports, $travel, $studentUpdateId)
    {
        global $conn;
        $sql = "UPDATE `hobbies` SET reading=$reading, music=$music,
                sports=$sports, travel=$travel WHERE studentId=$studentUpdateId";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function updateQualifications($examination, $board, $percentage, $yop, $qualificationId)
    {
        global $conn;
        $sql = "UPDATE `qualifications` SET examination='$examination',
                board='$board', percentage=$percentage, yop='$yop' WHERE qualificationId=$qualificationId";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function deleteQualifications($qualificationId)
    {
        global $conn;
        $sql = "UPDATE `qualifications` SET status=0 WHERE qualificationId=$qualificationId";
        $result = mysqli_query($conn, $sql);
        return $result;
    }

    public function deleteStudent($studId)
    {
        global $conn;
        $sql = "UPDATE student
        JOIN qualifications ON student.studentId = qualifications.studentId
        JOIN hobbies ON student.studentId = hobbies.studentId
        SET student.status = 0,
            qualifications.status = 0,
            hobbies.status = 0
        WHERE student.studentId = $studId";
        $result = mysqli_query($conn,$sql);
        return $result;
    }

    public function getStudentRecord($studentId)
    {
        global $conn;
        // $sql = "SELECT * 
        // FROM student 
        // JOIN hobbies ON student.studentId = hobbies.studentId AND student.status = 1 AND hobbies.status = 1 
        // JOIN qualifications ON student.studentId = qualifications.studentId AND qualifications.status = 1 
        // WHERE student.studentId=$studentId";
        $sql = "SELECT s.*, c.countryName, st.stateName, ci.cityName, h.*, q.*
                FROM student s
                JOIN countries c ON s.countryId = c.countryId
                JOIN states st ON s.stateId = st.stateId
                JOIN cities ci ON s.cityId = ci.cityId
                JOIN hobbies h ON s.studentId = h.studentId AND s.status = 1 AND h.status = 1
                JOIN qualifications q ON s.studentId = q.studentId AND q.status = 1
                WHERE s.studentId = $studentId";
        $result = mysqli_query($conn, $sql);
        return $result;
    }
}
?>