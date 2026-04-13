CREATE TABLE IF NOT EXISTS `vaccine_batches` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `vaccine_id` INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `quantity_added` INT(11) NOT NULL DEFAULT 0,
    `quantity_remaining` INT(11) NOT NULL DEFAULT 0,
    `manufacture_date` DATE NOT NULL,
    `expiration_date` DATE NOT NULL,
    `updated_at` INT(11) NOT NULL,
    `created_at` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_vaccine_batches_vaccine_id` (`vaccine_id`),
    KEY `idx_vaccine_batches_expiration_date` (`expiration_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
