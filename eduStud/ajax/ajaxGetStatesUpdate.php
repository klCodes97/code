<?php
include "../models/studentRegistrationModel.php"; 
$objStudentRegistrationModel = new studentRegistrationModel();

//while populate & onchange country
if(isset($_POST['countryId']))
{
    $countryId          =   $_POST['countryId'];
    $result             =   $objStudentRegistrationModel->getStates($countryId);
    $dropdownOptions    =   '';

    while($row=mysqli_fetch_array($result))
    {
        $dropdownOptions .= '<option value="'.$row['stateName'].'" data-state-id="'.$row['stateId'].'">'.$row['stateName'].'</option>';
    }
    echo $dropdownOptions;
}
