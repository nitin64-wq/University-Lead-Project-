-- Add inline-editable columns for telecaller lead management
USE leadflow_crm;

ALTER TABLE `leads`
    ADD COLUMN IF NOT EXISTS `warm_case`               VARCHAR(255) NULL AFTER `warm_level`,
    ADD COLUMN IF NOT EXISTS `cold_reason`             VARCHAR(255) NULL AFTER `warm_case`,
    ADD COLUMN IF NOT EXISTS `not_communicated_reason` VARCHAR(255) NULL AFTER `cold_reason`,
    ADD COLUMN IF NOT EXISTS `no_pursue_reason`        TEXT         NULL AFTER `not_communicated_reason`;

-- Allow temperature to store 'HOT' and 'Not Communicated' (change from ENUM to VARCHAR)
ALTER TABLE `leads`
    MODIFY COLUMN `temperature` VARCHAR(50) DEFAULT 'Cold';
