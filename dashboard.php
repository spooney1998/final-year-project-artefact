<?php
session_start();

if (!isset($_SESSION['userID']) || !isset($_SESSION['userRole'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "dashboard";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Roehampton Library :: Dashboard</title>
  <link rel="icon" href="images/roe.png" type="icon/png">
  <meta name="viewport" content="width=device-width,initial-scale=1">


  <!-- START: Template CSS-->
  <link rel="stylesheet" href="dist/vendors/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.theme.min.css">
  <link rel="stylesheet" href="dist/vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="dist/vendors/flags-icon/css/flag-icon.min.css">
  <link rel="stylesheet" href="dist/vendors/fancybox/jquery.fancybox.min.css">
  <!-- END Template CSS-->

  <!-- START: Page CSS-->
  <link rel="stylesheet" href="dist/vendors/chartjs/Chart.min.css">
  <link rel="stylesheet" href="dist/vendors/lineprogressbar/jquery.lineProgressbar.min.css">
  <!-- END: Page CSS-->

  <!-- START: Page CSS-->
  <link rel="stylesheet" href="dist/vendors/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="dist/vendors/ionicons/css/ionicons.min.css">

  <!-- START: Custom CSS-->
  <link rel="stylesheet" href="dist/css/main.css">
  <!-- END: Custom CSS-->
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
    <div class="container-fluid site-width ">
      <!-- START: Breadcrumbs-->
      <div class="row ">
        <div class="col-12 align-self-center">
          <div class="sub-header mt-3 py-3 align-self-center d-sm-flex w-100 rounded">
            <div class="w-sm-100 mr-auto">
              <h4 class="mb-0"><?php echo ucfirst((isset($username) && !empty($username)) ? "Welcome Back, " . ucfirst($username) : "Dashboard"); ?></h4>
              <p>Check out how you are doing!</p>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <?php //echo date("D, d-M-Y"); 
              ?><br />
              <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li> -->
              <div class="card border-bottom-0 mt-3 mt-sm-0">
                <div class="card-content border-bottom border-primary border-w-5">

                  <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
                    <div class="card-body p-4">

                      <span class="mb-0 font-w-600 text-primary">
                        <h6> <i class="fas fa-book"></i> Total Books</h6>
                      </span>
                      <?php
                      $stmt = $conn->prepare("SELECT COUNT(*) FROM library_books");
                      $stmt->execute() or die($stmt->error);
                      $stmt->bind_result($totalBooks);
                      $stmt->fetch();
                      $stmt->close();
                      ?>
                      <h4 class="mb-0 font-w-800 tx-s-20"><?= number_format($totalBooks); ?></h4>
                    </div>
                  <?php } elseif ($_SESSION['userRole'] == "user") { ?>

                    <div class="card-body p-4">

                      <span class="mb-0 font-w-600 text-primary">
                        <h6> <i class="fas fa-book"></i> Late Return Fine</h6>
                      </span>
                      <h4 class="mb-0 font-w-800 tx-s-20">&#x00A3;<?= ((!empty($getUserInfo['fine'])) ? number_format($getUserInfo['fine']) : '0'); ?></h4>
                      <button class="btn btn-primary btn-sm text-center mt-2" type="button" <?= ((empty($getUserInfo['fine']) || $getUserInfo['fine'] == 0) ? 'disabled' : ''); ?> value="<?= ((!empty($getUserInfo['fine'])) ? number_format($getUserInfo['fine']) : '0'); ?>" onclick="handlePayFine();">Pay <?= ((!empty($getUserInfo['fine']) && $getUserInfo['fine'] > 0) ? '&#x00A3;' . number_format($getUserInfo['fine']) : ''); ?> Fine</button>
                    </div>
                  <?php } else {
                    echo date("D, d-M-Y")
                  ?><br />
                    <!-- <li class="breadcrumb-item"><a href="#">Home</a></li> -->
                    <li class="breadcrumb-item active">Dashboard</li>
                  <?php } ?>
                </div>
              </div>
          </div>
          </ol>
        </div>
      </div>
    </div>
    <!-- END: Breadcrumbs-->

    <!-- START: Card Data-->

    <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
      <div class="row">
        <!-- Available Books -->
        <div class="col-12 col-sm-6 col-xl-4 mt-3">
          <div class="card">
            <div class="card-body p-0">
              <div class='p-4 align-self-center'>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM library_books WHERE status='available' AND quantity > 0");
                $stmt->execute() or die($stmt->error);
                $stmt->bind_result($availableBooks);
                $stmt->fetch();
                $stmt->close();

                //Calculate percentage against total books
                $availablePercentage = (($availableBooks > 0) ? ($availableBooks / $totalBooks) * 100 : 0);
                ?>
                <h2><?= number_format($availableBooks); ?></h2>
                <h6 class="card-liner-subtitle">Available Books</h6>
              </div>
              <div class="barfiller" data-color="#03883c">
                <div class="tipWrap">
                  <span class="tip rounded primary">
                    <span class="tip-arrow"></span>
                  </span>
                </div>
                <span class="fill" data-percentage="<?= round($availablePercentage); ?>"></span>
              </div>
            </div>
          </div>
        </div>
        <!-- Borrowed Books -->
        <div class="col-12 col-sm-6 col-xl-4 mt-3">
          <div class="card">
            <div class="card-body p-0">
              <?php
              $stmt = $conn->prepare("SELECT COUNT(*) FROM library_borrowings WHERE returnDate =''");
              $stmt->execute() or die($stmt->error);
              $stmt->bind_result($borrowedBooks);
              $stmt->fetch();
              $stmt->close();

              //Calculate percentage against total books
              $borrowedPercentage = (($borrowedBooks > 0) ? ($borrowedBooks / $totalBooks) * 100 : 0);
              ?>
              <div class='p-4 align-self-center'>
                <h2><?= number_format($borrowedBooks); ?></h2>
                <h6 class="card-liner-subtitle">Borrowed Books</h6>
              </div>
              <div class="barfiller" data-color="#17a2b8">
                <div class="tipWrap">
                  <span class="tip rounded info">
                    <span class="tip-arrow"></span>
                  </span>
                </div>
                <span class="fill" data-percentage="<?= round($borrowedPercentage); ?>"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Reserved Books -->
        <div class="col-12 col-sm-6 col-xl-4 mt-3">
          <div class="card">
            <div class="card-body p-0">
              <?php
              $stmt = $conn->prepare("SELECT COUNT(*) FROM library_reservations");
              $stmt->execute() or die($stmt->error);
              $stmt->bind_result($reservedBooks);
              $stmt->fetch();
              $stmt->close();

              //Calculate percentage against total books
              $reservedPercentage = (($reservedBooks > 0) ? ($reservedBooks / $totalBooks) * 100 : 0);
              ?>
              <div class='p-4 align-self-center'>
                <h2><?= number_format($reservedBooks); ?></h2>
                <h6 class="card-liner-subtitle">Reserved Books</h6>
              </div>
              <div class="barfiller" data-color="#1ee0ac">
                <div class="tipWrap">
                  <span class="tip rounded success">
                    <span class="tip-arrow"></span>
                  </span>
                </div>
                <span class="fill" data-percentage="<?= round($reservedPercentage); ?>"></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Bar Chart for the analytical view of the book entries -->
        <div class="col-12 col-lg-8  mt-3">
          <div class="card">
            <div class="card-content">
              <div class="card-body">
                <div id="apex_analytic_chart" class="height-500"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Most borrowed Books By Category -->
        <div class="col-12 col-md-6 col-lg-4 mt-3">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h6 class="card-title">Most Borrowed Books By Categories</h6>
            </div>
            <div class="card-content">
              <div class="card-body p-0">
                <ul class="list-group list-unstyled">
                  <?php
                  // Initialize an array to store the count of borrowed books for each category
                  $categoryCounts = array();

                  // Prepare the SQL query to fetch the count of borrowed books for each category
                  $sql = "SELECT bb.category, COUNT(*) AS count
                FROM library_borrowings lb
                LEFT JOIN library_books bb ON lb.bookID = bb.id
                GROUP BY bb.category
                ORDER BY count DESC
                LIMIT 4";

                  // Prepare the statement
                  $stmt = $conn->prepare($sql);
                  if ($stmt === false) {
                    die("Error in preparing statement: " . $conn->error);
                  }

                  // Execute the statement
                  if (!$stmt->execute()) {
                    die("Error in executing statement: " . $stmt->error);
                  }

                  // Bind the result variables
                  $stmt->bind_result($category, $count);

                  $totalBorrowedBooks = 0;

                  // Fetch the results
                  while ($stmt->fetch()) {
                    // Store the count of borrowed books for each category in the array
                    $categoryCounts[$category] = $count;

                    $totalBorrowedBooks += $count;
                  }

                  // Close the statement
                  $stmt->close();

                  // Array of color classes
                  $colors = ['success', 'warning', 'info', 'danger'];
                  $colorIndex = 0;
                  $dataColors = ['#1ee0ac', '#ffc107', '#17a2b8', '#f64e60'];
                  $dataColorIndex = 0;

                  // Output the results
                  foreach ($categoryCounts as $category => $count) {
                    //Calculate percentage against total borrowed books
                    $categoryPercentage = ($totalBorrowedBooks > 0) ? ($count / $totalBorrowedBooks) * 100 : 0;

                    $cssClass = $colors[$colorIndex];
                    $dataColor = $dataColors[$dataColorIndex];
                    $colorIndex++;
                    $dataColorIndex++;
                    if ($colorIndex >= count($colors)) {
                      $colorIndex = 0;
                      $dataColorIndex  = 0;
                    }
                  ?>
                    <li class="p-4 border-bottom">
                      <div class="w-100">
                        <?= $category; ?>
                        <div class="barfiller h-7 rounded" data-color="<?= $dataColor; ?>">
                          <div class="tipWrap">
                            <span class="tip rounded <?= $cssClass; ?>">
                              <span class="tip-arrow"></span>
                            </span>
                          </div>
                          <span class="fill" data-percentage="<?= round($categoryPercentage); ?>"></span>
                        </div>
                      </div>
                    </li>
                  <?php } ?>

                </ul>
              </div>
            </div>
          </div>
        </div>

      </div>

    <?php } elseif ($_SESSION['userRole'] == "user") { ?>
      <div class="row m-2">
        <!-- Borrowed Books-->
        <div class="col-12 col-sm-6 col-xl-3 mt-3">
          <div class="card">
            <div class="card-body text-danger border-bottom border-danger border-w-5">
              <div class='p-4 align-self-center'>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM library_borrowings WHERE userID=?");
                $stmt->bind_param("s", $getUserInfo['userID']);
                $stmt->execute() or die($stmt->error);
                $stmt->bind_result($borrowedBooks);
                $stmt->fetch();
                $stmt->close();
                ?>
                <h2 class="text-center"><?= number_format($borrowedBooks); ?></h2>
                <h6 class="text-center">Borrowed Books</h6>
              </div>
            </div>
          </div>
        </div>

        <!-- Returned Books-->
        <div class="col-12 col-sm-6 col-xl-3 mt-3">
          <div class="card">
            <div class="card-body text-primary border-bottom border-primary border-w-5">
              <div class='p-4 align-self-center'>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM library_borrowings WHERE userID=? AND returnDate !=''");
                $stmt->bind_param("s", $getUserInfo['userID']);
                $stmt->execute() or die($stmt->error);
                $stmt->bind_result($returnedBooks);
                $stmt->fetch();
                $stmt->close();
                ?>
                <h2 class="text-center"><?= number_format($returnedBooks); ?></h2>
                <h6 class="text-center">Returned Books</h6>
              </div>
            </div>
          </div>
        </div>

        <!-- Favorite Books -->
        <div class="col-12 col-sm-6 col-xl-3 mt-3">
          <div class="card">
            <div class="card-body text-info border-bottom border-info border-w-5">
              <div class='p-4 align-self-center'>

                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM library_favorite_books WHERE userID=?");
                $stmt->bind_param("s", $getUserInfo['userID']);
                $stmt->execute() or die($stmt->error);
                $stmt->bind_result($favoriteBooks);
                $stmt->fetch();
                $stmt->close();
                ?>
                <h2 class="text-center"><?= number_format($favoriteBooks); ?></h2>
                <h6 class="text-center">Favorite Books</h6>
              </div>
            </div>
          </div>
        </div>

        <!-- Reserved Books -->
        <div class="col-12 col-sm-6 col-xl-3 mt-3">
          <div class="card">
            <div class="card-body text-warning border-bottom border-warning border-w-5">
              <div class='p-4 align-self-center'>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM library_reservations WHERE userID=?");
                $stmt->bind_param("s", $getUserInfo['userID']);
                $stmt->execute() or die($stmt->error);
                $stmt->bind_result($reservedBooks);
                $stmt->fetch();
                $stmt->close();
                ?>
                <h2 class="text-center"><?= number_format($reservedBooks); ?></h2>
                <h6 class="text-center">Reserved Books</h6>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Books -->
        <div class="col-12 col-lg-9 mt-3">
          <div class="card">
            <h4 class="mt-3 mb-0 ml-3">Recent Books</h4>
            <div class="card-body">
              <div class="row">
                <?php
                $stmt = $conn->prepare("SELECT * FROM library_books ORDER BY addedDate DESC LIMIT 4");
                $stmt->execute() or die($stmt->error);
                $getResult = $stmt->get_result();

                if ($stmt->num_rows < 1) {
                  while ($result = $getResult->fetch_array()) {
                    // $cardType = $cardType[array_rand($cardType)]; // Randomly select a card type

                    $coverImage = "resources/" . $result['coverImage'];
                    $coverImage = ((empty($result['coverImage']) || file_exists($coverImage)) ? $coverImage  : "images/no-preview.jpeg");
                ?>
                    <!--Books item-->
                    <div class="col-12 col-md-6 col-lg-3 my-3 note personal all starred">
                      <div class="position-relative" style="height: 16rem; overflow: hidden;">
                        <img src="<?= $coverImage; ?>" style="width: 100%; height: auto;" alt="" class="img-fluid">
                        <div class="caption-bg fade bg-transparent text-right">
                          <div class="d-table w-100 h-100 ">
                            <div class="d-table-cell align-bottom">
                              <!-- <div class="mb-3">
                            <a href="javascript:void(0);" class=" rounded-left bg-white px-3 py-2 shadow2"><i class="icon-heart"></i></a>
                          </div> -->
                              <div class="mb-4">
                                <a data-fancybox-group="gallery" href="<?= $coverImage; ?>" class=" fancybox rounded-left bg-white px-3 py-3 shadow2"><i class="fas fa-search"></i></a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card" style="cursor: pointer;">
                        <div class="card-content">
                          <div class="card-body p-4" style="height:12rem">
                            <p class="mb-2 clamp-1"><a href="books" class="font-weight-bold text-primary"><?= ucfirst($result['title']); ?>
                                (<?= ucfirst($result['category']); ?>)</a></p>
                            <!-- <div class="d-inline-block text-danger pl-2">$285.00</div> -->
                            <p class="font-w-500 tx-s-12"><i class="icon-calendar"></i> <span class="note-date"> June 14th, 2020 (Next
                                Availability)</span></p>
                            <div class="note-content mb-4">
                              <p class="clamp-2"><?= $result['description']; ?></p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php }
                  $stmt->close(); ?>
              </div>
            </div>
          <?php } else { ?>
            <div class="col-12 alert alert-danger mt-5 ml-4 text-center" style="width:100% !important" style="margin: 0 auto">
              <h3><i class="fas fa-exclamation-triangle"></i></h3>
              <p>There are no recent Books at the moment</p>
            </div>
          <?php } ?>
          </div>
        </div>

        <!-- Recent Borrows -->
        <div class="col-12 col-md-6 col-lg-3 mt-3">
          <div class="card  overflow-hidden">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h6 class="card-title">Recent Borrows</h6>
            </div>
            <div class="card-content">
              <div class="card-body p-0">

                <?php
                $stmt = $conn->prepare("SELECT * FROM library_borrowings bb JOIN library_books b ON b.id = bb.bookID WHERE userID=?");
                $stmt->bind_param("s", $getUserInfo['userID']);
                $stmt->execute() or die($stmt->error);
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                  while ($getMyBorrowings = $result->fetch_array()) {

                    $coverImage = "resources/" . $getMyBorrowings['coverImage'];
                    $coverImage = ((empty($getMyBorrowings['coverImage']) || file_exists($coverImage)) ? $coverImage  : "images/no-preview.jpeg");
                ?>
                    <ul class="list-group list-unstyled">
                      <li class="p-2 border-bottom zoom">
                        <div class="media d-flex w-100">
                          <a href="books"><img src="<?= $coverImage; ?>" alt="" class="img-fluid ml-0 mt-2  rounded-square" width="40"></a>
                          <div class="media-body align-self-center pl-2">
                            <span class="mb-0 font-w-600"><?= $getMyBorrowings['title'] . " (" . $getMyBorrowings['subCategory'] . ")"; ?></span><br>
                            <p class="mb-0 font-w-500 tx-s-12">By <?= $getMyBorrowings['author']; ?></p>
                            <p class="mb-0 font-w-500 tx-s-12">Borrowed on: <?= date("d-m-Y, h:i A", strtotime($getMyBorrowings['borrowedDate'])); ?></p>
                            <p class="mb-0 font-w-500 tx-s-12"> <?= (!empty($getMyBorrowings['returnDate'])) ? '<span class="text-primary">Returned on: ' . date("d-m-Y, h:i A", strtotime($getMyBorrowings['returnDate'])) . '</span>' : '<span class="text-danger">Not returned</span>'; ?></p>
                          </div>
                        </div>
                      </li>
                    <?php }
                } else { ?>
                    <div class="col-12 alert alert-danger mt-5 m-2 text-center" style="width:100% !important" style="margin: 0 auto">
                      <h3><i class="fas fa-exclamation-triangle"></i></h3>
                      <p>You do not have a recent borrow at the moment</p>
                    </div>
                  <?php } ?>
                    </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php  } ?>
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
  <!-- END: Template JS-->

  <!-- START: APP JS-->
  <!-- <script src="dist/js/app.js"></script> -->
  <!-- END: APP JS-->

  <!-- START: Page Vendor JS-->
  <script src="dist/vendors/fancybox/jquery.fancybox.min.js"></script>
  <script src="dist/vendors/apexcharts/apexcharts.min.js"></script>
  <script src="dist/vendors/lineprogressbar/jquery.lineProgressbar.js"></script>
  <script src="dist/vendors/lineprogressbar/jquery.barfiller.js"></script>
  <!-- END: Page Vendor JS-->

  <!-- START: Page JS-->
  <!-- <script src="dist/js/home.script.js"></script> -->
  <!-- END: Page JS-->
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

    $(window).on("load", function() {
      // Animate loader off screen
      $(".se-pre-con").fadeOut("slow");
    });
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


    /////////////////////////////////// Analytic Chart /////////////////////

    var primarycolor = getComputedStyle(document.body).getPropertyValue('--primarycolor');
    var bordercolor = getComputedStyle(document.body).getPropertyValue('--bordercolor');
    var bodycolor = getComputedStyle(document.body).getPropertyValue('--bodycolor');
    var theme = 'light';
    if ($('body').hasClass('dark')) {
      theme = 'dark';
    }
    if ($('body').hasClass('dark-alt')) {
      theme = 'dark';
    }

    if ($("#apex_analytic_chart").length > 0) {
      options = {
        theme: {
          mode: theme
        },
        chart: {
          height: 350,
          type: 'bar',
        },
        responsive: [{
          breakpoint: 767,
          options: {
            chart: {
              height: 220
            }
          }
        }],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        colors: ['#03883c', '#17a2b8', '#1ee0ac'],
        series: [{
          name: 'Available Books',
          data: [16, 98, 113, 101, 0, 0, 0, 0, 0]
        }, {
          name: 'Borrowed Books',
          data: [12, 48, 88, 75, 0, 0, 0, 0, 0]
        }, {
          name: 'Reserved Books',
          data: [12, 38, 88, 75, 0, 0, 0, 0, 0]
        }],
        xaxis: {
          categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        },
        yaxis: {
          title: {
            text: '(copies)'
          }
        },
        fill: {
          opacity: 1

        },
        tooltip: {
          y: {
            formatter: function(val) {
              return val + " copies"
            }
          }
        }
      }

      var chart = new ApexCharts(
        document.querySelector("#apex_analytic_chart"),
        options
      );
      chart.render();
    }

    //////////////////////////// Stacking /////////////////////////////////
    if ($('.barfiller').length > 0) {
      $(".barfiller").each(function() {
        $(this).barfiller({
          barColor: $(this).data('color')
        });
      });
    }
  </script>
</body>
<!-- END: Body-->

</html>