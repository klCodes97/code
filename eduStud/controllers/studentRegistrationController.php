<?php
include "../models/studentRegistrationModel.php";

class studentRegistrationController
{
    public function getNewRegistrationNumber()
    {
        $objStudentRegistrationModel = new studentRegistrationModel();
        $result = $objStudentRegistrationModel->getLastRegistrationNumber();
        $row = mysqli_fetch_array($result);
        $lastRegistrationNumber = $row['registrationNumber'];
        $lastRegistrationNumberArray = explode("/", $lastRegistrationNumber);
        if(count($lastRegistrationNumberArray) == 3)
        {
            $regNo = $lastRegistrationNumberArray[2]+1;
        }
        else
        {
            $regNo = 1;
        }
        $newRegistrationNumber = "STUD/".date("Y")."/$regNo";
        $newRegistrationNumberHtml = '<input class="inputFields" type="text" name="registrationNumber" id="IdRegNo" value="'.$newRegistrationNumber.'" style="width: 100%;">
                                        <input type="hidden" name="hiddenRegNo" id="idHiddenRegNo" value="'.$newRegistrationNumber.'">';
        return $newRegistrationNumberHtml;
    }

    public function checkRegistrationNumberDuplication($registrationNumber)
    {
        $objStudentRegistrationModel = new studentRegistrationModel();
        $result = $objStudentRegistrationModel->checkRegistrationNumberDuplication($registrationNumber);
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
        return $count;
    }

    public function setStudent($registrationNumber, $imageUrl, $firstName, $lastName, $fathersName, $mothersName, $dob, 
                                $mobile, $address, $countryId, $stateId, $cityId, $pinCode, $email, $gender)
    {
        $objStudentRegistrationModel = new studentRegistrationModel();
        $lastId = $objStudentRegistrationModel->setStudent($registrationNumber, $imageUrl, $firstName, $lastName, $fathersName, $mothersName, $dob, 
                                    $mobile, $address, $countryId, $stateId, $cityId, $pinCode, $email, $gender);
        return $lastId;
    }

    public function setHobbies($studentId, $reading, $music, $sports, $travel)
    {
        $objStudentRegistrationModel = new studentRegistrationModel();
        $result = $objStudentRegistrationModel->setHobbies($studentId, $reading, $music, $sports, $travel);
        return $result;
       
    }

    public function setQualifications($studentId, $examination, $board, $percentage, $yop)
    {
        $objStudentRegistrationModel = new studentRegistrationModel();
        $result = $objStudentRegistrationModel->setQualifications($studentId, $examination, $board, $percentage, $yop);
        return $result;
    }

    public function getCountries()
    {
        $objStudentRegistrationModel = new studentRegistrationModel();
        $countries = $objStudentRegistrationModel->getCountries();
        $dropdownOptions = '';
        while($row=mysqli_fetch_assoc($countries))
        {
            $countryId          =       $row['countryId'];
            $countryName        =       $row['countryName'];
            $dropdownOptions   .=       '<option value="'.$countryName.'+'.$countryId.'">'.ucfirst($countryName).'</option>';
        }
        return $dropdownOptions;
    }

    public function getCountriesUpdate()
    {
        $objStudentRegistrationModel = new studentRegistrationModel();
        $countries = $objStudentRegistrationModel->getCountries();
        $dropdownOptions = '';
        while($row=mysqli_fetch_assoc($countries))
        {
            $countryId          =       $row['countryId'];
            $countryName        =       $row['countryName'];
            $dropdownOptions   .=       '<option value="'.$countryName.'" data-country-id="'.$countryId.'">'.ucfirst($countryName).'</option>';
        }
        return $dropdownOptions;
    }

    public function getStudents()
    {
        $objStudentRegistrationModel = new studentRegistrationModel();
        $students = $objStudentRegistrationModel->getStudents();
        $studentsList = '';
        if($students->num_rows != 0)
        {
            while($row = mysqli_fetch_assoc($students))
            {
                $studentId              = $row['studentId'];
                $registrationNumber     = $row['registrationNumber'];
                $firstName              = $row['firstName'];
                $lastName               = $row['lastName'];
                $dob                    = $row['dob'];
                $gender                 = $row['gender'];
                $mobile                 = $row['mobile'];

                $studentsList          .= '<tr>
                                                <td>'.$registrationNumber.'</td>
                                                <td>'.$firstName.'</td>
                                                <td>'.$lastName.'</td>
                                                <td>'.$dob.'</td>
                                                <td>'.$gender.'</td>
                                                <td>'.$mobile.'</td>
                                                <td>
                                                    <input type="text" name="deleteStudId" value="'.$studentId.'" hidden>
                                                    <button class="btn btn-sm btn-success me-3" onclick="GetDetails('.$studentId.')">Edit</button>
                                                    <form action="" method="post" class="deleteStudentForm d-inline" id="DeleteStud">
                                                        <input name="studId" type="hidden" value="'.$studentId.'">
                                                        <button type="submit" name="deleteStudent" class="btn btn-sm btn-danger deleteStudData" style="display:inline">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>';
            }
       
        }
        else
        {
            $studentsList.='<tr>
                                <td colspan=7 style="text-align: center;">NO DATA FOUND</td>
                            </tr>';
        }

        return $studentsList;
        
    }

    public function imageFileUpload($imageFileName)
    {
        $filenameSeperate = explode('.', $imageFileName);
        $fileExtension = strtolower($filenameSeperate[1]);
        $extension = array('jpeg', 'JPEG', 'jpg', 'JPG', 'png', 'PNG');//allowed extentons by the user
        //checkes whether the file selected by the user is allowed or not
        if(in_array($fileExtension, $extension))
        {
            $newImageName = uniqid("IMG-", true).'.'.$fileExtension;
            $uploadImage = '../images/uploads/'.$newImageName;//this to be insert in db
        }
        return $uploadImage;

    }

    public function moveUploadedImageToFolder($imageFileTmp, $imageUrl)
    {
        move_uploaded_file($imageFileTmp, $imageUrl);
    }

    public function deleteStudent($studId)
    {
        $objStudentRegistrationModel = new studentRegistrationModel();
        $result = $objStudentRegistrationModel->deleteStudent($studId);
        return $result;
    }

    public function printRegistrationForm($studentId){
        $objStudentRegistrationModel = new studentRegistrationModel();
        $result = $objStudentRegistrationModel->getStudentRecord($studentId);
        $flag = 0;
        $htmlOutput = '';
        $slNo = 1;
        foreach ($result as $row) 
        {
            if ($flag == 0) 
            {
                $registrationNumber    =      $row['registrationNumber'];
                $imageUrl              =      $row['imageUrl'];
                $firstName             =      $row['firstName'];
                $lastName              =      $row['lastName'];
                $fathersName           =      $row['fathersName'];
                $mothersName           =      $row['mothersName'];
                $dob                   =      $row['dob'];
                $gender                =      $row['gender'];
                $isMale                =      ($gender == 'male') ? '&#x2611;' : '&#x2610;';
                $isFemale              =      ($gender == 'female') ? '&#x2611;' : '&#x2610;';
                $isOthers              =      ($gender == 'others') ? '&#x2611;' : '&#x2610;';

                $mobile                =      $row['mobile'];
                $email                 =      $row['email'];
                $address               =      $row['address'];
                $countryName           =      $row['countryName'];
                $stateName             =      $row['stateName'];
                $cityName              =      $row['cityName'];
                $pinCode               =      $row['pinCode'];
                
                $reading               =      ($row['reading'] == 1) ? '&#x2611;' : '&#x2610;';
                $music                 =      ($row['music'] == 1) ? '&#x2611;' : '&#x2610;';
                $sports                =      ($row['sports'] == 1) ? '&#x2611;' : '&#x2610;';
                $travel                =      ($row['travel'] == 1) ? '&#x2611;' : '&#x2610;';


                $htmlOutput.='
                <table border="0" width="100%" cellpadding="3" style="border-collapse: collapse;">
                    <tr>
                        <th align="left" >REGISTRATION NO: '.$registrationNumber.'</th>
                        <td align="right" rowspan="4">
                            <img src="'.$imageUrl.'" width="90px" height="100px">
                        </td>
                    </tr>
                    <tr>
                        <th align="left">DATE: 22/02/2024</th>
                    </tr>
                    <tr>
                        <th style="color: #fff;">Dummytext1</th>
                    </tr>
                    <tr>
                        <th style="color: #fff;">Dummytext2</th>
                    </tr>
                </table>
                <table width="100%"  border="0" cellpadding="5px" style="border-collapse:collapse; margin-top:10px;">
                    <tr>
                        <th style="font-size:18px;">ADMISSION FORM</th>
                    </tr>
                </table>
                <div class="personalInfo" width="100%" style="margin-top:15px;">
                    <table width="95%"  border="0" cellpadding="5px" style="border-collapse:collapse;margin-left: auto; margin-right: auto;">
                        <tr style="background-color: #f0f0f0;">
                            <th align="left">PERSONAL INFORMATION</th>
                        </tr>
                    </table>
                    <table width="95%"  border="0" cellpadding="5px" style="border-collapse:collapse; margin-left: auto; margin-right: auto;margin-top:10px;">
                        <tr>
                            <td width="40%">First Name</td>
                            <td>:</td>
                            <td>'.$firstName.'</td>
                        </tr>
                        <tr>
                            <td>Last Name</td>
                            <td>:</td>
                            <td>'.$lastName.'</td>
                        </tr>
                        <tr>
                            <td>Father&#39;s Name</td>
                            <td>:</td>
                            <td>'.$fathersName.'</td>
                        </tr>
                        <tr>
                            <td>Mother&#39;s Name</td>
                            <td>:</td>
                            <td>'.$mothersName.'</td>
                        </tr>
                        <tr>
                            <td>Date of Birth</td>
                            <td>:</td>
                            <td>'.$dob.'</td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td>:</td>
                            <td style="padding: 0%;">
                                <table width="100%" border="0" cellpadding="5px" style="border-collapse:collapse;">
                                    <tr>
                                        <td width="20%">
                                            <span>'.$isMale.'</span>
                                            <span>Male</span>
                                        </td>
                                        <td width="20%">
                                            <span>'.$isFemale.'</span>
                                            <span>Female</span>
                                        </td>
                                        <td width="20%">
                                            <span>'.$isOthers.'</span>
                                            <span>Others</span>
                                        </td>
                                        <td width="40%">
            
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>Mobile Number</td>
                            <td>:</td>
                            <td>'.$mobile.'</td>
                        </tr>
                        <tr>
                            <td>Email Id</td>
                            <td>:</td>
                            <td>'.$email.'</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;" >Address</td>
                            <td style="vertical-align: top;">:</td>
                            <td>'.$address.'</td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td>:</td>
                            <td>'.$cityName.'</td>
                        </tr>
                        <tr>
                            <td>State</td>
                            <td>:</td>
                            <td>'.$stateName.'</td>
                        </tr>
                        <tr>
                            <td>Country</td>
                            <td>:</td>
                            <td>'.$countryName.'</td>
                        </tr>
                        <tr>
                            <td>PIN Code</td>
                            <td>:</td>
                            <td>'.$pinCode.'</td>
                        </tr>
                        <tr>
                            <td>Hobbies</td>
                            <td>:</td>
                            <td style="padding: 0%;">
                                <table width="100%" border="0" cellpadding="5px" style="border-collapse:collapse;">
                                    <tr>
                                        <td width="20%">
                                            <span>'.$reading.'</span>
                                            <span>Reading</span>
                                        </td>
                                        <td width="20%">
                                            <span>'.$music.'</span>
                                            <span>Music</span>
                                        </td>
                                        <td width="20%">
                                            <span>'.$sports.'</span>
                                            <span>Sports</span>
                                        </td>
                                        <td width="20%">
                                            <span>'.$travel.'</span>
                                            <span>Travel</span>
                                        </td>
                                        <td width="20%">
            
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="eduInfo" width="100%" style="margin-top:15px;">
                    <table width="95%"  border="0" cellpadding="5px" style="border-collapse:collapse;margin-left: auto; margin-right: auto;">
                        <tr style="background-color: #f0f0f0;">
                            <th align="left">EDUCATIONAL QUALIFICATIONS</th>
                        </tr>
                    </table>
                    <table width="95%"  border="1" cellpadding="5px" style="border-collapse:collapse;margin-left: auto; margin-right: auto;margin-top:10px;">
                        <tr>
                            <th align="left">SL NO</th>
                            <th align="left">EXAMINATION</th>
                            <th align="left">BOARD</th>
                            <th align="left">PERCENTAGE</th>
                            <th align="left">YEAR OF PASSING</th>
                        </tr>';
    
                $flag = 1;
            }

            $examination               =      $row['examination'];
            $board                     =      $row['board'];
            $percentage                =      $row['percentage'];
            $yop                       =      $row['yop'];
            $htmlOutput .='<tr>
                                <td>'.$slNo.'</td>
                                <td>'.$examination.'</td>
                                <td>'.$board.'</td>
                                <td>'.$percentage.'</td>
                                <td>'.$yop.'</td>
                            </tr>';
            $slNo += 1;
        }
        $htmlOutput .= '</table></div>';

       
        return $htmlOutput;
    
    }
}
?>