<?php
/* Đoạn mã xử lý PHP. */
define("TITLE", "Tìm kiếm Trích dẫn");
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/footer.php";

$keyword = trim((string) ($_GET["keyword"] ?? ""));
$selected_source = trim((string) ($_GET["source"] ?? ""));
$submitted = array_key_exists("submit", $_GET) || array_key_exists("keyword", $_GET) || array_key_exists("source", $_GET);

$sources = [];
$quotes = [];
$error_message = null;
$reason = null;
$query = null;

try {
    $pdo = get_database_connection();
    
    /* Lấy danh sách nguồn/tác giả. */
    $source_query = 'SELECT DISTINCT source FROM quotes ORDER BY source';
    $statement = $pdo->prepare($source_query);
    $statement->execute();
    $sources = $statement->fetchAll(PDO::FETCH_COLUMN);
    
    /* Thực hiện tìm kiếm. */
    if ($submitted) {
        $query = 'SELECT id, quote, source, favorite, date_entered FROM quotes WHERE quote ILIKE :keyword';
        
        if ($selected_source !== "") {
            $query .= " AND source = :source";
        }
        
        $query .= ' ORDER BY date_entered DESC';
        
        $statement = $pdo->prepare($query);
        $statement->bindValue(":keyword", "%" . $keyword . "%");
        
        if ($selected_source !== "") {
            $statement->bindValue(":source", $selected_source);
        }
        
        $statement->execute();
        $quotes = $statement->fetchAll();
    }
} catch (PDOException $e) {
    $error_message = "Không thể tìm kiếm trích dẫn";
    $reason = $e->getMessage();
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
     FORM TÌM KIẾM
========================= -->
<div class="row justify-content-center mb-2">
    <div class="col-lg-10 col-xl-9">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <!-- Header -->
            <div class="card-header bg-primary text-white border-0 py-1">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-1" style="width: 30px; height: 30px;">
                        <i class="bi bi-search fs-6"></i>
                    </div>
                    <div>
                        <h2 class="h6 fw-bold mb-1">Tìm kiếm Trích dẫn</h2>
                        <p class="mb-0 opacity-75">Tìm kiếm trích dẫn theo từ khóa hoặc tác giả</p>
                    </div>
                </div>
            </div>
            
            <!-- Form -->
            <div class="card-body p-4">
                <form action="search.php" method="get">
                    <div class="row g-3 align-items-end">
                        <!-- Từ khóa -->
                        <div class="col-md-6">
                            <label for="keyword" class="form-label fw-semibold">
                                <i class="bi bi-key me-1"></i>
                                Từ khóa
                            </label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-sm bg-light">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="keyword" name="keyword" class="form-control form-control-sm" placeholder="Nhập từ khóa..." value="<?= html_escape($keyword) ?>">
                            </div>
                        </div>
                        
                        <!-- Nguồn -->
                        <div class="col-md-4">
                            <label for="source" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>
                                Nguồn / Tác giả
                            </label>
                            <select id="source" name="source" class="form-select">
                                <option value="">Tất cả nguồn / tác giả</option>
                                <?php foreach ($sources as $source): ?>
                                    <option value="<?= html_escape($source) ?>" <?php if ($source === $selected_source): ?>selected<?php endif; ?>>
                                        <?= html_escape($source) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Submit -->
                        <div class="col-md-2 d-grid">
                            <button type="submit" name="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-search me-1"></i>
                                Tìm kiếm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- =========================
     KẾT QUẢ TÌM KIẾM
========================= -->
<?php if ($submitted && empty($error_message)): ?>
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <!-- Tiêu đề kết quả -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-1 gap-2">
                <div>
                    <h3 class="h5 fw-bold mb-1">
                        <i class="bi bi-list-ul text-primary me-1"></i>
                        Kết quả tìm kiếm
                    </h3>
                    <small class="text-muted">
                        <?php if ($keyword !== ""): ?>
                            Từ khóa: <strong><?= html_escape($keyword) ?></strong>
                        <?php endif; ?>
                        
                        <?php if ($selected_source !== ""): ?>
                            <?php if ($keyword !== ""): ?>
                                <span class="mx-1">•</span>
                            <?php endif; ?>
                            Nguồn: <strong><?= html_escape($selected_source) ?></strong>
                        <?php endif; ?>
                    </small>
                </div>
                
                <?php if (!empty($quotes)): ?>
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        <?= count($quotes) ?> kết quả
                    </span>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($quotes)): ?>
                <!-- Danh sách kết quả -->
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($quotes as $quote): ?>
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <!-- Quote -->
                                <div class="d-flex align-items-start">
                                    <div class="text-primary me-1 flex-shrink-0">
                                        <i class="bi bi-quote fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <blockquote class="blockquote mb-1">
                                            <p class="fs-5 fw-semibold lh-base mb-0">
                                                <?= html_escape($quote["quote"]) ?>
                                            </p>
                                        </blockquote>
                                        
                                        <!-- Source -->
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <span class="text-muted">
                                                <i class="bi bi-person me-1"></i>
                                                <?= html_escape($quote["source"]) ?>
                                            </span>
                                            <?php if (is_checked($quote["favorite"])): ?>
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                                    <i class="bi bi-star-fill me-1"></i>
                                                    Yêu thích
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Date -->
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                Ngày thêm: <?= html_escape($quote["date_entered"]) ?>
                                            </small>
                                        </div>
                                        
                                        <!-- Admin -->
                                        <?php if (is_administrator()): ?>
                                            <div class="border-top mt-3 pt-3">
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="edit_quote.php?id=<?= urlencode($quote["id"]) ?>" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-pencil-square me-1"></i>
                                                        Sửa
                                                    </a>
                                                    <a href="delete_quote.php?id=<?= urlencode($quote["id"]) ?>" class="btn btn-outline-danger btn-sm">
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
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Không tìm thấy -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center p-2">
                        <div class="bg-light text-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-1" style="width: 30px; height: 30px;">
                            <i class="bi bi-search fs-5"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Không tìm thấy trích dẫn</h4>
                        <p class="text-muted mb-2">
                            Không có trích dẫn nào phù hợp với điều kiện tìm kiếm của bạn.
                        </p>
                        <a href="search.php" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Xóa bộ lọc
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php render_page_footer(); ?>