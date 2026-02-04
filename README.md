# core-project-backend

Overview
- PHP + MySQL backend providing endpoints for employees, attendance, leave/time-off and payroll.
- This build version has NO authentication (endpoints are public). Add auth and TLS at your own risk...even tho not having them IS a risk.

Quick start: 
1. Clone repo.
2. Copy your JSON data files into /data:
   - data/attendance.json
   - data/employee_info.json
   - data/payroll_data.json
3. Copy .env.example -> .env and edit DB credentials.
4. Install dependencies:
   composer install
5. Create DB & tables:
   - Run `schema.sql` in MySQL Workbench or: mysql -u root -p < schema.sql
6. Import data:
   php src/import_data.php
7. Run local server:
   php -S localhost:8000 -t public
8. Endpoints (examples):
   - GET /api/employees
   - GET /api/employees/1
   - POST /api/attendance (body: employeeId,date,status)
   - POST /api/leave (body: employeeId,date,reason)
   - POST /api/payroll (body: employeeId,hoursWorked,leaveDeductions)

Testing
- Run PHPUnit:
  vendor/bin/phpunit 👈(This could be wrong)

Security notice
- WARNING: This version is intentionally unauthenticated for development/demo only. Do NOT deploy publicly without adding authentication and HTTPS...please 👉👈🥺.
