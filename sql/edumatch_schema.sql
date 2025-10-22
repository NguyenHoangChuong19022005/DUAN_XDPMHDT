-- EduMatch Database Schema
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` enum('student','admin','provider') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL UNIQUE,
  `gpa` decimal(3,2) DEFAULT 3.00,
  `major` varchar(100) DEFAULT 'CNTT',
  `phone` varchar(20),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL UNIQUE,
  `organization` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `scholarships` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `major` varchar(100) NOT NULL,
  `country` varchar(50) NOT NULL,
  `gpa_min` decimal(3,2) NOT NULL,
  `deadline` date NOT NULL,
  `amount` decimal(10,2) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `cv` varchar(255),
  `cover_letter` text,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `scholarship_id` (`scholarship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorite` (`student_id`,`scholarship_id`),
  KEY `student_id` (`student_id`),
  KEY `scholarship_id` (`scholarship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `scholarship_id` (`scholarship_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`)
);

-- ✅ INSERT TEST USERS
INSERT INTO `users` (`email`, `password`, `name`, `role`) VALUES 
('admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin EduMatch', 'admin'),
('student@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', 'student'),
('harvard@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Harvard University', 'provider'),
('oxford@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Oxford University', 'provider');

-- ✅ INSERT STUDENT & PROVIDERS
INSERT INTO `students` (`user_id`, `gpa`, `major`, `phone`) VALUES (2, 3.75, 'CNTT', '0123456789');
INSERT INTO `providers` (`user_id`, `organization`) VALUES (3, 'Harvard University'), (4, 'Oxford University');

-- ✅ INSERT 10 SCHOLARSHIPS
INSERT INTO `scholarships` (`provider_id`, `title`, `description`, `major`, `country`, `gpa_min`, `deadline`, `amount`) VALUES
(1, 'Harvard CS Full Scholarship 2026', 'Full tuition + living expenses', 'CNTT', 'USA', 3.70, '2026-03-15', 50000),
(1, 'Harvard Business Scholarship', 'MBA program full funding', 'Kinh tế', 'USA', 3.50, '2026-02-28', 75000),
(2, 'Oxford AI Research Grant', 'PhD funding AI/ML research', 'CNTT', 'UK', 3.80, '2026-01-31', 40000),
(1, 'Harvard Medical Full Ride', 'MD program scholarship', 'Y khoa', 'USA', 3.90, '2026-04-10', 60000),
(2, 'Oxford Engineering Excellence', 'Engineering scholarship', 'Kỹ thuật', 'UK', 3.60, '2026-02-15', 35000),
(1, 'Harvard Data Science Award', 'MS Data Science funding', 'CNTT', 'USA', 3.65, '2026-03-01', 45000),
(2, 'Oxford Economics Fellowship', 'Economics graduate program', 'Kinh tế', 'UK', 3.70, '2026-01-20', 42000),
(1, 'Harvard Law Merit Scholarship', 'JD program funding', 'Luật', 'USA', 3.55, '2026-03-30', 30000),
(2, 'Oxford Physics Research', 'Physics PhD scholarship', 'Vật lý', 'UK', 3.85, '2026-02-10', 38000),
(1, 'Harvard International Grant', 'Undergrad intl students', 'Tất cả', 'USA', 3.40, '2026-04-20', 25000);