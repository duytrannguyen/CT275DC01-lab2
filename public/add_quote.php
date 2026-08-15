<?php
/* Đoạn mã xử lý PHP. */
define("TITLE", "Thêm một Trích dẫn");
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/footer.php";
$has_access = ensure_admin_access();
$form_data = [
    "quote" => "",
    "source" => "",
    "favorite" => false,
];
$success_message = null;
$error_message = null;
$reason = null;
$query = null;
if (!$has_access) {
    $error_message = "Bạn không có quyền truy cập trang này";
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $form_data = [
        "quote" => trim((string) ($_POST["quote"] ?? "")),
        "source" => trim((string) ($_POST["source"] ?? "")),
        "favorite" => isset($_POST["favorite"]),
    ];
    if ($form_data["quote"] === "" || $form_data["source"] === "") {
        $error_message = "Hãy nhập đầy đủ trích dẫn và nguồn.";
    } else {
        $query = 'INSERT INTO quotes (quote, source, favorite)
                  VALUES (:quote, :source, :favorite)';
        try {
            $pdo = get_database_connection();
            $statement = $pdo->prepare($query);
            $statement->bindValue(":quote", $form_data["quote"]);
            $statement->bindValue(":source", $form_data["source"]);
            $statement->bindValue(
                ":favorite",
                $form_data["favorite"],
                PDO::PARAM_BOOL,
            );
            $statement->execute();
            $success_message = "Trích dẫn đã được thêm thành công.";
            $form_data = [
                "quote" => "",
                "source" => "",
                "favorite" => false,
            ];
        } catch (PDOException $e) {
            $error_message = "Không thể thêm trích dẫn";
            $reason = $e->getMessage();
        }
    }
}
?>
<!--
    Đoạn mã HTML trình bày nội dung trang web.
-->
<?php render_page_header(); ?>
<?php if (!empty($error_message)): ?>
    <?php include __DIR__ . "/../partials/show_error.php"; ?>
<?php endif; ?>
<!-- =========================
     THÔNG BÁO THÀNH CÔNG
========================= -->
<?php if (!empty($success_message)): ?>
    <div
        class="alert alert-success border-0 shadow-sm rounded-3 mb-2"
        role="alert"
    >
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-6 me-1"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">
                    Thành công!
                </h5>
                <p class="mb-0">
                    <?= html_escape($success_message) ?>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if ($has_access): ?>
    <!-- =========================
         FORM THÊM TRÍCH DẪN
    ========================= -->
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Card Header -->
                <div class="card-header bg-primary text-white border-0 py-1">
                    <div class="d-flex align-items-center">
                        <div
                            class="bg-white text-primary rounded-circle
                                   d-flex align-items-center
                                   justify-content-center me-1"
                            style="width: 30px; height: 30px;"
                        >
                            <i class="bi bi-plus-lg fs-6"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-bold mb-1">
                                Thêm một Trích dẫn
                            </h2>
                            <p class="mb-0 opacity-75">
                                Thêm trích dẫn mới vào hệ thống
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Form -->
                <div class="card-body p-2">
                    <form
                        action="add_quote.php"
                        method="post"
                    >
                        <!-- Trích dẫn -->
                        <div class="mb-2">
                            <label
                                for="quote"
                                class="form-label fw-semibold"
                            >
                                <i class="bi bi-chat-quote me-1"></i>
                                Trích dẫn
                                <span class="text-danger">*</span>
                            </label>
                            <textarea
                                id="quote"
                                name="quote"
                                class="form-control form-control-sm"
                                rows="2"
                                placeholder="Nhập nội dung trích dẫn..."
                                required
                            ><?= html_escape($form_data["quote"]) ?></textarea>
                            
                        </div>
                        <!-- Nguồn -->
                        <div class="mb-2">
                            <label
                                for="source"
                                class="form-label fw-semibold"
                            >
                                <i class="bi bi-person me-1"></i>
                                Nguồn
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-sm">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input
                                    type="text"
                                    id="source"
                                    name="source"
                                    class="form-control form-control-sm"
                                    placeholder="Ví dụ: Albert Einstein"
                                    value="<?= html_escape(
                                        $form_data["source"],
                                    ) ?>"
                                    required
                                >
                            </div>
                            
                        </div>
                        <!-- Yêu thích -->
                        <div class="card bg-light border-0 rounded-3 mb-2">
                            <div class="card-body">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="favorite"
                                        value="1"
                                        id="favorite"
                                        <?php if ($form_data["favorite"]): ?>
                                            checked
                                        <?php endif; ?>
                                    >
                                    <label
                                        class="form-check-label fw-semibold"
                                        for="favorite"
                                    >
                                        <i class="bi bi-star-fill text-warning me-1"></i>
                                        Đây là trích dẫn yêu thích?
                                    </label>
                                </div>
                                
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div
                            class="d-flex flex-column flex-sm-row
                                   justify-content-end gap-2"
                        >
                            <a
                                href="index.php"
                                class="btn btn-sm btn-outline-secondary px-4"
                            >
                                <i class="bi bi-x-circle me-1"></i>
                                Hủy
                            </a>
                            <button
                                type="submit"
                                name="submit"
                                class="btn btn-sm btn-primary px-4"
                            >
                                <i class="bi bi-plus-circle me-1"></i>
                                Thêm Trích dẫn
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Card Footer -->
                <div class="card-footer bg-light border-0 py-1">
                    <small class="text-muted">
                        <i class="bi bi-shield-lock me-1"></i>
                        Chỉ quản trị viên mới có quyền thêm trích dẫn.
                    </small>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php render_page_footer(); ?>
