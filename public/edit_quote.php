<?php
/* Đoạn mã xử lý PHP. */
define("TITLE", "Sửa một Trích dẫn");
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/footer.php";

$has_access = ensure_admin_access();
$form_data = [
    "id" => "",
    "quote" => "",
    "source" => "",
    "favorite" => false,
];
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
    $form_data = [
        "id" => $id ?? "",
        "quote" => trim((string) ($_POST["quote"] ?? "")),
        "source" => trim((string) ($_POST["source"] ?? "")),
        "favorite" => isset($_POST["favorite"]),
    ];
    
    if ($id === null) {
        $error_message = "Mã trích dẫn không hợp lệ.";
    } elseif ($form_data["quote"] === "" || $form_data["source"] === "") {
        $error_message = "Hãy nhập đầy đủ trích dẫn và nguồn.";
    } else {
        $query = 'UPDATE quotes SET quote = :quote, source = :source, favorite = :favorite WHERE id = :id';
        try {
            $pdo = get_database_connection();
            $statement = $pdo->prepare($query);
            $statement->bindValue(":quote", $form_data["quote"]);
            $statement->bindValue(":source", $form_data["source"]);
            $statement->bindValue(":favorite", $form_data["favorite"], PDO::PARAM_BOOL);
            $statement->bindValue(":id", $id, PDO::PARAM_INT);
            $statement->execute();
            
            if ($statement->rowCount() > 0) {
                $success_message = "Trích dẫn đã được cập nhật thành công.";
            } else {
                $error_message = "Không tìm thấy trích dẫn cần cập nhật.";
            }
        } catch (PDOException $e) {
            $error_message = "Không thể cập nhật trích dẫn";
            $reason = $e->getMessage();
        }
    }
} else {
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
            $quote = $statement->fetch();
            
            if ($quote) {
                $form_data = [
                    "id" => $quote["id"],
                    "quote" => $quote["quote"],
                    "source" => $quote["source"],
                    "favorite" => is_checked($quote["favorite"]),
                ];
            } else {
                $error_message = "Không tìm thấy trích dẫn cần cập nhật.";
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
     THÔNG BÁO THÀNH CÔNG
========================= -->
<?php if (!empty($success_message)): ?>
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-2" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-6 me-1"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">Cập nhật thành công!</h5>
                <p class="mb-0"><?= html_escape($success_message) ?></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($has_access && $form_data["id"] !== ""): ?>
    <!-- =========================
         FORM SỬA TRÍCH DẪN
    ========================= -->
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Card Header -->
                <div class="card-header bg-primary text-white border-0 py-1">
                    <div class="d-flex align-items-center">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-1" style="width: 30px; height: 30px;">
                            <i class="bi bi-pencil-square fs-6"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-bold mb-1">Sửa một Trích dẫn</h2>
                            <p class="mb-0 opacity-75">Cập nhật nội dung trích dẫn</p>
                        </div>
                    </div>
                </div>
                
                <!-- Form Body -->
                <div class="card-body p-2">
                    <form action="edit_quote.php" method="post">
                        <!-- ID -->
                        <input type="hidden" name="id" value="<?= html_escape($form_data["id"]) ?>">
                        
                        <!-- ID hiển thị -->
                        <div class="mb-2">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-hash me-1"></i>
                                Mã trích dẫn
                            </label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-sm bg-light">
                                    <i class="bi bi-database"></i>
                                </span>
                                <input type="text" class="form-control form-control-sm bg-light" value="<?= html_escape($form_data["id"]) ?>" readonly>
                            </div>
                        </div>
                        
                        <!-- Trích dẫn -->
                        <div class="mb-2">
                            <label for="quote" class="form-label fw-semibold">
                                <i class="bi bi-chat-quote me-1"></i>
                                Trích dẫn <span class="text-danger">*</span>
                            </label>
                            <textarea id="quote" name="quote" class="form-control form-control-sm" rows="2" placeholder="Nhập nội dung trích dẫn..." required><?= html_escape($form_data["quote"]) ?></textarea>
                        </div>
                        
                        <!-- Nguồn -->
                        <div class="mb-2">
                            <label for="source" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>
                                Nguồn <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-sm">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" id="source" name="source" class="form-control form-control-sm" placeholder="Tên tác giả hoặc nguồn" value="<?= html_escape($form_data["source"]) ?>" required>
                            </div>
                        </div>
                        
                        <!-- Favorite -->
                        <div class="card bg-light border-0 rounded-3 mb-2">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="favorite" value="1" id="favorite" <?php if ($form_data["favorite"]): ?>checked<?php endif; ?>>
                                    <label class="form-check-label fw-semibold" for="favorite">
                                        <i class="bi bi-star-fill text-warning me-1"></i>
                                        Đây là trích dẫn yêu thích?
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Buttons -->
                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                            <a href="view_quotes.php" class="btn btn-sm btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i>
                                Hủy
                            </a>
                            <button type="submit" name="submit" class="btn btn-sm btn-primary px-4">
                                <i class="bi bi-save me-1"></i>
                                Cập nhật Trích dẫn
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Card Footer -->
                <div class="card-footer bg-light border-0 text-center py-1">
                    <small class="text-muted">
                        <i class="bi bi-shield-lock me-1"></i>
                        Chỉ quản trị viên mới có quyền chỉnh sửa trích dẫn.
                    </small>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php render_page_footer(); ?>