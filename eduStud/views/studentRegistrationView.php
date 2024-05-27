<?php
include "../controllers/studentRegistrationController.php";
require_once "../mpdf/vendor/autoload.php";
$objStudentRegistrationController = new studentRegistrationController();
if(isset($_POST['submit']))
{
    $registrationNumber         =       $_POST['registrationNumber'];
    $firstName                  =       $_POST['firstName'];
    $lastName                   =       $_POST['lastName'];
    $fathersName                =       $_POST['fathersName'];
    $mothersName                =       $_POST['mothersName'];
    $dob                        =       $_POST['dob'];
    $mobile                     =       $_POST['mobile'];
    $address                    =       $_POST['address'];
    $countryId                  =       explode("+", $_POST['country'])[1];
    $stateId                    =       explode("+", $_POST['state'])[1];
    $cityId                     =       $_POST['cityId'];
    $pinCode                    =       $_POST['pinCode'];
    $email                      =       $_POST['email'];
    $gender                     =       $_POST['gender'];

    // Initialize hobby variables
    $reading                    =       isset($_POST['reading']) ? 1 : 0;
    $music                      =       isset($_POST['music']) ? 1 : 0;
    $sports                     =       isset($_POST['sports']) ? 1 : 0;
    $travel                     =       isset($_POST['travel']) ? 1 : 0;

    //Dynamic Row Educational Qualifications
    $examinationArray           =       $_POST['examination'];
    $boardArray                 =       $_POST['board'];
    $percentageArray            =       $_POST['percentage'];
    $yopArray                   =       $_POST['yop'];

    //Image file upload
    $image                      =       $_FILES['profileImage'];
    $imageFileName              =       $image['name'];
    $imageFileError             =       $image['error'];
    $imageFileTmp               =       $image['tmp_name'];

    $duplicationCount = $objStudentRegistrationController->checkRegistrationNumberDuplication($registrationNumber);
    if($duplicationCount == 0)
    {
        if($imageFileError == 0)
        {
            //for creating path of image file to insert into db
            $imageUrl = $objStudentRegistrationController->imageFileUpload($imageFileName);
            $studentId = $objStudentRegistrationController->setStudent($registrationNumber, $imageUrl, $firstName, $lastName, $fathersName, $mothersName, $dob, 
                                                            $mobile, $address, $countryId, $stateId, $cityId, $pinCode, $email, $gender);
            if($studentId)
            {
                echo "Student Data inserted Successfully";
            }
            else
            {
                die("Student Data not inserted!!!!!!");
            }
            //for move the uploaded image file into the specified folder
            $objStudentRegistrationController->moveUploadedImageToFolder($imageFileTmp, $imageUrl);
            $hobbiesResult = $objStudentRegistrationController->setHobbies($studentId, $reading, $music, $sports, $travel);
            if($hobbiesResult)
            {
                echo "Hobbies Data inserted Successfully";
            }
            else
            {
                die("Hobbies Data not inserted!!!!!!");
            }
            for($i=0; $i<count($examinationArray);$i++)
            {
                $examination = $examinationArray[$i];
                $board = $boardArray[$i];
                $percentage = $percentageArray[$i];
                $yop = $yopArray[$i];
                $qualificationsResult = $objStudentRegistrationController->setQualifications($studentId, $examination, $board, $percentage, $yop);
            }
        }
        //header('location:studentRegistrationView.php');
        
        header('Location: studentRegistrationPrint.php?studentId=' . $studentId);
    }
    else
    {
        header('location:studentRegistrationView.php');
        echo '<script>alert("Registration Number Duplication Found!!!");</script>';
    }
    
}

if(isset($_POST['deleteStudent']))
{
    $id = $_POST['studId'];
    $result = $objStudentRegistrationController->deleteStudent($id);
    if($result)
    {
        header('location:studentRegistrationView.php');
    }
    else
    {
        die("Connection failed: " .mysqli_connect_error());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <section class="header">
        <nav class="navbar bg-dark navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand navBrand" href="#">EduRegister</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav" >
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>

    <section class="content">

        <!-- ---------------------------------------------------------------- [REGISTRATION MODAL] ---------------------------------------------------------------- -->
        <div class="modal fade modal-xl" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" >
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="container-fluid p-0 m-0">
                        <div class="row ">
                            <div class="col d-flex justify-content-center align-items-center " >
                                <div class="reg bg-light  px-5 py-3 border border-secondary-subtle rounded">
                                    <!-- Form Started -->
                                    <form action="" method="POST" enctype="multipart/form-data" id="idRegistrationForm"> 
                                        <div class="row g-0 mb-4" >
                                            <div class="col-1 mainHeadingSide"></div>
                                            <div class="col-10">
                                                <div class="text-center position-relative">
                                                    <h2 class="text-uppercase main_heading" style="margin: 0;">Registration Form</h2>
                                                </div>
                                            </div>
                                            <div class="col-1 d-flex align-items-center justify-content-center mainHeadingSide">
                                                <div class="">
                                                    <button class="btn btn btn-outline-danger closeRegModalBtn" type="button" >
                                                        <i class="fa-solid  fa-rectangle-xmark "></i>
                                                    </button>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div></div>
                                        <div>

                                            <!-- Reg no and Image Starts -->
                                            <div class="row form_row ">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="IdRegNo" class="fw-semibold">Registration No</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <!-- <input class="inputFields" type="text" name="registrationNumber" id="IdRegNo" style="width: 100%;"> -->
                                                            <?php
                                                                $result = $objStudentRegistrationController->getNewRegistrationNumber();
                                                                echo $result;
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <img id="imagePreview" src="../images/dummy_prof.png" alt="Image Preview" class="img-fluid me-0 dummyImg" style="width: 100px; height:100px;">
                                                        </div>
                                                        <div class="col-8">
                                                            <div class="row align-items-center" style="height: 100%;">
                                                                <div class="col">
                                                                    <input type="file" name="profileImage" class="inputFields fileUploadBtn" id="idProfileImageFile" style="border: none;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Reg no and Image Ended -->

                                            <!-- First name and Last name starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idFirstName" class="fw-semibold">First Name</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="text" name="firstName" class="inputFields" id="idFirstName" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idLastName" class="fw-semibold">Last Name</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="text" name="lastName" class="inputFields" id="idLastName" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- First name and Last name Ended -->

                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idFathersName" class="fw-semibold">Father's Name</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="text" name="fathersName" class="inputFields" id="idFathersName" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idMothersName" class="fw-semibold">Mother's Name</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="text" name="mothersName" class="inputFields" id="idMothersName" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Dob and mobile starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idDob" class="fw-semibold">Date Of Birth</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="date" name="dob" class="inputFields" id="idDob" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idMobile" class="fw-semibold">Mobile</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="number" name="mobile" class="inputFields" id="idMobile" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Dob and mobile Ends -->

                                            <!-- Address and country starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idAddress" class="fw-semibold">Address</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <textarea name="address" id="idAddress" cols="" rows=""  class=""></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idCountry" class="fw-semibold">Country</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <select name="country"  id="idCountry">
                                                                <option value="">Choose Country</option>
                                                                <!-- Fetch Available Countries From Database and Display as Dropdown -->
                                                                <?php 
                                                                    $result = $objStudentRegistrationController->getCountries();
                                                                    echo $result;
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Address and country Ends -->

                                            <!-- State and city Starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idState" class="fw-semibold">State</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <!-- States options are fetched from db using ajax while onchange country -->
                                                            <select name="state" id="idState">
                                                                <option value="">Choose State</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idCity" class="fw-semibold">City</label>
                                                        </div>
                                                        <div class="col-lg-8 cityNamesReg">
                                                            <input type="text" name="city" class="inputFields" id="idCity" autocomplete="off">
                                                            <!-- In ths div city names are populated -->
                                                            <div  id="cityList">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- State and city Ends -->

                                            <!-- Pincode and Email Starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idPinCode" class="fw-semibold">Pincode</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="number" name="pinCode" class="inputFields" id="idPinCode" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idEmail" class="fw-semibold">Email</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="email" name="email" class="inputFields" id="idEmail" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Pincode and Email Ends -->

                                            <!-- Gender Starts -->
                                            <div class="row form_row align-items-center mt-3" style="height: auto;">
                                                <div class="col">
                                                    <label for="" class="fw-semibold">Gender </label>
                                                </div>
                                                <div class="col">
                                                    <input type="radio" name="gender" id="male" value="male" >
                                                    <label for="male">Male</label>
                                                </div>
                                                <div class="col">
                                                    <input type="radio" name="gender" id="female" value="female">
                                                    <label for="female">Female</label>
                                                </div>
                                                <div class="col">
                                                    <input type="radio" name="gender" id="others" value="others">
                                                    <label for="others">Others</label>
                                                </div>
                                                <div class="col"></div>
                                            </div>
                                            <!-- Gender Ends -->


                                            <div class="row form_row align-items-center mt-3"  style="height: auto;">
                                                <div class="col">
                                                    <label for="" class="fw-semibold">Hobbies</label>
                                                </div>
                                                <div class="col">
                                                    <input type="checkbox" name="reading" value="reading" id="idReading">
                                                    <label for="idReading">Reading</label>
                                                </div>
                                                <div class="col">
                                                    <input type="checkbox" name="music" value="music" id="idMusic">
                                                    <label for="idMusic">Music</label>
                                                </div>
                                                <div class="col">
                                                    <input type="checkbox" name="sports" value="sports" id="idSports">
                                                    <label for="idSports">Sports</label>
                                                </div>
                                                <div class="col">
                                                    <input type="checkbox" name="travel" value="travel" id="idTravel">
                                                    <label for="idTravel">Travel</label>
                                                </div>
                                            </div>

                                            <div class="row form_row mt-3">
                                                <div class="col">
                                                    <label for="" class="fw-semibold">Qualifications</label>
                                                </div>
                                            </div>
                                            <div class="row form_row ">
                                                <div class="col">
                                                    <div style="width: 100%;">
                                                        <table style="width: 100%;" class="table table-secondary table-bordered" id="idQualificationTable">
                                                            <tr>
                                                                <th>Sl.No.&nbsp;&nbsp;&nbsp;</th>
                                                                <th>Examination</th>
                                                                <th>Board</th>
                                                                <th>Percentage</th>
                                                                <th colspan="2">Year of Passing</th>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <p>1</p>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="examination[]" class="tableInput" style="margin-right: 5px;">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="board[]" class="tableInput" style="margin-right: 5px;">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="percentage[]" class="tableInput" style="margin-right: 5px;">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="yop[]" class="tableInput" style="margin-right: 5px;">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-secondary" id="addRowBtnReg">
                                                                        <span>
                                                                            <i class="fa-solid fa-plus"></i>
                                                                        </span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-4"></div>
                                                <div class="col-4 d-flex flex-column align-items-center">
                                                    <div>
                                                        <button type="submit" class="reg_button" name="submit">Register</button>
                                                    </div>
                                                </div>
                                                <div class="col-4"></div>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ---------------------------------------------------------------- [REGISTRATION MODAL END] ---------------------------------------------------------------- -->
    

        <!-- ---------------------------------------------------------------- [UPDATE MODAL] ---------------------------------------------------------------- -->
        <div class="modal fade modal-xl" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true" >
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="container-fluid p-0 m-0">
                        <div class="row ">
                            <div class="col d-flex justify-content-center align-items-center " >
                                <div class="reg bg-light  px-5 py-3 border border-secondary-subtle rounded">
                                    <!-- Form Started -->
                                    <form class="studform" id="updateForm" enctype="multipart/form-data">
                                        <div class="row g-0 mb-4" >
                                            <div class="col-1 mainHeadingSide"></div>
                                            <div class="col-10">
                                                <div class="text-center position-relative">
                                                    <h2 class="text-uppercase main_heading" style="margin: 0;">Update Details</h2>
                                                </div>
                                            </div>
                                            <div class="col-1 d-flex align-items-center justify-content-center mainHeadingSide">
                                                <div class="">
                                                    <button class="btn btn btn-outline-danger closeUpdateModalBtn" type="button">
                                                        <i class="fa-solid  fa-rectangle-xmark "></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div></div>

                                        <div>
                                            <!-- Reg no and Image Starts -->
                                            <div class="row form_row ">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idRegNoUpdate" class="fw-semibold">Registration No</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input class="inputFields" type="text" name="registrationNumber" id="idRegNoUpdate" style="width: 100%;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row">
                                                        <div class="col-4">
                                                            <img id="UpdateimagePreview" src="../images/dummy_prof.png" alt="Image Preview" class="img-fluid me-0 dummyImg" style="width: 100px; height: 100px;">
                                                        </div>
                                                        <div class="col-8">
                                                            <div class="row align-items-center" style="height: 100%;">
                                                                <div class="col">
                                                                    <input type="file" name="nameUpdateProfileImage" id="idProfilePicUpdate" class="inputFields fileUploadBtn" style="border: none;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Reg no and Image Ended -->

                                            <!-- First name and Last name starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idFirstNameUpdate" class="fw-semibold">First Name</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="text" name="firstName" class="inputFields" id="idFirstNameUpdate" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idLastNameUpdate" class="fw-semibold">Last Name</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="text" name="lastName" class="inputFields" id="idLastNameUpdate" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- First name and Last name Ended -->

                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idFathersNameUpdate" class="fw-semibold">Father's Name</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="text" name="fathersName" class="inputFields" id="idFathersNameUpdate" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idMothersNameUpdate" class="fw-semibold">Mother's Name</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="text" name="mothersName" class="inputFields" id="idMothersNameUpdate" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Dob and mobile starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idDobUpdate" class="fw-semibold">Date Of Birth</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="date" name="dob" class="inputFields" id="idDobUpdate" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idMobileUpdate" class="fw-semibold">Mobile</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="number" name="mobile" class="inputFields" id="idMobileUpdate" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Dob and mobile Ends -->

                                            <!-- Address and country starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idAddressUpdate" class="fw-semibold">Address</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <textarea name="address" id="idAddressUpdate" cols="" rows=""  class=""></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idCountryUpdate" class="fw-semibold">Country</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <select name="country" id="idCountryUpdate">
                                                                <!-- Fetch Available Countries From Database and Display as Dropdown -->
                                                                <?php
                                                                    $result = $objStudentRegistrationController->getCountriesUpdate();
                                                                    echo $result;
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Address and country Ends -->

                                            <!-- State and city Starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idStateUpdate" class="fw-semibold">State</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <select name="state" id="idStateUpdate">
                                                                <option value="">Choose State</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idCityUpdate" class="fw-semibold">City</label>
                                                        </div>
                                                        <div class="col-lg-8 city_names_update">
                                                            <input type="text" name="city" class="inputFields" id="idCityUpdate" autocomplete="off">
                                                            <div  id="cityListUpdate"></div>
                                                            <div id="cityIdPopulate"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- State and city Ends -->

                                            <!-- Pincode and Email Starts -->
                                            <div class="row form_row">
                                                <div class="col-lg-6 pe-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idPinCodeUpdate" class="fw-semibold">Pincode</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="number" name="pin" class="inputFields" id="idPinCodeUpdate" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 ps-lg-5">
                                                    <div class="row align-items-center" style="height: 100%;">
                                                        <div class="col-lg-4">
                                                            <label for="idEmailUpdate" class="fw-semibold">Email</label>
                                                        </div>
                                                        <div class="col-lg-8">
                                                            <input type="email" name="email" class="inputFields" id="idEmailUpdate" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Pincode and Email Ends -->

                                            <!-- Gender Starts -->
                                            <div class="row form_row align-items-center mt-3" style="height: auto;">
                                                <div class="col">
                                                    <label for="" class="fw-semibold">Gender </label>
                                                </div>
                                                <div class="col">
                                                    <input type="radio" name="gender" id="idMaleUpdate" value="male">
                                                    <label for="male">Male</label>
                                                </div>
                                                <div class="col">
                                                    <input type="radio" name="gender" id="idFemaleUpdate" value="female">
                                                    <label for="female">Female</label>
                                                </div>
                                                <div class="col">
                                                    <input type="radio" name="gender" id="idOthersUpdate" value="others">
                                                    <label for="others">Others</label>
                                                </div>
                                                <div class="col">

                                                </div>
                                            </div>
                                            <!-- Gender Ends -->

                                            <div class="row form_row align-items-center mt-3"  style="height: auto;">
                                                <div class="col">
                                                    <label for="" class="fw-semibold">Hobbies</label>
                                                </div>
                                                <div class="col">
                                                    <input type="checkbox" name="reading" value="reading" id="idReadingUpdate">
                                                    <label for="idReadingUpdate">Reading</label>
                                                </div>
                                                <div class="col">
                                                    <input type="checkbox" name="music" value="music" id="idMusicUpdate">
                                                    <label for="idMusicUpdate">Music</label>
                                                </div>
                                                <div class="col">
                                                    <input type="checkbox" name="sports" value="sports" id="idSportsUpdate">
                                                    <label for="idSportsUpdate">Sports</label>
                                                </div>
                                                <div class="col">
                                                    <input type="checkbox" name="travel" value="travel" id="idTravelUpdate">
                                                    <label for="idTravelUpdate">Travel</label>
                                                </div>
                                            </div>

                                            <div class="row form_row mt-3">
                                                <div class="col">
                                                    <label for="" class="fw-semibold">Qualifications</label>
                                                </div>
                                            </div>
                                            <div class="row form_row ">
                                                <div class="col">
                                                    <div>
                                                        <table class="table table-secondary table-bordered" id="idQualificationTableUpdate">
                                                            <tr>
                                                                <th>Sl.No.&nbsp;&nbsp;&nbsp;</th>
                                                                <th>Examination</th>
                                                                <th>Board</th>
                                                                <th>Percentage</th>
                                                                <th >Year of Passing</th>
                                                                <th>
                                                                    <button type="button" class="btn btn-sm btn-secondary" id="addRowBtnUpdate">
                                                                        <span>
                                                                            <i class="fa-solid fa-plus"></i>
                                                                        </span>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                            <span id="id_qualifications_content">
                                                                
                                                            </span>
                                                        </table>
                                                        <input type="hidden" name="studentUpdateId" id="hiddendata">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-4"></div>
                                                <div class="col-4 d-flex flex-column align-items-center">
                                                    <div>
                                                        <button type="submit" class="reg_button" name="submit_update" id="submit_update" value="update">Update</button>
                                                    </div>
                                                </div>
                                                <div class="col-4"></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ---------------------------------------------------------------- [UPDATE MODAL END] ---------------------------------------------------------------- -->


        <!-- ---------------------------------------------------------------- [HOME PAGE STUDENT LIST] ---------------------------------------------------------------- -->

        <div class="container-fluid">
            <div class="card mt-3">
                <h5 class="card-header card_header">
                    <div class="row" style="height: 100%;">
                        <div class="col d-flex justify-content-center align-items-center" >
                            <div>
                                STUDENT DETAILS 
                            </div>
                        </div>
                        <div class="col d-flex justify-content-center align-items-center">
                            <div>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Register Now</button>
                            </div>
                        </div>
                    </div>
                    <div class="d-inline-block">

                    </div>
                </h5>
                <div class="card-body">
                    <table class="table table-striped mt-2 display" id="stud_table">
                        <thead>
                            <tr>
                            <th>Regstration No</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Mobile</th>
                            <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $students_list = $objStudentRegistrationController->getStudents();
                                echo $students_list;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ---------------------------------------------------------------- [HOME PAGE STUDENT LIST END] ---------------------------------------------------------------- -->

    </section>
    <style>
        *{
            margin: 0;
            padding: 0;
            font-family: sans-serif;
            font-size: 15px;
        }
        .card_header
        {
            height: 4rem;
            background-color: #333 !important;
            color: #fff !important;
        }

        .modal
        {
            padding: 0 !important;
            margin: 0 !important;
        }
        .modal-content
        {
            padding: 0px !important;
            margin: 0 !important;
        }
        
        body
        {
            background-image: url(../images/bg-registration-form-1.jpg);
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        .reg
        {
            width: 100%;
            height: auto;
        }
        .form_row
        {
            margin-bottom: 1rem;
        }  
        .inputFields, textarea, select
        {
            width: 100%;
            height: auto; 
            /* 2rem */
            margin-bottom:3px !important;
            border: none;
            border-bottom: 2px solid black;
            background-color: #ffffff00;
        }
        .other_section
        {
            margin-bottom:10px !important;
        }
        input:focus, textarea:focus, select:focus
        {
            outline: none;
        }
        textarea
        {
            width: 100%;
        }
        table tr .inputFields
        {
            margin-right: 2px;
            border-bottom: 2px solid black;
        }
        .reg_button 
        {
            border: none;
            width: 150px;
            height: 40px;
            margin: auto;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            background: #333;
            font-size: 15px;
            color: #fff;
        }
        .dummyImg
        {
            border: 2px solid black;
            border-radius: 10px;
        }
        .main_heading
        {
            padding: 10px 0px 10px 0px;
            margin-bottom: 20px;
            color: #fff;
            background: #333;
        }
        .mainHeadingSide
        {
            background: #333;
        }

        .tableInput
        {
            margin-bottom:3px !important;
            border: none;
            border-bottom: 2px solid black;
            background-color: #ffffff00;
        }
        .cityNamesReg, .city_names_update
        {
            position: relative;
        }
        #cityList, #cityListUpdate 
        {
            position: absolute;
            left: 0;
            right: 0;
            margin:0 10px;
            z-index: 1;
            box-sizing: content-box;
            background-color: #fff;
            max-height: 95px;
            overflow-y: scroll;
        }
        .citynames:hover
        {
            cursor: pointer;
            background-color: #e0e0e6;
        }
    </style> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>

        //<-------------------------------------------------------------------------- [REGISTRATION] -------------------------------------------------------------------------->

        //[REGISTRATION] Changing state dropdown options as per the selected country
        $(document).ready(function()
        {
            //Preventing the modal become hide whe clicking outside the edges of modal content(transperant part)
            $('#exampleModal').modal({backdrop: 'static', keyboard: false});
            
            //RegistrationModal exit confirmation
            $('.closeRegModalBtn').click(function() 
            {
                if (confirm('Do You Want To Exit?')) 
                {
                    $('#idRegistrationForm').trigger("reset");
                    $('#cityList').empty().fadeOut();
                    $('#imagePreview').attr('src', '../images/dummy_prof.png');
                    $('#exampleModal').find('input[type!="file"], textarea, select').css('border', '');
                    $('#exampleModal').find('input[type="file"]').css('border', 'none');
                    $('#exampleModal').modal('hide');
                }
            });

            //RegistrationModal ProfileImage preview
            $(document).on('change', '#idProfileImageFile', function(e)
            {
                var image = URL.createObjectURL(e.target.files[0]);
                $('#imagePreview').attr('src', image);
            });

            $(document).on('change', '#idCountry', function(e)
            {
                $('#idCity').val("");//[REGISTRATION] Changing the country dropdown options will remove the city name
                let selectedCountryValue = $('#idCountry').val();
                let selectedCountryValueArray = selectedCountryValue.split("+")
                
                //ajax call
                $.post("../ajax/ajaxGetStates.php",{countryId:selectedCountryValueArray[1]},function(data,status){
                    //Work only if got response data from ajax page
                    $("#idState option:gt(0)").remove();
                    $('#idState').append(data);
                });
            });

            //[REGISTRATION] Changing the state dropdown options will remove the city name
            $(document).on('change', '#idState', function(e)
            {
                $('#idCity').val("");
            });
            
            //reg no duplication check
            $(document).on('keyup', '#IdRegNo', function(e) 
            {
                var typedRegNo = $(this).val();
                var expectedPrefix = 'STUD/'+new Date().getFullYear()+'/';
                
                if (typedRegNo.startsWith(expectedPrefix)) 
                {
                    $.post("../ajax/ajaxCheckRegistrationNumberDuplication.php", {changedRegNo: typedRegNo}, function(data, status) {
                        console.log("output" + data);
                        if (data != 0) 
                        {
                            alert("Registration Number Duplication Found!!!");
                            $('#IdRegNo').val($('#idHiddenRegNo').val());
                        }
                    });
                } 
                else 
                {
                    alert("The registration number should be in STUD/"+new Date().getFullYear()+"/____ format");
                    $(this).val(expectedPrefix);
                }
            });

            //[REGISTRATION] City Auto Complete
            $(document).on('keyup', '#idCity', function(e)
            {
                let selectedStateValue = $('#idState').val();
                let selectedStateValueArray = selectedStateValue.split("+")
                var typedCityValue = $(this). val();

                if(typedCityValue == '')
                {
                    $('#cityList').empty().fadeOut();
                }

                if (typedCityValue != "")
                {
                    $.post("../ajax/ajaxCityAutoComplete.php",{	stateId:selectedStateValueArray[1],searchText:typedCityValue},function(data,status)
                    {
                        $('#cityList').fadeIn();
                        $("#cityList").html(data);
                    });
                }
                $(document).on('click', 'li', function()
                {
                    $('#idCity').val($(this).text());
                    $('#cityList').fadeOut();
                    $('li').not(this).remove();
                });

                // Fade out #cityList when clicking outside of it
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#cityList').length && !$(e.target).is('#idCity')) 
                    {
                        $('#cityList').fadeOut();
                        //$('#idCity').val('');
                    }
                });
            });

            //[REGISTRATION] Dynamic Row
            var slno = $('#idQualificationTable tr').length ;
            $('#addRowBtnReg').click(function()
            {
                console.log("clicked" ,slno);
                var html = '';
                html += '<tr><td><p>'+slno+'</p></td>';
                html += '<td><input type="text" name="examination[]" class="tableInput" style="margin-right: 5px;"></td>';
                html += '<td><input type="text" name="board[]" class="tableInput" style="margin-right: 5px;"></td>';
                html += '<td><input type="text" name="percentage[]" class="tableInput" style="margin-right: 5px;"></td>';
                html += '<td><input type="text" name="yop[]" class="tableInput" style="margin-right: 5px;"></td>';
                html += '<td><button type="button" class="btn btn-sm btn-danger remove" name="remove"><span><i class="fa-solid fa-minus"></i></span></button></td>';
                html += '</tr>';
                $('#idQualificationTable').append(html);
                slno += 1;
            });

            //if we dynamically add a button or element here it is remove button with class name remove. 
            //in this case we will use the following method to access the dynamically added html element.
            $(document).on('click', '.remove', function()
            {
                console.log("remove clicked");
                $(this).closest('tr').remove();

                // Update slno for remaining rows
                $('#idQualificationTable tr').each(function(index) 
                {
                    $(this).find('td:first p').text(index);
                });
                slno -= 1;
            });

            //[REGISTRATION] Form submit validation; Checks for empty/null input values and give Alert
            $(document).on('submit', '#idRegistrationForm', function(e)
            {
                $('#idRegistrationForm:visible').find('input[type!="file"], textarea, select').css('border', '');
                $('#idRegistrationForm:visible').find('input[type="file"]').css('border', 'none');
                let hasEmptyField = false;

                // Check each input in the form
                $('#idRegistrationForm:visible').find('input, textarea, select').each(function() 
                {
                    
                    // If the input is empty
                    if (!$(this).val()) 
                    {
                        // Prevent form submission
                        e.preventDefault();
                        // Optionally, you can show an alert or highlight the empty field
                        alert('Please fill all the fields');
                        $(this).css('border', '1px solid red');
                        hasEmptyField = true;
                        // Exit the loop
                        return false;
                    }
                });

                if (hasEmptyField) 
                {
                    return false;
                }

                if (!$('input[name="gender"]:checked').length) 
                {
                    e.preventDefault();
                    alert('Please select a Gender');
                }
                // $('#idRegistrationForm').trigger("reset"); 

            });

        });

        //<-------------------------------------------------------------------------- [REGISTRATION ENDS] -------------------------------------------------------------------------->



        //<------------------------------------------------------------------------------- [UPDATE] ------------------------------------------------------------------------------->

        //[UPDATE] Fetch the details of a student and populates the details in UpdateForm
        function GetDetails(updateStudentId)
        {
            $("#hiddendata").val(updateStudentId);

            $.post("../ajax/ajaxGetStudentDetails.php",{updateStudentId:updateStudentId},function(data,status)
            {
                //console.log(data);
                var studentRecord = JSON.parse(data);
                studentRecord.forEach(student => 
                {
                    $('#idRegNoUpdate').val(student.registrationNumber);
                    $('#UpdateimagePreview').attr('src', student.imageUrl);
                    $('#idFirstNameUpdate').val(student.firstName);
                    $('#idLastNameUpdate').val(student.lastName);
                    $('#idFathersNameUpdate').val(student.fathersName);
                    $('#idMothersNameUpdate').val(student.mothersName);
                    $('#idDobUpdate').val(student.dob);
                    $('#idMobileUpdate').val(student.mobile);
                    $('#idAddressUpdate').val(student.address);
                    $('#idCountryUpdate').val(student.countryName);

                    $.post("../ajax/ajaxGetStatesUpdate.php",{countryId:student.countryId},function(data,status)
                    {
                        $("#idStateUpdate option:gt(0)").remove();
                        $('#idStateUpdate').append(data);
                        $('#idStateUpdate').val(student.stateName);
                    }); 

                    $('#idCityUpdate').val(student.cityName);

                    //Here we populate the existing id of the city in a hidden input field. it is coming from ajax response.
                    $("#cityIdPopulate").html(student.cityInfoHtml);

                    $('#idPinCodeUpdate').val(student.pinCode);
                    $('#idEmailUpdate').val(student.email);

                    $('#idMaleUpdate').prop('checked', student.gender == 'male' ? true : false);
                    $('#idFemaleUpdate').prop('checked', student.gender == 'female' ? true : false);
                    $('#idOthersUpdate').prop('checked', student.gender == 'others' ? true : false);

                    $('#idReadingUpdate').prop('checked', student.reading == 1 ? true : false);
                    $('#idMusicUpdate').prop('checked', student.music == 1 ? true : false);
                    $('#idSportsUpdate').prop('checked', student.sports == 1 ? true : false);
                    $('#idTravelUpdate').prop('checked', student.travel == 1 ? true : false);

                    var qualificationsArray = student.qualifications;
                    //console.log(qualificationsArray);

                    //Existing qualifications Deynamic Row
                    var slno = 1 ;
                    $("#idQualificationTableUpdate").find("tr:gt(0)").remove();
                    for(i in qualificationsArray)
                    {
                        var html = '';
                        html += '<tr><td><p>'+slno+'</p><input type="hidden" name="qualificationId[]" id="existingQualificationId'+slno+'"></td>';
                        html += '<td><input type="text" name="examination[]" id="idExaminationUpdate'+slno+'" class="tableInput" style="margin-right: 5px;"></td>';
                        html += '<td><input type="text" name="board[]" id="idBoardUpdate'+slno+'" class="tableInput" style="margin-right: 5px;"></td>';
                        html += '<td><input type="text" name="percentage[]" id="idPercentageUpdate'+slno+'" class="tableInput" style="margin-right: 5px;"></td>';
                        html += '<td><input type="text" name="yop[]" id="idYopUpdate'+slno+'" class="tableInput" style="margin-right: 5px;"></td>';
                        html += '<td><button type="button" class="btn btn-sm btn-danger removeRow" name="remove"><span><i class="fa-solid fa-minus"></i></span></button>';
                        html += '<input type="hidden" name="status[]" id="existingQualificationStatus'+slno+'" value=1></td>'
                        html += '</tr>';
                        $('#idQualificationTableUpdate').append(html);
                        $('#existingQualificationId'+slno).val(qualificationsArray[i].qualificationId);
                        $('#idExaminationUpdate'+slno).val(qualificationsArray[i].examination);
                        $('#idBoardUpdate'+slno).val(qualificationsArray[i].board);
                        $('#idPercentageUpdate'+slno).val(qualificationsArray[i].percentage);
                        $('#idYopUpdate'+slno).val(qualificationsArray[i].yop);
                    
                        slno += 1;
                    }
                   
                    //console.log("-------------------------");
                });
              
            });
            $("#updateModal").modal("show");
        }

        
        $(document).ready(function()
        {   
            //Preventing the modal become hide whe clicking outside the edges of modal content(transperant part)
            $('#updateModal').modal({backdrop: 'static', keyboard: false});
            
            //UpdateModal exit confirmation
            $('.closeUpdateModalBtn').click(function() 
            {
                if (confirm('Do You Want To Exit?')) 
                {
                    $('#updateModal').modal('hide');
                    $('#UpdateimagePreview').attr('src', '../images/dummy_prof.png');
                }
            });

            $(document).on('change', '#idProfilePicUpdate', function(e)
            {
                var image = URL.createObjectURL(e.target.files[0]);
                $('#UpdateimagePreview').attr('src', image);
            });

            //[UPDATE] Changing state dropdown options as per the selected country
            $(document).on('change', '#idCountryUpdate', function(e)
            {
                let c_id = $(this).find("option:selected").data("country-id");
                //console.log("country id is: "+ c_id)
                
                $.post("../ajax/ajaxGetStatesUpdate.php",{countryId:c_id},function(data,status)
                {
                    $("#idStateUpdate option:gt(0)").remove();
                    $('#idStateUpdate').append(data);
                    $('#idCityUpdate').val("");
                });
                
            });

            //[UPDATE] Changing the state dropdown options will remove the city name
            $(document).on('change', '#idStateUpdate', function(e)
            {
                $('#idCityUpdate').val("");
            });


            //[UPDATE] City Auto Complete
            $(document).on('keyup', '#idCityUpdate', function(e)
            {
                //removing the hidden city id while populating student details on update modal
                $('#cityIdPopulate').remove();
                let stateId = $('#idStateUpdate option:selected').data('state-id');
                var typedCityValue = $(this). val();

                if(typedCityValue == '')
                {
                    $('#cityListUpdate').empty().fadeOut();
                }

                if (typedCityValue != "")
                {
                    $.post("../ajax/ajaxCityAutoComplete.php",{stateId:stateId, searchText:typedCityValue},function(data,status)
                    {
                        $('#cityListUpdate').fadeIn();
                        $("#cityListUpdate").html(data);
                    });
                }

                $(document).on('click', 'li', function()
                {
                    $('#idCityUpdate').val($(this).text());
                    $('#cityListUpdate').fadeOut();
                    $('li').not(this).remove();
                });

                // Fade out #cityListUpdate when clicking outside of it
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#cityListUpdate').length && !$(e.target).is('#idCityUpdate')) 
                    {
                        $('#cityListUpdate').fadeOut();
                        //$('#idCityUpdate').val('');
                    }
                });
            });


            //[UPDATE] Dynamic Row
            $("#updateModal").on('shown.bs.modal', function()
            {
                // Initialize slno to the number of existing rows 
                var slno = $('#idQualificationTableUpdate tr').length ;
                var count = 1;

                $('#addRowBtnUpdate').click(function()
                {
                    var html = '';
                    html += '<tr class="dynamic-element"><td><p>'+slno+'</p><input type="hidden" name="qualificationId[]" id="newQualificationId'+count+'"></td>';
                    html += '<td><input type="text" name="examination[]" class="tableInput" style="margin-right: 5px;"></td>';
                    html += '<td><input type="text" name="board[]" class="tableInput" style="margin-right: 5px;"></td>';
                    html += '<td><input type="text" name="percentage[]" class="tableInput" style="margin-right: 5px;"></td>';
                    html += '<td><input type="text" name="yop[]" class="tableInput" style="margin-right: 5px;"></td>';
                    html += '<td><button type="button" class="btn btn-sm btn-danger removeRow" name="removeRow"><span><i class="fa-solid fa-minus"></i></span></button>';
                    html += '<input type="hidden" name="status[]" id="newQualificationStatus'+count+'" value=2></td>'
                    html += '</tr>';
                    $('#idQualificationTableUpdate').append(html);
                    $('#newQualificationId'+count).val("new");
                    slno += 1;
                    count += 1;
                });

                $(document).on('click', '.removeRow', function()
                {
                    //If it is an existing qualification then its value is the qualificationId of that existing qualification
                    //If it is a new qualification the its value is 'new'
                    var isExistingOrNew = $(this).closest('tr').find('input[type="hidden"]:eq(0)').val(); //first hidden input in the dynamic row

                    //qualificationStatus variable is not using inside Jquery ith is write just for understanding. 
                    //It is the value of the second hidden input in dynamic row
                    //the status can be --> 1(ExistingQualification) --> 2(CreateNewQualification) -->3(DeleteExistingQualification)
                    var qualificationStatus = $(this).closest('tr').find('input[type="hidden"]:eq(1)').val();//status
                    //console.log(isExistingOrNew, qualificationStatus)

                    if(isExistingOrNew=='new')
                    {
                        $(this).closest('tr').remove();
                    }

                    if(isExistingOrNew != 'new')
                    {
                        var hiddenRow = $(this).closest('tr');
                        hiddenRow.hide();
                        $(this).closest('tr').find('input[type="hidden"]:eq(1)').val(3);

                        //[bugFix] This is to prevent the update form is not submitting because of there is an empty input field in removed (but existing in db) dynamic row
                        hiddenRow.find('input[type="text"]').each(function() 
                        {
                            if ($(this).val() === '') 
                            {
                                $(this).val('dummyData');
                            }
                        });

                    }

                    $('#idQualificationTableUpdate tr:visible').each(function(index) 
                    {
                        $(this).find('td:first p').text(index);
                    });
                    slno -= 1;
                });

            });


            //[UPDATE] Update Form submission ajax call, update with image / without image logic
            $('#updateForm').on('submit', function(e)
            {
                e.preventDefault();
                $('#updateForm:visible').find('input[type!="file"], textarea, select').css('border', '');

                let hasEmptyField = false;
                // Check each input in the form
                $('#updateForm:visible').find('input, textarea, select').each(function() 
                {
                    if ($(this).attr('type') === 'file') {
                        return true; // Continue to the next iteration of the loop
                    }
                    // If the input is empty
                    if (!$(this).val()) 
                    {
                        
                        // Optionally, you can show an alert or highlight the empty field
                        alert('Please fill all the fields');
                        $(this).css('border', '1px solid red');
                        hasEmptyField = true;
                        // Exit the loop
                        return false;
                    }
                });

                if (hasEmptyField) {
                    return false;
                }

                if (!$('input[name="gender"]:checked').length) 
                {
                    //e.preventDefault();
                    alert('Please select a radio option');
                    return false;
                }

                let formData = new FormData(this);
                
                if($('#idProfilePicUpdate')[0].files[0])
                {
                    formData.append('withImage', $('#idProfilePicUpdate')[0].files[0]);
                }
                else
                {
                    formData.append('withoutImage', 'no image is selected');
                }
                let countryId = $('#idCountryUpdate').find("option:selected").data("country-id");
                let stateId = $('#idStateUpdate').find("option:selected").data("state-id");
                //console.log(countryId,stateId)

                //append countryId and stateId in formData
                formData.append('countryId', countryId);
                formData.append('stateId', stateId);
                
                // for (let pair of formData.entries()) {
                //     console.log(pair[0] + ': ' + pair[1]);
                // }
                
                $.ajax({
                    url: '../ajax/ajaxUpdateStudentDetails.php',
                    type: 'POST',
                    data: formData,
                    dataType:"JSON",
                    processData: false,
                    contentType: false,
                    success: function(data) 
                    {
                        console.log("submitted");
                        // This should contain the server's response
                        $("#updateModal").modal("hide");
                        location.reload();
                    }
                });
            });

            $('#updateModal').on('hidden.bs.modal', function () 
            {
                location.reload();
            });

        });
        //<------------------------------------------------------------------------------- [UPDATE ENDS] ------------------------------------------------------------------------------->

        //<------------------------------------------------------------------------------- [HOME PAGE] ------------------------------------------------------------------------------->

        //Delete student confirmation
        $(document).ready(function() 
        {
            $('.deleteStudData').click(function(e) 
            {
                var confirmation = confirm("Are you sure you want to delete this student?");
                if (!confirmation) 
                {
                    e.preventDefault();
                }
            });
        });


    </script>
</body>
</html>
