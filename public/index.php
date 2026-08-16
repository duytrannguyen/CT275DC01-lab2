<?php
/* Đoạn mã xử lý PHP. */
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/footer.php";

$query = 'SELECT id, quote, source, favorite FROM quotes ORDER BY date_entered DESC LIMIT 1';

if (isset($_GET["random"])) {
    $query = 'SELECT id, quote, source, favorite FROM quotes ORDER BY RANDOM() LIMIT 1';
} elseif (isset($_GET["favorite"])) {
    $query = 'SELECT id, quote, source, favorite FROM quotes WHERE favorite = true ORDER BY RANDOM() LIMIT 1';
}

$latest_quote = null;
$error_message = null;
$reason = null;
$pdo = null;

try {
    $pdo = get_database_connection();
} catch (PDOException $e) {
    $error_message = "Không thể kết nối đến cơ sở dữ liệu";
    $reason = $e->getMessage();
}

if ($pdo instanceof PDO) {
    try {
        $statement = $pdo->prepare($query);
        $statement->execute();
        $latest_quote = $statement->fetch();
    } catch (PDOException $e) {
        $error_message = "Không thể lấy dữ liệu";
        $reason = $e->getMessage();
    }
}
?>
<!-- Đoạn mã HTML trình bày nội dung trang web. -->
<?php render_page_header(); ?>

<!-- =========================
     HIỂN THỊ TRÍCH DẪN
========================= -->
<?php if (!empty($latest_quote)): ?>
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <!-- Quote Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-2">
                    <!-- Icon -->
                    <div class="text-center mb-2">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 30px; height: 30px;">
                            <i class="bi bi-chat-quote-fill fs-6"></i>
                        </div>
                    </div>
                    
                    <!-- Quote -->
                    <figure class="mb-2">
                        <blockquote class="blockquote text-center mb-2">
                            <p class="fs-6 fw-semibold lh-base text-dark mb-0">
                                <i class="bi bi-quote text-primary me-2"></i>
                                <?= html_escape($latest_quote["quote"]) ?>
                                <i class="bi bi-quote text-primary ms-2"></i>
                            </p>
                        </blockquote>
                        <!-- Source -->
                        <figcaption class="blockquote-footer text-center fs-6">
                            <?= html_escape($latest_quote["source"]) ?>
                        </figcaption>
                    </figure>
                    
                    <!-- Favorite -->
                    <?php if (is_checked($latest_quote["favorite"])): ?>
                        <div class="text-center mb-2">
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-star-fill me-1"></i>
                                Yêu thích
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Admin actions -->
                    <?php if (is_administrator()): ?>
                        <div class="border-top pt-4 mt-2">
                            <p class="text-center text-muted mb-1">
                                <i class="bi bi-shield-lock me-1"></i>
                                <strong>Quản trị Trích dẫn</strong>
                            </p>
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="edit_quote.php?id=<?= urlencode($latest_quote["id"]) ?>" class="btn btn-sm btn-outline-primary px-4">
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Sửa
                                </a>
                                <a href="delete_quote.php?id=<?= urlencode($latest_quote["id"]) ?>" class="btn btn-sm btn-outline-danger px-4" onclick="return confirm('Bạn có chắc chắn muốn xóa trích dẫn này?');">
                                    <i class="bi bi-trash3 me-1"></i>
                                    Xóa
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<!-- =========================
     LỖI
========================= -->
<?php elseif (!empty($error_message)): ?>
    <?php include __DIR__ . "/../partials/show_error.php"; ?>

<!-- =========================
     KHÔNG CÓ DỮ LIỆU
========================= -->
<?php else: ?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center p-2">
                    <div class="bg-light text-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-1" style="width: 30px; height: 30px;">
                        <i class="bi bi-chat-square-text fs-5"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Chưa có trích dẫn</h4>
                    <p class="text-muted mb-0">Hiện tại không có trích dẫn nào để hiển thị.</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- =========================
     CÁC CHẾ ĐỘ HIỂN THỊ
========================= -->
<div class="d-flex justify-content-center mt-2 mb-1">
    <div class="btn-group shadow-sm" role="group">
        <a href="index.php" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-clock-history me-1"></i>
            Mới nhất
        </a>
        <a href="index.php?random=true" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-shuffle me-1"></i>
            Ngẫu nhiên
        </a>
        <a href="index.php?favorite=true" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-star-fill me-1"></i>
            Yêu thích
        </a>
    </div>
</div>

<?php render_page_footer(); ?>