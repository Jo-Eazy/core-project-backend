/*
-- Run after schema_workbench.sql. Uses FOREIGN_KEY_CHECKS toggling to allow re-seeding.
USE `modern_tech_solutions`;

SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;

-- Employees (ids kept to match employee_info.json)
INSERT INTO employees (id, name, position, department, salary, employment_history, contact)
VALUES
(1, 'Sibongile Nkosi', 'Software Engineer', 'Development', 70000, 'Joined in 2015, promoted to Senior in 2018', 'sibongile.nkosi@moderntech.com'),
(2, 'Lungile Moyo', 'HR Manager', 'HR', 80000, 'Joined in 2013, promoted to Manager in 2017', 'lungile.moyo@moderntech.com'),
(3, 'Thabo Molefe', 'Quality Analyst', 'QA', 55000, 'Joined in 2018', 'thabo.molefe@moderntech.com'),
(4, 'Keshav Naidoo', 'Sales Representative', 'Sales', 60000, 'Joined in 2020', 'keshav.naidoo@moderntech.com'),
(5, 'Zanele Khumalo', 'Marketing Specialist', 'Marketing', 58000, 'Joined in 2019', 'zanele.khumalo@moderntech.com'),
(6, 'Sipho Zulu', 'UI/UX Designer', 'Design', 65000, 'Joined in 2016', 'sipho.zulu@moderntech.com'),
(7, 'Naledi Moeketsi', 'DevOps Engineer', 'IT', 72000, 'Joined in 2017', 'naledi.moeketsi@moderntech.com'),
(8, 'Farai Gumbo', 'Content Strategist', 'Marketing', 56000, 'Joined in 2021', 'farai.gumbo@moderntech.com'),
(9, 'Karabo Dlamini', 'Accountant', 'Finance', 62000, 'Joined in 2018', 'karabo.dlamini@moderntech.com'),
(10,'Fatima Patel', 'Customer Support Lead', 'Support', 58000, 'Joined in 2016', 'fatima.patel@moderntech.com')
ON DUPLICATE KEY UPDATE
  name=VALUES(name),
  position=VALUES(position),
  department=VALUES(department),
  salary=VALUES(salary),
  employment_history=VALUES(employment_history),
  contact=VALUES(contact);

-- Attendance (from attendance.json) - use INSERT IGNORE to skip duplicate employee+date rows
INSERT IGNORE INTO attendance (employee_id, date, status)
VALUES
(1,'2025-07-25','Present'),
(1,'2025-07-26','Absent'),
(1,'2025-07-27','Present'),
(1,'2025-07-28','Present'),
(1,'2025-07-29','Present'),

(2,'2025-07-25','Present'),
(2,'2025-07-26','Present'),
(2,'2025-07-27','Absent'),
(2,'2025-07-28','Present'),
(2,'2025-07-29','Present'),

(3,'2025-07-25','Present'),
(3,'2025-07-26','Present'),
(3,'2025-07-27','Present'),
(3,'2025-07-28','Absent'),
(3,'2025-07-29','Present'),

(4,'2025-07-25','Absent'),
(4,'2025-07-26','Present'),
(4,'2025-07-27','Present'),
(4,'2025-07-28','Present'),
(4,'2025-07-29','Present'),

(5,'2025-07-25','Present'),
(5,'2025-07-26','Present'),
(5,'2025-07-27','Absent'),
(5,'2025-07-28','Present'),
(5,'2025-07-29','Present'),

(6,'2025-07-25','Present'),
(6,'2025-07-26','Present'),
(6,'2025-07-27','Absent'),
(6,'2025-07-28','Present'),
(6,'2025-07-29','Present'),

(7,'2025-07-25','Present'),
(7,'2025-07-26','Present'),
(7,'2025-07-27','Present'),
(7,'2025-07-28','Absent'),
(7,'2025-07-29','Present'),

(8,'2025-07-25','Present'),
(8,'2025-07-26','Absent'),
(8,'2025-07-27','Present'),
(8,'2025-07-28','Present'),
(8,'2025-07-29','Present'),

(9,'2025-07-25','Present'),
(9,'2025-07-26','Present'),
(9,'2025-07-27','Present'),
(9,'2025-07-28','Absent'),
(9,'2025-07-29','Present'),

(10,'2025-07-25','Present'),
(10,'2025-07-26','Present'),
(10,'2025-07-27','Absent'),
(10,'2025-07-28','Present'),
(10,'2025-07-29','Present');

-- Leave requests (mapped to enum values)
INSERT INTO leave_requests (employee_id, start_date, end_date, status, reason)
VALUES
(1,'2025-07-22','2025-07-22','approved','Sick Leave'),
(1,'2024-12-01','2024-12-01','pending','Personal'),

(2,'2025-07-15','2025-07-15','rejected','Family Responsibility'),
(2,'2024-12-02','2024-12-02','approved','Vacation'),

(3,'2025-07-10','2025-07-10','approved','Medical Appointment'),
(3,'2024-12-05','2024-12-05','pending','Personal'),

(4,'2025-07-20','2025-07-20','approved','Bereavement'),

(5,'2024-12-01','2024-12-01','pending','Childcare'),

(6,'2025-07-18','2025-07-18','approved','Sick Leave'),

(7,'2025-07-22','2025-07-22','pending','Vacation'),

(8,'2024-12-02','2024-12-02','approved','Medical Appointment'),

(9,'2025-07-19','2025-07-19','rejected','Childcare'),

(10,'2024-12-03','2024-12-03','pending','Vacation')
ON DUPLICATE KEY UPDATE
  status = VALUES(status),
  reason = VALUES(reason);

-- Payroll (seed payroll table) - uses ON DUPLICATE KEY UPDATE because of unique (employee_id, month)
INSERT INTO payroll (employee_id, month, hours_worked, overtime_hours, leave_deductions, bonus, final_salary)
VALUES
(1,'July 2025',160,0,8,0,69500),
(2,'July 2025',150,0,10,0,79000),
(3,'July 2025',170,0,4,0,54800),
(4,'July 2025',165,0,6,0,59700),
(5,'July 2025',158,0,5,0,57850),
(6,'July 2025',168,0,2,0,64800),
(7,'July 2025',175,0,3,0,71800),
(8,'July 2025',160,0,0,0,56000),
(9,'July 2025',155,0,5,0,61500),
(10,'July 2025',162,0,4,0,57750)
ON DUPLICATE KEY UPDATE
  hours_worked = VALUES(hours_worked),
  leave_deductions = VALUES(leave_deductions),
  final_salary = VALUES(final_salary),
  calculated_at = CURRENT_TIMESTAMP;

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

