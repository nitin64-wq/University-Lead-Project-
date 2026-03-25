-- Add excel_created_by column to leads table
ALTER TABLE `leads` ADD COLUMN `excel_created_by` VARCHAR(255) NULL AFTER `admission_status`;
