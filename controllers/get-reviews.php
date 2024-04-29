<?php
session_start();
include("db_connect.php");

//LOAD REVIEWS TABLE FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showReviews']) && isset($_POST['query'])) {
  //	echo "Great a table"; die();
  $limit = 20; //mysqli_real_escape_string($conn, $_POST["pageLimit"]);
  $page = 1;

  if (!empty($_POST['page']) && $_POST['page']  >= 1) {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  } else {
    $start = 0;
  }


  $whereSQL = " r.bookID != '' ";


  if ($_POST['query'] != '') { //to work on this for role access later
    $keyword = mysqli_real_escape_string($conn, $_POST['query']);

    $whereSQL = " r.bookID != '' AND (r.bookID LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR r.reviews LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR r.rating LIKE '%" . str_replace(' ', '%', $keyword) . "%') ";
  }

  // Fetch records based on the query
  $query = "SELECT r.*,b.* FROM library_books_review r INNER JOIN library_books b ON b.id = r.bookID LEFT JOIN library_users u ON u.userID = r.userID   WHERE $whereSQL ";
  $query .= 'ORDER BY r.sn ';

  $filter_query = $query . 'LIMIT ' . $start . ', ' . $limit . ' ';

  $statement = mysqli_query($conn, $query) or die(mysqli_error($conn));
  $total_data = mysqli_num_rows($statement);

  $statement = mysqli_query($conn, $filter_query);
  $total_filter_data = mysqli_num_rows($statement);

  $output = ''; // Initialize output to accept HTML content
?>

  <div class="table-responsive">
    <table id="" class="display table dataTable table-striped table-bordered">
      <thead>
        <tr>
          <th class="text-center">Book ID</th>
          <th class="text-center">Title</th>
          <th class="text-center">Review</th>
          <th class="text-center">Rating</th>
          <th class="text-center">Options</th>
        </tr>
      </thead>
      <tbody id="">
        <?php

        if ($total_data > 0) {
          while ($result = mysqli_fetch_array($statement)) {
        ?>

            <tr>
              <td class="text-center"><?php echo strtoupper($result['bookID']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['title']); ?></td>
              <td><?php echo ucfirst($result['reviews']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['rating']); ?></td>
              <td class="text-center">
                <a href="javascript:void(0);" data-toggle="modal" data-target="#reviewsEditModal" onClick="loadReviewsEditModal(this);" data-value="<?php echo $result['sn']; ?>">
                  <span class="fas fa-bars"></span>
                </a>
              </td>
            </tr>

          <?php    }
        } else { ?>
          <tr class="table-danger">
            <td colspan="8" class="text-center">No Review was found!</td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <th class="text-center">Book ID</th>
          <th class="text-center">Title</th>
          <th class="text-center">Review</th>
          <th class="text-center">Rating</th>
          <th class="text-center">Options</th>
        </tr>
      </tfoot>
    </table>
    <!-- Modal Section for modify Course Starts -->
    <div class="modal fade bd-example-modal-lg" id="reviewsEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">

          <div class="modal-body" id="displayReviewsInputs"></div>
        </div>
      </div>
    </div>
    <!-- Modal Section for modify Course Starts -->
  </div>

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
                                <a class="page-link review-page-link" href="javascript:void(0);" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
                            </li>
                        ';
              $previous_id = $page_array[$count] - 1;
              if ($previous_id > 0) {
                $previous_link = '<li class="page-item"><a class="page-link review-page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
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
                $next_link = '<li class="page-item"><a class="page-link review-page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
              }
            } else {
              if ($page_array[$count] == '...') {
                $page_link .= '
                                <li class="page-link review-page-link page-item disabled">
                                    <a class="" href="javascript:void(0);">...</a>
                                </li>
                            ';
              } else {
                $page_link .= '
                                <li class="page-item"><a class="page-link review-page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
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
//LOAD REVIEWS TABLE FUNCTION ||||||Ends>>>>>>>

//LOAD REVIEW EDIT MODAL |||||||| Starts >>>>>>>>>>
if (isset($_POST['getReviewsEdit']) && !empty($_POST['reviewID'])) {
  $reviewID = mysqli_real_escape_string($conn, $_POST['reviewID']);

  //get review information 
  $queryReview = mysqli_query($conn, "SELECT * FROM library_books_review WHERE sn='$reviewID'") or die(mysqli_error($conn));
  $getReviewData = mysqli_fetch_array($queryReview);

  if (!empty($getReviewData)) { ?>
    <div class="modal-header">
      <h5 class="modal-title" id="myLargeModalLabel10">Review Content</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
      <blockquote class="blockquote my-4 p-5 bg-info position-relative text-white rounded">
        <p class="font-weight-bold"><?= $getReviewData['reviews']; ?></p>
      </blockquote>
    </div>
    <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
      <div class="justify-content-center text-center">
        <button type="button" class="btn btn-danger btn-sm mb-3" id="reviewDeleteBtn" onClick="handleDeleteReview(this);" data-value="<?= $reviewID; ?>" data-dismiss="modal" aria-label="Close">Delete Review</button>
        <script>
          function handleDeleteReview(review_id) {
            //console.log('welcome');
            var reviewID = $(review_id).data("value");
            console.log(reviewID);
            swal({
                title: "Are you sure to delete review?",
                text: "Deleting this review removes it across the portal.",
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
                  url: 'controllers/get-reviews',
                  async: true,
                  // processData: false,
                  // contentType: false,
                  // mimeType: 'multipart/form-data',
                  // cache: false,
                  data: {
                    deleteReviewContent: 1,
                    reviewID: reviewID
                  },
                  beforeSend: function() { // Corrected from beforeSubmit to beforeSend
                    $("#reviewDeleteBtn").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
                  },
                  success: function(response) {
                    var currentReviewPage = $('.review-page-link').data('page_number');
                    loadReviews(currentReviewPage);
                    var status = response.status;
                    var message = response.message;
                    var responseStatus = response.responseStatus;
                    var header = response.header;

                    if (status === true) {
                      $("#deleteReviewMsg").html(response).css("color", "green").show();
                      swal(header, message, responseStatus);
                      //loadDepartments(); //Reload registered courses table
                    } else {
                      swal(header, message, responseStatus);
                    }
                  },
                  error: function() {
                    $("#deleteReviewMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
                    swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                  },
                  complete: function() { // Moved the timeout code to the complete callback
                    setTimeout(function() {
                      $("#deleteReviewMsg").fadeOut(300);
                    }, 3000);
                    $("#reviewDeleteBtn").html("Delete Review").show(); // Reset the button text
                  }
                });
              });
          }
        </script>
      </div>
    <?php } ?>
    <div class="modal-footer">
      <center style="margin: 0px auto;">
        <span id="deleteReviewMsg"></span>
      </center>
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
<?php
  }

  exit();
}
//LOAD REVIEW EDIT MODAL |||||||| Ends >>>>>>>>>>

//DELETE REVIEW FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['deleteReviewContent'], $_POST['reviewID'])) {

  $reviewID = mysqli_real_escape_string($conn, $_POST['reviewID']);

  // Updated Registered review
  $queryUpdateReview = mysqli_query($conn, "DELETE FROM library_books_review WHERE sn='" . $reviewID . "'") or die(mysqli_error($conn));

  if ($queryUpdateReview) {
    $status = true;
    $header = 'Successful!';
    $message = 'Review Deleted Successfully';
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
//DELETE REVIEW FUNCTION |||||||Ends>>>>>>>>>>
?>