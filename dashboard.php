<?php
// Include helper functions for reading/writing JSON data and escaping output
require_once __DIR__ . '/includes/functions.php';
// Load employees list from the employees.json file
$employees = readData('employees');
// Load attendance records from the attendance.json file
$attendance = readData('attendance');
// Load leave requests from the leaves.json file
$leaves = readData('leaves');

// Count the total number of employees
$empCount = count($employees);
// Count the total number of attendance records
$attCount = count($attendance);
// Count how many leave requests are pending (status is missing or explicitly 'Pending')
$pendingLeaves = 0;
foreach ($leaves as $l) {
    if (!isset($l['status']) || $l['status'] === 'Pending') $pendingLeaves++;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navigation menu linking to all main pages of the HR system -->
<nav>
  <a href="dashboard.php">Dashboard</a>
  <a href="employees.php">Employees</a>
  <a href="attendance.php">Attendance</a>
  <a href="timeoff.php">Time Off</a>
  <a href="payroll.php">Payroll</a>
  <a href="index.php">Logout</a>
</nav>

<div class="container">
<h1>HR Dashboard</h1>

<!-- Display key metrics in card format -->
<div class="cards">
  <!-- Show total employee count -->
  <div class="card">Employees: <span id="empCount"><?php echo h($empCount); ?></span></div>
  <!-- Show total attendance records count -->
  <div class="card">Attendance Records: <?php echo h($attCount); ?></div>
  <!-- Show number of pending leave requests -->
  <div class="card">Pending Leave Requests: <?php echo h($pendingLeaves); ?></div>
</div>

<h2>Recent Employees</h2>
<!-- Display the 5 most recently added employees in reverse order -->
<ul>
<?php foreach (array_slice(array_reverse($employees), 0, 5) as $e): ?>
  <!-- Display employee name, role, and formatted salary -->
  <li><?php echo h($e['name']); ?> — <?php echo h($e['role']); ?> — R<?php echo number_format($e['salary'],2); ?></li>
<?php endforeach; ?>
</ul>

</div>

<script src="js/app.js"></script>
</body>
</html>

