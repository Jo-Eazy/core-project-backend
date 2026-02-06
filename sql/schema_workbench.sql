/*
-- Run in MySQL Workbench: this creates the database, drops existing objects if present,
-- and (re)creates tables and views. (You can also re-run it, according to CHATGPT)
 
 
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS modern_tech_solutions
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE modern_tech_solutions;

-- Drop views/tables if they already exist (safe re-run)
DROP VIEW IF EXISTS vw_recent_attendance;
DROP VIEW IF EXISTS vw_employee_summary;

DROP TABLE IF EXISTS payroll;
DROP TABLE IF EXISTS leave_requests;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS employees;

-- Employees
CREATE TABLE employees (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(100),
    department VARCHAR(100),
    salary DECIMAL(12,2) NOT NULL DEFAULT 0,
    employment_history TEXT,
    contact VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendance
CREATE TABLE attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present','Absent') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_emp_date (employee_id, date),
    INDEX idx_att_date (date),
    CONSTRAINT fk_att_employee FOREIGN KEY (employee_id) REFERENCES employees(id)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Leave requests (single-day requests represented with start_date=end_date for your JSON)
CREATE TABLE leave_requests (
    leave_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Prevent exact duplicate leave requests for same employee and dates
    UNIQUE KEY uniq_leave_emp_dates (employee_id, start_date, end_date),
    INDEX idx_leave_status (status),
    CONSTRAINT fk_leave_employee FOREIGN KEY (employee_id) REFERENCES employees(id)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payroll (with uniqueness per employee+month to avoid duplicates on seed)
CREATE TABLE payroll (
    payroll_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    month VARCHAR(50) NOT NULL,
    hours_worked INT NOT NULL,
    overtime_hours INT DEFAULT 0,
    leave_deductions INT DEFAULT 0,
    bonus DECIMAL(12,2) DEFAULT 0,
    final_salary DECIMAL(12,2) NOT NULL,
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_pay_emp_month (employee_id, month),
    INDEX idx_pay_employee (employee_id),
    CONSTRAINT fk_pay_employee FOREIGN KEY (employee_id) REFERENCES employees(id)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Helpful read-only views
CREATE OR REPLACE VIEW vw_employee_summary AS
SELECT e.id, e.name, e.position, e.department, e.salary,
  (SELECT COUNT(*) FROM attendance a WHERE a.employee_id = e.id) AS attendance_count,
  (SELECT COUNT(*) FROM leave_requests l WHERE l.employee_id = e.id AND l.status = 'pending') AS pending_leaves
FROM employees e;

CREATE OR REPLACE VIEW vw_recent_attendance AS
SELECT a.attendance_id, a.employee_id, e.name AS employee_name, a.date, a.status
FROM attendance a
JOIN employees e ON e.id = a.employee_id
ORDER BY a.date DESC, a.attendance_id DESC;

SET FOREIGN_KEY_CHECKS = 1;

