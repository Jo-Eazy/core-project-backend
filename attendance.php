<?php
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$employees = readData('employees');
$employeeId = 0; // preserve selection on validation errors
$status = 'Present';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = isset($_POST['employeeId']) ? (int)$_POST['employeeId'] : 0;
    $status = trim($_POST['status'] ?? 'Present');
    if ($employeeId <= 0) $errors[] = "Select an employee.";
    if ($status === '') $errors[] = "Status is required.";

    if (empty($errors)) {
        $attendance = readData('attendance');
        $attendance[] = [
            'id' => nextId($attendance),
            'employee_id' => $employeeId,
            'status' => $status,
            'created_at' => date('c')
        ];
        writeData('attendance', $attendance);
        header('Location: attendance.php');
        exit;
    }
}

$attendance = readData('attendance');
// Normalize attendance data into a flat list of entries with consistent keys
$attendanceList = [];
if (isset($attendance['attendanceAndLeave']) && is_array($attendance['attendanceAndLeave'])) {
  foreach ($attendance['attendanceAndLeave'] as $person) {
    $empId = $person['employeeId'] ?? null;
    $name = $person['name'] ?? null;
    if (!empty($person['attendance']) && is_array($person['attendance'])) {
      foreach ($person['attendance'] as $att) {
        $attendanceList[] = [
          'employee_id' => $empId,
          'status' => $att['status'] ?? '',
          'created_at' => $att['date'] ?? null,
          'employee_name' => $name
        ];
      }
    }
  }
} elseif (is_array($attendance)) {
  foreach ($attendance as $it) {
    if (is_array($it) && (isset($it['employee_id']) || isset($it['employeeId']))) {
      $attendanceList[] = [
        'employee_id' => $it['employee_id'] ?? $it['employeeId'] ?? null,
        'status' => $it['status'] ?? '',
        'created_at' => $it['created_at'] ?? $it['date'] ?? null,
      ];
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Attendance</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav>
<a href="dashboard.php">Home</a>
<a href="attendance.php">Attendance</a>
</nav>

<div class="container">
<h2>Mark Attendance</h2>

<?php if ($errors): ?>
  <div style="color:#b00020;"><?php foreach ($errors as $err) echo "<div>".h($err)."</div>"; ?></div>
<?php endif; ?>

<form method="post" action="attendance.php">
  <select name="employeeId" id="attName" required>
    <option value="">-- Select employee --</option>
    <?php foreach ($employees as $e): ?>
      <option value="<?php echo (int)$e['id']; ?>"<?php echo ((int)$e['id'] === (int)$employeeId) ? ' selected' : ''; ?>><?php echo h($e['name']); ?></option>
    <?php endforeach; ?>
  </select>
  <select name="status" id="status">
    <option value="Present"<?php echo ($status === 'Present') ? ' selected' : ''; ?>>Present</option>
    <option value="Absent"<?php echo ($status === 'Absent') ? ' selected' : ''; ?>>Absent</option>
  </select>
  <button type="submit">Save</button>
</form>

<h3>Recent Attendance</h3>
<ul id="attendanceList">
<?php if (empty($attendanceList)): ?>
  <li>No attendance records yet.</li>
<?php else: ?>
  <?php foreach (array_reverse($attendanceList) as $a):
    $empName = $a['employee_name'] ?? null;
    if (!$empName) {
        foreach ($employees as $e) { if ((int)$e['id'] === (int)($a['employee_id'] ?? 0)) { $empName = $e['name']; break; } }
    }
    $time = !empty($a['created_at']) ? date('Y-m-d H:i', strtotime($a['created_at'])) : '—';
  ?>
    <li><?php echo h($empName ?? 'Unknown'); ?> - <?php echo h($a['status'] ?? ''); ?> - <?php echo h($time); ?></li><br>
  <?php endforeach; ?>
<?php endif; ?>
</ul>
</div>

<script src="js/app.js"></script>
</body>
</html>

