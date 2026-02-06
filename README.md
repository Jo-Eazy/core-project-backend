# ModernTech — HR: Attendance, Payroll & Time-Off Backend (PHP + JSON + "MAYBE" MySQL seeds)

Lightweight PHP backend and admin pages for a small HR system (employees, attendance, leave requests, payroll calculator).  
This repository stores runtime data as JSON files (in `data/`) and includes "MAYBE" SQL scripts to create a MySQL database copy of the same dummy data. There is NO authentication — intended for local development and demos only, due to there being no authentication.

Contents
- PHP pages (UI + server-side handlers): index.php, dashboard.php, employees.php, attendance.php, timeoff.php, payroll.php
- Shared helpers: `includes/functions.php` (read/write JSON safely)
- Runtime data (JSON storage): `data/*.json` (employees.json, attendance.json, leaves.json, payroll.json, test_rw.json)
- Original dummy JSON files (source): `data/employee_info.json`, `data/attendance.json`, `data/payroll_data.json`
- Import script: `import_dummy_data.php` — merges source JSON into runtime JSON
- "MAYBE" SQL for MySQL Workbench: `schema_workbench.sql`, `seed_workbench.sql`, `verify_workbench.sql` (safe to re-run)
- Test: `test_rw.php` (verifies write permissions)
- Frontend assets: `css/style.css`, `js/app.js`
- 
