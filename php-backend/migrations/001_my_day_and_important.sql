USE taskflow;

ALTER TABLE tasks
    ADD COLUMN my_day_date DATE NULL AFTER due_date,
    ADD COLUMN is_important BOOLEAN NOT NULL DEFAULT FALSE AFTER my_day_date,
    ADD INDEX ix_tasks_my_day (user_id, my_day_date),
    ADD INDEX ix_tasks_important (user_id, is_important);
