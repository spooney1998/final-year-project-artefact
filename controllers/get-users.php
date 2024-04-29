<?php
session_start();
include("db_connect.php");

//GENERIC FUNCTIONS FOR BOOK RESERVATION |||||| >>>>>>
include("globalFunctions.php");
//GENERIC FUNCTIONS FOR BOOK RESERVATION |||||| >>>>>>

//LOAD USERS TABLE FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showUsers']) && isset($_POST['query'])) {
  //	echo "Great a table"; die();
  $limit = 20; //mysqli_real_escape_string($conn, $_POST["pageLimit"]);
  $page = 1;

  if (!empty($_POST['page']) && $_POST['page']  >= 1) {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  } else {
    $start = 0;
  }

  //$role = mysqli_real_escape_string($conn, $_POST["userRole"]);
  $whereSQL = " userID != '' ";


  if ($_POST['query'] != '') { //to work on this for role access later
    $keyword = mysqli_real_escape_string($conn, $_POST['query']);

    $whereSQL = " userID != '' AND (userID LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR name LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR email LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR phone LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR address LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR username LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR onlineStatus LIKE '%" . str_replace(' ', '%', $keyword) . "%'  OR role LIKE '%" . str_replace(' ', '%', $keyword) . "%') ";
  }

  // Fetch records based on the query
  $query = "SELECT * FROM library_users WHERE $whereSQL ";
  $query .= ' ORDER BY username ';

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
          <th></th>
          <th class="text-center">User ID</th>
          <th class="text-center">Username</th>
          <th class="text-center">Full Name</th>
          <th class="text-center">Email </th>
          <th class="text-center">Phone</th>
          <th class="text-center">Role</th>
          <th class="text-center">Late Return Fine</th>
          <th class="text-center">Options</th>
        </tr>
      </thead>
      <tbody id="">
        <?php

        if ($total_data > 0) {
          $sn = 0;
          while ($result = mysqli_fetch_array($statement)) {


            $userPassport = "resources/" . $result['passport'];
            $userPassport = ((empty($result['passport']) || file_exists("../" . $userPassport)) ? $userPassport  : "../images/no-preview.jpeg");
        ?>

            <tr data-passport="<?= $userPassport ?>">
              <td class="text-center">
                <img src="<?= $userPassport; ?>" alt="<?= $result['username']; ?>" class="img-fluid ml-0 mt-2  rounded-circle" style="width:40px; height:40px" />
              </td>
              <td class="text-center"><?php echo strtoupper($result['userID']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['username']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['name']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['email']); ?></td>
              <td class="text-center"><?php echo $result['phone']; ?></td>
              <td class="text-center"><?php echo ucfirst($result['role']); ?></td>
              <td class="text-center">&#x00A3;<?php echo number_format($result['fine']); ?></td>
              <td class="text-center">
                <a href="javascript:void(0);" data-toggle="modal" data-target="#usersEditModal" onClick="loadUsersEditModal(this);" data-value="<?php echo $result['userID']; ?>">
                  <span class="fas fa-bars"></span>
                </a>
              </td>
            </tr>

          <?php    }
        } else { ?>
          <tr class="table-danger">
            <td colspan="8" class="text-center">No User was found!</td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <th></th>
          <th class="text-center">User ID</th>
          <th class="text-center">Username</th>
          <th class="text-center">Full Name</th>
          <th class="text-center">Email </th>
          <th class="text-center">Phone</th>
          <th class="text-center">Role</th>
          <th class="text-center">Late Return Fine</th>
          <th class="text-center">Options</th>
        </tr>
      </tfoot>
    </table>
    <!-- Modal Section for modify Course Starts -->
    <div class="modal fade bd-example-modal-xl" id="usersEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <center> <img id="modalUserPassport" src="" alt="" class="img-fluid ml-0 mt-2 rounded-circle" style="width:100px; height:100px;" /></center>
          <div class="modal-body" id="displayUsersInputs"></div>
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
                                <a class="page-link user-page-link" href="javascript:void(0);" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
                            </li>
                        ';
              $previous_id = $page_array[$count] - 1;
              if ($previous_id > 0) {
                $previous_link = '<li class="page-item"><a class="page-link user-page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
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
                $next_link = '<li class="page-item"><a class="page-link user-page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
              }
            } else {
              if ($page_array[$count] == '...') {
                $page_link .= '
                                <li class="page-link user-page-link page-item disabled">
                                    <a class="" href="javascript:void(0);">...</a>
                                </li>
                            ';
              } else {
                $page_link .= '
                                <li class="page-item"><a class="page-link user-page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
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

  <div class="text-center mt-2"><?php echo  $start_result . ' - ' . $end_result . ' of ' . $total_data . ' Results'; ?></div>
  <?php

  exit();
}
//LOAD USERS TABLE FUNCTION ||||||Ends>>>>>>>

//LOAD USER EDIT MODAL |||||||| Starts >>>>>>>>>>
if (isset($_POST['getUsersEdit']) && !empty($_POST['userID'])) {
  $userID = mysqli_real_escape_string($conn, $_POST['userID']);

  //get user information 
  $queryUser = mysqli_query($conn, "SELECT * FROM library_users WHERE userID='$userID'") or die(mysqli_error($conn));
  $getUserData = mysqli_fetch_array($queryUser);

  if (!empty($getUserData)) { ?>
    <div class="modal-header">
      <h5 class="modal-title" id="myLargeModalLabel10">Manage <?php echo ucfirst($getUserData['username']); ?> Profile</h5>
      <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
    </div>

    <!-- Tabs Nav Links -->
    <div class="profile-menu mt-4 theme-background border  z-index-1 p-2">
      <div class="d-sm-flex">
        <div class="align-self-center">
          <ul class="nav nav-pills flex-column flex-sm-row" id="bookContent" role="tablist">
            <li class="nav-item ml-0">
              <a class="nav-link  py-2 px-4 px-lg-4 active" data-toggle="tab" href="#userReservations"><i class="fas fa-user-tag"></i> User Reservations</a>
            </li>
            <li class="nav-item ml-0">
              <a class="nav-link  py-2 px-4 px-lg-4" data-toggle="tab" href="#userBorrows" onclick="loadUserBorrowedBooks(1);"><i class="fas fa-book"></i> User Borrowed Books</a>
            </li>
            <li class="nav-item ml-0">
              <a class="nav-link  py-2 px-4 px-lg-4" data-toggle="tab" href="#userReads" onclick="loadUserPastReads(1);"><i class="fas fa-user-clock"></i> User Past Reads</a>
            </li>
            <li class="nav-item ml-0">
              <a class="nav-link  py-2 px-4 px-lg-4" data-toggle="tab" href="#modifyUser"><i class="fas fa-user-edit"></i> Modify User</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- Tabs Nav Links -->

    <div class="tab-content">
      <!-- User Reservations -->
      <div class="col-12 mt-2 tab-pane fade in active" id="userReservations">

        <div class="invoices list">
          <?php
          $stmt = $conn->prepare("SELECT r.bookID AS reservedBookID, r.*,b.* FROM library_reservations r LEFT JOIN library_books b ON b.id = r.bookID  WHERE r.userID =?");
          $stmt->bind_param("s", $getUserData['userID']);
          $stmt->execute() or die($stmt->error);
          $result = $stmt->get_result();
          if ($result->num_rows > 0) {
            while ($getUserReservations = $result->fetch_array()) {
              $coverImage = "resources/" . $getUserReservations['coverImage'];
              $coverImage = ((empty($getUserReservations['coverImage']) || file_exists("../" . $coverImage)) ? $coverImage  : "../images/no-preview.jpeg");
              $bookID = $getUserReservations['reservedBookID'];

          ?>
              <div class="invoice" href="javascript:void(0);" onDbClick="<?= userBorrowedBook($conn, $userID, $bookID) ? 'javascript : void(0);' : 'handleIssueBook(this);'; ?>" data-value="<?php echo $bookID; ?>">
                <div class="invoice-content" data-status="generated-invoice">
                  <div class="invoice-info">
                    <div class="col-md-12 col-lg-12 position-relative">
                      <img class="cliname img-fluid" title="<?php echo strtoupper($getUserReservations['title']); ?>" src="<?= $coverImage; ?>" style="width: 80px; height: 90px;">
                    </div>
                  </div>
                  <div class="invoice-info">
                    <p class="mb-0 small">Title: </p>
                    <p class="cliname"><?php echo ucfirst($getUserReservations['title']); ?></p>
                  </div>
                  <div class="invoice-info">
                    <p class="mb-0 small">Book Type: </p>
                    <p class="cliname"><?php echo ucfirst($getUserReservations['subCategory']); ?></p>
                  </div>
                  <div class="invoice-info">
                    <p class="mb-0 small">Reservation Date: </p>
                    <p class="cliname"><?php echo date("D. d M Y, h:i a", strtotime($getUserReservations['reservedDate'])); ?></p>
                  </div>
                  <div class="invoice-info ">
                    <td class="mb-0 small text-center">
                      <?php if (userBorrowedBook($conn, $userID, $bookID)) { ?>
                        <div class="inline-flex">
                          <a class="btn btn-danger btn-sm disabled" href="javascript:void(0);">
                            Borrowed
                          </a>
                        </div>
                      <?php } else { ?>
                        <a class="btn btn-info btn-sm" href="javascript:void(0);" onClick="handleIssueBook(this);" data-value="<?php echo $bookID; ?>" data-btn-value="<?php echo $bookID; ?>">
                          Issue Book
                        </a>
                      <?php } ?>
                    </td>
                  </div>
                </div>
              </div>
            <?php
            }
            $stmt->close();
          } else {
            ?>
            <div class="alert alert-danger text-center">
              <h3 class="text-center">
                <i class="fas fa-user-alt-slash"></i>
              </h3>
              <p class="tex-center">User has no reservation at the moment!</p>
            </div>
          <?php } ?>
        </div>
      </div>

      <!-- User Borrowed Books -->
      <div class="col-12 mt-2 tab-pane fade" id="userBorrows">

        <!-- Display user past reads -->
        <div class="card-body">
          <input type="search" id="userBorrowedBookSearchEntry" class="form-control mt-4" placeholder="Search For User Past Reads" />
          <div class="card-body" id="displayBorrowedBooks"> </div>
        </div>
      </div>

      <!-- User Past Reads -->
      <div class="col-12 mt-2 tab-pane fade" id="userReads">

        <!-- Display user past reads -->
        <div class="card-body">
          <input type="search" id="userPastReadSearchEntry" class="form-control mt-4" placeholder="Search For User Past Reads" />
          <div class="card-body" id="displayPastReads"> </div>
        </div>
      </div>

      <!-- Modify User -->
      <div class="col-12 mt-2 tab-pane fade" id="modifyUser">
        <form id="userEditForm">
          <!--User Inputs-->
          <div class="col-12 mt-3">
            <div class="card">
              <div class="card-content">
                <div class="card-body py-5">
                  <div class="row">

                    <div class="form-group col-sm-6">
                      <div class="input-group">
                        <label class="col-12" for="userRole">User Role<span class="text-danger">*</span>
                          <select class="form-control" id="userRole" name="userRole" required="">
                            <option value="" disabled>Select User Role</option>
                            <option value="user" <?php echo (($getUserData['role'] == "user") ? 'selected' : ''); ?>>User</option>
                            <option value="librarian" <?php echo (($getUserData['role'] == "librarian") ? 'selected' : ''); ?>>Librarian</option>
                          </select>
                          <input type="hidden" name="userID" id="userID" value="<?= $getUserData['userID']; ?>" />
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-sm-6">
                      <div class="input-group">
                        <label class="col-12" for="name">Full Name<span class="text-danger">*</span>
                          <input class="form-control" type="text" name="name" id="name" placeholder="Full Name" required="" value="<?= $getUserData['name']; ?>" />
                          <div class="invalid-feedback">Full name is required </div>
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-sm-6">
                      <div class="input-group">
                        <label class="col-12" for="username">User Name<span class="text-danger">*</span>
                          <input type="text" class="form-control" id="username" name="username" placeholder="Username (No spaces)" required="" value="<?= $getUserData['username']; ?>" maxlength="20" />
                          <div class="invalid-feedback" id="username-feedback" style="display:none"> Username must contain only alphanumeric characters without space.</div>
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-sm-6">
                      <div class="input-group">
                        <label class="col-12" for="email">Email Address<span class="text-danger">*</span>
                          <input type="email" class="form-control" name="email" id="email" placeholder="E-mail Address" required="" value="<?= $getUserData['email']; ?>" maxlength="100" readonly />
                          <div class="invalid-feedback" id="email-feedback">Valid E-mail is required </div>
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-sm-6">
                      <div class="input-group">
                        <label class="col-12" for="phone">Phone Number<span class="text-danger">*</span>
                          <input type="text" class="form-control" name="phone" placeholder="Phone Number" required="" value="<?= $getUserData['phone']; ?>" maxlength="13" />
                          <div class="invalid-feedback"> Phone Number is required </div>
                        </label>
                      </div>
                    </div>

                    <div class="form-group col-sm-6">
                      <div class="input-group">
                        <label class="col-12" for="address">Resident Address<span class="text-danger">*</span>
                          <textarea class="form-control" name="address" placeholder="Enter Resident Address" required=""><?= $getUserData['address']; ?></textarea>
                          <div class="invalid-feedback">Resident Address is required</div>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--User Inputs-->

          <div class="modal-footer">
            <center style="margin: 0px auto;">
              <span id="modifyUserMsg"></span>
            </center>
            <button type="submit" class="btn btn-warning" id="updateUserBtn">Update User</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>

    <script>
      // Function to modify user information
      $("#userEditForm").submit(function(e) {
        e.preventDefault();
        //console.log('welcome');
        var userForm = new FormData($("#userEditForm")[0]);
        userForm.append("modifyUserInfo", true);
        swal({
            title: "Are you sure to update User?",
            text: "Updating this user is effective across the portal.",
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
              url: 'controllers/get-users',
              async: true,
              processData: false,
              contentType: false,
              // mimeType: 'multipart/form-data',
              // cache: false,
              data: userForm,
              beforeSend: function() { // Corrected from beforeSubmit to beforeSend
                $("#updateUserBtn").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
              },
              success: function(response) {

                var status = response.status;
                var message = response.message;
                var responseStatus = response.responseStatus;
                var header = response.header;

                if (status === true) {
                  $("#modifyUserMsg").html(response).css("color", "green").show();
                  swal(header, message, responseStatus);
                  //loadUsers(); //Reload registered courses table
                } else {
                  swal(header, message, responseStatus);
                }
              },
              error: function() {
                $("#modifyUserMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
              },
              complete: function() { // Moved the timeout code to the complete callback
                setTimeout(function() {
                  $("#modifyUserMsg").fadeOut(300);
                }, 3000);
                $("#updateUserBtn").html("Update Course").show(); // Reset the button text
              }
            });
          });
      });
      // Function to modify user information

      //LOAD USERS PAST READS FUNCTION>>>>>>STARTS
      function loadUserPastReads(page, query = '') {
        var userID = "<?= $userID; ?>";
        //var sortStatus = $("#sortStatus").val();
        //var pastReadPageLimit = $("#paymentPageLimit").val();

        $.ajax({
          url: "controllers/get-history",
          method: "POST",
          async: true,
          data: {
            showUserPastReads: 1,
            userID: userID,
            query: query,
            page: page
          },
          beforeSend: function() {
            $("#displayPastReads").html("").show();
            showUsersSkeletonLoader();
          },
          success: function(data) {
            setTimeout(function() {
              $("#displayPastReads").html(data).show();

              $('.skeleton-loader').remove(); // Remove skeleton loader
            }, 1000);
          },
          error: function(data) {
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
            setTimeout(function() {
              loadUserPastReads(page);
            }, 5000);
          }
        });
      };

      $(document).on('click', '.userPastRead-page-link', function() {
        var page = $(this).data('page_number');
        var query = $('#userPastReadSearchEntry').val();
        loadUserPastReads(page, query);
      });

      $('#userPastReadSearchEntry').on('keyup change paste', function() {
        var query = $('#userPastReadSearchEntry').val();
        loadUserPastReads(1, query);

      });

      $("#userPastReadPageLimit").on("change", function() { //page limit
        loadUserPastReads(1);
      });

      // Skeleton Loader
      function showUsersSkeletonLoader() {
        var skeletonHtml = `
					<div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
					</div>
				`;

        for (var i = 0; i < 1; i++) {
          $('#displayPastReads').append(skeletonHtml);

          $('#displayBorrowedBooks').append(skeletonHtml);
        }
      }
      //LOAD USERS PAST READS FUNCTION>>>>>>ENDS

      //LOAD USERS BORROWED BOOKS FUNCTION>>>>>>STARTS
      function loadUserBorrowedBooks(page, query = '') {
        var userID = "<?= $userID; ?>";
        //var sortStatus = $("#sortStatus").val();
        //var pastReadPageLimit = $("#paymentPageLimit").val();

        $.ajax({
          url: "controllers/get-history",
          method: "POST",
          async: true,
          data: {
            showUserBorrowedBooks: 1,
            userID: userID,
            query: query,
            page: page
          },
          beforeSend: function() {
            $("#displayBorrowedBooks").html("").show();
            showUsersSkeletonLoader();
          },
          success: function(data) {
            setTimeout(function() {
              $("#displayBorrowedBooks").html(data).show();

              $('.skeleton-loader').remove(); // Remove skeleton loader
            }, 1000);
          },
          error: function(data) {
            swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
            setTimeout(function() {
              loadUserBorrowedBooks(page);
            }, 5000);
          }
        });
      };

      $(document).on('click', '.userBorrowedBook-page-link', function() {
        var page = $(this).data('page_number');
        var query = $('#userBorrowedBookSearchEntry').val();
        loadBorrowedBooks(page, query);
      });

      $('#userBorrowedBookSearchEntry').on('keyup change paste', function() {
        var query = $('#userBorrowedBookSearchEntry').val();
        loadUserBorrowedBooks(1, query);
      });

      $("#userBorrowedBookPageLimit").on("change", function() { //page limit
        loadUserBorrowedBooks(1);
      });

      //LOAD USERS BORROWED BOOKS FUNCTION>>>>>>ENDS

      //FUNCTION TO HANDLE BOOK ISSUING FOR BORROWING >>>>>>Starts
      function handleIssueBook(reservedBook) {
        var reservedBookID = $(reservedBook).data('value');
        var userID = "<?= $userID; ?>";
        var btnValue = $(reservedBook).data('btn-value');

        console.log(reservedBookID);
        swal({
            title: "Are you sure to issue Book?",
            text: "You are about issuing this book to selected user.",
            icon: "question",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            cancelButtonClass: "btn-danger",
            confirmButtonText: "Continue!",
            cancelButtonText: "Cancel!",
            closeOnConfirm: false,
            //closeOnCancel: false
          },
          function() {
            $.ajax({
              url: "controllers/get-books",
              type: "POST",
              async: true,
              data: {
                issueBookForBorrow: true,
                bookID: reservedBookID,
                userID: userID
              },
              beforeSend: function(newBookRequestResponse) {
                $(reservedBook).html("<span><i class='fa fa-spin fa-spinner'></i> Please wait... </span>").show();
                $(reservedBook).prop("disabled", true);
              },
              success: function(newBookRequestResponse) {
                $(reservedBook).html("issued").show();
                $(reservedBook).removeClass("btn-info");
                $(reservedBook).addClass("btn-danger");
                var status = newBookRequestResponse.status;
                var message = newBookRequestResponse.message;
                var header = newBookRequestResponse.header;
                var responseStatus = newBookRequestResponse.responseStatus;

                $(reservedBook).prop("disabled", true);

                var currentUserPastReadPage = $('.userPastRead-page-link').data('page_number');
                loadUserPastReads(currentUserPastReadPage); //load current book page

                // console.log(newBookRequestResponse);
                swal(header, message, responseStatus);

              },
              error: function(newBookRequestResponse) {
                $(reservedBook).html("Issue Book").show();
                $(reservedBook).prop("disabled", false);
                swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
              },
            });
          });
      }
      //FUNCTION TO HANDLE BOOK ISSUING FOR BORROWING >>>>>>Ends
    </script>
<?php
  }

  exit();
}
//LOAD USER EDIT MODAL |||||||| Ends >>>>>>>>>>

//UPDATE USER FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['userID'], $_POST['username'], $_POST['modifyUserInfo'])) {

  // date_default_timezone_set("Africa/Lagos"); 
  // $date = date("j-m-Y, g:i a");
  $userRole = mysqli_real_escape_string($conn, $_POST['userRole']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $userID = mysqli_real_escape_string($conn, $_POST['userID']);


  // Check for duplicate
  $stmt = $conn->prepare("SELECT * FROM `library_users` WHERE `role` = ? AND `userID` = ? AND `name` = ? AND `username` = ? AND `email` = ? AND `phone` = ? AND `address` = ?");
  $stmt->bind_param('sssssss', $userRole, $userID, $name, $username, $email, $phone, $address);
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
  $getDuplicateInfo = $result->fetch_array();


  // Check if password is set, otherwise use the current password
  if (isset($_POST['password']) && !empty($_POST['password'])) {
    $password = md5($_POST['password']);
  } else {
    $password = ((!empty($getDuplicateInfo['password'])) ? $getDuplicateInfo['password'] : $_POST['currentPassword']);
  }

  $imageUploaded = isset($_FILES['userImage']) && !empty($_FILES['userImage']['tmp_name']);
  $dataChanged = $stmt->num_rows < 1;
  $stmt->close();

  if ($imageUploaded && !$dataChanged) {
    // Update user profile image
    $sqlUser = mysqli_query($conn, "SELECT * FROM library_users WHERE userID='" . $userID . "'") or die(mysqli_error($conn));
    $getUser = mysqli_fetch_array($sqlUser);

    ((file_exists("../resources/" . $getUser["passport"])) ? unlink("../resources/" . $getUser["passport"]) : '');

    $valid_extensions = array('jpeg', 'jpg', 'png', 'gif');
    $path = 'passport/';
    $directory = '../resources/passport/';
    $img = $_FILES['userImage']['name'];
    $tmp = $_FILES['userImage']['tmp_name'];
    $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
    $final_image = rand(1000, 1000000) . $img;

    if (in_array($ext, $valid_extensions)) {
      $path = $path . strtolower($final_image);
      $directory = $directory . strtolower($final_image);

      if (move_uploaded_file($tmp, $directory)) {
        $stmt = mysqli_prepare($conn, "UPDATE library_users SET passport=? WHERE userID=?");
        $stmt->bind_param('ss', $path, $userID);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
          $response = array('status' => true, 'header' => 'Successful!', 'message' => 'User Image Updated Successfully', 'responseStatus' => 'success');
        } else {
          $response = array('status' => false, 'header' => 'No Changes!', 'message' => 'There is nothing to update', 'responseStatus' => 'warning');
        }
        $stmt->close();
      }
    }
  } elseif ($dataChanged && !$imageUploaded) {
    // Update user information
    $stmt = $conn->prepare("UPDATE library_users SET `role` = ?, `name` = ?, `username` = ?, `phone` = ?, `address` = ?, `password` = ? WHERE userID = ?");
    $stmt->bind_param('sssssss', $userRole, $name, $username, $phone, $address, $password, $userID);
    $stmt->execute() or die($stmt->error);

    if ($stmt->affected_rows > 0) {
      $response = array('status' => true, 'header' => 'Successful!', 'message' => 'User Information Updated Successfully', 'responseStatus' => 'success');
    } else {
      $response = array('status' => false, 'header' => 'No Changes!', 'message' => 'There is nothing to update', 'responseStatus' => 'warning');
    }

    $stmt->close();
  } elseif ($dataChanged && $imageUploaded) {
    // Both image and data changed
    $sqlUser = mysqli_query($conn, "SELECT * FROM library_users WHERE userID='" . $userID . "'") or die(mysqli_error($conn));
    $getUser = mysqli_fetch_array($sqlUser);

    if (file_exists("../resources/" . $getUser["passport"])) {
      unlink("../resources/" . $getUser["passport"]);
    }

    $valid_extensions = array('jpeg', 'jpg', 'png', 'gif');
    $path = 'passport/';
    $directory = '../resources/passport/';
    $img = $_FILES['userImage']['name'];
    $tmp = $_FILES['userImage']['tmp_name'];
    $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
    $final_image = rand(1000, 1000000) . $img;

    if (in_array($ext, $valid_extensions)) {
      $path = $path . strtolower($final_image);
      $directory = $directory . strtolower($final_image);

      if (move_uploaded_file($tmp, $directory)) {
        $stmt = $conn->prepare("UPDATE library_users SET `role` = ?, `name` = ?, `username` = ?, `phone` = ?, `address` = ?, `password` = ?, `passport`=? WHERE userID = ?");
        $stmt->bind_param('ssssssss', $userRole, $name, $username, $phone, $address, $password, $path, $userID);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
          $response = array('status' => true, 'header' => 'Successful!', 'message' => 'User Image Updated Successfully', 'responseStatus' => 'success');
        } else {
          $response = array('status' => false, 'header' => 'No Changes!', 'message' => 'There is nothing to update', 'responseStatus' => 'warning');
        }
        $stmt->close();
      }
    }
  } else {
    $response = array('status' => false, 'header' => 'No Changes!', 'message' => 'There is nothing to update', 'responseStatus' => 'warning');
  }

  header('Content-Type: application/json');
  echo json_encode($response);

  exit();
}
//UPDATE USER FUNCTION |||||||Ends>>>>>>>>>>
?>