<?php
session_start();

if (isset($_SESSION['userID']) && isset($_SESSION['userRole'])) { //Correct condition to be adjusted in other projects
  header("location:./dashboard");
}
?>

<!DOCTYPE html>
<html lang="en">
<!-- START: Head-->

<head>
  <meta charset="UTF-8">
  <title>Roehampton Library</title>
  <link rel="icon" href="images/roe.png" type="icon/png">
  <meta name="viewport" content="width=device-width,initial-scale=1">


  <!-- START: Template CSS-->
  <link rel="stylesheet" href="dist/vendors/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.min.css">
  <link rel="stylesheet" href="dist/vendors/jquery-ui/jquery-ui.theme.min.css">
  <link rel="stylesheet" href="dist/vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="dist/vendors/flags-icon/css/flag-icon.min.css">
  <!-- END Template CSS-->

  <!-- START: Page CSS-->
  <link rel="stylesheet" href="dist/vendors/chartjs/Chart.min.css">
  <!-- END: Page CSS-->

  <!-- START: Page CSS-->
  <link rel="stylesheet" href="dist/vendors/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="dist/vendors/ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/vendors/sweetalert/sweetalert.css">

  <!-- START: Custom CSS-->
  <link rel="stylesheet" href="dist/css/main.css">
  <!-- END: Custom CSS-->
  <style>
    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      /* Transparent black overlay */
    }

    .lock-image {
      width: 100%;
      height: auto;
      /* Adjust height as needed */
      background-size: cover;
    }

    .lock-image::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      /* Transparent black overlay */
    }

    .text-overlay {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      text-align: center;
      z-index: 1;
      /* Ensure the text is above the overlay */
      width: 80%;
      /* Adjust width as needed */
    }

    .panel {
      display: none;
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
    }

    .panel.active {
      display: flex;
      opacity: 1;
    }
  </style>
</head>
<!-- END Head-->

<!-- START: Body-->

<body id="main-container" class="default" style="background: url(images/library-view-bg.jpg)no-repeat center center fixed;background-size:cover;height:100%;overflow: auto;position:relative">
  <!-- START: Pre Loader-->
  <div class="se-pre-con">
    <img class="loader" src="images/roe.png" alt="">
  </div>
  <!-- END: Pre Loader-->

  <!-- START: Main Content-->
  <div class="container">
    <!-- Login Panel -->
    <div class="row vh-100 justify-content-between align-items-center panel active" id="login-panel">
      <div class="col-12">
        <div class="col-md-12 text-center justify-content-center">
          <h2 class="heading-section"><img src="images/roe.png" style="height: 100px;" /> </h2>
          <h3 style="font-weight:700;color:white;font-size:35px">Roehampton Library</h3>
        </div>
        <form id="userLoginForm" class="row row-eq-height lockscreen  mt-3 mb-5">
          <div class="lock-image col-12 col-sm-5" style="background-image: url('images/mod.jpg')!important; ">
            <div class="text-overlay">
              <!-- Your text goes here -->
              <h2 style="font-weight:800">Login</h2>
              <p>Sign in to your account to access library services.</p>
            </div>
          </div>
          <div class=" login-form col-12 col-sm-7">
            <div class="form-group mb-3">
              <label for="emailaddress">Username/Email address</label>
              <input class="form-control" type="text" id="loginID" name="loginID" required="" placeholder="Enter your username or email">
            </div>

            <div class="form-group mb-3">
              <label for="password">Password</label>
              <input class="form-control" type="password" required="" name="loginPassword" id="loginPassword" placeholder="Enter your password">
            </div>
            <!-- 
            <div class="custom-control">Forget Password?</div> -->
            <div class="form-group mb-3">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="showhide" toggle='#loginPassword'>
                <label class="custom-control-label" for="showhide">Show Password</label>
              </div>
            </div>
            <!-- Login Message -->
            <div id="loginMsg" class="text-center"> </div>
            <div class="form-group mb-0">
              <button class="btn btn-primary" type="submit" id="loginBtn"> Log In </button>
            </div>
            <div class="mt-2">Don't have an account? <a href="javascript:void(0);" onClick="handlePanelSwitch();">Create an Account</a></div>
          </div>
        </form>
      </div>
    </div>
    <!-- Login Panel -->

    <!-- Registration Panel -->
    <div class="row vh-100 justify-content-between align-items-center panel" id="registration-panel">
      <div class="col-12">
        <div class="col-md-12 text-center justify-content-center">
          <h2 class="heading-section"><img src="images/roe.png" style="height: 100px;" /> </h2>
          <h3 style="font-weight:700;color:white;font-size:35px">Roehampton Library</h3>
        </div>
        <form id="registrationForm" class="row row-eq-height lockscreen needs-validation mt-3 mb-5" novalidate>
          <div class="lock-image col-12 col-sm-5" style="background-image: url('images/mod.jpg')!important; ">
            <div class="text-overlay">
              <!-- Your text goes here -->
              <h2 style="font-weight:800">Registration</h2>
              <p>New to the library? Register now to start borrowing books. </p>
            </div>
          </div>
          <div class="login-form col-12 col-sm-7">

            <div class="form-group mb-3">
              <input type="text" class="form-control" name="name" placeholder="Full Name" required="" maxlength="50" />
              <div class="invalid-feedback">Full name is required </div>
            </div>

            <div class="form-group mb-3">
              <input type="text" class="form-control" id="username" name="username" placeholder="Username (No spaces)" required="" maxlength="20" />
              <div class="invalid-feedback" id="username-feedback" style="display:none"> Username must contain only alphanumeric characters without space.</div>
            </div>

            <div class="form-group mb-3">
              <input type="email" class="form-control" name="email" id="email" placeholder="E-mail Address" required="" maxlength="100" />
              <div class="invalid-feedback" id="email-feedback">Valid E-mail is required </div>
            </div>

            <div class="form-group mb-3">
              <input type="text" class="form-control" name="phone" placeholder="Phone Number" required="" maxlength="13" />
              <div class="invalid-feedback"> Phone Number is required </div>
            </div>

            <div class="form-group mb-3">
              <input type="password" class="form-control" id="password" name="password" placeholder="password" required="" />
              <div class="invalid-feedback">Please enter a password.</div>
            </div>
            <div class="form-group mb-3">
              <input type="password" class="form-control" id="confirmPassword" name="password" placeholder="Confirm password" required="" />
              <div class="invalid-feedback">Passwords do not match.</div>
            </div>
            <textarea class="form-control" name="address" placeholder="Enter Resident Address" required=""></textarea>
            <div class="invalid-feedback">Resident Address is required</div>

            <div class="form-group mb-3">
              <div class="form-group mt-3 mb-0">
                <button class="btn btn-primary" type="submit" id="regBtn"> Register </button>
              </div>
            </div>

            <div class="mt-2">Already have an account? <a href="javascript:void(0);" onClick="handlePanelSwitch();">Sign In</a></div>
          </div>
        </form>
      </div>

    </div>
    <!-- Registration Panel -->
  </div>

  <!-- END: Content-->

  <!-- START: Template JS-->
  <script src="dist/vendors/jquery/jquery-3.3.1.min.js"></script>
  <script src="dist/vendors/jquery-ui/jquery-ui.min.js"></script>
  <script src="dist/vendors/moment/moment.js"></script>
  <script src="dist/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/vendors/slimscroll/jquery.slimscroll.min.js"></script>
  <script src="dist/vendors/sweetalert/sweetalert.min.js"></script>
  <!-- END: Template JS-->

  <!-- START: CUSTOM JS-->
  <script src="js/index.js"></script>
  <!-- END: CUSTOM JS-->
</body>
<!-- END: Body-->

</html>