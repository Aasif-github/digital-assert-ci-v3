-- Creating the users table to store user information
CREATE TABLE `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Creating the roles table to store different roles
CREATE TABLE `roles` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Creating the permissions table to store permissions
CREATE TABLE `permissions` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Creating the user_roles table to assign roles to users (many-to-many relationship)
CREATE TABLE `user_roles` (
    `user_id` INT(11) UNSIGNED NOT NULL,
    `role_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `role_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Creating the role_permissions table to assign permissions to roles (many-to-many relationship)
CREATE TABLE `role_permissions` (
    `role_id` INT(11) UNSIGNED NOT NULL,
    `permission_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserting sample data for roles
INSERT INTO `roles` (`name`, `description`) VALUES
('admin', 'Administrator with full access'),
('editor', 'Editor with limited access to content management'),
('viewer', 'Viewer with read-only access');

-- Inserting sample data for permissions
INSERT INTO `permissions` (`name`, `description`) VALUES
('create_post', 'Permission to create posts'),
('edit_post', 'Permission to edit posts'),
('delete_post', 'Permission to delete posts'),
('view_post', 'Permission to view posts'),
('manage_users', 'Permission to manage users');

-- Inserting sample data for role_permissions (assigning permissions to roles)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
((SELECT `id` FROM `roles` WHERE `name` = 'admin'), (SELECT `id` FROM `permissions` WHERE `name` = 'create_post')),
((SELECT `id` FROM `roles` WHERE `name` = 'admin'), (SELECT `id` FROM `permissions` WHERE `name` = 'edit_post')),
((SELECT `id` FROM `roles` WHERE `name` = 'admin'), (SELECT `id` FROM `permissions` WHERE `name` = 'delete_post')),
((SELECT `id` FROM `roles` WHERE `name` = 'admin'), (SELECT `id` FROM `permissions` WHERE `name` = 'view_post')),
((SELECT `id` FROM `roles` WHERE `name` = 'admin'), (SELECT `id` FROM `permissions` WHERE `name` = 'manage_users')),
((SELECT `id` FROM `roles` WHERE `name` = 'editor'), (SELECT `id` FROM `permissions` WHERE `name` = 'create_post')),
((SELECT `id` FROM `roles` WHERE `name` = 'editor'), (SELECT `id` FROM `permissions` WHERE `name` = 'edit_post')),
((SELECT `id` FROM `roles` WHERE `name` = 'editor'), (SELECT `id` FROM `permissions` WHERE `name` = 'view_post')),
((SELECT `id` FROM `roles` WHERE `name` = 'viewer'), (SELECT `id` FROM `permissions` WHERE `name` = 'view_post'));

-- Inserting a sample user
INSERT INTO `users` (`username`, `email`, `password`) VALUES
('admin_user', 'admin@example.com', '$2y$10$examplehashedpassword'); -- Replace with actual hashed password

-- Assigning the admin role to the sample user
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
((SELECT `id` FROM `users` WHERE `username` = 'admin_user'), (SELECT `id` FROM `roles` WHERE `name` = 'admin'));