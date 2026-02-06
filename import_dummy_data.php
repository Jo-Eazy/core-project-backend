<?php
// import_dummy_data.php
// Run from project root: php import_dummy_data.php
// Requires includes/functions.php from the previous step.

require_once __DIR__ . '/includes/functions.php';

function findFile($basename) {
    // check several common locations
    $candidates = [
        __DIR__ . '/' . $basename,
        __DIR__ . '/data/' . $basename,
        __DIR__ . '/data/original/' . $basename
    ];
    foreach ($candidates as $p) {
        if (file_exists($p)) return $p;
    }
    return null;
}

function loadJsonFilePath($basename) {
    $path = findFile($basename);
    if ($path === null) {
        echo "File not found: $basename\n";
        return null;
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    if ($data === null) {
        echo "Invalid JSON in $path\n";
        return null;
    }
    return $data;
}

// Load app current data (creates empty file if missing)
$appEmployees = readData('employees');   // array of employee objects with 'id'
$appAttendance = readData('attendance'); // array of attendance records
$appLeaves = readData('leaves');         // array of leave requests
$appPayroll = readData('payroll');       // array of payroll records (optional)

// Index helpers
function indexById(array $arr, $keyField = 'id') {
    $index = [];
    foreach ($arr as $i => $item) {
        if (isset($item[$keyField])) $index[(int)$item[$keyField]] = $i;
    }
    return $index;
}

$empIndex = indexById($appEmployees, 'id');

// 1) Import employee_info.json -> data/employees.json
$empDataRaw = loadJsonFilePath('employee_info.json');
if ($empDataRaw && isset($empDataRaw['employeeInformation'])) {
    $countAdded = 0;
    foreach ($empDataRaw['employeeInformation'] as $e) {
        $id = (int)($e['employeeId'] ?? 0);
        if ($id <= 0) continue;
        // Standardize the shape for app's employees.json
        $record = [
            'id' => $id,
            'name' => $e['name'] ?? '',
            'role' => $e['position'] ?? '',
            'department' => $e['department'] ?? '',
            'salary' => isset($e['salary']) ? (float)$e['salary'] : 0,
            'employment_history' => $e['employmentHistory'] ?? '',
            'contact' => $e['contact'] ?? '',
            'created_at' => date('c')
        ];
        if (isset($empIndex[$id])) {
            // update existing record (preserve existing created_at)
            $idx = $empIndex[$id];
            $created = $appEmployees[$idx]['created_at'] ?? $record['created_at'];
            $appEmployees[$idx] = $record;
            $appEmployees[$idx]['created_at'] = $created;
        } else {
            $appEmployees[] = $record;
            $empIndex[$id] = count($appEmployees) - 1;
            $countAdded++;
        }
    }
    writeData('employees', $appEmployees);
    echo "Employees: added/updated $countAdded new employees (employees.json updated).\n";
} else {
    echo "Skipping employee_info.json (not found or invalid).\n";
}

// Reload index after potential writes
$appEmployees = readData('employees');
$empIndex = indexById($appEmployees, 'id');

// 2) Import attendance.json -> data/attendance.json and data/leaves.json
$attendanceRaw = loadJsonFilePath('attendance.json');
$attAdded = 0;
$leaveAdded = 0;
if ($attendanceRaw && isset($attendanceRaw['attendanceAndLeave'])) {
    // Create lookup for existing attendance by employee+date
    $existingAttMap = [];
    foreach ($appAttendance as $a) {
        $key = ((int)($a['employee_id'] ?? 0)) . '|' . ($a['date'] ?? '');
        $existingAttMap[$key] = true;
    }
    // Existing leaves by employee+date
    $existingLeaveMap = [];
    foreach ($appLeaves as $l) {
        $key = ((int)($l['employee_id'] ?? 0)) . '|' . ($l['date'] ?? '');
        $existingLeaveMap[$key] = true;
    }

    foreach ($attendanceRaw['attendanceAndLeave'] as $entry) {
        $empId = (int)($entry['employeeId'] ?? 0);
        // attendance array
        if (!empty($entry['attendance']) && is_array($entry['attendance'])) {
            foreach ($entry['attendance'] as $att) {
                $date = $att['date'] ?? '';
                $status = $att['status'] ?? '';
                if ($date === '') continue;
                $key = $empId . '|' . $date;
                if (isset($existingAttMap[$key])) continue; // skip duplicates
                $rec = [
                    'id' => nextId($appAttendance),
                    'employee_id' => $empId,
                    'date' => $date,
                    'status' => $status,
                    'created_at' => $date . 'T00:00:00+00:00'
                ];
                $appAttendance[] = $rec;
                $existingAttMap[$key] = true;
                $attAdded++;
            }
        }
        // leaveRequests array
        if (!empty($entry['leaveRequests']) && is_array($entry['leaveRequests'])) {
            foreach ($entry['leaveRequests'] as $lr) {
                $date = $lr['date'] ?? '';
                $reason = $lr['reason'] ?? '';
                $status = $lr['status'] ?? 'Pending';
                if ($date === '') continue;
                $key = $empId . '|' . $date;
                if (isset($existingLeaveMap[$key])) continue; // skip duplicates
                $rec = [
                    'id' => nextId($appLeaves),
                    'employee_id' => $empId,
                    'date' => $date,
                    'reason' => $reason,
                    'status' => $status,
                    'created_at' => $date . 'T00:00:00+00:00'
                ];
                $appLeaves[] = $rec;
                $existingLeaveMap[$key] = true;
                $leaveAdded++;
            }
        }
    }
    if ($attAdded > 0) {
        writeData('attendance', $appAttendance);
    }
    if ($leaveAdded > 0) {
        writeData('leaves', $appLeaves);
    }
    echo "Attendance: added $attAdded records. Leaves: added $leaveAdded records.\n";
} else {
    echo "Skipping attendance.json (not found or invalid).\n";
}

// 3) Import payroll_data.json -> data/payroll.json (upsert by employee_id)
$payrollRaw = loadJsonFilePath('payroll_data.json');
$payAdded = 0;
$payUpdated = 0;
if ($payrollRaw && isset($payrollRaw['payrollData'])) {
    // index existing payroll by employee_id
    $payIndex = [];
    foreach ($appPayroll as $i => $p) {
        if (isset($p['employee_id'])) $payIndex[(int)$p['employee_id']] = $i;
    }

    foreach ($payrollRaw['payrollData'] as $p) {
        $empId = (int)($p['employeeId'] ?? 0);
        $hours = isset($p['hoursWorked']) ? (int)$p['hoursWorked'] : 0;
        $deductions = isset($p['leaveDeductions']) ? (int)$p['leaveDeductions'] : 0;
        $final = isset($p['finalSalary']) ? (float)$p['finalSalary'] : 0.0;
        if ($empId <= 0) continue;
        $rec = [
            'id' => isset($payIndex[$empId]) ? $appPayroll[$payIndex[$empId]]['id'] : nextId($appPayroll),
            'employee_id' => $empId,
            'hours_worked' => $hours,
            'leave_deductions' => $deductions,
            'final_salary' => $final,
            'calculated_at' => date('c')
        ];
        if (isset($payIndex[$empId])) {
            $appPayroll[$payIndex[$empId]] = $rec;
            $payUpdated++;
        } else {
            $appPayroll[] = $rec;
            $payAdded++;
        }
    }
    if ($payAdded > 0 || $payUpdated > 0) {
        writeData('payroll', $appPayroll);
    }
    echo "Payroll: added $payAdded new, updated $payUpdated existing.\n";
} else {
    echo "Skipping payroll_data.json (not found or invalid).\n";
}

echo "Import finished.\n";
?>
