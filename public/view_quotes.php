<?php
/**
 * Trang xem tất cả các trích dẫn
 */
define("TITLE", "Xem tất cả các Trích dẫn");
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/footer.php";
$has_access = ensure_admin_access();
$quotes = [];
$error_message = null;
$reason = null;
if (!$has_access) {
    $error_message = "Bạn không có quyền truy cập trang này";
} else {
    $query =
        "SELECT id, quote, source, favorite, date_entered FROM quotes ORDER BY date_entered DESC";
    try {
        $pdo = get_database_connection();
        $statement = $pdo->prepare($query);
        $statement->execute();
        $quotes = $statement->fetchAll();
    } catch (PDOException $e) {
        $error_message = "Không thể lấy danh sách trích dẫn";
        $reason = $e->getMessage();
    }
}
?>
<?php render_page_header(); ?>
<!-- Thông báo lỗi -->
<?php if (!empty($error_message)): ?>
    <?php include __DIR__ . "/../partials/show_error.php"; ?>
<?php endif; ?>
<?php if ($has_access): ?>
    <!-- Danh sách trích dẫn -->
    <?php if (!empty($quotes)): ?>
        <div class="row g-2">
            <?php foreach ($quotes as $quote): ?>
                <div class="col-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-2">
                            <!-- Header card -->
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-1" style="width: 30px; height: 30px;">
                                        <i class="bi bi-quote fs-6"></i>
                                    </div>
                                    <div>
                                        <span class="badge bg-light text-secondary border">
                                            #<?= html_escape($quote["id"]) ?>
                                        </span>
                                    </div>
                                </div>
                                <?php if (is_checked($quote["favorite"])): ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-star-fill me-1"></i>
                                        Yêu thích
                                    </span>
                                <?php endif; ?>
                            </div>
                            <!-- Nội dung quote -->
                            <blockquote class="blockquote mb-2">
                                <p class="fs-5 fw-semibold lh-base mb-0">
                                    "<?= html_escape($quote["quote"]) ?>"
                                </p>
                            </blockquote>
                            <!-- Thông tin -->
                            <div class="row g-3 mb-1">
                                <!-- Source -->
                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-person me-1"></i>
                                            Nguồn / Tác giả
                                        </small>
                                        <strong><?= html_escape(
                                            $quote["source"],
                                        ) ?></strong>
                                    </div>
                                </div>
                                <!-- Date -->
                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Ngày thêm
                                        </small>
                                        <strong><?= html_escape(
                                            $quote["date_entered"],
                                        ) ?></strong>
                                    </div>
                                </div>
                            </div>
                            <!-- Action -->
                            <div class="border-top pt-3">
                                <div class="d-flex flex-wrap justify-content-end gap-2">
                                    <a href="edit_quote.php?id=<?= urlencode(
                                        $quote["id"],
                                    ) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Sửa
                                    </a>
                                    <a href="delete_quote.php?id=<?= urlencode(
                                        $quote["id"],
                                    ) ?>" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash3 me-1"></i>
                                        Xóa
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <!-- Không có dữ liệu -->
    <?php elseif (empty($error_message)): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center p-2">
                <div class="bg-light text-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-1" style="width: 75px; height: 75px;">
                    <i class="bi bi-chat-square-text fs-5"></i>
                </div>
                <h4 class="fw-bold">Chưa có trích dẫn</h4>
                <p class="text-muted mb-2">Hiện tại chưa có trích dẫn nào trong cơ sở dữ liệu.</p>
                <a href="add_quote.php" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Thêm Trích dẫn đầu tiên
                </a>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php render_page_footer(); ?>
