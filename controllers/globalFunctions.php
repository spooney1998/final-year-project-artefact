<?php
// ::::::::::::::::::::::BOOK RESERVATION FUNCTIONS:::::::::::::::::::::::::::::

// Function to get the quantity of a book from the library_books table
function getBookQuantity($conn, $bookID)
{
  $stmt = $conn->prepare("SELECT quantity FROM library_books WHERE id = ?");
  $stmt->bind_param("s", $bookID);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $stmt->close();
  return $row['quantity'];
}

// Function to count the number of reservations for a book from the library_reservations table
function getReservationCount($conn, $bookID)
{
  $stmt = $conn->prepare("SELECT COUNT(*) AS reservation_count FROM library_reservations WHERE bookID = ?");
  $stmt->bind_param("s", $bookID);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $stmt->close();
  return $row['reservation_count'];
}

// Function to check if the user already has a reservation for a book
function userHasReservation($conn, $userID, $bookID)
{
  $stmt = $conn->prepare("SELECT COUNT(*) AS reservation_count FROM library_reservations WHERE userID = ? AND bookID = ?");
  $stmt->bind_param("ss", $userID, $bookID);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $stmt->close();
  return $row['reservation_count'] > 0;
}

// Function to check if the user currently borrowed the book
function userBorrowedBook($conn, $userID, $bookID)
{
  $stmt = $conn->prepare("SELECT COUNT(*) AS borrowed_count FROM library_borrowings WHERE userID = ? AND bookID = ? AND returnDate =''");
  $stmt->bind_param("ss", $userID, $bookID);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $stmt->close();
  return $row['borrowed_count'] > 0;
}

// Function to check if the user has borrowed book in past show book as read
function userHasReadBook($conn, $userID, $bookID)
{
  $stmt = $conn->prepare("SELECT COUNT(*) AS borrowed_count FROM library_borrowings WHERE userID = ? AND bookID = ? AND returnDate !=''");
  $stmt->bind_param("ss", $userID, $bookID);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $stmt->close();
  return $row['borrowed_count'] > 0;
}

// Function to insert a reservation into the library_reservations table
function insertReservation($conn, $userID, $bookID)
{

  // Generate a random Book ID
  $characters = '1234567890';
  $charactersLength = strlen($characters);
  $randomString = "";
  for ($i = 0; $i < 8; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  $reservationID = $randomString;

  $stmt = $conn->prepare("INSERT INTO library_reservations (reservationID, userID, bookID, reservedDate) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("sss", $reservationID, $userID, $bookID);
  $success = $stmt->execute();
  $stmt->close();
  return $success;
}

// Function to insert a new borrowed book into the library_borrowings table
function issueBookForBorrow($conn, $userID, $bookID)
{
  date_default_timezone_set("Africa/Lagos");
  $borrowedDate = date("j-m-Y, g:i a");
  $default = '';

  // Get return duration from library_books table
  $stmt = $conn->prepare("SELECT returnDuration FROM library_books WHERE id = ?");
  $stmt->bind_param("s", $bookID);
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
  $getBookDetails = $result->fetch_array();
  $returnDuration = $getBookDetails['returnDuration'];
  $stmt->close();

  // Calculate due date based on return duration
  $dueDate = date('j-m-Y, g:i a', strtotime("+$returnDuration days"));

  // Generate a random Issue ID
  $characters = '1234567890';
  $charactersLength = strlen($characters);
  $randomString = "";
  for ($i = 0; $i < 8; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  $issueID = $randomString;

  // Insert borrowed book into library_borrowings table
  $stmt = $conn->prepare("INSERT INTO library_borrowings(`issueID`, `userID`, `bookID`, `borrowedDate`, `dueDate`, `returnDate`) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $issueID, $userID, $bookID, $borrowedDate, $dueDate, $default);
  $success = $stmt->execute();
  $stmt->close();

  // Remove Book from reservations library_reservations table
  $stmt = $conn->prepare("DELETE FROM library_reservations WHERE userID=? AND bookID=?");
  $stmt->bind_param("ss", $userID, $bookID);
  $success = $stmt->execute();
  $stmt->close();

  return $success;
}

// Function to update book as returned book on library_borrowings table
function acceptReturnBook($conn, $userID, $bookID)
{
  date_default_timezone_set("Africa/Lagos");
  $returnDate = date("j-m-Y, g:i a");
  $default = '';

  // Insert borrowed book into library_borrowings table
  $stmt = $conn->prepare("UPDATE library_borrowings SET `returnDate` =? WHERE userID =? AND bookID =? AND returnDate=?");
  $stmt->bind_param("ssss", $returnDate, $userID, $bookID, $default);
  $success = $stmt->execute();
  $stmt->close();

  return $success;
}

// Function to remove a reservation from the library_reservations table
function removeReservation($conn, $userID, $bookID)
{
  $stmt = $conn->prepare("DELETE FROM library_reservations WHERE userID=? AND bookID=?");
  $stmt->bind_param("ss", $userID, $bookID);
  $success = $stmt->execute();
  $stmt->close();
  return $success;
}
// ::::::::::::::::::::::BOOK RESERVATION FUNCTIONS:::::::::::::::::::::::::::::
