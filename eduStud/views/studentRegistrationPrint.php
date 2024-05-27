<?php
include "../controllers/studentRegistrationController.php";
require_once "../mpdf/vendor/autoload.php";
$objStudentRegistrationController = new studentRegistrationController();
$html ='';
if(isset($_GET['studentId']))
{
    $studentId = $_GET['studentId'];
    $printContent= $objStudentRegistrationController->printRegistrationForm($studentId);
    //echo $printContent;
    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8','format' => 'A4','orientation' => 'P', 'default_font' =>'Myriad Pro','default_font_size' =>10 ]);
    $mpdf->SetTitle('STUDENT REGISTRATION FORM');
    $mpdf->SetSubject('STUDENT REGISTRATION FORM');
    $mpdf->WriteHTML($printContent);
    $mpdf->Output();
    exit; // Ensure that no further output is sent
}
?>