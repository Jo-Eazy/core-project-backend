/*
USE modern_tech_solutions;

-- 1) COUNT for employees
SELECT COUNT(*) AS employees_count FROM employees;

-- 2) ORDER BY Showing the first 10 employees
SELECT id, name, position, department, salary FROM employees ORDER BY id LIMIT 10;

-- 3) Recent attendance (joined)
SELECT a.attendance_id, a.employee_id, e.name, a.date, a.status
FROM attendance a
JOIN employees e ON e.id = a.employee_id
ORDER BY a.date DESC, a.attendance_id DESC
LIMIT 50;

-- 4) Pending leave requests
SELECT l.leave_id, l.employee_id, e.name, l.start_date, l.status, l.reason
FROM leave_requests l
JOIN employees e ON e.id = l.employee_id
WHERE l.status = 'pending'
ORDER BY l.start_date DESC;

-- 5) Payroll snapshot
SELECT p.payroll_id, p.employee_id, e.name, p.month, p.final_salary
FROM payroll p
JOIN employees e ON e.id = p.employee_id
ORDER BY p.month DESC, p.employee_id;

