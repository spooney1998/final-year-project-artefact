<!-- START: Footer-->
<footer class="site-footer">
    © Copyright <?php echo date("Y"); ?> Roehampton University Library.
</footer>
<!-- END: Footer-->

<!-- START: Back to top-->
<a href="#" class="scrollup text-center">
    <i class="icon-arrow-up"></i>
</a>
<!-- END: Back to top-->

<!-- Global Functions -->
<!-- ::::::::::::::::::::::::::::::::::: -->

<script>
    function handleBookReservation(bookID) {
        var bookID = $(bookID).val();

        // console.log(bookID);
        swal({
                title: "Are you sure to make reservation?",
                text: "You are about adding this book to your reservation list.",
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
                        makeBookReservation: true,
                        bookID: bookID
                    },
                    beforeSend: function(newBookRequestResponse) {
                        $("#reservationBtn").html("<span><i class='fa fa-spin fa-spinner'></i> Please wait... </span>").show();
                        $("#reservationBtn").prop("disabled", true);
                    },
                    success: function(newBookRequestResponse) {
                        $("#reservationBtn").html("Reserved").show();
                        var status = newBookRequestResponse.status;
                        var message = newBookRequestResponse.message;
                        var header = newBookRequestResponse.header;
                        var responseStatus = newBookRequestResponse.responseStatus;

                        $("#reservationBtn").prop("disabled", true);

                        <?php if ($page == "books") { ?>
                            var currentBookPage = $('.book-page-link').data('page_number');
                            loadBooks(currentBookPage); //load current book page
                        <?php } elseif ($page == "favorite-books") { ?>
                            // var currentBookPage = $('.favorite-page-link').data('page_number');
                            // loadFavorites(currentBookPage); //load current book page

                        <?php } elseif ($page == "history") { ?>
                            // var currentBookPage = $('.pastRead-page-link').data('page_number');
                            // loadPastReads(currentBookPage); //load current book page
                        <?php } ?>

                        // console.log(newBookRequestResponse);
                        swal(header, message, responseStatus);

                    },
                    error: function(newBookRequestResponse) {
                        $("#reservationBtn").html("Save Book").show();
                        $("#reservationBtn").prop("disabled", false);
                        swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
                    },
                });
            });

    }

    function handleRemoveBookReservation(bookID) {
        var bookID = $(bookID).val();

        // console.log(bookID);
        swal({
                title: "Are you sure to remove reservation?",
                text: "You are about removing this book from your reservation list.",
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
                    data: {
                        removeBookReservation: true,
                        bookID: bookID
                    },
                    beforeSend: function(removeBookRequestResponse) {
                        $("#removeReservationBtn").html("<span><i class='fa fa-spin fa-spinner'></i> Please wait... </span>").show();
                        $("#removeReservationBtn").prop("disabled", true);
                    },
                    success: function(removeBookRequestResponse) {
                        $("#removeReservationBtn").prop("disabled", true);
                        $("#removeReservationBtn").html("Removed").show();
                        var status = removeBookRequestResponse.status;
                        var message = removeBookRequestResponse.message;
                        var header = removeBookRequestResponse.header;
                        var responseStatus = removeBookRequestResponse.responseStatus;

                        $("#removeReservationBtn").prop("disabled", true);


                        <?php if ($page == "books") { ?>
                            var currentBookPage = $('.book-page-link').data('page_number');
                            loadBooks(currentBookPage); //load current book page
                        <?php } elseif ($page == "favorite-books") { ?>
                            // var currentBookPage = $('.favorite-page-link').data('page_number');
                            // loadFavorites(currentBookPage); //load current book page

                        <?php } elseif ($page == "history") { ?>
                            // var currentBookPage = $('.pastRead-page-link').data('page_number');
                            // loadPastReads(currentBookPage); //load current book page
                        <?php } ?>


                        // console.log(newBookRequestResponse);
                        swal(header, message, responseStatus);

                    },
                    error: function(removeBookRequestResponse) {
                        $("#removeReservationBtn").html("Save Book").show();
                        $("#removeReservationBtn").prop("disabled", false);
                        swal("Connectivity Error", "Connectivity Error, Check your internet and try again", "error");
                    },
                });
            });

    }
</script>


<!-- ::::::::::::::::::::::::::::::::::: -->
<!-- Global Functions -->