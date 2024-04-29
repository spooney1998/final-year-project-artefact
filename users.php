<?php
session_start();

if (!isset($_SESSION['userID']) || !isset($_SESSION['userRole'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "users";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Roehampton Library :: Users</title>
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
      height: 20px;
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

  <!-- Role Access -->

  <?php if ($_SESSION['userRole'] != "admin" && $_SESSION['userRole'] != "librarian") {
  ?>
    <script>
      window.location.href = "dashboard";
    </script>
  <?php
  } ?>
  <!-- Role Access -->

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
              <h4 class="mb-0">Users</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item active"><a href="users">Users</a></li>
              <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
            </ol>
          </div>
        </div>
      </div>
      <!-- END: Breadcrumbs-->

      <!-- START: Card Data-->

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class=" card-header justify-content-between align-items-center">
              <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addNewUser" style="float:right"> + Add New User</button>
            </div>
            <div class="card-body">
              <input type="search" id="usersSearchEntry" class="form-control mt-4" placeholder="Search For Users" />
              <!-- ******display table data -->
              <div class="card-body" id="displayUsers"> </div>
              <!-- ******display table data -->
            </div>
          </div>
        </div>
        <!-- Modal Section to Add New User -->
        <div class="modal fade bd-example-modal-lg" id="addNewUser" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel10">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form id="registrationForm" class="needs-validation" novalidate>
                <div class="modal-body">
                  <div class="row mt-3">

                    <!--New User Inputs-->
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
                                      <option value="user">User</option>
                                      <option value="librarian">Librarian</option>
                                    </select>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="name">Full Name<span class="text-danger">*</span>
                                    <input class="form-control" type="text" name="name" id="name" placeholder="Full Name" required="" />
                                    <div class="invalid-feedback">Full name is required </div>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="username">User Name<span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Username (No spaces)" required="" maxlength="20" />
                                    <div class="invalid-feedback" id="username-feedback" style="display:none"> Username must contain only alphanumeric characters without space.</div>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="email">Email Address<span class="text-danger">*</span>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="E-mail Address" required="" maxlength="100" />
                                    <div class="invalid-feedback" id="email-feedback">Valid E-mail is required </div>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="password">Password<span class="text-danger">*</span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="password" required="" />
                                    <div class="invalid-feedback">Please enter a password.</div>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="confirmPassword">Confirm Password<span class="text-danger">*</span>
                                    <input type="password" class="form-control" id="confirmPassword" name="password" placeholder="Confirm password" required="" />
                                    <div class="invalid-feedback">Passwords do not match.</div>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="phone">Phone Number<span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="phone" placeholder="Phone Number" required="" maxlength="13" />
                                    <div class="invalid-feedback"> Phone Number is required </div>
                                  </label>
                                </div>
                              </div>

                              <div class="form-group col-sm-6">
                                <div class="input-group">
                                  <label class="col-12" for="address">Resident Address<span class="text-danger">*</span>
                                    <textarea class="form-control" name="address" placeholder="Enter Resident Address" required=""></textarea>
                                    <div class="invalid-feedback">Resident Address is required</div>
                                  </label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!--New User Inputs-->
                  </div>
                </div>

                <div class="modal-footer">
                  <center style="margin: 0px auto;">
                    <span id="addNewUserMsg"></span>
                  </center>
                  <button class="btn btn-primary" id="regBtn">Save User</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- Modal Section to Add New User -->
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
      loadUsers(1);
    });

    //LOAD USERS FUNCTION>>>>>>STARTS
    function loadUsers(page, query = '') {
      //var sortStatus = $("#sortStatus").val();
      //var usersPageLimit = $("#paymentPageLimit").val();
      var userRole = "user";

      $.ajax({
        url: "controllers/get-users",
        method: "POST",
        async: true,
        data: {
          showUsers: 1,
          query: query,
          page: page,
          userRole: userRole
        },
        beforeSend: function() {
          $("#displayUsers").html("").show();
          showUsersSkeletonLoader();
        },
        success: function(data) {
          setTimeout(function() {
            $("#displayUsers").html(data).show();

            $('.skeleton-loader').remove(); // Remove skeleton loader
          }, 1000);
        },
        error: function(data) {
          swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
          setTimeout(function() {
            loadUsers(page);
          }, 5000);
        }
      });
    };

    $(document).on('click', '.user-page-link', function() {
      var page = $(this).data('page_number');
      var query = $('#usersSearchEntry').val();
      loadUsers(page, query);
    });

    $('#usersSearchEntry').on('keyup change paste', function() {
      var query = $('#usersSearchEntry').val();
      loadUsers(1, query);

    });

    $("#usersPageLimit").on("change", function() { //page limit
      loadUsers(1);
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
						<div  class='skeleton-loader'> </div>
						<div  class='skeleton-loader'> </div>
					</div>
				`;

      for (var i = 0; i < 2; i++) {
        $('#displayUsers').append(skeletonHtml);
      }
    }
    //LOAD USERS FUNCTION>>>>>>ENDS

    //ADD NEW USER FUNCTION>>>>>>STARTS
    $("#addNewUserForm").submit(function(e) {
      e.preventDefault();

      var addUserForm = new FormData($("#addNewUserForm")[0]);
      swal({
          title: "Are you sure to add User?",
          text: "You are about adding a new user to the portal.",
          icon: 'question',
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: 'btn-success',
          cancelButtonClass: 'btn-danger',
          confirmButtonText: 'Continue!',
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
            data: addUserForm,
            beforeSend: function() { // Corrected from beforeSubmit to beforeSend
              $("#saveNewUser").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
            },
            success: function(response) {
              var status = response.status;
              var message = response.message;
              var responseStatus = response.responseStatus;
              var header = response.header;

              if (status === true) {
                $("#addNewUserMsg").html(response).css("color", "green").show();
                $("#addNewUserForm")[0].reset();
                swal(header, message, responseStatus);
              } else {
                swal(header, message, responseStatus);
              }
            },
            error: function() {
              $("#addNewUserMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
              swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
            },
            complete: function() { // Moved the timeout code to the complete callback
              setTimeout(function() {
                $("#addNewUserMsg").fadeOut(300);
              }, 3000);
              $("#saveNewUser").html("Save User").show(); // Reset the button text
            }
          });
        });
    });
    //ADD NEW USER FUNCTION>>>>>>ENDS

    //LOAD USER EDIT FUNCTION >>>>STARTS
    function loadUsersEditModal(button) {
      var userID = $(button).data("value");
      // Necessary for the user image on modal open
      var row = button.closest('tr');
      var passport = row.dataset.passport;
      var modalPassport = document.getElementById('modalUserPassport');
      modalPassport.src = passport;
      // Necessary for the user image on modal open

      //console.log(courseID);
      $.ajax({
        url: 'controllers/get-users',
        method: 'POST',
        async: true,
        data: {
          getUsersEdit: 1,
          userID: userID
        },
        beforeSend: function(gRcs) {
          $(button).html("<span class='fa fa-spin fa-spinner'></span>").show();
          $('#displayUsersInputs').html("").show();
          modalSkeletonLoader();
        },
        success: function(gRcs) {
          $("#usersEditModal").modal('show');
          $(button).html('<span class="fas fa-bars"></span>').show();
          setTimeout(function() {
            $('#displayUsersInputs').html(gRcs).show();
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
        $('#displayUsersInputs').append(skeletonHtml);
        $('#displayAllocatedCoursesInputs').append(skeletonHtml);
      }
    }
    //LOAD USER EDIT FUNCTION >>>>ENDS


    /*==============================================================
          Form Validation 
          ============================================================= */
    var forms = document.getElementsByClassName("needs-validation");
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener(
        "submit",
        function(event) {
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add("was-validated");
        },
        false
      );
    });

    // Add event listeners for password and confirm password fields
    ["password", "confirmPassword"].forEach(function(id) {
      document.getElementById(id).addEventListener("input", function() {
        // Check if both password and confirm password fields are non-empty
        var password = document.getElementById("password").value.trim();
        var confirmPassword = document.getElementById("confirmPassword").value.trim();

        if (password !== confirmPassword) {
          // Passwords do not match, display error message
          document.getElementById("confirmPassword").setCustomValidity("Passwords do not match.");
          $("#regBtn").prop("disabled", true);
        } else {
          // Passwords match, clear any existing error message
          document.getElementById("confirmPassword").setCustomValidity("");
          $("#regBtn").prop("disabled", false);
        }

        // Add Bootstrap's was-validated class to trigger styling for validation
        this.classList.add("was-validated");
      });
    });

    document.getElementById("username").addEventListener("input", function() {
      var username = this.value.trim();
      var usernameFeedback = document.getElementById("username-feedback");

      // Regular expression to match usernames without space
      var usernameRegex = /^[a-zA-Z0-9]*$/;

      if (!usernameRegex.test(username)) {
        // Invalid username format, display error message
        usernameFeedback.style.display = "block";
        this.setCustomValidity("Username must contain only alphanumeric characters without space.");
        $("#regBtn").prop("disabled", true);
      } else {
        // Valid username format, clear any existing error message
        usernameFeedback.style.display = "none";
        this.setCustomValidity("");
        $("#regBtn").prop("disabled", false);
      }
    });

    /*==============================================================
          Verify User Registration Email for duplicates
    ============================================================= */
    $("#email").on("input paste", function() {
      verifyUserEntryEmail();
    });

    function verifyUserEntryEmail() {
      var email = $("#email").val();
      $.ajax({
        type: "POST",
        url: "controllers/userAuth",
        async: true,
        data: {
          userEmailEntryVer: 1,
          email: email,
        },
        success: function(response) {
          var status = response.status;
          var message = response.message;

          if (status === true) {
            $("#email-feedback").html(message).show();
            $("#regBtn").hide();
          } else {
            $("#regBtn").show();
            $("#email-feedback").hide();
          }
        },
        error: function() {
          $("#email-feedback")
            .html(
              "Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>"
            )
            .css("color", "red")
            .show();
          swal(
            "Connectivity Error",
            "Error in connectivity, please check your internet connection and try again",
            "error"
          );
        },
      });
    }
    /*==============================================================
          Verify User Registration Username for duplicates
    ============================================================= */
    $("#username").on("input paste", function() {
      verifyUserEntryUsername();
    });

    function verifyUserEntryUsername() {
      var username = $("#username").val();
      $.ajax({
        type: "POST",
        url: "controllers/userAuth",
        async: true,
        data: {
          userUsernameEntryVer: 1,
          username: username,
        },
        success: function(response) {
          var status = response.status;
          var message = response.message;

          if (status === true) {
            $("#username-feedback").html(message).show();
            $("#regBtn").hide();
          } else {
            $("#regBtn").show();
            $("#username-feedback").hide();
          }
        },
        error: function() {
          $("#username-feedback")
            .html(
              "Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>"
            )
            .css("color", "red")
            .show();
          swal(
            "Connectivity Error",
            "Error in connectivity, please check your internet connection and try again",
            "error"
          );
        },
      });
    }

    /*==============================================================
          Function to register users
          ============================================================= */

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

    $("#registrationForm").submit(function(e) {
      e.preventDefault();
      var formID = "registrationForm";
      var registrationFormId = new FormData($("#registrationForm")[0]);
      var password = $("#password").val();
      var confirmPassword = $("#confirmPassword").val();
      if (password !== confirmPassword) {
        swal("Check Password", "Password does not match", "error");
      } else if (checkFormInputsNotEmpty(formID)) {
        registrationFormId.append("regUser", true);
        swal({
            title: "Are you sure to continue?",
            text: "You are about processing your registration.",
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
              url: "controllers/userAuth",
              type: "POST",
              async: true,
              data: registrationFormId,
              processData: false, // Important when sending FormData
              contentType: false, // Important when sending FormData
              beforeSend: function(lgFx) {
                $("#regBtn").html("<span><i class='fa fa-spin fa-spinner'></i> Registering... </span>").show();
                $("#regBtn").prop("disabled", true);
              },
              success: function(lgFx) {
                $("#regBtn").prop("disabled", false);
                $("#regBtn").html("Register").show();
                var status = lgFx.status;
                var message = lgFx.message;
                var header = lgFx.header;
                var feedbackResponse = lgFx.feedbackResponse;
                var logID = $("#username").val();

                swal(header, message, status);

                if (status === "success") {
                  var currentPage = $('.user-page-link').data('page_number');
                  loadUsers(currentPage);
                  $("#logID").val(logID);
                  $("#registrationForm")[0].reset();
                }
              },
              error: function(lgFx) {
                $("#regBtn").html("Register").show();
                $("#regBtn").prop("disabled", false);
                swal(
                  "Connectivity Error",
                  "Connectivity Error, Check your internet and try again",
                  "error"
                );
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