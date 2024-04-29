<?php

session_start();

include("db_connect.php");

date_default_timezone_set("Africa/Lagos");
$date = date("j-m-Y, g:i a");


if (isset($_SESSION["userID"])) {

    $studentID = $_SESSION["userID"];
    $onlineStatus = '0';
    $serverAddress = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("UPDATE library_users SET `lastAccess`=?, `onlineStatus`=?, `lastDeviceIP`=? WHERE `userID`=?");
    $stmt->bind_param("ssss", $date, $onlineStatus, $serverAddress, $studentID);
    $stmt->execute() or die($stmt->error);
    $stmt->close();

    if ($stmt) {
        $sessions = array('userID', 'userRole');
        foreach ($sessions as $session) {
            if (isset($_SESSION[$session])) {
                unset($_SESSION[$session]);
            }
        }

        //GET PREVIOUS PAGE INTO SESSION
        isset($_SESSION['previousPage']);
        header("location:../");
    }
}
header("location:../");
