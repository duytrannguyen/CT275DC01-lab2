<?php
/* Đoạn mã xử lý PHP. */
define("TITLE", "Đăng nhập");
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/footer.php";
$loggedin = isset($_SESSION["user"]);
$error_message = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    if ($email !== "" && $password !== "") {
        if ($email === "me@example.com" && $password === "testpass") {
            $_SESSION["user"] = "me";
            $loggedin = true;
        } else {
            $error_message = "Địa chỉ email và mật khẩu không khớp!";
        }
    } else {
        $error_message =
            "Hãy đảm bảo rằng bạn cung cấp đầy đủ địa chỉ email và mật khẩu!";
    }
}
?>
<!--
    Đoạn mã HTML trình bày nội dung trang web.
-->
<?php render_page_header(); ?>
<!-- =========================
     THÔNG BÁO LỖI
========================= -->
<?php if (!empty($error_message)): ?>
    <?php include __DIR__ . "/../partials/show_error.php"; ?>
<?php endif; ?>
<!-- =========================
     ĐĂNG NHẬP THÀNH CÔNG
========================= -->
<?php if ($loggedin): ?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center p-2">
                    <!-- Icon -->
                    <div
                        class="bg-success text-white rounded-circle
                               d-inline-flex align-items-center
                               justify-content-center shadow-sm mb-2"
                        style="width: 75px; height: 75px;"
                    >
                        <i class="bi bi-check-lg fs-1"></i>
                    </div>
                    <h2 class="fw-bold text-success mb-1">
                        Đăng nhập thành công!
                    </h2>
                    <p class="text-muted mb-2">
                        Bạn đã đăng nhập vào hệ thống.
                    </p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a
                            href="index.php"
                            class="btn btn-sm btn-primary px-4"
                        >
                            <i class="bi bi-house-door me-1"></i>
                            Về trang chủ
                        </a>
                        <?php if (is_administrator()): ?>
                            <a
                                href="add_quote.php"
                                class="btn btn-sm btn-outline-primary px-4"
                            >
                                <i class="bi bi-plus-circle me-1"></i>
                                Thêm trích dẫn
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- =========================
     FORM ĐĂNG NHẬP
========================= -->
<?php else: ?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Header Form -->
                <div class="card-header bg-primary text-white text-center py-1 border-0">
                    <div
                        class="bg-white text-primary rounded-circle
                               d-inline-flex align-items-center
                               justify-content-center mb-1"
                        style="width: 30px; height: 30px;"
                    >
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                    <h2 class="h6 fw-bold mb-1">
                        Đăng nhập
                    </h2>
                    <p class="mb-0 opacity-75">
                        Đăng nhập vào hệ thống quản lý trích dẫn
                    </p>
                </div>
                <!-- Form Body -->
                <div class="card-body p-2">
                    <form
                        action="login.php"
                        method="post"
                    >
                        <!-- Email -->
                        <div class="mb-2">
                            <label
                                for="email"
                                class="form-label fw-semibold"
                            >
                                <i class="bi bi-envelope me-1"></i>
                                Địa chỉ Email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-sm bg-light">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control form-control-sm"
                                    placeholder="Nhập địa chỉ email"
                                    value="<?= html_escape(
                                        $_POST["email"] ?? "",
                                    ) ?>"
                                    autocomplete="email"
                                    required
                                >
                            </div>
                        </div>
                        <!-- Password -->
                        <div class="mb-2">
                            <label
                                for="password"
                                class="form-label fw-semibold"
                            >
                                <i class="bi bi-lock me-1"></i>
                                Mật khẩu
                            </label>
                            <div class="input-group">
                                <span class="input-group-text input-group-text-sm bg-light">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control form-control-sm"
                                    placeholder="Nhập mật khẩu"
                                    autocomplete="current-password"
                                    required
                                >
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    id="togglePassword"
                                    title="Hiện/ẩn mật khẩu"
                                >
                                    <i
                                        class="bi bi-eye"
                                        id="passwordIcon"
                                    ></i>
                                </button>
                            </div>
                        </div>
                        <!-- Submit -->
                        <div class="d-grid">
                            <button
                                type="submit"
                                name="submit"
                                class="btn btn-sm btn-primary btn-lg"
                            >
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Đăng nhập
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Footer Form -->
                <div class="card-footer bg-light border-0 text-center py-1">
                    <small class="text-muted">
                        <i class="bi bi-shield-lock me-1"></i>
                        Thông tin đăng nhập của bạn được bảo mật.
                    </small>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- Hiện / ẩn mật khẩu -->
<script>
document.getElementById('togglePassword')?.addEventListener('click', function () {
    const password = document.getElementById('password');
    const icon = document.getElementById('passwordIcon');
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});
</script>
<?php render_page_footer($loggedin); ?>
