        <div class="sidebar">
            <div class="site-width">
                <!-- START: Menu-->
                <ul id="side-menu" class="sidebar-menu">
                    <li class="dropdown active"><a href="javascript:void();"><i class="icon-home mr-1"></i> Library Panel</a>
                        <ul>
                            <li <?php if ($page == "dashboard") {
                                    echo "class='active'";
                                } ?>><a href="dashboard"><i class="icon-home"></i>Dashboard</a></li>

                            <li <?php if ($page == "books") {
                                    echo "class='active'";
                                } ?>><a href="books"><i class="fas fa-search"></i>Search Books</a></li>
                            <li <?php if ($page == "book-category") {
                                    echo "class='active'";
                                } ?>><a href="book-category"><i class="fas fa-sitemap"></i>Book Category</a></li>

                            <?php if ($_SESSION['userRole'] == "admin" || $_SESSION['userRole'] == "librarian") { ?>
                                <li <?php if ($page == "reviews") {
                                        echo "class='active'";
                                    } ?>><a href="reviews"><i class="far fa-star"></i>Reviews</a></li>
                                <li <?php if ($page == "users") {
                                        echo "class='active'";
                                    } ?>><a href="users"><i class="fas fa-users"></i>Users</a></li>
                            <?php } ?>

                            <li <?php if ($page == "favorite-books") {
                                    echo "class='active'";
                                } ?>><a href="favorite-books"><i class="fas fa-heart"></i>My Favorite Books</a></li>

                            <li <?php if ($page == "history") {
                                    echo "class='active'";
                                } ?>><a href="history"><i class="fas fa-history"></i>My Past Reads</a></li>
                            <li <?php if ($page == "my-profile") {
                                    echo "class='active'";
                                } ?>><a href="my-profile"><i class="fas fa-user-cog"></i>My Profile</a></li>
                        </ul>
                    </li>
                </ul>
                <!-- END: Menu-->
                <ol class="breadcrumb bg-transparent align-self-center m-0 p-0 ml-auto">
                    <li class="breadcrumb-item"><a href="javascript:void();">Application</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>