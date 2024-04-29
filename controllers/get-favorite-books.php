<?php
session_start();
include("db_connect.php");


//GENERIC FUNCTIONS FOR BOOK RESERVATION |||||| >>>>>>
include("globalFunctions.php");
//GENERIC FUNCTIONS FOR BOOK RESERVATION |||||| >>>>>>

//LOAD FAVORITES TABLE FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showFavorites']) && isset($_POST['query'])) {
  //	echo "Great a table"; die();
  $limit = 20; //mysqli_real_escape_string($conn, $_POST["pageLimit"]);
  $page = 1;

  if (!empty($_POST['page']) && $_POST['page']  >= 1) {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  } else {
    $start = 0;
  }


  $whereSQL = " r.bookID != '' AND r.userID ='" . $_SESSION['userID'] . "' ";


  if ($_POST['query'] != '') { //to work on this for role access later
    $keyword = mysqli_real_escape_string($conn, $_POST['query']);

    $whereSQL = " r.bookID != '' AND (r.bookID LIKE '%" . str_replace(' ', '%', $keyword) . "%'OR b.title LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.category LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.subCategory LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.description LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.author LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.ISBN LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.publisher LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.returnDuration LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.fineAmount LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.publishedDate LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.location LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.status LIKE '%" . str_replace(' ', '%', $keyword) . "%') ";
  }

  // Fetch records based on the query
  $query = "SELECT r.sn AS favoriteBookID, r.*,b.* FROM library_favorite_books r INNER JOIN library_books b ON b.id = r.bookID INNER JOIN library_users u ON u.userID = r.userID WHERE $whereSQL ";
  $query .= 'ORDER BY r.sn ';

  $filter_query = $query . 'LIMIT ' . $start . ', ' . $limit . ' ';

  $statement = mysqli_query($conn, $query) or die(mysqli_error($conn));
  $total_data = mysqli_num_rows($statement);

  $statement = mysqli_query($conn, $filter_query);
  $total_filter_data = mysqli_num_rows($statement);

  $output = ''; // Initialize output to accept HTML content
?>

  <div class="invoices list">
    <?php
    if ($total_data > 0) {
      while ($result = mysqli_fetch_array($statement)) {


        $coverImage = "resources/" . $result['coverImage'];
        $coverImage = ((empty($result['coverImage']) || file_exists("../" . $coverImage)) ? $coverImage  : "../images/no-preview.jpeg");

    ?>

        <div class="invoice" href="javascript:void(0);" data-toggle="modal" data-target="#favoritesEditModal" onClick="loadFavoritesEditModal(this);" data-value="<?php echo $result['favoriteBookID']; ?>">
          <div class="invoice-content" data-status="generated-invoice">
            <div class="invoice-info">
              <div class="col-md-12 col-lg-12 position-relative">
                <img class="cliname img-fluid" alt="<?php echo strtoupper($result['bookID']); ?>" src="<?= $coverImage; ?>" style="width: 90px; height: 100px;">
              </div>
            </div>
            <div class="invoice-info">
              <p class="mb-0 small">Book ID: </p>
              <p class="invoice-no"><?php echo strtoupper($result['bookID']); ?></p>
            </div>
            <div class="invoice-info">
              <p class="mb-0 small">Title: </p>
              <p class="cliname"><?php echo ucfirst($result['title']); ?></p>
            </div>
            <div class="invoice-info">
              <p class="mb-0 small">Book Type: </p>
              <p class="cliname"><?php echo ucfirst($result['subCategory']); ?></p>
            </div>
            <div class="invoice-info">
              <p class="mb-0 small">Published Date: </p>
              <p class="invoice-due-date"><?= ((!empty($result['publishedDate'])) ? date("dS, M Y", strtotime($result['publishedDate'])) : ''); ?></p>
            </div>
            <div class="invoice-status-info">
              <p class="mb-0 small">Status </p>
              <p class="cliname"><?php echo ucfirst($result['status']); ?></p>
            </div>
            <div class="line-h-1 h5">
              <td class="text-center">
                <a href="javascript:void(0);" data-toggle="modal" data-target="#favoritesEditModal" onClick="loadFavoritesEditModal(this);" data-value="<?php echo $result['favoriteBookID']; ?>">
                  <span class="fas fa-bars"></span>
                </a>
              </td>
            </div>
          </div>
        </div>


      <?php    }
    } else { ?>
      <div class="alert alert-danger text-center">
        <h3><i class="fa fa-exclamation-triangle"></i></h3>
        <p class="text-center">No Favorite Book was found!</p>
      </div>
    <?php } ?>
  </div>

  <!-- Modal Section for modify Course Starts -->
  <div class="modal fade bd-example-modal-lg" id="favoritesEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <div class="modal-body" id="displayFavoritesInputs"></div>
      </div>
    </div>
  </div>
  <!-- Modal Section for modify Course Starts -->

  <div class="mt-6 card-body">
    <nav aria-label="...">
      <ul class="pagination rounded-active justify-content-center">
        <?php
        $total_links = ceil($total_data / $limit);
        $previous_link = '';
        $next_link = '';
        $page_link = '';

        if ($total_links > 5) {
          if ($page < 5) {
            for ($count = 1; $count <= 5; $count++) {
              $page_array[] = $count;
            }
            $page_array[] = '...';
            $page_array[] = $total_links;
          } else {
            $end_limit = $total_links - 5;
            if ($page > $end_limit) {
              $page_array[] = 1;
              $page_array[] = '...';
              for ($count = $end_limit; $count <= $total_links; $count++) {
                $page_array[] = $count;
              }
            } else {
              $page_array[] = 1;
              $page_array[] = '...';
              for ($count = $page - 1; $count <= $page + 1; $count++) {
                $page_array[] = $count;
              }
              $page_array[] = '...';
              $page_array[] = $total_links;
            }
          }
        } else {
          for ($count = 1; $count <= $total_links; $count++) {
            $page_array[] = $count;
          }
        }

        if (isset($page_array) && count($page_array) >= 1) { // This (if statement) line might be useful on other projects where pagination has been used
          for ($count = 0; $count < count($page_array); $count++) {
            if ($page == $page_array[$count]) {
              $page_link .= '
                            <li class="page-item active">
                                <a class="page-link favorite-page-link" href="javascript:void(0);" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
                            </li>
                        ';
              $previous_id = $page_array[$count] - 1;
              if ($previous_id > 0) {
                $previous_link = '<li class="page-item"><a class="page-link favorite-page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
              } else {
                $previous_link = '
                            <li class="page-item disabled">
                                <a href="javascript:void(0)" class="page-link">Previous</a>
                            </li>
                            ';
              }
              $next_id = $page_array[$count] + 1;
              if ($next_id > $total_links) {
                $next_link = '
                            <li class="page-item disabled">
                                <a href="javascript:void(0)" class="page-link">Next</a>
                            </li>
                            ';
              } else {
                $next_link = '<li class="page-item"><a class="page-link favorite-page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
              }
            } else {
              if ($page_array[$count] == '...') {
                $page_link .= '
                                <li class="page-link favorite-page-link page-item disabled">
                                    <a class="" href="javascript:void(0);">...</a>
                                </li>
                            ';
              } else {
                $page_link .= '
                                <li class="page-item"><a class="page-link favorite-page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
                            ';
              }
            }
          }
        }
        $output .= $previous_link . $page_link . $next_link;
        $start_result = ($page - 1) * $limit + 1;
        $end_result = min($start_result + $limit - 1, $total_data);

        echo $output; ?>
      </ul>
    </nav>
  </div>

  <div class="text-center mt-2"><?php echo (($end_result > 0) ? $start_result . ' - ' . $end_result . ' of ' . $total_data . ' Results' : 'no records found'); ?></div>
  <?php

  exit();
}
//LOAD FAVORITES TABLE FUNCTION ||||||Ends>>>>>>>

//LOAD FAVORITE EDIT MODAL |||||||| Starts >>>>>>>>>>
if (isset($_POST['getFavoritesEdit']) && !empty($_POST['favoriteID'])) {
  $favoriteID = mysqli_real_escape_string($conn, $_POST['favoriteID']);

  //get favorite information 
  $stmt = $conn->prepare("SELECT f.*,b.* FROM library_favorite_books f INNER JOIN library_books b ON b.id = f.bookID WHERE f.sn=?");
  $stmt->bind_param("s", $favoriteID);
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
  $getBookInfo = $result->fetch_array();

  $coverImage = "resources/" . $getBookInfo['coverImage'];
  $coverImage = ((empty($getBookInfo['coverImage']) || file_exists("../" . $coverImage)) ? $coverImage  : "../images/no-preview.jpeg");


  if (!empty($getBookInfo)) { ?>
    <div class="modal-header">
      <h5 class="modal-title" id="myLargeModalLabel10"><?= ucfirst($getBookInfo['title']); ?></h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i class="icon-close"></i>
      </button>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-12 mt-2">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12 col-lg-5 position-relative" style="height: 42rem;overflow: hidden">
                  <img class="img-fluid" alt="Book Details" src="<?= $coverImage; ?>" style="width: 100%; height: auto;">
                </div>
                <div class="col-md-12 col-lg-7">
                  <div class="card-body border brd-gray border-top-0 border-right-0 border-left-0">
                    <h3 class="mb-0"><a href="#" class="f-weight-500 text-primary"> <?= ((!empty($getBookInfo['title'])) ? $getBookInfo['title'] : 'Book Title'); ?></a></h3>
                  </div>
                  <div class="card-body border border-top-0 border-right-0 border-left-0">
                    <div class="clearfix">
                      <div class="float-left mr-2">
                        <ul class="list-inline mb-0">
                          <li class="list-inline-item"><a href="#" class="text-primary"><i class="icon-star"></i></a></li>
                          <li class="list-inline-item"><a href="#" class="text-primary"><i class="icon-star"></i></a></li>
                          <li class="list-inline-item"><a href="#" class="text-primary"><i class="icon-star"></i></a></li>
                          <li class="list-inline-item"><a href="#"><i class="icon-star"></i></a></li>
                          <li class="list-inline-item"><a href="#"><i class="icon-star"></i></a></li>
                        </ul>
                      </div>
                      <span>(3 reviews)</span>
                    </div>
                  </div>
                  <div class="card-body border brd-gray border-top-0 border-right-0 border-left-0">
                    <div class="row">
                      <div class="col-12">
                        <div class="float-left ml-2">
                          <h4 class="lato-font mb-0 text-danger"> <?= ((!empty($getBookInfo['fineAmount'])) ? "&#x00A3;" . number_format($getBookInfo['fineAmount']) : '&#8358;0'); ?></h4> Dues for late return
                        </div>
                      </div>

                    </div>
                  </div>
                  <div class="card-body border brd-gray border-top-0 border-right-0 border-left-0">
                    <p class="mb-0" lang="ca"> <?= ((!empty($getBookInfo['description'])) ? $getBookInfo['description'] : 'Book Content'); ?></p>
                  </div>
                  <div class="card-body">
                    <ul class="list-unstyled">
                      <li class="font-weight-bold dark-color mb-2">ID: <span class="body-color font-weight-normal"><?= ((!empty($getBookInfo['id'])) ? $getBookInfo['id'] : ''); ?></span></li>
                      <li class="font-weight-bold dark-color mb-2">ISBN: <span class="body-color font-weight-normal"><?= ((!empty($getBookInfo['ISBN'])) ? ucfirst($getBookInfo['ISBN']) : 'N/A'); ?></span></li>
                      <li class="font-weight-bold dark-color mb-2">Author: <span class="body-color font-weight-normal"><?= ((!empty($getBookInfo['author'])) ? ucfirst($getBookInfo['author']) : ''); ?></span></li>
                      <li class="font-weight-bold dark-color mb-2">Publisher: <span class="body-color font-weight-normal"><?= ((!empty($getBookInfo['publisher'])) ? ucfirst($getBookInfo['publisher']) : 'N/A'); ?></span></li>
                      <li class="font-weight-bold dark-color mb-2">Published Date: <span class="body-color font-weight-normal"><?= ((!empty($getBookInfo['publishedDate'])) ? date("dS, M Y", strtotime($getBookInfo['publishedDate'])) : ''); ?></span></li>
                      <li class="font-weight-bold dark-color mb-2">Book Status: <span class="body-color font-weight-normal"> <?= ((!empty($getBookInfo['status'])) ? ucfirst($getBookInfo['status']) : ''); ?></span></li>
                      <li class="font-weight-bold dark-color mb-2">Return Duration: <span class="body-color font-weight-normal"><?= ((!empty($getBookInfo['returnDuration'])) ? $getBookInfo['returnDuration'] : ''); ?> Days</span></li>
                    </ul>
                  </div>
                  <div class="card-body border brd-gray border-top-0 border-right-0 border-left-0">

                    <?php
                    $bookID = $getBookInfo['id'];
                    $userID = $_SESSION['userID'];
                    $bookQuantity = getBookQuantity($conn, $bookID);
                    $reservationCount = getReservationCount($conn, $bookID);

                    // Check if the logged user currently borrowed this book
                    if (userBorrowedBook($conn, $userID, $bookID)) { ?>
                      <div class="d-inline-block mr-3">
                        <button type="button" class="btn btn-danger" disabled>You haven't returned</button>
                      </div>
                    <?php
                      // Check if the number of reservations exceeds the quantity of the book
                    } elseif ($reservationCount >= $bookQuantity) { ?>
                      <div class="d-inline-block mr-3">
                        <button type="button" class="btn btn-danger" disabled>Unavailable</button>
                      </div>
                    <?php
                      // Check if the logged user already reserved this book
                    } elseif (userHasReservation($conn, $userID, $bookID)) { ?>
                      <div class="d-inline-block mr-3">
                        <button type="button" class="btn btn-secondary" onclick="handleRemoveBookReservation(this);" value="<?= $getBookInfo['id']; ?>" id="removeReservationBtn">Remove From Reservation</button>
                      </div>
                    <?php
                      // Make book available for reservation
                    } else { ?>
                      <div class="d-inline-block mr-3">
                        <button type="button" class="btn btn-warning" onclick="handleBookReservation(this);" value="<?= $getBookInfo['id']; ?>" id="reservationBtn">Reserve Book</button>
                      </div>

                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="justify-content-center text-center">
      <button type="button" class="btn btn-danger btn-sm mb-3" id="favoriteDeleteBtn" onClick="handleRemoveFavorite(this);" data-value="<?= $favoriteID; ?>" data-dismiss="modal" aria-label="Close">Remove From Favorite</button>
      <script>
        function handleRemoveFavorite(favorite_id) {
          //console.log('welcome');
          var favoriteID = $(favorite_id).data("value");
          console.log(favoriteID);
          swal({
              title: "Are you sure to remove from favorite?",
              text: "You are about removing <?= strtoupper($getBookInfo['title']); ?> from your favorite list.",
              icon: 'question',
              type: "warning",
              showCancelButton: true,
              confirmButtonClass: 'btn-success',
              cancelButtonClass: 'btn-danger',
              confirmButtonText: 'Yes, Update!',
              cancelButtonText: 'Cancel!',
              closeOnConfirm: false,
              //closeOnCancel: false
            },
            function() {
              $.ajax({
                type: 'POST',
                url: 'controllers/get-favorite-books',
                async: true,
                // processData: false,
                // contentType: false,
                // mimeType: 'multipart/form-data',
                // cache: false,
                data: {
                  deleteFavoriteContent: 1,
                  favoriteID: favoriteID
                },
                beforeSend: function() { // Corrected from beforeSubmit to beforeSend
                  $("#favoriteDeleteBtn").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
                },
                success: function(response) {
                  var currentFavoritePage = $('.favorite-page-link').data('page_number');
                  loadFavorites(currentFavoritePage);
                  var status = response.status;
                  var message = response.message;
                  var responseStatus = response.responseStatus;
                  var header = response.header;

                  if (status === true) {
                    $("#deleteFavoriteMsg").html(response).css("color", "green").show();
                    swal(header, message, responseStatus);
                    //loadDepartments(); //Reload registered courses table
                  } else {
                    swal(header, message, responseStatus);
                  }
                },
                error: function() {
                  $("#deleteFavoriteMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
                  swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                },
                complete: function() { // Moved the timeout code to the complete callback
                  setTimeout(function() {
                    $("#deleteFavoriteMsg").fadeOut(300);
                  }, 3000);
                  $("#favoriteDeleteBtn").html("Delete Favorite").show(); // Reset the button text
                }
              });
            });
        }
      </script>
    </div>
    <div class="modal-footer">
      <center style="margin: 0px auto;">
        <span id="deleteFavoriteMsg"></span>
      </center>
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
<?php
  }

  exit();
}
//LOAD FAVORITE EDIT MODAL |||||||| Ends >>>>>>>>>>

//DELETE FAVORITE FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['deleteFavoriteContent'], $_POST['favoriteID'])) {

  $favoriteID = mysqli_real_escape_string($conn, $_POST['favoriteID']);

  // Updated Registered favorite
  $queryUpdateFavorite = mysqli_query($conn, "DELETE FROM library_favorite_books WHERE sn='" . $favoriteID . "'") or die(mysqli_error($conn));

  if ($queryUpdateFavorite) {
    $status = true;
    $header = 'Successful!';
    $message = 'Favorite Deleted Successfully';
    $responseStatus = 'success';
  } else {
    $status = false;
    $header = 'Failed!';
    $message = 'An error occurred, try again';
    $responseStatus = 'error';
  }

  $response = array('status' => $status, 'message' => $message, 'responseStatus' => $responseStatus, 'header' => $header);

  header('Content-Type: application/json');
  echo json_encode($response);

  exit();
}
//DELETE FAVORITE FUNCTION |||||||Ends>>>>>>>>>>
?>