        <div id="app-view" class="app-shell d-none">
            <aside class="sidebar">
                <nav class="side-navigation" aria-label="Danh sách tác vụ">
                    <button
                        class="side-link active"
                        type="button"
                        data-view="my_day"
                    >
                        <span class="side-icon">☼</span
                        ><span>Ngày của tôi</span>
                    </button>
                    <button
                        class="side-link"
                        type="button"
                        data-view="important"
                    >
                        <span class="side-icon">☆</span><span>Quan trọng</span>
                    </button>
                    <button class="side-link" type="button" data-view="all">
                        <span class="side-icon">⌂</span><span>Tác vụ</span>
                    </button>
                </nav>
            </aside>
            <main class="workspace">
                <header class="topbar">
                    <a class="topbar-brand top-brand" href="/">
                        <span class="brand-mark">✳</span
                        ><strong>TaskFlow</strong>
                    </a>
                    <label class="global-search"
                        ><span>⌕</span
                        ><input
                            id="global-search-input"
                            type="search"
                            placeholder="Tìm kiếm"
                            aria-label="Tìm kiếm tác vụ"
                    /></label>
                    <div class="topbar-right">
                        <span id="today-label" class="today-label"></span>
                        <div class="account-menu">
                            <button
                                id="account-toggle"
                                class="account-toggle"
                                type="button"
                                aria-label="Thông tin tài khoản"
                                aria-expanded="false"
                                aria-controls="account-dropdown"
                            >
                                <span class="top-avatar" id="top-avatar"
                                    >T</span
                                >
                            </button>
                            <section
                                id="account-dropdown"
                                class="account-dropdown d-none"
                                aria-label="Thông tin tài khoản"
                            >
                                <div class="account-dropdown-header">
                                    <span
                                        class="profile-avatar"
                                        id="profile-avatar"
                                        >T</span
                                    >
                                    <div>
                                        <strong id="profile-name"
                                            >Tài khoản</strong
                                        ><span id="account-email"
                                            >Tài khoản TaskFlow</span
                                        >
                                    </div>
                                </div>
                                <button
                                    id="manage-account-button"
                                    class="account-logout account-manage"
                                    type="button"
                                >
                                    <span>⚙</span> Quản lý tài khoản
                                </button>
                                <button
                                    id="logout-button"
                                    class="account-logout"
                                    type="button"
                                >
                                    <span>↗</span> Đăng xuất
                                </button>
                            </section>
                        </div>
                    </div>
                </header>
                <div class="page-content">
                    <div id="task-page">
                        <section class="todo-heading">
                            <div class="todo-title-wrap">
                                <span
                                    class="view-title-icon"
                                    id="view-title-icon"
                                    >☼</span
                                >
                                <div>
                                    <h1 id="view-title">Ngày của tôi</h1>
                                    <p id="view-date" class="muted"></p>
                                </div>
                                <button
                                    id="view-more-button"
                                    class="top-icon"
                                    type="button"
                                    aria-label="Tùy chọn"
                                >
                                    ···
                                </button>
                            </div>
                            <div class="view-tools">
                                <button
                                    class="view-tool active"
                                    data-layout="grid"
                                    type="button"
                                >
                                    ▦ <span>Lưới</span></button
                                ><button
                                    class="view-tool"
                                    data-layout="list"
                                    type="button"
                                >
                                    ☰ <span>Danh sách</span></button
                                ><span class="toolbar-spacer"></span
                                ><label class="sort-tool"
                                    >↕
                                    <select
                                        id="sort-select"
                                        aria-label="Sắp xếp"
                                    >
                                        <option value="due">Sắp xếp</option>
                                        <option value="created">
                                            Ngày tạo
                                        </option>
                                        <option value="priority">
                                            Mức ưu tiên
                                        </option>
                                    </select></label
                                ><button
                                    class="view-tool"
                                    id="create-task-button"
                                    type="button"
                                >
                                    ＋ <span>Tạo tác vụ</span>
                                </button>
                            </div>
                        </section>
                        <section
                            class="stats-grid"
                            aria-label="Thống kê công việc"
                        >
                            <article class="stat-card">
                                <div class="stat-top">
                                    <span>Tổng công việc</span
                                    ><span class="stat-icon violet">▦</span>
                                </div>
                                <div class="stat-number" id="stat-total">—</div>
                                <div class="stat-foot">
                                    Trong danh sách của bạn
                                </div>
                            </article>
                            <article class="stat-card">
                                <div class="stat-top">
                                    <span>Cần làm</span
                                    ><span class="stat-icon amber">◷</span>
                                </div>
                                <div class="stat-number" id="stat-todo">—</div>
                                <div class="stat-foot">Đang chờ bắt đầu</div>
                            </article>
                            <article class="stat-card">
                                <div class="stat-top">
                                    <span>Đang thực hiện</span
                                    ><span class="stat-icon blue">↗</span>
                                </div>
                                <div class="stat-number" id="stat-doing">—</div>
                                <div class="stat-foot">
                                    Bạn đang tiến triển tốt
                                </div>
                            </article>
                            <article class="stat-card">
                                <div class="stat-top">
                                    <span>Hoàn thành</span
                                    ><span class="stat-icon green">✓</span>
                                </div>
                                <div class="stat-number" id="stat-done">—</div>
                                <div class="stat-foot">
                                    Từng việc một, thật tuyệt!
                                </div>
                            </article>
                        </section>
                        <section class="overdue-banner">
                            <span class="overdue-symbol">!</span
                            ><span
                                ><strong id="overdue-count">0 công việc</strong>
                                đang quá hạn. Hãy xem lại kế hoạch của bạn
                                nhé.</span
                            ><span class="banner-sparkle">✦</span>
                        </section>
                        <section class="tasks-section">
                            <div class="section-heading">
                                <div>
                                    <h2 id="list-heading">Việc cần làm</h2>
                                    <p class="muted" id="list-subtitle">
                                        Những việc bạn muốn tập trung hôm nay.
                                    </p>
                                </div>
                                <span class="task-count" id="task-count"
                                    >0 công việc</span
                                >
                            </div>
                            <form id="quick-task-form" class="quick-task-form">
                                <div class="quick-task-main">
                                    <span class="quick-check">○</span
                                    ><input
                                        id="quick-task-title"
                                        type="text"
                                        maxlength="160"
                                        placeholder="Thêm tác vụ"
                                        aria-label="Tên tác vụ mới"
                                        required
                                    />
                                </div>
                                <div class="quick-task-actions">
                                    <button
                                        type="button"
                                        id="quick-my-day"
                                        class="quick-action"
                                        title="Thêm vào Ngày của tôi"
                                    >
                                        ☼</button
                                    ><button
                                        type="button"
                                        id="quick-important"
                                        class="quick-action"
                                        title="Đánh dấu quan trọng"
                                    >
                                        ☆</button
                                    ><button type="submit" class="quick-submit">
                                        Thêm
                                    </button>
                                </div>
                            </form>
                            <div class="toolbar">
                                <label class="search-box">
                                    <span aria-hidden="true">⌕</span
                                    ><input
                                        id="search-input"
                                        type="search"
                                        placeholder="Tìm công việc..."
                                        aria-label="Tìm công việc" /></label
                                ><label class="select-wrap"
                                    ><span class="visually-hidden"
                                        >Lọc trạng thái</span
                                    ><select
                                        id="status-filter"
                                        aria-label="Lọc theo trạng thái"
                                    >
                                        <option value="all">
                                            Tất cả trạng thái
                                        </option>
                                        <option value="todo">Cần làm</option>
                                        <option value="doing">Đang làm</option>
                                        <option value="done">Hoàn thành</option>
                                    </select></label
                                ><label class="select-wrap"
                                    ><span class="visually-hidden"
                                        >Lọc ưu tiên</span
                                    ><select
                                        id="priority-filter"
                                        aria-label="Lọc theo ưu tiên"
                                    >
                                        <option value="all">
                                            Mọi mức ưu tiên
                                        </option>
                                        <option value="high">Cao</option>
                                        <option value="medium">
                                            Trung bình
                                        </option>
                                        <option value="low">Thấp</option>
                                    </select></label
                                >
                            </div>
                            <div
                                id="task-list"
                                class="task-list"
                                aria-live="polite"
                            ></div>
                            <div id="empty-state" class="empty-state d-none">
                                <div class="empty-art">✳</div>
                                <h3 id="empty-title">Chưa có công việc nào</h3>
                                <p class="muted" id="empty-copy">
                                    Bắt đầu bằng cách thêm việc đầu tiên vào
                                    danh sách nhé.
                                </p>
                                <button
                                    class="btn-outline-custom"
                                    id="empty-create-button"
                                >
                                    ＋ Tạo công việc</button
                                ><button
                                    class="btn-outline-custom d-none"
                                    id="clear-filters-button"
                                >
                                    Xóa bộ lọc
                                </button>
                            </div>
                        </section>
                        <footer class="page-footer">
                            Làm tốt lắm. Hãy tiếp tục theo nhịp của riêng bạn
                            <span>✦</span>
                        </footer>
                    </div>
                    <section
                        id="account-page"
                        class="account-page d-none"
                        aria-labelledby="account-page-title"
                    >
                        <div class="account-page-heading">
                            <button
                                id="account-back-button"
                                class="account-back"
                                type="button"
                            >
                                ← Quay lại công việc
                            </button>
                            <h1 id="account-page-title">Quản lý tài khoản</h1>
                            <p class="muted">
                                Cập nhật thông tin và bảo mật tài khoản TaskFlow
                                của bạn.
                            </p>
                        </div>
                        <form id="account-form" novalidate>
                            <div class="account-page-grid">
                                <section class="account-panel">
                                    <div class="account-panel-heading">
                                        <span class="account-panel-icon"
                                            >◉</span
                                        >
                                        <h2>Thông tin cá nhân</h2>
                                    </div>
                                    <div class="field-group">
                                        <label for="account-username"
                                            >Tên đăng nhập</label
                                        >
                                        <input
                                            id="account-username"
                                            class="form-control"
                                            minlength="3"
                                            maxlength="40"
                                            required
                                        />
                                    </div>
                                    <div class="field-group">
                                        <label for="account-email-input"
                                            >Email</label
                                        >
                                        <input
                                            id="account-email-input"
                                            class="form-control"
                                            type="email"
                                            maxlength="254"
                                            required
                                        />
                                    </div>
                                </section>
                                <section class="account-panel">
                                    <div class="account-panel-heading">
                                        <span
                                            class="account-panel-icon security-icon"
                                            >◇</span
                                        >
                                        <h2>Bảo mật</h2>
                                    </div>
                                    <div class="field-group">
                                        <label for="password-new"
                                            >Mật khẩu mới</label
                                        >
                                        <input
                                            id="password-new"
                                            class="form-control"
                                            type="password"
                                            autocomplete="new-password"
                                            minlength="8"
                                            maxlength="128"
                                        />
                                    </div>
                                    <div class="field-group">
                                        <label for="password-confirm"
                                            >Xác nhận mật khẩu mới</label
                                        >
                                        <input
                                            id="password-confirm"
                                            class="form-control"
                                            type="password"
                                            autocomplete="new-password"
                                            minlength="8"
                                            maxlength="128"
                                        />
                                    </div>
                                </section>
                            </div>
                            <div class="account-confirmation">
                                <div class="field-group">
                                    <label for="account-current-password"
                                        >Mật khẩu hiện tại
                                        <span class="required-mark"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        id="account-current-password"
                                        class="form-control"
                                        type="password"
                                        autocomplete="current-password"
                                        required
                                    />
                                    <p class="muted">
                                        Nhập để xác nhận thay đổi.
                                    </p>
                                </div>
                                <div class="account-actions">
                                    <button
                                        class="btn-outline-custom"
                                        type="submit"
                                        data-account-action="profile"
                                    >
                                        Lưu thông tin
                                    </button>
                                    <button
                                        class="btn-primary-custom"
                                        type="submit"
                                        data-account-action="password"
                                    >
                                        Cập nhật mật khẩu <span>→</span>
                                    </button>
                                </div>
                            </div>
                            <div
                                id="account-form-error"
                                class="form-error"
                                role="alert"
                            ></div>
                        </form>
                    </section>
                </div>
            </main>
        </div>
