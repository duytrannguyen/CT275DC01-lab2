<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * Kiểm tra người dùng có phải administrator hay không.
 */
function is_administrator(string $user = "me"): bool
{
    return isset($_SESSION["user"]) && $_SESSION["user"] === $user;
}
/**
 * Kiểm tra quyền administrator.
 */
function ensure_admin_access(): bool
{
    if (is_administrator()) {
        return true;
    }
    http_response_code(403);
    return false;
}
/**
 * Kết nối PostgreSQL Database.
 */
function get_database_connection(): PDO
{
    static $pdo = null;
    // Nếu đã có kết nối thì sử dụng lại.
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    // Thông tin database.
    $host = getenv("DB_HOST") ?: getenv("PGHOST") ?: "localhost";
    $port = getenv("DB_PORT") ?: getenv("PGPORT") ?: "5432";
    $database = getenv("DB_NAME") ?: getenv("PGDATABASE") ?: "ct275_lab2";
    $username = getenv("DB_USER") ?: getenv("PGUSER") ?: "postgres";
    // Mật khẩu PostgreSQL.
    $password = getenv("DB_PASSWORD");
    if ($password === false || $password === "") {
        $password = "dongduy122";
    }
    // DSN PostgreSQL.
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        die(
            '<div class="container mt-5">
                <div class="alert alert-danger">
                    <h4 class="alert-heading">
                        <i class="bi bi-database-x"></i>
                        Không thể kết nối Database
                    </h4>
                    <p class="mb-0">' .
                html_escape($e->getMessage()) .
                '</p>
                </div>
            </div>'
        );
    }
    return $pdo;
}
/**
 * Kiểm tra giá trị checkbox.
 */
function is_checked($value): bool
{
    return in_array($value, [true, 1, "1", "t", "true", "yes", "on"], true);
}
/**
 * Escape dữ liệu HTML để chống XSS.
 */
function html_escape(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}
/**
 * Render phần Header của trang.
 */
function render_page_header(): void
{
    $title = defined("TITLE") ? TITLE : "Trang các Trích dẫn";
    $logged_in = isset($_SESSION["user"]);
    ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >
    <!-- CSS riêng -->
    <link
        rel="stylesheet"
        media="all"
        href="css/style.css?v=<?= time() ?>"
    >
    <title><?= html_escape($title) ?></title>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a
                class="navbar-brand fw-bold"
                href="/"
            >
                <i class="bi bi-chat-quote-fill me-2"></i>
                Trang các Trích dẫn
            </a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div
                class="collapse navbar-collapse"
                id="mainNavbar"
            >
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="/"
                        >
                            <i class="bi bi-house-door me-1"></i>
                            Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="search.php"
                        >
                            <i class="bi bi-search me-1"></i>
                            Tìm kiếm
                        </a>
                    </li>
                    <?php if ($logged_in): ?>
                        <?php if (is_administrator()): ?>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="view_quotes.php"
                                >
                                    <i class="bi bi-card-list me-1"></i>
                                    Trích dẫn
                                </a>
                            </li>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="add_quote.php"
                                >
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Thêm
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                href="logout.php"
                            >
                                <i class="bi bi-box-arrow-right me-1"></i>
                                Đăng xuất
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                href="login.php"
                            >
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Đăng nhập
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main Container -->
    <main class="container py-1">
        <!-- Page Header -->
        <div class="card border-0 shadow-sm mb-2">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div
                        class="bg-primary text-white rounded-circle
                               d-flex align-items-center justify-content-center
                               me-1"
                        style="width: 30px; height: 30px;"
                    >
                        <i class="bi bi-chat-quote fs-6"></i>
                    </div>
                    <div>
                        <h1 class="h6 mb-1 fw-bold">
                            <?= html_escape($title) ?>
                        </h1>
                        <p class="text-muted mb-0">
                            Quản lý và tìm kiếm các trích dẫn
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- BEGIN CHANGEABLE CONTENT. -->
<?php
}
