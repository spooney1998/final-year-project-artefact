<?php
session_start();

if (!isset($_SESSION['userID']) || !isset($_SESSION['userRole'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "books";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Roehampton Library :: Books</title>
  <link rel="icon" href="images/roe.png" type="icon/png">
  <meta name="viewport" content="width=device-width,initial-scale=1">


  <!-- START: Template CSS-->
  <link rel="stylesheet" href="dist/vendors/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.theme.min.css">
  <link rel="stylesheet" href="dist/vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="dist/vendors/flags-icon/css/flag-icon.min.css">
  <link rel="stylesheet" href="dist/vendors/sweetalert/sweetalert.css">
  <!-- END Template CSS-->

  <!-- START: Page CSS-->
  <link rel="stylesheet" href="dist/vendors/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="dist/vendors/ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/vendors/fancybox/jquery.fancybox.min.css">
  <link rel="stylesheet" href="dist/vendors/select2/css/select2.min.css" />
  <link rel="stylesheet" href="dist/vendors/select2/css/select2-bootstrap.min.css" />
  <!-- END: Page CSS-->

  <!-- START: Custom CSS-->
  <link rel="stylesheet" href="dist/css/main.css">
  <!-- END: Custom CSS-->
  <style>
    .skeleton-loader {
      display: inline-block;
      width: 100%;
      max-width: 23rem;
      /* Adjust as needed */
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }

    .skeleton-content,
    .skeleton-footer {
      background-color: #f0f0f0;
      /* Light gray background */
      height: 3rem;
      /* Adjust height as needed */
      margin-bottom: 10px;
      /* Adjust spacing between elements */
    }

    .skeleton-content {
      height: 16rem;
      /* Adjust height as needed */
    }

    .modal-skeleton-loader {
      width: 100%;
      height: 30rem;
      border-radius: 4px;
    }

    /* Animation for skeleton loading effect */
    @keyframes shimmer {
      0% {
        background-position: -200px 0;
      }

      100% {
        background-position: 200px 0;
      }
    }

    .skeleton-loader .skeleton-header,
    .skeleton-loader .skeleton-content,
    .skeleton-loader .skeleton-footer,
    .modal-skeleton-loader {
      background-image: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 300px 100%;
      animation: shimmer 1.5s infinite;
      display: flex;
    }

    .clamp-1 {
      display: -webkit-box;
      -webkit-line-clamp: 1;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>
</head>
<!-- END Head-->

<!-- START: Body-->

<body id="main-container" class="default compact-menu">
  <!-- START: Pre Loader-->
  <div class="se-pre-con">
    <img class="loader" src="images/roe.png" alt="">
  </div>
  <!-- END: Pre Loader-->

  <!-- START: Header-->
  <?php include("inc/header.php"); ?>
  <!-- END: Header-->

  <!-- START: Main Menu-->
  <?php include("inc/sidebar.php"); ?>
  <!-- END: Main Menu-->

  <!-- START: Main Content-->
  <main>
    <div class="container-fluid site-width">
      <!-- START: Breadcrumbs-->
      <div class="row ">
        <div class="col-12 align-self-center">
          <div class="sub-header mt-3 py-3 align-self-center d-sm-flex w-100 rounded">
            <div class="w-sm-100 mr-auto">
              <h4 class="mb-0">Search Books</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item"><a href="./">Dashboard</a></li>
              <li class="breadcrumb-item active"><?= ucfirst($page); ?></li>
          </div>
          </ol>
        </div>
      </div>
    </div>
    <!-- END: Breadcrumbs-->

    <!-- START: Card Data-->
    <div class="row">
      <div class="col-12 col-lg-3 col-xl-2 mb-4 mt-3 pr-lg-0 flip-menu">
        <a href="#" class="d-inline-block d-lg-none mt-1 flip-menu-close"><i class="icon-close"></i></a>
        <div class="card border h-100 mail-menu-section ">
          <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
            <div class="media d-block text-center  p-3">
              <a href="#" class="bg-primary w-100 d-block py-2 px-2 rounded text-white" data-toggle="modal" data-target="#addNewBookModal">
                <i class="fas fa-book-medical align-middle text-white"></i> <span>Add New Book</span>
              </a>
            </div>

            <!-- Add Book Form-->
            <div class="modal fade" id="addNewBookModal">
              <div class="modal-dialog modal-xl">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">
                      <i class="fas fa-book-medical"></i> New Book
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <i class="icon-close"></i>
                    </button>
                  </div>
                  <div class="modal-body">
                    <!-- Tabs Nav Links -->
                    <div class="profile-menu mt-4 theme-background border  z-index-1 p-2">
                      <div class="d-sm-flex">
                        <div class="align-self-center">
                          <ul class="nav nav-pills flex-column flex-sm-row" id="myTab" role="tablist">
                            <li class="nav-item ml-0">
                              <a class="nav-link  py-2 px-4 px-lg-4 active" data-toggle="tab" href="#addBook"><i class="fas fa-book-medical"></i> Add a book</a>
                            </li>
                            <li class="nav-item ml-0">
                              <a class="nav-link  py-2 px-4 px-lg-4" data-toggle="tab" href="#bookUploads"><i class="fas fa-upload"></i> Upload Books</a>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <!-- Tabs Nav Links -->

                    <div class="tab-content">

                      <!-- Add Single book  -->
                      <div id="addBook" class="tab-pane fade in active">
                        <form id="newBookForm" class="needs-validation" novalidate>
                          <div class="row">
                            <div class="col-12 mt-2">
                              <div class="card">
                                <div class="card-body">
                                  <div class="row">
                                    <div class="col-md-12 col-lg-4 position-relative" style="height: 37rem;">
                                      <img class="img-fluid" alt="New Book Cover Image" src="images/no-preview.jpeg" id="newBookCoverImagePreview" style="width: 100%; height: auto;">
                                      <div class="text-center z-index-1" style="margin:0px auto !important;">
                                        <label for="bookCoverImage" class="file-upload btn btn-warning btn-sm px-4 rounded-pill shadow mt-3"><i class="fa fa-upload mr-2"></i>Select Cover Image<input id="bookCoverImage" onchange="previewImage(event);" name="bookCoverImage" type="file" required>
                                        </label>
                                      </div>
                                    </div>
                                    <div class="col-md-12 col-lg-8">
                                      <div class="row">
                                        <!-- Book Type Select -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="bookType">Book Type<span class="text-danger">*</span>
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
                                              <select class="form-control" id="newBookType" name="newBookType" required="">
                                                <option label="Choose one thing" readonly value="">Select type of book</option>
                                                <?php
                                                // Loop through the groups and generate option groups and options
                                                foreach ($groups as $categoryName => $categories) {
                                                  echo '<optgroup label="' . $categoryName . '" >';
                                                  foreach ($categories as $category) {
                                                    echo '<option value="' . $category['type'] . '">' . $category['type'] . '</option>';
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
                                            <label class="col-12" for="newBookTitle">Book Title<span class="text-danger">*</span>
                                              <input class="form-control" type="text" name="newBookTitle" id="newBookTitle" placeholder="Book Title" required="" maxlength="100" />
                                              <div class="invalid-feedback">Book title is required </div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book Author Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookAuthor">Author<span class="text-danger">*</span>
                                              <input type="text" class="form-control" id="newBookAuthor" name="newBookAuthor" placeholder="Book Author" required="" maxlength="50" />
                                              <div class="invalid-feedback"> Book Author is required</div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Boon ISBN Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookISBN">ISBN<span class="text-danger">*</span>
                                              <input type="text" class="form-control" name="newBookISBN" id="newBookISBN" placeholder="Enter ISBN" required="" maxlength="100" />
                                              <div class="invalid-feedback">ISBN is required </div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book Publisher Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookPublisher">Publisher<span class="text-danger">*</span>
                                              <input type="text" class="form-control" id="newBookPublisher" name="newBookPublisher" placeholder="Book Publisher" required="" maxlength="50" />
                                              <div class="invalid-feedback"> Book Publisher is required</div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book Return Duration Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookReturnDuration"> Return Duration (Days)<span class="text-danger">*</span>
                                              <input type="number" class="form-control" id="newBookReturnDuration" name="newBookReturnDuration" placeholder="Book Return Duration in days" required="" maxlength="3" max="60" min="1" value="7" />
                                              <div class="invalid-feedback"> Book Return Duration is required</div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book lateReturnFine Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookLateReturnFine">Late Return Fine(£)<span class="text-danger">*</span>
                                              <input type="number" class="form-control" id="newBookLateReturnFine" name="newBookLateReturnFine" placeholder="Book Late Return Fine(£)" required="" maxlength="50" min="0" value="50" />
                                              <div class="invalid-feedback"> Book late Return Fine is required</div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book Published Date Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookPublishedDate">Published Date<span class="text-danger">*</span>
                                              <input type="date" class="form-control" id="newBookPublishedDate" name="newBookPublishedDate" placeholder="Book Published Date" required="" maxlength="50" />
                                              <div class="invalid-feedback"> Book Published Date is required</div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book Location Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookLocation">Location<span class="text-danger">*</span>
                                              <input type="text" class="form-control" id="newBookLocation" name="newBookLocation" placeholder="Book Location" required="" />
                                              <div class="invalid-feedback"> Book Location is required</div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book Shelve Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookShelve">Shelve<span class="text-danger">*</span>
                                              <input type="text" class="form-control" id="newBookShelve" name="newBookShelve" placeholder="Book Shelve" required="" />
                                              <div class="invalid-feedback"> Book Shelve is required</div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book Rack Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookRack">Rack<span class="text-danger">*</span>
                                              <input type="text" class="form-control" id="newBookRack" name="newBookRack" placeholder="Book Rack" required="" />
                                              <div class="invalid-feedback"> Book Rack is required</div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book Quantity Input -->
                                        <div class="form-group col-sm-6">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookQuantity">How Many Of this book is available?<span class="text-danger">*</span>
                                              <input type="number" class="form-control" id="newBookQuantity" name="newBookQuantity" placeholder="Book Quantity" required="" min="1" value="1" />
                                              <div class="invalid-feedback"> Book Quantity is required</div>
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Book Description Input -->
                                        <div class="form-group col-sm-12">
                                          <div class="input-group">
                                            <label class="col-12" for="newBookDescription">Book Description<span class="text-danger">*</span>
                                              <textarea class="form-control" name="newBookDescription" placeholder="Enter Book Description" required=""></textarea>
                                              <div class="invalid-feedback">Book description is required</div>
                                            </label>
                                          </div>
                                        </div>
                                        <div style="margin: 0px auto">
                                          <button class="btn btn-primary" type="submit" id="saveNewBookBtn">Save Book</button>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                      <!-- Add Single book  -->

                      <!-- Book CSV Uploads Tab -->
                      <div class="tab-pane fade" id="bookUploads">
                        <div class="row">
                          <div class="col-12 mt-2">
                            <div class="alert alert-info">

                              <b>Kindly follow the guidelines below for successful book uploads:</b>
                              <ol type="1">
                                <li>All books will be uploaded without an initial book cover image.</li>
                                <li>Book CSV sheets are to follow the column arrangements below for organized entry:</li>
                                <p>`title`, `Book Category (e.g., Fiction)`, `type (e.g., Novels)`, `description`, `author`, `ISBN`, `publisher`, `Return Duration (in days)`, `Fine Amount`, `published Date`, `location`, `shelf`, `rack`, `quantity`</p>
                                <li>After successful uploads, book cover images are to be updated immediately for visual references.</li>
                              </ol>
                            </div>
                          </div>
                          <div class="card col-12">
                            <div class="card-body">

                              <div class="text-center z-index-1" style="margin:0px auto !important;">
                                <form id="bookCSVUploadForm">
                                  <label for="bookCSVFile" class="file-upload btn btn-warning btn-sm px-4 rounded-pill shadow mt-3"><i class="fa fa-upload mr-2"></i>Select Book CSV File<input id="bookCSVFile" name="bookCSVFile" type="file" required accept=".csv">
                                  </label>
                                  <div>&nbsp;</div>
                                  <button type="submit" class="btn btn-primary btn-sm mt-3" id="bookCSVUploadBtn">Upload Book CSV</button>
                                </form>
                              </div>

                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Book CSV Uploads Tab -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Add Book Form -->
          <?php } ?>


          <!-- Book Content -Preview,modify etc. -->
          <div class="modal fade bd-example-modal-lg" id="bookContent" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
            <div class="modal-dialog modal-xl">
              <!-- display book content -->
              <div class="modal-content" id="displayBookContent">
              </div>
            </div>
          </div>

          <ul class="list-unstyled inbox-nav  mb-0 mt-2 notes-menu">
            <li class="nav-item"><a href="#" data-notetype="all" class="nav-link active select-link-status" data-status=""><i class="fas fa-book pr-2"></i> All Books</a></li>
            <li class="nav-item"><a href="#" data-notetype="starred" class="nav-link select-link-status" data-status="available"><i class="fas fa-book-open pr-2"></i> Available Books</a></li>
            <li class="nav-item"><a href="#" data-notetype="important" class="nav-link select-link-status" data-status="borrowed"><i class="fas fa-handshake pr-2"></i> Borrowed Books</a></li>
            <li class="nav-item"><a href="#" data-notetype="important" class="nav-link select-link-status" data-status="reserved"><i class="far fa-calendar-alt pr-2"></i> Reserved Books</a></li>
          </ul>
          <div class="eagle-divider"></div>
          <div class="card-header py-1 mt-4">
            <h6 class="mb-0">Color Labels</h6>
          </div>
          <ul class="nav flex-column font-weight-bold mt-3 note-label" id="myTab1" role="tablist">
            <li class="nav-item  px-3">
              <a class="nav-link text-primary" data-label="business-note" href="#">
                <i class="icon-pin"></i> Available Books
              </a>
            </li>
            <li class="nav-item  px-3">
              <a class="nav-link text-danger" data-label="private-note" href="#">
                <i class="icon-pin"></i> Unavailable Books
              </a>
            </li>
            <li class="nav-item  px-3">
              <a class="nav-link text-danger" data-label="private-note" href="#">
                <i class="icon-pin"></i> Borrowed Books
              </a>
            </li>
            <li class="nav-item  px-3">
              <a class="nav-link text-warning" data-label="social-note" href="#">
                <i class="icon-pin"></i> Reserved Books
              </a>
            </li>
            <li class="nav-item px-3 ">
              <a class="nav-link text-info" data-label="work-note" href="#">
                <i class="icon-pin"></i> Read Books
              </a>
            </li>

          </ul>

        </div>
      </div>

      <div class="col-12 col-lg-9 col-xl-10 mb-4 mt-3 pl-lg-0">
        <div class="card border  h-100 notes-list-section">
          <!-- Search Inputs :: Start -->
          <div class="card-header border-bottom p-1 d-flex">
            <a href="#" class="d-inline-block d-lg-none flip-menu-toggle"><i class="icon-menu"></i></a>
            <select class="" id="booksPageLimit">
              <option value="" disabled>Per Page</option>
              <option value="20">20 Books</option>
              <option value="50">50 Books</option>
              <option value="100">100 Books</option>
              <option value="200">200 Books</option>
            </select>
            <input type="text" id="booksSearchEntry" class="form-control border-0 p-2 w-70 h-100 invoice-search" placeholder="Search Books By ID, Title, Author, Publisher,  etc.">
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

            <select class="p-4 form-select" style="width: 30%;" id="bookType">
              <option label="Choose one thing" readonly value="">All types of books</option>
              <?php
              // Loop through the groups and generate option groups and options
              foreach ($groups as $categoryName => $categories) {
                echo '<optgroup label="' . $categoryName . '" >';
                foreach ($categories as $category) {
                  echo '<option value="' . $category['type'] . '">' . $category['type'] . '</option>';
                }
                echo '</optgroup>';
              }
              ?>
            </select>

          </div>
          <!-- Search Inputs :: End-->

          <!-- Display Books -->
          <div class="notes" style="max-height:40rem;overflow:auto" id="displayBooks"></div>
          <!-- Display Books -->

        </div>
      </div>
    </div>
    <!-- END: Card Data-->
  </main>
  <!-- END: Content-->



  <!-- START: Footer-->
  <?php include("inc/footer.php"); ?>
  <!-- END: Footer-->


  <!-- START: Back to top-->
  <a href="#" class="scrollup text-center">
    <i class="icon-arrow-up"></i>
  </a>
  <!-- END: Back to top-->

  <!-- START: Template JS-->
  <script src="dist/vendors/jquery/jquery-3.3.1.min.js"></script>
  <script src="dist/vendors/jquery-ui/jquery-ui.min.js"></script>
  <script src="dist/vendors/moment/moment.js"></script>
  <script src="dist/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/vendors/slimscroll/jquery.slimscroll.min.js"></script>
  <script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
  <!-- END: Template JS-->

  <!-- START: APP JS-->
  <!-- <script src="dist/js/app.js"></script> -->
  <!-- END: APP JS-->

  <!-- START: Page JS-->
  <script src="dist/vendors/fancybox/jquery.fancybox.min.js"></script>
  <script src="dist/vendors/select2/js/select2.full.min.js"></script>
  <script src="dist/js/notes.script.js"></script>
  <script src="dist/js/select2.script.js"></script>
  <!-- END: Page JS-->
  <script>
    $(window).on("load", function() {
      // Animate loader off screen
      $(".se-pre-con").fadeOut("slow");

      //load books
      loadBooks(1);

    });

    // PREVIEW NEW BOOK COVER IMAGE ONCE ITS SELECTED STARTS
    /*** Create an arrow function that will be called when an image is selected.*/
    const previewImage = (event) => {
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
        const imagePreviewElement = document.querySelector("#newBookCoverImagePreview");
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
        const imagePreviewElement = document.querySelector("#newBookCoverImagePreview");
        /**
         * Set a default image when no image is selected.
         */
        imagePreviewElement.src = "images/no-preview.jpeg";
      }
    };
    // PREVIEW NEW BOOK COVER IMAGE ONCE ITS SELECTED ENDS


    /*==============================================================
     Sidebar 
     ============================================================= */
    $('.sidebarCollapse').on('click', function() {
      $('body').toggleClass('compact-menu');
      $('.sidebar').toggleClass('active');
    });

    $('.mobilesearch').on('click', function() {
      $('.search-form').toggleClass('d-none');

    });

    ///////////////// Flip Menu ///////////

    $(".flip-menu-toggle").on("click", function() {
      $('.flip-menu').toggleClass('active');
    });
    $(".flip-menu-close").on("click", function() {
      $('.flip-menu').toggleClass('active');
    });

    //LOAD BOOKS FUNCTION>>>>>>STARTS
    function loadBooks(page, query = '', status = '') {
      var booksPageLimit = $("#booksPageLimit").val();
      var bookType = $('#bookType').val();
      // console.log(status);

      $.ajax({
        url: "controllers/get-books",
        method: "POST",
        async: true,
        data: {
          showBooks: 1,
          query: query,
          booksPageLimit: booksPageLimit,
          bookType: bookType,
          bookStatus: status,
          page: page
        },
        beforeSend: function() {
          $("#displayBooks").html("").show();
          showBooksSkeletonLoader();
        },
        success: function(data) {
          setTimeout(function() {
            $("#displayBooks").html(data).show();

            $('.skeleton-loader').remove(); // Remove skeleton loader
          }, 1000);
        },
        error: function(data) {
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          setTimeout(function() {
            loadBooks(page);
          }, 5000);
        }
      });
    };

    $('.select-link-status').on('click', function() {
      var status = $(this).data('status');
      var query = $('#booksSearchEntry').val();
      loadBooks(1, query, status);
    });

    $(document).on('click', '.book-page-link', function() {
      var page = $(this).data('page_number');
      var query = $('#booksSearchEntry').val();
      loadBooks(page, query, status);
    });

    $('#booksSearchEntry').on('keyup change paste', function() {
      var query = $('#booksSearchEntry').val();
      loadBooks(1, query, status);
    });

    $('#bookType').on('change', function() {
      loadBooks((1));
    });

    $("#booksPageLimit").on("change", function() { //page limit
      loadBooks(1);
    });

    // Skeleton Loader
    function showBooksSkeletonLoader() {
      var skeletonHtml = `
				<div class="skeleton-loader">
          <div class="skeleton-content"></div>
          <div class="skeleton-footer"></div>
        </div>
				`;

      for (var i = 0; i < 8; i++) {
        $('#displayBooks').append(skeletonHtml);
      }
    }
    //LOAD BOOKS FUNCTION>>>>>>ENDS


    //LOAD BOOK CONTENT MODAL FUNCTION >>>>STARTS
    function loadBookContentsModal(button) {
      var bookID = $(button).data("value");
      //console.log(courseID);
      $.ajax({
        url: 'controllers/get-books',
        method: 'POST',
        async: true,
        data: {
          getBookContents: 1,
          bookID: bookID
        },
        beforeSend: function(gRcs) {
          // $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
          $('#displayBookContent').html("").show();
          modalSkeletonLoader();
        },
        success: function(gRcs) {
          $("#bookContentsModal").modal('show');
          // $(button).html('<span class="fas fa-bars"></span>').show();
          setTimeout(function() {
            $('#displayBookContent').html(gRcs).show();
            $('.modal-skeleton-loader').remove(); // Remove skeleton loader
          }, 2000);
        },
        error: function(gRcs) {
          $(button).html('<span class="fas fa-bars"></span>').show();
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
        }
      });
    }
    // Skeleton Loader
    function modalSkeletonLoader() {
      var skeletonHtml = `
						<div  class='modal-skeleton-loader'> </div>
				`;

      for (var i = 0; i < 1; i++) {
        $('#displayBookContent').append(skeletonHtml);
      }
    }
    //LOAD BOOK CONTENT MODAL FUNCTION >>>>ENDS

    //Function to check if required input is not empty before submitting form
    function checkFormInputsNotEmpty(formID) {
      var form = document.getElementById(formID);
      //console.log(form);
      if (!form) {
        console.error('Form element with ID ' + form + ' not found');
        return false;
      }

      var inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
      var isEmpty = false;

      inputs.forEach(function(input) {
        if (input.value.trim() === '') {
          isEmpty = true;
        }
      });

      return !isEmpty;
    }

    $("#newBookForm").submit(function(e) {
      e.preventDefault();
      var formID = "newBookForm";
      var bookForm = new FormData($("#newBookForm")[0]);
      if (checkFormInputsNotEmpty(formID)) {
        bookForm.append("addNewBook", true);
        swal({
            title: "Are you sure to continue?",
            text: "You are about adding a new book.",
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
              data: bookForm,
              processData: false, // Important when sending FormData
              contentType: false, // Important when sending FormData
              beforeSend: function(newBookRequestResponse) {
                $("#saveNewBookBtn").html("<span><i class='fa fa-spin fa-spinner'></i> Registering... </span>").show();
                $("#saveNewBookBtn").prop("disabled", true);
              },
              success: function(newBookRequestResponse) {
                $("#saveNewBookBtn").prop("disabled", false);
                $("#saveNewBookBtn").html("Save Book").show();
                var status = newBookRequestResponse.status;
                var message = newBookRequestResponse.message;
                var header = newBookRequestResponse.header;
                var responseStatus = newBookRequestResponse.responseStatus;

                // console.log(newBookRequestResponse);
                swal(header, message, responseStatus);

                if (responseStatus === "success") {
                  var currentPage = $('.book-page-link').data('page_number');
                  loadBooks(currentPage);
                  $("#newBookForm")[0].reset();
                }
              },
              error: function(newBookRequestResponse) {
                $("#saveNewBookBtn").html("Save Book").show();
                $("#saveNewBookBtn").prop("disabled", false);
                swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
              },
            });
          }
        );
      } else {
        swal("Check Required Fields", "Require fields cannot be empty", "error");
      }
    });
  </script>
</body>
<!-- END: Body-->

</html>