<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-2" role="alert">
        <div class="d-flex align-items-start">
            <!-- Icon -->
            <div class="me-1">
                <i class="bi bi-exclamation-triangle-fill fs-6"></i>
            </div>
            <!-- Nội dung -->
            <div class="flex-grow-1">
                <h5 class="alert-heading fw-bold mb-2">
                    Có lỗi xảy ra!
                </h5>
                <p class="mb-2">
                    <?= html_escape($error_message) ?>
                    <?php if (!empty($reason)): ?>
                        <br>
                        <span class="text-danger-emphasis">
                            <?= nl2br(html_escape($reason)) ?>
                        </span>
                    <?php endif; ?>
                </p>
                <?php if (!empty($query)): ?>
                    <hr>
                    <p class="mb-2 fw-semibold">
                        <i class="bi bi-code-slash me-1"></i>
                        Câu lệnh SQL:
                    </p>
                    <div class="bg-dark text-light rounded-3 p-3 overflow-auto">
                        <code class="text-light">
                            <?= html_escape($query) ?>
                        </code>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
