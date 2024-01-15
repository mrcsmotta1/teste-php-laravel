CREATE TABLE IF NOT EXISTS `migrations` (
    `id` integer primary key AUTO_INCREMENT not null,
    `migration` varchar (255) not null,
    `batch` integer not null
);
CREATE TABLE IF NOT EXISTS `users` (
    `id` INTEGER PRIMARY KEY AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `email_verified_at` DATETIME,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(255),
    `created_at` DATETIME,
    `updated_at` DATETIME
);

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email` varchar (255) not null,
    `token` varchar (255) not null,
    `created_at` datetime,
    primary key (`email`)
);
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` integer primary key AUTO_INCREMENT not null,
    `uuid` varchar (255) UNIQUE not null,
    `connection` text not null,
    `queue` text not null,
    `payload` text not null,
    `exception` text not null,
    `failed_at` datetime not null default CURRENT_TIMESTAMP
);

INSERT INTO migrations
VALUES(1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO migrations
VALUES(
        2,
        '2014_10_12_100000_create_password_reset_tokens_table',
        1
    );
INSERT INTO migrations
VALUES(3, '2019_08_19_000000_create_failed_jobs_table', 1);

