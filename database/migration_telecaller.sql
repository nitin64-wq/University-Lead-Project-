-- =====================================================
--  LeadFlow CRM — Telecaller Migration
--  Run this to add telecaller features
-- =====================================================

USE leadflow_crm;

-- Add assigned_member_id and last_call_date to leads
ALTER TABLE leads
    ADD COLUMN IF NOT EXISTS assigned_member_id INT UNSIGNED AFTER assigned_team_id,
    ADD COLUMN IF NOT EXISTS last_call_date DATE AFTER assigned_member_id,
    ADD CONSTRAINT fk_lead_member FOREIGN KEY (assigned_member_id) REFERENCES members(id) ON DELETE SET NULL;

-- ─── call_logs ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS call_logs (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id         INT UNSIGNED NOT NULL,
    telecaller_id   INT UNSIGNED NOT NULL,
    call_duration   VARCHAR(50),
    call_type       ENUM('Fresh','Follow Up') DEFAULT 'Fresh',
    lead_status     ENUM('New','Contacted','Interested','Follow Up','Converted','Not Interested','No Response','Call Not Picked') DEFAULT 'New',
    temperature     ENUM('Hot','Warm','Cold') DEFAULT 'Cold',
    warm_level      VARCHAR(50),
    next_follow_up  DATE,
    remarks         TEXT,
    admission_status ENUM('Pending','Done','Cancelled') DEFAULT 'Pending',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_log_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    CONSTRAINT fk_log_member FOREIGN KEY (telecaller_id) REFERENCES members(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Update lead_status to support more values
ALTER TABLE leads
    MODIFY COLUMN lead_status ENUM('New','Contacted','Interested','Follow Up','Converted','Not Interested','No Response','Call Not Picked') DEFAULT 'New';

-- ─── notifications ────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id   INT UNSIGNED NOT NULL,
    title       VARCHAR(200) NOT NULL,
    message     TEXT,
    is_read     TINYINT(1) DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
) ENGINE=InnoDB;
