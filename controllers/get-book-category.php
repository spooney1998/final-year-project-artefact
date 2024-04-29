<?php
session_start();
include("db_connect.php");

//LOAD CATEGORIES TABLE FUNCTION ||||||Starts>>>>>>>
if (!empty($_POST['showCategories']) && isset($_POST['query'])) {

  //	echo "Great a table"; die();
  $limit = 20; //mysqli_real_escape_string($conn, $_POST["pageLimit"]);
  $page = 1;

  if (!empty($_POST['page']) && $_POST['page']  >= 1) {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  } else {
    $start = 0;
  }


  $whereSQL = " categoryID != '' ";


  if ($_POST['query'] != '') { //to work on this for role access later
    $keyword = mysqli_real_escape_string($conn, $_POST['query']);

    $whereSQL = " categoryID != '' AND (categoryID LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR categoryName LIKE '%" . str_replace(' ', '%', $keyword) . "%' OR type LIKE '%" . str_replace(' ', '%', $keyword) . "%') ";
  }

  // Fetch records based on the query
  $query = "SELECT * FROM library_book_category WHERE $whereSQL ";
  $query .= 'ORDER BY categoryName ';

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
          <th class="text-center">ID</th>
          <th class="text-center">Type</th>
          <th class="text-center">Category</th>
          <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
            <th class="text-center">Options</th>
          <?php } ?>
        </tr>
      </thead>
      <tbody id="">
        <?php

        if ($total_data > 0) {
          while ($result = mysqli_fetch_array($statement)) {
            $start++;
        ?>

            <tr>
              <td class="text-center"><?= $start; ?></td>
              <td><?php echo ucfirst($result['type']); ?></td>
              <td class="text-center"><?php echo ucfirst($result['categoryName']); ?></td>

              <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
                <td class="text-center">
                  <a href="javascript:void(0);" data-toggle="modal" data-target="#categoriesEditModal" onClick="loadCategoriesEditModal(this);" data-value="<?php echo $result['categoryID']; ?>">
                    <span class="fas fa-bars"></span>
                  </a>
                </td>
              <?php } ?>
            </tr>

          <?php    }
        } else { ?>
          <tr class="table-danger">
            <td colspan="8" class="text-center">No Category was found!</td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <th class="text-center">ID</th>
          <th class="text-center">Type</th>
          <th class="text-center">Category</th>
          <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
            <th class="text-center">Options</th>
          <?php } ?>
        </tr>
      </tfoot>
    </table>
    <!-- Modal Section for modify Course Starts -->
    <div class="modal fade bd-example-modal-lg" id="categoriesEditModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel10" aria-hcategoryIDden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">

          <div class="modal-body" id="displayCategoriesInputs"></div>
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
                                <a class="page-link category-page-link" href="javascript:void(0);" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
                            </li>
                        ';
              $previous_categoryID = $page_array[$count] - 1;
              if ($previous_categoryID > 0) {
                $previous_link = '<li class="page-item"><a class="page-link category-page-link" href="javascript:void(0)" data-page_number="' . $previous_categoryID . '">Previous</a></li>';
              } else {
                $previous_link = '
                            <li class="page-item disabled">
                                <a href="javascript:void(0)" class="page-link">Previous</a>
                            </li>
                            ';
              }
              $next_categoryID = $page_array[$count] + 1;
              if ($next_categoryID > $total_links) {
                $next_link = '
                            <li class="page-item disabled">
                                <a href="javascript:void(0)" class="page-link">Next</a>
                            </li>
                            ';
              } else {
                $next_link = '<li class="page-item"><a class="page-link category-page-link" href="javascript:void(0)" data-page_number="' . $next_categoryID . '">Next</a></li>';
              }
            } else {
              if ($page_array[$count] == '...') {
                $page_link .= '
                                <li class="page-link category-page-link page-item disabled">
                                    <a class="" href="javascript:void(0);">...</a>
                                </li>
                            ';
              } else {
                $page_link .= '
                                <li class="page-item"><a class="page-link category-page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
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
//LOAD CATEGORIES TABLE FUNCTION ||||||Ends>>>>>>>

//ADD NEW CATEGORY FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['categoryName'], $_POST['categoryType'])) {

  ///////////////////////////////////////////////////////////////////////////
  $characters = '1234567890';
  $charactersLength = strlen($characters);
  $randomString = substr($_POST['categoryName'], 0, 2);
  for ($i = 0; $i < 5; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  $categoryID = mysqli_real_escape_string($conn, $randomString);
  ///////////////////////////////////////////////////////////////////////////


  // date_default_timezone_set("Africa/Lagos"); // Corrected timezone capitalization
  // $date = date("j-m-Y, g:i a");
  $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);
  $categoryType = mysqli_real_escape_string($conn, $_POST['categoryType']);

  // Check for category duplicates 
  $queryCategories = mysqli_query($conn, "SELECT * FROM library_book_category WHERE categoryName = '$categoryName' AND type='$categoryType' ") or die(mysqli_error($conn));
  $getDuplicate = mysqli_fetch_array($queryCategories);

  if (!empty($getDuplicate)) {
    $status = false;
    $header = 'Duplicate Entry!';
    $message = 'Category Already Exist';
    $responseStatus = 'warning';
  } else {
    // Insert new category
    $queryNewCategory = mysqli_query($conn, "INSERT INTO library_book_category (`categoryID`, `type`, `categoryName`) VALUES ('$categoryID', '$categoryType', '$categoryName')") or die(mysqli_error($conn));

    if ($queryNewCategory) {
      $status = true;
      $header = 'Successful!';
      $message = 'Category Added Successfully';
      $responseStatus = 'success';
    } else {
      $status = false;
      $header = 'Failed!';
      $message = 'An error occurred, try again';
      $responseStatus = 'error';
    }
  }

  $response = array('status' => $status, 'message' => $message, 'responseStatus' => $responseStatus, 'header' => $header);

  header('Content-Type: application/json');
  echo json_encode($response);

  exit();
}
//ADD NEW CATEGORY FUNCTION |||||||Ends>>>>>>>>>>

//LOAD CATEGORY EDIT MODAL |||||||| Starts >>>>>>>>>>
if (isset($_POST['getCategoriesEdit']) && !empty($_POST['categoryID'])) {
  $categoryID = mysqli_real_escape_string($conn, $_POST['categoryID']);
  //get category information 
  $queryCategory = mysqli_query($conn, "SELECT * FROM library_book_category WHERE categoryID='$categoryID'") or die(mysqli_error($conn));
  $getCategoryData = mysqli_fetch_array($queryCategory);

  if (!empty($getCategoryData)) { ?>
    <div class="modal-header">
      <h5 class="modal-title" id="myLargeModalLabel10">Modify <?php echo ucfirst($getCategoryData['type']); ?></h5>
      <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hcategoryIDden="true">&times;</span></button>-->
    </div>
    <form id="categoryEditForm">
      <!--Category Inputs-->
      <div class="col-12 mt-3">
        <div class="card">
          <div class="card-content">
            <div class="card-body py-5">
              <div class="row">

                <div class="form-group col-sm-6">
                  <div class="input-group">
                    <label class="col-12" for="modifyCategoryName">Book Category<span class="text-danger">*</span>
                      <select class="form-control" id="modifyCategoryName" name="modifyCategoryName" required="" value="<?php echo $getCategoryData['type']; ?>">

                        <option value="" disabled>Select Book Category</option>
                        <?php
                        $queryBookCategory = mysqli_query($conn, "SELECT DISTINCT categoryName FROM library_book_category ORDER BY categoryName ASC") or die(mysqli_error($conn));
                        while ($getBooksCategory = mysqli_fetch_array($queryBookCategory)) {
                        ?>
                          <option <?php echo (($getBooksCategory['categoryName'] == $getCategoryData['categoryName']) ? 'selected' : ''); ?> value="<?php echo $getBooksCategory['categoryName']; ?>">
                            <?php echo $getBooksCategory['categoryName']; ?>
                          </option>
                        <?php } ?>
                      </select>

                    </label>
                  </div>
                </div>

                <div class="form-group col-sm-6">
                  <div class="input-group">
                    <label class="col-12" for="modifyCategoryType">Book Type<span class="text-danger">*</span>
                      <input class="form-control" type="text" name="modifyCategoryType" id="modifyCategoryType" placeholder="Enter Category Type (ex. novels,poetry etc.)" required="" value="<?php echo $getCategoryData['type']; ?>" />
                      <input type="hidden" name="modifyCategoryID" id="modifyCategoryID" value="<?php echo $getCategoryData['categoryID']; ?>" placeholder="">
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--Category Inputs-->
      </div>
      <div class="modal-footer">
        <center style="margin: 0px auto;">
          <span id="modifyCategoryMsg"></span>
        </center>
        <button type="submit" class="btn btn-warning" id="updateCategoryBtn">Update Category</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </form>
    <script>
      $("#categoryEditForm").submit(function(e) {
        e.preventDefault();
        //console.log('welcome');
        var categoryForm = new FormData($("#categoryEditForm")[0]);

        // console.log(categoryForm);
        swal({
            title: "Are you sure to update Category?",
            text: "Updating this category is effective across the portal.",
            icon: 'question',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: 'btn-success',
            cancelButtonClass: 'btn-danger',
            confirmButtonText: 'Yes, Update!',
            cancelButtonText: 'Cancel!',
            closeOnConfirm: false,
            // closeOnCancel: false
          },
          function() {
            $.ajax({
              type: 'POST',
              url: 'controllers/get-book-category',
              processData: false,
              contentType: false,
              // mimeType: 'multipart/form-data',
              // cache: false,
              data: categoryForm,
              beforeSend: function() { // Corrected from beforeSubmit to beforeSend
                $("#updateCategoryBtn").html("<span class='fa fa-spin fa-spinner'></span>please wait...").show();
              },
              success: function(response) {
                var status = response.status;
                var message = response.message;
                var responseStatus = response.responseStatus;
                var header = response.header;

                if (status === true) {
                  $("#modifyCategoryMsg").html(response).css("color", "green").show();
                  swal(header, message, responseStatus);
                  //loadCategories(); //Reload registered courses table
                } else {
                  swal(header, message, responseStatus);
                }
              },
              error: function() {
                $("#modifyCategoryMsg").html("Error in connectivity, please check your internet connection and try again <i class='fa fa-exclamation-triangle'></i>").css("color", "red").show();
                swal("Connectivity Error!", "Please check your internet connection and try again!", "error");
              },
              complete: function() { // Moved the timeout code to the complete callback
                setTimeout(function() {
                  $("#modifyCategoryMsg").fadeOut(300);
                }, 3000);
                $("#updateCategoryBtn").html("Update Course").show(); // Reset the button text
              }
            });
          });
      });
    </script>
<?php
  }

  exit();
}
//LOAD CATEGORY EDIT MODAL |||||||| Ends >>>>>>>>>>

//UPDATE CATEGORY FUNCTION |||||||Starts>>>>>>>>>>
if (isset($_POST['modifyCategoryID'], $_POST['modifyCategoryName'])) {

  // echo "We reach oooo";
  // die();
  // date_default_timezone_set("Africa/Lagos"); 
  // $date = date("j-m-Y, g:i a");
  $modifyCategoryID = mysqli_real_escape_string($conn, $_POST['modifyCategoryID']);
  $modifyCategoryType = mysqli_real_escape_string($conn, $_POST['modifyCategoryType']);
  $modifyCategoryName = mysqli_real_escape_string($conn, $_POST['modifyCategoryName']);
  // Check for category duplicates 
  $queryCategory = mysqli_query($conn, "SELECT * FROM library_book_category WHERE categoryID = '$modifyCategoryID'  AND type='$modifyCategoryType' AND categoryName='$modifyCategoryName'") or die(mysqli_error($conn));
  $getDuplicate = mysqli_fetch_array($queryCategory);

  if (!empty($getDuplicate)) {
    $status = false;
    $header = 'No Changes!';
    $message = 'There is noting to update';
    $responseStatus = 'warning';
  } else {
    // Updated Book category
    $queryUpdateCategory = mysqli_query($conn, "UPDATE library_book_category SET `categoryName`='$modifyCategoryName', `type`='$modifyCategoryType' WHERE categoryID='$modifyCategoryID'") or die(mysqli_error($conn));

    if ($queryUpdateCategory) {
      $status = true;
      $header = 'Successful!';
      $message = 'Category Updated Successfully';
      $responseStatus = 'success';
    } else {
      $status = false;
      $header = 'Failed!';
      $message = 'An error occurred, try again';
      $responseStatus = 'error';
    }
  }

  $response = array('status' => $status, 'message' => $message, 'responseStatus' => $responseStatus, 'header' => $header);

  header('Content-Type: application/json');
  echo json_encode($response);

  exit();
}
//UPDATE CATEGORY FUNCTION |||||||Ends>>>>>>>>>>
?>