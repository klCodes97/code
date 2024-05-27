<?php
include "../models/studentRegistrationModel.php"; 
$objStudentRegistrationModel = new studentRegistrationModel();
if(isset($_POST['stateId']))
{
    $output     =   '';
    $stateId    =   $_POST['stateId'];
    $searchText =   $_POST['searchText'];
    $result     =   $objStudentRegistrationModel->getCities($searchText, $stateId);
    $output     =   '<ul class="list-unstyled">';
    
    if(mysqli_num_rows($result)>0)
    {
        while($row=mysqli_fetch_array($result))
        {
            $output .='<li class="citynames">'.$row["cityName"].'<input type="hidden" class="hiddenCityId" name="cityId" value="'.$row["cityId"].'">'.'</li>';
        }
    }
    else
    {
        $output .= '<li>City Not Found</li>';
    }
    $output .= '</ul>';
    echo $output;
}
?>