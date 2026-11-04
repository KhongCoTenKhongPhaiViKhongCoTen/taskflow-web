    <body>
        <main id="auth-view" class="auth-shell">
            <section class="auth-brand">
                <a class="brand" href="/"
                    ><span class="brand-mark">✳</span> taskflow</a
                >
                <div class="auth-message">
                    <span class="eyebrow">LÀM VIỆC CÓ CHỦ ĐÍCH</span>
                    <h1>Việc hôm nay,<br /><em>gọn gàng</em> hơn.</h1>
                    <p>
                        Một không gian nhỏ để sắp xếp công việc, theo dõi tiến
                        độ và tập trung vào điều quan trọng.
                    </p>
                    <div class="auth-note">
                        <span class="note-icon">✦</span
                        ><span>Chậm mà chắc. Từng việc một.</span>
                    </div>
                </div>
                <div class="auth-footer">
                    © 2026 TaskFlow · Không gian của riêng bạn
                </div>
            </section>
            <section class="auth-panel-wrap">
                <div class="auth-panel">
                    <div class="mobile-brand">
                        <a class="brand" href="/"
                            ><span class="brand-mark">✳</span> taskflow</a
                        >
                    </div>
                    <span class="eyebrow" id="auth-eyebrow"
                        >CHÀO MỪNG TRỞ LẠI</span
                    >
                    <h2 id="auth-title">Đăng nhập</h2>
                    <p class="muted" id="auth-subtitle">
                        Tiếp tục nơi bạn đã dừng lại.
                    </p>
                    <div class="auth-tabs" role="tablist">
                        <button
                            class="auth-tab active"
                            type="button"
                            data-auth-mode="login"
                        >
                            Đăng nhập
                        </button>
                        <button
                            class="auth-tab"
                            type="button"
                            data-auth-mode="register"
                        >
                            Tạo tài khoản
                        </button>
                    </div>
                    <form id="auth-form" novalidate>
                        <div class="field-group d-none" id="email-group">
                            <label for="auth-email">Email</label
                            ><input
                                id="auth-email"
                                class="form-control"
                                type="email"
                                autocomplete="email"
                                placeholder="ban@example.com"
                            />
                        </div>
                        <div class="field-group">
                            <label for="auth-username">Tên đăng nhập</label
                            ><input
                                id="auth-username"
                                class="form-control"
                                autocomplete="username"
                                minlength="3"
                                maxlength="40"
                                placeholder="Tên bạn muốn dùng"
                                required
                            />
                        </div>
                        <div class="field-group">
                            <div class="label-row">
                                <label for="auth-password">Mật khẩu</label>
                            </div>
                            <input
                                id="auth-password"
                                class="form-control"
                                type="password"
                                autocomplete="current-password"
                                minlength="8"
                                placeholder="Ít nhất 8 ký tự"
                                required
                            />
                        </div>
                        <div
                            id="auth-error"
                            class="form-error"
                            role="alert"
                        ></div>
                        <button
                            class="btn-primary-custom w-100"
                            id="auth-submit"
                            type="submit"
                        >
                            <span>Đăng nhập</span
                            ><span aria-hidden="true">→</span>
                        </button>
                    </form>
                    <p class="auth-switch muted">
                        <span id="switch-copy">Chưa có tài khoản?</span>
                        <button type="button" id="switch-auth">
                            Tạo tài khoản
                        </button>
                    </p>
                </div>
            </section>
        </main>
