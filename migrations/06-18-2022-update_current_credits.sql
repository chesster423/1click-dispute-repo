ALTER TABLE `users`
	CHANGE COLUMN `currentCredits` `currentCredits` FLOAT NOT NULL DEFAULT 0 AFTER `cardID`;