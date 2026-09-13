-- Philippine HRMS Database Schema
-- MySQL 8+

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =========================================================
-- ROLES & PERMISSIONS
-- =========================================================
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    module VARCHAR(50) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_module (module)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE role_permissions (
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- USERS
-- =========================================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NULL,
    role_id INT UNSIGNED NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    failed_attempts INT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    last_login_at DATETIME NULL,
    last_login_ip VARCHAR(45) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    INDEX idx_email (email),
    INDEX idx_role (role_id),
    INDEX idx_active (is_active),
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- ORGANIZATION
-- =========================================================
CREATE TABLE branches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    address VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE departments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    head_employee_id INT UNSIGNED NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE positions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    department_id INT UNSIGNED NULL,
    salary_min DECIMAL(12,2) NULL,
    salary_max DECIMAL(12,2) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_dept (department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE employment_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- EMPLOYEES
-- =========================================================
CREATE TABLE employees (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(80) NOT NULL,
    middle_name VARCHAR(80) NULL,
    last_name VARCHAR(80) NOT NULL,
    suffix VARCHAR(10) NULL,
    birth_date DATE NULL,
    gender ENUM('Male','Female','Other') NULL,
    civil_status ENUM('Single','Married','Widowed','Separated','Divorced') NULL,
    nationality VARCHAR(60) NULL DEFAULT 'Filipino',
    email VARCHAR(150) NULL,
    mobile_number VARCHAR(30) NULL,
    address_line VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    province VARCHAR(100) NULL,
    postal_code VARCHAR(10) NULL,
    emergency_contact_name VARCHAR(150) NULL,
    emergency_contact_relation VARCHAR(50) NULL,
    emergency_contact_number VARCHAR(30) NULL,
    department_id INT UNSIGNED NULL,
    position_id INT UNSIGNED NULL,
    branch_id INT UNSIGNED NULL,
    employment_type_id INT UNSIGNED NULL,
    supervisor_id INT UNSIGNED NULL,
    date_hired DATE NULL,
    regularization_date DATE NULL,
    date_separated DATE NULL,
    employment_status ENUM('Probationary','Regular','Contractual','Project-Based','Part-Time','Casual','Separated') NOT NULL DEFAULT 'Probationary',
    basic_salary DECIMAL(12,2) NOT NULL DEFAULT 0,
    pay_frequency ENUM('Monthly','Semi-Monthly','Weekly','Daily') NOT NULL DEFAULT 'Semi-Monthly',
    photo_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    INDEX idx_code (employee_code),
    INDEX idx_dept (department_id),
    INDEX idx_pos (position_id),
    INDEX idx_status (employment_status),
    INDEX idx_hired (date_hired),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE SET NULL,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL,
    FOREIGN KEY (employment_type_id) REFERENCES employment_types(id) ON DELETE SET NULL,
    FOREIGN KEY (supervisor_id) REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE employee_government_ids (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    sss_number VARCHAR(30) NULL,
    philhealth_number VARCHAR(30) NULL,
    pagibig_number VARCHAR(30) NULL,
    tin_number VARCHAR(30) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_emp (employee_id),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE employee_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    document_type VARCHAR(60) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NULL,
    mime_type VARCHAR(100) NULL,
    file_size INT UNSIGNED NULL,
    expires_at DATE NULL,
    uploaded_by INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    INDEX idx_emp (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- ATTENDANCE
-- =========================================================
CREATE TABLE attendance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    attendance_date DATE NOT NULL,
    time_in DATETIME NULL,
    time_out DATETIME NULL,
    hours_worked DECIMAL(6,2) NOT NULL DEFAULT 0,
    late_minutes INT UNSIGNED NOT NULL DEFAULT 0,
    undertime_minutes INT UNSIGNED NOT NULL DEFAULT 0,
    overtime_minutes INT UNSIGNED NOT NULL DEFAULT 0,
    night_diff_minutes INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('Present','Late','Absent','On Leave','Holiday','Rest Day','Official Business','Work From Home','Half Day') NOT NULL DEFAULT 'Absent',
    remarks VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_emp_date (employee_id, attendance_date),
    INDEX idx_date (attendance_date),
    INDEX idx_status (status),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE overtime_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    request_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    hours DECIMAL(5,2) NOT NULL,
    reason VARCHAR(500) NULL,
    status ENUM('Pending','Approved','Rejected','Cancelled') NOT NULL DEFAULT 'Pending',
    approved_by INT UNSIGNED NULL,
    approved_at DATETIME NULL,
    remarks VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_emp (employee_id),
    INDEX idx_status (status),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- LEAVE
-- =========================================================
CREATE TABLE leave_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    default_days DECIMAL(5,2) NOT NULL DEFAULT 0,
    is_paid TINYINT(1) NOT NULL DEFAULT 1,
    requires_attachment TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE leave_balances (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    leave_type_id INT UNSIGNED NOT NULL,
    year SMALLINT NOT NULL,
    entitled DECIMAL(6,2) NOT NULL DEFAULT 0,
    used DECIMAL(6,2) NOT NULL DEFAULT 0,
    balance DECIMAL(6,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_emp_type_year (employee_id, leave_type_id, year),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE leave_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    leave_type_id INT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    days DECIMAL(5,2) NOT NULL,
    reason TEXT NULL,
    attachment_path VARCHAR(255) NULL,
    status ENUM('Pending','Manager Approved','HR Approved','Approved','Rejected','Cancelled') NOT NULL DEFAULT 'Pending',
    manager_id INT UNSIGNED NULL,
    manager_approved_at DATETIME NULL,
    hr_id INT UNSIGNED NULL,
    hr_approved_at DATETIME NULL,
    remarks VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_emp (employee_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- HOLIDAYS
-- =========================================================
CREATE TABLE holidays (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    holiday_date DATE NOT NULL,
    holiday_type ENUM('Regular Holiday','Special Non-Working Holiday','Special Working Holiday','Company Holiday') NOT NULL,
    description VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (holiday_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- PAYROLL CONFIGURATION TABLES
-- =========================================================
CREATE TABLE government_contribution_tables (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    contribution_type ENUM('SSS','PhilHealth','Pag-IBIG') NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NULL,
    salary_min DECIMAL(12,2) NOT NULL DEFAULT 0,
    salary_max DECIMAL(12,2) NOT NULL DEFAULT 0,
    employee_rate DECIMAL(7,4) NOT NULL DEFAULT 0,
    employer_rate DECIMAL(7,4) NOT NULL DEFAULT 0,
    employee_fixed DECIMAL(12,2) NOT NULL DEFAULT 0,
    employer_fixed DECIMAL(12,2) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type_dates (contribution_type, effective_from, effective_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tax_tables (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tax_type ENUM('Semi-Monthly','Monthly','Weekly','Daily','Annual') NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NULL,
    income_min DECIMAL(12,2) NOT NULL DEFAULT 0,
    income_max DECIMAL(12,2) NOT NULL DEFAULT 0,
    base_tax DECIMAL(12,2) NOT NULL DEFAULT 0,
    excess_rate DECIMAL(7,4) NOT NULL DEFAULT 0,
    excess_over DECIMAL(12,2) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type_dates (tax_type, effective_from, effective_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- PAYROLL
-- =========================================================
CREATE TABLE payroll_periods (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    period_name VARCHAR(100) NOT NULL,
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    pay_date DATE NOT NULL,
    pay_frequency ENUM('Monthly','Semi-Monthly','Weekly','Daily') NOT NULL,
    status ENUM('Draft','Processing','For Review','Approved','Locked','Cancelled') NOT NULL DEFAULT 'Draft',
    created_by INT UNSIGNED NULL,
    approved_by INT UNSIGNED NULL,
    approved_at DATETIME NULL,
    locked_at DATETIME NULL,
    notes VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_dates (date_from, date_to)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payrolls (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payroll_period_id INT UNSIGNED NOT NULL,
    employee_id INT UNSIGNED NOT NULL,
    basic_salary DECIMAL(12,2) NOT NULL DEFAULT 0,
    days_worked DECIMAL(6,2) NOT NULL DEFAULT 0,
    gross_pay DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_earnings DECIMAL(12,2) NOT NULL DEFAULT 0,
    sss_employee DECIMAL(12,2) NOT NULL DEFAULT 0,
    sss_employer DECIMAL(12,2) NOT NULL DEFAULT 0,
    philhealth_employee DECIMAL(12,2) NOT NULL DEFAULT 0,
    philhealth_employer DECIMAL(12,2) NOT NULL DEFAULT 0,
    pagibig_employee DECIMAL(12,2) NOT NULL DEFAULT 0,
    pagibig_employer DECIMAL(12,2) NOT NULL DEFAULT 0,
    withholding_tax DECIMAL(12,2) NOT NULL DEFAULT 0,
    other_deductions DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_deductions DECIMAL(12,2) NOT NULL DEFAULT 0,
    net_pay DECIMAL(12,2) NOT NULL DEFAULT 0,
    status ENUM('Draft','Computed','Approved','Locked') NOT NULL DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_period_emp (payroll_period_id, employee_id),
    INDEX idx_emp (employee_id),
    FOREIGN KEY (payroll_period_id) REFERENCES payroll_periods(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payroll_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payroll_id INT UNSIGNED NOT NULL,
    item_type ENUM('Earning','Deduction') NOT NULL,
    item_code VARCHAR(40) NOT NULL,
    item_name VARCHAR(120) NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    quantity DECIMAL(10,2) NULL,
    rate DECIMAL(12,4) NULL,
    remarks VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_payroll (payroll_id),
    FOREIGN KEY (payroll_id) REFERENCES payrolls(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payslips (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payroll_id INT UNSIGNED NOT NULL,
    employee_id INT UNSIGNED NOT NULL,
    slip_number VARCHAR(40) NOT NULL UNIQUE,
    issued_at DATETIME NOT NULL,
    file_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_payroll (payroll_id),
    INDEX idx_emp (employee_id),
    FOREIGN KEY (payroll_id) REFERENCES payrolls(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- RECRUITMENT
-- =========================================================
CREATE TABLE job_postings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    department_id INT UNSIGNED NULL,
    position_id INT UNSIGNED NULL,
    description TEXT NULL,
    requirements TEXT NULL,
    employment_type_id INT UNSIGNED NULL,
    slots INT UNSIGNED NOT NULL DEFAULT 1,
    status ENUM('Open','Closed','On Hold') NOT NULL DEFAULT 'Open',
    posted_at DATE NULL,
    closes_at DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE applicants (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    email VARCHAR(150) NULL,
    mobile_number VARCHAR(30) NULL,
    resume_path VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_posting_id INT UNSIGNED NOT NULL,
    applicant_id INT UNSIGNED NOT NULL,
    status ENUM('New','Screening','Interview','Assessment','Shortlisted','Job Offer','Hired','Rejected') NOT NULL DEFAULT 'New',
    remarks VARCHAR(500) NULL,
    applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_job (job_posting_id),
    INDEX idx_status (status),
    FOREIGN KEY (job_posting_id) REFERENCES job_postings(id) ON DELETE CASCADE,
    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE interviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id INT UNSIGNED NOT NULL,
    scheduled_at DATETIME NOT NULL,
    interviewer_id INT UNSIGNED NULL,
    mode ENUM('In-Person','Phone','Video') NOT NULL DEFAULT 'In-Person',
    status ENUM('Scheduled','Completed','Cancelled','No Show') NOT NULL DEFAULT 'Scheduled',
    feedback TEXT NULL,
    rating TINYINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- TRAINING
-- =========================================================
CREATE TABLE trainings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    trainer VARCHAR(150) NULL,
    training_date DATE NULL,
    duration_hours DECIMAL(6,2) NULL,
    location VARCHAR(150) NULL,
    cost DECIMAL(12,2) NOT NULL DEFAULT 0,
    certificate_expiry DATE NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE employee_trainings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    training_id INT UNSIGNED NOT NULL,
    status ENUM('Assigned','In Progress','Completed','Failed','Cancelled') NOT NULL DEFAULT 'Assigned',
    score DECIMAL(5,2) NULL,
    certificate_path VARCHAR(255) NULL,
    completed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_emp_training (employee_id, training_id),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- PERFORMANCE
-- =========================================================
CREATE TABLE performance_reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    reviewer_id INT UNSIGNED NULL,
    review_period VARCHAR(50) NOT NULL,
    review_date DATE NOT NULL,
    attendance_score DECIMAL(5,2) NOT NULL DEFAULT 0,
    productivity_score DECIMAL(5,2) NOT NULL DEFAULT 0,
    quality_score DECIMAL(5,2) NOT NULL DEFAULT 0,
    teamwork_score DECIMAL(5,2) NOT NULL DEFAULT 0,
    overall_score DECIMAL(5,2) NOT NULL DEFAULT 0,
    manager_comments TEXT NULL,
    employee_comments TEXT NULL,
    status ENUM('Draft','Submitted','Acknowledged') NOT NULL DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_emp (employee_id),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE performance_goals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    review_id INT UNSIGNED NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    target_date DATE NULL,
    weight DECIMAL(5,2) NOT NULL DEFAULT 0,
    progress DECIMAL(5,2) NOT NULL DEFAULT 0,
    status ENUM('Active','Completed','Cancelled') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- ASSETS
-- =========================================================
CREATE TABLE employee_assets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_code VARCHAR(40) NOT NULL UNIQUE,
    asset_name VARCHAR(150) NOT NULL,
    serial_number VARCHAR(80) NULL,
    category VARCHAR(60) NOT NULL,
    employee_id INT UNSIGNED NULL,
    date_issued DATE NULL,
    condition_on_issue VARCHAR(60) NULL,
    date_returned DATE NULL,
    condition_on_return VARCHAR(60) NULL,
    status ENUM('Available','Assigned','Returned','Damaged','Lost','Retired') NOT NULL DEFAULT 'Available',
    remarks VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_emp (employee_id),
    INDEX idx_status (status),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- SEPARATION
-- =========================================================
CREATE TABLE resignations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employee_id INT UNSIGNED NOT NULL,
    separation_type ENUM('Resignation','Termination','Retirement','End of Contract') NOT NULL,
    effective_date DATE NOT NULL,
    reason TEXT NULL,
    status ENUM('Submitted','Clearance In Progress','Approved','Final Payroll','Completed','Cancelled') NOT NULL DEFAULT 'Submitted',
    submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_emp (employee_id),
    INDEX idx_status (status),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE clearance (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    resignation_id INT UNSIGNED NOT NULL,
    department VARCHAR(60) NOT NULL,
    cleared TINYINT(1) NOT NULL DEFAULT 0,
    cleared_by INT UNSIGNED NULL,
    cleared_at DATETIME NULL,
    remarks VARCHAR(255) NULL,
    FOREIGN KEY (resignation_id) REFERENCES resignations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- NOTIFICATIONS & AUDIT
-- =========================================================
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    message VARCHAR(500) NOT NULL,
    link VARCHAR(255) NULL,
    type VARCHAR(40) NOT NULL DEFAULT 'info',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id, is_read),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    user_email VARCHAR(150) NULL,
    action VARCHAR(80) NOT NULL,
    module VARCHAR(60) NOT NULL,
    record_id VARCHAR(60) NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_module (module),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE system_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(80) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_group VARCHAR(40) NOT NULL DEFAULT 'general',
    description VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;