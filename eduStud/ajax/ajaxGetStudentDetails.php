<?php

include "../models/studentRegistrationModel.php"; 
$objStudentRegistrationModel = new studentRegistrationModel();
if(isset($_POST['updateStudentId']))
{
    $updateStudentId = $_POST['updateStudentId'];
    $result = $objStudentRegistrationModel->getStudentRecord($updateStudentId);
    $finalResult = array();

    foreach ($result as $row) 
    {
        $studentId = $row['studentId'];
        $htmlOutput = '';
        $htmlOutput .='<input type="hidden" class="hiddenCityId" name="cityId" value="'.$row["cityId"].'">';

        // Check if the studentId already exists in $finalResult
        if (!isset($finalResult[$studentId])) 
        {
            // If not, add the student details
            $finalResult[$studentId] = array(
                'studentId'             =>      $row['studentId'],
                'registrationNumber'    =>      $row['registrationNumber'],
                'imageUrl'              =>      $row['imageUrl'],
                'firstName'             =>      $row['firstName'],
                'lastName'              =>      $row['lastName'],
                'fathersName'           =>      $row['fathersName'],
                'mothersName'           =>      $row['mothersName'],
                'dob'                   =>      $row['dob'],
                'mobile'                =>      $row['mobile'],
                'address'               =>      $row['address'],
                'countryId'             =>      $row['countryId'],
                'countryName'           =>      $row['countryName'],
                'stateId'               =>      $row['stateId'],
                'stateName'             =>      $row['stateName'],
                'cityId'                =>      $row['cityId'],
                'cityName'              =>      $row['cityName'],
                'pinCode'               =>      $row['pinCode'],
                'email'                 =>      $row['email'],
                'gender'                =>      $row['gender'],
                'status'                =>      $row['status'],
                'hobbieId'              =>      $row['hobbieId'],
                'reading'               =>      $row['reading'],
                'music'                 =>      $row['music'],
                'sports'                =>      $row['sports'],
                'travel'                =>      $row['travel'],
                'cityInfoHtml'          =>      $htmlOutput,
                'qualifications'        =>      array(),
            );
        }

        // Add qualification details to the 'qualifications' array
        $finalResult[$studentId]['qualifications'][] = array(
            'qualificationId'           =>      $row['qualificationId'],
            'examination'               =>      $row['examination'],
            'board'                     =>      $row['board'],
            'percentage'                =>      $row['percentage'],
            'yop'                       =>      $row['yop'],
        );
    }

    // Convert the associative array to indexed array
    $finalResult = array_values($finalResult);

    // Now $finalResult contains the desired structure
    echo json_encode($finalResult);
    
}
else
{
    $response['status'] = 200;
    $response['message'] = "Invalid or data not found";
}

?>