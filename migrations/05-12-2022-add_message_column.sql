ALTER TABLE `purchase_history`
	ADD COLUMN `message` VARCHAR(150) NULL DEFAULT '' AFTER `stripeResponse`;


ALTER TABLE `purchase_history`
	CHANGE COLUMN `receiptUrl` `receiptUrl` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `status`,
	CHANGE COLUMN `stripeResponse` `stripeResponse` TEXT NULL COLLATE 'utf8mb4_general_ci' AFTER `receiptUrl`;