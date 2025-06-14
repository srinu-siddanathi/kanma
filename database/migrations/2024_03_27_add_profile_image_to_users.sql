-- Add profile_image column to users table
ALTER TABLE `users`
ADD COLUMN `profile_image` varchar(255) DEFAULT NULL AFTER `last_status_update`; 