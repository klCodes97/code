<?php
include "../models/studentRegistrationModel.php"; 
$objStudentRegistrationModel = new studentRegistrationModel();
if(isset($_POST['changedRegNo']))
{
    
    $typedRegNo =   $_POST['changedRegNo'];
    $result = $objStudentRegistrationModel->checkRegistrationNumberDuplication($typedRegNo);
    $row = mysqli_fetch_assoc($result);
    $regNoCount = $row['count'];
    echo $regNoCount;
    
}
?>