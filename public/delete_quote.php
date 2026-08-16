<?php
/* Đoạn mã xử lý PHP. */
define("TITLE", "Xóa một Trích dẫn");
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/footer.php";

$has_access = ensure_admin_access();
$quote_details = null;
$success_message = null;
$error_message = null;
$reason = null;
$query = null;

$get_positive_id = static function ($value): ?int {
    if (is_array($value)) {
        return null;
    }
    $id = filter_var($value, FILTER_VALIDATE_INT, [
        "options" => [
            "min_range" => 1,
        ],
    ]);
    return $id === false ? null : (int) $id;
};

if (!$has_access) {
    $error_message = "Bạn không có quyền truy cập trang này";
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $get_positive_id($_POST["id"] ?? null);
    if ($id === null) {
        $error_message = "Mã trích dẫn không hợp lệ.";
    } else {
        try {
            $pdo = get_database_connection();
            
            /* Lấy thông tin trích dẫn trước khi xóa. */
            $query = 'SELECT id, quote, source, favorite FROM quotes WHERE id = :id';
            $statement = $pdo->prepare($query);
            $statement->bindValue(":id", $id, PDO::PARAM_INT);
            $statement->execute();
            $quote_details = $statement->fetch();
            
            if (!$quote_details) {
                $error_message = "Không tìm thấy trích dẫn cần xóa.";
            } else {
                /* Xóa trích dẫn. */
                $query = 'DELETE FROM quotes WHERE id = :id';
                $statement = $pdo->prepare($query);
                $statement->bindValue(":id", $id, PDO::PARAM_INT);
                $statement->execute();
                
                if ($statement->rowCount() > 0) {
                    $success_message = "Trích dẫn đã được xóa thành công.";
                    $quote_details = null;
                } else {
                    $error_message = "Không tìm thấy trích dẫn cần xóa.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Không thể xóa trích dẫn";
            $reason = $e->getMessage();
        }
    }
} else {
    /* Lấy ID từ URL. */
    $id = $get_positive_id($_GET["id"] ?? null);
    if ($id === null) {
        $error_message = "Mã trích dẫn không hợp lệ.";
    } else {
        $query = 'SELECT id, quote, source, favorite FROM quotes WHERE id = :id';
        try {
            $pdo = get_database_connection();
            $statement = $pdo->prepare($query);
            $statement->bindValue(":id", $id, PDO::PARAM_INT);
            $statement->execute();
            $quote_details = $statement->fetch();
            
            if (!$quote_details) {
                $error_message = "Không tìm thấy trích dẫn cần xóa.";
            }
        } catch (PDOException $e) {
            $error_message = "Không thể lấy thông tin trích dẫn";
            $reason = $e->getMessage();
        }
    }
}
?>
<!-- Đoạn mã HTML trình bày nội dung trang web. -->
<?php render_page_header(); ?>

<!-- =========================
    THÔNG BÁO LỖI
========================= -->
<?php if (!empty($error_message)): ?>
    <?php include __DIR__ . "/../partials/show_error.php"; ?>
<?php endif; ?>

<!-- =========================
    XÓA THÀNH CÔNG
========================= -->
<?php if (!empty($success_message)): ?>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-4" role="alert">
                <div class="d-flex align-items-start">
                    <i class="bi bi-check-circle-fill fs-5 me-1"></i>
                    <div class="flex-grow-1">
                        <h4 class="alert-heading fw-bold">Xóa thành công!</h4>
                        <p class="mb-1"><?= html_escape($success_message) ?></p>
                        <a href="view_quotes.php" class="btn btn-sm btn-success">
                            <i class="bi bi-card-list me-1"></i>
                            Xem danh sách trích dẫn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- =========================
     XÁC NHẬN XÓA
========================= -->
<?php if ($has_access && !empty($quote_details)): ?>
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Header cảnh báo -->
                <div class="card-header bg-danger text-white border-0 text-center py-1">
                    <div class="bg-white text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-1" style="width: 30px; height: 30px;">
                        <i class="bi bi-trash3-fill fs-6"></i>
                    </div>
                    <h2 class="h6 fw-bold mb-1">Xóa một Trích dẫn</h2>
                    <p class="mb-0 opacity-75">Thao tác này không thể hoàn tác</p>
                </div>
                
                <!-- Nội dung -->
                <div class="card-body p-2">
                    <!-- Cảnh báo -->
                    <div class="alert alert-warning border-0 rounded-3 mb-2">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill fs-6 me-1"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Bạn có chắc chắn muốn xóa?</h5>
                                <p class="mb-0">Trích dẫn này sẽ bị xóa vĩnh viễn khỏi cơ sở dữ liệu.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quote -->
                    <div class="card bg-light border-0 rounded-4 mb-2">
                        <div class="card-body p-4">
                            <div class="text-center mb-1">
                                <i class="bi bi-chat-quote-fill text-danger fs-5"></i>
                            </div>
                            <blockquote class="blockquote text-center mb-2">
                                <p class="fs-6 fw-semibold lh-base">
                                    “<?= html_escape($quote_details["quote"]) ?>”
                                </p>
                            </blockquote>
                            
                            <!-- Source -->
                            <div class="text-center">
                                <span class="text-muted">
                                    <i class="bi bi-person me-1"></i>
                                    <?= html_escape($quote_details["source"]) ?>
                                </span>
                            </div>
                            
                            <!-- Favorite -->
                            <?php if (is_checked($quote_details["favorite"])): ?>
                                <div class="text-center mt-3">
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-star-fill me-1"></i>
                                        Yêu thích
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Form -->
                    <form action="delete_quote.php" method="post">
                        <input type="hidden" name="id" value="<?= html_escape($quote_details["id"]) ?>">
                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                            <!-- Hủy -->
                            <a href="view_quotes.php" class="btn btn-sm btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i>
                                Hủy
                            </a>
                            <!-- Xóa -->
                            <button type="submit" name="submit" class="btn btn-sm btn-danger px-4" onclick="return confirm('Bạn có chắc chắn muốn xóa trích dẫn này?');">
                                <i class="bi bi-trash3 me-1"></i>
                                Xóa Trích dẫn này
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="card-footer bg-light border-0 text-center py-1">
                    <small class="text-muted">
                        <i class="bi bi-shield-lock me-1"></i>
                        Chỉ quản trị viên mới có quyền xóa trích dẫn.
                    </small>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php render_page_footer(); ?>