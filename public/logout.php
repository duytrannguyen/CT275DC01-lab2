<?php
/* Đoạn mã xử lý PHP. */
define("TITLE", "Đăng xuất");
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/footer.php";
$is_loggedin = isset($_SESSION["user"]);
if ($is_loggedin) {
    unset($_SESSION["user"]);
    $_SESSION = [];
    session_regenerate_id(true);
}
?>
<!--
    Đoạn mã HTML trình bày nội dung trang web.
-->
<?php render_page_header(); ?>
<!-- =========================
     ĐĂNG XUẤT THÀNH CÔNG
========================= -->
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div
            class="card border-0 shadow-sm
                   rounded-4 overflow-hidden"
        >
            <!-- Header -->
            <div
                class="card-header bg-success
                       text-white border-0
                       text-center py-1"
            >
                <div
                    class="bg-white text-success
                           rounded-circle
                           d-inline-flex
                           align-items-center
                           justify-content-center
                           mb-1"
                    style="width: 30px; height: 30px;"
                >
                    <i
                        class="bi bi-box-arrow-right
                               fs-5"
                    ></i>
                </div>
                <h2 class="h6 fw-bold mb-1">
                    Đăng xuất thành công
                </h2>
                <p class="mb-0 opacity-75">
                    Bạn đã đăng xuất khỏi hệ thống
                </p>
            </div>
            <!-- Nội dung -->
            <div class="card-body text-center p-2">
                <div
                    class="alert alert-success
                           border-0 rounded-3 mb-2"
                    role="alert"
                >
                    <i
                        class="bi bi-check-circle-fill
                               me-2"
                    ></i>
                    Bạn đã đăng xuất thành công.
                </div>
                <p class="text-muted mb-2">
                    Cảm ơn bạn đã sử dụng hệ thống
                    quản lý Trích dẫn.
                </p>
                <!-- Buttons -->
                <div
                    class="d-flex flex-column
                           flex-sm-row
                           justify-content-center
                           gap-2"
                >
                    <a
                        href="index.php"
                        class="btn btn-sm btn-primary px-4"
                    >
                        <i
                            class="bi bi-house-door me-1"
                        ></i>
                        Về trang chủ
                    </a>
                    <a
                        href="login.php"
                        class="btn btn-sm btn-outline-primary px-4"
                    >
                        <i
                            class="bi bi-box-arrow-in-right me-1"
                        ></i>
                        Đăng nhập lại
                    </a>
                </div>
            </div>
            <!-- Footer -->
            <div
                class="card-footer bg-light
                       border-0 text-center py-1"
            >
                <small class="text-muted">
                    <i
                        class="bi bi-shield-check me-1"
                    ></i>
                    Phiên đăng nhập của bạn đã được kết thúc.
                </small>
            </div>
        </div>
    </div>
</div>
<?php render_page_footer(); ?>
