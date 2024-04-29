<?php
session_start();
include("db_connect.php");


//FUNCTION TO LOGIN USER |||||| Starts >>>> 
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['authUser']) && $_POST['authUser'] == true) {

  $loginID = mysqli_real_escape_string($conn, $_POST['loginID']);
  $password = mysqli_real_escape_string($conn, $_POST['loginPassword']);
  date_default_timezone_set("Africa/Lagos");
  $date = mysqli_real_escape_string($conn, date("j-m-Y, g:i a"));


  //get users Data
  $stmt = $conn->prepare("SELECT * FROM library_users WHERE username=? OR email =?");
  $stmt->bind_param("ss", $loginID, $loginID);
  $stmt->execute() or die($stmt->error);
  $userResult = $stmt->get_result();
  $getUser = $userResult->fetch_array();
  $stmt->close();

  //Get Access Control From Preference
  $stmt = $conn->prepare("SELECT * FROM preference");
  $stmt->execute() or die($stmt->error);
  $preferencesResult = $stmt->get_result();
  $getPreferenceData = $preferencesResult->fetch_array();
  $stmt->close();

  header("Content-Type:application/json");

  if ($getPreferenceData['portalAccess'] != "enable") { //check for portal access control
    $response = array("status" => "error", "message" => "<span> Access Denied <i class='fa fa-exclamation-triangle'></i></span>");
    echo json_encode($response);
  } else {
    if ($userResult->num_rows > 0) {
      if ($getUser['password'] === md5($password)) {

        //prepare user sessions
        $_SESSION['userID'] = $getUser["userID"];
        $_SESSION['userRole'] = $getUser["role"];

        $_SESSION['portalAccess'] = $getPreferenceData["portalAccess"];
        $response = array("status" => "success", "message" => "<i class='fa fa-shield'></i> Credentials Confirmed", "redirectPage" => 'dashboard');
      } else {

        $response = array("status" => "error", "message" => "Invalid Authentication", "redirectPage" => "");
      }
      echo json_encode($response);
    } else { //*** Give feedback as no records found*/
      $response = array("status" => "warning", "message" => " No Record Found. ", "redirectPage" => '');
      echo json_encode($response);
    }
  }

  exit();
}
//FUNCTION TO LOGIN USER |||||| Ends >>>> 

//FUNCTION TO REGISTER NEW USER |||||| Starts >>>> 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regUser']) && !empty($_POST['username'])) {
  $username = $_POST['username'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $password = md5($_POST['password']);
  $address = $_POST['address'];
  ((!empty(isset($_POST["userRole"]))) ? $role = $_POST["userRole"] : $role = 'user');

  // Generate a random user ID
  $characters = '1234567890';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < 10; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  $userID = $randomString;

  date_default_timezone_set("Africa/Lagos");
  $date = date('j-m-Y, g:i a');

  header("Content-Type: application/json");
  // Prepare the SQL statement
  $stmt = $conn->prepare("INSERT INTO library_users (userID, name, email, password, phone, address, username, role, fine, regDate, lastAccess, onlineStatus, lastDeviceIP,passport)VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?,'null','0','null','null')");
  $stmt->bind_param("sssssssss", $userID, $name, $email, $password, $phone, $address, $username, $role, $date);
  $stmt->execute();

  // Check if the insertion was successful
  if ($stmt->affected_rows > 0) {
    $status = "success";
    $message = "Successful Registration";
    $feedbackResponse = true;
    $header = "Registered";
  } else {
    $status = "error";
    $message = "Unable to register user.";
    $feedbackResponse = false;
    $header = "Error";
  }

  $response = array("status" => $status, "message" => $message, "feedbackResponse" => $feedbackResponse, "header" => $header);
  echo json_encode($response);
  // Close the statement
  $stmt->close();

  exit();
}
//FUNCTION TO REGISTER NEW USER |||||| Ends >>>> 

//VERIFY ADMISSION ENTRY EMAIL BEFORE SUBMITTING |||||| Starts >>>>>>>
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userEmailEntryVer'])) {
  if (isset($_POST['email'])) {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM library_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($getDuplicate);
    $stmt->fetch();
    $stmt->close();

    if ($getDuplicate > 0) {
      $response = array("status" => true, "message" => "Email already exists.");
    } else {
      $response = array("status" => false, "message" => "Email is unique.");
    }
  } else {
    $response = array("status" => false, "message" => "Email not provided.");
  }
  header("Content-Type: application/json");
  echo json_encode($response);
  exit();
}
//VERIFY ADMISSION ENTRY EMAIL BEFORE SUBMITTING |||||| Ends >>>>>>>

//VERIFY ADMISSION ENTRY USERNAME BEFORE SUBMITTING |||||| Starts >>>>>>>
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userUsernameEntryVer'])) {
  if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM library_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($getDuplicate);
    $stmt->fetch();
    $stmt->close();

    if ($getDuplicate > 0) {
      $response = array("status" => true, "message" => "Username has been taken.");
    } else {
      $response = array("status" => false, "message" => "Username is unique.");
    }
  } else {
    $response = array("status" => false, "message" => "Username not provided.");
  }
  header("Content-Type: application/json");
  echo json_encode($response);
  exit();
}
//VERIFY ADMISSION ENTRY USERNAME BEFORE SUBMITTING |||||| Ends >>>>>>>