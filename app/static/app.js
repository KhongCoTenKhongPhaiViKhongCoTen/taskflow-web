(() => {
  const $ = (selector) => document.querySelector(selector);
  const authView = $("#auth-view");
  const appView = $("#app-view");
  const authForm = $("#auth-form");
  const taskModal = new bootstrap.Modal($("#task-modal"));
  const toast = new bootstrap.Toast($("#app-toast"), { delay: 2600 });
  let authMode = "login";
  let searchTimer;
  let currentUser;
  let currentView = "my_day";
  let quickImportant = false;
  let quickMyDay = true;

  const labels = {
    status: { todo: "Cần làm", doing: "Đang làm", done: "Hoàn thành" },
    priority: { low: "Thấp", medium: "Trung bình", high: "Cao" },
  };

  async function api(path, options = {}) {
    const response = await fetch(path, {
      credentials: "same-origin",
      ...options,
      headers: {
        ...(options.body ? { "Content-Type": "application/json" } : {}),
        ...options.headers,
      },
    });
    if (response.status === 204) return null;
    const payload = await response.json().catch(() => ({}));
    if (!response.ok) {
      if (
        response.status === 401 &&
        path !== "/api/login" &&
        path !== "/api/register"
      )
        showAuth();
      const detail = Array.isArray(payload.detail)
        ? payload.detail.map((issue) => issue.msg).join(" ")
        : payload.detail;
      throw new Error(detail || "Đã xảy ra lỗi. Vui lòng thử lại.");
    }
    return payload;
  }

  function showAuth() {
    currentUser = null;
    appView.classList.add("d-none");
    authView.classList.remove("d-none");
    $("#auth-password").value = "";
  }

  function showApp(user) {
    currentUser = user;
    authView.classList.add("d-none");
    appView.classList.remove("d-none");
    const name = user.username || "bạn";
    $("#profile-name").textContent = name;
    $("#account-email").textContent = user.email || "Tài khoản TaskFlow";
    $("#profile-avatar").textContent = name.charAt(0).toUpperCase();
    $("#top-avatar").textContent = name.charAt(0).toUpperCase();
    refreshDashboard();
    setView(currentView);
    const dateLabel = new Intl.DateTimeFormat("vi-VN", {
      weekday: "long",
      day: "numeric",
      month: "long",
    }).format(new Date());
    $("#view-date").textContent = dateLabel;
    $("#today-label").textContent = dateLabel;
  }

  function openAccountPage() {
    $("#account-dropdown").classList.add("d-none");
    $("#account-toggle").setAttribute("aria-expanded", "false");
    $("#task-page").classList.add("d-none");
    $("#account-page").classList.remove("d-none");
    $("#account-form").reset();
    $("#account-username").value = currentUser?.username || "";
    $("#account-email-input").value = currentUser?.email || "";
    $("#account-form-error").textContent = "";
  }

  function updateAccountUI(user) {
    currentUser = user;
    const name = user.username || "Tài khoản";
    $("#profile-name").textContent = name;
    $("#account-email").textContent = user.email || "Tài khoản TaskFlow";
    $("#profile-avatar").textContent = name.charAt(0).toUpperCase();
    $("#top-avatar").textContent = name.charAt(0).toUpperCase();
  }

  function setAuthMode(mode) {
    authMode = mode;
    const registering = mode === "register";
    $("#email-group").classList.toggle("d-none", !registering);
    $("#auth-email").required = registering;
    $("#auth-password").autocomplete = registering
      ? "new-password"
      : "current-password";
    $("#auth-password").minLength = registering ? 8 : 1;
    $("#auth-eyebrow").textContent = registering
      ? "BẮT ĐẦU HÀNH TRÌNH"
      : "CHÀO MỪNG TRỞ LẠI";
    $("#auth-title").textContent = registering ? "Tạo tài khoản" : "Đăng nhập";
    $("#auth-subtitle").textContent = registering
      ? "Tạo không gian riêng cho công việc của bạn."
      : "Tiếp tục nơi bạn đã dừng lại.";
    $("#auth-submit span:first-child").textContent = registering
      ? "Tạo tài khoản"
      : "Đăng nhập";
    $("#switch-copy").textContent = registering
      ? "Đã có tài khoản?"
      : "Chưa có tài khoản?";
    $("#switch-auth").textContent = registering ? "Đăng nhập" : "Tạo tài khoản";
    document
      .querySelectorAll("[data-auth-mode]")
      .forEach((tab) =>
        tab.classList.toggle("active", tab.dataset.authMode === mode),
      );
    $("#auth-error").textContent = "";
  }

  document
    .querySelectorAll("[data-auth-mode]")
    .forEach((tab) =>
      tab.addEventListener("click", () => setAuthMode(tab.dataset.authMode)),
    );
  $("#switch-auth").addEventListener("click", () =>
    setAuthMode(authMode === "login" ? "register" : "login"),
  );

  authForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    const button = $("#auth-submit");
    const error = $("#auth-error");
    error.textContent = "";
    if (!authForm.reportValidity()) return;
    button.disabled = true;
    try {
      const payload = {
        username: $("#auth-username").value.trim(),
        password: $("#auth-password").value,
      };
      if (authMode === "register")
        payload.email = $("#auth-email").value.trim();
      const user = await api(
        authMode === "register" ? "/api/register" : "/api/login",
        { method: "POST", body: JSON.stringify(payload) },
      );
      authForm.reset();
      showApp(user);
    } catch (err) {
      error.textContent = err.message;
    } finally {
      button.disabled = false;
    }
  });

  async function refreshDashboard() {
    try {
      const data = await api("/api/dashboard");
      ["total", "todo", "doing", "done"].forEach((key) => {
        $(`#stat-${key}`).textContent = data[key];
      });
      $("#overdue-count").textContent =
        `${data.overdue} ${data.overdue === 1 ? "công việc" : "công việc"}`;
      $(".overdue-banner").classList.toggle("has-overdue", data.overdue > 0);
    } catch (err) {
      showToast(err.message);
    }
  }

  async function refreshTasks() {
    const list = $("#task-list");
    list.replaceChildren();
    const loading = document.createElement("div");
    loading.className = "loading-state";
    loading.textContent = "Đang tải công việc...";
    list.append(loading);
    const params = new URLSearchParams();
    const query = $("#search-input").value.trim();
    const status = $("#status-filter").value;
    const priority = $("#priority-filter").value;
    if (query) params.set("q", query);
    if (currentView !== "all") params.set("view", currentView);
    if (status !== "all") params.set("status", status);
    if (priority !== "all") params.set("priority", priority);
    try {
      const tasks = await api(`/api/tasks?${params.toString()}`);
      const sort = $("#sort-select").value;
      if (sort === "created")
        tasks.sort((a, b) => b.created_at.localeCompare(a.created_at));
      if (sort === "priority") {
        const rank = { high: 0, medium: 1, low: 2 };
        tasks.sort((a, b) => rank[a.priority] - rank[b.priority]);
      }
      list.replaceChildren();
      $("#task-count").textContent =
        `${tasks.length} ${tasks.length === 1 ? "công việc" : "công việc"}`;
      $("#empty-state").classList.toggle("d-none", tasks.length > 0);
      list.classList.toggle("d-none", tasks.length === 0);
      const hasFilters =
        Boolean(query) || status !== "all" || priority !== "all";
      $("#empty-title").textContent = hasFilters
        ? "Không tìm thấy công việc"
        : "Chưa có công việc nào";
      $("#empty-copy").textContent = hasFilters
        ? "Thử thay đổi từ khóa hoặc bỏ bớt bộ lọc nhé."
        : "Bắt đầu bằng cách thêm việc đầu tiên vào danh sách nhé.";
      $("#empty-create-button").classList.toggle("d-none", hasFilters);
      $("#clear-filters-button").classList.toggle("d-none", !hasFilters);
      tasks.forEach((task) => list.append(createTaskCard(task)));
    } catch (err) {
      list.replaceChildren();
      const message = document.createElement("div");
      message.className = "error-state";
      message.textContent = err.message;
      list.append(message);
      $("#empty-state").classList.add("d-none");
    }
  }

  function createTaskCard(task) {
    const card = document.createElement("article");
    card.className = `task-card${task.status === "done" ? " is-done" : ""}${task.is_important ? " is-important" : ""}`;
    const check = document.createElement("button");
    check.type = "button";
    check.className = `task-check${task.status === "done" ? " checked" : ""}`;
    check.setAttribute(
      "aria-label",
      task.status === "done"
        ? "Đánh dấu chưa hoàn thành"
        : "Đánh dấu hoàn thành",
    );
    check.textContent = task.status === "done" ? "✓" : "";
    check.addEventListener("click", async () => {
      try {
        const payload = {
          title: task.title,
          description: task.description,
          status: task.status === "done" ? "todo" : "done",
          priority: task.priority,
          due_date: task.due_date,
          my_day_date: task.my_day_date,
          is_important: task.is_important,
        };
        await api(`/api/tasks/${task.id}`, {
          method: "PUT",
          body: JSON.stringify(payload),
        });
        await Promise.all([refreshDashboard(), refreshTasks()]);
        showToast(
          task.status === "done"
            ? "Đã chuyển về cần làm."
            : "Tuyệt vời! Công việc đã hoàn thành.",
        );
      } catch (err) {
        showToast(err.message);
      }
    });

    const content = document.createElement("div");
    content.className = "task-main";
    const title = document.createElement("h3");
    title.className = "task-title";
    title.textContent = task.title;
    content.append(title);
    if (task.description) {
      const description = document.createElement("p");
      description.className = "task-description";
      description.textContent = task.description;
      content.append(description);
    }

    const metadata = document.createElement("div");
    metadata.className = "task-meta";
    const statusBadge = document.createElement("span");
    statusBadge.className = `badge-status status-${task.status}`;
    statusBadge.textContent = labels.status[task.status];
    metadata.append(statusBadge);
    const priorityBadge = document.createElement("span");
    priorityBadge.className = `badge-priority priority-${task.priority}`;
    priorityBadge.textContent = `◆ ${labels.priority[task.priority]}`;
    metadata.append(priorityBadge);
    if (task.due_date) {
      const due = document.createElement("span");
      const isOverdue =
        task.status !== "done" &&
        task.due_date < new Date().toISOString().slice(0, 10);
      due.className = `task-due${isOverdue ? " overdue" : ""}`;
      due.textContent = `${isOverdue ? "! " : "◷ "}${new Intl.DateTimeFormat("vi-VN", { day: "numeric", month: "short", year: "numeric", timeZone: "UTC" }).format(new Date(`${task.due_date}T00:00:00Z`))}`;
      metadata.append(due);
    }
    content.append(metadata);

    const actions = document.createElement("div");
    actions.className = "task-actions";
    const edit = document.createElement("button");
    edit.type = "button";
    edit.className = "icon-button";
    edit.title = "Chỉnh sửa";
    edit.setAttribute("aria-label", `Chỉnh sửa ${task.title}`);
    edit.textContent = "✎";
    edit.addEventListener("click", () => openTaskModal(task));
    const important = document.createElement("button");
    important.type = "button";
    important.className = `icon-button task-star${task.is_important ? " selected" : ""}`;
    important.title = task.is_important
      ? "Bỏ đánh dấu quan trọng"
      : "Đánh dấu quan trọng";
    important.setAttribute("aria-label", important.title);
    important.textContent = task.is_important ? "★" : "☆";
    important.addEventListener("click", () =>
      saveTaskChanges(
        task,
        { is_important: !task.is_important },
        "Đã cập nhật mức quan trọng.",
      ),
    );
    const myDay = document.createElement("button");
    myDay.type = "button";
    myDay.className = `icon-button task-my-day${task.my_day_date === todayIso() ? " selected" : ""}`;
    myDay.title =
      task.my_day_date === todayIso()
        ? "Bỏ khỏi Ngày của tôi"
        : "Thêm vào Ngày của tôi";
    myDay.setAttribute("aria-label", myDay.title);
    myDay.textContent = "☼";
    myDay.addEventListener("click", () =>
      saveTaskChanges(
        task,
        { my_day_date: task.my_day_date === todayIso() ? null : todayIso() },
        "Đã cập nhật Ngày của tôi.",
      ),
    );
    const remove = document.createElement("button");
    remove.type = "button";
    remove.className = "icon-button delete-action";
    remove.title = "Xóa công việc";
    remove.setAttribute("aria-label", `Xóa ${task.title}`);
    remove.textContent = "⌫";
    remove.addEventListener("click", async () => {
      if (!window.confirm(`Bạn có chắc muốn xóa “${task.title}” không?`))
        return;
      try {
        await api(`/api/tasks/${task.id}`, { method: "DELETE" });
        await Promise.all([refreshDashboard(), refreshTasks()]);
        showToast("Đã xóa công việc.");
      } catch (err) {
        showToast(err.message);
      }
    });
    actions.append(important, myDay, edit, remove);
    card.append(check, content, actions);
    return card;
  }

  function openTaskModal(task = null) {
    $("#task-form").reset();
    $("#task-form-error").textContent = "";
    $("#task-id").value = task?.id ?? "";
    $("#task-title").value = task?.title ?? "";
    $("#task-description").value = task?.description ?? "";
    $("#task-status").value = task?.status ?? "todo";
    $("#task-priority").value = task?.priority ?? "medium";
    $("#task-due-date").value = task?.due_date ?? "";
    $("#task-my-day").checked =
      task?.my_day_date === todayIso() || (!task && currentView === "my_day");
    $("#task-important").checked = Boolean(task?.is_important);
    $("#task-modal-title").textContent = task
      ? "Chỉnh sửa công việc"
      : "Tạo công việc";
    $("#task-save-button span:first-child").textContent = task
      ? "Lưu thay đổi"
      : "Lưu công việc";
    taskModal.show();
    window.setTimeout(() => $("#task-title").focus(), 150);
  }

  $("#task-form").addEventListener("submit", async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    if (!form.reportValidity()) return;
    const button = $("#task-save-button");
    const error = $("#task-form-error");
    error.textContent = "";
    button.disabled = true;
    const id = $("#task-id").value;
    const dueDate = $("#task-due-date").value;
    const payload = {
      title: $("#task-title").value.trim(),
      description: $("#task-description").value.trim(),
      status: $("#task-status").value,
      priority: $("#task-priority").value,
      due_date: dueDate || null,
      my_day_date: $("#task-my-day").checked ? todayIso() : null,
      is_important: $("#task-important").checked,
    };
    try {
      await api(id ? `/api/tasks/${id}` : "/api/tasks", {
        method: id ? "PUT" : "POST",
        body: JSON.stringify(payload),
      });
      taskModal.hide();
      await Promise.all([refreshDashboard(), refreshTasks()]);
      showToast(id ? "Đã cập nhật công việc." : "Đã thêm công việc mới.");
    } catch (err) {
      error.textContent = err.message;
    } finally {
      button.disabled = false;
    }
  });

  function todayIso() {
    const now = new Date();
    return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`;
  }

  function setView(view) {
    currentView = view;
    const titles = {
      my_day: ["☼", "Ngày của tôi", "Những việc bạn muốn tập trung hôm nay."],
      important: ["☆", "Quan trọng", "Các tác vụ bạn đã đánh dấu quan trọng."],
      all: ["⌂", "Tác vụ", "Tất cả công việc của bạn."],
    };
    const [icon, title, subtitle] = titles[view] || titles.my_day;
    $("#view-title-icon").textContent = icon;
    $("#view-title").textContent = title;

    $("#list-heading").textContent = title;
    $("#list-subtitle").textContent = subtitle;
    document
      .querySelectorAll("[data-view]")
      .forEach((button) =>
        button.classList.toggle("active", button.dataset.view === view),
      );
    quickMyDay = view === "my_day";
    $("#quick-my-day").classList.toggle("selected", quickMyDay);
    $("#quick-my-day").setAttribute("aria-pressed", String(quickMyDay));
    refreshTasks();
  }

  async function saveTaskChanges(task, changes, message) {
    const payload = {
      title: task.title,
      description: task.description || "",
      status: task.status,
      priority: task.priority,
      due_date: task.due_date || null,
      my_day_date: task.my_day_date || null,
      is_important: Boolean(task.is_important),
      ...changes,
    };
    try {
      await api(`/api/tasks/${task.id}`, {
        method: "PUT",
        body: JSON.stringify(payload),
      });
      await Promise.all([refreshDashboard(), refreshTasks()]);
      showToast(message);
    } catch (err) {
      showToast(err.message);
    }
  }

  function showToast(message) {
    $("#toast-message").textContent = message;
    toast.show();
  }

  $("#create-task-button").addEventListener("click", () => openTaskModal());
  document
    .querySelectorAll("[data-view]")
    .forEach((button) =>
      button.addEventListener("click", () => setView(button.dataset.view)),
    );
  $("#global-search-input").addEventListener("input", (event) => {
    $("#search-input").value = event.target.value;
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(refreshTasks, 250);
  });
  $("#quick-my-day").addEventListener("click", () => {
    quickMyDay = !quickMyDay;
    $("#quick-my-day").classList.toggle("selected", quickMyDay);
    $("#quick-my-day").setAttribute("aria-pressed", String(quickMyDay));
  });
  $("#quick-important").addEventListener("click", () => {
    quickImportant = !quickImportant;
    $("#quick-important").classList.toggle("selected", quickImportant);
    $("#quick-important").setAttribute("aria-pressed", String(quickImportant));
  });
  $("#quick-task-form").addEventListener("submit", async (event) => {
    event.preventDefault();
    const input = $("#quick-task-title");
    const title = input.value.trim();
    if (!title) return;
    const button = event.currentTarget.querySelector(".quick-submit");
    button.disabled = true;
    try {
      await api("/api/tasks", {
        method: "POST",
        body: JSON.stringify({
          title,
          description: "",
          status: "todo",
          priority: "medium",
          due_date: null,
          my_day_date: quickMyDay ? todayIso() : null,
          is_important: quickImportant,
        }),
      });
      input.value = "";
      quickImportant = false;
      $("#quick-important").classList.remove("selected");
      await Promise.all([refreshDashboard(), refreshTasks()]);
      showToast("Đã thêm tác vụ.");
    } catch (err) {
      showToast(err.message);
    } finally {
      button.disabled = false;
      input.focus();
    }
  });
  document.querySelectorAll("[data-layout]").forEach((button) =>
    button.addEventListener("click", () => {
      document
        .querySelectorAll("[data-layout]")
        .forEach((item) => item.classList.toggle("active", item === button));
      $("#task-list").classList.toggle(
        "list-layout",
        button.dataset.layout === "list",
      );
    }),
  );
  $("#sort-select").addEventListener("change", refreshTasks);
  $("#empty-create-button").addEventListener("click", () => openTaskModal());
  $("#clear-filters-button").addEventListener("click", () => {
    $("#search-input").value = "";
    $("#status-filter").value = "all";
    $("#priority-filter").value = "all";
    refreshTasks();
  });
  $("#search-input").addEventListener("input", () => {
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(refreshTasks, 250);
  });
  $("#status-filter").addEventListener("change", refreshTasks);
  $("#priority-filter").addEventListener("change", refreshTasks);
  $("#account-toggle").addEventListener("click", () => {
    const dropdown = $("#account-dropdown");
    const isOpening = dropdown.classList.contains("d-none");
    dropdown.classList.toggle("d-none", !isOpening);
    $("#account-toggle").setAttribute("aria-expanded", String(isOpening));
  });
  document.addEventListener("click", (event) => {
    if (!event.target.closest(".account-menu")) {
      $("#account-dropdown").classList.add("d-none");
      $("#account-toggle").setAttribute("aria-expanded", "false");
    }
  });
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      $("#account-dropdown").classList.add("d-none");
      $("#account-toggle").setAttribute("aria-expanded", "false");
    }
  });
  $("#manage-account-button").addEventListener("click", openAccountPage);
  $("#account-back-button").addEventListener("click", () => {
    $("#account-page").classList.add("d-none");
    $("#task-page").classList.remove("d-none");
  });
  $("#account-form").addEventListener("submit", async (event) => {
    event.preventDefault();
    const action = event.submitter?.dataset.accountAction;
    const error = $("#account-form-error");
    const buttons = [
      ...event.currentTarget.querySelectorAll("button[type=submit]"),
    ];
    const username = $("#account-username");
    const email = $("#account-email-input");
    const currentPassword = $("#account-current-password").value;
    error.textContent = "";

    if (!currentPassword) {
      error.textContent = "Nhập mật khẩu hiện tại để xác nhận thay đổi.";
      $("#account-current-password").focus();
      return;
    }

    let payload = { current_password: currentPassword };
    if (action === "profile") {
      if (!username.reportValidity() || !email.reportValidity()) return;
      payload.username = username.value.trim();
      payload.email = email.value.trim();
    } else if (action === "password") {
      const newPassword = $("#password-new").value;
      const confirmation = $("#password-confirm").value;
      if (newPassword.length < 8 || newPassword.length > 128) {
        error.textContent = "Mật khẩu mới cần có từ 8 đến 128 ký tự.";
        $("#password-new").focus();
        return;
      }
      if (newPassword !== confirmation) {
        error.textContent = "Mật khẩu xác nhận không khớp.";
        $("#password-confirm").focus();
        return;
      }
      payload.new_password = newPassword;
    } else {
      return;
    }

    buttons.forEach((button) => {
      button.disabled = true;
    });
    try {
      const user = await api("/api/account", {
        method: "PUT",
        body: JSON.stringify(payload),
      });
      updateAccountUI(user);
      $("#account-current-password").value = "";
      if (action === "profile") {
        showToast("Thông tin tài khoản đã được cập nhật.");
      } else {
        $("#password-new").value = "";
        $("#password-confirm").value = "";
        showToast("Mật khẩu đã được cập nhật.");
      }
    } catch (err) {
      error.textContent = err.message;
    } finally {
      buttons.forEach((button) => {
        button.disabled = false;
      });
    }
  });
  $("#logout-button").addEventListener("click", async () => {
    try {
      await api("/api/logout", { method: "POST" });
    } catch (err) {
      showToast(err.message);
    }
    showAuth();
    setAuthMode("login");
  });

  api("/api/me")
    .then(showApp)
    .catch(() => showAuth());
})();
