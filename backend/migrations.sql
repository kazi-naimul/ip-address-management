-- Create migrations table
CREATE TABLE IF NOT EXISTS migrations (
    id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    migration varchar(255) NOT NULL UNIQUE,
    batch int NOT NULL
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name varchar(255) NOT NULL,
    email varchar(255) NOT NULL UNIQUE,
    password varchar(255) NOT NULL,
    remember_token varchar(100),
    created_at timestamp NULL,
    updated_at timestamp NULL,
    KEY `users_email_index` (`email`)
);

-- Create ip_addresses table
CREATE TABLE IF NOT EXISTS ip_addresses (
    id bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ip_address varchar(255) NOT NULL UNIQUE,
    label varchar(255) NOT NULL,
    created_by bigint unsigned NOT NULL,
    created_at timestamp NULL,
    updated_at timestamp NULL,
    CONSTRAINT ip_addresses_created_by_foreign FOREIGN KEY (created_by) REFERENCES users(id),
    KEY `ip_addresses_ip_address_index` (`ip_address`)
);

-- Create audit_logs table
CREATE TABLE IF NOT EXISTS audit_logs (
    id bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id bigint unsigned NOT NULL,
    action varchar(255) NOT NULL,
    model_type varchar(255),
    model_id bigint unsigned,
    details json,
    created_at timestamp NULL,
    updated_at timestamp NULL,
    CONSTRAINT audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id),
    KEY `audit_logs_user_id_index` (`user_id`)
);

-- Record migrations
INSERT IGNORE INTO migrations (migration, batch) VALUES
('2026_01_01_094142_create_users_table', 1),
('2026_01_02_094232_create_ip_addresses_table', 1),
('2026_01_03_082323_create_audit_logs_table', 1);
