-- =====================================================
--  LeadFlow CRM — MySQL Database Schema
-- =====================================================

CREATE DATABASE IF NOT EXISTS leadflow_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE leadflow_crm;

-- ─── admins ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admins (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin (password: admin123)
INSERT INTO admins (name, email, password) VALUES
('Admin', 'admin@leadflow.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE id=id;

-- ─── teams ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS teams (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ─── members ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS members (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    phone      VARCHAR(20),
    password   VARCHAR(255) NOT NULL,
    role       ENUM('Telecaller','Team Lead','Manager') DEFAULT 'Telecaller',
    team_id    INT UNSIGNED,
    shift      ENUM('Morning','Evening','Night') DEFAULT 'Morning',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_member_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ─── leads ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS leads (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_name      VARCHAR(150) NOT NULL,
    father_name       VARCHAR(150),
    student_contact   VARCHAR(20),
    parent_contact    VARCHAR(20),
    stream            VARCHAR(100),
    category          VARCHAR(50),
    school_name       VARCHAR(200),
    district          VARCHAR(100),
    village           VARCHAR(100),
    course_interested VARCHAR(150),
    telecaller_name   VARCHAR(150),
    call_duration     VARCHAR(50),
    call_type         ENUM('Fresh','Follow Up') DEFAULT 'Fresh',
    availability_date DATE,
    lead_status       ENUM('New','Contacted','Interested','Follow Up','Converted','Not Interested') DEFAULT 'New',
    temperature       ENUM('Hot','Warm','Cold') DEFAULT 'Cold',
    warm_level        VARCHAR(50),
    next_follow_up    DATE,
    remarks           TEXT,
    admission_status  ENUM('Pending','Done','Cancelled') DEFAULT 'Pending',
    excel_created_by  VARCHAR(255) NULL,
    assigned_team_id  INT UNSIGNED,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_lead_team FOREIGN KEY (assigned_team_id) REFERENCES teams(id) ON DELETE SET NULL,
    UNIQUE KEY uq_lead (student_name, student_contact)
) ENGINE=InnoDB;

-- ─── lead_assignments (audit log) ────────────────────
CREATE TABLE IF NOT EXISTS lead_assignments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id     INT UNSIGNED NOT NULL,
    team_id     INT UNSIGNED NOT NULL,
    assigned_by INT UNSIGNED,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_la_lead   FOREIGN KEY (lead_id)     REFERENCES leads(id)  ON DELETE CASCADE,
    CONSTRAINT fk_la_team   FOREIGN KEY (team_id)     REFERENCES teams(id)  ON DELETE CASCADE,
    CONSTRAINT fk_la_admin  FOREIGN KEY (assigned_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;
