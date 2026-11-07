-- events table
CREATE TABLE IF NOT EXISTS `events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `family_id` BIGINT UNSIGNED NOT NULL,
    `creator_user_id` BIGINT UNSIGNED NULL,
    `type` ENUM('birthday', 'death_anniversary', 'marriage_anniversary', 'marriage', 'family_reunion') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `event_date` DATETIME NOT NULL,
    `location` VARCHAR(500) NULL,
    `cover_image` VARCHAR(500) NULL,
    `is_private` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `events_family_id_index` (`family_id`),
    INDEX `events_event_date_index` (`event_date`),
    CONSTRAINT `events_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE,
    CONSTRAINT `events_creator_user_id_foreign` FOREIGN KEY (`creator_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- event_attendances table
CREATE TABLE IF NOT EXISTS `event_attendances` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `event_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('going', 'maybe', 'no') NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `event_attendance_unique` (`event_id`, `user_id`),
    INDEX `event_attendances_event_id_index` (`event_id`),
    INDEX `event_attendances_user_id_index` (`user_id`),
    CONSTRAINT `event_attendances_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `event_attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- events table (PostgreSQL)
-- CREATE TABLE IF NOT EXISTS "events" (
--     "id" BIGSERIAL PRIMARY KEY,
--     "family_id" BIGINT UNSIGNED NOT NULL,
--     "creator_user_id" BIGINT UNSIGNED,
--     "type" VARCHAR(50) NOT NULL,
--     "title" VARCHAR(255) NOT NULL,
--     "description" TEXT,
--     "event_date" TIMESTAMP NOT NULL,
--     "location" VARCHAR(500),
--     "cover_image" VARCHAR(500),
--     "is_private" BOOLEAN NOT NULL DEFAULT FALSE,
--     "created_at" TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
--     "updated_at" TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
-- );
-- 
-- CREATE INDEX IF NOT EXISTS "events_family_id_index" ON "events" ("family_id");
-- CREATE INDEX IF NOT EXISTS "events_event_date_index" ON "events" ("event_date");
-- 
-- ALTER TABLE "events" ADD CONSTRAINT "events_family_id_foreign" FOREIGN KEY ("family_id") REFERENCES "families" ("id") ON DELETE CASCADE;
-- ALTER TABLE "events" ADD CONSTRAINT "events_creator_user_id_foreign" FOREIGN KEY ("creator_user_id") REFERENCES "users" ("id") ON DELETE SET NULL;

-- event_attendances table (PostgreSQL)
-- CREATE TABLE IF NOT EXISTS "event_attendances" (
--     "id" BIGSERIAL PRIMARY KEY,
--     "event_id" BIGINT UNSIGNED NOT NULL,
--     "user_id" BIGINT UNSIGNED NOT NULL,
--     "status" VARCHAR(10) NOT NULL,
--     "created_at" TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
--     "updated_at" TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
-- );
-- 
-- CREATE UNIQUE INDEX IF NOT EXISTS "event_attendance_unique" ON "event_attendances" ("event_id", "user_id");
-- CREATE INDEX IF NOT EXISTS "event_attendances_event_id_index" ON "event_attendances" ("event_id");
-- CREATE INDEX IF NOT EXISTS "event_attendances_user_id_index" ON "event_attendances" ("user_id");
-- 
-- ALTER TABLE "event_attendances" ADD CONSTRAINT "event_attendances_event_id_foreign" FOREIGN KEY ("event_id") REFERENCES "events" ("id") ON DELETE CASCADE;
-- ALTER TABLE "event_attendances" ADD CONSTRAINT "event_attendances_user_id_foreign" FOREIGN KEY ("user_id") REFERENCES "users" ("id") ON DELETE CASCADE;