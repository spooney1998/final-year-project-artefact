<?php
session_start();

if (!isset($_SESSION['userID']) || !isset($_SESSION['userRole'])) { //Correct condition to be adjusted in other projects
  header("location:./");
}

$page = "my-profile";
$_SESSION["previousPage"] = $page;
include("controllers/db_connect.php");

?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Roehampton Library :: My Profile</title>
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
    .overlay {
      background-color: rgba(0, 0, 0, 0.7);
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      position: relative;
      z-index: 1;
    }

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
              <h4 class="mb-0">Past Reads</h4>
            </div>

            <ol class="breadcrumb bg-transparent align-self-center m-0 p-0">
              <li class="breadcrumb-item active"><a href="pastRead">Past Reads</a></li>
              <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
            </ol>
          </div>
        </div>
      </div>
      <!-- END: Breadcrumbs-->

      <!-- START: Card Data-->
      <form id="modifyUserForm">
        <div class="row">
          <div class="col-12 mt-3">
            <div class="position-relative">
              <div class="background-image-maker py-5"></div>
              <div class="holder-image">
                <img src="<?= $imagePath; ?>" alt="" class="img-fluid d-none">
              </div>
              <div class="position-relative px-3 py-5 overlay">
                <div class="media d-md-flex d-block">
                  <a href="javascript:void(0);"><img src="<?= $imagePath; ?>" id="userImagePreview" style="width: 90px;height:90px" alt="Profile Image" class="img-fluid rounded-circle"></a>
                  <div class="media-body z-index-1">
                    <div class="pl-4">
                      <h1 class="display-4 text-uppercase text-white mb-0"><?= $getUserInfo['name']; ?></h1>
                      <h6 class="text-uppercase text-white mb-0"><?= $getUserInfo['role'] . "/" . $getUserInfo['email']; ?></h6>
                    </div>

                  </div>
                </div>
                <div class="col-12 z-index-1">
                  <label style="float:right" for="userImage" class="file-upload btn btn-info btn-sm px-4 rounded-pill shadow"><i class="fa fa-upload mr-2"></i>Change Profile Image<input id="userImage" onchange="previewImage(event);" name="userImage" type="file">
                  </label>
                </div>
              </div>
            </div>
            <div class="profile-menu mt-4 theme-background border  z-index-1 p-2">
              <div class="d-sm-flex">
                <div class="align-self-center">
                  <ul class="nav nav-pills flex-column flex-sm-row" id="myTab" role="tablist">
                    <li class="nav-item ml-0">
                      <a class="nav-link  py-2 px-4 px-lg-4 active" data-toggle="tab" href="#modify"> Modify Profile</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-content">

          <!--MODIFY PROFILE-->
          <div id="modify" class="tab-pane fade in active">
            <div class="row mt-3">

              <!--User Info Inputs-->
              <div class="col-12 col-md-8">
                <div class="card">
                  <div class="card-content">
                    <div class="card-body py-5">
                      <div class="row">
                        <div class="form-group col-sm-6">
                          <div class="input-group">
                            <label class="col-12" for="modifyUserFname">Full Name <span class="text-danger">*</span>
                              <input type="text" class="form-control" id="userFname" name="name" value="<?php echo $getUserInfo['name']; ?>" required="" />
                              <input type="hidden" name="userID" id="userID" value="<?= $getUserInfo['userID']; ?>" />
                              <input type="hidden" name="userRole" id="userRole" value="<?= $getUserInfo['role']; ?>" />
                            </label>
                          </div>
                        </div>
                        <div class="form-group col-sm-6">
                          <div class="input-group">
                            <label class="col-12" for="modifyUserLname">username <span class="text-danger">*</span>
                              <input type="text" class="form-control" id="userLname" name="username" value="<?php echo $getUserInfo['username']; ?>" required="" />
                            </label>
                          </div>
                        </div>
                        <div class="form-group col-sm-6">
                          <div class="input-group">
                            <label class="col-12" for="modifyUserEmail">Email Address <span class="text-danger">*</span>
                              <input type="text" class="form-control" id="userEmail" name="email" value="<?php echo $getUserInfo['email']; ?>" required="" readonly="" style="text-transform:capitalize" />
                            </label>
                          </div>
                        </div>
                        <div class="form-group col-sm-6">
                          <div class="input-group">
                            <label class="col-12" for="userPhone">Phone Number <span class="text-danger">*</span>
                              <input type="text" class="form-control" id="userPhone" name="phone" value="<?php echo $getUserInfo['phone']; ?>" required="" maxlength="13" />
                            </label>
                          </div>
                        </div>
                        <div class="form-group col-sm-6">
                          <div class="input-group">
                            <label class="col-12" for="userPassword">Current Password <span class="text-danger">*</span>
                              <input type="password" class="form-control text-primary" id="myCurrentPassword" name="myCurrentPassword" style="font-weight:800" required="" placeholder="Enter your current password to save changes" autocomplete="off" />
                            </label>
                          </div>
                        </div>
                        <div class="form-group col-sm-6">
                          <div class="input-group">
                            <label class="col-12" for="userPassword">New Password
                              <input type="password" class="form-control" id="userPassword" name="password" placeholder="Enter new password" />
                              <input type="hidden" class="form-control" id="currentPassword" name="currentPassword" value="<?php echo $getUserInfo['password']; ?>" />
                            </label>
                          </div>
                        </div>
                        <div class="form-group col-sm-6">
                          <div class="input-group">
                            <label class="col-12" for="ipAccess">Last IP Access
                              <input class="form-control text-danger" id="ipAccess" value="<?php echo $getUserInfo['lastDeviceIP']; ?>" readonly style="font-weight:700" />
                            </label>
                          </div>
                        </div>
                        <div class="form-group col-sm-12">
                          <div class="input-group">
                            <label class="col-12" for="biography">Resident Address
                              <textarea rows="5" class="form-control" id="address" name="address" required=""><?php echo $getUserInfo['address'] ?></textarea>
                            </label>
                          </div>
                          <center style="margin: 0px auto;">
                            <span id="userMsg"></span>
                          </center>
                          <div>&nbsp;</div>
                          <button type="submit" class="btn btn-primary" id="saveUser" style="float:right">Save Changes</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--User Info Inputs-->

              <!--PROFILE IMAGE Panel-->
              <div class="col-12 col-md-4">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Profile Image</h4>
                  </div>
                  <div class="card-body p-0">
                    <center>
                      <a href="javascript:void();"><img src="<?= $imagePath; ?>" class="img-fluid" style="max-height:30rem"></a>
                    </center>
                  </div>
                </div>
              </div>
              <!--PROFILE IMAGE Panel-->
            </div>
          </div>
          <!--MODIFY PROFILE-->
        </div>
      </form>
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

  <script src="js/core.js"></script>
  <script src="js/md5.js"></script>

  <script>
    $(document).ready(function() {
      // Restricts input for the given textbox to the given inputFilter, starts.
      function setInputFilter(textbox, inputFilter) {
        ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
          textbox.oldValue = "";
          textbox.addEventListener(event, function() {
            if (inputFilter(this.value)) {
              this.oldValue = this.value;
              this.oldSelectionStart = this.selectionStart;
              this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
              this.value = this.oldValue;
              this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
          });
        });
      }
      // Restrict input to digits and '.' by using a regular expression filter.
      setInputFilter(document.getElementById("userPhone"), function(value) {
        return /^\d*$/.test(value)
      });
    });

    // PREVIEW USER IMAGE ONCE ITS SELECTED STARTS
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
        const imagePreviewElement = document.querySelector("#userImagePreview");
        /**
         * Assign the path to the image preview element.
         */
        imagePreviewElement.src = imageSrc;
        /**
         * Show the element by changing the display value to "block".
         */
        //			imagePreviewElement.style.display = "block";
        imagePreviewElement.style.display = "";
      }
    };
    // PREVIEW USER IMAGE ONCE ITS SELECTED ENDS


    $(document).ready(function() {
      $("#saveUser").prop("disabled", true);
      //verify my current password >>>start
      $("#myCurrentPassword").on("input keyup", function() {

        if ($("#myCurrentPassword").val().length > 3) {
          var myCurrentPassword = CryptoJS.MD5($("#myCurrentPassword").val());
          var currentPassword = $("#currentPassword").val();

          if (myCurrentPassword == currentPassword) {
            $("#saveUser").show();
            $("#saveUser").prop("disabled", false);
            $("#userMsg").html("Current Password Confirmed <span class='fa fa-check'></span>").css('color', '#071688').show(); //returning confirm msg

            //NOTIFICATION ->>STARS
            swal("Confirmed!", "Current Password Confirmed,You May Continue!", "success");
            //NOTIFICATION ->>ENDS

          } else {
            $("#saveUser").hide();
            $("#saveUser").prop("disabled", true);
            $("#userMsg").hide();
          }

          setTimeout(function() {
            $("#userMsg").fadeOut(300);
          }, 3000);
        }
      });
      //verify my current password >>>End

    });


    //UPDATE USER FORM STARTS
    $("#modifyUserForm").submit(function(e) {
      e.preventDefault();
      var modifyUserForm = new FormData($("#modifyUserForm")[0]);

      modifyUserForm.append("modifyUserInfo", true);
      $.ajax({
        type: 'POST',
        url: "controllers/get-users",
        async: true,
        processData: false,
        contentType: false,
        // mimeType: 'multipart/form-data',
        // cache: false, // To unable request pages to be cached
        data: modifyUserForm,
        beforeSubmit: function(response) {
          $("#userMsg").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
        },
        success: function(response) {
          var status = response.status;
          var message = response.message;
          var header = response.header;
          var responseStatus = response.responseStatus;

          swal(header, message, responseStatus);

        },
        error: function(response) {
          $("#userMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
        }
      });

      setTimeout(function() {
        $("#userMsg").hide();
      }, 3000);
    });
    //UPDATE USER FORM ENDS
  </script>

</body>
<!-- END: Body-->

</html>