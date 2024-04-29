<?php
session_start();

if (!isset($_SESSION['userID']) || !isset($_SESSION['userRole'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "favorite-books";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Roehampton Library :: Favorite Books</title>
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
  <link rel="stylesheet" href="dist/vendors/chartjs/Chart.min.css">
  <!-- END: Page CSS-->

  <!-- START: Page CSS-->
  <link rel="stylesheet" href="dist/vendors/morris/morris.css">
  <link rel="stylesheet" href="dist/vendors/weather-icons/css/pe-icon-set-weather.min.css">
  <link rel="stylesheet" href="dist/vendors/chartjs/Chart.min.css">
  <link rel="stylesheet" href="dist/vendors/starrr/starrr.css">
  <link rel="stylesheet" href="dist/vendors/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="dist/vendors/ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/vendors/jquery-jvectormap/jquery-jvectormap-2.0.3.css">
  <!-- END: Page CSS-->

  <!-- START: Custom CSS-->
  <link rel="stylesheet" href="dist/css/main.css">
  <!-- END: Custom CSS-->
  <style>
    /* Skeleton Loader Styles */
    .skeleton-loader {
      animation: shimmer 1.5s infinite linear;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      width: 100%;
      height: 80px;
      margin: 0 15px;
      border-radius: 4px;
      display: inline-block;
    }

    .modal-skeleton-loader {
      animation: shimmer 1.5s infinite linear;
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200% 100%;
      width: 100%;
      height: 300px;
      border-radius: 4px;
      display: flex;
    }

    /* Additional styling for better visual separation between cells */
    .skeleton-loader+.skeleton-loader {
      margin-top: 0;
    }


    @keyframes shimmer {
      0% {
        background-position: -200% 0;
      }

      100% {
        background-position: 200% 0;
      }
    }
  </style>
</head>
<!-- END Head-->

<!-- START: Body-->

<body id="main-container" class="default">
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
        <div class="col-12  align-self-center">
          <div class="sub-header mt-3 py-3 align-self-center d-sm-flex w-100 rounded">
            <div class="w-sm-100 mr-auto">
              <h4 class="mb-0">Favorite Books</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item active"><a href="favorite">Favorite Books</a></li>
              <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
            </ol>
          </div>
        </div>
      </div>
      <!-- END: Breadcrumbs-->

      <!-- START: Card Data-->
      <!-- <div class="row">
                <div class="card card-body col-12">
                    <div class="alert-warning h-200 text-center">
                        <i class="fa fa-cog fa-spin h1 mt-3"></i>
                        <h3 cal>Module Maintenance</h3>
                        <p>This page is currently under module maintenance and might take a while to finish. </p>
                        <p>Kindly contact <b>ICT Support</b> for more information on this update.</p>
                    </div>

                </div>
            </div> -->

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <input type="search" id="favoriteSearchEntry" class="form-control mt-4" placeholder="Search For Favorites" />
              <!-- ******display table data -->
              <div class="card-body" id="displayFavorites"> </div>
              <!-- ******display table data -->
            </div>
          </div>
        </div>
      </div>
      <!-- END: Card DATA-->
    </div>
  </main>
  <!-- END: Content-->



  <!-- START: Footer-->
  <footer>
    <?php include("inc/footer.php"); ?>
  </footer>
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
  <script src="dist/js/app.js"></script>
  <!-- END: APP JS-->

  <script>
    $(document).ready(function() {
      loadFavorites(1);
    });

    //LOAD FAVORITES FUNCTION>>>>>>STARTS
    function loadFavorites(page, query = '') {
      //var sortStatus = $("#sortStatus").val();
      //var favoritePageLimit = $("#paymentPageLimit").val();

      $.ajax({
        url: "controllers/get-favorite-books",
        method: "POST",
        async: true,
        data: {
          showFavorites: 1,
          query: query,
          page: page
        },
        beforeSend: function() {
          $("#displayFavorites").html("").show();
          showFavoritesSkeletonLoader();
        },
        success: function(dFav) {
          setTimeout(function() {
            $("#displayFavorites").html(dFav).show();

            $('.skeleton-loader').remove(); // Remove skeleton loader
          }, 1000);
        },
        error: function(dFav) {
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          setTimeout(function() {
            loadFavorites(page);
          }, 5000);
        }
      });
    };

    $(document).on('click', '.favorite-page-link', function() {
      var page = $(this).data('page_number');
      var query = $('#favoriteSearchEntry').val();
      loadFavorites(page, query);
    });

    $('#favoriteSearchEntry').on('keyup change paste', function() {
      var query = $('#favoriteSearchEntry').val();
      loadFavorites(1, query);

    });

    $("#favoritePageLimit").on("change", function() { //page limit
      loadFavorites(1);
    });

    // Skeleton Loader
    function showFavoritesSkeletonLoader() {
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
        $('#displayFavorites').append(skeletonHtml);
      }
    }
    //LOAD FAVORITES FUNCTION>>>>>>ENDS

    //LOAD FAVORITE CONTENT MODAL FUNCTION >>>>STARTS
    function loadFavoritesEditModal(button) {
      var favoriteID = $(button).data("value");
      // console.log(favoriteID);
      $.ajax({
        url: 'controllers/get-favorite-books',
        method: 'POST',
        async: true,
        data: {
          getFavoritesEdit: 1,
          favoriteID: favoriteID
        },
        beforeSend: function(gRcs) {
          // $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
          $('#displayFavoritesInputs').html("").show();
          modalSkeletonLoader();
        },
        success: function(gRcs) {
          $("#favoritesEditModal").modal('show');
          setTimeout(function() {
            $('#displayFavoritesInputs').html(gRcs).show();
            $('.modal-skeleton-loader').remove(); // Remove skeleton loader
          }, 1000);
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
        $('#displayFavoritesInputs').append(skeletonHtml);
      }
    }
    //LOAD FAVORITE CONTENT MODAL FUNCTION >>>>ENDS
  </script>

</body>
<!-- END: Body-->

</html>