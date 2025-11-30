-- MySQL Schema for E-Guidance Platform
-- This schema is designed for FreeSQLDatabase.com

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(20) NOT NULL DEFAULT 'student',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Profiles table
CREATE TABLE IF NOT EXISTS `profiles` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `name` VARCHAR(100),
    `email` VARCHAR(100),
    `phone` VARCHAR(20),
    `bio` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Appointments table
CREATE TABLE IF NOT EXISTS `appointments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `student_id` INT(11) NOT NULL,
    `counselor_id` INT(11) NOT NULL,
    `date` DATE NOT NULL,
    `time` TIME NOT NULL,
    `purpose` TEXT,
    `status` VARCHAR(20) DEFAULT 'pending',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`counselor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Announcements table
CREATE TABLE IF NOT EXISTS `announcements` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `counselor_id` INT(11) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `is_published` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`counselor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resources table
CREATE TABLE IF NOT EXISTS `resources` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `file_path` VARCHAR(255) NOT NULL,
    `type` VARCHAR(20) NOT NULL DEFAULT 'document',
    `thumbnail` VARCHAR(255),
    `duration` INT(11),
    `views` INT(11) NOT NULL DEFAULT 0,
    `counselor_id` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`counselor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student feedback table
CREATE TABLE IF NOT EXISTS `student_feedback` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `student_id` INT(11) NOT NULL,
    `counselor_id` INT(11) NOT NULL,
    `rating` INT(11) CHECK (`rating` >= 1 AND `rating` <= 5),
    `feedback` TEXT,
    `status` VARCHAR(20) DEFAULT 'new',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`counselor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Career assessments table
CREATE TABLE IF NOT EXISTS `career_assessments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `student_id` INT(11) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `type` VARCHAR(20) NOT NULL,
    `score` DECIMAL(5,2),
    `results` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `chk_type` CHECK (`type` IN ('interest', 'aptitude', 'personality'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Career pathways table
CREATE TABLE IF NOT EXISTS `career_pathways` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `field` VARCHAR(100) NOT NULL,
    `education_required` TEXT,
    `skills_required` TEXT,
    `job_outlook` TEXT,
    `salary_range` VARCHAR(100),
    `created_by` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Scholarships table
CREATE TABLE IF NOT EXISTS `scholarships` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `provider` VARCHAR(255) NOT NULL,
    `eligibility_criteria` TEXT,
    `application_deadline` DATE,
    `award_amount` VARCHAR(100),
    `application_link` VARCHAR(500),
    `created_by` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wellness forms table
CREATE TABLE IF NOT EXISTS `wellness_forms` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `is_active` BOOLEAN DEFAULT TRUE,
    `counselor_id` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`counselor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wellness form questions table
CREATE TABLE IF NOT EXISTS `wellness_form_questions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `wellness_form_id` INT(11) NOT NULL,
    `question_text` TEXT NOT NULL,
    `question_type` VARCHAR(50) NOT NULL,
    `scale_min` INT(11) DEFAULT 1,
    `scale_max` INT(11) DEFAULT 5,
    `is_required` BOOLEAN DEFAULT TRUE,
    `sort_order` INT(11) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`wellness_form_id`) REFERENCES `wellness_forms`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wellness form responses table
CREATE TABLE IF NOT EXISTS `wellness_form_responses` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `wellness_form_id` INT(11) NOT NULL,
    `student_id` INT(11) NOT NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`wellness_form_id`) REFERENCES `wellness_forms`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wellness form answers table
CREATE TABLE IF NOT EXISTS `wellness_form_answers` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `response_id` INT(11) NOT NULL,
    `question_id` INT(11) NOT NULL,
    `answer_text` TEXT,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`response_id`) REFERENCES `wellness_form_responses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`question_id`) REFERENCES `wellness_form_questions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes for better performance
CREATE INDEX `idx_users_role` ON `users`(`role`);
CREATE INDEX `idx_appointments_date` ON `appointments`(`date`);
CREATE INDEX `idx_appointments_status` ON `appointments`(`status`);
CREATE INDEX `idx_resources_type` ON `resources`(`type`);
CREATE INDEX `idx_career_assessments_type` ON `career_assessments`(`type`);
CREATE INDEX `idx_career_pathways_field` ON `career_pathways`(`field`);
CREATE INDEX `idx_wellness_forms_active` ON `wellness_forms`(`is_active`);
