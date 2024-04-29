<?php
session_start();
include("db_connect.php");

//GENERIC FUNCTIONS FOR BOOK RESERVATION |||||| >>>>>>
include("globalFunctions.php");
//GENERIC FUNCTIONS FOR BOOK RESERVATION |||||| >>>>>>

//LOAD BOOKS TABLE FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showBooks']) && isset($_POST['query'])) {

  //	echo "Great a table"; die();
  $limit =  mysqli_real_escape_string($conn, $_POST["booksPageLimit"]);
  $page = 1;

  if (!empty($_POST['page']) && $_POST['page']  >= 1) {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  } else {
    $start = 0;
  }

  $whereSQL = " b.id !='' ";

  if (!empty($_POST['query'])) {
    $keyword = mysqli_real_escape_string($conn, $_POST['query']);

    $whereSQL .= " AND (b.id LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.title LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.category LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.subCategory LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.description LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.author LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.ISBN LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.publisher LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.returnDuration LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.fineAmount LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.publishedDate LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.location LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR b.status LIKE '%" . str_replace(' ', '%', $keyword) . "%') ";
  }


  if (!empty($_POST['bookType'])) {
    $bookType = mysqli_real_escape_string($conn, $_POST['bookType']);
    $whereSQL .= " AND  (b.subCategory = '" . $bookType . "' OR b.category = '" . $bookType . "') ";
  }

  if (!empty($_POST['bookStatus']) && $_POST['bookStatus'] == "borrowed") {
    ($_SESSION['userRole']  == "admin" || $_SESSION['userRole'] == "librarian") ? $query = "SELECT b.*, br.* FROM `library_borrowings` br INNER JOIN library_books b ON b.id = br.bookID WHERE returnDate ='' AND $whereSQL " : $query = "SELECT b.*, br.* FROM `library_borrowings` br INNER JOIN library_books b ON b.id = br.bookID WHERE br.userID ='" . $_SESSION['userID'] . "' AND returnDate ='' AND $whereSQL ";
  } elseif (!empty($_POST['bookStatus']) && $_POST['bookStatus'] == "reserved") {
    ($_SESSION['userRole']  == "admin" || $_SESSION['userRole'] == "librarian") ? $query = "SELECT b.*, r.* FROM `library_reservations` r INNER JOIN library_books b ON b.id = r.bookID WHERE $whereSQL " :
      $query = "SELECT b.*, r.* FROM `library_reservations` r INNER JOIN library_books b ON b.id = r.bookID WHERE r.userID ='" . $_SESSION['userID'] . "' AND $whereSQL ";
  } elseif (!empty($_POST['bookStatus']) && $_POST['bookStatus'] == "available") {
    ($_SESSION['userRole']  == "admin" || $_SESSION['userRole'] == "librarian") ? $query = "SELECT b.* FROM `library_books` b WHERE b.status ='available' AND b.quantity > 0 AND $whereSQL " :  $query = "SELECT * FROM `library_books` b WHERE $whereSQL ";
  } else {
    $query = "SELECT * FROM `library_books` b WHERE $whereSQL ";
  }

  // Fetch records based on the query
  $query .= 'ORDER BY b.addedDate ASC ';

  $filter_query = $query . 'LIMIT ' . $start . ', ' . $limit . ' ';

  $statement = mysqli_query($conn, $query) or die(mysqli_error($conn));
  $total_data = mysqli_num_rows($statement);

  $statement = mysqli_query($conn, $filter_query);
  $total_filter_data = mysqli_num_rows($statement);

  $output = ''; // Initialize output to accept HTML content

?>
  <div class="row">
    <?php

    if ($total_data > 0) {
      while ($result = mysqli_fetch_array($statement)) {

        if ($_SESSION['userRole']  == "admin" || $_SESSION['userRole'] == "librarian") {
          if ($result['status'] == 'available') {
            $cardType = 'business-note';
          } elseif ($result['status'] == 'borrowed') {
            $cardType = 'private-note';
          } elseif ($result['status'] == 'reserved') {
            $cardType =  'social-note';
          } elseif ($result['status'] == 'read') {
            $cardType =  'work-note';
          } else {
            $cardType = 'business-note';
          }
        } else { //if you are logged in a regular user
          $bookID = $result['id'];
          $userID = $_SESSION['userID'];
          // Check if the number of reservations exceeds the quantity of the book
          $bookQuantity = getBookQuantity($conn, $bookID);
          $reservationCount = getReservationCount($conn, $bookID);
          if ($reservationCount >= $bookQuantity) {
            $cardType = 'private-note';
            // Check if the logged user already reserved this book
          } elseif (userHasReservation($conn, $userID, $bookID)) {
            $cardType =  'social-note';
            // Check if the logged user currently borrowed this book
          } elseif (userBorrowedBook($conn, $userID, $bookID)) {
            $cardType = 'private-note';
            // Check if the logged user has borrowed this book in past
          } elseif (userHasReadBook($conn, $userID, $bookID)) {
            $cardType =  'work-note';
          } else {
            $cardType = 'business-note';
          }
        }
        // $cardType = $cardType[array_rand($cardType)]; // Randomly select a card type

        $coverImage = "resources/" . $result['coverImage'];
        $coverImage = ((empty($result['coverImage']) || file_exists("../" . $coverImage)) ? $coverImage  : "../images/no-preview.jpeg");
    ?>
        <!--Books item-->
        <div class="col-12 col-md-6 col-lg-3 my-3 note <?= $cardType; ?> all starred " style="cursor: pointer;" data-toggle="modal" data-target="#bookContent" data-type="<?= $cardType; ?>" data-value="<?= $result['id']; ?>" onClick="loadBookContentsModal(this);">
          <div class="position-relative" style="height: 20rem; overflow: hidden;">
            <img src="<?= $coverImage; ?>" style="width: 100%; height: auto;" alt="" class="img-fluid">
            <div class="caption-bg fade bg-transparent text-right">
              <div class="d-table w-100 h-100 ">
                <div class="d-table-cell align-bottom">
                  <div class="mb-3">
                    <a href="javascript:void(0);" class=" rounded-left bg-white px-3 py-2 shadow2"><i class="icon-heart"></i></a>
                  </div>
                  <div class="mb-4">
                    <a data-fancybox-group="gallery" href="<?= $coverImage; ?>" class=" fancybox rounded-left bg-white px-3 py-2 shadow2"><i class="fas fa-search"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card" style="cursor: pointer;" data-toggle="modal" data-target="#bookContent" data-type="<?= $cardType; ?>" data-value="<?= $result['id']; ?>" onClick="loadBookContentsModal(this);">
            <div class="card-content">
              <div class="card-body p-4" style="height:12rem">
                <p class="mb-2 clamp-1"><a href="javascript:void(0);" class="font-weight-bold text-primary"><?= ucfirst($result['title']); ?>
                    (<?= ucfirst($result['category']); ?>)</a></p>
                <!-- <div class="d-inline-block text-danger pl-2">$285.00</div> -->
                <p class="font-w-500 tx-s-12"><i class="icon-calendar"></i> <span class="note-date"> June 14th, 2020 (Next
                    Availability)</span></p>
                <div class="note-content mb-4">
                  <p class="clamp-2"><?= $result['description']; ?></p>
                </div>
                <div class="d-flex notes-tool">
                  <ul class="list-inline mb-0 mt-2">
                    <li class="list-inline-item"><a href="#" class="text-primary"><i class="icon-star"></i></a></li>
                    <li class="list-inline-item"><a href="#" class="text-primary"><i class="icon-star"></i></a></li>
                    <li class="list-inline-item"><a href="#" class="text-primary"><i class="icon-star"></i></a></li>
                    <li class="list-inline-item"><a href="#"><i class="icon-star"></i></a></li>
                    <li class="list-inline-item"><a href="#"><i class="icon-star"></i></a></li>
                  </ul>
                  <div class="ml-auto">
                    <span class="dot"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
          /*==============================================================
               Gallery Feature for Zoom 
           ============================================================= */
          $('.fancybox').fancybox();

          $(".filter-button").click(function() {
            var value = $(this).attr('data-group');

            if (value == "all") {
              //$('.filter').removeClass('hidden');
              $('.item').show('1000');
            } else {

              $(".item").not('.' + value).hide('3000');
              $('.item').filter('.' + value).show('3000');

            }
            $(".filter-button").removeClass('active');
            $(this).addClass("active");
          });
        </script>
      <?php    }
    } else { ?>
      <div class="col-12 alert alert-danger mt-5 ml-4 text-center" style="width:100% !important" style="margin: 0 auto">
        <h3><i class="fas fa-exclamation-triangle"></i></h3>
        <p>There are no available Books at the moment</p>
      </div>
    <?php  } ?>
  </div>
  <div>&nbsp;</div>
  <div class="mt-3 card-body" style="margin: 0 auto">
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
                                <a class="page-link book-page-link" href="javascript:void(0);" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
                            </li>
                        ';
              $previous_id = $page_array[$count] - 1;
              if ($previous_id > 0) {
                $previous_link = '<li class="page-item"><a class="page-link book-page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">Previous</a></li>';
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
                $next_link = '<li class="page-item"><a class="page-link book-page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">Next</a></li>';
              }
            } else {
              if ($page_array[$count] == '...') {
                $page_link .= '
                                <li class="page-link book-page-link page-item disabled">
                                    <a class="" href="javascript:void(0);">...</a>
                                </li>
                            ';
              } else {
                $page_link .= '
                                <li class="page-item"><a class="page-link book-page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
                            ';
              }
            }
          }
        }

        $output .= $previous_link . $page_link . $next_link;
        $start_result = ($page - 1) * $limit + 1;
        $end_result = min($start_result + $limit - 1, $total_data);

        echo $output;
        ?>
      </ul>
    </nav>
    <div class="text-center mt-2">
      <?php echo (($end_result > 0) ? $start_result . ' - ' . $end_result . ' of ' . $total_data . ' Results' : 'No records found'); ?>
    </div>
  </div>
<?php

  exit();
}
//LOAD BOOKS TABLE FUNCTION ||||||Ends>>>>>>>

//LOAD BOOK CONTENT MODAL FUNCTION ||||||Starts>>>>>>>
if (isset($_POST['getBookContents'], $_POST['bookID'])) {

  $userID = $_SESSION['userID']; //Needed for the functions
  $bookID = mysqli_real_escape_string($conn, $_POST['bookID']);
  $stmt = $conn->prepare("SELECT * FROM library_books WHERE id=?");
  $stmt->bind_param("s", $bookID);
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
  $getBookInfo = $result->fetch_array();

  $coverImage = "resources/" . $getBookInfo['coverImage'];
  $coverImage = ((!empty($getBookInfo['coverImage']) && file_exists("../" . $coverImage)) ? $coverImage  : "../images/no-preview.jpeg");

?>
  <div class="modal-header">
    <h5 class="modal-title">
      <i class="fas fa-book-open"></i> <?= ((!empty($getBookInfo['title'])) ? $getBookInfo['title'] : 'Book Content'); ?>
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <i class="icon-close"></i>
    </button>
  </div>
  <div class="modal-body">

    <!-- Tabs Nav Links -->
    <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
      <div class="profile-menu mt-4 theme-background border  z-index-1 p-2">
        <div class="d-sm-flex">
          <div class="align-self-center">
            <ul class="nav nav-pills flex-column flex-sm-row" id="bookContent" role="tablist">
              <li class="nav-item ml-0">
                <a class="nav-link  py-2 px-4 px-lg-4 active" data-toggle="tab" href="#bookDetails"><i class="fas fa-book-medical"></i> Book Details</a>
              </li>

              <li class="nav-item ml-0">
                <a class="nav-link  py-2 px-4 px-lg-4" data-toggle="tab" href="#modifyBook"><i class="fas fa-edit"></i> Modify Book</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    <?php } ?>
    <!-- Tabs Nav Links -->

    <div class="tab-content">
      <div class="col-12 mt-2 tab-pane fade in active" id="bookDetails">
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
                        <h4 class="lato-font mb-0 text-danger"> &#x00A3;<?= ((!empty($getBookInfo['fineAmount'])) ? number_format($getBookInfo['fineAmount']) : '0'); ?></h4> Dues for late return
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
      <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
        <div class="col-12 mt-2 tab-pane fade" id="modifyBook">
          <form id="modifyBookForm" class="needs-validation" novalidate>
            <div class="row">
              <div class="col-12 mt-2">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12 col-lg-4 position-relative" style="height: 37rem;">
                        <img class="img-fluid" alt="<?= $getBookInfo['title']; ?>" src="<?= $coverImage; ?>" id="modifyBookCoverImagePreview" style="width: 100%; height: auto;">
                        <div class="text-center z-index-1" style="margin:0px auto !important;">
                          <label for="modifyBookCoverImage" class="file-upload btn btn-warning btn-sm px-4 rounded-pill shadow mt-3"><i class="fa fa-upload mr-2"></i>Change Cover Image<input id="modifyBookCoverImage" onchange="previewBookModifyImage(event);" name="bookCoverImage" type="file" required>
                          </label>
                        </div>
                      </div>
                      <div class="col-md-12 col-lg-8">
                        <div class="row">
                          <!-- Book Type Select -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="modifyBookType">Book Type<span class="text-danger">*</span>
                                <?php

                                // Prepare the SQL statement
                                $sql = "SELECT `CategoryID`, `CategoryName`, `Type` FROM `library_book_category`";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $stmt->bind_result($categoryId, $categoryName, $type);

                                // Initialize variables to store category groups
                                $groups = array();

                                // Fetch categories from the database
                                while ($stmt->fetch()) {
                                  // Check if the type already exists as a group
                                  if (!isset($groups[$categoryName])) {
                                    // If not, create a new group
                                    $groups[$categoryName] = array();
                                  }
                                  // Add the category to the appropriate group
                                  $groups[$categoryName][] = array('id' => $categoryId, 'type' => $type);
                                }

                                // Close the statement
                                $stmt->close();
                                ?>
                                <select class="form-control" id="modifyBookType" name="newBookType" required="">
                                  <option label="Choose one thing" readonly value="">Select type of book</option>
                                  <?php
                                  // Loop through the groups and generate option groups and options
                                  foreach ($groups as $categoryName => $categories) {
                                    echo '<optgroup label="' . $categoryName . '"  >';
                                    foreach ($categories as $category) {
                                      echo '<option value="' . $category['type'] . '" ' . (($category['type'] == $getBookInfo['subCategory']) ? "selected" : "") . ' >' . $category['type'] . '</option>';
                                    }
                                    echo '</optgroup>';
                                  }
                                  ?>
                                </select>
                              </label>
                            </div>
                          </div>

                          <!-- Book Title Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="modifyBookTitle">Book Title<span class="text-danger">*</span>
                                <input class="form-control" type="text" name="newBookTitle" id="modifyBookTitle" placeholder="Book Title" required="" maxlength="100" value="<?= $getBookInfo['title']; ?>" />
                                <div class="invalid-feedback">Book title is required </div>
                              </label>
                              <input type="hidden" name="bookID" value="<?= $getBookInfo['id']; ?>" />
                            </div>
                          </div>

                          <!-- Book Author Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="modifyBookAuthor">Author<span class="text-danger">*</span>
                                <input type="text" class="form-control" id="modifyBookAuthor" name="newBookAuthor" placeholder="Book Author" required="" maxlength="50" value="<?= $getBookInfo['author']; ?>" />
                                <div class="invalid-feedback"> Book Author is required</div>
                              </label>
                            </div>
                          </div>

                          <!-- Boon ISBN Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="newBookISBN">ISBN<span class="text-danger">*</span>
                                <input type="text" class="form-control" name="newBookISBN" id="newBookISBN" placeholder="Enter ISBN" required="" maxlength="100" value="<?= $getBookInfo['ISBN']; ?>" />
                                <div class="invalid-feedback">ISBN is required </div>
                              </label>
                            </div>
                          </div>

                          <!-- Book Publisher Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="newBookPublisher">Publisher<span class="text-danger">*</span>
                                <input type="text" class="form-control" id="newBookPublisher" name="newBookPublisher" placeholder="Book Publisher" required="" maxlength="50" value="<?= $getBookInfo['publisher']; ?>" />
                                <div class="invalid-feedback"> Book Publisher is required</div>
                              </label>
                            </div>
                          </div>

                          <!-- Book Return Duration Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="newBookReturnDuration"> Return Duration (Days)<span class="text-danger">*</span>
                                <input type="number" class="form-control" id="newBookReturnDuration" name="newBookReturnDuration" placeholder="Book Return Duration in days" required="" maxlength="3" max="60" min="1" value="<?= $getBookInfo['returnDuration']; ?>" />
                                <div class="invalid-feedback"> Book Return Duration is required</div>
                              </label>
                            </div>
                          </div>

                          <!-- Book lateReturnFine Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="newBookLateReturnFine">Late Return Fine(£)<span class="text-danger">*</span>
                                <input type="number" class="form-control" id="newBookLateReturnFine" name="newBookLateReturnFine" placeholder="Book Late Return Fine(£)" required="" maxlength="50" min="0" value="<?= $getBookInfo['fineAmount']; ?>" />
                                <div class="invalid-feedback"> Book late Return Fine is required</div>
                              </label>
                            </div>
                          </div>

                          <!-- Book Published Date Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="newBookPublishedDate">Published Date<span class="text-danger">*</span>
                                <input type="date" class="form-control" id="newBookPublishedDate" name="newBookPublishedDate" placeholder="Book Published Date" required="" maxlength="50" value="<?= date('Y-m-d', strtotime($getBookInfo['publishedDate'])); ?>" />
                                <div class="invalid-feedback"> Book Published Date is required</div>
                              </label>
                            </div>
                          </div>

                          <!-- Book Location Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="newBookLocation">Location<span class="text-danger">*</span>
                                <input type="text" class="form-control" id="newBookLocation" name="newBookLocation" placeholder="Book Location" required="" value="<?= $getBookInfo['location']; ?>" />
                                <div class="invalid-feedback"> Book Location is required</div>
                              </label>
                            </div>
                          </div>

                          <!-- Book Shelve Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="newBookShelve">Shelve<span class="text-danger">*</span>
                                <input type="text" class="form-control" id="newBookShelve" name="newBookShelve" placeholder="Book Shelve" required="" value="<?= $getBookInfo['shelve']; ?>" />
                                <div class="invalid-feedback"> Book Shelve is required</div>
                              </label>
                            </div>
                          </div>

                          <!-- Book Rack Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="newBookRack">Rack<span class="text-danger">*</span>
                                <input type="text" class="form-control" id="newBookRack" name="newBookRack" placeholder="Book Rack" required="" value="<?= $getBookInfo['rack']; ?>" />
                                <div class="invalid-feedback"> Book Rack is required</div>
                              </label>
                            </div>
                          </div>

                          <!-- Book Quantity Input -->
                          <div class="form-group col-sm-6">
                            <div class="input-group">
                              <label class="col-12" for="newBookQuantity">How Many Of this book is available?<span class="text-danger">*</span>
                                <input type="number" class="form-control" id="newBookQuantity" name="newBookQuantity" placeholder="Book Quantity" required="" min="1" value="<?= $getBookInfo['quantity']; ?>" />
                                <div class="invalid-feedback"> Book Quantity is required</div>
                              </label>
                            </div>
                          </div>

                          <!-- Book Description Input -->
                          <div class="form-group col-sm-12">
                            <div class="input-group">
                              <label class="col-12" for="newBookDescription">Book Description<span class="text-danger">*</span>
                                <textarea class="form-control" name="newBookDescription" placeholder="Enter Book Description" required=""><?= $getBookInfo['description']; ?></textarea>
                                <div class="invalid-feedback">Book description is required</div>
                              </label>
                            </div>
                          </div>
                          <div id="modifyBookMsg"></div>
                          <div style="margin: 0px auto">
                            <button class="btn btn-primary" type="submit" id="modifyBookBtn">Update Book</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
          <script>
            //For select
            $('select').each(function() {
              $(this).select2({
                theme: 'bootstrap4',
                width: 'style',
                placeholder: $(this).attr('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
              });
            });


            // PREVIEW BOOK COVER IMAGE ONCE ITS SELECTED STARTS
            /*** Create an arrow function that will be called when an image is selected.*/
            var previewBookModifyImage = (event) => {
              // console.log(event);
              /**
               * Get the selected files.
               */
              const imageFiles = event.target.files;
              /**
               * Count the number of files selected.
               */
              const imageFilesLength = imageFiles.length;
              /**
               * If at least one image is selected, then proceed to display the preview.
               */
              if (imageFilesLength > 0) {
                /**
                 * Get the image path.
                 */
                const imageSrc = URL.createObjectURL(imageFiles[0]);
                /**
                 * Select the image preview element.
                 */
                const imagePreviewElement = document.querySelector("#modifyBookCoverImagePreview");
                /**
                 * Assign the path to the image preview element.
                 */
                imagePreviewElement.src = imageSrc;
                /**
                 * Show the element by changing the display value to "block".
                 */
                //			imagePreviewElement.style.display = "block";
                imagePreviewElement.style.display = "";
              } else {
                /**
                 * Select the element where you want to set the image.
                 */
                const imagePreviewElement = document.querySelector("#modifyBookCoverImagePreview");
                /**
                 * Set a default image when no image is selected.
                 */
                imagePreviewElement.src = "<?= $coverImage; ?>";
              }
            };
            // PREVIEW BOOK COVER IMAGE ONCE ITS SELECTED ENDS

            $("#modifyBookForm").submit(function(e) {
              e.preventDefault();
              //console.log('welcome');
              var bookForm = new FormData($("#modifyBookForm")[0]);
              bookForm.append("modifyBookInfo", true);
              swal({
                  title: "Are you sure to update Book?",
                  text: "Updating this book is effective across the portal.",
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
                    url: 'controllers/get-books',
                    async: true,
                    processData: false,
                    contentType: false,
                    // mimeType: 'multipart/form-data',
                    // cache: false,
                    data: bookForm,
                    beforeSend: function() { // Corrected from beforeSubmit to beforeSend
                      $("#modifyBookBtn").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
                    },
                    success: function(response) {

                      var status = response.status;
                      var message = response.message;
                      var responseStatus = response.responseStatus;
                      var header = response.header;

                      if (status === true) {
                        $("#modifyBookMsg").html(response).css("color", "green").show();
                        swal(header, message, responseStatus);
                        //loadUsers(); //Reload registered courses table
                      } else {
                        swal(header, message, responseStatus);
                      }
                    },
                    error: function() {
                      $("#modifyBookMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
                      swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
                    },
                    complete: function() { // Moved the timeout code to the complete callback
                      setTimeout(function() {
                        $("#modifyBookMsg").fadeOut(300);
                      }, 3000);
                      $("#modifyBookBtn").html("Update Book").show(); // Reset the button text
                    }
                  });
                });
            });
          </script>
        </div>
      <?php } ?>
    </div>
  </div>
<?php
  exit();
}
//LOAD BOOK CONTENT MODAL FUNCTION ||||||Ends>>>>>>>

//ADD NEW BOOK FUNCTION ||||||Starts>>>>>>>
if (isset($_POST['addNewBook'], $_POST['newBookType'], $_POST['newBookTitle'])) {

  $newBookType = $_POST['newBookType'];
  $newBookTitle = $_POST['newBookTitle'];
  $newBookAuthor = $_POST['newBookAuthor'];
  $newBookISBN = $_POST['newBookISBN'];
  $newBookPublisher = $_POST['newBookPublisher'];
  $newBookReturnDuration = $_POST['newBookReturnDuration'];
  $newBookLateReturnFine = $_POST['newBookLateReturnFine'];
  $newBookPublishedDate = date("d-m-Y", strtotime($_POST['newBookPublishedDate']));
  $newBookLocation = $_POST['newBookLocation'];
  $newBookShelve = $_POST['newBookShelve'];
  $newBookRack = $_POST['newBookRack'];
  $newBookQuantity = $_POST['newBookQuantity'];
  $newBookDescription = $_POST['newBookDescription'];

  date_default_timezone_set("Africa/Lagos");
  $date = date("j-m-Y, g:i a");

  //Get book type category
  $stmt = $conn->prepare("SELECT * FROM library_book_category WHERE type=?");
  $stmt->bind_param("s", $newBookType);
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
  $getBookCategory = $result->fetch_array();
  $bookCategory = $getBookCategory['categoryName'];
  $stmt->close();


  // Generate a random Book ID
  $characters = '1234567890';
  $charactersLength = strlen($characters);
  $randomString = substr($newBookTitle, 0, 2);
  for ($i = 0; $i < 8; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  $bookID = $randomString;


  // Check for duplicate
  $stmt = $conn->prepare("SELECT * FROM library_books WHERE title = ? AND author = ? AND category = ? AND publisher = ?");
  $stmt->bind_param('sssss', $newBookTitle, $newBookAuthor, $bookCategory, $username, $newBookPublisher);
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
  $getDuplicateInfo = $result->fetch_array();

  if ($stmt->num_rows > 0) {
    $response = array('status' => false, 'header' => 'Duplicate Entry!', 'message' => 'Book Already Exist', 'responseStatus' => 'warning');
  } elseif (isset($_FILES['bookCoverImage']) && !empty($_FILES['bookCoverImage']['tmp_name'])) {

    $valid_extensions = array('jpeg', 'jpg', 'png');
    $path = 'books/';
    $directory = '../resources/books/';
    $img = $_FILES['bookCoverImage']['name'];
    $tmp = $_FILES['bookCoverImage']['tmp_name'];
    $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
    $final_image = rand(1000, 1000000) . str_replace(' ', '_', $img);

    if (in_array($ext, $valid_extensions)) {
      $path = $path . strtolower($final_image);
      $directory = $directory . strtolower($final_image);

      if (move_uploaded_file($tmp, $directory)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO library_books(`id`, `title`, `category`, `subCategory`, `description`, `author`, `ISBN`, `publisher`, `coverImage`, `returnDuration`, `fineAmount`, `publishedDate`, `location`, `shelve`, `rack`, `quantity`, `status`, `addedDate`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'available',?)");
        $stmt->bind_param('sssssssssssssssss', $bookID, $newBookTitle, $bookCategory, $newBookType, $newBookDescription, $newBookAuthor, $newBookISBN, $newBookPublisher, $path, $newBookReturnDuration, $newBookLateReturnFine, $newBookPublishedDate, $newBookLocation, $newBookShelve, $newBookRack, $newBookQuantity, $date);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
          $response = array('status' => true, 'header' => 'Successful!', 'message' => 'User Image Updated Successfully', 'responseStatus' => 'success');
        } else {
          $response = array('status' => false, 'header' => 'Failed!', 'message' => 'An error occurred, please try again', 'responseStatus' => 'error');
        }
        $stmt->close();
      }
    }
  } else {
    $response = array('status' => false, 'header' => 'No cover image!', 'message' => 'Book cover image is required', 'responseStatus' => 'warning');
  }


  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}
//ADD NEW BOOK FUNCTION ||||||Ends>>>>>>>

//UPDATE BOOK FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['bookID'], $_POST['newBookTitle'], $_POST['modifyBookInfo'])) {

  // date_default_timezone_set("Africa/Lagos"); 
  // $date = date("j-m-Y, g:i a");
  $newBookType = $_POST['newBookType'];
  $newBookTitle = $_POST['newBookTitle'];
  $newBookAuthor = $_POST['newBookAuthor'];
  $newBookISBN = $_POST['newBookISBN'];
  $newBookPublisher = $_POST['newBookPublisher'];
  $newBookReturnDuration = $_POST['newBookReturnDuration'];
  $newBookLateReturnFine = $_POST['newBookLateReturnFine'];
  $newBookPublishedDate = date("d-m-Y", strtotime($_POST['newBookPublishedDate']));
  $newBookLocation = $_POST['newBookLocation'];
  $newBookShelve = $_POST['newBookShelve'];
  $newBookRack = $_POST['newBookRack'];
  $newBookQuantity = $_POST['newBookQuantity'];
  $newBookDescription = $_POST['newBookDescription'];
  $bookID = $_POST['bookID'];

  date_default_timezone_set("Africa/Lagos");
  $date = date("j-m-Y, g:i a");

  //Get book type category
  $stmt = $conn->prepare("SELECT * FROM library_book_category WHERE type=?");
  $stmt->bind_param("s", $newBookType);
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
  $getBookCategory = $result->fetch_array();
  $bookCategory = $getBookCategory['categoryName'];
  $stmt->close();

  // Check for duplicate
  $stmt = $conn->prepare("SELECT * FROM library_books WHERE title=? AND category=? AND subCategory=? AND description=? AND author=? AND ISBN=? AND publisher=? AND returnDuration=? AND fineAmount=? AND publishedDate=? AND location=? AND shelve=? AND rack=? AND quantity=? AND id =?");
  $stmt->bind_param('sssssssssssssss', $newBookTitle, $bookCategory, $newBookType, $newBookDescription, $newBookAuthor, $newBookISBN, $newBookPublisher, $newBookReturnDuration, $newBookLateReturnFine, $newBookPublishedDate, $newBookLocation, $newBookShelve, $newBookRack, $newBookQuantity, $bookID);
  $stmt->execute() or die($stmt->error);
  $result = $stmt->get_result();
  $getDuplicateInfo = $result->fetch_array();

  $imageUploaded = isset($_FILES['bookCoverImage']) && !empty($_FILES['bookCoverImage']['tmp_name']);
  $dataChanged = $stmt->num_rows < 1;
  $stmt->close();

  if ($imageUploaded) {
    // Update book Cover image
    $sqlBook = mysqli_query($conn, "SELECT * FROM library_books WHERE id='" . $bookID . "'") or die(mysqli_error($conn));
    $getBook = mysqli_fetch_array($sqlBook);

    ((!empty($getBook["coverImage"]) && file_exists("../resources/" . $getBook["coverImage"])) ? unlink("../resources/" . $getBook["coverImage"]) : '');

    $valid_extensions = array('jpeg', 'jpg', 'png');
    $path = 'books/';
    $directory = '../resources/books/';
    $img = $_FILES['bookCoverImage']['name'];
    $tmp = $_FILES['bookCoverImage']['tmp_name'];
    $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
    $final_image = rand(1000, 1000000) . str_replace(' ', '_', $img);

    if (in_array($ext, $valid_extensions)) {
      $path = $path . strtolower($final_image);
      $directory = $directory . strtolower($final_image);

      if (move_uploaded_file($tmp, $directory)) {
        $stmt = mysqli_prepare($conn, "UPDATE `library_books` SET `title`=?, `category`=?, `subCategory`=?, `description`=?,  `author`=?,  `ISBN`=?, `publisher`=?, `coverImage`=?, `returnDuration`=?, `fineAmount`=?, `publishedDate`=?, `location`=?, `shelve`=?, `rack`=?, `quantity`=? WHERE `id`=?");
        $stmt->bind_param('ssssssssssssssss', $newBookTitle, $bookCategory, $newBookType, $newBookDescription, $newBookAuthor, $newBookISBN, $newBookPublisher, $path, $newBookReturnDuration, $newBookLateReturnFine, $newBookPublishedDate, $newBookLocation, $newBookShelve, $newBookRack, $newBookQuantity, $bookID);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
          $response = array('status' => true, 'header' => 'Successful!', 'message' => 'Book has been updated successfully', 'responseStatus' => 'success');
        } else {
          $response = array('status' => false, 'header' => 'Error Occurred!', 'message' => 'There is nothing to update', 'responseStatus' => 'warning');
        }
        $stmt->close();
      }
    }
  } elseif ($dataChanged && !$imageUploaded) {
    // Update book information
    $stmt = mysqli_prepare($conn, "UPDATE `library_books` SET `title`=?, `category`=?, `subCategory`=?, `description`=?, `author`=?, `ISBN`=?, `publisher`=?, `returnDuration`=?, `fineAmount`=?, `publishedDate`=?, `location`=?, `shelve`=?, `rack`=?, `quantity`=? WHERE `id`=?");
    $stmt->bind_param('sssssssssssssss', $newBookTitle, $bookCategory, $newBookType, $newBookDescription, $newBookAuthor, $newBookISBN, $newBookPublisher, $newBookReturnDuration, $newBookLateReturnFine, $newBookPublishedDate, $newBookLocation, $newBookShelve, $newBookRack, $newBookQuantity, $bookID);
    $stmt->execute();

    if ($stmt->errno) {
      // Handle the error gracefully
      $response = array('status' => false, 'header' => 'Error!', 'message' => $stmt->error, 'responseStatus' => 'error');
    } else {
      if ($stmt->affected_rows > 0) {
        $response = array('status' => true, 'header' => 'Successful!', 'message' => 'Book has been updated successfully', 'responseStatus' => 'success');
      } else {
        $response = array('status' => false, 'header' => 'No Changes!', 'message' => 'There is nothing to update', 'responseStatus' => 'warning');
      }
    }

    $stmt->close();
  } else {
    $response = array('status' => false, 'header' => 'No Changes!', 'message' => 'There is nothing to update', 'responseStatus' => 'warning');
  }

  header('Content-Type: application/json');
  echo json_encode($response);

  exit();
}
//UPDATE BOOK FUNCTION |||||||Ends>>>>>>>>>>

//NEW BOOK RESERVATION FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['makeBookReservation'], $_POST['bookID'])) {

  // Function to reserve a book
  function reserveBook($conn, $userID, $bookID)
  {
    // Check if the number of reservations exceeds the quantity of the book
    $bookQuantity = getBookQuantity($conn, $bookID);
    $reservationCount = getReservationCount($conn, $bookID);

    if ($reservationCount >= $bookQuantity) {
      return array('status' => false, 'header' => 'Reservation Failed', 'message' => 'No more reservations can be made for this book.', 'responseStatus' => 'warning');
    }

    // Check if the user already reserved this book
    if (userHasReservation($conn, $userID, $bookID)) {
      return array('status' => false, 'header' => 'Reservation Failed', 'message' => 'You already have a reservation for this book.', 'responseStatus' => 'warning');
    }

    // Check if the user already have this book borrowed and yet to return
    if (userBorrowedBook($conn, $userID, $bookID)) {
      return array('status' => false, 'header' => 'Reservation Failed', 'message' => 'You currently have this book and yet to return.', 'responseStatus' => 'warning');
    }

    // Insert reservation into the library_reservations table
    if (insertReservation($conn, $userID, $bookID)) {
      return array('status' => true, 'header' => 'Reservation Successful', 'message' => 'Book reserved successfully for the next 48 Hours.', 'responseStatus' => 'success');
    } else {
      return array('status' => false, 'header' => 'Reservation Failed', 'message' => 'Failed to make reservation. Please try again later.', 'responseStatus' => 'error');
    }
  }

  //Usage
  $userID = $_SESSION['userID'];
  $bookID = $_POST['bookID'];
  $response = reserveBook($conn, $userID, $bookID);


  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}
//NEW BOOK RESERVATION FUNCTION |||||||Ends>>>>>>>>>>

//REMOVE BOOK RESERVATION FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['removeBookReservation'], $_POST['bookID'])) {

  // Function to reserve a book
  function removeBookReservation($conn, $userID, $bookID)
  {

    // Check if the user already reserved this book
    if (userHasReservation($conn, $userID, $bookID)) {
      // remove reservation from the library_reservations table
      if (removeReservation($conn, $userID, $bookID)) {
        return array('status' => true, 'header' => 'Successful', 'message' => 'Reservation was successful removed.', 'responseStatus' => 'success');
      } else {
        return array('status' => false, 'header' => 'Failed', 'message' => 'Failed to remove reservation. Please try again later.', 'responseStatus' => 'error');
      }
    } else {
      return array('status' => false, 'header' => 'No Reservation', 'message' => 'You currently don`t have a reservation for this book.', 'responseStatus' => 'error');
    }
  }

  //Usage
  $userID = $_SESSION['userID'];
  $bookID = $_POST['bookID'];
  $response = removeBookReservation($conn, $userID, $bookID);


  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}
//REMOVE BOOK RESERVATION FUNCTION |||||||Ends>>>>>>>>>>

//ISSUING NEW BOOK FOR BORROW FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['issueBookForBorrow'], $_POST['bookID'], $_POST['userID'])) {

  // Function to issue a book
  function issueBook($conn, $userID, $bookID)
  {

    // Check if the user already borrowed this book and yet to return
    if (userBorrowedBook($conn, $userID, $bookID)) {
      return array('status' => false, 'header' => 'Book Issue Failed', 'message' => 'Book already issued to user and yet to return', 'responseStatus' => 'warning');
    }

    // Insert issued book into the library_reservations table
    if (issueBookForBorrow($conn, $userID, $bookID)) {
      return array('status' => true, 'header' => 'Book Issue Successful', 'message' => 'Book has been issued to user successfully ', 'responseStatus' => 'success');
    } else {
      return array('status' => false, 'header' => 'Book Issue Failed', 'message' => 'Failed to issue book. Please try again later.', 'responseStatus' => 'error');
    }
  }

  //Usage
  $userID = $_POST['userID'];
  $bookID = $_POST['bookID'];
  $response = issueBook($conn, $userID, $bookID);


  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}
//ISSUING NEW BOOK FOR BORROW FUNCTION |||||||Ends>>>>>>>>>>

//ACCEPTING RETURNED BOOKS FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['acceptBookReturn'], $_POST['bookID'], $_POST['userID'])) {

  // Function to return a book
  function returnBook($conn, $userID, $bookID)
  {

    // // Check if the user already returned this book
    // if (userHasReadBook($conn, $userID, $bookID)) {
    //   return array('status' => false, 'header' => 'Book Return Failed', 'message' => 'Book already returned', 'responseStatus' => 'warning');
    // }

    // Insert issued book into the library_reservations table
    if (acceptReturnBook($conn, $userID, $bookID)) {
      return array('status' => true, 'header' => 'Book Accepted and Returned Successfully', 'message' => 'Book has been accepted as returned ', 'responseStatus' => 'success');
    } else {
      return array('status' => false, 'header' => 'Book Return Failed', 'message' => 'Failed to accept book. Please try again later.', 'responseStatus' => 'error');
    }
  }

  //Usage
  $userID = $_POST['userID'];
  $bookID = $_POST['bookID'];
  $response = returnBook($conn, $userID, $bookID);


  header('Content-Type: application/json');
  echo json_encode($response);
  exit();
}
//ACCEPTING RETURNED BOOKS FUNCTION |||||||Ends>>>>>>>>>>
?>