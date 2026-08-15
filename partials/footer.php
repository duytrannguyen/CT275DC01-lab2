<?php
function render_page_footer(bool $is_loggedin = false): void
{
    ?> 
    <!-- END CHANGEABLE CONTENT. -->
    <!-- Footer Navigation -->
    <footer class="mt-5">
        <div class="container-fluid px-0">
            <?php if (
                (is_administrator() &&
                    basename($_SERVER["PHP_SELF"]) !== "logout.php") ||
                $is_loggedin
            ): ?>
                <div class="border-top pt-4 pb-3">
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">
                        <a href="add_quote.php" 
                           class="btn btn-sm btn-primary btn-sm px-3">
                            <i class="bi bi-plus-circle"></i>
                            Thêm Trích dẫn
                        </a>
                        <a href="view_quotes.php" 
                           class="btn btn-sm btn-outline-primary btn-sm px-3">
                            <i class="bi bi-card-list"></i>
                            Xem tất cả Trích dẫn
                        </a>
                        <a href="search.php" 
                           class="btn btn-sm btn-outline-secondary btn-sm px-3">
                            <i class="bi bi-search"></i>
                            Tìm kiếm
                        </a>
                        <a href="logout.php" 
                           class="btn btn-sm btn-outline-danger btn-sm px-3">
                            <i class="bi bi-box-arrow-right"></i>
                            Đăng xuất
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="border-top pt-4 pb-3">
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">
                        <a href="/" 
                           class="btn btn-sm btn-outline-primary btn-sm px-4">
                            <i class="bi bi-house"></i>
                            Trang chủ
                        </a>
                        <a href="search.php" 
                           class="btn btn-sm btn-outline-secondary btn-sm px-4">
                            <i class="bi bi-search"></i>
                            Tìm kiếm
                        </a>
                        <a href="login.php" 
                           class="btn btn-sm btn-primary btn-sm px-4">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Đăng nhập
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <!-- Copyright -->
        <div id="footer" class="bg-dark text-white text-center py-1 mt-2 rounded-top">
            <div class="container">
                <small class="text-white-50">
                    &copy; <?= date("Y") ?> 
                    <span class="fw-semibold text-white">
                        Quote Management System
                    </span>
                    — All Rights Reserved.
                </small>
            </div>
        </div>
    </footer>
    </div><!-- container -->
    </body>
    </html>
<?php
}
?>
