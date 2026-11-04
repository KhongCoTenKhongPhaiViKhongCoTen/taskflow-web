        <div
            class="modal fade"
            id="task-modal"
            tabindex="-1"
            aria-labelledby="task-modal-title"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content task-modal-content">
                    <form id="task-form" novalidate>
                        <div class="modal-header">
                            <div>
                                <span class="eyebrow">THÊM VÀO DANH SÁCH</span>
                                <h2 class="modal-title" id="task-modal-title">
                                    Tạo công việc
                                </h2>
                            </div>
                            <button
                                type="button"
                                class="modal-close"
                                data-bs-dismiss="modal"
                                aria-label="Đóng"
                            >
                                ×
                            </button>
                        </div>
                        <div class="modal-body">
                            <input id="task-id" type="hidden" />
                            <div class="field-group">
                                <label for="task-title"
                                    >Tên công việc
                                    <span class="required-mark">*</span></label
                                ><input
                                    class="form-control"
                                    id="task-title"
                                    maxlength="160"
                                    placeholder="Ví dụ: Hoàn thiện báo cáo"
                                    required
                                />
                            </div>
                            <div class="field-group">
                                <label for="task-description"
                                    >Mô tả
                                    <span class="optional-label"
                                        >Không bắt buộc</span
                                    ></label
                                ><textarea
                                    class="form-control"
                                    id="task-description"
                                    rows="3"
                                    maxlength="10000"
                                    placeholder="Thêm một vài chi tiết..."
                                ></textarea>
                            </div>
                            <div class="modal-fields">
                                <div class="field-group">
                                    <label for="task-status">Trạng thái</label
                                    ><select
                                        class="form-select"
                                        id="task-status"
                                    >
                                        <option value="todo">Cần làm</option>
                                        <option value="doing">Đang làm</option>
                                        <option value="done">Hoàn thành</option>
                                    </select>
                                </div>
                                <div class="field-group">
                                    <label for="task-priority"
                                        >Mức ưu tiên</label
                                    ><select
                                        class="form-select"
                                        id="task-priority"
                                    >
                                        <option value="low">Thấp</option>
                                        <option value="medium" selected>
                                            Trung bình
                                        </option>
                                        <option value="high">Cao</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field-group">
                                <label for="task-due-date"
                                    >Hạn hoàn thành
                                    <span class="optional-label"
                                        >Không bắt buộc</span
                                    ></label
                                ><input
                                    class="form-control"
                                    id="task-due-date"
                                    type="date"
                                />
                            </div>
                            <div class="task-extra-options">
                                <label
                                    ><input id="task-my-day" type="checkbox" />
                                    Thêm vào Ngày của tôi</label
                                ><label
                                    ><input
                                        id="task-important"
                                        type="checkbox"
                                    />
                                    Đánh dấu quan trọng</label
                                >
                            </div>
                            <div
                                id="task-form-error"
                                class="form-error"
                                role="alert"
                            ></div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn-outline-custom"
                                data-bs-dismiss="modal"
                            >
                                Hủy</button
                            ><button
                                type="submit"
                                class="btn-primary-custom"
                                id="task-save-button"
                            >
                                <span>Lưu công việc</span
                                ><span aria-hidden="true">→</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div
                id="app-toast"
                class="toast app-toast"
                role="status"
                aria-live="polite"
                aria-atomic="true"
            >
                <div class="toast-body">
                    <span class="toast-check">✓</span
                    ><span id="toast-message">Đã cập nhật.</span>
                </div>
            </div>
        </div>
