$(window).on("load", function () {
  // Animate loader off screen
  $(".se-pre-con").fadeOut("slow");
});

/*==============================================================
      Form Validation 
      ============================================================= */
var forms = document.getElementsByClassName("needs-validation");
// Loop over them and prevent submission
var validation = Array.prototype.filter.call(forms, function (form) {
  form.addEventListener(
    "submit",
    function (event) {
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
["password", "confirmPassword"].forEach(function (id) {
  document.getElementById(id).addEventListener("input", function () {
    // Check if both password and confirm password fields are non-empty
    var password = document.getElementById("password").value.trim();
    var confirmPassword = document
      .getElementById("confirmPassword")
      .value.trim();

    if (password !== confirmPassword) {
      // Passwords do not match, display error message
      document
        .getElementById("confirmPassword")
        .setCustomValidity("Passwords do not match.");
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

document.getElementById("username").addEventListener("input", function () {
  var username = this.value.trim();
  var usernameFeedback = document.getElementById("username-feedback");

  // Regular expression to match usernames without space
  var usernameRegex = /^[a-zA-Z0-9]*$/;

  if (!usernameRegex.test(username)) {
    // Invalid username format, display error message
    usernameFeedback.style.display = "block";
    this.setCustomValidity(
      "Username must contain only alphanumeric characters without space."
    );
    $("#regBtn").prop("disabled", true);
  } else {
    // Valid username format, clear any existing error message
    usernameFeedback.style.display = "none";
    this.setCustomValidity("");
    $("#regBtn").prop("disabled", false);
  }
});
/*==============================================================
      Panel Switch Function From Login to Registration Panel
      ============================================================= */
function handlePanelSwitch() {
  var loginPanel = document.getElementById("login-panel");
  var registrationPanel = document.getElementById("registration-panel");

  if (loginPanel.classList.contains("active")) {
    loginPanel.classList.remove("active");
    registrationPanel.classList.add("active");
  } else {
    loginPanel.classList.add("active");
    registrationPanel.classList.remove("active");
  }
}

/*==============================================================
      Function to show and hide login password
      ============================================================= */
$("#showhide").click(function () {
  //$(this).toggleClass("fa-eye fa-eye-slash");
  var input = $($(this).attr("toggle"));
  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});

/*==============================================================
          Function to register users
============================================================= */

//Function to check if required input is not empty before submitting form
function checkFormInputsNotEmpty(formID) {
  var form = document.getElementById(formID);
  //console.log(form);
  if (!form) {
    console.error("Form element with ID " + form + " not found");
    return false;
  }

  var inputs = form.querySelectorAll(
    "input[required], select[required], textarea[required]"
  );
  var isEmpty = false;

  inputs.forEach(function (input) {
    if (input.value.trim() === "") {
      isEmpty = true;
    }
  });

  return !isEmpty;
}

$("#registrationForm").submit(function (e) {
  e.preventDefault();
  var formID = "registrationForm";
  var registrationFormId = new FormData($("#registrationForm")[0]);
  var password = $("#password").val();
  var confirmPassword = $("#confirmPassword").val();
  if (password !== confirmPassword) {
    swal("Check Password", "Password does not match", "error");
  } else if (checkFormInputsNotEmpty(formID)) {
    registrationFormId.append("regUser", true);
    swal(
      {
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
      function () {
        $.ajax({
          url: "controllers/userAuth",
          type: "POST",
          async: true,
          data: registrationFormId,
          processData: false, // Important when sending FormData
          contentType: false, // Important when sending FormData
          beforeSend: function (lgFx) {
            $("#regBtn")
              .html(
                "<span><i class='fa fa-spin fa-spinner'></i> Registering... </span>"
              )
              .show();
            $("#regBtn").prop("disabled", true);
          },
          success: function (lgFx) {
            $("#regBtn").prop("disabled", false);
            $("#regBtn").html("Register").show();
            var status = lgFx.status;
            var message = lgFx.message;
            var header = lgFx.header;
            var feedbackResponse = lgFx.feedbackResponse;
            var logID = $("#username").val();

            swal(header, message, status);

            if (status === "success") {
              $("#logID").val(logID);
              $("#registrationForm")[0].reset();
              handlePanelSwitch();
            }
          },
          error: function (lgFx) {
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

/*==============================================================
      Authorize User Login
      ============================================================= */
$("#userLoginForm").submit(function (e) {
  e.preventDefault();

  var loginForm = new FormData($("#userLoginForm")[0]);

  loginForm.append("authUser", true);

  $.ajax({
    url: "controllers/userAuth",
    type: "POST",
    async: true,
    data: loginForm,
    processData: false, // Important when sending FormData
    contentType: false, // Important when sending FormData
    beforeSend: function (lgFx) {
      $("#loginBtn").prop("disabled", true);
      $("#loginBtn")
        .html(
          "<span><i class='fa fa-spin fa-spinner'></i> Verifying... </span>"
        )
        .show();
    },
    success: function (lgFx) {
      var status = lgFx.status;
      var message = lgFx.message;
      var redirectPage = lgFx.redirectPage;

      if (status === "success") {
        setTimeout(function () {
          $("#loginMsg").html(message).css("color", "#4A8717").show();
        }, 2000);
        setTimeout(function () {
          $("#loginBtn")
            .html("<i class='fa fa-spin fa-spinner'></i> Redirecting...")
            .css("color", "white")
            .show();
          window.location.href = redirectPage;
        }, 3000);
      } else if (status === "error") {
        setTimeout(function () {
          $("#loginBtn").prop("disabled", false);
          $("#loginBtn").html("Login").show();
          $("#loginMsg").html(message).css("color", "red").show();
          swal("Invalid Entry, Try Again", message, status);
        }, 2000);
      } else if (status === "warning") {
        setTimeout(function () {
          $("#loginBtn").prop("disabled", false);
          $("#loginBtn").html("Login").show();
          $("#loginMsg").html(message).css("color", "red").show();
          swal(message, "Kindly create an account", status);
        }, 2000);
      }
      //$("#loginMsg").html(lgFx).show();
    },
    error: function (lgFx) {
      $("#loginBtn").html("Login").show();
      $("#loginBtn").prop("disabled", false);
      $("#loginMsg")
        .html("Connectivity Error, Check your internet and try again")
        .css("color", "red")
        .show();
    },
  });
});

/*==============================================================
      Verify User Registration Email for duplicates
      ============================================================= */
$("#email").on("input paste", function () {
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
    success: function (response) {
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
    error: function () {
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
$("#username").on("input paste", function () {
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
    success: function (response) {
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
    error: function () {
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
